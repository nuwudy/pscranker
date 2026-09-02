<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\DrillAttempt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PscRankerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $catRenaissance = Category::create([
            'name' => 'Kerala Renaissance',
            'name_malayalam' => 'കേരള നവോത്ഥാനം',
            'slug' => 'kerala-renaissance',
            'icon' => 'sparkles',
            'badge_color' => 'purple',
            'description' => 'Sree Narayana Guru, Ayyankali, Chattampi Swamikal, Vaikom Satyagraha & historical agitations.',
            'order' => 1,
        ]);

        $catScert = Category::create([
            'name' => 'SCERT School Textbooks',
            'name_malayalam' => 'SCERT സ്കൂൾ പാഠപുസ്തകങ്ങൾ (Std 5-10)',
            'slug' => 'scert-basics',
            'icon' => 'book-open',
            'badge_color' => 'emerald',
            'description' => 'Direct questions from Basic Science, Social Science, and Chemistry textbooks Std 5 to 10.',
            'order' => 2,
        ]);

        $catMaths = Category::create([
            'name' => 'Maths & Mental Ability',
            'name_malayalam' => 'കണക്കും റീസണിങ്ങും',
            'slug' => 'maths-mental-ability',
            'icon' => 'calculator',
            'badge_color' => 'amber',
            'description' => 'Time & Work, Trains, Ratio, Number Series, and Shortcuts without paper calculation.',
            'order' => 3,
        ]);

        $catGk = Category::create([
            'name' => 'Current Affairs & Kerala GK',
            'name_malayalam' => 'സമകാലികം & പൊതുവിജ്ഞാനം',
            'slug' => 'current-affairs-gk',
            'icon' => 'globe',
            'badge_color' => 'blue',
            'description' => 'Rivers, Dams, Wildlife Sanctuaries, Panchayati Raj, and Recent Awards.',
            'order' => 4,
        ]);

        // 2. Create Flagship 3-Min Speed Drill Quiz
        $quiz = Quiz::create([
            'category_id' => null, // Multi-category Blitz
            'title' => '3-Min Kerala PSC Rapid Fire Blitz',
            'title_malayalam' => '3 മിനിറ്റ് റാപ്പിഡ് ഫയർ സ്പീഡ് ഡ്രിൽ',
            'slug' => '3-min-rapid-blitz',
            'description' => '10 high-yield Kerala PSC questions. 20 seconds per question. +1.00 for correct, -0.33 negative marking! Fasten your seatbelt!',
            'time_limit_seconds' => 180,
            'question_time_limit' => 20,
            'total_marks' => 10.00,
            'negative_marking_rate' => 0.33,
            'difficulty' => 'medium',
            'is_active' => true,
        ]);

        // 3. Create Authentic Kerala PSC Questions with Malayalam texts, Trap warnings, and Explanations
        $questions = [
            [
                'category_id' => $catRenaissance->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Who founded the "Sadhu Jana Paripalana Sangham" (SJPS) in the year 1907?',
                'question_text_malayalam' => '1907-ൽ സാധുജന പരിപാലന സംഘം (SJPS) സ്ഥാപിച്ചത് ആര്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Sree Narayana Guru', 'text_ml' => 'ശ്രീനാരായണ ഗുരു'],
                    ['key' => 'B', 'text' => 'Ayyankali', 'text_ml' => 'അയ്യൻകാളി'],
                    ['key' => 'C', 'text' => 'Chattampi Swamikal', 'text_ml' => 'ചട്ടമ്പി സ്വാമികൾ'],
                    ['key' => 'D', 'text' => 'Pandit Karuppan', 'text_ml' => 'പണ്ഡിറ്റ് കറുപ്പൻ'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Mahatma Ayyankali founded Sadhu Jana Paripalana Sangham in 1907 at Venganoor to fight for education and social rights of the downtrodden.',
                'explanation_malayalam' => '1907-ൽ വെങ്ങാനൂരിൽ വെച്ച് സാധുജന പരിപാലന സംഘം സ്ഥാപിച്ചത് മഹാത്മാ അയ്യൻകാളിയാണ്. SNDP യോഗം സ്ഥാപിച്ചത് 1903-ൽ ശ്രീനാരായണ ഗുരുവാണ്.',
                'trap_warning' => 'PSC Trap Alert: SNDP (1903 - ശ്രീനാരായണ ഗുരു) വേറെ, SJPS (1907 - അയ്യൻകാളി) വേറെ! ധൃതികൂട്ടി A കുത്തി -0.33 വാങ്ങല്ലേ!',
                'meme_image_url' => '/images/meme_card.jpg',
                'psc_exam_reference' => 'LDC 2021 Thiruvananthapuram',
                'difficulty' => 'easy',
            ],
            [
                'category_id' => $catRenaissance->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Which historic struggle is associated with the slogan "Ayyobhimanam" and the famous Villuvandi Chidambaram march?',
                'question_text_malayalam' => 'പൊതുവഴിയിലൂടെ വില്ലുവണ്ടി ഓടിച്ച് സഞ്ചാരസ്വാതന്ത്ര്യം പ്രഖ്യാപിച്ച നവോത്ഥാന നായകൻ ആര്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Vaikunda Swamikal', 'text_ml' => 'വൈകുണ്ഠ സ്വാമികൾ'],
                    ['key' => 'B', 'text' => 'Ayyankali', 'text_ml' => 'അയ്യൻകാളി (1893)'],
                    ['key' => 'C', 'text' => 'Mannathu Padmanabhan', 'text_ml' => 'മന്നത്ത് പത്മനാഭൻ'],
                    ['key' => 'D', 'text' => 'Dr. Palpu', 'text_ml' => 'ഡോ. പല്പു'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Ayyankali conducted the historic Villuvandi Samaram in 1893 from Venganoor to Chala market, demanding public road transit rights.',
                'explanation_malayalam' => '1893-ലെ ചരിത്രപ്രസിദ്ധമായ വില്ലുവണ്ടി സമരം നയിച്ചത് അയ്യൻകാളിയാണ്. വെങ്ങാനൂരിൽ നിന്ന് ചാല ചന്തയിലേക്കായിരുന്നു യാത്ര.',
                'trap_warning' => 'വർഷം ഓർക്കുക: 1893! തൊട്ടടുത്ത ഓപ്ഷനിൽ 1898 എന്ന് PSC വിചാരിച്ചാൽ തരും, കണ്ണ് തുറന്ന് കാണണം!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'Secretariat Assistant 2018',
                'difficulty' => 'easy',
            ],
            [
                'category_id' => $catGk->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Which is the longest west-flowing river entirely originating and flowing within Kerala?',
                'question_text_malayalam' => 'കേരളത്തിൽ മാത്രം ഒഴുകുന്ന ഏറ്റവും നീളം കൂടിയ നദി ഏത്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Periyar (244 km)', 'text_ml' => 'പെരിയാർ (244 കി.മീ)'],
                    ['key' => 'B', 'text' => 'Bharathapuzha (209 km)', 'text_ml' => 'ഭാരതപ്പുഴ (209 കി.മീ)'],
                    ['key' => 'C', 'text' => 'Pamba (176 km)', 'text_ml' => 'പമ്പ (176 കി.മീ)'],
                    ['key' => 'D', 'text' => 'Chaliyar (169 km)', 'text_ml' => 'ചാലിയാർ (169 കി.മീ)'],
                ],
                'correct_option' => 'A',
                'explanation' => 'Periyar is 244 km long, originating from Sivagiri Hills. Bharathapuzha total length is 250 km, but only 209 km flows through Kerala.',
                'explanation_malayalam' => 'പെരിയാറിന്റെ നീളം 244 കി.മീ ആണ്. ഭാരതപ്പുഴയുടെ മൊത്തം നീളം 250 കി.മീ ആണെങ്കിലും കേരളത്തിൽ 209 കി.മീ മാത്രമേ ഉള്ളൂ. അതിനാൽ പൂർണ്ണമായും കേരളത്തിലുള്ളതിൽ ഏറ്റവും വലുത് പെരിയാറാണ്.',
                'trap_warning' => 'PSC Classic Maha Trap: ഭാരതപ്പുഴ കണ്ട ഉടനെ ചാടിവീഴരുത്! "കേരളത്തിലൂടെ ഒഴുകുന്ന" എന്നാണോ "പൂർണ്ണമായും" എന്നാണോ ചോദ്യം എന്ന് ശ്രദ്ധിക്കണം!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'CPO 2023',
                'difficulty' => 'medium',
            ],
            [
                'category_id' => $catScert->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'What is the chemical formula of "Quick Lime" commonly asked in SCERT Class 9 Chemistry?',
                'question_text_malayalam' => 'നീറ്റുകക്കയുടെ (Quick Lime) രാസസൂത്രം ഏതാണ്?',
                'options' => [
                    ['key' => 'A', 'text' => 'CaCO3 (Calcium Carbonate)', 'text_ml' => 'CaCO3 (ചുണ്ണാമ്പുകല്ല്)'],
                    ['key' => 'B', 'text' => 'CaO (Calcium Oxide)', 'text_ml' => 'CaO (കാൽസ്യം ഓക്സൈഡ്)'],
                    ['key' => 'C', 'text' => 'Ca(OH)2 (Slaked Lime)', 'text_ml' => 'Ca(OH)2 (കുമ്മായം / തെളിച്ച ചുണ്ണാമ്പുവെള്ളം)'],
                    ['key' => 'D', 'text' => 'CaCl2', 'text_ml' => 'CaCl2'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Quick lime is Calcium Oxide (CaO). When water is added, it forms Slaked lime Ca(OH)2. Marble/Limestone is CaCO3.',
                'explanation_malayalam' => 'നീറ്റുകക്ക = CaO (Calcium Oxide). കുമ്മായം / ചുണ്ണാമ്പുവെള്ളം = Ca(OH)2. ചുണ്ണാമ്പുകല്ല് = CaCO3.',
                'trap_warning' => 'SCERT Trap Alert: "നീറ്റുകക്ക" (CaO) യും "കുമ്മായവും" (Ca(OH)2) മാറിപ്പോയാൽ PSC മാർക്ക് അങ്ങോട്ട് കൊടുക്കേണ്ടി വരും!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'SCERT Std 9 / Village Field Assistant 2023',
                'difficulty' => 'medium',
            ],
            [
                'category_id' => $catMaths->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'If 12 men can complete a work in 18 days, in how many days can 9 men complete the same work?',
                'question_text_malayalam' => '12 ആളുകൾ ഒരു ജോലി 18 ദിവസം കൊണ്ട് ചെയ്തു തീർക്കും എങ്കിൽ, 9 ആളുകൾ ഇതേ ജോലി എത്ര ദിവസം കൊണ്ട് ചെയ്യും?',
                'options' => [
                    ['key' => 'A', 'text' => '24 days', 'text_ml' => '24 ദിവസങ്ങൾ'],
                    ['key' => 'B', 'text' => '16 days', 'text_ml' => '16 ദിവസങ്ങൾ'],
                    ['key' => 'C', 'text' => '20 days', 'text_ml' => '20 ദിവസങ്ങൾ'],
                    ['key' => 'D', 'text' => '27 days', 'text_ml' => '27 ദിവസങ്ങൾ'],
                ],
                'correct_option' => 'A',
                'explanation' => 'M1 x D1 = M2 x D2. So 12 x 18 = 9 x D2 => D2 = (12 x 18) / 9 = 12 x 2 = 24 days.',
                'explanation_malayalam' => 'M1 × D1 = M2 × D2 എന്ന ലളിതമായ ഫോർമുല: 12 × 18 = 9 × D2. D2 = (12 × 18) / 9 = 24 ദിവസങ്ങൾ.',
                'trap_warning' => 'ആളുകൾ കുറയുമ്പോൾ ദിവസങ്ങൾ കൂടണം! 18-ൽ താഴെയുള്ള ഓപ്ഷനുകൾ (16) ഒറ്റനോട്ടത്തിൽ വെട്ടാം!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'LGS 2021 / Maths Speed Drill',
                'difficulty' => 'easy',
            ],
            [
                'category_id' => $catRenaissance->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'The historic Temple Entry Proclamation (Kshethra Praveshana Vilambaram) was issued in Travancore in which year?',
                'question_text_malayalam' => 'തിരുവിതാംകൂറിൽ ചരിത്രപ്രസിദ്ധമായ ക്ഷേത്രപ്രവേശന വിളംബരം പുറപ്പെടുവിച്ച വർഷം ഏത്?',
                'options' => [
                    ['key' => 'A', 'text' => '1931 November 1', 'text_ml' => '1931 നവംബർ 1'],
                    ['key' => 'B', 'text' => '1936 November 12', 'text_ml' => '1936 നവംബർ 12'],
                    ['key' => 'C', 'text' => '1924 March 30', 'text_ml' => '1924 മാർച്ച് 30'],
                    ['key' => 'D', 'text' => '1947 August 15', 'text_ml' => '1947 ഓഗസ്റ്റ് 15'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Sri Chithira Thirunal Balarama Varma issued the Temple Entry Proclamation on November 12, 1936 (Malayalam date: 1112 Thulam 27).',
                'explanation_malayalam' => 'ശ്രീ ചിത്തിര തിരുനാൾ ബാലരാമവർമ്മയാണ് 1936 നവംബർ 12-ന് ക്ഷേത്രപ്രവേശന വിളംബരം പുറപ്പെടുവിച്ചത്. ഇത് ആധുനിക തിരുവിതാംകൂറിന്റെ മാഗ്നാകാർട്ട എന്ന് അറിയപ്പെടുന്നു.',
                'trap_warning' => '1924 വൈക്കം സത്യാഗ്രഹമാണ്, 1931 ഗുരുവായൂർ സത്യാഗ്രഹമാണ്. 1936-ലാണ് വിളംബരം! ഓർത്തു വെക്കുക!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'Sub Inspector 2020',
                'difficulty' => 'medium',
            ],
            [
                'category_id' => $catGk->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Which bird sanctuary in Kerala was described by Dr. Salim Ali as the "richest bird habitat in peninsular India"?',
                'question_text_malayalam' => 'ഡോ. സലിം അലി "ഭാരത ഉപദ്വീപിലെ ഏറ്റവും സമ്പന്നമായ പക്ഷിസങ്കേതം" എന്ന് വിശേഷിപ്പിച്ചത് ഏതിനെയാണ്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Kumarakom Bird Sanctuary', 'text_ml' => 'കുമരകം പക്ഷിസങ്കേതം'],
                    ['key' => 'B', 'text' => 'Thattekkad Bird Sanctuary', 'text_ml' => 'തട്ടേക്കാട് പക്ഷിസങ്കേതം'],
                    ['key' => 'C', 'text' => 'Mangalavanam Bird Sanctuary', 'text_ml' => 'മംഗളവനം പക്ഷിസങ്കേതം'],
                    ['key' => 'D', 'text' => 'Kadalundi Bird Sanctuary', 'text_ml' => 'കടലുണ്ടി പക്ഷിസങ്കേതം'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Thattekkad Bird Sanctuary in Ernakulam is named after Dr. Salim Ali, who surveyed it in 1933.',
                'explanation_malayalam' => 'എറണാകുളം ജില്ലയിലെ തട്ടേക്കാട് പക്ഷിസങ്കേതമാണ് ഡോ. സലിം അലിയുടെ പേരിൽ അറിയപ്പെടുന്നത് (1983-ൽ നിലവിൽ വന്നു).',
                'trap_warning' => 'കുമരകവും തട്ടേക്കാടും തമ്മിൽ ആളുകൾ തെറ്റിക്കാറുണ്ട്. ഡോ. സലിം അലി പക്ഷിസങ്കേതം = തട്ടേക്കാട്!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'Beat Forest Officer 2022',
                'difficulty' => 'easy',
            ],
            [
                'category_id' => $catScert->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Which enzyme present in human saliva converts starch into maltose?',
                'question_text_malayalam' => 'മനുഷ്യന്റെ ഉമിനീരിൽ അടങ്ങിയിരിക്കുന്ന അന്നജത്തെ മാൾട്ടോസ് ആക്കി മാറ്റുന്ന എൻസൈം ഏതാണ്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Pepsin', 'text_ml' => 'പെപ്സിൻ'],
                    ['key' => 'B', 'text' => 'Salivary Amylase (Ptyalin)', 'text_ml' => 'സലൈവറി അമിലേസ് (ടയാലിൻ)'],
                    ['key' => 'C', 'text' => 'Trypsin', 'text_ml' => 'ട്രിപ്സിൻ'],
                    ['key' => 'D', 'text' => 'Lipase', 'text_ml' => 'ലൈപേസ്'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Salivary amylase (Ptyalin) in saliva hydrolyzes starch into maltose. Pepsin acts in stomach on proteins.',
                'explanation_malayalam' => 'ഉമിനീരിലെ സലൈവറി അമിലേസ് (ടയാലിൻ) ആണ് അന്നജത്തെ മാൾട്ടോസ് ആക്കി മാറ്റുന്നത്. പെപ്സിൻ ആമാശയത്തിൽ പ്രോട്ടീനെ ദഹിപ്പിക്കുന്നു.',
                'trap_warning' => 'പെപ്സിനും ട്രിപ്സിനും പ്രോട്ടീൻ ദഹനത്തിനാണ്! അമിലേസ് മാത്രമാണ് അന്നജം (Carbohydrates) മാറ്റുന്നത്!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'SCERT Std 10 Biology / LDC 2020',
                'difficulty' => 'easy',
            ],
            [
                'category_id' => $catMaths->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Find the odd one out from the following number series: 2, 3, 5, 7, 9, 11, 13',
                'question_text_malayalam' => 'താഴെ പറയുന്ന ശ്രേണിയിലെ ഒറ്റപ്പെട്ട സംഖ്യ ഏത്: 2, 3, 5, 7, 9, 11, 13',
                'options' => [
                    ['key' => 'A', 'text' => '2', 'text_ml' => '2'],
                    ['key' => 'B', 'text' => '9', 'text_ml' => '9'],
                    ['key' => 'C', 'text' => '7', 'text_ml' => '7'],
                    ['key' => 'D', 'text' => '13', 'text_ml' => '13'],
                ],
                'correct_option' => 'B',
                'explanation' => 'All other numbers are prime numbers (അഭാജ്യ സംഖ്യകൾ). 9 is a composite number (ഭാജ്യ സംഖ്യ: 3x3).',
                'explanation_malayalam' => '2, 3, 5, 7, 11, 13 എന്നിവയെല്ലാം പ്രൈം നമ്പറുകളാണ് (അഭാജ്യ സംഖ്യകൾ). 9 എന്നത് ഒരു ഭാജ്യ സംഖ്യയാണ് (3 × 3 = 9).',
                'trap_warning' => 'പലരും "2 ഇരട്ടസംഖ്യയല്ലേ" എന്ന് കരുതി 2 തെരഞ്ഞെടുത്ത് നെഗറ്റീവ് വാങ്ങും! 2 എന്നത് ഒരേയൊരു ഇരട്ട അഭാജ്യ സംഖ്യയാണ് (Even Prime Number)!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'Maths & Mental Ability / KSRTC Conductor Exam',
                'difficulty' => 'medium',
            ],
            [
                'category_id' => $catRenaissance->id,
                'quiz_id' => $quiz->id,
                'question_text' => 'Who is known as the "Father of Political Agitation in Travancore"?',
                'question_text_malayalam' => '"തിരുവിതാംകൂറിലെ രാഷ്ട്രീയ പ്രക്ഷോഭങ്ങളുടെ പിതാവ്" എന്ന് അറിയപ്പെടുന്നത് ആര്?',
                'options' => [
                    ['key' => 'A', 'text' => 'Swadeshabhimani Ramakrishna Pillai', 'text_ml' => 'സ്വദേശാഭിമാനി രാമകൃഷ്ണപിള്ള'],
                    ['key' => 'B', 'text' => 'G. P. Pillai (Barrister G. P. Pillai)', 'text_ml' => 'ജി. പി. പിള്ള (ബാരിസ്റ്റർ ജി. പി. പിള്ള)'],
                    ['key' => 'C', 'text' => 'K. Kelappan', 'text_ml' => 'കെ. കേളപ്പൻ'],
                    ['key' => 'D', 'text' => 'T. K. Madhavan', 'text_ml' => 'ടി. കെ. മാധവൻ'],
                ],
                'correct_option' => 'B',
                'explanation' => 'Barrister G. P. Pillai was the brain behind the Malayali Memorial (1891) and is hailed as the father of political agitation in Travancore.',
                'explanation_malayalam' => '1891-ലെ മലയാളി മെമ്മോറിയലിന്റെ മുഖ്യ സൂത്രധാരനായ ജി. പി. പിള്ളയാണ് തിരുവിതാംകൂറിലെ രാഷ്ട്രീയ പ്രക്ഷോഭങ്ങളുടെ പിതാവ്. സ്വദേശാഭിമാനി പത്രാധിപർ രാമകൃഷ്ണപിള്ളയാണ്.',
                'trap_warning' => 'PSC Super Trap: സ്വദേശാഭിമാനി രാമകൃഷ്ണപിള്ളയെ കണ്ടാൽ ഉടൻ ചാടരുത്! "രാഷ്ട്രീയ പ്രക്ഷോഭങ്ങളുടെ പിതാവ്" ജി.പി. പിള്ളയാണ്!',
                'meme_image_url' => null,
                'psc_exam_reference' => 'Kerala PSC Degree Level Prelims 2021',
                'difficulty' => 'hard',
            ],
        ];

        foreach ($questions as $q) {
            Question::create($q);
        }

        // 4. Create Mock Top Leaderboard Candidates (matching Behance screenshot!)
        $mockAttempts = [
            [
                'quiz_id' => $quiz->id,
                'candidate_name' => 'Rahul K.',
                'total_questions' => 10,
                'correct_answers' => 9,
                'wrong_answers' => 1,
                'unanswered' => 0,
                'score' => 8.67,
                'accuracy_percentage' => 90.00,
                'time_taken_seconds' => 74,
                'completed_at' => now()->subMinutes(12),
            ],
            [
                'quiz_id' => $quiz->id,
                'candidate_name' => 'Mini S.',
                'total_questions' => 10,
                'correct_answers' => 8,
                'wrong_answers' => 2,
                'unanswered' => 0,
                'score' => 7.34,
                'accuracy_percentage' => 80.00,
                'time_taken_seconds' => 88,
                'completed_at' => now()->subMinutes(25),
            ],
            [
                'quiz_id' => $quiz->id,
                'candidate_name' => 'Arun P.',
                'total_questions' => 10,
                'correct_answers' => 8,
                'wrong_answers' => 1,
                'unanswered' => 1,
                'score' => 7.67,
                'accuracy_percentage' => 88.89,
                'time_taken_seconds' => 95,
                'completed_at' => now()->subMinutes(42),
            ],
            [
                'quiz_id' => $quiz->id,
                'candidate_name' => 'Anjali Nair',
                'total_questions' => 10,
                'correct_answers' => 7,
                'wrong_answers' => 2,
                'unanswered' => 1,
                'score' => 6.34,
                'accuracy_percentage' => 77.78,
                'time_taken_seconds' => 102,
                'completed_at' => now()->subHours(1),
            ],
            [
                'quiz_id' => $quiz->id,
                'candidate_name' => 'Vishnu M.',
                'total_questions' => 10,
                'correct_answers' => 7,
                'wrong_answers' => 3,
                'unanswered' => 0,
                'score' => 6.01,
                'accuracy_percentage' => 70.00,
                'time_taken_seconds' => 110,
                'completed_at' => now()->subHours(2),
            ],
        ];

        foreach ($mockAttempts as $attempt) {
            DrillAttempt::create($attempt);
        }
    }
}
