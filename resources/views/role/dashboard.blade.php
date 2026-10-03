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
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-950 via-blue-900 to-sky-800 p-7 text-white shadow-xl sm:p-9">
            <div class="relative z-10 max-w-2xl"><p class="text-sm font-semibold tracking-wide text-blue-200">{{ $labels[$role] }}</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Selamat datang, {{ auth()->user()->name }}</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-blue-100">Semoga hari Anda produktif. Berikut ringkasan terbaru aktivitas pembelajaran dan akademik Anda.</p>
                <div class="mt-6 flex flex-wrap gap-3"><a href="{{ route($role.'.announcements.index') }}" class="inline-flex rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/25 transition hover:bg-white/25">Lihat pengumuman sekolah</a>
                    @if($role !== 'teacher' || $canManageExams)<a href="{{ route($role.'.exam-schedules.index') }}" class="inline-flex rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-blue-950 transition hover:bg-blue-50">{{ $role === 'student' ? 'Lihat jadwal ujian' : ($role === 'parent' ? 'Jadwal ujian anak' : 'Buat jadwal ujian') }}</a>@endif
                </div>
            </div><div class="pointer-events-none absolute -right-10 -top-24 h-72 w-72 rounded-full border-[36px] border-white/10"></div><div class="pointer-events-none absolute -bottom-32 right-32 h-64 w-64 rounded-full bg-sky-400/10 blur-2xl"></div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <a href="{{ route($role.'.announcements.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"><p class="text-xs font-bold uppercase tracking-wide text-blue-700">Informasi sekolah</p><h2 class="mt-2 text-lg font-bold text-slate-900">Pengumuman</h2><p class="mt-1 text-sm text-slate-500">Baca informasi terbaru dari sekolah.</p><span class="mt-4 inline-block text-sm font-semibold text-blue-800">Buka pengumuman →</span></a>
            @if($role !== 'teacher' || $canManageExams)
                <a href="{{ route($role.'.exam-schedules.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md"><p class="text-xs font-bold uppercase tracking-wide text-blue-700">Kalender akademik</p><h2 class="mt-2 text-lg font-bold text-slate-900">{{ $role === 'student' ? 'Jadwal Ujian Saya' : ($role === 'parent' ? 'Jadwal Ujian Anak' : 'Kelola Jadwal Ujian') }}</h2><p class="mt-1 text-sm text-slate-500">{{ $role === 'teacher' ? 'Atur jadwal ujian per siswa dan mata pelajaran.' : 'Lihat jadwal ujian sesuai akses akun Anda.' }}</p><span class="mt-4 inline-block text-sm font-semibold text-blue-800">{{ $role === 'teacher' ? 'Buat jadwal →' : 'Lihat jadwal →' }}</span></a>
            @endif
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
