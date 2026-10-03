<?php

namespace App\Http\Controllers;

use App\Models\ClassStudent;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExamScheduleController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($this->canManage(), 403);

        $classes = $this->manageableClasses()->with('academicYear')->orderBy('name')->get();
        $classId = $request->integer('school_class_id') ?: $classes->first()?->id;
        abort_if($classId && ! $classes->contains('id', $classId), 403);

        $students = $classId
            ? Student::query()->whereHas('schoolClasses', fn ($query) => $query->where('school_classes.id', $classId))->orderBy('name')->get()
            : collect();
        $examSchedules = ExamSchedule::with(['schoolClass', 'student.user', 'subject', 'creator'])
            ->whereIn('school_class_id', $classes->pluck('id'))
            ->when($classId, fn ($query) => $query->where('school_class_id', $classId))
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $examTypes = $this->examTypes();

        return view('exam-schedules.manage', compact('classes', 'classId', 'students', 'examSchedules', 'subjects', 'examTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManage(), 403);
        $validated = $this->validateSchedule($request);
        $this->assertStudentIsInClass($validated['school_class_id'], $validated['student_id']);
        $this->assertNoStudentConflict($validated['student_id'], $validated['starts_at'], $validated['ends_at']);

        ExamSchedule::create([...$validated, 'created_by' => Auth::id()]);

        return back()->with('success', 'Jadwal ujian siswa berhasil ditambahkan.');
    }

    public function update(Request $request, ExamSchedule $examSchedule): RedirectResponse
    {
        abort_unless($this->canManageClass($examSchedule->school_class_id), 403);
        $validated = $this->validateSchedule($request);
        $this->assertStudentIsInClass($validated['school_class_id'], $validated['student_id']);
        $this->assertNoStudentConflict($validated['student_id'], $validated['starts_at'], $validated['ends_at'], $examSchedule->id);
        $examSchedule->update($validated);

        return back()->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(ExamSchedule $examSchedule): RedirectResponse
    {
        abort_unless($this->canManageClass($examSchedule->school_class_id), 403);
        $examSchedule->delete();

        return back()->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    public function studentIndex(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);
        $examSchedules = ExamSchedule::with(['schoolClass', 'subject'])
            ->where('student_id', $student->id)
            ->orderBy('starts_at')
            ->paginate(15);

        return view('exam-schedules.student', compact('examSchedules'));
    }

    public function parentIndex(Request $request): View
    {
        $guardian = Auth::user()->guardian;
        abort_unless($guardian, 403);
        $children = $guardian->students()->with('schoolClasses')->orderBy('name')->get();
        abort_if($children->isEmpty(), 403, 'Belum ada siswa yang ditautkan ke akun orang tua ini.');
        $childId = $request->integer('child_id') ?: $children->first()->id;
        $child = $children->firstWhere('id', $childId);
        abort_unless($child, 403, 'Siswa tersebut tidak tertaut ke akun Anda.');

        $examSchedules = ExamSchedule::with(['schoolClass', 'subject'])
            ->where('student_id', $child->id)
            ->orderBy('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('exam-schedules.parent', compact('children', 'child', 'examSchedules'));
    }

    private function validateSchedule(Request $request): array
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'exam_type' => ['required', 'in:practical,school_exam,pts,pas'],
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'room' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['starts_at'] = Carbon::parse($validated['starts_at'])->format('Y-m-d H:i:s');
        $validated['ends_at'] = Carbon::parse($validated['ends_at'])->format('Y-m-d H:i:s');

        return $validated;
    }

    private function manageableClasses(): Builder
    {
        $query = SchoolClass::query();
        if (! Auth::user()->hasRole(['super_admin', 'admin'])) {
            $query->where('homeroom_teacher_id', Auth::id());
        }

        return $query;
    }

    private function canManage(): bool
    {
        return Auth::user()->hasRole(['super_admin', 'admin'])
            || (Auth::user()->hasRole('teacher') && $this->manageableClasses()->exists());
    }

    private function canManageClass(int $classId): bool
    {
        return $this->canManage() && $this->manageableClasses()->whereKey($classId)->exists();
    }

    private function assertStudentIsInClass(int $classId, int $studentId): void
    {
        abort_unless($this->manageableClasses()->whereKey($classId)->exists(), 403);
        abort_unless(ClassStudent::where('school_class_id', $classId)->where('student_id', $studentId)->exists(), 422, 'Siswa harus terdaftar pada kelas yang dipilih.');
    }

    private function assertNoStudentConflict(int $studentId, string $startsAt, string $endsAt, ?int $exceptId = null): void
    {
        $conflict = ExamSchedule::where('student_id', $studentId)
            ->when($exceptId, fn ($query) => $query->where('id', '!=', $exceptId))
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();

        abort_if($conflict, 422, 'Jadwal siswa berbenturan dengan ujian lain.');
    }

    private function examTypes(): array
    {
        return ['practical' => 'Ujian Praktik', 'school_exam' => 'Ujian Sekolah', 'pts' => 'PTS', 'pas' => 'PAS'];
    }
}
