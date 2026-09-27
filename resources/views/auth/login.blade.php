<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIAKAD + LMS E-Learning</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 p-8 space-y-6">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-xl bg-indigo-600 text-white font-black text-2xl items-center justify-center shadow-md">
                S
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">SIAKAD + LMS</h1>
            <p class="text-xs text-slate-500">Sistem Informasi Akademik & E-Learning Terpadu<br><span class="font-medium text-slate-700">SMA Nusantara Digital</span></p>
        </div>

        @if($errors->any())
            <div class="p-3 text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Pengguna</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@school.test') }}" required 
                       class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                </div>
                <input type="password" id="password" name="password" value="password" required 
                       class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    <span>Ingat Sesi Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition shadow-sm hover:shadow">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Quick Demo Switcher for Evaluation -->
        <div class="pt-4 border-t border-slate-100">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Demo Pengujian (1-Klik Isi)</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="fillForm('admin@school.test', 'password')" class="p-2 border border-slate-200 rounded-lg text-left hover:bg-indigo-50 hover:border-indigo-300 transition group">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-600 block">Admin / TU</span>
                    <span class="text-[10px] text-slate-500">admin@school.test</span>
                </button>
                <button type="button" onclick="fillForm('teacher@school.test', 'password')" class="p-2 border border-slate-200 rounded-lg text-left hover:bg-indigo-50 hover:border-indigo-300 transition group">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-600 block">Guru</span>
                    <span class="text-[10px] text-slate-500">teacher@school.test</span>
                </button>
                <button type="button" onclick="fillForm('student@school.test', 'password')" class="p-2 border border-slate-200 rounded-lg text-left hover:bg-indigo-50 hover:border-indigo-300 transition group">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-600 block">Siswa</span>
                    <span class="text-[10px] text-slate-500">student@school.test</span>
                </button>
                <button type="button" onclick="fillForm('parent@school.test', 'password')" class="p-2 border border-slate-200 rounded-lg text-left hover:bg-indigo-50 hover:border-indigo-300 transition group">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-600 block">Orang Tua</span>
                    <span class="text-[10px] text-slate-500">parent@school.test</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillForm(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
