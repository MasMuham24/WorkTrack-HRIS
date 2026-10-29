@extends('layouts.app')

@section('title', 'Tambah Karyawan')
@section('header_title', 'Manajemen Karyawan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Tambah Karyawan Baru</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftarkan akun dan profil lengkap karyawan baru.</p>
        </div>
        <div>
            <a href="{{ route('management.employees.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <form action="{{ route('management.employees.store') }}" method="POST">
            @csrf
            
            <div class="p-5 sm:p-8 space-y-5">
                <!-- Kode & Nama -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                    <div>
                        <label for="employee_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kode Karyawan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm font-mono text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: EMP002">
                        @error('employee_code')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: Budi Santoso">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: budi.santoso">
                        @error('username')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: budi@bintanganugrah.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Telepon -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Telepon
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Contoh: 081234567890">
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departemen & Jabatan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="departement_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Departemen <span class="text-rose-500">*</span>
                        </label>
                        <select name="departement_id" id="departement_id" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('departement_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('departement_id')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="position_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jabatan <span class="text-rose-500">*</span>
                        </label>
                        <select name="position_id" id="position_id" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" data-dept="{{ $pos->departement_id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->name }} ({{ $pos->departement->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Tanggal Masuk & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="hire_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Bergabung <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                        @error('hire_date')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Akun <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 pt-2 border-t border-slate-100">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Akun <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="reset" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Reset
                </button>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition">
                    <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                    Simpan Data Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const positions = @json($positions);
    const deptSelect = document.getElementById('departement_id');
    const posSelect = document.getElementById('position_id');

    function filterPositions() {
        const selectedDept = deptSelect.value;
        const currentVal = posSelect.value;
        posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
        
        positions.forEach(pos => {
            if (!selectedDept || pos.departement_id == selectedDept) {
                const option = document.createElement('option');
                option.value = pos.id;
                option.textContent = pos.name;
                option.dataset.dept = pos.departement_id;
                if (currentVal == pos.id) option.selected = true;
                posSelect.appendChild(option);
            }
        });
    }

    deptSelect.addEventListener('change', function() {
        posSelect.value = '';
        filterPositions();
    });

    filterPositions();
</script>
@endpush