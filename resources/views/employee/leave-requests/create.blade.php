@extends('layouts.app')

@section('title', 'Buat Pengajuan Cuti / Izin')
@section('header_title', 'Buat Pengajuan Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800">Form Pengajuan Cuti & Izin</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Silakan isi formulir di bawah ini dengan lengkap dan benar.</p>
        </div>
        <div>
            <a href="{{ route('employee.leave-requests.index') }}" class="inline-flex items-center text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-300 rounded-xl px-3.5 py-2 hover:bg-slate-50 transition-colors shadow-xs">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden" x-data="leaveForm()">
        <form action="{{ route('employee.leave-requests.store') }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf

            <!-- Leave Type -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Jenis Pengajuan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="relative flex flex-col items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-center"
                        :class="leave_type === 'cuti' ? 'border-indigo-600 bg-indigo-50/70 text-indigo-700 ring-2 ring-indigo-500/20' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="leave_type" value="cuti" class="sr-only" x-model="leave_type" required>
                        <i data-feather="calendar" class="w-5 h-5 mb-1.5"></i>
                        <span class="text-xs font-bold">Cuti Tahunan</span>
                    </label>

                    <label class="relative flex flex-col items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-center"
                        :class="leave_type === 'sakit' ? 'border-rose-600 bg-rose-50/70 text-rose-700 ring-2 ring-rose-500/20' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="leave_type" value="sakit" class="sr-only" x-model="leave_type">
                        <i data-feather="activity" class="w-5 h-5 mb-1.5"></i>
                        <span class="text-xs font-bold">Izin Sakit</span>
                    </label>

                    <label class="relative flex flex-col items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-center"
                        :class="leave_type === 'penting' ? 'border-amber-600 bg-amber-50/70 text-amber-700 ring-2 ring-amber-500/20' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="leave_type" value="penting" class="sr-only" x-model="leave_type">
                        <i data-feather="alert-circle" class="w-5 h-5 mb-1.5"></i>
                        <span class="text-xs font-bold">Izin Penting</span>
                    </label>

                    <label class="relative flex flex-col items-center justify-center p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-center"
                        :class="leave_type === 'lainnya' ? 'border-slate-600 bg-slate-100 text-slate-800 ring-2 ring-slate-400/20' : 'border-slate-200 text-slate-600'">
                        <input type="radio" name="leave_type" value="lainnya" class="sr-only" x-model="leave_type">
                        <i data-feather="file-text" class="w-5 h-5 mb-1.5"></i>
                        <span class="text-xs font-bold">Lainnya</span>
                    </label>
                </div>
                @error('leave_type')
                    <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Range Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Start Date -->
                <div class="space-y-1.5">
                    <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" id="start_date" name="start_date" x-model="start_date" @change="calculateDuration"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-sm text-slate-800 bg-white" required>
                    </div>
                    @error('start_date')
                        <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="space-y-1.5">
                    <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Tanggal Selesai <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" id="end_date" name="end_date" x-model="end_date" @change="calculateDuration" :min="start_date"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-sm text-slate-800 bg-white" required>
                    </div>
                    @error('end_date')
                        <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Duration Indicator -->
            <div x-show="duration > 0" x-cloak class="p-3.5 bg-indigo-50/80 border border-indigo-200/80 rounded-xl text-xs text-indigo-800 flex items-center">
                <i data-feather="info" class="w-4 h-4 mr-2 text-indigo-600 shrink-0"></i>
                <span>Pengajuan ini terhitung selama <strong x-text="duration" class="font-bold text-indigo-900"></strong> hari kerja.</span>
            </div>

            <!-- Reason / Description -->
            <div class="space-y-1.5">
                <label for="reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Alasan / Keterangan <span class="text-rose-500">*</span>
                </label>
                <textarea id="reason" name="reason" rows="4" placeholder="Tuliskan alasan pengajuan secara jelas..."
                    class="block w-full p-3.5 rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-sm text-slate-800 bg-white placeholder-slate-400" required></textarea>
                @error('reason')
                    <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('employee.leave-requests.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                    Batalkan
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    <i data-feather="send" class="w-4 h-4 mr-2"></i>
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function leaveForm() {
        return {
            leave_type: 'cuti',
            start_date: '',
            end_date: '',
            duration: 0,
            calculateDuration() {
                if (this.start_date && this.end_date) {
                    const start = new Date(this.start_date);
                    const end = new Date(this.end_date);
                    if (end >= start) {
                        const timeDiff = end.getTime() - start.getTime();
                        this.duration = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;
                    } else {
                        this.duration = 0;
                    }
                } else {
                    this.duration = 0;
                }
            }
        }
    }
</script>
@endpush
@endsection
