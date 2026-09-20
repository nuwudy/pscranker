<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SessionContent;
use App\Models\User;
use App\Models\UserSessionProgress;

test('category system supports subject tags and inline quick store', function () {
    $admin = User::factory()->create(['email' => 'admin_cat@pscranker.com']);

    // Inline quick store via AJAX with unique category name
    $response = $this->actingAs($admin)->postJson(route('admin.categories.quick-store'), [
        'name' => 'Malayalam Language & Grammar',
        'name_malayalam' => 'മലയാള ഭാഷയും വ്യാകരണവും',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'category' => [
            'name' => 'Malayalam Language & Grammar',
            'slug' => 'malayalam-language-grammar',
        ]
    ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'Malayalam Language & Grammar',
        'slug' => 'malayalam-language-grammar',
    ]);
});

test('session runner decomposes contents into sequential units with hook mcq, content units, and final omr unit', function () {
    $category = Category::create(['name' => 'Kerala Renaissance Test', 'slug' => 'kerala-renaissance-test', 'order' => 10]);

    $session = Session::create([
        'title' => 'Session 1: Sree Narayana Guru Movement',
        'slug' => 'sree-narayana-guru-movement',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // Unit 1: Hook MCQ Opener
    SessionContent::create([
        'session_id' => $session->id,
        'unit_order' => 1,
        'unit_title' => 'Unit 1: Concept Hook Challenge',
        'type' => 'hook_mcq',
        'content_data' => [
            'question_text' => 'In which year was the historic Aruvippuram Prathishta performed?',
            'question_text_malayalam' => 'ചരിത്രപ്രസിദ്ധമായ അരുവിപ്പുറം പ്രതിഷ്ഠ നടന്ന വർഷം ഏത്?',
            'option_a' => '1888',
            'option_b' => '1898',
            'option_c' => '1903',
            'option_d' => '1885',
            'correct_option' => 'A',
            'explanation' => 'Aruvippuram Prathishta was performed by Sree Narayana Guru in 1888 on Shivaratri night.',
        ],
        'order' => 1,
    ]);

    // Unit 2: Core Concept Notes (Text & Audio)
    SessionContent::create([
        'session_id' => $session->id,
        'unit_order' => 2,
        'unit_title' => 'Unit 2: Core Foundations & Teachings',
        'type' => 'text',
        'content_data' => [
            'title' => 'One Caste, One Religion, One God for Man',
            'body' => 'Guru consecrated a Shiva idol at Aruvippuram and founded SNDP Yogam in 1903.',
            'callout_type' => 'golden_rule',
            'callout_text' => 'Aruvippuram installation was on 1888 Kumbha Shivaratri.',
        ],
        'order' => 1,
    ]);

    // Unit 3: Practice MCQ (Feeds OMR Exam Bank)
    SessionContent::create([
        'session_id' => $session->id,
        'unit_order' => 3,
        'unit_title' => 'Unit 3: Practice Drill',
        'type' => 'practice_mcq',
        'content_data' => [
            'question_text' => 'Who was the first secretary of SNDP Yogam formed in 1903?',
            'question_text_malayalam' => '1903-ൽ രൂപീകൃതമായ SNDP യോഗത്തിന്റെ ആദ്യ ജനറൽ സെക്രട്ടറി ആര്?',
            'option_a' => 'Kumaran Asan',
            'option_b' => 'Dr. Palpu',
            'option_c' => 'Sree Narayana Guru',
            'option_d' => 'T.K. Madhavan',
            'correct_option' => 'A',
            'explanation' => 'Mahakavi Kumaran Asan was appointed the first General Secretary of SNDP Yogam.',
        ],
        'order' => 1,
    ]);

    $structuredUnits = $session->structured_units;

    // Must have 4 units: Unit 1 (Hook), Unit 2 (Foundations), Unit 3 (Practice Drill), Unit 4 (Final Capstone OMR)
    expect(count($structuredUnits))->toBe(4);
    expect($structuredUnits[0]['title'])->toBe('Unit 1: Concept Hook Challenge');
    expect($structuredUnits[0]['blocks'][0]['type'])->toBe('hook_mcq');

    expect($structuredUnits[1]['title'])->toBe('Unit 2: Core Foundations & Teachings');
    expect($structuredUnits[2]['title'])->toBe('Unit 3: Practice Drill');

    // Final unit must be Capstone OMR Exam with aggregated questions
    $lastUnit = end($structuredUnits);
    expect($lastUnit['is_omr_unit'])->toBeTrue();
    expect($lastUnit['title'])->toContain('Capstone OMR Sheet Exam');
    expect(count($lastUnit['questions']))->toBe(2); // Hook MCQ + Practice MCQ
});

test('omr submission evaluates marks with +1 for correct and -0.33 penalty for incorrect', function () {
    $category = Category::create(['name' => 'History Unique Track', 'slug' => 'history-unique-track', 'order' => 15]);

    $session = Session::create([
        'title' => 'Session 1: Kerala History Mock',
        'slug' => 'kerala-history-mock',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 100,
        'is_active' => true,
    ]);

    $q1 = Question::create([
        'session_id' => $session->id,
        'phase_type' => 'omr',
        'question_text' => 'Capital of Travancore?',
        'option_a' => 'Thiruvananthapuram',
        'option_b' => 'Kollam',
        'option_c' => 'Kochi',
        'option_d' => 'Kozhikode',
        'correct_option' => 'A',
    ]);

    $q2 = Question::create([
        'session_id' => $session->id,
        'phase_type' => 'omr',
        'question_text' => 'Year of Kundara Proclamation?',
        'option_a' => '1809',
        'option_b' => '1812',
        'option_c' => '1857',
        'option_d' => '1905',
        'correct_option' => 'A',
    ]);

    $q3 = Question::create([
        'session_id' => $session->id,
        'phase_type' => 'omr',
        'question_text' => 'Who led the Guruvayur Satyagraha?',
        'option_a' => 'K. Kelappan',
        'option_b' => 'A.K. Gopalan',
        'option_c' => 'Mannathu Padmanabhan',
        'option_d' => 'P. Krishna Pillai',
        'correct_option' => 'A',
    ]);

    $user = User::factory()->create();

    // User attempts:
    // Q1: Correct (A) -> +1.00
    // Q2: Wrong (B) -> -0.33
    // Q3: Unattempted -> 0.00
    // Net marks = 1.00 - 0.33 = 0.67
    $response = $this->actingAs($user)->postJson(route('api.session.omr-submit', $session->id), [
        'answers' => [
            $q1->id => 'A',
            $q2->id => 'B',
        ],
        'stream' => 'subject',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'summary' => [
            'total_questions' => 3,
            'attempted' => 2,
            'correct' => 1,
            'wrong' => 1,
            'unattempted' => 1,
            'net_marks' => 0.67,
            'max_marks' => 3.0,
        ]
    ]);

    // Check database progress record
    $progress = UserSessionProgress::where('user_id', $user->id)
        ->where('session_id', $session->id)
        ->first();

    expect($progress)->not()->toBeNull();
    expect((float)$progress->net_marks)->toBe(0.67);
    expect($progress->completed_at)->not()->toBeNull();
});

test('cumulative score ledger tracks running total across sessions and recalculates on retake', function () {
    $category = Category::create(['name' => 'Constitution Unique Track', 'slug' => 'constitution-unique-track', 'order' => 20]);
    $user = User::factory()->create();

    // Session 1: 5 Questions, User scores 4.00
    $session1 = Session::create([
        'title' => 'Constitutional Preamble',
        'slug' => 'constitutional-preamble',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 100,
        'is_active' => true,
    ]);

    for ($i = 1; $i <= 5; $i++) {
        Question::create([
            'session_id' => $session1->id,
            'phase_type' => 'omr',
            'question_text' => "S1 Question $i",
            'option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D',
            'correct_option' => 'A',
        ]);
    }

    UserSessionProgress::create([
        'user_id' => $user->id,
        'session_id' => $session1->id,
        'completed_at' => now(),
        'net_marks' => 4.00,
        'xp_earned' => 100,
    ]);

    // Session 2: 5 Questions, User scores 5.00
    $session2 = Session::create([
        'title' => 'Fundamental Rights',
        'slug' => 'fundamental-rights',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 100,
        'is_active' => true,
    ]);

    for ($i = 1; $i <= 5; $i++) {
        Question::create([
            'session_id' => $session2->id,
            'phase_type' => 'omr',
            'question_text' => "S2 Question $i",
            'option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D',
            'correct_option' => 'A',
        ]);
    }

    UserSessionProgress::create([
        'user_id' => $user->id,
        'session_id' => $session2->id,
        'completed_at' => now(),
        'net_marks' => 5.00,
        'xp_earned' => 100,
    ]);

    // Check Cumulative Score Ledger:
    // Session 1: 4/5 + Session 2: 5/5 = 9.00 / 10.00 (90%)
    $ledger = $session2->getCumulativeLedger($user->id, null, 'subject');
    expect((float)$ledger['cumulative_score'])->toBe(9.0);
    expect((float)$ledger['cumulative_max'])->toBe(10.0);
    expect((float)$ledger['cumulative_percentage'])->toBe(90.0);
    expect($ledger['completed_sessions'])->toBe(2);

    // Retake Session 2: Reset Session 2's score
    $retakeResponse = $this->actingAs($user)->postJson(route('api.session.retake', $session2->id), [
        'stream' => 'subject',
    ]);

    $retakeResponse->assertStatus(200);
    $retakeResponse->assertJson([
        'success' => true,
        'cumulative_ledger' => [
            'cumulative_score' => 4.0, // Only Session 1 remains
            'cumulative_max' => 5.0,
            'cumulative_percentage' => 80.0,
            'completed_sessions' => 1,
        ]
    ]);

    // Session 2 progress should be reset in database
    $session2Progress = UserSessionProgress::where('user_id', $user->id)
        ->where('session_id', $session2->id)
        ->first();

    expect((float)$session2Progress->net_marks)->toBe(0.0);
    expect($session2Progress->completed_at)->toBeNull();
});
