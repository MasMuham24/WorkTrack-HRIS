<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $leaveRequests = LeaveRequest::with(['user', 'approver'])
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('leave_type'), function ($query) {
                $query->where('leave_type', request('leave_type'));
            })
            ->latest()
            ->paginate(10);

        $pending = LeaveRequest::where('status', 'pending')->count();
        $approved = LeaveRequest::where('status', 'approved')->count();
        $rejected = LeaveRequest::where('status', 'rejected')->count();
        $total = LeaveRequest::count();

        return view('hr.leave-requests.index', compact('leaveRequests', 'pending', 'approved', 'rejected', 'total'));
    }

    public function show(string $id): View
    {
        $leaveRequest = LeaveRequest::with(['user', 'approver'])->findOrFail($id);
        return view('hr.leave-requests.show', compact('leaveRequest'));
    }

    public function statistics(): JsonResponse
    {
        return response()->json([
            'pendingCuti' => LeaveRequest::query()->where('status', 'pending')->where('leave_type', 'cuti')->count(),
            'approvedCuti' => LeaveRequest::query()->where('status', 'approved')->where('leave_type', 'cuti')->count(),
            'rejectedCuti' => LeaveRequest::query()->where('status', 'rejected')->where('leave_type', 'cuti')->count(),
            'pendingIzin' => LeaveRequest::query()->where('status', 'pending')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
            'approvedIzin' => LeaveRequest::query()->where('status', 'approved')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
            'rejectedIzin' => LeaveRequest::query()->where('status', 'rejected')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
        ]);
    }

    public function live(): JsonResponse
    {
        $requests = LeaveRequest::query()
            ->with(['user', 'user.department'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn (LeaveRequest $request) => [
                'id' => $request->id,
                'name' => $request->user->name ?? '-',
                'department' => $request->user->department->name ?? '-',
                'leave_type' => $request->leave_type,
                'start_date' => $request->start_date?->format('Y-m-d'),
                'end_date' => $request->end_date?->format('Y-m-d'),
                'reason' => $request->reason,
                'status' => $request->status,
                'created_at' => $request->created_at?->toIso8601String(),
            ]);

        $latestRequest = LeaveRequest::latest('id')->first();

        return response()->json([
            'total_pending' => LeaveRequest::query()->where('status', 'pending')->count(),
            'latest_id' => $latestRequest?->id ?? 0,
            'pendingCuti' => LeaveRequest::query()->where('status', 'pending')->where('leave_type', 'cuti')->count(),
            'approvedCuti' => LeaveRequest::query()->where('status', 'approved')->where('leave_type', 'cuti')->count(),
            'rejectedCuti' => LeaveRequest::query()->where('status', 'rejected')->where('leave_type', 'cuti')->count(),
            'pendingIzin' => LeaveRequest::query()->where('status', 'pending')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
            'approvedIzin' => LeaveRequest::query()->where('status', 'approved')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
            'rejectedIzin' => LeaveRequest::query()->where('status', 'rejected')->whereIn('leave_type', ['sakit', 'penting', 'lainnya'])->count(),
            'requests' => $requests,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => $validated['status'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('hr.leave-requests.index')
            ->with('success', 'Pengajuan cuti ' . ($validated['status'] === 'approved' ? 'disetujui' : 'ditolak') . '.');
    }
}