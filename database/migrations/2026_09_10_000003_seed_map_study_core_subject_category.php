<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Session;
use App\Models\Question;
use App\Models\SessionContent;

return new class extends Migration
{
    /**
     * Run the migrations to establish the 7th PSC core subject: Map & Globe Study.
     */
    public function up(): void
    {
        $mapCategory = Category::firstOrCreate(
            ['slug' => 'map-study'],
            [
                'name' => 'Map & Globe Study',
                'name_malayalam' => 'ഭൂപട & ഗ്ലോബ് പഠനം',
                'icon' => 'globe-alt',
                'badge_color' => 'indigo',
                'description' => '3D Globe spatial cognition, world geography, historical invasion routes, maritime choke points, and Kerala physical relief.',
                'order' => 7,
            ]
        );

        // Create initial high-yield interactive unit with map_globe content block
        $session = Session::firstOrCreate(
            ['slug' => 'map-study-unit-1-pacific-reality-and-choke-points'],
            [
                'title' => 'Map Study Unit 1: The Pacific Reality & World Choke Points',
                'title_malayalam' => 'ഭൂപട പഠനം: ശാന്തസമുദ്ര അയൽപക്കങ്ങളും പ്രധാന സമുദ്ര പാതകളും',
                'category_id' => $mapCategory->id,
                'order' => 1,
                'xp_reward' => 300,
                'is_active' => true,
                'is_premium' => false,
                'price' => 0.00,
                'in_general_stream' => true,
                'general_stream_order' => 5,
            ]
        );

        if ($session->contents()->count() === 0) {
            SessionContent::create([
                'session_id' => $session->id,
                'type' => 'map_globe',
                'order' => 1,
                'content_data' => [
                    'mode' => '3d_globe',
                    'preset' => 'pacific_reality',
                    'title' => 'The Pacific Reality: USA & Asia Neighbors',
                    'title_malayalam' => 'ശാന്തസമുദ്ര അയൽപക്കങ്ങൾ: യു.എസും ഏഷ്യയും',
                    'center_lat' => 28.0,
                    'center_lng' => -170.0,
                    'zoom' => 1.2,
                    'description' => 'Dispel the flat map myth! Look how USA and Asia (China/Japan/Russia) are facing each other across the Pacific Ocean. Bering Strait is only 82 km wide.',
                    'notes_malayalam' => 'പരന്ന മാപ്പുകളിൽ അമേരിക്കയും ചൈനയും ലോകത്തിന്റെ ഇരുവശത്താണെന്ന് തോന്നുമെങ്കിലും ഗ്ലോബിൽ അവർ ശാന്തസമുദ്രത്തിന് ഇരുവശമുള്ള അടുത്ത അയൽക്കാരാണ്. ബെയ്റിംഗ് കടലിടുക്കിന് 82 കി.മീ മാത്രമാണ് വീതി.',
                    'markers' => [
                        ['label' => 'Bering Strait (82 km)', 'lat' => 65.7, 'lng' => -168.9, 'color' => '#38BDF8', 'note' => 'Separates Asia (Russia) & North America (Alaska, USA)', 'note_malayalam' => 'ഏഷ്യയെയും വടക്കേ അമേരിക്കയെയും വേർതിരിക്കുന്ന കടലിടുക്ക് (82 കി.മീ).'],
                        ['label' => 'Pearl Harbor (Hawaii)', 'lat' => 21.36, 'lng' => -157.97, 'color' => '#EF4444', 'note' => 'Dec 7, 1941 attack brought USA into WWII', 'note_malayalam' => '1941 ഡിസംബർ 7-ലെ ആക്രമണം അമേരിക്കയെ രണ്ടാം ലോകമഹായുദ്ധത്തിൽ എത്തിച്ചു.'],
                        ['label' => 'Tokyo, Japan', 'lat' => 35.68, 'lng' => 139.69, 'color' => '#F59E0B', 'note' => 'Pacific Rim trade hub', 'note_malayalam' => 'ശാന്തസമുദ്ര വ്യാവസായിക ഇടനാഴിയുടെ പ്രധാന കേന്ദ്രം.'],
                        ['label' => 'Shanghai, China', 'lat' => 31.23, 'lng' => 121.47, 'color' => '#10B981', 'note' => 'Busiest container port facing the Pacific', 'note_malayalam' => 'ലോകത്തിലെ ഏറ്റവും തിരക്കേറിയ കണ്ടെയ്നർ തുറമുഖം.'],
                        ['label' => 'San Francisco, USA', 'lat' => 37.77, 'lng' => -122.42, 'color' => '#8B5CF6', 'note' => 'Key Pacific gateway port of USA', 'note_malayalam' => 'യു.എസിന്റെ പടിഞ്ഞാറൻ ശാന്തസമുദ്ര തുറമുഖ നഗരം.']
                    ]
                ]
            ]);

            SessionContent::create([
                'session_id' => $session->id,
                'type' => 'text',
                'order' => 2,
                'content_data' => [
                    'title' => 'Flat Map Illusion vs 3D Reality',
                    'scert_reference' => 'SCERT Social Science Std 9 & 10 Geography',
                    'body' => '<ul><li><strong>ബെയ്റിംഗ് കടലിടുക്ക്:</strong> ഏഷ്യയെയും വടക്കേ അമേരിക്കയെയും വേർതിരിക്കുന്നു (82 കി.മീ).</li><li><strong>അന്താരാഷ്ട്ര തീയതിരേഖ:</strong> 180° രേഖാംശരേഖയെ അടിസ്ഥാനമാക്കി ബെയ്റിംഗ് കടലിടുക്കിലൂടെ കടന്നുപോകുന്നു.</li><li><strong>പസഫിക് റിം (Ring of Fire):</strong> ലോകത്തിലെ ഏറ്റവും കൂടുതൽ അഗ്നിപർവ്വതങ്ങളും ഭൂകമ്പങ്ങളും നടക്കുന്ന മേഖല.</li></ul>',
                ]
            ]);

            // Diagnostic question
            Question::create([
                'session_id' => $session->id,
                'phase_type' => 'diagnostic',
                'question_text' => 'Which strait separates the continents of Asia and North America?',
                'question_text_malayalam' => 'ഏഷ്യയെയും വടക്കേ അമേരിക്കയെയും പരസ്പരം വേർതിരിക്കുന്ന കടലിടുക്ക് ഏത്?',
                'option_a' => 'Bering Strait',
                'option_b' => 'Strait of Gibraltar',
                'option_c' => 'Malacca Strait',
                'option_d' => 'Palk Strait',
                'correct_option' => 'A',
                'explanation' => 'The Bering Strait separates Russia (Asia) from Alaska (North America) and is only 82 km wide at its narrowest point.',
                'explanation_malayalam' => 'ബെയ്റിംഗ് കടലിടുക്ക് റഷ്യയെയും (ഏഷ്യ) അലാസ്കയെയും (വടക്കേ അമേരിക്ക) പരസ്പരം വേർതിരിക്കുന്നു (വീതി 82 കി.മീ).',
                'trap_warning' => 'Do not confuse with Gibraltar (Europe & Africa) or Palk Strait (India & Sri Lanka).',
                'order' => 1,
            ]);

            // Unified MCQs & OMR
            $q1 = [
                'session_id' => $session->id,
                'category_id' => $mapCategory->id,
                'question_text' => 'Through which strait does the International Date Line pass?',
                'question_text_malayalam' => 'അന്താരാഷ്ട്ര തീയതിരേഖ കടന്നുപോകുന്ന കടലിടുക്ക് ഏതാണ്?',
                'option_a' => 'Bering Strait',
                'option_b' => 'Bosphorus Strait',
                'option_c' => 'Hormuz Strait',
                'option_d' => 'Cook Strait',
                'correct_option' => 'A',
                'explanation' => 'The International Date Line zigzag passes through the Bering Strait between Asia and America.',
                'explanation_malayalam' => 'അന്താരാഷ്ട്ര തീയതിരേഖ (180° മെറിഡിയൻ) ബെയ്റിംഗ് കടലിടുക്കിലൂടെയാണ് കടന്നുപോകുന്നത്.',
                'order' => 1,
            ];

            $q2 = [
                'session_id' => $session->id,
                'category_id' => $mapCategory->id,
                'question_text' => 'Which maritime canal connects the Mediterranean Sea directly with the Red Sea?',
                'question_text_malayalam' => 'മെഡിറ്ററേനിയൻ കടലിനെയും ചെങ്കടലിനെയും തമ്മിൽ ബന്ധിപ്പിക്കുന്ന കനാൽ ഏതാണ്?',
                'option_a' => 'Panama Canal',
                'option_b' => 'Suez Canal',
                'option_c' => 'Kiel Canal',
                'option_d' => 'Corinth Canal',
                'correct_option' => 'B',
                'explanation' => 'Suez Canal in Egypt opened in 1869, connecting Mediterranean with Red Sea.',
                'explanation_malayalam' => '1869-ൽ തുറന്ന സൂയസ് കനാൽ മെഡിറ്ററേനിയൻ കടലിനെയും ചെങ്കടലിനെയും ബന്ധിപ്പിക്കുന്നു.',
                'order' => 2,
            ];

            Question::create(array_merge($q1, ['phase_type' => 'reinforcement']));
            Question::create(array_merge($q1, ['phase_type' => 'omr']));
            Question::create(array_merge($q2, ['phase_type' => 'reinforcement']));
            Question::create(array_merge($q2, ['phase_type' => 'omr']));
        }
    }

    public function down(): void
    {
        // No-op rollback
    }
};
