<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\View\View;

class LeaveManagementController extends Controller
{
    public function index(): View
    {
        $cutiRequests = LeaveRequest::with(['user', 'approver'])
            ->where('leave_type', 'cuti')
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('leave_type'), function ($query) {
                $query->where('leave_type', request('leave_type'));
            })
            ->latest()
            ->paginate(10);

        $izinRequests = LeaveRequest::with(['user', 'approver'])
            ->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('leave_type'), function ($query) {
                $query->where('leave_type', request('leave_type'));
            })
            ->latest()
            ->paginate(10);

        // Leave statistics
        $pendingCuti = LeaveRequest::query()
            ->where('status', 'pending')
            ->where('leave_type', 'cuti')
            ->count();

        $approvedCuti = LeaveRequest::query()
            ->where('status', 'approved')
            ->where('leave_type', 'cuti')
            ->count();

        $rejectedCuti = LeaveRequest::query()
            ->where('status', 'rejected')
            ->where('leave_type', 'cuti')
            ->count();

        $pendingIzin = LeaveRequest::query()
            ->where('status', 'pending')
            ->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])
            ->count();

        $approvedIzin = LeaveRequest::query()
            ->where('status', 'approved')
            ->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])
            ->count();

        $rejectedIzin = LeaveRequest::query()
            ->where('status', 'rejected')
            ->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])
            ->count();

        return view('hr.leave-management.index', compact(
            'cutiRequests',
            'izinRequests',
            'pendingCuti',
            'approvedCuti',
            'rejectedCuti',
            'pendingIzin',
            'approvedIzin',
            'rejectedIzin'
        ));
    }
}
