@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Portal Wali Murid</p>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Anak Terhubung</h1>
        <p class="mt-1 text-xs text-slate-500">Daftar siswa yang berada dalam pengawasan dan perwalian akun Anda.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($children as $child)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-700 text-base">
                            {{ strtoupper(substr($child->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $child->name }}</h3>
                            <p class="font-mono text-xs text-slate-500">NIS: {{ $child->student_number }}</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Kelas Akademik:</dt>
                            <dd class="font-semibold text-slate-700">{{ $child->schoolClasses->pluck('name')->join(', ') ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Tahun Ajaran:</dt>
                            <dd class="font-semibold text-slate-700">{{ $child->schoolClasses->first()?->academicYear?->name ?? '2024/2025' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-400">Email Akun Siswa:</dt>
                            <dd class="font-medium text-slate-700 truncate max-w-[140px]">{{ $child->user?->email }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('parent.attendance.index', ['child_id' => $child->id]) }}" class="inline-flex items-center justify-center py-2 px-3 rounded-lg bg-indigo-50 text-indigo-700 font-semibold hover:bg-indigo-100 transition">
                        Cek Presensi
                    </a>
                    <a href="{{ route('parent.grades.index', ['child_id' => $child->id]) }}" class="inline-flex items-center justify-center py-2 px-3 rounded-lg bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100 transition">
                        Cek Nilai
                    </a>
                    <a href="{{ route('parent.assignments.index', ['child_id' => $child->id]) }}" class="inline-flex items-center justify-center py-2 px-3 rounded-lg bg-amber-50 text-amber-700 font-semibold hover:bg-amber-100 transition">
                        Cek Tugas
                    </a>
                    <a href="{{ route('parent.courses.index', ['child_id' => $child->id]) }}" class="inline-flex items-center justify-center py-2 px-3 rounded-lg bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 transition">
                        Kursus LMS
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h3 class="font-semibold text-slate-800 text-sm">Belum Ada Anak Terhubung</h3>
                <p class="mt-1 text-xs text-slate-500">Hubungi pihak tata usaha sekolah untuk menghubungkan akun wali murid dengan profil siswa.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
