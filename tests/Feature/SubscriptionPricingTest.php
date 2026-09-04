<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pricing page loads successfully with progressive rebate tiers', function () {
    $response = $this->get('/pricing');

    $response->assertStatus(200);
    $response->assertSee('Prepaid Learning Pass');
    $response->assertSee('Select Your Prep Duration');
    $response->assertSee('1 Month Flex Pass');
    $response->assertSee('3 Months Exam Sprint');
    $response->assertSee('1 Year All-Access Pass');
    $response->assertSee('Terms &amp; Conditions', false);
    $response->assertSee('Privacy Policy', false);
    $response->assertSee('Cancellation &amp; Refund Policy', false);
    $response->assertSee('Razorpay Verified');
});

test('progressive rebate schedule calculates correct discounts from base fee', function () {
    SiteSetting::set('course_base_monthly_fee', 300);
    SiteSetting::set('rebate_2m', 10);
    SiteSetting::set('rebate_3m', 15);
    SiteSetting::set('rebate_6m', 25);
    SiteSetting::set('rebate_12m', 40);

    $tiers = collect(SiteSetting::getPricingTiers())->keyBy('months');

    // 1 Month: 300 * 1 = 300, 0% rebate = 300
    expect($tiers[1]['base_total'])->toBe(300.0)
        ->and($tiers[1]['discount_amount'])->toBe(0.0)
        ->and($tiers[1]['final_price'])->toBe(300.0);

    // 2 Months: 300 * 2 = 600, 10% rebate = 60 saved, 540 final
    expect($tiers[2]['base_total'])->toBe(600.0)
        ->and($tiers[2]['discount_amount'])->toBe(60.0)
        ->and($tiers[2]['final_price'])->toBe(540.0)
        ->and($tiers[2]['effective_per_month'])->toBe(270.0);

    // 3 Months: 300 * 3 = 900, 15% rebate = 135 saved, 765 final
    expect($tiers[3]['base_total'])->toBe(900.0)
        ->and($tiers[3]['discount_amount'])->toBe(135.0)
        ->and($tiers[3]['final_price'])->toBe(765.0)
        ->and($tiers[3]['effective_per_month'])->toBe(255.0);

    // 6 Months: 300 * 6 = 1800, 25% rebate = 450 saved, 1350 final
    expect($tiers[6]['base_total'])->toBe(1800.0)
        ->and($tiers[6]['discount_amount'])->toBe(450.0)
        ->and($tiers[6]['final_price'])->toBe(1350.0);

    // 12 Months: 300 * 12 = 3600, 40% rebate = 1440 saved, 2160 final
    expect($tiers[12]['base_total'])->toBe(3600.0)
        ->and($tiers[12]['discount_amount'])->toBe(1440.0)
        ->and($tiers[12]['final_price'])->toBe(2160.0)
        ->and($tiers[12]['effective_per_month'])->toBe(180.0);
});

test('razorpay order creation and payment verification activates subscription', function () {
    $user = User::factory()->create(['email' => 'student@example.com']);
    $this->actingAs($user);

    // 1. Create Order
    $orderResponse = $this->postJson('/subscription/create-order', [
        'months' => 3,
        'name' => 'Student Candidate',
        'email' => 'student@example.com',
    ]);

    $orderResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'months' => 3,
        ]);

    $orderId = $orderResponse->json('order_id');
    expect($orderId)->not->toBeNull();

    // 2. Verify Payment
    $verifyResponse = $this->postJson('/subscription/verify-payment', [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => 'pay_test_' . uniqid(),
    ]);

    $verifyResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    // 3. Check User has been granted subscription
    $user->refresh();
    expect($user->isSubscribed())->toBeTrue()
        ->and($user->subscribed_until->isFuture())->toBeTrue()
        ->and($user->subscription_plan)->toBe('3 Months Plan');
});

test('admin can update base monthly fee and rebate percentages from dashboard', function () {
    $admin = User::factory()->create(['email' => 'admin@pscranker.com']);
    $this->actingAs($admin);

    $response = $this->post('/admin/settings/pricing', [
        'course_base_monthly_fee' => 399,
        'rebate_2m' => 12,
        'rebate_3m' => 18,
        'rebate_6m' => 28,
        'rebate_12m' => 45,
        'razorpay_key_id' => 'rzp_test_customKey123',
        'razorpay_key_secret' => 'customSecret456',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(SiteSetting::get('course_base_monthly_fee'))->toBe('399')
        ->and(SiteSetting::get('rebate_12m'))->toBe('45')
        ->and(SiteSetting::get('razorpay_key_id'))->toBe('rzp_test_customKey123');
});

test('all mandatory razorpay compliance legal pages load properly', function () {
    $this->get('/terms')->assertStatus(200)->assertSee('Terms and Conditions');
    $this->get('/privacy')->assertStatus(200)->assertSee('Privacy Policy');
    $this->get('/refund-policy')->assertStatus(200)->assertSee('Cancellation &amp; Refund Policy', false);
    $this->get('/contact')->assertStatus(200)->assertSee('Contact Us &amp; Student Support', false);
});

test('subscribed student can access premium sessions without lock', function () {
    $category = Category::create([
        'name' => 'General Science',
        'slug' => 'general-science',
        'order' => 1,
    ]);

    $premiumSession = Session::create([
        'title' => 'Advanced Blood Circulation & Heart Anatomy',
        'slug' => 'advanced-blood-circulation',
        'category_id' => $category->id,
        'order' => 2,
        'is_active' => true,
        'is_premium' => true,
        'price' => 299,
    ]);

    // Question for session
    Question::create([
        'session_id' => $premiumSession->id,
        'category_id' => $category->id,
        'phase_type' => 'diagnostic',
        'question_text' => 'Which chamber pumps oxygenated blood?',
        'option_a' => 'Left Ventricle',
        'option_b' => 'Right Ventricle',
        'option_c' => 'Left Atrium',
        'option_d' => 'Right Atrium',
        'correct_option' => 'A',
    ]);

    // Unsubscribed student sees lock
    $freeStudent = User::factory()->create();
    $this->actingAs($freeStudent);
    $lockedResponse = $this->get(route('session.show', $premiumSession->slug));
    $lockedResponse->assertStatus(200);
    $lockedResponse->assertSee('PRO Unit Locked');
    $lockedResponse->assertSee('Unlock with UPI / PhonePe / Razorpay');

    // Subscribed student bypasses lock and accesses 4-phase micro loop
    $subscribedStudent = User::factory()->create([
        'subscribed_until' => now()->addMonths(3),
        'subscription_plan' => '3 Months Plan',
    ]);
    $this->actingAs($subscribedStudent);
    $unlockedResponse = $this->get(route('session.show', $premiumSession->slug));
    $unlockedResponse->assertStatus(200);
    $unlockedResponse->assertDontSee('This Unit is Locked');
    $unlockedResponse->assertSee('Advanced Blood Circulation & Heart Anatomy');
});
