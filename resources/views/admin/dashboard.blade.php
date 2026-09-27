@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm text-slate-500">Ringkasan operasional sekolah</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Dashboard Admin</h2>
            </div>
            @if($activeYear && $activeSemester)
                <p class="text-sm text-slate-500">{{ $activeYear->name }} · Semester {{ $activeSemester->name }}</p>
            @endif
        </div>

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