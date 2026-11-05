<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $date = $request->date;
        $status = $request->status;
        $departementId = $request->departement_id;

        $attendances = Attendance::with(['user.department', 'user.position'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('attendance_date', $date);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($departementId, function ($query) use ($departementId) {
                $query->whereHas('user', function ($q) use ($departementId) {
                    $q->where('departement_id', $departementId);
                });
            })
            ->latest('attendance_date')
            ->paginate(10)
            ->withQueryString();

        $departments = Departemen::query()->orderBy('name', 'asc')->get();

        return view('admin.attendances.index', compact('attendances', 'departments', 'search', 'date', 'status', 'departementId'));
    }
}
