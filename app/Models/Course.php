<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'school_class_id',
        'semester_id',
        'teacher_id',
        'status',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function gradeComponents(): HasMany
    {
        return $this->hasMany(GradeComponent::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }
}
