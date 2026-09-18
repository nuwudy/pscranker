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
        Schema::create('affiliate_slabs', function (Blueprint $table) {
            $table->id();
            $table->string('slab_code', 50)->index(); // e.g. PRSL-2609-001
            $table->string('month_period', 7)->nullable()->index(); // e.g. '2026-09' or null for platform default
            $table->unsignedInteger('order')->default(1);
            $table->decimal('min_target', 12, 2)->default(0.00);
            $table->decimal('max_target', 12, 2)->nullable(); // null means open-ended upper bracket (e.g. 150001+)
            $table->decimal('basic_payout_percentage', 5, 2)->default(10.00);
            $table->decimal('bonus_percentage', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default 10 slabs as specified in client spreadsheet
        $defaultSlabs = [
            ['order' => 1, 'min_target' => 1.00, 'max_target' => 10000.00, 'basic' => 10.00, 'bonus' => 0.00],
            ['order' => 2, 'min_target' => 10001.00, 'max_target' => 20000.00, 'basic' => 10.00, 'bonus' => 5.00],
            ['order' => 3, 'min_target' => 20001.00, 'max_target' => 30000.00, 'basic' => 10.00, 'bonus' => 7.00],
            ['order' => 4, 'min_target' => 30001.00, 'max_target' => 40000.00, 'basic' => 10.00, 'bonus' => 9.00],
            ['order' => 5, 'min_target' => 40001.00, 'max_target' => 50000.00, 'basic' => 10.00, 'bonus' => 11.00],
            ['order' => 6, 'min_target' => 50001.00, 'max_target' => 75000.00, 'basic' => 10.00, 'bonus' => 13.00],
            ['order' => 7, 'min_target' => 75001.00, 'max_target' => 100000.00, 'basic' => 10.00, 'bonus' => 15.00],
            ['order' => 8, 'min_target' => 100001.00, 'max_target' => 125000.00, 'basic' => 10.00, 'bonus' => 17.00],
            ['order' => 9, 'min_target' => 125001.00, 'max_target' => 150000.00, 'basic' => 10.00, 'bonus' => 19.00],
            ['order' => 10, 'min_target' => 150001.00, 'max_target' => null, 'basic' => 10.00, 'bonus' => 21.00],
        ];

        $currentMonth = now()->format('ym'); // e.g. 2609
        $now = now();

        foreach ($defaultSlabs as $slab) {
            $code = sprintf('PRSL-%s-%03d', $currentMonth, $slab['order']);
            DB::table('affiliate_slabs')->insert([
                'slab_code' => $code,
                'month_period' => null, // default template for all months
                'order' => $slab['order'],
                'min_target' => $slab['min_target'],
                'max_target' => $slab['max_target'],
                'basic_payout_percentage' => $slab['basic'],
                'bonus_percentage' => $slab['bonus'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_slabs');
    }
};
