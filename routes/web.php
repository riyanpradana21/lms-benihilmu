<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminModuleController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\Parent\ParentPortalController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\StudentKtsController;
use App\Http\Controllers\Student\StudentQuizController;
use App\Http\Controllers\StudentVerificationController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherGradeController;
use App\Http\Controllers\Teacher\TeacherQuizController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/verify/student/{token}', [StudentVerificationController::class, 'verify'])
    ->name('verify.student');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Super Admin & Admin
    Route::middleware('role:super_admin|admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::patch('/academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
        Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

        Route::resource('semesters', SemesterController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/semesters/{semester}/activate', [SemesterController::class, 'activate'])->name('semesters.activate');
        Route::resource('curriculums', CurriculumController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('subjects', SubjectController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('classes', SchoolClassController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::post('/classes/{class}/students', [SchoolClassController::class, 'assignStudent'])->name('classes.students.store');
        Route::delete('/classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])->name('classes.students.destroy');
        Route::resource('schedules', ScheduleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('teachers', TeacherController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('/teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
        Route::put('/teachers/{teacher}/subjects', [TeacherController::class, 'updateSubjects'])->name('teachers.subjects.update');
        Route::resource('students', StudentController::class)->only(['index', 'store', 'show']);
        Route::patch('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');

        Route::get('/report-cards', [ReportCardController::class, 'index'])->name('report-cards.index');
        Route::get('/report-cards/{reportCard}', [ReportCardController::class, 'show'])->name('report-cards.show');

        foreach (['users', 'courses', 'attendance', 'grades', 'audit-logs'] as $module) {
            Route::get('/'.$module, [AdminModuleController::class, 'index'])->defaults('module', $module)
                ->name($module.'.index');
        }
    });

    // Teacher
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function (): void {
        Route::get('/dashboard', [RoleDashboardController::class, 'index'])->defaults('role', 'teacher')->name('dashboard');
        Route::get('/courses', [LearningController::class, 'teacherCourses'])->name('courses.index');
        Route::get('/assignments', [LearningController::class, 'teacherAssignments'])->name('assignments.index');
        Route::patch('/submissions/{submission}/grade', [LearningController::class, 'gradeAssignment'])->name('submissions.grade');
        Route::get('/question-banks', [LearningController::class, 'teacherQuestionBanks'])->name('question-banks.index');

        // Teacher Quizzes & CBT
        Route::get('/quizzes', [TeacherQuizController::class, 'index'])->name('quizzes.index');
        Route::post('/quizzes', [TeacherQuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{quiz}/attempts', [TeacherQuizController::class, 'attempts'])->name('quizzes.attempts');

        // Teacher Attendance
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [TeacherAttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/{session}/edit', [TeacherAttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('/attendance/{session}', [TeacherAttendanceController::class, 'update'])->name('attendance.update');

        // Teacher Grades
        Route::get('/grades', [TeacherGradeController::class, 'index'])->name('grades.index');
        Route::post('/grades/components', [TeacherGradeController::class, 'storeComponent'])->name('grades.components.store');
        Route::put('/grades', [TeacherGradeController::class, 'updateGrades'])->name('grades.update');
    });

    // Student
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function (): void {
        Route::get('/dashboard', [RoleDashboardController::class, 'index'])->defaults('role', 'student')->name('dashboard');
        Route::get('/schedule', [LearningController::class, 'studentSchedule'])->name('schedule');
        Route::get('/kts', [StudentKtsController::class, 'show'])->name('kts');

        // LMS Courses & Lessons
        Route::get('/courses', [LearningController::class, 'studentCourses'])->name('courses.index');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/lessons/{lesson}', [StudentCourseController::class, 'lessonShow'])->name('lessons.show');
        Route::post('/lessons/{lesson}/complete', [StudentCourseController::class, 'completeLesson'])->name('lessons.complete');
        Route::post('/lessons/{lesson}/bookmark', [StudentCourseController::class, 'bookmarkLesson'])->name('lessons.bookmark');
        Route::post('/lessons/{lesson}/note', [StudentCourseController::class, 'noteLesson'])->name('lessons.note');

        // Assignments
        Route::get('/assignments', [LearningController::class, 'studentAssignments'])->name('assignments.index');
        Route::post('/assignments/{assignment}/submit', [LearningController::class, 'submitAssignment'])->name('assignments.submit');

        // Quizzes & CBT
        Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/start', [StudentQuizController::class, 'start'])->name('quizzes.start');
        Route::get('/quizzes/{quiz}/take/{attempt}', [StudentQuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/attempt/{attempt}/answer', [StudentQuizController::class, 'saveAnswer'])->name('quizzes.save-answer');
        Route::post('/quizzes/attempt/{attempt}/violation', [StudentQuizController::class, 'logViolation'])->name('quizzes.violation');
        Route::post('/quizzes/attempt/{attempt}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::get('/quizzes/{quiz}/result/{attempt}', [StudentQuizController::class, 'result'])->name('quizzes.result');

        // Grades & Attendance
        Route::get('/grades', [LearningController::class, 'studentGrades'])->name('grades.index');
        Route::get('/attendance', [LearningController::class, 'studentAttendance'])->name('attendance.index');
    });

    // Parent
    Route::middleware('role:parent')->prefix('parent')->name('parent.')->group(function (): void {
        Route::get('/dashboard', [RoleDashboardController::class, 'index'])->defaults('role', 'parent')->name('dashboard');
        Route::get('/children', [ParentPortalController::class, 'children'])->name('children');
        Route::get('/attendance', [ParentPortalController::class, 'attendance'])->name('attendance.index');
        Route::get('/grades', [ParentPortalController::class, 'grades'])->name('grades.index');
        Route::get('/assignments', [ParentPortalController::class, 'assignments'])->name('assignments.index');
        Route::get('/courses', [ParentPortalController::class, 'courses'])->name('courses.index');
    });
});
