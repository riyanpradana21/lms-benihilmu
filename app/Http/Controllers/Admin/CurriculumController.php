<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Curriculum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(): View
    {
        $curriculums = Curriculum::withCount('subjects')->paginate(10);

        return view('admin.curriculums.index', compact('curriculums'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'year' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $curriculum = Curriculum::create([
            'name' => $validated['name'],
            'year' => $validated['year'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLog::log('create_curriculum', $curriculum, null, $curriculum->toArray());

        return redirect()->route('admin.curriculums.index')->with('success', 'Kurikulum berhasil ditambahkan.');
    }
}
