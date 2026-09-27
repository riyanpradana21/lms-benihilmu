<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'content',
        'order',
        'is_published',
        'estimated_minutes',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_published' => 'boolean',
            'estimated_minutes' => 'integer',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LessonAttachment::class)->orderBy('order');
    }

    public function progress(): HasMany
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
}
