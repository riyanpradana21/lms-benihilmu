<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Kartu Tanda Siswa (Digital KTS) - SIAKAD</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex items-center justify-center p-4 sm:p-6 selection:bg-indigo-600 selection:text-white">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-200/90 p-6 sm:p-8 space-y-6">
        <!-- Top Navigation -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Beranda</span>
            </a>
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200/60">
                Layanan Verifikasi Publik
            </span>
        </div>

        <!-- School Header -->
        <div class="text-center space-y-1.5">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-white font-extrabold text-xl shadow-md shadow-indigo-200">
                S
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Verifikasi Resmi Identitas Siswa</h1>
            <p class="text-xs text-slate-500 font-medium">{{ $institution?->name ?? 'SMA Nusantara Digital' }}</p>
        </div>

        @if($student && $student->is_active)
            <div class="p-5 bg-gradient-to-br from-emerald-50 to-teal-50/40 border border-emerald-200/80 rounded-2xl space-y-4 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 uppercase tracking-wide bg-emerald-100/70 px-2.5 py-1 rounded-lg">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Terverifikasi Sah</span>
                    </span>
                    <span class="text-[11px] font-semibold text-slate-400 font-mono">ID: {{ $student->student_number }}</span>
                </div>

                <div class="space-y-2 text-xs divide-y divide-emerald-100/80">
                    <div class="flex justify-between pt-1">
                        <span class="text-slate-500 font-medium">Nomor Induk Siswa (NIS):</span>
                        <span class="font-bold text-slate-900 font-mono text-sm">{{ $student->student_number }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-500 font-medium">Nama Peserta Didik:</span>
                        <span class="font-bold text-slate-900">{{ $student->name }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-500 font-medium">Kelas Rombel:</span>
                        <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $student->schoolClasses->first()?->name ?? 'Belum ditentukan' }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-500 font-medium">Tahun Ajaran:</span>
                        <span class="font-semibold text-slate-800">{{ $student->schoolClasses->first()?->academicYear?->name ?? '2024/2025' }}</span>
                    </div>
                    <div class="flex justify-between pt-2">
                        <span class="text-slate-500 font-medium">Status Keaktifan:</span>
                        <span class="font-bold text-emerald-700">Aktif Terdaftar</span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 text-center leading-relaxed">
                Data divalidasi langsung oleh sistem database SIAKAD SMA Nusantara Digital tanpa perantara.
            </p>
        @else
            <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl space-y-2 text-center shadow-sm">
                <div class="mx-auto w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="text-sm font-bold text-rose-800">Data Siswa Tidak Ditemukan / Non-Aktif</div>
                <p class="text-xs text-rose-600">Nomor Induk Siswa <strong class="font-mono">"{{ $token }}"</strong> tidak cocok dengan arsip data siswa aktif sekolah.</p>
            </div>
        @endif

        <!-- Quick Lookup Another NIS Form -->
        <div class="pt-4 border-t border-slate-100">
            <form onsubmit="event.preventDefault(); var val = document.getElementById('search_token').value.trim(); if(val) window.location.href='{{ url('/verify/student') }}/' + encodeURIComponent(val);" class="space-y-2">
                <label for="search_token" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cek NIS Lainnya</label>
                <div class="flex gap-2">
                    <input type="text" id="search_token" placeholder="Masukkan NIS siswa..." required
                           class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-mono">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                        Periksa
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-2 text-xs">
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition">
                Masuk ke Portal &rarr;
            </a>
            <button type="button" onclick="window.print()" class="text-slate-400 hover:text-slate-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Bukti</span>
            </button>
        </div>
    </div>
</body>
</html>
