<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

require __DIR__.'/auth.php';
require __DIR__.'/profile.php';
require __DIR__.'/admin.php';
require __DIR__.'/hr.php';
require __DIR__.'/employee.php';
require __DIR__.'/management.php';
require __DIR__.'/reports.php';