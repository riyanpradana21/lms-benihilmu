@extends('layouts.app')

@php
    $role = (string) $role;
    $labels = [
        'teacher' => 'Dashboard Guru',
        'student' => 'Dashboard Siswa',
        'parent' => 'Dashboard Orang Tua',
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <div>
            <p class="text-sm text-slate-500">Selamat datang kembali, {{ auth()->user()->name }}.</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $labels[$role] }}</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($data['cards'] as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="font-semibold text-slate-900">{{ $data['itemTitle'] }}</h3>
            <div class="mt-4 divide-y divide-slate-100">
                @forelse($data['items'] as $item)
                    <div class="flex flex-col gap-1 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <div><p class="font-medium text-slate-900">{{ $item->title ?? $item->name }}</p><p class="text-xs text-slate-500">{{ $item->subject?->name ?? $item->schoolClasses->pluck('name')->join(', ') }}</p></div>
                        @if(isset($item->schoolClass))<span class="text-xs text-slate-500">{{ $item->schoolClass?->name }}</span>@endif
                    </div>
                @empty
                    <p class="py-5 text-sm text-slate-500">{{ $data['empty'] }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection