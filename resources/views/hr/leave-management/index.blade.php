@extends('layouts.app')

@section('title', 'Pengajuan Cuti & Izin')
@section('header_title', 'Pengajuan Cuti & Izin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengajuan Cuti & Izin</h2>
            <p class="text-sm text-slate-500 mt-1">Approval dan riwayat pengajuan cuti & izin karyawan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Cuti</h3>
                <span class="text-xs text-slate-500">{{ $pendingCuti }} pending</span>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Pending</p>
                    <p class="text-lg font-bold text-amber-600">{{ $pendingCuti }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Disetujui</p>
                    <p class="text-lg font-bold text-emerald-600">{{ $approvedCuti }}</p>
                </div>
                <div class="bg-rose-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Ditolak</p>
                    <p class="text-lg font-bold text-rose-600">{{ $rejectedCuti }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($cutiRequests as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-2">
                                            {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-800">{{ $item->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d/m') }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Pending</span>
                                    @elseif($item->status === 'approved')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <div class="flex items-center gap-1">
                                            <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">ACC</button>
                                            </form>
                                            <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 hover:bg-rose-200">REJ</button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="text-slate-400 hover:text-indigo-600">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-slate-400 text-xs">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cutiRequests->hasPages())
                <div class="mt-2">{{ $cutiRequests->links() }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Izin</h3>
                <span class="text-xs text-slate-500">{{ $pendingIzin }} pending</span>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Pending</p>
                    <p class="text-lg font-bold text-amber-600">{{ $pendingIzin }}</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Disetujui</p>
                    <p class="text-lg font-bold text-emerald-600">{{ $approvedIzin }}</p>
                </div>
                <div class="bg-rose-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Ditolak</p>
                    <p class="text-lg font-bold text-rose-600">{{ $rejectedIzin }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($izinRequests as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-2">
                                            {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-800">{{ $item->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600">
                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d/m') }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Pending</span>
                                    @elseif($item->status === 'approved')
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Disetujui</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($item->status === 'pending')
                                        <div class="flex items-center gap-1">
                                            <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">ACC</button>
                                            </form>
                                            <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 hover:bg-rose-200">REJ</button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="text-slate-400 hover:text-indigo-600">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-slate-400 text-xs">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($izinRequests->hasPages())
                <div class="mt-2">{{ $izinRequests->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection