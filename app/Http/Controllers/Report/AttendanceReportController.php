<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\AttendanceExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportController extends Controller
{
    public function __construct(
        protected AttendanceExportService $exportService
    ) {}

    /**
     * Display monthly attendance report summary.
     */
    public function index(Request $request): View
    {
        [$month, $year] = $this->resolvePeriod($request);

        $summary = $this->exportService->getMonthlySummary($month, $year);

        $months = collect(range(1, 12))->mapWithKeys(fn ($m) => [
            $m => Carbon::create()->month($m)->translatedFormat('F'),
        ]);

        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 1);

        return view('reports.attendance', compact('summary', 'month', 'year', 'months', 'years'));
    }

    /**
     * Export monthly attendance summary to Excel (.xlsx).
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$month, $year] = $this->resolvePeriod($request);

        return $this->exportService->exportExcel($month, $year);
    }

    /**
     * Export monthly attendance summary to CSV (.csv).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        [$month, $year] = $this->resolvePeriod($request);

        return $this->exportService->exportCsv($month, $year);
    }

    /**
     * Resolve and validate month and year from request.
     *
     * @return array{0: int, 1: int}
     */
    protected function resolvePeriod(Request $request): array
    {
        $rawMonth = $request->input('month');
        $rawYear = $request->input('year');

        $now = Carbon::now();

        $month = is_numeric($rawMonth) ? (int) $rawMonth : (int) $now->month;
        $year = is_numeric($rawYear) ? (int) $rawYear : (int) $now->year;

        if ($month < 1 || $month > 12) {
            $month = (int) $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = (int) $now->year;
        }

        return [$month, $year];
    }
}
