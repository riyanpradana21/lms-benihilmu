@extends('layouts.app')

@php
    $value = function (object $row, string $key): string {
        return match ($key) {
            'title', 'name' => (string) ($row->title ?? $row->name),
            'course' => (string) ($row->course?->title ?? '-'),
            'subject' => (string) ($row->session?->subject?->name ?? $row->subject?->name ?? '-'),
            'component' => (string) ($row->gradeComponent?->name ?? '-'),
            'score' => (string) $row->score,
            'graded_at' => $row->graded_at?->format('d M Y') ?? '-',
            'date' => $row->session?->date?->format('d M Y') ?? '-',
            'status' => ucfirst((string) $row->status),
            'note' => (string) ($row->note ?: '-'),
            'duration' => $row->duration_minutes.' menit',
            'window' => ($row->starts_at?->format('d M H:i') ?? '-') . ' - ' . ($row->ends_at?->format('d M H:i') ?? '-'),
            'deadline' => $row->deadline?->format('d M Y H:i') ?? '-',
            'day' => ucfirst((string) $row->day_of_week),
            'time' => substr((string) $row->start_time, 0, 5).' - '.substr((string) $row->end_time, 0, 5),
            'teacher' => (string) ($row->teacher?->user?->name ?? '-'),
            'room' => (string) ($row->room ?: '-'),
            'questions' => (string) $row->questions->count(),
            default => '-',
        };
    };
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3"><div><p class="text-sm text-slate-500">Ruang pembelajaran dan pemantauan</p><h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $title }}</h2><p class="mt-2 text-sm text-slate-500">{{ $description }}</p></div>@if($title === 'Jadwal Pelajaran')<a href="{{ route('student.schedule.print') }}" class="rounded-lg bg-blue-950 px-4 py-2 text-sm font-semibold text-white print:hidden">Cetak / Simpan PDF</a>@endif</div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>@foreach($columns as $column)<th class="px-5 py-3">{{ $column }}</th>@endforeach</tr></thead><tbody class="divide-y divide-slate-100">@forelse($rows as $row)<tr>@foreach($columns as $key => $column)<td class="px-5 py-4 {{ $loop->first ? 'font-medium text-slate-900' : 'text-slate-600' }}">{{ $value($row, $key) }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($columns) }}" class="px-5 py-14 text-center"><p class="font-semibold text-slate-900">Belum ada data</p><p class="mt-2 text-sm text-slate-500">{{ $empty }}</p></td></tr>@endforelse</tbody></table></div><div class="border-t border-slate-100 px-5 py-4">{{ $rows->links() }}</div></section>
</div>
@if($title === 'Jadwal Pelajaran')
<style>@media print { nav, aside, header, footer, button, .pagination { display: none !important; } body { background: white !important; } main { padding: 0 !important; } section { border: 0 !important; box-shadow: none !important; } }</style>
@endif
@endsection
