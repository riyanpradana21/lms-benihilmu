<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'expires_at',
        'submitted_at',
        'score',
        'is_auto_submitted',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'is_auto_submitted' => 'boolean',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(QuizViolation::class, 'attempt_id');
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function remainingSeconds(): int
    {
        if ($this->status !== 'in_progress' || $this->isExpired()) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }
}
