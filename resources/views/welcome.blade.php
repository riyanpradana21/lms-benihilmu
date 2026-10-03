<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SIAKAD + LMS — SMA Nusantara Digital</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased">

    <!-- ========================================================= -->
    <!-- NAVBAR -->
    <!-- ========================================================= -->

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-xl">

        <div class="mx-auto flex h-[72px] w-full max-w-7xl items-center justify-between px-5 sm:px-6 lg:px-8">

            <!-- Brand -->
            <a
                href="{{ url('/') }}"
                class="flex shrink-0 items-center gap-3"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white">
                    S
                </div>

                <div class="leading-tight">
                    <div class="text-sm font-bold tracking-tight text-slate-950">
                        SIAKAD + LMS
                    </div>

                    <div class="mt-0.5 text-[11px] font-medium text-slate-500">
                        SMA Nusantara Digital
                    </div>
                </div>
            </a>


            <!-- Navigation -->
            <nav class="hidden items-center lg:flex">

                <a
                    href="#tentang"
                    class="ml-8 text-sm font-medium text-slate-500 transition hover:text-slate-950"
                >
                    Tentang
                </a>

                <a
                    href="#fitur"
                    class="ml-8 text-sm font-medium text-slate-500 transition hover:text-slate-950"
                >
                    Fitur
                </a>

                <a
                    href="#keunggulan"
                    class="ml-8 text-sm font-medium text-slate-500 transition hover:text-slate-950"
                >
                    Keunggulan
                </a>

                <a
                    href="#demo"
                    class="ml-8 text-sm font-medium text-slate-500 transition hover:text-slate-950"
                >
                    Demo
                </a>

                <a
                    href="#verifikasi"
                    class="ml-8 text-sm font-medium text-slate-500 transition hover:text-slate-950"
                >
                    Verifikasi
                </a>

            </nav>


            <!-- Login -->
            <div class="shrink-0">

                @auth

                    <a
                        href="{{ url('/dashboard') }}"
                        class="inline-flex h-10 items-center rounded-xl bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Dashboard
                    </a>

                @else

                    @if (Route::has('login'))

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex h-10 items-center rounded-xl bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-slate-800"
                        >
                            Masuk
                        </a>

                    @endif

                @endauth

            </div>

        </div>
    </header>


    <main>

        <!-- ===================================================== -->
        <!-- HERO -->
        <!-- ===================================================== -->

        <section class="relative overflow-hidden border-b border-slate-200 bg-white">

            <!-- Background -->
            <div class="pointer-events-none absolute inset-0">

                <div class="absolute left-1/2 top-[-280px] h-[650px] w-[900px] -translate-x-1/2 rounded-full bg-indigo-50 blur-3xl"></div>

                <div class="absolute right-[-200px] top-[300px] h-[500px] w-[500px] rounded-full bg-violet-50 blur-3xl"></div>

            </div>


            <div class="relative mx-auto w-full max-w-7xl px-5 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">

                <div class="flex flex-col gap-14 lg:flex-row lg:items-center lg:gap-16">

                    <!-- Hero Content -->
                    <div class="w-full lg:w-1/2">

                        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1.5">

                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span>

                            <span class="text-xs font-semibold text-indigo-700">
                                Platform Pendidikan Terintegrasi
                            </span>

                        </div>


                        <h1 class="mt-7 max-w-xl text-4xl font-bold leading-[1.08] tracking-[-0.04em] text-slate-950 sm:text-5xl lg:text-[58px]">

                            Semua aktivitas sekolah.

                            <span class="block text-indigo-600">
                                Satu platform.
                            </span>

                        </h1>


                        <p class="mt-6 max-w-lg text-base leading-7 text-slate-600">
                            SIAKAD + LMS menghubungkan administrasi akademik,
                            pembelajaran digital, ujian, nilai, dan monitoring
                            siswa dalam satu ekosistem yang sederhana.
                        </p>


                        <!-- CTA -->
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                            @auth

                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="inline-flex h-12 items-center justify-center rounded-xl bg-slate-950 px-6 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:-translate-y-0.5 hover:bg-slate-800"
                                >
                                    Buka Dashboard

                                    <svg
                                        class="ml-2 h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"
                                        />
                                    </svg>
                                </a>

                            @else

                                @if (Route::has('login'))

                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex h-12 items-center justify-center rounded-xl bg-slate-950 px-6 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:-translate-y-0.5 hover:bg-slate-800"
                                    >
                                        Masuk ke Sistem

                                        <svg
                                            class="ml-2 h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                                            />
                                        </svg>
                                    </a>

                                @endif

                            @endauth


                            <a
                                href="#demo"
                                class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                            >
                                Lihat Demo
                            </a>

                        </div>


                        <!-- Trust -->
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3">

                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50">
                                    <svg
                                        class="h-3 w-3 text-emerald-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>

                                Data terpusat
                            </div>


                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50">
                                    <svg
                                        class="h-3 w-3 text-emerald-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>

                                Multi-role
                            </div>


                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50">
                                    <svg
                                        class="h-3 w-3 text-emerald-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>

                                Digital learning
                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- DASHBOARD PREVIEW -->
                    <!-- ================================================= -->

                    <div class="w-full lg:w-1/2">

                        <div class="relative mx-auto w-full max-w-[620px]">

                            <!-- Glow -->
                            <div class="absolute -inset-5 rounded-[32px] bg-indigo-100/70 blur-3xl"></div>


                            <!-- Browser -->
                            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">

                                <!-- Browser Bar -->
                                <div class="flex h-12 items-center justify-between border-b border-slate-200 bg-slate-50 px-4">

                                    <div class="flex items-center gap-1.5">

                                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>

                                    </div>


                                    <div class="hidden rounded-lg border border-slate-200 bg-white px-5 py-1.5 text-[10px] font-medium text-slate-400 sm:block">
                                        app.smanusantaradigital.sch.id
                                    </div>


                                    <div class="w-12"></div>

                                </div>


                                <!-- Application -->
                                <div class="flex min-h-[430px] bg-slate-50">

                                    <!-- Sidebar -->
                                    <aside class="hidden w-[76px] shrink-0 border-r border-slate-200 bg-white p-3 sm:block">

                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-950 text-xs font-bold text-white">
                                            S
                                        </div>


                                        <div class="mt-8 space-y-2">

                                            <div class="flex h-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M3 11.5L12 4l9 7.5M5.5 10v9.5h13V10"
                                                    />
                                                </svg>

                                            </div>


                                            <div class="flex h-9 items-center justify-center rounded-xl text-slate-300">

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        d="M5 6h14M5 12h14M5 18h14"
                                                    />
                                                </svg>

                                            </div>


                                            <div class="flex h-9 items-center justify-center rounded-xl text-slate-300">

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        d="M12 5v14M5 12h14"
                                                    />
                                                </svg>

                                            </div>


                                            <div class="flex h-9 items-center justify-center rounded-xl text-slate-300">

                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M4 18V6m0 12h16M8 15l3-4 3 2 4-6"
                                                    />
                                                </svg>

                                            </div>

                                        </div>

                                    </aside>


                                    <!-- Dashboard -->
                                    <div class="min-w-0 flex-1 p-5 sm:p-6">

                                        <!-- Header -->
                                        <div class="flex items-start justify-between gap-4">

                                            <div class="min-w-0">

                                                <div class="text-[9px] font-semibold uppercase tracking-wider text-slate-400">
                                                    Senin, 03 Oktober 2026
                                                </div>

                                                <h3 class="mt-1 text-sm font-bold text-slate-950 sm:text-base">
                                                    Selamat datang kembali
                                                </h3>

                                            </div>


                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-bold text-indigo-700">
                                                AF
                                            </div>

                                        </div>


                                        <!-- Stats -->
                                        <div class="mt-6 grid grid-cols-3 gap-3">

                                            <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-3">

                                                <div class="truncate text-[9px] font-medium text-slate-400">
                                                    Mata Pelajaran
                                                </div>

                                                <div class="mt-2 text-xl font-bold text-slate-950">
                                                    12
                                                </div>

                                                <div class="mt-1 text-[9px] font-medium text-emerald-600">
                                                    +2 semester ini
                                                </div>

                                            </div>


                                            <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-3">

                                                <div class="truncate text-[9px] font-medium text-slate-400">
                                                    Tugas Aktif
                                                </div>

                                                <div class="mt-2 text-xl font-bold text-slate-950">
                                                    04
                                                </div>

                                                <div class="mt-1 text-[9px] font-medium text-amber-600">
                                                    2 perlu dikerjakan
                                                </div>

                                            </div>


                                            <div class="min-w-0 rounded-xl border border-slate-200 bg-white p-3">

                                                <div class="truncate text-[9px] font-medium text-slate-400">
                                                    Rata-rata Nilai
                                                </div>

                                                <div class="mt-2 text-xl font-bold text-slate-950">
                                                    87.4
                                                </div>

                                                <div class="mt-1 text-[9px] font-medium text-emerald-600">
                                                    +4.2% bulan ini
                                                </div>

                                            </div>

                                        </div>


                                        <!-- Dashboard Cards -->
                                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">

                                            <!-- Chart -->
                                            <div class="w-full rounded-xl border border-slate-200 bg-white p-4 sm:w-1/2">

                                                <div class="flex items-center justify-between">

                                                    <div class="text-[10px] font-bold text-slate-950">
                                                        Aktivitas Belajar
                                                    </div>

                                                    <span class="text-[8px] font-semibold text-indigo-600">
                                                        Minggu ini
                                                    </span>

                                                </div>


                                                <div class="mt-6 flex h-28 items-end gap-2">

                                                    <div class="h-[35%] flex-1 rounded-t bg-indigo-100"></div>
                                                    <div class="h-[55%] flex-1 rounded-t bg-indigo-200"></div>
                                                    <div class="h-[45%] flex-1 rounded-t bg-indigo-200"></div>
                                                    <div class="h-[72%] flex-1 rounded-t bg-indigo-400"></div>
                                                    <div class="h-[60%] flex-1 rounded-t bg-indigo-300"></div>
                                                    <div class="h-[90%] flex-1 rounded-t bg-indigo-600"></div>
                                                    <div class="h-[68%] flex-1 rounded-t bg-indigo-400"></div>

                                                </div>

                                            </div>


                                            <!-- Schedule -->
                                            <div class="w-full rounded-xl border border-slate-200 bg-white p-4 sm:w-1/2">

                                                <div class="text-[10px] font-bold text-slate-950">
                                                    Jadwal Berikutnya
                                                </div>


                                                <div class="mt-4 space-y-2">

                                                    <div class="flex items-center gap-2 rounded-lg bg-slate-50 p-2.5">

                                                        <div class="h-8 w-8 shrink-0 rounded-lg bg-indigo-100"></div>

                                                        <div class="min-w-0">

                                                            <div class="truncate text-[9px] font-bold text-slate-800">
                                                                Matematika
                                                            </div>

                                                            <div class="mt-0.5 text-[8px] text-slate-400">
                                                                08:00 — Ruang 204
                                                            </div>

                                                        </div>

                                                    </div>


                                                    <div class="flex items-center gap-2 rounded-lg bg-slate-50 p-2.5">

                                                        <div class="h-8 w-8 shrink-0 rounded-lg bg-violet-100"></div>

                                                        <div class="min-w-0">

                                                            <div class="truncate text-[9px] font-bold text-slate-800">
                                                                Bahasa Indonesia
                                                            </div>

                                                            <div class="mt-0.5 text-[8px] text-slate-400">
                                                                10:00 — Ruang 201
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <!-- Progress -->
                                        <div class="mt-3 rounded-xl border border-slate-200 bg-white p-4">

                                            <div class="flex items-center justify-between">

                                                <div>

                                                    <div class="text-[10px] font-bold text-slate-950">
                                                        Progress Pembelajaran
                                                    </div>

                                                    <div class="mt-1 text-[8px] text-slate-400">
                                                        Semester Ganjil
                                                    </div>

                                                </div>


                                                <div class="text-sm font-bold text-indigo-600">
                                                    78%
                                                </div>

                                            </div>


                                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">

                                                <div class="h-full w-[78%] rounded-full bg-indigo-600"></div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- Floating status -->
                            <div class="absolute -bottom-5 -left-5 hidden rounded-xl border border-slate-200 bg-white p-3 shadow-xl shadow-slate-900/10 sm:block">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50">

                                        <svg
                                            class="h-4 w-4 text-emerald-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>

                                    </div>

                                    <div>

                                        <div class="text-[10px] font-bold text-slate-900">
                                            Semua sistem normal
                                        </div>

                                        <div class="mt-0.5 text-[9px] text-slate-400">
                                            Platform siap digunakan
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- TRUST / PRODUCT CATEGORIES -->
        <!-- ===================================================== -->

        <section class="border-b border-slate-200 bg-white">

            <div class="mx-auto flex w-full max-w-7xl flex-col sm:flex-row sm:divide-x sm:divide-slate-200">

                <div class="flex-1 px-6 py-7 text-center sm:py-8">
                    <div class="text-sm font-bold text-slate-950">
                        Academic Management
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        Kelola seluruh data akademik
                    </div>
                </div>


                <div class="flex-1 border-t border-slate-200 px-6 py-7 text-center sm:border-t-0 sm:py-8">
                    <div class="text-sm font-bold text-slate-950">
                        Digital Learning
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        Materi, tugas dan pembelajaran
                    </div>
                </div>


                <div class="flex-1 border-t border-slate-200 px-6 py-7 text-center sm:border-t-0 sm:py-8">
                    <div class="text-sm font-bold text-slate-950">
                        Assessment
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        Ujian, kuis dan nilai
                    </div>
                </div>


                <div class="flex-1 border-t border-slate-200 px-6 py-7 text-center sm:border-t-0 sm:py-8">
                    <div class="text-sm font-bold text-slate-950">
                        Multi Role
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        Admin, guru, siswa & orang tua
                    </div>
                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- ABOUT -->
        <!-- ===================================================== -->

        <section
            id="tentang"
            class="border-b border-slate-200 bg-white py-20 sm:py-24"
        >

            <div class="mx-auto w-full max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-24">

                    <div class="w-full lg:w-1/2">

                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                            Tentang Platform
                        </div>

                        <h2 class="mt-4 max-w-xl text-3xl font-bold leading-tight tracking-[-0.03em] text-slate-950 sm:text-4xl">
                            Infrastruktur digital untuk operasional sekolah modern.
                        </h2>

                    </div>


                    <div class="w-full lg:w-1/2">

                        <p class="text-base leading-7 text-slate-600">
                            SIAKAD + LMS dirancang untuk menyatukan proses
                            akademik dan pembelajaran digital dalam satu
                            platform yang mudah digunakan.
                        </p>

                        <p class="mt-5 text-base leading-7 text-slate-600">
                            Guru dapat fokus mengajar, siswa dapat belajar
                            dengan lebih terstruktur, sementara administrasi
                            sekolah memiliki data yang lebih terpusat dan
                            mudah dipantau.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- FEATURES -->
        <!-- ===================================================== -->

        <section
            id="fitur"
            class="bg-slate-50 py-20 sm:py-24"
        >

            <div class="mx-auto w-full max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="max-w-2xl">

                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                        Product Features
                    </div>

                    <h2 class="mt-4 text-3xl font-bold tracking-[-0.03em] text-slate-950 sm:text-4xl">
                        Semua modul yang dibutuhkan sekolah.
                    </h2>

                    <p class="mt-4 text-base leading-7 text-slate-500">
                        Setiap modul dirancang untuk bekerja sebagai satu
                        ekosistem, bukan aplikasi yang berdiri sendiri.
                    </p>

                </div>


                <!-- Feature Grid -->
                <div class="mt-12 flex flex-wrap gap-4">

                    <!-- Card 1 -->
                    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"
                                />
                            </svg>

                        </div>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            SIAKAD
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Kelola siswa, guru, kelas, mata pelajaran,
                            jadwal, absensi dan data akademik.
                        </p>

                    </div>


                    <!-- Card 2 -->
                    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 text-violet-600">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 5.5A2.5 2.5 0 016.5 3H20v18H6.5A2.5 2.5 0 014 18.5v-13z"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M8 7h8M8 11h8M8 15h5"
                                />
                            </svg>

                        </div>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            LMS
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Materi pembelajaran, bab, kuis, tugas
                            dan aktivitas belajar digital.
                        </p>

                    </div>


                    <!-- Card 3 -->
                    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="4"
                                    y="3"
                                    width="16"
                                    height="18"
                                    rx="2"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M8 7h8M8 11h8M8 15h5"
                                />

                            </svg>

                        </div>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Assessment
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Ujian CBT, kuis, penilaian dan
                            rekap hasil belajar siswa.
                        </p>

                    </div>


                    <!-- Card 4 -->
                    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"
                                />

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"
                                />

                            </svg>

                        </div>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Multi Role
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Workspace berbeda untuk admin,
                            guru, siswa dan orang tua.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- ADVANTAGES -->
        <!-- ===================================================== -->

        <section
            id="keunggulan"
            class="border-y border-slate-200 bg-white py-20 sm:py-24"
        >

            <div class="mx-auto w-full max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-12 lg:flex-row lg:items-center lg:gap-20">

                    <!-- Content -->
                    <div class="w-full lg:w-1/2">

                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                            Kenapa Platform Ini
                        </div>

                        <h2 class="mt-4 max-w-xl text-3xl font-bold leading-tight tracking-[-0.03em] text-slate-950 sm:text-4xl">
                            Lebih sedikit tools.
                            Lebih banyak fokus.
                        </h2>

                        <p class="mt-5 max-w-xl text-base leading-7 text-slate-500">
                            Semua informasi penting sekolah berada dalam
                            satu workspace yang terstruktur.
                        </p>


                        <div class="mt-9 space-y-7">

                            <div class="flex gap-4">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-xs font-bold text-white">
                                    01
                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-950">
                                        Data Terpusat
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Data akademik dan pembelajaran
                                        dikelola dari satu sumber.
                                    </p>

                                </div>

                            </div>


                            <div class="flex gap-4">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-xs font-bold text-white">
                                    02
                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-950">
                                        Role-Based Access
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Setiap pengguna melihat fitur
                                        sesuai dengan perannya.
                                    </p>

                                </div>

                            </div>


                            <div class="flex gap-4">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-950 text-xs font-bold text-white">
                                    03
                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-950">
                                        Responsive
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Nyaman digunakan melalui desktop,
                                        tablet maupun smartphone.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Visual -->
                    <div class="w-full lg:w-1/2">

                        <div class="relative mx-auto max-w-xl">

                            <div class="absolute -inset-5 rounded-[32px] bg-indigo-100/60 blur-3xl"></div>


                            <div class="relative overflow-hidden rounded-3xl bg-slate-950 p-7 text-white shadow-2xl shadow-slate-900/20 sm:p-9">

                                <div class="flex items-center justify-between">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-sm font-bold">
                                            S
                                        </div>

                                        <div>

                                            <div class="text-xs font-bold">
                                                SIAKAD + LMS
                                            </div>

                                            <div class="mt-0.5 text-[10px] text-slate-500">
                                                School Management Platform
                                            </div>

                                        </div>

                                    </div>


                                    <div class="flex items-center gap-2">

                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>

                                        <span class="text-[9px] font-medium text-emerald-300">
                                            Operational
                                        </span>

                                    </div>

                                </div>


                                <div class="mt-10">

                                    <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-indigo-300">
                                        Connected workspace
                                    </div>

                                    <h3 class="mt-3 text-2xl font-bold leading-tight tracking-[-0.03em] sm:text-3xl">
                                        Satu ekosistem untuk seluruh aktivitas sekolah.
                                    </h3>

                                </div>


                                <div class="mt-8 space-y-2">

                                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/20 text-indigo-300">
                                            01
                                        </div>

                                        <div class="text-xs font-medium">
                                            Administrasi Akademik
                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/20 text-violet-300">
                                            02
                                        </div>

                                        <div class="text-xs font-medium">
                                            Digital Learning
                                        </div>

                                    </div>


                                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300">
                                            03
                                        </div>

                                        <div class="text-xs font-medium">
                                            Assessment & Reporting
                                        </div>

                                    </div>

                                </div>


                                <div class="mt-8 border-t border-white/10 pt-6">

                                    <div class="flex items-center justify-between">

                                        <div>

                                            <div class="text-[10px] text-slate-500">
                                                Platform status
                                            </div>

                                            <div class="mt-1 text-sm font-bold">
                                                Ready for learning
                                            </div>

                                        </div>


                                        <div class="text-right">

                                            <div class="text-2xl font-bold">
                                                99.9%
                                            </div>

                                            <div class="text-[9px] text-slate-500">
                                                service availability
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- DEMO -->
        <!-- ===================================================== -->

        <section
            id="demo"
            class="bg-slate-50 py-20 sm:py-24"
        >

            <div class="mx-auto w-full max-w-7xl px-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">

                    <div class="max-w-2xl">

                        <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                            Demo Account
                        </div>

                        <h2 class="mt-4 text-3xl font-bold tracking-[-0.03em] text-slate-950 sm:text-4xl">
                            Jelajahi platform berdasarkan role.
                        </h2>

                        <p class="mt-4 text-base leading-7 text-slate-500">
                            Gunakan akun demo berikut untuk melihat
                            pengalaman pengguna pada masing-masing role.
                        </p>

                    </div>


                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500">

                        Password:

                        <code class="ml-1 rounded-md bg-slate-100 px-2 py-1 font-mono font-semibold text-slate-800">
                            password
                        </code>

                    </div>

                </div>


                <div class="mt-10 flex flex-wrap gap-4">

                    <!-- Admin -->
                    <div class="flex w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <span class="w-fit rounded-md bg-indigo-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-indigo-700">
                            Tata Usaha
                        </span>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Operator / Admin
                        </h3>

                        <p class="mt-2 min-h-[72px] text-sm leading-6 text-slate-500">
                            Kelola data master siswa, guru,
                            kelas, jadwal dan nilai.
                        </p>

                        <div class="mt-5 rounded-xl bg-slate-50 p-3">

                            <div class="text-[9px] font-medium text-slate-400">
                                Email
                            </div>

                            <div class="mt-1 break-all font-mono text-[11px] font-semibold text-slate-800">
                                admin@school.test
                            </div>

                        </div>

                        <a
                            href="{{ route('login') }}"
                            class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-xs font-semibold text-white transition hover:bg-slate-800"
                        >
                            Login Admin
                        </a>

                    </div>


                    <!-- Teacher -->
                    <div class="flex w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <span class="w-fit rounded-md bg-violet-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-violet-700">
                            Pendidik
                        </span>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Guru
                        </h3>

                        <p class="mt-2 min-h-[72px] text-sm leading-6 text-slate-500">
                            Kelola materi, tugas, kuis,
                            ujian dan nilai siswa.
                        </p>

                        <div class="mt-5 rounded-xl bg-slate-50 p-3">

                            <div class="text-[9px] font-medium text-slate-400">
                                Email
                            </div>

                            <div class="mt-1 break-all font-mono text-[11px] font-semibold text-slate-800">
                                teacher@school.test
                            </div>

                        </div>

                        <a
                            href="{{ route('login') }}"
                            class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-xs font-semibold text-white transition hover:bg-slate-800"
                        >
                            Login Guru
                        </a>

                    </div>


                    <!-- Student -->
                    <div class="flex w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <span class="w-fit rounded-md bg-emerald-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-emerald-700">
                            Peserta Didik
                        </span>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Siswa
                        </h3>

                        <p class="mt-2 min-h-[72px] text-sm leading-6 text-slate-500">
                            Akses materi, tugas, ujian,
                            progres dan Kartu Tanda Siswa.
                        </p>

                        <div class="mt-5 rounded-xl bg-slate-50 p-3">

                            <div class="text-[9px] font-medium text-slate-400">
                                Email
                            </div>

                            <div class="mt-1 break-all font-mono text-[11px] font-semibold text-slate-800">
                                student@school.test
                            </div>

                        </div>

                        <a
                            href="{{ route('login') }}"
                            class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-xs font-semibold text-white transition hover:bg-slate-800"
                        >
                            Login Siswa
                        </a>

                    </div>


                    <!-- Parent -->
                    <div class="flex w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:w-[calc(50%-8px)] lg:w-[calc(25%-12px)]">

                        <span class="w-fit rounded-md bg-amber-50 px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-amber-700">
                            Wali Murid
                        </span>

                        <h3 class="mt-5 text-sm font-bold text-slate-950">
                            Orang Tua
                        </h3>

                        <p class="mt-2 min-h-[72px] text-sm leading-6 text-slate-500">
                            Pantau kehadiran, nilai, tugas
                            dan perkembangan belajar anak.
                        </p>

                        <div class="mt-5 rounded-xl bg-slate-50 p-3">

                            <div class="text-[9px] font-medium text-slate-400">
                                Email
                            </div>

                            <div class="mt-1 break-all font-mono text-[11px] font-semibold text-slate-800">
                                parent@school.test
                            </div>

                        </div>

                        <a
                            href="{{ route('login') }}"
                            class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-xs font-semibold text-white transition hover:bg-slate-800"
                        >
                            Login Orang Tua
                        </a>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- KTS VERIFICATION -->
        <!-- ===================================================== -->

        <section
            id="verifikasi"
            class="border-y border-slate-200 bg-white py-20 sm:py-24"
        >

            <div class="mx-auto w-full max-w-5xl px-5 sm:px-6 lg:px-8">

                <div class="flex flex-col gap-10 lg:flex-row lg:items-center lg:gap-16">

                    <div class="w-full lg:w-5/12">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="16"
                                    rx="2"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M7 8h4M7 12h3"
                                />

                                <circle
                                    cx="16"
                                    cy="11"
                                    r="2.5"
                                />

                            </svg>

                        </div>


                        <div class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                            Public Verification
                        </div>


                        <h2 class="mt-3 text-3xl font-bold tracking-[-0.03em] text-slate-950">
                            Verifikasi Kartu Tanda Siswa
                        </h2>


                        <p class="mt-4 text-sm leading-6 text-slate-500">
                            Validasi identitas siswa menggunakan
                            Nomor Induk Siswa tanpa harus masuk
                            ke dashboard internal.
                        </p>

                    </div>


                    <!-- Form -->
                    <div class="w-full lg:w-7/12">

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">

                            <div class="rounded-xl border border-slate-200 bg-white p-5">

                                <div class="flex items-center justify-between gap-4">

                                    <div>

                                        <div class="text-sm font-bold text-slate-950">
                                            Cek data siswa
                                        </div>

                                        <div class="mt-1 text-xs text-slate-400">
                                            Masukkan Nomor Induk Siswa
                                        </div>

                                    </div>


                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50">

                                        <svg
                                            class="h-4 w-4 text-emerald-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>

                                    </div>

                                </div>


                                <form
                                    class="mt-5 flex flex-col gap-3 sm:flex-row"
                                    onsubmit="event.preventDefault(); var nis = document.getElementById('kts_token').value.trim(); if (nis) { window.location.href = '{{ url('/verify/student') }}/' + encodeURIComponent(nis); }"
                                >

                                    <input
                                        type="text"
                                        id="kts_token"
                                        value="202401001"
                                        placeholder="Contoh: 202401001"
                                        required
                                        class="h-11 min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10"
                                    >


                                    <button
                                        type="submit"
                                        class="inline-flex h-11 shrink-0 items-center justify-center rounded-xl bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800"
                                    >
                                        Verifikasi
                                    </button>

                                </form>


                                <div class="mt-5 border-t border-slate-100 pt-4">

                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Sample NIS
                                    </div>


                                    <div class="mt-2 flex flex-wrap gap-2">

                                        <button
                                            type="button"
                                            onclick="document.getElementById('kts_token').value='202401001'"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600"
                                        >
                                            202401001
                                        </button>


                                        <button
                                            type="button"
                                            onclick="document.getElementById('kts_token').value='202401002'"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600"
                                        >
                                            202401002
                                        </button>


                                        <button
                                            type="button"
                                            onclick="document.getElementById('kts_token').value='202401003'"
                                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[10px] font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600"
                                        >
                                            202401003
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- CTA -->
        <!-- ===================================================== -->

        <section class="relative overflow-hidden bg-slate-950 py-20 sm:py-24">

            <div class="pointer-events-none absolute left-1/2 top-[-250px] h-[600px] w-[800px] -translate-x-1/2 rounded-full bg-indigo-600/20 blur-3xl"></div>


            <div class="relative mx-auto w-full max-w-3xl px-5 text-center sm:px-6">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-sm font-bold text-slate-950">
                    S
                </div>


                <div class="mt-6 text-xs font-bold uppercase tracking-[0.2em] text-indigo-300">
                    SIAKAD + LMS
                </div>


                <h2 class="mt-4 text-3xl font-bold tracking-[-0.03em] text-white sm:text-4xl">
                    Satu platform untuk sekolah yang lebih terhubung.
                </h2>


                <p class="mx-auto mt-5 max-w-xl text-sm leading-7 text-slate-400 sm:text-base">
                    Kelola akademik, pembelajaran, evaluasi,
                    dan aktivitas sekolah dari satu sistem.
                </p>


                <div class="mt-8">

                    @auth

                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex h-12 items-center rounded-xl bg-white px-7 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                        >
                            Buka Dashboard
                        </a>

                    @else

                        @if (Route::has('login'))

                            <a
                                href="{{ route('login') }}"
                                class="inline-flex h-12 items-center rounded-xl bg-white px-7 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                            >
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

    <footer class="border-t border-slate-800 bg-slate-950">

        <div class="mx-auto flex w-full max-w-7xl flex-col gap-5 px-5 py-8 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-xs font-bold text-slate-950">
                    S
                </div>

                <div>

                    <div class="text-xs font-bold text-white">
                        SIAKAD + LMS
                    </div>

                    <div class="mt-0.5 text-[10px] text-slate-500">
                        SMA Nusantara Digital
                    </div>

                </div>

            </div>


            <div class="text-[11px] text-slate-500">
                © {{ date('Y') }} SMA Nusantara Digital. All rights reserved.
            </div>

        </div>

    </footer>

</body>
</html>