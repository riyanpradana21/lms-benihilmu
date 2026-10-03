@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Evaluasi Pembelajaran</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kuis & Ujian CBT</h1>
            <p class="mt-1 text-xs text-slate-500">Daftar evaluasi online berbasis komputer (CBT) yang tersedia dari kelas Anda.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($quizzes as $quiz)
            @php
                $quizAttempts = $attempts->get($quiz->id, collect());
                $completedAttempts = $quizAttempts->whereIn('status', ['submitted', 'graded']);
                $activeAttempt = $quizAttempts->firstWhere('status', 'in_progress');
                $bestScore = $completedAttempts->max('score');
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $quiz->course?->subject?->code }}</span>
                        @if($activeAttempt)
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 animate-pulse">
                                Berjalan
                            </span>
                        @elseif($completedAttempts->isNotEmpty())
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                                Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                                Tersedia
                            </span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $quiz->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $quiz->description ?: 'Evaluasi komprehensif materi kursus.' }}</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Durasi:</span>
                            <span class="font-semibold text-slate-700">{{ $quiz->duration_minutes }} Menit</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Jumlah Soal:</span>
                            <span class="font-semibold text-slate-700">{{ $quiz->questions_count }} Butir</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Batas Nilai:</span>
                            <span class="font-semibold text-slate-700">{{ $quiz->passing_score ?: 70 }}/100</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Nilai Terbaik:</span>
                            <span class="font-bold text-indigo-600">{{ $bestScore !== null ? $bestScore : '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('student.quizzes.show', $quiz) }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        @if($activeAttempt)
                            Lanjutkan Ujian &rarr;
                        @else
                            Detail & Mulai Ujian &rarr;
                        @endif
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Belum Ada Kuis atau Ujian</h3>
                <p class="mt-1 text-xs text-slate-500">Evaluasi yang dipublikasikan oleh guru akan tampil di halaman ini.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $quizzes->links() }}</div>
</div>
@endsection
