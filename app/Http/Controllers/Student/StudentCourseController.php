<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\StudentNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentCourseController extends Controller
{
    public function show(Course $course): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');

        $isEnrolled = $student->schoolClasses()->where('school_classes.id', $course->school_class_id)->exists();
        abort_unless($isEnrolled, 403, 'Anda tidak terdaftar di kelas untuk kursus ini.');

        $course->load([
            'subject',
            'teacher.user',
            'chapters.topics.lessons' => function ($query) use ($student): void {
                $query->with(['progress' => fn ($q) => $q->where('student_id', $student->id)])
                    ->where('is_published', true)
                    ->orderBy('order');
            },
        ]);

        $totalLessons = 0;
        $completedLessons = 0;

        foreach ($course->chapters as $chapter) {
            foreach ($chapter->topics as $topic) {
                foreach ($topic->lessons as $lesson) {
                    $totalLessons++;
                    if ($lesson->progress->first()?->completed_at) {
                        $completedLessons++;
                    }
                }
            }
        }

        $progressPercentage = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

        return view('student.courses.show', compact('course', 'totalLessons', 'completedLessons', 'progressPercentage'));
    }

    public function lessonShow(Course $course, Lesson $lesson): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');

        // Validate course enrollment
        $isEnrolled = $student->schoolClasses()->where('school_classes.id', $course->school_class_id)->exists();
        abort_unless($isEnrolled, 403, 'Anda tidak terdaftar di kelas untuk kursus ini.');

        // Verify lesson belongs to course
        $lesson->load(['topic.chapter', 'attachments']);
        abort_unless($lesson->topic?->chapter?->course_id === $course->id, 404, 'Materi tidak ditemukan dalam kursus ini.');

        // Track or create initial progress
        $progress = LessonProgress::firstOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            ['started_at' => now(), 'progress_percentage' => 10]
        );

        $isBookmarked = Bookmark::where('student_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        $notes = StudentNote::where('student_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->latest()
            ->get();

        // Get all lessons for navigation and table of contents
        $course->load(['chapters.topics.lessons' => fn ($q) => $q->where('is_published', true)->orderBy('order')]);

        $allLessons = collect();
        foreach ($course->chapters as $ch) {
            foreach ($ch->topics as $tp) {
                foreach ($tp->lessons as $ls) {
                    $allLessons->push($ls);
                }
            }
        }

        $currentIndex = $allLessons->search(fn ($l): bool => $l->id === $lesson->id);
        $prevLesson = $currentIndex !== false && $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false && $currentIndex < ($allLessons->count() - 1) ? $allLessons->get($currentIndex + 1) : null;

        return view('student.lessons.show', compact('course', 'lesson', 'progress', 'isBookmarked', 'notes', 'prevLesson', 'nextLesson'));
    }

    public function completeLesson(Lesson $lesson): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $lesson->load('topic.chapter.course');
        $course = $lesson->topic->chapter->course;
        abort_unless($student->schoolClasses()->where('school_classes.id', $course->school_class_id)->exists(), 403);

        LessonProgress::updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            [
                'completed_at' => now(),
                'progress_percentage' => 100,
            ]
        );

        return back()->with('success', 'Materi ditandai telah selesai dipelajari.');
    }

    public function bookmarkLesson(Lesson $lesson): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $bookmark = Bookmark::where('student_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $msg = 'Bookmark berhasil dihapus.';
        } else {
            Bookmark::create([
                'student_id' => $student->id,
                'lesson_id' => $lesson->id,
            ]);
            $msg = 'Materi berhasil disimpan ke bookmark.';
        }

        return back()->with('success', $msg);
    }

    public function noteLesson(Request $request, Lesson $lesson): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        StudentNote::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson->id,
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Catatan pribadi berhasil disimpan.');
    }
}
