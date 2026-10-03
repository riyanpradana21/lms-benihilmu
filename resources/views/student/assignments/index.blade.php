@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">LMS Siswa</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tugas & Asesmen Mandiri</h1>
            <p class="mt-1 text-xs text-slate-500">Kumpulan tugas dan latihan dari mata pelajaran kelas Anda. Kumpulkan jawaban tepat waktu sebelum batas akhir.</p>
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

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse($assignments as $assignment)
            @php($submission = $assignment->submissions->first())
            @php($isPastDeadline = $assignment->deadline?->isPast())
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <span class="rounded-md bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700">
                                {{ $assignment->course?->subject?->code }} &bull; {{ $assignment->course?->subject?->name }}
                            </span>
                            <h3 class="mt-2 text-base font-bold text-slate-900">{{ $assignment->title }}</h3>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $assignment->max_score }} Poin
                        </span>
                    </div>

                    <p class="mt-2 text-xs leading-relaxed text-slate-600">
                        @if($assignment->instructions)
                            @richContent($assignment->instructions)
                        @else
                            Tidak ada instruksi khusus dari guru.
                        @endif
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Batas: <strong class="{{ $isPastDeadline ? 'text-rose-600' : 'text-slate-700' }}">{{ $assignment->deadline?->format('d M Y, H:i') }}</strong></span>
                        </div>

                        <div>
                            @if(! $submission)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-600">
                                    Belum Dikumpulkan
                                </span>
                            @elseif($submission->status === 'graded')
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                                    &check; Sudah Dinilai
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700">
                                    Menunggu Dinilai
                                </span>
                            @endif

                            @if($submission && $submission->is_late)
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 ml-1">
                                    Terlambat
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submission / Grade Section -->
                <div class="mt-5 pt-4 border-t border-slate-100">
                    @if($submission && $submission->status === 'graded')
                        <!-- Graded Result Box -->
                        <div class="rounded-xl bg-emerald-50/70 border border-emerald-200 p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-emerald-900">Nilai Akhir:</span>
                                <span class="text-xl font-bold text-emerald-700">{{ $submission->score }} <span class="text-xs text-emerald-600 font-normal">/ {{ $assignment->max_score }}</span></span>
                            </div>

                            @if($submission->feedback?->feedback)
                                <div class="pt-2 border-t border-emerald-200/60 text-xs text-emerald-950">
                                    <span class="font-bold block text-[11px] uppercase tracking-wider text-emerald-800 mb-0.5">Catatan & Umpan Balik Guru:</span>
                                    <p class="italic leading-relaxed">"{{ $submission->feedback->feedback }}"</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Submission Form -->
                        @if($isPastDeadline && ! $assignment->allow_late)
                            <div class="rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-800">
                                Batas waktu pengumpulan telah berakhir. Tugas ini tidak menerima pengumpulan susulan.
                            </div>
                        @else
                            <form method="POST" enctype="multipart/form-data" action="{{ route('student.assignments.submit', $assignment) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        {{ $submission ? 'Perbarui Jawaban / Tautan:' : 'Jawaban Anda / Tautan Tugas:' }}
                                    </label>
                                    <textarea name="content" rows="3" placeholder="Tuliskan jawaban, tautan, atau tempel kode dengan format ```php ... ``` ..."
                                              class="w-full rounded-xl border-slate-300 text-xs p-2.5 focus:ring-2 focus:ring-indigo-500">{{ $submission?->content }}</textarea>
                                    <input type="file" name="media" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="mt-2 block w-full text-xs text-slate-600"><p class="mt-1 text-[11px] text-slate-500">Bisa kirim teks, gambar, atau video langsung (maksimal 20 MB).</p>
                                </div>

                                <button type="submit" class="inline-flex items-center justify-center w-full py-2 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
                                    <span>{{ $submission ? 'Kirim Ulang Jawaban' : 'Kumpulkan Tugas Sekarang' }}</span>
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Belum Ada Tugas</h3>
                <p class="mt-1 text-xs text-slate-500">Semua tugas dari mata pelajaran kelas Anda telah selesai atau belum ada tugas baru.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $assignments->links() }}</div>
</div>
@endsection
