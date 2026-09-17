<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    public function index()
    {
        $todayAttendance =
            $this->attendanceService->getTodayAttendance();

        $attendances =
            $this->attendanceService->getAttendanceHistory();

        $offices =
            $this->attendanceService->getOffices();

        return view(
            'hr.attendance.index',
            compact(
                'todayAttendance',
                'attendances',
                'offices'
            )
        );
    }

    public function checkIn(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        try {
            $attendance = $this->attendanceService->checkIn(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                isset($validated['accuracy'])
                    ? (float) $validated['accuracy']
                    : null
            );

            $msg = 'Check in berhasil di ' .
                $attendance->office->name .
                ' (Jarak: ' .
                number_format($attendance->distance, 0) .
                ' meter).';

            return $request->ajax()
                ? response()->json([
                    'message' => $msg,
                ])
                : redirect()
                    ->route('hr.attendance.index')
                    ->with('success', $msg);

        } catch (\RuntimeException $e) {

            return $request->ajax()
                ? response()->json([
                    'message' => $e->getMessage(),
                ], 422)
                : back()->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function checkOut(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->attendanceService->checkOut(
                (float) $validated['latitude'],
                (float) $validated['longitude'],
                isset($validated['accuracy'])
                    ? (float) $validated['accuracy']
                    : null
            );

            $msg = 'Check out berhasil.';

            return $request->ajax()
                ? response()->json([
                    'message' => $msg,
                ])
                : redirect()
                    ->route('hr.attendance.index')
                    ->with('success', $msg);

        } catch (\RuntimeException $e) {

            return $request->ajax()
                ? response()->json([
                    'message' => $e->getMessage(),
                ], 422)
                : back()->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}
