<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\GradeComponent;
use App\Models\StudentGrade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherGradeController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $courses = $teacher->courses()
            ->with(['subject', 'schoolClass.students.user', 'gradeComponents'])
            ->get();

        $selectedCourseId = (int) $request->input('course_id', $courses->first()?->id);
        $selectedCourse = $courses->firstWhere('id', $selectedCourseId);

        $components = $selectedCourse?->gradeComponents ?? collect();
        $selectedComponentId = (int) $request->input('component_id', $components->first()?->id);
        $selectedComponent = $components->firstWhere('id', $selectedComponentId);

        $students = $selectedCourse?->schoolClass?->students ?? collect();

        $grades = collect();
        if ($selectedCourse && $selectedComponent) {
            $grades = StudentGrade::where('course_id', $selectedCourse->id)
                ->where('grade_component_id', $selectedComponent->id)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.grades.index', compact('courses', 'selectedCourse', 'components', 'selectedComponent', 'students', 'grades'));
    }

    public function storeComponent(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:assignment,quiz,midterm,final,project,practical'],
            'weight' => ['required', 'numeric', 'min:1', 'max:100'],
        ]);

        abort_unless($teacher->courses()->whereKey($validated['course_id'])->exists(), 403);

        $existingWeight = (float) GradeComponent::where('course_id', $validated['course_id'])->sum('weight');
        if (($existingWeight + (float) $validated['weight']) > 100.0) {
            $remaining = max(0, 100.0 - $existingWeight);

            return back()->withInput()->with('error', 'Total bobot komponen nilai tidak boleh melebihi 100%. Sisa bobot yang tersedia untuk kursus ini: '.$remaining.'%.');
        }

        GradeComponent::create($validated);

        return back()->with('success', 'Komponen nilai baru berhasil ditambahkan.');
    }

    public function updateGrades(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'grade_component_id' => ['required', 'exists:grade_components,id'],
            'grades' => ['required', 'array'],
            'grades.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        abort_unless($teacher->courses()->whereKey($validated['course_id'])->exists(), 403);

        DB::transaction(function () use ($validated): void {
            $courseId = $validated['course_id'];
            $componentId = $validated['grade_component_id'];
            $userId = Auth::id();

            foreach ($validated['grades'] as $studentId => $score) {
                if ($score !== null && $score !== '') {
                    StudentGrade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'course_id' => $courseId,
                            'grade_component_id' => $componentId,
                        ],
                        [
                            'score' => $score,
                            'graded_by' => $userId,
                            'graded_at' => now(),
                        ]
                    );
                }
            }
        });

        return back()->with('success', 'Nilai siswa berhasil disimpan.');
    }
}
