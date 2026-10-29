@extends('layouts.app')

@section('title', 'Pengajuan Cuti')
@section('header_title', 'Pengajuan Cuti')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Daftar Pengajuan Cuti Karyawan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Approval dan riwayat pengajuan cuti seluruh karyawan.</p>
        </div>
        <div>
            <a href="{{ route('hr.leave-management.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-xl px-3.5 py-2 transition-colors">
                <i data-feather="grid" class="w-4 h-4 mr-1.5"></i>
                Lihat Panel Live Approval
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Pending</p>
                <p class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-0.5">{{ $pending ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-feather="clock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Disetujui</p>
                <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $approved ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-feather="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Ditolak</p>
                <p class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-0.5">{{ $rejected ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-feather="x-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider truncate">Total</p>
                <p class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-0.5">{{ $total ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                <i data-feather="file-text" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[760px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Karyawan</th>
                        <th class="px-5 py-3.5">Jenis Cuti</th>
                        <th class="px-5 py-3.5">Tanggal Mulai - Selesai</th>
                        <th class="px-5 py-3.5">Durasi</th>
                        <th class="px-5 py-3.5">Alasan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($leaveRequests as $item)
                        @php
                            $duration = \Carbon\Carbon::parse($item->start_date)->diffInDays($item->end_date) + 1;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $item->user->employee_code ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                @if($item->leave_type === 'cuti')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Cuti</span>
                                @elseif($item->leave_type === 'sakit')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">Sakit</span>
                                @elseif($item->leave_type === 'penting')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Izin Penting</span>
                                @else
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-slate-100 text-slate-700">Lainnya</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs sm:text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs sm:text-sm font-bold text-slate-800">
                                {{ $duration }} Hari
                            </td>
                            <td class="px-5 py-3.5 text-xs sm:text-sm text-slate-600 max-w-xs truncate">
                                {{ Str::limit($item->reason, 45) }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($item->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($item->status === 'approved')
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
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                @if($item->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors inline-block" title="Lihat Detail">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">
                                                ACC
                                            </button>
                                        </form>
                                        <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">
                                                REJ
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors inline-block" title="Detail">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada pengajuan cuti yang tercatat.</p>
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