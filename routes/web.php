<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DrillController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MemeBankController;
use App\Http\Controllers\OmrController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\Admin\AdminSessionController;

// Main Homepage matching Behance Mockup
Route::get('/', [HomeController::class, 'index'])->name('home');

// 4-Phase Micro-Learning Sessions Engine
Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
Route::get('/session/{slug}', [SessionController::class, 'show'])->name('session.show');
Route::post('/api/session/{id}/progress', [SessionController::class, 'saveProgress'])->name('api.session.progress');
Route::post('/api/session/{id}/omr-submit', [SessionController::class, 'submitOmr'])->name('api.session.omr-submit');

// Speed Drills - Rapid Fire 3-minute Engine
Route::get('/drill/{slug?}', [DrillController::class, 'show'])->name('drill.show');
Route::get('/api/quiz/{id}/questions', [DrillController::class, 'getQuestions'])->name('api.quiz.questions');
Route::post('/api/drill/submit', [DrillController::class, 'submitAttempt'])->name('api.drill.submit');

// Leaderboard & Competition
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Meme Mnemonics Bank
Route::get('/memebank', [MemeBankController::class, 'index'])->name('memebank');

use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Interactive OMR Bubble Simulator
Route::get('/omr-simulator', [OmrController::class, 'index'])->name('omr.simulator');

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\PricingController;

// Prepaid Pricing Engine & Razorpay Checkout
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::post('/subscription/create-order', [PricingController::class, 'createOrder'])->name('subscription.create-order');
Route::post('/subscription/verify-payment', [PricingController::class, 'verifyPayment'])->name('subscription.verify-payment');

// Mandatory Razorpay Compliance & Policy Pages
Route::get('/terms', [PricingController::class, 'terms'])->name('terms');
Route::get('/privacy', [PricingController::class, 'privacy'])->name('privacy');
Route::get('/refund-policy', [PricingController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/contact', [PricingController::class, 'contact'])->name('contact');

// Admin Mission Control Dashboard & Content Builder Routes (Protected by Auth)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::post('settings/pricing', [DashboardController::class, 'updatePricingSettings'])->name('settings.pricing');
    Route::resource('sessions', AdminSessionController::class);
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media/api-list', [MediaController::class, 'apiList'])->name('media.api-list');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
});

// Quick Aliases for Navigation links
Route::get('/courses', [SessionController::class, 'index'])->name('courses');

Route::get('/profile', function () {
    return redirect()->route('leaderboard');
})->name('profile');
