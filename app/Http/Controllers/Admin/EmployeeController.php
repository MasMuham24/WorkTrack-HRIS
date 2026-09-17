<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Departemen;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $departementId = $request->departement_id;
        $status = $request->status;

        $employees = User::query()
            ->with(['department', 'position'])
            ->employees()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employee_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($departementId, fn ($query) => $query->where('departement_id', $departementId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $departments = Departemen::orderBy('name')->get();

        return view('management.employees.index', compact('employees', 'search', 'departments', 'departementId', 'status'));
    }

    public function create()
    {
        $departments = Departemen::query()->orderBy('name', 'asc')->get();
        $positions = Position::query()->orderBy('name', 'asc')->get();

        return view('management.employees.create', compact('departments', 'positions'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        User::create([
            'employee_code' => $request->employee_code,
            'departement_id' => $request->departement_id,
            'position_id' => $request->position_id,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => 'employee',
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'status' => $request->status,
            'password' => $request->password,
        ]);

        return redirect()
            ->route('management.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(User $employee)
    {
        abort_if(! in_array($employee->role, ['employee', 'hr']), 404);

        $departments = Departemen::query()->orderBy('name', 'asc')->get();
        $positions = Position::query()->orderBy('name', 'asc')->get();
        return view('management.employees.edit', compact(
            'employee',
            'departments',
            'positions'
        ));
    }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        abort_if(! in_array($employee->role, ['employee', 'hr']), 404);

        $data = [
            'employee_code' => $request->employee_code,
            'departement_id' => $request->departement_id,
            'position_id' => $request->position_id,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $employee->update($data);

        return redirect()
            ->route('management.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        abort_if(! in_array($employee->role, ['employee', 'hr']), 404);

        $employee->delete();

        return redirect()
            ->route('management.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
