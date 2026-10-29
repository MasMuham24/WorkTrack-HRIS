@extends('layouts.app')

@section('title', 'Tambah Kantor')
@section('header_title', 'Manajemen Kantor')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #office-map {
            height: 350px;
            width: 100%;
            border-radius: 0.75rem;
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800">Tambah Kantor Baru</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tentukan titik koordinat kantor dan radius presensi
                    (geofencing).</p>
            </div>
            <div>
                <a href="{{ route('management.offices.index') }}"
                    class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                    Kembali
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <form action="{{ route('management.offices.store') }}" method="POST">
                @csrf

                <div class="p-5 sm:p-8 space-y-5">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Kantor <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: Kantor Pusat Jakarta, Cabang Bandung">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Leaflet Map --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Pilih Lokasi Kantor di Peta
                            </label>
                            <button type="button" id="gps-locate-btn"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-lg border border-indigo-200 transition-colors">
                                <i data-feather="navigation" class="w-3.5 h-3.5"></i>
                                Gunakan Lokasi Saya
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mb-2">
                            <i data-feather="info" class="w-3 h-3 inline-block mr-0.5"></i>
                            Klik pada peta, geser marker, atau gunakan GPS perangkat untuk menentukan koordinat kantor.
                        </p>
                        <div id="gps-locate-status" class="hidden text-xs rounded-lg px-3 py-2 font-medium mb-2"></div>
                        <div id="office-map" class="border border-slate-300"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="latitude"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Latitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" required
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm font-mono text-slate-800 placeholder-slate-400 bg-white"
                                placeholder="Contoh: -6.1947000">
                            @error('latitude')
                                <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="longitude"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Longitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" required
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm font-mono text-slate-800 placeholder-slate-400 bg-white"
                                placeholder="Contoh: 106.8241200">
                            @error('longitude')
                                <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="radius" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Radius Toleransi (Meter) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="radius" id="radius" value="{{ old('radius', 100) }}" required min="10"
                            max="10000"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white"
                            placeholder="Contoh: 100">
                        <p class="text-[11px] text-slate-400 mt-1">Jarak maksimum toleransi posisi karyawan dari titik
                            kantor agar presensi diterima.</p>
                        @error('radius')
                            <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label for="check_in_time"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jam Masuk <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="check_in_time" id="check_in_time"
                                value="{{ old('check_in_time', '08:00') }}" required
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                            <p class="text-[11px] text-slate-400 mt-1">Karyawan terlambat jika check-in melewati jam masuk +
                                5 menit.</p>
                            @error('check_in_time')
                                <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="check_out_time"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jam Pulang <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" name="check_out_time" id="check_out_time"
                                value="{{ old('check_out_time', '16:00') }}" required
                                class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 text-xs sm:text-sm text-slate-800 bg-white">
                            <p class="text-[11px] text-slate-400 mt-1">Harus lebih lambat dari jam masuk.</p>
                            @error('check_out_time')
                                <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div
                    class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <button type="reset"
                        class="w-full sm:w-auto px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Reset
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs sm:text-sm font-bold shadow-md shadow-indigo-600/20 transition">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i>
                        Simpan Kantor
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            'use strict';

            // Default center: Indonesia (Monas, Jakarta) — hanya untuk tampilan awal
            var DEFAULT_LAT = -6.1751;
            var DEFAULT_LNG = 106.8650;
            var DEFAULT_ZOOM = 13;

            var latInput = document.getElementById('latitude');
            var lngInput = document.getElementById('longitude');
            var radiusInput = document.getElementById('radius');

            if (!latInput || !lngInput || !radiusInput) return;

            // Baca nilai old() jika ada (dari validasi server gagal)
            var savedLat = parseFloat(latInput.value);
            var savedLng = parseFloat(lngInput.value);
            var savedRadius = parseInt(radiusInput.value, 10) || 100;

            var hasValidSaved = isValidCoord(savedLat, savedLng);

            // Inisialisasi peta
            var mapCenter = hasValidSaved ? [savedLat, savedLng] : [DEFAULT_LAT, DEFAULT_LNG];
            var map = L.map('office-map').setView(mapCenter, DEFAULT_ZOOM);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Marker & circle — hanya ditampilkan jika ada koordinat valid (old() setelah validasi gagal)
            var marker = null;
            var circle = null;

            if (hasValidSaved) {
                marker = createMarker(savedLat, savedLng);
                circle = L.circle([savedLat, savedLng], { radius: savedRadius, color: '#4f46e5', fillColor: '#4f46e5', fillOpacity: 0.10 }).addTo(map);
            }

            // Klik pada peta → pindahkan/buat marker, isi input
            map.on('click', function (e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                placeMarker(lat, lng);
                updateInputs(lat, lng);
            });

            // Input latitude/longitude diubah manual → pindahkan marker jika valid
            latInput.addEventListener('change', syncMarkerFromInputs);
            lngInput.addEventListener('change', syncMarkerFromInputs);

            // Input radius diubah → perbarui lingkaran
            radiusInput.addEventListener('input', function () {
                var r = parseInt(radiusInput.value, 10);
                if (circle && r >= 10) {
                    circle.setRadius(r);
                }
            });

            // --- Helper functions ---

            function isValidCoord(lat, lng) {
                return (
                    !isNaN(lat) && isFinite(lat) && lat >= -90 && lat <= 90 &&
                    !isNaN(lng) && isFinite(lng) && lng >= -180 && lng <= 180
                );
            }

            function createMarker(lat, lng) {
                var m = L.marker([lat, lng], { draggable: true }).addTo(map);
                m.on('dragend', function (e) {
                    var pos = e.target.getLatLng();
                    updateInputs(pos.lat, pos.lng);
                    updateCircle(pos.lat, pos.lng);
                });
                return m;
            }

            function placeMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = createMarker(lat, lng);
                }
                updateCircle(lat, lng);
            }

            function updateCircle(lat, lng) {
                var r = parseInt(radiusInput.value, 10) || 100;
                if (circle) {
                    circle.setLatLng([lat, lng]);
                    circle.setRadius(r);
                } else {
                    circle = L.circle([lat, lng], { radius: r, color: '#4f46e5', fillColor: '#4f46e5', fillOpacity: 0.10 }).addTo(map);
                }
            }

            function updateInputs(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            function syncMarkerFromInputs() {
                var lat = parseFloat(latInput.value);
                var lng = parseFloat(lngInput.value);
                if (!isValidCoord(lat, lng)) return;
                placeMarker(lat, lng);
                map.setView([lat, lng], map.getZoom());
            }

            // ─── Tombol "Gunakan Lokasi Saya" (admin) ────────────────────────
            var gpsBtn = document.getElementById('gps-locate-btn');
            var gpsStatusEl = document.getElementById('gps-locate-status');

            function setAdminGpsStatus(msg, type) {
                gpsStatusEl.className = 'text-xs rounded-lg px-3 py-2 font-medium mb-2';
                gpsStatusEl.style.display = 'block';
                if (type === 'success') {
                    gpsStatusEl.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
                } else {
                    gpsStatusEl.classList.add('bg-rose-50', 'text-rose-800', 'border', 'border-rose-200');
                }
                gpsStatusEl.textContent = msg;
            }

            if (gpsBtn) {
                gpsBtn.addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        setAdminGpsStatus('Browser tidak mendukung geolocation.', 'error');
                        return;
                    }
                    gpsBtn.disabled = true;
                    gpsBtn.innerHTML = '<span class="inline-block animate-pulse">⏳ Mendeteksi...</span>';

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;
                            var acc = position.coords.accuracy ? Math.round(position.coords.accuracy) : null;

                            if (!isValidCoord(lat, lng)) {
                                setAdminGpsStatus('Koordinat GPS tidak valid. Coba lagi.', 'error');
                                gpsBtn.disabled = false;
                                gpsBtn.innerHTML = '<i data-feather="navigation" class="w-3.5 h-3.5"></i> Gunakan Lokasi Saya';
                                feather.replace();
                                return;
                            }

                            placeMarker(lat, lng);
                            updateInputs(lat, lng);
                            map.setView([lat, lng], 16);

                            var accText = acc ? ' (akurasi ±' + acc + ' m)' : '';
                            setAdminGpsStatus(
                                'Lokasi berhasil dideteksi' + accText + '. Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6) + '. Periksa posisi marker sebelum menyimpan.',
                                'success'
                            );
                            gpsBtn.disabled = false;
                            gpsBtn.innerHTML = '<i data-feather="navigation" class="w-3.5 h-3.5"></i> Gunakan Lokasi Saya';
                            feather.replace();
                        },
                        function (error) {
                            var msg = 'Gagal mendeteksi lokasi GPS.';
                            if (error.code === error.PERMISSION_DENIED) {
                                msg = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser Anda.';
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                msg = 'Informasi lokasi tidak tersedia.';
                            } else if (error.code === error.TIMEOUT) {
                                msg = 'Permintaan lokasi timeout. Coba lagi.';
                            }
                            setAdminGpsStatus(msg, 'error');
                            gpsBtn.disabled = false;
                            gpsBtn.innerHTML = '<i data-feather="navigation" class="w-3.5 h-3.5"></i> Gunakan Lokasi Saya';
                            feather.replace();
                        },
                        { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                    );
                });
            }
        })();
    </script>
@endpush
