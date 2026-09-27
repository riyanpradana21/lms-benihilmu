<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CBT Exam: {{ $quiz->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col antialiased selection:bg-indigo-600 selection:text-white select-none">

    <!-- Top Sticky CBT Bar -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white text-base">
                    S
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 truncate max-w-xs sm:max-w-md">{{ $quiz->title }}</h1>
                    <p class="text-[11px] text-slate-500">{{ $quiz->course?->subject?->name ?? 'Ujian CBT' }} &bull; {{ auth()->user()->name }}</p>
                </div>
            </div>

            <!-- Server-Authoritative Timer Box -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900 text-white font-mono text-sm shadow-sm" id="timer-box">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="countdown-display">--:--:--</span>
                </div>

                <button onclick="confirmFinalSubmit()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                    Selesai & Kumpulkan
                </button>
            </div>
        </div>
    </header>

    <!-- Violation Warning Banner (Floating) -->
    <div id="violation-toast" class="hidden fixed top-20 right-6 z-50 bg-rose-600 text-white text-xs p-4 rounded-xl shadow-xl border border-rose-700 max-w-sm">
        <div class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span>Peringatan Integritas Ujian</span>
        </div>
        <p class="mt-1 text-[11px] text-rose-100" id="violation-msg">Perpindahan jendela/tab ujian tercatat oleh sistem.</p>
    </div>

    <!-- Main CBT Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left: Current Question Area (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm flex flex-col justify-between min-h-[480px]">
            @php
                $questions = $quiz->questions;
            @endphp

            @foreach($questions as $index => $q)
                @php
                    $saved = $savedAnswers->get($q->id)?->answer_text;
                @endphp
                <div class="question-container {{ $index === 0 ? '' : 'hidden' }}" id="question-panel-{{ $index }}" data-question-id="{{ $q->id }}" data-index="{{ $index }}">
                    <!-- Question Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">
                            Soal Nomor {{ $index + 1 }} dari {{ $questions->count() }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">Bobot: {{ $q->score ?: 10 }} Poin</span>
                    </div>

                    <!-- Question Text -->
                    <div class="text-slate-800 text-sm sm:text-base leading-relaxed font-medium mb-8">
                        {!! nl2br(e($q->question_text)) !!}
                    </div>

                    <!-- Options / Answer Input -->
                    <div class="space-y-3">
                        @if($q->options->isNotEmpty())
                            @foreach($q->options as $optIndex => $opt)
                                @php
                                    $optionLetter = chr(65 + $optIndex);
                                    $isChecked = ($saved == $opt->id || $saved == $opt->option_text);
                                @endphp
                                <label class="flex items-center gap-3.5 p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/30 cursor-pointer transition {{ $isChecked ? 'bg-indigo-50 border-indigo-500 ring-1 ring-indigo-500' : '' }}" id="label-opt-{{ $opt->id }}">
                                    <input type="radio" name="answer_{{ $q->id }}" value="{{ $opt->id }}" {{ $isChecked ? 'checked' : '' }}
                                           onchange="autoSaveAnswer({{ $q->id }}, '{{ $opt->id }}', {{ $index }})"
                                           class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700">{{ $optionLetter }}</span>
                                    <span class="text-xs sm:text-sm text-slate-700">{{ $opt->option_text }}</span>
                                </label>
                            @endforeach
                        @else
                            <!-- Short Answer / Essay -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-2">Jawaban Anda:</label>
                                <textarea rows="4" onblur="autoSaveAnswer({{ $q->id }}, this.value, {{ $index }})"
                                          placeholder="Tuliskan jawaban Anda di sini..."
                                          class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $saved }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Footer Question Navigation -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                <button type="button" onclick="navigateQuestion(-1)" id="btn-prev" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition disabled:opacity-40" disabled>
                    &larr; Soal Sebelumnya
                </button>

                <span id="save-status" class="text-[11px] text-emerald-600 font-medium hidden">Jawaban tersimpan</span>

                <button type="button" onclick="navigateQuestion(1)" id="btn-next" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    Soal Berikutnya &rarr;
                </button>
            </div>
        </div>

        <!-- Right: Questions Navigation Number Grid (4 cols) -->
        <aside class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Navigasi Butir Soal</h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Warna biru menandakan soal telah terisi jawaban.</p>
            </div>

            <div class="grid grid-cols-5 gap-2 text-xs font-bold">
                @foreach($questions as $index => $q)
                    @php
                        $isAnswered = !empty($savedAnswers->get($q->id)?->answer_text);
                    @endphp
                    <button type="button" onclick="jumpToQuestion({{ $index }})" id="nav-btn-{{ $index }}"
                            class="h-10 rounded-xl border transition flex items-center justify-center {{ $isAnswered ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }} {{ $index === 0 ? 'ring-2 ring-indigo-400' : '' }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="pt-4 border-t border-slate-100">
                <form action="{{ route('student.quizzes.submit', $attempt) }}" method="POST" id="submit-quiz-form">
                    @csrf
                    <button type="button" onclick="confirmFinalSubmit()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kumpulkan Lembar Ujian</span>
                    </button>
                </form>
            </div>
        </aside>
    </main>

    <!-- CBT Logic: Server-authoritative timer, Autosave, Anti-cheat -->
    <script>
        let remainingSeconds = {{ $remainingSeconds }};
        let currentQuestion = 0;
        const totalQuestions = {{ $questions->count() }};
        const attemptId = {{ $attempt->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Timer Logic
        function updateTimer() {
            if (remainingSeconds <= 0) {
                document.getElementById('countdown-display').innerText = "00:00:00";
                autoSubmitOnExpire();
                return;
            }

            let hours = Math.floor(remainingSeconds / 3600);
            let minutes = Math.floor((remainingSeconds % 3600) / 60);
            let seconds = remainingSeconds % 60;

            let hStr = String(hours).padStart(2, '0');
            let mStr = String(minutes).padStart(2, '0');
            let sStr = String(seconds).padStart(2, '0');

            document.getElementById('countdown-display').innerText = `${hStr}:${mStr}:${sStr}`;

            if (remainingSeconds < 300) {
                document.getElementById('timer-box').classList.remove('bg-slate-900');
                document.getElementById('timer-box').classList.add('bg-rose-600');
            }

            remainingSeconds--;
        }

        setInterval(updateTimer, 1000);
        updateTimer();

        function autoSubmitOnExpire() {
            alert('Waktu ujian telah berakhir! Lembar ujian otomatis dikumpulkan.');
            const form = document.getElementById('submit-quiz-form');
            const hiddenAuto = document.createElement('input');
            hiddenAuto.type = 'hidden';
            hiddenAuto.name = 'auto';
            hiddenAuto.value = '1';
            form.appendChild(hiddenAuto);
            form.submit();
        }

        // 2. Question Navigation
        function jumpToQuestion(index) {
            document.querySelectorAll('.question-container').forEach(el => el.classList.add('hidden'));
            document.getElementById(`question-panel-${index}`).classList.remove('hidden');

            document.querySelectorAll('[id^="nav-btn-"]').forEach(el => el.classList.remove('ring-2', 'ring-indigo-400'));
            document.getElementById(`nav-btn-${index}`).classList.add('ring-2', 'ring-indigo-400');

            currentQuestion = index;
            document.getElementById('btn-prev').disabled = (currentQuestion === 0);
            if (currentQuestion === totalQuestions - 1) {
                document.getElementById('btn-next').innerText = "Kumpulkan Ujian";
            } else {
                document.getElementById('btn-next').innerText = "Soal Berikutnya \u2192";
            }
        }

        function navigateQuestion(delta) {
            let nextIndex = currentQuestion + delta;
            if (nextIndex >= 0 && nextIndex < totalQuestions) {
                jumpToQuestion(nextIndex);
            } else if (nextIndex >= totalQuestions) {
                confirmFinalSubmit();
            }
        }

        // 3. Autosave Answer via Ajax
        function autoSaveAnswer(questionId, answerText, questionIndex) {
            const statusEl = document.getElementById('save-status');
            statusEl.innerText = 'Menyimpan...';
            statusEl.classList.remove('hidden', 'text-rose-600');
            statusEl.classList.add('text-indigo-600');

            fetch(`{{ url('/student/quizzes/attempt') }}/${attemptId}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer_text: answerText
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusEl.innerText = 'Tersimpan otomatis';
                    statusEl.classList.remove('text-indigo-600');
                    statusEl.classList.add('text-emerald-600');
                    setTimeout(() => statusEl.classList.add('hidden'), 2000);

                    // Update grid button indicator
                    const btn = document.getElementById(`nav-btn-${questionIndex}`);
                    btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                    btn.classList.remove('bg-slate-50', 'text-slate-700', 'border-slate-200');
                } else {
                    statusEl.innerText = 'Gagal menyimpan';
                    statusEl.classList.add('text-rose-600');
                }
            })
            .catch(() => {
                statusEl.innerText = 'Koneksi terputus';
                statusEl.classList.add('text-rose-600');
            });
        }

        // 4. Anti-Cheat Monitoring
        function logViolationEvent(type) {
            fetch(`{{ url('/student/quizzes/attempt') }}/${attemptId}/violation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    event_type: type,
                    metadata: { timestamp: new Date().toISOString() }
                })
            })
            .then(res => res.json())
            .then(data => {
                const toast = document.getElementById('violation-toast');
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 4000);

                if (data.auto_submitted) {
                    alert('Batas pelanggaran terlampaui. Ujian otomatis dikumpulkan.');
                    window.location.reload();
                }
            });
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                logViolationEvent('tab_hidden');
            }
        });

        window.addEventListener('blur', () => {
            logViolationEvent('window_blur');
        });

        function confirmFinalSubmit() {
            if (confirm('Apakah Anda yakin ingin menyelesaikan dan mengumpulkan lembar ujian ini?')) {
                document.getElementById('submit-quiz-form').submit();
            }
        }
    </script>
</body>
</html>
