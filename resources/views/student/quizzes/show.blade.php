@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('student.quizzes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Kuis</span>
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $quiz->course?->subject?->code }}</span>
                    <span class="text-xs text-slate-400">&bull; {{ $quiz->course?->title }}</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $quiz->title }}</h1>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $quiz->description ?: 'Ujian Berbasis Komputer (CBT).' }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-medium text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Column: Instructions & Action (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <h2 class="text-base font-bold text-slate-900">Petunjuk & Ketentuan Pengerjaan</h2>
                
                <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 text-xs">1</span>
                        <div>
                            <span class="font-bold text-slate-800">Server-Authoritative Timer:</span>
                            Waktu pengerjaan dihitung oleh server. Jika halaman dimuat ulang (refresh), waktu tidak akan bertambah atau mengulang dari awal.
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 text-xs">2</span>
                        <div>
                            <span class="font-bold text-slate-800">Penyimpanan Otomatis (Autosave):</span>
                            Setiap opsi jawaban yang Anda pilih akan langsung tersimpan ke sistem tanpa perlu menekan tombol simpan per soal.
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 text-xs">3</span>
                        <div>
                            <span class="font-bold text-slate-800">Integritas CBT & Anti-Cheat:</span>
                            Aktivitas perpindahan tab, keluar dari layar penuh, atau kehilangan fokus pada layar ujian akan tercatat dalam log aktivitas ujian.
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    @if($activeAttempt)
                        <div class="text-xs">
                            <span class="text-amber-600 font-semibold block">Anda memiliki sesi ujian yang sedang berjalan.</span>
                            <span class="text-slate-400">Sisa waktu akan dilanjutkan sesuai waktu server.</span>
                        </div>
                        <a href="{{ route('student.quizzes.take', [$quiz, $activeAttempt]) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 text-xs font-semibold shadow-md transition">
                            <span>Lanjutkan Pengerjaan Ujian</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @elseif($canAttempt)
                        <div class="text-xs text-slate-500">
                            Pastikan koneksi internet stabil sebelum menekan tombol mulai.
                        </div>
                        <form action="{{ route('student.quizzes.start', $quiz) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 text-xs font-semibold shadow-md transition">
                                <span>Mulai Mengerjakan Ujian</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    @else
                        <div class="text-xs text-rose-600 font-semibold">
                            Batas percobaan telah tercapai atau jadwal ujian tidak aktif.
                        </div>
                        <button disabled class="rounded-xl bg-slate-100 px-6 py-3 text-xs font-semibold text-slate-400 cursor-not-allowed">
                            Ujian Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>

            <!-- History of Attempts -->
            @if($attempts->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat Percobaan Anda</h3>
                    <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden text-xs">
                        @foreach($attempts as $index => $att)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                                <div>
                                    <div class="font-bold text-slate-800">Percobaan ke-{{ $attempts->count() - $index }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Mulai: {{ $att->started_at?->format('d M Y, H:i') }} &bull;
                                        Selesai: {{ $att->submitted_at ? $att->submitted_at->format('H:i') : 'Belum selesai' }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <div class="text-[10px] uppercase font-bold text-slate-400">Nilai</div>
                                        <div class="text-base font-extrabold {{ $att->score >= ($quiz->passing_score ?: 70) ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $att->score }}
                                        </div>
                                    </div>
                                    @if(in_array($att->status, ['submitted', 'graded'], true))
                                        <a href="{{ route('student.quizzes.result', [$quiz, $att]) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs hover:bg-slate-200 transition">
                                            Lihat Hasil
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Specs Column (4 cols) -->
        <aside class="lg:col-span-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4 text-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Spesifikasi Kuis</h3>
            
            <div class="space-y-3 divide-y divide-slate-100">
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500">Durasi Pengerjaan:</span>
                    <span class="font-bold text-slate-800">{{ $quiz->duration_minutes }} Menit</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500">Jumlah Soal:</span>
                    <span class="font-bold text-slate-800">{{ $quiz->questions->count() }} Butir</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500">Standar Kelulusan (KKM):</span>
                    <span class="font-bold text-indigo-600">{{ $quiz->passing_score ?: 70 }} / 100</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500">Maksimal Percobaan:</span>
                    <span class="font-bold text-slate-800">{{ $quiz->attempt_limit > 0 ? $quiz->attempt_limit . ' Kali' : 'Tak Terbatas' }}</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500">Batas Pelanggaran Tab:</span>
                    <span class="font-bold text-slate-800">{{ $quiz->violation_threshold ?: 5 }} Kali</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
