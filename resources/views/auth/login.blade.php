<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIAKAD + LMS E-Learning</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-900 selection:bg-indigo-600 selection:text-white">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 w-full">
        
        <!-- KOLOM 1: Sisi Branding / SaaS Showcase -->
        <div class="hidden lg:flex lg:col-span-7 relative bg-gradient-to-br from-indigo-50/60 via-slate-50 to-indigo-100/40 p-12 xl:p-16 flex-col justify-between overflow-hidden border-r border-slate-200/80">
            <!-- Background Decorative Blur Elements -->
            <div class="absolute -top-32 -left-32 w-80 h-80 bg-indigo-400/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Header Brand -->
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-white font-bold text-xl shadow-lg shadow-indigo-200">
                        S
                    </div>
                    <span class="font-bold text-lg tracking-tight text-slate-900">SIAKAD + LMS</span>
                </div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Operasional Normal
                </span>
            </div>

            <!-- Main Value Proposition -->
            <div class="relative z-10 my-auto max-w-xl space-y-6 py-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200/80 text-indigo-700 text-xs font-bold tracking-wide uppercase shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Enterprise Academic Suite
                </div>
                <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                    Ekosistem Pembelajaran & Administrasi Terpadu
                </h1>
                <p class="text-slate-600 text-sm xl:text-base leading-relaxed font-normal">
                    Kelola nilai, presensi, materi e-learning, hingga laporan akademik secara real-time dalam satu dashboard SaaS modern untuk <span class="text-indigo-900 font-bold">SMA Nusantara Digital</span>.
                </p>

                <!-- Feature Grid Cards -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-white/80 border border-slate-200/90 shadow-sm backdrop-blur-md space-y-1.5">
                        <div class="text-indigo-700 font-bold text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Keamanan Tinggi
                        </div>
                        <p class="text-xs text-slate-500">Enkripsi data tingkat lanjut untuk privasi sekolah.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/80 border border-slate-200/90 shadow-sm backdrop-blur-md space-y-1.5">
                        <div class="text-indigo-700 font-bold text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Real-Time Sync
                        </div>
                        <p class="text-xs text-slate-500">Sinkronisasi instan antara guru, siswa, dan orang tua.</p>
                    </div>
                </div>
            </div>

            <!-- Footer Meta -->
            <div class="relative z-10 text-xs text-slate-500 flex items-center justify-between">
                <span>&copy; {{ date('Y') }} SMA Nusantara Digital.</span>
                <span class="font-mono text-indigo-600 font-semibold">v2.9-saas</span>
            </div>
        </div>

        <!-- KOLOM 2: Area Form Login (Rombak Total & Clean UI) -->
        <div class="lg:col-span-5 flex items-center justify-center p-6 sm:p-10 lg:p-12 bg-slate-50">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/90 p-8 sm:p-10 space-y-6">
                
                <!-- Mobile Top Brand / Back Link -->
                <div class="flex items-center justify-between lg:hidden mb-2">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Beranda</span>
                    </a>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Sistem Aktif
                    </span>
                </div>

                <!-- Form Header -->
                <div class="space-y-1.5">
                    <div class="flex lg:hidden h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold text-lg mb-3 shadow-md shadow-indigo-200">
                        S
                    </div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat Datang</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Masukkan kredensial akun Anda untuk masuk ke sistem.</p>
                </div>

                <!-- Error Notification -->
                @if($errors->any())
                    <div class="p-3.5 text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-2.5 shadow-sm">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Input Email (Clean Container with Divider) -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email Pengguna</label>
                        <div class="relative flex items-center bg-slate-50/60 border border-slate-300 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition shadow-sm overflow-hidden">
                            <!-- Icon Box + Divider -->
                            <div class="pl-3.5 pr-3 py-3 flex items-center justify-center border-r border-slate-200 text-slate-400 bg-slate-100/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                            </div>
                            <!-- Input Field (Kosong) -->
                            <input type="email" id="email" name="email" value="" required autofocus
                                   placeholder="nama@school.test"
                                   class="w-full px-3.5 py-2.5 bg-transparent border-none text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-0">
                        </div>
                    </div>

                    <!-- Input Password (Clean Container with Divider) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                        </div>
                        <div class="relative flex items-center bg-slate-50/60 border border-slate-300 rounded-xl focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition shadow-sm overflow-hidden">
                            <!-- Icon Box + Divider -->
                            <div class="pl-3.5 pr-3 py-3 flex items-center justify-center border-r border-slate-200 text-slate-400 bg-slate-100/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <!-- Input Field (Kosong) -->
                            <input type="password" id="password" name="password" value="" required 
                                   placeholder="••••••••"
                                   class="w-full px-3.5 py-2.5 bg-transparent border-none text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-0">
                            <!-- Toggle Button Eye -->
                            <button type="button" onclick="togglePasswordVisibility()" class="px-3.5 py-3 text-slate-400 hover:text-slate-600 transition flex items-center justify-center" title="Lihat/Sembunyikan Sandi">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center space-x-2 text-slate-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                            <span>Ingat Sesi Saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition shadow-lg shadow-indigo-200 hover:shadow-indigo-300 active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Back Link Footer -->
                <div class="text-center pt-2">
                    <a href="{{ url('/') }}" class="text-xs text-slate-500 hover:text-indigo-600 transition">&larr; Kembali ke Beranda Utama</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>';
            } else {
                pwd.type = 'password';
                eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>
</html>