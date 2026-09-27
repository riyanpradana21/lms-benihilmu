@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Back & Header -->
    <div>
        <a href="{{ route('student.courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Kursus</span>
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $course->subject?->code }}</span>
                    <span class="text-xs text-slate-400">&bull; {{ $course->schoolClass?->name }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $course->title }}</h1>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed max-w-2xl">{{ $course->description ?: 'Materi silabus akademik terstruktur.' }}</p>
            </div>
            <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-sm">
                    {{ substr($course->teacher?->user?->name ?? 'G', 0, 1) }}
                </div>
                <div class="text-xs">
                    <div class="text-slate-400">Guru Pengampu:</div>
                    <div class="font-bold text-slate-800">{{ $course->teacher?->user?->name ?? 'Belum ditentukan' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between text-xs mb-2">
            <span class="font-semibold text-slate-700">Progres Pembelajaran Anda</span>
            <span class="font-bold text-indigo-600">{{ $progressPercentage }}% Selesai ({{ $completedLessons }}/{{ $totalLessons }} Materi)</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
        </div>
    </div>

    <!-- Syllabus Chapters & Topics & Lessons -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Silabus & Materi Pembelajaran</h2>

        @forelse($course->chapters as $chapter)
            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                <div class="bg-slate-50/80 px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Bab {{ $chapter->order }}</div>
                        <h3 class="text-base font-bold text-slate-900">{{ $chapter->title }}</h3>
                        @if($chapter->description)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $chapter->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    @forelse($chapter->topics as $topic)
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                <span>{{ $topic->title }}</span>
                            </h4>

                            <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                                @forelse($topic->lessons as $lesson)
                                    @php
                                        $isComplete = $lesson->progress->first()?->completed_at !== null;
                                    @endphp
                                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                                        <div class="flex items-center gap-3">
                                            @if($isComplete)
                                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                </div>
                                            @else
                                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('student.lessons.show', [$course, $lesson]) }}" class="text-sm font-semibold text-slate-800 hover:text-indigo-600 transition">
                                                    {{ $lesson->title }}
                                                </a>
                                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                                    <span>{{ $lesson->estimated_minutes ? $lesson->estimated_minutes . ' menit membaca' : 'Materi teks & dokumen' }}</span>
                                                    @if($isComplete)
                                                        <span class="text-emerald-600 font-semibold">&bull; Selesai</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('student.lessons.show', [$course, $lesson]) }}" class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                            <span>Buka Materi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-slate-400">Belum ada materi dalam topik ini.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada topik dalam bab ini.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Belum ada bab atau materi</h3>
                <p class="mt-1 text-xs text-slate-500">Guru pengampu belum mempublikasikan materi untuk kursus ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
