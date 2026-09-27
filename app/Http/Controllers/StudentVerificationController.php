<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Student;
use Illuminate\View\View;

class StudentVerificationController extends Controller
{
    /**
     * Public student verification page showing minimal non-sensitive information.
     */
    public function verify(string $token): View
    {
        // Token matches student_number
        $student = Student::where('student_number', $token)
            ->with(['schoolClasses.academicYear'])
            ->first();

        $institution = Institution::first();

        return view('verify_student', compact('student', 'token', 'institution'));
    }
}
