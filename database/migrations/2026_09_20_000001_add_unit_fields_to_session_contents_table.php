<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('session_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('session_contents', 'unit_order')) {
                $table->integer('unit_order')->default(1)->after('session_id');
            }
            if (!Schema::hasColumn('session_contents', 'unit_title')) {
                $table->string('unit_title')->nullable()->after('unit_order');
            }
        });

        // Ensure Core PSC categories exist
        $coreCategories = [
            [
                'slug' => 'general-knowledge',
                'name' => 'General Knowledge',
                'name_malayalam' => 'പൊതുവിജ്ഞാനം (GK)',
                'icon' => 'globe-alt',
                'badge_color' => 'blue',
                'description' => 'Comprehensive Kerala & Indian General Knowledge, constitution, geography, and current facts.',
                'order' => 1,
            ],
            [
                'slug' => 'quantitative-aptitude',
                'name' => 'Quantitative Aptitude',
                'name_malayalam' => 'ഗണിതവും ക്വാണ്ടിറ്റേറ്റീവ് അഭിരുചിയും',
                'icon' => 'calculator',
                'badge_color' => 'emerald',
                'description' => 'Shortcuts, mental math, arithmetic, and problem solving for Kerala PSC.',
                'order' => 2,
            ],
            [
                'slug' => 'english',
                'name' => 'English Language',
                'name_malayalam' => 'ജനറൽ ഇംഗ്ലീഷ് & ഗ്രാമർ',
                'icon' => 'language',
                'badge_color' => 'indigo',
                'description' => 'Vocabulary, grammar rules, idioms, and PSC previous questions.',
                'order' => 3,
            ],
            [
                'slug' => 'history',
                'name' => 'History & Renaissance',
                'name_malayalam' => 'ചരിത്രവും നവോത്ഥാനവും',
                'icon' => 'academic-cap',
                'badge_color' => 'purple',
                'description' => 'Kerala renaissance reformers, national movement, and modern history.',
                'order' => 4,
            ],
            [
                'slug' => 'science',
                'name' => 'General Science',
                'name_malayalam' => 'ജനറൽ സയൻസ് (SCERT)',
                'icon' => 'beaker',
                'badge_color' => 'amber',
                'description' => 'Physics, chemistry, biology, and environment based on SCERT textbooks.',
                'order' => 5,
            ],
            [
                'slug' => 'current-affairs',
                'name' => 'Current Affairs & GK',
                'name_malayalam' => 'സമകാലിക വിഷയങ്ങൾ',
                'icon' => 'newspaper',
                'badge_color' => 'rose',
                'description' => 'Monthly roundups, government schemes, summits, and awards.',
                'order' => 6,
            ],
        ];

        foreach ($coreCategories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_contents', function (Blueprint $table) {
            if (Schema::hasColumn('session_contents', 'unit_title')) {
                $table->dropColumn('unit_title');
            }
            if (Schema::hasColumn('session_contents', 'unit_order')) {
                $table->dropColumn('unit_order');
            }
        });
    }
};
