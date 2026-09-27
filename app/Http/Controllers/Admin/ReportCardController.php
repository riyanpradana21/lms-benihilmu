<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Institution;
use App\Models\ReportCard;
use App\Models\StudentGrade;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(): View
    {
        $reportCards = ReportCard::with(['student.user', 'schoolClass', 'semester.academicYear'])
            ->latest()
            ->paginate(15);

        return view('admin.report_cards.index', compact('reportCards'));
    }

    public function show(ReportCard $reportCard): View
    {
        $reportCard->load(['student.user', 'schoolClass.homeroomTeacher', 'semester.academicYear']);
        $institution = Institution::first();
        $student = $reportCard->student;

        // Fetch subjects and grades for this student's class
        $courses = Course::with(['subject', 'teacher.user', 'gradeComponents'])
            ->where('school_class_id', $reportCard->school_class_id)
            ->where('semester_id', $reportCard->semester_id)
            ->get();

        $grades = StudentGrade::where('student_id', $student->id)
            ->whereIn('course_id', $courses->pluck('id'))
            ->get()
            ->groupBy('course_id');

        // Calculate subject final scores based on weights
        $subjectScores = [];
        $totalSum = 0;
        $courseCount = 0;

        foreach ($courses as $course) {
            $courseGrades = $grades->get($course->id, collect());
            $components = $course->gradeComponents;

            $weightedScore = 0;
            $totalWeight = 0;

            foreach ($components as $comp) {
                $g = $courseGrades->firstWhere('grade_component_id', $comp->id);
                if ($g) {
                    $weightedScore += ($g->score * ($comp->weight / 100));
                    $totalWeight += $comp->weight;
                }
            }

            $finalSubjectScore = $totalWeight > 0 ? round(($weightedScore / ($totalWeight / 100)), 1) : ($courseGrades->avg('score') ?: 80);
            $predicate = $finalSubjectScore >= 88 ? 'A (Sangat Baik)' : ($finalSubjectScore >= 78 ? 'B (Baik)' : ($finalSubjectScore >= 68 ? 'C (Cukup)' : 'D (Kurang)'));

            $subjectScores[] = [
                'code' => $course->subject?->code,
                'name' => $course->subject?->name,
                'teacher' => $course->teacher?->user?->name ?? 'Guru Mapel',
                'kkm' => 75,
                'score' => $finalSubjectScore,
                'predicate' => $predicate,
            ];

            $totalSum += $finalSubjectScore;
            $courseCount++;
        }

        $averageScore = $courseCount > 0 ? round($totalSum / $courseCount, 1) : $reportCard->gpa;

        // Attendance stats
        $attendances = Attendance::where('student_id', $student->id)->get();
        $attendanceSummary = [
            'sick' => $attendances->where('status', 'sick')->count(),
            'permission' => $attendances->where('status', 'permission')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        return view('admin.report_cards.show', compact('reportCard', 'institution', 'student', 'subjectScores', 'averageScore', 'attendanceSummary'));
    }
}
