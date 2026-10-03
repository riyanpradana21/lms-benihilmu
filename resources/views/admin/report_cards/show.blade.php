@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.report-cards.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Rapor</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Lembar Hasil Belajar Siswa</h1>
            <p class="mt-1 text-xs text-slate-500">Pratinjau resmi laporan hasil evaluasi semester siswa.</p>
        </div>

        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Dokumen Rapor</span>
        </button>
    </div>

    <!-- Printable Official Sheet -->
    <div class="flex justify-center">
        <div id="printable-report" class="bg-white rounded-2xl border border-slate-200 p-8 sm:p-12 max-w-4xl w-full shadow-lg text-slate-800 space-y-8">
            <!-- Kop Surat Sekolah -->
            <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 font-extrabold text-2xl text-white">
                        S
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-wider text-slate-900">{{ $institution?->name ?? 'Nama Sekolah' }}</h2>
                        <p class="text-xs text-slate-600">{{ $institution?->address ?? 'Alamat sekolah belum diatur' }}</p>
                        <p class="text-[11px] text-slate-500">Telepon: {{ $institution?->phone ?? '—' }} &bull; Website: {{ $institution?->website ?? '—' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs font-mono font-bold text-slate-400">RAPOR-{{ $reportCard->id }}</span>
                </div>
            </div>

            <!-- Judul Laporan -->
            <div class="text-center space-y-1">
                <h3 class="text-base font-bold uppercase tracking-widest text-slate-900">Laporan Capaian Hasil Belajar Siswa</h3>
                <p class="text-xs text-slate-500">Tahun Ajaran {{ $reportCard->semester?->academicYear?->name ?? '—' }} &bull; Semester {{ $reportCard->semester?->name ?? '—' }}</p>
            </div>

            <!-- Identitas Siswa Grid -->
            <div class="grid grid-cols-2 gap-4 text-xs p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div class="space-y-1.5">
                    <div class="flex">
                        <span class="w-36 text-slate-500">Nama Siswa:</span>
                        <span class="font-bold text-slate-900">{{ $student->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-36 text-slate-500">Nomor Induk Siswa (NIS):</span>
                        <span class="font-mono font-bold text-slate-800">{{ $student->student_number }}</span>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="flex">
                        <span class="w-32 text-slate-500">Kelas / Rombel:</span>
                        <span class="font-bold text-slate-900">{{ $reportCard->schoolClass?->name }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-slate-500">Wali Kelas:</span>
                        <span class="font-semibold text-slate-800">{{ $reportCard->schoolClass?->homeroomTeacher?->name ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabel Nilai Mata Pelajaran -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">A. Nilai Akademik & Capaian Kompetensi</h4>
                <table class="w-full text-left text-xs border border-slate-300">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-300">
                        <tr>
                            <th class="p-3 border-r border-slate-300 w-12 text-center">No</th>
                            <th class="p-3 border-r border-slate-300">Mata Pelajaran</th>
                            <th class="p-3 border-r border-slate-300 text-center">Tugas</th>
                            <th class="p-3 border-r border-slate-300 text-center">Kuis</th>
                            <th class="p-3 border-r border-slate-300 text-center">PTS</th>
                            <th class="p-3 border-r border-slate-300 text-center">PAS</th>
                            <th class="p-3 border-r border-slate-300 w-24 text-center">Nilai Akhir</th>
                            <th class="p-3 w-40 text-center">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($subjectScores as $idx => $sub)
                            <tr>
                                <td class="p-3 border-r border-slate-200 text-center">{{ $idx + 1 }}</td>
                                <td class="p-3 border-r border-slate-200 font-semibold text-slate-900">
                                    {{ $sub['name'] }}
                                    <span class="text-[10px] text-slate-400 block font-normal">{{ $sub['teacher'] }}</span>
                                </td>
                                <td class="p-3 border-r border-slate-200 text-center font-mono">{{ $sub['components']['assignment'] ?? '—' }}</td>
                                <td class="p-3 border-r border-slate-200 text-center font-mono">{{ $sub['components']['quiz'] ?? '—' }}</td>
                                <td class="p-3 border-r border-slate-200 text-center font-mono">{{ $sub['components']['midterm'] ?? '—' }}</td>
                                <td class="p-3 border-r border-slate-200 text-center font-mono">{{ $sub['components']['final'] ?? '—' }}</td>
                                <td class="p-3 border-r border-slate-200 text-center font-bold text-slate-900 font-mono">{{ $sub['score'] ?? '—' }}</td>
                                <td class="p-3 text-center font-semibold text-slate-700">{{ $sub['predicate'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400">Belum ada mata pelajaran tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold border-t border-slate-300">
                        <tr>
                            <td colspan="6" class="p-3 border-r border-slate-300 text-right">Rata-rata Nilai Siswa:</td>
                            <td class="p-3 border-r border-slate-300 text-center text-indigo-700 font-mono text-sm">{{ $averageScore }}</td>
                            <td class="p-3 text-center">Peringkat: {{ $reportCard->rank ? 'Ke-'.$reportCard->rank : '—' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Rekap Ketidakhadiran & Catatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <!-- Ketidakhadiran -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-700 mb-2">B. Ketidakhadiran</h4>
                    <table class="w-full border border-slate-300">
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="p-2.5 bg-slate-50 text-slate-600 border-r border-slate-200">1. Sakit</td>
                                <td class="p-2.5 text-center font-bold">{{ $attendanceSummary['sick'] }} hari</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 bg-slate-50 text-slate-600 border-r border-slate-200">2. Izin</td>
                                <td class="p-2.5 text-center font-bold">{{ $attendanceSummary['permission'] }} hari</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 bg-slate-50 text-slate-600 border-r border-slate-200">3. Tanpa Keterangan</td>
                                <td class="p-2.5 text-center font-bold">{{ $attendanceSummary['absent'] }} hari</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Catatan Wali Kelas -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-slate-700 mb-2">C. Catatan Perkembangan Belajar</h4>
                    <div class="p-3.5 border border-slate-300 rounded-lg min-h-[95px] text-slate-600 leading-relaxed italic bg-slate-50">
                        {{ $reportCard->teacher_notes ?: 'Belum ada catatan wali kelas.' }}
                    </div>
                </div>
            </div>

            <!-- Tanda Tangan Pengesahan -->
            <div class="pt-8 grid grid-cols-3 text-center text-xs gap-4">
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="text-slate-700 font-medium">Orang Tua / Wali Murid</p>
                    <div class="h-20"></div>
                    <p class="border-t border-slate-400 mx-6 pt-1 font-semibold text-slate-800">( .................................... )</p>
                </div>

                <div>
                    <p class="text-slate-500">{{ now()->translatedFormat('d F Y') }}</p>
                    <p class="text-slate-700 font-medium">Wali Kelas</p>
                    <div class="h-20"></div>
                    <p class="border-t border-slate-400 mx-6 pt-1 font-semibold text-slate-800">{{ $reportCard->schoolClass?->homeroomTeacher?->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-slate-500">&nbsp;</p>
                    <p class="text-slate-700 font-medium">Kepala Sekolah</p>
                    <div class="h-20"></div>
                    <p class="border-t border-slate-400 mx-6 pt-1 font-semibold text-slate-800">{{ $institution?->principal_name ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printable-report, #printable-report * {
        visibility: visible;
    }
    #printable-report {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
