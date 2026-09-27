<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $teachers = Teacher::with(['user', 'subjects'])
            ->when($search, function ($q, $search): void {
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhere('employee_number', 'like', "%{$search}%");
            })
            ->when($status !== null && $status !== '', function ($q) use ($status): void {
                $q->where('is_active', (bool) $status);
            })
            ->latest()
            ->paginate(10);

        $subjects = Subject::orderBy('name')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('admin.teachers.index', compact('teachers', 'subjects', 'activeYear', 'search', 'status'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'employee_number' => ['nullable', 'string', 'unique:teachers,employee_number'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'gender' => ['required', 'in:male,female'],
            'specialization' => ['nullable', 'string'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();

        DB::transaction(function () use ($validated, $activeYear, &$teacher): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
            $user->assignRole($teacherRole);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_number' => $validated['employee_number'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'],
                'specialization' => $validated['specialization'] ?? null,
                'is_active' => true,
            ]);

            if (! empty($validated['subject_ids']) && $activeYear) {
                $syncData = [];
                foreach ($validated['subject_ids'] as $subjectId) {
                    $syncData[$subjectId] = ['academic_year_id' => $activeYear->id];
                }
                $teacher->subjects()->sync($syncData);
            }
        });

        AuditLog::log('create_teacher', $teacher, null, $teacher->toArray());

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function toggleStatus(Teacher $teacher): RedirectResponse
    {
        $teacher->update(['is_active' => ! $teacher->is_active]);

        AuditLog::log('toggle_teacher_status', $teacher, null, ['is_active' => $teacher->is_active]);

        return back()->with('success', 'Status keaktifan guru berhasil diubah.');
    }

    public function updateSubjects(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $syncData = [];
        if (! empty($validated['subject_ids'])) {
            foreach ($validated['subject_ids'] as $subjectId) {
                $syncData[$subjectId] = ['academic_year_id' => $activeYear->id];
            }
        }

        $teacher->subjects()->sync($syncData);

        AuditLog::log('update_teacher_subjects', $teacher, null, $validated);

        return back()->with('success', 'Penugasan mata pelajaran guru berhasil diperbarui.');
    }
}
