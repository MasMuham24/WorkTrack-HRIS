<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $totalEmployees = User::query()->where('role', 'employee')->count();
        $presentToday = Attendance::query()->whereDate('attendance_date', $today)->count();
        $absentToday = max(0, $totalEmployees - $presentToday);
        $leaveToday = LeaveRequest::query()->where('status', 'approved')->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->count();
        $recentAttendances = Attendance::with(['user', 'user.department'])->whereDate('attendance_date', $today)->latest()->take(5)->get();
        return view('admin.dashboard', compact(
            'totalEmployees',
            'presentToday',
            'absentToday',
            'leaveToday',
            'recentAttendances'
        ));
    }
}
