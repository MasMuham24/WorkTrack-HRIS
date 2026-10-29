<?php

use App\Http\Controllers\Report\AttendanceReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,hr'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
    Route::get('/attendance/export/excel', [AttendanceReportController::class, 'exportExcel'])->name('attendance.export.excel');
    Route::get('/attendance/export/csv', [AttendanceReportController::class, 'exportCsv'])->name('attendance.export.csv');
});
