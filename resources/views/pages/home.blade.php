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

                    <!-- Secondary CTA for Speed Drill -->
                    <a 
                        href="#drill-section" 
                        class="px-6 py-4 bg-white hover:bg-slate-100 text-slate-900 font-black text-sm sm:text-base rounded-full border-2 border-slate-200 shadow-sm hover:shadow transition flex items-center justify-center gap-2"
                    >
                        <span>⚡ 3-Min Speed Drill</span>
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
                <div class="relative z-10 w-full max-w-[380px] sm:max-w-[430px] rounded-3xl p-2 group">
                    <img 
                        src="{{ asset('images/hero-mascot.webp') }}" 
                        onerror="this.onerror=null; this.src='{{ asset('images/hero-mascot.jpg') }}'"
                        alt="Kerala PSC Candidate Super Speed Runner" 
                        class="w-full h-auto object-cover rounded-3xl shadow-2xl border-4 border-white transition-transform duration-300 group-hover:scale-[1.01]"
                    >
                </div>

            </div>

        </div>

        <!-- 3 QUICK-ACCESS FEATURE CARDS: Behance Mockup Exact Layout -->
        <div class="mt-12 sm:mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Daily Speed Duel Live Leaderboard (Behance Blue Card) -->
            <div class="bg-white rounded-3xl p-6 border-2 border-blue-200/80 shadow-xl shadow-blue-500/5 hover:shadow-blue-500/15 transition-all flex flex-col justify-between group">
                <div>
                    <!-- Header with Trophy -->
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl shadow-inner">
                            🏆
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-tight">Daily Speed Duel</h3>
                            <p class="text-xs font-bold text-[#0052FF] uppercase tracking-wider">Live Leaderboard</p>
                        </div>
                    </div>

                    <!-- Top 3 Candidates Row (Rahul K., Mini S., Arun P. from screenshot) -->
                    <div class="space-y-2.5 mb-6">
                        @foreach($leaderboardTop as $idx => $candidate)
                            <div class="flex items-center justify-between p-2.5 rounded-xl {{ $idx === 0 ? 'bg-amber-50/70 border border-amber-200/80' : 'bg-slate-50 border border-slate-100' }}">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 h-6 rounded-full {{ $idx === 0 ? 'bg-amber-400 text-slate-950' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : 'bg-amber-600 text-white') }} flex items-center justify-center font-black text-xs">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($candidate->candidate_name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">{{ $candidate->candidate_name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 font-black text-sm text-slate-900">
                                    <span>{{ number_format($candidate->score * 400 + rand(10, 50)) }}</span>
                                    <span class="text-xs text-amber-500">★</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- CTA Button (Behance: Challenge Now) -->
                <a 
                    href="{{ route('leaderboard') }}" 
                    class="w-full py-3 text-center font-black text-sm text-white bg-[#0052FF] hover:bg-blue-700 active:scale-95 rounded-2xl shadow-md transition"
                >
                    Challenge Now ⚡
                </a>
            </div>

            <!-- Card 2: Meme Mnemonics (Behance Yellow Card) -->
            <div class="bg-gradient-to-br from-[#FFD200] to-amber-400 rounded-3xl p-6 border-2 border-yellow-300 shadow-xl shadow-yellow-500/15 flex flex-col justify-between group">
                <div>
                    <!-- Header with Laughing Emoji -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/90 text-slate-900 flex items-center justify-center text-2xl shadow-sm">
                            😂
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-950 leading-tight">Meme Mnemonics</h3>
                            <p class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Funny Kerala GK Capsules</p>
                        </div>
                    </div>

                    <!-- Meme Image Preview (Behance exact: Malayalam movie comedy scene) -->
                    <div class="relative rounded-2xl overflow-hidden shadow-md border-2 border-white/60 mb-5 bg-slate-900">
                        <img 
                            src="/images/meme_card.jpg" 
                            alt="PSC Exam Meme Mnemonics" 
                            class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-slate-950/90 to-transparent p-2 text-center">
                            <span class="text-[11px] font-black text-yellow-300">ക്യാപ്സൂളിൽ കുടുങ്ങിയ PSC ചോദ്യങ്ങൾ!</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Button (Behance: Learn Now) -->
                <a 
                    href="{{ route('memebank') }}" 
                    class="w-full py-3 text-center font-black text-sm text-slate-950 bg-white hover:bg-slate-50 active:scale-95 rounded-2xl shadow-md transition"
                >
                    Learn Now 💡
                </a>
            </div>

            <!-- Card 3: OMR Bubble Simulator (Behance White Card with OMR preview) -->
            <div class="bg-white rounded-3xl p-6 border-2 border-blue-200/80 shadow-xl shadow-blue-500/5 hover:shadow-blue-500/15 transition-all flex flex-col justify-between group">
                <div>
                    <!-- Header with OMR Icon -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#0052FF] flex items-center justify-center text-2xl shadow-inner">
                            📝
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-slate-900 leading-tight">OMR Bubble Simulator</h3>
                            <p class="text-xs font-bold text-[#0052FF] uppercase tracking-wider">Practice Negative Marking</p>
                        </div>
                    </div>

                    <!-- Interactive OMR Sheet Preview (from Behance mockup) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 mb-5 font-mono text-[11px]">
                        <div class="flex justify-between items-center text-slate-500 font-bold border-b border-slate-200 pb-1.5 mb-2">
                            <span>OMR Sheet</span>
                            <span class="text-slate-400">A B C D</span>
                        </div>
                        
                        <!-- Sample Mini Bubbles Row -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-bold">01</span>
                                <div class="flex gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full bg-slate-900 border border-slate-900 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-bold">02</span>
                                <div class="flex gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full bg-slate-900 border border-slate-900 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-bold">03</span>
                                <div class="flex gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full bg-slate-900 border border-slate-900 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                    <span class="w-3.5 h-3.5 rounded-full border border-slate-400 inline-block"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-2 border-t border-slate-200 flex justify-between items-center text-[10px] font-bold text-slate-500">
                            <span>Time remaining:</span>
                            <span class="text-blue-600">15m 00s</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Button (Behance: Practice Now) -->
                <a 
                    href="{{ route('omr.simulator') }}" 
                    class="w-full py-3 text-center font-black text-sm text-white bg-[#0052FF] hover:bg-blue-700 active:scale-95 rounded-2xl shadow-md transition"
                >
                    Practice Now 🎯
                </a>
            </div>

        </div>

    </div>
</section>

<!-- INTERACTIVE 3-MINUTE SPEED DRILL SECTION (Live Playable on Homepage) -->
<section id="drill-section" class="py-16 bg-gradient-to-b from-blue-50/50 to-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-100 text-yellow-900 text-xs font-black uppercase tracking-wider mb-2">
                <span>⚡ Free Live Drill Engine</span>
            </span>
            <h2 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                Experience the 3-Minute Rapid Fire
            </h2>
            <p class="text-base sm:text-lg font-semibold text-slate-600 mt-2">
                Standard Kerala PSC Negative Marking: <strong class="text-emerald-600">+1.00 Mark</strong> for right, <strong class="text-red-600">-0.33 Mark</strong> for wrong!
            </p>
        </div>

        <!-- The Speed Drill Alpine Component -->
        <x-speed-drill :quizId="$featuredQuiz ? $featuredQuiz->id : 1" :standalone="false" />

    </div>
</section>

<!-- CATEGORY EXPLORATION & TOPIC VAULT -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <span class="text-xs font-black uppercase text-[#0052FF] tracking-wider">Targeted Exam Practice</span>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                    Drill by Kerala PSC Syllabus
                </h2>
            </div>
            <p class="text-sm font-semibold text-slate-500 mt-2 md:mt-0">
                Direct questions curated from Previous Question Papers (PYQ) and SCERT Textbooks.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <div class="p-6 rounded-3xl border-2 border-slate-100 hover:border-[#0052FF] bg-gradient-to-b from-slate-50 to-white shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0052FF] flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                            @if($category->icon === 'sparkles') ✨
                            @elseif($category->icon === 'book-open') 📚
                            @elseif($category->icon === 'calculator') 🧮
                            @else 🌍
                            @endif
                        </div>
                        <h3 class="text-lg font-black text-slate-900 leading-snug">{{ $category->name }}</h3>
                        <p class="text-xs font-bold text-[#0052FF] mt-0.5">{{ $category->name_malayalam }}</p>
                        <p class="text-xs text-slate-500 mt-2.5 leading-relaxed">{{ $category->description }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">{{ $category->questions_count }} Trap Questions</span>
                        <a href="{{ route('drill.show') }}" class="text-xs font-black text-[#0052FF] group-hover:translate-x-1 transition flex items-center gap-1">
                            Drill →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

<!-- WHY PSCRANKER GAMIFIED SYSTEM WORKS -->
<section class="py-16 bg-slate-950 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-black uppercase text-yellow-400 tracking-wider">The Science of Speed</span>
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mt-1">
                Why 3-Minute Drills Beat 10-Hour Cramming
            </h2>
            <p class="text-slate-400 text-sm sm:text-base mt-2">
                In Kerala PSC, losing 1 mark to a trap drops you 500 ranks. Here is how we build your exam reflexes:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">🛡️</div>
                <h3 class="text-lg font-black text-white">Reflex Negative Avoidance</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Most students fail because of over-attempting traps. Our 20-second buzzer builds the gut instinct to skip doubtful traps and protect your +1.00 marks.
                </p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">🧠</div>
                <h3 class="text-lg font-black text-white">Meme Mnemonics Memory</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Ever forgotten the year of Temple Entry Proclamation? Associate years and historical figures with unforgettable Malayalam movie comedy scenes.
                </p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
                <div class="text-3xl mb-3">📱</div>
                <h3 class="text-lg font-black text-white">PWA Mobile Friendly</h3>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
                    Install PSCRanker directly on your phone home screen like an app. Practice during your bus commute, tea breaks, or 5 minutes before bed.
                </p>
            </div>
        </div>

        <!-- Bottom CTA Banner in Dark Section -->
        <div class="mt-12 text-center">
            <a 
                href="{{ route('drill.show') }}" 
                class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black text-lg shadow-lg shadow-yellow-500/20 active:scale-95 transition"
            >
                <span>Launch Full-Screen Speed Drill</span>
                <span class="text-xl">⚡</span>
            </a>
        </div>

    </div>
</section>

@endsection
