<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveRequestStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public LeaveRequest $leaveRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public static function getLeaveTypeLabel(string $type): string
    {
        return match ($type) {
            'sakit' => 'izin sakit',
            'cuti' => 'cuti tahunan',
            'penting' => 'izin penting',
            'lainnya' => 'izin lainnya',
            default => 'izin',
        };
    }

    public function getMessage(): string
    {
        $typeLabel = self::getLeaveTypeLabel($this->leaveRequest->leave_type);
        $statusText = $this->leaveRequest->status === 'approved' ? 'disetujui' : 'ditolak';

        return "Pengajuan {$typeLabel} Anda telah {$statusText}.";
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = self::getLeaveTypeLabel($this->leaveRequest->leave_type);
        $statusText = $this->leaveRequest->status === 'approved' ? 'disetujui' : 'ditolak';

        return [
            'leave_request_id' => $this->leaveRequest->id,
            'leave_type' => $this->leaveRequest->leave_type,
            'leave_type_label' => $typeLabel,
            'status' => $this->leaveRequest->status,
            'title' => 'Pengajuan '.ucwords($typeLabel).' '.ucfirst($statusText),
            'message' => $this->getMessage(),
            'start_date' => $this->leaveRequest->start_date?->format('Y-m-d'),
            'end_date' => $this->leaveRequest->end_date?->format('Y-m-d'),
            'start_date_formatted' => $this->leaveRequest->start_date?->translatedFormat('d M Y'),
            'end_date_formatted' => $this->leaveRequest->end_date?->translatedFormat('d M Y'),
            'approved_by_name' => $this->leaveRequest->approver?->name,
            'approved_at' => $this->leaveRequest->approved_at?->toIso8601String(),
            'url' => route('employee.leave-requests.show', $this->leaveRequest->id),
        ];
    }
}
