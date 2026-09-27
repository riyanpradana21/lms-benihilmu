<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentKtsController extends Controller
{
    public function show(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Profil siswa tidak ditemukan.');

        $student->load(['schoolClasses.academicYear']);
        $schoolClass = $student->schoolClasses->first();
        $institution = Institution::first();

        $verificationUrl = route('verify.student', ['token' => $student->student_number]);

        // Generate clean local SVG QR code
        $qrCodeSvg = QrCode::size(130)->margin(1)->generate($verificationUrl);

        return view('student.kts', compact('student', 'schoolClass', 'institution', 'verificationUrl', 'qrCodeSvg'));
    }
}
