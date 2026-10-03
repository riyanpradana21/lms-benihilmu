@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Pusat Asesmen & Evaluasi</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Bank Soal Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola kumpulan bank soal, butir soal pilihan ganda, benar/salah, isian singkat, dan esai untuk kuis CBT.</p>
        </div>

        <button type="button" onclick="document.getElementById('modal-create-bank').classList.remove('hidden')"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Bank Soal Baru</span>
        </button>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Bank Soal Saya</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalBanks }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Butir Soal Tersimpan</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalQuestions }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-purple-50 p-3 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Mata Pelajaran Aktif</p>
                    <p class="text-xl font-bold text-slate-900">{{ $subjects->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('teacher.question-banks.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama bank soal atau deskripsi..."
                       class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 text-xs focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="w-full sm:w-56">
                <select name="subject_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 py-2 text-xs focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                            {{ $subj->code }} - {{ $subj->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'subject_id']))
                <a href="{{ route('teacher.question-banks.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Question Banks Grid -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($questionBanks as $bank)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">
                            {{ $bank->subject?->code ?: 'UMUM' }} &bull; {{ $bank->subject?->name ?: 'Lintas Mapel' }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                            {{ $bank->questions_count }} Soal
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $bank->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                        {{ $bank->description ?: 'Tidak ada deskripsi tambahan.' }}
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Pembuat: <strong>{{ $bank->creator?->name ?: 'Guru' }}</strong></span>
                        <span>{{ $bank->created_at?->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <a href="{{ route('teacher.question-banks.show', $bank) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <span>Kelola Soal ({{ $bank->questions_count }})</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    @if($bank->created_by === auth()->id())
                        <form action="{{ route('teacher.question-banks.destroy', $bank) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus bank soal ini beserta seluruh soal di dalamnya?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition" title="Hapus Bank Soal">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Bank Soal</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Mulai dengan membuat bank soal pertama Anda untuk menampung butir-butir pertanyaan CBT.</p>
                <button type="button" onclick="document.getElementById('modal-create-bank').classList.remove('hidden')"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Bank Soal Sekarang</span>
                </button>
            </div>
        @endforelse
    </div>

    <div>{{ $questionBanks->links() }}</div>

    <!-- Modal Create Question Bank -->
    <div id="modal-create-bank" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Buat Bank Soal Baru</h3>
                <button type="button" onclick="document.getElementById('modal-create-bank').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.question-banks.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Pelajaran</label>
                    <select name="subject_id" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        <option value="">-- Pilih Mata Pelajaran (Opsional) --</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->code }} - {{ $subj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama / Judul Bank Soal <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Bank Soal Matematika Kelas X - Semester 1"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi / Catatan Lingkup Materi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi materi atau standar kompetensi dasar..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-bank').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Bank Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
