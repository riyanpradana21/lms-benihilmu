@php
    $user = auth()->user();
    $isAdmin = $user->hasRole(['super_admin', 'admin']);
    $isTeacher = $user->hasRole('teacher');
    $isStudent = $user->hasRole('student');
    $isParent = $user->hasRole('parent');
    $isHomeroomTeacher = $isTeacher && \App\Models\SchoolClass::where('homeroom_teacher_id', $user->id)->exists();
@endphp

<div class="space-y-6 text-sm">
    @if($isAdmin)
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard Admin</span>
            </a>
        </div>

        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Struktur SIAKAD</div>
            <div class="space-y-1">
                <a href="{{ route('admin.announcements.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.announcements.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Pengumuman</a>
                <a href="{{ route('admin.exam-schedules.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('admin.exam-schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Jadwal Ujian Siswa</a>
                <a href="{{ route('admin.academic-years.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.academic-years.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Tahun Ajaran</span>
                </a>
                <a href="{{ route('admin.semesters.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.semesters.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Semester</span>
                </a>
                <a href="{{ route('admin.curriculums.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.curriculums.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Kurikulum</span>
                </a>
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Mata Pelajaran</span>
                </a>
                <a href="{{ route('admin.classes.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.classes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Kelas & Rombel</span>
                </a>
                <a href="{{ route('admin.schedules.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jadwal Pelajaran</span>
                </a>
            </div>
        </div>

        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Warga Sekolah</div>
            <div class="space-y-1">
                <a href="{{ route('admin.teachers.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.teachers.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Data Guru</span>
                </a>
                <a href="{{ route('admin.students.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.students.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Data Siswa</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Pengguna & Role</span>
                </a>
            </div>
        </div>

        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">LMS & Akademik</div>
            <div class="space-y-1">
                <a href="{{ route('admin.courses.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.courses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Kursus LMS</span>
                </a>
                <a href="{{ route('admin.attendance.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.attendance.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Rekap Presensi</span>
                </a>
                <a href="{{ route('admin.grades.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.grades.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Rekap Nilai</span>
                </a>
                <a href="{{ route('admin.report-cards.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.report-cards.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Rapor Siswa</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('admin.audit-logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Audit Log</span>
                </a>
            </div>
        </div>
    @endif

    @if($isTeacher)
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Menu Guru</div>
            <div class="space-y-1">
                <a href="{{ route('teacher.announcements.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('teacher.announcements.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Pengumuman Sekolah</a>
                @if($isHomeroomTeacher)<a href="{{ route('teacher.exam-schedules.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('teacher.exam-schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Jadwal Ujian</a>@endif
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard Guru</span>
                </a>
                <a href="{{ route('teacher.courses.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.courses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Kursus & Materi</span>
                </a>
                <a href="{{ route('teacher.assignments.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Tugas & Penilaian</span>
                </a>
                <a href="{{ route('teacher.question-banks.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.question-banks.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Bank Soal</span>
                </a>
                <a href="{{ route('teacher.quizzes.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.quizzes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Kuis & CBT</span>
                </a>
                <a href="{{ route('teacher.attendance.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Presensi Siswa</span>
                </a>
                <a href="{{ route('teacher.grades.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('teacher.grades.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                    <span>Bobot & Input Nilai</span>
                </a>
            </div>
        </div>
    @endif

    @if($isStudent)
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Menu Siswa</div>
            <div class="space-y-1">
                <a href="{{ route('student.announcements.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('student.announcements.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Pengumuman</a>
                <a href="{{ route('student.exam-schedules.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('student.exam-schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Jadwal Ujian</a>
                <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard Siswa</span>
                </a>
                <a href="{{ route('student.schedule') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.schedule') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jadwal Pelajaran</span>
                </a>
                <a href="{{ route('student.courses.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.courses.*') || request()->routeIs('student.lessons.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Kursus LMS</span>
                </a>
                <a href="{{ route('student.assignments.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Tugas Saya</span>
                </a>
                <a href="{{ route('student.quizzes.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.quizzes.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Kuis & CBT</span>
                </a>
                <a href="{{ route('student.grades.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.grades.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Nilai & Rapor</span>
                </a>
                <a href="{{ route('student.attendance.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.attendance.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Presensi Saya</span>
                </a>
                <a href="{{ route('student.kts') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('student.kts') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    <span>Kartu Pelajar (KTS)</span>
                </a>
            </div>
        </div>
    @endif

    @if($isParent)
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 px-2">Portal Orang Tua</div>
            <div class="space-y-1">
                <a href="{{ route('parent.announcements.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('parent.announcements.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Pengumuman</a>
                <a href="{{ route('parent.exam-schedules.index') }}" class="block rounded-lg px-3 py-2 font-medium {{ request()->routeIs('parent.exam-schedules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Jadwal Ujian Anak</a>
                <a href="{{ route('parent.dashboard') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('parent.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Ringkasan Anak</span>
                </a>
                <a href="{{ route('parent.attendance.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('parent.attendance.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Presensi Anak</span>
                </a>
                <a href="{{ route('parent.grades.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('parent.grades.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Nilai Anak</span>
                </a>
                <a href="{{ route('parent.assignments.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('parent.assignments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Tugas Anak</span>
                </a>
                <a href="{{ route('parent.courses.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('parent.courses.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Kursus LMS Anak</span>
                </a>
            </div>
        </div>
    @endif
</div>
