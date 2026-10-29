@extends('layouts.app')

@section('title', 'Presensi HR')
@section('header_title', 'Presensi Harian')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Card Menu / Aksi Absensi Hari Ini -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Presensi HR Hari Ini</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center sm:text-right space-x-2 sm:space-x-0">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 font-mono text-xl sm:text-2xl font-extrabold tracking-tight"
                    x-data x-init="setInterval(() => $el.innerText = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)">
                    {{ \Carbon\Carbon::now()->format('H:i:s') }}
                </span>
            </div>
        </div>

        <div class="p-5 sm:p-8">
            @if(!$todayAttendance)
                <!-- Belum Check In -->
                <div class="text-center py-6 sm:py-8 max-w-lg mx-auto space-y-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto shadow-inner">
                        <i data-feather="clock" class="w-8 h-8 sm:w-10 sm:h-10"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Anda belum melakukan Check In hari ini</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1.5 leading-relaxed">
                            Jam masuk kerja pukul <strong>08:00 WIB</strong>, jam pulang pukul <strong>16:00 WIB</strong>. Harap pastikan GPS/Lokasi perangkat aktif untuk presensi.
                        </p>
                    </div>
                    <div id="checkin-button" class="pt-2">
                        <button type="button" id="checkin-btn"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg shadow-indigo-600/25 transition-all transform active:scale-98">
                            <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                            Check In Sekarang (Deteksi Lokasi)
                        </button>
                    </div>
                </div>
            @elseif(!$todayAttendance->check_out)
                <!-- Sudah Check In, Belum Check Out -->
                <div class="space-y-6">
                    @if($todayAttendance->office)
                        <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 sm:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs sm:text-sm font-bold text-blue-900">
                                        Kantor: <span>{{ $todayAttendance->office->name }}</span>
                                    </p>
                                    <p class="text-xs text-blue-700 mt-0.5">
                                        Radius valid: {{ number_format($todayAttendance->office->radius) }} meter
                                    </p>
                                </div>
                                <div class="text-left sm:text-right text-xs text-blue-700">
                                    @if($todayAttendance->latitude && $todayAttendance->longitude)
                                        <p class="font-medium text-blue-900">Lokasi Tercatat:</p>
                                        <p>Lat: {{ number_format($todayAttendance->latitude, 5) }}, Lon: {{ number_format($todayAttendance->longitude, 5) }}</p>
                                        @if($todayAttendance->distance !== null)
                                            <p class="font-semibold text-blue-800 mt-0.5">Jarak: {{ number_format($todayAttendance->distance, 0) }} meter</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jam Masuk</span>
                            <p class="text-lg sm:text-xl font-extrabold text-slate-800 mt-1">{{ $todayAttendance->check_in }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Presensi</span>
                            <p class="text-lg sm:text-xl font-extrabold mt-1">
                                @if($todayAttendance->status === 'terlambat')
                                    <span class="text-amber-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Terlambat ({{ $todayAttendance->late_minutes }} mnt)
                                    </span>
                                @else
                                    <span class="text-emerald-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Hadir Tepat Waktu
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jam Keluar</span>
                            <p class="text-lg sm:text-xl font-bold text-slate-400 mt-1">Belum Check Out</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Jadwal pulang 16:00 WIB</p>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <div id="checkout-button">
                            <button type="button" id="checkout-btn"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg shadow-emerald-600/25 transition-all transform active:scale-98">
                                <i data-feather="log-out" class="w-5 h-5 mr-2"></i>
                                Check Out Sekarang (Deteksi Lokasi)
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Sudah Selesai Check In & Check Out -->
                <div class="space-y-6">
                    @if($todayAttendance->office)
                        <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 sm:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs sm:text-sm font-bold text-blue-900">
                                        Kantor: <span>{{ $todayAttendance->office->name }}</span>
                                    </p>
                                    <p class="text-xs text-blue-700 mt-0.5">
                                        Radius valid: {{ number_format($todayAttendance->office->radius) }} meter
                                    </p>
                                </div>
                                <div class="text-left sm:text-right text-xs text-blue-700">
                                    @if($todayAttendance->latitude && $todayAttendance->longitude)
                                        <p class="font-medium text-blue-900">Lokasi Tercatat:</p>
                                        <p>Lat: {{ number_format($todayAttendance->latitude, 5) }}, Lon: {{ number_format($todayAttendance->longitude, 5) }}</p>
                                        @if($todayAttendance->distance !== null)
                                            <p class="font-semibold text-blue-800 mt-0.5">Jarak: {{ number_format($todayAttendance->distance, 0) }} meter</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jam Masuk</span>
                            <p class="text-lg sm:text-xl font-extrabold text-slate-800 mt-1">{{ $todayAttendance->check_in }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Presensi</span>
                            <p class="text-lg sm:text-xl font-extrabold mt-1">
                                @if($todayAttendance->status === 'terlambat')
                                    <span class="text-amber-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Terlambat ({{ $todayAttendance->late_minutes }} mnt)
                                    </span>
                                @else
                                    <span class="text-emerald-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Hadir Tepat Waktu
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jam Keluar</span>
                            <p class="text-lg sm:text-xl font-extrabold text-slate-800 mt-1">{{ $todayAttendance->check_out }}</p>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-semibold mb-4">
                            Presensi hari ini telah selesai lengkap. Terima kasih atas kerja sama Anda!
                        </div>
                        <button type="button" disabled class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 bg-slate-100 text-slate-400 rounded-xl font-bold text-sm cursor-not-allowed">
                            <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                            Sudah Check Out (Presensi Selesai)
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Absensi Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100">
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Riwayat Absensi Anda</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar rekam jejak kehadiran Anda di PT Bintang Anugrah.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Jam Masuk</th>
                        <th class="px-5 py-3.5">Jam Keluar</th>
                        <th class="px-5 py-3.5">Kantor</th>
                        <th class="px-5 py-3.5">Jarak</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Keterlambatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap font-medium text-slate-800">
                                {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                {{ $att->check_in ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                {{ $att->check_out ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                {{ $att->office ? $att->office->name : 'Tidak terdaftar' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                @if($att->distance !== null)
                                    <span class="font-medium">{{ number_format($att->distance, 0) }} m</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($att->status === 'terlambat')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                        Terlambat
                                    </span>
                                @elseif($att->status === 'hadir')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Hadir
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600">
                                @if($att->late_minutes > 0)
                                    <span class="text-amber-600 font-semibold">{{ $att->late_minutes }} menit</span>
                                @else
                                    <span class="text-slate-400">Tepat waktu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-feather="calendar" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada riwayat absensi tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation tidak didukung oleh browser ini'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy
                    });
                },
                (error) => {
                    let msg = 'Gagal mendeteksi lokasi.';
                    if (error.code === error.PERMISSION_DENIED) {
                        msg = 'Izin akses lokasi (GPS) ditolak. Mohon aktifkan izin lokasi di browser Anda.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        msg = 'Informasi lokasi tidak tersedia.';
                    } else if (error.code === error.TIMEOUT) {
                        msg = 'Waktu permintaan lokasi habis.';
                    }
                    reject(new Error(msg));
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    async function performCheckIn() {
        const button = document.getElementById('checkin-btn');
        if (!button) return;
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="inline-block animate-pulse">⏳ Mendeteksi lokasi...</span>';

        try {
            const location = await getLocation();
            const formData = new FormData();
            formData.append('latitude', location.latitude);
            formData.append('longitude', location.longitude);
            formData.append('accuracy', location.accuracy || '');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch('{{ route("hr.attendance.check-in") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Check In!',
                    text: data.message,
                    confirmButtonColor: '#4f46e5'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Check In',
                    text: data.message || 'Terjadi kesalahan saat check in',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Perhatian Lokasi',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        } finally {
            button.disabled = false;
            button.innerHTML = originalHtml;
            feather.replace();
        }
    }

    async function performCheckOut() {
        const button = document.getElementById('checkout-btn');
        if (!button) return;
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="inline-block animate-pulse">⏳ Mendeteksi lokasi...</span>';

        try {
            const location = await getLocation();
            const formData = new FormData();
            formData.append('latitude', location.latitude);
            formData.append('longitude', location.longitude);
            formData.append('accuracy', location.accuracy || '');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const response = await fetch('{{ route("hr.attendance.check-out") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Check Out!',
                    text: data.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Check Out',
                    text: data.message || 'Terjadi kesalahan saat check out',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Perhatian Lokasi',
                text: error.message,
                confirmButtonColor: '#ef4444'
            });
        } finally {
            button.disabled = false;
            button.innerHTML = originalHtml;
            feather.replace();
        }
    }

    document.getElementById('checkin-btn')?.addEventListener('click', performCheckIn);
    document.getElementById('checkout-btn')?.addEventListener('click', performCheckOut);
</script>
@endpush
