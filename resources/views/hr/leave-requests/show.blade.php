@extends('layouts.app')

@section('title', 'Detail Pengajuan Cuti')
@section('header_title', 'Detail Pengajuan Cuti')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Detail Permohonan Cuti</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Informasi lengkap pengajuan cuti atau izin karyawan.</p>
        </div>
        <div>
            <a href="{{ route('hr.leave-requests.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <!-- Karyawan -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pemohon</span>
                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $leaveRequest->user->name ?? '-' }}</p>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $leaveRequest->user->employee_code ?? '-' }}</p>
                </div>

                <!-- Jenis Cuti -->
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
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">Lainnya</span>
                        @endif
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rentang Waktu</span>
                    <p class="text-xs sm:text-sm font-bold text-slate-800 mt-1">
                        {{ \Carbon\Carbon::parse($leaveRequest->start_date)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($leaveRequest->end_date)->translatedFormat('d M Y') }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($leaveRequest->start_date)->diffInDays($leaveRequest->end_date) + 1 }} Hari Kerja
                    </p>
                </div>

                <!-- Status -->
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Permohonan</span>
                    <div class="mt-1">
                        @if($leaveRequest->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                Menunggu Persetujuan
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
            </div>

            <!-- Approver -->
            @if($leaveRequest->approver)
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Diproses Oleh</span>
                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $leaveRequest->approver->name }}</p>
                </div>
            @endif

            <!-- Alasan -->
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alasan / Keterangan</span>
                <p class="text-xs sm:text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $leaveRequest->reason }}</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-end">
            <a href="{{ route('hr.leave-requests.index') }}" class="px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection