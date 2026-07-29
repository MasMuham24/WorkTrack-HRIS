@extends('layouts.app')

@section('title', 'Data Kantor')
@section('header_title', 'Manajemen Kantor')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-xl font-semibold text-slate-800">Daftar Kantor</h2>
        <p class="text-sm text-slate-500 mt-1">Kelola data kantor di perusahaan.</p>
    </div>
    
    <div>
        <a href="{{ route('management.offices.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-colors w-full sm:w-auto">
            <i data-feather="plus" class="w-4 h-4 mr-2"></i>
            Tambah Kantor
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="relative w-full sm:max-w-xs">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i data-feather="search" class="w-4 h-4"></i>
            </div>
            <form method="GET" action="{{ route('management.offices.index') }}">
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm placeholder-slate-400" placeholder="Cari kantor...">
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Kantor</th>
                    <th class="px-6 py-4">Latitude</th>
                    <th class="px-6 py-4">Longitude</th>
                    <th class="px-6 py-4">Radius (Meter)</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($offices as $index => $office)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $offices->firstItem() + $index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-800">{{ $office->name }}</td>
                    <td class="px-6 py-4">{{ number_format($office->latitude, 7) }}</td>
                    <td class="px-6 py-4">{{ number_format($office->longitude, 7) }}</td>
                    <td class="px-6 py-4">{{ $office->radius }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end">
                            <a href="{{ route('management.offices.edit', ['office' => $office->id]) }}" class="text-indigo-600 hover:text-indigo-900 mx-1 bg-indigo-50 p-2 rounded-md hover:bg-indigo-100 transition-colors" title="Edit">
                                <i data-feather="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('management.offices.destroy', ['office' => $office->id]) }}" method="POST" class="inline-block m-0 p-0 delete-form" data-id="{{ $office->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-900 mx-1 bg-red-50 p-2 rounded-md hover:bg-red-100 transition-colors" title="Hapus">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-feather="inbox" class="w-10 h-10 text-slate-300 mb-3"></i>
                            <p>Belum ada data kantor.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($offices->hasPages())
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        {{ $offices->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.querySelector('button').addEventListener('click', function () {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data kantor yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
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
