<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\User;
use App\Models\UserSessionProgress;

test('unauthenticated users are redirected from admin dashboard', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can view admin dashboard with all metrics', function () {
    $user = User::factory()->create();

    $category = Category::create([
        'name' => 'General Science',
        'slug' => 'general-science',
        'order' => 1,
    ]);

    $session = Session::create([
        'title' => 'Biology Cells & Tissues',
        'slug' => 'biology-cells-tissues',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    Question::create([
        'session_id' => $session->id,
        'category_id' => $category->id,
        'phase_type' => 'diagnostic',
        'question_text' => 'What is the powerhouse of the cell?',
        'option_a' => 'Ribosome',
        'option_b' => 'Mitochondria',
        'option_c' => 'Nucleus',
        'option_d' => 'Golgi apparatus',
        'correct_option' => 'B',
        'points' => 1.00,
        'negative_points' => 0.33,
    ]);

    UserSessionProgress::create([
        'user_id' => $user->id,
        'session_id' => $session->id,
        'current_phase' => 'summary',
        'diagnostic_status' => 'correct',
        'reinforcement_score' => 4.00,
        'omr_score' => 5.00,
        'net_marks' => 5.00,
        'xp_earned' => 200,
        'time_taken_seconds' => 180,
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Admin Dashboard');
    $response->assertSee('Kerala PSC Mission Control');
    $response->assertSee('Biology Cells &amp; Tissues', false);
    $response->assertSee('Recent Session Activity');
});
