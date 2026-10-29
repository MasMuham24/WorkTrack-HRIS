@extends('layouts.app')

@section('title', 'Laporan Rekap Absensi')
@section('header_title', 'Laporan Absensi')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Form -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Laporan Rekap Absensi Bulanan</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Rekapitulasi kehadiran seluruh pegawai untuk periode <strong>{{ $months[$month] ?? '' }} {{ $year }}</strong>.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.attendance') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 max-w-2xl">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Bulan</label>
                <select name="month" class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    @foreach($months as $val => $label)
                        <option value="{{ $val }}" {{ $val == $month ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Pilih Tahun</label>
                <select name="year" class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition">
                    <i data-feather="filter" class="w-4 h-4 mr-1.5"></i>
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Stats Cards Summary for this month -->
    @php
        $totPresent = $summary->sum('present');
        $totLate = $summary->sum('late');
        $totCuti = $summary->sum('cuti');
        $totSick = $summary->sum('sick');
        $totAbsent = $summary->sum('absent');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Hadir</span>
            <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $totPresent }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Terlambat</span>
            <p class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-0.5">{{ $totLate }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Cuti</span>
            <p class="text-xl sm:text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $totCuti }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Sakit</span>
            <p class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-0.5">{{ $totSick }}</p>
        </div>
        <div class="col-span-2 sm:col-span-1 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Alpha</span>
            <p class="text-xl sm:text-2xl font-extrabold text-red-600 mt-0.5">{{ $totAbsent }}</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Nama Karyawan</th>
                        <th class="px-5 py-3.5 text-center">Hadir</th>
                        <th class="px-5 py-3.5 text-center">Terlambat</th>
                        <th class="px-5 py-3.5 text-center">Cuti</th>
                        <th class="px-5 py-3.5 text-center">Sakit</th>
                        <th class="px-5 py-3.5 text-center">Alpha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($summary as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800 text-sm">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->present > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-400' }}">
                                    {{ $item->present }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->late > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'text-slate-400' }}">
                                    {{ $item->late }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->cuti > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-slate-400' }}">
                                    {{ $item->cuti }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->sick > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'text-slate-400' }}">
                                    {{ $item->sick }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->absent > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'text-slate-400' }}">
                                    {{ $item->absent }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada data absensi untuk periode ini.</p>
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