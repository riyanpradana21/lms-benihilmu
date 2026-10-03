@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('teacher.quizzes.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Kuis</span>
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $quiz->course?->subject?->code }} &bull; {{ $quiz->course?->schoolClass?->name }}</span>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Hasil & Jawaban Siswa: {{ $quiz->title }}</h1>
                <p class="mt-1 text-xs text-slate-500">Daftar pengerjaan CBT oleh siswa, rekap skor, dan integritas ujian.</p>
            </div>
        </div>
    </div>

    <!-- Table of Attempts -->
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">NIS</th>
                        <th class="px-6 py-3.5">Nama Siswa</th>
                        <th class="px-6 py-3.5">Mulai Pengerjaan</th>
                        <th class="px-6 py-3.5">Waktu Selesai</th>
                        <th class="px-6 py-3.5">Pelanggaran Tab</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attempts as $att)
                        @php
                            $isPassed = $att->score >= ($quiz->passing_score ?: 70);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $att->student?->student_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $att->student?->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $att->started_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $att->submitted_at ? $att->submitted_at->format('H:i') : '-' }}
                                @if($att->is_auto_submitted)
                                    <span class="text-[10px] text-amber-600 block">(Auto-submit)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($att->violations->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700">
                                        {{ $att->violations->count() }} Peringatan
                                    </span>
                                @else
                                    <span class="text-[11px] text-emerald-600 font-medium">Tertib (0)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array($att->status, ['submitted', 'graded'], true))
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-700 animate-pulse">
                                        Sedang Dikerjakan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-base font-extrabold {{ $isPassed ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $att->score }}
                                </span>
                                @if($att->is_locked && $att->status === 'in_progress')
                                    <form method="POST" action="{{ route('teacher.quizzes.attempts.open-access', [$quiz, $att]) }}" class="mt-2">
                                        @csrf @method('PATCH')
                                        <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-[10px] font-semibold text-white">Open Access</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Belum ada siswa yang mengerjakan kuis ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $attempts->links() }}</div>
    </div>
</div>
@endsection
