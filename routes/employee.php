<?php

use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\Employee\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    });

    Route::resource('leave-requests', LeaveRequestController::class)->only(['index', 'create', 'store', 'show']);

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});
