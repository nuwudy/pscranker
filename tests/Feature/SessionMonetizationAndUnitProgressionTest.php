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
    $category = Category::create(['name' => 'History', 'slug' => 'history', 'order' => 1]);

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
