@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-650/20 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-xs text-white text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
                    Panel Administrator
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h2>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-xl">
                    Pantau data kehadiran harian, absensi pegawai, serta statistik perusahaan secara terpusat hari ini ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}).
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('admin.attendances.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl font-semibold text-xs sm:text-sm transition-all shadow-sm">
                    <i data-feather="clock" class="w-4 h-4 mr-2"></i>
                    Semua Rekap Absensi
                </a>
            </div>
        </div>
        <!-- Decorative circle -->
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Karyawan -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Karyawan</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1">{{ $totalEmployees }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Terdaftar di sistem</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-feather="users" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Hari Ini</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $presentToday }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">Sudah check-in</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-feather="user-check" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Belum Absen -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Belum Absen</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">{{ $absentToday }}</h3>
                    <p class="text-[11px] text-rose-500 font-medium mt-1">Belum hadir</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-feather="user-x" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Cuti / Izin -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Cuti / Izin</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $leaveToday }}</h3>
                    <p class="text-[11px] text-amber-600 font-medium mt-1">Sedang tidak hadir</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Kehadiran Hari Ini Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Aktivitas Kehadiran Hari Ini</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar presensi karyawan yang masuk pada hari ini.</p>
            </div>
            <a href="{{ route('admin.attendances.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 self-start sm:self-auto inline-flex items-center">
                Lihat Selengkapnya <i data-feather="chevron-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Karyawan</th>
                        <th class="px-5 py-3.5">Departemen</th>
                        <th class="px-5 py-3.5">Jam Masuk</th>
                        <th class="px-5 py-3.5">Jam Keluar</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($att->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-sm">{{ $att->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">{{ $att->user->employee_code ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 text-sm">
                                <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-md">
                                    {{ $att->user->department->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-700 font-medium text-sm">
                                <span class="inline-flex items-center">
                                    <i data-feather="log-in" class="w-3.5 h-3.5 mr-1.5 text-emerald-600"></i>
                                    {{ $att->check_in ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-700 font-medium text-sm">
                                @if($att->check_out)
                                    <span class="inline-flex items-center text-slate-700">
                                        <i data-feather="log-out" class="w-3.5 h-3.5 mr-1.5 text-slate-400"></i>
                                        {{ $att->check_out }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum checkout</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($att->status === 'terlambat')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Hadir
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="calendar" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada aktivitas presensi hari ini.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Data kehadiran yang masuk akan otomatis tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
