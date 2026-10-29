<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class AttendanceExportService
{
    /**
     * Get monthly attendance summary for all employees.
     *
     * @return Collection<int, object>
     */
    public function getMonthlySummary(int $month, int $year): Collection
    {
        $periodCarbon = Carbon::createFromDate($year, $month, 1);
        $periodLabel = $periodCarbon->translatedFormat('F Y');

        $employees = User::query()
            ->where('role', 'employee')
            ->with(['department', 'position'])
            ->orderBy('name')
            ->get();

        $attendances = Attendance::query()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get()
            ->groupBy('user_id');

        return $employees->map(function ($employee, $index) use ($attendances, $periodLabel) {
            /** @var Collection<int, Attendance> $records */
            $records = $attendances->get($employee->id, collect());

            $presentOnTime = $records->where('status', 'hadir')->count();
            $late = $records->where('status', 'terlambat')->count();
            $totalPresent = $presentOnTime + $late;
            $cuti = $records->where('status', 'cuti')->count();
            $sick = $records->where('status', 'sakit')->count();
            $absent = $records->where('status', 'alpha')->count();
            $lateMinutes = (int) $records->sum('late_minutes');

            $totalWorkMinutes = 0;
            foreach ($records as $record) {
                if ($record->check_in && $record->check_out) {
                    $in = Carbon::parse($record->check_in);
                    $out = Carbon::parse($record->check_out);
                    if ($out->greaterThan($in)) {
                        $totalWorkMinutes += $in->diffInMinutes($out);
                    }
                }
            }

            $workHours = intdiv($totalWorkMinutes, 60);
            $workMins = $totalWorkMinutes % 60;
            $workHoursFormatted = "{$workHours} jam {$workMins} mnt";
            $workHoursDecimal = round($totalWorkMinutes / 60, 2);

            return (object) [
                'no' => $index + 1,
                'user_id' => $employee->id,
                'employee_code' => $employee->employee_code ?? '-',
                'name' => $employee->name,
                'department' => $employee->department->name ?? '-',
                'position' => $employee->position->name ?? '-',
                'period' => $periodLabel,
                'present' => $presentOnTime,
                'late' => $late,
                'total_present' => $totalPresent,
                'cuti' => $cuti,
                'sick' => $sick,
                'absent' => $absent,
                'late_minutes' => $lateMinutes,
                'total_work_minutes' => $totalWorkMinutes,
                'work_hours_formatted' => $workHoursFormatted,
                'work_hours_decimal' => $workHoursDecimal,
            ];
        });
    }

    /**
     * Export monthly attendance summary to CSV.
     */
    public function exportCsv(int $month, int $year): StreamedResponse
    {
        $summary = $this->getMonthlySummary($month, $year);
        $monthPadded = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $filename = "rekap-absensi-{$year}-{$monthPadded}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($summary) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'NIP / ID Karyawan',
                'Nama Karyawan',
                'Departemen',
                'Jabatan',
                'Periode',
                'Hadir Tepat Waktu',
                'Terlambat',
                'Total Hari Masuk',
                'Cuti',
                'Sakit',
                'Alpha',
                'Total Keterlambatan (Menit)',
                'Total Jam Kerja',
            ]);

            foreach ($summary as $item) {
                fputcsv($handle, [
                    $item->no,
                    $this->sanitizeCsvValue($item->employee_code),
                    $this->sanitizeCsvValue($item->name),
                    $this->sanitizeCsvValue($item->department),
                    $this->sanitizeCsvValue($item->position),
                    $this->sanitizeCsvValue($item->period),
                    $item->present,
                    $item->late,
                    $item->total_present,
                    $item->cuti,
                    $item->sick,
                    $item->absent,
                    $item->late_minutes,
                    $item->work_hours_formatted,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export monthly attendance summary to XLSX using native ZipArchive OpenXML format.
     */
    public function exportExcel(int $month, int $year): BinaryFileResponse
    {
        $summary = $this->getMonthlySummary($month, $year);
        $monthPadded = str_pad((string) $month, 2, '0', STR_PAD_LEFT);
        $filename = "rekap-absensi-{$year}-{$monthPadded}.xlsx";

        $periodCarbon = Carbon::createFromDate($year, $month, 1);
        $periodLabel = $periodCarbon->translatedFormat('F Y');

        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        $zip = new ZipArchive;
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file Excel sementara.');
        }

        // [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', $this->buildContentTypesXml());

        // _rels/.rels
        $zip->addFromString('_rels/.rels', $this->buildGlobalRelsXml());

        // xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRelsXml());

        // xl/workbook.xml
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbookXml());

        // xl/styles.xml
        $zip->addFromString('xl/styles.xml', $this->buildStylesXml());

        // xl/worksheets/sheet1.xml
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($summary, $periodLabel));

        $zip->close();

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Sanitize text against CSV formula injection.
     */
    public function sanitizeCsvValue(mixed $value): string
    {
        $str = (string) $value;
        if ($str === '' || $str === '-') {
            return $str;
        }

        if (in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$str;
        }

        return $str;
    }

    /**
     * Escape string for XML.
     */
    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function buildContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'.
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'.
            '<Default Extension="xml" ContentType="application/xml"/>'.
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'.
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'.
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'.
            '</Types>';
    }

    private function buildGlobalRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'.
            '</Relationships>';
    }

    private function buildWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'.
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'.
            '</Relationships>';
    }

    private function buildWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'.
            '<sheets>'.
            '<sheet name="Rekap Absensi" sheetId="1" r:id="rId1"/>'.
            '</sheets>'.
            '</workbook>';
    }

    private function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.
            '<fonts count="3">'.
            '<font><name val="Calibri"/><sz val="11"/></font>'.
            '<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>'.
            '<font><b/><name val="Calibri"/><sz val="14"/><color rgb="FF1E293B"/></font>'.
            '</fonts>'.
            '<fills count="4">'.
            '<fill><patternFill patternType="none"/></fill>'.
            '<fill><patternFill patternType="gray125"/></fill>'.
            '<fill><patternFill patternType="solid"><fgColor rgb="FF4F46E5"/><bgColor indexed="64"/></patternFill></fill>'.
            '<fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill>'.
            '</fills>'.
            '<borders count="2">'.
            '<border><left/><right/><top/><bottom/><diagonal/></border>'.
            '<border>'.
            '<left style="thin"><color rgb="FFCBD5E1"/></left>'.
            '<right style="thin"><color rgb="FFCBD5E1"/></right>'.
            '<top style="thin"><color rgb="FFCBD5E1"/></top>'.
            '<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'.
            '</border>'.
            '</borders>'.
            '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'.
            '<cellXfs count="6">'.
            // 0: Normal text (left, with border)
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment vertical="center"/></xf>'.
            // 1: Header (bold white text, indigo fill, centered, border)
            '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'.
            // 2: Centered numeric / code (centered, with border)
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'.
            // 3: Right aligned number (with border)
            '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>'.
            // 4: Title (14pt bold, no border)
            '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>'.
            // 5: Total Summary row (bold text, gray fill, border)
            '<xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'.
            '</cellXfs>'.
            '</styleSheet>';
    }

    private function buildSheetXml(Collection $summary, string $periodLabel): string
    {
        $cols = [
            ['width' => 6],   // No
            ['width' => 18],  // NIP
            ['width' => 28],  // Nama
            ['width' => 22],  // Departemen
            ['width' => 22],  // Jabatan
            ['width' => 16],  // Periode
            ['width' => 18],  // Hadir Tepat Waktu
            ['width' => 14],  // Terlambat
            ['width' => 16],  // Total Hadir
            ['width' => 12],  // Cuti
            ['width' => 12],  // Sakit
            ['width' => 12],  // Alpha
            ['width' => 25],  // Total Keterlambatan
            ['width' => 20],  // Total Jam Kerja
        ];

        $colsXml = '<cols>';
        foreach ($cols as $idx => $c) {
            $colNum = $idx + 1;
            $colsXml .= "<col min=\"{$colNum}\" max=\"{$colNum}\" width=\"{$c['width']}\" customWidth=\"1\"/>";
        }
        $colsXml .= '</cols>';

        $rowsXml = '';

        // Row 1: Title
        $rowsXml .= '<row r="1" ht="24" customHeight="1">';
        $rowsXml .= '<c r="A1" s="4" t="inlineStr"><is><t>LAPORAN REKAPITULASI ABSENSI BULANAN</t></is></c>';
        $rowsXml .= '</row>';

        // Row 2: Periode
        $rowsXml .= '<row r="2" ht="18" customHeight="1">';
        $rowsXml .= '<c r="A2" s="0" t="inlineStr"><is><t>Periode: '.$this->xmlEscape($periodLabel).'</t></is></c>';
        $rowsXml .= '</row>';

        // Row 3: Blank separator
        $rowsXml .= '<row r="3" ht="12" customHeight="1"></row>';

        // Row 4: Column Headers
        $headers = [
            'No',
            'NIP / ID Karyawan',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Periode',
            'Hadir Tepat Waktu',
            'Terlambat',
            'Total Hari Masuk',
            'Cuti',
            'Sakit',
            'Alpha',
            'Total Keterlambatan (Menit)',
            'Total Jam Kerja',
        ];

        $rowsXml .= '<row r="4" ht="28" customHeight="1">';
        foreach ($headers as $cIdx => $hText) {
            $cellRef = $this->columnLetter($cIdx + 1).'4';
            $rowsXml .= "<c r=\"{$cellRef}\" s=\"1\" t=\"inlineStr\"><is><t>".$this->xmlEscape($hText).'</t></is></c>';
        }
        $rowsXml .= '</row>';

        // Data rows start at 5
        $rowNum = 5;
        foreach ($summary as $item) {
            $rowsXml .= "<row r=\"{$rowNum}\" ht=\"20\" customHeight=\"1\">";
            // A: No
            $rowsXml .= '<c r="A'.$rowNum.'" s="2"><v>'.$item->no.'</v></c>';
            // B: NIP
            $rowsXml .= '<c r="B'.$rowNum.'" s="2" t="inlineStr"><is><t>'.$this->xmlEscape($this->sanitizeCsvValue($item->employee_code)).'</t></is></c>';
            // C: Nama
            $rowsXml .= '<c r="C'.$rowNum.'" s="0" t="inlineStr"><is><t>'.$this->xmlEscape($this->sanitizeCsvValue($item->name)).'</t></is></c>';
            // D: Departemen
            $rowsXml .= '<c r="D'.$rowNum.'" s="0" t="inlineStr"><is><t>'.$this->xmlEscape($this->sanitizeCsvValue($item->department)).'</t></is></c>';
            // E: Jabatan
            $rowsXml .= '<c r="E'.$rowNum.'" s="0" t="inlineStr"><is><t>'.$this->xmlEscape($this->sanitizeCsvValue($item->position)).'</t></is></c>';
            // F: Periode
            $rowsXml .= '<c r="F'.$rowNum.'" s="2" t="inlineStr"><is><t>'.$this->xmlEscape($this->sanitizeCsvValue($item->period)).'</t></is></c>';
            // G: Hadir Tepat Waktu (numeric)
            $rowsXml .= '<c r="G'.$rowNum.'" s="2"><v>'.$item->present.'</v></c>';
            // H: Terlambat (numeric)
            $rowsXml .= '<c r="H'.$rowNum.'" s="2"><v>'.$item->late.'</v></c>';
            // I: Total Hari Masuk (numeric)
            $rowsXml .= '<c r="I'.$rowNum.'" s="2"><v>'.$item->total_present.'</v></c>';
            // J: Cuti (numeric)
            $rowsXml .= '<c r="J'.$rowNum.'" s="2"><v>'.$item->cuti.'</v></c>';
            // K: Sakit (numeric)
            $rowsXml .= '<c r="K'.$rowNum.'" s="2"><v>'.$item->sick.'</v></c>';
            // L: Alpha (numeric)
            $rowsXml .= '<c r="L'.$rowNum.'" s="2"><v>'.$item->absent.'</v></c>';
            // M: Total Keterlambatan Menit (numeric)
            $rowsXml .= '<c r="M'.$rowNum.'" s="3"><v>'.$item->late_minutes.'</v></c>';
            // N: Total Jam Kerja (formatted text)
            $rowsXml .= '<c r="N'.$rowNum.'" s="2" t="inlineStr"><is><t>'.$this->xmlEscape($item->work_hours_formatted).'</t></is></c>';

            $rowsXml .= '</row>';
            $rowNum++;
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
            '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.
            $colsXml.
            '<sheetData>'.
            $rowsXml.
            '</sheetData>'.
            '</worksheet>';
    }

    private function columnLetter(int $colNumber): string
    {
        $letter = '';
        while ($colNumber > 0) {
            $modulo = ($colNumber - 1) % 26;
            $letter = chr(65 + $modulo).$letter;
            $colNumber = (int) (($colNumber - $modulo) / 26);
        }

        return $letter;
    }
}
