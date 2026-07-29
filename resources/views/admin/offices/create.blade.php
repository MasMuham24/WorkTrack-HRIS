@extends('layouts.app')
@section('title', 'Tambah Kantor')
@section('header_title', 'Manajemen Kantor')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-10rem)]">
    <div class="w-full max-w-2xl">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Tambah Kantor Baru</h2>
                <p class="text-sm text-slate-500 mt-1">Masukkan informasi kantor yang akan ditambahkan.</p>
            </div>
            <a href="{{ route('management.offices.index') }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-lg px-4 py-2.5 shadow-sm hover:bg-slate-50 transition-colors shrink-0">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('management.offices.store') }}" method="POST">
                @csrf
                
                <div class="p-6 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Kantor <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-2.5 border placeholder-slate-400"
                            placeholder="Contoh: Kantor Pusat, Kantor Cabang, Kantor Regional">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="latitude" class="block text-sm font-medium text-slate-700 mb-1">Latitude <span class="text-red-500">*</span></label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" required
                            class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-2.5 border placeholder-slate-400"
                            placeholder="Contoh: -6.1947000">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-slate-700 mb-1">Longitude <span class="text-red-500">*</span></label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" required
                            class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-2.5 border placeholder-slate-400"
                            placeholder="Contoh: 106.8241200">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="radius" class="block text-sm font-medium text-slate-700 mb-1">Radius (Meter) <span class="text-red-500">*</span></label>
                        <input type="number" name="radius" id="radius" value="{{ old('radius') }}" required min="10" max="5000"
                            class="block w-full border-slate-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-2.5 border placeholder-slate-400"
                            placeholder="Contoh: 100">
                        @error('radius')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="reset" class="px-4 py-2 bg-white border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
