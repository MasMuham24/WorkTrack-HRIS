@extends('layouts.app')

@section('title', 'Pengajuan Cuti / Izin')
@section('header_title', 'Pengajuan Cuti & Izin')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Daftar Pengajuan Cuti & Izin</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola dan pantau status permohonan cuti atau izin Anda.</p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('employee.leave-requests.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Buat Pengajuan Baru
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Total</p>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-0.5">{{ $leaveRequests->total() }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i data-feather="file-text" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Pending</p>
                <p class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-0.5">{{ $totalPending }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-feather="clock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Disetujui</p>
                <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $totalApproved }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-feather="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Ditolak</p>
                <p class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-0.5">{{ $totalRejected }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-feather="x-circle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Tanggal Pengajuan</th>
                        <th class="px-5 py-3.5">Jenis</th>
                        <th class="px-5 py-3.5">Rentang Tanggal</th>
                        <th class="px-5 py-3.5">Durasi</th>
                        <th class="px-5 py-3.5">Keterangan</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($leaveRequests as $req)
                        @php
                            $start = \Carbon\Carbon::parse($req->start_date);
                            $end = \Carbon\Carbon::parse($req->end_date);
                            $duration = $start->diffInDays($end) + 1;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-800 font-medium text-xs sm:text-sm">
                                {{ $req->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                @if($req->leave_type === 'cuti')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Cuti Tahunan
                                    </span>
                                @elseif($req->leave_type === 'sakit')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        Izin Sakit
                                    </span>
                                @elseif($req->leave_type === 'penting')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        Izin Penting
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-slate-100 text-slate-700">
                                        Lainnya
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 text-xs sm:text-sm">
                                {{ $start->translatedFormat('d M Y') }} s/d {{ $end->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-800 font-bold text-xs sm:text-sm">
                                {{ $duration }} Hari
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate text-xs sm:text-sm">
                                {{ $req->reason }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada riwayat pengajuan cuti atau izin.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaveRequests->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
