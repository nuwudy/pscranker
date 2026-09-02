<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DrillController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MemeBankController;
use App\Http\Controllers\OmrController;

// Main Homepage matching Behance Mockup
Route::get('/', [HomeController::class, 'index'])->name('home');

// Speed Drills - Rapid Fire 3-minute Engine
Route::get('/drill/{slug?}', [DrillController::class, 'show'])->name('drill.show');
Route::get('/api/quiz/{id}/questions', [DrillController::class, 'getQuestions'])->name('api.quiz.questions');
Route::post('/api/drill/submit', [DrillController::class, 'submitAttempt'])->name('api.drill.submit');

// Leaderboard & Competition
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Meme Mnemonics Bank
Route::get('/memebank', [MemeBankController::class, 'index'])->name('memebank');

// Interactive OMR Bubble Simulator
Route::get('/omr-simulator', [OmrController::class, 'index'])->name('omr.simulator');

// Quick Stubs for Navigation links
Route::get('/courses', function () {
    return redirect()->route('home')->with('info', 'Free Kerala PSC Micro-Courses launching this week!');
})->name('courses');

Route::get('/profile', function () {
    return redirect()->route('leaderboard');
})->name('profile');
