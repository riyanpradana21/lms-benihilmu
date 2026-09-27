<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::withCount(['semesters', 'schoolClasses'])
            ->latest('start_date')
            ->paginate(10);

        return view('admin.academic_years.index', compact('academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        DB::transaction(function () use ($validated, $isActive, &$academicYear): void {
            if ($isActive) {
                AcademicYear::where('is_active', true)->update(['is_active' => false]);
            }

            $academicYear = AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $isActive,
            ]);

            // Automatically create two default semesters: Ganjil and Genap
            $academicYear->semesters()->create([
                'name' => 'Ganjil',
                'start_date' => $validated['start_date'],
                'end_date' => date('Y-12-20', strtotime($validated['start_date'])),
                'is_active' => $isActive,
            ]);

            $academicYear->semesters()->create([
                'name' => 'Genap',
                'start_date' => date('Y-01-05', strtotime($validated['end_date'])),
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);
        });

        AuditLog::log('create_academic_year', $academicYear, null, $academicYear->toArray());

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan beserta semester Ganjil & Genap.');
    }

    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        DB::transaction(function () use ($academicYear): void {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
        });

        AuditLog::log('activate_academic_year', $academicYear);

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran '.$academicYear->name.' telah diaktifkan.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        AuditLog::log('delete_academic_year', $academicYear, $academicYear->toArray(), null);
        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
