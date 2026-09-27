<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'submitted_at',
        'is_late',
        'score',
        'graded_at',
        'graded_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
            'score' => 'decimal:2',
            'graded_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SubmissionAttachment::class, 'submission_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(SubmissionFeedback::class, 'submission_id');
    }
}
