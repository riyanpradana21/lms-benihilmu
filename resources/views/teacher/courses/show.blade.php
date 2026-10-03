@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('teacher.courses.index') }}" class="hover:text-indigo-600 transition">Kursus & Materi</a>
                <span>&rsaquo;</span>
                <span class="text-slate-700 font-semibold">{{ $course->title }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $course->title }}</h1>
            <p class="mt-1 text-xs text-slate-500">
                Mata Pelajaran: <strong class="text-indigo-600">{{ $course->subject?->name }} ({{ $course->subject?->code }})</strong> &bull;
                Kelas: <span class="font-semibold text-slate-700">{{ $course->schoolClass?->name }}</span> &bull;
                Semester: <span class="font-semibold text-slate-700">{{ $course->semester?->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>

            <!-- Toggle Publish Status -->
            <form action="{{ route('teacher.courses.toggle-status', $course) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($course->status === 'published')
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2.5 text-xs font-semibold text-emerald-800 hover:bg-emerald-100 transition" title="Klik untuk mengubah menjadi Draf">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Status: Publik (Aktif)</span>
                    </button>
                @else
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-3.5 py-2.5 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition" title="Klik untuk mempublikasikan kursus">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Status: Draf (Klik Publikasikan)</span>
                    </button>
                @endif
            </form>

            <button type="button" onclick="document.getElementById('modal-create-chapter').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Bab Baru</span>
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

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Bab</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $course->chapters->count() }} Bab</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Topik</span>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalTopicsCount }} Topik</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Materi Pembelajaran</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalLessonsCount }} Lesson</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Siswa Terdaftar</span>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $enrolledStudentsCount }} Siswa</p>
        </div>
    </div>

    <!-- Curriculum Syllabus Tree -->
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Struktur Kurikulum dan Silabus ({{ $course->chapters->count() }} Bab)</h2>
            <span class="text-xs text-slate-400">Urutan bab dan materi belajar daring</span>
        </div>

        @forelse($course->chapters as $cIndex => $chapter)
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <!-- Chapter Header -->
                <div class="p-5 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                            {{ $cIndex + 1 }}
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $chapter->title }}</h3>
                            @if($chapter->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $chapter->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center">
                        <button type="button" onclick="openTopicModal({{ $chapter->id }}, '{{ addslashes($chapter->title) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Topik</span>
                        </button>

                        <form action="{{ route('teacher.courses.chapters.destroy', [$course, $chapter]) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Bab ini beserta seluruh topik dan materi di dalamnya?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Bab">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Topics within Chapter -->
                <div class="p-5 space-y-4">
                    @forelse($chapter->topics as $tIndex => $topic)
                        <div class="rounded-xl border border-slate-200/90 bg-white p-4 space-y-3">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-md bg-slate-200 text-slate-700 font-bold text-[11px] flex items-center justify-center">
                                        {{ $cIndex + 1 }}.{{ $tIndex + 1 }}
                                    </span>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $topic->title }}</h4>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openLessonModal({{ $topic->id }}, '{{ addslashes($topic->title) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-semibold transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Tambah Materi</span>
                                    </button>

                                    <form action="{{ route('teacher.courses.topics.destroy', [$course, $chapter, $topic]) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik ini beserta seluruh materinya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-400 hover:text-rose-600 transition" title="Hapus Topik">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Lessons in Topic -->
                            <div class="space-y-2">
                                @forelse($topic->lessons as $lIndex => $lesson)
                                    <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span class="font-medium text-slate-800 truncate">{{ $lesson->title }}</span>
                                            <span class="text-[10px] text-slate-400">&bull; {{ $lesson->estimated_minutes }} Menit</span>
                                            @if($lesson->is_published)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">Publik</span>
                                            @else
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-600 font-semibold">Draf</span>
                                            @endif
                                        </div>

                                        <form action="{{ route('teacher.courses.lessons.destroy', [$course, $topic, $lesson]) }}" method="POST"
                                              onsubmit="return confirm('Hapus materi pembelajaran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus Materi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic py-1">Belum ada materi pembelajaran di topik ini. Klik "Tambah Materi" di atas.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic py-2 text-center">Belum ada topik pada bab ini. Klik tombol "Tambah Topik".</p>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Bab Kurikulum</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Mulai susun materi dengan menambahkan Bab pertama Anda (contoh: Bab 1: Eksponen dan Logaritma).</p>
                <button type="button" onclick="document.getElementById('modal-create-chapter').classList.remove('hidden')"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Bab Pertama</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Create Chapter -->
    <div id="modal-create-chapter" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Tambah Bab Kurikulum Baru</h3>
                <button type="button" onclick="document.getElementById('modal-create-chapter').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.courses.chapters.store', $course) }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Bab <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Bab 1 - Bilangan Berpangkat & Bentuk Akar"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi / Capaian Pembelajaran</label>
                    <textarea name="description" rows="3" placeholder="Ringkasan kompetensi dasar yang akan dicapai pada bab ini..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-chapter').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Bab
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Create Topic (Dynamic) -->
    <div id="modal-create-topic" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Topik Pembahasan</h3>
                    <p id="topic-modal-subtitle" class="text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="document.getElementById('modal-create-topic').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="form-create-topic" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Topik <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Topik 1.1 Sifat-sifat Operasi Eksponen"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Uraian ringkas topik..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-topic').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Topik
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Create Lesson (Dynamic) -->
    <div id="modal-create-lesson" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Materi Pembelajaran</h3>
                    <p id="lesson-modal-subtitle" class="text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="document.getElementById('modal-create-lesson').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="form-create-lesson" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Materi (Lesson) <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Pengenalan Konsep Bilangan Berpangkat Bulat Positif"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Estimasi Membaca (Menit)</label>
                        <input type="number" name="estimated_minutes" value="15" required min="1" max="300"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Publikasi</label>
                        <select name="is_published" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                            <option value="1">Langsung Publikasikan</option>
                            <option value="0">Simpan sebagai Draf</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Isi Materi Pembelajaran <span class="text-rose-500">*</span></label>
                    <textarea name="content" rows="6" required placeholder="Tuliskan teks penjelasan, materi, contoh soal, atau petunjuk belajar bagi siswa..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs font-mono"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-lesson').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTopicModal(chapterId, chapterTitle) {
    document.getElementById('topic-modal-subtitle').innerText = 'Bab: ' + chapterTitle;
    document.getElementById('form-create-topic').action = '/teacher/courses/{{ $course->id }}/chapters/' + chapterId + '/topics';
    document.getElementById('modal-create-topic').classList.remove('hidden');
}

function openLessonModal(topicId, topicTitle) {
    document.getElementById('lesson-modal-subtitle').innerText = 'Topik: ' + topicTitle;
    document.getElementById('form-create-lesson').action = '/teacher/courses/{{ $course->id }}/topics/' + topicId + '/lessons';
    document.getElementById('modal-create-lesson').classList.remove('hidden');
}
</script>
@endsection
