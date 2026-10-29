@extends('layouts.app')

@section('title', 'Detail Pengajuan Izin / Cuti')
@section('header_title', 'Detail Pengajuan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Detail Permohonan Izin / Cuti</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Informasi status dan rincian permohonan yang diajukan.</p>
        </div>
        <div>
            <a href="{{ route('employee.leave-requests.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Status Alert Banner -->
    @if($leaveRequest->status === 'approved')
        <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-feather="check-circle" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-emerald-900">Pengajuan Telah Disetujui</h3>
                <p class="text-xs text-emerald-700 mt-0.5 leading-relaxed">
                    Permohonan {{ \App\Notifications\LeaveRequestStatusUpdated::getLeaveTypeLabel($leaveRequest->leave_type) }} Anda telah disetujui
                    @if($leaveRequest->approver)
                        oleh <strong>{{ $leaveRequest->approver->name }}</strong>
                    @endif
                    @if($leaveRequest->approved_at)
                        pada {{ $leaveRequest->approved_at->translatedFormat('d F Y, H:i') }} WIB.
                    @else
                        oleh HR.
                    @endif
                </p>
            </div>
        </div>
    @elseif($leaveRequest->status === 'rejected')
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-feather="x-circle" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-rose-900">Pengajuan Ditolak</h3>
                <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                    Permohonan {{ \App\Notifications\LeaveRequestStatusUpdated::getLeaveTypeLabel($leaveRequest->leave_type) }} Anda telah ditolak
                    @if($leaveRequest->approver)
                        oleh <strong>{{ $leaveRequest->approver->name }}</strong>
                    @endif
                    @if($leaveRequest->approved_at)
                        pada {{ $leaveRequest->approved_at->translatedFormat('d F Y, H:i') }} WIB.
                    @else
                        oleh HR.
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-feather="clock" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-bold text-amber-900">Menunggu Tinjauan HR</h3>
                <p class="text-xs text-amber-700 mt-0.5 leading-relaxed">
                    Pengajuan Anda sedang dalam proses peninjauan oleh tim HR. Anda akan menerima notifikasi segera setelah permohonan diproses.
                </p>
            </div>
        </div>
    @endif

    <!-- Detail Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Jenis Pengajuan -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jenis Pengajuan</span>
                    <div class="mt-1">
                        @if($leaveRequest->leave_type === 'cuti')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Cuti Tahunan</span>
                        @elseif($leaveRequest->leave_type === 'sakit')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">Izin Sakit</span>
                        @elseif($leaveRequest->leave_type === 'penting')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Izin Penting</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">Izin Lainnya</span>
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Permohonan</span>
                    <div class="mt-1">
                        @if($leaveRequest->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                Menunggu Tinjauan
                            </span>
                        @elseif($leaveRequest->status === 'approved')
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
                    </div>
                </div>

                <!-- Rentang Waktu -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rentang Waktu</span>
                    @php
                        $start = \Carbon\Carbon::parse($leaveRequest->start_date);
                        $end = \Carbon\Carbon::parse($leaveRequest->end_date);
                        $duration = $start->diffInDays($end) + 1;
                    @endphp
                    <p class="text-xs sm:text-sm font-bold text-slate-800 mt-1">
                        {{ $start->translatedFormat('d M Y') }} s/d {{ $end->translatedFormat('d M Y') }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Total Durasi: <span class="font-bold text-slate-700">{{ $duration }} Hari</span>
                    </p>
                </div>

                <!-- Tanggal Diajukan -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Waktu Diajukan</span>
                    <p class="text-xs sm:text-sm font-bold text-slate-800 mt-1">
                        {{ $leaveRequest->created_at->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5 font-mono">
                        {{ $leaveRequest->created_at->format('H:i') }} WIB
                    </p>
                </div>
            </div>

            <!-- Approver Information jika sudah diproses -->
            @if($leaveRequest->status !== 'pending' && $leaveRequest->approver)
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditinjau & Diproses Oleh</span>
                        <p class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">{{ $leaveRequest->approver->name }} (HR)</p>
                    </div>
                    @if($leaveRequest->approved_at)
                        <div class="text-right">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Waktu Keputusan</span>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $leaveRequest->approved_at->translatedFormat('d M Y, H:i') }} WIB</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Alasan / Keterangan -->
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alasan / Keterangan Pengajuan</span>
                <p class="text-xs sm:text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $leaveRequest->reason }}</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-between items-center">
            <span class="text-xs text-slate-400 font-mono">ID Pengajuan: #{{ $leaveRequest->id }}</span>
            <a href="{{ route('employee.leave-requests.index') }}" class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection

