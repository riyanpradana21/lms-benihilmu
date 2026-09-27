@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Portal Wali Murid</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekap Presensi Anak</h1>
            <p class="mt-1 text-xs text-slate-500">Pantau kehadiran dan ketidakhadiran anak Anda secara transparan.</p>
        </div>

        <!-- Child Switcher Tabs -->
        @if($children->count() > 1)
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                @foreach($children as $ch)
                    <a href="{{ route('parent.attendance.index', ['child_id' => $ch->id]) }}"
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
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-700 text-sm">
                {{ strtoupper(substr($selectedChild->name, 0, 2)) }}
            </div>
            <div>
                <div class="font-bold text-slate-900 text-sm">{{ $selectedChild->name }}</div>
                <div class="text-slate-400">NIS: {{ $selectedChild->student_number }} &bull; Kelas: {{ $selectedChild->schoolClasses->pluck('name')->join(', ') }}</div>
            </div>
        </div>
    </div>

    <!-- Attendance Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Hadir</span>
            <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ $summary['present'] }}</span>
            <span class="text-[10px] text-slate-400">Pertemuan</span>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Sakit</span>
            <span class="text-2xl font-extrabold text-blue-600 mt-1 block">{{ $summary['sick'] }}</span>
            <span class="text-[10px] text-slate-400">Dengan surat dokter</span>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Izin</span>
            <span class="text-2xl font-extrabold text-amber-600 mt-1 block">{{ $summary['permission'] }}</span>
            <span class="text-[10px] text-slate-400">Pemberitahuan resmi</span>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanpa Keterangan (Alpa)</span>
            <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ $summary['absent'] }}</span>
            <span class="text-[10px] text-slate-400">Tidak ada kabar</span>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Mata Pelajaran</th>
                        <th class="px-6 py-3.5">Guru Pengajar</th>
                        <th class="px-6 py-3.5">Status Kehadiran</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $att->session?->date ? $att->session->date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $att->session?->subject?->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $att->session?->teacher?->user?->name ?? 'Guru Mapel' }}</td>
                            <td class="px-6 py-4">
                                @if($att->status === 'present')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Hadir</span>
                                @elseif($att->status === 'sick')
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-700">Sakit</span>
                                @elseif($att->status === 'permission')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-700">Izin</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-700">Alpa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $att->note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Belum ada riwayat presensi yang tercatat untuk anak ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $attendances->links() }}</div>
    </div>
</div>
@endsection
