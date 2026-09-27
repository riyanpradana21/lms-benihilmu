<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Kartu Tanda Siswa (KTS)</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-slate-200 p-6 space-y-6 text-center">
        <div class="inline-flex w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-xl items-center justify-center shadow-md">
            S
        </div>

        <div>
            <h1 class="text-lg font-bold text-slate-900">Verifikasi Resmi Siswa</h1>
            <p class="text-xs text-slate-500">{{ $institution?->name ?? 'SMA Nusantara Digital' }}</p>
        </div>

        @if($student && $student->is_active)
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl space-y-3">
                <div class="inline-flex items-center space-x-1.5 text-xs font-bold text-emerald-700 uppercase tracking-wide">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Status: Terverifikasi Aktif</span>
                </div>

                <div class="text-left space-y-1.5 text-xs text-slate-600 pt-2 border-t border-emerald-100">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Nomor Induk Siswa:</span>
                        <span class="font-semibold text-slate-800">{{ $student->student_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Nama Lengkap:</span>
                        <span class="font-semibold text-slate-800">{{ $student->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kelas:</span>
                        <span class="font-semibold text-slate-800">{{ $student->schoolClasses->first()?->name ?? 'Belum ditentukan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tahun Ajaran:</span>
                        <span class="font-semibold text-slate-800">{{ $student->schoolClasses->first()?->academicYear?->name ?? '2024/2025' }}</span>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed">
                Halaman verifikasi resmi publik ini hanya menampilkan informasi non-rahasia untuk keperluan validasi identitas siswa.
            </p>
        @else
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl space-y-2">
                <span class="text-xs font-bold text-rose-700">Data Siswa Tidak Ditemukan / Tidak Aktif</span>
                <p class="text-xs text-rose-600">Kode atau nomor siswa yang dipindai tidak valid.</p>
            </div>
        @endif

        <div class="pt-2">
            <a href="{{ route('login') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                &larr; Masuk ke Portal SIAKAD
            </a>
        </div>
    </div>
</body>
</html>
