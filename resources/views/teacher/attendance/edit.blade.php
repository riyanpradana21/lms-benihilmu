@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('teacher.attendance.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Presensi</span>
        </a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700">{{ $session->subject?->code }} &bull; {{ $session->schoolClass?->name }}</span>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Input Presensi: {{ $session->date->format('d F Y') }}</h1>
                <p class="mt-1 text-xs text-slate-500">Mata Pelajaran: {{ $session->subject?->name }} &bull; Jam Mulai: {{ $session->start_time ?: '07:30' }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('teacher.attendance.update', $session) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3.5">NIS</th>
                            <th class="px-6 py-3.5">Nama Siswa</th>
                            <th class="px-6 py-3.5 text-center">Status Kehadiran</th>
                            <th class="px-6 py-3.5">Catatan (Opsional)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($session->attendances as $att)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $att->student?->student_number }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $att->student?->name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-4">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendances[{{ $att->student_id }}][status]" value="present" {{ $att->status === 'present' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500">
                                            <span class="font-bold text-emerald-700 text-xs">Hadir</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendances[{{ $att->student_id }}][status]" value="sick" {{ $att->status === 'sick' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                            <span class="font-bold text-blue-700 text-xs">Sakit</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendances[{{ $att->student_id }}][status]" value="permission" {{ $att->status === 'permission' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                            <span class="font-bold text-amber-700 text-xs">Izin</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="attendances[{{ $att->student_id }}][status]" value="absent" {{ $att->status === 'absent' ? 'checked' : '' }}
                                                   class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500">
                                            <span class="font-bold text-rose-700 text-xs">Alpa</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="attendances[{{ $att->student_id }}][note]" value="{{ $att->note }}" placeholder="Catatan khusus..."
                                           class="w-full text-xs rounded-lg border border-slate-200 px-3 py-1.5 focus:ring-1 focus:ring-indigo-500">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">Total {{ $session->attendances->count() }} siswa terdaftar pada kelas ini.</span>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md transition">
                    Simpan Perubahan Presensi
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
