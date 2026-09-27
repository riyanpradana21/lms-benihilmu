<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAKAD + LMS - SMA Nusantara Digital</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-600 selection:text-white">

    <!-- ========================================================= -->
    <!-- NAVBAR -->
    <!-- ========================================================= -->
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6 lg:px-8">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-base font-bold text-white shadow-md shadow-indigo-200 transition group-hover:scale-105">
                    S
                </div>
                <div class="leading-tight">
                    <div class="text-base font-bold text-slate-900">SIAKAD + LMS</div>
                    <div class="text-xs text-slate-500 font-medium">SMA Nusantara Digital</div>
                </div>
            </a>

            <!-- Menu Desktop -->
            <nav class="hidden items-center gap-6 lg:gap-8 md:flex">
                <a href="#tentang" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Tentang</a>
                <a href="#fitur" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Fitur Utama</a>
                <a href="#manfaat" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Keunggulan</a>
                <a href="#demo" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Akun Demo</a>
                <a href="#verifikasi-kts" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Verifikasi KTS</a>
            </nav>

            <!-- Action Button -->
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700">
                        Dashboard
                    </a>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-100 transition hover:bg-indigo-700">
                            Masuk ke Sistem
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================================= -->
    <!-- MAIN CONTENT -->
    <!-- ========================================================= -->
    <main>
        <!-- HERO SECTION -->
        <section class="relative overflow-hidden bg-white py-16 lg:py-20 border-b border-slate-100">
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute left-1/2 top-0 h-[450px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-tr from-indigo-100/50 to-violet-100/30 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">
                    <!-- LEFT COLUMN (7 Cols) -->
                    <div class="lg:col-span-7">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                            <span class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            SIAKAD & LMS E-Learning Terintegrasi
                        </div>
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl leading-[1.15]">
                            Satu Siswa. Satu Profil. <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">Satu Platform Belajar.</span>
                        </h1>
                        <p class="mt-5 text-base sm:text-lg leading-relaxed text-slate-600 max-w-2xl">
                            Ekosistem terpadu SMA Nusantara Digital yang menghubungkan struktur akademik SIAKAD dengan LMS E-Learning secara otomatis: dari kurikulum, jadwal pelajaran anti-bentrok, materi bab berjenjang, ujian CBT server-authoritative, hingga portal orang tua.
                        </p>

                        <!-- CTA Actions -->
                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-indigo-600 px-7 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                                    Buka Dashboard Saya
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-indigo-600 px-7 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                                    Masuk ke Sistem
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endauth
                            <a href="#demo" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-300 bg-white px-6 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                Coba Akun Demo
                            </a>
                            <a href="#verifikasi-kts" class="inline-flex h-12 items-center justify-center rounded-xl border border-indigo-100 bg-indigo-50/60 px-5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100/60">
                                Verifikasi KTS
                            </a>
                        </div>

                        <!-- Checkmarks badge -->
                        <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs font-medium text-slate-500">
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Single Source of Truth</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Server-Authoritative CBT</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Digital KTS QR Verified</span>
                        </div>
                    </div>

                    <!-- RIGHT VISUAL COLUMN (5 Cols) -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 border border-slate-200/90 shadow-xl shadow-slate-200/50">
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white shadow-sm">
                                        S
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">SMA Nusantara Digital</div>
                                        <div class="text-xs text-indigo-600 font-medium">Sistem Terintegrasi v3.0</div>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60">SIAKAD Aktif</span>
                            </div>
                            
                            <div class="mt-4 space-y-3">
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-slate-800">Tahun Ajaran 2024/2025</div>
                                            <div class="text-[11px] text-slate-500">Semester Ganjil &bull; Kurikulum Merdeka</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Aktif</span>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100 text-violet-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-slate-800">CBT & Ujian Digital</div>
                                            <div class="text-[11px] text-slate-500">Timer Server &bull; Anti-Cheat Log</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-violet-700 bg-violet-50 px-2 py-0.5 rounded">Ready</span>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-slate-800">Kartu Siswa (KTS) Digital</div>
                                            <div class="text-[11px] text-slate-500">QR Code Verifikasi Publik</div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Valid</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                                <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                                    Pilih Peran & Masuk &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VALUE STRIP -->
        <section class="border-y border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid divide-y divide-slate-100 py-6 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                    <div class="px-6 py-4 text-center sm:first:pl-0">
                        <div class="text-base font-bold text-slate-900">Akademik Terintegrasi</div>
                        <div class="mt-1 text-xs text-slate-500">Seluruh data administrasi dalam satu sistem</div>
                    </div>
                    <div class="px-6 py-4 text-center">
                        <div class="text-base font-bold text-slate-900">Pembelajaran Digital</div>
                        <div class="mt-1 text-xs text-slate-500">Materi, tugas, dan ujian lebih mudah diakses</div>
                    </div>
                    <div class="px-6 py-4 text-center sm:last:pr-0">
                        <div class="text-base font-bold text-slate-900">Multi-Role User</div>
                        <div class="mt-1 text-xs text-slate-500">Akses khusus admin, guru, siswa & orang tua</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT SECTION -->
        <section id="tentang" class="py-24 bg-slate-50/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:gap-16 items-center">
                    <div>
                        <div class="inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">Tentang Platform</div>
                        <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                            Teknologi modern yang membantu sekolah berkembang lebih cepat.
                        </h2>
                    </div>
                    <div class="space-y-4 text-slate-600 text-base leading-relaxed">
                        <p>
                            SIAKAD + LMS dirancang khusus untuk memenuhi standar operasional pendidikan modern di SMA Nusantara Digital. Sistem menggabungkan pencatatan data akademik dengan pusat pembelajaran mandiri siswa.
                        </p>
                        <p>
                            Penyederhanaan alur kerja ini memastikan guru dapat fokus mendidik, siswa lebih aktif belajar, dan orang tua mendapatkan transparansi penuh atas perkembangan anak mereka.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section id="fitur" class="py-24 bg-white">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-2xl mb-16">
                    <div class="inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">Fitur Utama</div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        Satu platform, solusi untuk berbagai kebutuhan.
                    </h2>
                    <p class="mt-4 text-base text-slate-600">
                        Semua fungsi krusial sekolah tersedia dalam ekosistem terpadu yang saling terhubung secara otomatis.
                    </p>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Feature 1 -->
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 transition hover:bg-white hover:shadow-xl hover:shadow-slate-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 mb-6">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Akademik & SIAKAD</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Kelola master data siswa, guru, kelas, kurikulum, dan penjadwalan secara terpusat.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 transition hover:bg-white hover:shadow-xl hover:shadow-slate-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 mb-6">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v18H6.5A2.5 2.5 0 0 1 4 18.5v-13Z"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">E-Learning & LMS</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Penyediaan materi mendalam berstruktur bab, kuis interaktif, dan tugas online.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 transition hover:bg-white hover:shadow-xl hover:shadow-slate-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 mb-6">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Multi-Role Portal</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Antarmuka khusus dan aman yang disesuaikan untuk admin, guru, siswa, dan orang tua.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-8 transition hover:bg-white hover:shadow-xl hover:shadow-slate-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 mb-6">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18"/><path d="M17 7.5A4.5 4.5 0 0 0 12.5 3C10 3 8 4.5 8 6.5S9.5 10 12.5 11c3 .75 4.5 2 4.5 4.5S14.5 21 12 21a4.5 4.5 0 0 1-4.5-4.5"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Nilai & e-Raport</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">Rekapitulasi nilai transparan serta penerbitan laporan hasil belajar secara digital.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- BENEFITS SECTION -->
        <section id="manfaat" class="py-24 bg-slate-50/50 border-t border-slate-200">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid items-center gap-16 lg:grid-cols-2 lg:gap-20">
                    <div>
                        <div class="inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-3">Keunggulan Sistem</div>
                        <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                            Lebih sedikit aplikasi, lebih banyak fokus belajar.
                        </h2>
                        <p class="mt-4 text-base text-slate-600">
                            Informasi sekolah yang terhubung secara real-time membantu efisiensi operasional harian.
                        </p>

                        <div class="mt-8 space-y-6">
                            <div class="flex gap-4">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-md shadow-indigo-200">01</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Data Terpusat & Akurat</h3>
                                    <p class="mt-1 text-sm text-slate-500">Tidak ada duplikasi data; informasi nilai dan absensi sinkron secara otomatis.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-md shadow-indigo-200">02</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Akses Fleksibel Per Peran</h3>
                                    <p class="mt-1 text-sm text-slate-500">Setiap pengguna langsung diarahkan ke dasbor dan fitur yang relevan dengan tugasnya.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-md shadow-indigo-200">03</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Pengalaman Pengguna Sederhana</h3>
                                    <p class="mt-1 text-sm text-slate-500">Antarmuka bersih dirancang dengan teknologi responsif agar nyaman dibuka lewat laptop maupun ponsel.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Banner Right -->
                    <div>
                        <div class="relative rounded-3xl bg-indigo-600 p-8 sm:p-12 shadow-2xl shadow-indigo-200 text-white overflow-hidden">
                            <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-indigo-500/50 blur-2xl"></div>
                            <div class="relative z-10">
                                <div class="text-xs font-bold uppercase tracking-widest text-indigo-200">SIAKAD + LMS Antigravity</div>
                                <h3 class="mt-3 text-2xl sm:text-3xl font-extrabold leading-tight">Satu ekosistem lengkap untuk seluruh aktivitas sekolah.</h3>
                                <div class="mt-8 space-y-3">
                                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                        <span class="text-sm font-medium">Administrasi Akademik & SIAKAD</span>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                        <span class="text-sm font-medium">Modul Pembelajaran & Materi LMS</span>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                        <span class="text-sm font-medium">Evaluasi Ujian & Rekap Nilai</span>
                                    </div>
                                </div>
        <!-- DEMO ACCOUNTS SECTION -->
        <section id="demo" class="py-20 bg-white border-t border-slate-200">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-14">
                    <div class="inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">Evaluasi Cepat</div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        Akun Uji Coba Multi-Role Siap Pakai
                    </h2>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base">
                        Gunakan akun demo yang telah di-seeding untuk mencoba fitur spesifik dari tiap hak akses. Kata sandi default semua akun: <code class="font-mono bg-slate-100 text-indigo-700 px-2 py-0.5 rounded font-semibold text-sm">password</code>
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Card 1: Admin TU -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6 flex flex-col justify-between hover:border-indigo-300 hover:bg-white transition shadow-sm hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md">Tata Usaha</span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Operator / Admin</h3>
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Kelola data master siswa, guru, kelas rombel, mapel, jadwal anti-bentrok, dan rekap absensi/nilai sekolah.
                            </p>
                            <div class="mt-4 p-3 bg-white rounded-xl border border-slate-100 text-xs font-mono space-y-1">
                                <div class="text-slate-500 text-[11px]">Email:</div>
                                <div class="font-semibold text-slate-800 break-all">admin@school.test</div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="mt-5 inline-flex items-center justify-center w-full py-2 px-3 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            Login Admin &rarr;
                        </a>
                    </div>

                    <!-- Card 2: Teacher -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6 flex flex-col justify-between hover:border-violet-300 hover:bg-white transition shadow-sm hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-violet-600 bg-violet-50 px-2.5 py-1 rounded-md">Tenaga Pendidik</span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Guru (Budi Santoso)</h3>
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Kelola materi modul bab/topik, kuis CBT server-authoritative, koreksi & nilai tugas siswa, serta presensi kelas.
                            </p>
                            <div class="mt-4 p-3 bg-white rounded-xl border border-slate-100 text-xs font-mono space-y-1">
                                <div class="text-slate-500 text-[11px]">Email:</div>
                                <div class="font-semibold text-slate-800 break-all">teacher@school.test</div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="mt-5 inline-flex items-center justify-center w-full py-2 px-3 text-xs font-semibold rounded-lg bg-violet-600 text-white hover:bg-violet-700 transition">
                            Login Guru &rarr;
                        </a>
                    </div>

                    <!-- Card 3: Student -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6 flex flex-col justify-between hover:border-emerald-300 hover:bg-white transition shadow-sm hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md">Peserta Didik</span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Siswa (Ahmad Fauzan)</h3>
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Baca materi pelajaran, tandai progres, kumpulkan tugas online, ikuti ujian CBT, dan akses Kartu Pelajar (KTS).
                            </p>
                            <div class="mt-4 p-3 bg-white rounded-xl border border-slate-100 text-xs font-mono space-y-1">
                                <div class="text-slate-500 text-[11px]">Email:</div>
                                <div class="font-semibold text-slate-800 break-all">student@school.test</div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="mt-5 inline-flex items-center justify-center w-full py-2 px-3 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">
                            Login Siswa &rarr;
                        </a>
                    </div>

                    <!-- Card 4: Parent -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6 flex flex-col justify-between hover:border-amber-300 hover:bg-white transition shadow-sm hover:shadow-md">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">Wali Murid</span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">Orang Tua (Hendra F.)</h3>
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                                Pantau riwayat kehadiran, nilai rapor, tugas, dan progres belajar anak secara aman dan transparan per anak.
                            </p>
                            <div class="mt-4 p-3 bg-white rounded-xl border border-slate-100 text-xs font-mono space-y-1">
                                <div class="text-slate-500 text-[11px]">Email:</div>
                                <div class="font-semibold text-slate-800 break-all">parent@school.test</div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="mt-5 inline-flex items-center justify-center w-full py-2 px-3 text-xs font-semibold rounded-lg bg-amber-600 text-white hover:bg-amber-700 transition">
                            Login Orang Tua &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- KTS VERIFICATION QUICK LOOKUP SECTION -->
        <section id="verifikasi-kts" class="py-20 bg-slate-50/60 border-t border-slate-200">
            <div class="mx-auto max-w-4xl px-6 lg:px-8 text-center">
                <div class="inline-block text-xs font-bold uppercase tracking-widest text-indigo-600 mb-2">Validasi Identitas Resmi</div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Verifikasi Kartu Tanda Siswa (Digital KTS)
                </h2>
                <p class="mt-3 text-slate-600 text-sm sm:text-base max-w-2xl mx-auto">
                    Setiap Kartu Tanda Siswa dilengkapi QR Code yang mengarah ke tautan verifikasi publik untuk memastikan keabsahan data siswa tanpa membocorkan data rahasia.
                </p>

                <div class="mt-8 max-w-xl mx-auto bg-white rounded-2xl p-6 shadow-md border border-slate-200 text-left">
                    <form onsubmit="event.preventDefault(); var nis = document.getElementById('kts_token').value.trim(); if(nis) window.location.href = '{{ url('/verify/student') }}/' + encodeURIComponent(nis);" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label for="kts_token" class="sr-only">Nomor Induk Siswa (NIS)</label>
                            <input type="text" id="kts_token" placeholder="Masukkan NIS (contoh: 202401001)" value="202401001" required
                                   class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        </div>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cek Validitas</span>
                        </button>
                    </form>
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        <span class="font-medium">Sampel NIS Terdaftar:</span>
                        <button type="button" onclick="document.getElementById('kts_token').value='202401001'" class="underline hover:text-indigo-600">202401001 (Ahmad Fauzan)</button> &bull;
                        <button type="button" onclick="document.getElementById('kts_token').value='202401002'" class="underline hover:text-indigo-600">202401002 (Bella Safira)</button> &bull;
                        <button type="button" onclick="document.getElementById('kts_token').value='202401003'" class="underline hover:text-indigo-600">202401003 (Dimas Pratama)</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="bg-slate-950 py-20 text-center text-white">
            <div class="mx-auto max-w-4xl px-6 lg:px-8">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Mulai gunakan SIAKAD + LMS sekarang</h2>
                <p class="mx-auto mt-4 max-w-xl text-slate-400 text-sm sm:text-base">
                    Tingkatkan efisiensi dan kualitas pembelajaran digital di sekolah Anda melalui satu platform terpadu.
                </p>
                <div class="mt-8">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-white px-8 text-sm font-semibold text-slate-950 transition hover:bg-slate-100 shadow-lg">
                            Buka Dashboard
                        </a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-white px-8 text-sm font-semibold text-slate-950 transition hover:bg-slate-100 shadow-lg">
                                Masuk ke Sistem
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <!-- ========================================================= -->
    <!-- FOOTER -->
    <!-- ========================================================= -->
    <footer class="bg-slate-950 border-t border-slate-800/80">
        <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-bold text-white">SIAKAD + LMS</div>
                    <div class="mt-1 text-xs text-slate-400">SMA Nusantara Digital</div>
                </div>
                <div class="text-xs text-slate-400">
                    © {{ date('Y') }} SMA Nusantara Digital. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>