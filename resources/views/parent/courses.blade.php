@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Portal Wali Murid</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Kursus LMS Anak</h1>
            <p class="mt-1 text-xs text-slate-500">Mata pelajaran dan silabus digital yang dipelajari anak di sekolah.</p>
        </div>

        @if($children->count() > 1)
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                @foreach($children as $ch)
                    <a href="{{ route('parent.courses.index', ['child_id' => $ch->id]) }}"
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
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-700 text-sm">
                {{ strtoupper(substr($selectedChild->name, 0, 2)) }}
            </div>
            <div>
                <div class="font-bold text-slate-900 text-sm">{{ $selectedChild->name }}</div>
                <div class="text-slate-400">NIS: {{ $selectedChild->student_number }} &bull; Kelas: {{ $selectedChild->schoolClasses->pluck('name')->join(', ') }}</div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($courses as $course)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $course->subject?->code }}</span>
                        <span class="text-xs text-slate-400">{{ $course->chapters->count() }} Bab Silabus</span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900">{{ $course->title }}</h3>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $course->description ?: 'Materi kurikulum sekolah.' }}</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Guru Pengampu:</span>
                        <span class="font-semibold text-slate-800">{{ $course->teacher?->user?->name ?? 'Belum ditentukan' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Belum Ada Kursus</h3>
                <p class="mt-1 text-xs text-slate-500">Kursus akan muncul setelah kelas anak dipetakan.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $courses->links() }}</div>
</div>
@endsection
