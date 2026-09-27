<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'attempt_limit',
        'passing_score',
        'is_randomized',
        'violation_threshold',
        'auto_submit_on_violation',
        'starts_at',
        'ends_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'attempt_limit' => 'integer',
            'passing_score' => 'integer',
            'is_randomized' => 'boolean',
            'violation_threshold' => 'integer',
            'auto_submit_on_violation' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot('order')
            ->orderByPivot('order')
            ->withTimestamps();
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
