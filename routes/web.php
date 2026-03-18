<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherDashboardController;

Route::get('/', function () {
    return redirect()->route('teacher.dashboard');
});

Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
Route::post('/api/ai/generate-quiz', [TeacherDashboardController::class, 'generateAiQuiz'])->name('api.generate-quiz');
Route::post('/api/ai/analyze-student', [TeacherDashboardController::class, 'analyzeStudentProgress'])->name('api.analyze-student');

// EMERGENCY DEPLOY ROUTE: Run migrations on Vercel
Route::get('/deploy-migrate', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migrasi Berhasil: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Migrasi Gagal: " . $e->getMessage();
    }
});
