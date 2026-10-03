<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ClassStudent;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $classId = $request->get('class_id');
        $status = $request->get('status');

        $activeYear = AcademicYear::where('is_active', true)->first();

        $students = Student::with(['user', 'schoolClasses' => function ($q) use ($activeYear): void {
            if ($activeYear) {
                $q->where('class_students.academic_year_id', $activeYear->id);
            }
        }])
            ->when($search, function ($q, $search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$search}%"));
            })
            ->when($classId, function ($q, $classId): void {
                $q->whereHas('schoolClasses', fn ($cq) => $cq->where('school_classes.id', $classId));
            })
            ->when($status !== null && $status !== '', function ($q) use ($status): void {
                $q->where('is_active', (bool) $status);
            })
            ->orderBy('student_number')
            ->paginate(10);

        $classes = SchoolClass::when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students', 'classes', 'search', 'classId', 'status', 'activeYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'unique:students,student_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'class_id' => ['nullable', 'exists:school_classes,id'],
            'guardian_name' => ['nullable', 'required_with:guardian_email', 'string', 'max:255'],
            'guardian_email' => ['nullable', 'required_with:guardian_name', 'email', 'unique:users,email'],
            'guardian_password' => ['nullable', 'required_with:guardian_email', 'string', 'min:8'],
            'guardian_phone' => ['nullable', 'string', 'max:50'],
            'guardian_relationship' => ['nullable', 'required_with:guardian_email', 'in:father,mother,guardian'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        if (! empty($validated['class_id'])) {
            $schoolClass = SchoolClass::whereKey($validated['class_id'])
                ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
                ->first();
            abort_unless($activeYear && $schoolClass, 422, 'Kelas harus berasal dari tahun ajaran aktif.');
            abort_if($schoolClass->students()->count() >= $schoolClass->capacity, 422, 'Kapasitas kelas yang dipilih sudah penuh.');
        }

        DB::transaction(function () use ($validated, $activeYear, &$student): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $studentRole = Role::firstOrCreate(['name' => 'student']);
            $user->assignRole($studentRole);

            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $validated['student_number'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'place_of_birth' => $validated['place_of_birth'] ?? null,
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            if (! empty($validated['class_id']) && $activeYear) {
                ClassStudent::create([
                    'school_class_id' => $validated['class_id'],
                    'student_id' => $student->id,
                    'academic_year_id' => $activeYear->id,
                ]);
            }

            if (! empty($validated['guardian_email'])) {
                $parentUser = User::create([
                    'name' => $validated['guardian_name'],
                    'email' => $validated['guardian_email'],
                    'password' => Hash::make($validated['guardian_password']),
                ]);
                $parentUser->assignRole(Role::firstOrCreate(['name' => 'parent']));
                $guardian = Guardian::create([
                    'user_id' => $parentUser->id,
                    'name' => $validated['guardian_name'],
                    'phone' => $validated['guardian_phone'] ?? null,
                    'relationship' => $validated['guardian_relationship'],
                    'is_active' => true,
                ]);
                $guardian->students()->attach($student->id);
            }
        });

        AuditLog::log('create_student', $student, null, $student->toArray());

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        $student->load([
            'user',
            'guardians.user',
            'schoolClasses' => fn ($q) => $q->where('class_students.academic_year_id', $activeYear?->id),
            'attendances.session.subject',
            'grades.course.subject',
            'grades.gradeComponent',
            'lessonProgress.lesson.topic.chapter.course',
        ]);

        // Attendance Summary
        $attendanceStats = [
            'present' => $student->attendances->where('status', 'present')->count(),
            'sick' => $student->attendances->where('status', 'sick')->count(),
            'permission' => $student->attendances->where('status', 'permission')->count(),
            'absent' => $student->attendances->where('status', 'absent')->count(),
        ];

        // LMS progress calculation
        $totalLessons = $student->lessonProgress->count();
        $completedLessons = $student->lessonProgress->where('progress_percentage', 100)->count();

        // Verification token for Digital KTS QR Code (opaque base64 hash)
        $verificationToken = base64_encode(hash_hmac('sha256', $student->student_number, config('app.key')));
        $verificationUrl = route('verify.student', ['token' => $student->student_number]);

        return view('admin.students.show', compact('student', 'attendanceStats', 'totalLessons', 'completedLessons', 'verificationUrl'));
    }

    public function toggleStatus(Student $student): RedirectResponse
    {
        $student->update(['is_active' => ! $student->is_active]);

        AuditLog::log('toggle_student_status', $student, null, ['is_active' => $student->is_active]);

        return back()->with('success', 'Status keaktifan siswa berhasil diubah.');
    }
}
