@extends('layouts.app')

@section('title', 'Presensi Karyawan')
@section('header_title', 'Presensi Harian')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #attendance-map {
            height: 300px;
            width: 100%;
            border-radius: 0.75rem;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Card Menu / Aksi Absensi Hari Ini -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Presensi Hari Ini</h2>
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
                            Jam kerja mengikuti jadwal kantor yang terdeteksi saat Check In. Harap pastikan GPS/Lokasi perangkat aktif untuk presensi.
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
                    <!-- Location Info Display -->
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
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                @if($scheduledCheckOut)
                                    Jadwal pulang {{ $scheduledCheckOut->format('H:i') }} WIB
                                @else
                                    Jadwal pulang kantor belum tersedia
                                @endif
                            </p>
                        </div>
                    </div>

                    @php
                        $checkOutAvailable = $scheduledCheckOut !== null && \Carbon\Carbon::now()->greaterThanOrEqualTo($scheduledCheckOut);
                    @endphp
                    <div class="text-center pt-2">
                        <div id="checkout-button">
                            <button type="button" id="checkout-btn"
                                data-checkout-at="{{ $scheduledCheckOut?->toIso8601String() }}"
                                data-checkout-label="{{ $scheduledCheckOut?->format('H:i') }}"
                                data-checkout-enabled="{{ $checkOutAvailable ? 'true' : 'false' }}"
                                @disabled(! $checkOutAvailable)
                                class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm sm:text-base rounded-xl shadow-lg shadow-emerald-600/25 transition-all transform active:scale-98 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed disabled:shadow-none disabled:hover:bg-slate-100">
                                <i data-feather="log-out" class="w-5 h-5 mr-2"></i>
                                Check Out Sekarang (Deteksi Lokasi)
                            </button>
                            <p id="checkout-schedule-note" class="text-xs text-slate-500 mt-3">
                                @if($scheduledCheckOut)
                                    @if($checkOutAvailable)
                                        Check out sudah dapat dilakukan.
                                    @else
                                        Check out dapat dilakukan mulai pukul {{ $scheduledCheckOut->format('H:i') }} WIB.
                                    @endif
                                @else
                                    Jadwal pulang kantor tidak tersedia. Hubungi administrator.
                                @endif
                            </p>
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
                            Presensi hari ini telah selesai lengkap. Terima kasih atas dedikasi dan kerja keras Anda!
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

    <!-- Peta Lokasi Kantor -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Peta Lokasi Kantor</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    @if($todayAttendance && $todayAttendance->office)
                        Menampilkan kantor presensi hari ini beserta radius geofencing.
                    @else
                        Semua kantor terdaftar yang dapat digunakan untuk presensi.
                    @endif
                </p>
            </div>
            <button type="button" id="my-location-btn"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs sm:text-sm rounded-xl border border-indigo-200 transition-colors shrink-0">
                <i data-feather="navigation" class="w-4 h-4"></i>
                Lokasi Saya
            </button>
        </div>

        <div class="p-4 sm:p-5 space-y-3">
            <!-- Status GPS -->
            <div id="gps-status" class="hidden text-xs rounded-xl px-3 py-2.5 font-medium"></div>

            <!-- Map Container -->
            <div id="attendance-map" class="border border-slate-200"></div>

            <!-- Legenda -->
            <div class="flex flex-wrap gap-3 text-xs text-slate-500 pt-1">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-600 border-2 border-white shadow-sm inline-block"></span>
                    Kantor
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-200 border border-indigo-400 inline-block"></span>
                    Radius Geofencing
                </span>
                <span class="inline-flex items-center gap-1.5" id="legend-user" style="display:none!important">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white shadow-sm inline-block"></span>
                    Posisi Anda
                </span>
                @if($todayAttendance && $todayAttendance->latitude && $todayAttendance->longitude)
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 border-2 border-white shadow-sm inline-block"></span>
                    Posisi Check-In
                </span>
                @endif
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed">
                <i data-feather="info" class="w-3 h-3 inline-block mr-0.5"></i>
                Tombol "Lokasi Saya" hanya menampilkan posisi di peta sebagai indikasi visual. Keputusan presensi tetap ditentukan oleh validasi GPS di server saat Anda menekan Check In/Out.
            </p>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // ─── Check-in / Check-out logic (tidak diubah) ───────────────────────────
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

            const response = await fetch('{{ route("employee.attendance.check-in") }}', {
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
        if (!button || button.disabled) return;
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

            const response = await fetch('{{ route("employee.attendance.check-out") }}', {
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
            button.innerHTML = originalHtml;
            syncCheckoutButton();
            feather.replace();
        }
    }

    let checkoutTimer = null;

    function syncCheckoutButton() {
        const button = document.getElementById('checkout-btn');
        if (!button) return false;

        const checkoutAt = new Date(button.dataset.checkoutAt);
        const note = document.getElementById('checkout-schedule-note');

        if (!button.dataset.checkoutAt || Number.isNaN(checkoutAt.getTime())) {
            button.disabled = true;
            button.dataset.checkoutEnabled = 'false';
            return false;
        }

        const allowed = Date.now() >= checkoutAt.getTime();
        button.disabled = !allowed;
        button.dataset.checkoutEnabled = allowed ? 'true' : 'false';

        if (note) {
            note.textContent = allowed
                ? 'Check out sudah dapat dilakukan.'
                : `Check out dapat dilakukan mulai pukul ${button.dataset.checkoutLabel} WIB.`;
        }

        return allowed;
    }

    function startCheckoutTimer() {
        const button = document.getElementById('checkout-btn');
        if (!button || !button.dataset.checkoutAt) return;

        if (syncCheckoutButton()) return;

        checkoutTimer = setInterval(() => {
            if (syncCheckoutButton()) {
                clearInterval(checkoutTimer);
                checkoutTimer = null;
            }
        }, 1000);
    }

    startCheckoutTimer();
    window.addEventListener('beforeunload', () => {
        if (checkoutTimer) {
            clearInterval(checkoutTimer);
            checkoutTimer = null;
        }
    });

    document.getElementById('checkin-btn')?.addEventListener('click', performCheckIn);
    document.getElementById('checkout-btn')?.addEventListener('click', performCheckOut);

    // ─── Peta Leaflet ─────────────────────────────────────────────────────────
    @php
        $attendanceOffice = ($todayAttendance && $todayAttendance->office) ? $todayAttendance->office : null;
        $mapOfficeData = [];
        if ($attendanceOffice) {
            $mapOfficeData[] = [
                'name' => $attendanceOffice->name,
                'lat' => (float) $attendanceOffice->latitude,
                'lng' => (float) $attendanceOffice->longitude,
                'radius' => (int) $attendanceOffice->radius,
            ];
        } else {
            foreach ($offices as $office) {
                $mapOfficeData[] = [
                    'name' => $office->name,
                    'lat' => (float) $office->latitude,
                    'lng' => (float) $office->longitude,
                    'radius' => (int) $office->radius,
                ];
            }
        }
        $mapCheckinLat = ($todayAttendance && $todayAttendance->latitude) ? (float) $todayAttendance->latitude : null;
        $mapCheckinLng = ($todayAttendance && $todayAttendance->longitude) ? (float) $todayAttendance->longitude : null;
    @endphp

    (function () {
        'use strict';

        var officeData = @json($mapOfficeData);
        var checkinLat = @json($mapCheckinLat);
        var checkinLng = @json($mapCheckinLng);

        var mapEl = document.getElementById('attendance-map');
        if (!mapEl || !window.L) return;
        if (officeData.length === 0) {
            mapEl.innerHTML = '<div class="flex items-center justify-center h-full text-slate-400 text-sm">Tidak ada data kantor.</div>';
            return;
        }

        // Tentukan pusat peta dari rata-rata semua kantor
        var avgLat = officeData.reduce(function (s, o) { return s + o.lat; }, 0) / officeData.length;
        var avgLng = officeData.reduce(function (s, o) { return s + o.lng; }, 0) / officeData.length;

        var map = L.map('attendance-map').setView([avgLat, avgLng], officeData.length === 1 ? 15 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Marker icon kantor (biru indigo)
        var officeIcon = L.divIcon({
            className: '',
            html: '<div style="width:14px;height:14px;background:#4f46e5;border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(79,70,229,.5)"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        var bounds = L.latLngBounds();

        officeData.forEach(function (office) {
            if (!isFinite(office.lat) || !isFinite(office.lng)) return;

            var latlng = [office.lat, office.lng];
            bounds.extend(latlng);

            L.marker(latlng, { icon: officeIcon })
                .bindTooltip('<strong>' + office.name + '</strong><br>Radius: ' + office.radius + ' m', { permanent: false })
                .addTo(map);

            L.circle(latlng, {
                radius: office.radius,
                color: '#4f46e5',
                fillColor: '#4f46e5',
                fillOpacity: 0.08,
                weight: 2
            }).addTo(map);
        });

        // Marker posisi check-in karyawan (amber)
        if (checkinLat !== null && checkinLng !== null && isFinite(checkinLat) && isFinite(checkinLng)) {
            var checkinIcon = L.divIcon({
                className: '',
                html: '<div style="width:12px;height:12px;background:#f59e0b;border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(245,158,11,.5)"></div>',
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });
            L.marker([checkinLat, checkinLng], { icon: checkinIcon })
                .bindTooltip('Posisi Check-In Anda', { permanent: false })
                .addTo(map);
            bounds.extend([checkinLat, checkinLng]);
        }

        // Fit bounds jika ada lebih dari 1 titik
        if (bounds.isValid()) {
            if (officeData.length === 1 && checkinLat === null) {
                map.setView([officeData[0].lat, officeData[0].lng], 15);
            } else {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
            }
        }

        // ─── Tombol "Lokasi Saya" ───────────────────────────────────────────
        var myLocationBtn = document.getElementById('my-location-btn');
        var gpsStatus = document.getElementById('gps-status');
        var legendUser = document.getElementById('legend-user');
        var userMarker = null;
        var userCircle = null;

        // Marker icon user (hijau emerald)
        var userIcon = L.divIcon({
            className: '',
            html: '<div style="width:14px;height:14px;background:#10b981;border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(16,185,129,.5)"></div>',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        function setGpsStatus(msg, type) {
            gpsStatus.className = 'text-xs rounded-xl px-3 py-2.5 font-medium';
            gpsStatus.style.display = 'block';
            if (type === 'success') {
                gpsStatus.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
            } else if (type === 'warning') {
                gpsStatus.classList.add('bg-amber-50', 'text-amber-800', 'border', 'border-amber-200');
            } else {
                gpsStatus.classList.add('bg-rose-50', 'text-rose-800', 'border', 'border-rose-200');
            }
            gpsStatus.textContent = msg;
        }

        // Haversine distance (meter) — untuk indikasi visual saja
        function haversine(lat1, lng1, lat2, lng2) {
            var R = 6371000;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLng = (lng2 - lng1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        if (myLocationBtn) {
            myLocationBtn.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    setGpsStatus('Browser Anda tidak mendukung geolocation.', 'error');
                    return;
                }

                myLocationBtn.disabled = true;
                myLocationBtn.innerHTML = '<span class="inline-block animate-pulse">⏳ Mendeteksi...</span>';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        var acc = position.coords.accuracy ? Math.round(position.coords.accuracy) : null;

                        // Tampilkan / perbarui marker user
                        if (userMarker) {
                            userMarker.setLatLng([lat, lng]);
                        } else {
                            userMarker = L.marker([lat, lng], { icon: userIcon })
                                .bindTooltip('Posisi Anda', { permanent: false })
                                .addTo(map);
                        }

                        // Lingkaran akurasi GPS
                        if (acc && acc > 0) {
                            if (userCircle) {
                                userCircle.setLatLng([lat, lng]);
                                userCircle.setRadius(acc);
                            } else {
                                userCircle = L.circle([lat, lng], {
                                    radius: acc,
                                    color: '#10b981',
                                    fillColor: '#10b981',
                                    fillOpacity: 0.06,
                                    weight: 1,
                                    dashArray: '4'
                                }).addTo(map);
                            }
                        }

                        map.setView([lat, lng], 15);
                        legendUser.style.removeProperty('display');

                        // Hitung jarak ke kantor terdekat dari officeData (indikasi visual)
                        var distInfo = '';
                        if (officeData.length > 0) {
                            var nearest = null;
                            var minDist = Infinity;
                            officeData.forEach(function (o) {
                                var d = haversine(lat, lng, o.lat, o.lng);
                                if (d < minDist) { minDist = d; nearest = o; }
                            });
                            if (nearest) {
                                var distM = Math.round(minDist);
                                var inside = minDist <= nearest.radius;
                                distInfo = ' | Jarak ke ' + nearest.name + ': ' + distM + ' m — ' +
                                    (inside ? '✅ Dalam radius' : '❌ Luar radius');
                            }
                        }

                        var accText = acc ? ' (akurasi ±' + acc + ' m)' : '';
                        setGpsStatus('Posisi Anda berhasil ditampilkan' + accText + '.' + distInfo + ' (Hanya indikasi visual.)', 'success');

                        // Restore button
                        myLocationBtn.disabled = false;
                        myLocationBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg> Lokasi Saya';
                        feather.replace();
                    },
                    function (error) {
                        var msg = 'Gagal mendeteksi lokasi.';
                        if (error.code === error.PERMISSION_DENIED) {
                            msg = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser untuk menggunakan fitur ini.';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            msg = 'Informasi lokasi tidak tersedia saat ini.';
                        } else if (error.code === error.TIMEOUT) {
                            msg = 'Permintaan lokasi timeout. Coba lagi.';
                        }
                        setGpsStatus(msg, 'error');
                        myLocationBtn.disabled = false;
                        myLocationBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg> Lokasi Saya';
                        feather.replace();
                    },
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                );
            });
        }
    })();
</script>
@endpush
