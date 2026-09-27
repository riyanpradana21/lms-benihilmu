<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ParentPortalController extends Controller
{
    public function children(): View
    {
        $guardian = $this->guardian();
        $children = $guardian->students()
            ->with(['schoolClasses.academicYear', 'user'])
            ->get();

        return view('parent.children', compact('children'));
    }

    public function attendance(Request $request): View
    {
        $guardian = $this->guardian();
        $children = $guardian->students()->with('schoolClasses')->get();
        abort_if($children->isEmpty(), 403, 'Belum ada anak yang terhubung ke akun Anda.');

        $selectedChild = $this->resolveSelectedChild($guardian, $request->input('child_id'));

        $attendances = Attendance::with(['session.subject', 'session.teacher.user'])
            ->where('student_id', $selectedChild->id)
            ->latest()
            ->paginate(15);

        $summary = [
            'present' => Attendance::where('student_id', $selectedChild->id)->where('status', 'present')->count(),
            'sick' => Attendance::where('student_id', $selectedChild->id)->where('status', 'sick')->count(),
            'permission' => Attendance::where('student_id', $selectedChild->id)->where('status', 'permission')->count(),
            'absent' => Attendance::where('student_id', $selectedChild->id)->where('status', 'absent')->count(),
        ];

        return view('parent.attendance', compact('children', 'selectedChild', 'attendances', 'summary'));
    }

    public function grades(Request $request): View
    {
        $guardian = $this->guardian();
        $children = $guardian->students()->with('schoolClasses')->get();
        abort_if($children->isEmpty(), 403, 'Belum ada anak yang terhubung ke akun Anda.');

        $selectedChild = $this->resolveSelectedChild($guardian, $request->input('child_id'));

        $grades = StudentGrade::with(['course.subject', 'gradeComponent', 'grader'])
            ->where('student_id', $selectedChild->id)
            ->latest('graded_at')
            ->paginate(15);

        return view('parent.grades', compact('children', 'selectedChild', 'grades'));
    }

    public function assignments(Request $request): View
    {
        $guardian = $this->guardian();
        $children = $guardian->students()->with('schoolClasses')->get();
        abort_if($children->isEmpty(), 403, 'Belum ada anak yang terhubung ke akun Anda.');

        $selectedChild = $this->resolveSelectedChild($guardian, $request->input('child_id'));

        $classIds = $selectedChild->schoolClasses()->pluck('school_classes.id');
        $courseIds = Course::whereIn('school_class_id', $classIds)->pluck('id');

        $assignments = Assignment::with([
            'course.subject',
            'submissions' => fn ($q) => $q->where('student_id', $selectedChild->id),
        ])
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->latest('deadline')
            ->paginate(12);

        return view('parent.assignments', compact('children', 'selectedChild', 'assignments'));
    }

    public function courses(Request $request): View
    {
        $guardian = $this->guardian();
        $children = $guardian->students()->with('schoolClasses')->get();
        abort_if($children->isEmpty(), 403, 'Belum ada anak yang terhubung ke akun Anda.');

        $selectedChild = $this->resolveSelectedChild($guardian, $request->input('child_id'));

        $classIds = $selectedChild->schoolClasses()->pluck('school_classes.id');
        $courses = Course::with(['subject', 'teacher.user', 'chapters'])
            ->whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('parent.courses', compact('children', 'selectedChild', 'courses'));
    }

    private function guardian(): Guardian
    {
        $guardian = Auth::user()->guardian;
        abort_unless($guardian, 403, 'Profil orang tua / wali tidak ditemukan.');

        return $guardian;
    }

    private function resolveSelectedChild(Guardian $guardian, ?string $childId): Student
    {
        if ($childId) {
            // Strict authorization check: verify parent-child relationship server-side!
            $child = $guardian->students()->where('students.id', $childId)->first();
            abort_unless($child, 403, 'Anda tidak memiliki hak akses ke data siswa ini.');

            return $child;
        }

        $firstChild = $guardian->students()->first();
        abort_unless($firstChild, 403, 'Tidak ada anak yang terhubung ke akun Anda.');

        return $firstChild;
    }
}
