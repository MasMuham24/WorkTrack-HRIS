@extends('layouts.app')

@section('title', 'Rekap Absensi Karyawan')
@section('header_title', 'Manajemen Absensi')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Rekap Absensi Karyawan</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau kehadiran, jam masuk, jam keluar, dan status keterlambatan seluruh karyawan.</p>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Karyawan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-feather="search" class="w-3.5 h-3.5"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Nama / Kode / Username"
                        class="block w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400 bg-white">
                </div>
            </div>

            <!-- Date -->
            <div>
                <label for="date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal</label>
                <input type="date" name="date" id="date" value="{{ $date ?? '' }}"
                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" id="status"
                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ ($status ?? '') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ ($status ?? '') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="cuti" {{ ($status ?? '') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="sakit" {{ ($status ?? '') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ ($status ?? '') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <!-- Department -->
            <div>
                <label for="departement_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Departemen</label>
                <select name="departement_id" id="departement_id"
                    class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ ($departementId ?? '') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2 pt-1 sm:pt-0">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition-colors">
                    <i data-feather="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                    Terapkan
                </button>
                <a href="{{ route('admin.attendances.index') }}" class="inline-flex items-center justify-center px-3 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 rounded-xl text-xs sm:text-sm font-semibold transition-colors" title="Reset Filter">
                    <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[780px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Karyawan</th>
                        <th class="px-5 py-3.5">Departemen & Jabatan</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Jam Masuk</th>
                        <th class="px-5 py-3.5">Jam Keluar</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Keterlambatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($att->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $att->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $att->user->employee_code ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                <div class="font-medium text-slate-800">{{ $att->user->department->name ?? '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $att->user->position->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-700 font-medium">
                                {{ $att->check_in ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-700">
                                {{ $att->check_out ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($att->status === 'terlambat')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Terlambat
                                    </span>
                                @elseif($att->status === 'hadir')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Hadir
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                @if($att->late_minutes > 0)
                                    <span class="text-amber-600 font-bold">{{ $att->late_minutes }} menit</span>
                                @else
                                    <span class="text-slate-400">Tepat waktu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Tidak ada data absensi yang sesuai filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
