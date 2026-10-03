@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-950 via-blue-900 to-sky-800 p-7 text-white shadow-xl sm:p-9">
            <div class="relative z-10 max-w-2xl"><p class="text-sm font-semibold tracking-wide text-blue-200">Dashboard Administrasi</p><h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Selamat datang, {{ auth()->user()->name }}</h1><p class="mt-3 text-sm leading-6 text-blue-100">Pantau aktivitas sekolah, kelola informasi, dan lihat ringkasan akademik dari satu tempat.</p><div class="mt-6 flex flex-wrap gap-3"><a href="{{ route('admin.announcements.index') }}" class="inline-flex rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">Kelola pengumuman</a><a href="{{ route('admin.exam-schedules.index') }}" class="inline-flex rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-blue-950 transition hover:bg-blue-50">Buat jadwal ujian</a></div></div><div class="pointer-events-none absolute -right-10 -top-24 h-72 w-72 rounded-full border-[36px] border-white/10"></div><div class="pointer-events-none absolute -bottom-32 right-32 h-64 w-64 rounded-full bg-sky-400/10 blur-2xl"></div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2"><a href="{{ route('admin.announcements.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"><p class="text-xs font-bold uppercase tracking-wide text-blue-700">Komunikasi sekolah</p><h2 class="mt-2 text-lg font-bold text-slate-900">Kelola Pengumuman</h2><p class="mt-1 text-sm text-slate-500">Terbitkan informasi untuk siswa, guru, dan orang tua.</p><span class="mt-4 inline-block text-sm font-semibold text-blue-800">Buat pengumuman →</span></a><a href="{{ route('admin.exam-schedules.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"><p class="text-xs font-bold uppercase tracking-wide text-blue-700">Kalender akademik</p><h2 class="mt-2 text-lg font-bold text-slate-900">Jadwal Ujian Siswa</h2><p class="mt-1 text-sm text-slate-500">Atur Ujian Praktik, Ujian Sekolah, PTS, dan PAS per siswa dan mata pelajaran.</p><span class="mt-4 inline-block text-sm font-semibold text-blue-800">Buat jadwal →</span></a></div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => 'Siswa Aktif', 'value' => $stats['total_students']],
                ['label' => 'Guru Aktif', 'value' => $stats['total_teachers']],
                ['label' => 'Kelas', 'value' => $stats['total_classes']],
                ['label' => 'Mata Pelajaran', 'value' => $stats['total_subjects']],
                ['label' => 'Kursus LMS', 'value' => $stats['total_courses']],
                ['label' => 'Menunggu Dinilai', 'value' => $stats['pending_grading']],
                ['label' => 'Presensi Hari Ini', 'value' => $stats['attendance_today']],
            ] as $stat)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-slate-900">Kelas terbaru</h3>
                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($recentClasses as $schoolClass)
                        <div class="flex items-center justify-between py-3 text-sm">
                            <span class="font-medium text-slate-800">{{ $schoolClass->name }}</span>
                            <span class="text-slate-500">{{ $schoolClass->students_count }} siswa</span>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-slate-500">Belum ada data kelas.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-slate-900">Kursus terbaru</h3>
                <div class="mt-4 divide-y divide-slate-100">
                    @forelse($recentCourses as $course)
                        <div class="py-3 text-sm">
                            <p class="font-medium text-slate-800">{{ $course->title }}</p>
                            <p class="mt-1 text-slate-500">{{ $course->subject?->name }} · {{ $course->schoolClass?->name }}</p>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-slate-500">Belum ada kursus.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
