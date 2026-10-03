@extends('layouts.app')

@php($role = auth()->user()->hasRole(['super_admin', 'admin']) ? 'admin' : (auth()->user()->hasRole('teacher') ? 'teacher' : (auth()->user()->hasRole('student') ? 'student' : 'parent')))

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header><p class="text-sm font-medium text-blue-700">Informasi sekolah</p><h1 class="mt-1 text-2xl font-bold text-slate-900">Pengumuman</h1><p class="mt-1 text-sm text-slate-500">Pengumuman terbaru untuk warga sekolah.</p></header>
    @if($canManage)
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-slate-900">Buat pengumuman</h2>
            <form method="POST" action="{{ route($role.'.announcements.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">@csrf
                <label class="text-sm font-medium text-slate-700">Judul<input name="title" required maxlength="255" class="mt-1 w-full rounded-lg border-slate-300" value="{{ old('title') }}"></label>
                <div class="grid grid-cols-2 gap-3"><label class="text-sm font-medium text-slate-700">Terbit<input type="datetime-local" name="published_at" class="mt-1 w-full rounded-lg border-slate-300"></label><label class="text-sm font-medium text-slate-700">Berakhir<input type="datetime-local" name="expires_at" class="mt-1 w-full rounded-lg border-slate-300"></label></div>
                <label class="text-sm font-medium text-slate-700 md:col-span-2">Isi<textarea name="body" rows="5" required maxlength="20000" class="mt-1 w-full rounded-lg border-slate-300">{{ old('body') }}</textarea></label>
                <div class="md:col-span-2"><button class="rounded-lg bg-blue-950 px-4 py-2.5 text-sm font-semibold text-white">Terbitkan</button></div>
            </form>
        </section>
    @endif
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-lg font-bold text-slate-900">{{ $announcement->title }}</h2><p class="mt-1 text-xs text-slate-500">{{ $announcement->author?->name ?? 'Administrasi sekolah' }} · {{ $announcement->published_at?->format('d/m/Y H:i') ?? 'Draf' }}</p></div>
                    @if($canManage && (auth()->user()->hasRole(['super_admin', 'admin']) || $announcement->author_id === auth()->id()))
                        <form method="POST" action="{{ route($role.'.announcements.destroy', $announcement) }}" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="text-sm font-semibold text-rose-700">Hapus</button></form>
                    @endif
                </div>
                <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $announcement->body }}</div>
                @if($canManage && (auth()->user()->hasRole(['super_admin', 'admin']) || $announcement->author_id === auth()->id()))
                    <details class="mt-4"><summary class="cursor-pointer text-sm font-semibold text-blue-800">Edit pengumuman</summary><form method="POST" action="{{ route($role.'.announcements.update', $announcement) }}" class="mt-3 grid gap-3 md:grid-cols-2">@csrf @method('PUT')<input name="title" value="{{ $announcement->title }}" required class="rounded-lg border-slate-300"><div class="grid grid-cols-2 gap-2"><input type="datetime-local" name="published_at" value="{{ $announcement->published_at?->format('Y-m-d\TH:i') }}" class="rounded-lg border-slate-300"><input type="datetime-local" name="expires_at" value="{{ $announcement->expires_at?->format('Y-m-d\TH:i') }}" class="rounded-lg border-slate-300"></div><textarea name="body" rows="4" required class="rounded-lg border-slate-300 md:col-span-2">{{ $announcement->body }}</textarea><button class="w-fit rounded-lg bg-blue-950 px-4 py-2 text-sm font-semibold text-white">Simpan</button></form></details>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">Belum ada pengumuman aktif.</div>
        @endforelse
    </div>
    {{ $announcements->links() }}
</div>
@endsection
