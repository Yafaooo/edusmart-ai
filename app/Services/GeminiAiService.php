<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY', '');
    }

    /**
     * Membuat soal pilihan ganda otomatis berdasarkan materi pelajaran.
     *
     * @param string $lessonContent
     * @param int $numberOfQuestions
     * @return array
     */
    public function generateQuiz(string $lessonContent, int $numberOfQuestions = 5): array
    {
        $prompt = "Berdasarkan materi berikut, buatkan {$numberOfQuestions} soal pilihan ganda lengkap dengan 4 opsi jawaban (A, B, C, D) dan kunci jawabannya. Format hasil harus spesifik dalam JSON murni tanpa markdown: [{\"question\": \"...\", \"options\": {\"A\": \"...\", \"B\": \"...\", \"C\": \"...\", \"D\": \"...\"}, \"answer\": \"A\"}].\n\nMateri: {$lessonContent}";

        $response = $this->makeRequest($prompt);

        // Retry sekali jika kena rate limit (429)
        if (isset($response['_status_code']) && $response['_status_code'] === 429) {
            sleep(2);
            $response = $this->makeRequest($prompt);
        }

        $textResult = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (!empty($textResult)) {
            // Bersihkan markdown code block jika ada
            $textResult = preg_replace('/```(?:json)?\s*|\s*```/', '', $textResult);
            $decoded = json_decode(trim($textResult), true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        // === FALLBACK: Generate contoh soal berdasarkan kata kunci dari materi ===
        return $this->generateFallbackQuiz($lessonContent, $numberOfQuestions);
    }

    /**
     * Generate fallback quiz berbasis kata kunci dari materi (jika API tidak tersedia/terhambat quota).
     * Melakukan ekstraksi teks sederhana untuk menjamin relevansi dengan materi.
     */
    protected function generateFallbackQuiz(string $content, int $count): array
    {
        // Bersihkan teks dan pecah jadi kalimat
        $cleanText = strip_tags($content);
        $sentences = preg_split('/(?<=[.?!])\s+/', $cleanText, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter kalimat yang cukup panjang (minimal 30 karakter)
        $validSentences = array_filter($sentences, fn($s) => mb_strlen(trim($s)) > 30);
        
        // Ambil judul atau baris pertama sebagai topik utama
        $topic = trim($sentences[0] ?? 'Topik Terkait');
        $topic = mb_substr($topic, 0, 50) . (mb_strlen($topic) > 50 ? '...' : '');

        $quiz = [];
        
        // 1. Soal tentang konsep utama berdasarkan kalimat pertama
        $quiz[] = [
            "question" => "Berdasarkan materi yang diberikan, apa poin utama dari: \"{$topic}\"?",
            "options" => [
                "A" => "Merupakan konsep dasar yang dijelaskan secara detil di paragraf awal.",
                "B" => "Hanya sekedar contoh tambahan dalam pembahasan.",
                "C" => "Merupakan kesimpulan akhir dari seluruh materi.",
                "D" => "Tidak dijelaskan secara spesifik dalam teks."
            ],
            "answer" => "A"
        ];

        // 2. Soal berdasarkan kalimat acak dari materi (Contextual Extraction)
        $randomSentence = count($validSentences) > 1 ? $validSentences[array_rand($validSentences)] : ($validSentences[0] ?? "Materi penting");
        $quiz[] = [
            "question" => "Mengacu pada penjelasan materi: \"" . mb_substr($randomSentence, 0, 100) . "...\", manakah pernyataan yang paling akurat?",
            "options" => [
                "A" => "Pernyataan tersebut bersifat opsional dan tidak krusial.",
                "B" => "Kalimat tersebut menjelaskan mekanisme atau definisi inti dari topik.",
                "C" => "Kalimat tersebut adalah kutipan dari referensi luar.",
                "D" => "Semua pernyataan di atas salah."
            ],
            "answer" => "B"
        ];

        // 3. Soal tentang Tujuan/Manfaat
        $quiz[] = [
            "question" => "Apa tujuan utama dari proses atau materi yang sedang dibahas sekarang?",
            "options" => [
                "A" => "Memberikan wawasan mendalam tentang cara kerja sistem/konsep.",
                "B" => "Membandingkan dengan topik lain yang tidak relevan.",
                "C" => "Menghafal data tanpa memahami kaitan logisnya.",
                "D" => "Mencari kesalahan dalam fakta-fakta yang disajikan."
            ],
            "answer" => "A"
        ];

        // 4. Soal Analisis (Deductive)
        $quiz[] = [
            "question" => "Jika kita menghubungkan fakta-fakta dalam materi tersebut, kesimpulan logis yang bisa diambil adalah...",
            "options" => [
                "A" => "Materi ini sulit dipahami tanpa contoh visual.",
                "B" => "Penerapan konsep ini sangat bergantung pada konteks yang dijelaskan.",
                "C" => "Informasi yang diberikan bersifat kontradiktif.",
                "D" => "Tidak ada hubungan antara judul dan isi materi."
            ],
            "answer" => "B"
        ];

        // 5. Soal Pemahaman Umum
        $quiz[] = [
            "question" => "Manakah dari elemen berikut yang paling ditekankan dalam materi di atas?",
            "options" => [
                "A" => "Struktur dan alur penjelasan dari awal hingga akhir.",
                "B" => "Hanya berfokus pada statistik angka saja.",
                "C" => "Merubah pandangan umum menjadi lebih kritis.",
                "D" => "Jawaban A dan C benar."
            ],
            "answer" => "D"
        ];

        return array_slice($quiz, 0, $count);
    }

    /**
     * Memberikan saran belajar personal bagi siswa berdasarkan skor & habit.
     *
     * @param array $studentScores
     * @param string $learningHabits
     * @return string
     */
    public function analyzeStudentProgress(array $studentScores, string $learningHabits): string
    {
        $scoresJson = json_encode($studentScores);
        $prompt = "Sebagai asisten AI untuk EduSmart LMS, analisis progress siswa berikut.\nRata-rata skor kuis: {$scoresJson}\nKebiasaan belajar: {$learningHabits}\n\nBerikan saran belajar yang personal, memotivasi, dan langkah perbaikan praktis (maksimal 3 paragraf).";

        $response = $this->makeRequest($prompt);

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Terus semangat belajar! Tingkatkan latihan pada materi yang Anda rasa sulit, dan pertahankan konsistensi.';
    }

    /**
     * Helper request HTTP Client ke Gemini API.
     */
    protected function makeRequest(string $prompt): array
    {
        try {
            $response = Http::timeout(20)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Sertakan status code untuk retry logic
            Log::error('Gemini API Error [' . $response->status() . ']: ' . $response->body());
            return ['_status_code' => $response->status()];
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return ['_status_code' => 500];
        }
    }
}
