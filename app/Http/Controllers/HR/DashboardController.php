<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Departemen;
use App\Models\LeaveRequest;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $pendingLeaveRequestsCount = LeaveRequest::query()->where('status', 'pending')->count();
        $approvedLeaveRequestsCount = LeaveRequest::query()->where('status', 'approved')->count();
        $activeEmployeesCount = User::query()->where('role', 'employee')->where('status', 'active')->count();
        $usersCount = User::query()->count();
        $positionsCount = Position::query()->count();
        $inactiveUsersCount = User::query()
            ->where('status', '!=', 'active')
            ->count();
        $departmentsCount = Departemen::query()->count();
        $pendingLeaves = LeaveRequest::query()
            ->with(['user', 'user.department'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Late employees today
        $lateEmployees = Attendance::query()
            ->with(['user', 'user.department'])
            ->whereDate('attendance_date', $today)
            ->where('status', 'terlambat')
            ->get();

        return view('hr.dashboard', compact(
            'pendingLeaveRequestsCount',
            'approvedLeaveRequestsCount',
            'activeEmployeesCount',
            'departmentsCount',
            'usersCount',
            'positionsCount',
            'inactiveUsersCount',
            'pendingLeaves',
            'lateEmployees'
        ));
    }
}
