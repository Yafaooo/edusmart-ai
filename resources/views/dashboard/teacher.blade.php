<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Dashboard - EduSmart AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out forwards',
                        'slide-up': 'slideUp 0.4s ease-out forwards',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(16px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-medium text-sm; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 99px; }
        .quiz-option-label { cursor: pointer; transition: all 0.2s; }
        .quiz-option-input:checked + .quiz-option-label { background: rgba(99, 102, 241, 0.2); border-color: #6366f1; color: #a5b4fc; }
    </style>
</head>
<body class="bg-slate-900 text-slate-200 flex h-screen overflow-hidden selection:bg-indigo-500 selection:text-white">

    <!-- ===========================================
         SIDEBAR
    =========================================== -->
    <aside id="sidebar" class="w-72 glass h-full flex flex-col p-6 border-r border-slate-700/50 fixed lg:relative z-40 -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="flex items-center gap-4 mb-12 flex-shrink-0">
            <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-lg font-bold text-white shadow-lg shadow-indigo-500/40 flex-shrink-0">E</div>
            <h1 class="text-xl font-bold tracking-tight text-white">EduSmart <span class="text-indigo-400">AI</span></h1>
        </div>
        <nav class="flex flex-col gap-2 flex-1">
            <a href="#dashboard-section" onclick="showSection('dashboard-section', this)" class="sidebar-link bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 active-link">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="#quiz-section" onclick="showSection('quiz-section', this)" class="sidebar-link text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent hover:border-slate-700">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Generate AI Quiz
            </a>
            <a href="#ai-feedback-section" onclick="showSection('ai-feedback-section', this)" class="sidebar-link text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent hover:border-slate-700">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                AI Feedback Siswa
            </a>
            <a href="#courses-section" onclick="showSection('courses-section', this)" class="sidebar-link text-slate-400 hover:text-white hover:bg-slate-800/60 border border-transparent hover:border-slate-700">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Manajemen Kelas
            </a>
        </nav>
        <div class="flex items-center gap-3 glass border border-slate-700 rounded-2xl p-4 flex-shrink-0">
            <img src="https://ui-avatars.com/api/?name=Teacher+Pro&background=4f46e5&color=fff&bold=true" class="w-10 h-10 rounded-full flex-shrink-0" alt="Profile">
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">Teacher Pro</p>
                <p class="text-xs text-indigo-400 font-medium">✦ Premium Plan</p>
            </div>
        </div>
    </aside>

    <!-- ===========================================
         MAIN CONTENT
    =========================================== -->
    <div class="flex-1 flex flex-col overflow-hidden" style="background: radial-gradient(ellipse at 80% 0%, rgba(79,70,229,0.12) 0%, transparent 60%), #0f172a;">
        <!-- Top Navbar -->
        <header class="h-16 glass border-b border-slate-700/50 flex items-center justify-between px-6 flex-shrink-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span id="page-breadcrumb" class="text-white font-semibold text-sm">Dashboard Overview</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 glass rounded-xl px-3 py-1.5 border border-indigo-500/20">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-xs text-emerald-400 font-medium">Gemini AI Online</span>
                </div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- ======================================
                 SECTION 1: DASHBOARD OVERVIEW
            ====================================== -->
            <section id="dashboard-section" class="section-panel">
                <!-- Heading -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-white mb-1">Welcome back, Teacher! 👋</h2>
                    <p class="text-slate-400 text-sm">Pantau performa siswa Anda dan buat kuis AI dengan mudah.</p>
                </div>
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                    <div class="glass p-6 rounded-2xl hover:border-indigo-500/30 transition">
                        <p class="text-slate-400 text-xs font-medium mb-2 uppercase tracking-widest">Total Siswa Aktif</p>
                        <p class="text-4xl font-bold text-white">{{ $statistics['total_students'] }}</p>
                        <span class="text-xs text-emerald-400 font-medium mt-2 inline-block bg-emerald-400/10 px-2 py-0.5 rounded-full">↑ 12.5%</span>
                    </div>
                    <div class="glass p-6 rounded-2xl hover:border-indigo-500/30 transition">
                        <p class="text-slate-400 text-xs font-medium mb-2 uppercase tracking-widest">Rata-rata Nilai</p>
                        <p class="text-4xl font-bold text-white">{{ $statistics['average_score'] }}<span class="text-2xl text-slate-500">%</span></p>
                        <span class="text-xs text-emerald-400 font-medium mt-2 inline-block bg-emerald-400/10 px-2 py-0.5 rounded-full">↑ 5.2%</span>
                    </div>
                    <div class="glass p-6 rounded-2xl hover:border-indigo-500/30 transition">
                        <p class="text-slate-400 text-xs font-medium mb-2 uppercase tracking-widest">Kelas Aktif</p>
                        <p class="text-4xl font-bold text-white">{{ $statistics['active_courses'] }}</p>
                        <span class="text-xs text-indigo-400 font-medium mt-2 inline-block bg-indigo-400/10 px-2 py-0.5 rounded-full">Semester Ganjil</span>
                    </div>
                </div>
                <!-- Chart + Quick Action -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 glass p-6 rounded-2xl">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-white">Grafik Performa Siswa</h3>
                        </div>
                        <div style="height:260px"><canvas id="studentChart"></canvas></div>
                    </div>
                    <div class="glass p-6 rounded-2xl flex flex-col gap-4">
                        <h3 class="text-lg font-bold text-white">Aksi Cepat</h3>
                        <button onclick="showSection('quiz-section', document.querySelector('[href=\'#quiz-section\']'))" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold text-sm hover:opacity-90 transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Generate AI Quiz
                        </button>
                        <button onclick="showSection('ai-feedback-section', document.querySelector('[href=\'#ai-feedback-section\']'))" class="w-full py-3 glass border border-slate-700 text-slate-300 rounded-xl font-semibold text-sm hover:border-indigo-500/50 hover:text-white transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                            Analisis Siswa AI
                        </button>
                        <div class="glass rounded-xl p-4 border border-amber-500/20 mt-auto">
                            <p class="text-xs text-amber-300 font-semibold mb-1">💡 AI Insight</p>
                            <p class="text-xs text-slate-400 leading-relaxed">Modul Aljabar butuh perhatian. Pertimbangkan latihan tambahan untuk meningkatkan nilai siswa.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================
                 SECTION 2: GENERATE AI QUIZ
            ====================================== -->
            <section id="quiz-section" class="section-panel hidden">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-white mb-1">⚡ Generate AI Quiz</h2>
                    <p class="text-slate-400 text-sm">Tempelkan materi pelajaran, AI Gemini akan membuat 5 soal pilihan ganda secara otomatis.</p>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Input Form -->
                    <div class="glass p-6 rounded-2xl">
                        <form id="quizForm">
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Materi Pelajaran <span class="text-red-400">*</span></label>
                            <textarea id="lessonContent" name="lesson_content" rows="10" placeholder="Contoh: Fotosintesis adalah proses biokimia yang dilakukan oleh tumbuhan untuk mengubah energi cahaya menjadi energi kimia..." class="w-full bg-slate-800/80 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-xl p-4 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"></textarea>
                            <p id="contentError" class="text-red-400 text-xs mt-1 hidden">Materi minimal 20 karakter.</p>
                            <div class="flex gap-3 mt-4">
                                <button type="button" onclick="loadSampleContent()" class="text-xs glass border border-slate-700 text-slate-400 hover:text-white rounded-lg px-4 py-2 transition">Muat Contoh Materi</button>
                                <button type="submit" id="generateBtn" class="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-90 text-white rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Generate Kuis
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Quiz Result -->
                    <div id="quizResultContainer" class="flex flex-col gap-4">
                        <!-- Empty State -->
                        <div id="quizEmptyState" class="glass rounded-2xl p-10 flex flex-col items-center justify-center text-center h-full min-h-64">
                            <div class="w-16 h-16 bg-slate-800 rounded-full flex items-center justify-center mb-4 text-3xl">⚡</div>
                            <p class="text-slate-400 text-sm">Kuis hasil generasi AI akan muncul di sini.</p>
                            <p class="text-slate-600 text-xs mt-1">Masukkan materi dan klik "Generate Kuis"</p>
                        </div>
                        <!-- Quiz Cards will be injected here -->
                        <div id="quizCards" class="flex flex-col gap-4 hidden"></div>
                    </div>
                </div>
            </section>

            <!-- ======================================
                 SECTION 3: AI FEEDBACK SISWA
            ====================================== -->
            <section id="ai-feedback-section" class="section-panel hidden">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-white mb-1">🧠 AI Analisis Progress Siswa</h2>
                    <p class="text-slate-400 text-sm">Masukkan data siswa. AI Gemini akan memberikan saran belajar personal.</p>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    <!-- Input Form -->
                    <div class="glass p-6 rounded-2xl">
                        <form id="feedbackForm">
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-300 mb-2">Skor Kuis Siswa (pisahkan dengan koma)</label>
                                <input type="text" id="studentScores" value="70, 65, 75, 80, 60" class="w-full bg-slate-800/80 border border-slate-700 text-slate-200 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="contoh: 70, 65, 80">
                            </div>
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-slate-300 mb-2">Kebiasaan Belajar Siswa</label>
                                <textarea id="learningHabits" rows="5" class="w-full bg-slate-800/80 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-xl p-4 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Contoh: Siswa jarang mengerjakan PR, sering absen, tapi aktif saat diskusi...">Siswa jarang mengerjakan PR, sering tidak mengumpulkan tugas tepat waktu, namun aktif bertanya saat diskusi kelas. Nilai ulangan harian rata-rata di bawah 75.</textarea>
                            </div>
                            <button type="submit" id="analyzeBtn" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:opacity-90 text-white rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                Analisis dengan AI
                            </button>
                        </form>
                    </div>
                    <!-- Feedback Result -->
                    <div id="feedbackResultContainer" class="glass rounded-2xl p-6 flex flex-col">
                        <div id="feedbackEmptyState" class="flex flex-col items-center justify-center text-center h-full min-h-64">
                            <div class="text-4xl mb-3">🧠</div>
                            <p class="text-slate-400 text-sm">Saran personal dari AI akan tampil di sini.</p>
                        </div>
                        <div id="feedbackResult" class="hidden animate-fade-in">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-indigo-300">Saran Belajar Personal dari Gemini AI</p>
                            </div>
                            <div id="feedbackText" class="text-sm text-slate-300 leading-relaxed whitespace-pre-wrap bg-slate-800/50 rounded-xl p-4 border border-slate-700"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ======================================
                 SECTION 4: MANAJEMEN KELAS
            ====================================== -->
            <section id="courses-section" class="section-panel hidden">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-white mb-1">📚 Manajemen Kelas</h2>
                    <p class="text-slate-400 text-sm">Daftar materi & kelas yang Anda pegang.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @php
                        $courses = [
                            ['icon' => '🔬', 'title' => 'Biologi Kelas X', 'lessons' => 8, 'students' => 32, 'color' => 'emerald'],
                            ['icon' => '🧮', 'title' => 'Matematika Kelas XI', 'lessons' => 12, 'students' => 28, 'color' => 'indigo'],
                            ['icon' => '🏛️', 'title' => 'Sejarah Indonesia', 'lessons' => 6, 'students' => 35, 'color' => 'amber'],
                            ['icon' => '⚛️', 'title' => 'Fisika Kelas XI', 'lessons' => 10, 'students' => 25, 'color' => 'purple'],
                        ];
                        $colorMap = ['emerald' => 'text-emerald-400 bg-emerald-400/10', 'indigo' => 'text-indigo-400 bg-indigo-400/10', 'amber' => 'text-amber-400 bg-amber-400/10', 'purple' => 'text-purple-400 bg-purple-400/10'];
                    @endphp
                    @foreach($courses as $course)
                    <div class="glass p-6 rounded-2xl hover:border-indigo-500/30 transition group cursor-pointer animate-slide-up">
                        <div class="flex justify-between items-start mb-5">
                            <div class="w-12 h-12 rounded-xl {{ $colorMap[$course['color']] }} text-2xl flex items-center justify-center">{{ $course['icon'] }}</div>
                            <span class="{{ $colorMap[$course['color']] }} text-xs font-medium px-2 py-1 rounded-full">Aktif</span>
                        </div>
                        <h3 class="text-base font-bold text-white mb-1 group-hover:text-indigo-300 transition">{{ $course['title'] }}</h3>
                        <div class="flex gap-4 text-xs text-slate-500 mt-3">
                            <span>📖 {{ $course['lessons'] }} Pelajaran</span>
                            <span>👤 {{ $course['students'] }} Siswa</span>
                        </div>
                        <button onclick="loadCourseLesson('{{ $course['title'] }}')" class="mt-4 w-full py-2 glass border border-slate-700 text-slate-400 hover:text-indigo-300 hover:border-indigo-500/40 rounded-lg text-xs font-semibold transition">
                            Generate Quiz Kelas Ini →
                        </button>
                    </div>
                    @endforeach
                </div>
            </section>

        </main>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ============================================================
        // CHART.JS - Grafik Performa Siswa
        // ============================================================
        const labels = {!! json_encode($statistics['chart_labels']) !!};
        const chartData = {!! json_encode($statistics['chart_data']) !!};
        const ctx = document.getElementById('studentChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(99,102,241,0.4)');
        gradient.addColorStop(1, 'rgba(99,102,241,0)');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{ label: 'Rata-rata Nilai', data: chartData, borderColor: '#818cf8', borderWidth: 2.5, backgroundColor: gradient, fill: true, tension: 0.4, pointBackgroundColor: '#1e293b', pointBorderColor: '#818cf8', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', titleColor: '#fff', bodyColor: '#94a3b8', padding: 12, cornerRadius: 8, displayColors: false, borderColor: 'rgba(255,255,255,0.08)', borderWidth: 1 }},
                scales: { y: { min: 40, max: 100, grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false }, ticks: { color: '#64748b' }}, x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b' }}}
            }
        });

        // ============================================================
        // NAVIGATION - Show / Hide Section
        // ============================================================
        const breadcrumbs = { 'dashboard-section': 'Dashboard Overview', 'quiz-section': '⚡ Generate AI Quiz', 'ai-feedback-section': '🧠 AI Feedback Siswa', 'courses-section': '📚 Manajemen Kelas' };
        function showSection(sectionId, clickedLink) {
            document.querySelectorAll('.section-panel').forEach(s => s.classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');
            document.querySelectorAll('.sidebar-link').forEach(l => {
                l.classList.remove('bg-indigo-600/10', 'text-indigo-400', 'border-indigo-500/20');
                l.classList.add('text-slate-400', 'border-transparent');
            });
            if (clickedLink) {
                clickedLink.classList.add('bg-indigo-600/10', 'text-indigo-400', 'border-indigo-500/20');
                clickedLink.classList.remove('text-slate-400', 'border-transparent');
            }
            document.getElementById('page-breadcrumb').textContent = breadcrumbs[sectionId] || '';
            document.getElementById('sidebar').classList.add('-translate-x-full');
            return false;
        }

        // ============================================================
        // GENERATE AI QUIZ - AJAX ke /api/ai/generate-quiz
        // ============================================================
        document.getElementById('quizForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const content = document.getElementById('lessonContent').value.trim();
            const errEl = document.getElementById('contentError');
            if (content.length < 20) { errEl.classList.remove('hidden'); return; }
            errEl.classList.add('hidden');

            const btn = document.getElementById('generateBtn');
            setButtonLoading(btn, 'Menganalisis Materi...');

            try {
                const response = await fetch('{{ route("api.generate-quiz") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ lesson_content: content })
                });
                const result = await response.json();

                if (!response.ok || result.status === 'error') {
                    showToast('❌ ' + (result.message || 'Terjadi kesalahan pada AI. Coba lagi.'), 'error');
                    return;
                }

                renderQuizCards(result.data);
                showToast('✅ ' + result.message, 'success');
            } catch (err) {
                showToast('❌ Koneksi gagal. Pastikan server berjalan.', 'error');
            } finally {
                resetButton(btn, `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Generate Kuis`);
            }
        });

        function renderQuizCards(quizData) {
            const container = document.getElementById('quizCards');
            const emptyState = document.getElementById('quizEmptyState');
            container.innerHTML = '';
            quizData.forEach((quiz, index) => {
                const optionKeys = Object.keys(quiz.options || {});
                const optionsHtml = optionKeys.map(key => `
                    <div class="flex items-center gap-0 mb-2">
                        <input type="radio" id="q${index}_${key}" name="quiz_${index}" value="${key}" class="quiz-option-input sr-only">
                        <label for="q${index}_${key}" class="quiz-option-label w-full flex items-center gap-3 text-sm text-slate-300 glass border border-slate-700 rounded-xl px-4 py-3">
                            <span class="w-7 h-7 flex-shrink-0 rounded-lg bg-slate-700 text-slate-300 text-xs font-bold flex items-center justify-center">${key}</span>
                            ${quiz.options[key]}
                        </label>
                    </div>`).join('');
                container.innerHTML += `
                    <div class="glass p-5 rounded-2xl animate-slide-up border border-slate-700/50">
                        <div class="flex gap-3 items-start mb-4">
                            <span class="w-7 h-7 flex-shrink-0 bg-indigo-600 text-white rounded-lg text-xs font-bold flex items-center justify-center mt-0.5">${index+1}</span>
                            <p class="text-sm text-white font-medium leading-relaxed">${quiz.question}</p>
                        </div>
                        <div class="pl-10">${optionsHtml}</div>
                        <div class="pl-10 mt-3">
                            <button onclick="revealAnswer(this, '${quiz.answer}')" class="text-xs text-indigo-400 hover:text-indigo-200 transition">Lihat Kunci Jawaban</button>
                            <span class="answer-reveal text-xs text-emerald-400 font-bold ml-2 hidden">Jawaban: ${quiz.answer}</span>
                        </div>
                    </div>`;
            });
            emptyState.classList.add('hidden');
            container.classList.remove('hidden');
        }

        function revealAnswer(btn, answer) {
            const span = btn.nextElementSibling;
            span.classList.toggle('hidden');
            btn.textContent = span.classList.contains('hidden') ? 'Lihat Kunci Jawaban' : 'Sembunyikan';
        }

        // ============================================================
        // AI ANALYZE STUDENT PROGRESS - AJAX ke /api/ai/analyze-student
        // ============================================================
        document.getElementById('feedbackForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const scoresRaw = document.getElementById('studentScores').value;
            const habits = document.getElementById('learningHabits').value.trim();
            const scores = scoresRaw.split(',').map(s => parseInt(s.trim())).filter(n => !isNaN(n));

            const btn = document.getElementById('analyzeBtn');
            setButtonLoading(btn, 'Menganalisis Data Siswa...');

            try {
                const response = await fetch('{{ route("api.analyze-student") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ scores: scores, habits: habits })
                });
                const result = await response.json();

                if (!response.ok || result.status === 'error') {
                    showToast('❌ ' + (result.message || 'Gagal menganalisis data siswa.'), 'error');
                    return;
                }

                document.getElementById('feedbackEmptyState').classList.add('hidden');
                const fd = document.getElementById('feedbackResult');
                fd.classList.remove('hidden');
                document.getElementById('feedbackText').textContent = result.feedback;
                showToast('✅ Analisis AI berhasil!', 'success');
            } catch(err) {
                showToast('❌ Koneksi gagal. Pastikan server berjalan.', 'error');
            } finally {
                resetButton(btn, `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg> Analisis dengan AI`);
            }
        });

        // ============================================================
        // UTILITY FUNCTIONS
        // ============================================================
        function setButtonLoading(btn, text) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-wait');
            btn.innerHTML = `<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span>${text}</span>`;
        }
        function resetButton(btn, html) {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-wait');
            btn.innerHTML = html;
        }
        function loadSampleContent() {
            document.getElementById('lessonContent').value = 'Fotosintesis adalah proses biokimia yang dilakukan oleh tumbuhan hijau, alga, dan beberapa bakteri untuk mengubah energi cahaya matahari menjadi energi kimia dalam bentuk glukosa. Proses ini terjadi di dalam kloroplas, organel sel yang mengandung pigmen hijau bernama klorofil. Reaksi fotosintesis berlangsung dalam dua tahap utama: reaksi terang yang terjadi di membran tilakoid dan reaksi gelap (siklus Calvin) yang terjadi di stroma kloroplas.';
        }
        function loadCourseLesson(title) {
            showSection('quiz-section', document.querySelector('[href="#quiz-section"]'));
            const contents = {
                'Biologi Kelas X': 'Sel adalah unit struktural dan fungsional terkecil dari makhluk hidup. Setiap sel memiliki membran plasma, sitoplasma, dan materi genetik berupa DNA. Sel prokariotik tidak memiliki nukleus sejati, sedangkan sel eukariotik memiliki nukleus yang dibungkus membran. Organel sel eukariotik meliputi mitokondria, retikulum endoplasma, badan Golgi, ribosom, lisosom, dan kloroplas (pada sel tumbuhan).',
                'Matematika Kelas XI': 'Pertidaksamaan kuadrat adalah pertidaksamaan yang memuat bentuk kuadrat ax² + bx + c dengan a ≠ 0. Langkah penyelesaiannya: ubah ke bentuk standar, cari akar-akar dengan rumus abc atau faktorisasi, tentukan tanda fungsi pada setiap interval menggunakan garis bilangan, lalu tentukan himpunan penyelesaian sesuai tanda pertidaksamaan.',
                'Sejarah Indonesia': 'Proklamasi Kemerdekaan Indonesia pada 17 Agustus 1945 merupakan puncak dari pergerakan nasional yang panjang. Soekarno dan Hatta membacakan teks proklamasi di Jalan Pegangsaan Timur 56, Jakarta. Sebelumnya terjadi peristiwa Rengasdengklok di mana golongan muda mendesak Soekarno-Hatta untuk segera memproklamasikan kemerdekaan tanpa menunggu Jepang.',
                'Fisika Kelas XI': 'Hukum Newton tentang gerak: Hukum I menyatakan benda diam tetap diam dan benda bergerak tetap bergerak lurus beraturan jika resultan gaya yang bekerja nol (hukum inersia). Hukum II menyatakan percepatan benda sebanding dengan resultan gaya dan berbanding terbalik dengan massa benda (F=ma). Hukum III menyatakan setiap aksi memiliki reaksi yang sama besar namun berlawanan arah.',
            };
            document.getElementById('lessonContent').value = contents[title] || '';
        }
        function showToast(msg, type) {
            const el = document.createElement('div');
            el.className = `fixed top-5 right-5 z-50 px-5 py-3 rounded-xl text-sm font-medium shadow-lg animate-fade-in ${ type === 'success' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30' }`;
            el.textContent = msg;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }
    </script>
</body>
</html>
