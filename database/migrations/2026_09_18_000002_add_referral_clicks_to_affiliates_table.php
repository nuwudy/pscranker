<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('affiliates') && !Schema::hasColumn('affiliates', 'referral_clicks')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->unsignedInteger('referral_clicks')->default(0)->after('notes');
            });
        }

        if (Schema::hasTable('affiliate_leads') && !Schema::hasColumn('affiliate_leads', 'source')) {
            Schema::table('affiliate_leads', function (Blueprint $table) {
                $table->string('source', 30)->default('phone_lead')->after('status'); // phone_lead, referral_link
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('affiliates') && Schema::hasColumn('affiliates', 'referral_clicks')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->dropColumn('referral_clicks');
            });
        }

        if (Schema::hasTable('affiliate_leads') && Schema::hasColumn('affiliate_leads', 'source')) {
            Schema::table('affiliate_leads', function (Blueprint $table) {
                $table->dropColumn('source');
            });
        }
    }
};
