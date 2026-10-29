@auth
@php
    $role = Auth::user()->role;
@endphp

<nav class="space-y-1">
    <!-- Section: Menu Utama -->
    <p class="px-3 pt-2 pb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menu Utama</p>

    <a href="{{ route($role . '.dashboard') }}"
        class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ request()->routeIs('*.dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
        <i data-feather="home" class="w-4 h-4 mr-3 {{ request()->routeIs('*.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
        Dashboard
    </a>

    @if(in_array($role, ['admin', 'hr', 'employee']))
        @php
            $attendanceRoute = match ($role) {
                'admin' => 'admin.attendances.index',
                'hr' => 'hr.attendance.index',
                default => 'employee.attendance.index',
            };
            $isAttendanceActive = request()->routeIs('admin.attendances.*', 'hr.attendance.*', 'employee.attendance.*');
        @endphp
        <a href="{{ route($attendanceRoute) }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isAttendanceActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
            <i data-feather="clock" class="w-4 h-4 mr-3 {{ $isAttendanceActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            Presensi / Absensi
        </a>
    @endif

    @if($role === 'employee')
        @php
            $isLeaveActive = request()->routeIs('employee.leave-requests.*');
        @endphp
        <a href="{{ route('employee.leave-requests.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isLeaveActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
            <i data-feather="calendar" class="w-4 h-4 mr-3 {{ $isLeaveActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            Cuti & Izin
        </a>
    @endif

    @if($role === 'hr')
        @php
            $isLeaveMgmtActive = request()->routeIs('hr.leave-management.*', 'hr.leave-requests.*');
        @endphp
        <a href="{{ route('hr.leave-management.index') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isLeaveMgmtActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
            <i data-feather="calendar" class="w-4 h-4 mr-3 {{ $isLeaveMgmtActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            Pengajuan Cuti & Izin
        </a>
    @endif

    @if(in_array($role, ['admin', 'hr']))
        @php
            $isReportActive = request()->routeIs('reports.*');
        @endphp
        <a href="{{ route('reports.attendance') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isReportActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
            <i data-feather="file-text" class="w-4 h-4 mr-3 {{ $isReportActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            Laporan Absensi
        </a>
    @endif

    <!-- Section: Manajemen (Admin & HR) -->
    @if(in_array($role, ['admin', 'hr']))
        <div class="pt-4 mt-3 border-t border-slate-100">
            <p class="px-3 pb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Manajemen</p>

            @php
                $isDeptActive = request()->routeIs('management.departments.*');
            @endphp
            <a href="{{ route('management.departments.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isDeptActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i data-feather="briefcase" class="w-4 h-4 mr-3 {{ $isDeptActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                Departemen
            </a>

            @if($role === 'admin')
                @php
                    $isOfficeActive = request()->routeIs('management.offices.*');
                @endphp
                <a href="{{ route('management.offices.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isOfficeActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                    <i data-feather="map-pin" class="w-4 h-4 mr-3 {{ $isOfficeActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                    Kantor / Lokasi
                </a>
            @endif

            @php
                $isPosActive = request()->routeIs('management.positions.*');
            @endphp
            <a href="{{ route('management.positions.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isPosActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i data-feather="award" class="w-4 h-4 mr-3 {{ $isPosActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                Jabatan
            </a>

            @php
                $isEmpActive = request()->routeIs('management.employees.*');
            @endphp
            <a href="{{ route('management.employees.index') }}"
                class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isEmpActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
                <i data-feather="users" class="w-4 h-4 mr-3 {{ $isEmpActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                Karyawan
            </a>
        </div>
    @endif

    <!-- Section: Akun Pengguna -->
    <div class="pt-4 mt-3 border-t border-slate-100">
        <p class="px-3 pb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengaturan Akun</p>

        @php
            $isProfileActive = request()->routeIs('profile.*');
        @endphp
        <a href="{{ route('profile.edit') }}"
            class="flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group {{ $isProfileActive ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 font-semibold' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900' }}">
            <i data-feather="user" class="w-4 h-4 mr-3 {{ $isProfileActive ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            Profil Saya
        </a>
    </div>
</nav>
@endauth