@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Dokumen Akademik</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rapor Hasil Belajar Siswa</h1>
            <p class="mt-1 text-xs text-slate-500">Penerbitan dan cetak laporan hasil belajar per semester.</p>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <label for="class_id" class="text-sm font-medium text-slate-700">Filter kelas</label>
        <select id="class_id" name="class_id" class="rounded-lg border-slate-300 text-sm" onchange="this.form.submit()">
            <option value="">Semua kelas</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @if($classId)<a href="{{ route('admin.report-cards.index') }}" class="text-sm font-semibold text-indigo-700">Hapus filter</a>@endif
    </form>

    <!-- Table of Report Cards -->
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">NIS</th>
                        <th class="px-6 py-3.5">Nama Siswa</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Periode Semester</th>
                        <th class="px-6 py-3.5">Peringkat</th>
                        <th class="px-6 py-3.5">Nilai Rata-rata</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reportCards as $rc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $rc->student?->student_number }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $rc->student?->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $rc->schoolClass?->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $rc->semester?->name }} ({{ $rc->semester?->academicYear?->name }})</td>
                            <td class="px-6 py-4">
                                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">
                                    {{ $rc->rank ? 'Ke-'.$rc->rank : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                {{ $rc->gpa ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.report-cards.show', $rc) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak Rapor</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Belum ada data rapor siswa yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $reportCards->links() }}</div>
    </div>
</div>
@endsection
