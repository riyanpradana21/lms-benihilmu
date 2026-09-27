<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAttachment extends Model
{
    protected $fillable = [
        'lesson_id',
        'filename',
        'disk',
        'path',
        'mime_type',
        'size',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'order' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
