<?php

namespace App\Services;

use App\Models\Schedule;

class ScheduleConflictService
{
    /**
     * Check if a schedule has time conflicts.
     *
     * @return array{has_conflict: bool, message: ?string}
     */
    public function checkConflict(
        int $academicYearId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        int $schoolClassId,
        int $teacherId,
        ?int $ignoreScheduleId = null,
        ?string $room = null
    ): array {
        // 1. Validate times
        if ($startTime >= $endTime) {
            return [
                'has_conflict' => true,
                'message' => 'Jam selesai harus lebih akhir daripada jam mulai.',
            ];
        }

        // 2. Check class conflict (same class cannot have two classes at the same time)
        $classConflict = Schedule::where('academic_year_id', $academicYearId)
            ->where('day_of_week', $dayOfWeek)
            ->where('school_class_id', $schoolClassId)
            ->when($ignoreScheduleId, fn ($q) => $q->where('id', '!=', $ignoreScheduleId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->with(['subject', 'teacher.user'])
            ->first();

        if ($classConflict) {
            return [
                'has_conflict' => true,
                'message' => sprintf(
                    'Bentrok jadwal kelas: Kelas ini sudah memiliki jadwal mata pelajaran "%s" (%s - %s) bersama guru %s.',
                    $classConflict->subject?->name ?? 'N/A',
                    substr($classConflict->start_time, 0, 5),
                    substr($classConflict->end_time, 0, 5),
                    $classConflict->teacher?->user?->name ?? 'N/A'
                ),
            ];
        }

        // 3. Check teacher conflict (same teacher cannot teach two classes at the same time)
        $teacherConflict = Schedule::where('academic_year_id', $academicYearId)
            ->where('day_of_week', $dayOfWeek)
            ->where('teacher_id', $teacherId)
            ->when($ignoreScheduleId, fn ($q) => $q->where('id', '!=', $ignoreScheduleId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->with(['schoolClass', 'subject'])
            ->first();

        if ($teacherConflict) {
            return [
                'has_conflict' => true,
                'message' => sprintf(
                    'Bentrok jadwal guru: Guru sudah mengajar di kelas "%s" untuk mata pelajaran "%s" (%s - %s).',
                    $teacherConflict->schoolClass?->name ?? 'N/A',
                    $teacherConflict->subject?->name ?? 'N/A',
                    substr($teacherConflict->start_time, 0, 5),
                    substr($teacherConflict->end_time, 0, 5)
                ),
            ];
        }

        // 4. Check room conflict if a specific room is assigned
        $trimmedRoom = trim($room ?? '');
        if ($trimmedRoom !== '') {
            $roomConflict = Schedule::where('academic_year_id', $academicYearId)
                ->where('day_of_week', $dayOfWeek)
                ->where('room', $trimmedRoom)
                ->when($ignoreScheduleId, fn ($q) => $q->where('id', '!=', $ignoreScheduleId))
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->with(['schoolClass', 'subject'])
                ->first();

            if ($roomConflict) {
                return [
                    'has_conflict' => true,
                    'message' => sprintf(
                        'Bentrok ruangan: Ruangan "%s" sudah dijadwalkan untuk kelas "%s" pada mapel "%s" (%s - %s).',
                        $trimmedRoom,
                        $roomConflict->schoolClass?->name ?? 'N/A',
                        $roomConflict->subject?->name ?? 'N/A',
                        substr($roomConflict->start_time, 0, 5),
                        substr($roomConflict->end_time, 0, 5)
                    ),
                ];
            }
        }

        return [
            'has_conflict' => false,
            'message' => null,
        ];
    }
}
