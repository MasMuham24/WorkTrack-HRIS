<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AttendanceService
{
    public function getTodayAttendance(): ?Attendance
    {
        return Attendance::query()
            ->with('office')
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', Carbon::today())
            ->first();
    }

    public function getAttendanceHistory(int $perPage = 10)
    {
        return Attendance::query()
            ->with('office')
            ->where('user_id', Auth::id())
            ->latest('attendance_date')
            ->paginate($perPage);
    }

    public function getOffices()
    {
        return Office::all();
    }

    public function checkIn(float $latitude, float $longitude, ?float $accuracy = null): Attendance
    {
        $today = Carbon::today();

        $existingAttendance = Attendance::query()
            ->where('user_id', Auth::id())
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingAttendance) {
            throw new RuntimeException(
                'Anda sudah melakukan check in hari ini.'
            );
        }

        $nearestOffice = $this->findNearestOffice(
            $latitude,
            $longitude
        );

        if (! $nearestOffice) {
            throw new RuntimeException(
                'Tidak ada kantor terdaftar. Hubungi administrator.'
            );
        }

        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            (float) $nearestOffice->latitude,
            (float) $nearestOffice->longitude
        );

        if ($distance > $nearestOffice->radius) {
            throw new RuntimeException(
                'Anda berada di luar area kantor yang diizinkan. '.
                'Jarak Anda: '.
                number_format($distance, 0).
                ' meter dari '.
                $nearestOffice->name.
                ' (maksimal radius: '.
                $nearestOffice->radius.
                ' meter).'
            );
        }

        $now = Carbon::now();
        $scheduledCheckIn = Carbon::today()->setTimeFromTimeString(
            $nearestOffice->check_in_time
        );
        $toleranceEnd = $scheduledCheckIn->copy()->addMinutes(5);
        $lateSeconds = $now->getTimestamp() - $scheduledCheckIn->getTimestamp();

        $lateMinutes = 0;
        $status = 'hadir';

        if ($now->greaterThan($toleranceEnd)) {
            $lateMinutes = max(0, intdiv($lateSeconds, 60));
            $status = 'terlambat';
        }

        return Attendance::create([
            'user_id' => Auth::id(),
            'office_id' => $nearestOffice->id,
            'attendance_date' => $today,
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'distance' => round($distance, 2),
        ]);
    }

    public function getScheduledCheckOutAt(Attendance $attendance): ?Carbon
    {
        if (! $attendance->office) {
            return null;
        }

        return Carbon::today()->setTimeFromTimeString(
            $attendance->office->check_out_time
        );
    }

    public function checkOut(float $latitude, float $longitude, ?float $accuracy = null): Attendance
    {
        $attendance = $this->getTodayAttendance();

        if (! $attendance) {
            throw new RuntimeException(
                'Anda belum melakukan check in hari ini.'
            );
        }

        if ($attendance->check_out) {
            throw new RuntimeException(
                'Anda sudah melakukan check out.'
            );
        }

        $scheduledCheckOut = $this->getScheduledCheckOutAt($attendance);

        if (! $scheduledCheckOut) {
            throw new RuntimeException(
                'Jadwal pulang kantor tidak ditemukan. Hubungi administrator.'
            );
        }

        $now = Carbon::now();

        if ($now->lessThan($scheduledCheckOut)) {
            throw new RuntimeException(
                'Check out belum dapat dilakukan. Jam pulang kantor pukul '.
                $scheduledCheckOut->format('H:i').
                ' WIB.'
            );
        }

        $attendance->update([
            'check_out' => $now->format('H:i:s'),
        ]);

        return $attendance->fresh('office');
    }

    private function findNearestOffice(float $latitude, float $longitude): ?Office
    {
        $offices = Office::all();
        $nearestOffice = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($offices as $office) {
            $distance = $this->calculateDistance($latitude, $longitude, (float) $office->latitude, (float) $office->longitude);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestOffice = $office;
            }
        }

        return $nearestOffice;
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
