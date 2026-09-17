<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Affiliates Table (Promoters)
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('affiliate_code', 50)->unique();
            $table->string('status', 20)->default('active'); // active, pending, suspended
            $table->decimal('commission_rate', 5, 2)->default(15.00); // Default 15%
            $table->string('payout_method', 30)->default('upi'); // upi, bank_transfer
            $table->json('payout_details')->nullable(); // { upi_id: '...', bank_name: '...', account_no: '...', ifsc: '...' }
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Affiliate Leads Table (Prospects registered by Promoters)
        Schema::create('affiliate_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->string('candidate_name', 150);
            $table->string('candidate_phone', 15)->index(); // Normalized 10 digits
            $table->string('alternate_phone', 15)->nullable();
            $table->string('status', 25)->default('lead'); // lead, converted, expired
            $table->text('notes')->nullable(); // Followup notes
            $table->foreignId('converted_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamp('valid_until')->nullable(); // Expiration of attribution window (e.g. 60 days)
            $table->timestamps();

            // Index for fast lead lookup by phone and status
            $table->index(['candidate_phone', 'status']);
        });

        // 3. Affiliate Commissions & Monthly Disbursements Ledger
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('affiliate_lead_id')->nullable()->constrained('affiliate_leads')->nullOnDelete();
            $table->foreignId('subscription_payment_id')->nullable()->constrained('subscription_payments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // The student who paid
            $table->decimal('course_amount', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(15.00);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); // commission + bonus
            $table->string('period_month', 7); // Format: 'YYYY-MM' (e.g. '2026-08') for monthly grouping
            $table->string('status', 25)->default('pending'); // pending, ready, disbursed
            $table->timestamp('disbursed_at')->nullable();
            $table->string('payout_reference', 100)->nullable(); // Bank UTR or UPI Transaction Reference
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'period_month', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_leads');
        Schema::dropIfExists('affiliates');
    }
};
