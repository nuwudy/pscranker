@extends('layouts.app')

@section('title', ($session->title_malayalam ?? $session->title) . ' — 4-Phase Micro-Learning Capsule | PSCRanker')

@section('content')
<div 
    x-data="sessionEngine({
        sessionId: {{ $session->id }},
        sessionTitle: @js($session->title),
        sessionTitleMl: @js($session->title_malayalam),
        xpReward: {{ $session->xp_reward }},
        categoryName: @js($session->category ? $session->category->name : 'Kerala PSC'),
        diagnostic: @js($session->diagnosticQuestion),
        contents: @js($session->contents),
        reinforcement: @js($session->effective_reinforcement_questions),
        omrQuestions: @js($session->effective_omr_questions),
        progressSaveUrl: @js(route('api.session.progress', $session->id)),
        omrSubmitUrl: @js(route('api.session.omr-submit', $session->id)),
        csrfToken: '{{ csrf_token() }}'
    })"
    x-init="initEngine()"
    class="py-4 sm:py-8 bg-gradient-to-b from-blue-50/60 via-slate-50 to-white min-h-[90vh] select-none"
>
@if($isLocked)
    @if(($lockReason ?? 'requires_premium') === 'requires_registration')
        <!-- ============================================================= -->
        <!-- FREE REGISTERED MEMBER ACCESS REQUIRED GATE                   -->
        <!-- ============================================================= -->
        <div class="py-8 sm:py-16 max-w-xl mx-auto px-4">
            <div class="bg-white rounded-3xl border-2 border-blue-200 shadow-2xl p-6 sm:p-10 text-center relative overflow-hidden">
                
                <!-- Glow Accent -->
                <div class="absolute -top-16 -right-16 w-36 h-36 bg-blue-200/40 rounded-full blur-2xl pointer-events-none"></div>

                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-[#0052FF] to-blue-600 text-white flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-blue-500/30">
                    🎓
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-900 border border-blue-200 text-xs font-black uppercase tracking-wider rounded-full mb-3">
                    <span>Free for Registered Members</span>
                </span>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    {{ $session->title }}
                </h1>

                @if($session->title_malayalam)
                    <p class="text-sm font-bold text-[#0052FF] mt-1 font-['Noto_Sans_Malayalam']">
                        {{ $session->title_malayalam }}
                    </p>
                @endif

                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-3 leading-relaxed">
                    This high-yield learning capsule is reserved free for registered members. Create a free account or log in with your mobile number to start learning right now.
                </p>

                <!-- Free Member Benefits -->
                <div class="my-6 p-4 rounded-2xl bg-blue-50/60 border border-blue-200 text-left space-y-2.5 text-xs font-bold text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-black">✓</span>
                        <span>100% Free Forever — No credit card or fee needed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-black">✓</span>
                        <span>Save your scores, diagnostic badges &amp; XP progress</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-black">✓</span>
                        <span>Full access to Diagnostic, Lesson, MCQs &amp; OMR Test</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-600 font-black">✓</span>
                        <span>Quick 10-second registration with name &amp; phone</span>
                    </div>
                </div>

                <!-- Registration & Login CTAs -->
                <div class="space-y-3 mb-6">
                    <a 
                        href="{{ route('register') }}" 
                        class="w-full py-3.5 bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-black text-sm rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 active:scale-95 border border-blue-400"
                    >
                        <span>REGISTER FREE ACCOUNT (10 SECONDS) ⚡</span>
                    </a>
                    
                    <a 
                        href="{{ route('login') }}" 
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs rounded-xl transition flex items-center justify-center gap-1.5 border border-slate-300"
                    >
                        <span>Already registered? Log in with Mobile or Email →</span>
                    </a>
                </div>

                <!-- Navigation Back -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 text-xs font-bold">
                    @if($previousSession)
                        <a href="{{ route('session.show', $previousSession->slug) }}" class="text-slate-600 hover:text-slate-900 hover:underline">
                            ← Previous Unit: {{ Str::limit($previousSession->title, 20) }}
                        </a>
                    @endif
                    <a href="{{ route('sessions.index') }}" class="text-[#0052FF] hover:underline">
                        Browse All Free Units →
                    </a>
                </div>

            </div>
        </div>
    @else
        <!-- ============================================================= -->
        <!-- PREMIUM PAYWALL GATE (PhonePe / Razorpay Ready)              -->
        <!-- ============================================================= -->
        <div class="py-8 sm:py-16 max-w-xl mx-auto px-4">
            <div class="bg-white rounded-3xl border-2 border-amber-300 shadow-2xl p-6 sm:p-10 text-center relative overflow-hidden">
                
                <!-- Glow Accent -->
                <div class="absolute -top-16 -right-16 w-36 h-36 bg-amber-300/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-amber-400 to-yellow-500 text-slate-950 flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-yellow-400/30">
                    👑
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-900 border border-amber-200 text-xs font-black uppercase tracking-wider rounded-full mb-3">
                    <span>PRO Unit Locked</span>
                </span>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    {{ $session->title }}
                </h1>

                @if($session->title_malayalam)
                    <p class="text-sm font-bold text-[#0052FF] mt-1 font-['Noto_Sans_Malayalam']">
                        {{ $session->title_malayalam }}
                    </p>
                @endif

                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-3 leading-relaxed">
                    This is an advanced high-yield PSC Rank Maker capsule featuring exclusive SCERT mnemonics, audio explanations, and full OMR simulator test.
                </p>

                <!-- Feature Badges -->
                <div class="my-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left space-y-2.5 text-xs font-bold text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-500 font-black">✓</span>
                        <span>Phase 1 Diagnostic Trap Hook (+50 XP)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-500 font-black">✓</span>
                        <span>Phase 2 Audio Summary &amp; Visual Mnemonics</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-500 font-black">✓</span>
                        <span>Phase 3 20-Sec Speed Blitz with Multipliers</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-emerald-500 font-black">✓</span>
                        <span>Phase 4 Authentic Kerala PSC OMR Bubble Exam</span>
                    </div>
                </div>

                <!-- Price and Payment Gateway CTA -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-blue-950 text-white shadow-xl mb-6">
                    <span class="text-[10px] uppercase font-bold text-yellow-400 tracking-widest block mb-1">
                        👑 Premium Unit • Prepaid Pass Required
                    </span>
                    <div class="text-2xl sm:text-3xl font-black text-white font-mono">
                        Prepaid Learning Pass
                    </div>
                    <p class="text-[11px] text-slate-300 mt-1">Unlocks all current &amp; upcoming PSC units • Plans start from ₹{{ (int)\App\Models\SiteSetting::get('course_base_monthly_fee', 299) }} (Save up to 40%)</p>

                    <!-- PG Buttons preview -->
                    <div class="mt-4 pt-4 border-t border-slate-800">
                        <a 
                            href="{{ route('pricing') }}" 
                            class="w-full py-3.5 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-300 hover:to-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 active:scale-95"
                        >
                            <span>Unlock with UPI / PhonePe / Razorpay 🚀</span>
                        </a>
                        <div class="flex items-center justify-center gap-3 mt-2.5 text-[10px] text-slate-400">
                            <span>🔒 256-Bit Razorpay</span>
                            <span>•</span>
                            <span>UPI / PhonePe / GPay / Cards</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Back -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 text-xs font-bold">
                    @if($previousSession)
                        <a href="{{ route('session.show', $previousSession->slug) }}" class="text-slate-600 hover:text-slate-900 hover:underline">
                            ← Back to Previous Unit: {{ Str::limit($previousSession->title, 20) }}
                        </a>
                    @endif
                    <a href="{{ route('sessions.index') }}" class="text-[#0052FF] hover:underline">
                        Browse All Free Units →
                    </a>
                </div>

            </div>
        </div>
    @endif
@else
    <div class="max-w-4xl mx-auto px-3 sm:px-6">

        <!-- Top Breadcrumbs & Phase Stepper Header -->
        <div class="bg-white/90 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-blue-100/90 shadow-sm p-3.5 sm:p-5 mb-5 sm:mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3 sm:mb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('sessions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-[#0052FF] hover:underline bg-blue-50/80 px-3 py-1.5 rounded-full border border-blue-100 transition">
                        <span>← Course Units</span>
                    </a>
                    
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-blue-100 text-blue-900 border border-blue-200">
                        <span>Unit {{ $unitNumber }} of {{ $totalUnits }}</span>
                    </span>

                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-purple-100 text-purple-800">
                        <span>⚡</span>
                        <span x-text="categoryName"></span>
                    </span>

                    @if($session->access_level === 'premium' || $session->is_premium)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 shadow-xs">
                            <span>👑 PRO PASS</span>
                        </span>
                    @elseif($session->access_level === 'registered')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-900 border border-blue-300">
                            🔵 MEMBER FREE
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            FREE UNIT
                        </span>
                    @endif
                </div>

                <!-- Unit Navigation & Live XP Pill -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Previous & Next Unit Navigation buttons -->
                    @if($previousSession)
                        <a 
                            href="{{ route('session.show', ['slug' => $previousSession->slug, 'stream' => $stream]) }}" 
                            class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 transition flex items-center gap-1"
                            title="Go to previous unit: {{ $previousSession->title }}"
                        >
                            <span>← Prev Unit</span>
                        </a>
                    @endif

                    @if($nextSession)
                        <a 
                            href="{{ route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) }}" 
                            id="headerNextUnitBtn"
                            style="{{ ($isCompleted ?? false) ? '' : 'display: none;' }}"
                            class="px-2.5 py-1 bg-[#0052FF] hover:bg-blue-700 text-white rounded-lg text-xs font-black transition flex items-center gap-1 shadow-xs"
                            title="Go to next unit: {{ $nextSession->title }}"
                        >
                            <span>Next Unit →</span>
                        </a>
                    @endif

                    <!-- Retake Button in Header -->
                    @if($session->isCustomCode())
                        <button 
                            type="button" 
                            id="headerRetakeBtn"
                            style="{{ ($isCompleted ?? false) ? '' : 'display: none;' }}"
                            onclick="window.PSCRanker?.retakeSession()" 
                            class="px-2.5 py-1 bg-white hover:bg-blue-50 text-[#0052FF] hover:text-blue-800 rounded-lg text-xs font-black border border-blue-200 transition flex items-center gap-1 shadow-2xs active:scale-95 cursor-pointer"
                            title="Reset this session and start fresh from beginning"
                        >
                            <span class="text-sm">🔄</span>
                            <span>Retake</span>
                        </button>
                    @else
                        <button 
                            type="button" 
                            id="headerRetakeBtn"
                            style="{{ ($isCompleted ?? false) ? '' : 'display: none;' }}"
                            @click="restartSession()" 
                            class="px-2.5 py-1 bg-white hover:bg-blue-50 text-[#0052FF] hover:text-blue-800 rounded-lg text-xs font-black border border-blue-200 transition flex items-center gap-1 shadow-2xs active:scale-95 cursor-pointer"
                            title="Reset this session and start fresh from beginning"
                        >
                            <span class="text-sm">🔄</span>
                            <span>Retake</span>
                        </button>
                    @endif

                    <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-900 rounded-full text-xs font-black shadow-xs">
                        <span class="text-amber-500 animate-pulse">⚡</span>
                        <span x-text="totalXpEarned"></span> <span class="text-[10px] text-amber-700 font-bold uppercase">XP</span>
                    </div>

                    <div class="text-[11px] font-mono font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                        ⏱️ <span x-text="formatTime(totalSessionSeconds)">00:00</span>
                    </div>
                </div>
            </div>

            <!-- Session Title & Malayalam Micro-copy -->
            <div class="mb-4">
                <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                    @if($stream === 'general')
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 font-black border border-blue-200">
                            🚂 GENERAL TRAIN • Unit {{ $unitNumber }} of {{ $totalUnits }}
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-black border border-purple-200">
                            {{ strtoupper($session->category ? $session->category->name : 'SUBJECT') }} • Unit {{ $unitNumber }} of {{ $totalUnits }}
                        </span>
                    @endif
                    <span>•</span>
                    <span>{{ $session->category ? $session->category->name : 'General Syllabus' }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>{{ $session->title }}</span>
                </h1>
                @if($session->title_malayalam)
                    <p class="text-sm sm:text-base font-bold text-[#0052FF] mt-0.5 font-['Noto_Sans_Malayalam']">
                        {{ $session->title_malayalam }}
                    </p>
                @endif
            </div>

            @if($session->isCustomCode())
                <!-- Custom Capsule Info Banner -->
                <div class="mt-3 pt-3 border-t border-blue-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-black">
                        <span>⚡ Self-Contained Interactive Capsule</span>
                    </span>
                    <span class="text-slate-500 font-bold text-[11px]">Contains Hook Question • Micro-Lesson • MCQs • OMR Test</span>
                </div>
            @else
                <!-- 4-Phase Progress Indicator Stepper -->
                <div class="grid grid-cols-4 gap-1.5 sm:gap-3 text-center">
                    <!-- Phase 1: Diagnostic -->
                    <div 
                        @click="canJumpTo('diagnostic') ? setPhase('diagnostic') : null"
                        :class="{
                            'border-[#0052FF] bg-blue-50/70 text-[#0052FF] ring-2 ring-blue-500/20 font-black': currentPhase === 'diagnostic',
                            'border-emerald-300 bg-emerald-50 text-emerald-800 font-bold': phaseUnlocked.lesson,
                            'border-slate-200 bg-slate-50/70 text-slate-400': !phaseUnlocked.lesson && currentPhase !== 'diagnostic',
                            'cursor-pointer hover:shadow-xs': canJumpTo('diagnostic')
                        }"
                        class="p-2 sm:p-2.5 rounded-xl border transition-all text-left flex flex-col justify-between"
                    >
                        <div class="flex items-center justify-between text-[10px] sm:text-xs">
                            <span class="font-bold uppercase tracking-wider">Phase 1</span>
                            <span x-show="phaseUnlocked.lesson">✅</span>
                            <span x-show="!phaseUnlocked.lesson && currentPhase === 'diagnostic'" class="animate-pulse">🎯</span>
                        </div>
                        <div class="text-[11px] sm:text-xs font-black truncate mt-1">Diagnostic Hook</div>
                    </div>

                    <!-- Phase 2: Lesson Capsule -->
                    <div 
                        @click="canJumpTo('lesson') ? setPhase('lesson') : null"
                        :class="{
                            'border-[#0052FF] bg-blue-50/70 text-[#0052FF] ring-2 ring-blue-500/20 font-black': currentPhase === 'lesson',
                            'border-emerald-300 bg-emerald-50 text-emerald-800 font-bold': phaseUnlocked.reinforcement,
                            'border-slate-200 bg-slate-50/70 text-slate-400': !phaseUnlocked.lesson,
                            'cursor-pointer hover:shadow-xs': canJumpTo('lesson')
                        }"
                        class="p-2 sm:p-2.5 rounded-xl border transition-all text-left flex flex-col justify-between"
                    >
                        <div class="flex items-center justify-between text-[10px] sm:text-xs">
                            <span class="font-bold uppercase tracking-wider">Phase 2</span>
                            <span x-show="phaseUnlocked.reinforcement">✅</span>
                            <span x-show="!phaseUnlocked.lesson">🔒</span>
                            <span x-show="phaseUnlocked.lesson && !phaseUnlocked.reinforcement && currentPhase === 'lesson'" class="animate-pulse">📖</span>
                        </div>
                        <div class="text-[11px] sm:text-xs font-black truncate mt-1">Micro-Lesson</div>
                    </div>

                    <!-- Phase 3: Speed Blitz -->
                    <div 
                        @click="canJumpTo('reinforcement') ? setPhase('reinforcement') : null"
                        :class="{
                            'border-[#0052FF] bg-blue-50/70 text-[#0052FF] ring-2 ring-blue-500/20 font-black': currentPhase === 'reinforcement',
                            'border-emerald-300 bg-emerald-50 text-emerald-800 font-bold': phaseUnlocked.omr,
                            'border-slate-200 bg-slate-50/70 text-slate-400': !phaseUnlocked.reinforcement,
                            'cursor-pointer hover:shadow-xs': canJumpTo('reinforcement')
                        }"
                        class="p-2 sm:p-2.5 rounded-xl border transition-all text-left flex flex-col justify-between"
                    >
                        <div class="flex items-center justify-between text-[10px] sm:text-xs">
                            <span class="font-bold uppercase tracking-wider">Phase 3</span>
                            <span x-show="phaseUnlocked.omr">✅</span>
                            <span x-show="!phaseUnlocked.reinforcement">🔒</span>
                            <span x-show="phaseUnlocked.reinforcement && !phaseUnlocked.omr && currentPhase === 'reinforcement'" class="animate-pulse">⚡</span>
                        </div>
                        <div class="text-[11px] sm:text-xs font-black truncate mt-1">Speed Blitz</div>
                    </div>

                    <!-- Phase 4: Final OMR -->
                    <div 
                        @click="canJumpTo('omr') ? setPhase('omr') : null"
                        :class="{
                            'border-[#0052FF] bg-blue-50/70 text-[#0052FF] ring-2 ring-blue-500/20 font-black': currentPhase === 'omr' || currentPhase === 'summary',
                            'border-emerald-300 bg-emerald-50 text-emerald-800 font-bold': sessionCompleted,
                            'border-slate-200 bg-slate-50/70 text-slate-400': !phaseUnlocked.omr,
                            'cursor-pointer hover:shadow-xs': canJumpTo('omr')
                        }"
                        class="p-2 sm:p-2.5 rounded-xl border transition-all text-left flex flex-col justify-between"
                    >
                        <div class="flex items-center justify-between text-[10px] sm:text-xs">
                            <span class="font-bold uppercase tracking-wider">Phase 4</span>
                            <span x-show="sessionCompleted">🏆</span>
                            <span x-show="!phaseUnlocked.omr">🔒</span>
                            <span x-show="phaseUnlocked.omr && !sessionCompleted" class="animate-pulse">📝</span>
                        </div>
                        <div class="text-[11px] sm:text-xs font-black truncate mt-1">OMR Challenge</div>
                    </div>
                </div>
            @endif
        </div>

        @if($session->isCustomCode())
            <!-- ========================================================= -->
            <!-- CUSTOM CODE SESSION CANVAS (Interactive Custom HTML)     -->
            <!-- ========================================================= -->
            <div class="custom-session-wrapper mb-10">
                @if($session->feature_video || $session->feature_image)
                    <!-- Feature Media for Custom Code Capsule (Docked strictly into the lesson screen via script) -->
                    <div id="psc-custom-feature-image-banner" class="hidden mb-6 rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-white relative">
                        @if($session->feature_video)
                            @if($session->isFeatureVideoEmbed())
                                <div class="w-full aspect-video">
                                    <iframe 
                                        src="{{ $session->getFeatureVideoEmbedUrl() }}" 
                                        title="{{ $session->title }}"
                                        class="w-full h-full rounded-2xl" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            @else
                                <div class="w-full aspect-video bg-black flex items-center justify-center">
                                    <video 
                                        controls 
                                        playsinline 
                                        preload="metadata"
                                        poster="{{ $session->feature_image }}" 
                                        class="w-full h-full max-h-[480px] rounded-2xl object-contain"
                                    >
                                        <source src="{{ $session->feature_video }}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        @elseif($session->feature_image)
                            <div class="max-h-[460px] flex items-center justify-center">
                                <img 
                                    src="{{ $session->feature_image }}" 
                                    alt="{{ $session->title }}"
                                    class="w-full h-auto max-h-[460px] object-cover sm:object-contain rounded-2xl"
                                    loading="eager"
                                >
                            </div>
                        @endif
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-blue-100/90 shadow-md p-4 sm:p-8 relative">
                    {!! $session->custom_html !!}
                </div>

                <!-- Slim Unified Completion Action Bar: Revealed ONLY when session is completed -->
                <div 
                    id="pscranker-bottom-completion-bar" 
                    style="{{ ($isCompleted ?? false) ? '' : 'display: none;' }}"
                    class="mt-4 bg-slate-900 border border-slate-800 rounded-2xl px-4 py-2.5 shadow-md flex flex-col sm:flex-row items-center justify-between gap-3 transition-all duration-300"
                >
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-300">
                        <span class="text-amber-400 text-base">🎉</span>
                        <span id="pscranker-completion-status-text">{{ ($isCompleted ?? false) ? 'Session Completed!' : 'Finished this lesson?' }}</span>
                        <span class="text-slate-500 font-normal text-[11px] hidden md:inline">• Continue to the next unit or retake</span>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <!-- Retake button: revealed when session is completed -->
                        <button 
                            type="button" 
                            id="pscranker-retake-unit-btn"
                            onclick="window.PSCRanker?.retakeSession()"
                            style="{{ ($isCompleted ?? false) ? 'display: inline-flex;' : 'display: none;' }}"
                            class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-blue-400 hover:text-blue-300 font-bold text-xs rounded-xl border border-slate-700 transition active:scale-95 items-center gap-1.5 cursor-pointer"
                            title="Reset all questions and restart from beginning"
                        >
                            <span>🔄 Retake Unit</span>
                        </button>

                        <button 
                            type="button" 
                            id="pscranker-complete-unit-btn"
                            onclick="window.PSCRanker?.completeSession()"
                            style="display: none;"
                            class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-yellow-400 to-amber-500 hover:from-yellow-300 hover:to-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl shadow transition active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span>Claim +{{ $session->xp_reward }} XP &amp; Complete 🚀</span>
                        </button>

                        @if($nextSession)
                            <a 
                                href="{{ route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) }}" 
                                id="pscranker-next-unit-btn"
                                class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-[#0052FF] via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow transition active:scale-95 flex items-center justify-center gap-1.5 cursor-pointer"
                                title="Next Unit: {{ $nextSession->title }}"
                            >
                                <span>അടുത്ത യൂണിറ്റ് (Next Unit ➔)</span>
                            </a>
                        @else
                            <a 
                                href="{{ route('sessions.index', ['stream' => $stream]) }}" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition"
                            >
                                <span>🎉 All Units Completed!</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
        <!-- ============================================================= -->
        <!-- PHASE 1: DIAGNOSTIC HOOK (Pre-Test)                           -->
        <!-- ============================================================= -->
        <div x-show="currentPhase === 'diagnostic'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
            <template x-if="diagnostic">
                <div class="bg-white rounded-3xl border-2 border-blue-100 shadow-xl p-5 sm:p-8 relative overflow-hidden">
                    
                    <!-- Decorative Background elements -->
                    <div class="absolute -top-10 -right-10 w-36 h-36 bg-yellow-100/60 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Phase 1 Header Banner -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-sm">
                                1
                            </span>
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider text-blue-600">Diagnostic Hook (Pre-Test)</span>
                                <p class="text-[11px] text-slate-500 font-medium">Test your instinct before reading the concept capsule!</p>
                            </div>
                        </div>

                        <span class="text-xs font-black bg-amber-100 text-amber-900 px-3 py-1 rounded-full border border-amber-200">
                            +50 XP First Strike ⚡
                        </span>
                    </div>

                    <!-- Diagnostic Question Text -->
                    <div class="mb-6">
                        <h2 class="text-lg sm:text-2xl font-black text-slate-900 leading-snug font-['Outfit']" x-text="diagnostic.question_text"></h2>
                        <template x-if="diagnostic.question_text_malayalam">
                            <p class="text-base sm:text-xl font-bold text-[#0052FF] mt-2 leading-relaxed font-['Noto_Sans_Malayalam']" x-text="diagnostic.question_text_malayalam"></p>
                        </template>
                    </div>

                    <!-- MCQ Options List -->
                    <div class="space-y-3 mb-6">
                        <template x-for="(opt, idx) in getQuestionOptions(diagnostic)" :key="opt.key">
                            <button 
                                @click="answerDiagnostic(opt.key)"
                                :disabled="diagnosticState.answered"
                                class="w-full text-left p-4 rounded-2xl border-2 transition-all flex items-center justify-between gap-3 group active:scale-[0.99]"
                                :class="{
                                    'bg-white border-slate-200 hover:border-[#0052FF] hover:bg-blue-50/40 text-slate-800': !diagnosticState.answered,
                                    'bg-emerald-50 border-emerald-500 text-emerald-950 font-bold shadow-md shadow-emerald-500/10': diagnosticState.answered && opt.key === diagnostic.correct_option,
                                    'bg-red-50 border-red-400 text-red-950': diagnosticState.answered && diagnosticState.selectedOption === opt.key && opt.key !== diagnostic.correct_option,
                                    'opacity-50 border-slate-200 bg-slate-50': diagnosticState.answered && opt.key !== diagnostic.correct_option && diagnosticState.selectedOption !== opt.key
                                }"
                            >
                                <div class="flex items-center gap-3.5">
                                    <span 
                                        class="w-9 h-9 rounded-xl border flex items-center justify-center font-black text-sm shrink-0 transition"
                                        :class="{
                                            'bg-slate-100 border-slate-300 text-slate-700 group-hover:bg-[#0052FF] group-hover:text-white group-hover:border-[#0052FF]': !diagnosticState.answered,
                                            'bg-emerald-600 border-emerald-600 text-white': diagnosticState.answered && opt.key === diagnostic.correct_option,
                                            'bg-red-600 border-red-600 text-white': diagnosticState.answered && diagnosticState.selectedOption === opt.key && opt.key !== diagnostic.correct_option,
                                            'bg-slate-100 border-slate-200 text-slate-400': diagnosticState.answered && opt.key !== diagnostic.correct_option && diagnosticState.selectedOption !== opt.key
                                        }"
                                        x-text="opt.key"
                                    ></span>
                                    <span class="text-sm sm:text-base font-semibold leading-relaxed font-['Noto_Sans_Malayalam']" x-text="opt.text"></span>
                                </div>

                                <!-- Feedback icon -->
                                <div>
                                    <template x-if="diagnosticState.answered && opt.key === diagnostic.correct_option">
                                        <span class="text-xl text-emerald-600">✅</span>
                                    </template>
                                    <template x-if="diagnosticState.answered && diagnosticState.selectedOption === opt.key && opt.key !== diagnostic.correct_option">
                                        <span class="text-xl text-red-500">❌</span>
                                    </template>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Interactive Immediate Feedback Card -->
                    <template x-if="diagnosticState.answered">
                        <div class="mt-6 pt-6 border-t-2 border-slate-100">
                            
                            <!-- If Correct: Confetti, Praise, +50 XP -->
                            <template x-if="diagnosticState.isCorrect">
                                <div class="p-5 sm:p-6 bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-emerald-500/10 border-2 border-emerald-400 rounded-2xl">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 shadow-md shadow-emerald-500/30">
                                            🎉
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base sm:text-lg font-black text-emerald-900 font-['Noto_Sans_Malayalam']">
                                                    കലക്കി! Rank Maker Move! 🚀
                                                </h3>
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-200 text-emerald-950 animate-bounce">
                                                    +50 First Strike XP
                                                </span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-emerald-800 font-medium mt-1 leading-relaxed">
                                                You nailed the core instinct right off the bat! Let's explore the deep historical details and mnemonics in the lesson capsule.
                                            </p>
                                            <template x-if="diagnostic.explanation || diagnostic.explanation_malayalam">
                                                <div class="mt-3 p-3 bg-white/80 rounded-xl border border-emerald-200 text-xs text-slate-700 font-['Noto_Sans_Malayalam']" x-text="diagnostic.explanation_malayalam || diagnostic.explanation"></div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex justify-end">
                                        <button 
                                            @click="proceedToLesson()" 
                                            class="w-full sm:w-auto px-6 py-3 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white text-sm font-black rounded-xl shadow-md transition flex items-center justify-center gap-2"
                                        >
                                            <span>പാഠത്തിലേക്ക് കടക്കാം (Open Lesson Capsule)</span>
                                            <span>→</span>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- If Incorrect: Humorous Admonition & Trap Explanation -->
                            <template x-if="!diagnosticState.isCorrect">
                                <div class="p-5 sm:p-6 bg-gradient-to-r from-amber-500/10 via-red-500/5 to-amber-500/10 border-2 border-amber-400 rounded-2xl">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl shrink-0 shadow-md shadow-amber-500/30">
                                            💡
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-base sm:text-lg font-black text-amber-950 font-['Noto_Sans_Malayalam']">
                                                    PSC പണി തന്നല്ലോ! കുഴപ്പമില്ല, നേരെ പാഠത്തിലേക്ക് വിട്ടോ! ⚡
                                                </h3>
                                            </div>
                                            <p class="text-xs sm:text-sm text-amber-900 font-medium mt-1">
                                                This was a classic PSC trap question designed to catch 80% of candidates! Good news: Diagnostic doesn't penalize your rank score.
                                            </p>
                                            
                                            <!-- Trap Warning Box -->
                                            <template x-if="diagnostic.trap_warning_text || diagnostic.trap_warning">
                                                <div class="mt-3 p-3.5 bg-amber-50 rounded-xl border border-amber-300 text-xs sm:text-sm text-amber-950 font-bold font-['Noto_Sans_Malayalam'] flex items-start gap-2">
                                                    <span class="text-base">⚠️</span>
                                                    <div>
                                                        <span class="font-black underline uppercase text-[10px] tracking-wider text-amber-800 block mb-0.5">PSC Trap Alert:</span>
                                                        <span x-text="diagnostic.trap_warning_text || diagnostic.trap_warning"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Explanation -->
                                            <template x-if="diagnostic.explanation_malayalam || diagnostic.explanation">
                                                <div class="mt-2.5 text-xs text-slate-700 font-['Noto_Sans_Malayalam']" x-text="diagnostic.explanation_malayalam || diagnostic.explanation"></div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex justify-end">
                                        <button 
                                            @click="proceedToLesson()" 
                                            class="w-full sm:w-auto px-6 py-3 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 active:scale-95 text-sm font-black rounded-xl shadow-md transition flex items-center justify-center gap-2 border border-yellow-400"
                                        >
                                            <span>പാഠത്തിലേക്ക് കടക്കാം (Unlock Lesson Capsule)</span>
                                            <span>→</span>
                                        </button>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>

                </div>
            </template>
            <template x-if="!diagnostic">
                <div class="bg-white rounded-3xl p-8 text-center text-slate-600">
                    <p>No diagnostic question found. Proceed directly to the lesson!</p>
                    <button @click="proceedToLesson()" class="mt-4 px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl">Continue to Lesson</button>
                </div>
            </template>
        </div>

        <!-- ============================================================= -->
        <!-- PHASE 2: MULTIMEDIA MICRO-LESSON CAPSULE                      -->
        <!-- ============================================================= -->
        <div x-show="currentPhase === 'lesson'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white rounded-3xl border-2 border-blue-100 shadow-xl p-5 sm:p-8">
                
                <!-- Phase 2 Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-sm">
                            2
                        </span>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-blue-600">Phase 2: Multimedia Micro-Lesson Capsule</span>
                            <p class="text-[11px] text-slate-500 font-medium">Concept summaries, mnemonic infographics, audio bites & SCERT highlights</p>
                        </div>
                    </div>
                    <span class="text-xs font-black bg-blue-100 text-[#0052FF] px-3 py-1 rounded-full">
                        Concept Capsule
                    </span>
                </div>

                @if($session->feature_video || $session->feature_image)
                    <!-- Featured Media / Featured Image Banner above Manual Lesson Capsule Blocks -->
                    <div class="mb-6 rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-white relative">
                        @if($session->feature_video)
                            @if($session->isFeatureVideoEmbed())
                                <div class="w-full aspect-video">
                                    <iframe 
                                        src="{{ $session->getFeatureVideoEmbedUrl() }}" 
                                        title="{{ $session->title }}"
                                        class="w-full h-full rounded-2xl" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            @else
                                <div class="w-full aspect-video bg-black flex items-center justify-center">
                                    <video 
                                        controls 
                                        playsinline 
                                        preload="metadata"
                                        poster="{{ $session->feature_image }}" 
                                        class="w-full h-full max-h-[480px] rounded-2xl object-contain"
                                    >
                                        <source src="{{ $session->feature_video }}">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        @elseif($session->feature_image)
                            <div class="max-h-[460px] flex items-center justify-center">
                                <img 
                                    src="{{ $session->feature_image }}" 
                                    alt="{{ $session->title }}"
                                    class="w-full h-auto max-h-[460px] object-cover sm:object-contain rounded-2xl"
                                    loading="eager"
                                >
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Dynamic Content Blocks -->
                <div class="space-y-6">
                    <template x-for="(block, idx) in contents" :key="block.id || idx">
                        <div class="rounded-2xl border border-slate-200/90 overflow-hidden bg-[#FAFBFD] transition hover:border-blue-300">
                            
                            <!-- 1. IMAGE BLOCK (Infographic / Meme Mnemonic Card) -->
                            <template x-if="block.type === 'image'">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">🖼️</span>
                                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide">
                                                <span x-text="block.content_data.title || 'Infographic Mnemonic Card'"></span>
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                            Visual Memory
                                        </span>
                                    </div>
                                    <div class="rounded-xl overflow-hidden bg-slate-900/5 border border-slate-200 text-center">
                                        <img 
                                            :src="block.content_data.url" 
                                            :alt="block.content_data.caption || 'Infographic'"
                                            class="w-full max-h-[420px] object-contain mx-auto transition-transform duration-200 hover:scale-[1.01]"
                                            loading="lazy"
                                        >
                                    </div>
                                    <template x-if="block.content_data.caption">
                                        <p class="text-xs sm:text-sm text-slate-600 mt-2.5 font-semibold font-['Noto_Sans_Malayalam'] italic text-center" x-text="block.content_data.caption"></p>
                                    </template>
                                </div>
                            </template>

                            <!-- 2. AUDIO BLOCK (Custom Audio Player for 30-60s Spoken Summary) -->
                            <template x-if="block.type === 'audio'">
                                <div class="p-4 sm:p-5 bg-gradient-to-r from-blue-900 to-slate-900 text-white rounded-2xl">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center text-sm">🎙️</span>
                                            <div>
                                                <span class="text-xs font-black text-white uppercase tracking-wide block" x-text="block.content_data.title || '30-Sec Spoken Concept Capsule'"></span>
                                                <span class="text-[10px] text-blue-300 font-medium">Quick audio revision for memory retention</span>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-mono font-bold bg-blue-800 text-blue-200 px-2 py-0.5 rounded-full">
                                            <span x-text="block.content_data.duration || '0:45'"></span>
                                        </span>
                                    </div>

                                    <!-- Custom Audio Player Bar -->
                                    <div class="mt-4 p-3 bg-slate-800/80 rounded-xl border border-slate-700 flex flex-col sm:flex-row items-center gap-4">
                                        <div class="flex items-center gap-3 w-full sm:w-auto">
                                            <!-- Play/Pause Button -->
                                            <button 
                                                @click="toggleAudio(block.id || idx, block.content_data.url)"
                                                class="w-11 h-11 rounded-full bg-[#FFD200] hover:bg-yellow-400 active:scale-95 text-slate-950 flex items-center justify-center text-lg font-black shrink-0 shadow-md transition"
                                            >
                                                <span x-show="!isPlayingAudio(block.id || idx)">▶</span>
                                                <span x-show="isPlayingAudio(block.id || idx)">⏸</span>
                                            </button>
                                            
                                            <!-- Waveform simulation bars -->
                                            <div class="flex items-end gap-1 h-6 shrink-0">
                                                <span class="w-1 bg-yellow-400 rounded-full transition-all duration-150" :class="isPlayingAudio(block.id || idx) ? 'h-5 animate-pulse' : 'h-2'"></span>
                                                <span class="w-1 bg-yellow-400 rounded-full transition-all duration-150" :class="isPlayingAudio(block.id || idx) ? 'h-6 animate-bounce' : 'h-4'"></span>
                                                <span class="w-1 bg-yellow-400 rounded-full transition-all duration-150" :class="isPlayingAudio(block.id || idx) ? 'h-3 animate-pulse' : 'h-1'"></span>
                                                <span class="w-1 bg-yellow-400 rounded-full transition-all duration-150" :class="isPlayingAudio(block.id || idx) ? 'h-5 animate-bounce' : 'h-3'"></span>
                                                <span class="w-1 bg-yellow-400 rounded-full transition-all duration-150" :class="isPlayingAudio(block.id || idx) ? 'h-4 animate-pulse' : 'h-2'"></span>
                                            </div>
                                        </div>

                                        <!-- Spoken summary transcript / description -->
                                        <div class="text-xs text-slate-300 font-['Noto_Sans_Malayalam'] leading-relaxed flex-grow">
                                            <span x-text="block.content_data.transcript || 'ഹെഡ്‌സെറ്റ് വച്ച് ശ്രദ്ധിച്ചു കേൾക്കൂ — അരുവിപ്പുറം ശിവപ്രതിഷ്ഠയുടെ ചരിത്രപരമായ പ്രാധാന്യവും PSC പരീക്ഷകളിൽ നിരന്തരം ആവർത്തിക്കുന്ന പ്രധാന പോയിന്റുകളും.'"></span>
                                        </div>
                                    </div>
                                    <audio :id="'audio-player-' + (block.id || idx)" :src="block.content_data.url" preload="none" class="hidden"></audio>
                                </div>
                            </template>

                            <!-- 3. VIDEO BLOCK (Embedded Short Explainer Video / Reel format) -->
                            <template x-if="block.type === 'video'">
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">🎬</span>
                                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide" x-text="block.content_data.title || 'Micro-Video Explainer'"></span>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                                            Video Capsule
                                        </span>
                                    </div>
                                    
                                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900 shadow-md">
                                        <template x-if="isVideoEmbed(block.content_data.url)">
                                            <iframe 
                                                :src="getVideoEmbedUrl(block.content_data.url)" 
                                                class="w-full h-full border-0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen
                                            ></iframe>
                                        </template>
                                        <template x-if="!isVideoEmbed(block.content_data.url)">
                                            <video 
                                                controls 
                                                class="w-full h-full object-cover" 
                                                :src="block.content_data.url"
                                            ></video>
                                        </template>
                                    </div>
                                    <template x-if="block.content_data.caption">
                                        <p class="text-xs text-slate-600 mt-2 font-medium font-['Noto_Sans_Malayalam']" x-text="block.content_data.caption"></p>
                                    </template>
                                </div>
                            </template>

                            <!-- 4. TEXT BLOCK (Key Bullet Points, SCERT References, Syllabus Tags) -->
                            <template x-if="block.type === 'text' || block.type === 'html'">
                                <div class="p-5 sm:p-6 bg-white">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">📚</span>
                                            <h3 class="text-sm sm:text-base font-black text-slate-900" x-text="block.content_data.title || 'Key Points & SCERT Focus'"></h3>
                                        </div>
                                        
                                        <!-- Syllabus Tag Pills -->
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <template x-for="tag in (block.content_data.tags || ['#SCERTStd9', '#KeralaRenaissance', '#LDC2024'])" :key="tag">
                                                <span class="text-[10px] font-black bg-blue-50 text-[#0052FF] border border-blue-200 px-2 py-0.5 rounded-md" x-text="tag"></span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- SCERT Reference Callout -->
                                    <template x-if="block.content_data.scert_reference">
                                        <div class="mb-4 p-3 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-xs font-bold text-emerald-950 flex items-center gap-2">
                                            <span>📖</span>
                                            <span>SCERT Textbook Source: <strong x-text="block.content_data.scert_reference"></strong></span>
                                        </div>
                                    </template>

                                    <!-- Formatted Content -->
                                    <div class="prose prose-sm max-w-none text-slate-700 font-['Noto_Sans_Malayalam'] space-y-2 leading-relaxed" x-html="block.content_data.body"></div>
                                </div>
                            </template>

                            <!-- 5. 3D GLOBE / MAP BLOCK (Spatial Visualization Engine) -->
                            <template x-if="block.type === 'map_globe'">
                                <div 
                                    x-data="{
                                        globeInstance: null,
                                        activePin: null,
                                        currentMode: block.content_data.mode || '3d_globe',
                                        isSpinning: false,
                                        initSessionGlobe() {
                                            this.$nextTick(() => {
                                                const canvas = document.getElementById('session-globe-canvas-' + (block.id || idx));
                                                if (!canvas || !window.PscGlobe) return;

                                                this.globeInstance = new window.PscGlobe(canvas, {
                                                    mode: this.currentMode,
                                                    centerLat: block.content_data.center_lat !== undefined ? block.content_data.center_lat : 20.0,
                                                    centerLng: block.content_data.center_lng !== undefined ? block.content_data.center_lng : 78.0,
                                                    zoom: block.content_data.zoom || 1.4,
                                                    autoSpin: false,
                                                    markers: block.content_data.markers || [],
                                                    routes: block.content_data.routes || [],
                                                    onMarkerClick: (m) => {
                                                        this.activePin = m;
                                                    }
                                                });
                                            });
                                        },
                                        toggleMode() {
                                            this.currentMode = (this.currentMode === '3d_globe') ? '2d_map' : '3d_globe';
                                            if (this.globeInstance) {
                                                this.globeInstance.setMode(this.currentMode);
                                            }
                                        },
                                        toggleSpin() {
                                            if (this.globeInstance) {
                                                this.isSpinning = this.globeInstance.toggleAutoSpin();
                                            }
                                        },
                                        focusPin(pin) {
                                            this.activePin = pin;
                                            if (this.globeInstance) {
                                                this.globeInstance.activeMarker = pin;
                                                this.globeInstance.flyTo(pin.lat, pin.lng, Math.max(1.8, (block.content_data.zoom || 1.4) * 1.15));
                                            }
                                        },
                                        reset() {
                                            this.activePin = null;
                                            if (this.globeInstance) {
                                                this.globeInstance.flyTo(
                                                    block.content_data.center_lat !== undefined ? block.content_data.center_lat : 20.0,
                                                    block.content_data.center_lng !== undefined ? block.content_data.center_lng : 78.0,
                                                    block.content_data.zoom || 1.4
                                                );
                                            }
                                        }
                                    }"
                                    x-init="initSessionGlobe()"
                                    class="p-4 sm:p-6 bg-[#0B132B] text-white rounded-2xl overflow-hidden"
                                >
                                    <!-- Header -->
                                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-800">
                                        <div class="flex items-center gap-2">
                                            <span class="w-8 h-8 rounded-lg bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-base">🌐</span>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="text-sm sm:text-base font-black text-white" x-text="block.content_data.title || '3D Globe Spatial Exploration'"></h3>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                        Spatial Map
                                                    </span>
                                                </div>
                                                <template x-if="block.content_data.title_malayalam">
                                                    <p class="text-xs font-bold text-amber-300 font-['Noto_Sans_Malayalam'] mt-0.5" x-text="block.content_data.title_malayalam"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Canvas Control Buttons -->
                                        <div class="flex items-center gap-1.5 bg-slate-900/90 p-1 rounded-xl border border-slate-800">
                                            <button 
                                                type="button" 
                                                @click="toggleMode()" 
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg transition"
                                                :class="currentMode === '3d_globe' ? 'bg-[#0052FF] text-white' : 'text-slate-400 hover:text-white'"
                                            >
                                                <span x-show="currentMode === '3d_globe'">🌐 3D Globe</span>
                                                <span x-show="currentMode !== '3d_globe'">🗺️ 2D Map</span>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="toggleSpin()" 
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg transition"
                                                :class="isSpinning ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'"
                                                title="Toggle Rotation"
                                            >
                                                <span x-text="isSpinning ? '⏸' : '▶ Spin'"></span>
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="reset()" 
                                                class="px-2 py-1 text-xs font-bold text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
                                                title="Reset View"
                                            >
                                                🎯
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Canvas Viewport -->
                                    <div 
                                        class="relative w-full rounded-2xl overflow-hidden bg-slate-950 border-2 border-slate-800 cursor-grab active:cursor-grabbing shadow-inner"
                                        style="width: 100%; height: 380px; min-height: 320px; position: relative;"
                                    >
                                        <canvas :id="'session-globe-canvas-' + (block.id || idx)" style="width: 100%; height: 100%; display: block;"></canvas>
                                        
                                        <div class="absolute bottom-3 left-3 text-[10px] text-slate-400 bg-slate-900/80 backdrop-blur-md px-2.5 py-0.5 rounded-full border border-slate-800 pointer-events-none">
                                            Drag to rotate • Scroll to zoom • Tap pins
                                        </div>
                                    </div>

                                    <!-- Pinpoints Pills -->
                                    <template x-if="block.content_data.markers && block.content_data.markers.length">
                                        <div class="mt-4 pt-3 border-t border-slate-800/80">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Exam Pinpoints:</span>
                                                <span class="text-[10px] text-slate-500">Tap pin to rotate</span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <template x-for="(pin, pIdx) in block.content_data.markers" :key="pIdx">
                                                    <button 
                                                        type="button" 
                                                        @click="focusPin(pin)"
                                                        class="px-2.5 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 border"
                                                        :class="activePin && activePin.label === pin.label ? 'bg-amber-400 text-slate-950 border-amber-300 font-black' : 'bg-slate-900 hover:bg-slate-800 text-slate-200 border-slate-800'"
                                                    >
                                                        <span class="w-1.5 h-1.5 rounded-full" :style="'background-color: ' + (pin.color || '#38BDF8')"></span>
                                                        <span x-text="pin.label"></span>
                                                    </button>
                                                </template>
                                            </div>

                                            <!-- Active Pin Inspector Card -->
                                            <template x-if="activePin">
                                                <div class="mt-3 p-3 bg-slate-900/90 rounded-xl border border-amber-400/30 text-xs">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-black text-amber-300" x-text="activePin.label"></span>
                                                        <button type="button" @click="activePin = null" class="text-slate-400 hover:text-white">✕</button>
                                                    </div>
                                                    <p class="text-slate-200 mt-1" x-text="activePin.note"></p>
                                                    <template x-if="activePin.note_malayalam">
                                                        <p class="text-amber-200 mt-1 font-semibold font-['Noto_Sans_Malayalam']" x-text="activePin.note_malayalam"></p>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Explanatory Notes & Malayalam Spatial Tip -->
                                    <template x-if="block.content_data.description || block.content_data.notes_malayalam">
                                        <div class="mt-4 p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2 text-xs">
                                            <template x-if="block.content_data.notes_malayalam">
                                                <p class="text-slate-200 font-medium font-['Noto_Sans_Malayalam'] leading-relaxed" x-text="block.content_data.notes_malayalam"></p>
                                            </template>
                                            <template x-if="block.content_data.description">
                                                <p class="text-slate-400 leading-relaxed italic" x-text="block.content_data.description"></p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>

                <!-- Mark Complete & Continue Action -->
                <div class="mt-8 pt-6 border-t-2 border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                        <span>💡 Concept mastered? Test your retention in Speed Blitz!</span>
                    </div>

                    <button 
                        @click="completeLessonAndProceed()" 
                        class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white font-black text-sm sm:text-base rounded-2xl shadow-xl shadow-blue-500/25 active:scale-95 transition flex items-center justify-center gap-3 border border-blue-400"
                    >
                        <span>Mark Complete & Start Speed Blitz</span>
                        <span class="text-yellow-300 text-xl">⚡</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- PHASE 3: REINFORCEMENT DRILL (Speed Blitz)                    -->
        <!-- ============================================================= -->
        <div x-show="currentPhase === 'reinforcement'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white rounded-3xl border-2 border-blue-100 shadow-xl overflow-hidden">
                
                <!-- Speed Blitz Top Header -->
                <div class="bg-slate-900 text-white p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-yellow-400 text-slate-950 flex items-center justify-center font-black text-sm">
                            3
                        </span>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Phase 3: Reinforcement Drill (Speed Blitz)</span>
                            <p class="text-[11px] text-slate-300">Question <span x-text="blitzIndex + 1"></span> of <span x-text="reinforcement.length"></span></p>
                        </div>
                    </div>

                    <!-- 20s Countdown Timer Pill -->
                    <div class="flex items-center gap-3">
                        <!-- Speed streak bonus alert -->
                        <div x-show="blitzTimer > 10" class="hidden sm:inline-flex items-center gap-1 text-[11px] font-black text-yellow-400 bg-yellow-400/10 px-2.5 py-1 rounded-full border border-yellow-400/30">
                            <span>🔥 1.5x XP Boost Active</span>
                        </div>

                        <!-- Radial / Digital countdown -->
                        <div 
                            class="flex items-center gap-2 px-3 py-1.5 rounded-full font-mono font-black text-sm transition-colors"
                            :class="{
                                'bg-emerald-950 text-emerald-400 border border-emerald-500': blitzTimer > 10,
                                'bg-amber-950 text-amber-400 border border-amber-500': blitzTimer <= 10 && blitzTimer > 5,
                                'bg-red-950 text-red-400 border border-red-500 animate-pulse': blitzTimer <= 5
                            }"
                        >
                            <span>⏱️</span>
                            <span x-text="blitzTimer + 's'"></span>
                        </div>
                    </div>
                </div>

                <!-- Timer Progress Bar -->
                <div class="w-full bg-slate-800 h-2">
                    <div 
                        class="h-full transition-all duration-1000 ease-linear"
                        :class="blitzTimer > 10 ? 'bg-emerald-400' : (blitzTimer > 5 ? 'bg-amber-400' : 'bg-red-500')"
                        :style="'width: ' + ((blitzTimer / 20) * 100) + '%'"
                    ></div>
                </div>

                <!-- Active Reinforcement Question Body -->
                <template x-if="currentBlitzQuestion">
                    <div class="p-5 sm:p-8">
                        
                        <!-- Question Text -->
                        <div class="mb-6">
                            <h2 class="text-lg sm:text-2xl font-black text-slate-900 leading-snug font-['Outfit']" x-text="currentBlitzQuestion.question_text"></h2>
                            <template x-if="currentBlitzQuestion.question_text_malayalam">
                                <p class="text-base sm:text-xl font-bold text-[#0052FF] mt-2 leading-relaxed font-['Noto_Sans_Malayalam']" x-text="currentBlitzQuestion.question_text_malayalam"></p>
                            </template>
                        </div>

                        <!-- 4 Options Grid -->
                        <div class="space-y-3 mb-6">
                            <template x-for="opt in getQuestionOptions(currentBlitzQuestion)" :key="opt.key">
                                <button 
                                    @click="answerBlitz(opt.key)"
                                    :disabled="blitzAnswered"
                                    class="w-full text-left p-4 rounded-2xl border-2 transition-all flex items-center justify-between gap-3 group active:scale-[0.99]"
                                    :class="{
                                        'bg-white border-slate-200 hover:border-[#0052FF] hover:bg-blue-50/30 text-slate-800': !blitzAnswered,
                                        'bg-emerald-50 border-emerald-500 text-emerald-950 font-bold shadow-md shadow-emerald-500/10': blitzAnswered && opt.key === currentBlitzQuestion.correct_option,
                                        'bg-red-50 border-red-400 text-red-950': blitzAnswered && blitzSelectedOption === opt.key && opt.key !== currentBlitzQuestion.correct_option,
                                        'opacity-50 border-slate-200 bg-slate-50': blitzAnswered && opt.key !== currentBlitzQuestion.correct_option && blitzSelectedOption !== opt.key
                                    }"
                                >
                                    <div class="flex items-center gap-3.5">
                                        <span 
                                            class="w-9 h-9 rounded-xl border flex items-center justify-center font-black text-sm shrink-0 transition"
                                            :class="{
                                                'bg-slate-100 border-slate-300 text-slate-700 group-hover:bg-[#0052FF] group-hover:text-white group-hover:border-[#0052FF]': !blitzAnswered,
                                                'bg-emerald-600 border-emerald-600 text-white': blitzAnswered && opt.key === currentBlitzQuestion.correct_option,
                                                'bg-red-600 border-red-600 text-white': blitzAnswered && blitzSelectedOption === opt.key && opt.key !== currentBlitzQuestion.correct_option,
                                                'bg-slate-100 border-slate-200 text-slate-400': blitzAnswered && opt.key !== currentBlitzQuestion.correct_option && blitzSelectedOption !== opt.key
                                            }"
                                            x-text="opt.key"
                                        ></span>
                                        <span class="text-sm sm:text-base font-semibold leading-relaxed font-['Noto_Sans_Malayalam']" x-text="opt.text"></span>
                                    </div>

                                    <div>
                                        <template x-if="blitzAnswered && opt.key === currentBlitzQuestion.correct_option">
                                            <span class="text-xl text-emerald-600">✅</span>
                                        </template>
                                        <template x-if="blitzAnswered && blitzSelectedOption === opt.key && opt.key !== currentBlitzQuestion.correct_option">
                                            <span class="text-xl text-red-500">❌</span>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <!-- Feedback Box on Answer -->
                        <template x-if="blitzAnswered">
                            <div class="p-4 rounded-2xl border mb-6" :class="blitzIsCorrect ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg" x-text="blitzIsCorrect ? '🎉' : '⚠️'"></span>
                                        <span class="text-sm font-black" :class="blitzIsCorrect ? 'text-emerald-900' : 'text-red-900'">
                                            <span x-text="blitzIsCorrect ? (blitzUnderTenSec ? 'SUPER SPEED! +30 XP (1.5x Multiplier!)' : 'CORRECT! +20 XP') : 'INCORRECT! Review the core fact below:'"></span>
                                        </span>
                                    </div>
                                </div>
                                <template x-if="currentBlitzQuestion.explanation_malayalam || currentBlitzQuestion.explanation">
                                    <p class="text-xs text-slate-700 mt-2 font-['Noto_Sans_Malayalam']" x-text="currentBlitzQuestion.explanation_malayalam || currentBlitzQuestion.explanation"></p>
                                </template>
                            </div>
                        </template>

                        <!-- Next Question or Proceed Button -->
                        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
                            <span class="text-xs text-slate-400 font-bold">
                                Blitz Score: <strong class="text-slate-800" x-text="blitzCorrectCount"></strong> / <span x-text="reinforcement.length"></span>
                            </span>

                            <template x-if="blitzAnswered">
                                <button 
                                    @click="nextBlitzQuestion()" 
                                    class="px-6 py-3 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-sm rounded-xl shadow-md transition flex items-center gap-2 active:scale-95"
                                >
                                    <span x-text="blitzIndex + 1 < reinforcement.length ? 'Next Question →' : 'Complete Blitz & Open OMR 📝'"></span>
                                </button>
                            </template>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- PHASE 4: FINAL OMR SHEET CHALLENGE (Exam-Day Simulation)      -->
        <!-- ============================================================= -->
        <div x-show="currentPhase === 'omr'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white rounded-3xl border-2 border-blue-200 shadow-2xl p-5 sm:p-8">
                
                <!-- OMR Header -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-200 mb-6">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-sm">
                            4
                        </span>
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-[#0052FF]">Phase 4: Final OMR Sheet Challenge</span>
                            <h2 class="text-lg sm:text-xl font-black text-slate-950">Official Kerala PSC Exam Simulation</h2>
                        </div>
                    </div>

                    <!-- Scoring Rule Matrix Chips -->
                    <div class="flex items-center gap-2 text-xs font-mono font-bold">
                        <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-lg">✅ +1.00</span>
                        <span class="bg-red-100 text-red-800 px-2.5 py-1 rounded-lg">⚠️ -0.33</span>
                        <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">⚪ 0.00</span>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- MOBILE VIEW (< lg): Slidable Question Booklet + Instant OMR -->
                <!-- ========================================================= -->
                <div class="block lg:hidden space-y-4">
                    
                    <!-- 1. Slidable Active Question Card (Authentic Non-Clickable PSC Booklet Format) -->
                    <template x-if="omrQuestions.length > 0 && omrQuestions[omrActiveIndex]">
                        <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-sm p-4 transition-all">
                            
                            <!-- Card Header: Question Counter & Jumper Pills -->
                            <div class="flex items-center justify-between gap-2 pb-2.5 mb-2.5 border-b border-slate-200">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-300 text-slate-800 font-mono font-bold text-xs">
                                        Question <span x-text="omrActiveIndex + 1"></span> of <span x-text="omrQuestions.length"></span>
                                    </span>
                                    <span x-show="omrAnswers[omrQuestions[omrActiveIndex].id]" class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300 flex items-center gap-1">
                                        ✓ Bubbled
                                    </span>
                                </div>

                                <!-- Question Quick Jump Carousel -->
                                <div class="flex items-center gap-1 overflow-x-auto scrollbar-none">
                                    <template x-for="(q, idx) in omrQuestions" :key="q.id">
                                        <button 
                                            type="button"
                                            @click="selectOmrQuestion(idx)"
                                            class="px-2 py-0.5 rounded-md text-[11px] font-black transition-all shrink-0 flex items-center gap-0.5"
                                            :class="omrActiveIndex === idx 
                                                ? 'bg-[#0052FF] text-white ring-2 ring-blue-300 shadow-xs' 
                                                : (omrAnswers[q.id] 
                                                    ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' 
                                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200')"
                                            :title="'Jump to Q' + (idx + 1)"
                                        >
                                            <span x-text="'Q' + (idx + 1)"></span>
                                            <span x-show="omrAnswers[q.id]" class="text-[8px]">✓</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Swipeable Question Content Area (Pure Printed Exam Paper Format - Non-Clickable) -->
                            <div 
                                @touchstart="handleTouchStart($event)" 
                                @touchend="handleTouchEnd($event)"
                                class="touch-pan-y py-1 select-text"
                            >
                                <div class="flex items-start gap-2">
                                    <span class="text-sm sm:text-base font-black text-slate-900 font-serif shrink-0 mt-0.5" x-text="(omrActiveIndex + 1) + '.'"></span>
                                    <div class="flex-1 space-y-1">
                                        <template x-if="omrQuestions[omrActiveIndex].question_text_malayalam">
                                            <p class="text-sm sm:text-base font-bold text-slate-950 leading-relaxed font-['Noto_Sans_Malayalam']" x-text="omrQuestions[omrActiveIndex].question_text_malayalam"></p>
                                        </template>
                                        <template x-if="omrQuestions[omrActiveIndex].question_text">
                                            <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug font-['Outfit']" x-text="omrQuestions[omrActiveIndex].question_text"></p>
                                        </template>
                                    </div>
                                </div>

                                <!-- Ordinary 4 Options Text (NO button borders, NO clickable look - Pure Printed Exam Style) -->
                                <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-3 pt-2.5 border-t border-dashed border-slate-200 text-xs sm:text-sm text-slate-900 font-['Noto_Sans_Malayalam']">
                                    <template x-for="opt in getQuestionOptions(omrQuestions[omrActiveIndex])" :key="opt.key">
                                        <div class="flex items-start gap-1.5 py-0.5 select-text">
                                            <span class="font-bold text-slate-900 shrink-0" x-text="'(' + opt.key + ')'"></span>
                                            <span class="leading-relaxed text-slate-900" x-text="opt.text"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Card Bottom Navigation Bar with Prominent Next Button -->
                            <div class="mt-3.5 pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                                <button 
                                    type="button" 
                                    @click="prevOmrQuestion()" 
                                    :disabled="omrActiveIndex === 0" 
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs disabled:opacity-30 disabled:cursor-not-allowed transition flex items-center gap-1 active:scale-95"
                                >
                                    <span>◀ Prev</span>
                                </button>

                                <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">👈 Swipe to slide 👉</span>

                                <button 
                                    type="button" 
                                    @click="nextOmrQuestion()" 
                                    :disabled="omrActiveIndex === omrQuestions.length - 1" 
                                    class="px-5 py-2.5 rounded-xl bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white font-black text-xs sm:text-sm shadow-md shadow-blue-500/20 disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center gap-2 border border-blue-600"
                                >
                                    <span>Next Question</span>
                                    <span class="text-base leading-none">▶</span>
                                </button>
                            </div>

                        </div>
                    </template>

                    <!-- 2. Mobile OMR Sheet (Directly Below Question Card) -->
                    <div class="bg-[#FAFBFD] border-2 border-dashed border-slate-400/90 rounded-2xl p-4 font-mono shadow-sm">
                        
                        <!-- OMR Top Banner -->
                        <div class="border-b-2 border-slate-300 pb-2 mb-3 text-center">
                            <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">KERALA PUBLIC SERVICE COMMISSION</div>
                            <div class="text-[9px] text-slate-500 uppercase">OFFICIAL OBJECTIVE OMR SHEET</div>
                            <div class="flex items-center justify-between text-[10px] text-slate-600 mt-1 font-bold">
                                <span>SERIES: <strong>A</strong></span>
                                <span>BUBBLED: <strong class="text-[#0052FF]" x-text="Object.keys(omrAnswers).length + ' / ' + omrQuestions.length"></strong></span>
                            </div>
                        </div>

                        <!-- Bubble Rows -->
                        <div class="space-y-2">
                            <template x-for="(q, idx) in omrQuestions" :key="q.id">
                                <div 
                                    @click="selectOmrQuestion(idx)"
                                    class="flex items-center justify-between p-2 rounded-xl transition cursor-pointer"
                                    :class="omrActiveIndex === idx 
                                        ? 'bg-blue-50/90 border-2 border-blue-500 ring-2 ring-blue-500/20 shadow-xs' 
                                        : 'bg-white border border-slate-200/90'"
                                >
                                    <!-- Question Number & Active Indicator -->
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-black text-slate-800 w-5" x-text="(idx + 1) < 10 ? '0' + (idx + 1) : (idx + 1)"></span>
                                        <span x-show="omrActiveIndex === idx" class="w-1.5 h-1.5 rounded-full bg-[#0052FF] animate-ping"></span>
                                    </div>
                                    
                                    <!-- Bubble Options A B C D (The ONLY clickable answer targets) -->
                                    <div class="flex items-center gap-2">
                                        <template x-for="opt in ['A', 'B', 'C', 'D']" :key="opt">
                                            <button 
                                                type="button"
                                                @click.stop="fillOmrBubble(q.id, opt); selectOmrQuestion(idx)"
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 flex items-center justify-center text-[11px] font-bold transition-all duration-150 active:scale-90"
                                                :class="omrAnswers[q.id] === opt 
                                                    ? 'bg-slate-900 border-slate-950 text-white shadow-inner scale-105 ring-1 ring-slate-950' 
                                                    : 'bg-white border-slate-400 text-slate-700 hover:border-slate-800'"
                                            >
                                                <span x-text="opt"></span>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Clear / Erase Button -->
                                    <button 
                                        type="button"
                                        @click.stop="clearOmrBubble(q.id)" 
                                        x-show="omrAnswers[q.id]"
                                        class="text-slate-400 hover:text-red-500 text-xs transition p-1"
                                        title="Erase bubble"
                                    >
                                        ✕
                                    </button>
                                    <span x-show="!omrAnswers[q.id]" class="w-4"></span>
                                </div>
                            </template>
                        </div>

                        <!-- OMR Micro Instructions -->
                        <div class="mt-3 pt-2.5 border-t border-slate-200 text-[9px] text-slate-500 leading-tight space-y-1">
                            <p>⚠️ Darken circles on this OMR sheet with black pen ink simulation.</p>
                            <p>⚠️ Kerala PSC penalty: <strong>-0.33 marks</strong> for wrong bubbles.</p>
                        </div>

                        <!-- Submit OMR Button -->
                        <div class="mt-4">
                            <button 
                                type="button"
                                @click="submitOmrSheet()"
                                :disabled="isSubmittingOmr"
                                class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 border-2 border-yellow-400"
                            >
                                <span x-show="!isSubmittingOmr">SUBMIT OMR SHEET 📝</span>
                                <span x-show="isSubmittingOmr" class="flex items-center gap-2">
                                    <span class="w-4 h-4 border-2 border-white border-t-yellow-400 rounded-full animate-spin"></span>
                                    <span>Calculating Rank Score...</span>
                                </span>
                            </button>
                        </div>

                    </div>

                </div>

                <!-- ========================================================= -->
                <!-- DESKTOP VIEW (>= lg): Authentic Question Booklet + OMR    -->
                <!-- ========================================================= -->
                <div class="hidden lg:grid lg:grid-cols-12 gap-6">
                    
                    <!-- Left: Authentic Kerala PSC Question Booklet (Non-clickable) (7 cols) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="bg-slate-100 p-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 flex items-center justify-between font-mono">
                            <span class="tracking-wide">📖 KERALA PUBLIC SERVICE COMMISSION — QUESTION BOOKLET</span>
                            <span class="text-[10px] uppercase bg-white px-2 py-0.5 rounded border border-slate-300 text-slate-700 font-sans">Series A</span>
                        </div>

                        <div class="space-y-4 max-h-[620px] overflow-y-auto pr-2">
                            <template x-for="(q, idx) in omrQuestions" :key="q.id">
                                <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-xs">
                                    <!-- Question Number + Text -->
                                    <div class="flex items-start gap-2.5">
                                        <span class="font-bold text-slate-900 text-sm shrink-0 font-serif" x-text="(idx + 1) + '.'"></span>
                                        <div class="space-y-1 flex-1">
                                            <template x-if="q.question_text_malayalam">
                                                <p class="text-sm font-bold text-slate-950 leading-relaxed font-['Noto_Sans_Malayalam']" x-text="q.question_text_malayalam"></p>
                                            </template>
                                            <template x-if="q.question_text">
                                                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug font-['Outfit']" x-text="q.question_text"></p>
                                            </template>
                                            
                                            <!-- Authentic 2-column ordinary question options (NON-CLICKABLE) -->
                                            <div class="grid grid-cols-2 gap-x-6 gap-y-2 mt-2.5 pt-2 border-t border-dashed border-slate-200 text-xs text-slate-900 font-['Noto_Sans_Malayalam']">
                                                <template x-for="opt in getQuestionOptions(q)" :key="opt.key">
                                                    <div class="flex items-start gap-1.5 py-0.5 select-text">
                                                        <span class="font-bold text-slate-900 shrink-0" x-text="'(' + opt.key + ')'"></span>
                                                        <span class="leading-relaxed text-slate-900" x-text="opt.text"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Right: Authentic Kerala PSC OMR Bubble Grid (5 cols on lg) -->
                    <div class="lg:col-span-5">
                        <div class="sticky top-24 bg-[#FAFBFD] border-2 border-dashed border-slate-400/80 rounded-2xl p-4 sm:p-5 font-mono shadow-sm">
                            
                            <!-- OMR Top Banner -->
                            <div class="border-b-2 border-slate-300 pb-2 mb-4 text-center">
                                <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">KERALA PUBLIC SERVICE COMMISSION</div>
                                <div class="text-[9px] text-slate-500 uppercase">OFFICIAL OBJECTIVE OMR SHEET</div>
                                <div class="flex items-center justify-between text-[10px] text-slate-600 mt-1 font-bold">
                                    <span>SERIES: <strong>A</strong></span>
                                    <span>BUBBLED: <strong class="text-[#0052FF]" x-text="Object.keys(omrAnswers).length + ' / ' + omrQuestions.length"></strong></span>
                                </div>
                            </div>

                            <!-- Bubble Rows -->
                            <div class="space-y-2.5 max-h-[440px] overflow-y-auto pr-1">
                                <template x-for="(q, idx) in omrQuestions" :key="q.id">
                                    <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200/90 shadow-xs hover:border-blue-400 transition">
                                        <!-- Question Number -->
                                        <span class="text-xs font-black text-slate-800 w-6" x-text="(idx + 1) < 10 ? '0' + (idx + 1) : (idx + 1)"></span>
                                        
                                        <!-- Bubble Options A B C D (Touch targets) -->
                                        <div class="flex items-center gap-2 sm:gap-2.5">
                                            <template x-for="opt in ['A', 'B', 'C', 'D']" :key="opt">
                                                <button 
                                                    @click="fillOmrBubble(q.id, opt)"
                                                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full border-2 flex items-center justify-center text-[11px] font-bold transition-all duration-150 active:scale-90"
                                                    :class="omrAnswers[q.id] === opt ? 'bg-slate-900 border-slate-950 text-white shadow-inner scale-105 ring-1 ring-slate-950' : 'bg-white border-slate-400 text-slate-700 hover:border-slate-800 hover:bg-slate-50'"
                                                    :title="'Question ' + (idx + 1) + ' Option ' + opt"
                                                >
                                                    <span x-text="opt"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Clear / Erase Button -->
                                        <button 
                                            @click="clearOmrBubble(q.id)" 
                                            x-show="omrAnswers[q.id]"
                                            class="text-slate-300 hover:text-red-500 text-xs transition p-1"
                                            title="Erase bubble"
                                        >
                                            ✕
                                        </button>
                                        <span x-show="!omrAnswers[q.id]" class="w-4"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- OMR Micro Instructions -->
                            <div class="mt-4 pt-3 border-t border-slate-200 text-[9px] text-slate-500 leading-tight space-y-1">
                                <p>⚠️ Darken circles on this OMR sheet with black pen ink simulation.</p>
                                <p>⚠️ Kerala PSC penalty: <strong>-0.33 marks</strong> for wrong bubbles.</p>
                            </div>

                            <!-- Submit OMR Button -->
                            <div class="mt-5">
                                <button 
                                    @click="submitOmrSheet()"
                                    :disabled="isSubmittingOmr"
                                    class="w-full py-3.5 px-4 bg-slate-900 hover:bg-slate-800 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 border-2 border-yellow-400"
                                >
                                    <span x-show="!isSubmittingOmr">SUBMIT OMR SHEET 📝</span>
                                    <span x-show="isSubmittingOmr" class="flex items-center gap-2">
                                        <span class="w-4 h-4 border-2 border-white border-t-yellow-400 rounded-full animate-spin"></span>
                                        <span>Calculating Rank Score...</span>
                                    </span>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- ============================================================= -->
        <!-- PHASE 5: SESSION SUMMARY & LOCAL RANK BADGE                   -->
        <!-- ============================================================= -->
        <div x-show="currentPhase === 'summary'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-98" x-transition:enter-end="opacity-100 scale-100">
            <div class="bg-white rounded-3xl border-2 border-blue-200 shadow-2xl p-6 sm:p-10 relative overflow-hidden">
                
                <!-- Celebratory Fanfare Banner -->
                <div class="text-center max-w-xl mx-auto mb-8">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-yellow-400 to-amber-500 text-slate-950 flex items-center justify-center text-4xl mx-auto mb-4 shadow-lg shadow-yellow-400/30 animate-bounce">
                        🏆
                    </div>
                    <span class="px-3 py-1 bg-blue-50 text-[#0052FF] text-xs font-black uppercase tracking-wider rounded-full border border-blue-200">
                        Session Complete
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-2">
                        അഭിനന്ദനങ്ങൾ! Session Mastered!
                    </h2>
                    <p class="text-sm sm:text-base font-bold text-[#0052FF] mt-1 font-['Noto_Sans_Malayalam']">
                        നവോത്ഥാന ചരിത്രത്തിലെ നിർണ്ണായക ചോദ്യങ്ങൾ നിങ്ങൾ പൂർത്തിയാക്കി!
                    </p>
                </div>

                <!-- Rank Badge Card -->
                <template x-if="omrSummary">
                    <div class="max-w-md mx-auto mb-8 p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-blue-950 text-white text-center shadow-xl border-2 border-yellow-400 relative">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 block mb-1">Earned Session Rank Badge</span>
                        <div class="text-2xl sm:text-3xl font-black text-yellow-300" x-text="omrSummary.rank_badge"></div>
                        
                        <div class="grid grid-cols-3 gap-3 mt-5 pt-4 border-t border-slate-800 text-center">
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Net Marks</div>
                                <div class="text-lg font-black font-mono text-emerald-400" x-text="omrSummary.net_marks + ' / ' + omrSummary.max_marks"></div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Accuracy</div>
                                <div class="text-lg font-black font-mono text-yellow-400" x-text="omrSummary.accuracy + '%'"></div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Total XP</div>
                                <div class="text-lg font-black font-mono text-amber-300" x-text="'+' + totalXpEarned"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Detailed 4-Phase Score Breakdown Matrix -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-8">
                    <!-- Phase 1 Breakdown -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-black uppercase text-slate-500 block">1. Diagnostic</span>
                        <div class="text-lg font-black mt-1" :class="diagnosticState.isCorrect ? 'text-emerald-600' : 'text-amber-600'">
                            <span x-text="diagnosticState.isCorrect ? '+50 XP' : 'Trap Avoided'"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-bold" x-text="diagnosticState.isCorrect ? 'First Strike Ace' : 'Learned Trap'"></span>
                    </div>

                    <!-- Phase 2 Breakdown -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-black uppercase text-slate-500 block">2. Micro-Lesson</span>
                        <div class="text-lg font-black text-blue-600 mt-1">Completed</div>
                        <span class="text-[10px] text-slate-500 font-bold" x-text="contents.length + ' Media Blocks'"></span>
                    </div>

                    <!-- Phase 3 Breakdown -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-black uppercase text-slate-500 block">3. Speed Blitz</span>
                        <div class="text-lg font-black text-emerald-600 mt-1" x-text="blitzCorrectCount + ' / ' + reinforcement.length"></div>
                        <span class="text-[10px] text-slate-500 font-bold">Speed Multipliers</span>
                    </div>

                    <!-- Phase 4 Breakdown -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-black uppercase text-slate-500 block">4. Final OMR</span>
                        <template x-if="omrSummary">
                            <div class="text-lg font-black text-[#0052FF] mt-1" x-text="omrSummary.net_marks + ' pts'"></div>
                        </template>
                        <span class="text-[10px] text-slate-500 font-bold">Strict PSC Marking</span>
                    </div>
                </div>

                <!-- OMR Question Detailed Review Accordion / List -->
                <template x-if="omrDetails && omrDetails.length > 0">
                    <div class="border-t border-slate-200 pt-6 mb-8">
                        <h3 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                            <span>📝 OMR Answer Key & Trap Breakdown</span>
                        </h3>

                        <div class="space-y-3">
                            <template x-for="(item, idx) in omrDetails" :key="item.id">
                                <div 
                                    class="p-4 rounded-xl border transition-all"
                                    :class="item.is_correct ? 'bg-emerald-50/70 border-emerald-300' : (item.is_attempted ? 'bg-red-50/70 border-red-300' : 'bg-slate-50 border-slate-300')"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-2">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs shrink-0 mt-0.5" :class="item.is_correct ? 'bg-emerald-600 text-white' : (item.is_attempted ? 'bg-red-600 text-white' : 'bg-slate-400 text-white')" x-text="idx + 1"></span>
                                            <div>
                                                <p class="text-xs sm:text-sm font-bold text-slate-900 font-['Outfit']" x-text="item.question_text"></p>
                                                <template x-if="item.question_text_malayalam">
                                                    <p class="text-xs sm:text-sm font-semibold text-[#0052FF] mt-0.5 font-['Noto_Sans_Malayalam']" x-text="item.question_text_malayalam"></p>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Marks delta badge -->
                                        <div class="text-right shrink-0">
                                            <span 
                                                class="px-2 py-0.5 rounded text-[11px] font-black"
                                                :class="item.is_correct ? 'bg-emerald-200 text-emerald-950' : (item.is_attempted ? 'bg-red-200 text-red-950' : 'bg-slate-200 text-slate-700')"
                                                x-text="item.is_correct ? '+1.00' : (item.is_attempted ? '-0.33' : '0.00')"
                                            ></span>
                                        </div>
                                    </div>

                                    <div class="mt-2 text-xs flex flex-wrap items-center gap-4 text-slate-600">
                                        <span>Your Bubble: <strong :class="item.is_correct ? 'text-emerald-700' : 'text-red-700'" x-text="item.user_answer || 'Unattempted'"></strong></span>
                                        <span>Correct Answer: <strong class="text-emerald-800" x-text="item.correct_answer"></strong></span>
                                    </div>

                                    <!-- Trap warning if wrong -->
                                    <template x-if="!item.is_correct && item.trap_warning">
                                        <div class="mt-2 p-2 bg-amber-100/80 rounded-lg text-[11px] font-bold text-amber-950 font-['Noto_Sans_Malayalam']">
                                            ⚠️ <strong>PSC Trap:</strong> <span x-text="item.trap_warning"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Sequential Unit Navigation CTAs: Previous Unit, Retake, and Next Unit -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-slate-200">
                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        @if($previousSession)
                            <a 
                                href="{{ route('session.show', ['slug' => $previousSession->slug, 'stream' => $stream]) }}" 
                                class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition flex items-center justify-center gap-1.5 border border-slate-300"
                            >
                                <span>← Prev Unit ({{ Str::limit($previousSession->title, 18) }})</span>
                            </a>
                        @endif

                        <button 
                            @click="restartSession()" 
                            class="px-5 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs sm:text-sm rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 border border-blue-400 active:scale-95 cursor-pointer"
                            title="Reset all questions and restart this unit from Phase 1"
                        >
                            <span class="text-base">🔄</span>
                            <span>RETAKE SESSION (വീണ്ടും ചെയ്യുക)</span>
                        </button>

                        <a 
                            href="{{ route('sessions.index', ['stream' => $stream]) }}" 
                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition text-center"
                        >
                            All Units
                        </a>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        @if($nextSession)
                            <a 
                                href="{{ route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) }}" 
                                class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-[#0052FF] via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-black text-sm sm:text-base rounded-2xl shadow-xl shadow-blue-500/30 transition text-center flex items-center justify-center gap-2 border-2 border-yellow-400 group active:scale-95 animate-pulse"
                            >
                                <span>CONTINUE TO NEXT UNIT ➔</span>
                                <span class="text-yellow-300 group-hover:translate-x-1.5 transition-transform font-bold text-xs">
                                    ({{ Str::limit($nextSession->title, 22) }})
                                </span>
                            </a>
                        @else
                            <a 
                                href="{{ route('sessions.index') }}" 
                                class="w-full sm:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-xl shadow-lg transition text-center flex items-center justify-center gap-2"
                            >
                                <span>🎉 CURRICULUM MASTERED! VIEW ALL UNITS</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        @endif

    </div>
@endif
</div>

@push('scripts')
<script>
function sessionEngine(config) {
    return {
        sessionId: config.sessionId,
        sessionTitle: config.sessionTitle,
        sessionTitleMl: config.sessionTitleMl,
        xpReward: config.xpReward,
        categoryName: config.categoryName,
        diagnostic: config.diagnostic,
        contents: config.contents || [],
        reinforcement: config.reinforcement || [],
        omrQuestions: config.omrQuestions || [],
        progressSaveUrl: config.progressSaveUrl,
        omrSubmitUrl: config.omrSubmitUrl,
        csrfToken: config.csrfToken,

        // State Machine
        currentPhase: 'diagnostic', // 'diagnostic' -> 'lesson' -> 'reinforcement' -> 'omr' -> 'summary'
        phaseUnlocked: {
            diagnostic: true,
            lesson: false,
            reinforcement: false,
            omr: false,
        },
        sessionCompleted: false,

        // Overall Session Metrics
        totalSessionSeconds: 0,
        sessionTimerInterval: null,
        totalXpEarned: 0,

        // Phase 1: Diagnostic State
        diagnosticState: {
            answered: false,
            selectedOption: null,
            isCorrect: false,
        },

        // Phase 2: Audio Player State
        activeAudioId: null,

        // Phase 3: Speed Blitz State
        blitzIndex: 0,
        blitzTimer: 20,
        blitzTimerInterval: null,
        blitzAnswered: false,
        blitzSelectedOption: null,
        blitzIsCorrect: false,
        blitzUnderTenSec: false,
        blitzCorrectCount: 0,

        // Phase 4: OMR Grid State
        omrAnswers: {},
        omrActiveIndex: 0,
        mobileOmrView: 'slide', // 'slide' (compact slidable booklet) or 'all' (full list)
        touchStartX: 0,
        isSubmittingOmr: false,
        omrSummary: null,
        omrDetails: [],

        initEngine() {
            // Start total session timer
            this.sessionTimerInterval = setInterval(() => {
                this.totalSessionSeconds++;
            }, 1000);

            // If no diagnostic question exists, directly unlock lesson
            if (!this.diagnostic) {
                this.phaseUnlocked.lesson = true;
                this.currentPhase = 'lesson';
            }
        },

        canJumpTo(phase) {
            if (phase === 'diagnostic') return true;
            return !!this.phaseUnlocked[phase];
        },

        setPhase(phase) {
            if (this.canJumpTo(phase)) {
                this.currentPhase = phase;
                if (phase === 'reinforcement' && !this.blitzTimerInterval && this.reinforcement.length > 0) {
                    this.startBlitzTimer();
                }
            }
        },

        // -------------------------------------------------------------
        // Phase 1: Diagnostic Logic
        // -------------------------------------------------------------
        answerDiagnostic(selectedOption) {
            if (this.diagnosticState.answered || !this.diagnostic) return;

            this.diagnosticState.answered = true;
            this.diagnosticState.selectedOption = selectedOption;
            const correctOpt = (this.diagnostic.correct_option || '').toUpperCase().trim();
            this.diagnosticState.isCorrect = (selectedOption.toUpperCase().trim() === correctOpt);

            if (this.diagnosticState.isCorrect) {
                this.totalXpEarned += 50;
                if (window.confetti) {
                    window.confetti({
                        particleCount: 80,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                }
                if (window.PscSound) {
                    window.PscSound.playCorrect();
                }
            } else {
                if (window.PscSound) {
                    window.PscSound.playWrong();
                }
            }

            this.phaseUnlocked.lesson = true;
            this.persistProgress('diagnostic', {
                diagnostic_status: this.diagnosticState.isCorrect ? 'correct' : 'incorrect',
                xp_earned: this.totalXpEarned,
            });
        },

        proceedToLesson() {
            this.phaseUnlocked.lesson = true;
            this.currentPhase = 'lesson';
            this.persistProgress('lesson');
            window.scrollTo({ top: 100, behavior: 'smooth' });
        },

        // -------------------------------------------------------------
        // Phase 2: Multimedia Lesson Logic
        // -------------------------------------------------------------
        toggleAudio(id, url) {
            const player = document.getElementById('audio-player-' + id);
            if (!player) return;

            if (this.activeAudioId === id && !player.paused) {
                player.pause();
                this.activeAudioId = null;
            } else {
                // Pause any other active audio
                document.querySelectorAll('audio').forEach(a => a.pause());
                player.play().catch(e => console.log('Audio playback error:', e));
                this.activeAudioId = id;
            }
        },

        isPlayingAudio(id) {
            return this.activeAudioId === id;
        },

        isVideoEmbed(url) {
            if (!url) return false;
            return url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com');
        },

        getVideoEmbedUrl(url) {
            if (!url) return '';
            if (url.includes('youtube.com/watch?v=')) {
                return url.replace('watch?v=', 'embed/');
            }
            if (url.includes('youtu.be/')) {
                return url.replace('youtu.be/', 'youtube.com/embed/');
            }
            return url;
        },

        completeLessonAndProceed() {
            // Stop any playing audio
            document.querySelectorAll('audio').forEach(a => a.pause());
            this.activeAudioId = null;

            this.phaseUnlocked.reinforcement = true;
            this.currentPhase = 'reinforcement';
            this.startBlitzTimer();
            this.persistProgress('reinforcement');
            window.scrollTo({ top: 100, behavior: 'smooth' });
        },

        // -------------------------------------------------------------
        // Phase 3: Speed Blitz Logic
        // -------------------------------------------------------------
        get currentBlitzQuestion() {
            return this.reinforcement[this.blitzIndex] || null;
        },

        startBlitzTimer() {
            clearInterval(this.blitzTimerInterval);
            this.blitzTimer = 20;
            this.blitzAnswered = false;
            this.blitzSelectedOption = null;

            this.blitzTimerInterval = setInterval(() => {
                if (this.blitzTimer > 0) {
                    this.blitzTimer--;
                    if (this.blitzTimer <= 5 && window.PscSound) {
                        window.PscSound.playTick();
                    }
                } else {
                    // Time's up for current question
                    clearInterval(this.blitzTimerInterval);
                    this.answerBlitz(null);
                }
            }, 1000);
        },

        answerBlitz(option) {
            if (this.blitzAnswered) return;

            clearInterval(this.blitzTimerInterval);
            this.blitzAnswered = true;
            this.blitzSelectedOption = option;

            const q = this.currentBlitzQuestion;
            if (!q) return;

            const correctOpt = (q.correct_option || '').toUpperCase().trim();
            this.blitzIsCorrect = option && (option.toUpperCase().trim() === correctOpt);
            this.blitzUnderTenSec = (20 - this.blitzTimer) <= 10;

            if (this.blitzIsCorrect) {
                this.blitzCorrectCount++;
                const xpGain = this.blitzUnderTenSec ? 30 : 20; // 1.5x streak multiplier
                this.totalXpEarned += xpGain;

                if (window.PscSound) window.PscSound.playCorrect();
                if (window.confetti && this.blitzUnderTenSec) {
                    window.confetti({ particleCount: 35, spread: 50, origin: { y: 0.7 } });
                }
            } else {
                if (window.PscSound) window.PscSound.playWrong();
            }
        },

        nextBlitzQuestion() {
            if (this.blitzIndex + 1 < this.reinforcement.length) {
                this.blitzIndex++;
                this.startBlitzTimer();
            } else {
                // Blitz finished! Unlock OMR Challenge
                this.phaseUnlocked.omr = true;
                this.currentPhase = 'omr';
                this.persistProgress('omr', {
                    reinforcement_score: this.blitzCorrectCount,
                    xp_earned: this.totalXpEarned,
                });
                window.scrollTo({ top: 100, behavior: 'smooth' });
            }
        },

        // -------------------------------------------------------------
        // Phase 4: OMR Sheet Simulation Logic
        // -------------------------------------------------------------
        selectOmrQuestion(index) {
            if (index >= 0 && index < this.omrQuestions.length) {
                this.omrActiveIndex = index;
            }
        },

        nextOmrQuestion() {
            if (this.omrActiveIndex + 1 < this.omrQuestions.length) {
                this.omrActiveIndex++;
            }
        },

        prevOmrQuestion() {
            if (this.omrActiveIndex > 0) {
                this.omrActiveIndex--;
            }
        },

        handleTouchStart(e) {
            if (e.changedTouches && e.changedTouches[0]) {
                this.touchStartX = e.changedTouches[0].screenX;
            }
        },

        handleTouchEnd(e) {
            if (!this.touchStartX || !e.changedTouches || !e.changedTouches[0]) return;
            const diff = e.changedTouches[0].screenX - this.touchStartX;
            if (diff > 45) this.prevOmrQuestion();
            if (diff < -45) this.nextOmrQuestion();
            this.touchStartX = 0;
        },

        fillOmrBubble(questionId, option) {
            this.omrAnswers[questionId] = option;
            if (window.PscSound) window.PscSound.playTick();

            // Sync active index to this question
            const qIdx = this.omrQuestions.findIndex(q => q.id === questionId);
            if (qIdx !== -1) {
                this.omrActiveIndex = qIdx;
            }
        },

        clearOmrBubble(questionId) {
            delete this.omrAnswers[questionId];
        },

        async submitOmrSheet() {
            this.isSubmittingOmr = true;

            try {
                const response = await fetch(this.omrSubmitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        answers: this.omrAnswers,
                        time_taken_seconds: this.totalSessionSeconds,
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.omrSummary = data.summary;
                    this.omrDetails = data.questions;
                    this.sessionCompleted = true;
                    const headerNextBtn = document.getElementById('headerNextUnitBtn');
                    if (headerNextBtn) headerNextBtn.style.display = 'inline-flex';
                    const headerRetakeBtn = document.getElementById('headerRetakeBtn');
                    if (headerRetakeBtn) headerRetakeBtn.style.display = 'inline-flex';
                    
                    // Add net marks points into total XP
                    const omrXp = Math.max(0, Math.round(data.summary.net_marks * 20));
                    this.totalXpEarned += omrXp;

                    this.currentPhase = 'summary';

                    if (window.PscSound) window.PscSound.playFanfare();
                    if (window.confetti) {
                        window.confetti({
                            particleCount: 150,
                            spread: 100,
                            origin: { y: 0.5 }
                        });
                    }

                    this.persistProgress('summary', {
                        omr_score: data.summary.net_marks,
                        net_marks: data.summary.net_marks,
                        xp_earned: this.totalXpEarned,
                        is_completed: true,
                    });

                    window.scrollTo({ top: 100, behavior: 'smooth' });
                }
            } catch (err) {
                console.error('OMR Submit Error:', err);
                alert('Error submitting OMR sheet. Please try again.');
            } finally {
                this.isSubmittingOmr = false;
            }
        },

        restartSession() {
            this.currentPhase = this.diagnostic ? 'diagnostic' : 'lesson';
            this.phaseUnlocked = {
                diagnostic: true,
                lesson: !this.diagnostic,
                reinforcement: false,
                omr: false,
            };
            this.sessionCompleted = false;
            const headerNextBtn = document.getElementById('headerNextUnitBtn');
            if (headerNextBtn) headerNextBtn.style.display = 'none';
            const headerRetakeBtn = document.getElementById('headerRetakeBtn');
            if (headerRetakeBtn) headerRetakeBtn.style.display = 'none';
            this.diagnosticState = { answered: false, selectedOption: null, isCorrect: false };
            this.blitzIndex = 0;
            this.blitzTimer = 20;
            if (this.blitzTimerInterval) {
                clearInterval(this.blitzTimerInterval);
                this.blitzTimerInterval = null;
            }
            this.blitzAnswered = false;
            this.blitzSelectedOption = null;
            this.blitzIsCorrect = false;
            this.blitzUnderTenSec = false;
            this.blitzCorrectCount = 0;
            this.omrAnswers = {};
            this.omrActiveIndex = 0;
            this.omrSummary = null;
            this.omrDetails = [];
            this.totalXpEarned = 0;
            this.totalSessionSeconds = 0;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (window.PscSound && window.PscSound.playTick) {
                window.PscSound.playTick();
            }
        },

        // Helper to normalize options from question model
        getQuestionOptions(q) {
            if (!q) return [];
            if (q.resolved_options && q.resolved_options.length > 0) {
                return q.resolved_options;
            }
            if (q.options && Array.isArray(q.options)) {
                return q.options;
            }
            const opts = [];
            if (q.option_a) opts.push({ key: 'A', text: q.option_a });
            if (q.option_b) opts.push({ key: 'B', text: q.option_b });
            if (q.option_c) opts.push({ key: 'C', text: q.option_c });
            if (q.option_d) opts.push({ key: 'D', text: q.option_d });
            return opts;
        },

        formatTime(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        },

        // Send progress state to server API asynchronously
        persistProgress(phase, extra = {}) {
            fetch(this.progressSaveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_phase: phase,
                    time_taken_seconds: this.totalSessionSeconds,
                    ...extra
                })
            }).catch(e => console.log('Progress persist error:', e));
        }
    }
}

// Global Bridge for Custom Code Sessions & Universal Session Reset
window.PSCRanker = {
    sessionId: {{ $session->id }},
    xpReward: {{ $session->xp_reward }},
    progressUrl: @js(route('api.session.progress', $session->id)),
    csrfToken: '{{ csrf_token() }}',
    nextSessionUrl: @js($nextSession ? route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) : route('sessions.index')),
    isCompleted: @js((bool) ($isCompleted ?? false)),

    completeSession: async function(customXp) {
        this.isCompleted = true;
        const btn = document.getElementById('pscranker-complete-unit-btn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span>Saving Progress... ⚡</span>';
        }
        const xp = customXp || this.xpReward || 250;
        try {
            await fetch(this.progressUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_phase: 'summary',
                    is_completed: true,
                    xp_earned: xp,
                    time_taken_seconds: 90
                })
            });
        } catch (e) {
            console.error('Progress save error:', e);
        }

        // Reveal completion bar, Next Unit button and Retake button at the end of the session
        const completionBar = document.getElementById('pscranker-bottom-completion-bar');
        if (completionBar) {
            completionBar.style.display = 'flex';
        }
        const retakeBtn = document.getElementById('pscranker-retake-unit-btn');
        if (retakeBtn) {
            retakeBtn.style.display = 'inline-flex';
        }
        const nextUnitBtn = document.getElementById('pscranker-next-unit-btn');
        if (nextUnitBtn) {
            nextUnitBtn.style.display = 'inline-flex';
        }
        const statusText = document.getElementById('pscranker-completion-status-text');
        if (statusText) {
            statusText.innerText = 'Session Completed! 🎉';
        }
        const headerNextBtn = document.getElementById('headerNextUnitBtn');
        if (headerNextBtn) {
            headerNextBtn.style.display = 'inline-flex';
        }
        const headerRetakeBtn = document.getElementById('headerRetakeBtn');
        if (headerRetakeBtn) {
            headerRetakeBtn.style.display = 'inline-flex';
        }

        if (window.showPscModal) {
            window.showPscModal({
                type: 'celebration',
                icon: '🏆',
                badge: '🎉 Capsule Completed!',
                title: 'Congratulations, PSC Ranker!',
                titleMalayalam: 'കലക്കി! ഈ ക്യാപ്സ്യൂൾ നിങ്ങൾ വിജയകരമായി പൂർത്തിയാക്കി! 🚀',
                message: 'You have completed this Kerala PSC Capsule and earned +' + xp + ' XP! Your rank points have been saved.',
                xp: xp,
                nextUrl: this.nextSessionUrl,
                confirmText: this.nextSessionUrl ? 'അടുത്ത പാഠത്തിലേക്ക് പോകാം (Next Unit) ➔' : 'Awesome, Continue ⚡',
                cancelText: 'ഇവിടെ തുടരുക (Stay Here)',
                showCancel: !!this.nextSessionUrl,
                showRetake: true,
                retakeText: '🔄 ഈ യൂണിറ്റ് വീണ്ടും ചെയ്യുക (Retake Unit)',
                onRetake: () => {
                    this.retakeSession();
                }
            });
        } else {
            if (window.confetti) {
                window.confetti({ particleCount: 140, spread: 90, origin: { y: 0.55 } });
            }
            if (window.PscSound && window.PscSound.playFanfare) {
                window.PscSound.playFanfare();
            }
            alert('🎉 Congratulations! You have completed this Kerala PSC Capsule and earned +' + xp + ' XP!');
            if (this.nextSessionUrl) {
                window.location.href = this.nextSessionUrl;
            }
        }
    },

    retakeSession: function() {
        this.isCompleted = false;

        // Hide completion bar, Retake, and Next Unit button when restarting session
        const completionBar = document.getElementById('pscranker-bottom-completion-bar');
        if (completionBar) {
            completionBar.style.display = 'none';
        }
        const retakeBtn = document.getElementById('pscranker-retake-unit-btn');
        if (retakeBtn) {
            retakeBtn.style.display = 'none';
        }
        const headerNextBtn = document.getElementById('headerNextUnitBtn');
        if (headerNextBtn) {
            headerNextBtn.style.display = 'none';
        }
        const headerRetakeBtn = document.getElementById('headerRetakeBtn');
        if (headerRetakeBtn) {
            headerRetakeBtn.style.display = 'none';
        }

        // 1. Reset Custom Code internal JavaScript state
        if (window.pscState) {
            window.pscState.xp = 0;
            window.pscState.hookSolved = false;
            window.pscState.hookAnswered = false;
            if (window.pscState.mcqs) {
                Object.keys(window.pscState.mcqs).forEach(k => {
                    window.pscState.mcqs[k] = false;
                });
            }
            if (window.pscState.omr) {
                Object.keys(window.pscState.omr).forEach(k => {
                    window.pscState.omr[k] = null;
                });
            }
        }

        // 2. Reset Custom Code XP Counter & Progress Bar
        const xpVal = document.getElementById('psc-xp-val');
        if (xpVal) xpVal.innerText = '0';
        const pBar = document.getElementById('psc-progress-bar');
        if (pBar) pBar.style.width = '25%';

        // 3. Clean all options & buttons across custom capsule DOM
        const customWrapper = document.querySelector('.custom-session-wrapper') || document;
        const allOptionButtons = customWrapper.querySelectorAll(
            '.psc-opt-btn, .psc-bubble, .psc-omr-choice-row, [data-opt], button[onclick*="pscSelectHook"], button[onclick*="pscCheckMcq"]'
        );
        allOptionButtons.forEach(btn => {
            btn.classList.remove('correct', 'wrong', 'darkened', 'selected', 'disabled', 'active');
            btn.style.pointerEvents = 'auto';
            btn.style.backgroundColor = '';
            btn.style.borderColor = '';
            btn.style.color = '';
            if ('disabled' in btn) btn.disabled = false;
        });

        // 4. Hide all feedback containers & clear content
        const feedbackEls = customWrapper.querySelectorAll(
            '.psc-feedback, .psc-omr-result-box, [id$="-feedback"], [id^="psc-drill-fb-"], #psc-omr-result'
        );
        feedbackEls.forEach(fb => {
            fb.style.display = 'none';
            fb.classList.remove('psc-fb-correct', 'psc-fb-trap');
            fb.style.background = '';
            fb.style.border = '';
            fb.style.color = '';
            const content = fb.querySelector('#psc-hook-feedback-content');
            if (content) content.innerHTML = '';
        });

        // 5. Hide all intra-capsule Next/Continue buttons
        const nextButtons = customWrapper.querySelectorAll(
            '[id^="psc-next-mcq-btn-"], #psc-to-omr-btn, .psc-btn-next'
        );
        nextButtons.forEach(btn => {
            btn.style.display = 'none';
            btn.classList.remove('psc-pulse');
        });

        // 6. Reset sequential screens back to Screen 1 (Hook Question)
        const screenIds = ['psc-screen-hook', 'psc-screen-lesson', 'psc-screen-mcqs', 'psc-screen-omr'];
        screenIds.forEach((id, idx) => {
            const el = document.getElementById(id);
            if (el) {
                el.style.display = (idx === 0 ? 'block' : 'none');
            }
        });

        // Also reset any elements using .psc-screen
        const screens = customWrapper.querySelectorAll('.psc-screen');
        if (screens.length > 0) {
            screens.forEach((sc, i) => {
                sc.style.display = (i === 0 ? 'block' : 'none');
            });
        }

        // Reset single-screen MCQ cards to Card 1
        const mcq1 = document.getElementById('psc-mcq-card-1');
        const mcq2 = document.getElementById('psc-mcq-card-2');
        if (mcq1) mcq1.style.display = 'block';
        if (mcq2) mcq2.style.display = 'none';

        // 7. Reset Stepper Pills: Pill 1 active, others inactive and uncompleted
        const pillIds = ['psc-pill-hook', 'psc-pill-lesson', 'psc-pill-mcqs', 'psc-pill-omr'];
        pillIds.forEach((id, idx) => {
            const pill = document.getElementById(id);
            if (pill) {
                pill.classList.remove('completed');
                if (idx === 0) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            }
        });

        const allStepPills = customWrapper.querySelectorAll('.psc-step-pill');
        if (allStepPills.length > 0) {
            allStepPills.forEach((p, idx) => {
                p.classList.remove('completed', 'active');
                if (idx === 0) p.classList.add('active');
            });
        }

        // 8. Re-enable Completion Button in Session Runner
        const completeBtn = document.getElementById('pscranker-complete-unit-btn');
        if (completeBtn) {
            completeBtn.disabled = false;
            completeBtn.innerHTML = '<span>Claim +' + (this.xpReward || 250) + ' XP &amp; Complete 🚀</span>';
        }

        // 9. Call custom capsule's own reset function if defined
        if (typeof window.pscResetCapsule === 'function') {
            try { window.pscResetCapsule(); } catch (err) { console.warn(err); }
        }

        // 10. Scroll smoothly to top of capsule
        const targetContainer = document.getElementById('psc-capsule-container') || customWrapper;
        if (targetContainer) {
            targetContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // 11. Subtle sound confirmation
        if (window.PscSound && window.PscSound.playTick) {
            window.PscSound.playTick();
        }
    }
};

// Dock Feature Image strictly inside the Lesson screen for Custom Code capsules
document.addEventListener('DOMContentLoaded', function() {
    const banner = document.getElementById('psc-custom-feature-image-banner');
    if (!banner) return;

    // Check where the lesson screen or slot is located inside custom code:
    const slot = document.getElementById('psc-feature-image-slot');
    const lessonScreen = document.getElementById('psc-screen-lesson') 
                      || document.querySelector('.psc-screen-lesson') 
                      || document.querySelector('[data-screen="lesson"]')
                      || document.querySelector('.psc-lesson-part');

    if (slot) {
        slot.appendChild(banner);
        banner.classList.remove('hidden');
    } else if (lessonScreen) {
        // Dock into the lesson screen right above the lesson title/card
        const card = lessonScreen.querySelector('.psc-card') || lessonScreen;
        const lessonTitle = card.querySelector('.psc-lesson-title') || card.querySelector('h1, h2, h3, h4');
        if (lessonTitle) {
            card.insertBefore(banner, lessonTitle);
        } else {
            card.prepend(banner);
        }
        banner.classList.remove('hidden');
    } else {
        // Fallback for single-screen lesson code
        const card = document.querySelector('.custom-session-wrapper .bg-white');
        if (card) {
            card.prepend(banner);
            banner.classList.remove('hidden');
        }
    }

    // Intercept pscGoTo to ensure completion bar stays hidden during intermediate screens
    if (typeof window.pscGoTo === 'function') {
        const origPscGoTo = window.pscGoTo;
        window.pscGoTo = function(step) {
            origPscGoTo.apply(this, arguments);
            const completionBar = document.getElementById('pscranker-bottom-completion-bar');
            if (completionBar && !window.PSCRanker?.isCompleted) {
                // On hook, lesson, mcqs, strictly keep completion bar hidden
                completionBar.style.display = 'none';
            }
        };
    }
});
</script>
@endpush
@endsection
