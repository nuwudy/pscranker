<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\DrillAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrillController extends Controller
{
    public function show($slug = null)
    {
        $quiz = null;
        if ($slug) {
            $quiz = Quiz::with('questions.category')->where('slug', $slug)->first();
        }
        if (!$quiz) {
            $quiz = Quiz::with('questions.category')->where('slug', '3-min-rapid-blitz')->first() 
                ?? Quiz::with('questions.category')->first();
        }

        return view('pages.drill', compact('quiz'));
    }

    public function getQuestions($id)
    {
        $quiz = Quiz::with(['questions.category'])->findOrFail($id);

        $formattedQuestions = $quiz->questions->map(function ($q) {
            return [
                'id' => $q->id,
                'category' => $q->category ? $q->category->name : 'General GK',
                'category_ml' => $q->category ? $q->category->name_malayalam : 'പൊതുവിജ്ഞാനം',
                'question_text' => $q->question_text,
                'question_text_malayalam' => $q->question_text_malayalam,
                'options' => $q->options,
                'correct_option' => $q->correct_option,
                'explanation' => $q->explanation,
                'explanation_malayalam' => $q->explanation_malayalam,
                'trap_warning' => $q->trap_warning,
                'meme_image_url' => $q->meme_image_url,
                'psc_exam_reference' => $q->psc_exam_reference,
                'points' => (float)$q->points,
                'negative_points' => (float)$q->negative_points,
            ];
        });

        return response()->json([
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'title_malayalam' => $quiz->title_malayalam,
                'time_limit_seconds' => $quiz->time_limit_seconds,
                'question_time_limit' => $quiz->question_time_limit,
                'total_marks' => (float)$quiz->total_marks,
                'negative_marking_rate' => (float)$quiz->negative_marking_rate,
            ],
            'questions' => $formattedQuestions,
        ]);
    }

    public function submitAttempt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:quizzes,id',
            'candidate_name' => 'nullable|string|max:100',
            'total_questions' => 'required|integer',
            'correct_answers' => 'required|integer',
            'wrong_answers' => 'required|integer',
            'unanswered' => 'required|integer',
            'score' => 'required|numeric',
            'accuracy_percentage' => 'required|numeric',
            'time_taken_seconds' => 'required|integer',
            'answers_summary' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $attempt = DrillAttempt::create([
            'quiz_id' => $request->input('quiz_id'),
            'user_id' => auth()->id() ?? null,
            'session_id' => session()->getId(),
            'candidate_name' => $request->input('candidate_name', 'Guest Ranker #' . rand(100, 999)),
            'total_questions' => $request->input('total_questions'),
            'correct_answers' => $request->input('correct_answers'),
            'wrong_answers' => $request->input('wrong_answers'),
            'unanswered' => $request->input('unanswered'),
            'score' => $request->input('score'),
            'accuracy_percentage' => $request->input('accuracy_percentage'),
            'time_taken_seconds' => $request->input('time_taken_seconds'),
            'answers_summary' => $request->input('answers_summary'),
            'completed_at' => now(),
        ]);

        // Calculate ranking percentile
        $totalAttempts = DrillAttempt::where('quiz_id', $request->input('quiz_id'))->count();
        $lowerScores = DrillAttempt::where('quiz_id', $request->input('quiz_id'))
            ->where('score', '<', $request->input('score'))
            ->count();

        $percentile = $totalAttempts > 1 ? round(($lowerScores / ($totalAttempts - 1)) * 100, 1) : 95.0;

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'score' => (float)$attempt->score,
            'percentile' => $percentile,
            'message' => 'Drill submitted successfully! Your rank is recorded.',
        ]);
    }
}
