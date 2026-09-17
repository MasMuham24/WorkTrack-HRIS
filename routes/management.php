<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\PositionController;
use Illuminate\Support\Facades\Route;

//hr
Route::middleware(['auth', 'role:admin,hr'])->prefix('management')->name('management.')->group(function () {
    Route::resource('departments', DepartmentController::class)->except('show')->parameters(['departments' => 'departemen']);
    Route::resource('positions', PositionController::class)->except('show');
    Route::resource('employees', EmployeeController::class)->except('show');
});
// admin
Route::middleware(['auth', 'role:admin'])->prefix('management')->name('management.')->group(function () {
    Route::resource('offices', OfficeController::class)->except('show');
});
