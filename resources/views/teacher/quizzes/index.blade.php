@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Manajemen Evaluasi</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kuis & Ujian CBT Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola kuis daring terstruktur dengan server-authoritative timer dan pemantauan integritas ujian.</p>
        </div>

        <button onclick="document.getElementById('modal-create-quiz').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Kuis Baru</span>
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($quizzes as $quiz)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $quiz->course?->subject?->code }} &bull; {{ $quiz->course?->schoolClass?->name }}</span>
                        @if($quiz->is_published)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Publik</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">Draf</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $quiz->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $quiz->description ?: 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Durasi:</span>
                            <span class="font-semibold text-slate-700">{{ $quiz->duration_minutes }} Menit</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Soal:</span>
                            <span class="font-semibold text-slate-700">{{ $quiz->questions_count }} Butir</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">KKM:</span>
                            <span class="font-semibold text-indigo-600">{{ $quiz->passing_score }} Poin</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Peserta Mengerjakan:</span>
                            <span class="font-bold text-slate-800">{{ $quiz->attempts_count }} Siswa</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <a href="{{ route('teacher.quizzes.questions', $quiz) }}" class="inline-flex items-center justify-center py-2 px-3 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <span>Kelola Soal ({{ $quiz->questions_count }})</span>
                    </a>
                    <a href="{{ route('teacher.quizzes.attempts', $quiz) }}" class="inline-flex items-center justify-center py-2 px-3 text-xs font-semibold rounded-xl bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition">
                        <span>Hasil ({{ $quiz->attempts_count }}) &rarr;</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Belum Ada Kuis</h3>
                <p class="mt-1 text-xs text-slate-500">Klik tombol "Buat Kuis Baru" untuk membuat evaluasi CBT pertama Anda.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $quizzes->links() }}</div>

    <!-- Modal Create Quiz -->
    <div id="modal-create-quiz" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Buat Kuis & Evaluasi Baru</h3>
                <button type="button" onclick="document.getElementById('modal-create-quiz').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.quizzes.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Kursus Kelas</label>
                    <select name="course_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->schoolClass?->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Kuis</label>
                    <input type="text" name="title" required placeholder="Contoh: Kuis Harian 1 - Eksponen"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi / Petunjuk</label>
                    <textarea name="description" rows="2" placeholder="Petunjuk khusus untuk siswa..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" value="60" required min="5" max="300"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">KKM Kelulusan</label>
                        <input type="number" name="passing_score" value="70" required min="0" max="100"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Batas Percobaan (0=unlimited)</label>
                        <input type="number" name="attempt_limit" value="1" required min="0" max="10"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Batas Pelanggaran Tab</label>
                        <input type="number" name="violation_threshold" value="5" required min="1" max="20"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Publikasi</label>
                    <select name="is_published" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="1">Langsung Publikasikan ke Siswa</option>
                        <option value="0">Simpan sebagai Draf</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-quiz').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm">
                        Simpan Kuis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
