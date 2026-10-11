<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $session->title }} - PSC MicroLearning</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Malayalam:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Noto Sans Malayalam', sans-serif; }
        .lesson-markdown h1 { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.3; }
        .lesson-markdown h2 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-top: 1.1rem; margin-bottom: 0.4rem; line-height: 1.3; }
        .lesson-markdown h3 { font-size: 0.95rem; font-weight: 800; color: #334155; margin-top: 1rem; margin-bottom: 0.35rem; line-height: 1.3; }
        .lesson-markdown p { margin-bottom: 0.75rem; line-height: 1.6; }
        .lesson-markdown ul { list-style-type: disc; padding-left: 1.25rem; margin-bottom: 0.75rem; }
        .lesson-markdown ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.75rem; }
        .lesson-markdown li { margin-bottom: 0.3rem; line-height: 1.5; }
        .lesson-markdown hr { margin: 1rem 0; border-color: #e2e8f0; }
        .lesson-markdown strong { font-weight: 800; color: #0f172a; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-indigo-50/30 to-purple-50/20 min-h-screen text-slate-800 antialiased flex flex-col items-center justify-center sm:py-6"
    x-data="pscmSessionData()" x-init="initData({{ json_encode($session) }})">

    {{-- Main Mobile Container Container --}}
    <div class="w-full max-w-md bg-white min-h-screen sm:min-h-[820px] sm:max-h-[90vh] sm:rounded-3xl shadow-2xl flex flex-col relative overflow-hidden border border-slate-100 sm:my-auto">
        
        <!-- Header / Progress & Quick Actions -->
        <div class="px-5 py-3.5 bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-20 flex items-center justify-between gap-3">
            
            {{-- Prev step button --}}
            <button @click="prevStep" 
                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition disabled:opacity-30" 
                    :disabled="currentStep === 0 || currentStep >= 9">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            {{-- Progress pill track --}}
            <div class="flex-1 px-1">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 mb-1">
                    <span x-text="getStepTitle()"></span>
                    <span x-text="`${Math.min(currentStep + 1, 9)}/9`"></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2 rounded-full transition-all duration-300" :style="`width: ${(Math.min(currentStep, 9) / 9) * 100}%`"></div>
                </div>
            </div>

            <!-- Timer during OMR test -->
            <div x-show="currentStep === 9" class="font-mono text-xs font-extrabold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200 animate-pulse" x-text="formatTime(omrTimeLeft)"></div>

            {{-- Skip step button --}}
            <button @click="nextStep" x-show="currentStep < 8" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 px-2 py-1 rounded-lg hover:bg-indigo-50 transition">
                Skip &rarr;
            </button>

            {{-- Catalog close button --}}
            <a href="{{ route('learner.pscm.index') }}" x-show="currentStep >= 8" class="text-slate-400 hover:text-slate-700 p-1" title="Back to catalog">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>

        <!-- Category & Session Badge (Screens 0 to 7) -->
        <div x-show="currentStep < 8" class="px-6 pt-3 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                <span>🏷️</span>
                <span>{{ $session->category ?? 'GK' }}</span>
            </span>

            {{-- Prev / Next session links if available --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                @if(isset($prevSession))
                    <a href="{{ route('learner.pscm.show', $prevSession->slug) }}" class="hover:text-indigo-600 p-1 rounded hover:bg-slate-100 transition" title="Previous session: {{ $prevSession->title }}">
                        &larr; Prev Lesson
                    </a>
                @endif
                @if(isset($prevSession) && isset($nextSession))
                    <span>•</span>
                @endif
                @if(isset($nextSession))
                    <a href="{{ route('learner.pscm.show', $nextSession->slug) }}" class="hover:text-indigo-600 p-1 rounded hover:bg-slate-100 transition" title="Next session: {{ $nextSession->title }}">
                        Next Lesson &rarr;
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Content Scrollable Area -->
        <div class="flex-1 overflow-y-auto p-6 flex flex-col justify-between">
            
            <!-- Screen 0: Hook Question -->
            <div x-show="currentStep === 0" class="flex-1 flex flex-col justify-center animate-fade-in">
                <div class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-3 w-fit border border-blue-100">
                    🎯 Previous PSC Question
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug mb-6" x-text="session.hook_question?.question || 'PSC Question'"></h2>
                
                <div class="space-y-3 mb-6">
                    <template x-for="(opt, idx) in session.hook_question?.options" :key="idx">
                        <button @click="selectHookAnswer(idx)"
                            :class="getHookBtnClass(idx)"
                            class="w-full text-left p-4 rounded-2xl border-2 transition-all font-semibold text-sm shadow-sm flex items-center gap-4 cursor-pointer">
                            <span class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center shrink-0 border transition"
                                  :class="hookAnswer === idx ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                  x-text="['A','B','C','D'][idx]"></span>
                            <span class="flex-1 pl-1 leading-relaxed" x-text="opt"></span>
                        </button>
                    </template>
                </div>

                <!-- Instant feedback for Hook Question -->
                <div x-show="hookAnswer !== null" class="mb-6 p-4 rounded-2xl border transition-all animate-fade-in" 
                     :class="hookAnswer === session.hook_question?.correct_index ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                    <div class="flex items-center gap-2 font-bold text-sm mb-1">
                        <span x-text="hookAnswer === session.hook_question?.correct_index ? '✅ Correct Answer!' : '❌ Incorrect.'"></span>
                    </div>
                    <p class="text-xs leading-relaxed opacity-90" x-text="session.hook_question?.explanation || (hookAnswer === session.hook_question?.correct_index ? 'Great job! Let\'s break down the concepts behind this PSC question.' : 'Don\'t worry! Let\'s analyze this question and master the concept.')"></p>
                </div>

                <div class="mt-auto pt-4">
                    <button @click="nextStep" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-200 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span x-text="hookAnswer !== null ? 'Continue to Analysis' : 'Analyze This Question'"></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Screen 1: Explainer Video (Optional) -->
            <div x-show="currentStep === 1" class="flex-1 flex flex-col justify-between animate-fade-in">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2 block">🎬 Video Insight</span>
                    <h2 class="text-xl font-black text-slate-900 mb-4">Core Explanation</h2>
                    
                    <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-lg bg-black flex items-center justify-center border border-slate-200">
                        <template x-if="session.media?.video_url && isYouTube(session.media.video_url)">
                            <iframe class="w-full h-full" :src="getYouTubeEmbedUrl(session.media.video_url)" title="Video Explanation" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </template>
                        <template x-if="session.media?.video_url && !isYouTube(session.media.video_url)">
                            <video class="w-full h-full object-cover" controls :src="session.media.video_url"></video>
                        </template>
                        <template x-if="!session.media?.video_url">
                            <span class="text-white/60 text-xs">No video specified</span>
                        </template>
                    </div>
                </div>
                <div class="pt-6 flex items-center gap-3">
                    <button @click="prevStep" type="button" class="px-5 py-4 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">
                        &larr; Back
                    </button>
                    <button @click="nextStep" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition">
                        Continue to Concept &rarr;
                    </button>
                </div>
            </div>

            <!-- Screen 2: Visual Concept (Optional) -->
            <div x-show="currentStep === 2" class="flex-1 flex flex-col justify-between animate-fade-in">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2 block">🖼️ Concept Anchor</span>
                    <h2 class="text-xl font-black text-slate-900 mb-4">Visual Memory Hook</h2>
                    
                    <div class="w-full rounded-2xl overflow-hidden shadow-sm bg-slate-100 border border-slate-200 flex items-center justify-center min-h-[220px]">
                        <template x-if="session.media?.image_url">
                            <img :src="session.media.image_url" :alt="session.media.image_caption || 'Concept Anchor'" class="w-full max-h-80 object-contain rounded-2xl">
                        </template>
                        <template x-if="!session.media?.image_url">
                            <span class="text-slate-400 text-xs">No image specified</span>
                        </template>
                    </div>
                    
                    <p class="mt-3 text-xs text-slate-600 text-center font-semibold bg-slate-50 p-2 rounded-xl border border-slate-100" x-show="session.media?.image_caption" x-text="session.media?.image_caption"></p>
                </div>
                <div class="pt-6 flex items-center gap-3">
                    <button @click="prevStep" type="button" class="px-5 py-4 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">
                        &larr; Back
                    </button>
                    <button @click="nextStep" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition">
                        Read Lesson &rarr;
                    </button>
                </div>
            </div>

            <!-- Screen 3: Core Lesson -->
            <div x-show="currentStep === 3" class="flex-1 flex flex-col justify-between animate-fade-in">
                <div>
                    <div class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-3 border border-emerald-100">
                        📖 High-Yield SCERT/NCERT Lesson
                    </div>
                    <h2 class="text-xl font-black text-slate-900 mb-4 leading-tight">{{ $session->title }}</h2>
                    <div class="lesson-markdown text-slate-700 text-sm font-medium bg-slate-50/70 p-4 rounded-2xl border border-slate-200" x-html="renderMarkdown(session.lesson_content)"></div>
                </div>
                <div class="pt-6 flex items-center gap-3">
                    <button @click="prevStep" type="button" class="px-5 py-4 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">
                        &larr; Back
                    </button>
                    <button @click="nextStep" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Start Practice MCQs (4)</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Screens 4-7: Practice MCQs -->
            <template x-for="(mcq, idx) in session.practice_mcqs" :key="idx">
                <div x-show="currentStep === (4 + idx)" class="flex-1 flex flex-col justify-between animate-fade-in">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100" x-text="`Practice MCQ ${idx + 1} of 4`"></span>
                            <span class="text-xs text-slate-400 font-semibold">Immediate Feedback</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 mb-5 leading-snug" x-text="mcq.question"></h2>
                        <div class="space-y-3">
                            <template x-for="(opt, optIdx) in mcq.options" :key="optIdx">
                                <button @click="selectPracticeAnswer(idx, optIdx)" 
                                    :class="getPracticeBtnClass(idx, optIdx)"
                                    class="w-full text-left p-4 rounded-2xl border-2 transition-all font-semibold text-sm shadow-sm flex items-center gap-4">
                                    <span class="w-8 h-8 rounded-full font-bold text-xs flex items-center justify-center shrink-0 border"
                                          :class="practiceAnswers[idx] === optIdx ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-100 text-slate-600 border-slate-200'"
                                          x-text="['A','B','C','D'][optIdx]"></span>
                                    <span class="flex-1 pl-1 leading-relaxed" x-text="opt"></span>
                                </button>
                            </template>
                        </div>
                        
                        <!-- Instant Explainer Feedback -->
                        <div x-show="practiceAnswers[idx] !== null" class="mt-5 p-4 rounded-2xl border transition-all animate-fade-in" :class="practiceAnswers[idx] === mcq.correct_index ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900'">
                            <div class="flex items-center gap-2 font-bold text-sm mb-1">
                                <span x-text="practiceAnswers[idx] === mcq.correct_index ? '✅ Spot on! Correct answer.' : '❌ Not quite. Review explanation:'"></span>
                            </div>
                            <p class="text-xs leading-relaxed opacity-90" x-text="mcq.explanation"></p>
                        </div>
                    </div>

                    <div class="pt-6 flex items-center gap-3">
                        <button @click="prevStep" type="button" class="px-5 py-4 rounded-2xl border-2 border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition text-sm">
                            &larr; Back
                        </button>
                        <button x-show="practiceAnswers[idx] !== null" @click="nextStep" class="flex-1 bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-lg hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                            <span>Continue</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Screen 8: OMR Instructions Screen -->
            <div x-show="currentStep === 8" class="flex-1 flex flex-col justify-center items-center text-center animate-fade-in">
                <div class="w-20 h-20 bg-rose-50 border border-rose-100 rounded-3xl flex items-center justify-center mb-6 shadow-sm">
                    <span class="text-4xl">📝</span>
                </div>
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider mb-2">Simulated PSC Exam</span>
                <h2 class="text-2xl font-black text-slate-900 mb-2">Interactive OMR Test</h2>
                <p class="text-slate-500 text-sm mb-6 max-w-xs">5 Total Questions (Hook + Practice MCQs) with strict Kerala PSC negative marking (0.33 deduction per wrong answer).</p>
                
                <div class="w-full bg-slate-50 rounded-2xl p-4 mb-8 border border-slate-200 text-left text-xs space-y-2">
                    <div class="flex justify-between text-slate-700">
                        <span class="font-semibold">Questions:</span>
                        <span class="font-bold">5 Questions</span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span class="font-semibold">Time Allotted:</span>
                        <span class="font-bold">5:00 Minutes</span>
                    </div>
                    <div class="flex justify-between text-slate-700">
                        <span class="font-semibold">Marking Scheme:</span>
                        <span class="font-bold">+1.00 / -0.33</span>
                    </div>
                </div>

                <button @click="startOMR" class="w-full bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-rose-200 transition transform active:scale-95">
                    Start OMR Test Now &rarr;
                </button>
            </div>

            <!-- Screen 9: OMR Interface -->
            <div x-show="currentStep === 9" class="flex-1 flex flex-col justify-between animate-fade-in">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-rose-600 uppercase tracking-wider" x-text="`Question ${currentOmrQ + 1} of ${omrQuestions.length}`"></span>
                        <span class="text-xs font-semibold text-slate-400">OMR Bubble Sheet</span>
                    </div>
                    
                    <h3 class="font-bold text-slate-900 text-base mb-4 leading-snug" x-text="omrQuestions[currentOmrQ]?.question"></h3>
                    
                    <div class="space-y-3 mb-6">
                        <template x-for="(opt, oIdx) in omrQuestions[currentOmrQ]?.options" :key="oIdx">
                            <div class="flex items-center gap-4 p-3.5 border-2 rounded-2xl bg-slate-50/70 hover:bg-white transition cursor-pointer"
                                 @click="omrAnswers[currentOmrQ] = oIdx"
                                 :class="omrAnswers[currentOmrQ] === oIdx ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-200'">
                                <!-- OMR Bubble Circle -->
                                <button type="button"
                                    :class="omrAnswers[currentOmrQ] === oIdx ? 'bg-slate-900 border-slate-900 text-white ring-2 ring-indigo-400' : 'bg-white border-slate-400 text-slate-600'"
                                    class="w-9 h-9 rounded-full border-2 flex items-center justify-center transition-all font-bold text-sm shrink-0">
                                    <span x-text="['A','B','C','D'][oIdx]"></span>
                                </button>
                                <span class="text-slate-800 font-semibold text-sm leading-relaxed select-none pl-1 flex-1" x-text="opt"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Navigation grid & Submit -->
                <div class="pt-4 border-t border-slate-100">
                    <div class="flex justify-between items-center mb-4 gap-2">
                        <button @click="currentOmrQ = Math.max(0, currentOmrQ - 1)" 
                                class="px-4 py-2 border border-slate-300 rounded-xl font-bold text-xs text-slate-700 hover:bg-slate-50 transition disabled:opacity-40" 
                                :disabled="currentOmrQ === 0">
                            &larr; Previous
                        </button>

                        <!-- Bubble Number Jumpers -->
                        <div class="flex justify-center space-x-1.5">
                            <template x-for="(q, idx) in omrQuestions" :key="idx">
                                <button @click="currentOmrQ = idx"
                                    :class="{
                                        'ring-2 ring-indigo-500 font-black': currentOmrQ === idx,
                                        'bg-slate-900 text-white': omrAnswers[idx] !== null,
                                        'bg-slate-200 text-slate-600': omrAnswers[idx] === null
                                    }"
                                    class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center transition" x-text="idx + 1"></button>
                            </template>
                        </div>

                        <button @click="currentOmrQ = Math.min(omrQuestions.length - 1, currentOmrQ + 1)" 
                                class="px-4 py-2 border border-slate-300 rounded-xl font-bold text-xs text-slate-700 hover:bg-slate-50 transition disabled:opacity-40" 
                                :disabled="currentOmrQ === (omrQuestions.length - 1)">
                            Next &rarr;
                        </button>
                    </div>

                    <button @click="submitOMR" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-emerald-100 transition">
                        Submit & Calculate Score
                    </button>
                </div>
            </div>

            <!-- Screen 10: Gamified Evaluation & Completion Screen -->
            <div x-show="currentStep === 10" class="flex-1 flex flex-col justify-center items-center text-center animate-fade-in">
                <div class="w-24 h-24 bg-gradient-to-tr from-amber-100 to-yellow-50 rounded-3xl flex items-center justify-center mb-4 shadow-sm border border-amber-200">
                    <span class="text-5xl">🏆</span>
                </div>
                
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">Session Complete!</span>
                <h2 class="text-3xl font-black text-slate-900 mb-2" x-text="`Score: ${score.toFixed(2)} / 5.00`"></h2>
                
                <div class="flex items-center gap-2 mb-6 text-xs font-bold">
                    <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full"><span x-text="correctCount"></span> Correct</span>
                    <span class="bg-rose-100 text-rose-800 px-3 py-1 rounded-full"><span x-text="wrongCount"></span> Wrong</span>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full"><span x-text="unattemptedCount"></span> Skipped</span>
                </div>
                
                {{-- Rewards --}}
                <div class="w-full bg-gradient-to-br from-indigo-50/80 to-purple-50/80 rounded-2xl p-4 mb-6 text-left border border-indigo-100 text-xs">
                    <h4 class="font-extrabold text-indigo-950 uppercase tracking-wider mb-2">Rewards Unlocked</h4>
                    <div class="flex justify-between items-center text-indigo-900 font-semibold mb-1">
                        <span>XP Earned:</span>
                        <span class="text-sm font-black text-indigo-700">+150 XP ⚡</span>
                    </div>
                    <div class="flex justify-between items-center text-indigo-900 font-semibold">
                        <span>Daily PSC Streak:</span>
                        <span class="text-sm font-black text-amber-600">Active 🔥</span>
                    </div>
                </div>

                {{-- Previous and Next Lesson Navigation Buttons --}}
                <div class="w-full space-y-3">
                    @if(isset($nextSession))
                        <a href="{{ route('learner.pscm.show', $nextSession->slug) }}" class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition transform active:scale-95">
                            <span>Next Lesson: {{ Str::limit($nextSession->title, 25) }}</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endif

                    <div class="flex items-center gap-3">
                        @if(isset($prevSession))
                            <a href="{{ route('learner.pscm.show', $prevSession->slug) }}" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition text-center">
                                &larr; Previous Lesson
                            </a>
                        @endif
                        <a href="{{ route('learner.pscm.index') }}" class="flex-1 py-3 px-4 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-black transition text-center">
                            Browse All Lessons 📚
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function pscmSessionData() {
            return {
                session: {},
                currentStep: 0,
                hookAnswer: null,
                practiceAnswers: [null, null, null, null],
                
                // OMR state
                omrQuestions: [],
                omrAnswers: [],
                currentOmrQ: 0,
                omrTimeLeft: 300,
                omrTimer: null,
                
                // Results state
                score: 0,
                correctCount: 0,
                wrongCount: 0,
                unattemptedCount: 0,

                initData(sessionData) {
                    this.session = sessionData;
                    
                    // Parse hook_question if needed
                    let hookQ = this.session.hook_question;
                    if (typeof hookQ === 'string') {
                        try { hookQ = JSON.parse(hookQ); } catch(e) {}
                    }

                    let practiceQs = this.session.practice_mcqs || [];
                    if (typeof practiceQs === 'string') {
                        try { practiceQs = JSON.parse(practiceQs); } catch(e) {}
                    }

                    // Prepare OMR questions (Hook + 4 Practice)
                    this.omrQuestions = [];
                    if (hookQ && hookQ.question) {
                        this.omrQuestions.push(hookQ);
                    }
                    if (Array.isArray(practiceQs)) {
                        this.omrQuestions.push(...practiceQs);
                    }

                    this.omrAnswers = new Array(this.omrQuestions.length).fill(null);
                },

                getStepTitle() {
                    const titles = [
                        'Hook Question',
                        'Explainer Video',
                        'Visual Anchor',
                        'Core Lesson',
                        'Practice 1',
                        'Practice 2',
                        'Practice 3',
                        'Practice 4',
                        'OMR Test Intro',
                        'OMR Test',
                        'Completed'
                    ];
                    return titles[this.currentStep] || 'Learning';
                },

                pauseMedia() {
                    // Pause HTML5 video players
                    const videos = document.querySelectorAll('video');
                    videos.forEach(v => {
                        try { v.pause(); } catch(e) {}
                    });

                    // Stop YouTube iframe audio/video by re-assigning src or posting postMessage
                    const iframes = document.querySelectorAll('iframe');
                    iframes.forEach(iframe => {
                        try {
                            iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                        } catch(e) {}
                    });
                },

                nextStep() {
                    this.pauseMedia();
                    let next = this.currentStep + 1;
                    
                    // Skip media screens if no data
                    if (next === 1 && !this.session.media?.video_url) next++;
                    if (next === 2 && !this.session.media?.image_url) next++;
                    
                    this.currentStep = next;
                },

                prevStep() {
                    this.pauseMedia();
                    let prev = this.currentStep - 1;
                    if (prev === 2 && !this.session.media?.image_url) prev--;
                    if (prev === 1 && !this.session.media?.video_url) prev--;
                    this.currentStep = Math.max(0, prev);
                },

                renderMarkdown(text) {
                    if (!text) return '';
                    if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
                        return marked.parse(text);
                    }
                    // Comprehensive Regex Fallback if marked is loading
                    let html = text
                        .replace(/^### (.*$)/gim, '<h3 class="text-sm font-bold text-slate-800 mt-3 mb-1">$1</h3>')
                        .replace(/^## (.*$)/gim, '<h2 class="text-base font-extrabold text-slate-900 mt-4 mb-1.5">$1</h2>')
                        .replace(/^# (.*$)/gim, '<h1 class="text-lg font-black text-slate-900 mt-4 mb-2">$1</h1>')
                        .replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold text-slate-900">$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em class="italic">$1</em>')
                        .replace(/^---$/gim, '<hr class="my-3 border-slate-200">')
                        .replace(/^[\*\-] (.*$)/gim, '<li class="ml-4 list-disc mb-1">$1</li>')
                        .replace(/\n\n/g, '<div class="h-2"></div>')
                        .replace(/\n/g, '<br>');
                    return html;
                },

                isYouTube(url) {
                    if (!url) return false;
                    return url.includes('youtube.com') || url.includes('youtu.be');
                },

                getYouTubeEmbedUrl(url) {
                    if (!url) return '';
                    let videoId = '';
                    if (url.includes('youtu.be/')) {
                        videoId = url.split('youtu.be/')[1]?.split('?')[0];
                    } else if (url.includes('youtube.com/watch')) {
                        const match = url.match(/[?&]v=([^&]+)/);
                        videoId = match ? match[1] : '';
                    } else if (url.includes('youtube.com/embed/')) {
                        return url;
                    }
                    return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
                },

                selectHookAnswer(optIdx) {
                    if (this.hookAnswer !== null) return;
                    this.hookAnswer = optIdx;
                },

                getHookBtnClass(optIdx) {
                    const ans = this.hookAnswer;
                    const correctIdx = this.session.hook_question?.correct_index;
                    
                    if (ans === null) {
                        return 'border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 bg-white text-slate-800';
                    }
                    
                    if (optIdx === correctIdx) {
                        return 'bg-emerald-50 border-emerald-500 text-emerald-900 shadow-sm';
                    }
                    if (optIdx === ans && optIdx !== correctIdx) {
                        return 'bg-rose-50 border-rose-500 text-rose-900 shadow-sm';
                    }
                    
                    return 'border-slate-200 opacity-40 bg-slate-50 text-slate-500';
                },

                selectPracticeAnswer(mcqIdx, optIdx) {
                    if (this.practiceAnswers[mcqIdx] !== null) return;
                    this.practiceAnswers[mcqIdx] = optIdx;
                },

                getPracticeBtnClass(mcqIdx, optIdx) {
                    const ans = this.practiceAnswers[mcqIdx];
                    const correctIdx = this.session.practice_mcqs[mcqIdx]?.correct_index;
                    
                    if (ans === null) return 'border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 bg-white text-slate-800';
                    
                    if (optIdx === correctIdx) return 'bg-emerald-50 border-emerald-500 text-emerald-900 shadow-sm';
                    if (optIdx === ans && optIdx !== correctIdx) return 'bg-rose-50 border-rose-500 text-rose-900 shadow-sm';
                    
                    return 'border-slate-200 opacity-40 bg-slate-50 text-slate-500';
                },

                startOMR() {
                    this.currentStep = 9;
                    this.omrTimeLeft = this.session.omr_config?.time_limit_seconds || 300;
                    this.omrTimer = setInterval(() => {
                        this.omrTimeLeft--;
                        if (this.omrTimeLeft <= 0) {
                            this.submitOMR();
                        }
                    }, 1000);
                },

                formatTime(seconds) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                submitOMR() {
                    if (this.omrTimer) clearInterval(this.omrTimer);
                    
                    this.correctCount = 0;
                    this.wrongCount = 0;
                    this.unattemptedCount = 0;

                    this.omrAnswers.forEach((ans, idx) => {
                        const target = this.omrQuestions[idx];
                        if (ans === null) {
                            this.unattemptedCount++;
                        } else if (ans === target.correct_index) {
                            this.correctCount++;
                        } else {
                            this.wrongCount++;
                        }
                    });

                    this.score = this.correctCount - (this.wrongCount * 0.33);
                    if(this.score < 0) this.score = 0;

                    this.currentStep = 10;
                    
                    if(this.score >= 3) {
                        confetti({
                            particleCount: 150,
                            spread: 70,
                            origin: { y: 0.6 }
                        });
                    }

                    // Save attempt asynchronously
                    fetch(`/micro-learning/${this.session.id}/attempt`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            practice_responses: this.practiceAnswers,
                            omr_responses: this.omrAnswers,
                            gamification: { score: this.score, correct: this.correctCount, wrong: this.wrongCount }
                        })
                    }).catch(() => {});
                }
            }
        }
    </script>
</body>
</html>
