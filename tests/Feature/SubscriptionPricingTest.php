<?php

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pricing page loads successfully with 6 progressive rebate cards and daily pricing framework', function () {
    $response = $this->get('/pricing');

    $response->assertStatus(200);
    $response->assertSee('Kerala PSC Prep from just');
    $response->assertSee('Select Your Target Pass');
    $response->assertSee('1 Day Flex Pass');
    $response->assertSee('1 Week Crash Pass');
    $response->assertSee('1 Month Regular Pass');
    $response->assertSee('3 Months Exam Sprint');
    $response->assertSee('6 Months Semester Pass');
    $response->assertSee('1 Year All-Access Pass');
    $response->assertSee('Schedule of Progressive Rebates');
    $response->assertSee('Terms &amp; Conditions', false);
    $response->assertSee('Privacy Policy', false);
    $response->assertSee('Cancellation &amp; Refund Policy', false);
    $response->assertSee('Razorpay Gateway');
});

test('progressive rebate schedule calculates correct discounts from daily base fee', function () {
    // Test Balanced Growth Model at ₹10 base daily rate
    SiteSetting::set('course_base_daily_fee', 10);
    SiteSetting::set('rebate_1w', 14);
    SiteSetting::set('rebate_1m', 25);
    SiteSetting::set('rebate_3m', 33);
    SiteSetting::set('rebate_6m', 40);
    SiteSetting::set('rebate_1y', 45);

    $tiers = collect(SiteSetting::getPricingTiers())->keyBy('days');

    // 1 Day: ₹10 * 1 = ₹10, 0% off = ₹10 (₹10/day)
    expect($tiers[1]['base_total'])->toBe(10.0)
        ->and($tiers[1]['discount_amount'])->toBe(0.0)
        ->and($tiers[1]['final_price'])->toBe(10.0)
        ->and($tiers[1]['per_day_cost'])->toBe(10.0);

    // 1 Week (7 Days): ₹10 * 7 = ₹70, 14% off = ₹60 (₹8.57/day)
    expect($tiers[7]['base_total'])->toBe(70.0)
        ->and($tiers[7]['discount_amount'])->toBe(10.0)
        ->and($tiers[7]['final_price'])->toBe(60.0)
        ->and($tiers[7]['per_day_cost'])->toBe(8.57);

    // 1 Month (30 Days): ₹10 * 30 = ₹300, 25% off = ₹225 (₹7.50/day)
    expect($tiers[30]['base_total'])->toBe(300.0)
        ->and($tiers[30]['discount_amount'])->toBe(75.0)
        ->and($tiers[30]['final_price'])->toBe(225.0)
        ->and($tiers[30]['per_day_cost'])->toBe(7.50);

    // 3 Months (90 Days): ₹10 * 90 = ₹900, 33% off = ₹600 (₹6.67/day)
    expect($tiers[90]['base_total'])->toBe(900.0)
        ->and($tiers[90]['discount_amount'])->toBe(300.0)
        ->and($tiers[90]['final_price'])->toBe(600.0)
        ->and($tiers[90]['per_day_cost'])->toBe(6.67);

    // 6 Months (180 Days): ₹10 * 180 = ₹1800, 40% off = ₹1080 (₹6.00/day)
    expect($tiers[180]['base_total'])->toBe(1800.0)
        ->and($tiers[180]['discount_amount'])->toBe(720.0)
        ->and($tiers[180]['final_price'])->toBe(1080.0)
        ->and($tiers[180]['per_day_cost'])->toBe(6.00);

    // 1 Year (365 Days): ₹10 * 365 = ₹3650, 45% off with psychological 99 rounding = ₹1999 (₹5.48/day)
    expect($tiers[365]['base_total'])->toBe(3650.0)
        ->and($tiers[365]['final_price'])->toBe(1999.0)
        ->and($tiers[365]['per_day_cost'])->toBe(5.48);
});

test('admin setting daily base rate to ₹5 recalculates all tiers proportionally', function () {
    SiteSetting::set('course_base_daily_fee', 5);

    $tiers = collect(SiteSetting::getPricingTiers())->keyBy('days');

    // 1 Day at ₹5 = ₹5
    expect($tiers[1]['final_price'])->toBe(5.0)
        ->and($tiers[1]['per_day_cost'])->toBe(5.0);

    // 1 Week (7 Days) at ₹5 = ₹35, 14% off = ₹30 (₹4.29/day)
    expect($tiers[7]['final_price'])->toBe(30.0);

    // 1 Month (30 Days) at ₹5 = ₹150, 25% off = ₹113
    expect($tiers[30]['final_price'])->toBe(113.0);
});

test('razorpay order creation and payment verification activates subscription by days', function () {
    $user = User::factory()->create(['email' => 'student@example.com']);
    $this->actingAs($user);

    // 1. Create Order with 7 days pass
    $orderResponse = $this->postJson('/subscription/create-order', [
        'days' => 7,
        'name' => 'Student Candidate',
        'email' => 'student@example.com',
    ]);

    $orderResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'days' => 7,
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

    // 3. Check User has been granted subscription with duration_days
    $user->refresh();
    expect($user->isSubscribed())->toBeTrue()
        ->and($user->subscribed_until->isFuture())->toBeTrue()
        ->and($user->subscription_plan)->toBe('1 Week Crash Pass');
});

test('admin can update daily base fee and rebate percentages from dashboard', function () {
    $admin = User::factory()->create(['email' => 'admin@pscranker.com']);
    $this->actingAs($admin);

    $response = $this->post('/admin/settings/pricing', [
        'course_base_daily_fee' => 5,
        'rebate_1w' => 15,
        'rebate_1m' => 25,
        'rebate_3m' => 35,
        'rebate_6m' => 42,
        'rebate_1y' => 50,
        'razorpay_key_id' => 'rzp_test_customKey123',
        'razorpay_key_secret' => 'customSecret456',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(SiteSetting::get('course_base_daily_fee'))->toBe('5')
        ->and(SiteSetting::get('rebate_1y'))->toBe('50')
        ->and(SiteSetting::get('razorpay_key_id'))->toBe('rzp_test_customKey123');
});

test('all mandatory razorpay compliance legal pages load properly', function () {
    $this->get('/terms')
        ->assertStatus(200)
        ->assertSee('Terms and Conditions')
        ->assertSee('9495 249 224');

    $this->get('/privacy')
        ->assertStatus(200)
        ->assertSee('Privacy Policy')
        ->assertSee('9495 249 224');

    $this->get('/refund-policy')
        ->assertStatus(200)
        ->assertSee('Cancellation &amp; Refund Policy', false)
        ->assertSee('5 to 7 business days')
        ->assertSee('9495 249 224');

    $this->get('/shipping-policy')
        ->assertStatus(200)
        ->assertSee('Shipping &amp; Delivery Policy', false)
        ->assertSee('instantaneously')
        ->assertSee('9495 249 224');

    $this->get('/about')
        ->assertStatus(200)
        ->assertSee('About PSCRanker')
        ->assertSee('9495 249 224');

    $this->get('/contact')
        ->assertStatus(200)
        ->assertSee('Contact Us &amp; Student Support', false)
        ->assertSee('infopscranker@gmail.com')
        ->assertSee('9495 249 224');
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
