@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header_title', 'Pengaturan Akun & Profil')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Informasi Profil Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-base shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Informasi Pribadi & Kontak</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, username, email, dan nomor telepon akun Anda.</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                {{ $user->role }}
            </span>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="p-5 sm:p-8 space-y-5">
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white">
                        @error('username')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Telepon -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Telepon (WhatsApp)
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Contoh: 081234567890">
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Metadata Pegawai Non-editable -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kode Karyawan</span>
                        <span class="block text-sm font-mono font-bold text-slate-800 mt-0.5">{{ $user->employee_code ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Hak Akses Role</span>
                        <span class="block text-sm font-bold text-slate-800 mt-0.5 uppercase">{{ $user->role }}</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-end">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/20 transition">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- Ganti Password Card -->
    <div id="password-section" class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base shrink-0">
                <i data-feather="lock" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800">Keamanan & Ganti Password</h2>
                <p class="text-xs text-slate-500 mt-0.5">Gunakan kombinasi password yang kuat dan aman untuk melindungi akun Anda.</p>
            </div>
        </div>

        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-5 sm:p-8 space-y-5">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password Saat Ini <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="current_password" id="current_password" required
                        class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                        placeholder="Masukkan password Anda sekarang">
                    @error('current_password')
                        <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Konfirmasi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Ketik ulang password baru">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-end">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-amber-600/20 transition">
                    <i data-feather="key" class="w-4 h-4 mr-2"></i>
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
