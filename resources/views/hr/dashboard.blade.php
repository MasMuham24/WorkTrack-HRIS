@extends('layouts.app')

@section('title', 'Dashboard HR')
@section('header_title', 'Dashboard HR')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-650/20 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-white/15 backdrop-blur-xs text-white text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
                    Divisi Human Resources
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h2>
                <p class="text-blue-100 text-xs sm:text-sm mt-1 max-w-xl">
                    Kelola data kepegawaian, verifikasi pengajuan cuti & izin karyawan, serta pantau kehadiran hari ini.
                </p>
            </div>
            <div class="shrink-0 flex flex-wrap items-center gap-2">
                <a href="{{ route('hr.leave-management.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white text-indigo-700 hover:bg-blue-50 rounded-xl font-semibold text-xs sm:text-sm transition-all shadow-sm">
                    <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                    Approval Cuti & Izin
                </a>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Total Karyawan -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Total Karyawan</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-1">{{ $usersCount }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5 hidden sm:block">Karyawan terdaftar</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-feather="users" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Karyawan Aktif -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Karyawan Aktif</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $activeEmployeesCount }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-0.5 hidden sm:block">Status aktif</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-feather="check-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Tidak Aktif -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Nonaktif</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-rose-600 mt-1">{{ $inactiveUsersCount }}</h3>
                    <p class="text-[11px] text-rose-500 font-medium mt-0.5 hidden sm:block">Status nonaktif</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i data-feather="user-x" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Cuti Menunggu -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Cuti Pending</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $pendingLeaveRequestsCount }}</h3>
                    <p class="text-[11px] text-amber-600 font-medium mt-0.5 hidden sm:block">Perlu review</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-feather="clock" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Cuti Disetujui -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Cuti Disetujui</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $approvedLeaveRequestsCount }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-0.5 hidden sm:block">Total disetujui</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                    <i data-feather="award" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Departemen -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Departemen</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-purple-600 mt-1">{{ $departmentsCount }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5 hidden sm:block">Divisi aktif</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i data-feather="briefcase" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Posisi -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Jabatan / Posisi</p>
                    <h3 class="text-xl sm:text-3xl font-extrabold text-indigo-600 mt-1">{{ $positionsCount }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5 hidden sm:block">Struktur jabatan</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-feather="bookmark" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
            </div>
        </div>

        <!-- Link ke Presensi HR -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
            <div>
                <p class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Presensi Pribadi</p>
                <p class="text-xs text-slate-500 mt-1">Lakukan check-in & check-out harian Anda</p>
            </div>
            <div class="mt-3">
                <a href="{{ route('hr.attendance.index') }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Buka Absensi Saya <i data-feather="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Approval Queues Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pengajuan Cuti Menunggu -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-feather="clock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-800">Pengajuan Cuti Menunggu</h2>
                        <p class="text-[11px] text-slate-400">Verifikasi permohonan cuti tahunan</p>
                    </div>
                </div>
                <a href="{{ route('hr.leave-management.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Semua
                </a>
            </div>
            <div class="p-4 sm:p-5">
                @if($pendingLeaves->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingLeaves as $leave)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/70 gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $leave->user->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        <span class="inline-block px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 font-medium uppercase text-[10px] mr-1">
                                            {{ $leave->leave_type }}
                                        </span>
                                        {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                    <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-xs">
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-slate-400">
                        <i data-feather="check-circle" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm font-medium text-slate-600">Tidak ada pengajuan cuti yang tertunda.</p>
                        <p class="text-xs text-slate-400 mt-0.5">Semua permohonan sudah diproses.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Access Box -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-feather="sliders" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-800">Aksi Cepat Manajemen HR</h2>
                        <p class="text-[11px] text-slate-400">Pintasan menu administrasi karyawan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('management.employees.create') }}" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group flex items-start space-x-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Tambah Karyawan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Daftarkan akun karyawan baru</p>
                        </div>
                    </a>

                    <a href="{{ route('reports.attendance') }}" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group flex items-start space-x-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Laporan Bulanan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Rekap absensi bulanan perusahaan</p>
                        </div>
                    </a>

                    <a href="{{ route('management.departments.index') }}" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group flex items-start space-x-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i data-feather="briefcase" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Data Departemen</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kelola divisi dan unit kerja</p>
                        </div>
                    </a>

                    <a href="{{ route('management.positions.index') }}" class="p-4 rounded-xl border border-slate-200/70 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group flex items-start space-x-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <i data-feather="award" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600">Data Jabatan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Kelola tingkatan dan posisi kerja</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>E-Absensi v2.0</span>
                <span>PT Bintang Anugrah</span>
            </div>
        </div>
    </div>
</div>
@endsection