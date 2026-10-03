@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('teacher.assignments.index') }}" class="hover:text-indigo-600 transition">Tugas & Penilaian</a>
                <span>&rsaquo;</span>
                <span class="text-slate-700 font-semibold">{{ $assignment->title }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $assignment->title }}</h1>
            <p class="mt-1 text-xs text-slate-500">
                Kursus: <strong class="text-indigo-600">{{ $assignment->course?->title }}</strong> &bull;
                Kelas: <span class="font-semibold text-slate-700">{{ $assignment->course?->schoolClass?->name }}</span> &bull;
                Mata Pelajaran: <span class="font-semibold text-slate-700">{{ $assignment->course?->subject?->name }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.assignments.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Daftar Tugas</span>
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

    <!-- Assignment Meta Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <span class="rounded-md bg-indigo-50 px-2.5 py-1 font-bold text-indigo-700">Maks. {{ $assignment->max_score }} Poin</span>
                <span class="text-slate-500">Batas Waktu: <strong class="{{ $assignment->deadline?->isPast() ? 'text-rose-600' : 'text-slate-800' }}">{{ $assignment->deadline?->format('d M Y, H:i') }}</strong></span>
                @if($assignment->allow_late)
                    <span class="text-[11px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-medium">Boleh Terlambat</span>
                @else
                    <span class="text-[11px] bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full font-medium">Tepat Waktu Saja</span>
                @endif
            </div>

            <div>
                @if($assignment->is_published)
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Publik</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">Draf</span>
                @endif
            </div>
        </div>

        @if($assignment->instructions)
            <div class="pt-3 border-t border-slate-100 text-xs text-slate-700">
                <span class="font-bold text-slate-900 block mb-1">Instruksi Pengerjaan:</span>
                <div class="leading-relaxed text-slate-600">@richContent($assignment->instructions)</div>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Siswa Kelas</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalEnrolled }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Telah Mengumpulkan</span>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $submittedCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sudah Dinilai</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $gradedCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm text-center">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Belum Mengumpulkan</span>
            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $totalEnrolled - $submittedCount }}</p>
        </div>
    </div>

    <!-- Students & Submissions Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Daftar Submisi Siswa ({{ $studentsWithSubmissions->count() }})</h2>
            <span class="text-xs text-slate-400">Status pengerjaan tugas oleh siswa rombel</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 text-[11px] font-bold uppercase text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Siswa</th>
                        <th class="py-3 px-4">Status Submisi</th>
                        <th class="py-3 px-4">Waktu Penyerahan</th>
                        <th class="py-3 px-4">Jawaban / Berkas</th>
                        <th class="py-3 px-4">Nilai</th>
                        <th class="py-3 px-4">Umpan Balik (Feedback)</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($studentsWithSubmissions as $row)
                        @php($student = $row->student)
                        @php($sub = $row->submission)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $student->user?->name }}</div>
                                <div class="text-[11px] text-slate-400">NIS: {{ $student->student_number }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                @if(! $sub)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-600">
                                        Belum Mengumpulkan
                                    </span>
                                @elseif($sub->status === 'graded')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                                        &check; Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700">
                                        Menunggu Penilaian
                                    </span>
                                @endif

                                @if($sub && $sub->is_late)
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 ml-1">
                                        Terlambat
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-[11px] text-slate-500">
                                {{ $sub ? $sub->submitted_at?->format('d M Y, H:i') : '-' }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($sub)
                                    <details class="max-w-xs"><summary class="cursor-pointer font-semibold text-indigo-700">Lihat jawaban</summary><div class="mt-2 max-h-64 overflow-y-auto rounded-lg bg-slate-50 p-3 text-xs leading-relaxed">@richContent($sub->content)</div></details>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                @if($sub && $sub->score !== null)
                                    <span class="font-bold text-sm text-indigo-600">{{ $sub->score }}</span>
                                    <span class="text-[10px] text-slate-400">/ {{ $assignment->max_score }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-[11px] text-slate-500 max-w-[200px] truncate">
                                {{ $sub?->feedback?->feedback ?: '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($sub)
                                    <button type="button"
                                            onclick="openGradeModal({{ $sub->id }}, '{{ addslashes($student->user?->name) }}', {{ $sub->score ?? 0 }}, '{{ addslashes($sub->feedback?->feedback ?? '') }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
                                        <span>{{ $sub->status === 'graded' ? 'Ubah Nilai' : 'Beri Nilai' }}</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs italic">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                Belum ada siswa yang terdaftar di kelas untuk kursus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal View Student Answer -->
    <div id="modal-view-answer" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Jawaban Submisi Tugas</h3>
                    <p id="answer-modal-student" class="text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="document.getElementById('modal-view-answer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs text-slate-800 leading-relaxed whitespace-pre-line max-h-96 overflow-y-auto" id="answer-modal-content">
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="document.getElementById('modal-view-answer').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Grade Submission -->
    <div id="modal-grade-submission" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Penilaian Tugas</h3>
                    <p id="grade-modal-student" class="text-xs text-slate-500"></p>
                </div>
                <button type="button" onclick="document.getElementById('modal-grade-submission').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="form-grade-submission" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nilai / Skor (0 - {{ $assignment->max_score }}) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.5" name="score" id="grade-input-score" required min="0" max="{{ $assignment->max_score }}"
                           class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs text-base font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan / Umpan Balik Guru (Feedback)</label>
                    <textarea name="feedback" id="grade-input-feedback" rows="3" placeholder="Berikan catatan perbaikan atau apresiasi terhadap hasil kerja siswa..."
                              class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-grade-submission').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewAnswerModal(studentName, answerText) {
    document.getElementById('answer-modal-student').innerText = 'Siswa: ' + studentName;
    document.getElementById('answer-modal-content').innerText = answerText;
    document.getElementById('modal-view-answer').classList.remove('hidden');
}

function openGradeModal(submissionId, studentName, score, feedback) {
    document.getElementById('grade-modal-student').innerText = 'Siswa: ' + studentName;
    document.getElementById('grade-input-score').value = score || '';
    document.getElementById('grade-input-feedback').value = feedback || '';
    document.getElementById('form-grade-submission').action = '/teacher/submissions/' + submissionId + '/grade';
    document.getElementById('modal-grade-submission').classList.remove('hidden');
}
</script>
@endsection
