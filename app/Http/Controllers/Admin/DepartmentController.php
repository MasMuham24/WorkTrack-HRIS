<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Departemen;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $departements = Departemen::with('office')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.departements.index', compact('departements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $offices = Office::query()->orderBy('name', 'asc')->get();

        return view('admin.departements.create', compact('offices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Departemen::create($request->validated());

        return redirect()->route('management.departments.index')->with('success', 'Departemen baru berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departemen $departemen): View
    {
        $departemen->loadMissing('office');

        return view('admin.departements.edit', compact('departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Departemen $departemen): RedirectResponse
    {
        $departemen->update($request->safe()->only(['name', 'description']));

        return redirect()->route('management.departments.index')->with('success', 'Data departemen berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departemen $departemen): RedirectResponse
    {
        $departemen->delete();

        return redirect()->route('management.departments.index')->with('success', 'Data departemen berhasil dihapus');
    }
}
