<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Absensi') - PT BINTANG ANUGRAH</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex">
        <!-- Sidebar -->
        <aside class="bg-white w-64 border-r border-slate-200 hidden md:block flex-shrink-0 flex-col h-screen sticky top-0">
            <div class="h-16 flex items-center px-6 border-b border-slate-200">
                <span class="text-xl font-bold text-indigo-600">E-Absensi</span>
            </div>

            <div class="p-4 flex-1 overflow-y-auto">
                <nav class="space-y-1">
                    <a href="{{ Auth::user() ? route(Auth::user()->role . '.dashboard') : '#' }}"
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('*.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="home" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>

                    <a href="{{ Auth::user() && Auth::user()->role === 'employee' ? route('employee.attendance.index') : '#' }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('employee.attendance.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="clock" class="w-5 h-5 mr-3"></i>
                        Absensi
                    </a>

                    @if(Auth::user() && Auth::user()->role === 'employee')
                    <a href="{{ route('employee.leave-requests.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('employee.leave-requests.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="calendar" class="w-5 h-5 mr-3"></i>
                        Pengajuan Cuti / Izin
                    </a>
                    @endif

                    @if(Auth::user() && Auth::user()->role === 'hr')
                    <a href="{{ route('hr.leave-management.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('hr.leave-management.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="calendar" class="w-5 h-5 mr-3"></i>
                        Pengajuan Cuti & Izin
                    </a>
                    @endif

                    @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'hr'))
                    <a href="{{ route('reports.attendance') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                        Laporan Absensi
                    </a>
                    @endif

                    @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'hr'))
                    <div class="pt-4 mt-4 border-t border-slate-200">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Manajemen</p>

                        <a href="{{ route('management.departments.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.departments.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="briefcase" class="w-5 h-5 mr-3"></i>
                            Departemen
                        </a>

                        <a href="{{ route('management.offices.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.offices.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                            Kantor
                        </a>

                        <a href="{{ route('management.positions.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.positions.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="award" class="w-5 h-5 mr-3"></i>
                            Jabatan
                        </a>

                        <a href="{{ route('management.employees.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.employees.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="users" class="w-5 h-5 mr-3"></i>
                            Karyawan
                        </a>

                        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg font-medium transition-colors">
                            <i data-feather="settings" class="w-5 h-5 mr-3"></i>
                            Pengaturan
                        </a>
                    </div>
                    @endif
                </nav>
            </div>

            <!-- User Menu Sidebar -->
            <div class="p-4 border-t border-slate-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-slate-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen"
             class="fixed inset-0 bg-slate-900/50 z-40 md:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false">
        </div>

        <!-- Mobile Sidebar -->
        <aside x-show="sidebarOpen"
               class="fixed inset-y-0 left-0 bg-white w-64 shadow-xl z-50 transform md:hidden flex flex-col"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">

            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200">
                <span class="text-xl font-bold text-indigo-600">E-Absensi</span>
                <button @click="sidebarOpen = false" class="text-slate-500 hover:text-slate-700">
                    <i data-feather="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="p-4 flex-1 overflow-y-auto">
                <nav class="space-y-1">
                    <!-- Mobile Navigation Links (Same as desktop) -->
                    <a href="{{ Auth::user() ? route(Auth::user()->role . '.dashboard') : '#' }}"
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('*.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="home" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>

                    <a href="{{ Auth::user() && Auth::user()->role === 'employee' ? route('employee.attendance.index') : '#' }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('employee.attendance.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="clock" class="w-5 h-5 mr-3"></i>
                        Absensi
                    </a>

                    @if(Auth::user() && Auth::user()->role === 'employee')
                    <a href="{{ route('employee.leave-requests.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('employee.leave-requests.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="calendar" class="w-5 h-5 mr-3"></i>
                        Pengajuan Cuti / Izin
                    </a>
                    @endif

                    @if(Auth::user() && Auth::user()->role === 'hr')
                    <a href="{{ route('hr.leave-management.index') }}" 
                       class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('hr.leave-management.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="calendar" class="w-5 h-5 mr-3"></i>
                        Pengajuan Cuti & Izin
                    </a>
                    @endif

                    @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'hr'))
                    <a href="{{ route('reports.attendance') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i data-feather="file-text" class="w-5 h-5 mr-3"></i>
                        Laporan Absensi
                    </a>
                    @endif

                    @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'hr'))
                    <div class="pt-4 mt-4 border-t border-slate-200">
                        <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Manajemen</p>

                        <a href="{{ route('management.departments.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.departments.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="briefcase" class="w-5 h-5 mr-3"></i>
                            Departemen
                        </a>

                        <a href="{{ route('management.offices.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.offices.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="map-pin" class="w-5 h-5 mr-3"></i>
                            Kantor
                        </a>

                        <a href="{{ route('management.positions.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.positions.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="award" class="w-5 h-5 mr-3"></i>
                            Jabatan
                        </a>

                        <a href="{{ route('management.employees.index') }}" class="flex items-center px-3 py-2.5 rounded-lg font-medium transition-colors {{ request()->routeIs('management.employees.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i data-feather="users" class="w-5 h-5 mr-3"></i>
                            Karyawan
                        </a>

                        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-indigo-600 rounded-lg font-medium transition-colors">
                            <i data-feather="settings" class="w-5 h-5 mr-3"></i>
                            Pengaturan
                        </a>
                    </div>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">

            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 sticky top-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-700 focus:outline-none mr-4">
                        <i data-feather="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-slate-800 hidden sm:block">@yield('header_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="text-slate-500 hover:text-indigo-600 transition-colors relative">
                        <i data-feather="bell" class="w-5 h-5"></i>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Profile Dropdown (Top right) -->
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ Auth::user()->name }}</span>
                            <i data-feather="chevron-down" class="w-4 h-4 text-slate-500 hidden sm:block"></i>
                        </button>

                        <div x-show="dropdownOpen"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-slate-200 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profil Saya</a>
                            <a href="{{ route('profile.edit') }}#password-section" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Ganti Password</a>
                            <div class="border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8">
                <!-- Mobile Header Title (if topbar title is hidden) -->
                <div class="sm:hidden mb-4">
                     <h1 class="text-xl font-semibold text-slate-800">@yield('header_title', 'Dashboard')</h1>
                </div>

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Initialize Feather Icons -->
    <script>
        feather.replace();
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
                    title: 'Oops...',
                    html: `<ul class="text-left list-disc list-inside text-sm text-red-500">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>`,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
