@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Portal Wali Murid</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekap Nilai Anak</h1>
            <p class="mt-1 text-xs text-slate-500">Nilai tugas, kuis, dan ujian hasil evaluasi dari guru pengampu.</p>
        </div>

        @if($children->count() > 1)
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                @foreach($children as $ch)
                    <a href="{{ route('parent.grades.index', ['child_id' => $ch->id]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $selectedChild->id === $ch->id ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        {{ $ch->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Active Child Banner -->
    <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-2xl shadow-sm text-xs">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 font-bold text-emerald-700 text-sm">
                {{ strtoupper(substr($selectedChild->name, 0, 2)) }}
            </div>
            <div>
                <div class="font-bold text-slate-900 text-sm">{{ $selectedChild->name }}</div>
                <div class="text-slate-400">NIS: {{ $selectedChild->student_number }} &bull; Kelas: {{ $selectedChild->schoolClasses->pluck('name')->join(', ') }}</div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">Mata Pelajaran</th>
                        <th class="px-6 py-3.5">Kursus LMS</th>
                        <th class="px-6 py-3.5">Komponen Penilaian</th>
                        <th class="px-6 py-3.5">Tanggal Input</th>
                        <th class="px-6 py-3.5 text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($grades as $grade)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $grade->course?->subject?->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $grade->course?->title }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                    {{ $grade->gradeComponent?->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-400 text-[11px]">
                                {{ $grade->graded_at ? $grade->graded_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-base font-extrabold {{ $grade->score >= 70 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $grade->score }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Belum ada nilai yang dipublikasikan untuk anak ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $grades->links() }}</div>
    </div>
</div>
@endsection
