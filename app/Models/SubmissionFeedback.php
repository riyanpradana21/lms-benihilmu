<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFeedback extends Model
{
    protected $fillable = [
        'submission_id',
        'feedback',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }
}
