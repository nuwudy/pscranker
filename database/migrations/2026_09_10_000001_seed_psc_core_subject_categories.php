<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Session;
use App\Models\Question;
use App\Models\SessionContent;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to establish the 6 official PSC core subjects and starter units.
     */
    public function up(): void
    {
        // 1. Map / Rename existing legacy categories to standard PSC subjects to preserve existing session links
        DB::table('categories')->where('slug', 'kerala-renaissance')->update([
            'slug' => 'history',
            'name' => 'History & Renaissance',
            'name_malayalam' => 'ചരിത്രവും നവോത്ഥാനവും',
            'icon' => 'academic-cap',
            'badge_color' => 'purple',
            'description' => 'Kerala Renaissance leaders, socio-religious reform movements, and Indian freedom struggle.',
            'order' => 4,
        ]);

        DB::table('categories')->where('slug', 'scert-basics')->update([
            'slug' => 'science',
            'name' => 'General Science',
            'name_malayalam' => 'ജനറൽ സയൻസ് (SCERT)',
            'icon' => 'beaker',
            'badge_color' => 'amber',
            'description' => 'Physics, Chemistry, Biology and high-yield SCERT Std 5-10 school textbook concepts.',
            'order' => 3,
        ]);

        DB::table('categories')->where('slug', 'maths-mental-ability')->update([
            'slug' => 'maths',
            'name' => 'Maths & Mental Ability',
            'name_malayalam' => 'ഗണിതവും മാനസികശേഷിയും',
            'icon' => 'calculator',
            'badge_color' => 'emerald',
            'description' => 'Simple arithmetic, percentages, speed maths, reasoning, number series, and mental ability.',
            'order' => 2,
        ]);

        DB::table('categories')->where('slug', 'current-affairs-gk')->update([
            'slug' => 'current-affairs',
            'name' => 'Current Affairs & GK',
            'name_malayalam' => 'സമകാലിക വിഷയങ്ങളും പൊതുവിജ്ഞാനവും',
            'icon' => 'newspaper',
            'badge_color' => 'rose',
            'description' => 'Latest national & Kerala current affairs, government schemes, awards, sports, and hot PSC topics.',
            'order' => 6,
        ]);

        // 2. Ensure English and Geography categories exist
        $english = Category::firstOrCreate(
            ['slug' => 'english'],
            [
                'name' => 'English',
                'name_malayalam' => 'പൊതു ഇംഗ്ലീഷ് & ഗ്രാമർ',
                'icon' => 'book-open',
                'badge_color' => 'blue',
                'description' => 'General English, grammar rules, tenses, subject-verb agreement, idioms, and vocabulary.',
                'order' => 1,
            ]
        );

        $geography = Category::firstOrCreate(
            ['slug' => 'geography'],
            [
                'name' => 'Geography',
                'name_malayalam' => 'ഭൂമിശാസ്ത്രം (കേരളം & ഇന്ത്യ)',
                'icon' => 'globe-alt',
                'badge_color' => 'teal',
                'description' => 'Kerala geography, 44 rivers, dams, wildlife sanctuaries, Western Ghats, and Indian geography.',
                'order' => 5,
            ]
        );

        // 3. Create a Starter Unit for English: "Subject-Verb Agreement & Golden Rules"
        $englishSession = Session::firstOrCreate(
            ['slug' => 'english-unit-1-subject-verb-agreement'],
            [
                'title' => 'English Unit 1: Subject-Verb Agreement & Golden Rules',
                'title_malayalam' => 'സബ്ജക്ട്-വെർബ് എഗ്രിമെൻ്റ് — എളുപ്പവഴികളും PSC ചോദ്യങ്ങളും',
                'category_id' => $english->id,
                'order' => 1,
                'xp_reward' => 250,
                'is_active' => true,
                'is_premium' => false,
                'price' => 0.00,
            ]
        );

        if ($englishSession->contents()->count() === 0) {
            SessionContent::create([
                'session_id' => $englishSession->id,
                'type' => 'text',
                'order' => 1,
                'content_data' => [
                    'heading' => 'Golden Rule 1: Neither... Nor & Either... Or',
                    'body' => 'When two subjects are joined by "Either... or" or "Neither... nor", the verb agrees with the subject CLOSER to it! Example: Neither the teacher nor the students WERE present. Neither the students nor the teacher WAS present.',
                    'mnemonic' => 'Proximity Rule: The nearest subject captures the verb!',
                ],
            ]);

            SessionContent::create([
                'session_id' => $englishSession->id,
                'type' => 'image',
                'order' => 2,
                'content_data' => [
                    'url' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80',
                    'title' => 'Subject-Verb Agreement Rule Chart',
                    'caption' => 'Rule Chart: "Along with", "As well as", "Together with" always take the first subject!',
                ],
            ]);

            SessionContent::create([
                'session_id' => $englishSession->id,
                'type' => 'video',
                'order' => 3,
                'content_data' => [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'title' => 'Kerala PSC English Grammar Concepts',
                    'caption' => 'High-yield breakdown of subject-verb agreement for PSC degree and 10th level prelims.',
                ],
            ]);

            // Diagnostic question
            Question::create([
                'session_id' => $englishSession->id,
                'phase_type' => 'diagnostic',
                'question_text' => 'Neither the teacher nor the students _______ present in the class.',
                'question_text_malayalam' => 'താഴെ പറയുന്ന വാക്യത്തിൽ ശരിയായ ക്രിയാരൂപം ഏത്?',
                'option_a' => 'was',
                'option_b' => 'were',
                'option_c' => 'is',
                'option_d' => 'has',
                'correct_option' => 'B',
                'explanation' => 'With "Neither... nor", the verb agrees with the nearer subject "students" (plural), so "were" is correct.',
                'trap_warning' => 'Do not look at the first subject "teacher". Always match the verb to the nearest subject!',
                'order' => 1,
            ]);

            // MCQs & OMR mirrored questions
            $q1 = [
                'session_id' => $englishSession->id,
                'question_text' => 'Bread and butter _______ his favorite breakfast.',
                'question_text_malayalam' => 'ശരിയായ വെർബ് ചേർക്കുക:',
                'option_a' => 'is',
                'option_b' => 'are',
                'option_c' => 'were',
                'option_d' => 'have',
                'correct_option' => 'A',
                'explanation' => 'When two singular nouns together express one single idea, the verb is singular (is).',
                'order' => 1,
            ];

            $q2 = [
                'session_id' => $englishSession->id,
                'question_text' => 'The Prime Minister, together with his ministers, _______ arrived.',
                'question_text_malayalam' => 'ശരിയായ വെർബ് ചേർക്കുക:',
                'option_a' => 'have',
                'option_b' => 'has',
                'option_c' => 'are',
                'option_d' => 'were',
                'correct_option' => 'B',
                'explanation' => 'Words joined by "together with", "as well as" do not affect the subject number. The subject is "The Prime Minister" (singular).',
                'order' => 2,
            ];

            Question::create(array_merge($q1, ['phase_type' => 'reinforcement']));
            Question::create(array_merge($q1, ['phase_type' => 'omr']));
            Question::create(array_merge($q2, ['phase_type' => 'reinforcement']));
            Question::create(array_merge($q2, ['phase_type' => 'omr']));
        }

        // 4. Create a Starter Unit for Geography: "Rivers & Backwaters of Kerala"
        $geoSession = Session::firstOrCreate(
            ['slug' => 'geography-unit-1-rivers-of-kerala'],
            [
                'title' => 'Geography Unit 1: The 44 Rivers of Kerala & Lifelines',
                'title_malayalam' => 'കേരളത്തിലെ 44 നദികളും കായലുകളും — പ്രധാന പരീക്ഷാ വസ്തുതകൾ',
                'category_id' => $geography->id,
                'order' => 1,
                'xp_reward' => 250,
                'is_active' => true,
                'is_premium' => false,
                'price' => 0.00,
            ]
        );

        if ($geoSession->contents()->count() === 0) {
            SessionContent::create([
                'session_id' => $geoSession->id,
                'type' => 'text',
                'order' => 1,
                'content_data' => [
                    'heading' => 'Core Facts: Kerala Rivers',
                    'body' => 'Kerala has 44 rivers. 41 flow westwards into the Arabian Sea, and 3 flow eastwards into Tamil Nadu/Karnataka (Kabani, Bhavani, Pambar). The longest river is Periyar (244 km), followed by Bharathapuzha (209 km within Kerala).',
                    'mnemonic' => 'Eastward Flowing: K-B-P (Kabani, Bhavani, Pambar)',
                ],
            ]);

            SessionContent::create([
                'session_id' => $geoSession->id,
                'type' => 'image',
                'order' => 2,
                'content_data' => [
                    'url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
                    'title' => 'Kerala River Basins Map',
                    'caption' => 'Periyar (244 km) & Bharathapuzha (209 km) river drainage basins.',
                ],
            ]);

            Question::create([
                'session_id' => $geoSession->id,
                'phase_type' => 'diagnostic',
                'question_text' => 'Which is the longest river flowing entirely within Kerala?',
                'question_text_malayalam' => 'കേരളത്തിൽ പൂർണ്ണമായും ഒഴുകുന്ന ഏറ്റവും നീളം കൂടിയ നദി ഏത്?',
                'option_a' => 'Periyar',
                'option_b' => 'Bharathapuzha',
                'option_c' => 'Pamba',
                'option_d' => 'Chaliyar',
                'correct_option' => 'A',
                'explanation' => 'Periyar is the longest river in Kerala (244 km). Bharathapuzha has a total length of 250 km, but only 209 km lies within Kerala.',
                'trap_warning' => 'PSC Trap: Bharathapuzha is often confused as the longest, but Periyar has the longest course within Kerala.',
                'order' => 1,
            ]);

            $gq1 = [
                'session_id' => $geoSession->id,
                'question_text' => 'Which of the following is an east-flowing river in Kerala?',
                'question_text_malayalam' => 'കേരളത്തിൽ കിഴക്കോട്ട് ഒഴുകുന്ന നദി ഏതാണ്?',
                'option_a' => 'Periyar',
                'option_b' => 'Kabani',
                'option_c' => 'Pamba',
                'option_d' => 'Chaliyar',
                'correct_option' => 'B',
                'explanation' => 'Kabani, Bhavani, and Pambar are the three east-flowing rivers in Kerala.',
                'order' => 1,
            ];

            Question::create(array_merge($gq1, ['phase_type' => 'reinforcement']));
            Question::create(array_merge($gq1, ['phase_type' => 'omr']));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback to preserve data
    }
};
