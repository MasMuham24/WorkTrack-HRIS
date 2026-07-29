@extends('layouts.app')

@section('title', 'Presensi Karyawan')
@section('header_title', 'Absensi Harian')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Card Menu / Aksi Absensi Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Presensi Hari Ini</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="text-right">
                <span class="text-2xl font-bold text-indigo-600" x-data x-init="setInterval(() => $el.innerText = new Date().toLocaleTimeString(), 1000)">
                    {{ \Carbon\Carbon::now()->format('H:i:s') }}
                </span>
            </div>
        </div>

        <div class="p-6">
            @if(!$todayAttendance)
                <!-- Belum Check In -->
                <div class="text-center py-8 space-y-4">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto">
                        <i data-feather="clock" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">Anda belum melakukan Check In hari ini</h3>
                        <p class="text-sm text-slate-500 mt-1">Jam masuk kerja pukul 08:00 WIB, jam pulang pukul 16:00 WIB. Harap melakukan check-in tepat waktu.</p>
                    </div>
                    <div id="checkin-button">
                        <button type="button" id="checkin-btn" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5">
                            <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                            Check In Sekarang (Deteksi Lokasi)
                        </button>
                    </div>
                </div>
            @elseif(!$todayAttendance->check_out)
                <!-- Sudah Check In, Belum Check Out (Tombol berubah menjadi Check Out) -->
                <div class="space-y-6">
                    <!-- Location Info Display -->
                    @if($todayAttendance->office)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-blue-800">Kantor Terdaftar: <span class="font-semibold">{{ $todayAttendance->office->name }}</span></p>
                                <p class="text-sm text-blue-600">Radius: {{ number_format($todayAttendance->office->radius) }} meter</p>
                            </div>
                            <div>
                                @if($todayAttendance->latitude && $todayAttendance->longitude)
                                <p class="text-sm font-medium text-blue-800">Lokasi Anda:</p>
                                <p class="text-sm text-blue-600">Lat: {{ number_format($todayAttendance->latitude, 6) }}, Lon: {{ number_format($todayAttendance->longitude, 6) }}</p>
                                @if($todayAttendance->distance)
                                <p class="text-sm text-blue-600">Jarak: {{ number_format($todayAttendance->distance, 0) }} meter dari kantor</p>
                                @endif
                                @if($todayAttendance->accuracy)
                                <p class="text-sm text-blue-600">Akurasi GPS: {{ number_format($todayAttendance->accuracy, 2) }} meter</p>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Masuk</span>
                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $todayAttendance->check_in }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</span>
                            <p class="text-lg font-bold mt-1">
                                @if($todayAttendance->status === 'terlambat')
                                    <span class="text-amber-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Terlambat ({{ $todayAttendance->late_minutes }} mnt)
                                    </span>
                                @else
                                    <span class="text-emerald-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Hadir
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Keluar</span>
                            <p class="text-lg font-bold text-slate-400 mt-1">Belum Check Out</p>
                            <p class="text-xs text-slate-400 mt-0.5">Jadwal pulang 16:00 WIB</p>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <div id="checkout-button">
                            <button type="button" id="checkout-btn" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 border border-transparent rounded-xl font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5">
                                <i data-feather="log-out" class="w-5 h-5 mr-2"></i>
                                Check Out Sekarang (Deteksi Lokasi)
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Sudah Selesai Check In & Check Out (Tombol Disabled) -->
                <div class="space-y-6">
                    <!-- Location Info Display -->
                    @if($todayAttendance->office)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-blue-800">Kantor Terdaftar: <span class="font-semibold">{{ $todayAttendance->office->name }}</span></p>
                                <p class="text-sm text-blue-600">Radius: {{ number_format($todayAttendance->office->radius) }} meter</p>
                            </div>
                            <div>
                                @if($todayAttendance->latitude && $todayAttendance->longitude)
                                <p class="text-sm font-medium text-blue-800">Lokasi Anda:</p>
                                <p class="text-sm text-blue-600">Lat: {{ number_format($todayAttendance->latitude, 6) }}, Lon: {{ number_format($todayAttendance->longitude, 6) }}</p>
                                @if($todayAttendance->distance)
                                <p class="text-sm text-blue-600">Jarak: {{ number_format($todayAttendance->distance, 0) }} meter dari kantor</p>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Masuk</span>
                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $todayAttendance->check_in }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</span>
                            <p class="text-lg font-bold mt-1">
                                @if($todayAttendance->status === 'terlambat')
                                    <span class="text-amber-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 mr-2"></span> Terlambat ({{ $todayAttendance->late_minutes }} mnt)
                                    </span>
                                @else
                                    <span class="text-emerald-600 inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Hadir
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jam Keluar</span>
                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $todayAttendance->check_out }}</p>
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-medium mb-4">
                            Presensi hari ini telah selesai. Terima kasih atas kerja keras Anda!
                        </div>
                        <button type="button" disabled class="inline-flex items-center justify-center px-6 py-3 bg-slate-200 text-slate-400 rounded-xl font-semibold cursor-not-allowed">
                            <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                            Sudah Check Out (Selesai)
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Absensi Milik User Yang Sedang Login -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Riwayat Absensi Anda</h2>
            <p class="text-sm text-slate-500 mt-0.5">Daftar rekam jejak kehadiran Anda di PT Bintang Anugrah.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jam Keluar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kantor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jarak</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterlambatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-800">
                                {{ \Carbon\Carbon::parse($att->attendance_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $att->check_in ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $att->check_out ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $att->office ? $att->office->name : 'Tidak terdaftar' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($att->distance)
                                    <span class="font-medium">{{ number_format($att->distance, 0) }} m</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($att->status === 'terlambat')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                        Terlambat
                                    </span>
                                @elseif($att->status === 'hadir')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        Hadir
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($att->late_minutes > 0)
                                    <span class="text-amber-600 font-medium">{{ $att->late_minutes }} menit</span>
                                @else
                                    <span class="text-slate-400">Tepat waktu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                Belum ada riwayat absensi tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="p-4 border-t border-slate-200">
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
                    reject(error);
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
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="w-5 h-5 mr-2 animate-spin" data-feather="loader">Loading...</i> Mendeteksi lokasi...';
        feather.replace();

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
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat check in',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error Lokasi',
                text: 'Tidak dapat mendapatkan lokasi: ' + error.message,
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
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="w-5 h-5 mr-2 animate-spin" data-feather="loader">Loading...</i> Mendeteksi lokasi...';
        feather.replace();

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
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#10b981'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat check out',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error Lokasi',
                text: 'Tidak dapat mendapatkan lokasi: ' + error.message,
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
</content>