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

test('admin can save and update a session with a feature_image', function () {
    $admin = User::factory()->create(['email' => 'admin-feature@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'history'], ['name' => 'History', 'order' => 4]);

    $this->actingAs($admin);

    // 1. Create with feature_image
    $response = $this->post(route('admin.sessions.store'), [
        'title' => 'History Unit 5: Revolt of 1857',
        'title_malayalam' => '1857 ഒന്നാം സ്വാതന്ത്ര്യ സമരം',
        'slug' => 'revolt-of-1857',
        'feature_image' => 'https://images.unsplash.com/photo-sample-revolt.jpg',
        'category_id' => $category->id,
        'order' => 5,
        'xp_reward' => 300,
        'is_active' => 1,
        'creation_mode' => 'manual',
    ]);

    $response->assertRedirect();
    $session = Session::where('slug', 'revolt-of-1857')->first();
    expect($session)->not->toBeNull();
    expect($session->feature_image)->toBe('https://images.unsplash.com/photo-sample-revolt.jpg');

    // 2. Update feature_image
    $updateResponse = $this->put(route('admin.sessions.update', $session), [
        'title' => 'History Unit 5: Revolt of 1857 (Updated)',
        'title_malayalam' => '1857 സമരം',
        'slug' => 'revolt-of-1857',
        'feature_image' => '/storage/media/images/revolt-banner.png',
        'category_id' => $category->id,
        'order' => 5,
        'xp_reward' => 350,
        'is_active' => 1,
        'creation_mode' => 'manual',
    ]);

    $updateResponse->assertRedirect();
    $session->refresh();
    expect($session->feature_image)->toBe('/storage/media/images/revolt-banner.png');
});

test('feature image appears above the lesson in both custom code and manual modes', function () {
    $category = Category::firstOrCreate(['slug' => 'science'], ['name' => 'Science', 'order' => 3]);

    // 1. Manual Session with feature_image
    $manualSession = Session::create([
        'title' => 'Human Eye & Vision',
        'title_malayalam' => 'മനുഷ്യ നേത്രം',
        'slug' => 'human-eye-vision',
        'feature_image' => 'https://example.com/eye-anatomy.jpg',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
        'creation_mode' => 'manual',
    ]);

    $manualResponse = $this->get(route('session.show', $manualSession->slug));
    $manualResponse->assertStatus(200);
    $manualResponse->assertSee('https://example.com/eye-anatomy.jpg');
    $manualResponse->assertSee('Featured Image Banner above Manual Lesson Capsule Blocks', false);

    // 2. Custom Code Session with feature_image
    $codeSession = Session::create([
        'title' => 'Newton Laws of Motion',
        'title_malayalam' => 'ന്യൂട്ടന്റെ ചലന നിയമങ്ങൾ',
        'slug' => 'newton-laws-of-motion',
        'feature_image' => 'https://example.com/newton-apple.png',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 250,
        'is_active' => true,
        'creation_mode' => 'code',
        'custom_html' => '<div class="newton-experiment">First Law...</div>',
    ]);

    $codeResponse = $this->get(route('session.show', $codeSession->slug));
    $codeResponse->assertStatus(200);
    $codeResponse->assertSee('https://example.com/newton-apple.png');
    $codeResponse->assertSee('psc-custom-feature-image-banner', false);
    $codeResponse->assertSee('newton-experiment');

    // 3. Verify it is kept hidden from catalog index cards
    $catalogResponse = $this->get(route('sessions.index'));
    $catalogResponse->assertStatus(200);
    $catalogResponse->assertDontSee('Unit Featured Cover Thumbnail');
});

test('admin can save and update a session with a feature_video', function () {
    $admin = User::factory()->create(['email' => 'admin-video@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'geography'], ['name' => 'Geography', 'order' => 2]);

    $this->actingAs($admin);

    // 1. Create session with YouTube feature_video
    $response = $this->post(route('admin.sessions.store'), [
        'title' => 'Kerala Rivers and Dams Video Masterclass',
        'title_malayalam' => 'കേരളത്തിലെ നദികളും അണക്കെട്ടുകളും',
        'slug' => 'kerala-rivers-and-dams-video',
        'feature_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'feature_image' => 'https://example.com/rivers-poster.jpg',
        'category_id' => $category->id,
        'order' => 7,
        'xp_reward' => 250,
        'is_active' => 1,
        'creation_mode' => 'manual',
    ]);

    $response->assertRedirect();
    $session = Session::where('slug', 'kerala-rivers-and-dams-video')->first();
    expect($session)->not->toBeNull();
    expect($session->feature_video)->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    expect($session->feature_image)->toBe('https://example.com/rivers-poster.jpg');
    expect($session->isFeatureVideoEmbed())->toBeTrue();
    expect($session->getFeatureVideoEmbedUrl())->toContain('youtube.com/embed/dQw4w9WgXcQ');

    // 2. Update with a direct MP4 feature_video
    $updateResponse = $this->put(route('admin.sessions.update', $session), [
        'title' => 'Kerala Rivers and Dams Video Masterclass (Updated)',
        'title_malayalam' => 'കേരളത്തിലെ നദികൾ',
        'slug' => 'kerala-rivers-and-dams-video',
        'feature_video' => '/storage/media/videos/rivers-documentary.mp4',
        'category_id' => $category->id,
        'order' => 7,
        'xp_reward' => 300,
        'is_active' => 1,
        'creation_mode' => 'manual',
    ]);

    $updateResponse->assertRedirect();
    $session->refresh();
    expect($session->feature_video)->toBe('/storage/media/videos/rivers-documentary.mp4');
    expect($session->isFeatureVideoEmbed())->toBeFalse();
    expect($session->getFeatureVideoEmbedUrl())->toBe('/storage/media/videos/rivers-documentary.mp4');
});

test('feature video appears above the lesson in both custom code and manual modes', function () {
    $category = Category::firstOrCreate(['slug' => 'polity'], ['name' => 'Polity', 'order' => 1]);

    // 1. Manual Session with YouTube feature_video
    $manualSession = Session::create([
        'title' => 'Indian Constitution Preamble Video Lecture',
        'title_malayalam' => 'ഭരണഘടനാ ആമുഖം',
        'slug' => 'constitution-preamble-video',
        'feature_video' => 'https://youtu.be/keralaPscVid123',
        'feature_image' => 'https://example.com/preamble-poster.jpg',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
        'creation_mode' => 'manual',
    ]);

    $manualResponse = $this->get(route('session.show', $manualSession->slug));
    $manualResponse->assertStatus(200);
    $manualResponse->assertSee('youtube.com/embed/keralaPscVid123');
    $manualResponse->assertSee('iframe', false);

    // 2. Custom Code Session with direct MP4 video
    $codeSession = Session::create([
        'title' => 'Fundamental Rights Interactive Capsule',
        'title_malayalam' => 'മൗലികാവകാശങ്ങൾ',
        'slug' => 'fundamental-rights-code',
        'feature_video' => '/storage/media/videos/fundamental-rights.mp4',
        'feature_image' => '/storage/media/images/rights-poster.png',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 250,
        'is_active' => true,
        'creation_mode' => 'code',
        'custom_html' => '<div class="psc-screen-lesson"><div class="psc-card"><h2 class="psc-lesson-title">Lesson</h2><p>Content</p></div></div>',
    ]);

    $codeResponse = $this->get(route('session.show', $codeSession->slug));
    $codeResponse->assertStatus(200);
    $codeResponse->assertSee('/storage/media/videos/fundamental-rights.mp4');
    $codeResponse->assertSee('<video', false);
    $codeResponse->assertSee('controls', false);
    $codeResponse->assertSee('psc-custom-feature-image-banner');
});

test('home page START COURSE UNITS button launches the first mixed session', function () {
    $first = Session::where('is_active', true)
        ->where('in_general_stream', true)
        ->orderBy('general_stream_order', 'asc')
        ->first();

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee(route('session.show', ['slug' => $first->slug, 'stream' => 'general']));
    $response->assertSee('START COURSE UNITS');
    $response->assertSee('7 Core PSC Subjects');
});

test('admin can view mixed practice concocter interface', function () {
    $admin = User::factory()->create(['email' => 'admin-mixed@pscranker.com']);
    $this->actingAs($admin);

    $response = $this->get(route('admin.mixed-practice.index'));
    $response->assertStatus(200);
    $response->assertSee('Mixed Practice Train Concocter');
    $response->assertSee('Active Mixed Train');
    $response->assertSee('Subject Sessions Pool');
});

test('admin can toggle and reorder sessions in mixed practice train via API while subject tracks remain unchanged', function () {
    $admin = User::factory()->create(['email' => 'admin-toggle@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'maths'], ['name' => 'Maths', 'order' => 2]);

    $session1 = Session::create([
        'title' => 'Maths Ratio & Proportion',
        'slug' => 'maths-ratio-proportion',
        'category_id' => $category->id,
        'order' => 3, // Subject unit #3
        'in_general_stream' => false,
        'general_stream_order' => null,
        'xp_reward' => 250,
        'is_active' => true,
    ]);

    $session2 = Session::create([
        'title' => 'Maths Percentage Tricks',
        'slug' => 'maths-percentage-tricks',
        'category_id' => $category->id,
        'order' => 4, // Subject unit #4
        'in_general_stream' => false,
        'general_stream_order' => null,
        'xp_reward' => 250,
        'is_active' => true,
    ]);

    $this->actingAs($admin);

    // 1. Toggle session1 into Mixed Train
    $toggleRes1 = $this->postJson(route('admin.mixed-practice.toggle'), [
        'session_id' => $session1->id,
    ]);
    $toggleRes1->assertStatus(200);
    $toggleRes1->assertJson(['success' => true, 'in_general_stream' => true]);

    // 2. Toggle session2 into Mixed Train
    $toggleRes2 = $this->postJson(route('admin.mixed-practice.toggle'), [
        'session_id' => $session2->id,
    ]);
    $toggleRes2->assertStatus(200);
    $toggleRes2->assertJson(['success' => true, 'in_general_stream' => true]);

    // 3. Reorder train so session2 is first and session1 is second
    $reorderRes = $this->postJson(route('admin.mixed-practice.reorder'), [
        'ordered_ids' => [$session2->id, $session1->id],
    ]);
    $reorderRes->assertStatus(200);
    $reorderRes->assertJson(['success' => true]);

    $session1->refresh();
    $session2->refresh();
    expect($session2->general_stream_order)->toBe(1);
    expect($session1->general_stream_order)->toBe(2);

    // 4. Verify that subject-wise unit ordering remains completely UNCHANGED
    expect($session1->order)->toBe(3);
    expect($session2->order)->toBe(4);
});

test('new session automatically gets next sequential unit number in its subject when order is omitted', function () {
    $admin = User::factory()->create(['email' => 'admin-auto@pscranker.com']);
    $categoryA = Category::firstOrCreate(['slug' => 'science-auto'], ['name' => 'General Science', 'order' => 3]);
    $categoryB = Category::firstOrCreate(['slug' => 'history-auto'], ['name' => 'History', 'order' => 4]);

    $this->actingAs($admin);

    // Create first unit in Science
    $res1 = $this->post(route('admin.sessions.store'), [
        'title' => 'Science Unit 1: Digestive System',
        'category_id' => $categoryA->id,
        'xp_reward' => 250,
        'creation_mode' => 'code',
    ]);
    $res1->assertRedirect();
    $session1 = Session::where('slug', 'science-unit-1-digestive-system')->first();
    expect($session1->order)->toBe(1);

    // Create second unit in Science WITHOUT order field
    $res2 = $this->post(route('admin.sessions.store'), [
        'title' => 'Science Unit 2: Respiratory System',
        'category_id' => $categoryA->id,
        'xp_reward' => 250,
        'creation_mode' => 'code',
    ]);
    $res2->assertRedirect();
    $session2 = Session::where('slug', 'science-unit-2-respiratory-system')->first();
    expect($session2->order)->toBe(2);

    // Create unit in History WITHOUT order field - should start at 1 for History
    $res3 = $this->post(route('admin.sessions.store'), [
        'title' => 'History Unit 1: Kerala Renaissance Pioneers',
        'category_id' => $categoryB->id,
        'xp_reward' => 250,
        'creation_mode' => 'code',
    ]);
    $res3->assertRedirect();
    $session3 = Session::where('slug', 'history-unit-1-kerala-renaissance-pioneers')->first();
    expect($session3->order)->toBe(1);
});

test('new session automatically gets appended to mixed practice train with sequential order', function () {
    $admin = User::factory()->create(['email' => 'admin-train-auto@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'english-auto'], ['name' => 'English', 'order' => 1]);

    $this->actingAs($admin);

    $currentMaxTrain = Session::where('in_general_stream', true)->max('general_stream_order') ?? 0;

    $res = $this->post(route('admin.sessions.store'), [
        'title' => 'English Unit 99: Idioms and Phrases',
        'category_id' => $category->id,
        'xp_reward' => 250,
        'creation_mode' => 'code',
    ]);
    $res->assertRedirect();

    $session = Session::where('slug', 'english-unit-99-idioms-and-phrases')->first();
    expect($session->in_general_stream)->toBeTrue();
    expect($session->general_stream_order)->toBe($currentMaxTrain + 1);
});

test('admin can explicitly change train order of a session and all other train sessions shift without collision', function () {
    $admin = User::factory()->create(['email' => 'admin-reorder-ctrl@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'gk-train'], ['name' => 'General Knowledge', 'order' => 1]);

    $this->actingAs($admin);

    Session::query()->update(['in_general_stream' => false]);

    // Create 3 sessions
    $s1 = Session::create([
        'title' => 'Original Step 1',
        'slug' => 'original-step-1',
        'category_id' => $category->id,
        'order' => 1,
        'in_general_stream' => true,
        'general_stream_order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $s2 = Session::create([
        'title' => 'Original Step 2',
        'slug' => 'original-step-2',
        'category_id' => $category->id,
        'order' => 2,
        'in_general_stream' => true,
        'general_stream_order' => 2,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $s3 = Session::create([
        'title' => 'Original Step 3 (Wants to be first)',
        'slug' => 'original-step-3-first',
        'category_id' => $category->id,
        'order' => 3,
        'in_general_stream' => true,
        'general_stream_order' => 3,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // Admin edits s3 and sets general_stream_order = 1
    $updateRes = $this->put(route('admin.sessions.update', $s3), [
        'title' => 'Step 3 Promoted to Step 1',
        'slug' => 'original-step-3-first',
        'category_id' => $category->id,
        'order' => 3,
        'in_general_stream' => 1,
        'general_stream_order' => 1,
        'xp_reward' => 200,
        'is_active' => 1,
        'creation_mode' => 'code',
    ]);
    $updateRes->assertRedirect();

    $s1->refresh();
    $s2->refresh();
    $s3->refresh();

    // Verify s3 is now Step 1, s1 shifted to Step 2, s2 shifted to Step 3
    expect($s3->general_stream_order)->toBe(1);
    expect($s1->general_stream_order)->toBe(2);
    expect($s2->general_stream_order)->toBe(3);

    // Verify runner navigation for the general stream follows this new order:
    // Next unit after s3 is s1, and next unit after s1 is s2
    $nextAfterS3 = $s3->getNextSession('general');
    expect($nextAfterS3)->not->toBeNull();
    expect($nextAfterS3->id)->toBe($s1->id);

    $nextAfterS1 = $s1->getNextSession('general');
    expect($nextAfterS1)->not->toBeNull();
    expect($nextAfterS1->id)->toBe($s2->id);

    // Verify homepage launches s3 as Step 1
    $firstMixed = Session::where('is_active', true)
        ->where('in_general_stream', true)
        ->orderBy('general_stream_order', 'asc')
        ->orderBy('id', 'asc')
        ->first();
    expect($firstMixed->id)->toBe($s3->id);
});

test('admin can directly reposition a session in mixed practice studio via single session API', function () {
    $admin = User::factory()->create(['email' => 'admin-studio-reorder@pscranker.com']);
    $category = Category::firstOrCreate(['slug' => 'science-studio'], ['name' => 'Science', 'order' => 1]);

    $this->actingAs($admin);

    $unitA = Session::create([
        'title' => 'Studio Unit A',
        'slug' => 'studio-unit-a',
        'category_id' => $category->id,
        'order' => 1,
        'in_general_stream' => true,
        'general_stream_order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $unitB = Session::create([
        'title' => 'Studio Unit B',
        'slug' => 'studio-unit-b',
        'category_id' => $category->id,
        'order' => 2,
        'in_general_stream' => true,
        'general_stream_order' => 2,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    $unitC = Session::create([
        'title' => 'Studio Unit C',
        'slug' => 'studio-unit-c',
        'category_id' => $category->id,
        'order' => 3,
        'in_general_stream' => true,
        'general_stream_order' => 3,
        'xp_reward' => 200,
        'is_active' => true,
    ]);

    // Move unit C directly to position 1
    $res = $this->postJson(route('admin.mixed-practice.reorder'), [
        'session_id' => $unitC->id,
        'target_order' => 1,
    ]);
    $res->assertStatus(200);
    $res->assertJson(['success' => true]);

    $unitA->refresh();
    $unitB->refresh();
    $unitC->refresh();

    expect($unitC->general_stream_order)->toBe(1);
    expect($unitA->general_stream_order)->toBe(2);
    expect($unitB->general_stream_order)->toBe(3);
});







