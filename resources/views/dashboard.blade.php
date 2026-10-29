@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 sm:p-10 text-white shadow-xl shadow-indigo-650/20 text-center relative overflow-hidden">
        <div class="relative z-10 space-y-4">
            <div class="inline-flex items-center justify-center py-1.5 px-4 bg-white/15 backdrop-blur-xs rounded-full text-xs font-semibold uppercase tracking-wider text-white">
                <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                Sistem E-Absensi Aktif
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Selamat Datang, {{ $user->name }}!
            </h1>

            <p class="text-indigo-100 text-sm sm:text-base max-w-lg mx-auto">
                Anda masuk sebagai <strong class="text-white">{{ $roleLabel }}</strong> di <span class="text-white font-semibold">PT BINTANG ANUGRAH</span>.
            </p>

            <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-md mx-auto text-left">
                <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/10">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-200">Username</span>
                    <span class="block text-sm font-bold text-white mt-0.5">{{ $user->username }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/10">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-200">ID Karyawan</span>
                    <span class="block text-sm font-bold text-white mt-0.5">{{ $user->employee_code ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    </div>
</div>
@endsection
