@extends('layouts.app')

@section('title', 'Tambah Jabatan')
@section('header_title', 'Manajemen Jabatan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Tambah Jabatan Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tentukan nama jabatan dan hubungkan ke departemen yang sesuai.</p>
        </div>
        <div>
            <a href="{{ route('management.positions.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <form action="{{ route('management.positions.store') }}" method="POST">
            @csrf
            
            <div class="p-5 sm:p-8 space-y-5">
                <!-- Nama Jabatan -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Contoh: Senior Developer, Staff HRD, Manager Operasional">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departemen -->
                <div>
                    <label for="departement_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Departemen Naungan <span class="text-rose-500">*</span>
                    </label>
                    <select name="departement_id" id="departement_id" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departements as $dept)
                            <option value="{{ $dept->id }}" {{ old('departement_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('departement_id')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="4"
                        class="block w-full p-3.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Deskripsi tugas dan tanggung jawab jabatan ini (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="reset" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Reset
                </button>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                    Simpan Jabatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
