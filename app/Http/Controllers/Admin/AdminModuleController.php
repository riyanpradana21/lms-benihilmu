<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\ReportCard;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\View\View;

class AdminModuleController extends Controller
{
    public function index(string $module): View
    {
        $definitions = [
            'users' => ['title' => 'Pengguna & Role', 'description' => 'Akun yang memiliki akses ke sistem.', 'query' => User::with('roles')->latest()->take(25)->get(), 'columns' => ['name' => 'Nama', 'email' => 'Email', 'roles' => 'Role']],
            'courses' => ['title' => 'Kursus LMS', 'description' => 'Kursus yang diturunkan dari struktur akademik.', 'query' => Course::with(['subject', 'schoolClass', 'teacher.user'])->latest()->take(25)->get(), 'columns' => ['title' => 'Kursus', 'subject' => 'Mapel', 'schoolClass' => 'Kelas', 'teacher' => 'Guru']],
            'attendance' => ['title' => 'Rekap Presensi', 'description' => 'Riwayat kehadiran siswa dari sesi yang tercatat.', 'query' => Attendance::with(['student', 'session.subject'])->latest()->take(25)->get(), 'columns' => ['student' => 'Siswa', 'subject' => 'Mapel', 'status' => 'Status']],
            'grades' => ['title' => 'Rekap Nilai', 'description' => 'Nilai yang sudah dicatat guru.', 'query' => StudentGrade::with(['student', 'course.subject', 'gradeComponent'])->latest()->take(25)->get(), 'columns' => ['student' => 'Siswa', 'course' => 'Kursus', 'component' => 'Komponen', 'score' => 'Nilai']],
            'report-cards' => ['title' => 'Rapor Siswa', 'description' => 'Ringkasan hasil belajar per semester.', 'query' => ReportCard::with(['student', 'schoolClass', 'semester'])->latest()->take(25)->get(), 'columns' => ['student' => 'Siswa', 'class' => 'Kelas', 'period' => 'Periode']],
            'audit-logs' => ['title' => 'Audit Log', 'description' => 'Aktivitas penting yang tercatat untuk penelusuran.', 'query' => AuditLog::with('user')->latest('created_at')->take(25)->get(), 'columns' => ['action' => 'Aksi', 'user' => 'Aktor', 'created_at' => 'Waktu']],
        ];

        abort_unless(isset($definitions[$module]), 404);

        $definition = $definitions[$module];
        $rows = $definition['query'];

        return view('admin.module.index', compact('module', 'definition', 'rows'));
    }
}
