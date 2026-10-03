<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $canManage = $this->canManage();
        $isAdmin = Auth::user()->hasRole(['super_admin', 'admin']);

        $announcements = Announcement::with('author')
            ->when(! $isAdmin, fn ($query) => $query->where(function ($query) use ($canManage): void {
                $query->where(fn ($published) => $published->whereNotNull('published_at')->where('published_at', '<=', now())
                    ->where(fn ($active) => $active->whereNull('expires_at')->orWhere('expires_at', '>', now())));
                if ($canManage) {
                    $query->orWhere('author_id', Auth::id());
                }
            }))
            ->latest('published_at')
            ->paginate(12);

        return view('announcements.index', compact('announcements', 'canManage'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManage(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
        ]);

        Announcement::create([
            ...$validated,
            'author_id' => Auth::id(),
            'published_at' => $validated['published_at'] ?? now(),
        ]);

        return back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($this->canEdit($announcement), 403);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:published_at'],
        ]);
        $announcement->update($validated);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        abort_unless($this->canEdit($announcement), 403);
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function canManage(): bool
    {
        $user = Auth::user();

        return $user->hasRole(['super_admin', 'admin'])
            || ($user->hasRole('teacher') && SchoolClass::where('homeroom_teacher_id', $user->id)->exists());
    }

    private function canEdit(Announcement $announcement): bool
    {
        $user = Auth::user();

        return $user->hasRole(['super_admin', 'admin'])
            || ($announcement->author_id === $user->id && $this->canManage());
    }
}
