@extends('layouts.admin')
@section('title', isset($session) ? 'Edit MicroLearning Unit' : 'Create MicroLearning Unit')
@section('content')

    <div class="py-6 sm:py-8 bg-slate-50 min-h-screen" 
         x-data="pscmAdminForm()" 
         x-init="initData()">
         
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- ========================================================================= --}}
            {{-- TOP STICKY BAR: NAVIGATION & ACTIONS                                      --}}
            {{-- ========================================================================= --}}
            <div class="sticky top-0 z-30 bg-slate-50/95 backdrop-blur-md py-3 -my-3 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.pscm.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-900 shadow-2xs transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-700">Single Unit Studio</span>
                            <span class="text-xs text-slate-400 font-bold">• 4-Phase Micro-Learning</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                            {{ isset($session) ? 'Edit Course Unit' : 'Create Course Unit' }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    @if(isset($session) && !empty($session->slug))
                        <a href="{{ route('learner.pscm.show', $session->slug) }}" target="_blank" class="px-3 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                            <span>👁️ Learner View ↗</span>
                        </a>
                    @endif

                    <button type="button" 
                            @click="submitForm()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black text-xs sm:text-sm shadow-md shadow-emerald-200 transition cursor-pointer">
                        <span>💾 Save &amp; Launch Unit</span>
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl text-xs font-bold text-emerald-950 flex items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border-2 border-rose-300 text-rose-900 px-5 py-4 rounded-2xl text-xs font-bold shadow-2xs">
                    <p class="font-black text-sm mb-1.5">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ========================================================================= --}}
            {{-- AI FAST-TRACK ACCELERATOR CARD (Generate from Question Screenshot / OCR)   --}}
            {{-- ========================================================================= --}}
            <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl shadow-indigo-950/10 border border-indigo-700/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-3 border-b border-indigo-700/60">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-white/10 text-white font-black text-sm flex items-center justify-center border border-white/20">
                            ✨
                        </span>
                        <div>
                            <h2 class="text-base sm:text-lg font-black tracking-tight text-white flex items-center gap-2">
                                <span>AI Instant Course Creator</span>
                                <span class="text-[10px] bg-emerald-400 text-emerald-950 px-2 py-0.5 rounded-full font-black uppercase">Gemini 1.5 Flash</span>
                            </h2>
                            <p class="text-xs text-indigo-200">Paste your PSC question (from OCR / screenshot / paper) to auto-generate the Hook, Lesson, and 4 MCQs in 5 seconds!</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="copyPromptTemplate()" 
                                class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-indigo-100 rounded-lg text-[11px] font-bold border border-white/15 transition flex items-center gap-1 cursor-pointer">
                            <span>📋 Copy AI Prompt</span>
                        </button>
                        <button type="button" 
                                @click="showJsonImporter = !showJsonImporter" 
                                class="px-2.5 py-1.5 bg-white/10 hover:bg-white/20 text-indigo-100 rounded-lg text-[11px] font-bold border border-white/15 transition flex items-center gap-1 cursor-pointer">
                            <span>📦 Paste JSON</span>
                        </button>
                    </div>
                </div>

                {{-- Question Paste Box --}}
                <div class="space-y-3">
                    <textarea 
                        x-model="rawHookQuestion" 
                        rows="3" 
                        class="w-full bg-slate-900/60 border border-indigo-400/30 rounded-2xl p-3.5 text-white placeholder-indigo-300/60 text-xs sm:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition" 
                        placeholder="Paste question text here... (e.g. 'Question 7:- Considering the given list, which one falls in the category of Jovian Planet? A:- Mercury, B:- Mars, C:- Jupiter, D:- Earth. Correct Answer:- Option-C')">
                    </textarea>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-[11px] text-indigo-200 flex items-center gap-1.5 font-medium">
                            <span class="text-emerald-400 font-bold">⚡ What happens:</span>
                            <span>Extracts question &bull; Classifies subject &bull; Generates SCERT facts &bull; Builds 4 MCQs &bull; Ready to edit below.</span>
                        </div>

                        <button 
                            type="button" 
                            @click="generateAI()" 
                            :disabled="loadingAi" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-emerald-950 rounded-xl font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/20 disabled:opacity-60 transition cursor-pointer shrink-0">
                            <svg x-show="loadingAi" class="animate-spin w-4 h-4 text-emerald-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-show="!loadingAi">✨ Auto-Generate Entire Unit</span>
                            <span x-show="loadingAi">Generating Lesson &amp; MCQs...</span>
                        </button>
                    </div>

                    <p x-show="aiErrorMsg" x-text="aiErrorMsg" class="text-rose-300 font-bold text-xs bg-rose-950/60 p-2.5 rounded-xl border border-rose-500/50"></p>
                    <p x-show="aiSuccessMsg" x-text="aiSuccessMsg" class="text-emerald-300 font-bold text-xs bg-emerald-950/60 p-2.5 rounded-xl border border-emerald-500/50"></p>
                </div>

                {{-- Optional Direct JSON Importer Box --}}
                <div x-show="showJsonImporter" x-transition class="mt-4 pt-4 border-t border-indigo-700/60 space-y-2">
                    <div class="flex items-center justify-between text-xs text-indigo-200">
                        <span class="font-bold">Paste Structured AI JSON Response:</span>
                        <span class="text-[10px] text-indigo-300">Auto-populates all inputs below</span>
                    </div>
                    <textarea 
                        x-model="rawJsonInput" 
                        rows="4" 
                        class="w-full bg-slate-900/80 font-mono text-xs text-emerald-400 p-3 rounded-xl border border-indigo-500/40 focus:outline-none" 
                        placeholder='{"category": "Geography", "suggested_title": "...", "hook_question": {...}, "lesson_content": "...", "practice_mcqs": [...]}'>
                    </textarea>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showJsonImporter = false" class="px-3 py-1 bg-white/10 hover:bg-white/20 text-xs rounded-lg font-bold">Cancel</button>
                        <button type="button" @click="importRawJson()" class="px-4 py-1 bg-emerald-500 hover:bg-emerald-400 text-emerald-950 text-xs rounded-lg font-black">Parse &amp; Load Fields</button>
                    </div>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- MAIN FORM: SUBMITS AS A SINGLE COHESIVE UNIT                              --}}
            {{-- ========================================================================= --}}
            <form id="pscm-unit-form" 
                  action="{{ isset($session) ? route('admin.pscm.update', $session->id) : route('admin.pscm.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-6">
                @csrf
                @if(isset($session)) @method('PUT') @endif

                {{-- Hidden JSON fields that stay synced with the visual fields --}}
                <input type="hidden" name="hook_question" :value="JSON.stringify(hookQ)">
                <input type="hidden" name="practice_mcqs" :value="JSON.stringify(practiceQs)">

                {{-- ===================================================================== --}}
                {{-- CARD 1: UNIT ESSENTIALS & ACCESS TIERS                                --}}
                {{-- ===================================================================== --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <span class="w-7 h-7 rounded-xl bg-blue-50 text-[#0052FF] font-black text-xs flex items-center justify-center border border-blue-200">
                            1
                        </span>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Course Unit Metadata &amp; Monetization</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Classify your unit, set the course title, and configure the paywall access layer.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Session Title --}}
                        <div class="md:col-span-2">
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                Unit Title <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                x-model="title" 
                                required 
                                placeholder="e.g. Jovian Planets &amp; Solar System Classification" 
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-sm transition">
                        </div>

                        {{-- Subject Category --}}
                        <div>
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                PSC Subject Track <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                name="category" 
                                x-model="category" 
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-sm transition">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }} ({{ $key }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Access Layer --}}
                        <div>
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                Access Layer <span class="text-amber-500 font-black">★</span>
                            </label>
                            <select 
                                name="access_level" 
                                x-model="accessLevel" 
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-sm transition"
                                :class="accessLevel === 'premium' ? 'border-amber-300 bg-amber-50/40 text-amber-900' : (accessLevel === 'registered' ? 'border-blue-300 bg-blue-50/40 text-blue-900' : '')">
                                <option value="premium">👑 Paid / PRO Layer (Default)</option>
                                <option value="registered">👤 Free for Registered Members</option>
                                <option value="free">🎁 Free (Open to Everyone)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1" x-text="accessLevel === 'premium' ? 'Requires active PRO subscription to play.' : (accessLevel === 'registered' ? 'Free for logged-in students.' : 'Unrestricted public access.')"></p>
                        </div>

                        {{-- Publication Status --}}
                        <div>
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                Publication Status
                            </label>
                            <select 
                                name="status" 
                                x-model="status" 
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-semibold text-sm transition">
                                <option value="published">🟢 Published (Live for Students)</option>
                                <option value="draft">🟡 Draft (Admin Only)</option>
                                <option value="archived">⚪ Archived</option>
                            </select>
                        </div>

                        {{-- Individual Unit Price if Paid --}}
                        <div x-show="accessLevel === 'premium'">
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                Standalone Unit Price (₹)
                            </label>
                            <input 
                                type="number" 
                                name="price" 
                                x-model="price" 
                                step="1" 
                                min="0" 
                                class="w-full border border-slate-200 rounded-xl py-2.5 px-3.5 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-bold text-sm transition" 
                                placeholder="199">
                        </div>
                    </div>
                </div>

                {{-- ===================================================================== --}}
                {{-- CARD 2: OPTIONAL MEDIA ANCHOR (Upload Video/Image OR Paste URL)       --}}
                {{-- ===================================================================== --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-purple-50 text-purple-700 font-black text-xs flex items-center justify-center border border-purple-200">
                                2
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Visual &amp; Video Anchors</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Optional media shown to learners between Hook Question and the Core Lesson.</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-200">
                            Optional Media
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Video Anchor Card --}}
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-3">
                            <div>
                                <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1 flex items-center justify-between">
                                    <span>🎥 Explainer Video</span>
                                    <span class="text-[10px] text-slate-400 font-medium">YouTube / MP4</span>
                                </label>
                                
                                <input 
                                    type="text" 
                                    name="video_url" 
                                    x-model="videoUrl" 
                                    placeholder="https://www.youtube.com/watch?v=... or direct MP4 link" 
                                    class="w-full border border-slate-200 rounded-xl p-2.5 text-xs text-slate-800 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition">

                                <!-- Direct Video File Upload -->
                                <div class="mt-2.5 pt-2.5 border-t border-slate-200 flex items-center justify-between">
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition shadow-2xs" 
                                           :class="uploadingVideo ? 'opacity-50 pointer-events-none' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span x-text="uploadingVideo ? 'Uploading MP4...' : '📁 Upload Local Video'"></span>
                                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime" class="hidden" @change="uploadMediaFile($event, 'video')">
                                    </label>

                                    <button type="button" x-show="videoUrl" @click="videoUrl = ''" class="text-xs text-rose-500 hover:text-rose-700 font-bold">Remove</button>
                                </div>
                            </div>

                            <div x-show="videoUrl" class="text-[11px] text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-lg border border-emerald-200 font-bold flex items-center gap-1">
                                <span>✓ Video Attached</span>
                            </div>
                        </div>

                        {{-- Image / Diagram Card --}}
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-3">
                            <div>
                                <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1 flex items-center justify-between">
                                    <span>🖼️ Visual Anchor / Diagram / Meme</span>
                                    <span class="text-[10px] text-slate-400 font-medium">PNG / JPG / WebP</span>
                                </label>
                                
                                <input 
                                    type="text" 
                                    name="image_url" 
                                    x-model="imageUrl" 
                                    placeholder="https://... or uploaded image URL" 
                                    class="w-full border border-slate-200 rounded-xl p-2.5 text-xs text-slate-800 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition mb-2">
                                
                                <input 
                                    type="text" 
                                    name="image_caption" 
                                    x-model="imageCaption" 
                                    placeholder="Optional caption (e.g. 'Jovian vs Terrestrial Planets Diagram')" 
                                    class="w-full border border-slate-200 rounded-xl p-2 text-xs text-slate-800 bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition">

                                <!-- Direct Image File Upload -->
                                <div class="mt-2.5 pt-2.5 border-t border-slate-200 flex items-center justify-between">
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition shadow-2xs" 
                                           :class="uploadingImage ? 'opacity-50 pointer-events-none' : ''">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span x-text="uploadingImage ? 'Uploading Image...' : '📁 Upload Local Image'"></span>
                                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" @change="uploadMediaFile($event, 'image')">
                                    </label>

                                    <button type="button" x-show="imageUrl" @click="imageUrl = ''; imageCaption = ''" class="text-xs text-rose-500 hover:text-rose-700 font-bold">Remove</button>
                                </div>
                            </div>

                            <div x-show="imageUrl" class="text-[11px] text-purple-700 bg-purple-50 px-2.5 py-1.5 rounded-lg border border-purple-200 font-bold flex items-center gap-1">
                                <span>✓ Image Attached</span>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Status Alert --}}
                    <div x-show="uploadMsg" class="p-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs font-bold" x-text="uploadMsg"></div>
                </div>

                {{-- ===================================================================== --}}
                {{-- CARD 3: PHASE 1 — HOOK QUESTION (Interactive Visual Fields)           --}}
                {{-- ===================================================================== --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-amber-50 text-amber-700 font-black text-xs flex items-center justify-center border border-amber-200">
                                3
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Phase 1: Hook Question (Diagnostic Opener)</h3>
                                <p class="text-[11px] text-slate-500 font-medium">The authentic Kerala PSC question presented first to spark curiosity and diagnostic testing.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800">
                            Screen 0 &bull; OMR Q1
                        </span>
                    </div>

                    {{-- Question Text --}}
                    <div>
                        <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                            Hook Question Text (Malayalam &amp; English) <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            x-model="hookQ.question" 
                            rows="3" 
                            required 
                            placeholder="e.g. Considering the given list, which one falls in the category of Jovian Planet? / താഴെ തന്നിരിക്കുന്നവയിൽ നിന്നും ജോവിയൻ ഗ്രഹങ്ങളുടെ ഗണത്തിൽ ഉൾപ്പെടുത്താവുന്നതിനെ കണ്ടെത്തുക"
                            class="w-full border border-slate-200 rounded-xl p-3 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-semibold text-sm transition"></textarea>
                    </div>

                    {{-- 4 Options with Correct Answer Selector --}}
                    <div>
                        <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span>Options A, B, C, D</span>
                            <span class="text-[11px] text-slate-500 font-medium">Click the green circle to pick the Correct Answer</span>
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="(opt, optIdx) in hookQ.options" :key="optIdx">
                                <div class="flex items-center gap-2 p-2 rounded-2xl border transition"
                                     :class="hookQ.correct_index === optIdx ? 'bg-emerald-50/80 border-emerald-400 shadow-2xs' : 'bg-slate-50 border-slate-200'">
                                    
                                    <!-- Option Letter / Correct Selector Button -->
                                    <button 
                                        type="button" 
                                        @click="hookQ.correct_index = optIdx"
                                        class="w-8 h-8 rounded-xl font-black text-xs shrink-0 transition flex items-center justify-center cursor-pointer"
                                        :class="hookQ.correct_index === optIdx ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:border-emerald-300'">
                                        <span x-text="['A', 'B', 'C', 'D'][optIdx]"></span>
                                    </button>

                                    <!-- Option Text Input -->
                                    <input 
                                        type="text" 
                                        x-model="hookQ.options[optIdx]" 
                                        :placeholder="'Option ' + ['A', 'B', 'C', 'D'][optIdx]" 
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:outline-none">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- PSC Trap Warning & Detailed Explanation --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5 flex items-center gap-1.5 text-amber-800">
                                <span>⚠️ PSC Trap Warning</span>
                                <span class="text-[10px] text-slate-400 font-normal">Common mistake alert</span>
                            </label>
                            <textarea 
                                x-model="hookQ.trap_text" 
                                rows="2" 
                                placeholder="e.g. വ്യാഴം, ശനി, യുറാനസ്, നെപ്റ്റ്യൂൺ എന്നിവയാണ് ജോവിയൻ ഗ്രഹങ്ങൾ. ബുധൻ, ശുക്രൻ, ഭൂമി, ചൊവ്വ എന്നിവ ഭൗമ ഗ്രഹങ്ങളാണ് (Terrestrial)!" 
                                class="w-full border border-amber-200 rounded-xl p-2.5 text-xs text-slate-800 bg-amber-50/30 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-200 transition"></textarea>
                        </div>

                        <div>
                            <label class="block text-slate-800 text-xs font-black uppercase tracking-wider mb-1.5">
                                Official Answer Explanation
                            </label>
                            <textarea 
                                x-model="hookQ.explanation" 
                                rows="2" 
                                placeholder="e.g. സൗരയൂഥത്തിലെ വാതക ഭീമന്മാരായ ബാഹ്യഗ്രഹങ്ങളെ ജോവിയൻ ഗ്രഹങ്ങൾ എന്ന് വിളിക്കുന്നു. നൽകിയിട്ടുള്ളവയിൽ വ്യാഴം (Jupiter) ആണ് ജോവിയൻ ഗ്രഹം." 
                                class="w-full border border-slate-200 rounded-xl p-2.5 text-xs text-slate-800 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ===================================================================== --}}
                {{-- CARD 4: PHASE 2 — CORE LESSON (Markdown & SCERT Notes)                --}}
                {{-- ===================================================================== --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-700 font-black text-xs flex items-center justify-center border border-indigo-200">
                                4
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Phase 2: Core Micro-Lesson (SCERT Notes)</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Concise, scannable study notes with high-yield exam facts formatted in Markdown.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="insertLessonTemplate()" 
                                    class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-bold border border-indigo-200 transition cursor-pointer">
                                <span>+ Insert SCERT Template</span>
                            </button>
                            <button type="button" 
                                    @click="previewLesson = !previewLesson" 
                                    class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 transition cursor-pointer">
                                <span x-text="previewLesson ? '✏️ Edit Markdown' : '👁️ Preview'"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Quick Markdown Formatting Bar --}}
                    <div x-show="!previewLesson" class="flex flex-wrap items-center gap-1.5 p-1.5 bg-slate-50 rounded-xl border border-slate-200 text-xs font-bold">
                        <button type="button" @click="insertFormat('h2')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-slate-700">H2</button>
                        <button type="button" @click="insertFormat('h3')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-slate-700">H3</button>
                        <button type="button" @click="insertFormat('bold')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-slate-700 font-black">B</button>
                        <button type="button" @click="insertFormat('bullet')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-slate-700">• List</button>
                        <button type="button" @click="insertFormat('keynote')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-indigo-700">📌 Key Fact</button>
                        <button type="button" @click="insertFormat('trap')" class="px-2 py-1 bg-white hover:bg-slate-100 rounded-md border border-slate-200 text-amber-700">⚠️ Trap</button>
                    </div>

                    {{-- Markdown Textarea --}}
                    <div x-show="!previewLesson">
                        <textarea 
                            id="lesson_markdown_input"
                            name="lesson_content" 
                            x-model="lessonContent" 
                            rows="9" 
                            required 
                            placeholder="Write high-yield SCERT / PSC notes in markdown here..." 
                            class="w-full border border-slate-200 rounded-2xl p-4 text-slate-900 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 font-sans text-sm leading-relaxed transition"></textarea>
                    </div>

                    {{-- Markdown Rendered Preview --}}
                    <div x-show="previewLesson" class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 min-h-[200px] text-sm prose max-w-none text-slate-800" x-html="renderMarkdown(lessonContent)"></div>
                </div>

                {{-- ===================================================================== --}}
                {{-- CARD 5: PHASE 3 — 4 PRACTICE MCQS (Interactive Visual Question Cards) --}}
                {{-- ===================================================================== --}}
                <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-700 font-black text-xs flex items-center justify-center border border-emerald-200">
                                5
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Phase 3: 4 Reinforcement Practice MCQs</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Practice drill questions derived directly from the lesson to build mastery.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                            Screens 4 to 7 &bull; OMR Q2-Q5
                        </span>
                    </div>

                    {{-- 4 MCQ Cards Loop --}}
                    <div class="space-y-4">
                        <template x-for="(mcq, mIdx) in practiceQs" :key="mIdx">
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/40 space-y-3.5 hover:border-indigo-200 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-2xs" x-text="mIdx + 1"></span>
                                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider" x-text="'Practice MCQ #' + (mIdx + 1)"></span>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-500" x-text="'OMR Bubble: Q' + (mIdx + 2)"></span>
                                </div>

                                {{-- Question Text --}}
                                <div>
                                    <input 
                                        type="text" 
                                        x-model="mcq.question" 
                                        :placeholder="'Enter Practice Question #' + (mIdx + 1) + ' (Malayalam / English)...'" 
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-900 focus:border-indigo-500 focus:outline-none">
                                </div>

                                {{-- 4 Options with Correct Answer Selector --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <template x-for="(opt, oIdx) in mcq.options" :key="oIdx">
                                        <div class="flex items-center gap-2 p-1.5 rounded-xl border transition"
                                             :class="mcq.correct_index === oIdx ? 'bg-emerald-50 border-emerald-400' : 'bg-white border-slate-200'">
                                            
                                            <button 
                                                type="button" 
                                                @click="mcq.correct_index = oIdx"
                                                class="w-7 h-7 rounded-lg font-black text-xs shrink-0 transition flex items-center justify-center cursor-pointer"
                                                :class="mcq.correct_index === oIdx ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-emerald-100'">
                                                <span x-text="['A', 'B', 'C', 'D'][oIdx]"></span>
                                            </button>

                                            <input 
                                                type="text" 
                                                x-model="mcq.options[oIdx]" 
                                                :placeholder="'Option ' + ['A', 'B', 'C', 'D'][oIdx]" 
                                                class="w-full bg-transparent border-0 px-2 py-1 text-xs font-semibold text-slate-800 focus:outline-none">
                                        </div>
                                    </template>
                                </div>

                                {{-- Explanation --}}
                                <div>
                                    <input 
                                        type="text" 
                                        x-model="mcq.explanation" 
                                        placeholder="Malayalam / English Explanation for this practice question..." 
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:border-indigo-500 focus:outline-none">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ===================================================================== --}}
                {{-- CARD 6: PHASE 4 — CAPSTONE 5-QUESTION OMR EXAM SUMMARY                --}}
                {{-- ===================================================================== --}}
                <div class="bg-gradient-to-br from-emerald-50 via-teal-50 to-blue-50/50 rounded-3xl border-2 border-emerald-200 p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                6
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-black text-slate-900 leading-tight">Phase 4: Capstone OMR Exam &amp; Scorecard</h3>
                                <p class="text-[11px] text-slate-600 font-medium">Automatic 5-question exam with timed simulation, negative marking, and detailed answer review.</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-300">
                            5 Questions &bull; Kerala PSC Marking
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs font-bold pt-1">
                        <div class="p-3 bg-white rounded-2xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-slate-400 uppercase block">Q1</span>
                            <span class="text-emerald-700 font-black">Hook Question</span>
                            <span class="text-[10px] text-slate-500 block truncate" x-text="hookQ.question ? '✓ Ready' : 'Pending'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-slate-400 uppercase block">Q2</span>
                            <span class="text-emerald-700 font-black">Practice #1</span>
                            <span class="text-[10px] text-slate-500 block truncate" x-text="practiceQs[0]?.question ? '✓ Ready' : 'Pending'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-slate-400 uppercase block">Q3</span>
                            <span class="text-emerald-700 font-black">Practice #2</span>
                            <span class="text-[10px] text-slate-500 block truncate" x-text="practiceQs[1]?.question ? '✓ Ready' : 'Pending'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-slate-400 uppercase block">Q4</span>
                            <span class="text-emerald-700 font-black">Practice #3</span>
                            <span class="text-[10px] text-slate-500 block truncate" x-text="practiceQs[2]?.question ? '✓ Ready' : 'Pending'"></span>
                        </div>
                        <div class="p-3 bg-white rounded-2xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-slate-400 uppercase block">Q5</span>
                            <span class="text-emerald-700 font-black">Practice #4</span>
                            <span class="text-[10px] text-slate-500 block truncate" x-text="practiceQs[3]?.question ? '✓ Ready' : 'Pending'"></span>
                        </div>
                    </div>
                </div>

                {{-- ===================================================================== --}}
                {{-- BOTTOM CTA BAR                                                        --}}
                {{-- ===================================================================== --}}
                <div class="pt-4 flex items-center justify-between">
                    <a href="{{ route('admin.pscm.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:text-slate-900 font-bold text-xs sm:text-sm transition">
                        Cancel
                    </a>

                    <button 
                        type="button" 
                        @click="submitForm()" 
                        class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-95 text-white font-black text-sm shadow-lg shadow-emerald-200 transition cursor-pointer">
                        <span>💾 Save &amp; Launch Course Unit</span>
                        <span>➔</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- ALPINE.JS CONTROLLER                                                      --}}
    {{-- ========================================================================= --}}
    <script>
        function pscmAdminForm() {
            return {
                // Metadata
                title: @js(old('title', $session->title ?? '')),
                category: @js(old('category', $session->category ?? 'GK')),
                status: @js(old('status', $session->status ?? 'published')),
                accessLevel: @js(old('access_level', $session->access_level ?? 'premium')),
                price: @js(old('price', $session->price ?? 199)),

                // Media
                videoUrl: @js(old('video_url', $session->media['video_url'] ?? '')),
                imageUrl: @js(old('image_url', $session->media['image_url'] ?? '')),
                imageCaption: @js(old('image_caption', $session->media['image_caption'] ?? '')),
                uploadingVideo: false,
                uploadingImage: false,
                uploadMsg: '',

                // Phase 1: Hook Question
                hookQ: {
                    question: '',
                    options: ['', '', '', ''],
                    correct_index: 0,
                    trap_text: '',
                    explanation: ''
                },

                // Phase 2: Lesson Content
                lessonContent: @js(old('lesson_content', $session->lesson_content ?? '')),
                previewLesson: false,

                // Phase 3: 4 Practice MCQs
                practiceQs: [
                    { question: '', options: ['', '', '', ''], correct_index: 0, explanation: '' },
                    { question: '', options: ['', '', '', ''], correct_index: 0, explanation: '' },
                    { question: '', options: ['', '', '', ''], correct_index: 0, explanation: '' },
                    { question: '', options: ['', '', '', ''], correct_index: 0, explanation: '' }
                ],

                // AI Generator state
                rawHookQuestion: '',
                loadingAi: false,
                aiErrorMsg: '',
                aiSuccessMsg: '',
                showJsonImporter: false,
                rawJsonInput: '',

                initData() {
                    // Load existing hook question if available
                    let rawHook = @js(old('hook_question', $session->hook_question ?? null));
                    if (typeof rawHook === 'string') {
                        try { rawHook = JSON.parse(rawHook); } catch(e) {}
                    }
                    if (rawHook && typeof rawHook === 'object') {
                        this.hookQ.question = rawHook.question || '';
                        this.hookQ.options = Array.isArray(rawHook.options) && rawHook.options.length === 4 
                            ? rawHook.options 
                            : [rawHook.option_a || '', rawHook.option_b || '', rawHook.option_c || '', rawHook.option_d || ''];
                        this.hookQ.correct_index = typeof rawHook.correct_index === 'number' 
                            ? rawHook.correct_index 
                            : (['A', 'B', 'C', 'D'].indexOf(rawHook.correct_option || 'A') !== -1 ? ['A', 'B', 'C', 'D'].indexOf(rawHook.correct_option) : 0);
                        this.hookQ.trap_text = rawHook.trap_text || rawHook.trap_warning || '';
                        this.hookQ.explanation = rawHook.explanation || '';
                    }

                    // Load existing practice MCQs if available
                    let rawPractice = @js(old('practice_mcqs', $session->practice_mcqs ?? null));
                    if (typeof rawPractice === 'string') {
                        try { rawPractice = JSON.parse(rawPractice); } catch(e) {}
                    }
                    if (Array.isArray(rawPractice) && rawPractice.length > 0) {
                        this.practiceQs = rawPractice.slice(0, 4).map(q => ({
                            question: q.question || '',
                            options: Array.isArray(q.options) && q.options.length === 4 
                                ? q.options 
                                : [q.option_a || '', q.option_b || '', q.option_c || '', q.option_d || ''],
                            correct_index: typeof q.correct_index === 'number' 
                                ? q.correct_index 
                                : (['A', 'B', 'C', 'D'].indexOf(q.correct_option || 'A') !== -1 ? ['A', 'B', 'C', 'D'].indexOf(q.correct_option) : 0),
                            explanation: q.explanation || ''
                        }));
                    }
                    while (this.practiceQs.length < 4) {
                        this.practiceQs.push({ question: '', options: ['', '', '', ''], correct_index: 0, explanation: '' });
                    }
                },

                async uploadMediaFile(event, type) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (type === 'video') this.uploadingVideo = true;
                    if (type === 'image') this.uploadingImage = true;
                    this.uploadMsg = `Uploading ${file.name}...`;

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('_token', document.querySelector('input[name="_token"]').value);

                    try {
                        const response = await fetch('{{ route('admin.media.store') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success && data.media && data.media.url) {
                            if (type === 'video') {
                                this.videoUrl = data.media.url;
                            } else {
                                this.imageUrl = data.media.url;
                                if (!this.imageCaption) {
                                    this.imageCaption = data.media.name;
                                }
                            }
                            this.uploadMsg = `✅ Uploaded successfully: ${data.media.name}`;
                            setTimeout(() => { this.uploadMsg = ''; }, 4000);
                        } else {
                            alert('Upload failed: ' + (data.message || 'Error occurred.'));
                            this.uploadMsg = '';
                        }
                    } catch (err) {
                        alert('Upload failed: ' + err.message);
                        this.uploadMsg = '';
                    } finally {
                        if (type === 'video') this.uploadingVideo = false;
                        if (type === 'image') this.uploadingImage = false;
                        event.target.value = '';
                    }
                },

                async generateAI() {
                    if (!this.rawHookQuestion.trim()) {
                        this.aiErrorMsg = 'Please paste your PSC question or screenshot text first!';
                        return;
                    }

                    this.loadingAi = true;
                    this.aiErrorMsg = '';
                    this.aiSuccessMsg = '';

                    try {
                        const response = await fetch('{{ route('admin.pscm.generate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ hook_question: this.rawHookQuestion })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.populateData(data);
                            this.aiSuccessMsg = '✨ Success! Entire 4-phase unit auto-generated. Review and customize fields below!';
                            setTimeout(() => { this.aiSuccessMsg = ''; }, 6000);
                        } else {
                            this.aiErrorMsg = data.message || 'AI Generation failed. You can paste the JSON response directly below.';
                        }
                    } catch (e) {
                        this.aiErrorMsg = e.message;
                    } finally {
                        this.loadingAi = false;
                    }
                },

                importRawJson() {
                    try {
                        const data = JSON.parse(this.rawJsonInput);
                        this.populateData(data);
                        this.showJsonImporter = false;
                        this.rawJsonInput = '';
                        this.aiSuccessMsg = '✅ Loaded JSON into all fields successfully!';
                        setTimeout(() => { this.aiSuccessMsg = ''; }, 4000);
                    } catch(e) {
                        alert('Invalid JSON: ' + e.message);
                    }
                },

                populateData(data) {
                    if (data.suggested_title && !this.title) {
                        this.title = data.suggested_title;
                    }
                    if (data.category) {
                        this.category = data.category;
                    }
                    if (data.lesson_content) {
                        this.lessonContent = data.lesson_content;
                    }

                    const hook = data.hook_question || data.hook_question_json;
                    if (hook) {
                        this.hookQ.question = hook.question || '';
                        if (Array.isArray(hook.options) && hook.options.length === 4) {
                            this.hookQ.options = [...hook.options];
                        }
                        this.hookQ.correct_index = typeof hook.correct_index === 'number' ? hook.correct_index : 0;
                        this.hookQ.trap_text = hook.trap_text || hook.trap_warning || '';
                        this.hookQ.explanation = hook.explanation || '';
                    }

                    if (Array.isArray(data.practice_mcqs) && data.practice_mcqs.length > 0) {
                        this.practiceQs = data.practice_mcqs.slice(0, 4).map(q => ({
                            question: q.question || '',
                            options: Array.isArray(q.options) && q.options.length === 4 ? [...q.options] : ['', '', '', ''],
                            correct_index: typeof q.correct_index === 'number' ? q.correct_index : 0,
                            explanation: q.explanation || ''
                        }));
                    }
                },

                insertLessonTemplate() {
                    const tpl = `## 📌 പ്രധാന പോയിന്റുകൾ (High-Yield PSC Facts)\n\n- **നിർവ്വചനം:** പ്രധാന ആശയവും നിർവ്വചനവും ഇവിടെ.\n- **പ്രത്യേകതകൾ:** SCERT പാഠപുസ്തകത്തിലെ വസ്തുതകൾ.\n\n> ⚠️ **പരീക്ഷാ ട്രാപ്പ്:** കുട്ടികൾക്ക് പൊതുവെ തെറ്റുന്ന ആശയക്കുഴപ്പങ്ങൾ ഇവിടെ.\n\n> 🎯 **മുൻവർഷ ചോദ്യം:** PSC പരീക്ഷകളിൽ ആവർത്തിച്ച പോയിന്റുകൾ.`;
                    this.lessonContent = (this.lessonContent ? this.lessonContent + '\n\n' : '') + tpl;
                },

                insertFormat(type) {
                    const el = document.getElementById('lesson_markdown_input');
                    if (!el) return;
                    const start = el.selectionStart;
                    const end = el.selectionEnd;
                    const val = el.value || '';
                    const sel = val.substring(start, end);

                    let prefix = '', suffix = '';
                    if (type === 'h2') { prefix = '\n## '; suffix = '\n'; }
                    else if (type === 'h3') { prefix = '\n### '; suffix = '\n'; }
                    else if (type === 'bold') { prefix = '**'; suffix = '**'; }
                    else if (type === 'bullet') { prefix = '\n- '; suffix = ''; }
                    else if (type === 'keynote') { prefix = '\n> 📌 **Key Fact:** '; suffix = '\n'; }
                    else if (type === 'trap') { prefix = '\n> ⚠️ **PSC Trap:** '; suffix = '\n'; }

                    el.value = val.substring(0, start) + prefix + (sel || 'text') + suffix + val.substring(end);
                    this.lessonContent = el.value;
                    el.focus();
                },

                renderMarkdown(text) {
                    if (!text) return '<p class="text-slate-400 italic">No lesson content yet.</p>';
                    if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
                        return marked.parse(text);
                    }
                    return text.replace(/\n/g, '<br>');
                },

                copyPromptTemplate() {
                    const prompt = `You are an expert Kerala PSC exam content creator.
Create a complete micro-learning session for this PSC question:
"${this.rawHookQuestion || '[PASTE PSC QUESTION HERE]'}"

Return in strict JSON:
{
  "category": "Geography",
  "suggested_title": "Topic Title",
  "hook_question": {
    "question": "...",
    "options": ["A", "B", "C", "D"],
    "correct_index": 0,
    "trap_text": "...",
    "explanation": "..."
  },
  "lesson_content": "## Markdown SCERT notes with bullet points...",
  "practice_mcqs": [
    { "question": "...", "options": ["A", "B", "C", "D"], "correct_index": 0, "explanation": "..." },
    { "question": "...", "options": ["A", "B", "C", "D"], "correct_index": 0, "explanation": "..." },
    { "question": "...", "options": ["A", "B", "C", "D"], "correct_index": 0, "explanation": "..." },
    { "question": "...", "options": ["A", "B", "C", "D"], "correct_index": 0, "explanation": "..." }
  ]
}`;
                    navigator.clipboard.writeText(prompt);
                    alert('📋 AI Prompt Template copied to clipboard! You can paste it into Gemini or ChatGPT.');
                },

                submitForm() {
                    if (!this.title.trim()) {
                        alert('Please provide a Unit Title!');
                        return;
                    }
                    if (!this.hookQ.question.trim()) {
                        alert('Please provide the Hook Question text!');
                        return;
                    }
                    document.getElementById('pscm-unit-form').submit();
                }
            };
        }
    </script>

@endsection
