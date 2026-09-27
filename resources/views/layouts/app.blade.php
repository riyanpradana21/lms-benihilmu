<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'SIAKAD LMS') }} - Sistem Informasi Akademik & LMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="h-full bg-slate-50 font-sans antialiased text-slate-700" x-data="{ mobileMenuOpen: false }">

    <div class="min-h-screen">

        {{-- =========================================================
            DESKTOP SIDEBAR
        ========================================================== --}}
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-slate-200 bg-white lg:flex">

            {{-- Brand --}}
            <div class="flex h-16 shrink-0 items-center border-b border-slate-100 px-5">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white shadow-sm shadow-indigo-200">
                        S
                    </div>

                    <div class="min-w-0">
                        <div class="truncate text-sm font-bold tracking-tight text-slate-900">
                            BenihIlmu
                        </div>

                        <div class="truncate text-[10px] font-medium text-slate-400">
                            E-Learning Systems
                        </div>
                    </div>
                </a>
            </div>


            {{-- Navigation --}}
            <div class="flex-1 overflow-y-auto px-3 py-5">
                @include('layouts.navigation')
            </div>


            {{-- User Footer --}}
            <div class="border-t border-slate-100 bg-white p-3">

                <div class="flex items-center gap-3 rounded-xl px-2 py-2.5 transition-colors hover:bg-slate-50">

                    {{-- Avatar --}}
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-600">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>

                    {{-- User Info --}}
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="truncate text-[11px] capitalize text-slate-400">
                            {{ str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'User') }}
                        </p>
                    </div>

                    {{-- Logout --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit" title="Keluar"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>

                </div>

            </div>

        </aside>


        {{-- =========================================================
            MOBILE BACKDROP
        ========================================================== --}}
        <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm lg:hidden"
            @click="mobileMenuOpen = false" style="display: none;"></div>


        {{-- =========================================================
            MOBILE SIDEBAR
        ========================================================== --}}
        <aside x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white shadow-xl lg:hidden"
            style="display: none;">

            {{-- Mobile Brand --}}
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100 px-5">

                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">
                        S
                    </div>

                    <div>
                        <div class="text-sm font-bold text-slate-900">
                            BenihIlmu
                        </div>

                        <div class="text-[10px] font-medium text-slate-400">
                            E-Learning Systems
                        </div>
                    </div>
                </div>

                <button type="button" @click="mobileMenuOpen = false"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>


            {{-- Mobile Navigation --}}
            <div class="flex-1 overflow-y-auto px-3 py-5">
                @include('layouts.navigation')
            </div>


            {{-- Mobile User --}}
            <div class="border-t border-slate-100 p-4">

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-600">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-slate-900">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="truncate text-[11px] text-slate-400">
                            {{ auth()->user()->email }}
                        </p>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                            title="Keluar">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>

                </div>

            </div>

        </aside>


        {{-- =========================================================
            MAIN CONTENT
        ========================================================== --}}
        <div class="min-h-screen lg:pl-64">

            {{-- =====================================================
                TOP NAVBAR
            ====================================================== --}}
            <header class="sticky top-0 z-30 h-16 border-b border-slate-200/70 bg-white/90 backdrop-blur-xl">

                <div class="flex h-full items-center justify-between px-4 sm:px-6 lg:px-8">

                    {{-- =====================================================
            LEFT SIDE
        ====================================================== --}}
                    <div class="flex min-w-0 items-center gap-3">

                        {{-- Mobile Menu --}}
                        <button type="button" @click="mobileMenuOpen = true"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>


                        {{-- Page Heading --}}
                        <div class="min-w-0">

                            <h1 class="truncate text-sm font-semibold tracking-tight text-slate-900 sm:text-[15px]">
                                {{ $heading ?? 'Dashboard' }}
                            </h1>

                            <div class="mt-0.5 hidden items-center gap-1.5 sm:flex">
                                <span class="text-[11px] text-slate-400">
                                    SIAKAD
                                </span>

                                <span class="text-[10px] text-slate-300">
                                    /
                                </span>

                                <span class="text-[11px] text-slate-400">
                                    LMS
                                </span>
                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
            RIGHT SIDE
        ====================================================== --}}
                    <div class="flex items-center gap-2 sm:gap-3">


                        {{-- =================================================
                ACTIVE ACADEMIC PERIOD
            ================================================== --}}
                        @if (isset($activeYear, $activeSemester) && $activeYear && $activeSemester)
                            <div
                                class="hidden items-center gap-2.5 rounded-lg px-2.5 py-1.5 transition-colors hover:bg-slate-50 sm:flex">

                                {{-- Active Indicator --}}
                                <span class="relative flex h-2 w-2 shrink-0">

                                    <span
                                        class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-40"></span>

                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>

                                </span>


                                {{-- Academic Information --}}
                                <div class="flex items-center gap-1.5 whitespace-nowrap">

                                    <span class="text-[11px] font-medium text-slate-400">
                                        {{ $activeYear->name }}
                                    </span>

                                    <span class="text-slate-300">
                                        ·
                                    </span>

                                    <span class="text-[11px] font-semibold text-slate-600">
                                        Semester {{ $activeSemester->name }}
                                    </span>

                                </div>

                            </div>
                        @endif


                        {{-- Divider --}}
                        @if (isset($activeYear, $activeSemester) && $activeYear && $activeSemester)
                            <div class="hidden h-5 w-px bg-slate-200 sm:block"></div>
                        @endif


                        {{-- =================================================
                USER DROPDOWN
            ================================================== --}}
                        <div class="relative" x-data="{ open: false }">

                            <button type="button" @click="open = !open"
                                class="group flex items-center gap-2 rounded-xl px-1.5 py-1 transition-colors hover:bg-slate-50 focus:outline-none">

                                {{-- Avatar --}}
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-[10px] font-bold text-indigo-600 ring-1 ring-indigo-100 transition group-hover:bg-indigo-100">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                                </div>


                                {{-- User --}}
                                <div class="hidden min-w-0 text-left md:block">

                                    <p class="max-w-[150px] truncate text-xs font-semibold text-slate-800">
                                        {{ auth()->user()->name }}
                                    </p>

                                    <p class="mt-0.5 text-[10px] capitalize text-slate-400">
                                        {{ str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'User') }}
                                    </p>

                                </div>


                                {{-- Chevron --}}
                                <svg class="hidden h-4 w-4 text-slate-400 transition-transform duration-200 group-hover:text-slate-500 md:block"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                            </button>


                            {{-- =================================================
                    USER MENU
                ================================================== --}}
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
                                class="absolute right-0 mt-2.5 w-60 origin-top-right overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50"
                                style="display: none;">

                                {{-- User Header --}}
                                <div class="px-4 py-3.5">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-600">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-xs font-semibold text-slate-900">
                                                {{ auth()->user()->name }}
                                            </p>

                                            <p class="mt-0.5 truncate text-[10px] text-slate-400">
                                                {{ auth()->user()->email }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- Academic Info in Mobile Dropdown --}}
                                @if (isset($activeYear, $activeSemester) && $activeYear && $activeSemester)
                                    <div class="border-y border-slate-100 bg-slate-50/70 px-4 py-3 sm:hidden">

                                        <div class="flex items-center gap-2">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            <div>

                                                <p
                                                    class="text-[9px] font-medium uppercase tracking-wide text-slate-400">
                                                    Periode Aktif
                                                </p>

                                                <p class="mt-0.5 text-[11px] font-semibold text-slate-700">
                                                    {{ $activeYear->name }}
                                                    <span class="mx-1 text-slate-300">·</span>
                                                    Semester {{ $activeSemester->name }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>
                                @endif


                                {{-- Logout --}}
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf

                                    <button type="submit"
                                        class="flex w-full items-center gap-2.5 px-4 py-3 text-left text-xs font-medium text-rose-600 transition hover:bg-rose-50">

                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>

                                        <span>
                                            Keluar
                                        </span>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </header>


            {{-- =====================================================
                FLASH ALERTS
            ====================================================== --}}
            <div class="px-4 pt-4 sm:px-6 lg:px-8">

                @if (session('success'))
                    <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                        role="alert">

                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>

                        <span>{{ session('success') }}</span>

                    </div>
                @endif


                @if (session('error'))
                    <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
                        role="alert">

                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>

                        <span>{{ session('error') }}</span>

                    </div>
                @endif


                @if ($errors->any())

                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
                        role="alert">

                        <p class="font-semibold">
                            Terdapat kesalahan pengisian data:
                        </p>

                        <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>

                @endif

            </div>


            {{-- =====================================================
                PAGE CONTENT
            ====================================================== --}}
            <main class="min-h-[calc(100vh-4rem)] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

                {{ $slot ?? '' }}

                @yield('content')

            </main>

        </div>

    </div>

    @livewireScripts

</body>

</html>
