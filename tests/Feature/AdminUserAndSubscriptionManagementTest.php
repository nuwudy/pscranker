<?php

use App\Models\SubscriptionPayment;
use App\Models\User;

test('admin can view user management directory', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Anand Krishna',
        'phone' => '9847123456',
        'email' => 'anand@example.com',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));
    $response->assertStatus(200);
    $response->assertSee('Candidates');
    $response->assertSee('Subscriptions');
    $response->assertSee('Anand Krishna');
    $response->assertSee('9847123456');
});

test('account creation strictly requires a password', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Fathima Beevi',
        'phone' => '9447112233',
        'email' => 'fathima@example.com',
        // password omitted
    ]);

    $response->assertSessionHasErrors(['password']);
});

test('admin can manually create candidate account with mandatory password', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Fathima Beevi',
        'phone' => '9447112233',
        'email' => 'fathima@example.com',
        'password' => 'SecretPass123',
    ]);

    $response->assertSessionHas('success');
    $response->assertSessionHas('new_user_credentials');

    $this->assertDatabaseHas('users', [
        'name' => 'Fathima Beevi',
        'phone' => '9447112233',
        'email' => 'fathima@example.com',
        'is_admin' => false,
    ]);

    $user = User::where('email', 'fathima@example.com')->first();
    expect(\Illuminate\Support\Facades\Hash::check('SecretPass123', $user->password))->toBeTrue();
});

test('admin can edit existing candidate details and reset password', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Naseem Old',
        'phone' => '9895920422',
        'email' => 'naseem@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('OldPassword1'),
    ]);

    $response = $this->actingAs($admin)->put(route('admin.users.update', $student), [
        'name' => 'Naseem Ahmed',
        'phone' => '9895920422',
        'email' => 'naseem.updated@example.com',
        'password' => 'NewStrongPass99',
    ]);

    $response->assertSessionHas('success');

    $student->refresh();
    expect($student->name)->toEqual('Naseem Ahmed');
    expect($student->email)->toEqual('naseem.updated@example.com');
    expect(\Illuminate\Support\Facades\Hash::check('NewStrongPass99', $student->password))->toBeTrue();
});

test('candidate can log in with updated password via both phone and email', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Naseem',
        'phone' => '9895920422',
        'email' => 'nuwudy@gmail.com',
        'password' => 'InitialPass123',
    ]);

    // Admin updates student password to Amter786
    $updateResponse = $this->actingAs($admin)->put(route('admin.users.update', $student), [
        'name' => 'Naseem',
        'phone' => '9895920422',
        'email' => 'nuwudy@gmail.com',
        'password' => 'Amter786',
    ]);
    $updateResponse->assertSessionHas('success');

    // Logout admin
    auth()->logout();

    // Student logs in with phone 9895920422 and Amter786
    $loginPhoneResponse = $this->post('/login', [
        'login' => '9895920422',
        'password' => 'Amter786',
    ]);
    $loginPhoneResponse->assertSessionHasNoErrors();
    $loginPhoneResponse->assertRedirect();

    // Logout
    auth()->logout();

    // Student logs in with email nuwudy@gmail.com and Amter786
    $loginEmailResponse = $this->post('/login', [
        'login' => 'nuwudy@gmail.com',
        'password' => 'Amter786',
    ]);
    $loginEmailResponse->assertSessionHasNoErrors();
    $loginEmailResponse->assertRedirect();

    // Logout
    auth()->logout();

    // Student logs in with +91 country code prefix
    $loginPrefixResponse = $this->post('/login', [
        'login' => '+919895920422',
        'password' => 'Amter786',
    ]);
    $loginPrefixResponse->assertSessionHasNoErrors();
    $loginPrefixResponse->assertRedirect();

    // Logout
    auth()->logout();

    // Student logs in with 11-digit accidental extra digit (e.g. 98959204224)
    $loginTypoResponse = $this->post('/login', [
        'login' => '98959204224',
        'password' => 'Amter786',
    ]);
    $loginTypoResponse->assertSessionHasNoErrors();
    $loginTypoResponse->assertRedirect();
});

test('admin can create candidate account with instant offline PRO subscription', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Vishnu Prasad',
        'phone' => '9895123456',
        'email' => 'vishnu@example.com',
        'password' => 'Vishnu@1234',
        'activate_subscription' => 1,
        'duration_months' => 3,
        'payment_mode' => 'gpay_upi',
        'amount' => 762,
        'notes' => 'UPI Ref: 421098765432',
    ]);

    $response->assertSessionHas('success');

    $user = User::where('email', 'vishnu@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->isSubscribed())->toBeTrue();
    expect($user->subscriptionDaysRemaining())->toBeGreaterThan(80);

    $this->assertDatabaseHas('subscription_payments', [
        'user_id' => $user->id,
        'amount' => 762.00,
        'duration_months' => 3,
        'status' => 'paid',
    ]);
});

test('admin can gift or activate offline subscription on existing user', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Sujith Kumar',
        'subscribed_until' => null,
    ]);

    expect($student->isSubscribed())->toBeFalse();

    $response = $this->actingAs($admin)->post(route('admin.users.gift-subscription', $student), [
        'duration_months' => 6,
        'payment_mode' => 'cash',
        'amount' => 1345,
        'notes' => 'Offline direct cash payment at PSC academy',
    ]);

    $response->assertSessionHas('success');

    $student->refresh();
    expect($student->isSubscribed())->toBeTrue();
    expect($student->subscriptionDaysRemaining())->toBeGreaterThan(170);

    $payment = SubscriptionPayment::where('user_id', $student->id)->latest()->first();
    expect($payment)->not->toBeNull();
    expect((float)$payment->amount)->toEqual(1345.00);
    expect($payment->status)->toEqual('paid');
});

test('gifting subscription extends an already active subscription', function () {
    $admin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $existingExpiry = now()->addDays(30);
    $student = User::factory()->create([
        'subscribed_until' => $existingExpiry,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.gift-subscription', $student), [
        'duration_months' => 2,
        'payment_mode' => 'gift',
        'amount' => 0,
        'notes' => 'Referral reward gift pass',
    ]);

    $response->assertSessionHas('success');

    $student->refresh();
    // Expiry should be original 30 days + 2 months (around 90 days total)
    expect($student->subscriptionDaysRemaining())->toBeGreaterThan(85);
});

test('admin can promote candidate to administrator and demote them', function () {
    $superAdmin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $candidate = User::factory()->create([
        'is_admin' => false,
    ]);

    expect($candidate->isAdmin())->toBeFalse();

    // Promote to Admin
    $promoteResponse = $this->actingAs($superAdmin)->post(route('admin.users.toggle-admin', $candidate));
    $promoteResponse->assertSessionHas('success');

    $candidate->refresh();
    expect($candidate->isAdmin())->toBeTrue();
    expect($candidate->is_admin)->toBeTrue();

    // Demote back to Candidate
    $demoteResponse = $this->actingAs($superAdmin)->post(route('admin.users.toggle-admin', $candidate));
    $demoteResponse->assertSessionHas('success');

    $candidate->refresh();
    expect($candidate->isAdmin())->toBeFalse();
    expect($candidate->is_admin)->toBeFalse();
});

test('superadmin cannot be demoted and self-demotion is prevented', function () {
    $superAdmin = User::factory()->create([
        'email' => 'admin@pscranker.com',
        'is_admin' => true,
    ]);

    $secondaryAdmin = User::factory()->create([
        'email' => 'subadmin@pscranker.com',
        'is_admin' => true,
    ]);

    // Secondary admin trying to demote Superadmin
    $response = $this->actingAs($secondaryAdmin)->post(route('admin.users.toggle-admin', $superAdmin));
    $response->assertSessionHas('error');
    expect($superAdmin->refresh()->isAdmin())->toBeTrue();

    // Secondary admin trying to self-demote
    $selfResponse = $this->actingAs($secondaryAdmin)->post(route('admin.users.toggle-admin', $secondaryAdmin));
    $selfResponse->assertSessionHas('error');
    expect($secondaryAdmin->refresh()->is_admin)->toBeTrue();
});
