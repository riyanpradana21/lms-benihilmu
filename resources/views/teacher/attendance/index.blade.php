@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Presensi & Kehadiran</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Presensi Kelas Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Pencatatan sesi kehadiran siswa pada mata pelajaran yang Anda ampu.</p>
        </div>

        <button onclick="document.getElementById('modal-create-session').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buka Sesi Presensi Baru</span>
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table of Sessions -->
    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Mata Pelajaran</th>
                        <th class="px-6 py-3.5">Jam Mulai</th>
                        <th class="px-6 py-3.5">Rekap Kehadiran</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                        @php
                            $attendances = $session->attendances;
                            $present = $attendances->where('status', 'present')->count();
                            $sick = $attendances->where('status', 'sick')->count();
                            $permission = $attendances->where('status', 'permission')->count();
                            $absent = $attendances->where('status', 'absent')->count();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $session->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $session->schoolClass?->name }}</td>
                            <td class="px-6 py-4 text-slate-700">{{ $session->subject?->name }}</td>
                            <td class="px-6 py-4 font-mono text-slate-500">{{ $session->start_time ?: '07:30' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-[11px]">
                                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-bold">H: {{ $present }}</span>
                                    <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded font-bold">S: {{ $sick }}</span>
                                    <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded font-bold">I: {{ $permission }}</span>
                                    <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded font-bold">A: {{ $absent }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('teacher.attendance.edit', $session) }}" class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                    <span>Input / Edit Presensi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada sesi presensi yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $sessions->links() }}</div>
    </div>

    <!-- Modal Create Session -->
    <div id="modal-create-session" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-base">Buka Sesi Presensi Baru</h3>
                <button type="button" onclick="document.getElementById('modal-create-session').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('teacher.attendance.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelas Rombel</label>
                    <select name="school_class_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jam Mulai</label>
                        <input type="text" name="start_time" value="{{ date('H:i') }}" required placeholder="07:30"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-create-session').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm">
                        Buka & Input Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
