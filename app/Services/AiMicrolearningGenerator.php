<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiMicrolearningGenerator
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generate($hookQuestion)
    {
        if (!$this->apiKey) {
            throw new \Exception('GEMINI_API_KEY is not set in .env');
        }

        $rawInput = is_array($hookQuestion) ? json_encode($hookQuestion) : $hookQuestion;

        $prompt = "You are an expert Kerala PSC exam content creator.
        
Given this raw input which contains a PSC question:
" . $rawInput . "

First, identify the main question and its correct answer.
Then, please generate four things in strict JSON format:
1. 'category': One of the following exact category keys: \"GK\", \"History\", \"Geography\", \"English\", \"Malayalam\", \"Constitution\", \"Science\", \"Maths\", \"Current Affairs\".
2. 'suggested_title': A short, engaging topic title for this session (max 60 characters).
3. 'hook_question': A parsed JSON object of the provided question containing 'question' (string), 'options' (array of 4 strings), 'correct_index' (integer 0-3), and 'explanation' (string).
4. 'lesson_content': A high-yield, structured micro-lesson in Markdown format. It should focus on the core topic of the question and include standard facts (SCERT/NCERT base) relevant to Kerala PSC. Use scannable bullet points and bold key facts. Do not make it too long.
5. 'practice_mcqs': An array of 4 relevant, PSC-standard practice MCQs derived from the lesson. Each MCQ must have 'question' (string), 'options' (array of 4 strings), 'correct_index' (integer 0-3), and 'explanation' (string).

IMPORTANT: Return ONLY raw JSON. No markdown wrappers, no backticks. The schema must be exactly:
{
  \"category\": \"History\",
  \"suggested_title\": \"Topic Title Here\",
  \"hook_question\": {
    \"question\": \"...\",
    \"options\": [\"...\", \"...\", \"...\", \"...\"],
    \"correct_index\": 0,
    \"explanation\": \"...\"
  },
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
        ])->timeout(120)->post($this->baseUrl . '?key=' . $this->apiKey, [
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
