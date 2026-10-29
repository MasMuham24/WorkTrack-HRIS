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
            <!-- Export Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('reports.attendance.export.excel', ['month' => $month, 'year' => $year]) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition-colors">
                    <i data-feather="file-text" class="w-4 h-4 mr-1.5"></i>
                    Ekspor Excel (.xlsx)
                </a>
                <a href="{{ route('reports.attendance.export.csv', ['month' => $month, 'year' => $year]) }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl shadow-xs transition-colors">
                    <i data-feather="download" class="w-4 h-4 mr-1.5 text-slate-500"></i>
                    Ekspor CSV (.csv)
                </a>
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
        $totTotalPresent = $summary->sum('total_present');
        $totCuti = $summary->sum('cuti');
        $totSick = $summary->sum('sick');
        $totAbsent = $summary->sum('absent');
        $totLateMins = $summary->sum('late_minutes');
        $totWorkMins = $summary->sum('total_work_minutes');
        $totWorkHours = intdiv($totWorkMins, 60);
        $totWorkRemainMins = $totWorkMins % 60;
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</span>
            <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-0.5">{{ $totPresent }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Terlambat</span>
            <p class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-0.5">{{ $totLate }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Hari Masuk</span>
            <p class="text-xl sm:text-2xl font-extrabold text-indigo-600 mt-0.5">{{ $totTotalPresent }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cuti & Sakit</span>
            <p class="text-xl sm:text-2xl font-extrabold text-blue-600 mt-0.5">{{ $totCuti + $totSick }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $totCuti }} Cuti / {{ $totSick }} Sakit</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Alpha</span>
            <p class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-0.5">{{ $totAbsent }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Keterlambatan</span>
            <p class="text-xl sm:text-2xl font-extrabold text-amber-700 mt-0.5">{{ number_format($totLateMins) }} <span class="text-xs font-semibold text-slate-400">mnt</span></p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Karyawan</th>
                        <th class="px-5 py-3.5">Departemen & Jabatan</th>
                        <th class="px-4 py-3.5 text-center">Tepat Waktu</th>
                        <th class="px-4 py-3.5 text-center">Terlambat</th>
                        <th class="px-4 py-3.5 text-center">Total Masuk</th>
                        <th class="px-4 py-3.5 text-center">Cuti</th>
                        <th class="px-4 py-3.5 text-center">Sakit</th>
                        <th class="px-4 py-3.5 text-center">Alpha</th>
                        <th class="px-4 py-3.5 text-center">Keterlambatan</th>
                        <th class="px-5 py-3.5 text-center">Jam Kerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($summary as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800 text-sm">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="leading-tight">{{ $item->name }}</p>
                                        <span class="text-xs font-mono font-normal text-slate-400">{{ $item->employee_code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                <p class="font-semibold text-slate-700">{{ $item->department }}</p>
                                <span class="text-slate-400">{{ $item->position }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->present > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'text-slate-400' }}">
                                    {{ $item->present }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->late > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'text-slate-400' }}">
                                    {{ $item->late }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->total_present > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-slate-400' }}">
                                    {{ $item->total_present }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->cuti > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-slate-400' }}">
                                    {{ $item->cuti }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->sick > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'text-slate-400' }}">
                                    {{ $item->sick }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-bold {{ $item->absent > 0 ? 'bg-red-50 text-red-700 border border-red-200' : 'text-slate-400' }}">
                                    {{ $item->absent }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-center text-xs font-medium text-slate-700">
                                @if($item->late_minutes > 0)
                                    <span class="text-amber-700 font-bold">{{ $item->late_minutes }} mnt</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-center text-xs font-medium text-slate-700">
                                @if($item->total_work_minutes > 0)
                                    <span class="font-semibold">{{ $item->work_hours_formatted }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400 text-sm">
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
