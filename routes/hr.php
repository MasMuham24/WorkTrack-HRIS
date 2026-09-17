<?php

use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\DashboardController;
use App\Http\Controllers\HR\LeaveApplicationController;
use App\Http\Controllers\HR\LeaveManagementController;
use App\Http\Controllers\HR\LeaveRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leave-management', [LeaveManagementController::class, 'index'])->name('leave-management.index');
    Route::resource('leave-applications', LeaveApplicationController::class);
    Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('leave-requests/{leave_request}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::put('leave-requests/{leave_request}', [LeaveRequestController::class, 'update'])->name('leave-requests.update');
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    });
});
