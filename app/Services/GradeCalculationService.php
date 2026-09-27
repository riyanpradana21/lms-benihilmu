<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Support\Collection;

class GradeCalculationService
{
    /**
     * Calculate student final grade for a specific course.
     *
     * @return array{
     *     final_score: float,
     *     letter_grade: string,
     *     components: Collection,
     *     total_weight: float
     * }
     */
    public function calculateForStudent(Course $course, Student $student): array
    {
        $components = $course->gradeComponents()->get();
        $totalWeight = (float) $components->sum('weight');

        $grades = StudentGrade::where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('grade_component_id');

        $weightedTotal = 0.0;
        $componentDetails = collect();

        foreach ($components as $component) {
            $score = isset($grades[$component->id]) ? (float) $grades[$component->id]->score : 0.0;
            $weight = (float) $component->weight;
            $weightedContribution = ($score * $weight) / 100.0;

            $weightedTotal += $weightedContribution;

            $componentDetails->push([
                'id' => $component->id,
                'name' => $component->name,
                'type' => $component->type,
                'weight' => $weight,
                'score' => $score,
                'weighted_score' => round($weightedContribution, 2),
            ]);
        }

        $finalScore = round($weightedTotal, 2);
        $letterGrade = $this->determineLetterGrade($finalScore);

        return [
            'final_score' => $finalScore,
            'letter_grade' => $letterGrade,
            'components' => $componentDetails,
            'total_weight' => $totalWeight,
        ];
    }

    /**
     * Convert numeric grade to letter grade standard (Indonesian High School / A-B-C-D-E).
     */
    public function determineLetterGrade(float $score): string
    {
        if ($score >= 88.0) {
            return 'A';
        }

        if ($score >= 76.0) {
            return 'B';
        }

        if ($score >= 60.0) {
            return 'C';
        }

        if ($score >= 45.0) {
            return 'D';
        }

        return 'E';
    }
}
