@extends('layouts.app')

@php
    $value = function (object $row, string $key): string {
        return match ($key) {
            'name' => (string) $row->name,
            'email' => (string) $row->email,
            'roles' => $row->roles->pluck('name')->join(', '),
            'title' => (string) $row->title,
            'subject' => (string) ($row->subject?->name ?? $row->session?->subject?->name),
            'schoolClass', 'class' => (string) ($row->schoolClass?->name ?? '-'),
            'teacher' => (string) ($row->teacher?->user?->name ?? '-'),
            'student' => (string) ($row->student?->name ?? '-'),
            'status' => ucfirst((string) $row->status),
            'course' => (string) ($row->course?->title ?? '-'),
            'component' => (string) ($row->gradeComponent?->name ?? '-'),
            'score' => (string) $row->score,
            'period' => (string) ($row->semester?->name ?? '-'),
            'action' => (string) $row->action,
            'user' => (string) ($row->user?->name ?? 'Sistem'),
            'created_at' => $row->created_at?->format('d M Y H:i') ?? '-',
            default => '-',
        };
    };
@endphp

@section('content')
<div class="space-y-6">
    <div><p class="text-sm text-slate-500">Administrasi dan pemantauan sekolah</p><h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $definition['title'] }}</h2><p class="mt-2 text-sm text-slate-500">{{ $definition['description'] }}</p></div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>@foreach($definition['columns'] as $column)<th class="px-5 py-3">{{ $column }}</th>@endforeach</tr></thead><tbody class="divide-y divide-slate-100">@forelse($rows as $row)<tr>@foreach($definition['columns'] as $key => $column)<td class="px-5 py-4 {{ $loop->first ? 'font-medium text-slate-900' : 'text-slate-600' }}">{{ $value($row, $key) }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($definition['columns']) }}" class="px-5 py-14 text-center"><p class="font-semibold text-slate-900">Belum ada data</p><p class="mt-2 text-sm text-slate-500">Data akan muncul setelah aktivitas terkait tersimpan.</p></td></tr>@endforelse</tbody></table></div></section>
</div>
@endsection
