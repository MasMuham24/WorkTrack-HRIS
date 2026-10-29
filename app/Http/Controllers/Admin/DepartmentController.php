<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) :View
    {
        $departements = Departemen::query()
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
        return view('admin.departements.create');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departemen $departemen): View
    {
        return view('admin.departements.edit', compact('departemen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Departemen $departemen): RedirectResponse
    {
        $departemen->update($request->validated());
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
