<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Departemen;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $positions = Position::with('departement')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('departement', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.positions.index', compact('positions', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departements = Departemen::query()->orderBy('name', 'asc')->get();

        return view('admin.positions.create', compact('departements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        Position::create($request->validated());

        return redirect()->route('management.positions.index')->with('success', 'Position berhasil ditambahkan');
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
    public function edit(Position $position)
    {
        $departements = Departemen::query()->orderBy('name', 'asc')->get();

        return view('admin.positions.edit', compact('position', 'departements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $position->update($request->validated());

        return redirect()->route('management.positions.index')->with('success', 'Position berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('management.positions.index')->with('success', 'Position berhasil dihapus');
    }
}
