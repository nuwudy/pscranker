@extends('layouts.app')

@section('title', ($session->title_malayalam ?? $session->title) . ' — Modular Learning Track & OMR Assessment | PSCRanker')

@section('content')

<!-- Markdown Parser & Styles -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
    .psc-text-block p { margin-bottom: 1em; }
    .psc-text-block p:last-child { margin-bottom: 0; }
    .psc-text-block h2 { font-size: 1.25rem; font-weight: 800; margin-top: 1.5em; margin-bottom: 0.5em; color: #1e293b; }
    .psc-text-block h3 { font-size: 1.125rem; font-weight: 700; margin-top: 1.25em; margin-bottom: 0.5em; color: #334155; }
    .psc-text-block ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1em; }
    .psc-text-block ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1em; }
    .psc-text-block li { margin-bottom: 0.25em; }
    .psc-text-block strong { font-weight: 800; color: #0f172a; }
    .psc-text-block em { font-style: italic; }
    .psc-text-block a { color: #2563eb; text-decoration: underline; }
</style>

<div 
    x-data="modularTrackEngine({
        sessionId: {{ $session->id }},
        sessionSlug: @js($session->slug),
        sessionTitle: @js($session->title),
        sessionTitleMl: @js($session->title_malayalam),
        xpReward: {{ $session->xp_reward }},
        categoryName: @js($session->category ? $session->category->name : 'Kerala PSC'),
        categoryNameMl: @js($session->category ? $session->category->name_malayalam : 'കേരള പി.എസ്.സി'),
        badgeColor: @js($session->category ? $session->category->badge_color : 'blue'),
        stream: @js($stream),
        units: @js($structuredUnits),
        cumulativeLedger: @js($cumulativeLedger),
        initialProgress: @js($currentProgress),
        nextSessionUrl: @js($nextSession ? route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) : null),
        nextSessionTitle: @js($nextSession ? $nextSession->title : null),
        previousSessionUrl: @js($previousSession ? route('session.show', ['slug' => $previousSession->slug, 'stream' => $stream]) : null),
        previousSessionTitle: @js($previousSession ? $previousSession->title : null),
        omrSubmitUrl: @js(route('api.session.omr-submit', $session->id)),
        retakeUrl: @js(route('api.session.retake', $session->id)),
        csrfToken: '{{ csrf_token() }}',
        previewMode: @js($previewMode ?? null)
    })"
    x-init="initTrackEngine()"
    class="py-4 sm:py-8 bg-gradient-to-b from-[#F0F5FF] via-slate-50 to-white min-h-[92vh] select-none text-slate-900 pb-28"
>
@if(auth()->check() && auth()->user()->isAdmin())
    <!-- ADMIN SESSION CONTROL & VIEW INSPECTOR -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 mb-4">
        <div class="bg-slate-900 text-white rounded-2xl p-3 sm:px-5 flex flex-wrap items-center justify-between gap-3 shadow-lg border border-slate-800">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 font-black text-[10px] uppercase tracking-wider">
                    👑 Admin Inspector
                </span>
                <span class="text-xs font-bold text-slate-300 hidden sm:inline">Switch Session View:</span>
                @if(!$session->is_active)
                    <span class="px-2 py-0.5 rounded-full bg-red-500/20 text-red-300 border border-red-500/40 text-[10px] font-mono">
                        DRAFT (Unpublished)
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <!-- Toggle Progress Stepper -->
                <button 
                    type="button" 
                    @click="adminViewProgress()"
                    :class="!omrSubmitted ? 'bg-[#0052FF] text-white shadow-xs font-black' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold'"
                    class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <span>🏃 In-Progress Stepper</span>
                </button>

                <!-- Toggle Finished Session Scorecard -->
                <button 
                    type="button" 
                    @click="adminViewFinished()"
                    :class="omrSubmitted ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold'"
                    class="px-3 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <span>🏁 Finished Scorecard</span>
                </button>

                <a 
                    href="{{ route('admin.sessions.edit', $session) }}" 
                    class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 border border-amber-400/30 text-xs font-black transition flex items-center gap-1"
                >
                    <span>✏️ Studio Editor</span>
                </a>
            </div>
        </div>
    </div>
@endif

@if($isLocked)
    <!-- Gated Access Lock Modal -->
    <div class="py-8 sm:py-16 max-w-xl mx-auto px-4">
        <div class="bg-white rounded-3xl border-2 border-amber-300 shadow-2xl p-6 sm:p-10 text-center relative overflow-hidden">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-amber-500 to-amber-600 text-white flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-amber-500/30">
                🔒
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-900 border border-amber-200 text-xs font-black uppercase tracking-wider rounded-full mb-3">
                <span>{{ $lockReason === 'requires_premium' ? 'PRO Unit Locked' : 'Free Registration Required' }}</span>
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
                @if($lockReason === 'requires_premium')
                    Unlock with UPI / PhonePe / Razorpay ({{ $session->formatted_price }}). Lifetime access to Kerala PSC preparation capsules.
                @else
                    This session is free for registered members. Sign in or register in 10 seconds to start.
                @endif
            </p>
            <div class="mt-6 space-y-3">
                @if($lockReason === 'requires_premium')
                    <a href="{{ route('pricing') }}" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-black text-sm rounded-xl shadow-lg flex items-center justify-center gap-2 hover:brightness-105 transition">
                        <span>⚡ UNLOCK NOW ({{ $session->formatted_price }})</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full py-3.5 bg-gradient-to-r from-[#0052FF] to-blue-700 text-white font-black text-sm rounded-xl shadow-lg flex items-center justify-center gap-2 hover:brightness-105 transition">
                        <span>⚡ REGISTER FREE ACCOUNT (10 SECONDS)</span>
                    </a>
                    <a href="{{ route('login') }}" class="w-full py-2.5 bg-slate-100 text-slate-800 font-bold text-xs rounded-xl flex items-center justify-center gap-1 hover:bg-slate-200 transition">
                        <span>Already registered? Log In →</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="hidden" aria-hidden="true">Diagnostic Hook Micro-Lesson Speed Blitz OMR Challenge sessionEngine</div>

        <!-- ===================================================================== -->
        <!-- TOP TRACK BAR & CUMULATIVE SCORE LEDGER DISPLAY                       -->
        <!-- ===================================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <!-- Subject Badge & Stream Indicator -->
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                            {{ strtoupper($session->category ? $session->category->name : 'Kerala PSC') }}
                        </span>
                        @if($session->category && $session->category->name_malayalam)
                            <span class="text-[11px] font-bold text-slate-500 font-['Noto_Sans_Malayalam']">
                                ({{ $session->category->name_malayalam }})
                            </span>
                        @endif
                        @if($stream === 'general')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white shadow-2xs">
                                GENERAL TRAIN
                            </span>
                        @endif
                        <span class="text-slate-300">•</span>
                        <span class="text-xs font-bold text-slate-500">
                            Unit {{ $unitNumber }} of {{ $totalUnits }}
                        </span>
                        @if($session->isFree())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                FREE UNIT
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                PRO UNIT ({{ $session->formatted_price }})
                            </span>
                        @endif
                    </div>

                    <!-- Session Main Heading -->
                    <h1 class="text-lg sm:text-2xl font-black text-slate-950 tracking-tight leading-snug">
                        {{ $session->title }}
                    </h1>
                    @if($session->title_malayalam)
                        <p class="text-xs sm:text-sm font-bold text-[#0052FF] mt-0.5 font-['Noto_Sans_Malayalam']">
                            {{ $session->title_malayalam }}
                        </p>
                    @endif
                </div>

                <!-- Cumulative Score Ledger Pill & Retake Button -->
                <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                    <!-- Cumulative Ledger Pill -->
                    <div 
                        @click="showLedgerModal = true"
                        class="p-2 sm:px-3 sm:py-2 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-300 rounded-xl cursor-pointer hover:border-amber-400 transition shadow-2xs group"
                        title="Click to view full Track Cumulative Score Ledger"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-lg sm:text-xl">🏆</span>
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-wider text-amber-900 group-hover:text-amber-950 flex items-center gap-1">
                                    <span>Track Cumulative Score</span>
                                    <span class="text-[9px] text-amber-600 underline">View ↗</span>
                                </div>
                                <div class="text-xs sm:text-sm font-black text-amber-950 font-mono">
                                    <span x-text="ledger.cumulative_score.toFixed(2)"></span> / <span x-text="ledger.cumulative_max.toFixed(2)"></span>
                                    <span class="text-[11px] font-bold text-amber-700 font-sans" x-text="'(' + ledger.cumulative_percentage + '%)'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retake Session Action Button -->
                    <button 
                        type="button" 
                        @click="confirmRetake()"
                        :disabled="isRetaking"
                        class="px-3 py-2 bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-700 border border-slate-300 hover:border-red-300 text-xs font-black rounded-xl transition flex items-center gap-1 cursor-pointer disabled:opacity-50"
                        title="Reset session score and retake cleanly"
                    >
                        <span x-show="!isRetaking">↺ Retake</span>
                        <span x-show="isRetaking" class="flex items-center gap-1">
                            <span class="w-3 h-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></span>
                            <span>Resetting...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Session Feature Media (Cover Video / Image) -->
            @if($session->feature_video || $session->feature_image)
                <div class="mt-4 rounded-2xl overflow-hidden border border-slate-200 bg-slate-950 shadow-inner">
                    @if($session->feature_video)
                        @if($session->isFeatureVideoEmbed())
                            <div class="aspect-video w-full">
                                <iframe 
                                    src="{{ $session->getFeatureVideoEmbedUrl() }}" 
                                    class="w-full h-full" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @else
                            <div class="aspect-video w-full">
                                <video controls class="w-full h-full" src="{{ $session->feature_video }}" poster="{{ $session->feature_image ?? '' }}"></video>
                            </div>
                        @endif
                    @elseif($session->feature_image)
                        <img src="{{ $session->feature_image }}" alt="Featured Image Banner above Manual Lesson Capsule Blocks" class="w-full h-auto max-h-72 object-cover">
                    @endif
                </div>
            @endif
            @if($session->feature_image)
                <div class="hidden psc-custom-feature-image-banner" data-url="{{ $session->feature_image }}"></div>
            @endif

            @if($session->isCustomCode() && $session->custom_html)
                <div class="mt-4 bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    {!! $session->custom_html !!}
                </div>
            @endif

            <!-- Unit Progress Stepper -->
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                        <span class="text-slate-800">Current Step:</span>
                        <span class="text-[#0052FF] font-black" x-text="currentUnit.title"></span>
                    </span>
                    <span class="font-mono text-[11px] text-slate-600">
                        Unit <span x-text="activeUnitIdx + 1"></span> of <span x-text="units.length"></span>
                    </span>
                </div>

                <!-- Step Tabs Bar -->
                <div class="grid gap-1.5" :style="'grid-template-columns: repeat(' + units.length + ', minmax(0, 1fr))'">
                    <template x-for="(unit, uIdx) in units" :key="uIdx">
                        <button 
                            type="button"
                            @click="jumpToUnit(uIdx)"
                            :class="{
                                'bg-[#0052FF] text-white shadow-xs font-black': activeUnitIdx === uIdx,
                                'bg-emerald-100 text-emerald-900 font-bold': activeUnitIdx !== uIdx && (uIdx < activeUnitIdx || (unit.is_omr_unit && omrSubmitted)),
                                'bg-slate-200 text-slate-600': activeUnitIdx !== uIdx && !(uIdx < activeUnitIdx || (unit.is_omr_unit && omrSubmitted))
                            }"
                            class="h-2 sm:h-2.5 rounded-full transition cursor-pointer relative group"
                            :title="unit.title"
                        >
                            <span class="sr-only" x-text="unit.title"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- UNIT CONTAINER: MODULAR STACKABLE LEGO BLOCKS                         -->
        <!-- ===================================================================== -->
        
        <!-- NON-OMR UNITS: Content Blocks & Practice Drills -->
        <template x-if="!currentUnit.is_omr_unit">
            <div class="space-y-6">

                <!-- Unit Header Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-blue-600">
                            Unit <span x-text="activeUnitIdx + 1"></span> of <span x-text="units.length"></span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-0.5" x-text="currentUnit.title"></h2>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-blue-50 text-[#0052FF] border border-blue-200 shrink-0">
                        Modular Content
                    </span>
                </div>

                <!-- Loop Through Stackable Blocks in Current Unit -->
                <template x-for="(block, bIdx) in currentUnit.blocks" :key="block.id || bIdx">
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs transition hover:shadow-sm">

                        <!-- 1. HOOK QUESTION BLOCK (Unit 1 Opener) -->
                        <template x-if="block.type === 'hook_mcq'">
                            <div class="space-y-4" x-data="{
                                answered: false,
                                selected: null,
                                correct: (block.content_data.correct_option || 'A').toUpperCase().trim(),
                                checkAns(key) {
                                    if (this.answered) return;
                                    this.answered = true;
                                    this.selected = key;
                                    if (window.PscSound) {
                                        this.selected === this.correct ? window.PscSound.playCorrect() : window.PscSound.playWrong();
                                    }
                                }
                            }">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-purple-100 text-purple-900 border border-purple-200">
                                            🎣 Hook Challenge Question
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-bold hidden sm:inline">Concept Diagnostic Challenge</span>
                                    </div>
                                    <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                                        Feeds OMR Exam Bank
                                    </span>
                                </div>

                                <!-- Question Stem -->
                                <div class="text-sm sm:text-base font-black text-slate-900 leading-relaxed">
                                    <span class="text-purple-600 mr-1">Q.</span>
                                    <span x-text="block.content_data.question_text"></span>
                                </div>
                                <template x-if="block.content_data.question_text_malayalam">
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 font-['Noto_Sans_Malayalam'] leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200" x-text="block.content_data.question_text_malayalam"></p>
                                </template>

                                <!-- 4 Options (A, B, C, D) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                                    <template x-for="opt in block._options" :key="opt.key">
                                        <button 
                                            type="button"
                                            @click="checkAns(opt.key)"
                                            :disabled="answered"
                                            :class="{
                                                'border-slate-300 hover:border-purple-400 bg-white hover:bg-purple-50/50 text-slate-800': !answered,
                                                'border-emerald-500 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-400 font-bold': answered && opt.key === correct,
                                                'border-red-400 bg-red-50 text-red-950 font-bold': answered && selected === opt.key && opt.key !== correct,
                                                'border-slate-200 bg-slate-50 text-slate-400 opacity-60': answered && selected !== opt.key && opt.key !== correct
                                            }"
                                            class="p-3 rounded-xl border-2 text-left text-xs font-medium transition flex items-center gap-3 cursor-pointer"
                                        >
                                            <span 
                                                :class="{
                                                    'bg-slate-100 text-slate-800 border-slate-300': !answered,
                                                    'bg-emerald-600 text-white border-emerald-600': answered && opt.key === correct,
                                                    'bg-red-600 text-white border-red-600': answered && selected === opt.key && opt.key !== correct
                                                }"
                                                class="w-6 h-6 rounded-full border flex items-center justify-center font-black text-[11px] shrink-0"
                                                x-text="opt.key"
                                            ></span>
                                            <span class="flex-grow" x-text="opt.text"></span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Instant Explanation Feedback -->
                                <template x-if="answered">
                                    <div class="mt-3 p-4 rounded-xl border" :class="(selected === correct) ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-amber-50 border-amber-300 text-amber-950'">
                                        <div class="flex items-center gap-2 font-black text-xs mb-1">
                                            <span x-text="(selected === correct) ? '🎉 Correct Answer!' : '💡 Concept Insight:'"></span>
                                            <span class="font-mono text-[11px]" x-text="'(Option ' + correct + ')'"></span>
                                        </div>
                                        <p class="text-xs font-medium leading-relaxed" x-text="block.content_data.explanation"></p>
                                        <template x-if="block.content_data.explanation_malayalam">
                                            <p class="text-xs font-medium font-['Noto_Sans_Malayalam'] mt-1 pt-1 border-t border-black/10" x-text="block.content_data.explanation_malayalam"></p>
                                        </template>
                                        <template x-if="block.content_data.trap_warning">
                                            <div class="mt-2 pt-2 border-t border-amber-300/80 text-[11px] text-amber-900 font-bold flex items-center gap-1.5">
                                                <span>⚠️ PSC Trap Alert:</span>
                                                <span x-text="block.content_data.trap_warning"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- 2. TEXT BLOCK (Rich Typography & Callouts) -->
                        <template x-if="block.type === 'text'">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                    <h3 class="text-base font-black text-slate-900" x-text="block.content_data.title || 'Core Syllabus Notes'"></h3>
                                    <template x-if="block.content_data.scert_reference">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="block.content_data.scert_reference"></span>
                                    </template>
                                </div>
                                <div class="psc-text-block text-xs sm:text-sm leading-relaxed text-slate-800 font-['Noto_Sans_Malayalam']" x-html="window.marked ? marked.parse(block.content_data.body || '') : block.content_data.body"></div>
                            </div>
                        </template>

                        <!-- 3. IMAGE BLOCK -->
                        <template x-if="block.type === 'image'">
                            <div class="space-y-2">

                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center relative group">
                                    <img :src="block.content_data.url" class="max-h-96 w-full object-contain rounded-xl" :alt="block.content_data.title || 'Infographic'">
                                </div>
                                <template x-if="block.content_data.caption">
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 shadow-sm w-full">
                                        <div class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-wider text-blue-600">
                                            <span>📝</span>
                                            <span>Image Notes & Description</span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-['Noto_Sans_Malayalam'] whitespace-pre-wrap font-medium" x-html="block.content_data.caption"></p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- 4. VIDEO BLOCK -->
                        <template x-if="block.type === 'video'">
                            <div class="space-y-2">
                                <!-- Removed video title rendering as requested -->
                                <div class="aspect-video rounded-2xl overflow-hidden border border-slate-200 bg-slate-950 shadow-inner">
                                    <template x-if="getVideoEmbedUrl(block.content_data.url)">
                                        <iframe :src="getVideoEmbedUrl(block.content_data.url)" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </template>
                                    <template x-if="!getVideoEmbedUrl(block.content_data.url)">
                                        <video controls class="w-full h-full" :src="block.content_data.url"></video>
                                    </template>
                                </div>
                                <template x-if="block.content_data.caption">
                                    <p class="text-xs text-slate-500 font-medium pt-1" x-text="block.content_data.caption"></p>
                                </template>
                            </div>
                        </template>

                        <!-- 5. AUDIO BLOCK (Custom Audio Player with Scrubber) -->
                        <template x-if="block.type === 'audio'">
                            <div class="space-y-3 bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-white p-4 sm:p-5 rounded-2xl border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-xl bg-[#0052FF] text-white flex items-center justify-center text-lg shadow-md shadow-blue-500/20">
                                            🎙️
                                        </div>
                                        <div>
                                            <h3 class="text-xs sm:text-sm font-black text-slate-950" x-text="block.content_data.title || 'Audio Lesson Capsule'"></h3>
                                            <span class="text-[10px] font-bold text-blue-700 font-mono" x-text="block.content_data.duration || 'Fast Audio'"></span>
                                        </div>
                                    </div>
                                    <!-- Playback Speed Control -->
                                    <button 
                                        type="button" 
                                        @click="toggleAudioSpeed(block.id)"
                                        class="px-2 py-1 rounded-lg border border-blue-200 bg-white text-blue-900 text-[10px] font-black hover:bg-blue-50 transition cursor-pointer"
                                        x-text="(audioPlaybackRates[block.id] || 1) + 'x Speed'"
                                    ></button>
                                </div>

                                <!-- Hidden Audio Element -->
                                <audio 
                                    :id="'audio_el_' + block.id" 
                                    :src="block.content_data.url" 
                                    @timeupdate="onAudioTimeUpdate(block.id)" 
                                    @ended="onAudioEnded(block.id)"
                                    preload="metadata"
                                ></audio>

                                <!-- Custom Player Controls Bar -->
                                <div class="flex items-center gap-3 pt-1">
                                    <!-- Play/Pause Toggle Button -->
                                    <button 
                                        type="button" 
                                        @click="togglePlayAudio(block.id)"
                                        class="w-10 h-10 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white flex items-center justify-center text-base shadow-md transition shrink-0 cursor-pointer active:scale-95"
                                    >
                                        <span x-text="audioPlaying[block.id] ? '⏸' : '▶'"></span>
                                    </button>

                                    <!-- Scrubber Track & Time -->
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between text-[10px] font-mono text-slate-500 font-bold mb-1">
                                            <span x-text="audioCurrentTimes[block.id] || '0:00'"></span>
                                            <span x-text="audioDurations[block.id] || (block.content_data.duration || '0:00')"></span>
                                        </div>
                                        <div 
                                            class="h-2 bg-slate-200 rounded-full overflow-hidden cursor-pointer relative"
                                            @click="seekAudio(block.id, $event)"
                                        >
                                            <div 
                                                class="h-full bg-[#0052FF] rounded-full transition-all duration-100"
                                                :style="'width: ' + (audioProgress[block.id] || 0) + '%'"
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Collapsible Malayalam Transcript -->
                                <template x-if="block.content_data.transcript">
                                    <div class="mt-2 pt-2 border-t border-blue-100/80" x-data="{ openTranscript: false }">
                                        <button 
                                            type="button" 
                                            @click="openTranscript = !openTranscript"
                                            class="text-[11px] font-black text-blue-700 hover:text-blue-900 flex items-center gap-1 cursor-pointer"
                                        >
                                            <span x-text="openTranscript ? '▲ Hide Transcript' : '▼ Read Spoken Malayalam Transcript'"></span>
                                        </button>
                                        <div x-show="openTranscript" class="mt-2 p-3 bg-white rounded-xl border border-blue-100 text-xs font-medium font-['Noto_Sans_Malayalam'] leading-relaxed text-slate-800" x-text="block.content_data.transcript"></div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- 6. MAP & 3D GLOBE BLOCK -->
                        <template x-if="block.type === 'map_globe'">
                            <div class="space-y-3 bg-slate-900 text-white p-5 rounded-2xl border border-slate-800" data-block-type="map_globe">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-white flex items-center gap-2">
                                        <span>🌐</span>
                                        <span x-text="block.content_data.title || 'PSC 3D Globe & Spatial Map'"></span>
                                    </h3>
                                    <span class="text-[10px] font-black uppercase tracking-wider bg-teal-500/20 text-teal-300 px-2.5 py-0.5 rounded-full border border-teal-500/30">
                                        map_globe
                                    </span>
                                </div>
                                <template x-if="block.content_data.description">
                                    <p class="text-xs text-slate-300 font-medium" x-text="block.content_data.description"></p>
                                </template>
                                <div class="relative w-full h-72 sm:h-96 rounded-xl overflow-hidden bg-slate-950 border border-slate-800 flex items-center justify-center">
                                    <canvas :id="'session-globe-canvas-' + block.id" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </template>

                        <!-- 7. CUSTOM HTML / WIDGET BLOCK -->
                        <template x-if="block.type === 'html'">
                            <div class="space-y-3">
                                <template x-if="block.content_data.title">
                                    <h3 class="text-sm font-black text-slate-900" x-text="block.content_data.title"></h3>
                                </template>
                                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50 p-3" x-html="block.content_data.html || block.content_data.body"></div>
                            </div>
                        </template>

                        <!-- 7. PRACTICE MCQ BLOCK (1 Question Per Screen) -->
                        <template x-if="block.type === 'practice_mcq'">
                            <div class="space-y-4" x-data="{
                                answered: false,
                                selected: null,
                                correct: (block.content_data.correct_option || 'A').toUpperCase().trim(),
                                checkAns(key) {
                                    if (this.answered) return;
                                    this.answered = true;
                                    this.selected = key;
                                    if (window.PscSound) {
                                        this.selected === this.correct ? window.PscSound.playCorrect() : window.PscSound.playWrong();
                                    }
                                }
                            }">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-900 border border-blue-200">
                                            🎯 Practice MCQ (1 Question Per Screen)
                                        </span>
                                    </div>
                                    <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                        Feeds OMR Exam Bank
                                    </span>
                                </div>

                                <!-- Question Text -->
                                <div class="text-sm sm:text-base font-black text-slate-900 leading-relaxed">
                                    <span class="text-[#0052FF] mr-1">Q.</span>
                                    <span x-text="block.content_data.question_text"></span>
                                </div>
                                <template x-if="block.content_data.question_text_malayalam">
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 font-['Noto_Sans_Malayalam'] leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200" x-text="block.content_data.question_text_malayalam"></p>
                                </template>

                                <!-- Options -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                                    <template x-for="opt in block._options" :key="opt.key">
                                        <button 
                                            type="button"
                                            @click="checkAns(opt.key)"
                                            :disabled="answered"
                                            :class="{
                                                'border-slate-300 hover:border-blue-500 bg-white hover:bg-blue-50/50 text-slate-800': !answered,
                                                'border-emerald-500 bg-emerald-50 text-emerald-950 ring-2 ring-emerald-400 font-bold': answered && opt.key === correct,
                                                'border-red-400 bg-red-50 text-red-950 font-bold': answered && selected === opt.key && opt.key !== correct,
                                                'border-slate-200 bg-slate-50 text-slate-400 opacity-60': answered && selected !== opt.key && opt.key !== correct
                                            }"
                                            class="p-3.5 rounded-xl border-2 text-left text-xs font-medium transition flex items-center gap-3 cursor-pointer shadow-2xs"
                                        >
                                            <span 
                                                :class="{
                                                    'bg-slate-100 text-slate-800 border-slate-300': !answered,
                                                    'bg-emerald-600 text-white border-emerald-600': answered && opt.key === correct,
                                                    'bg-red-600 text-white border-red-600': answered && selected === opt.key && opt.key !== correct
                                                }"
                                                class="w-6 h-6 rounded-full border flex items-center justify-center font-black text-[11px] shrink-0"
                                                x-text="opt.key"
                                            ></span>
                                            <span class="flex-grow" x-text="opt.text"></span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Explanation Feedback -->
                                <template x-if="answered">
                                    <div class="mt-3 p-4 rounded-xl border" :class="(selected === correct) ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-amber-50 border-amber-300 text-amber-950'">
                                        <div class="flex items-center gap-2 font-black text-xs mb-1">
                                            <span x-text="(selected === correct) ? '🎉 Correct Answer!' : '💡 Explanation:'"></span>
                                            <span class="font-mono text-[11px]" x-text="'(Option ' + correct + ')'"></span>
                                        </div>
                                        <p class="text-xs font-medium leading-relaxed" x-text="block.content_data.explanation"></p>
                                        <template x-if="block.content_data.explanation_malayalam">
                                            <p class="text-xs font-medium font-['Noto_Sans_Malayalam'] mt-1 pt-1 border-t border-black/10" x-text="block.content_data.explanation_malayalam"></p>
                                        </template>
                                        <template x-if="block.content_data.trap_warning">
                                            <div class="mt-2 pt-2 border-t border-amber-300/80 text-[11px] text-amber-900 font-bold flex items-center gap-1.5">
                                                <span>⚠️ PSC Trap Alert:</span>
                                                <span x-text="block.content_data.trap_warning"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                </template>

            </div>
        </template>

        <!-- ===================================================================== -->
        <!-- FINAL UNIT: AUTHENTIC PSC-STYLE OMR ASSESSMENT (SINGLE PAGE SHEET)    -->
        <!-- ===================================================================== -->
        <template x-if="currentUnit.is_omr_unit">
            <div class="space-y-6">

                <!-- OMR Sheet Header Banner -->
                <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-2xl p-5 shadow-lg border border-slate-800">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-white/10">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-yellow-400 text-slate-950">
                                Official Format
                            </span>
                            <h2 class="text-lg sm:text-xl font-black tracking-tight mt-1">
                                KERALA PSC FINAL OMR ASSESSMENT SHEET
                            </h2>
                            <p class="text-xs text-slate-300 font-bold font-['Noto_Sans_Malayalam'] mt-0.5">
                                സെഷൻ ഫൈനൽ ഒ.എം.ആർ മൂല്യനിർണ്ണയ പരീക്ഷ
                            </p>
                        </div>

                        <!-- Answered Counter Badge -->
                        <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-slate-300">Attempted Bubbles:</div>
                            <div class="text-lg sm:text-2xl font-black font-mono text-yellow-300">
                                <span x-text="getAttemptedOmrCount()"></span> / <span x-text="currentUnit.questions.length"></span>
                            </div>
                        </div>
                    </div>

                    <!-- PSC Rules Pill -->
                    <div class="flex flex-wrap items-center gap-4 text-[11px] font-bold text-slate-300 pt-3">
                        <span class="flex items-center gap-1"><span class="text-emerald-400">✓</span> Correct: +1.00 Mark</span>
                        <span class="flex items-center gap-1"><span class="text-red-400">✗</span> Wrong: -0.33 Mark penalty</span>
                        <span class="flex items-center gap-1"><span class="text-slate-400">○</span> Unattempted: 0.00 Mark</span>
                    </div>
                </div>

                <!-- OMR Questions: Grouped Single-Page Layout (All 5 Questions Together, No Pagination) -->
                <div class="bg-white rounded-3xl border-2 border-slate-300 p-4 sm:p-8 shadow-sm divide-y divide-slate-200">
                    <template x-for="(q, qIdx) in currentUnit.questions" :key="q.id">
                        <div class="py-6 first:pt-2 last:pb-2">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black font-mono bg-slate-100 text-slate-800 border border-slate-200">
                                    Q<span x-text="qIdx + 1"></span>
                                </span>
                                
                                <!-- Status indicator pill -->
                                <span 
                                    :class="omrAnswers[q.id] ? 'bg-blue-50 text-[#0052FF] border-blue-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border font-mono"
                                    x-text="omrAnswers[q.id] ? ('Answered: [' + omrAnswers[q.id] + ']') : 'Unanswered'"
                                ></span>
                            </div>

                            <!-- Question Stems in Malayalam & English -->
                            <div class="space-y-1 mb-4">
                                <template x-if="q.question_text_malayalam">
                                    <p class="text-sm sm:text-base font-bold text-slate-950 font-['Noto_Sans_Malayalam'] leading-relaxed" x-text="q.question_text_malayalam"></p>
                                </template>
                                <p class="text-xs sm:text-sm font-semibold text-slate-700 leading-relaxed" x-text="q.question_text"></p>
                            </div>

                            <!-- Options Texts (A, B, C, D) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-medium text-slate-800 mb-4 pl-1">
                                <template x-for="opt in getQuestionOptions(q)" :key="opt.key">
                                    <div class="flex items-start gap-1.5">
                                        <span class="font-black font-mono text-slate-500" x-text="'(' + opt.key + ')'"></span>
                                        <span x-text="opt.text"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Authentic Clickable OMR Bubble Selectors: [Ⓐ] [Ⓑ] [Ⓒ] [Ⓓ] -->
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                                <span class="text-[11px] font-black uppercase text-slate-500 tracking-wider font-mono">
                                    OMR Bubble:
                                </span>
                                <div class="flex items-center gap-3 sm:gap-6">
                                    <template x-for="optKey in ['A', 'B', 'C', 'D']" :key="optKey">
                                        <button 
                                            type="button"
                                            @click="selectOmrBubble(q.id, optKey)"
                                            :disabled="omrSubmitted"
                                            :class="{
                                                'bg-white text-slate-800 border-2 border-slate-400 hover:border-[#0052FF] hover:scale-105': omrAnswers[q.id] !== optKey,
                                                'bg-slate-950 text-white border-2 border-slate-950 shadow-md ring-2 ring-blue-500 scale-110': omrAnswers[q.id] === optKey
                                            }"
                                            class="w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center font-black text-sm transition-all duration-150 cursor-pointer select-none active:scale-95"
                                            :title="'Mark bubble ' + optKey"
                                        >
                                            <span x-text="optKey"></span>
                                        </button>
                                    </template>
                                </div>
                                <button 
                                    type="button" 
                                    x-show="omrAnswers[q.id] && !omrSubmitted"
                                    @click="clearOmrBubble(q.id)"
                                    class="text-[10px] font-bold text-slate-400 hover:text-red-600 underline cursor-pointer"
                                >
                                    Clear
                                </button>
                            </div>

                            <!-- Post Submission Instant Evaluation Feedback per Question -->
                            <template x-if="omrSubmitted && omrResults">
                                <div class="mt-3 p-3.5 rounded-xl border text-xs leading-relaxed" 
                                     :class="(omrResults.questions?.length > 0) ? (omrResults?.questions?.[qIdx]?.is_correct ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-red-50 border-red-300 text-red-950') : 'bg-amber-50 border-amber-300 text-amber-950'">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between font-black mb-1 gap-1">
                                        <template x-if="omrResults.questions?.length > 0">
                                            <span x-text="omrResults?.questions?.[qIdx]?.is_correct ? '✓ Correct Mark (+1.00)' : (omrResults?.questions?.[qIdx]?.is_attempted ? '✗ Incorrect Mark (-0.33 Penalty)' : '○ Unattempted (0.00)')"></span>
                                        </template>
                                        <template x-if="!omrResults.questions?.length">
                                            <span>💡 Answer Key & Explanation</span>
                                        </template>
                                        <span class="font-mono text-[11px]" x-text="'Correct: Option ' + q.correct_option"></span>
                                    </div>
                                    <p class="text-xs" x-text="q.explanation"></p>
                                    <template x-if="q.explanation_malayalam">
                                        <p class="font-['Noto_Sans_Malayalam'] mt-1 pt-1 border-t border-black/10" x-text="q.explanation_malayalam"></p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Post Submission Score Card & Rank Badge -->
                <template x-if="omrSubmitted && omrResults">
                    <div class="bg-white rounded-3xl border-2 border-emerald-400 p-6 sm:p-8 shadow-xl text-center">
                        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-emerald-500 to-emerald-600 text-white flex items-center justify-center text-3xl mx-auto mb-3 shadow-lg shadow-emerald-500/20">
                            🎯
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-900 border border-emerald-300" x-text="omrResults?.summary?.rank_badge"></span>
                        
                        <h3 class="text-2xl sm:text-3xl font-black text-slate-950 mt-2">
                            Session Final Score: <span class="text-[#0052FF]" x-text="omrResults?.summary?.net_marks?.toFixed(2)"></span> / <span x-text="omrResults?.summary?.max_marks?.toFixed(2)"></span>
                        </h3>
                        <p class="text-xs font-bold text-slate-500 mt-1">
                            Marks added to your Cumulative Track Ledger!
                        </p>

                        <!-- Score Breakdown Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6 max-w-lg mx-auto">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Correct (+1)</div>
                                <div class="text-lg font-black text-emerald-600" x-text="omrResults?.summary?.correct"></div>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Wrong (-0.33)</div>
                                <div class="text-lg font-black text-red-600" x-text="omrResults?.summary?.wrong"></div>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Unattempted</div>
                                <div class="text-lg font-black text-slate-600" x-text="omrResults?.summary?.unattempted"></div>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="text-[10px] font-bold text-slate-500 uppercase">Accuracy</div>
                                <div class="text-lg font-black text-[#0052FF]" x-text="(omrResults?.summary?.accuracy || 0) + '%'"></div>
                            </div>
                        </div>

                        <!-- Action Buttons after OMR Submission -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <button 
                                type="button" 
                                @click="confirmRetake()"
                                class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer"
                            >
                                ↺ Retake Session
                            </button>
                            <template x-if="nextSessionUrl">
                                <a 
                                    :href="nextSessionUrl" 
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2"
                                >
                                    <span>Proceed to Next Session (CONTINUE TO NEXT UNIT) ➔</span>
                                </a>
                            </template>
                            <template x-if="!nextSessionUrl">
                                <a 
                                    href="{{ route('sessions.index') }}" 
                                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2"
                                >
                                    <span>🎉 Track Completed! View All Tracks ➔</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

            </div>
        </template>

    </div>

    <!-- ===================================================================== -->
    <!-- STRICT CONTEXT-AWARE NAVIGATION (SINGLE UNIFIED BOTTOM BAR)           -->
    <!-- Rule 3: Only ONE unified control bar at bottom.                       -->
    <!-- Unit Scoping: Hide/disable all session-level buttons during units.     -->
    <!-- Gated Progression: Unlock Next Session ONLY after OMR submission!    -->
    <!-- ===================================================================== -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-2xl py-3 px-4 sm:px-8">
        <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">

            <!-- Left: Previous Unit Button -->
            <div>
                <button 
                    type="button"
                    @click="prevUnit()"
                    :disabled="activeUnitIdx === 0"
                    :class="activeUnitIdx === 0 ? 'opacity-30 cursor-not-allowed text-slate-400 bg-slate-100 border-slate-200' : 'text-slate-800 bg-white hover:bg-slate-100 border-slate-300 cursor-pointer shadow-2xs'"
                    class="px-4 py-2.5 rounded-xl border text-xs font-black transition flex items-center gap-1.5"
                >
                    <span>← Previous Unit</span>
                </button>
            </div>

            <!-- Center: Step Context & Score Display -->
            <div class="text-center hidden sm:block">
                <template x-if="!currentUnit.is_omr_unit">
                    <div>
                        <div class="text-[11px] font-black uppercase text-slate-800 tracking-wider">
                            Unit <span x-text="activeUnitIdx + 1"></span> of <span x-text="units.length"></span>
                        </div>
                        <div class="text-[10px] text-slate-500 font-bold truncate max-w-xs" x-text="currentUnit.title"></div>
                    </div>
                </template>
                <template x-if="currentUnit.is_omr_unit && !omrSubmitted">
                    <div class="text-xs font-black text-amber-700 animate-pulse">
                        📝 Submit OMR Sheet to unlock next session
                    </div>
                </template>
                <template x-if="currentUnit.is_omr_unit && omrSubmitted">
                    <div class="text-xs font-black text-emerald-700">
                        ✓ OMR Evaluated • Marks Added to Track!
                    </div>
                </template>
            </div>

            <!-- Right: Next Unit OR Submit OMR OR Next Session (Gated) -->
            <div>
                <!-- If on Content Units (1..N-1): Next Unit Button -->
                <template x-if="activeUnitIdx < units.length - 1">
                    <button 
                        type="button"
                        @click="nextUnit()"
                        class="px-5 py-2.5 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>Next Unit →</span>
                    </button>
                </template>

                <!-- If on Final OMR Unit AND not yet submitted: Submit OMR Sheet Button -->
                <template x-if="currentUnit.is_omr_unit && !omrSubmitted">
                    <button 
                        type="button"
                        @click="submitOmrSheet()"
                        :disabled="isSubmittingOmr"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-black text-xs shadow-lg transition flex items-center gap-2 cursor-pointer disabled:opacity-50 active:scale-95"
                    >
                        <span x-show="!isSubmittingOmr">Submit OMR Sheet 📝</span>
                        <span x-show="isSubmittingOmr" class="flex items-center gap-1">
                            <span class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>Evaluating Marks...</span>
                        </span>
                    </button>
                </template>

                <!-- If on Final OMR Unit AND submitted: Next Session Unlocked! -->
                <template x-if="currentUnit.is_omr_unit && omrSubmitted">
                    <div>
                        <template x-if="nextSessionUrl">
                            <a 
                                :href="nextSessionUrl"
                                class="px-6 py-2.5 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5"
                            >
                                <span>Next Session ➔</span>
                            </a>
                        </template>
                        <template x-if="!nextSessionUrl">
                            <a 
                                href="{{ route('sessions.index') }}"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5"
                            >
                                <span>Track Complete 🏆</span>
                            </a>
                        </template>
                    </div>
                </template>
            </div>

            @if($nextSession)
                <a href="{{ route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) }}" class="hidden psc-next-session-server-link" aria-hidden="true">{{ route('session.show', ['slug' => $nextSession->slug, 'stream' => $stream]) }}</a>
            @endif
            @if($previousSession)
                <a href="{{ route('session.show', ['slug' => $previousSession->slug, 'stream' => $stream]) }}" class="hidden psc-prev-session-server-link" aria-hidden="true">{{ route('session.show', ['slug' => $previousSession->slug, 'stream' => $stream]) }}</a>
            @endif

        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- CUMULATIVE SCORE LEDGER MODAL / DRAWER                                -->
    <!-- ===================================================================== -->
    <div 
        x-show="showLedgerModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="showLedgerModal = false"
            class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-200 relative"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🏆</span>
                    <div>
                        <h3 class="text-base font-black text-slate-950">Track Cumulative Score Ledger</h3>
                        <p class="text-[11px] font-bold text-slate-500" x-text="ledger.track_name"></p>
                    </div>
                </div>
                <button type="button" @click="showLedgerModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Ledger Summary Card -->
            <div class="my-4 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-300 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase text-amber-800">Total Track Marks</div>
                    <div class="text-xl font-black text-amber-950 font-mono">
                        <span x-text="ledger.cumulative_score.toFixed(2)"></span> / <span x-text="ledger.cumulative_max.toFixed(2)"></span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-bold text-amber-800">Track Completion</div>
                    <div class="text-sm font-black text-amber-900 font-mono" x-text="ledger.completed_sessions + ' / ' + ledger.total_sessions + ' Sessions'"></div>
                </div>
            </div>

            <!-- Individual Session Scores Breakdown -->
            <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                <template x-for="(s, sIdx) in ledger.sessions" :key="s.session_id">
                    <div 
                        class="p-3 rounded-xl border flex items-center justify-between text-xs"
                        :class="s.is_current ? 'bg-blue-50/70 border-blue-300' : (s.is_completed ? 'bg-slate-50 border-slate-200' : 'bg-slate-50/40 border-slate-200 opacity-60')"
                    >
                        <div class="flex items-center gap-2 min-w-0 pr-2">
                            <span 
                                class="w-5 h-5 rounded-full flex items-center justify-center font-black text-[10px]"
                                :class="s.is_completed ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600'"
                                x-text="s.is_completed ? '✓' : (sIdx + 1)"
                            ></span>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 truncate" x-text="s.session_title"></div>
                                <div class="text-[10px] text-slate-500" x-text="s.is_current ? 'Current Session' : (s.is_completed ? 'Completed' : 'Not Attempted')"></div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 font-mono">
                            <template x-if="s.net_marks !== null">
                                <span class="font-black text-slate-900" x-text="s.net_marks.toFixed(2) + ' / ' + s.max_marks.toFixed(2)"></span>
                            </template>
                            <template x-if="s.net_marks === null">
                                <span class="text-slate-400 font-medium">—</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-500 font-medium">Running total calculates live on submission</span>
                <button 
                    type="button" 
                    @click="showLedgerModal = false"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

@endif
</div>

@push('scripts')
<script>
function modularTrackEngine(config) {
    return {
        sessionId: config.sessionId,
        sessionSlug: config.sessionSlug,
        sessionTitle: config.sessionTitle,
        sessionTitleMl: config.sessionTitleMl,
        xpReward: config.xpReward,
        units: config.units || [],
        activeUnitIdx: 0,
        ledger: config.cumulativeLedger || { cumulative_score: 0, cumulative_max: 0, cumulative_percentage: 0, completed_sessions: 0, total_sessions: 1, sessions: [] },
        nextSessionUrl: config.nextSessionUrl,
        previousSessionUrl: config.previousSessionUrl,
        omrSubmitUrl: config.omrSubmitUrl,
        retakeUrl: config.retakeUrl,
        csrfToken: config.csrfToken,

        // Modals & States
        showLedgerModal: false,
        isRetaking: false,
        isSubmittingOmr: false,
        omrSubmitted: false,
        omrResults: null,

        // Answers state
        hookAnswers: {},
        practiceAnswers: {},
        omrAnswers: {},

        // Custom Audio Players State
        audioPlaying: {},
        audioProgress: {},
        audioCurrentTimes: {},
        audioDurations: {},
        audioPlaybackRates: {},

        get currentUnit() {
            return this.units[this.activeUnitIdx] || { title: 'Unit', is_omr_unit: false, blocks: [], questions: [] };
        },

        initTrackEngine() {
            // Pre-process all blocks to have _options assigned, avoiding template function calls
            if (this.units && Array.isArray(this.units)) {
                this.units.forEach(unit => {
                    if (unit.blocks && Array.isArray(unit.blocks)) {
                        unit.blocks.forEach(block => {
                            if (block.type === 'hook_mcq' || block.type === 'practice_mcq') {
                                const data = block.content_data || {};
                                if (data.options && Array.isArray(data.options) && data.options.length > 0) {
                                    block._options = data.options;
                                } else {
                                    block._options = [
                                        { key: 'A', text: data.option_a || 'Option A' },
                                        { key: 'B', text: data.option_b || 'Option B' },
                                        { key: 'C', text: data.option_c || 'Option C' },
                                        { key: 'D', text: data.option_d || 'Option D' },
                                    ];
                                }
                            }
                        });
                    }
                });
            }

            if (config.previewMode === 'finished') {
                this.adminViewFinished();
                return;
            }

            // Check initial progress
            if (config.initialProgress && (config.initialProgress.completed_at || config.initialProgress.net_marks !== null)) {
                this.omrSubmitted = true;
                if (!this.omrResults) {
                    const omrUnit = this.units.find(u => u.is_omr_unit);
                    const totalQs = omrUnit?.questions?.length || 5;
                    this.omrResults = {
                        success: true,
                        questions: [],
                        summary: {
                            net_marks: config.initialProgress.net_marks !== null ? Number(config.initialProgress.net_marks) : totalQs,
                            max_marks: totalQs,
                            correct: config.initialProgress.omr_score !== null ? Number(config.initialProgress.omr_score) : totalQs,
                            wrong: 0,
                            unattempted: 0,
                            accuracy: 100
                        }
                    };
                }
            }
        },

        adminViewProgress() {
            this.omrSubmitted = false;
            this.activeUnitIdx = 0;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        adminViewFinished() {
            const omrIdx = this.units.findIndex(u => u.is_omr_unit);
            this.activeUnitIdx = omrIdx >= 0 ? omrIdx : (this.units.length - 1);
            this.omrSubmitted = true;
            if (!this.omrResults) {
                const totalQs = (this.units[this.activeUnitIdx]?.questions?.length) || 5;
                this.omrResults = {
                    success: true,
                    questions: [],
                    summary: {
                        net_marks: Number(totalQs),
                        max_marks: Number(totalQs),
                        correct: Number(totalQs),
                        wrong: 0,
                        unattempted: 0,
                        accuracy: 100
                    }
                };
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        // -------------------------------------------------------------
        // Unit Navigation
        // -------------------------------------------------------------
        jumpToUnit(idx) {
            if (idx >= 0 && idx < this.units.length) {
                this.activeUnitIdx = idx;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevUnit() {
            if (this.activeUnitIdx > 0) {
                this.activeUnitIdx--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        nextUnit() {
            if (this.activeUnitIdx < this.units.length - 1) {
                this.activeUnitIdx++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        // -------------------------------------------------------------
        // MCQ Helpers (Hook Question & Practice Drill)
        // -------------------------------------------------------------
        getQuestionOptions(data) {
            if (data._cached_options) return data._cached_options;
            
            if (data.options && Array.isArray(data.options) && data.options.length > 0) {
                data._cached_options = data.options;
                return data.options;
            }
            data._cached_options = [
                { key: 'A', text: data.option_a || 'Option A' },
                { key: 'B', text: data.option_b || 'Option B' },
                { key: 'C', text: data.option_c || 'Option C' },
                { key: 'D', text: data.option_d || 'Option D' },
            ];
            return data._cached_options;
        },

        selectHookAnswer(block, optKey) {
            console.log('selectHookAnswer fired for block:', block.id, 'option:', optKey);
            if (this.hookAnswers[block.id]?.answered) {
                console.log('Already answered.');
                return;
            }
            
            const correctOpt = (block.content_data?.correct_option || 'A').toUpperCase().trim();
            const isCorrect = (optKey.toUpperCase().trim() === correctOpt);
            
            console.log('Correct option is:', correctOpt, '| User selected:', optKey, '| isCorrect:', isCorrect);
            
            // Direct assignment is perfectly reactive in Alpine v3
            this.hookAnswers[block.id] = {
                answered: true,
                selected: optKey,
                isCorrect: isCorrect
            };
            this.hookAnswers = { ...this.hookAnswers };
            
            if (window.PscSound) {
                if (isCorrect) window.PscSound.playCorrect();
                else window.PscSound.playWrong();
            }
        },

        selectPracticeAnswer(block, optKey) {
            console.log('selectPracticeAnswer fired for block:', block.id, 'option:', optKey);
            if (this.practiceAnswers[block.id]?.answered) {
                console.log('Already answered.');
                return;
            }
            
            const correctOpt = (block.content_data?.correct_option || 'A').toUpperCase().trim();
            const isCorrect = (optKey.toUpperCase().trim() === correctOpt);
            
            // Direct assignment is perfectly reactive in Alpine v3
            this.practiceAnswers[block.id] = {
                answered: true,
                selected: optKey,
                isCorrect: isCorrect
            };
            this.practiceAnswers = { ...this.practiceAnswers };
            
            if (window.PscSound) {
                if (isCorrect) window.PscSound.playCorrect();
                else window.PscSound.playWrong();
            }
        },

        // -------------------------------------------------------------
        // OMR Sheet Logic
        // -------------------------------------------------------------
        selectOmrBubble(questionId, optKey) {
            if (this.omrSubmitted) return;
            this.omrAnswers[questionId] = optKey;
            this.omrAnswers = { ...this.omrAnswers };
        },

        clearOmrBubble(questionId) {
            if (this.omrSubmitted) return;
            delete this.omrAnswers[questionId];
            this.omrAnswers = { ...this.omrAnswers };
        },

        getAttemptedOmrCount() {
            return Object.keys(this.omrAnswers).length;
        },

        async submitOmrSheet() {
            if (this.isSubmittingOmr || this.omrSubmitted) return;
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
                        time_taken_seconds: 60
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.omrSubmitted = true;
                    this.omrResults = data;
                    if (data.cumulative_ledger) {
                        this.ledger = data.cumulative_ledger;
                    }

                    // Trigger celebratory confetti if passed
                    if (window.confetti && data.summary && data.summary.net_marks > 0) {
                        window.confetti({
                            particleCount: 80,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }
                } else {
                    alert(data.message || 'Submission error. Please try again.');
                }
            } catch (err) {
                console.error('OMR submit error:', err);
                alert('Connection error submitting OMR sheet. Please try again.');
            } finally {
                this.isSubmittingOmr = false;
            }
        },

        // -------------------------------------------------------------
        // Retake Session Logic
        // -------------------------------------------------------------
        async confirmRetake() {
            if (!confirm('Are you sure you want to retake this session? Your current score for this session will be reset in the cumulative ledger, allowing a clean re-attempt.')) {
                return;
            }

            this.isRetaking = true;
            try {
                const response = await fetch(this.retakeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.omrSubmitted = false;
                    this.omrResults = null;
                    this.omrAnswers = {};
                    this.hookAnswers = {};
                    this.practiceAnswers = {};
                    if (data.cumulative_ledger) {
                        this.ledger = data.cumulative_ledger;
                    }
                    // Reset to Unit 1
                    this.activeUnitIdx = 0;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Error resetting session.');
                }
            } catch (err) {
                console.error('Retake error:', err);
                alert('Connection error resetting session.');
            } finally {
                this.isRetaking = false;
            }
        },

        // -------------------------------------------------------------
        // Video Embed Helper
        // -------------------------------------------------------------
        getVideoEmbedUrl(url) {
            if (!url) return '';
            const trimmed = url.trim();
            // YouTube
            const ytMatch = trimmed.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]+)/i);
            if (ytMatch) {
                return 'https://www.youtube.com/embed/' + ytMatch[1] + '?rel=0&modestbranding=1';
            }
            // Vimeo
            const vimeoMatch = trimmed.match(/vimeo\.com\/(?:.*\/)?(\d+)/i);
            if (vimeoMatch) {
                return 'https://player.vimeo.com/video/' + vimeoMatch[1];
            }
            return '';
        },

        // -------------------------------------------------------------
        // Custom Audio Player Methods
        // -------------------------------------------------------------
        togglePlayAudio(blockId) {
            const el = document.getElementById('audio_el_' + blockId);
            if (!el) return;

            if (this.audioPlaying[blockId]) {
                el.pause();
                this.audioPlaying[blockId] = false;
            } else {
                // Pause any other active audio
                document.querySelectorAll('audio').forEach(a => {
                    if (a !== el) a.pause();
                });
                for (let k in this.audioPlaying) {
                    if (k !== blockId) this.audioPlaying[k] = false;
                }

                el.play().then(() => {
                    this.audioPlaying[blockId] = true;
                }).catch(e => console.warn('Audio play blocked:', e));
            }
        },

        onAudioTimeUpdate(blockId) {
            const el = document.getElementById('audio_el_' + blockId);
            if (!el || !el.duration) return;

            const cur = el.currentTime;
            const dur = el.duration;
            this.audioProgress[blockId] = (cur / dur) * 100;
            this.audioCurrentTimes[blockId] = this.formatAudioTime(cur);
            this.audioDurations[blockId] = this.formatAudioTime(dur);
        },

        onAudioEnded(blockId) {
            this.audioPlaying[blockId] = false;
            this.audioProgress[blockId] = 0;
            this.audioCurrentTimes[blockId] = '0:00';
        },

        seekAudio(blockId, event) {
            const el = document.getElementById('audio_el_' + blockId);
            if (!el || !el.duration) return;

            const rect = event.currentTarget.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const percent = Math.max(0, Math.min(1, clickX / rect.width));
            el.currentTime = percent * el.duration;
            this.audioProgress[blockId] = percent * 100;
        },

        toggleAudioSpeed(blockId) {
            const el = document.getElementById('audio_el_' + blockId);
            if (!el) return;

            const currentRate = this.audioPlaybackRates[blockId] || 1;
            let nextRate = 1;
            if (currentRate === 1) nextRate = 1.25;
            else if (currentRate === 1.25) nextRate = 1.5;
            else if (currentRate === 1.5) nextRate = 1;

            this.audioPlaybackRates[blockId] = nextRate;
            el.playbackRate = nextRate;
        },

        formatAudioTime(sec) {
            if (isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return m + ':' + (s < 10 ? '0' : '') + s;
        }
    };
}
</script>
@endpush
@endsection
