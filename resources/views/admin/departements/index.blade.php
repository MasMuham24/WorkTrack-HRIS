@extends('layouts.app')

@section('title', 'Data Departemen')
@section('header_title', 'Manajemen Departemen')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Daftar Departemen</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data struktur departemen / divisi perusahaan.</p>
        </div>
        
        <div>
            <a href="{{ route('management.departments.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Tambah Departemen
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" action="{{ route('management.departments.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative flex-1 max-w-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-feather="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 bg-white placeholder-slate-400"
                        placeholder="Cari nama atau deskripsi departemen...">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-xs">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('management.departments.index') }}" class="px-3 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 font-semibold text-xs sm:text-sm rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[540px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 w-16">No</th>
                        <th class="px-5 py-3.5">Nama Departemen</th>
                        <th class="px-5 py-3.5">Deskripsi</th>
                        <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($departements as $index => $dept)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-400 font-mono">
                            {{ $departements->firstItem() + $index }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800 text-sm">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                </div>
                                <span>{{ $dept->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 text-xs sm:text-sm">
                            {{ $dept->description ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right text-sm">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('management.departments.edit', ['departemen' => $dept->id]) }}"
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('management.departments.destroy', ['departemen' => $dept->id]) }}" method="POST" class="inline m-0 delete-form">
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
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">
                            <div class="flex flex-col items-center justify-center">
                                <i data-feather="inbox" class="w-8 h-8 text-slate-300 mb-2"></i>
                                <p class="font-medium text-slate-500">Belum ada data departemen.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($departements->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $departements->links() }}
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
                title: 'Hapus Departemen?',
                text: 'Data yang dihapus tidak dapat dipulihkan kembali!',
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
