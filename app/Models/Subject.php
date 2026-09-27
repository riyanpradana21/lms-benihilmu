<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'curriculum_id',
        'credits',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
        ];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects')
            ->withPivot('academic_year_id')
            ->withTimestamps();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
