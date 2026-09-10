<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SessionContent;
use App\Models\User;
use App\Models\UserSessionProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    // Seed initial session for tests
    $this->category = Category::firstOrCreate(
        ['slug' => 'kerala-renaissance'],
        [
            'name' => 'Kerala Renaissance',
            'name_malayalam' => 'കേരള നവോത്ഥാനം',
            'order' => 1,
        ]
    );

    $this->session = Session::updateOrCreate(
        ['slug' => 'sree-narayana-guru-aruvipuram-prathishta'],
        [
            'title' => 'Sree Narayana Guru & Aruvipuram Prathishta',
            'title_malayalam' => 'ശ്രീനാരായണഗുരുവും അരുവിപ്പുറം വിപ്ലവ പ്രതിഷ്ഠയും',
            'category_id' => $this->category->id,
            'order' => 1,
            'xp_reward' => 250,
            'is_active' => true,
        ]
    );

    $this->session->contents()->delete();
    $this->session->questions()->delete();

    // Diagnostic question
    Question::create([
        'session_id' => $this->session->id,
        'category_id' => $this->category->id,
        'phase_type' => 'diagnostic',
        'question_text' => 'In which year did Sree Narayana Guru perform Aruvipuram Prathishta?',
        'option_a' => '1887',
        'option_b' => '1888',
        'option_c' => '1898',
        'option_d' => '1903',
        'correct_option' => 'B',
        'trap_warning_text' => '1888 Shivaratri night, not 1887!',
        'points' => 1.00,
        'negative_points' => 0.33,
    ]);

    // OMR Questions (3 questions for testing)
    $this->omrQ1 = Question::create([
        'session_id' => $this->session->id,
        'category_id' => $this->category->id,
        'phase_type' => 'omr',
        'question_text' => 'Rabindranath Tagore visit year?',
        'option_a' => '1920',
        'option_b' => '1922',
        'option_c' => '1925',
        'option_d' => '1928',
        'correct_option' => 'B',
        'points' => 1.00,
        'negative_points' => 0.33,
    ]);

    $this->omrQ2 = Question::create([
        'session_id' => $this->session->id,
        'category_id' => $this->category->id,
        'phase_type' => 'omr',
        'question_text' => 'Advaita Ashramam Aluva year?',
        'option_a' => '1904',
        'option_b' => '1912',
        'option_c' => '1913',
        'option_d' => '1916',
        'correct_option' => 'C',
        'points' => 1.00,
        'negative_points' => 0.33,
    ]);

    $this->omrQ3 = Question::create([
        'session_id' => $this->session->id,
        'category_id' => $this->category->id,
        'phase_type' => 'omr',
        'question_text' => 'First General Secretary of SNDP?',
        'option_a' => 'Dr. Palpu',
        'option_b' => 'Kumaran Asan',
        'option_c' => 'T.K. Madhavan',
        'option_d' => 'C. Kesavan',
        'correct_option' => 'B',
        'points' => 1.00,
        'negative_points' => 0.33,
    ]);
});

test('sessions catalog page can be rendered', function () {
    $response = $this->get(route('sessions.index'));
    $response->assertStatus(200);
    $response->assertSee('Kerala PSC Session Capsules');
    $response->assertSee('Sree Narayana Guru &amp; Aruvipuram Prathishta', false);
});

test('session runner page can be rendered with all 4 phases', function () {
    $response = $this->get(route('session.show', $this->session->slug));
    $response->assertStatus(200);
    $response->assertSee('Diagnostic Hook');
    $response->assertSee('Micro-Lesson');
    $response->assertSee('Speed Blitz');
    $response->assertSee('OMR Challenge');
    $response->assertSee('sessionEngine');
});

test('session progress can be saved via API', function () {
    $response = $this->postJson(route('api.session.progress', $this->session->id), [
        'current_phase' => 'lesson',
        'diagnostic_status' => 'correct',
        'xp_earned' => 50,
        'time_taken_seconds' => 45,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'progress' => [
            'current_phase' => 'lesson',
            'diagnostic_status' => 'correct',
            'xp_earned' => 50,
        ]
    ]);

    $this->assertDatabaseHas('user_session_progress', [
        'session_id' => $this->session->id,
        'current_phase' => 'lesson',
        'diagnostic_status' => 'correct',
    ]);
});

test('omr submission strictly applies Kerala PSC scoring rules (+1.00, -0.33, 0.00)', function () {
    // Q1: B (Correct -> +1.00)
    // Q2: A (Wrong -> -0.33)
    // Q3: null (Unattempted -> 0.00)
    // Net: 1.00 - 0.33 = 0.67
    $response = $this->postJson(route('api.session.omr-submit', $this->session->id), [
        'answers' => [
            $this->omrQ1->id => 'B',
            $this->omrQ2->id => 'A',
            // Q3 omitted (unattempted)
        ],
        'time_taken_seconds' => 120,
    ]);

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['success'])->toBeTrue();
    expect($data['summary']['correct'])->toBe(1);
    expect($data['summary']['wrong'])->toBe(1);
    expect($data['summary']['unattempted'])->toBe(1);
    expect($data['summary']['net_marks'])->toBe(0.67);
    expect($data['summary']['rank_badge'])->not->toBeEmpty();
});

test('admin session manager routes redirect guest to login and allow authenticated user', function () {
    // Guest redirected to login
    $guestResponse = $this->get(route('admin.sessions.index'));
    $guestResponse->assertRedirect(route('login'));

    // Authenticated user can access
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->get(route('admin.sessions.index'));
    $response->assertStatus(200);
    $response->assertSee('Learning Sessions Manager');

    $editResponse = $this->actingAs($user)->get(route('admin.sessions.edit', $this->session));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('adminSessionBuilder');
});

test('login page can be rendered and user can log in', function () {
    $user = \App\Models\User::factory()->create([
        'email' => 'admin@pscranker.com',
        'password' => bcrypt('Amter9388$'),
    ]);

    $response = $this->get(route('login'));
    $response->assertStatus(200);
    $response->assertSee('Welcome Back');

    $loginResponse = $this->post(route('login'), [
        'email' => 'admin@pscranker.com',
        'password' => 'Amter9388$',
    ]);

    $loginResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('mcq added in admin session builder automatically appears on omr sheet and powers omr scoring', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.sessions.update', $this->session), [
        'title' => 'Updated Unified Session',
        'slug' => 'updated-unified-session',
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => '1',
        'questions_json' => json_encode([
            [
                'phase_type' => 'reinforcement',
                'question_text' => 'Where did the Aruvipuram consecration take place?',
                'option_a' => 'Neyyar Riverbank',
                'option_b' => 'Karamana',
                'option_c' => 'Periyar',
                'option_d' => 'Alappuzha',
                'correct_option' => 'A',
            ]
        ]),
    ]);

    $response->assertRedirect();

    // Verify question is present as both reinforcement and omr
    $this->assertDatabaseHas('questions', [
        'session_id' => $this->session->id,
        'phase_type' => 'reinforcement',
        'question_text' => 'Where did the Aruvipuram consecration take place?',
    ]);

    $this->assertDatabaseHas('questions', [
        'session_id' => $this->session->id,
        'phase_type' => 'omr',
        'question_text' => 'Where did the Aruvipuram consecration take place?',
    ]);

    // Verify session runner loads this question for both reinforcement and omr
    $runnerResponse = $this->get(route('session.show', 'updated-unified-session'));
    $runnerResponse->assertStatus(200);
    $runnerResponse->assertSee('Where did the Aruvipuram consecration take place?');

    // Verify OMR submission scores properly against this question
    $omrQuestion = \App\Models\Question::where('session_id', $this->session->id)
        ->where('phase_type', 'omr')
        ->first();

    $submitResponse = $this->postJson(route('api.session.omr-submit', $this->session->id), [
        'answers' => [
            $omrQuestion->id => 'A',
        ],
        'time_taken_seconds' => 10,
    ]);

    $submitResponse->assertStatus(200);
    $submitResponse->assertJson([
        'success' => true,
        'summary' => [
            'total_questions' => 1,
            'correct' => 1,
            'wrong' => 0,
            'net_marks' => 1,
        ]
    ]);
});

test('all 6 core psc subjects exist and are displayed with interactive units in the catalog', function () {
    $subjects = [
        ['slug' => 'english', 'name' => 'English', 'order' => 1],
        ['slug' => 'maths', 'name' => 'Maths & Mental Ability', 'order' => 2],
        ['slug' => 'science', 'name' => 'General Science', 'order' => 3],
        ['slug' => 'history', 'name' => 'History & Renaissance', 'order' => 4],
        ['slug' => 'geography', 'name' => 'Geography', 'order' => 5],
        ['slug' => 'current-affairs', 'name' => 'Current Affairs & GK', 'order' => 6],
        ['slug' => 'map-study', 'name' => 'Map & Globe Study', 'order' => 7],
    ];

    foreach ($subjects as $s) {
        Category::firstOrCreate(['slug' => $s['slug']], $s);
    }

    $response = $this->get(route('sessions.index'));
    $response->assertStatus(200);

    // Assert all 7 subjects are present
    $response->assertSee('English');
    $response->assertSee('Maths &amp; Mental Ability', false);
    $response->assertSee('General Science');
    $response->assertSee('History &amp; Renaissance', false);
    $response->assertSee('Geography');
    $response->assertSee('Current Affairs &amp; GK', false);
    $response->assertSee('Map &amp; Globe Study', false);

    // Assert 7 subjects filter is present
    $response->assertSee('7 CORE PSC SUBJECTS');
    $response->assertSee('activeSubject');
});

test('general stream displays mixed concocted units from different subjects with sequential train order', function () {
    $english = Category::firstOrCreate(['slug' => 'english'], ['name' => 'English', 'order' => 1]);
    $geography = Category::firstOrCreate(['slug' => 'geography'], ['name' => 'Geography', 'order' => 5]);

    $unit1 = Session::create([
        'title' => 'Concocted Step 1: English Nouns',
        'slug' => 'concocted-step-1-english',
        'category_id' => $english->id,
        'order' => 1,
        'in_general_stream' => true,
        'general_stream_order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $unit2 = Session::create([
        'title' => 'Concocted Step 2: Kerala Rivers',
        'slug' => 'concocted-step-2-geography',
        'category_id' => $geography->id,
        'order' => 1,
        'in_general_stream' => true,
        'general_stream_order' => 2,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $response = $this->get(route('sessions.index', ['stream' => 'general']));
    $response->assertStatus(200);
    $response->assertSee('General Train');
    $response->assertSee('Concocted Step 1: English Nouns');
    $response->assertSee('Concocted Step 2: Kerala Rivers');
    $response->assertSee('Train Step #1');
    $response->assertSee('Train Step #2');
});

test('session runner navigation respects stream parameter between general train and subject stream', function () {
    $english = Category::firstOrCreate(['slug' => 'english'], ['name' => 'English', 'order' => 1]);
    $geography = Category::firstOrCreate(['slug' => 'geography'], ['name' => 'Geography', 'order' => 5]);

    // English Unit 1 (Train Step 501)
    $eng1 = Session::create([
        'title' => 'English Unit 1',
        'slug' => 'eng-unit-1-dual',
        'category_id' => $english->id,
        'order' => 501,
        'in_general_stream' => true,
        'general_stream_order' => 501,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // English Unit 2 (Not next in train, but next in English)
    $eng2 = Session::create([
        'title' => 'English Unit 2',
        'slug' => 'eng-unit-2-dual',
        'category_id' => $english->id,
        'order' => 502,
        'in_general_stream' => true,
        'general_stream_order' => 999,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // Geography Unit 1 (Next in general train at Step 502)
    $geo1 = Session::create([
        'title' => 'Geography Unit 1',
        'slug' => 'geo-unit-1-dual',
        'category_id' => $geography->id,
        'order' => 501,
        'in_general_stream' => true,
        'general_stream_order' => 502,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // In General Stream: English Unit 1 next is Geography Unit 1!
    expect($eng1->getNextSession('general')->id)->toBe($geo1->id);

    // In Subject Stream: English Unit 1 next is English Unit 2!
    expect($eng1->getNextSession('subject')->id)->toBe($eng2->id);

    // Testing Runner HTTP response with stream=general
    $responseGen = $this->get(route('session.show', ['slug' => $eng1->slug, 'stream' => 'general']));
    $responseGen->assertStatus(200);
    $responseGen->assertSee('GENERAL TRAIN');
    $responseGen->assertSee(route('session.show', ['slug' => $geo1->slug, 'stream' => 'general']));

    // Testing Runner HTTP response with stream=subject
    $responseSub = $this->get(route('session.show', ['slug' => $eng1->slug, 'stream' => 'subject']));
    $responseSub->assertStatus(200);
    $responseSub->assertSee('ENGLISH');
    $responseSub->assertSee(route('session.show', ['slug' => $eng2->slug, 'stream' => 'subject']));
});

test('admin can set in_general_stream and general_stream_order when saving a session', function () {
    $admin = User::factory()->create(['email' => 'admin@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'maths'], ['name' => 'Maths', 'order' => 2]);

    $this->actingAs($admin);

    $response = $this->post(route('admin.sessions.store'), [
        'title' => 'Maths Unit 3: Speed Tricks',
        'title_malayalam' => 'വേഗ കണക്കുകൾ',
        'slug' => 'maths-unit-3-speed-tricks',
        'category_id' => $category->id,
        'order' => 3,
        'xp_reward' => 250,
        'is_active' => 1,
        'is_premium' => 0,
        'in_general_stream' => 1,
        'general_stream_order' => 7,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('learning_sessions', [
        'slug' => 'maths-unit-3-speed-tricks',
        'in_general_stream' => true,
        'general_stream_order' => 7,
    ]);
});




