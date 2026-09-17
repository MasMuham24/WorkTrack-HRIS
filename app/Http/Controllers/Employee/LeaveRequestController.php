<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $leaveRequests = LeaveRequest::query()->where('user_id', Auth::id())->latest()->paginate(10);
        $allLeaveRequests = LeaveRequest::query()->where('user_id', Auth::id())->get();
        $totalPending = $allLeaveRequests->where('status', 'pending')->count();
        $totalApproved = $allLeaveRequests->where('status', 'approved')->count();
        $totalRejected = $allLeaveRequests->where('status', 'rejected')->count();
        return view('employee.leave-requests.index', compact('leaveRequests', 'totalPending', 'totalApproved', 'totalRejected'));
    }

    public function create(): View
    {
        return view('employee.leave-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => ['required', 'in:cuti,sakit,penting,lainnya'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);
        return redirect()->route('employee.leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }
}
