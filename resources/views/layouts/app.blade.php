<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Absensi') - PT BINTANG ANUGRAH</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind & Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .toast-enter {
            animation: toast-slide-in 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .toast-exit {
            animation: toast-fade-out 0.35s ease-in forwards;
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
    @stack('styles')
</head>

<body class="h-full text-slate-800 antialiased selection:bg-indigo-500 selection:text-white"
    x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex">
        <!-- Desktop Sidebar -->
        <aside
            class="bg-white w-64 border-r border-slate-200/80 hidden md:flex flex-col shrink-0 h-screen sticky top-0 z-30 shadow-[1px_0_3px_rgba(0,0,0,0.02)]">
            <!-- Brand Header -->
            <div class="h-16 flex items-center px-6 border-b border-slate-100">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                        <i data-feather="check-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 block leading-tight">E-Absensi</span>
                        <span class="text-[10px] font-semibold tracking-wider text-indigo-600 uppercase block">PT Bintang Anugrah</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="p-3 flex-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </div>

            <!-- User Menu Footer -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/70 shadow-xs">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            <span class="inline-block text-[10px] font-medium text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded uppercase tracking-wider">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Logout">
                            <i data-feather="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen"
            x-cloak
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 md:hidden transition-opacity"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false">
        </div>

        <!-- Mobile Sidebar Drawer -->
        <aside x-show="sidebarOpen"
            x-cloak
            class="fixed inset-y-0 left-0 bg-white w-72 max-w-[85vw] shadow-2xl z-50 md:hidden flex flex-col"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">

            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-xs">
                        <i data-feather="check-square" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-base font-bold text-slate-900 block leading-tight">E-Absensi</span>
                        <span class="text-[9px] font-semibold tracking-wider text-indigo-600 uppercase block">PT Bintang Anugrah</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-3 flex-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </div>

            <div class="p-3 border-t border-slate-100 bg-slate-50/70">
                <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            <span class="text-[10px] text-slate-500 block uppercase">{{ Auth::user()->role }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Logout">
                            <i data-feather="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area Wrapper -->
        <div class="flex-1 flex flex-col min-h-0 min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header
                class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 sticky top-0 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                <div class="flex items-center min-w-0 mr-4">
                    <button @click="sidebarOpen = true"
                        type="button"
                        class="md:hidden p-2 -ml-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg focus:outline-none transition-colors mr-2"
                        aria-label="Buka Menu">
                        <i data-feather="menu" class="w-5 h-5"></i>
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-lg font-bold text-slate-800 truncate">
                            @yield('header_title', 'Dashboard')
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3 shrink-0">
                    <!-- Notifications -->
                    <div class="relative" id="notification-wrapper">
                        <button id="notification-bell" type="button"
                            class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50 rounded-xl transition-colors relative focus:outline-none"
                            aria-label="Notifikasi" title="Notifikasi">
                            <i data-feather="bell" class="w-5 h-5"></i>
                            <span id="notification-badge"
                                class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold items-center justify-center ring-2 ring-white"
                                style="display: none;"></span>
                        </button>

                        <!-- Notification Dropdown Panel (for Employee / General) -->
                        <div id="notification-panel"
                            class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200 ring-1 ring-black/5 z-50 overflow-hidden">
                            <div class="p-3.5 px-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-slate-800">Notifikasi</span>
                                    <span id="notification-unread-pill" class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700" style="display: none;">0 baru</span>
                                </div>
                                <button type="button" id="mark-all-read-btn" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                    Tandai semua dibaca
                                </button>
                            </div>
                            <div id="notification-list" class="max-h-[360px] overflow-y-auto divide-y divide-slate-100">
                                <div class="p-6 text-center text-xs text-slate-400">
                                    Memuat notifikasi...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                            class="flex items-center space-x-2.5 p-1 sm:px-2.5 sm:py-1.5 rounded-xl hover:bg-slate-100 transition-colors focus:outline-none border border-transparent hover:border-slate-200">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <span class="text-xs font-semibold text-slate-700 block leading-tight max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize block">{{ Auth::user()->role }}</span>
                            </div>
                            <i data-feather="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
                        </button>

                        <div x-show="dropdownOpen"
                            x-cloak
                            class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl py-1.5 border border-slate-200 ring-1 ring-black/5 focus:outline-none z-50"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            style="display: none;">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">{{ Auth::user()->role }}</p>
                            </div>
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i data-feather="user" class="w-4 h-4 mr-2.5 text-slate-400"></i>
                                Profil Saya
                            </a>
                            <a href="{{ route('profile.edit') }}#password-section"
                                class="flex items-center px-4 py-2 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <i data-feather="key" class="w-4 h-4 mr-2.5 text-slate-400"></i>
                                Ganti Password
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                    <i data-feather="log-out" class="w-4 h-4 mr-2.5 text-rose-500"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-slate-50/70 p-4 sm:p-6 lg:p-8 min-w-0">
                <div class="max-w-7xl mx-auto w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Initialize Feather Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();
        });
    </script>

    <!-- SweetAlert Global Notification handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#4f46e5',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    html: `<ul class="text-left list-disc list-inside text-sm text-red-500 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>`,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>

    <!-- Global Notification Container for Toasts -->
    <div id="notification-container"
        class="fixed top-20 right-4 sm:right-6 z-[9999] w-80 max-w-[calc(100vw-2rem)] space-y-3 pointer-events-none"></div>

    @if(Auth::check() && Auth::user()->isEmployee())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('notification-bell');
            const panel = document.getElementById('notification-panel');
            const badge = document.getElementById('notification-badge');
            const unreadPill = document.getElementById('notification-unread-pill');
            const list = document.getElementById('notification-list');
            const markAllBtn = document.getElementById('mark-all-read-btn');
            const container = document.getElementById('notification-container');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!bell || !panel) return;

            let notifications = [];
            const knownNotificationIds = new Set();
            let isFirstLoad = true;
            let pollingTimer = null;
            const pollInterval = 8000;

            function escapeHtml(val) {
                return String(val ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function timeAgo(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                const minutes = Math.floor((Date.now() - date.getTime()) / 60000);
                if (minutes < 1) return 'baru saja';
                if (minutes < 60) return minutes + ' mnt lalu';
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return hours + ' jam lalu';
                return Math.floor(hours / 24) + ' hari lalu';
            }

            function renderNotifications() {
                if (!list) return;
                if (notifications.length === 0) {
                    list.innerHTML = `
                        <div class="p-8 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <i data-feather="bell-off" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Belum ada notifikasi</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Notifikasi status permohonan izin akan muncul di sini.</p>
                        </div>
                    `;
                    if (window.feather) feather.replace();
                    return;
                }

                list.innerHTML = notifications.map(function (n) {
                    const isApproved = n.status === 'approved';
                    const iconBg = isApproved ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700';
                    const iconName = isApproved ? 'check-circle' : 'x-circle';
                    const bgClass = !n.read ? 'bg-indigo-50/50' : 'hover:bg-slate-50';

                    return `
                        <div class="notification-item p-3.5 px-4 cursor-pointer flex items-start gap-3 transition-colors ${bgClass}" data-id="${n.id}" data-url="${n.url}">
                            <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                <i data-feather="${iconName}" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-slate-800 truncate">${escapeHtml(n.title)}</p>
                                    <span class="text-[10px] text-slate-400 shrink-0 font-medium">${timeAgo(n.created_at)}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5 leading-snug">${escapeHtml(n.message)}</p>
                                ${n.start_date_formatted ? `<p class="text-[10px] text-slate-400 mt-1">Rentang: ${escapeHtml(n.start_date_formatted)} s/d ${escapeHtml(n.end_date_formatted || n.start_date_formatted)}</p>` : ''}
                            </div>
                            ${!n.read ? '<span class="w-2 h-2 rounded-full bg-indigo-600 mt-2 shrink-0"></span>' : ''}
                        </div>
                    `;
                }).join('');

                if (window.feather) feather.replace();
            }

            function showStatusToast(n) {
                if (!container) return;

                const isApproved = n.status === 'approved';
                const borderClass = isApproved ? 'border-l-emerald-500' : 'border-l-rose-500';
                const iconBg = isApproved ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600';
                const iconName = isApproved ? 'check-circle' : 'x-circle';
                const titleColor = isApproved ? 'text-emerald-700' : 'text-rose-700';
                const linkColor = isApproved ? 'text-emerald-600' : 'text-rose-600';

                const toast = document.createElement('div');
                toast.className = 'toast-enter pointer-events-auto max-w-sm w-full bg-white rounded-2xl shadow-xl border border-slate-200 border-l-4 ' + borderClass + ' p-4 flex items-start gap-3 cursor-pointer hover:bg-slate-50 transition-colors';
                toast.innerHTML =
                    '<div class="w-8 h-8 rounded-xl ' + iconBg + ' flex items-center justify-center shrink-0 shadow-xs">' +
                    '<i data-feather="' + iconName + '" class="w-4 h-4"></i>' +
                    '</div>' +
                    '<div class="min-w-0 flex-1">' +
                    '<div class="flex items-center justify-between gap-1">' +
                    '<p class="text-xs font-bold ' + titleColor + '">' + escapeHtml(n.title) + '</p>' +
                    '<span class="text-[10px] text-slate-400">' + escapeHtml(timeAgo(n.created_at)) + '</span>' +
                    '</div>' +
                    '<p class="text-xs text-slate-600 mt-0.5 leading-snug">' + escapeHtml(n.message) + '</p>' +
                    '<p class="text-[10px] ' + linkColor + ' font-semibold mt-1">Klik untuk lihat detail &rarr;</p>' +
                    '</div>' +
                    '<button type="button" class="text-slate-400 hover:text-slate-600 shrink-0 text-base p-1 leading-none" title="Tutup">×</button>';

                toast.addEventListener('click', async function (e) {
                    if (e.target.closest('button')) {
                        e.stopPropagation();
                        toast.remove();
                        return;
                    }
                    toast.remove();
                    try {
                        await fetch("{{ url('employee/notifications') }}/" + n.id + "/read", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    } catch (err) {}
                    if (n.url) {
                        window.location.href = n.url;
                    }
                });

                container.appendChild(toast);
                if (window.feather) feather.replace();

                setTimeout(function () {
                    toast.classList.remove('toast-enter');
                    toast.classList.add('toast-exit');
                    setTimeout(function () {
                        toast.remove();
                    }, 400);
                }, 6000);
            }

            async function loadNotifications() {
                try {
                    const res = await fetch("{{ route('employee.notifications.index') }}", {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const newNotifications = data.notifications || [];
                    const unread = data.unread_count || 0;

                    if (isFirstLoad) {
                        newNotifications.forEach(function (n) {
                            knownNotificationIds.add(n.id);
                        });
                        isFirstLoad = false;
                    } else {
                        newNotifications.forEach(function (n) {
                            if (!n.read && !knownNotificationIds.has(n.id)) {
                                knownNotificationIds.add(n.id);
                                showStatusToast(n);
                            }
                        });
                    }

                    notifications = newNotifications;

                    if (badge) {
                        badge.textContent = unread > 99 ? '99+' : String(unread || '');
                        badge.style.display = unread > 0 ? 'flex' : 'none';
                    }
                    if (unreadPill) {
                        unreadPill.textContent = unread + ' baru';
                        unreadPill.style.display = unread > 0 ? 'inline-block' : 'none';
                    }

                    if (!panel.classList.contains('hidden')) {
                        renderNotifications();
                    }

                    window.dispatchEvent(new CustomEvent('employee-live-data', { detail: data }));
                } catch (e) {}
            }

            function startPolling() {
                if (pollingTimer === null) {
                    pollingTimer = setInterval(loadNotifications, pollInterval);
                }
            }

            function stopPolling() {
                if (pollingTimer !== null) {
                    clearInterval(pollingTimer);
                    pollingTimer = null;
                }
            }

            bell.addEventListener('click', function (e) {
                e.stopPropagation();
                panel.classList.toggle('hidden');
                if (!panel.classList.contains('hidden')) {
                    renderNotifications();
                }
            });

            document.addEventListener('click', function (e) {
                if (!panel.contains(e.target) && !bell.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });

            list.addEventListener('click', async function (e) {
                const item = e.target.closest('.notification-item');
                if (!item) return;
                const id = item.dataset.id;
                const url = item.dataset.url;

                try {
                    await fetch("{{ url('employee/notifications') }}/" + id + "/read", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } catch (err) {}

                if (url) {
                    window.location.href = url;
                }
            });

            if (markAllBtn) {
                markAllBtn.addEventListener('click', async function () {
                    try {
                        await fetch("{{ route('employee.notifications.mark-all-read') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        loadNotifications();
                    } catch (err) {}
                });
            }

            renderNotifications();
            loadNotifications();
            startPolling();

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopPolling();
                } else {
                    loadNotifications();
                    startPolling();
                }
            });
        });
    </script>
    @elseif(Auth::check() && Auth::user()->isHR())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('notification-bell');
            const panel = document.getElementById('notification-panel');
            const badge = document.getElementById('notification-badge');
            const unreadPill = document.getElementById('notification-unread-pill');
            const list = document.getElementById('notification-list');
            const markAllBtn = document.getElementById('mark-all-read-btn');
            const container = document.getElementById('notification-container');

            if (!bell || !panel || !list) return;

            const NOTIFICATION_KEY = 'hr_leave_notifications';
            const MAX_NOTIFICATIONS = 50;
            const liveUrl = "{{ route('hr.leave-requests.live') }}";
            const detailUrlTemplate = "{{ route('hr.leave-requests.show', ':ID') }}";
            let latestLeaveRequestId = null;
            let isFirstLoad = true;
            let pollingTimer = null;
            const pollInterval = 10000;

            const leaveTypeLabels = {
                cuti: 'Cuti Tahunan',
                sakit: 'Izin Sakit',
                penting: 'Izin Penting',
                lainnya: 'Izin Lainnya'
            };

            function escapeHtml(val) {
                return String(val ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function timeAgo(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                const minutes = Math.floor((Date.now() - date.getTime()) / 60000);
                if (minutes < 1) return 'baru saja';
                if (minutes < 60) return minutes + ' mnt lalu';
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return hours + ' jam lalu';
                return Math.floor(hours / 24) + ' hari lalu';
            }

            function getNotifications() {
                try {
                    const parsed = JSON.parse(localStorage.getItem(NOTIFICATION_KEY));
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            function saveNotifications(items) {
                try {
                    localStorage.setItem(NOTIFICATION_KEY, JSON.stringify(items));
                } catch (e) {}
            }

            function getUnreadCount() {
                return getNotifications().filter(n => !n.read).length;
            }

            function updateBadge() {
                const count = getUnreadCount();
                if (badge) {
                    badge.textContent = count > 99 ? '99+' : String(count || '');
                    badge.style.display = count > 0 ? 'flex' : 'none';
                }
                if (unreadPill) {
                    unreadPill.textContent = count + ' baru';
                    unreadPill.style.display = count > 0 ? 'inline-block' : 'none';
                }
            }

            function renderNotifications() {
                const notifications = getNotifications();
                if (!list) return;

                if (notifications.length === 0) {
                    list.innerHTML = `
                        <div class="p-8 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <i data-feather="bell-off" class="w-5 h-5"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Belum ada notifikasi</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pengajuan cuti dan izin dari karyawan akan muncul di sini.</p>
                        </div>
                    `;
                    if (window.feather) feather.replace();
                    return;
                }

                list.innerHTML = notifications.map(function (n) {
                    const unread = !n.read;
                    const typeLabel = leaveTypeLabels[n.leave_type] || n.leave_type || 'Pengajuan';
                    const employee = escapeHtml(n.employee || 'Karyawan');
                    const reason = escapeHtml(n.reason || '');

                    let iconBg = 'bg-amber-100 text-amber-700';
                    let iconName = 'clock';
                    if (n.leave_type === 'cuti') {
                        iconBg = 'bg-indigo-100 text-indigo-700';
                        iconName = 'calendar';
                    } else if (n.leave_type === 'sakit') {
                        iconBg = 'bg-rose-100 text-rose-700';
                        iconName = 'activity';
                    }

                    return `
                        <div class="notification-item p-3.5 sm:px-4 hover:bg-slate-50 cursor-pointer flex items-start gap-3 transition-colors ${unread ? 'bg-indigo-50/50' : ''}" data-id="${n.id}">
                            <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                <i data-feather="${iconName}" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-slate-800 truncate">${employee}</p>
                                    <span class="text-[10px] text-slate-400 shrink-0 font-medium">${timeAgo(n.created_at)}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5 leading-snug">Mengajukan ${escapeHtml(typeLabel)}</p>
                                ${reason ? `<p class="text-[11px] text-slate-400 mt-0.5 truncate">${reason}</p>` : ''}
                                ${n.start_date ? `<p class="text-[10px] text-slate-400 mt-1">Rentang: ${escapeHtml(n.start_date)} s/d ${escapeHtml(n.end_date || n.start_date)}</p>` : ''}
                            </div>
                            ${unread ? '<span class="w-2 h-2 rounded-full bg-indigo-600 mt-2 shrink-0"></span>' : ''}
                        </div>
                    `;
                }).join('');

                if (window.feather) feather.replace();
            }

            function openNotificationDetail(id) {
                if (!id) return;
                const items = getNotifications().map(function (n) {
                    return Number(n.id) === Number(id) ? { ...n, read: true } : n;
                });
                saveNotifications(items);
                updateBadge();
                if (panel) panel.classList.add('hidden');
                window.location.href = detailUrlTemplate.replace(':ID', id);
            }

            window.openHRNotificationDetail = openNotificationDetail;

            function showLeaveToast(request) {
                if (!container) return;

                const typeLabel = leaveTypeLabels[request.leave_type] || request.leave_type || 'Cuti/Izin';
                const name = escapeHtml(request.name || 'Karyawan');

                const toast = document.createElement('div');
                toast.className = 'toast-enter pointer-events-auto max-w-sm w-full bg-white rounded-2xl shadow-xl border border-slate-200 border-l-4 border-l-emerald-500 p-4 flex items-start gap-3 cursor-pointer hover:bg-slate-50 transition-colors';
                toast.innerHTML =
                    '<div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-xs">' +
                    '<i data-feather="bell" class="w-4 h-4"></i>' +
                    '</div>' +
                    '<div class="min-w-0 flex-1">' +
                    '<div class="flex items-center justify-between gap-1">' +
                    '<p class="text-xs font-bold text-slate-800">Pengajuan Baru</p>' +
                    '<span class="text-[10px] text-slate-400">' + escapeHtml(timeAgo(request.created_at)) + '</span>' +
                    '</div>' +
                    '<p class="text-xs text-slate-600 mt-0.5">' + name + ' mengajukan ' + escapeHtml(typeLabel) + '</p>' +
                    '<p class="text-[10px] text-indigo-600 font-semibold mt-1">Klik untuk lihat detail &rarr;</p>' +
                    '</div>' +
                    '<button type="button" class="text-slate-400 hover:text-slate-600 shrink-0 text-base p-1 leading-none" title="Tutup">×</button>';

                toast.addEventListener('click', function (e) {
                    if (e.target.closest('button')) {
                        e.stopPropagation();
                        toast.remove();
                        return;
                    }
                    toast.remove();
                    openNotificationDetail(request.id);
                });

                container.appendChild(toast);
                if (window.feather) feather.replace();

                setTimeout(function () {
                    toast.classList.remove('toast-enter');
                    toast.classList.add('toast-exit');
                    setTimeout(function () {
                        toast.remove();
                    }, 400);
                }, 6000);
            }

            function storeNotification(request) {
                const items = getNotifications();
                const exists = items.some(n => Number(n.id) === Number(request.id));
                if (exists) return false;

                items.unshift({
                    id: request.id,
                    employee: request.name,
                    leave_type: request.leave_type,
                    start_date: request.start_date,
                    end_date: request.end_date,
                    reason: request.reason,
                    created_at: request.created_at,
                    read: false
                });

                if (items.length > MAX_NOTIFICATIONS) {
                    items.splice(MAX_NOTIFICATIONS);
                }

                saveNotifications(items);
                return true;
            }

            function processRequests(data) {
                const latestId = Number(data.latest_id || 0);

                if (isFirstLoad) {
                    latestLeaveRequestId = latestId;
                    isFirstLoad = false;

                    const currentItems = getNotifications();
                    if (currentItems.length === 0 && Array.isArray(data.requests) && data.requests.length > 0) {
                        data.requests.forEach(function (r) {
                            storeNotification(r);
                        });
                        updateBadge();
                        renderNotifications();
                    }
                    return;
                }

                if (latestId <= latestLeaveRequestId) return;

                const newRequests = (data.requests || []).filter(r => Number(r.id) > latestLeaveRequestId);
                if (newRequests.length === 0) {
                    latestLeaveRequestId = latestId;
                    return;
                }

                newRequests.forEach(function (request) {
                    if (storeNotification(request)) {
                        showLeaveToast(request);
                    }
                });

                updateBadge();
                if (!panel.classList.contains('hidden')) {
                    renderNotifications();
                }

                latestLeaveRequestId = latestId;
            }

            async function loadLiveRequests() {
                try {
                    const response = await fetch(liveUrl, {
                        cache: 'no-store',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) return;
                    const data = await response.json();

                    processRequests(data);

                    window.dispatchEvent(new CustomEvent('hr-live-data', { detail: data }));
                } catch (e) {}
            }

            function startPolling() {
                if (pollingTimer === null) {
                    pollingTimer = setInterval(loadLiveRequests, pollInterval);
                }
            }

            function stopPolling() {
                if (pollingTimer !== null) {
                    clearInterval(pollingTimer);
                    pollingTimer = null;
                }
            }

            bell.addEventListener('click', function (e) {
                e.stopPropagation();
                panel.classList.toggle('hidden');
                if (!panel.classList.contains('hidden')) {
                    renderNotifications();
                }
            });

            document.addEventListener('click', function (e) {
                if (!panel.contains(e.target) && !bell.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });

            list.addEventListener('click', function (e) {
                const item = e.target.closest('.notification-item');
                if (!item) return;
                const id = item.dataset.id;
                if (id) {
                    openNotificationDetail(id);
                }
            });

            if (markAllBtn) {
                markAllBtn.addEventListener('click', function () {
                    const items = getNotifications().map(n => ({ ...n, read: true }));
                    saveNotifications(items);
                    updateBadge();
                    renderNotifications();
                });
            }

            updateBadge();
            renderNotifications();
            loadLiveRequests();
            startPolling();

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    stopPolling();
                } else {
                    loadLiveRequests();
                    startPolling();
                }
            });
        });
    </script>
    @endif

    @stack('scripts')
</body>

</html>
