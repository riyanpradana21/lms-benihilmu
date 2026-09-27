@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('student.quizzes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Kuis</span>
        </a>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Hasil Evaluasi Ujian CBT</h1>
        <p class="mt-1 text-xs text-slate-500">Ringkasan perolehan nilai dan evaluasi ujian yang telah diselesaikan.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Score Summary Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ $quiz->course?->subject?->code }}</span>
                <h2 class="mt-2 text-xl font-bold text-slate-900">{{ $quiz->title }}</h2>
                <div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-slate-500">
                    <span>Mulai: {{ $attempt->started_at?->format('d M Y, H:i') }}</span>
                    <span>&bull;</span>
                    <span>Selesai: {{ $attempt->submitted_at?->format('H:i') }}</span>
                    @if($attempt->is_auto_submitted)
                        <span class="text-amber-600 font-semibold">(Otomatis saat waktu habis / batas pelanggaran)</span>
                    @endif
                </div>
            </div>

            <!-- Big Score Badge -->
            <div class="flex flex-col items-center sm:items-end">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Skor Akhir</div>
                <div class="text-4xl sm:text-5xl font-extrabold {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $attempt->score }}
                </div>
                <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $isPassed ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20' }}">
                    {{ $isPassed ? 'LULUS (Di atas KKM ' . ($quiz->passing_score ?: 70) . ')' : 'BELUM LULUS (Di bawah KKM ' . ($quiz->passing_score ?: 70) . ')' }}
                </span>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Soal:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quiz->questions->count() }} Butir</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Jawaban Terisi:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $attempt->answers->whereNotNull('answer_text')->count() }} Butir</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Jawaban Benar:</span>
                <span class="font-bold text-emerald-600 text-sm">{{ $attempt->answers->where('is_correct', true)->count() }} Butir</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Catatan Integritas:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $attempt->violations->count() }} Peringatan</span>
            </div>
        </div>
    </div>

    <!-- Review Questions and Answers -->
    <div class="space-y-4">
        <h3 class="text-base font-bold text-slate-900">Ulasan Jawaban & Pembahasan</h3>

        <div class="space-y-4">
            @php
                $answersByQuestion = $attempt->answers->keyBy('question_id');
            @endphp

            @foreach($quiz->questions as $index => $q)
                @php
                    $ans = $answersByQuestion->get($q->id);
                    $isCorrect = $ans?->is_correct;
                @endphp
                <div class="rounded-2xl border {{ $isCorrect ? 'border-emerald-200 bg-white' : 'border-rose-200 bg-white' }} p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-3 text-xs">
                        <span class="font-bold text-slate-700">Soal Nomor {{ $index + 1 }}</span>
                        @if($isCorrect)
                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-0.5 font-bold text-emerald-700">
                                Benar (+{{ $ans->score_earned ?? $q->score }} Poin)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2.5 py-0.5 font-bold text-rose-700">
                                Salah (0 Poin)
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-800 leading-relaxed font-medium mb-4">{!! nl2br(e($q->question_text)) !!}</p>

                    @if($q->options->isNotEmpty())
                        <div class="space-y-2 text-xs">
                            @foreach($q->options as $opt)
                                @php
                                    $isSelected = ($ans?->answer_text == $opt->id || $ans?->answer_text == $opt->option_text);
                                @endphp
                                <div class="p-3 rounded-xl border flex items-center justify-between {{ $opt->is_correct ? 'border-emerald-300 bg-emerald-50/50' : ($isSelected ? 'border-rose-300 bg-rose-50/50' : 'border-slate-100 bg-slate-50/50') }}">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold {{ $opt->is_correct ? 'text-emerald-700' : 'text-slate-600' }}">{{ $opt->option_text }}</span>
                                    </div>
                                    @if($isSelected && $opt->is_correct)
                                        <span class="text-[10px] font-bold text-emerald-700 uppercase">Jawaban Anda &bull; Kunci Benar</span>
                                    @elseif($isSelected && !$opt->is_correct)
                                        <span class="text-[10px] font-bold text-rose-700 uppercase">Jawaban Anda</span>
                                    @elseif($opt->is_correct)
                                        <span class="text-[10px] font-bold text-emerald-700 uppercase">Kunci Jawaban</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs p-3 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-bold text-slate-500 block">Jawaban Anda:</span>
                            <p class="text-slate-800 mt-1">{{ $ans?->answer_text ?: '(Tidak menjawab)' }}</p>
                            @if($q->correct_answer)
                                <span class="font-bold text-emerald-600 block mt-2">Kunci Jawaban Guru:</span>
                                <p class="text-emerald-800">{{ $q->correct_answer }}</p>
                            @endif
                        </div>
                    @endif

                    @if($q->explanation)
                        <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600">
                            <span class="font-bold text-indigo-600">Pembahasan:</span>
                            <p class="mt-1 leading-relaxed">{{ $q->explanation }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
