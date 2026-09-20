<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\User;

test('sessions correctly provide previous and next unit progression in sequence', function () {
    $category = Category::create(['name' => 'Renaissance', 'slug' => 'renaissance', 'order' => 1]);

    $unit1 = Session::create([
        'title' => 'Unit 1: Sree Narayana Guru',
        'slug' => 'unit-1-sng',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => true,
        'is_premium' => false,
    ]);

    $unit2 = Session::create([
        'title' => 'Unit 2: Ayyankali & SJPS',
        'slug' => 'unit-2-ayyankali',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 250,
        'is_active' => true,
        'is_premium' => false,
    ]);

    $unit3 = Session::create([
        'title' => 'Unit 3: Chattampi Swamikal',
        'slug' => 'unit-3-chattampi',
        'category_id' => $category->id,
        'order' => 3,
        'xp_reward' => 300,
        'is_active' => true,
        'is_premium' => true,
        'price' => 199.00,
    ]);

    expect($unit1->getPreviousSession())->toBeNull();
    expect($unit1->getNextSession()->id)->toBe($unit2->id);

    expect($unit2->getPreviousSession()->id)->toBe($unit1->id);
    expect($unit2->getNextSession()->id)->toBe($unit3->id);

    expect($unit3->getPreviousSession()->id)->toBe($unit2->id);
    expect($unit3->getNextSession())->toBeNull();

    expect($unit1->isFree())->toBeTrue();
    expect($unit1->formatted_price)->toBe('FREE');

    expect($unit3->isFree())->toBeFalse();
    expect($unit3->formatted_price)->toBe('₹199');
});

test('session runner shows next unit button and previous unit button for students', function () {
    $category = Category::firstOrCreate(['slug' => 'history'], ['name' => 'History', 'order' => 1]);

    $unit1 = Session::create([
        'title' => 'Unit 1: Intro',
        'slug' => 'unit-1-intro',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 100,
        'is_active' => true,
        'is_premium' => false,
    ]);

    $unit2 = Session::create([
        'title' => 'Unit 2: Deep Dive',
        'slug' => 'unit-2-deep-dive',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 150,
        'is_active' => true,
        'is_premium' => false,
    ]);

    // Question for Phase 1
    Question::create([
        'session_id' => $unit1->id,
        'phase_type' => 'diagnostic',
        'question_text' => 'Sample Hook Question',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'correct_option' => 'A',
    ]);

    $response = $this->get(route('session.show', $unit1->slug));
    $response->assertStatus(200);
    $response->assertSee('Next Unit →');
    $response->assertSee('CONTINUE TO NEXT UNIT');
    $response->assertSee('FREE UNIT');
});

test('premium unit displays paywall screen for guest and is bypassed for admin', function () {
    $category = Category::create(['name' => 'Special', 'slug' => 'special', 'order' => 1]);

    $premiumUnit = Session::create([
        'title' => 'Unit 5: Rank Maker Secrets',
        'slug' => 'unit-5-rank-maker',
        'category_id' => $category->id,
        'order' => 5,
        'xp_reward' => 500,
        'is_active' => true,
        'is_premium' => true,
        'price' => 299.00,
    ]);

    // Guest visiting premium unit sees paywall gate
    $guestResponse = $this->get(route('session.show', $premiumUnit->slug));
    $guestResponse->assertStatus(200);
    $guestResponse->assertSee('PRO Unit Locked');
    $guestResponse->assertSee('Unlock with UPI / PhonePe / Razorpay');
    $guestResponse->assertSee('₹299');

    // Admin visiting premium unit bypasses paywall
    $admin = User::factory()->create(['email' => 'admin@pscranker.com']);
    $adminResponse = $this->actingAs($admin)->get(route('session.show', $premiumUnit->slug));
    $adminResponse->assertStatus(200);
    $adminResponse->assertDontSee('PRO Unit Locked');
    $adminResponse->assertSee('Unit 1 of 1');
});

test('admin can create a premium session with custom price', function () {
    $admin = User::factory()->create(['email' => 'admin@pscranker.com']);

    $response = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'Pro Kerala Renaissance Capsule',
        'slug' => 'pro-renaissance-capsule',
        'order' => 10,
        'xp_reward' => 300,
        'is_active' => '1',
        'is_premium' => '1',
        'price' => 149.00,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('learning_sessions', [
        'title' => 'Pro Kerala Renaissance Capsule',
        'is_premium' => true,
        'price' => 149.00,
    ]);
});

test('home page hero leads students to course units catalog', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('START COURSE UNITS');
    $response->assertSee(route('sessions.index'));
    $response->assertSee('UNIT BY UNIT');
});

test('navigation uses single unified bottom bar and next session is unlocked upon completion', function () {
    $category = Category::create(['name' => 'General', 'slug' => 'general', 'order' => 1]);
    $user = User::factory()->create();

    $unit1 = Session::create([
        'title' => 'Unit 1: Foundations',
        'slug' => 'unit-1-foundations',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 100,
        'is_active' => true,
    ]);

    $unit2 = Session::create([
        'title' => 'Unit 2: Mastery',
        'slug' => 'unit-2-mastery',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 100,
        'is_active' => true,
    ]);

    // 1. Session runner has single unified bottom bar with previous/next unit controls
    $inProgressResponse = $this->actingAs($user)->get(route('session.show', $unit1->slug));
    $inProgressResponse->assertStatus(200);
    $inProgressContent = $inProgressResponse->getContent();
    
    expect($inProgressContent)->toContain('STRICT CONTEXT-AWARE NAVIGATION');
    expect($inProgressContent)->toContain('Next Unit →');
    expect($inProgressContent)->toContain('Submit OMR Sheet');

    // 2. When session is marked completed in progress records
    \App\Models\UserSessionProgress::create([
        'user_id' => $user->id,
        'session_id' => $unit1->id,
        'current_phase' => 'summary',
        'completed_at' => now(),
        'net_marks' => 5.0,
        'xp_earned' => 100,
    ]);

    $completedResponse = $this->actingAs($user)->get(route('session.show', $unit1->slug));
    $completedResponse->assertStatus(200);
    $completedContent = $completedResponse->getContent();

    expect($completedContent)->toContain('Next Session ➔');
    expect($completedContent)->toContain('CONTINUE TO NEXT UNIT');
});

test('new session form loads with paid premium tier by default and 3-tier toggles', function () {
    $admin = User::factory()->create(['email' => 'admin-tier@pscranker.com', 'is_admin' => true]);

    $response = $this->actingAs($admin)->get(route('admin.sessions.create'));
    $response->assertStatus(200);
    $response->assertSee('Monetization &amp; Access Tier', false);
    $response->assertSee('Free (Public)');
    $response->assertSee('Registered');
    $response->assertSee('Paid (PRO)');
    $response->assertSee('Paid by Default');
    $response->assertSee('Upload Image');
    $response->assertSee('Upload Audio');
    $response->assertSee('Upload Video');
});

test('creating session defaults to paid premium tier when tier is not specified', function () {
    $admin = User::factory()->create(['email' => 'admin-tier2@pscranker.com', 'is_admin' => true]);
    $category = Category::firstOrCreate(['slug' => 'polity'], ['name' => 'Polity', 'order' => 1]);

    $response = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'Default Paid Session Example',
        'slug' => 'default-paid-session-example',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => '1',
    ]);

    $response->assertRedirect();
    $session = Session::where('slug', 'default-paid-session-example')->firstOrFail();
    expect($session->access_level)->toBe('premium');
    expect($session->is_premium)->toBeTrue();
    expect((float)$session->price)->toBe(199.00);
    expect($session->isFree())->toBeFalse();
});

test('admin can toggle session to registered or free tier and gating works accordingly', function () {
    $admin = User::factory()->create(['email' => 'admin-tier3@pscranker.com', 'is_admin' => true]);
    $category = Category::firstOrCreate(['slug' => 'economics'], ['name' => 'Economics', 'order' => 2]);

    // 1. Create a Registered-only session
    $regResponse = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'Registered Members Only Unit',
        'slug' => 'registered-members-only-unit',
        'category_id' => $category->id,
        'order' => 2,
        'xp_reward' => 150,
        'is_active' => '1',
        'access_level' => 'registered',
        'is_premium' => '0',
    ]);

    $regResponse->assertRedirect();
    $regSession = Session::where('slug', 'registered-members-only-unit')->firstOrFail();
    expect($regSession->access_level)->toBe('registered');
    expect($regSession->is_premium)->toBeFalse();

    // Guest (unauthenticated) visiting registered session sees registration requirement
    auth()->logout();
    $guestResponse = $this->get(route('session.show', $regSession->slug));
    $guestResponse->assertStatus(200);
    $guestResponse->assertSee('Free Registration Required');
    $guestResponse->assertSee('REGISTER FREE ACCOUNT');

    // Registered candidate visiting can access without paywall
    $candidate = User::factory()->create();
    $candidateResponse = $this->actingAs($candidate)->get(route('session.show', $regSession->slug));
    $candidateResponse->assertStatus(200);
    $candidateResponse->assertDontSee('Free Registration Required');

    // 2. Create a Free Public session
    $freeResponse = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'Open Public Free Unit',
        'slug' => 'open-public-free-unit',
        'category_id' => $category->id,
        'order' => 3,
        'xp_reward' => 100,
        'is_active' => '1',
        'access_level' => 'guest',
        'is_premium' => '0',
    ]);

    $freeResponse->assertRedirect();
    $freeSession = Session::where('slug', 'open-public-free-unit')->firstOrFail();
    expect($freeSession->access_level)->toBe('guest');
    expect($freeSession->is_premium)->toBeFalse();

    auth()->logout();
    $publicGuestResponse = $this->get(route('session.show', $freeSession->slug));
    $publicGuestResponse->assertStatus(200);
    $publicGuestResponse->assertDontSee('Free Registration Required');
    $publicGuestResponse->assertDontSee('PRO Unit Locked');
});

test('admin session builder starts with clean unit without unwanted default blocks', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get(route('admin.sessions.create'));
    $response->assertStatus(200);
    $response->assertSee('This unit starts empty — no unwanted default blocks');
    $response->assertSee('+ Hook MCQ');
    $response->assertSee('+ Practice MCQ');
    $response->assertSee('+ Text Block');
});

test('admin can view created session in both in-progress and finished view modes', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::firstOrCreate(['slug' => 'polity'], ['name' => 'Indian Polity', 'order' => 1]);

    // 1. Create a session (even in draft mode)
    $createResponse = $this->actingAs($admin)->post(route('admin.sessions.store'), [
        'title' => 'Constitutional Preamble Studio Test',
        'slug' => 'constitutional-preamble-test',
        'category_id' => $category->id,
        'order' => 1,
        'xp_reward' => 200,
        'is_active' => '0', // Draft mode
        'access_level' => 'premium',
        'is_premium' => '1',
        'price' => 199.00,
    ]);

    $createResponse->assertRedirect();
    $createResponse->assertSessionHas('view_url');
    $createResponse->assertSessionHas('finished_url');

    $session = Session::where('slug', 'constitutional-preamble-test')->firstOrFail();

    // 2. Draft session is accessible to admin
    $adminRunnerResponse = $this->actingAs($admin)->get(route('session.show', $session->slug));
    $adminRunnerResponse->assertStatus(200);
    $adminRunnerResponse->assertSee('Admin Inspector');
    $adminRunnerResponse->assertSee('In-Progress Stepper');
    $adminRunnerResponse->assertSee('Finished Scorecard');
    $adminRunnerResponse->assertSee('DRAFT (Unpublished)');

    // 3. Draft session is NOT accessible to public guest
    auth()->logout();
    $guestResponse = $this->get(route('session.show', $session->slug));
    $guestResponse->assertStatus(404);

    // 4. Admin can view finished session scorecard mode via query parameter
    $finishedResponse = $this->actingAs($admin)->get(route('session.show', ['slug' => $session->slug, 'preview' => 'finished']));
    $finishedResponse->assertStatus(200);
    $finishedResponse->assertSee('Admin Inspector');

    // 5. Index page has View Live and Finished buttons
    $indexResponse = $this->actingAs($admin)->get(route('admin.sessions.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('👁️ Live ↗');
    $indexResponse->assertSee('🏁 Finished');
});


