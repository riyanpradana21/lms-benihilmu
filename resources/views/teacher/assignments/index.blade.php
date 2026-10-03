@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Manajemen Asesmen Siswa</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tugas dan Penilaian Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Buat penugasan terstruktur untuk kelas, periksa hasil pekerjaan siswa, dan berikan penilaian serta umpan balik (feedback).</p>
        </div>

        <button type="button" onclick="document.getElementById('modal-create-assignment').classList.remove('hidden')"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Tugas Baru</span>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Tugas Aktif</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalAssignments }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Submisi Terkumpul</p>
                    <p class="text-xl font-bold text-slate-900">{{ $totalSubmissions }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Menunggu Diperiksa</p>
                    <p class="text-xl font-bold text-amber-600">{{ $pendingGrading }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Grid -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($assignments as $assignment)
            @php($enrolled = $assignment->course?->schoolClass?->students()->count() ?: 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">
                            {{ $assignment->course?->subject?->code }} &bull; {{ $assignment->course?->schoolClass?->name }}
                        </span>

                        @if($assignment->is_published)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Publik</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">Draf</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $assignment->title }}</h3>
                    <p class="mt-1.5 text-xs text-slate-500 line-clamp-2">{{ $assignment->instructions ?: 'Tidak ada instruksi khusus.' }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Batas Waktu:</span>
                            <span class="font-semibold text-slate-700 {{ $assignment->deadline?->isPast() ? 'text-rose-600' : '' }}">
                                {{ $assignment->deadline?->format('d M Y, H:i') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Nilai Maksimum:</span>
                            <span class="font-bold text-indigo-600">{{ $assignment->max_score }} Poin</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-600">
                            <span class="text-slate-400">Pengumpulan Siswa:</span>
                            <span class="font-bold text-slate-800">{{ $assignment->submissions_count }} / {{ $enrolled }} Siswa</span>
                        </div>

                        <!-- Progress Bar -->
                        @php($percentage = $enrolled > 0 ? min(100, round(($assignment->submissions_count / $enrolled) * 100)) : 0)
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <a href="{{ route('teacher.assignments.show', $assignment) }}"
                       class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <span>Periksa Submisi ({{ $assignment->submissions_count }})</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 transition" title="Hapus Tugas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Tugas Dibuat</h3>
                <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">Klik tombol "Buat Tugas Baru" untuk menerbitkan penugasan dan latihan mandiri bagi siswa.</p>
                <button type="button" onclick="document.getElementById('modal-create-assignment').classList.remove('hidden')"
                        class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Tugas Baru Sekarang</span>
                </button>
            </div>
        @endforelse
    </div>

    <div>{{ $assignments->links() }}</div>

    <!-- Modal Create Assignment -->
    <div id="modal-create-assignment" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Buat Penugasan Baru</h3>
                <button type="button" onclick="document.getElementById('modal-create-assignment').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Kursus Kelas <span class="text-rose-500">*</span></label>
                    <select name="course_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->schoolClass?->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Tugas <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Latihan 1 - Pemecahan Masalah Eksponen"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Petunjuk & Instruksi Pengerjaan</label>
                    <textarea name="instructions" rows="3" placeholder="Tuliskan petunjuk pengerjaan, format berkas, atau kriteria penilaian..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                    <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="mt-2 block w-full text-xs text-slate-600"><p class="mt-1 text-[11px] text-slate-500">Media akan tampil langsung pada petunjuk tugas (maksimal 20 MB).</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Batas Waktu (Deadline) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="deadline" required value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nilai Maksimal</label>
                        <input type="number" name="max_score" value="100" required min="10" max="1000"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Toleransi Keterlambatan</label>
                        <select name="allow_late" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                            <option value="1">Izinkan Pengumpulan Terlambat</option>
                            <option value="0">Tolak Jika Melewati Batas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Publikasi</label>
                        <select name="is_published" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                            <option value="1">Langsung Publikasikan</option>
                            <option value="0">Simpan sebagai Draf</option>
                        </select>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-assignment').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
