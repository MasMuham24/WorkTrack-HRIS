@extends('layouts.auth')

@section('title', 'Login Presensi')

@section('content')
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-slate-900"
     style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">

    <!-- Soft Overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950/80 via-indigo-950/70 to-slate-900/80 backdrop-blur-xs"></div>

    <div class="max-w-md w-full relative z-10 animate__animated animate__fadeInUp animate__faster">
        <div class="bg-white/95 backdrop-blur-md p-6 sm:p-10 rounded-2xl shadow-2xl border border-white/20">

            <!-- Logo & Title -->
            <div class="text-center">
                <div class="mx-auto h-14 w-14 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/30 text-white">
                    <i data-feather="check-square" class="w-7 h-7"></i>
                </div>

                <h2 class="mt-4 text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    Sistem E-Absensi
                </h2>
                <p class="mt-1 text-xs font-semibold text-indigo-600 uppercase tracking-widest">
                    PT BINTANG ANUGRAH
                </p>
                <p class="mt-2 text-xs text-slate-500">
                    Silakan masukkan akun Anda untuk mencatat kehadiran
                </p>
            </div>

            <!-- Login Form -->
            <form class="mt-6 space-y-4 sm:space-y-5" action="{{ route('login.store') }}" method="POST">
                @csrf

                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="user" class="w-4 h-4"></i>
                        </div>
                        <input id="username" name="username" type="text" required value="{{ old('username') }}" autocomplete="username"
                            class="appearance-none block w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition duration-200 text-sm"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                <!-- Password Input -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-feather="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                            class="appearance-none block w-full pl-10 pr-10 py-2.5 sm:py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition duration-200 text-sm"
                            placeholder="Masukkan password Anda">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none transition">
                            <span x-show="!show" class="text-xs font-medium text-slate-500">Lihat</span>
                            <span x-show="show" style="display: none;" class="text-xs font-medium text-indigo-600">Sembunyi</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded bg-slate-50">
                        <span class="ml-2 text-xs text-slate-600 font-medium">
                            Ingat sesi saya
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex items-center justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 shadow-lg shadow-indigo-600/30 transform active:scale-[0.98]">
                        <i data-feather="log-in" class="w-4 h-4 mr-2"></i>
                        Masuk ke Sistem
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400">
                    &copy; 2026 PT Bintang Anugrah. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
