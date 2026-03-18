<?php

namespace App\Http\Controllers;

use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherDashboardController extends Controller
{
    protected GeminiAiService $aiService;

    public function __construct(GeminiAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Tampilkan halaman dashboard utama untuk guru.
     */
    public function index()
    {
        $statistics = [
            'total_students' => 120,
            'average_score' => 85,
            'active_courses' => 4,
            'chart_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            'chart_data' => [65, 70, 80, 85, 90]
        ];

        return view('dashboard.teacher', compact('statistics'));
    }

    /**
     * Endpoint API/AJAX untuk Generate AI Quiz secara dinamis dari materi.
     */
    public function generateAiQuiz(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_content' => 'required|string|min:20|max:5000'
        ]);
        
        $lessonContent = $request->lesson_content;
        
        // Panggil AI Service ke Gemini (dengan fallback otomatis jika API gagal)
        $quizData = $this->aiService->generateQuiz($lessonContent, 5);

        return response()->json([
            'status' => 'success',
            'message' => count($quizData) . ' soal kuis berhasil digenerate!',
            'data' => $quizData
        ]);
    }

    /**
     * Endpoint API/AJAX untuk menganalisis progress siswa dengan AI.
     * Menerima scores sebagai array angka atau string CSV ("70,65,80").
     */
    public function analyzeStudentProgress(Request $request): JsonResponse
    {
        $request->validate([
            'habits' => 'required|string|min:10|max:2000',
        ]);

        // Parse scores: bisa berupa array dari JSON, atau string CSV
        $rawScores = $request->input('scores', []);
        if (is_string($rawScores)) {
            $scores = array_map('intval', array_filter(array_map('trim', explode(',', $rawScores))));
        } else {
            $scores = array_map('intval', (array) $rawScores);
        }

        if (empty($scores)) {
            $scores = [70, 65, 75]; // Fallback jika tidak ada skor
        }

        $feedback = $this->aiService->analyzeStudentProgress($scores, $request->habits);

        return response()->json([
            'status'   => 'success',
            'feedback' => $feedback
        ]);
    }
}
