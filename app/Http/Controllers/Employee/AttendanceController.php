<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $todayAttendance = Attendance::query()
            ->with('office')
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        $attendances = Attendance::query()
            ->with('office')
            ->where('user_id', Auth::id())
            ->latest('attendance_date')
            ->paginate(10);

        $offices = Office::all();

        return view('employee.attendance.index', compact('todayAttendance', 'attendances', 'offices'));
    }

    public function checkIn(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $today = Carbon::today();

        $attendance = Attendance::query()
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        if ($attendance) {
            $msg = 'Anda sudah melakukan check in hari ini.';
            return $request->ajax() ? response()->json(['message' => $msg], 422) : back()->with('error', $msg);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $accuracy = $request->accuracy;

        $offices = Office::all();
        $nearestOffice = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = $this->calculateDistance(
                $latitude, $longitude,
                $office->latitude, $office->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestOffice = $office;
            }
        }

        if (! $nearestOffice) {
            $msg = 'Tidak ada kantor terdaftar. Hubungi administrator.';
            return $request->ajax() ? response()->json(['message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($minDistance > $nearestOffice->radius) {
            $msg = 'Anda berada di luar area kantor yang diizinkan. Jarak Anda: ' . number_format($minDistance, 0) . ' meter dari ' . $nearestOffice->name . ' (maksimal radius: ' . $nearestOffice->radius . ' meter).';
            return $request->ajax() ? response()->json(['message' => $msg], 422) : back()->with('error', $msg);
        }

        $now = Carbon::now();
        $officeTime = Carbon::today()->setTime(8, 0);

        $lateMinutes = 0;
        $status = 'hadir';

        if ($now->greaterThan($officeTime)) {
            $lateMinutes = $officeTime->diffInMinutes($now);
            $status = 'terlambat';
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'office_id' => $nearestOffice->id,
            'attendance_date' => $today,
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'distance' => round($minDistance, 2),
        ]);

        $msg = 'Check in berhasil di ' . $nearestOffice->name . ' (Jarak: ' . number_format($minDistance, 0) . ' meter).';

        return $request->ajax()
            ? response()->json(['message' => $msg])
            : redirect()->route('employee.attendance.index')->with('success', $msg);
    }

    public function checkOut(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $attendance = Attendance::query()
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if (! $attendance) {
            $msg = 'Anda belum melakukan check in hari ini.';
            return $request->ajax() ? response()->json(['message' => $msg], 422) : back()->with('error', $msg);
        }

        if ($attendance->check_out) {
            $msg = 'Anda sudah melakukan check out.';
            return $request->ajax() ? response()->json(['message' => $msg], 422) : back()->with('error', $msg);
        }

        $attendance->update([
            'check_out' => Carbon::now()->format('H:i:s'),
        ]);

        $msg = 'Check out berhasil.';

        return $request->ajax()
            ? response()->json(['message' => $msg])
            : redirect()->route('employee.attendance.index')->with('success', $msg);
    }

    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
