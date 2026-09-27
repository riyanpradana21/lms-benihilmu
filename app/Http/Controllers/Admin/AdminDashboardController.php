<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        $stats = [
            'total_students' => Student::where('is_active', true)->count(),
            'total_teachers' => Teacher::where('is_active', true)->count(),
            'total_classes' => SchoolClass::count(),
            'total_subjects' => Subject::count(),
            'total_courses' => Course::count(),
            'pending_grading' => AssignmentSubmission::where('status', 'submitted')->count(),
            'attendance_today' => Attendance::whereDate('created_at', now()->toDateString())->count(),
        ];

        $recentClasses = SchoolClass::with(['homeroomTeacher', 'academicYear'])
            ->withCount('students')
            ->latest()
            ->take(5)
            ->get();

        $recentCourses = Course::with(['subject', 'schoolClass', 'teacher.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'activeYear', 'activeSemester', 'recentClasses', 'recentCourses'));
    }
}
