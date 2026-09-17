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
        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500">Last updated: <span id="last-updated" class="font-medium text-emerald-600">just now</span></span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200" title="Data & statistik diperbarui otomatis setiap 10 detik">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Live
            </span>
        </div>
    </div>

    <div id="leave-request-table" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">Cuti</h3>
                <span class="text-xs text-slate-500"><span id="header-pending-cuti">{{ $pendingCuti }}</span> pending</span>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Pending</p>
                    <p class="text-lg font-bold text-amber-600"><span id="pending-cuti-count">{{ $pendingCuti }}</span></p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Disetujui</p>
                    <p class="text-lg font-bold text-emerald-600"><span id="approved-cuti-count">{{ $approvedCuti }}</span></p>
                </div>
                <div class="bg-rose-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Ditolak</p>
                    <p class="text-lg font-bold text-rose-600"><span id="rejected-cuti-count">{{ $rejectedCuti }}</span></p>
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
                    <tbody id="tbody-cuti" class="divide-y divide-slate-200">
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
                <span class="text-xs text-slate-500"><span id="header-pending-izin">{{ $pendingIzin }}</span> pending</span>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-amber-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Pending</p>
                    <p class="text-lg font-bold text-amber-600"><span id="pending-izin-count">{{ $pendingIzin }}</span></p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Disetujui</p>
                    <p class="text-lg font-bold text-emerald-600"><span id="approved-izin-count">{{ $approvedIzin }}</span></p>
                </div>
                <div class="bg-rose-50 rounded-lg p-3 text-center">
                    <p class="text-xs font-semibold text-slate-500">Ditolak</p>
                    <p class="text-lg font-bold text-rose-600"><span id="rejected-izin-count">{{ $rejectedIzin }}</span></p>
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
                    <tbody id="tbody-izin" class="divide-y divide-slate-200">
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

@push('scripts')
<style>
    .stat-flash {
        animation: stat-pulse 0.6s ease;
    }

    @keyframes stat-pulse {
        0% {
            background-color: rgba(16, 185, 129, 0.35);
        }
        100% {
            background-color: transparent;
        }
    }

    .new-request {
        animation: new-request-highlight 2.5s ease;
    }

    @keyframes new-request-highlight {
        0% {
            background-color: rgba(99, 102, 241, 0.25);
        }
        100% {
            background-color: transparent;
        }
    }

    .toast-enter {
        animation: toast-slide-in 0.3s ease;
    }

    .toast-exit {
        animation: toast-fade-out 0.4s ease forwards;
    }

    @keyframes toast-slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes toast-fade-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>
<script>
    (function () {
        let latestLeaveRequestId = null;
        let isFirstLoad = true;
        let pollingTimer = null;
        const pollInterval = 10000;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const updateUrl = "{{ route('hr.leave-requests.update', ':ID') }}";
        const notificationShowUrl = "{{ route('hr.leave-requests.show', ':ID') }}";

        const NOTIFICATION_KEY = 'hr_leave_notifications';
        const MAX_NOTIFICATIONS = 50;

        const leaveTypeLabels = {
            cuti: 'cuti',
            sakit: 'izin sakit',
            penting: 'izin kepentingan',
            lainnya: 'izin lainnya'
        };

        const notificationTypeLabels = {
            cuti: 'Cuti',
            sakit: 'Izin Sakit',
            penting: 'Izin Penting',
            lainnya: 'Izin'
        };

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

        function timeAgo(dateString) {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return '';
            }
            const minutes = Math.floor((Date.now() - date.getTime()) / 60000);
            if (minutes < 1) {
                return 'baru saja';
            }
            if (minutes < 60) {
                return minutes + ' menit yang lalu';
            }
            const hours = Math.floor(minutes / 60);
            if (hours < 24) {
                return hours + ' jam yang lalu';
            }
            return Math.floor(hours / 24) + ' hari yang lalu';
        }

        function flashElement(element) {
            if (!element) {
                return;
            }
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
                if (!element) {
                    return;
                }
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
                '<td class="px-3 py-3 whitespace-nowrap">' +
                '<div class="flex items-center">' +
                '<div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-2">' + initial + '</div>' +
                '<span class="text-sm font-medium text-slate-800">' + name + '</span>' +
                '</div>' +
                '</td>' +
                '<td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600">' + dateRange + '</td>' +
                '<td class="px-3 py-3 whitespace-nowrap">' +
                '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">Pending</span>' +
                '</td>' +
                '<td class="px-3 py-3 whitespace-nowrap">' +
                '<div class="flex items-center gap-1">' +
                '<form action="' + actionUrl + '" method="POST" class="m-0">' +
                '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                '<input type="hidden" name="_method" value="PUT">' +
                '<input type="hidden" name="status" value="approved">' +
                '<button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">ACC</button>' +
                '</form>' +
                '<form action="' + actionUrl + '" method="POST" class="m-0">' +
                '<input type="hidden" name="_token" value="' + csrfToken + '">' +
                '<input type="hidden" name="_method" value="PUT">' +
                '<input type="hidden" name="status" value="rejected">' +
                '<button type="submit" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 hover:bg-rose-200">REJ</button>' +
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
            if (!tbody) {
                return;
            }

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
        }

        function showLeaveNotification(request) {
            const container = document.getElementById('notification-container');
            if (!container) {
                return;
            }

            const typeLabel = leaveTypeLabels[request.leave_type] || request.leave_type;
            const name = escapeHtml(request.name || '-');

            const toast = document.createElement('div');
            toast.className = 'toast-enter pointer-events-auto max-w-sm w-full bg-white rounded-lg shadow-lg border border-slate-200 border-l-4 border-l-emerald-500 p-4 flex items-start gap-3';
            toast.innerHTML =
                '<span class="text-lg leading-none">🔔</span>' +
                '<div class="min-w-0 flex-1">' +
                '<p class="text-sm font-semibold text-slate-800">Pengajuan Baru</p>' +
                '<p class="text-sm text-slate-600">' + name + ' mengajukan ' + escapeHtml(typeLabel) + '</p>' +
                '<p class="text-xs text-slate-400 mt-0.5">' + escapeHtml(timeAgo(request.created_at)) + '</p>' +
                '</div>' +
                '<button type="button" class="text-slate-400 hover:text-slate-600 shrink-0" onclick="this.parentElement.remove()">×</button>';

            container.appendChild(toast);

            setTimeout(function () {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(function () {
                    toast.remove();
                }, 400);
            }, 5000);
        }

        function getNotifications() {
            try {
                const parsed = JSON.parse(localStorage.getItem(NOTIFICATION_KEY));
                return Array.isArray(parsed) ? parsed : [];
            } catch (error) {
                console.error('[HR Notifications] Gagal membaca localStorage:', error);
                return [];
            }
        }

        function saveNotifications(notifications) {
            try {
                localStorage.setItem(NOTIFICATION_KEY, JSON.stringify(notifications));
            } catch (error) {
                console.error('[HR Notifications] Gagal menyimpan localStorage:', error);
            }
        }

        function getUnreadCount() {
            return getNotifications().filter(function (notification) {
                return !notification.read;
            }).length;
        }

        function updateBellBadge() {
            const badge = document.getElementById('notification-badge');
            if (!badge) {
                return;
            }
            const count = getUnreadCount();
            badge.textContent = count > 99 ? '99+' : String(count || '');
            badge.style.display = count > 0 ? 'flex' : 'none';
        }

        function storeNotification(request) {
            const notifications = getNotifications();

            const alreadyExists = notifications.some(function (notification) {
                return Number(notification.id) === Number(request.id);
            });

            if (alreadyExists) {
                return false;
            }

            notifications.unshift({
                id: request.id,
                employee: request.name,
                leave_type: request.leave_type,
                created_at: request.created_at,
                read: false
            });

            if (notifications.length > MAX_NOTIFICATIONS) {
                notifications.splice(MAX_NOTIFICATIONS);
            }

            saveNotifications(notifications);

            return true;
        }

        function renderNotifications(list) {
            const notifications = getNotifications();

            if (notifications.length === 0) {
                list.innerHTML =
                    '<div class="px-4 py-10 text-center">' +
                    '<span class="text-2xl">🔔</span>' +
                    '<p class="text-sm text-slate-400 mt-2">Belum ada notifikasi</p>' +
                    '</div>';
                return;
            }

            list.innerHTML = notifications.map(function (notification) {
                const typeLabel = notificationTypeLabels[notification.leave_type] || notification.leave_type;
                const employee = escapeHtml(notification.employee || '-');
                const time = escapeHtml(timeAgo(notification.created_at));
                const unread = !notification.read;

                return '' +
                    '<div class="notification-item px-4 py-3 hover:bg-slate-50 cursor-pointer flex items-start gap-3 transition-colors" data-id="' + notification.id + '"' + (unread ? ' style="background-color: rgba(99, 102, 241, 0.06);"' : '') + '>' +
                    '<span class="mt-1.5 h-2 w-2 rounded-full flex-shrink-0 ' + (unread ? 'bg-indigo-500' : 'bg-transparent') + '"></span>' +
                    '<div class="min-w-0 flex-1">' +
                    '<p class="text-sm font-semibold text-slate-800">' + employee + '</p>' +
                    '<p class="text-xs text-slate-500">Mengajukan ' + escapeHtml(typeLabel) + '</p>' +
                    '<p class="text-xs text-slate-400 mt-0.5">' + time + '</p>' +
                    '</div>' +
                    '</div>';
            }).join('');
        }

        function getNotificationPanel() {
            let panel = document.getElementById('notification-panel');
            if (panel) {
                return panel;
            }

            panel = document.createElement('div');
            panel.id = 'notification-panel';
            panel.className = 'fixed top-16 right-4 z-[9999] w-[360px] max-w-[calc(100vw-2rem)] bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden hidden';
            panel.innerHTML =
                '<div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">' +
                '<h3 class="text-sm font-semibold text-slate-800">Notifications</h3>' +
                '<span class="text-lg leading-none">🔔</span>' +
                '</div>' +
                '<div id="notification-list" class="max-h-[60vh] overflow-y-auto divide-y divide-slate-100"></div>' +
                '<div class="px-4 py-2 border-t border-slate-200">' +
                '<button id="mark-all-read" type="button" class="w-full text-center text-xs font-semibold text-indigo-600 hover:text-indigo-800 py-1">Mark all as read</button>' +
                '</div>';

            document.body.appendChild(panel);

            document.getElementById('notification-list').addEventListener('click', function (event) {
                const item = event.target.closest('.notification-item');
                if (!item) {
                    return;
                }
                openNotificationDetail(item.getAttribute('data-id'));
            });

            document.getElementById('mark-all-read').addEventListener('click', function () {
                const notifications = getNotifications();
                notifications.forEach(function (notification) {
                    notification.read = true;
                });
                saveNotifications(notifications);
                updateBellBadge();
                renderNotifications(document.getElementById('notification-list'));
            });

            return panel;
        }

        function openNotificationPanel() {
            const panel = getNotificationPanel();
            renderNotifications(document.getElementById('notification-list'));
            panel.classList.remove('hidden');
        }

        function closeNotificationPanel() {
            const panel = document.getElementById('notification-panel');
            if (panel) {
                panel.classList.add('hidden');
            }
        }

        function toggleNotificationPanel() {
            const panel = getNotificationPanel();
            const isHidden = panel.classList.contains('hidden');
            if (isHidden) {
                openNotificationPanel();
            } else {
                closeNotificationPanel();
            }
        }

        function openNotificationDetail(id) {
            const notifications = getNotifications().map(function (notification) {
                return Number(notification.id) === Number(id) ? { ...notification, read: true } : notification;
            });
            saveNotifications(notifications);
            updateBellBadge();
            closeNotificationPanel();
            window.location.href = notificationShowUrl.replace(':ID', id);
        }

        function processRequests(data) {
            const latestId = Number(data.latest_id || 0);

            console.log('Live leave data:', data);
            console.log('Latest ID:', latestId);
            console.log('Previous ID:', latestLeaveRequestId);

            if (isFirstLoad) {
                latestLeaveRequestId = latestId;
                isFirstLoad = false;
                return;
            }

            if (latestId <= latestLeaveRequestId) {
                return;
            }

            const newRequests = (data.requests || []).filter(function (request) {
                return Number(request.id) > latestLeaveRequestId;
            });

            console.log('New leave requests:', newRequests);

            if (newRequests.length === 0) {
                latestLeaveRequestId = latestId;
                return;
            }

            const canInsert = shouldInsertRows();

            newRequests.forEach(function (request) {
                if (!storeNotification(request)) {
                    return;
                }

                updateBellBadge();

                if (canInsert) {
                    insertRequestRow(request);
                }
                showLeaveNotification(request);
            });

            latestLeaveRequestId = latestId;
        }

        async function loadLiveLeaveRequests() {
            const url = "{{ route('hr.leave-requests.live') }}";

            try {
                const response = await fetch(url, {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load leave requests');
                }

                const data = await response.json();

                updateStatistics(data);
                processRequests(data);

                const lastUpdated = document.getElementById('last-updated');
                if (lastUpdated) {
                    lastUpdated.textContent = 'just now';
                }
            } catch (error) {
                console.error('[HR Leave Management] Gagal memuat data live, akan dicoba lagi di interval berikutnya:', error);
            }
        }

        function startPolling() {
            if (pollingTimer === null) {
                pollingTimer = setInterval(loadLiveLeaveRequests, pollInterval);
            }
        }

        function stopPolling() {
            if (pollingTimer !== null) {
                clearInterval(pollingTimer);
                pollingTimer = null;
            }
        }

        function bindNotificationBell() {
            const bell = document.getElementById('notification-bell');
            if (bell) {
                bell.addEventListener('click', function (event) {
                    event.stopPropagation();
                    toggleNotificationPanel();
                });
            }

            document.addEventListener('click', function (event) {
                const panel = document.getElementById('notification-panel');
                if (!panel || panel.classList.contains('hidden')) {
                    return;
                }
                if (event.target.closest('#notification-bell') || event.target.closest('#notification-panel')) {
                    return;
                }
                closeNotificationPanel();
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateBellBadge();
            bindNotificationBell();
            loadLiveLeaveRequests();
            startPolling();
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopPolling();
            } else {
                loadLiveLeaveRequests();
                startPolling();
            }
        });
    })();
</script>
@endpush