@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Evaluasi & Penilaian</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Bobot & Input Nilai Guru</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola komponen penilaian berbobot dan catat nilai akademik siswa per kelas.</p>
        </div>

        @if($selectedCourse)
            <button onclick="document.getElementById('modal-create-component').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Komponen Nilai</span>
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- Course & Component Filters Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('teacher.grades.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end text-xs">
            <div class="sm:col-span-6">
                <label class="block font-semibold text-slate-700 mb-1.5">Pilih Kursus & Kelas</label>
                <select name="course_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ $selectedCourse?->id === $c->id ? 'selected' : '' }}>
                            {{ $c->title }} ({{ $c->schoolClass?->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-6">
                <label class="block font-semibold text-slate-700 mb-1.5">Pilih Komponen Nilai</label>
                <select name="component_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    @forelse($components as $comp)
                        <option value="{{ $comp->id }}" {{ $selectedComponent?->id === $comp->id ? 'selected' : '' }}>
                            {{ $comp->name }} (Bobot: {{ $comp->weight }}%)
                        </option>
                    @empty
                        <option value="">Belum ada komponen nilai</option>
                    @endforelse
                </select>
            </div>
        </form>
    </div>

    <!-- Active Component Summary -->
    @if($selectedComponent)
        <div class="flex items-center justify-between p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl text-xs">
            <div>
                <span class="text-indigo-600 font-bold uppercase tracking-wider text-[10px]">Komponen Aktif:</span>
                <span class="font-bold text-slate-800 ml-1">{{ $selectedComponent->name }}</span>
                <span class="text-slate-500 ml-2">&bull; Tipe: {{ strtoupper($selectedComponent->type) }}</span>
            </div>
            <div class="font-bold text-indigo-700">
                Bobot Nilai: {{ $selectedComponent->weight }}%
            </div>
        </div>
    @endif

    <!-- Students Grades Form Table -->
    @if($selectedCourse && $selectedComponent)
        <form action="{{ route('teacher.grades.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
            <input type="hidden" name="grade_component_id" value="{{ $selectedComponent->id }}">

            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-6 py-3.5">NIS</th>
                                <th class="px-6 py-3.5">Nama Siswa</th>
                                <th class="px-6 py-3.5">Status Siswa</th>
                                <th class="px-6 py-3.5 w-48">Nilai (0 - 100)</th>
                                <th class="px-6 py-3.5 text-right">Terakhir Dinilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $st)
                                @php
                                    $score = $grades->get($st->id)?->score;
                                    $gradedAt = $grades->get($st->id)?->graded_at;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-6 py-4 font-mono font-medium text-slate-700">{{ $st->student_number }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $st->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Aktif</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" step="0.01" min="0" max="100" name="grades[{{ $st->id }}]" value="{{ $score }}" placeholder="0.00"
                                               class="w-full text-xs font-bold rounded-lg border border-slate-300 px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 text-slate-800">
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-400 text-[11px]">
                                        {{ $gradedAt ? $gradedAt->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        Belum ada siswa yang terdaftar di kelas kursus ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Nilai akan otomatis masuk ke dalam perhitungan rapor semester siswa.</span>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md transition">
                        Simpan Nilai Siswa
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
            <h3 class="font-semibold text-slate-800 text-sm">Pilih Kursus & Komponen Nilai</h3>
            <p class="mt-1 text-xs text-slate-500">Buat atau pilih komponen nilai untuk memasukkan nilai siswa.</p>
        </div>
    @endif

    <!-- Modal Create Component -->
    @if($selectedCourse)
        <div id="modal-create-component" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-base">Tambah Komponen Nilai</h3>
                    <button type="button" onclick="document.getElementById('modal-create-component').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('teacher.grades.components.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kursus</label>
                        <input type="text" disabled value="{{ $selectedCourse->title }}" class="w-full rounded-xl bg-slate-100 border border-slate-200 p-2.5 text-xs text-slate-500">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Komponen</label>
                        <input type="text" name="name" required placeholder="Contoh: Tugas Mandiri, Kuis Bab 1, UTS, UAS"
                               class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori Tipe</label>
                            <select name="type" required class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs">
                                <option value="assignment">Tugas (Assignment)</option>
                                <option value="quiz">Kuis (Quiz)</option>
                                <option value="midterm">UTS (Midterm)</option>
                                <option value="final">UAS (Final)</option>
                                <option value="project">Proyek (Project)</option>
                                <option value="practical">Praktik (Practical)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Bobot Persentase (%)</label>
                            <input type="number" name="weight" value="20" required min="1" max="100"
                                   class="w-full rounded-xl border border-slate-300 p-2.5 focus:ring-2 focus:ring-indigo-500 text-xs font-bold">
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-create-component').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm">
                            Simpan Komponen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
