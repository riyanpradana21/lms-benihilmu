@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('teacher.quizzes.index') }}" class="hover:text-indigo-600 transition">Kuis CBT</a>
                <span>&rsaquo;</span>
                <span class="text-slate-700 font-semibold">{{ $quiz->title }}</span>
                <span>&rsaquo;</span>
                <span>Kelola Butir Soal</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $quiz->title }}</h1>
            <p class="mt-1 text-xs text-slate-500">
                Kursus: <strong class="text-indigo-600">{{ $quiz->course?->title }}</strong> &bull;
                Kelas: <span class="font-semibold text-slate-700">{{ $quiz->course?->schoolClass?->name }}</span> &bull;
                Mapel: <span class="font-semibold text-slate-700">{{ $quiz->course?->subject?->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.quizzes.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar Kuis</span>
            </a>

            <a href="{{ route('teacher.quizzes.attempts', $quiz) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Hasil Siswa ({{ $quiz->attempts()->count() }})</span>
            </a>
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

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Soal</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $quiz->questions->count() }} Butir</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Akumulasi Poin</span>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalScore }} Poin</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Durasi Pengerjaan</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $quiz->duration_minutes }} Menit</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">KKM Kelulusan</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $quiz->passing_score }} Poin</p>
        </div>
    </div>

    <!-- 2 Column Layout: Left = Current Quiz Questions, Right = Question Bank Importer -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Current Questions in Quiz (7 cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Soal Aktif di Kuis Ini ({{ $quiz->questions->count() }})</h2>
                    <p class="text-xs text-slate-500">Soal-soal ini akan dikerjakan oleh siswa pada ujian CBT.</p>
                </div>
            </div>

            @forelse($quiz->questions as $index => $q)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold">
                                {{ $index + 1 }}
                            </span>

                            @if($q->type === 'multiple_choice')
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">Pilihan Ganda</span>
                            @elseif($q->type === 'true_false')
                                <span class="rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">Benar / Salah</span>
                            @elseif($q->type === 'short_answer')
                                <span class="rounded-md bg-teal-50 px-2 py-0.5 text-xs font-semibold text-teal-700">Isian Singkat</span>
                            @else
                                <span class="rounded-md bg-purple-50 px-2 py-0.5 text-xs font-semibold text-purple-700">Esai</span>
                            @endif

                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                {{ $q->score }} Poin
                            </span>
                        </div>

                        <form action="{{ route('teacher.quizzes.questions.detach', [$quiz, $q]) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin melepas butir soal ini dari kuis? Soal tetap tersimpan di Bank Soal.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition" title="Lepas dari Kuis">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-3 text-xs text-slate-800 font-medium leading-relaxed whitespace-pre-line">
                        @richContent($q->question_text)
                    </div>

                    @if($q->options->isNotEmpty())
                        <div class="mt-3 grid gap-1.5 sm:grid-cols-2">
                            @foreach($q->options as $optIdx => $option)
                                @php($label = chr(65 + $optIdx))
                                <div class="flex items-center gap-2 p-2 rounded-lg text-[11px] {{ $option->is_correct ? 'bg-emerald-50 text-emerald-900 font-semibold border border-emerald-200' : 'bg-slate-50 text-slate-600' }}">
                                    <span class="w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px] {{ $option->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $label }}
                                    </span>
                                    <span class="truncate">{{ $option->option_text }}</span>
                                    @if($option->is_correct)
                                        <span class="text-emerald-600 ml-auto font-bold">&check;</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($q->correct_answer)
                        <div class="mt-2.5 rounded-lg bg-teal-50 border border-teal-200 p-2 text-[11px] text-teal-900">
                            <strong>Kunci:</strong> {{ $q->correct_answer }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <div class="mx-auto w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Belum Ada Soal di Kuis Ini</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Kuis CBT tidak dapat dikerjakan siswa sebelum soal dimasukkan. Gunakan formulir di sebelah kanan untuk memilih soal dari Bank Soal.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Right: Import from Question Bank Panel (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4 sticky top-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Impor dari Bank Soal</h2>
                        <p class="text-[11px] text-slate-500">Pilih soal yang relevan untuk dimasukkan ke kuis.</p>
                    </div>

                    <a href="{{ route('teacher.question-banks.index') }}" target="_blank"
                       class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">
                        <span>Buka Bank Soal</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <form action="{{ route('teacher.quizzes.questions.attach', $quiz) }}" method="POST" class="space-y-4">
                    @csrf

                    @php($allAvailableQuestions = $questionBanks->flatMap->questions)

                    @if($allAvailableQuestions->isNotEmpty())
                        <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl text-xs">
                            <label class="inline-flex items-center gap-2 cursor-pointer font-semibold text-slate-700">
                                <input type="checkbox" id="check-all-questions" onchange="toggleSelectAll(this)" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span>Pilih Semua Soal ({{ $allAvailableQuestions->count() }})</span>
                            </label>
                            <span class="text-[11px] text-slate-400">Tersedia untuk dimasukkan</span>
                        </div>

                        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                            @foreach($questionBanks as $bank)
                                @if($bank->questions->isNotEmpty())
                                    <div class="border border-slate-100 rounded-xl p-3 bg-slate-50/50">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-slate-800">{{ $bank->name }}</span>
                                            <span class="text-[10px] bg-slate-200/80 px-2 py-0.5 rounded-full text-slate-700 font-medium">
                                                {{ $bank->questions->count() }} Soal
                                            </span>
                                        </div>

                                        <div class="space-y-2">
                                            @foreach($bank->questions as $availQ)
                                                <label class="flex items-start gap-2.5 p-2 rounded-lg bg-white border border-slate-200/80 hover:border-indigo-300 cursor-pointer text-xs transition">
                                                    <input type="checkbox" name="question_ids[]" value="{{ $availQ->id }}"
                                                           class="question-checkbox mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">
                                                                {{ ucfirst(str_replace('_', ' ', $availQ->type)) }}
                                                            </span>
                                                            <span class="text-[10px] font-semibold text-indigo-600">{{ $availQ->score }} Poin</span>
                                                        </div>
                                                        <p class="text-slate-800 text-[11px] leading-tight line-clamp-2">
                                                            @richContent($availQ->question_text)
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambahkan Soal Terpilih ke Kuis</span>
                        </button>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center">
                            <p class="text-xs text-slate-600 font-medium">Tidak ada butir soal baru yang tersedia di bank soal.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Semua butir soal sudah ada di kuis ini atau Anda belum menambahkan soal ke bank soal.</p>
                            <a href="{{ route('teacher.question-banks.index') }}"
                               class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                <span>Buat butir soal baru di Bank Soal &rarr;</span>
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
}
</script>
@endsection
