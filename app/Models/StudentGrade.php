<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'grade_component_id',
        'score',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function gradeComponent(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
