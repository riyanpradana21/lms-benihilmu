@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div><p class="text-sm text-slate-500">Rekap hasil belajar per mata pelajaran</p><h1 class="mt-1 text-2xl font-bold text-slate-900">Nilai Saya</h1></div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Mata pelajaran</th><th class="px-5 py-3">Tugas</th><th class="px-5 py-3">Kuis</th><th class="px-5 py-3">PTS</th><th class="px-5 py-3">PAS</th><th class="px-5 py-3">Rata-rata</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($courses as $course)
                        @php
                            $typeNames = ['assignment' => 'assignment', 'quiz' => 'quiz', 'midterm' => 'midterm', 'final' => 'final'];
                            $scores = [];
                            foreach ($typeNames as $type => $key) {
                                $values = $course->gradeComponents->where('type', $type)->flatMap(fn ($component) => $component->studentGrades->pluck('score'));
                                $scores[$key] = $values->isNotEmpty() ? round($values->avg(), 1) : null;
                            }
                            $availableScores = collect($scores)->filter(fn ($score) => $score !== null);
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $course->subject?->name ?? $course->title }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $scores['assignment'] ?? '—' }}</td><td class="px-5 py-4 text-slate-700">{{ $scores['quiz'] ?? '—' }}</td><td class="px-5 py-4 text-slate-700">{{ $scores['midterm'] ?? '—' }}</td><td class="px-5 py-4 text-slate-700">{{ $scores['final'] ?? '—' }}</td>
                            <td class="px-5 py-4 font-semibold text-indigo-700">{{ $availableScores->isNotEmpty() ? round($availableScores->avg(), 1) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">Belum ada nilai untuk kelas aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $courses->links() }}</div>
    </section>
</div>
@endsection
