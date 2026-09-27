<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeComponent extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'type',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }
}
