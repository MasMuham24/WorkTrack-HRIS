@extends('layouts.app')

@section('title', 'Tambah Kantor')
@section('header_title', 'Manajemen Kantor')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Tambah Kantor Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tentukan titik koordinat kantor dan radius presensi (geofencing).</p>
        </div>
        <div>
            <a href="{{ route('management.offices.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <form action="{{ route('management.offices.store') }}" method="POST">
            @csrf
            
            <div class="p-5 sm:p-8 space-y-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Kantor <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Contoh: Kantor Pusat Jakarta, Cabang Bandung">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="latitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Latitude <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm font-mono text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: -6.1947000">
                        @error('latitude')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Longitude <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm font-mono text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: 106.8241200">
                        @error('longitude')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="radius" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Radius Toleransi (Meter) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="radius" id="radius" value="{{ old('radius', 100) }}" required min="10" max="10000"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Contoh: 100">
                    <p class="text-[11px] text-slate-400 mt-1">Jarak maksimum toleransi posisi karyawan dari titik kantor agar presensi diterima.</p>
                    @error('radius')
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
                    Simpan Kantor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
