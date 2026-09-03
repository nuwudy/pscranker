<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\Session;
use App\Models\SessionContent;
use Illuminate\Database\Seeder;

class SessionLearningEngineSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Category exists
        $category = Category::firstOrCreate(
            ['slug' => 'kerala-renaissance'],
            [
                'name' => 'Kerala Renaissance',
                'name_malayalam' => 'കേരള നവോത്ഥാനം',
                'icon' => 'sparkles',
                'badge_color' => 'purple',
                'description' => 'Sree Narayana Guru, Ayyankali, Chattampi Swamikal, and socio-religious reform movements.',
                'order' => 1,
            ]
        );

        // 2. Create the Flagship Learning Session
        $session = Session::updateOrCreate(
            ['slug' => 'sree-narayana-guru-aruvipuram-prathishta'],
            [
                'title' => 'Sree Narayana Guru & Aruvipuram Prathishta',
                'title_malayalam' => 'ശ്രീനാരായണഗുരുവും അരുവിപ്പുറം വിപ്ലവ പ്രതിഷ്ഠയും',
                'category_id' => $category->id,
                'order' => 1,
                'xp_reward' => 250,
                'is_active' => true,
            ]
        );

        // Clear existing session contents & questions to prevent duplicates
        $session->contents()->delete();
        $session->questions()->delete();

        // 3. Phase 2: Multimedia Content Blocks
        $contents = [
            [
                'type' => 'image',
                'order' => 1,
                'content_data' => [
                    'url' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?auto=format&fit=crop&w=1200&q=80',
                    'title' => 'നവോത്ഥാന നാഴികക്കല്ലുകൾ — Chronology Timeline Card',
                    'caption' => '1888 അരുവിപ്പുറം പ്രതിഷ്ഠ ➔ 1903 SNDP യോഗം ➔ 1912 ശാരദാ പ്രതിഷ്ഠ ➔ 1913 അദ്വൈതാശ്രമം ➔ 1924 സർവ്വമത സമ്മേളനം',
                ],
            ],
            [
                'type' => 'audio',
                'order' => 2,
                'content_data' => [
                    'url' => 'https://actions.google.com/sounds/v1/water/gentle_stream.ogg',
                    'title' => '45-സെക്കൻഡ് ഓഡിയോ സമ്മറി (അരുവിപ്പുറം പ്രതിഷ്ഠാ ചരിത്രം)',
                    'duration' => '0:45',
                    'transcript' => '1888-ൽ നെയ്യാറിന്റെ തീരത്ത് ശ്രീനാരായണഗുരു നടത്തിയ അരുവിപ്പുറം ശിവപ്രതിഷ്ഠ കേരള ചരിത്രത്തിലെ വിപ്ലവകരമായ വഴിത്തിരിവാണ്. സവർണ്ണ ആധിപത്യത്തിന് എതിരെയുള്ള ആത്മീയ വിപ്ലവം. "നാം പ്രതിഷ്ഠിച്ചത് ഈഴവ ശിവനെയാണ്" എന്ന ധീരമായ മറുപടിയും "ജാതിഭേദം മതദ്വേഷം ഏതുമില്ലാതെ..." എന്ന സമത്വ സന്ദേശവും ഓർക്കുക.',
                ],
            ],
            [
                'type' => 'video',
                'order' => 3,
                'content_data' => [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'title' => 'അരുവിപ്പുറം പ്രതിഷ്ഠയിലെ PSC കെണികൾ (Short Reel)',
                    'caption' => 'വർഷങ്ങൾ മാറിപ്പോകാതിരിക്കാനുള്ള എളുപ്പവഴികളും പരീക്ഷാ ഹാളിലെ സ്ഥിരം ട്രാപ്പുകളും!',
                ],
            ],
            [
                'type' => 'text',
                'order' => 4,
                'content_data' => [
                    'title' => 'SCERT പാഠപുസ്തക പ്രധാന ഫോക്കസ് പോയിന്റുകൾ',
                    'scert_reference' => 'SCERT Social Science Std 9 & 10 (പാഠം: നവോത്ഥാന കേരളം)',
                    'tags' => ['#SCERTStd9', '#KeralaRenaissance', '#LDC2024', '#DegreeLevelPSC'],
                    'body' => '
                        <div class="space-y-2">
                            <p class="font-bold text-slate-800">📌 <strong>അരുവിപ്പുറം ശിവപ്രതിഷ്ഠ (1888):</strong></p>
                            <ul class="list-disc pl-5 space-y-1 text-slate-700">
                                <li>നടന്ന വർഷം: <strong>1888 കുംഭമാസത്തിലെ ശിവരാത്രി നാളിൽ</strong>.</li>
                                <li>സ്ഥലം: തിരുവനന്തപുരം ജില്ലയിലെ നെയ്യാറ്റിൻകരയ്ക്കടുത്തുള്ള <strong>അരുവിപ്പുറം</strong> (നെയ്യാറിന്റെ തീരം).</li>
                                <li>ശില എടുത്തത്: നെയ്യാറിലെ ശങ്കരൻകുഴിയിൽ നിന്ന് മുങ്ങിയെടുത്ത ശില.</li>
                                <li>പ്രസിദ്ധമായ വരികൾ: <em>"ജാതിഭേദം മതദ്വേഷം ഏതുമില്ലാതെ സർവ്വരും സോദരത്വേന വാഴുന്ന മാതൃകാസ്ഥാനമാണിത്."</em></li>
                            </ul>
                            <p class="font-bold text-slate-800 mt-3">⚡ <strong>ഗുരുവിന്റെ മറ്റ് പ്രതിഷ്ഠകൾ (പരീക്ഷാ കെണികൾ):</strong></p>
                            <ul class="list-disc pl-5 space-y-1 text-slate-700">
                                <li><strong>1912 ശിവഗിരി ശാരദാ പ്രതിഷ്ഠ:</strong> അഷ്ടകോണാകൃതിയിലുള്ള ക്ഷേത്രത്തിൽ സരസ്വതി സങ്കൽപ്പത്തിലുള്ള ശാരദാ പ്രതിഷ്ഠ.</li>
                                <li><strong>1921 കാരമുക്ക് ക്ഷേത്രം:</strong> വിളക്ക് പ്രതിഷ്ഠ (ദീപ പ്രതിഷ്ഠ).</li>
                                <li><strong>1922 മുരുക്കുംപുഴ ക്ഷേത്രം:</strong> "സത്യം, ധർമ്മം, ദയ, ശാന്തി" എന്ന് രേഖപ്പെടുത്തിയ പ്രഭാ പ്രതിഷ്ഠ.</li>
                                <li><strong>1927 കളവങ്കോട് ക്ഷേത്രം:</strong> "ഓം ശാന്തി" എന്ന് രേഖപ്പെടുത്തിയ കണ്ണാടി പ്രതിഷ്ഠ.</li>
                            </ul>
                        </div>
                    ',
                ],
            ],
        ];

        foreach ($contents as $content) {
            SessionContent::create([
                'session_id' => $session->id,
                'type' => $content['type'],
                'order' => $content['order'],
                'content_data' => $content['content_data'],
            ]);
        }

        // 4. Phase 1: Diagnostic Question (Pre-Test Hook)
        Question::create([
            'session_id' => $session->id,
            'category_id' => $category->id,
            'phase_type' => 'diagnostic',
            'question_text' => 'In which year did Sree Narayana Guru perform the historic Aruvipuram Shiva Consecration?',
            'question_text_malayalam' => 'ശ്രീനാരായണഗുരു ചരിത്രപ്രസിദ്ധമായ അരുവിപ്പുറം ശിവപ്രതിഷ്ഠ നടത്തിയ വർഷം ഏതാണ്?',
            'option_a' => '1887',
            'option_b' => '1888',
            'option_c' => '1898',
            'option_d' => '1903',
            'correct_option' => 'B',
            'trap_warning_text' => '1887 അല്ല! 1887-ൽ മരുത്വാമലയിലെ ഏകാന്ത തപസ്സിന് ശേഷമുള്ള ഒരുക്കങ്ങളായിരുന്നു. 1888 ശിവരാത്രി രാത്രിയിലാണ് നെയ്യാറിൽ നിന്ന് ശിലയെടുത്ത് പ്രതിഷ്ഠിച്ചത്. 1903 SNDP യോഗം രൂപീകരിച്ച വർഷവുമാണ്.',
            'explanation' => 'Sree Narayana Guru performed the historic Aruvipuram Prathishta on the night of Shivaratri in 1888 on the banks of Neyyar river.',
            'explanation_malayalam' => '1888-ൽ തിരുവനന്തപുരത്തെ അരുവിപ്പുറത്ത് നെയ്യാറ്റിൽ നിന്ന് എടുത്ത ശില കൊണ്ടാണ് ഗുരു ശിവപ്രതിഷ്ഠ നടത്തിയത്. കേരള നവോത്ഥാന ചരിത്രത്തിലെ ആദ്യത്തെ നിശ്ശബ്ദ വിപ്ലവമായി ഇത് കണക്കാക്കപ്പെടുന്നു.',
            'psc_exam_reference' => 'LDC 2021 & Assistant Grade',
            'points' => 1.00,
            'negative_points' => 0.33,
        ]);

        // 5. Phase 3: Reinforcement Drill Questions (Speed Blitz)
        $reinforcementQuestions = [
            [
                'question_text' => 'On the banks of which river is Aruvipuram located?',
                'question_text_malayalam' => 'അരുവിപ്പുറം ഏത് നദിയുടെ തീരത്താണ് സ്ഥിതി ചെയ്യുന്നത്?',
                'option_a' => 'കരമനയാറ് (Karamana)',
                'option_b' => 'ഭാരതപ്പുഴ (Bharathapuzha)',
                'option_c' => 'നെയ്യാർ (Neyyar)',
                'option_d' => 'പെരിയാർ (Periyar)',
                'correct_option' => 'C',
                'explanation_malayalam' => 'തിരുവനന്തപുരം ജില്ലയിലെ നെയ്യാറ്റിൻകര താലൂക്കിലുള്ള നെയ്യാറിന്റെ തീരത്താണ് അരുവിപ്പുറം സ്ഥിതി ചെയ്യുന്നത്.',
            ],
            [
                'question_text' => 'Where did Sree Narayana Guru inscribe the famous message "Jathibhedam Mathadwesham..."?',
                'question_text_malayalam' => '"ജാതിഭേദം മതദ്വേഷം ഏതുമില്ലാതെ സർവ്വരും സോദരത്വേന വാഴുന്ന മാതൃകാസ്ഥാനമാണിത്" എന്ന സന്ദേശം ഗുരു എഴുതിവെച്ച സ്ഥലം?',
                'option_a' => 'ശിവഗിരി ശാരദാ മഠം',
                'option_b' => 'അരുവിപ്പുറം ക്ഷേത്രച്ചുവരുകൾ',
                'option_c' => 'ആലുവ അദ്വൈതാശ്രമം',
                'option_d' => 'ചെമ്പഴന്തി മണയ്ക്കൽ ക്ഷേത്രം',
                'correct_option' => 'B',
                'explanation_malayalam' => '1888-ലെ അരുവിപ്പുറം ശിവപ്രതിഷ്ഠയ്ക്ക് ശേഷം ക്ഷേത്ര ഭിത്തിയിലാണ് ഗുരു ഈ വരികൾ എഴുതിവെപ്പിച്ചത്.',
            ],
            [
                'question_text' => 'At which temple did Sree Narayana Guru perform the historic Mirror Consecration (Kannadi Prathishta)?',
                'question_text_malayalam' => 'ശ്രീനാരായണഗുരു കണ്ണാടി പ്രതിഷ്ഠ നടത്തിയ പ്രസിദ്ധമായ ക്ഷേത്രം ഏതാണ്?',
                'option_a' => 'കളവങ്കോട് ക്ഷേത്രം (Kalavancode)',
                'option_b' => 'മുരുക്കുംപുഴ ക്ഷേത്രം (Murukkumpuzha)',
                'option_c' => 'കാരമുക്ക് ക്ഷേത്രം (Karamukku)',
                'option_d' => 'ജഗന്നാഥ ക്ഷേത്രം തലശ്ശേരി',
                'correct_option' => 'A',
                'explanation_malayalam' => 'ആലപ്പുഴ ജില്ലയിലെ കളവങ്കോട് ക്ഷേത്രത്തിലാണ് "ഓം ശാന്തി" എന്ന് രേഖപ്പെടുത്തിയ കണ്ണാടി പ്രതിഷ്ഠ ഗുരു നടത്തിയത് (1927). മുരുക്കുംപുഴയിൽ പ്രഭാ പ്രതിഷ്ഠയും കാരമുക്കിൽ ദീപ പ്രതിഷ്ഠയുമാണ്.',
            ],
            [
                'question_text' => 'In which year was the Sree Narayana Dharma Paripalana (SNDP) Yogam officially registered?',
                'question_text_malayalam' => 'ശ്രീനാരായണ ധർമ്മ പരിപാലന (SNDP) യോഗം സ്ഥാപിതമായ വർഷം ഏതാണ്?',
                'option_a' => '1888',
                'option_b' => '1896',
                'option_c' => '1903',
                'option_d' => '1913',
                'correct_option' => 'C',
                'explanation_malayalam' => '1903 മെയ് 15-നാണ് കമ്പനി ആക്ട് പ്രകാരം SNDP യോഗം രജിസ്റ്റർ ചെയ്തത്. ഗുരു ആജീവനാന്ത അധ്യക്ഷനും കുമാരനാശാൻ ആദ്യ സെക്രട്ടറിയുമായിരുന്നു.',
            ],
        ];

        foreach ($reinforcementQuestions as $q) {
            Question::create([
                'session_id' => $session->id,
                'category_id' => $category->id,
                'phase_type' => 'reinforcement',
                'question_text' => $q['question_text'],
                'question_text_malayalam' => $q['question_text_malayalam'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'explanation_malayalam' => $q['explanation_malayalam'],
                'points' => 1.00,
                'negative_points' => 0.33,
            ]);
        }

        // 6. Phase 4: Final OMR Sheet Challenge Questions (5 questions)
        $omrQuestions = [
            [
                'question_text' => 'In which year did Rabindranath Tagore visit Sree Narayana Guru at Sivagiri?',
                'question_text_malayalam' => 'രവീന്ദ്രനാഥ ടാഗോർ ശിവഗിരിയിലെത്തി ശ്രീനാരായണഗുരുവിനെ സന്ദർശിച്ച വർഷം ഏതാണ്?',
                'option_a' => '1920',
                'option_b' => '1922',
                'option_c' => '1925',
                'option_d' => '1928',
                'correct_option' => 'B',
                'trap_warning' => '1922-ൽ ടാഗോറും, 1925-ൽ മഹാത്മാഗാന്ധിയുമാണ് ശിവഗിരിയിൽ ഗുരുവിനെ സന്ദർശിച്ചത്. വർഷങ്ങൾ മാറിപ്പോകരുത്!',
                'explanation_malayalam' => '1922 നവംബർ മാസത്തിലാണ് രവീന്ദ്രനാഥ ടാഗോർ ശിവഗിരിയിൽ ഗുരുവിനെ സന്ദർശിച്ചത്. സി.എഫ്. ആൻഡ്രൂസ് ആയിരുന്നു പരിഭാഷകൻ.',
            ],
            [
                'question_text' => 'In which year was the Advaita Ashramam at Aluva established by Sree Narayana Guru?',
                'question_text_malayalam' => 'ശ്രീനാരായണഗുരു ആലുവയിൽ അദ്വൈതാശ്രമം സ്ഥാപിച്ച വർഷം ഏതാണ്?',
                'option_a' => '1904',
                'option_b' => '1912',
                'option_c' => '1913',
                'option_d' => '1916',
                'correct_option' => 'C',
                'trap_warning' => '1904 ശിവഗിരി ആശ്രമം സ്ഥാപിതമായ വർഷമാണ്. ആലുവ അദ്വൈതാശ്രമം 1913-ലാണ്.',
                'explanation_malayalam' => '1913-ൽ പെരിയാറിന്റെ തീരത്താണ് ഗുരു ആലുവ അദ്വൈതാശ്രമം സ്ഥാപിച്ചത്. "ഒരു ജാതി ഒരു മതം ഒരു ദൈവം മനുഷ്യന്" എന്ന ആപ്തവാക്യം ആലേഖനം ചെയ്തത് ഇവിടെയാണ്.',
            ],
            [
                'question_text' => 'What was the motto of the All Religions Conference convened by Guru at Aluva in 1924?',
                'question_text_malayalam' => '1924-ൽ ആലുവയിൽ ഗുരു വിളിച്ചുചേർത്ത സർവ്വമത സമ്മേളനത്തിന്റെ ആപ്തവാക്യം എന്തായിരുന്നു?',
                'option_a' => 'വിദ്യ കൊണ്ട് പ്രബുദ്ധരാവുക',
                'option_b' => 'സംഘടന കൊണ്ട് ശക്തരാവുക',
                'option_c' => 'വാദിക്കാനും ജയിക്കാനുമല്ല, അറിയാനും അറിയിക്കാനുമാണ്',
                'option_d' => 'ഒരു ജാതി ഒരു മതം ഒരു ദൈവം',
                'correct_option' => 'C',
                'trap_warning' => 'എല്ലാ വാക്യങ്ങളും ഗുരുവിന്റേതാണ്, പക്ഷെ 1924 സർവ്വമത സമ്മേളനത്തിന്റെ പ്രമേയം "വാദിക്കാനും ജയിക്കാനുമല്ല, അറിയാനും അറിയിക്കാനുമാണ്" എന്നതായിരുന്നു.',
                'explanation_malayalam' => '1924-ൽ ഏഷ്യയിലെ തന്നെ രണ്ടാമത്തെ സർവ്വമത സമ്മേളനത്തിന് അധ്യക്ഷത വഹിച്ചത് ജസ്റ്റിസ് ടി. സദാശിവ അയ്യർ ആയിരുന്നു. വാദിക്കാനും ജയിക്കാനുമല്ല, അറിയാനും അറിയിക്കാനുമാണ് എന്നത് ഈ സമ്മേളനത്തിന്റെ പ്രഖ്യാപനമായിരുന്നു.',
            ],
            [
                'question_text' => 'The deity consecrated by Sree Narayana Guru at Sivagiri temple in 1912 was:',
                'question_text_malayalam' => '1912-ൽ ശിവഗിരിയിൽ ശ്രീനാരായണഗുരു പ്രതിഷ്ഠിച്ച ശാരദ ഏത് സങ്കൽപ്പത്തിലുള്ള ദേവതയാണ്?',
                'option_a' => 'ശക്തി സങ്കൽപ്പം (Shakti)',
                'option_b' => 'സരസ്വതി / വിദ്യ (Knowledge / Saraswati)',
                'option_c' => 'കാളി സങ്കൽപ്പം (Bhadrakali)',
                'option_d' => 'വൈഷ്ണവ സങ്കൽപ്പം (Vaishnava)',
                'correct_option' => 'B',
                'trap_warning' => 'ശാരദ എന്നാൽ അറിവിന്റെയും വിദ്യയുടെയും ദേവതയായ സരസ്വതിയുടെ സങ്കൽപ്പമാണ്. അഷ്ടകോണാകൃതിയിലുള്ള ക്ഷേത്രമാണിത്.',
                'explanation_malayalam' => '1912-ൽ ശിവഗിരിയിൽ അഷ്ടകോണാകൃതിയിലുള്ള ക്ഷേത്രത്തിൽ ജ്ഞാനത്തിന്റെ സങ്കൽപ്പമായ ശാരദാമഠം (സരസ്വതി) ഗുരു പ്രതിഷ്ഠിച്ചു.',
            ],
            [
                'question_text' => 'Who was the first General Secretary of SNDP Yogam?',
                'question_text_malayalam' => 'SNDP യോഗത്തിന്റെ ആദ്യത്തെ ജനറൽ സെക്രട്ടറി ആരായിരുന്നു?',
                'option_a' => 'ഡോ. പല്പു (Dr. Palpu)',
                'option_b' => 'മഹാകവി കുമാരനാശാൻ (Kumaran Asan)',
                'option_c' => 'ടി.കെ. മാധവൻ (T.K. Madhavan)',
                'option_d' => 'സി. കേശവൻ (C. Kesavan)',
                'correct_option' => 'B',
                'trap_warning' => 'ഡോ. പല്പു ആയിരുന്നു സംഘടനയുടെ ബുദ്ധികേന്ദ്രം. എന്നാൽ ആദ്യ സെക്രട്ടറി കുമാരനാശാനും പ്രസിഡന്റ് ശ്രീനാരായണഗുരുവുമായിരുന്നു.',
                'explanation_malayalam' => 'മഹാകവി കുമാരനാശാൻ 1903 മുതൽ 1919 വരെ ദീർഘകാലം SNDP യോഗത്തിന്റെ ജനറൽ സെക്രട്ടറിയായി സേവനമനുഷ്ഠിച്ചു.',
            ],
        ];

        foreach ($omrQuestions as $q) {
            Question::create([
                'session_id' => $session->id,
                'category_id' => $category->id,
                'phase_type' => 'omr',
                'question_text' => $q['question_text'],
                'question_text_malayalam' => $q['question_text_malayalam'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'trap_warning' => $q['trap_warning'] ?? null,
                'trap_warning_text' => $q['trap_warning'] ?? null,
                'explanation_malayalam' => $q['explanation_malayalam'],
                'points' => 1.00,
                'negative_points' => 0.33,
            ]);
        }
    }
}
