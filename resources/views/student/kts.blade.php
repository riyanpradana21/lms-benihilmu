@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Dokumen Identitas Resmi</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kartu Tanda Siswa (Digital KTS)</h1>
            <p class="mt-1 text-xs text-slate-500">Kartu identitas resmi siswa yang dilengkapi QR Code verifikasi publik.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Kartu</span>
            </button>
            <a href="{{ $verificationUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Uji Tautan Verifikasi</span>
            </a>
        </div>
    </div>

    <!-- Student Card Printable Container -->
    <div class="flex justify-center py-6">
        <div id="printable-kts" class="relative w-full max-w-xl rounded-2xl bg-white p-8 shadow-xl border border-slate-200/90 overflow-hidden">
            <!-- Background Decorative Watermark -->
            <div class="pointer-events-none absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-indigo-50/60 blur-2xl"></div>
            
            <!-- Header Institution -->
            <div class="flex items-center justify-between pb-5 border-b-2 border-indigo-600">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-xl font-bold text-white shadow-md">
                        S
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900">{{ $institution?->name ?? 'SMA Nusantara Digital' }}</h2>
                        <p class="text-[11px] text-slate-500 font-medium">{{ $institution?->address ?? 'Jl. Pendidikan Merdeka No. 45, Jakarta' }}</p>
                        <p class="text-[10px] text-indigo-600 font-semibold tracking-wider">KARTU TANDA SISWA (KTS)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        AKTIF
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <!-- Left: Avatar & Info -->
                <div class="md:col-span-8 flex gap-5">
                    <!-- Photo Box -->
                    <div class="flex-shrink-0 flex flex-col items-center">
                        <div class="w-24 h-32 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-400 font-semibold shadow-inner">
                            <span class="text-3xl text-indigo-400 font-bold">{{ substr($student->name, 0, 1) }}</span>
                        </div>
                        <span class="mt-2 text-[10px] text-slate-400 uppercase font-mono">FOTO 3x4</span>
                    </div>

                    <!-- Details Table -->
                    <div class="flex-1 space-y-2 text-xs">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Nomor Induk Siswa (NIS)</div>
                            <div class="font-mono font-bold text-slate-900 text-sm tracking-wide">{{ $student->student_number }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Nama Lengkap</div>
                            <div class="font-bold text-slate-800 text-sm">{{ $student->name }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Kelas & Tahun Ajaran</div>
                            <div class="font-semibold text-slate-700">
                                {{ $schoolClass?->name ?? 'X-MIPA-1' }} &bull; {{ $schoolClass?->academicYear?->name ?? '2024/2025' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Tempat, Tanggal Lahir</div>
                            <div class="font-medium text-slate-600">
                                {{ $student->place_of_birth ?? 'Jakarta' }}, {{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Jenis Kelamin</div>
                            <div class="font-medium text-slate-600">
                                {{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: QR Code Verifikasi -->
                <div class="md:col-span-4 flex flex-col items-center justify-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                    <div class="bg-white p-2 rounded-lg shadow-sm border border-slate-200">
                        {!! $qrCodeSvg !!}
                    </div>
                    <div class="mt-2 text-[10px] font-bold text-slate-700 uppercase tracking-wider">Pindai Verifikasi</div>
                    <div class="text-[9px] text-slate-400 font-mono break-all mt-0.5">NIS: {{ $student->student_number }}</div>
                </div>
            </div>

            <!-- Footer Card -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                <span>Diterbitkan resmi oleh Sistem Informasi Akademik SIAKAD</span>
                <span>Berlaku selama menjadi siswa aktif</span>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printable-kts, #printable-kts * {
        visibility: visible;
    }
    #printable-kts {
        position: absolute;
        left: 50%;
        top: 20%;
        transform: translateX(-50%);
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
    }
}
</style>
@endsection
