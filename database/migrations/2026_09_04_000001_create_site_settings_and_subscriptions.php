<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Site Settings Table for admin configuration
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Seed default configuration
            DB::table('site_settings')->insert([
                ['key' => 'course_base_monthly_fee', 'value' => '299', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'rebate_2m', 'value' => '10', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'rebate_3m', 'value' => '15', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'rebate_6m', 'value' => '25', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'rebate_12m', 'value' => '40', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'razorpay_key_id', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'razorpay_key_secret', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. Add subscription attributes to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscribed_until')) {
                $table->timestamp('subscribed_until')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'subscription_plan')) {
                $table->string('subscription_plan')->nullable()->after('subscribed_until');
            }
            if (!Schema::hasColumn('users', 'subscription_amount')) {
                $table->decimal('subscription_amount', 10, 2)->nullable()->after('subscription_plan');
            }
        });

        // 3. Subscription Payments Table for Razorpay order tracking
        if (!Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('razorpay_order_id')->unique();
                $table->string('razorpay_payment_id')->nullable()->index();
                $table->string('razorpay_signature')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 10)->default('INR');
                $table->integer('duration_months');
                $table->decimal('rebate_percentage', 5, 2)->default(0);
                $table->string('status')->default('created'); // created, paid, failed
                $table->json('payment_metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscription_amount')) {
                $table->dropColumn('subscription_amount');
            }
            if (Schema::hasColumn('users', 'subscription_plan')) {
                $table->dropColumn('subscription_plan');
            }
            if (Schema::hasColumn('users', 'subscribed_until')) {
                $table->dropColumn('subscribed_until');
            }
        });

        Schema::dropIfExists('site_settings');
    }
};
