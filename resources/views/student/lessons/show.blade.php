@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('student.courses.show', $course) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Silabus: {{ $course->title }}</span>
        </a>

        <!-- Action Buttons: Mark Complete & Bookmark -->
        <div class="flex items-center gap-2">
            <!-- Bookmark Toggle -->
            <form action="{{ route('student.lessons.bookmark', $lesson) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border {{ $isBookmarked ? 'border-amber-300 bg-amber-50 text-amber-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }} px-3.5 py-2 text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4 {{ $isBookmarked ? 'fill-amber-500 text-amber-500' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    <span>{{ $isBookmarked ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
            </form>

            <!-- Mark Complete Button -->
            <form action="{{ route('student.lessons.complete', $lesson) }}" method="POST">
                @csrf
                @if($progress->completed_at)
                    <button type="button" disabled class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-2 text-xs font-semibold">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Selesai Dipelajari</span>
                    </button>
                @else
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Tandai Selesai</span>
                    </button>
                @endif
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Reader Column (8 cols) -->
        <article class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <header class="pb-6 border-b border-slate-100">
                <div class="flex items-center gap-2 text-xs text-indigo-600 font-semibold mb-2">
                    <span>{{ $lesson->topic?->chapter?->title }}</span>
                    <span>&rsaquo;</span>
                    <span class="text-slate-500">{{ $lesson->topic?->title }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ $lesson->title }}</h1>
                <div class="mt-3 flex items-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Estimasi: {{ $lesson->estimated_minutes ?? 15 }} Menit</span>
                    </span>
                    @if($progress->completed_at)
                        <span class="text-emerald-600 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span>Selesai pada {{ $progress->completed_at->format('d/m/Y H:i') }}</span>
                        </span>
                    @endif
                </div>
            </header>

            <!-- Lesson Content -->
            <div class="prose prose-slate max-w-none text-slate-700 text-sm sm:text-base leading-relaxed space-y-4">
                {!! nl2br(e($lesson->content)) !!}
            </div>

            <!-- Attachments if any -->
            @if($lesson->attachments->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Lampiran & Dokumen Materi</h3>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach($lesson->attachments as $att)
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="font-medium text-slate-700 truncate">{{ $att->title }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $att->file_type ?? 'DOC' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Navigation: Previous / Next -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                @if($prevLesson)
                    <a href="{{ route('student.lessons.show', [$course, $prevLesson]) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span>Materi Sebelumnya</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextLesson)
                    <a href="{{ route('student.lessons.show', [$course, $nextLesson]) }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 text-xs font-semibold shadow-sm transition">
                        <span>Materi Selanjutnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <a href="{{ route('student.courses.show', $course) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-xs font-semibold shadow-sm transition">
                        <span>Kembali ke Silabus</span>
                    </a>
                @endif
            </div>
        </article>

        <!-- Right Column: Sidebar Table of Contents & Student Notes (4 cols) -->
        <aside class="lg:col-span-4 space-y-6">
            <!-- Table of Contents Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Daftar Isi Kursus</h3>
                <div class="space-y-4 max-h-[380px] overflow-y-auto pr-1 text-xs">
                    @foreach($course->chapters as $ch)
                        <div>
                            <div class="font-bold text-slate-800 text-[11px] mb-1.5">{{ $ch->title }}</div>
                            <div class="space-y-1 pl-2 border-l-2 border-slate-100">
                                @foreach($ch->topics as $tp)
                                    @foreach($tp->lessons as $ls)
                                        <a href="{{ route('student.lessons.show', [$course, $ls]) }}" class="block py-1 px-2 rounded-lg transition {{ $ls->id === $lesson->id ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                            {{ $ls->title }}
                                        </a>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Student Private Notes Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Catatan Pribadi Siswa</h3>
                    <span class="text-[10px] text-slate-400">Hanya Anda yang melihat</span>
                </div>

                <form action="{{ route('student.lessons.note', $lesson) }}" method="POST" class="space-y-2">
                    @csrf
                    <textarea name="content" rows="3" required placeholder="Tulis catatan penting dari materi ini..."
                              class="w-full text-xs rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition">
                        Simpan Catatan
                    </button>
                </form>

                @if($notes->isNotEmpty())
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        @foreach($notes as $note)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                                <p class="text-slate-700 whitespace-pre-wrap">{{ $note->content }}</p>
                                <span class="text-[10px] text-slate-400 mt-1 block">{{ $note->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
