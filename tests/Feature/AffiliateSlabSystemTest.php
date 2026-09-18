<?php

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLead;
use App\Models\AffiliateSlab;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Services\AffiliateAttributionService;
use App\Services\AffiliateSlabService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('default 10 target vs payout slabs are seeded from migration matching client spreadsheet', function () {
    $slabs = AffiliateSlab::orderBy('order', 'asc')->get();

    expect($slabs->count())->toBe(10);

    // Slab 1: 1 - 10,000 @ 10% (10% basic + 0% bonus)
    $slab1 = $slabs[0];
    expect((float)$slab1->min_target)->toBe(1.0)
        ->and((float)$slab1->max_target)->toBe(10000.0)
        ->and((float)$slab1->basic_payout_percentage)->toBe(10.0)
        ->and((float)$slab1->bonus_percentage)->toBe(0.0)
        ->and((float)$slab1->total_payout_percentage)->toBe(10.0);

    // Slab 4: 30,001 - 40,000 @ 19% (10% basic + 9% bonus)
    $slab4 = $slabs[3];
    expect((float)$slab4->min_target)->toBe(30001.0)
        ->and((float)$slab4->max_target)->toBe(40000.0)
        ->and((float)$slab4->basic_payout_percentage)->toBe(10.0)
        ->and((float)$slab4->bonus_percentage)->toBe(9.0)
        ->and((float)$slab4->total_payout_percentage)->toBe(19.0);

    // Slab 10: 150,001+ @ 31% (10% basic + 21% bonus)
    $slab10 = $slabs[9];
    expect((float)$slab10->min_target)->toBe(150001.0)
        ->and($slab10->max_target)->toBeNull()
        ->and((float)$slab10->basic_payout_percentage)->toBe(10.0)
        ->and((float)$slab10->bonus_percentage)->toBe(21.0)
        ->and((float)$slab10->total_payout_percentage)->toBe(31.0);
});

test('slab service precisely calculates client spreadsheet example (sales 32456 in slab 4 @ 19% total)', function () {
    $service = app(AffiliateSlabService::class);

    $slab = $service->getSlabForSales(32456.00);

    expect($slab)->not->toBeNull()
        ->and($slab->slab_code)->toContain('-004')
        ->and((float)$slab->basic_payout_percentage)->toBe(10.0)
        ->and((float)$slab->bonus_percentage)->toBe(9.0)
        ->and((float)$slab->total_payout_percentage)->toBe(19.0);

    $calc = $service->calculatePayout(32456.00, $slab);

    // 32456 * 10% = 3245.60 base
    // 32456 * 9%  = 2921.04 bonus
    // Total = 6166.64 (matches client's rounded ₹6,167)
    expect($calc['basic_amount'])->toBe(3245.60)
        ->and($calc['bonus_amount'])->toBe(2921.04)
        ->and($calc['total_amount'])->toBe(6166.64)
        ->and(round($calc['total_amount']))->toBe(6167.0);
});

test('affiliate dashboard displays target vs payout structure and next slab tracker', function () {
    $user = User::factory()->create(['phone' => '9895000001']);
    $affiliate = Affiliate::create([
        'user_id' => $user->id,
        'affiliate_code' => 'PSC-TEST01',
        'status' => 'active',
        'commission_rate' => 10.00,
        'payout_method' => 'upi',
        'payout_details' => ['upi_id' => 'test@upi'],
    ]);

    // Create a commission of 32,456 sales in current month
    $nowMonth = now()->format('Y-m');
    AffiliateCommission::create([
        'affiliate_id' => $affiliate->id,
        'affiliate_lead_id' => null,
        'subscription_payment_id' => null,
        'user_id' => null,
        'course_amount' => 32456.00,
        'commission_rate' => 10.00,
        'commission_amount' => 3245.60,
        'bonus_amount' => 2921.04,
        'total_amount' => 6166.64,
        'period_month' => $nowMonth,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('affiliate.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Target vs Payout Structure');
    $response->assertSee('PRSL-');
    $response->assertSee('Sales Value');
    $response->assertSee('32,456');
    $response->assertSee('6,167'); // rounded or formatted total
    $response->assertSee('Avg. Sales / Day');
    $response->assertSee('Avg. Earnings / Day');
    $response->assertSee('Next Slab Target:');
    $response->assertSee('Slab 5');
});

test('affiliate attribution automatically applies the progressive slab rate and bonus', function () {
    $promoter = User::factory()->create(['phone' => '9895111111']);
    $affiliate = Affiliate::create([
        'user_id' => $promoter->id,
        'affiliate_code' => 'PSC-PROMO1',
        'status' => 'active',
        'commission_rate' => 10.00,
    ]);

    $student = User::factory()->create(['phone' => '9895222222']);

    // Log a lead for this student
    AffiliateLead::create([
        'affiliate_id' => $affiliate->id,
        'candidate_name' => 'Rahul Nair',
        'candidate_phone' => '9895222222',
        'status' => 'lead',
        'source' => 'manual',
    ]);

    $payment = SubscriptionPayment::create([
        'user_id' => $student->id,
        'razorpay_order_id' => 'order_test_slab_123',
        'razorpay_payment_id' => 'pay_test_slab_123',
        'amount' => 35000.00, // falls into Slab 4 (30,001 - 40,000) @ 19%
        'currency' => 'INR',
        'duration_months' => 12,
        'status' => 'paid',
        'payment_metadata' => [
            'customer_phone' => '9895222222',
        ],
    ]);

    $attributionService = app(AffiliateAttributionService::class);
    $commission = $attributionService->attributePayment($payment);

    expect($commission)->not->toBeNull()
        ->and((float)$commission->course_amount)->toBe(35000.00)
        ->and((float)$commission->commission_rate)->toBe(19.00)
        ->and((float)$commission->commission_amount)->toBe(3500.00)
        ->and((float)$commission->bonus_amount)->toBe(3150.00) // 9% bonus
        ->and((float)$commission->total_amount)->toBe(6650.00); // 35000 * 19% = 6650
});

test('admin can view and update slab basic payout and bonus percentages', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $slab1 = AffiliateSlab::where('order', 1)->first();
    $slab2 = AffiliateSlab::where('order', 2)->first();

    $response = $this->actingAs($admin)->get(route('admin.affiliates.index', ['tab' => 'slabs']));

    $response->assertStatus(200);
    $response->assertSee('Target vs Payout Slabs Configuration');
    $response->assertSee('Basic Payout %');
    $response->assertSee('Bonus %');
    $response->assertSee($slab1->slab_code);

    // Update Slab 1 basic % to 12% and Slab 2 bonus to 5%
    $slabsPayload = [
        [
            'id' => $slab1->id,
            'min_target' => 1,
            'max_target' => 10000,
            'basic_payout_percentage' => 12.00,
            'bonus_percentage' => 1.50,
        ],
        [
            'id' => $slab2->id,
            'min_target' => 10001,
            'max_target' => 20000,
            'basic_payout_percentage' => 12.00,
            'bonus_percentage' => 5.00,
        ],
    ];

    // Keep the other slabs intact in payload
    $allSlabs = AffiliateSlab::whereNotIn('id', [$slab1->id, $slab2->id])->get();
    foreach ($allSlabs as $otherSlab) {
        $slabsPayload[] = [
            'id' => $otherSlab->id,
            'min_target' => $otherSlab->min_target,
            'max_target' => $otherSlab->max_target,
            'basic_payout_percentage' => $otherSlab->basic_payout_percentage,
            'bonus_percentage' => $otherSlab->bonus_percentage,
        ];
    }

    $updateResponse = $this->actingAs($admin)->post(route('admin.affiliates.slabs.update'), [
        'slabs' => $slabsPayload,
    ]);

    $updateResponse->assertRedirect(route('admin.affiliates.index', ['tab' => 'slabs']));

    $slab1->refresh();
    expect((float)$slab1->basic_payout_percentage)->toBe(12.00)
        ->and((float)$slab1->bonus_percentage)->toBe(1.50)
        ->and((float)$slab1->total_payout_percentage)->toBe(13.50);
});

test('admin can trigger recalculation of monthly commissions based on revised slabs', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $promoter = User::factory()->create(['phone' => '9895333333']);
    $affiliate = Affiliate::create([
        'user_id' => $promoter->id,
        'affiliate_code' => 'PSC-RECALC',
        'status' => 'active',
        'commission_rate' => 10.00,
    ]);

    $period = now()->format('Y-m');

    // Commission recorded at 10% flat base with 0 bonus (total 1000)
    $comm = AffiliateCommission::create([
        'affiliate_id' => $affiliate->id,
        'affiliate_lead_id' => null,
        'subscription_payment_id' => null,
        'user_id' => null,
        'course_amount' => 10000.00,
        'commission_rate' => 10.00,
        'commission_amount' => 1000.00,
        'bonus_amount' => 0.00,
        'total_amount' => 1000.00,
        'period_month' => $period,
        'status' => 'pending',
    ]);

    // Admin updates Slab 1 to have 10% basic + 4% bonus = 14%
    $slab1 = AffiliateSlab::where('order', 1)->first();
    $slab1->update([
        'bonus_percentage' => 4.00,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.affiliates.slabs.recalculate'), [
        'period_month' => $period,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $comm->refresh();
    // 10000 * 10% = 1000 base + 10000 * 4% = 400 bonus = 1400 total
    expect((float)$comm->bonus_amount)->toBe(400.00)
        ->and((float)$comm->total_amount)->toBe(1400.00);
});
