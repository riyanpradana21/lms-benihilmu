<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Chapter;
use App\Models\ClassStudent;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\GradeComponent;
use App\Models\Guardian;
use App\Models\Institution;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\ReportCard;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('report_cards')->truncate();
        DB::table('student_grades')->truncate();
        DB::table('grade_components')->truncate();
        DB::table('attendances')->truncate();
        DB::table('attendance_sessions')->truncate();
        DB::table('quiz_violations')->truncate();
        DB::table('quiz_answers')->truncate();
        DB::table('quiz_attempts')->truncate();
        DB::table('quiz_questions')->truncate();
        DB::table('quizzes')->truncate();
        DB::table('question_options')->truncate();
        DB::table('questions')->truncate();
        DB::table('question_banks')->truncate();
        DB::table('submission_feedback')->truncate();
        DB::table('submission_attachments')->truncate();
        DB::table('assignment_submissions')->truncate();
        DB::table('assignment_attachments')->truncate();
        DB::table('assignments')->truncate();
        DB::table('student_notes')->truncate();
        DB::table('bookmarks')->truncate();
        DB::table('lesson_progress')->truncate();
        DB::table('lesson_attachments')->truncate();
        DB::table('lessons')->truncate();
        DB::table('topics')->truncate();
        DB::table('chapters')->truncate();
        DB::table('courses')->truncate();
        DB::table('schedules')->truncate();
        DB::table('teacher_subjects')->truncate();
        DB::table('class_students')->truncate();
        DB::table('parent_student')->truncate();
        DB::table('guardians')->truncate();
        DB::table('students')->truncate();
        DB::table('teachers')->truncate();
        DB::table('school_classes')->truncate();
        DB::table('subjects')->truncate();
        DB::table('curriculums')->truncate();
        DB::table('semesters')->truncate();
        DB::table('academic_years')->truncate();
        DB::table('institutions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();
        // 1. Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);

        // 2. Institution
        $institution = Institution::create([
            'name' => 'SMA Nusantara Digital',
            'code' => 'SMAN-DIGITAL-01',
            'address' => 'Jl. Pendidikan Merdeka No. 45, Jakarta Selatan',
            'phone' => '021-7654321',
            'email' => 'info@smanusantara.sch.id',
            'website' => 'https://smanusantara.sch.id',
        ]);

        // 3. Academic Year & Semesters
        $academicYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-15',
            'end_date' => '2025-06-20',
            'is_active' => true,
        ]);

        $semesterGanjil = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Ganjil',
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-20',
            'is_active' => true,
        ]);

        $semesterGenap = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Genap',
            'start_date' => '2025-01-06',
            'end_date' => '2025-06-20',
            'is_active' => false,
        ]);

        // 4. Curriculum
        $curriculum = Curriculum::create([
            'name' => 'Kurikulum Merdeka 2024',
            'year' => '2024',
            'description' => 'Kurikulum nasional terintegrasi kompetensi abad 21.',
            'is_active' => true,
        ]);

        // 5. Subjects
        $subMatematika = Subject::create([
            'code' => 'MAT-X',
            'name' => 'Matematika Dasar',
            'description' => 'Aljabar, eksponen, logaritma, trigonometri dasar',
            'curriculum_id' => $curriculum->id,
            'credits' => 4,
        ]);

        $subInformatika = Subject::create([
            'code' => 'INF-X',
            'name' => 'Informatika & Pemrograman',
            'description' => 'Algoritma, pemrograman dasar, struktur data',
            'curriculum_id' => $curriculum->id,
            'credits' => 3,
        ]);

        $subIndo = Subject::create([
            'code' => 'IND-X',
            'name' => 'Bahasa Indonesia',
            'description' => 'Tata bahasa, teks eksposisi, literasi kritis',
            'curriculum_id' => $curriculum->id,
            'credits' => 3,
        ]);

        $subInggris = Subject::create([
            'code' => 'ENG-X',
            'name' => 'Bahasa Inggris',
            'description' => 'English conversation, reading comprehension, writing',
            'curriculum_id' => $curriculum->id,
            'credits' => 3,
        ]);

        $subFisika = Subject::create([
            'code' => 'FIS-X',
            'name' => 'Fisika Terapan',
            'description' => 'Mekanika kuantum dasar, dinamika gerak',
            'curriculum_id' => $curriculum->id,
            'credits' => 3,
        ]);

        // 6. Users: Super Admin & Admin
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@school.test',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole($superAdminRole);

        $adminEmail = env('SEED_ADMIN_EMAIL', 'admin@school.test');
        $adminPassword = env('SEED_ADMIN_PASSWORD', 'password');
        $admin = User::create([
            'name' => 'Tata Usaha / Operator',
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
        ]);
        $admin->assignRole($adminRole);

        // 7. Teachers
        $teacherEmail = env('SEED_TEACHER_EMAIL', 'teacher@school.test');
        $teacherPassword = env('SEED_TEACHER_PASSWORD', 'password');
        $teacherUser1 = User::create([
            'name' => 'Budi Santoso, M.Pd.',
            'email' => $teacherEmail,
            'password' => Hash::make($teacherPassword),
        ]);
        $teacherUser1->assignRole($teacherRole);
        $teacher1 = Teacher::create([
            'user_id' => $teacherUser1->id,
            'employee_number' => 'NIP198205122005011001',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 12, Jakarta',
            'gender' => 'male',
            'date_of_birth' => '1982-05-12',
            'specialization' => 'Matematika & Komputasi',
            'is_active' => true,
        ]);

        $teacherUser2 = User::create([
            'name' => 'Siti Rahmawati, S.Pd.',
            'email' => 'teacher2@school.test',
            'password' => Hash::make('password'),
        ]);
        $teacherUser2->assignRole($teacherRole);
        $teacher2 = Teacher::create([
            'user_id' => $teacherUser2->id,
            'employee_number' => 'NIP198603202009022002',
            'phone' => '081234567891',
            'address' => 'Jl. Cempaka No. 8, Jakarta',
            'gender' => 'female',
            'date_of_birth' => '1986-03-20',
            'specialization' => 'Bahasa & Literasi',
            'is_active' => true,
        ]);

        // Attach teacher subjects
        $teacher1->subjects()->attach([$subMatematika->id, $subInformatika->id], ['academic_year_id' => $academicYear->id]);
        $teacher2->subjects()->attach([$subIndo->id, $subInggris->id], ['academic_year_id' => $academicYear->id]);

        // 8. Classes (school_classes)
        $class1 = SchoolClass::create([
            'name' => 'X-MIPA-1',
            'grade_level' => '10',
            'academic_year_id' => $academicYear->id,
            'homeroom_teacher_id' => $teacherUser1->id,
            'room' => 'Ruang 101',
            'capacity' => 32,
        ]);

        $class2 = SchoolClass::create([
            'name' => 'X-MIPA-2',
            'grade_level' => '10',
            'academic_year_id' => $academicYear->id,
            'homeroom_teacher_id' => $teacherUser2->id,
            'room' => 'Ruang 102',
            'capacity' => 32,
        ]);

        // 9. Students
        $studentEmail = env('SEED_STUDENT_EMAIL', 'student@school.test');
        $studentPassword = env('SEED_STUDENT_PASSWORD', 'password');

        $studentUser1 = User::create([
            'name' => 'Ahmad Fauzan',
            'email' => $studentEmail,
            'password' => Hash::make($studentPassword),
        ]);
        $studentUser1->assignRole($studentRole);
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'student_number' => '202401001',
            'name' => 'Ahmad Fauzan',
            'gender' => 'male',
            'date_of_birth' => '2008-04-15',
            'place_of_birth' => 'Jakarta',
            'address' => 'Jl. Kenanga No. 5, Jakarta Selatan',
            'phone' => '082198765432',
            'is_active' => true,
        ]);

        $studentUser2 = User::create([
            'name' => 'Bella Safira',
            'email' => 'student2@school.test',
            'password' => Hash::make('password'),
        ]);
        $studentUser2->assignRole($studentRole);
        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'student_number' => '202401002',
            'name' => 'Bella Safira',
            'gender' => 'female',
            'date_of_birth' => '2008-08-22',
            'place_of_birth' => 'Bandung',
            'address' => 'Jl. Mawar No. 17, Jakarta Selatan',
            'phone' => '082198765433',
            'is_active' => true,
        ]);

        $studentUser3 = User::create([
            'name' => 'Dimas Pratama',
            'email' => 'student3@school.test',
            'password' => Hash::make('password'),
        ]);
        $studentUser3->assignRole($studentRole);
        $student3 = Student::create([
            'user_id' => $studentUser3->id,
            'student_number' => '202401003',
            'name' => 'Dimas Pratama',
            'gender' => 'male',
            'date_of_birth' => '2008-11-03',
            'place_of_birth' => 'Depok',
            'address' => 'Jl. Anggrek No. 2, Depok',
            'phone' => '082198765434',
            'is_active' => true,
        ]);

        // Assign students to Class 1
        ClassStudent::create(['school_class_id' => $class1->id, 'student_id' => $student1->id, 'academic_year_id' => $academicYear->id]);
        ClassStudent::create(['school_class_id' => $class1->id, 'student_id' => $student2->id, 'academic_year_id' => $academicYear->id]);
        ClassStudent::create(['school_class_id' => $class1->id, 'student_id' => $student3->id, 'academic_year_id' => $academicYear->id]);

        // 10. Parents
        $parentEmail = env('SEED_PARENT_EMAIL', 'parent@school.test');
        $parentPassword = env('SEED_PARENT_PASSWORD', 'password');

        $parentUser1 = User::create([
            'name' => 'Ir. Hendra Fauzan',
            'email' => $parentEmail,
            'password' => Hash::make($parentPassword),
        ]);
        $parentUser1->assignRole($parentRole);
        $guardian1 = Guardian::create([
            'user_id' => $parentUser1->id,
            'name' => 'Ir. Hendra Fauzan',
            'phone' => '081399887766',
            'address' => 'Jl. Kenanga No. 5, Jakarta Selatan',
            'relationship' => 'father',
            'is_active' => true,
        ]);
        // Link parent to Student 1 & 2
        $guardian1->students()->attach([$student1->id, $student2->id]);

        // 11. Schedules
        Schedule::create([
            'school_class_id' => $class1->id,
            'subject_id' => $subMatematika->id,
            'teacher_id' => $teacher1->id,
            'day_of_week' => 'monday',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
            'room' => 'Ruang 101',
            'academic_year_id' => $academicYear->id,
        ]);

        Schedule::create([
            'school_class_id' => $class1->id,
            'subject_id' => $subInformatika->id,
            'teacher_id' => $teacher1->id,
            'day_of_week' => 'tuesday',
            'start_time' => '09:15:00',
            'end_time' => '11:30:00',
            'room' => 'Lab Komputer 1',
            'academic_year_id' => $academicYear->id,
        ]);

        Schedule::create([
            'school_class_id' => $class1->id,
            'subject_id' => $subIndo->id,
            'teacher_id' => $teacher2->id,
            'day_of_week' => 'wednesday',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
            'room' => 'Ruang 101',
            'academic_year_id' => $academicYear->id,
        ]);

        // 12. LMS Courses
        $course1 = Course::create([
            'title' => 'Matematika Wajib Kelas X-MIPA-1',
            'description' => 'Materi pembelajaran aljabar, sistem persamaan, fungsi kuadrat, dan trigonometri untuk siswa kelas X.',
            'subject_id' => $subMatematika->id,
            'school_class_id' => $class1->id,
            'semester_id' => $semesterGanjil->id,
            'teacher_id' => $teacher1->id,
            'status' => 'published',
        ]);

        $course2 = Course::create([
            'title' => 'Informatika & Logika Pemrograman X-MIPA-1',
            'description' => 'Dasar pemikiran komputasional, struktur kontrol algoritma, dan dasar pemrograman web.',
            'subject_id' => $subInformatika->id,
            'school_class_id' => $class1->id,
            'semester_id' => $semesterGanjil->id,
            'teacher_id' => $teacher1->id,
            'status' => 'published',
        ]);

        // 13. Chapters, Topics, Lessons
        $chapter1 = Chapter::create([
            'course_id' => $course1->id,
            'title' => 'Bab 1: Eksponen dan Bentuk Akar',
            'description' => 'Mempelajari konsep perpangkatan, sifat perkalian dan pembagian eksponen, serta rasionalisasi bentuk akar.',
            'order' => 1,
        ]);

        $topic1 = Topic::create([
            'chapter_id' => $chapter1->id,
            'title' => 'Konsep dan Sifat Eksponen Bulat Positif',
            'description' => 'Definisi dasar eksponen dan pembuktian sifat-sifatnya.',
            'order' => 1,
        ]);

        $lesson1 = Lesson::create([
            'topic_id' => $topic1->id,
            'title' => '1.1 Pengertian Eksponen dan Notasi Ilmiah',
            'content' => '<h2>Pengertian Eksponen</h2><p>Eksponen atau bilangan berpangkat adalah bentuk perkalian berulang dari suatu bilangan yang sama.</p><p>Bentuk umum: <strong>a<sup>n</sup> = a &times; a &times; ... &times; a</strong> (sebanyak n faktor).</p><h3>Sifat-sifat Eksponen:</h3><ul><li>a<sup>m</sup> &times; a<sup>n</sup> = a<sup>m+n</sup></li><li>a<sup>m</sup> / a<sup>n</sup> = a<sup>m-n</sup></li><li>(a<sup>m</sup>)<sup>n</sup> = a<sup>m&times;n</sup></li></ul>',
            'order' => 1,
            'is_published' => true,
            'estimated_minutes' => 45,
        ]);

        $lesson2 = Lesson::create([
            'topic_id' => $topic1->id,
            'title' => '1.2 Persamaan Eksponen Sederhana',
            'content' => '<h2>Persamaan Eksponen</h2><p>Bila a<sup>f(x)</sup> = a<sup>p</sup> dengan a > 0 dan a &ne; 1, maka f(x) = p.</p><p>Selesaikan contoh latihan berikut untuk memahami prinsip dasar kesamaan basis.</p>',
            'order' => 2,
            'is_published' => true,
            'estimated_minutes' => 60,
        ]);

        // Lesson progress for student 1
        LessonProgress::create([
            'student_id' => $student1->id,
            'lesson_id' => $lesson1->id,
            'started_at' => now()->subDays(2),
            'completed_at' => now()->subDays(1),
            'progress_percentage' => 100,
        ]);

        // 14. Assignments & Submissions
        $assignment1 = Assignment::create([
            'course_id' => $course1->id,
            'title' => 'Tugas 1: Menyederhanakan Bentuk Pangkat dan Akar',
            'instructions' => 'Kerjakan soal latihan halaman 24 nomor 1 sampai 10 di buku catatan. Foto atau scan dalam format PDF/JPG lalu unggah sebelum tenggat waktu.',
            'deadline' => now()->addDays(5),
            'max_score' => 100,
            'allow_late' => true,
            'is_published' => true,
        ]);

        $submission1 = AssignmentSubmission::create([
            'assignment_id' => $assignment1->id,
            'student_id' => $student1->id,
            'content' => 'Berikut lembar pengerjaan tugas 1 matematika saya, terima kasih Pak Guru.',
            'submitted_at' => now()->subDay(),
            'is_late' => false,
            'score' => 95.00,
            'graded_at' => now()->subHours(6),
            'graded_by' => $teacherUser1->id,
            'status' => 'graded',
        ]);

        $submission1->feedback()->create([
            'feedback' => 'Pekerjaan sangat rapi dan langkah-langkah penyelesaian nomor 7 sangat sistematis. Pertahankan!',
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment1->id,
            'student_id' => $student2->id,
            'content' => 'Tugas telah dikumpulkan.',
            'submitted_at' => now()->subHours(2),
            'is_late' => false,
            'status' => 'submitted',
        ]);

        // 15. Question Bank & Questions
        $bank = QuestionBank::create([
            'name' => 'Bank Soal Matematika Eksponen X',
            'subject_id' => $subMatematika->id,
            'description' => 'Koleksi soal eksponen, logaritma, dan persamaan aljabar.',
            'created_by' => $teacherUser1->id,
        ]);

        $q1 = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'multiple_choice',
            'question_text' => 'Berapakah nilai dari (2^3)^2 * 2^4 / 2^8?',
            'explanation' => '(2^3)^2 = 2^6. Jadi 2^6 * 2^4 = 2^10. Kemudian 2^10 / 2^8 = 2^2 = 4.',
            'score' => 20,
            'correct_answer' => '4',
        ]);

        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '2', 'is_correct' => false, 'order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '4', 'is_correct' => true, 'order' => 2]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '8', 'is_correct' => false, 'order' => 3]);
        QuestionOption::create(['question_id' => $q1->id, 'option_text' => '16', 'is_correct' => false, 'order' => 4]);

        $q2 = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'true_false',
            'question_text' => 'Apakah a^0 = 1 berlaku untuk setiap bilangan riil a non-nol?',
            'explanation' => 'Benar, untuk a tidak sama dengan 0, a^0 selalu bernilai 1.',
            'score' => 20,
            'correct_answer' => 'true',
        ]);

        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Benar (True)', 'is_correct' => true, 'order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'option_text' => 'Salah (False)', 'is_correct' => false, 'order' => 2]);

        $q3 = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'short_answer',
            'question_text' => 'Jika 3^(2x - 1) = 27, tentukan nilai x!',
            'explanation' => '27 = 3^3. Maka 2x - 1 = 3 -> 2x = 4 -> x = 2.',
            'score' => 20,
            'correct_answer' => '2',
        ]);

        $q4 = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'multiple_choice',
            'question_text' => 'Bentuk sederhana dari sqrt(75) adalah...',
            'explanation' => 'sqrt(75) = sqrt(25 * 3) = 5 * sqrt(3).',
            'score' => 20,
            'correct_answer' => '5√3',
        ]);

        QuestionOption::create(['question_id' => $q4->id, 'option_text' => '3√5', 'is_correct' => false, 'order' => 1]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => '5√3', 'is_correct' => true, 'order' => 2]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => '25√3', 'is_correct' => false, 'order' => 3]);
        QuestionOption::create(['question_id' => $q4->id, 'option_text' => '15√3', 'is_correct' => false, 'order' => 4]);

        $q5 = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'essay',
            'question_text' => 'Jelaskan mengapa sifat pembagian eksponen a^m / a^n = a^(m-n) menghasilkan a^(-n) = 1/a^n ketika m = 0!',
            'explanation' => 'Ketika m = 0, a^0 / a^n = 1 / a^n. Di sisi lain, a^(0 - n) = a^(-n). Maka a^(-n) = 1/a^n.',
            'score' => 20,
            'correct_answer' => null,
        ]);

        // 16. Quiz / CBT
        $quiz = Quiz::create([
            'course_id' => $course1->id,
            'title' => 'Kuis CBT 1: Eksponen & Bentuk Akar',
            'description' => 'Kuis online 30 menit. Dilarang berpindah tab saat pengerjaan. Sistem menggunakan timer server-authoritative.',
            'duration_minutes' => 30,
            'attempt_limit' => 1,
            'passing_score' => 70,
            'is_randomized' => false,
            'violation_threshold' => 3,
            'auto_submit_on_violation' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
            'is_published' => true,
        ]);

        $quiz->questions()->attach([
            $q1->id => ['order' => 1],
            $q2->id => ['order' => 2],
            $q3->id => ['order' => 3],
            $q4->id => ['order' => 4],
            $q5->id => ['order' => 5],
        ]);

        // Existing finished attempt for Student 2
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student2->id,
            'started_at' => now()->subHours(3),
            'expires_at' => now()->subHours(3)->addMinutes(30),
            'submitted_at' => now()->subHours(2)->subMinutes(35),
            'score' => 80.00,
            'is_auto_submitted' => false,
            'status' => 'graded',
        ]);

        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'answer_text' => '4',
            'is_correct' => true,
            'score_earned' => 20.00,
        ]);

        QuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'answer_text' => 'true',
            'is_correct' => true,
            'score_earned' => 20.00,
        ]);

        // 17. Attendance Session & Attendances
        $session = AttendanceSession::create([
            'school_class_id' => $class1->id,
            'subject_id' => $subMatematika->id,
            'teacher_id' => $teacher1->id,
            'date' => now()->toDateString(),
            'start_time' => '07:30:00',
            'status' => 'closed',
        ]);

        Attendance::create([
            'session_id' => $session->id,
            'student_id' => $student1->id,
            'status' => 'present',
            'note' => 'Tepat waktu',
        ]);

        Attendance::create([
            'session_id' => $session->id,
            'student_id' => $student2->id,
            'status' => 'present',
            'note' => 'Tepat waktu',
        ]);

        Attendance::create([
            'session_id' => $session->id,
            'student_id' => $student3->id,
            'status' => 'permission',
            'note' => 'Izin mengikuti lomba sains',
        ]);

        // 18. Grade Components & Student Grades (Weights = 100%)
        $gcTugas = GradeComponent::create([
            'course_id' => $course1->id,
            'name' => 'Tugas & PR',
            'type' => 'assignment',
            'weight' => 20.00,
        ]);

        $gcKuis = GradeComponent::create([
            'course_id' => $course1->id,
            'name' => 'Kuis Harian',
            'type' => 'quiz',
            'weight' => 20.00,
        ]);

        $gcUTS = GradeComponent::create([
            'course_id' => $course1->id,
            'name' => 'Penilaian Tengah Semester (PTS)',
            'type' => 'midterm',
            'weight' => 25.00,
        ]);

        $gcUAS = GradeComponent::create([
            'course_id' => $course1->id,
            'name' => 'Penilaian Akhir Semester (PAS)',
            'type' => 'final',
            'weight' => 35.00,
        ]);

        // Grades for student 1
        StudentGrade::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'grade_component_id' => $gcTugas->id,
            'score' => 95.00,
            'graded_by' => $teacherUser1->id,
            'graded_at' => now(),
        ]);

        StudentGrade::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'grade_component_id' => $gcKuis->id,
            'score' => 88.00,
            'graded_by' => $teacherUser1->id,
            'graded_at' => now(),
        ]);

        StudentGrade::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'grade_component_id' => $gcUTS->id,
            'score' => 90.00,
            'graded_by' => $teacherUser1->id,
            'graded_at' => now(),
        ]);

        StudentGrade::create([
            'student_id' => $student1->id,
            'course_id' => $course1->id,
            'grade_component_id' => $gcUAS->id,
            'score' => 92.00,
            'graded_by' => $teacherUser1->id,
            'graded_at' => now(),
        ]);

        // 19. Report Card for Student 1
        ReportCard::create([
            'student_id' => $student1->id,
            'semester_id' => $semesterGanjil->id,
            'school_class_id' => $class1->id,
            'notes' => 'Ahmad menunjukkan pemahaman matematika dan nalar komputasi yang sangat baik. Pertahankan kedisiplinan dan rasa ingin tahu!',
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
