<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Departemen;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class AttendanceExportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createHR(): User
    {
        return User::factory()->create(['role' => 'hr']);
    }

    private function createEmployee(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'employee',
        ], $overrides));
    }

    public function test_guest_cannot_access_reports_or_exports(): void
    {
        $this->get(route('reports.attendance'))->assertRedirect('/login');
        $this->get(route('reports.attendance.export.excel'))->assertRedirect('/login');
        $this->get(route('reports.attendance.export.csv'))->assertRedirect('/login');
    }

    public function test_employee_cannot_access_reports_or_exports(): void
    {
        $employee = $this->createEmployee();

        $this->actingAs($employee)->get(route('reports.attendance'))->assertForbidden();
        $this->actingAs($employee)->get(route('reports.attendance.export.excel'))->assertForbidden();
        $this->actingAs($employee)->get(route('reports.attendance.export.csv'))->assertForbidden();
    }

    public function test_admin_and_hr_can_view_attendance_report_page(): void
    {
        $admin = $this->createAdmin();
        $hr = $this->createHR();

        $responseAdmin = $this->actingAs($admin)->get(route('reports.attendance'));
        $responseAdmin->assertOk()
            ->assertSee('Laporan Rekap Absensi Bulanan')
            ->assertSee('Ekspor Excel (.xlsx)')
            ->assertSee('Ekspor CSV (.csv)');

        $responseHR = $this->actingAs($hr)->get(route('reports.attendance'));
        $responseHR->assertOk()
            ->assertSee('Laporan Rekap Absensi Bulanan')
            ->assertSee('Ekspor Excel (.xlsx)')
            ->assertSee('Ekspor CSV (.csv)');
    }

    public function test_hr_can_export_csv_with_correct_headers_and_filename(): void
    {
        $hr = $this->createHR();
        $employee = $this->createEmployee(['name' => 'Budi Santoso']);

        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('rekap-absensi-2026-10.csv', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->streamedContent();
        $this->assertStringContainsString('NIP / ID Karyawan', $content);
        $this->assertStringContainsString('Nama Karyawan', $content);
        $this->assertStringContainsString('Total Keterlambatan (Menit)', $content);
        $this->assertStringContainsString('Total Jam Kerja', $content);
        $this->assertStringContainsString('Budi Santoso', $content);
    }

    public function test_admin_can_export_excel_with_correct_headers_and_filename(): void
    {
        $admin = $this->createAdmin();
        $employee = $this->createEmployee(['name' => 'Siti Rahma']);

        $response = $this->actingAs($admin)->get(route('reports.attendance.export.excel', [
            'month' => 5,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('rekap-absensi-2026-05.xlsx', $response->headers->get('Content-Disposition') ?? '');

        // Verify the binary response file is a valid ZIP archive containing XLSX structure
        $file = $response->getFile();
        $this->assertFileExists($file->getPathname());

        $zip = new ZipArchive;
        $opened = $zip->open($file->getPathname());
        $this->assertTrue($opened === true);
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('xl/workbook.xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $this->assertStringContainsString('Siti Rahma', $sheetXml);

        $zip->close();
    }

    public function test_export_only_includes_data_from_selected_period(): void
    {
        $hr = $this->createHR();
        $employee = $this->createEmployee(['name' => 'Ahmad Dahlan']);

        // Month 5
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-05-15',
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);

        // Month 6 (target)
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-06-10',
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-06-11',
            'check_in' => '08:30:00',
            'check_out' => '17:00:00',
            'status' => 'terlambat',
            'late_minutes' => 30,
        ]);

        // Month 7
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-07-01',
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);

        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 6,
            'year' => 2026,
        ]));

        $content = $response->streamedContent();

        // Target month 6 has:
        // Hadir ontime = 1, Late = 1, Total Masuk = 2, Late minutes = 30
        $lines = explode("\n", trim($content));
        $this->assertCount(2, $lines); // Header + 1 employee row
        $this->assertStringContainsString('Ahmad Dahlan', $lines[1]);
        $this->assertStringContainsString(',1,1,2,0,0,0,30,', $lines[1]);
    }

    public function test_export_calculates_summary_metrics_accurately(): void
    {
        $hr = $this->createHR();

        $dept = Departemen::create(['name' => 'Teknologi Informasi']);
        $pos = Position::create([
            'departement_id' => $dept->id,
            'name' => 'Software Engineer',
        ]);

        $employee = $this->createEmployee([
            'name' => 'Doni Pratama',
            'employee_code' => 'EMP-007',
            'departement_id' => $dept->id,
            'position_id' => $pos->id,
        ]);

        // 1. Hadir tepat waktu (08:00 to 17:00 = 9 hours = 540 minutes)
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-10-01',
            'check_in' => '08:00:00',
            'check_out' => '17:00:00',
            'status' => 'hadir',
            'late_minutes' => 0,
        ]);

        // 2. Terlambat (08:20 to 17:00 = 8h40m = 520 minutes, 20 late minutes)
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-10-02',
            'check_in' => '08:20:00',
            'check_out' => '17:00:00',
            'status' => 'terlambat',
            'late_minutes' => 20,
        ]);

        // 3. Cuti
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-10-05',
            'status' => 'cuti',
            'late_minutes' => 0,
        ]);

        // 4. Sakit
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-10-06',
            'status' => 'sakit',
            'late_minutes' => 0,
        ]);

        // 5. Alpha
        Attendance::create([
            'user_id' => $employee->id,
            'attendance_date' => '2026-10-07',
            'status' => 'alpha',
            'late_minutes' => 0,
        ]);

        // Total work minutes = 540 + 520 = 1060 minutes = 17 jam 40 mnt
        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 10,
            'year' => 2026,
        ]));

        $content = $response->streamedContent();

        $this->assertStringContainsString('EMP-007', $content);
        $this->assertStringContainsString('Doni Pratama', $content);
        $this->assertStringContainsString('Teknologi Informasi', $content);
        $this->assertStringContainsString('Software Engineer', $content);
        $this->assertStringContainsString('17 jam 40 mnt', $content);
        $this->assertStringContainsString(',1,1,2,1,1,1,20,', $content);
    }

    public function test_csv_sanitizes_formula_injection(): void
    {
        $hr = $this->createHR();
        $employee = $this->createEmployee([
            'name' => '=HYPERLINK("http://evil.com")',
            'employee_code' => '+62812345678',
        ]);

        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 10,
            'year' => 2026,
        ]));

        $content = $response->streamedContent();

        // Must prefix with single quote to disable execution in Excel
        $this->assertStringContainsString("'+62812345678", $content);
        $this->assertStringContainsString("'=HYPERLINK(\"\"http://evil.com\"\")", $content);
    }

    public function test_invalid_month_and_year_fallback_safely(): void
    {
        $hr = $this->createHR();

        Carbon::setTestNow('2026-10-15 10:00:00');

        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 99,
            'year' => 'invalid_year',
        ]));

        $response->assertOk();
        // Falls back to current month 10 and year 2026
        $this->assertStringContainsString('rekap-absensi-2026-10.csv', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_export_handles_empty_attendance_gracefully(): void
    {
        $hr = $this->createHR();
        $employee = $this->createEmployee(['name' => 'Karyawan Baru']);

        $response = $this->actingAs($hr)->get(route('reports.attendance.export.csv', [
            'month' => 10,
            'year' => 2026,
        ]));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Karyawan Baru', $content);
        $this->assertStringContainsString(',0,0,0,0,0,0,0,"0 jam 0 mnt"', $content);
    }
}
