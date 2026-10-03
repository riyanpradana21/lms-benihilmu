<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Media;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ContentMediaController extends Controller
{
    public function show(Media $media): Response
    {
        abort_unless($this->canView($media), 403);
        abort_unless(Storage::disk($media->disk)->exists($media->path), 404);

        $mime = $media->mime_type ?? 'application/octet-stream';
        abort_unless(str_starts_with($mime, 'image/') || str_starts_with($mime, 'video/'), 415);

        return Storage::disk($media->disk)->response($media->path, basename($media->filename), [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.preg_replace('/[^A-Za-z0-9._-]/', '_', basename($media->filename)).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function canView(Media $media): bool
    {
        $user = Auth::user();
        if ($user->hasRole(['super_admin', 'admin'])) {
            return true;
        }

        $resource = $media->model;
        if ($resource instanceof Question) {
            $resource->loadMissing('questionBank');
            if ($user->hasRole('teacher')) {
                return $resource->questionBank?->created_by === $user->id
                    || ($resource->questionBank?->subject_id && $user->teacher?->subjects()->whereKey($resource->questionBank->subject_id)->exists());
            }

            return $this->studentCanSeeQuestion($resource);
        }

        $assignment = $resource instanceof Assignment ? $resource : ($resource instanceof AssignmentSubmission ? $resource->assignment : null);
        if (! $assignment) {
            return false;
        }
        $assignment->loadMissing('course');

        if ($user->hasRole('teacher')) {
            return $user->teacher && $assignment->course?->teacher_id === $user->teacher->id;
        }
        if ($resource instanceof AssignmentSubmission) {
            return $this->studentCanSeeSubmission($resource);
        }

        return $this->studentCanSeeAssignment($assignment);
    }

    private function studentCanSeeQuestion(Question $question): bool
    {
        $studentIds = $this->visibleStudentIds();
        $classIds = $this->visibleClassIds();
        if ($studentIds->isEmpty() || $classIds->isEmpty()) {
            return false;
        }

        return Quiz::where('is_published', true)
            ->whereHas('questions', fn ($query) => $query->where('questions.id', $question->id))
            ->whereHas('course', fn ($query) => $query->where('status', 'published')->whereIn('school_class_id', $classIds))
            ->exists();
    }

    private function studentCanSeeAssignment(Assignment $assignment): bool
    {
        return $assignment->is_published && in_array($assignment->course?->school_class_id, $this->visibleClassIds()->all());
    }

    private function studentCanSeeSubmission(AssignmentSubmission $submission): bool
    {
        $studentIds = $this->visibleStudentIds();

        return $studentIds->contains($submission->student_id)
            && $this->studentCanSeeAssignment($submission->assignment);
    }

    private function visibleStudentIds(): Collection
    {
        $user = Auth::user();
        if ($user->hasRole('student')) {
            return collect([$user->student?->id])->filter();
        }
        if ($user->hasRole('parent')) {
            return $user->guardian?->students()->pluck('students.id') ?? collect();
        }

        return collect();
    }

    private function visibleClassIds(): Collection
    {
        $user = Auth::user();
        $studentIds = $this->visibleStudentIds();
        if ($studentIds->isEmpty()) {
            return collect();
        }

        return Student::whereIn('id', $studentIds)->with('schoolClasses')->get()
            ->flatMap(fn ($student) => $student->schoolClasses->pluck('id'))->unique()->values();
    }
}
