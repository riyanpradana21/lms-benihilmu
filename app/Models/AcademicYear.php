<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
