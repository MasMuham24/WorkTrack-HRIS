<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController;
use App\Http\Controllers\HR\LeaveApplicationController;
use App\Http\Controllers\HR\LeaveManagementController;
use App\Http\Controllers\HR\LeaveRequestController as HRLeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Report\AttendanceReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendances', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
});

Route::middleware(['auth', 'role:admin,hr'])->prefix('management')->name('management.')->group(function () {
    Route::resource('departments', DepartmentController::class)->except('show')->parameters(['departments' => 'departemen']);
    Route::resource('positions', PositionController::class)->except('show');
    Route::resource('employees', EmployeeController::class)->except('show');
    Route::resource('offices', OfficeController::class)->except('show');
});

Route::middleware(['auth', 'role:admin,hr'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/attendance', [AttendanceReportController::class, 'index'])->name('attendance');
});

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');
    Route::get('/leave-management', [LeaveManagementController::class, 'index'])->name('leave-management.index');
    Route::resource('leave-applications', LeaveApplicationController::class);
    Route::get('leave-requests', [HRLeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('leave-requests/{leave_request}', [HRLeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::put('leave-requests/{leave_request}', [HRLeaveRequestController::class, 'update'])->name('leave-requests.update');
});

Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    });
    Route::resource('leave-requests', LeaveRequestController::class)->only(['index', 'create', 'store']);
});
