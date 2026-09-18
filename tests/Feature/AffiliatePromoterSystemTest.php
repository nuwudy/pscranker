<?php

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLead;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Services\AffiliateAttributionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public affiliate partner onboarding page renders successfully', function () {
    $response = $this->get('/affiliate/join');

    $response->assertStatus(200);
    $response->assertSee('PSCRANKER PARTNER PROGRAM');
    $response->assertSee('Guide Aspirants to Success');
    $response->assertSee('Earn Direct Monthly Commissions');
    $response->assertSee('Apply to Become a Partner');
});

test('prospective affiliate can apply and gets activated immediately', function () {
    $response = $this->post('/affiliate/join', [
        'name' => 'Anu Krishna',
        'phone' => '+91 98950 12345',
        'email' => 'anu@example.com',
        'password' => 'secret123',
        'upi_id' => 'anu@okaxis',
        'notes' => 'Experienced PSC tele-promoter',
    ]);

    $response->assertRedirect(route('affiliate.dashboard'));

    $user = User::where('email', 'anu@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->phone)->toBe('9895012345')
        ->and($user->isAffiliate())->toBeTrue();

    $affiliate = $user->affiliate;
    expect($affiliate)->not->toBeNull()
        ->and($affiliate->status)->toBe('active')
        ->and((float)$affiliate->commission_rate)->toBe(15.0)
        ->and($affiliate->payout_details['upi_id'])->toBe('anu@okaxis');
});

test('affiliate can add prospect leads and phone is normalized to 10 digits', function () {
    $user = User::factory()->create(['phone' => '9895000001']);
    $affiliate = Affiliate::create([
        'user_id' => $user->id,
        'affiliate_code' => 'PSC-ANU01',
        'status' => 'active',
        'commission_rate' => 15.00,
    ]);

    $response = $this->actingAs($user)->post('/affiliate/leads', [
        'candidate_name' => 'Santhosh Kumar',
        'candidate_phone' => '+91 91234 56789',
        'notes' => 'Called today, interested in LDC course',
    ]);

    $response->assertRedirect(route('affiliate.dashboard'));
    $response->assertSessionHas('success');

    $lead = AffiliateLead::where('candidate_phone', '9123456789')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->candidate_name)->toBe('Santhosh Kumar')
        ->and($lead->affiliate_id)->toBe($affiliate->id)
        ->and($lead->status)->toBe('lead');
});

test('duplicate active lead cannot be claimed by another promoter', function () {
    $user1 = User::factory()->create(['phone' => '9895000001']);
    $affiliate1 = Affiliate::create([
        'user_id' => $user1->id,
        'affiliate_code' => 'PSC-ANU01',
        'status' => 'active',
    ]);

    AffiliateLead::create([
        'affiliate_id' => $affiliate1->id,
        'candidate_name' => 'Santhosh Kumar',
        'candidate_phone' => '9123456789',
        'status' => 'lead',
        'valid_until' => now()->addDays(60),
    ]);

    $user2 = User::factory()->create(['phone' => '9895000002']);
    $affiliate2 = Affiliate::create([
        'user_id' => $user2->id,
        'affiliate_code' => 'PSC-BOB02',
        'status' => 'active',
    ]);

    $response = $this->actingAs($user2)->post('/affiliate/leads', [
        'candidate_name' => 'Santhosh K',
        'candidate_phone' => '9123456789',
    ]);

    $response->assertSessionHas('error');
    expect(AffiliateLead::where('candidate_phone', '9123456789')->count())->toBe(1);
});

test('attribution engine automatically attributes conversion and calculates commission when student pays', function () {
    $promoterUser = User::factory()->create(['phone' => '9895000001', 'name' => 'Anu Promoter']);
    $affiliate = Affiliate::create([
        'user_id' => $promoterUser->id,
        'affiliate_code' => 'PSC-ANU01',
        'status' => 'active',
        'commission_rate' => 20.00, // 20% commission
    ]);

    $lead = AffiliateLead::create([
        'affiliate_id' => $affiliate->id,
        'candidate_name' => 'Santhosh Candidate',
        'candidate_phone' => '9123456789',
        'status' => 'lead',
        'valid_until' => now()->addDays(60),
    ]);

    // Student signs up with 9123456789
    $student = User::factory()->create([
        'name' => 'Santhosh Student',
        'phone' => '9123456789',
        'email' => 'santhosh@example.com',
    ]);

    // Payment occurs
    $payment = SubscriptionPayment::create([
        'user_id' => $student->id,
        'razorpay_order_id' => 'order_test_123',
        'razorpay_payment_id' => 'pay_test_456',
        'amount' => 1999.00,
        'currency' => 'INR',
        'duration_months' => 6,
        'status' => 'paid',
        'payment_metadata' => [
            'customer_phone' => '9123456789',
        ],
    ]);

    $attributionService = app(AffiliateAttributionService::class);
    $commission = $attributionService->recordConversion(
        student: $student,
        payment: $payment,
        courseAmount: 1999.00
    );

    expect($commission)->not->toBeNull()
        ->and($commission->affiliate_id)->toBe($affiliate->id)
        ->and((float)$commission->course_amount)->toBe(1999.00)
        ->and((float)$commission->commission_rate)->toBe(20.00)
        ->and((float)$commission->commission_amount)->toBe(399.80) // 20% of 1999
        ->and((float)$commission->total_amount)->toBe(399.80)
        ->and($commission->status)->toBe('pending');

    $lead->refresh();
    expect($lead->status)->toBe('converted')
        ->and($lead->converted_user_id)->toBe($student->id);
});

test('admin can view affiliate hub, add bonus, and execute monthly disbursement with UTR', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'phone' => '9895940500',
        'is_admin' => true,
    ]);

    $promoterUser = User::factory()->create(['name' => 'Anu Promoter', 'phone' => '9895000001']);
    $affiliate = Affiliate::create([
        'user_id' => $promoterUser->id,
        'affiliate_code' => 'PSC-ANU01',
        'status' => 'active',
        'commission_rate' => 15.00,
        'payout_details' => ['upi_id' => 'anu@okaxis'],
    ]);

    $month = now()->subMonth()->format('Y-m');

    // Create a pending commission
    AffiliateCommission::create([
        'affiliate_id' => $affiliate->id,
        'course_amount' => 1000.00,
        'commission_rate' => 15.00,
        'commission_amount' => 150.00,
        'bonus_amount' => 50.00,
        'total_amount' => 200.00,
        'period_month' => $month,
        'status' => 'pending',
    ]);

    // Admin views affiliate hub
    $response = $this->actingAs($admin)->get(route('admin.affiliates.index', ['tab' => 'disbursements', 'month' => $month]));
    $response->assertStatus(200);
    $response->assertSee('Anu Promoter');
    $response->assertSee('anu@okaxis');

    // Admin adds an extra bonus
    $this->actingAs($admin)->post("/admin/affiliates/{$affiliate->id}/bonus", [
        'bonus_amount' => 100,
        'period_month' => $month,
        'reason' => 'Target Achiever',
    ]);

    // Admin executes disbursement
    $disburseResponse = $this->actingAs($admin)->post("/admin/affiliates/{$affiliate->id}/disburse", [
        'period_month' => $month,
        'payout_reference' => 'UTR998877665544',
        'admin_notes' => 'Paid via GPay Business',
    ]);

    $disburseResponse->assertSessionHas('success');

    // Verify all commissions for this month are marked disbursed
    $unpaid = AffiliateCommission::where('affiliate_id', $affiliate->id)
        ->where('period_month', $month)
        ->where('status', 'pending')
        ->count();

    expect($unpaid)->toBe(0);

    $disbursed = AffiliateCommission::where('affiliate_id', $affiliate->id)
        ->where('period_month', $month)
        ->where('status', 'disbursed')
        ->get();

    expect($disbursed->count())->toBe(2)
        ->and($disbursed->first()->payout_reference)->toBe('UTR998877665544');
});

test('visiting referral link increments clicks and stores session and cookie', function () {
    $user = User::factory()->create();
    $affiliate = Affiliate::create([
        'user_id' => $user->id,
        'affiliate_code' => 'PSC-TESTLINK',
        'status' => 'active',
        'referral_clicks' => 0,
    ]);

    // First visit
    $response = $this->get('/?ref=PSC-TESTLINK');
    $response->assertStatus(200);
    $response->assertSessionHas('affiliate_ref', 'PSC-TESTLINK');
    $response->assertCookie('affiliate_ref', 'PSC-TESTLINK');

    $affiliate->refresh();
    expect($affiliate->referral_clicks)->toBe(1);

    // Same session browsing another page should not double count clicks
    $this->get('/about');
    $affiliate->refresh();
    expect($affiliate->referral_clicks)->toBe(1);
});

test('clean /ref/{code} route redirects to home with query parameter', function () {
    $user = User::factory()->create();
    $affiliate = Affiliate::create([
        'user_id' => $user->id,
        'affiliate_code' => 'PSC-CLEANURL',
        'status' => 'active',
    ]);

    $response = $this->get('/ref/PSC-CLEANURL');
    $response->assertRedirect('/?ref=PSC-CLEANURL');
});

test('affiliate dashboard displays copyable referral link and clicks count', function () {
    $user = User::factory()->create();
    $affiliate = Affiliate::create([
        'user_id' => $user->id,
        'affiliate_code' => 'PSC-MYLINK99',
        'status' => 'active',
        'commission_rate' => 20.00,
        'referral_clicks' => 42,
    ]);

    $response = $this->actingAs($user)->get(route('affiliate.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('PSC-MYLINK99');
    $response->assertSee('42');
    $response->assertSee('Copy Link');
    $response->assertSee('WhatsApp');
    $response->assertSee('Commission');
});

test('student signing up and purchasing via referral link earns affiliate commission', function () {
    $promoterUser = User::factory()->create(['phone' => '9895000099']);
    $affiliate = Affiliate::create([
        'user_id' => $promoterUser->id,
        'affiliate_code' => 'PSC-VIPREF',
        'status' => 'active',
        'commission_rate' => 15.00,
    ]);

    // Step 1: Candidate clicks referral link
    $this->get('/?ref=PSC-VIPREF');

    // Step 2: Candidate registers an account (never entered via phone lead)
    $studentUser = User::factory()->create([
        'name' => 'Link Registered Student',
        'phone' => '9988776655',
        'email' => 'linkstudent@example.com',
    ]);

    // Attribution service links student
    $service = app(AffiliateAttributionService::class);
    $service->linkRegisteredStudent($studentUser);

    // Lead should have been automatically generated with source = referral_link
    $lead = AffiliateLead::where('candidate_phone', '9988776655')->first();
    expect($lead)->not->toBeNull()
        ->and($lead->affiliate_id)->toBe($affiliate->id)
        ->and($lead->source)->toBe('referral_link')
        ->and($lead->status)->toBe('lead')
        ->and($lead->converted_user_id)->toBe($studentUser->id);

    // Step 3: Student purchases course subscription of ₹2,000
    $payment = SubscriptionPayment::create([
        'user_id' => $studentUser->id,
        'subscription_type' => 'super_ranker',
        'amount' => 2000.00,
        'currency' => 'INR',
        'duration_months' => 6,
        'razorpay_payment_id' => 'pay_link_test_123',
        'razorpay_order_id' => 'order_link_test_123',
        'status' => 'success',
    ]);

    $service->recordConversion(
        student: $studentUser,
        payment: $payment,
        courseAmount: 2000.00
    );

    $lead->refresh();
    expect($lead->status)->toBe('converted');

    // Step 4: Affiliate should have received 15% of 2000 = ₹300
    $commission = AffiliateCommission::where('affiliate_id', $affiliate->id)
        ->where('subscription_payment_id', $payment->id)
        ->first();

    expect($commission)->not->toBeNull()
        ->and((float)$commission->commission_rate)->toBe(15.0)
        ->and((float)$commission->commission_amount)->toBe(300.00)
        ->and($commission->status)->toBe('pending');
});
