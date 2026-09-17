@extends('layouts.app')

@section('title', 'Dashboard HR')
@section('header_title', 'Dashboard HR')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i data-feather="users" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Total Karyawan</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $usersCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i data-feather="check-circle" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Karyawan Aktif</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $activeEmployeesCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i data-feather="user-minus" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Tidak Aktif</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $inactiveUsersCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                <i data-feather="clock" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Cuti Menunggu</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $pendingLeaveRequestsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                <i data-feather="check-circle" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Cuti Disetujui</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $approvedLeaveRequestsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                <i data-feather="file-text" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Ijin Menunggu</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $pendingLeaveRequestsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <i data-feather="briefcase" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Departemen</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $departmentsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <i data-feather="award" class="w-6 h-6"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-slate-500">Posisi</h3>
                <p class="text-2xl font-bold text-slate-800">{{ $positionsCount }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Pengajuan Cuti Menunggu Persetujuan</h2>
        </div>
        <div class="p-6">
            @if($pendingLeaves->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingLeaves as $leave)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $leave->user->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($leave->leave_type) }} ({{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }})</p>
                            </div>
                            <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                    ACC
                                </button>
                            </form>
                            <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 hover:bg-rose-200">
                                    REJ
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 text-center py-6">Tidak ada pengajuan cuti tertunda.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Pengajuan Ijin Menunggu Persetujuan</h2>
        </div>
        <div class="p-6">
            @if($pendingLeaves->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingLeaves as $leave)
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $leave->user->name ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($leave->leave_type) }} ({{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M') }})</p>
                            </div>
                            <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 hover:bg-emerald-200">
                                    ACC
                                </button>
                            </form>
                            <form action="{{ route('hr.leave-requests.update', $leave->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800 hover:bg-rose-200">
                                    REJ
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400 text-center py-6">Tidak ada pengajuan ijin tertunda.</p>
            @endif
        </div>
    </div>
</div>
@endsection