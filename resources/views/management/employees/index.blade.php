@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('header_title', 'Manajemen Karyawan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Daftar Seluruh Karyawan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola akun dan data kepegawaian perusahaan.</p>
        </div>
        
        <div>
            <a href="{{ route('management.employees.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                Tambah Karyawan
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <!-- Filter Form -->
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" action="{{ route('management.employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-feather="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        class="block w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white placeholder-slate-400"
                        placeholder="Nama / Kode / Username...">
                </div>

                <div>
                    <select name="departement_id" class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ ($departementId ?? '') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status" class="block w-full px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-xs">
                        <i data-feather="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                        Filter
                    </button>
                    @if($search || $departementId || $status)
                        <a href="{{ route('management.employees.index') }}" class="px-3 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 font-semibold text-xs sm:text-sm rounded-xl transition" title="Reset Filter">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 w-16">No</th>
                        <th class="px-5 py-3.5">Karyawan</th>
                        <th class="px-5 py-3.5">Username & Email</th>
                        <th class="px-5 py-3.5">Departemen</th>
                        <th class="px-5 py-3.5">Jabatan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($employees as $index => $emp)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-400 font-mono">
                            {{ $employees->firstItem() + $index }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($emp->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $emp->name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $emp->employee_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs sm:text-sm">
                            <div class="font-medium text-slate-800">{{ $emp->username }}</div>
                            <div class="text-xs text-slate-400">{{ $emp->email }}</div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $emp->department->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ $emp->position->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($emp->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right text-sm">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('management.employees.edit', $emp->id) }}"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('management.employees.destroy', $emp->id) }}" method="POST" class="inline m-0 delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <i data-feather="users" class="w-8 h-8 text-slate-300 mb-2"></i>
                                <p class="font-medium text-slate-500">Tidak ada data karyawan yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($employees->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.querySelector('button').addEventListener('click', function () {
            Swal.fire({
                title: 'Hapus Karyawan?',
                text: 'Data akun karyawan yang dihapus tidak dapat dipulihkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush