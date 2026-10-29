<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get employee notifications (list and unread count).
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($n) {
                $data = $n->data;

                return [
                    'id' => $n->id,
                    'leave_request_id' => $data['leave_request_id'] ?? null,
                    'title' => $data['title'] ?? 'Notifikasi Pengajuan',
                    'message' => $data['message'] ?? '',
                    'leave_type' => $data['leave_type'] ?? 'cuti',
                    'leave_type_label' => $data['leave_type_label'] ?? '',
                    'status' => $data['status'] ?? 'pending',
                    'start_date' => $data['start_date'] ?? null,
                    'end_date' => $data['end_date'] ?? null,
                    'start_date_formatted' => $data['start_date_formatted'] ?? null,
                    'end_date_formatted' => $data['end_date_formatted'] ?? null,
                    'approved_by_name' => $data['approved_by_name'] ?? null,
                    'url' => $data['url'] ?? route('employee.leave-requests.index'),
                    'read' => $n->read(),
                    'created_at' => $n->created_at?->toIso8601String(),
                    'time_ago' => $n->created_at?->diffForHumans() ?? '',
                ];
            });

        $allLeaveRequests = LeaveRequest::query()->where('user_id', $user->id)->get();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
            'stats' => [
                'total_pending' => $allLeaveRequests->where('status', 'pending')->count(),
                'total_approved' => $allLeaveRequests->where('status', 'approved')->count(),
                'total_rejected' => $allLeaveRequests->where('status', 'rejected')->count(),
            ],
            'requests' => $allLeaveRequests->map(fn ($r) => [
                'id' => $r->id,
                'status' => $r->status,
            ]),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
