<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiMicrolearningGenerator
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generate(array $hookQuestion)
    {
        if (!$this->apiKey) {
            throw new \Exception('GEMINI_API_KEY is not set in .env');
        }

        // Handle if user pasted an array of objects instead of a single object
        if (isset($hookQuestion[0]) && is_array($hookQuestion[0])) {
            $hookQuestion = $hookQuestion[0];
        }

        $question = $hookQuestion['question'] ?? 'Unknown question';
        $options = isset($hookQuestion['options']) && is_array($hookQuestion['options']) ? implode(', ', $hookQuestion['options']) : 'Unknown options';
        $correctIndex = $hookQuestion['correct_index'] ?? 0;

        $prompt = "You are an expert Kerala PSC exam content creator.
        
Given this previous PSC question:
Question: " . $question . "
Options: " . $options . "
Correct Answer Index: " . $correctIndex . "

Please generate two things in strict JSON format:
1. 'lesson_content': A high-yield, structured micro-lesson in Markdown format. It should focus on the core topic of the question and include standard facts (SCERT/NCERT base) relevant to Kerala PSC. Use scannable bullet points and bold key facts. Do not make it too long.
2. 'practice_mcqs': An array of 4 relevant, PSC-standard practice MCQs derived from the lesson. Each MCQ must have 'question' (string), 'options' (array of 4 strings), 'correct_index' (integer 0-3), and 'explanation' (string).

IMPORTANT: Return ONLY raw JSON. No markdown wrappers, no backticks. The schema must be exactly:
{
  \"lesson_content\": \"markdown string here\",
  \"practice_mcqs\": [
    {
      \"question\": \"...\",
      \"options\": [\"...\", \"...\", \"...\", \"...\"],
      \"correct_index\": 0,
      \"explanation\": \"...\"
    }
  ]
}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '?key=' . $this->apiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to generate content from Gemini API: ' . $response->body());
        }

        $result = $response->json();
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        // Clean up markdown json blocks if any
        $text = str_replace(['```json', '```'], '', $text);
        $text = trim($text);

        return json_decode($text, true);
    }
}
