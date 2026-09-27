<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::with('academicYear')
            ->latest('start_date')
            ->paginate(10);

        $academicYears = AcademicYear::orderByDesc('name')->get();

        return view('admin.semesters.index', compact('semesters', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        DB::transaction(function () use ($validated, $isActive, &$semester): void {
            if ($isActive) {
                Semester::where('is_active', true)->update(['is_active' => false]);
            }

            $semester = Semester::create([
                'academic_year_id' => $validated['academic_year_id'],
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $isActive,
            ]);
        });

        AuditLog::log('create_semester', $semester, null, $semester->toArray());

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function activate(Semester $semester): RedirectResponse
    {
        DB::transaction(function () use ($semester): void {
            Semester::where('is_active', true)->update(['is_active' => false]);
            $semester->update(['is_active' => true]);
        });

        AuditLog::log('activate_semester', $semester);

        return redirect()->route('admin.semesters.index')->with('success', 'Semester '.$semester->name.' telah diaktifkan.');
    }
}
