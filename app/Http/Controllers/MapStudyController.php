<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Session;

class MapStudyController extends Controller
{
    /**
     * Display the 3D Globe & Map Study Lab.
     */
    public function index(Request $request)
    {
        $cases = [
            'pacific_reality' => [
                'id' => 'pacific_reality',
                'badge' => 'Map Distortion Buster',
                'badge_color' => 'blue',
                'title' => 'The Pacific Reality: USA & Asia Neighbors',
                'title_malayalam' => 'ശാന്തസമുദ്ര അയൽപക്കങ്ങൾ: യു.എസും ഏഷ്യൻ രാജ്യങ്ങളും',
                'mode' => '3d_globe',
                'center_lat' => 28.0,
                'center_lng' => -170.0,
                'zoom' => 1.15,
                'summary' => 'Flat Mercator maps tear the world at the 180° meridian, creating the illusion that the Americas and Asia are opposite ends of the earth. On a 3D globe, they directly face each other across the Pacific Ocean, connected by maritime shipping lanes and the narrow 82 km Bering Strait.',
                'summary_malayalam' => 'പരന്ന മാപ്പുകളിൽ നോക്കുമ്പോൾ അമേരിക്കയും ഏഷ്യയും (ചൈന, ജപ്പാൻ, റഷ്യ) ഭൂമിയുടെ രണ്ട് വിപരീത അറ്റങ്ങളിലാണെന്ന് തോന്നും. എന്നാൽ ഒരു 3D ഗ്ലോബിൽ നോക്കുമ്പോൾ അവർ ശാന്തസമുദ്രത്തിന് ഇരുവശവുമുള്ള തൊട്ടടുത്ത അയൽക്കാരാണെന്ന് വ്യക്തമാകും. ബെയ്റിംഗ് കടലിടുക്കിലൂടെ റഷ്യയും അലാസ്കയും (യു.എസ്) തമ്മിൽ കേവലം 82 കി.മീ ദൂരം മാത്രമേയുള്ളൂ.',
                'mentor_tip' => 'Always ask yourself when studying geopolitics: "What does this look like over the Pacific or Arctic circle, not just flat Mercator?"',
                'mentor_tip_malayalam' => 'ഫ്ളാറ്റ് മാപ്പുകൾ ഭൂമിയുടെ യഥാർത്ഥ അകലങ്ങളെ വളച്ചൊടിക്കുന്നു. ശാന്തസമുദ്രത്തിന് ചുറ്റുമുള്ള പസഫിക് റിം (Pacific Rim) രാജ്യങ്ങളെ ഗ്ലോബിൽ കണ്ട് പഠിച്ചാൽ കറന്റ് അഫയേഴ്സും ചരിത്രവും എളുപ്പത്തിൽ ഓർക്കാം.',
                'markers' => [
                    [
                        'label' => 'Bering Strait (82 km)',
                        'lat' => 65.7,
                        'lng' => -168.9,
                        'color' => '#38BDF8',
                        'note' => 'Separates Asia (Chukotka, Russia) from North America (Alaska, USA). Width: only 82 km. International Date Line passes through here.',
                        'note_malayalam' => 'ഏഷ്യയെയും വടക്കേ അമേരിക്കയെയും വേർതിരിക്കുന്ന കടലിടുക്ക്. റഷ്യയെയും അലാസ്കയെയും തമ്മിൽ വേർതിരിക്കുന്നു (82 കി.മീ). അന്താരാഷ്ട്ര തീയതിരേഖ കടന്നുപോകുന്നു.'
                    ],
                    [
                        'label' => 'Pearl Harbor (Hawaii)',
                        'lat' => 21.36,
                        'lng' => -157.97,
                        'color' => '#EF4444',
                        'note' => 'US Pacific Fleet Base. Attacked by Imperial Japan on Dec 7, 1941, triggering US entry into World War II.',
                        'note_malayalam' => '1941 ഡിസംബർ 7-ന് ജപ്പാൻ പേൾ ഹാർബർ ആക്രമിച്ചതോടെയാണ് അമേരിക്ക രണ്ടാം ലോകമഹായുദ്ധത്തിലേക്ക് നേരിട്ട് പ്രവേശിച്ചത്.'
                    ],
                    [
                        'label' => 'Tokyo, Japan',
                        'lat' => 35.68,
                        'lng' => 139.69,
                        'color' => '#F59E0B',
                        'note' => 'Capital of Japan. Key Pacific trade powerhouse and US treaty ally.',
                        'note_malayalam' => 'ജപ്പാന്റെ തലസ്ഥാനം; ശാന്തസമുദ്ര വ്യാവസായിക ഇടനാഴിയുടെ പ്രധാന കേന്ദ്രം.'
                    ],
                    [
                        'label' => 'Shanghai, China',
                        'lat' => 31.23,
                        'lng' => 121.47,
                        'color' => '#10B981',
                        'note' => 'World\'s busiest container port on the East China Sea facing the Pacific shipping lanes directly to North America.',
                        'note_malayalam' => 'ലോകത്തിലെ ഏറ്റവും തിരക്കേറിയ കണ്ടെയ്നർ തുറമുഖം; പസഫിക് സമുദ്രത്തിലൂടെ യു.എസിലേക്ക് നേരിട്ടുള്ള ചരക്കുപാത.'
                    ],
                    [
                        'label' => 'San Francisco, USA',
                        'lat' => 37.77,
                        'lng' => -122.42,
                        'color' => '#8B5CF6',
                        'note' => 'Primary West Coast deep-water port and gateway to trans-Pacific commerce.',
                        'note_malayalam' => 'അമേരിക്കൻ പടിഞ്ഞാറൻ തീരത്തെ പ്രധാന ശാന്തസമുദ്ര തുറമുഖ നഗരം.'
                    ]
                ],
                'routes' => [
                    ['from' => [37.77, -122.42], 'to' => [21.36, -157.97], 'color' => '#38BDF8'],
                    ['from' => [21.36, -157.97], 'to' => [35.68, 139.69], 'color' => '#38BDF8'],
                    ['from' => [35.68, 139.69], 'to' => [31.23, 121.47], 'color' => '#10B981'],
                    ['from' => [65.7, -168.9], 'to' => [37.77, -122.42], 'color' => '#F59E0B']
                ],
                'psc_questions' => [
                    [
                        'q' => 'ഏഷ്യയെയും വടക്കേ അമേരിക്കയെയും പരസ്പരം വേർതിരിക്കുന്ന കടലിടുക്ക് ഏത്?',
                        'a' => 'ബെയ്റിംഗ് കടലിടുക്ക് (Bering Strait)',
                        'fact' => 'അലാസ്കയെയും (യു.എസ്) സൈബീരിയയെയും (റഷ്യ) വേർതിരിക്കുന്നു. വീതി 82 കി.മീ.'
                    ],
                    [
                        'q' => 'അന്താരാഷ്ട്ര തീയതിരേഖ (International Date Line) കടന്നുപോകുന്ന കടലിടുക്ക് ഏതാണ്?',
                        'a' => 'ബെയ്റിംഗ് കടലിടുക്ക്',
                        'fact' => '180° രേഖാംശരേഖയെ അടിസ്ഥാനമാക്കിയാണ് അന്താരാഷ്ട്ര തീയതിരേഖ നിർണ്ണയിച്ചിരിക്കുന്നത്.'
                    ],
                    [
                        'q' => 'രണ്ടാം ലോകമഹായുദ്ധത്തിൽ അമേരിക്ക പങ്കാളിയാകാൻ കാരണമായ സംഭവം ഏതാണ്?',
                        'a' => 'പേൾ ഹാർബർ ആക്രമണം (1941 ഡിസംബർ 7)',
                        'fact' => 'ഹവായി ദ്വീപിലെ യു.എസ് നേവൽ ബേസ് ആയിരുന്നു പേൾ ഹാർബർ.'
                    ]
                ]
            ],

            'german_invasion' => [
                'id' => 'german_invasion',
                'badge' => 'World War II Geography',
                'badge_color' => 'red',
                'title' => 'German Blitzkrieg & WWII Invasion Routes (1939-1941)',
                'title_malayalam' => 'രണ്ടാം ലോകമഹായുദ്ധം: ജർമ്മൻ അധിനിവേശ പാതകൾ (1939-1941)',
                'mode' => '3d_globe',
                'center_lat' => 52.0,
                'center_lng' => 16.0,
                'zoom' => 2.2,
                'summary' => 'Visualize the spatial explosion of Hitler\'s Blitzkrieg (lightning war). By tracking Berlin\'s central location in Europe, understand why Poland was struck first to secure the east, followed by bypassing the fortified French Maginot Line via Belgium\'s Ardennes, before launching the fateful multi-pronged Operation Barbarossa into Russia.',
                'summary_malayalam' => 'യൂറോപ്പിൽ ജർമ്മനിയുടെ സ്ഥാനവും അതിന്റെ അതിർത്തികളും മനസ്സിലാക്കിയാൽ രണ്ടാം ലോകമഹായുദ്ധ ചരിത്രം കാണാതെ പഠിക്കേണ്ടി വരില്ല! 1939 സെപ്റ്റംബർ 1-ന് പോളണ്ടിലേക്ക് കടന്നുകയറി. ഫ്രാൻസിന്റെ വൻ കോട്ടയായ മജിനോട്ട് ലൈൻ (Maginot Line) ഒഴിവാക്കാൻ ബെൽജിയത്തിലെ ആർഡെൻസ് കാടുകളിലൂടെ മിന്നലാക്രമണം നടത്തി പാരീസ് പിടിച്ചെടുത്തു. 1941-ൽ സോവിയറ്റ് യൂണിയനിലേക്ക് ബാർബറോസ ഓപ്പറേഷൻ ആരംഭിച്ചു.',
                'mentor_tip' => 'Candidates memorize dates but forget geography: Germany had to avoid a two-front war. Seeing the borders on a map explains why each battle happened where it did.',
                'mentor_tip_malayalam' => 'ജർമ്മനിയുടെ ഭൂമിശാസ്ത്രപരമായ സ്ഥാനം (മധ്യ യൂറോപ്പ്) മനസ്സിലാക്കിയാൽ ഒന്നാം, രണ്ടാം ലോകമഹായുദ്ധങ്ങളുടെ എല്ലാ പരീക്ഷാ ചോദ്യങ്ങളും കൃത്യമായി ശരിയാക്കാം.',
                'markers' => [
                    [
                        'label' => 'Berlin (Nazi Germany)',
                        'lat' => 52.52,
                        'lng' => 13.41,
                        'color' => '#EF4444',
                        'note' => 'Heart of the Third Reich. From here, commands directed multi-front Blitzkrieg operations.',
                        'note_malayalam' => 'മൂന്നാം റൈക്കിന്റെ തലസ്ഥാനം; അധിനിവേശ സൈന്യങ്ങളുടെ കമാൻഡ് സെന്റർ.'
                    ],
                    [
                        'label' => 'Poland (Warsaw)',
                        'lat' => 52.23,
                        'lng' => 21.01,
                        'color' => '#F59E0B',
                        'note' => 'Invaded Sept 1, 1939. Blitzkrieg tactic debut. Britain and France declared war on Germany on Sept 3, 1939.',
                        'note_malayalam' => '1939 സെപ്റ്റംബർ 1-ലെ പോളിഷ് അധിനിവേശം രണ്ടാം ലോകമഹായുദ്ധത്തിന് തുടക്കം കുറിച്ചു.'
                    ],
                    [
                        'label' => 'Ardennes & Paris (France)',
                        'lat' => 48.86,
                        'lng' => 2.35,
                        'color' => '#38BDF8',
                        'note' => 'Panzer divisions bypassed the Maginot Line via Ardennes Forest in May 1940. Paris fell on June 14, 1940.',
                        'note_malayalam' => 'മജിനോട്ട് ലൈൻ മറികടന്ന് ആർഡെൻസ് കാട്ടിലൂടെയുള്ള ആക്രമണം. 1940 ജൂണിൽ പാരീസ് കീഴടങ്ങി.'
                    ],
                    [
                        'label' => 'Moscow / USSR (Barbarossa)',
                        'lat' => 55.75,
                        'lng' => 37.62,
                        'color' => '#10B981',
                        'note' => 'Operation Barbarossa launched June 22, 1941. Largest land invasion in human history; halted in Russian winter.',
                        'note_malayalam' => 'ഓപ്പറേഷൻ ബാർബറോസ (1941 ജൂൺ 22). സോവിയറ്റ് യൂണിയനെതിരെയുള്ള ലോകചരിത്രത്തിലെ ഏറ്റവും വലിയ കരയാക്രമണം.'
                    ]
                ],
                'routes' => [
                    ['from' => [52.52, 13.41], 'to' => [52.23, 21.01], 'color' => '#EF4444'],
                    ['from' => [52.52, 13.41], 'to' => [48.86, 2.35], 'color' => '#F59E0B'],
                    ['from' => [52.52, 13.41], 'to' => [55.75, 37.62], 'color' => '#10B981']
                ],
                'psc_questions' => [
                    [
                        'q' => 'രണ്ടാം ലോകമഹായുദ്ധം ഔദ്യോഗികമായി ആരംഭിച്ചത് ഏത് രാജ്യത്തിനെതിരെയുള്ള ജർമ്മൻ അധിനിവേശത്തോടെയാണ്?',
                        'a' => 'പോളണ്ട് (1939 സെപ്റ്റംബർ 1)',
                        'fact' => 'ബ്രിട്ടനും ഫ്രാൻസും സെപ്റ്റംബർ 3-ന് ജർമ്മനിക്കെതിരെ യുദ്ധം പ്രഖ്യാപിച്ചു.'
                    ],
                    [
                        'q' => 'ഫ്രാൻസ് ജർമ്മൻ അതിർത്തിയിൽ നിർമ്മിച്ച പ്രശസ്തമായ പ്രതിരോധ കോട്ടനിര ഏത്?',
                        'a' => 'മജിനോട്ട് ലൈൻ (Maginot Line)',
                        'fact' => 'ജർമ്മൻ പട ബെൽജിയത്തിലെ ആർഡെൻസ് വനമേഖലയിലൂടെ ഇതിനെ മറികടന്നു.'
                    ],
                    [
                        'q' => '1941 ജൂണിൽ ജർമ്മനി സോവിയറ്റ് യൂണിയനെതിരെ നടത്തിയ കൂറ്റൻ സൈനിക നീക്കത്തിന്റെ കോഡ് നാമം?',
                        'a' => 'ഓപ്പറേഷൻ ബാർബറോസ (Operation Barbarossa)',
                        'fact' => 'ചരിത്രത്തിലെ ഏറ്റവും വലിയ കരസേനാ നീക്കമായിരുന്നു ഇത്.'
                    ]
                ]
            ],

            'red_sea' => [
                'id' => 'red_sea',
                'badge' => 'Maritime Choke Points',
                'badge_color' => 'cyan',
                'title' => 'Red Sea, Suez Canal & Bab-el-Mandeb Choke Points',
                'title_malayalam' => 'ചെങ്കടലും നിർണായക സമുദ്ര പാതകളും (സൂയസ് കനാൽ & ബാബ് അൽ മന്ദബ്)',
                'mode' => '3d_globe',
                'center_lat' => 20.0,
                'center_lng' => 41.0,
                'zoom' => 2.1,
                'summary' => 'The Red Sea is the carotid artery of world shipping, carrying ~12% of global trade and ~30% of global container traffic. Master the two bottlenecks tested non-stop in PSC: the artificial Suez Canal in the north and the natural Bab-el-Mandeb strait in the south.',
                'summary_malayalam' => 'ലോക വ്യാപാരത്തിന്റെ 12 ശതമാനവും ഒഴുകുന്ന അതീവ തന്ത്രപ്രധാനമായ പാതയാണ് ചെങ്കടൽ. വടക്ക് മെഡിറ്ററേനിയൻ കടലുമായി ബന്ധിപ്പിക്കുന്ന മനുഷ്യനിർമ്മിത കനാലായ സൂയസ് കനാൽ (1869), തെക്ക് ഏദൻ ഉൾക്കടലുമായി ബന്ധിപ്പിക്കുന്ന ബാബ് അൽ മന്ദബ് കടലിടുക്ക് ("കണ്ണീരിന്റെ വാതിൽ"), പേർഷ്യൻ ഗൾഫിലെ ഹോർമുസ് കടലിടുക്ക് എന്നിവയാണ് പി.എസ്.സി പരീക്ഷകളിലെ സ്ഥിരം ചോദ്യങ്ങൾ.',
                'mentor_tip' => 'Connect it to Kerala history: the spice trade and Roman ships sailed through Bab-el-Mandeb across the Arabian Sea directly to Muziris (Kodungallur)!',
                'mentor_tip_malayalam' => 'പ്രാചീന റോമൻ കപ്പലുകൾ മുസിരിസിലേക്ക് (കൊടുങ്ങല്ലൂർ) സുഗന്ധവ്യഞ്ജന വ്യാപാരത്തിന് വന്നത് ചെങ്കടലും ബാബ് അൽ മന്ദബും കടന്നാണ്. ഈ മാപ്പ് കണ്ടാൽ ആ ചരിത്രം മനസ്സിൽ പതിയും.',
                'markers' => [
                    [
                        'label' => 'Suez Canal (Egypt)',
                        'lat' => 30.7,
                        'lng' => 32.34,
                        'color' => '#38BDF8',
                        'note' => 'Opened Nov 17, 1869 (engineered by Ferdinand de Lesseps). Connects Mediterranean Sea to Red Sea, eliminating the long voyage around Africa\'s Cape of Good Hope.',
                        'note_malayalam' => '1869 നവംബർ 17-ൽ തുറന്നു (ഫെർഡിനാൻഡ് ഡി ലെസ്സെപ്സ്). മെഡിറ്ററേനിയൻ കടലിനെയും ചെങ്കടലിനെയും ബന്ധിപ്പിക്കുന്നു.'
                    ],
                    [
                        'label' => 'Bab-el-Mandeb Strait',
                        'lat' => 12.58,
                        'lng' => 43.33,
                        'color' => '#EF4444',
                        'note' => 'Known as the "Gate of Tears" in Arabic. Connects the southern Red Sea to the Gulf of Aden / Arabian Sea. Only 29 km wide between Yemen and Djibouti.',
                        'note_malayalam' => 'അറബിയിൽ "കണ്ണീരിന്റെ വാതിൽ" എന്നറിയപ്പെടുന്നു. യെമനെയും ജിബൂട്ടിയെയും വേർതിരിച്ച് ചെങ്കടലിനെ ഏദൻ ഉൾക്കടലുമായി ബന്ധിപ്പിക്കുന്നു (29 കി.മീ വീതി).'
                    ],
                    [
                        'label' => 'Strait of Hormuz',
                        'lat' => 26.56,
                        'lng' => 56.25,
                        'color' => '#F59E0B',
                        'note' => 'Vital oil transit corridor connecting the Persian Gulf with the Gulf of Oman and Arabian Sea.',
                        'note_malayalam' => 'പേർഷ്യൻ ഗൾഫിനെയും ഒമാൻ ഉൾക്കടലിനെയും ബന്ധിപ്പിക്കുന്ന ലോകത്തെ ഏറ്റവും പ്രധാന എണ്ണക്കപ്പൽ പാത.'
                    ],
                    [
                        'label' => 'Muziris / Kochi (Kerala Coast)',
                        'lat' => 10.15,
                        'lng' => 76.20,
                        'color' => '#10B981',
                        'note' => 'Historic spice destination for Greco-Roman, Arab, and European traders navigating the Arabian Sea.',
                        'note_malayalam' => 'ചെങ്കടൽ വഴി അറബിക്കടൽ താണ്ടി പ്രാചീന റോമൻ വ്യാപാരികൾ എത്തിയ കേരളത്തിന്റെ സുഗന്ധവ്യഞ്ജന തീരം.'
                    ]
                ],
                'routes' => [
                    ['from' => [31.2, 32.3], 'to' => [30.7, 32.34], 'color' => '#38BDF8'],
                    ['from' => [30.7, 32.34], 'to' => [20.0, 38.5], 'color' => '#38BDF8'],
                    ['from' => [20.0, 38.5], 'to' => [12.58, 43.33], 'color' => '#EF4444'],
                    ['from' => [12.58, 43.33], 'to' => [10.15, 76.20], 'color' => '#10B981']
                ],
                'psc_questions' => [
                    [
                        'q' => 'മെഡിറ്ററേനിയൻ കടലിനെയും ചെങ്കടലിനെയും തമ്മിൽ ബന്ധിപ്പിക്കുന്ന കനാൽ ഏതാണ്?',
                        'a' => 'സൂയസ് കനാൽ (Suez Canal)',
                        'fact' => '1869-ൽ പ്രവർത്തനം ആരംഭിച്ചു. രൂപകൽപ്പന ചെയ്തത് ഫെർഡിനാൻഡ് ഡി ലെസ്സെപ്സ്.'
                    ],
                    [
                        'q' => 'അറബിയിൽ "കണ്ണീരിന്റെ വാതിൽ" (Gate of Tears) എന്നറിയപ്പെടുന്ന പ്രശസ്തമായ കടലിടുക്ക് ഏത്?',
                        'a' => 'ബാബ് അൽ മന്ദബ് (Bab-el-Mandeb)',
                        'fact' => 'ചെങ്കടലിനെയും ഏദൻ ഉൾക്കടലിനെയും ബന്ധിപ്പിക്കുന്നു.'
                    ],
                    [
                        'q' => 'പേർഷ്യൻ ഗൾഫിനെയും ഒമാൻ ഉൾക്കടലിനെയും പരസ്പരം ബന്ധിപ്പിക്കുന്ന തന്ത്രപ്രധാന കടലിടുക്ക്?',
                        'a' => 'ഹോർമുസ് കടലിടുക്ക് (Strait of Hormuz)',
                        'fact' => 'ലോകത്തെ പെട്രോളിയം കയറ്റുമതിയുടെ മൂന്നിലൊന്നും ഈ പാതയിലൂടെയാണ് കടന്നുപോകുന്നത്.'
                    ]
                ]
            ],

            'mandela' => [
                'id' => 'mandela',
                'badge' => 'World Leader Biography',
                'badge_color' => 'emerald',
                'title' => 'Nelson Mandela\'s Spatial Footsteps: Mvezo to Robben Island',
                'title_malayalam' => 'നെൽസൺ മണ്ടേലയുടെ ജീവിത പാത: എംവേസോ മുതൽ റോബൻ ദ്വീപ് വരെ',
                'mode' => '3d_globe',
                'center_lat' => -29.0,
                'center_lng' => 25.0,
                'zoom' => 2.1,
                'summary' => 'Follow the spatial landmarks of Nelson Rolihlahla Mandela (1918-2013). Grounding his journey across South Africa\'s provinces helps candidates instantly recall exam facts: birthplace Mvezo in Transkei, anti-apartheid protests in Soweto/Johannesburg, 18 of his 27 imprisonment years on Robben Island, and presidential inauguration in Pretoria.',
                'summary_malayalam' => 'നെൽസൺ മണ്ടേലയുടെ ജീവിതവുമായി ബന്ധപ്പെട്ട് പി.എസ്.സി ആവർത്തിച്ചു ചോദിക്കുന്ന എല്ലാ വസ്തുതകളും ദക്ഷിണാഫ്രിക്കയുടെ മാപ്പിൽ കൃത്യമായി കാണാം: ട്രാൻസ്കെയിലെ എംവേസോ ഗ്രാമത്തിൽ ജനനം (1918), സോവെറ്റോയിലെയും ജൊഹാനസ്ബർഗിലെയും വിപ്ലവ പ്രസ്ഥാനം, റിവോണിയ വിചാരണ, കേപ് ടൗൺ തീരത്തെ റോബൻ ദ്വീപിലെ 18 വർഷത്തെ ഏകാന്ത തടവ്, 1994-ൽ പ്രിട്ടോറിയയിലെ യൂണിയൻ ബിൽഡിംഗ്സിൽ പ്രസിഡന്റായുള്ള സത്യപ്രതിജ്ഞ.',
                'mentor_tip' => 'Robben Island is an island in Table Bay off Cape Town. Visualizing its isolation off the Atlantic coast makes his 18-year isolation deeply memorable.',
                'mentor_tip_malayalam' => 'റോബൻ ദ്വീപ് കേപ് ടൗണിൽ അറ്റ്ലാന്റിക് സമുദ്രത്തിലാണ് സ്ഥിതി ചെയ്യുന്നത്. മാപ്പിൽ ആ ദ്വീപിന്റെ സ്ഥാനം കണ്ടാൽ പിന്നീട് ഒരിക്കലും ചോദ്യം തെറ്റില്ല.',
                'markers' => [
                    [
                        'label' => 'Mvezo (Birthplace, 1918)',
                        'lat' => -31.95,
                        'lng' => 28.51,
                        'color' => '#10B981',
                        'note' => 'Born July 18, 1918 into the Madiba royal clan in the Thembu kingdom.',
                        'note_malayalam' => '1918 ജൂലൈ 18-ന് മഡിബ വംശത്തിൽ ജനനം. ജൂലൈ 18 അന്താരാഷ്ട്ര നെൽസൺ മണ്ടേല ദിനമായി ആചരിക്കുന്നു.'
                    ],
                    [
                        'label' => 'Johannesburg & Soweto',
                        'lat' => -26.20,
                        'lng' => 28.04,
                        'color' => '#F59E0B',
                        'note' => 'ANC Youth League headquarters, law practice with Oliver Tambo, and the 1963-64 Rivonia Trial.',
                        'note_malayalam' => 'ആഫ്രിക്കൻ നാഷണൽ കോൺഗ്രസ് (ANC) പോരാട്ടങ്ങൾ, റിവോണിയ വിചാരണയിൽ ജീവപര്യന്തം തടവുശിക്ഷ വിധിക്കപ്പെട്ട നഗരം.'
                    ],
                    [
                        'label' => 'Robben Island (Cape Town)',
                        'lat' => -33.81,
                        'lng' => 18.37,
                        'color' => '#EF4444',
                        'note' => 'Maximum security prison in Table Bay. Mandela held here for 18 of his 27 years in Prison Cell No. 466/64.',
                        'note_malayalam' => 'കേപ് ടൗൺ തീരത്തെ കനത്ത സുരക്ഷാ ജയിൽ. 27 വർഷത്തെ ജയിൽവാസത്തിൽ 18 വർഷം ഇവിടെയായിരുന്നു (തടവുപുള്ളി നമ്പർ: 466/64).'
                    ],
                    [
                        'label' => 'Pretoria (Union Buildings)',
                        'lat' => -25.74,
                        'lng' => 28.21,
                        'color' => '#8B5CF6',
                        'note' => 'Inaugurated as South Africa\'s first democratically elected Black President on May 10, 1994.',
                        'note_malayalam' => '1994 മെയ് 10-ന് ദക്ഷിണാഫ്രിക്കയുടെ ആദ്യ കറുത്തവർഗ്ഗക്കാരനായ പ്രസിഡന്റായി സത്യപ്രതിജ്ഞ ചെയ്ത സ്ഥലം.'
                    ]
                ],
                'routes' => [
                    ['from' => [-31.95, 28.51], 'to' => [-26.20, 28.04], 'color' => '#10B981'],
                    ['from' => [-26.20, 28.04], 'to' => [-33.81, 18.37], 'color' => '#EF4444'],
                    ['from' => [-33.81, 18.37], 'to' => [-25.74, 28.21], 'color' => '#8B5CF6']
                ],
                'psc_questions' => [
                    [
                        'q' => 'നെൽസൺ മണ്ടേലയെ 18 വർഷത്തോളം തടവിലാക്കിയ പ്രശസ്തമായ ദ്വീപ് ജയിൽ ഏതാണ്?',
                        'a' => 'റോബൻ ദ്വീപ് (Robben Island)',
                        'fact' => 'കേപ് ടൗണിനടുത്തുള്ള ടേബിൾ ബേയിലാണ് റോബൻ ദ്വീപ് സ്ഥിതി ചെയ്യുന്നത്.'
                    ],
                    [
                        'q' => 'അന്താരാഷ്ട്ര നെൽസൺ മണ്ടേല ദിനമായി ഐക്യരാഷ്ട്രസഭ ആചരിക്കുന്നത് ഏത് ദിവസമാണ്?',
                        'a' => 'ജൂലൈ 18 (മണ്ടേലയുടെ ജന്മദിനം)',
                        'fact' => '1918 ജൂലൈ 18-ന് ട്രാൻസ്കെയിലെ എംവേസോയിലാണ് മണ്ടേല ജനിച്ചത്.'
                    ],
                    [
                        'q' => 'നെൽസൺ മണ്ടേലയുടെ വിഖ്യാതമായ ആത്മകഥയുടെ പേരെന്ത്?',
                        'a' => 'ലോങ് വാക്ക് ടു ഫ്രീഡം (Long Walk to Freedom)',
                        'fact' => '1993-ൽ എഫ്.ഡബ്ല്യു. ഡി ക്ലർക്കിനൊപ്പം സമാധാനത്തിനുള്ള നോബൽ സമ്മാനം പങ്കിട്ടു.'
                    ]
                ]
            ],

            'kerala_rivers' => [
                'id' => 'kerala_rivers',
                'badge' => 'Kerala Physical Geography',
                'badge_color' => 'purple',
                'title' => 'Kerala Rivers, Western Ghats & Mountain Passes',
                'title_malayalam' => 'കേരളത്തിലെ നദികളും സഹ്യപർവ്വത ചുരങ്ങളും',
                'mode' => '2d_map',
                'center_lat' => 10.4,
                'center_lng' => 76.4,
                'zoom' => 3.6,
                'summary' => 'Kerala\'s physical relief shaped its history. Pinpoint the 44 rivers (41 west-flowing into the Arabian Sea, 3 east-flowing: Kabani, Bhavani, Pambar), the Palakkad Gap breach in the Western Ghats that allowed trade and monsoon winds, and mountain passes linking Tamil Nadu.',
                'summary_malayalam' => 'കേരളത്തിലെ 44 നദികളിൽ 41 എണ്ണം പടിഞ്ഞാറോട്ടും 3 എണ്ണം കിഴക്കോട്ടും (കബനി, ഭവാനി, പാമ്പാർ) ഒഴുകുന്നു. സഹ്യപർവ്വതത്തിലെ ഏറ്റവും വലിയ സ്വാഭാവിക വിടവായ പാലക്കാട് ചുരം (30-40 കി.മീ), ആര്യങ്കാവ് ചുരം, താമരശ്ശേരി ചുരം എന്നിവ മാപ്പിൽ സ്ഥാനനിർണ്ണയം ചെയ്ത് പഠിക്കാം.',
                'mentor_tip' => 'Visualizing the Western Ghats wall on the east explains why 41 rivers rush westward to the Arabian Sea within only 100-200 km, creating torrential waterfalls and rich estuaries.',
                'mentor_tip_malayalam' => 'സഹ്യപർവ്വതത്തിൽ നിന്ന് അറബിക്കടലിലേക്കുള്ള ചരിവ് മനസ്സിൽ കണ്ടാൽ കേരളത്തിലെ നദികൾക്ക് നീളം കുറയാനുള്ള കാരണവും ഡാമുകളുടെ സ്ഥാനവും വ്യക്തമാകും.',
                'markers' => [
                    [
                        'label' => 'Periyar (244 km) & Idukki',
                        'lat' => 9.85,
                        'lng' => 76.97,
                        'color' => '#38BDF8',
                        'note' => 'Longest river in Kerala (244 km). Origin: Sivagiri Hills. Life line of Kerala with Idukki Arch Dam.',
                        'note_malayalam' => 'കേരളത്തിലെ ഏറ്റവും നീളം കൂടിയ നദി (244 കി.മീ). ഉത്ഭവം ശിവഗിരി മലനിരകൾ. ഏഷ്യയിലെ വലിയ ആർച്ച് ഡാമായ ഇടുക്കി ഡാം സ്ഥിതി ചെയ്യുന്നു.'
                    ],
                    [
                        'label' => 'Bharathapuzha / Nila (209 km)',
                        'lat' => 10.78,
                        'lng' => 75.92,
                        'color' => '#F59E0B',
                        'note' => 'Second longest river (209 km). Origin: Anamalai Hills. Flows through Palakkad, Malappuram, and Thrissur.',
                        'note_malayalam' => 'കേരളത്തിലെ രണ്ടാമത്തെ നീളമേറിയ നദി (നിള). ആനമലയിൽ ഉത്ഭവിച്ച് പൊന്നാനിയിൽ അറബിക്കടലിൽ ചേരുന്നു.'
                    ],
                    [
                        'label' => 'Palakkad Gap (30-40 km)',
                        'lat' => 10.78,
                        'lng' => 76.65,
                        'color' => '#EF4444',
                        'note' => 'Major geographical break in Western Ghats connecting Kerala (Palakkad) with Tamil Nadu (Coimbatore). Allows southwest monsoon winds inland.',
                        'note_malayalam' => 'സഹ്യപർവ്വതത്തിലെ പ്രധാന വിടവ് (30-40 കി.മീ). കേരളത്തെയും തമിഴ്നാടിനെയും ബന്ധിപ്പിക്കുന്നു; തെക്കുപടിഞ്ഞാറൻ കാലവർഷക്കാറ്റ് തമിഴ്നാട്ടിലേക്ക് കടക്കുന്നത് ഇതുവഴിയാണ്.'
                    ],
                    [
                        'label' => 'Kabani River (East-flowing)',
                        'lat' => 11.83,
                        'lng' => 76.12,
                        'color' => '#10B981',
                        'note' => 'East-flowing river originating from Wayanad. Major tributary of the Kaveri river. Kuruvadweep island is situated in Kabani.',
                        'note_malayalam' => 'വയനാട്ടിൽ ഉത്ഭവിച്ച് കിഴക്കോട്ടൊഴുകി കാവേരിയിൽ ചേരുന്ന പ്രധാന നദി. കുറുവാദ്വീപ് കബനി നദിയിലാണ്.'
                    ],
                    [
                        'label' => 'Aryankavu Pass (Kollam)',
                        'lat' => 8.98,
                        'lng' => 77.15,
                        'color' => '#8B5CF6',
                        'note' => 'Pass in Western Ghats connecting Kollam with Shenkottai (Tamil Nadu). Historic rail/road route.',
                        'note_malayalam' => 'കൊല്ലത്തെയും ചെങ്കോട്ടയെയും (തമിഴ്നാട്) തമ്മിൽ ബന്ധിപ്പിക്കുന്ന സഹ്യപർവ്വത ചുരം.'
                    ]
                ],
                'routes' => [
                    ['from' => [9.5, 77.2], 'to' => [9.85, 76.97], 'color' => '#38BDF8'],
                    ['from' => [9.85, 76.97], 'to' => [10.15, 76.20], 'color' => '#38BDF8'],
                    ['from' => [10.4, 76.9], 'to' => [10.78, 76.65], 'color' => '#F59E0B'],
                    ['from' => [10.78, 76.65], 'to' => [10.78, 75.92], 'color' => '#F59E0B'],
                    ['from' => [11.83, 76.12], 'to' => [12.0, 76.6], 'color' => '#10B981']
                ],
                'psc_questions' => [
                    [
                        'q' => 'കേരളത്തിലൂടെ കിഴക്കോട്ട് ഒഴുകുന്ന 3 നദികൾ ഏതെല്ലാം?',
                        'a' => 'കബനി, ഭവാനി, പാമ്പാർ',
                        'fact' => 'മൂന്നും കാവേരി നദിയുടെ പോഷകനദികളാണ്.'
                    ],
                    [
                        'q' => 'സഹ്യപർവ്വത നിരയിലെ ഏറ്റവും വലിയ ചുരം (വിടവ്) ഏതാണ്?',
                        'a' => 'പാലക്കാട് ചുരം (Palakkad Gap)',
                        'fact' => 'ഏകദേശം 30 മുതൽ 40 കിലോമീറ്റർ വരെ വീതിയുണ്ട്. പാലക്കാടിനെയും കോയമ്പത്തൂരിനെയും ബന്ധിപ്പിക്കുന്നു.'
                    ],
                    [
                        'q' => 'കേരളത്തിലെ ഏറ്റവും നീളം കൂടിയ നദിയായ പെരിയാർ ഉത്ഭവിക്കുന്നത് എവിടെ നിന്നാണ്?',
                        'a' => 'ശിവഗിരി മലനിരകൾ (തമിഴ്നാട് അതിർത്തിയിലെ സുന്ദരമല)',
                        'fact' => 'നീളം 244 കിലോമീറ്റർ. "കേരളത്തിന്റെ ജീവരേഖ" എന്നറിയപ്പെടുന്നു.'
                    ]
                ]
            ]
        ];

        $activeCaseKey = $request->query('case', 'pacific_reality');
        if (!isset($cases[$activeCaseKey])) {
            $activeCaseKey = 'pacific_reality';
        }

        $activeCase = $cases[$activeCaseKey];

        // Fetch related interactive sessions for this topic if available
        $relatedSessions = Session::where('is_active', true)
            ->where(function($query) use ($activeCaseKey) {
                if ($activeCaseKey === 'kerala_rivers') {
                    $query->where('title', 'like', '%River%')
                          ->orWhere('title', 'like', '%Geography%')
                          ->orWhere('title', 'like', '%നദി%');
                } elseif ($activeCaseKey === 'mandela' || $activeCaseKey === 'german_invasion') {
                    $query->where('title', 'like', '%History%')
                          ->orWhere('title', 'like', '%Renaissance%')
                          ->orWhere('title', 'like', '%ചരിത്രം%');
                } else {
                    $query->where('title', 'like', '%World%')
                          ->orWhere('title', 'like', '%Geography%');
                }
            })
            ->take(3)
            ->get();

        if ($relatedSessions->isEmpty()) {
            $relatedSessions = Session::where('is_active', true)->take(3)->get();
        }

        return view('pages.map-study', [
            'cases' => $cases,
            'activeCase' => $activeCase,
            'activeCaseKey' => $activeCaseKey,
            'relatedSessions' => $relatedSessions,
        ]);
    }
}
