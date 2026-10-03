@extends('layouts.app')

@section('content')
@php($statusCodes = ['present' => 'H', 'sick' => 'S', 'permission' => 'I', 'absent' => 'A'])
<div class="space-y-6">
    <div><p class="text-sm text-slate-500">Kode presensi: H Hadir, S Sakit, I Izin, A Alpha</p><h1 class="mt-1 text-2xl font-bold text-slate-900">Presensi Saya</h1></div>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Tanggal</th><th class="px-5 py-3">Mata pelajaran</th><th class="px-5 py-3">Jam</th><th class="px-5 py-3 text-center">Kode</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                        @php($status = $session->attendances->first()?->status)
                        <tr><td class="px-5 py-4 text-slate-700">{{ $session->date?->format('d M Y') }}</td><td class="px-5 py-4 font-medium text-slate-900">{{ $session->subject?->name ?? '—' }}</td><td class="px-5 py-4 text-slate-600">{{ $session->start_time ? substr($session->start_time, 0, 5) : '—' }}</td><td class="px-5 py-4 text-center"><span title="{{ $status ? ucfirst($status) : 'Belum diisi' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 font-bold text-indigo-700">{{ $statusCodes[$status] ?? '—' }}</span></td></tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">Belum ada pertemuan tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $sessions->links() }}</div>
    </section>
</div>
@endsection
