@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Manajemen Pembelajaran LMS</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kursus dan Materi Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola kurikulum kursus yang ditugaskan kepada Anda, susun bab, topik pembahasan, dan materi digital.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Kursus Diampu</p>
                    <p class="text-xl font-bold text-slate-900">{{ $courses->total() }} Kursus</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Bab Tersusun</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalChapters }} Bab</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-purple-50 p-3 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Materi (Lessons)</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalLessons }} Materi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($courses as $course)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">
                            {{ $course->subject?->code }} &bull; {{ $course->schoolClass?->name }}
                        </span>

                        @if($course->status === 'published')
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Publik</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">Draf</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $course->title }}</h3>
                    <p class="mt-1.5 text-xs text-slate-500 line-clamp-2">{{ $course->description ?: 'Tidak ada deskripsi kursus.' }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-slate-50 rounded-xl p-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Bab</span>
                            <span class="font-bold text-slate-800">{{ $course->chapters_count }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Tugas</span>
                            <span class="font-bold text-slate-800">{{ $course->assignments_count }}</span>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Kuis CBT</span>
                            <span class="font-bold text-slate-800">{{ $course->quizzes_count }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100">
                    <a href="{{ route('teacher.courses.show', $course) }}"
                       class="inline-flex items-center justify-center w-full gap-2 py-2.5 px-4 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <span>Kelola Kurikulum & Materi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Kursus</h3>
                <p class="mt-1 text-xs text-slate-500">Kursus yang diberikan oleh administrator akademik akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $courses->links() }}</div>
</div>
@endsection
