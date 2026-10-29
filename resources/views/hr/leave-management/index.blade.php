@extends('layouts.app')

@section('title', 'Pengajuan Cuti & Izin')
@section('header_title', 'Pengajuan Cuti & Izin')

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Persetujuan Cuti & Izin Pegawai</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Approval dan riwayat pengajuan cuti & izin karyawan secara live.</p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <span class="text-xs text-slate-500">Update: <span id="last-updated" class="font-semibold text-emerald-600">baru saja</span></span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200" title="Data diperbarui otomatis setiap 10 detik">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Live Sync
            </span>
        </div>
    </div>

    <!-- Dual Column Grid -->
    <div id="leave-request-table" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Kolom Cuti -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Cuti Tahunan</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                        <span id="header-pending-cuti">{{ $pendingCuti }}</span> menunggu
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
                    <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pending</p>
                        <p class="text-lg sm:text-xl font-extrabold text-amber-700 mt-0.5"><span id="pending-cuti-count">{{ $pendingCuti }}</span></p>
                    </div>
                    <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Disetujui</p>
                        <p class="text-lg sm:text-xl font-extrabold text-emerald-700 mt-0.5"><span id="approved-cuti-count">{{ $approvedCuti }}</span></p>
                    </div>
                    <div class="bg-rose-50/70 border border-rose-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-rose-800 uppercase tracking-wider">Ditolak</p>
                        <p class="text-lg sm:text-xl font-extrabold text-rose-700 mt-0.5"><span id="rejected-cuti-count">{{ $rejectedCuti }}</span></p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[340px]">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-3.5 py-3">Karyawan</th>
                                <th class="px-3.5 py-3">Tanggal</th>
                                <th class="px-3.5 py-3">Status</th>
                                <th class="px-3.5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-cuti" class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @forelse($cutiRequests as $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-slate-800 truncate max-w-[120px] sm:max-w-none">{{ $item->user->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap text-xs text-slate-600">
                                        {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/y') }}
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        @if($item->status === 'pending')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                        @elseif($item->status === 'approved')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap text-right">
                                        @if($item->status === 'pending')
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors inline-block" title="Lihat Detail">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">ACC</button>
                                                </form>
                                                <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">REJ</button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors inline-block" title="Detail">
                                                <i data-feather="eye" class="w-4 h-4"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3.5 py-8 text-center text-slate-400 text-xs">Belum ada data pengajuan cuti.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($cutiRequests->hasPages())
                <div class="mt-4 pt-3 border-t border-slate-100">{{ $cutiRequests->links() }}</div>
            @endif
        </div>

        <!-- Kolom Izin -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i data-feather="activity" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Izin & Sakit</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                        <span id="header-pending-izin">{{ $pendingIzin }}</span> menunggu
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">
                    <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Pending</p>
                        <p class="text-lg sm:text-xl font-extrabold text-amber-700 mt-0.5"><span id="pending-izin-count">{{ $pendingIzin }}</span></p>
                    </div>
                    <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Disetujui</p>
                        <p class="text-lg sm:text-xl font-extrabold text-emerald-700 mt-0.5"><span id="approved-izin-count">{{ $approvedIzin }}</span></p>
                    </div>
                    <div class="bg-rose-50/70 border border-rose-200/60 rounded-xl p-3 text-center">
                        <p class="text-[10px] font-bold text-rose-800 uppercase tracking-wider">Ditolak</p>
                        <p class="text-lg sm:text-xl font-extrabold text-rose-700 mt-0.5"><span id="rejected-izin-count">{{ $rejectedIzin }}</span></p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[340px]">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-3.5 py-3">Karyawan</th>
                                <th class="px-3.5 py-3">Tanggal</th>
                                <th class="px-3.5 py-3">Status</th>
                                <th class="px-3.5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-izin" class="divide-y divide-slate-100 text-xs sm:text-sm">
                            @forelse($izinRequests as $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="font-bold text-slate-800 truncate max-w-[120px] sm:max-w-none">{{ $item->user->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap text-xs text-slate-600">
                                        {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/y') }}
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap">
                                        @if($item->status === 'pending')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                        @elseif($item->status === 'approved')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-3 whitespace-nowrap text-right">
                                        @if($item->status === 'pending')
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors inline-block" title="Lihat Detail">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">ACC</button>
                                                </form>
                                                <form action="{{ route('hr.leave-requests.update', $item->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">REJ</button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('hr.leave-requests.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors inline-block" title="Detail">
                                                <i data-feather="eye" class="w-4 h-4"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3.5 py-8 text-center text-slate-400 text-xs">Belum ada data pengajuan izin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($izinRequests->hasPages())
                <div class="mt-4 pt-3 border-t border-slate-100">{{ $izinRequests->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .stat-flash {
        animation: stat-pulse 0.6s ease;
    }
    @keyframes stat-pulse {
        0% { background-color: rgba(16, 185, 129, 0.35); }
        100% { background-color: transparent; }
    }
    .new-request {
        animation: new-request-highlight 2.5s ease;
    }
    @keyframes new-request-highlight {
        0% { background-color: rgba(99, 102, 241, 0.25); }
        100% { background-color: transparent; }
    }
    .toast-enter {
        animation: toast-slide-in 0.3s ease;
    }
    .toast-exit {
        animation: toast-fade-out 0.4s ease forwards;
    }
    @keyframes toast-slide-in {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes toast-fade-out {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>
<script>
    (function () {
        let latestLeaveRequestId = null;
        let isFirstLoad = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const updateUrl = "{{ route('hr.leave-requests.update', ':ID') }}";
        const notificationShowUrl = "{{ route('hr.leave-requests.show', ':ID') }}";

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDate(dateString) {
            const parts = String(dateString || '').slice(0, 10).split('-');
            if (parts.length !== 3) {
                return '-';
            }
            return parts[2] + '/' + parts[1];
        }

        function flashElement(element) {
            if (!element) return;
            element.classList.remove('stat-flash');
            void element.offsetWidth;
            element.classList.add('stat-flash');
        }

        function updateStatistics(data) {
            const fields = [
                ['pending-cuti-count', 'pendingCuti'],
                ['approved-cuti-count', 'approvedCuti'],
                ['rejected-cuti-count', 'rejectedCuti'],
                ['pending-izin-count', 'pendingIzin'],
                ['approved-izin-count', 'approvedIzin'],
                ['rejected-izin-count', 'rejectedIzin'],
                ['header-pending-cuti', 'pendingCuti'],
                ['header-pending-izin', 'pendingIzin']
            ];

            fields.forEach(function (field) {
                const element = document.getElementById(field[0]);
                if (!element) return;
                const value = data[field[1]];
                if (element.textContent !== String(value)) {
                    element.textContent = value;
                    flashElement(element);
                }
            });
        }

        function buildRequestRow(request) {
            const actionUrl = updateUrl.replace(':ID', request.id);
            const initial = escapeHtml((request.name || 'U').charAt(0)).toUpperCase();
            const name = escapeHtml(request.name);
            const dateRange = formatDate(request.start_date) + ' - ' + formatDate(request.end_date);

            return '' +
                '<tr class="new-request hover:bg-slate-50 transition-colors">' +
                '<td class="px-3.5 py-3 whitespace-nowrap">' +
                '<div class="flex items-center space-x-2">' +
                '<div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">' + initial + '</div>' +
                '<span class="font-bold text-slate-800">' + name + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="px-3.5 py-3 whitespace-nowrap text-xs text-slate-600">' + dateRange + '</td>' +
                '<td class="px-3.5 py-3 whitespace-nowrap">' +
                '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Pending</span>' +
                '</td>' +
                '<td class="px-3.5 py-3 whitespace-nowrap text-right">' +
                '<div class="inline-flex items-center gap-1.5">' +
                '<a href="' + notificationShowUrl.replace(':ID', request.id) + '" class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg transition-colors inline-block" title="Lihat Detail">' +
                '<i data-feather="eye" class="w-4 h-4"></i>' +
                '</a>' +
                '<form action="' + actionUrl + '" method="POST" class="m-0">' +
                '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                '<input type="hidden" name="_method" value="PUT">' +
                '<input type="hidden" name="status" value="approved">' +
                '<button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs">ACC</button>' +
                '</form>' +
                '<form action="' + actionUrl + '" method="POST" class="m-0">' +
                '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                '<input type="hidden" name="_method" value="PUT">' +
                '<input type="hidden" name="status" value="rejected">' +
                '<button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition">REJ</button>' +
                '</form>' +
                '</div>' +
                '</td>' +
                '</tr>';
        }

        function shouldInsertRows() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('status') || params.has('leave_type')) {
                return false;
            }
            const page = params.get('page');
            return !page || page === '1';
        }

        function insertRequestRow(request) {
            const tbody = document.getElementById(request.leave_type === 'cuti' ? 'tbody-cuti' : 'tbody-izin');
            if (!tbody) return;

            const emptyRow = tbody.querySelector('td[colspan="4"]');
            if (emptyRow) {
                tbody.replaceChildren();
            }

            tbody.insertAdjacentHTML('afterbegin', buildRequestRow(request));

            const firstRow = tbody.querySelector('tr');
            if (firstRow) {
                setTimeout(function () {
                    firstRow.classList.remove('new-request');
                }, 3000);
            }
            if (window.feather) feather.replace();
        }

        function processRequests(data) {
            const latestId = Number(data.latest_id || 0);

            if (isFirstLoad) {
                latestLeaveRequestId = latestId;
                isFirstLoad = false;
                return;
            }

            if (latestId <= latestLeaveRequestId) return;

            const newRequests = (data.requests || []).filter(r => Number(r.id) > latestLeaveRequestId);
            if (newRequests.length === 0) {
                latestLeaveRequestId = latestId;
                return;
            }

            const canInsert = shouldInsertRows();

            newRequests.forEach(function (request) {
                if (canInsert) insertRequestRow(request);
            });

            latestLeaveRequestId = latestId;
        }

        function openNotificationDetail(id) {
            if (window.openHRNotificationDetail) {
                window.openHRNotificationDetail(id);
            } else {
                window.location.href = notificationShowUrl.replace(':ID', id);
            }
        }
        window.openNotificationDetail = openNotificationDetail;

        window.addEventListener('hr-live-data', function (event) {
            const data = event.detail;
            if (!data) return;
            updateStatistics(data);
            processRequests(data);
            const lastUpdated = document.getElementById('last-updated');
            if (lastUpdated) lastUpdated.textContent = 'baru saja';
        });
    })();
</script>
@endpush