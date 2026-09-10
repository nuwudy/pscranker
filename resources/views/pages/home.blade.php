@extends('layouts.app')

@section('title', 'PSCRanker.com — Crack Kerala PSC with Super Speed!')

@section('content')

<!-- HERO SECTION: Matching Behance Mockup Exactly -->
<section class="relative pt-6 sm:pt-10 pb-16 overflow-hidden">
    
    <!-- Dynamic Lightning and Radial Background Atmosphere -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[650px] pointer-events-none -z-10">
        <div class="absolute -top-24 left-1/4 w-96 h-96 bg-blue-400/15 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-yellow-300/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Grid: Left Content & Right Mascot -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-center min-h-[520px]">
            
            <!-- Left Hero Content (Cols 7) -->
            <div class="lg:col-span-7 flex flex-col justify-center text-left z-10">
                
                <!-- Value Pill -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-5 w-fit shadow-xs">
                    <span class="text-amber-500 text-sm">⚡</span>
                    <span>സിലബസ് യൂണിറ്റുകൾ തിരിച്ച് പഠിക്കാം • UNIT BY UNIT COURSE</span>
                </div>

                <!-- Main Bold Headline: Punchy, Uncrowded 2-line Powerhouse -->
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-slate-950 tracking-tight leading-[1.1] uppercase">
                    CRACK KERALA PSC <br class="hidden sm:block">
                    WITH <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#0052FF] via-blue-600 to-amber-500">SUPER SPEED!</span>
                </h1>

                <!-- Crisp, High-Converting Subtitle -->
                <p class="text-base sm:text-lg font-medium text-slate-600 mt-4 leading-relaxed max-w-xl">
                    Master syllabus units through 10-minute micro-capsules. Practice real OMR exams, dodge negative marks, and accelerate your rank.
                </p>

                <!-- Primary and Secondary CTA Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
                    <!-- Primary CTA leading to Course / Sessions page -->
                    <a 
                        href="{{ route('sessions.index') }}" 
                        class="px-8 sm:px-10 py-4 sm:py-4.5 bg-[#0052FF] hover:bg-[#003ECC] text-white font-black text-base sm:text-lg rounded-full shadow-xl shadow-blue-500/35 hover:shadow-blue-500/50 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 border-2 border-[#FFD200] group"
                    >
                        <span>START COURSE UNITS</span>
                        <span class="text-yellow-300 group-hover:translate-x-1.5 transition-transform text-2xl font-black">➔</span>
                    </a>

                    <!-- Secondary CTA for 6 PSC Subjects -->
                    <a 
                        href="#special-subjects" 
                        class="px-6 py-4 bg-white hover:bg-slate-100 text-slate-900 font-black text-sm sm:text-base rounded-full border-2 border-slate-200 shadow-sm hover:shadow transition flex items-center justify-center gap-2"
                    >
                        <span>📚 6 Core PSC Subjects</span>
                    </a>
                </div>

                <!-- Unit <> Unit Roadmap Micro-Pill -->
                <div class="mt-6 flex items-center gap-2 text-xs font-bold text-slate-500">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sequential Units (Unit 1 ➔ Unit 2 ➔ Unit 3) • Free &amp; PRO Tracks</span>
                </div>

                <!-- Mini Mascot Badges / Key Metrics -->
                <div class="mt-8 pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 max-w-lg">
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-[#0052FF]">10-Min</div>
                        <div class="text-xs font-semibold text-slate-500">Bite-Sized Units</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-red-600">-0.33</div>
                        <div class="text-xs font-semibold text-slate-500">Strict PSC Marking</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-amber-500">Free + PRO</div>
                        <div class="text-xs font-semibold text-slate-500">Flexible Access</div>
                    </div>
                </div>

            </div>

            <!-- Right Hero Mascot & Dynamic Comic Accents (Cols 5) -->
            <div class="lg:col-span-5 relative flex justify-center items-center">
                
                <!-- Comic Energy Lightning / Starburst Backdrop -->
                <div class="absolute inset-0 flex items-center justify-center -z-10">
                    <div class="w-[360px] sm:w-[460px] h-[360px] sm:h-[460px] rounded-full bg-gradient-to-tr from-blue-100 via-yellow-100 to-white opacity-80 animate-spin-slow"></div>
                </div>

                <!-- Floating Question Sheets (matching Behance graphic) -->
                <div class="absolute -top-4 -left-4 sm:left-2 bg-white/95 backdrop-blur-sm p-3 rounded-2xl shadow-xl border-2 border-blue-100 rotate-[-12deg] animate-float-slow z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-[#0052FF] flex items-center justify-center font-black text-xs">PSC</div>
                        <div class="text-left">
                            <div class="text-[11px] font-black text-slate-800">1907 SJPS?</div>
                            <div class="text-[9px] font-bold text-emerald-600">✓ അയ്യൻകാളി +1.00</div>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-6 -right-2 sm:right-0 bg-white/95 backdrop-blur-sm p-3 rounded-2xl shadow-xl border-2 border-yellow-200 rotate-[10deg] animate-float-reverse z-20">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-yellow-100 text-yellow-800 flex items-center justify-center font-black text-xs">EXAM</div>
                        <div class="text-left">
                            <div class="text-[11px] font-black text-slate-800">Negative Avoided!</div>
                            <div class="text-[9px] font-bold text-red-500">🚫 Trap Dodged</div>
                        </div>
                    </div>
                </div>

                <!-- Mascot Illustration Image -->
                <div class="relative z-10 w-full max-w-[380px] sm:max-w-[440px] rounded-3xl p-2 group">
                    <img 
                        src="{{ asset('images/hero-mascot.webp?v=2') }}" 
                        onerror="this.onerror=null; this.src='{{ asset('images/hero-mascot.jpg?v=2') }}'"
                        alt="Kerala PSC Candidate Super Speed Runner" 
                        class="w-full h-auto object-contain rounded-3xl shadow-2xl border-4 border-white transition-transform duration-300 group-hover:scale-[1.01] bg-white"
                    >
                </div>

            </div>

        </div>

    </div>
</section>

<!-- 6 OFFICIAL KERALA PSC SPECIAL SUBJECTS -->
<section id="special-subjects" class="py-16 sm:py-20 bg-gradient-to-b from-slate-50 via-white to-slate-50 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-3 shadow-sm border border-blue-200">
                <span>🎯 Official Kerala PSC Syllabus 2026</span>
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                7 PSC Special Subjects
            </h2>
            <p class="text-base sm:text-lg font-semibold text-slate-600 mt-3">
                പ്രത്യേക പാഠങ്ങൾ — സിലബസ് അടിസ്ഥാനമാക്കി ഓരോ വിഷയവും യൂണിറ്റുകളായി തിരിച്ച് പഠിക്കാം. Diagnostic Test ➔ Media Lesson ➔ MCQs ➔ Authentic OMR Test!
            </p>
        </div>

        <!-- 7 Subject Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @php
                $subjectThemes = [
                    'english' => [
                        'emoji' => '📖',
                        'gradient' => 'from-blue-600 to-indigo-600',
                        'bg_light' => 'bg-blue-50/80',
                        'border' => 'border-blue-200',
                        'accent' => 'text-[#0052FF]',
                        'button_bg' => 'bg-[#0052FF] hover:bg-blue-700',
                        'features' => ['Grammar Rules & Tenses', 'Active & Passive Voice', 'Idioms, Phrasal Verbs & PYQs'],
                    ],
                    'maths' => [
                        'emoji' => '🧮',
                        'gradient' => 'from-amber-500 to-orange-600',
                        'bg_light' => 'bg-amber-50/80',
                        'border' => 'border-amber-200',
                        'accent' => 'text-amber-700',
                        'button_bg' => 'bg-amber-600 hover:bg-amber-700',
                        'features' => ['Speed Math Short Tricks', 'BODMAS, Percentage & Ratio', 'Time & Work, Reasoning Series'],
                    ],
                    'science' => [
                        'emoji' => '🔬',
                        'gradient' => 'from-emerald-500 to-teal-600',
                        'bg_light' => 'bg-emerald-50/80',
                        'border' => 'border-emerald-200',
                        'accent' => 'text-emerald-700',
                        'button_bg' => 'bg-emerald-600 hover:bg-emerald-700',
                        'features' => ['SCERT Standard 5-10 Topics', 'Human Body & Diseases', 'Physics & Chemistry PYQs'],
                    ],
                    'history' => [
                        'emoji' => '🏛️',
                        'gradient' => 'from-purple-600 to-fuchsia-700',
                        'bg_light' => 'bg-purple-50/80',
                        'border' => 'border-purple-200',
                        'accent' => 'text-purple-700',
                        'button_bg' => 'bg-purple-700 hover:bg-purple-800',
                        'features' => ['Kerala Renaissance Leaders', 'Freedom Movement in Kerala', 'Travancore & Cochin History'],
                    ],
                    'geography' => [
                        'emoji' => '🌍',
                        'gradient' => 'from-teal-600 to-cyan-700',
                        'bg_light' => 'bg-teal-50/80',
                        'border' => 'border-teal-200',
                        'accent' => 'text-teal-800',
                        'button_bg' => 'bg-teal-700 hover:bg-teal-800',
                        'features' => ['44 Kerala Rivers & Dams', 'Western Ghats & Sanctuaries', 'Districts & Physical Features'],
                    ],
                    'current-affairs' => [
                        'emoji' => '📰',
                        'gradient' => 'from-rose-600 to-pink-700',
                        'bg_light' => 'bg-rose-50/80',
                        'border' => 'border-rose-200',
                        'accent' => 'text-rose-700',
                        'button_bg' => 'bg-rose-600 hover:bg-rose-700',
                        'features' => ['Monthly Kerala Current Affairs', 'Awards, Sports & Honors', 'Indian Constitution & PYQ GK'],
                    ],
                    'map-study' => [
                        'emoji' => '🌐',
                        'gradient' => 'from-indigo-600 to-blue-700',
                        'bg_light' => 'bg-indigo-50/80',
                        'border' => 'border-indigo-200',
                        'accent' => 'text-indigo-800',
                        'button_bg' => 'bg-indigo-600 hover:bg-indigo-700',
                        'features' => ['3D Globe Spatial Cognition', 'WWII & Historical Invasions', 'Red Sea & Kerala Relief Gaps'],
                    ],
                ];
            @endphp

            @foreach($categories as $category)
                @php
                    $theme = $subjectThemes[$category->slug] ?? [
                        'emoji' => '📚',
                        'gradient' => 'from-blue-600 to-indigo-600',
                        'bg_light' => 'bg-slate-50',
                        'border' => 'border-slate-200',
                        'accent' => 'text-slate-900',
                        'button_bg' => 'bg-blue-600 hover:bg-blue-700',
                        'features' => ['Syllabus Based Lessons', 'Interactive Practice Sets', 'Real OMR Negative Marking'],
                    ];
                @endphp
                <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 {{ $theme['border'] }} shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 flex flex-col justify-between group relative overflow-hidden">
                    
                    <!-- Decorative top accent bar -->
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r {{ $theme['gradient'] }}"></div>

                    <div>
                        <!-- Header with Emoji Icon & Unit Count Badge -->
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl bg-white shadow-md border border-slate-100 flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                {{ $theme['emoji'] }}
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $theme['bg_light'] }} {{ $theme['accent'] }} text-xs font-black border {{ $theme['border'] }}">
                                <span>📚 {{ $category->sessions_count }} {{ Str::plural('Unit', $category->sessions_count) }}</span>
                            </span>
                        </div>

                        <!-- Subject Names -->
                        <h3 class="text-xl font-black text-slate-950 leading-tight group-hover:text-[#0052FF] transition-colors">
                            {{ $category->name }}
                        </h3>
                        <p class="text-xs font-bold {{ $theme['accent'] }} mt-1">
                            {{ $category->name_malayalam }}
                        </p>

                        <!-- Description -->
                        <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed">
                            {{ $category->description }}
                        </p>

                        <!-- Key Syllabus Bullets -->
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                            @foreach($theme['features'] as $feature)
                                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Direct 1-Click Action to Units -->
                    <div class="mt-6 pt-4 flex flex-col gap-2">
                        <a 
                            href="{{ $category->slug === 'map-study' ? route('map.study') : route('sessions.index', ['subject' => $category->slug]) }}" 
                            class="w-full py-3.5 px-5 text-center font-black text-sm text-white {{ $theme['button_bg'] }} active:scale-95 rounded-2xl shadow-md flex items-center justify-center gap-2 transition"
                        >
                            <span>{{ $category->slug === 'map-study' ? 'Launch 3D Map Lab' : 'Explore ' . $category->name . ' Units' }}</span>
                            <span class="text-base font-bold">➔</span>
                        </a>
                        @if($category->slug === 'map-study')
                            <a 
                                href="{{ route('sessions.index', ['subject' => 'map-study']) }}" 
                                class="w-full py-1.5 text-center font-bold text-xs text-indigo-700 hover:text-indigo-900 transition"
                            >
                                Practice Course Units ({{ $category->sessions_count }}) →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 4-Phase Learning Process Banner -->
        <div class="mt-14 sm:mt-16 bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-8 sm:p-10 text-white shadow-2xl border border-slate-800">
            <div class="max-w-4xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-yellow-400 text-slate-950 text-xs font-black uppercase tracking-wider mb-3">
                    ⭐ PSC Rank-Maker Methodology
                </span>
                <h3 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                    Every Special Lesson Follows the Proven 4-Phase Flow
                </h3>
                <p class="text-sm sm:text-base text-slate-300 mt-2 max-w-2xl mx-auto">
                    Designed to identify weaknesses, deliver high-yield concepts through multimedia, and drill exam reflexes under actual Kerala PSC negative marking.
                </p>

                <!-- 4 Steps Cards -->
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4 text-left">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/15">
                        <div class="text-2xl mb-1">🧪</div>
                        <div class="text-xs font-black text-yellow-300 uppercase tracking-wider">Phase 1</div>
                        <div class="text-sm font-bold text-white mt-0.5">Diagnostic Test</div>
                        <p class="text-[11px] text-slate-300 mt-1">Pinpoint your knowledge gaps before studying.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/15">
                        <div class="text-2xl mb-1">📖</div>
                        <div class="text-xs font-black text-yellow-300 uppercase tracking-wider">Phase 2</div>
                        <div class="text-sm font-bold text-white mt-0.5">Media Lesson</div>
                        <p class="text-[11px] text-slate-300 mt-1">Notes with audio, visual diagrams &amp; video.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/15">
                        <div class="text-2xl mb-1">💡</div>
                        <div class="text-xs font-black text-yellow-300 uppercase tracking-wider">Phase 3</div>
                        <div class="text-sm font-bold text-white mt-0.5">Interactive MCQs</div>
                        <p class="text-[11px] text-slate-300 mt-1">Practice questions with detailed trap explanations.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/15">
                        <div class="text-2xl mb-1">📝</div>
                        <div class="text-xs font-black text-yellow-300 uppercase tracking-wider">Phase 4</div>
                        <div class="text-sm font-bold text-white mt-0.5">PSC OMR Test</div>
                        <p class="text-[11px] text-slate-300 mt-1">Authentic OMR bubbles with -0.33 negative marking.</p>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="mt-8">
                    <a 
                        href="{{ route('sessions.index') }}" 
                        class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-[#0052FF] hover:bg-blue-500 text-white font-black text-base shadow-xl shadow-blue-500/30 active:scale-95 transition border-2 border-yellow-300"
                    >
                        <span>Start Learning Units Now</span>
                        <span class="text-yellow-300 text-xl font-black">➔</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- WHY PSCRANKER UNIT-BASED SYSTEM WORKS -->
<section class="py-16 bg-slate-950 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-black uppercase text-yellow-400 tracking-wider">Built For Kerala PSC Success</span>
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mt-1">
                Engineered for Top 100 Rank Holders
            </h2>
            <p class="text-slate-400 text-sm sm:text-base mt-2">
                In Kerala PSC examinations, losing 1 mark to a trap drops you 500 ranks. Here is how our 4-phase units secure your appointment:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">📝</div>
                <h3 class="text-lg font-black text-white">Realistic OMR Sheet Conditioning</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Most candidates lose ranks to bubbling panic and misread numbers. Our digital OMR simulator builds flawless bubble-filling habits with standard +1.00 and -0.33 rules.
                </p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">🎧</div>
                <h3 class="text-lg font-black text-white">Rich Multimedia Retention</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Retain complex dates, formulas, and grammar rules effortlessly. Every lesson integrates Malayalam audio voice notes, infographic diagrams, and video breakdowns.
                </p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">📱</div>
                <h3 class="text-lg font-black text-white">Installable Mobile PWA App</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Install PSCRanker directly on your smartphone home screen like a native app. Study units on your daily bus commute, lunch break, or 10 minutes before bed.
                </p>
            </div>
        </div>

        <!-- Bottom CTA Banner in Dark Section -->
        <div class="mt-12 text-center">
            <a 
                href="{{ route('sessions.index') }}" 
                class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black text-lg shadow-lg shadow-yellow-500/20 active:scale-95 transition"
            >
                <span>Browse All Course Units</span>
                <span class="text-xl font-bold">➔</span>
            </a>
        </div>

    </div>
</section>

@endsection
