<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_number',
        'name',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'address',
        'phone',
        'is_active',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'parent_student', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_students')
            ->withPivot('academic_year_id')
            ->withTimestamps();
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(StudentNote::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }
}
