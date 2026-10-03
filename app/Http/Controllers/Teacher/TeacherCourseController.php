<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherCourseController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $courses = $teacher->courses()
            ->with(['subject', 'schoolClass.academicYear', 'semester'])
            ->withCount(['chapters', 'assignments', 'quizzes'])
            ->latest()
            ->paginate(12);

        $totalChapters = Chapter::whereIn('course_id', $teacher->courses()->pluck('id'))->count();
        $totalLessons = Lesson::whereIn('topic_id', function ($sub) use ($teacher): void {
            $sub->select('topics.id')
                ->from('topics')
                ->join('chapters', 'topics.chapter_id', '=', 'chapters.id')
                ->whereIn('chapters.course_id', $teacher->courses()->pluck('id'));
        })->count();

        return view('teacher.courses.index', compact('courses', 'totalChapters', 'totalLessons'));
    }

    public function show(Course $course): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id, 403, 'Anda bukan pengampu kursus ini.');

        $course->load([
            'subject',
            'schoolClass.students.user',
            'semester',
            'chapters.topics.lessons.attachments',
        ]);

        $enrolledStudentsCount = $course->schoolClass?->students->count() ?? 0;
        $totalTopicsCount = $course->chapters->flatMap->topics->count();
        $totalLessonsCount = $course->chapters->flatMap->topics->flatMap->lessons->count();

        return view('teacher.courses.show', compact(
            'course',
            'enrolledStudentsCount',
            'totalTopicsCount',
            'totalLessonsCount'
        ));
    }

    public function toggleStatus(Course $course): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id, 403, 'Anda bukan pengampu kursus ini.');

        $newStatus = $course->status === 'published' ? 'draft' : 'published';
        $course->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'published' ? 'dipublikasikan (dapat diakses siswa)' : 'disimpan sebagai draf';

        return back()->with('success', "Status kursus berhasil diubah menjadi {$statusLabel}.");
    }

    public function storeChapter(Request $request, Course $course): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id, 403, 'Anda bukan pengampu kursus ini.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $maxOrder = (int) $course->chapters()->max('order');

        $course->chapters()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Bab baru berhasil ditambahkan ke kurikulum kursus.');
    }

    public function destroyChapter(Course $course, Chapter $chapter): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id && $chapter->course_id === $course->id, 403);

        $chapter->delete();

        return back()->with('success', 'Bab beserta materi di dalamnya berhasil dihapus.');
    }

    public function storeTopic(Request $request, Course $course, Chapter $chapter): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id && $chapter->course_id === $course->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $maxOrder = (int) $chapter->topics()->max('order');

        $chapter->topics()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Topik pembahasan baru berhasil ditambahkan.');
    }

    public function destroyTopic(Course $course, Chapter $chapter, Topic $topic): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id && $chapter->course_id === $course->id && $topic->chapter_id === $chapter->id, 403);

        $topic->delete();

        return back()->with('success', 'Topik pembahasan berhasil dihapus.');
    }

    public function storeLesson(Request $request, Course $course, Topic $topic): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id && $topic->chapter->course_id === $course->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:300'],
            'is_published' => ['required', 'boolean'],
        ]);

        $maxOrder = (int) $topic->lessons()->max('order');

        $topic->lessons()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'estimated_minutes' => $validated['estimated_minutes'],
            'is_published' => $validated['is_published'],
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Materi pembelajaran (lesson) berhasil disimpan.');
    }

    public function destroyLesson(Course $course, Topic $topic, Lesson $lesson): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');
        abort_unless($course->teacher_id === $teacher->id && $topic->chapter->course_id === $course->id && $lesson->topic_id === $topic->id, 403);

        $lesson->delete();

        return back()->with('success', 'Materi pembelajaran berhasil dihapus.');
    }
}
