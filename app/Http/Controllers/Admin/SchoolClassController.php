<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $academicYearId = $request->get('academic_year_id', $activeYear?->id);

        $classes = SchoolClass::with(['academicYear', 'homeroomTeacher'])
            ->withCount('students')
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(10);

        $academicYears = AcademicYear::orderByDesc('name')->get();
        $teachers = User::role('teacher')->orderBy('name')->get();

        return view('admin.classes.index', compact('classes', 'academicYears', 'teachers', 'academicYearId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'grade_level' => ['required', 'string'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'room' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:5', 'max:50'],
        ]);

        $class = SchoolClass::create($validated);
        AuditLog::log('create_class', $class, null, $class->toArray());

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load(['academicYear', 'homeroomTeacher', 'students.user']);

        // Available students who are not yet assigned to any class in this academic year
        $assignedStudentIds = ClassStudent::where('academic_year_id', $class->academic_year_id)->pluck('student_id');

        $availableStudents = Student::where('is_active', true)
            ->whereNotIn('id', $assignedStudentIds)
            ->orderBy('name')
            ->get();

        return view('admin.classes.show', compact('class', 'availableStudents'));
    }

    public function assignStudent(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
        ]);

        $studentId = $validated['student_id'];

        // Prevent duplicate student membership in the same academic year
        $existing = ClassStudent::where('academic_year_id', $class->academic_year_id)
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            return back()->with('error', 'Siswa tersebut sudah terdaftar di kelas lain pada tahun ajaran ini.');
        }

        if ($class->students()->count() >= $class->capacity) {
            return back()->with('error', 'Kapasitas kelas sudah penuh (Maksimal '.$class->capacity.' siswa).');
        }

        ClassStudent::create([
            'school_class_id' => $class->id,
            'student_id' => $studentId,
            'academic_year_id' => $class->academic_year_id,
        ]);

        AuditLog::log('assign_student_to_class', $class, null, ['student_id' => $studentId]);

        return back()->with('success', 'Siswa berhasil dimasukkan ke dalam kelas.');
    }

    public function removeStudent(SchoolClass $class, Student $student): RedirectResponse
    {
        ClassStudent::where('school_class_id', $class->id)
            ->where('student_id', $student->id)
            ->delete();

        AuditLog::log('remove_student_from_class', $class, ['student_id' => $student->id], null);

        return back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        AuditLog::log('delete_class', $class, $class->toArray(), null);
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
