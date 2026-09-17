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
