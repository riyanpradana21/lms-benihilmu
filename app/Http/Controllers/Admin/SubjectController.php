<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Curriculum;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('curriculum')
            ->withCount(['teachers', 'courses'])
            ->orderBy('code')
            ->paginate(10);

        $curriculums = Curriculum::all();

        return view('admin.subjects.index', compact('subjects', 'curriculums'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:subjects,code'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'curriculum_id' => ['nullable', 'exists:curriculums,id'],
            'credits' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $subject = Subject::create($validated);

        AuditLog::log('create_subject', $subject, null, $subject->toArray());

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:subjects,code,'.$subject->id],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'curriculum_id' => ['nullable', 'exists:curriculums,id'],
            'credits' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $old = $subject->toArray();
        $subject->update($validated);

        AuditLog::log('update_subject', $subject, $old, $subject->toArray());

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        AuditLog::log('delete_subject', $subject, $subject->toArray(), null);
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
