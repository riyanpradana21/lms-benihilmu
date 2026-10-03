<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function index(string $role): View|RedirectResponse
    {
        $user = Auth::user();

        $data = match ($role) {
            'teacher' => $this->teacherData($user->teacher),
            'student' => $this->studentData($user->student),
            'parent' => $this->parentData($user->guardian),
            default => [],
        };

        $canManageExams = $role === 'teacher' && SchoolClass::where('homeroom_teacher_id', $user->id)->exists();

        return view('role.dashboard', compact('role', 'data', 'canManageExams'));
    }

    private function teacherData(?Teacher $teacher): array
    {
        $courses = $teacher?->courses()
            ->with(['subject', 'schoolClass'])
            ->withCount(['assignments', 'quizzes'])
            ->latest()
            ->take(6)
            ->get() ?? collect();

        return [
            'cards' => [
                ['label' => 'Kursus aktif', 'value' => $teacher?->courses()->where('status', 'published')->count() ?? 0],
                ['label' => 'Tugas berjalan', 'value' => Assignment::whereIn('course_id', $courses->pluck('id'))->where('is_published', true)->count()],
                ['label' => 'Mata pelajaran', 'value' => $teacher?->subjects()->count() ?? 0],
                ['label' => 'Kelas diajar', 'value' => $courses->pluck('school_class_id')->unique()->count()],
            ],
            'items' => $courses,
            'itemTitle' => 'Kursus yang Anda kelola',
            'empty' => 'Belum ada kursus yang ditugaskan.',
        ];
    }

    private function studentData(?Student $student): array
    {
        $classIds = $student?->schoolClasses()->pluck('school_classes.id') ?? collect();
        $courses = Course::with(['subject', 'teacher.user'])
            ->whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();
        $assignmentCount = Assignment::whereIn('course_id', $courses->pluck('id'))->where('is_published', true)->where('deadline', '>=', now())->count();

        return [
            'cards' => [
                ['label' => 'Kursus aktif', 'value' => $courses->count()],
                ['label' => 'Tugas mendatang', 'value' => $assignmentCount],
                ['label' => 'Nilai tercatat', 'value' => $student?->grades()->count() ?? 0],
                ['label' => 'Kehadiran', 'value' => $student?->attendances()->where('status', 'present')->count() ?? 0],
            ],
            'items' => $courses,
            'itemTitle' => 'Kursus pembelajaran Anda',
            'empty' => 'Belum ada kursus untuk kelas Anda.',
        ];
    }

    private function parentData(?Guardian $guardian): array
    {
        $children = $guardian?->students()->with(['schoolClasses'])->get() ?? collect();

        return [
            'cards' => [
                ['label' => 'Anak terhubung', 'value' => $children->count()],
                ['label' => 'Total presensi', 'value' => $children->sum(fn (Student $student): int => $student->attendances()->count())],
                ['label' => 'Nilai tercatat', 'value' => $children->sum(fn (Student $student): int => $student->grades()->count())],
                ['label' => 'Tugas siswa', 'value' => $children->sum(fn (Student $student): int => $student->submissions()->count())],
            ],
            'items' => $children,
            'itemTitle' => 'Anak yang terhubung',
            'empty' => 'Belum ada anak yang terhubung ke akun ini.',
        ];
    }
}
