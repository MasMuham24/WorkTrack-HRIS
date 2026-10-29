@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('header_title', 'Dashboard Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-650/20 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-xs text-white text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
                    Portal Karyawan
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h2>
                <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-xl">
                    Hari ini {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}. Jangan lupa melakukan presensi tepat waktu.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('employee.attendance.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-sm">
                    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                    Buka Absensi Hari Ini
                </a>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Kehadiran Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Bulan Ini</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1">{{ $attendanceThisMonth }} <span class="text-base font-medium text-slate-400">Hari</span></h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">Total kehadiran tercatat</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Terlambat Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $lateThisMonth }} <span class="text-base font-medium text-slate-400">Kali</span></h3>
                    <p class="text-[11px] text-amber-600 font-medium mt-1">Bulan berjalan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-feather="alert-circle" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Sisa Cuti -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hak Cuti</p>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">12 <span class="text-base font-medium text-slate-400">Hari</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Cuti tahunan tersedia</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-feather="sun" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Two Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Presensi Hari Ini Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800">Presensi Hari Ini</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    @if($todayAttendance)
                        @if($todayAttendance->status === 'terlambat')
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-full uppercase">
                                Terlambat
                            </span>
                        @else
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-full uppercase">
                                Hadir Tepat Waktu
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold rounded-full">
                            Belum Absen
                        </span>
                    @endif
                </div>

                <div class="p-5 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Check In Card -->
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/70 flex items-center space-x-3.5">
                            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                <i data-feather="log-in" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Masuk</p>
                                <p class="text-base font-bold text-slate-800 mt-0.5">
                                    {{ $todayAttendance->check_in ?? 'Belum Check In' }}
                                </p>
                            </div>
                        </div>

                        <!-- Check Out Card -->
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/70 flex items-center space-x-3.5">
                            <div class="w-11 h-11 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                                <i data-feather="log-out" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Keluar</p>
                                <p class="text-base font-bold text-slate-800 mt-0.5">
                                    {{ $todayAttendance->check_out ?? 'Belum Check Out' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($todayAttendance && $todayAttendance->office)
                        <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-xs text-blue-800 flex items-center space-x-2">
                            <i data-feather="map-pin" class="w-4 h-4 text-blue-600 shrink-0"></i>
                            <span>Lokasi Terdaftar: <strong>{{ $todayAttendance->office->name }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-5 sm:p-6 border-t border-slate-100 bg-slate-50/40">
                <a href="{{ route('employee.attendance.index') }}" class="w-full flex items-center justify-center py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md shadow-indigo-600/20 transition-all">
                    <i data-feather="camera" class="w-4 h-4 mr-2"></i>
                    Buka Halaman Presensi Lengkap
                </a>
            </div>
        </div>

        <!-- Riwayat Kehadiran Terakhir Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800">Riwayat Terakhir</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Catatan presensi 5 hari kerja sebelumnya.</p>
                    </div>
                    <a href="{{ route('employee.attendance.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Selengkapnya
                    </a>
                </div>

                <div class="p-4 sm:p-6">
                    @if($recentAttendances->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentAttendances as $att)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70 gap-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                            <i data-feather="calendar" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-slate-800">
                                                {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('l, d M Y') }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $att->check_in ?? '-' }} s/d {{ $att->check_out ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="self-end sm:self-auto">
                                        @if($att->status === 'terlambat')
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Hadir
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 text-center text-slate-400">
                            <i data-feather="inbox" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-slate-500">Belum ada riwayat kehadiran tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-6 border-t border-slate-100 bg-slate-50/40">
                <a href="{{ route('employee.leave-requests.create') }}" class="w-full flex items-center justify-center py-2.5 px-4 bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/20 text-slate-700 rounded-xl font-semibold text-xs sm:text-sm transition-all">
                    <i data-feather="calendar" class="w-4 h-4 mr-2 text-indigo-600"></i>
                    Ajukan Cuti atau Izin Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
