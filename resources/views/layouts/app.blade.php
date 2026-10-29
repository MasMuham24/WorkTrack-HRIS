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
    </style>
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
                    <!-- Notifications (HR or Admin) -->
                    <button id="notification-bell" type="button"
                        class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50 rounded-xl transition-colors relative focus:outline-none"
                        aria-label="Notifikasi" title="Notifikasi">
                        <i data-feather="bell" class="w-5 h-5"></i>
                        <span id="notification-badge"
                            class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold items-center justify-center ring-2 ring-white"
                            style="display: none;"></span>
                    </button>

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

    @stack('scripts')
</body>

</html>
