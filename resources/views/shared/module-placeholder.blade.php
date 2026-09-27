@extends('layouts.app')

@php
    $moduleName = (string) $module;
    $moduleLabels = [
        'users' => 'Pengguna & Role',
        'courses' => 'Kursus LMS',
        'attendance' => 'Presensi',
        'grades' => 'Nilai',
        'report-cards' => 'Rapor Siswa',
        'audit-logs' => 'Audit Log',
        'assignments' => 'Tugas',
        'quizzes' => 'Kuis & CBT',
        'question-banks' => 'Bank Soal',
        'schedule' => 'Jadwal Pelajaran',
        'kts' => 'Kartu Pelajar',
        'children' => 'Data Anak',
    ];
    $label = $moduleLabels[$moduleName] ?? ucfirst(str_replace('-', ' ', $moduleName));
@endphp

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl font-bold text-indigo-600">{{ strtoupper(substr($label, 0, 1)) }}</div>
        <h2 class="mt-5 text-xl font-bold text-slate-900">{{ $label }}</h2>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Modul ini sudah terdaftar dalam navigasi aplikasi dan siap diisi dengan alur data berikutnya. Tidak ada data yang ditampilkan sebelum sumber datanya tersedia.</p>
    </div>
@endsection