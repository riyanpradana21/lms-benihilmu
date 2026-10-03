@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('teacher.question-banks.index') }}" class="hover:text-indigo-600 transition">Bank Soal</a>
                <span>&rsaquo;</span>
                <span class="text-slate-700 font-semibold">{{ $questionBank->name }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $questionBank->name }}</h1>
            <p class="mt-1 text-xs text-slate-500">
                Mata Pelajaran: <strong class="text-indigo-600">{{ $questionBank->subject?->name ?: 'Lintas Mapel' }}</strong> &bull;
                Dibuat oleh: <span class="text-slate-700 font-medium">{{ $questionBank->creator?->name ?: 'Guru' }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.question-banks.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>

            <button type="button" onclick="document.getElementById('modal-create-question').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Butir Soal</span>
            </button>
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

    <!-- Bank Summary Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Soal</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $questionBank->questions->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Poin</span>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalScore }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilihan Ganda</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $mcCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Benar / Salah</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $tfCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Isian & Esai</span>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $shortCount + $essayCount }}</p>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Daftar Pertanyaan ({{ $questionBank->questions->count() }})</h2>
            <span class="text-xs text-slate-400">Urutan butir soal dalam bank</span>
        </div>

        @forelse($questionBank->questions as $index => $q)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-900 text-white text-xs font-bold">
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

                    <form action="{{ route('teacher.question-banks.questions.destroy', [$questionBank, $q]) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal #{{ $index + 1 }} ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Soal">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Question Text -->
                <div class="mt-3 text-sm text-slate-800 font-medium leading-relaxed whitespace-pre-line">
                    @richContent($q->question_text)
                </div>

                <details class="mt-3">
                    <summary class="cursor-pointer text-xs font-semibold text-indigo-700">Edit butir soal</summary>
                    <form method="POST" enctype="multipart/form-data" action="{{ route("teacher.question-banks.questions.update", [$questionBank, $q]) }}" class="mt-3 space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        @csrf @method("PATCH")
                        <textarea name="question_text" rows="4" required class="w-full rounded-lg border-slate-300 text-sm">{{ $q->question_text }}</textarea>
                        <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="block w-full text-xs text-slate-600"><p class="text-[11px] text-slate-500">Gambar atau video opsional, maksimal 20 MB.</p>
                        <textarea name="explanation" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Pembahasan (opsional)">{{ $q->explanation }}</textarea>
                        <div class="flex flex-wrap items-center gap-3"><input type="number" name="score" value="{{ $q->score }}" min="1" max="100" required class="w-28 rounded-lg border-slate-300 text-sm"><button class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white">Simpan perubahan</button></div>
                    </form>
                </details>

                <!-- Question Options (if applicable) -->
                @if($q->options->isNotEmpty())
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach($q->options as $optIdx => $option)
                            @php($label = chr(65 + $optIdx))
                            <div class="flex items-center gap-2.5 p-2.5 rounded-xl border text-xs {{ $option->is_correct ? 'border-emerald-300 bg-emerald-50/60 font-semibold text-emerald-900' : 'border-slate-200 bg-slate-50 text-slate-700' }}">
                                <span class="w-5 h-5 flex-shrink-0 rounded-full flex items-center justify-center font-bold text-[11px] {{ $option->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $label }}
                                </span>
                                <span class="flex-1">{{ $option->option_text }}</span>
                                @if($option->is_correct)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Kunci
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif($q->correct_answer)
                    <div class="mt-3 rounded-xl bg-teal-50 border border-teal-200 p-3 text-xs text-teal-900">
                        <strong>Kunci Jawaban:</strong> {{ $q->correct_answer }}
                    </div>
                @endif

                <!-- Explanation (if present) -->
                @if($q->explanation)
                    <div class="mt-3 rounded-xl bg-slate-50 border border-slate-200/80 p-3 text-xs text-slate-600">
                        <div class="font-bold text-slate-700 mb-0.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pembahasan Soal:</span>
                        </div>
                        <p class="leading-relaxed">{{ $q->explanation }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Butir Soal</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Klik tombol "Tambah Butir Soal" di atas untuk menambahkan pertanyaan pilihan ganda, benar/salah, atau esai.</p>
                <button type="button" onclick="document.getElementById('modal-create-question').classList.remove('hidden')"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Butir Soal</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Create Question -->
    <div id="modal-create-question" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Butir Soal Baru</h3>
                    <p class="text-xs text-slate-500">Bank: {{ $questionBank->name }}</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-create-question').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.question-banks.questions.store', $questionBank) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tipe Pertanyaan <span class="text-rose-500">*</span></label>
                        <select name="type" id="question_type_select" onchange="toggleQuestionType(this.value)"
                                class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                            <option value="multiple_choice">Pilihan Ganda (A, B, C, D, E)</option>
                            <option value="true_false">Benar / Salah (True / False)</option>
                            <option value="short_answer">Isian Singkat</option>
                            <option value="essay">Esai / Uraian Bebas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bobot Poin Soal <span class="text-rose-500">*</span></label>
                        <input type="number" name="score" value="10" required min="1" max="100"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Teks Pertanyaan / Soal <span class="text-rose-500">*</span></label>
                    <textarea name="question_text" rows="3" required placeholder="Tuliskan pertanyaan dengan jelas..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                    <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="mt-2 block w-full text-xs text-slate-600"><p class="mt-1 text-[11px] text-slate-500">Lampirkan gambar atau video langsung ke soal (maksimal 20 MB).</p>
                </div>

                <!-- Multiple Choice Options Section -->
                <div id="section_mc" class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <label class="font-semibold text-slate-800">Opsi Jawaban & Kunci Benar</label>
                        <span class="text-[11px] text-slate-500">Pilih radio button di samping opsi yang benar</span>
                    </div>

                    <div class="space-y-2">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $idx => $letter)
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-slate-700 text-xs w-8">
                                    <input type="radio" name="correct_option" value="{{ $idx }}" {{ $idx === 0 ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <span>{{ $letter }}.</span>
                                </label>
                                <input type="text" name="options[]" placeholder="Opsi jawaban {{ $letter }}"
                                       class="flex-1 rounded-xl border border-slate-300 px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 bg-white">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- True / False Section -->
                <div id="section_tf" class="hidden space-y-3 bg-amber-50/50 p-4 rounded-xl border border-amber-200">
                    <label class="block font-semibold text-slate-800">Pilih Jawaban yang Benar</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 font-medium">
                            <input type="radio" name="true_false_answer" value="true" checked class="text-indigo-600 focus:ring-indigo-500">
                            <span>Benar (True)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700 font-medium">
                            <input type="radio" name="true_false_answer" value="false" class="text-indigo-600 focus:ring-indigo-500">
                            <span>Salah (False)</span>
                        </label>
                    </div>
                </div>

                <!-- Short Answer Section -->
                <div id="section_short" class="hidden space-y-2 bg-teal-50/50 p-4 rounded-xl border border-teal-200">
                    <label class="block font-semibold text-slate-800">Kunci Jawaban Singkat</label>
                    <input type="text" name="correct_answer" placeholder="Tuliskan kata atau angka kunci jawaban persis..."
                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500 bg-white">
                    <p class="text-[11px] text-slate-500">Siswa akan dinilai otomatis berdasarkan kecocokan kata kunci ini (tidak membedakan huruf besar/kecil).</p>
                </div>

                <!-- Essay Notice -->
                <div id="section_essay" class="hidden space-y-2 bg-purple-50/50 p-4 rounded-xl border border-purple-200">
                    <label class="block font-semibold text-slate-800">Petunjuk Jawaban Esai</label>
                    <p class="text-[11px] text-slate-600">Jawaban esai bersifat terbuka dan dinilai secara manual oleh guru melalui menu periksa jawaban kuis.</p>
                </div>

                <!-- Explanation Field -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pembahasan / Catatan Penjelasan (Opsional)</label>
                    <textarea name="explanation" rows="2" placeholder="Tuliskan langkah pengerjaan atau referensi buku untuk dipelajari siswa..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-question').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Butir Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleQuestionType(type) {
    document.getElementById('section_mc').classList.add('hidden');
    document.getElementById('section_tf').classList.add('hidden');
    document.getElementById('section_short').classList.add('hidden');
    document.getElementById('section_essay').classList.add('hidden');

    if (type === 'multiple_choice') {
        document.getElementById('section_mc').classList.remove('hidden');
    } else if (type === 'true_false') {
        document.getElementById('section_tf').classList.remove('hidden');
    } else if (type === 'short_answer') {
        document.getElementById('section_short').classList.remove('hidden');
    } else if (type === 'essay') {
        document.getElementById('section_essay').classList.remove('hidden');
    }
}
</script>
@endsection
