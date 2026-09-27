@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Portal Wali Murid</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tugas & Evaluasi Anak</h1>
            <p class="mt-1 text-xs text-slate-500">Pantau status pengerjaan tugas dan feedback dari bapak/ibu guru.</p>
        </div>

        @if($children->count() > 1)
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                @foreach($children as $ch)
                    <a href="{{ route('parent.assignments.index', ['child_id' => $ch->id]) }}"
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
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 font-bold text-amber-700 text-sm">
                {{ strtoupper(substr($selectedChild->name, 0, 2)) }}
            </div>
            <div>
                <div class="font-bold text-slate-900 text-sm">{{ $selectedChild->name }}</div>
                <div class="text-slate-400">NIS: {{ $selectedChild->student_number }} &bull; Kelas: {{ $selectedChild->schoolClasses->pluck('name')->join(', ') }}</div>
            </div>
        </div>
    </div>

    <!-- Assignments Grid -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isSubmitted = $submission !== null;
                $isGraded = $submission?->status === 'graded';
            @endphp
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $assignment->course?->subject?->code }}</span>
                        @if($isGraded)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Sudah Dinilai</span>
                        @elseif($isSubmitted)
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-700">Terkumpul</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-700">Belum Kumpul</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $assignment->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $assignment->description ?: 'Tidak ada petunjuk tambahan.' }}</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Batas Waktu:</span>
                            <span class="font-medium text-slate-700">{{ $assignment->deadline?->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Tugas:</span>
                            <span class="font-semibold {{ $isSubmitted ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $isSubmitted ? 'Terkumpul (' . $submission->submitted_at?->format('d/m/Y') . ')' : 'Menunggu' }}
                            </span>
                        </div>
                        @if($isGraded)
                            <div class="flex justify-between pt-1 border-t border-slate-100">
                                <span class="text-slate-500 font-bold">Nilai Diperoleh:</span>
                                <span class="font-extrabold text-emerald-600 text-sm">{{ $submission->score }} / {{ $assignment->max_score }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Tidak Ada Tugas</h3>
                <p class="mt-1 text-xs text-slate-500">Belum ada tugas aktif untuk kelas anak Anda saat ini.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $assignments->links() }}</div>
</div>
@endsection
