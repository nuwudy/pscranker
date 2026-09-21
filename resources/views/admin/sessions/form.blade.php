@extends('layouts.admin')

@section('title', ($isEdit ? 'Edit Session: ' . $session->title : 'Create New Modular Session') . ' — PSCRanker Admin')
@section('page_title', $isEdit ? 'Edit Modular Session' : 'Modular Session Studio')
@section('page_subtitle', 'Track ➔ Session ➔ Sequential Units ➔ Stackable Blocks & Capstone OMR')

@section('content')
<div 
    x-data="adminSessionBuilder({
        contents: @js($contents),
        diagnostic: @js($diagnosticQuestions->first()),
        reinforcement: @js($reinforcementQuestions->values()),
        omr: @js($omrQuestions->values()),
        featureImage: @js(old('feature_image', $session->feature_image ?? '')),
        featureVideo: @js(old('feature_video', $session->feature_video ?? '')),
        categoryId: @js(old('category_id', $session->category_id ?? (request('category_id') ?? ''))),
        nextOrdersByCategory: @js($nextOrdersByCategory ?? []),
        defaultNextOrder: @js($defaultNextOrder ?? 1),
        order: @js(old('order', $session->order ?? null)),
        inGeneralStream: @js((bool) old('in_general_stream', $session->in_general_stream ?? true)),
        generalStreamOrder: @js(old('general_stream_order', $session->general_stream_order ?? null)),
        nextTrainOrder: @js($nextTrainOrder ?? 1),
        categories: @js($categories),
        passMark: @js(old('pass_mark', $session->pass_mark ?? 50)),
        timeLimitMinutes: @js(old('time_limit_minutes', $session->time_limit_minutes ?? 10)),
        accessTier: @js(old('access_level', $session->access_level ?? ($session->is_premium ? 'premium' : ($isEdit ? 'guest' : 'premium')))),
        sessionPrice: @js(old('price', $session->price ?? 199)),
        isActive: @js((bool) old('is_active', $session->is_active ?? true)),
        isEdit: @js($isEdit)
    })"
    class="max-w-6xl mx-auto space-y-6"
>

    <!-- ================================================================= -->
    <!-- STUDIO TOP ACTION BAR & MODE SWITCHER                             -->
    <!-- ================================================================= -->
    <div class="sticky top-0 z-40 bg-slate-100/90 backdrop-blur-md py-3 -my-3 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 mb-6">
        <!-- Breadcrumb & Mode Selector -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sessions.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-[#0052FF] hover:border-blue-300 transition shadow-2xs text-xs font-bold flex items-center gap-1">
                <span>←</span>
                <span class="hidden sm:inline">Sessions</span>
            </a>
            
            <div class="flex items-center bg-white p-1 rounded-2xl border border-slate-200 shadow-2xs">
                <button 
                    type="button" 
                    @click="studioTab = 'builder'"
                    :class="studioTab === 'builder' ? 'bg-[#0052FF] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <span>🛠️</span>
                    <span>Builder Studio</span>
                </button>
                <button 
                    type="button" 
                    @click="studioTab = 'preview'"
                    :class="studioTab === 'preview' ? 'bg-[#0052FF] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                >
                    <span>👁️</span>
                    <span>Live Learner Preview</span>
                </button>
            </div>
        </div>

        <!-- Right Controls: Status Toggle & Primary Save -->
        <div class="flex items-center gap-2.5">
            <!-- Draft / Published Toggle -->
            <button 
                type="button"
                @click="isActive = !isActive"
                :class="isActive ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-slate-200 border-slate-300 text-slate-600'"
                class="px-3 py-1.5 rounded-xl text-xs font-black border transition flex items-center gap-2 cursor-pointer shadow-2xs"
            >
                <span class="w-2 h-2 rounded-full" :class="isActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                <span x-text="isActive ? 'Status: Published' : 'Status: Draft'"></span>
            </button>

            <!-- Media Library Quick Link -->
            <a 
                href="{{ route('admin.media.index') }}" 
                target="_blank"
                class="hidden md:flex px-3 py-2 bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-black rounded-xl transition border border-purple-200 items-center gap-1.5 shadow-2xs"
            >
                <span>📁 Media Bank ↗</span>
            </a>

            @if(!empty($session->slug))
                <!-- View Live Session Buttons -->
                <div class="flex items-center gap-1.5">
                    <a 
                        href="{{ route('session.show', $session->slug) }}" 
                        target="_blank"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                        title="View the live session progression stepper"
                    >
                        <span>👁️ View Live ↗</span>
                    </a>
                    <a 
                        href="{{ route('session.show', ['slug' => $session->slug, 'preview' => 'finished']) }}" 
                        target="_blank"
                        class="hidden sm:flex px-3 py-2 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 font-bold text-xs rounded-xl transition items-center gap-1 cursor-pointer"
                        title="View the finished session scorecard"
                    >
                        <span>🏁 Finished View ↗</span>
                    </a>
                </div>
            @endif

            <!-- Save & Publish Button -->
            <button 
                type="button" 
                @click="submitMainForm()"
                class="px-5 py-2 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer"
            >
                <span>💾</span>
                <span>{{ $isEdit ? 'Update Session' : 'Save & Publish' }}</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl text-xs font-bold text-emerald-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            @if(!empty($session->slug) || session('view_url'))
                <div class="flex items-center gap-2 shrink-0">
                    <a 
                        href="{{ session('view_url', route('session.show', $session->slug ?? '')) }}" 
                        target="_blank"
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-xs flex items-center gap-1.5 transition"
                    >
                        <span>👁️ View Live Session ➔</span>
                    </a>
                    <a 
                        href="{{ session('finished_url', route('session.show', ['slug' => $session->slug ?? '', 'preview' => 'finished'])) }}" 
                        target="_blank"
                        class="px-3 py-1.5 rounded-xl bg-white border border-emerald-400 text-emerald-800 hover:bg-emerald-100 font-black text-xs flex items-center gap-1.5 transition"
                    >
                        <span>🏁 Finished Scorecard ➔</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-300 rounded-2xl text-xs font-bold text-red-900">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ================================================================= -->
    <!-- TAB 1: BUILDER STUDIO                                             -->
    <!-- ================================================================= -->
    <div x-show="studioTab === 'builder'" class="space-y-8">
        <form 
            id="admin-session-form"
            action="{{ $isEdit ? route('admin.sessions.update', $session) : route('admin.sessions.store') }}" 
            method="POST"
            enctype="multipart/form-data"
            @submit="prepareFormData()"
            class="space-y-8"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Hidden JSON Payloads for Syncing Database -->
            <input type="hidden" name="contents_json" :value="JSON.stringify(serializedContents)">
            <input type="hidden" name="questions_json" :value="JSON.stringify(serializedQuestions)">
            <input type="hidden" name="creation_mode" value="manual">
            <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">

            <!-- ============================================================= -->
            <!-- CARD 1: SESSION ESSENTIALS (Compact, Clean & Modern)          -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-blue-50 text-[#0052FF] font-black text-xs flex items-center justify-center border border-blue-200">
                            1
                        </span>
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">
                                Session Essentials &amp; Subject Track
                            </h2>
                            <p class="text-[11px] text-slate-500 font-medium">Session title, subject classification, and cover visual.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Session Title English -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                            Session Title (English) *
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            x-model="sessionTitle"
                            required 
                            placeholder="e.g. Sree Narayana Guru & Aruvipuram Prathishta"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold rounded-xl border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                        >
                    </div>

                    <!-- Session Title Malayalam -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                            Session Title (Malayalam)
                        </label>
                        <input 
                            type="text" 
                            name="title_malayalam" 
                            x-model="sessionTitleMl"
                            placeholder="e.g. ശ്രീനാരായണഗുരുവും അരുവിപ്പുറം പ്രതിഷ്ഠയും"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold rounded-xl border border-slate-300 focus:border-[#0052FF] focus:outline-none font-['Noto_Sans_Malayalam']"
                        >
                    </div>

                    <!-- PSC Subject Track Selector -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                                PSC Subject Track *
                            </label>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="showQuickCategoryModal = true" 
                                    class="text-[10px] font-black text-[#0052FF] hover:underline flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200 cursor-pointer"
                                >
                                    <span>+ New Track</span>
                                </button>
                                <span class="text-[10px] font-bold text-blue-600" x-show="categoryId && nextOrdersByCategory[categoryId]">
                                    Next: Session #<span x-text="nextOrdersByCategory[categoryId]"></span>
                                </span>
                            </div>
                        </div>
                        <select 
                            name="category_id" 
                            x-model="categoryId"
                            @change="onCategoryChange($event.target.value)"
                            class="w-full px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 focus:border-[#0052FF] focus:outline-none bg-white"
                            required
                        >
                            <option value="">-- Select PSC Subject Track --</option>
                            <template x-for="cat in availableCategories" :key="cat.id">
                                <option :value="cat.id" :selected="cat.id == categoryId" x-text="cat.name + (cat.name_malayalam ? (' (' + cat.name_malayalam + ')') : '')"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Cover Media Selector (with Direct Upload & Library) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                                Session Cover Media
                            </label>
                            <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg">
                                <button 
                                    type="button" 
                                    @click="featureMediaTab = 'video'" 
                                    :class="featureMediaTab === 'video' ? 'bg-white text-blue-700 font-black shadow-2xs' : 'text-slate-600 font-bold'"
                                    class="px-2 py-0.5 rounded text-[10px] cursor-pointer"
                                >
                                    🎥 Video
                                </button>
                                <button 
                                    type="button" 
                                    @click="featureMediaTab = 'image'" 
                                    :class="featureMediaTab === 'image' ? 'bg-white text-blue-700 font-black shadow-2xs' : 'text-slate-600 font-bold'"
                                    class="px-2 py-0.5 rounded text-[10px] cursor-pointer"
                                >
                                    🖼️ Image
                                </button>
                            </div>
                        </div>

                        <!-- Video Cover Inputs -->
                        <div x-show="featureMediaTab === 'video'" class="flex items-center gap-2">
                            <input 
                                type="text" 
                                name="feature_video" 
                                x-model="featureVideo" 
                                placeholder="Paste YouTube / Vimeo / MP4 URL" 
                                class="flex-1 px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 bg-white"
                            >
                            <label class="px-2.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-black rounded-xl hover:bg-emerald-100 cursor-pointer shrink-0 transition flex items-center gap-1">
                                <input type="file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="hidden" @change="uploadCoverMedia($event, 'video')">
                                <span x-show="!_coverUploading">📤 Upload Video</span>
                                <span x-show="_coverUploading" class="inline-flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                                    <span>Uploading...</span>
                                </span>
                            </label>
                            <button 
                                type="button" 
                                @click="openMediaPickerForCover('video')" 
                                class="px-2.5 py-2 bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl hover:bg-purple-100 cursor-pointer shrink-0"
                            >
                                📂 Library
                            </button>
                        </div>

                        <!-- Image Cover Inputs -->
                        <div x-show="featureMediaTab === 'image'" class="flex items-center gap-2">
                            <input 
                                type="text" 
                                name="feature_image" 
                                x-model="featureImage" 
                                placeholder="Paste image URL (e.g. /storage/...)" 
                                class="flex-1 px-3 py-2 text-xs font-mono rounded-xl border border-slate-300 bg-white"
                            >
                            <label class="px-2.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-black rounded-xl hover:bg-emerald-100 cursor-pointer shrink-0 transition flex items-center gap-1">
                                <input type="file" accept="image/*" class="hidden" @change="uploadCoverMedia($event, 'image')">
                                <span x-show="!_coverUploading">📤 Upload Image</span>
                                <span x-show="_coverUploading" class="inline-flex items-center gap-1">
                                    <span class="w-2.5 h-2.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                                    <span>Uploading...</span>
                                </span>
                            </label>
                            <button 
                                type="button" 
                                @click="openMediaPickerForCover('image')" 
                                class="px-2.5 py-2 bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl hover:bg-purple-100 cursor-pointer shrink-0"
                            >
                                📂 Library
                            </button>
                            <template x-if="featureImage">
                                <img :src="featureImage" class="w-9 h-8 object-cover rounded-lg border border-slate-300 shrink-0">
                            </template>
                        </div>
                    </div>
                </div>

                <!-- ============================================================= -->
                <!-- 3-TIER MONETIZATION SYSTEM (TOGGLES: FREE / REGISTERED / PAID)-->
                <!-- ============================================================= -->
                <div class="mt-6 pt-5 border-t border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="block text-xs font-black text-slate-900 uppercase tracking-wide">
                                    Monetization &amp; Access Tier *
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                    Paid by Default
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                Toggle access rules for this session. Candidate enrollment depends on the active tier.
                            </p>
                        </div>

                        <!-- Active Tier Pill -->
                        <span 
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border shadow-2xs self-start sm:self-auto"
                            :class="{
                                'bg-emerald-100 text-emerald-900 border-emerald-300': accessTier === 'guest',
                                'bg-blue-100 text-blue-900 border-blue-300': accessTier === 'registered',
                                'bg-amber-100 text-amber-950 border-amber-300': accessTier === 'premium'
                            }"
                        >
                            <span x-text="accessTier === 'guest' ? '🟢 Tier 1: Free (Public)' : (accessTier === 'registered' ? '🔵 Tier 2: Free (Registered)' : '👑 Tier 3: Paid (PRO Pass)')"></span>
                        </span>
                    </div>

                    <!-- 3-Way Segmented Toggle Switch -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200 select-none">
                        
                        <!-- Toggle 1: Free (Public) -->
                        <button 
                            type="button" 
                            @click="setAccessTier('guest')" 
                            :class="accessTier === 'guest' ? 'bg-white text-emerald-950 shadow-sm border-emerald-400 font-black ring-2 ring-emerald-400/30' : 'text-slate-600 hover:text-slate-900 border-transparent font-bold hover:bg-white/50'"
                            class="flex items-center gap-3 px-3.5 py-3 rounded-xl border text-xs transition cursor-pointer text-left"
                        >
                            <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base shrink-0 border border-emerald-200">
                                🟢
                            </span>
                            <div class="min-w-0">
                                <div class="leading-tight font-black text-xs sm:text-sm">Free (Public)</div>
                                <div class="text-[10px] text-slate-500 font-medium truncate">No login required</div>
                            </div>
                        </button>

                        <!-- Toggle 2: Registered (Free Account) -->
                        <button 
                            type="button" 
                            @click="setAccessTier('registered')" 
                            :class="accessTier === 'registered' ? 'bg-white text-blue-950 shadow-sm border-blue-400 font-black ring-2 ring-blue-400/30' : 'text-slate-600 hover:text-slate-900 border-transparent font-bold hover:bg-white/50'"
                            class="flex items-center gap-3 px-3.5 py-3 rounded-xl border text-xs transition cursor-pointer text-left"
                        >
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-base shrink-0 border border-blue-200">
                                🔵
                            </span>
                            <div class="min-w-0">
                                <div class="leading-tight font-black text-xs sm:text-sm">Registered</div>
                                <div class="text-[10px] text-slate-500 font-medium truncate">Free with candidate login</div>
                            </div>
                        </button>

                        <!-- Toggle 3: Paid (PRO Pass) [DEFAULT] -->
                        <button 
                            type="button" 
                            @click="setAccessTier('premium')" 
                            :class="accessTier === 'premium' ? 'bg-gradient-to-r from-amber-50 via-yellow-50 to-orange-50 text-amber-950 shadow-sm border-amber-400 font-black ring-2 ring-amber-400/40' : 'text-slate-600 hover:text-slate-900 border-transparent font-bold hover:bg-white/50'"
                            class="flex items-center gap-3 px-3.5 py-3 rounded-xl border text-xs transition cursor-pointer text-left"
                        >
                            <span class="w-8 h-8 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-base shrink-0 border border-amber-300">
                                👑
                            </span>
                            <div class="min-w-0">
                                <div class="leading-tight font-black text-xs sm:text-sm flex items-center gap-1.5">
                                    <span>Paid (PRO)</span>
                                    <span class="px-1.5 py-0.5 rounded bg-amber-300/80 text-amber-950 font-black text-[9px] uppercase">Default</span>
                                </div>
                                <div class="text-[10px] text-slate-500 font-medium truncate">PRO pass / individual fee</div>
                            </div>
                        </button>

                    </div>

                    <!-- Hidden Inputs submitted with form to Laravel backend -->
                    <input type="hidden" name="access_level" :value="accessTier">
                    <input type="hidden" name="is_premium" :value="accessTier === 'premium' ? 1 : 0">

                    <!-- Dynamic Pricing Panel when Paid Tier is active -->
                    <div x-show="accessTier === 'premium'" x-transition class="mt-3.5 p-4 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="font-black text-amber-950">Session Price (₹):</span>
                            <div class="relative w-32">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 font-black">₹</span>
                                <input 
                                    type="number" 
                                    name="price" 
                                    x-model="sessionPrice" 
                                    min="0" 
                                    step="1"
                                    placeholder="199" 
                                    class="w-full pl-7 pr-3 py-1.5 bg-white border-2 border-amber-300 rounded-xl font-black text-amber-950 focus:border-amber-500 focus:outline-none text-sm"
                                >
                            </div>
                            <span class="text-[11px] text-slate-500 font-medium">One-time purchase / PRO bundle</span>
                        </div>

                        <!-- Quick Presets -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] text-amber-900 font-bold">Quick Presets:</span>
                            <template x-for="p in [49, 99, 149, 199, 299]" :key="p">
                                <button 
                                    type="button" 
                                    @click="sessionPrice = p" 
                                    :class="sessionPrice == p ? 'bg-amber-600 text-white font-black shadow-xs' : 'bg-white text-amber-950 border border-amber-300 font-bold hover:bg-amber-100'"
                                    class="px-2.5 py-1 rounded-lg text-xs transition cursor-pointer active:scale-95"
                                    x-text="'₹' + p"
                                ></button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Advanced Settings Drawer -->
                <div class="mt-5 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="showAdvanced = !showAdvanced" 
                        class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 cursor-pointer py-1"
                    >
                        <span x-text="showAdvanced ? '▼' : '▶'"></span>
                        <span>⚙️ Advanced Settings (URL Slug, Sequence Order, XP Reward, Mixed Practice Train)</span>
                    </button>

                    <div x-show="showAdvanced" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <!-- URL Slug -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wide mb-1">URL Slug</label>
                            <input 
                                type="text" 
                                name="slug" 
                                value="{{ old('slug', $session->slug) }}" 
                                placeholder="sree-narayana-guru-prathishta"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white font-mono text-xs"
                            >
                        </div>

                        <!-- Sequence Order -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-bold text-slate-700 uppercase tracking-wide">Sequence Order</label>
                                <button type="button" @click="updateAutoOrder()" class="text-[10px] text-[#0052FF] font-bold hover:underline">Auto</button>
                            </div>
                            <input 
                                type="number" 
                                name="order" 
                                x-model="order" 
                                min="1"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white font-bold"
                            >
                        </div>

                        <!-- XP Reward -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wide mb-1">XP Reward</label>
                            <input 
                                type="number" 
                                name="xp_reward" 
                                value="{{ old('xp_reward', $session->xp_reward ?? 250) }}" 
                                min="0"
                                class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-amber-600 font-bold"
                            >
                        </div>

                        <!-- Mixed Practice Train -->
                        <div class="sm:col-span-3 flex items-center gap-2 pt-2 border-t border-slate-200/60">
                            <input 
                                type="checkbox" 
                                id="in_general_stream" 
                                name="in_general_stream" 
                                value="1" 
                                x-model="inGeneralStream"
                                class="w-4 h-4 rounded text-[#0052FF]"
                            >
                            <label for="in_general_stream" class="font-bold text-slate-800 cursor-pointer">
                                Include in Mixed Practice Train (General Stream)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            </div>

            <!-- ============================================================= -->
            <!-- CARD 2: SEQUENTIAL UNITS & MODULAR LEGO-BLOCK STUDIO          -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-6 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-purple-50 text-purple-700 font-black text-xs flex items-center justify-center border border-purple-200">
                            2
                        </span>
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight">
                                Sequential Units &amp; Stackable Lego Blocks
                            </h2>
                            <p class="text-[11px] text-slate-500 font-medium">
                                Structure: <span class="font-bold text-purple-700">Track ➔ Session ➔ Sequential Units ➔ Stackable Blocks</span>.
                            </p>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        @click="addUnit()" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 active:scale-95 text-white font-black text-xs rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer shrink-0"
                    >
                        <span>＋ Add Sequential Unit</span>
                    </button>
                </div>

                <!-- Units List Container -->
                <div class="space-y-8">
                    <template x-for="(unit, uIdx) in units" :key="unit.id">
                        <div class="relative">
                            <div class="p-4 sm:p-5 rounded-2xl border-2 border-purple-200 bg-purple-50/15 space-y-4 transition hover:border-purple-300">
                            
                            <!-- Unit Header Bar -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-purple-200/60">
                                <div class="flex items-center gap-2.5 flex-1">
                                    <span class="px-2.5 py-1 rounded-lg bg-purple-700 text-white font-black text-xs uppercase tracking-wider shrink-0" x-text="'UNIT ' + (uIdx + 1)"></span>
                                    <input 
                                        type="text" 
                                        x-model="unit.title" 
                                        placeholder="Unit Title (e.g. Unit 1: Hook Challenge & Core Notes)" 
                                        class="flex-1 px-3 py-1.5 rounded-lg border border-purple-200 bg-white font-bold text-xs text-slate-900 focus:border-purple-600 focus:outline-none"
                                    >
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-auto">
                                    <button 
                                        type="button" 
                                        @click="moveUnitUp(uIdx)" 
                                        :disabled="uIdx === 0" 
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30 text-xs font-bold transition flex items-center justify-center cursor-pointer"
                                        title="Move Unit Up"
                                    >▲</button>
                                    <button 
                                        type="button" 
                                        @click="moveUnitDown(uIdx)" 
                                        :disabled="uIdx === units.length - 1" 
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 disabled:opacity-30 text-xs font-bold transition flex items-center justify-center cursor-pointer"
                                        title="Move Unit Down"
                                    >▼</button>
                                    <button 
                                        type="button" 
                                        @click="removeUnit(uIdx)" 
                                        :disabled="units.length <= 1" 
                                        class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 text-xs font-bold transition disabled:opacity-30 cursor-pointer"
                                        title="Delete Unit"
                                    >✕ Remove Unit</button>
                                </div>
                            </div>

                            <!-- Blocks Stacked inside this Unit -->
                            <div class="space-y-3 pl-1 sm:pl-3">
                                <template x-if="unit.blocks.length === 0">
                                    <div class="p-6 text-center bg-white rounded-2xl border-2 border-dashed border-purple-200/90 space-y-3 shadow-2xs">
                                        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-700 font-bold flex items-center justify-center text-lg mx-auto border border-purple-200">
                                            🧱
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-black text-slate-800">This unit starts empty — no unwanted default blocks</h4>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Click any block type below to stack your first component:</p>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="addBlockToUnit(uIdx, 'hook_mcq')" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-black rounded-xl transition flex items-center gap-1 cursor-pointer">
                                                <span>🎣 + Hook MCQ</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'practice_mcq')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-black rounded-xl transition flex items-center gap-1 cursor-pointer">
                                                <span>🎯 + Practice MCQ</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'text')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>📝 + Text Block</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'image')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>🖼️ + Image</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'audio')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>🎙️ + Audio</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'video')" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>🎥 + Video</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'map_globe')" class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>🌐 + 3D Map</span>
                                            </button>
                                            <button type="button" @click="addBlockToUnit(uIdx, 'html')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                                                <span>⚡ + HTML</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-for="(block, bIdx) in unit.blocks" :key="block.id">
                                    <div class="p-4 rounded-xl border border-slate-200 bg-white shadow-2xs space-y-3 transition hover:border-slate-300">
                                        
                                        <!-- Block Card Header -->
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs">
                                            <div class="flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center font-mono" x-text="bIdx + 1"></span>
                                                <span class="font-black px-2 py-0.5 rounded text-[10px] uppercase tracking-wider"
                                                    :class="{
                                                        'bg-purple-100 text-purple-900 border border-purple-200': block.type === 'hook_mcq',
                                                        'bg-blue-100 text-blue-900 border border-blue-200': block.type === 'practice_mcq',
                                                        'bg-emerald-100 text-emerald-900 border border-emerald-200': block.type === 'text',
                                                        'bg-amber-100 text-amber-900 border border-amber-200': block.type === 'image',
                                                        'bg-indigo-100 text-indigo-900 border border-indigo-200': block.type === 'audio',
                                                        'bg-red-100 text-red-900 border border-red-200': block.type === 'video',
                                                        'bg-teal-100 text-teal-900 border border-teal-200': block.type === 'map_globe',
                                                        'bg-slate-100 text-slate-900 border border-slate-200': block.type === 'html'
                                                    }"
                                                    x-text="getBlockLabel(block.type)"
                                                ></span>
                                            </div>

                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="moveBlockUp(uIdx, bIdx)" :disabled="bIdx === 0" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-[10px] disabled:opacity-30 cursor-pointer">▲</button>
                                                <button type="button" @click="moveBlockDown(uIdx, bIdx)" :disabled="bIdx === unit.blocks.length - 1" class="w-6 h-6 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-[10px] disabled:opacity-30 cursor-pointer">▼</button>
                                                <button type="button" @click="removeBlock(uIdx, bIdx)" class="w-6 h-6 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold cursor-pointer">✕</button>
                                            </div>
                                        </div>

                                        <!-- BLOCK TYPE 1: HOOK MCQ (OPENER) -->
                                        <template x-if="block.type === 'hook_mcq'">
                                            <div class="space-y-3 text-xs bg-purple-50/40 p-3.5 rounded-xl border border-purple-200">
                                                <div class="flex items-center justify-between pb-1 border-b border-purple-200">
                                                    <span class="font-bold text-purple-900">🎣 Hook Concept Question (Pre-Test Curiosity)</span>
                                                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">⚡ Auto-feeds to Capstone OMR</span>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Question (English) *</label>
                                                        <input type="text" x-model="block.content_data.question_text" placeholder="e.g. Which year was the Aruvipuram Prathishta held?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Question (Malayalam)</label>
                                                        <input type="text" x-model="block.content_data.question_text_malayalam" placeholder="e.g. അരുവിപ്പുറം പ്രതിഷ്ഠ നടന്ന വർഷം ഏത്?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam'] bg-white">
                                                    </div>
                                                </div>

                                                <!-- 4 Options -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_a" placeholder="Option A" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_b" placeholder="Option B" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_c" placeholder="Option C" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_d" placeholder="Option D" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                </div>

                                                <!-- Correct Option & Trap Warning -->
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Correct Option *</label>
                                                        <select x-model="block.content_data.correct_option" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-bold">
                                                            <option value="A">Option A</option>
                                                            <option value="B">Option B</option>
                                                            <option value="C">Option C</option>
                                                            <option value="D">Option D</option>
                                                        </select>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="font-bold text-amber-800 block mb-1">⚠️ PSC Trap Warning Alert</label>
                                                        <input type="text" x-model="block.content_data.trap_warning" placeholder="Beware: Shivaratri vs Sree Narayana Jayanti" class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-950 font-medium">
                                                    </div>
                                                </div>

                                                <!-- Explanations -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Explanation (English)</label>
                                                        <textarea x-model="block.content_data.explanation" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs"></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Explanation (Malayalam)</label>
                                                        <textarea x-model="block.content_data.explanation_malayalam" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-['Noto_Sans_Malayalam']"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 2: PRACTICE MCQ (INLINE UNIT QUIZ) -->
                                        <template x-if="block.type === 'practice_mcq'">
                                            <div class="space-y-3 text-xs bg-blue-50/40 p-3.5 rounded-xl border border-blue-200">
                                                <div class="flex items-center justify-between pb-1 border-b border-blue-200">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-blue-900">🎯 Practice MCQ Block (Instant Inline Feedback)</span>
                                                        <span class="text-[10px] font-bold text-blue-800 bg-blue-100 px-2 py-0.5 rounded">+1.00 / -0.33 Mark</span>
                                                    </div>
                                                    <span class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">⚡ Auto-feeds to Capstone OMR</span>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Question (English) *</label>
                                                        <input type="text" x-model="block.content_data.question_text" placeholder="e.g. Which river flows through Aruvipuram?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Question (Malayalam)</label>
                                                        <input type="text" x-model="block.content_data.question_text_malayalam" placeholder="അരുവിപ്പുറം ഏത് നദിയുടെ തീരത്താണ്?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam'] bg-white">
                                                    </div>
                                                </div>

                                                <!-- 4 Options -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_a" placeholder="Option A" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_b" placeholder="Option B" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_c" placeholder="Option C" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <input type="text" x-model="block.content_data.option_d" placeholder="Option D" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                </div>

                                                <!-- Correct Option & Trap Warning -->
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Correct Option *</label>
                                                        <select x-model="block.content_data.correct_option" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-bold">
                                                            <option value="A">Option A</option>
                                                            <option value="B">Option B</option>
                                                            <option value="C">Option C</option>
                                                            <option value="D">Option D</option>
                                                        </select>
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="font-bold text-amber-800 block mb-1">⚠️ PSC Trap Warning Alert</label>
                                                        <input type="text" x-model="block.content_data.trap_warning" placeholder="Beware of similar named locations" class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-950 font-medium">
                                                    </div>
                                                </div>

                                                <!-- Explanations -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Explanation (English)</label>
                                                        <textarea x-model="block.content_data.explanation" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs"></textarea>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Explanation (Malayalam)</label>
                                                        <textarea x-model="block.content_data.explanation_malayalam" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-['Noto_Sans_Malayalam']"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 3: RICH TEXT BLOCK (WITH USER REQUESTED TOOLBAR) -->
                                        <template x-if="block.type === 'text'">
                                            <div class="space-y-3 text-xs">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Block Heading</label>
                                                        <input type="text" x-model="block.content_data.title" placeholder="Core Concept &amp; SCERT Notes" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-bold">
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">SCERT Reference Note</label>
                                                        <input type="text" x-model="block.content_data.scert_reference" placeholder="SCERT Social Science Std 9, Chapter 4" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                </div>

                                                <!-- RICH TEXT FORMATTING TOOLBAR -->
                                                <div class="p-2 bg-slate-100 rounded-xl border border-slate-200 flex flex-wrap items-center gap-1 text-xs select-none">
                                                    <!-- Bold & Italic -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'bold')" class="w-7 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 font-black cursor-pointer" title="Bold">B</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'italic')" class="w-7 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 italic font-bold cursor-pointer" title="Italic">I</button>
                                                    <span class="text-slate-300">|</span>

                                                    <!-- Headings & Paragraph -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'h2')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 font-bold cursor-pointer" title="Heading 2">H2</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'h3')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 font-bold cursor-pointer" title="Heading 3">H3</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'p')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 font-mono cursor-pointer" title="Paragraph">¶</button>
                                                    <span class="text-slate-300">|</span>

                                                    <!-- Alignments -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'align-left')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 cursor-pointer" title="Align Left">⬅ Left</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'align-center')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 cursor-pointer" title="Align Center">↔ Center</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'align-right')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 cursor-pointer" title="Align Right">➡ Right</button>
                                                    <span class="text-slate-300">|</span>

                                                    <!-- Lists -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'ul')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 cursor-pointer" title="Bullet List">• Bullets</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'ol')" class="px-2 h-7 bg-white hover:bg-slate-200 rounded border border-slate-300 cursor-pointer" title="Numbered List">1. Numbered</button>
                                                    <span class="text-slate-300">|</span>

                                                    <!-- Color Chips -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'color-blue')" class="px-1.5 h-7 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded border border-blue-200 font-bold cursor-pointer" title="Blue Text">Blue</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'color-green')" class="px-1.5 h-7 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded border border-emerald-200 font-bold cursor-pointer" title="Green Text">Green</button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'color-red')" class="px-1.5 h-7 bg-red-50 text-red-700 hover:bg-red-100 rounded border border-red-200 font-bold cursor-pointer" title="Red Text">Red</button>
                                                    <span class="text-slate-300">|</span>

                                                    <!-- PSC Callout Blocks -->
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'note')" class="px-2 h-7 bg-blue-50 text-blue-800 hover:bg-blue-100 rounded border border-blue-300 font-bold flex items-center gap-1 cursor-pointer" title="PSC Note Callout">
                                                        <span>📌 + Note</span>
                                                    </button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'trap')" class="px-2 h-7 bg-amber-50 text-amber-900 hover:bg-amber-100 rounded border border-amber-300 font-bold flex items-center gap-1 cursor-pointer" title="PSC Trap Warning Callout">
                                                        <span>⚠️ + Trap</span>
                                                    </button>
                                                    <button type="button" @click="insertFormatting(uIdx, bIdx, 'exam')" class="px-2 h-7 bg-emerald-50 text-emerald-900 hover:bg-emerald-100 rounded border border-emerald-300 font-bold flex items-center gap-1 cursor-pointer" title="PYQ Callout">
                                                        <span>🎯 + Exam</span>
                                                    </button>
                                                </div>

                                                <textarea 
                                                    :id="'block_text_' + uIdx + '_' + bIdx"
                                                    x-model="block.content_data.body" 
                                                    rows="5" 
                                                    placeholder="Type or paste lesson content here. Supports bilingual Malayalam & English with formatting..."
                                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono text-xs bg-white focus:border-[#0052FF] focus:outline-none"
                                                ></textarea>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 4: IMAGE BLOCK (WITH DIRECT UPLOAD) -->
                                        <template x-if="block.type === 'image'">
                                            <div class="space-y-3 text-xs">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-1">
                                                            <label class="font-bold text-slate-700">Image Source (Direct Upload, URL, or Bank) *</label>
                                                            <div class="flex items-center gap-1.5">
                                                                <label class="text-[10px] font-black text-emerald-800 hover:bg-emerald-100 flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-300 cursor-pointer transition">
                                                                    <input type="file" accept="image/*" class="hidden" @change="uploadBlockMedia($event, uIdx, bIdx, 'image')">
                                                                    <span x-show="!block._uploading">📤 Upload Image</span>
                                                                    <span x-show="block._uploading" class="inline-flex items-center gap-1 text-emerald-700">
                                                                        <span class="w-2.5 h-2.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                                                                        <span>Uploading...</span>
                                                                    </span>
                                                                </label>
                                                                <button 
                                                                    type="button" 
                                                                    @click="openMediaPickerForBlock(uIdx, bIdx, 'image')" 
                                                                    class="text-[10px] font-black text-purple-700 hover:underline flex items-center gap-1 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-200 cursor-pointer"
                                                                >
                                                                    <span>🖼️ Media Bank</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="text" x-model="block.content_data.url" placeholder="https://... or /storage/media/images/photo.webp" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                        <span x-show="block._uploadSuccess" class="text-[10px] text-emerald-600 font-bold mt-1 block">Image uploaded and attached! ✅</span>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Image Title</label>
                                                        <input type="text" x-model="block.content_data.title" placeholder="Aruvipuram Shiva Temple Timeline" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="font-bold text-slate-700 block mb-1">Image Description / Notes</label>
                                                        <textarea x-model="block.content_data.caption" placeholder="Detailed notes about this image... (Line breaks are supported)" rows="3" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-sm"></textarea>
                                                    </div>
                                                </div>

                                                <!-- Image Preview -->
                                                <template x-if="block.content_data.url">
                                                    <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center gap-3">
                                                        <img :src="block.content_data.url" class="w-16 h-12 object-cover rounded-lg border border-slate-300 shadow-2xs" alt="Preview">
                                                        <div class="min-w-0 flex-1">
                                                            <div class="font-bold text-slate-800 text-xs truncate" x-text="block.content_data.title || 'Image Attachment'"></div>
                                                            <div class="font-mono text-[10px] text-slate-500 truncate" x-text="block.content_data.url"></div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 5: AUDIO BLOCK (WITH DIRECT UPLOAD) -->
                                        <template x-if="block.type === 'audio'">
                                            <div class="space-y-3 text-xs">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-1">
                                                            <label class="font-bold text-slate-700">Audio Stream (Upload MP3/WAV/M4A or URL) *</label>
                                                            <div class="flex items-center gap-1.5">
                                                                <label class="text-[10px] font-black text-indigo-800 hover:bg-indigo-100 flex items-center gap-1 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-300 cursor-pointer transition">
                                                                    <input type="file" accept="audio/*,.mp3,.wav,.ogg,.m4a" class="hidden" @change="uploadBlockMedia($event, uIdx, bIdx, 'audio')">
                                                                    <span x-show="!block._uploading">📤 Upload Audio</span>
                                                                    <span x-show="block._uploading" class="inline-flex items-center gap-1 text-indigo-700">
                                                                        <span class="w-2.5 h-2.5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></span>
                                                                        <span>Uploading...</span>
                                                                    </span>
                                                                </label>
                                                                <button 
                                                                    type="button" 
                                                                    @click="openMediaPickerForBlock(uIdx, bIdx, 'audio')" 
                                                                    class="text-[10px] font-black text-blue-700 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded-lg border border-blue-200 cursor-pointer"
                                                                >
                                                                    <span>🎙️ Media Bank</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="text" x-model="block.content_data.url" placeholder="https://... or /storage/media/audios/clip.mp3" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                        <span x-show="block._uploadSuccess" class="text-[10px] text-emerald-600 font-bold mt-1 block">Audio file uploaded and attached! ✅</span>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Audio Title / Mentor Voice Note</label>
                                                        <input type="text" x-model="block.content_data.title" placeholder="Aruvipuram Prathishta Audio Summary" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                </div>

                                                <!-- Live Audio Player Preview -->
                                                <template x-if="block.content_data.url">
                                                    <div class="p-2.5 bg-indigo-50/70 rounded-xl border border-indigo-200 flex items-center gap-3">
                                                        <span class="text-xl">🎙️</span>
                                                        <audio :src="block.content_data.url" controls class="h-8 flex-1"></audio>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 6: VIDEO BLOCK (WITH DIRECT UPLOAD) -->
                                        <template x-if="block.type === 'video'">
                                            <div class="space-y-3 text-xs">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <div class="flex items-center justify-between mb-1">
                                                            <label class="font-bold text-slate-700">Video Source (Upload Video or Embed URL) *</label>
                                                            <div class="flex items-center gap-1.5">
                                                                <label class="text-[10px] font-black text-red-800 hover:bg-red-100 flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded-lg border border-red-300 cursor-pointer transition">
                                                                    <input type="file" accept="video/mp4,video/webm,video/ogg,video/quicktime,.mp4,.webm,.mov" class="hidden" @change="uploadBlockMedia($event, uIdx, bIdx, 'video')">
                                                                    <span x-show="!block._uploading">📤 Upload Video</span>
                                                                    <span x-show="block._uploading" class="inline-flex items-center gap-1 text-red-700">
                                                                        <span class="w-2.5 h-2.5 border-2 border-red-600 border-t-transparent rounded-full animate-spin"></span>
                                                                        <span>Uploading...</span>
                                                                    </span>
                                                                </label>
                                                                <button 
                                                                    type="button" 
                                                                    @click="openMediaPickerForBlock(uIdx, bIdx, 'video')" 
                                                                    class="text-[10px] font-black text-red-700 hover:underline flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200 cursor-pointer"
                                                                >
                                                                    <span>🎬 Media Bank</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="text" x-model="block.content_data.url" placeholder="https://youtu.be/... or /storage/media/videos/clip.mp4" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                        <span x-show="block._uploadSuccess" class="text-[10px] text-emerald-600 font-bold mt-1 block">Video uploaded and attached! ✅</span>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Video Title</label>
                                                        <input type="text" x-model="block.content_data.title" placeholder="Kerala Renaissance Video Lecture" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                </div>

                                                <!-- Live Video Player Preview for uploaded files -->
                                                <template x-if="block.content_data.url && (block.content_data.url.includes('/storage/') || block.content_data.url.endsWith('.mp4') || block.content_data.url.endsWith('.webm'))">
                                                    <div class="p-2.5 bg-red-50/70 rounded-xl border border-red-200 space-y-2">
                                                        <span class="font-bold text-[11px] text-red-950 flex items-center gap-1">🎬 Video Preview:</span>
                                                        <video :src="block.content_data.url" controls class="max-h-52 rounded-lg mx-auto bg-black shadow-xs"></video>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 7: 3D GLOBE / MAP BLOCK -->
                                        <template x-if="block.type === 'map_globe'">
                                            <div class="space-y-3 text-xs bg-teal-50/40 p-3.5 rounded-xl border border-teal-200">
                                                <div class="flex items-center justify-between pb-1 border-b border-teal-200">
                                                    <span class="font-black text-teal-900">🌐 3D Globe &amp; Spatial Map Configuration</span>
                                                    <select 
                                                        @change="applyGlobePreset(block, $event.target.value)"
                                                        class="px-2 py-1 text-xs font-bold rounded-lg border border-teal-300 bg-white text-teal-900 cursor-pointer"
                                                    >
                                                        <option value="">-- Load Geographic Preset --</option>
                                                        <option value="pacific_reality">🌏 Pacific Reality (USA &amp; Asia Neighbors)</option>
                                                        <option value="german_invasion">🇩🇪 German Blitzkrieg (WWII 1939-1941)</option>
                                                        <option value="red_sea">🌊 Red Sea &amp; Choke Points</option>
                                                        <option value="kerala_rivers">🌴 Kerala Rivers &amp; Western Ghats Gaps</option>
                                                    </select>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Mode</label>
                                                        <select x-model="block.content_data.mode" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                            <option value="3d_globe">🌐 3D Interactive Globe</option>
                                                            <option value="2d_map">🗺️ 2D Cartographic Map</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Title (English)</label>
                                                        <input type="text" x-model="block.content_data.title" placeholder="Pacific Theater" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="font-bold text-slate-700 block mb-1">Title (Malayalam)</label>
                                                        <input type="text" x-model="block.content_data.title_malayalam" placeholder="പസഫിക് യാഥാർത്ഥ്യം" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam'] bg-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- BLOCK TYPE 8: HTML / WIDGET BLOCK -->
                                        <template x-if="block.type === 'html'">
                                            <div class="space-y-2 text-xs">
                                                <label class="font-bold text-slate-700 block">Custom HTML / Interactive Widget Code</label>
                                                <textarea x-model="block.content_data.html" rows="4" placeholder="<div>...custom widgets or interactive diagrams...</div>" class="w-full px-3 py-2 rounded-lg border border-slate-300 font-mono text-xs bg-slate-900 text-emerald-400"></textarea>
                                            </div>
                                        </template>

                                    </div>
                                </template>
                            </div>

                            <!-- Add Block Toolbar for This Unit -->
                            <div class="p-3 bg-white rounded-xl border border-dashed border-purple-300 flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xs font-black text-slate-700 flex items-center gap-1.5">
                                    <span>➕ Stack a Block onto</span>
                                    <span class="text-purple-700" x-text="'Unit ' + (uIdx + 1)"></span>:
                                </span>

                                <div class="flex flex-wrap items-center gap-1.5">
                                    <button type="button" @click="addBlockToUnit(uIdx, 'hook_mcq')" class="px-2.5 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 text-xs font-black rounded-lg hover:bg-purple-100 transition flex items-center gap-1 cursor-pointer">
                                        <span>🎣 + Hook MCQ</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'practice_mcq')" class="px-2.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black rounded-lg hover:bg-blue-100 transition flex items-center gap-1 cursor-pointer">
                                        <span>🎯 + Practice MCQ</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'text')" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-lg hover:bg-emerald-100 transition cursor-pointer">
                                        <span>📝 + Text Block</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'image')" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-lg hover:bg-amber-100 transition cursor-pointer">
                                        <span>🖼️ + Image</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'audio')" class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-lg hover:bg-indigo-100 transition cursor-pointer">
                                        <span>🎙️ + Audio</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'video')" class="px-2.5 py-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-bold rounded-lg hover:bg-red-100 transition cursor-pointer">
                                        <span>🎥 + Video</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'map_globe')" class="px-2.5 py-1.5 bg-teal-50 text-teal-700 border border-teal-200 text-xs font-bold rounded-lg hover:bg-teal-100 transition cursor-pointer">
                                        <span>🌐 + 3D Map</span>
                                    </button>
                                    <button type="button" @click="addBlockToUnit(uIdx, 'html')" class="px-2.5 py-1.5 bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold rounded-lg hover:bg-slate-200 transition cursor-pointer">
                                        <span>⚡ + HTML</span>
                                    </button>
                                </div>
                                </div>
                            </div>

                            <!-- Insert Unit Button (Between units) -->
                            <div class="absolute -bottom-[20px] left-1/2 -translate-x-1/2 z-10 flex items-center justify-center" x-show="uIdx < units.length - 1">
                                <button 
                                    type="button" 
                                    @click="insertUnit(uIdx + 1)" 
                                    class="h-7 flex items-center gap-1 px-3 rounded-full bg-white border-2 border-dashed border-purple-300 text-purple-600 hover:bg-purple-50 hover:border-solid hover:border-purple-500 hover:text-purple-800 font-bold text-[10px] uppercase tracking-wide shadow-sm hover:shadow-md transition cursor-pointer"
                                    title="Insert Unit Here"
                                >
                                    <span class="text-sm leading-none font-black">+</span>
                                    <span>Insert Unit</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Bottom Add Unit CTA -->
                <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <button 
                        type="button" 
                        @click="addUnit()" 
                        class="px-6 py-2.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-black rounded-xl transition inline-flex items-center gap-2 cursor-pointer shadow-2xs"
                    >
                        <span>＋ Add Sequential Unit</span>
                        <span x-text="'(Unit ' + (units.length + 1) + ')'"></span>
                    </button>
                </div>

            </div>

            <!-- ============================================================= -->
            <!-- CARD 3: FINAL UNIT: CAPSTONE OMR ASSESSMENT EXAM              -->
            <!-- ============================================================= -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-700 font-black text-xs flex items-center justify-center border border-emerald-200">
                            🏁
                        </span>
                        <div>
                            <h2 class="text-sm sm:text-base font-black text-slate-900 leading-tight flex items-center gap-2">
                                <span>Final Unit: Capstone OMR Assessment Exam</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">
                                    Kerala PSC Bubble Sheet
                                </span>
                            </h2>
                            <p class="text-[11px] text-slate-500 font-medium">
                                All Hook &amp; Practice MCQs from previous units are auto-compiled into the candidate's OMR Exam Sheet.
                            </p>
                        </div>
                    </div>

                    <button 
                        type="button" 
                        @click="addExtraOmrQuestion()" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black text-xs rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer shrink-0"
                    >
                        <span>＋ Add OMR Challenge Question</span>
                    </button>
                </div>

                <!-- OMR EXAM SETTINGS CONFIGURATION (Client Spec: pass mark, time limit, question pool) -->
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-emerald-950 uppercase tracking-wide mb-1">
                            Passing Mark Percentage (%) *
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input 
                                type="number" 
                                name="pass_mark" 
                                x-model="passMark" 
                                min="0" 
                                max="100" 
                                required
                                class="w-full px-3 py-1.5 rounded-lg border border-emerald-300 bg-white font-bold text-xs"
                            >
                            <span class="text-emerald-800 font-bold">%</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Default 50% required to pass OMR assessment.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-emerald-950 uppercase tracking-wide mb-1">
                            Assessment Time Limit (Minutes) *
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input 
                                type="number" 
                                name="time_limit_minutes" 
                                x-model="timeLimitMinutes" 
                                min="1" 
                                max="180" 
                                required
                                class="w-full px-3 py-1.5 rounded-lg border border-emerald-300 bg-white font-bold text-xs"
                            >
                            <span class="text-emerald-800 font-bold">mins</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Countdown timer urgency shown on bubble sheet.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-emerald-950 uppercase tracking-wide mb-1">
                            Kerala PSC Scoring Rule
                        </label>
                        <div class="p-2 rounded-lg bg-white border border-emerald-300 text-[11px] font-bold text-slate-700 flex items-center justify-between">
                            <span class="text-emerald-700">Correct: +1.00</span>
                            <span class="text-red-600">Wrong: -0.33</span>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Standard negative marking engine.</p>
                    </div>
                </div>

                <!-- Total Question Pool Summary Bar -->
                <div class="mb-5 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-slate-800">Total OMR Exam Pool:</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#0052FF] text-white font-black font-mono text-xs" x-text="totalOmrCount() + ' Questions'"></span>
                        <span class="text-slate-400">•</span>
                        <span class="font-mono text-slate-600 font-bold" x-text="(totalOmrCount() * 1.00).toFixed(2) + ' Maximum Marks (+1.00 / -0.33)'"></span>
                    </div>

                    <div class="text-[11px] text-slate-500 font-medium">
                        Auto-populates from Unit MCQs + Extra OMR challenge questions below
                    </div>
                </div>

                <!-- Auto-Compiled Questions from Units (Preview Roster) -->
                <div class="space-y-3 mb-6">
                    <div class="text-xs font-bold text-slate-700 flex items-center justify-between">
                        <span>📋 Auto-compiled Questions from Sequential Units:</span>
                        <span class="text-[11px] text-slate-400">Live reflection of Hook &amp; Practice MCQs</span>
                    </div>

                    <template x-for="(item, qIdx) in getCompiledUnitQuestions()" :key="'compiled_' + qIdx">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center font-mono shrink-0" x-text="qIdx + 1"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider shrink-0"
                                    :class="item.type === 'hook_mcq' ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-blue-100 text-blue-900 border border-blue-200'"
                                    x-text="item.unitTitle + ' (' + (item.type === 'hook_mcq' ? 'Hook MCQ' : 'Practice MCQ') + ')'"
                                ></span>
                                <span class="font-bold text-slate-800 truncate" x-text="item.question_text"></span>
                            </div>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-black font-mono text-[10px] shrink-0" x-text="'Ans: ' + item.correct_option"></span>
                        </div>
                    </template>

                    <template x-if="getCompiledUnitQuestions().length === 0">
                        <div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-center text-slate-400 text-xs">
                            Currently no Hook or Practice MCQs in your sequential units. Add a Hook MCQ or Practice MCQ to Unit 1 or Unit 2 to see them auto-compiled here!
                        </div>
                    </template>
                </div>

                <!-- Extra Standalone OMR Challenge Questions -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wide">Extra Standalone OMR Questions</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Add challenge questions exclusive to the final OMR assessment sheet.</p>
                        </div>
                        <button type="button" @click="addExtraOmrQuestion()" class="text-xs text-[#0052FF] font-bold hover:underline cursor-pointer">
                            + Add Question
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(q, qIdx) in extraOmrQuestions" :key="'extra_' + qIdx">
                            <div class="p-4 rounded-2xl border border-emerald-200 bg-emerald-50/20 space-y-3 text-xs">
                                <div class="flex items-center justify-between pb-2 border-b border-emerald-200 text-xs font-bold text-emerald-950">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center font-mono" x-text="qIdx + 1"></span>
                                        <span>Standalone OMR Challenge Question #<span x-text="qIdx + 1"></span></span>
                                    </div>
                                    <button type="button" @click="removeExtraOmrQuestion(qIdx)" class="text-red-600 hover:underline font-bold text-xs cursor-pointer">✕ Remove</button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="font-bold text-slate-700 block mb-1">Question Text (English) *</label>
                                        <input type="text" x-model="q.question_text" placeholder="e.g. Which of the following statements about Aruvipuram is correct?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-700 block mb-1">Question Text (Malayalam)</label>
                                        <input type="text" x-model="q.question_text_malayalam" placeholder="അരുവിപ്പുറത്തെക്കുറിച്ചുള്ള പ്രസ്താവനകളിൽ ഏതാണ് ശരി?" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-['Noto_Sans_Malayalam']">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="text" x-model="q.option_a" placeholder="Option A" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                    <input type="text" x-model="q.option_b" placeholder="Option B" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                    <input type="text" x-model="q.option_c" placeholder="Option C" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                    <input type="text" x-model="q.option_d" placeholder="Option D" class="w-full px-2.5 py-1 rounded-lg border border-slate-300 bg-white">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="font-bold text-slate-700 block mb-1">Correct Option *</label>
                                        <select x-model="q.correct_option" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-bold">
                                            <option value="A">Option A</option>
                                            <option value="B">Option B</option>
                                            <option value="C">Option C</option>
                                            <option value="D">Option D</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="font-bold text-amber-800 block mb-1">⚠️ PSC Trap Warning Alert</label>
                                        <input type="text" x-model="q.trap_warning" placeholder="Watch out for chronological sequence traps" class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-950 font-medium">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Sticky Bottom Submission Bar -->
            <div class="sticky bottom-4 z-30 p-4 rounded-2xl bg-slate-900/95 backdrop-blur-md text-white border border-slate-800 flex flex-wrap items-center justify-between gap-4 shadow-2xl">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full" :class="isActive ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400'"></span>
                    <span class="text-xs font-bold" x-text="isActive ? 'Will be published immediately' : 'Will be saved as unpublished draft'"></span>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        @click="isActive = false; submitMainForm()" 
                        class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition cursor-pointer"
                    >
                        Save as Draft
                    </button>
                    <button 
                        type="button" 
                        @click="isActive = true; submitMainForm()" 
                        class="px-6 py-2 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 text-xs font-black rounded-xl shadow-md transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                    >
                        <span>🚀</span>
                        <span>{{ $isEdit ? 'Update & Publish Session' : 'Save & Publish Session' }}</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: REAL-TIME LIVE LEARNER PREVIEW                             -->
    <!-- ================================================================= -->
    <div x-show="studioTab === 'preview'" class="space-y-6">
        <!-- Preview Notification Bar -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-blue-900 font-bold">
                <span class="text-base">👁️</span>
                <span>Live Candidate Preview Mode — Test the sequential units and capstone bubble sheet in real-time.</span>
            </div>
            <button 
                type="button" 
                @click="studioTab = 'builder'" 
                class="px-3 py-1.5 bg-[#0052FF] text-white rounded-lg font-black hover:bg-blue-700 transition cursor-pointer"
            >
                ← Return to Builder
            </button>
        </div>

        <!-- Student Runner Mockup Card -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xl">
            <!-- Header Banner -->
            <div class="p-6 sm:p-8 bg-slate-900 text-white border-b border-slate-800 relative overflow-hidden">
                <div class="relative z-10 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] font-black uppercase tracking-wider">
                            <span x-text="getCategoryNameById(categoryId) || 'PSC Track'"></span>
                        </span>
                        <span class="text-xs text-slate-400 font-mono" x-text="'Session #' + (order || '1')"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white" x-text="sessionTitle || 'Untitled Learning Session'"></h1>
                    <p class="text-xs sm:text-sm text-blue-400 font-['Noto_Sans_Malayalam']" x-text="sessionTitleMl || ''"></p>
                </div>
            </div>

            <!-- Stepper Navigation -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 overflow-x-auto">
                <div class="flex items-center gap-2 min-w-max">
                    <template x-for="(unit, uIdx) in units" :key="'step_' + uIdx">
                        <button 
                            type="button" 
                            @click="previewActiveUnitIdx = uIdx"
                            :class="previewActiveUnitIdx === uIdx ? 'bg-[#0052FF] text-white shadow-xs font-black' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 font-bold'"
                            class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                        >
                            <span x-text="'Unit ' + (uIdx + 1)"></span>
                        </button>
                    </template>

                    <!-- Capstone OMR Step -->
                    <button 
                        type="button" 
                        @click="previewActiveUnitIdx = units.length"
                        :class="previewActiveUnitIdx === units.length ? 'bg-emerald-600 text-white shadow-xs font-black' : 'bg-emerald-50 text-emerald-800 border border-emerald-300 hover:bg-emerald-100 font-bold'"
                        class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>📝 Capstone OMR Sheet</span>
                        <span class="px-1.5 py-0.2 rounded bg-emerald-200 text-emerald-950 text-[10px] font-black" x-text="totalOmrCount() + ' Qs'"></span>
                    </button>

                    <!-- Finished Session Scorecard Step -->
                    <button 
                        type="button" 
                        @click="previewActiveUnitIdx = units.length + 1"
                        :class="previewActiveUnitIdx === units.length + 1 ? 'bg-amber-500 text-slate-950 shadow-xs font-black' : 'bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 font-bold'"
                        class="px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>🏆 Finished Session Scorecard</span>
                    </button>
                </div>
            </div>

            <!-- Active Preview Body -->
            <div class="p-6 sm:p-8">
                <!-- If regular Unit is selected -->
                <template x-if="previewActiveUnitIdx < units.length && units[previewActiveUnitIdx]">
                    <div class="space-y-6 max-w-3xl mx-auto">
                        <div class="pb-3 border-b border-slate-200">
                            <span class="text-xs font-bold text-purple-700 uppercase tracking-wide" x-text="'UNIT ' + (previewActiveUnitIdx + 1)"></span>
                            <h2 class="text-lg font-black text-slate-900" x-text="units[previewActiveUnitIdx].title || ('Unit ' + (previewActiveUnitIdx + 1))"></h2>
                        </div>

                        <!-- Render stacked blocks -->
                        <div class="space-y-6">
                            <template x-for="(b, bIdx) in units[previewActiveUnitIdx].blocks" :key="'prev_b_' + bIdx">
                                <div class="space-y-3">
                                    <!-- Hook MCQ / Practice MCQ in runner -->
                                    <template x-if="b.type === 'hook_mcq' || b.type === 'practice_mcq'">
                                        <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-xs space-y-4">
                                            <div class="flex items-center justify-between">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase"
                                                    :class="b.type === 'hook_mcq' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                                                    x-text="b.type === 'hook_mcq' ? '🎣 Hook Concept Question' : '🎯 Practice Quiz'"
                                                ></span>
                                                <span class="text-[10px] font-bold text-slate-400">+1.00 Mark / -0.33 Negative</span>
                                            </div>

                                            <div class="space-y-1">
                                                <p class="font-bold text-slate-900 text-sm" x-text="b.content_data.question_text || 'Sample question stem...'"></p>
                                                <p class="text-xs text-[#0052FF] font-['Noto_Sans_Malayalam']" x-text="b.content_data.question_text_malayalam || ''"></p>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center font-mono text-[10px]">A</span>
                                                    <span x-text="b.content_data.option_a || 'Option A'"></span>
                                                </div>
                                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center font-mono text-[10px]">B</span>
                                                    <span x-text="b.content_data.option_b || 'Option B'"></span>
                                                </div>
                                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center font-mono text-[10px]">C</span>
                                                    <span x-text="b.content_data.option_c || 'Option C'"></span>
                                                </div>
                                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-800 flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center font-mono text-[10px]">D</span>
                                                    <span x-text="b.content_data.option_d || 'Option D'"></span>
                                                </div>
                                            </div>

                                            <template x-if="b.content_data.trap_warning">
                                                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950 font-medium">
                                                    <strong>⚠️ PSC Trap Alert:</strong> <span x-text="b.content_data.trap_warning"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Text Block in runner -->
                                    <template x-if="b.type === 'text'">
                                        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-3 prose prose-slate max-w-none text-xs sm:text-sm">
                                            <template x-if="b.content_data.title">
                                                <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-2" x-text="b.content_data.title"></h3>
                                            </template>
                                            <div class="whitespace-pre-line text-slate-700 leading-relaxed" x-html="b.content_data.body || 'Lesson text notes...'"></div>
                                        </div>
                                    </template>

                                    <!-- Image Block in runner -->
                                    <template x-if="b.type === 'image' && b.content_data.url">
                                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-white">
                                            <img :src="b.content_data.url" class="w-full max-h-96 object-cover" alt="Lesson Visual">
                                            <div class="p-3 text-xs text-slate-600 bg-slate-50 border-t border-slate-200">
                                                <div class="font-bold text-slate-800" x-text="b.content_data.title"></div>
                                                <div class="text-[11px] text-slate-500" x-text="b.content_data.caption"></div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Video Block in runner -->
                                    <template x-if="b.type === 'video' && b.content_data.url">
                                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-black aspect-video flex items-center justify-center text-white text-xs">
                                            <span>🎥 Embedded Video Player: <span class="font-mono text-slate-300" x-text="b.content_data.url"></span></span>
                                        </div>
                                    </template>

                                    <!-- Audio Block in runner -->
                                    <template x-if="b.type === 'audio' && b.content_data.url">
                                        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <span class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center text-lg">▶</span>
                                                <div>
                                                    <div class="text-xs font-black text-indigo-950" x-text="b.content_data.title || 'Mentor Voice Note'"></div>
                                                    <div class="text-[10px] text-indigo-700">Audio Lecture • Speed 1.0x / 1.5x / 2.0x</div>
                                                </div>
                                            </div>
                                            <audio controls :src="b.content_data.url" class="h-8"></audio>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- If Capstone OMR Unit is selected -->
                <template x-if="previewActiveUnitIdx === units.length">
                    <div class="space-y-6 max-w-3xl mx-auto">
                        <div class="p-6 rounded-3xl bg-slate-900 text-white space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-black uppercase">
                                    Kerala PSC OMR Bubble Sheet Simulator
                                </span>
                                <span class="font-mono text-xs text-yellow-400 font-bold" x-text="'⏱️ ' + timeLimitMinutes + ':00 Timer'"></span>
                            </div>
                            <h2 class="text-xl font-black text-white">Final Unit: Capstone OMR Assessment</h2>
                            <p class="text-xs text-slate-300">
                                Authentic timed Kerala PSC negative marking test. Answer by selecting bubbles below.
                            </p>
                        </div>

                        <!-- Sample Bubble Simulator Mockup -->
                        <div class="space-y-4">
                            <template x-for="i in Math.min(totalOmrCount() || 3, 5)" :key="'bubble_' + i">
                                <div class="p-4 rounded-2xl border border-slate-200 bg-white flex items-center justify-between gap-4 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-black flex items-center justify-center font-mono" x-text="'Q' + i"></span>
                                        <span class="font-bold text-slate-800">Sample Capstone Question stem...</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" class="w-7 h-7 rounded-full border-2 border-slate-300 hover:border-slate-800 text-[10px] font-black font-mono">A</button>
                                        <button type="button" class="w-7 h-7 rounded-full border-2 border-slate-300 hover:border-slate-800 text-[10px] font-black font-mono">B</button>
                                        <button type="button" class="w-7 h-7 rounded-full border-2 border-slate-300 hover:border-slate-800 text-[10px] font-black font-mono">C</button>
                                        <button type="button" class="w-7 h-7 rounded-full border-2 border-slate-300 hover:border-slate-800 text-[10px] font-black font-mono">D</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="text-center pt-4">
                            <button 
                                type="button" 
                                @click="previewActiveUnitIdx = units.length + 1" 
                                class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-xl shadow-md transition inline-flex items-center gap-2 cursor-pointer"
                            >
                                <span>Preview Finished Evaluation Scorecard ➔</span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- If Finished Session Scorecard is selected -->
                <template x-if="previewActiveUnitIdx === units.length + 1">
                    <div class="space-y-6 max-w-2xl mx-auto">
                        <div class="bg-white rounded-3xl border-2 border-slate-200 p-6 sm:p-8 text-center shadow-lg relative overflow-hidden">
                            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                🏆
                            </div>
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-black uppercase tracking-wider rounded-full mb-2">
                                Kerala PSC Session Assessment Passed!
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-950">
                                Session Final Score: <span class="text-[#0052FF]" x-text="((totalOmrCount() || 5) * 0.93).toFixed(2)"></span> / <span x-text="(totalOmrCount() || 5).toFixed(2)"></span>
                            </h3>
                            <p class="text-xs font-bold text-slate-500 mt-1">
                                Marks added to your Cumulative Track Ledger!
                            </p>

                            <!-- Score Breakdown Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-6">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Correct (+1)</div>
                                    <div class="text-lg font-black text-emerald-600" x-text="totalOmrCount() || 5"></div>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Wrong (-0.33)</div>
                                    <div class="text-lg font-black text-red-600">0</div>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Unattempted</div>
                                    <div class="text-lg font-black text-slate-600">0</div>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                    <div class="text-[10px] font-bold text-slate-500 uppercase">Accuracy</div>
                                    <div class="text-lg font-black text-[#0052FF]">100%</div>
                                </div>
                            </div>

                            <!-- XP Reward Badge -->
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-black mb-6">
                                <span>⚡ Rewarded:</span>
                                <span class="text-sm text-amber-700" x-text="'+' + (sessionXpReward || 250) + ' XP'"></span>
                                <span>• Rank Boosted!</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <button type="button" @click="previewActiveUnitIdx = 0" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                                    ↺ Retake Session
                                </button>
                                <button type="button" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>Proceed to Next Unit / Session ➔</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODALS: MEDIA LIBRARY PICKER & QUICK CATEGORY CREATOR             -->
    <!-- ================================================================= -->

    <!-- Media Library Picker Modal -->
    <div 
        x-show="showMediaModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 max-w-3xl w-full max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
            <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-base">📁</span>
                    <h3 class="text-sm font-black">Choose from Media Bank</h3>
                </div>

                <button 
                    type="button"
                    @click="showMediaModal = false" 
                    class="w-7 h-7 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs cursor-pointer"
                >✕</button>
            </div>

            <!-- Media Grid -->
            <div class="p-4 overflow-y-auto flex-1 bg-slate-50/50">
                <template x-if="isLoadingMedia">
                    <div class="text-center py-12">
                        <span class="inline-block w-6 h-6 border-2 border-[#0052FF] border-t-yellow-400 rounded-full animate-spin"></span>
                        <p class="text-xs text-slate-500 mt-2">Loading library items...</p>
                    </div>
                </template>

                <template x-if="!isLoadingMedia && mediaItems.length === 0">
                    <div class="text-center py-12 text-slate-400 text-xs">
                        No media assets found in library.
                    </div>
                </template>

                <template x-if="!isLoadingMedia && mediaItems.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <template x-for="item in mediaItems" :key="item.id">
                            <div 
                                @click="selectMedia(item)"
                                class="p-2.5 rounded-xl border border-slate-200 bg-white hover:border-[#0052FF] hover:shadow-md transition cursor-pointer group"
                            >
                                <div class="w-full h-24 rounded-lg bg-slate-100 overflow-hidden mb-2 flex items-center justify-center">
                                    <template x-if="item.file_type === 'image'">
                                        <img :src="item.url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="item.file_type === 'audio'">
                                        <span class="text-3xl">🎙️</span>
                                    </template>
                                    <template x-if="item.file_type === 'video'">
                                        <span class="text-3xl">🎬</span>
                                    </template>
                                </div>
                                <div class="text-xs font-bold text-slate-800 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="item.formatted_size"></div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="p-3 bg-white border-t border-slate-100 text-right">
                <button 
                    type="button" 
                    @click="showMediaModal = false" 
                    class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg cursor-pointer"
                >Close</button>
            </div>
        </div>
    </div>

    <!-- Quick PSC Subject Track Creator Modal -->
    <div 
        x-show="showQuickCategoryModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl border border-slate-200 p-6 max-w-md w-full shadow-2xl">
            <h3 class="text-base font-black text-slate-900 mb-3">Create New PSC Subject Track</h3>
            <div class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Subject Name (English) *</label>
                    <input type="text" x-model="quickCategoryName" placeholder="e.g. World Geography" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Subject Name (Malayalam)</label>
                    <input type="text" x-model="quickCategoryNameMl" placeholder="e.g. ലോക ഭൂമിശാസ്ത്രം" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-['Noto_Sans_Malayalam']">
                </div>
            </div>
            <div class="mt-5 flex items-center justify-end gap-2">
                <button type="button" @click="showQuickCategoryModal = false" class="px-4 py-2 text-slate-600 text-xs font-bold cursor-pointer">Cancel</button>
                <button type="button" @click="saveQuickCategory()" class="px-5 py-2 bg-[#0052FF] text-white text-xs font-black rounded-xl shadow cursor-pointer">Create Track</button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminSessionBuilder(initial) {
    window.adminModularSessionBuilder = adminSessionBuilder;
    return {
        studioTab: 'builder', // 'builder' or 'preview'
        previewActiveUnitIdx: 0,
        showAdvanced: false,

        sessionTitle: @js(old('title', $session->title ?? '')),
        sessionTitleMl: @js(old('title_malayalam', $session->title_malayalam ?? '')),
        featureImage: initial.featureImage || '',
        featureVideo: initial.featureVideo || '',
        featureMediaTab: (initial.featureVideo ? 'video' : 'image'),

        categoryId: initial.categoryId || '',
        availableCategories: initial.categories || [],
        nextOrdersByCategory: initial.nextOrdersByCategory || {},
        defaultNextOrder: initial.defaultNextOrder || 1,
        order: (initial.order !== null && initial.order !== '') ? initial.order : '',
        inGeneralStream: initial.inGeneralStream !== undefined ? Boolean(initial.inGeneralStream) : true,
        generalStreamOrder: (initial.generalStreamOrder !== null && initial.generalStreamOrder !== '') ? initial.generalStreamOrder : '',
        nextTrainOrder: initial.nextTrainOrder || 1,
        passMark: initial.passMark || 50,
        timeLimitMinutes: initial.timeLimitMinutes || 10,
        isActive: Boolean(initial.isActive),
        isEdit: Boolean(initial.isEdit),

        accessTier: initial.accessTier || 'premium',
        sessionPrice: (initial.sessionPrice !== undefined && initial.sessionPrice !== null && initial.sessionPrice !== '') ? initial.sessionPrice : 199,
        setAccessTier(tier) {
            this.accessTier = tier;
            if (tier === 'premium' && (!this.sessionPrice || this.sessionPrice <= 0)) {
                this.sessionPrice = 199;
            }
        },
        _coverUploading: false,

        showQuickCategoryModal: false,
        quickCategoryName: '',
        quickCategoryNameMl: '',

        showMediaModal: false,
        isLoadingMedia: false,
        mediaItems: [],
        activeMediaTarget: null,

        units: [],
        extraOmrQuestions: [],
        serializedContents: [],
        serializedQuestions: [],

        init() {
            this.buildUnitsFromInitial(initial);

            if (!this.isEdit) {
                if (!this.order) {
                    this.updateAutoOrder();
                }
                if (!this.generalStreamOrder) {
                    this.generalStreamOrder = this.nextTrainOrder;
                }
            }
        },

        buildUnitsFromInitial(initial) {
            const rawContents = initial.contents || [];
            const diagnosticQ = initial.diagnostic || null;
            const reinforcementQs = initial.reinforcement || [];

            if (rawContents.length === 0 && !diagnosticQ && reinforcementQs.length === 0) {
                // New empty session: initialize with clean Unit 1 with NO default blocks
                this.units = [
                    {
                        id: 'unit_' + Date.now(),
                        title: 'Unit 1: Concept Notes',
                        blocks: []
                    }
                ];
                return;
            }

            // Group raw contents by unit_order
            const unitMap = {};
            rawContents.forEach(item => {
                const uOrder = item.unit_order || 1;
                if (!unitMap[uOrder]) {
                    unitMap[uOrder] = {
                        id: 'unit_' + uOrder + '_' + Date.now(),
                        title: item.unit_title || ('Unit ' + uOrder + ': Learning Track'),
                        blocks: []
                    };
                }
                unitMap[uOrder].blocks.push({
                    id: 'block_' + (item.id || Date.now() + Math.random()),
                    type: item.type,
                    content_data: item.content_data || {}
                });
            });

            // If Unit 1 exists and we have an old diagnosticQ not yet present in Unit 1 blocks, insert it as hook_mcq
            if (diagnosticQ) {
                if (!unitMap[1]) {
                    unitMap[1] = {
                        id: 'unit_1_' + Date.now(),
                        title: 'Unit 1: Concept & Hook Challenge',
                        blocks: []
                    };
                }
                const hasHookBlock = unitMap[1].blocks.some(b => b.type === 'hook_mcq');
                if (!hasHookBlock) {
                    unitMap[1].blocks.unshift({
                        id: 'block_hook_' + diagnosticQ.id,
                        type: 'hook_mcq',
                        content_data: {
                            question_text: diagnosticQ.question_text || '',
                            question_text_malayalam: diagnosticQ.question_text_malayalam || '',
                            option_a: diagnosticQ.option_a || '',
                            option_b: diagnosticQ.option_b || '',
                            option_c: diagnosticQ.option_c || '',
                            option_d: diagnosticQ.option_d || '',
                            correct_option: diagnosticQ.correct_option || 'A',
                            trap_warning: diagnosticQ.trap_warning_text || (diagnosticQ.trap_warning || ''),
                            explanation: diagnosticQ.explanation || '',
                            explanation_malayalam: diagnosticQ.explanation_malayalam || ''
                        }
                    });
                }
            }

            // Sorted array of units
            const sortedOrders = Object.keys(unitMap).map(Number).sort((a, b) => a - b);
            if (sortedOrders.length === 0) {
                this.units = [{
                    id: 'unit_1',
                    title: 'Unit 1: Concept Notes',
                    blocks: []
                }];
            } else {
                this.units = sortedOrders.map(ord => unitMap[ord]);
            }

            // Any standalone reinforcement questions not present in blocks can be loaded into extraOmrQuestions
            const existingQTexts = new Set();
            this.units.forEach(u => {
                u.blocks.forEach(b => {
                    if ((b.type === 'hook_mcq' || b.type === 'practice_mcq') && b.content_data && b.content_data.question_text) {
                        existingQTexts.add(b.content_data.question_text.trim());
                    }
                });
            });

            reinforcementQs.forEach(q => {
                if (q.question_text && !existingQTexts.has(q.question_text.trim())) {
                    this.extraOmrQuestions.push({
                        question_text: q.question_text,
                        question_text_malayalam: q.question_text_malayalam || '',
                        option_a: q.option_a || '',
                        option_b: q.option_b || '',
                        option_c: q.option_c || '',
                        option_d: q.option_d || '',
                        correct_option: q.correct_option || 'A',
                        trap_warning: q.trap_warning_text || (q.trap_warning || ''),
                        explanation: q.explanation || '',
                        explanation_malayalam: q.explanation_malayalam || ''
                    });
                }
            });
        },

        addUnit() {
            const nextUnitNum = this.units.length + 1;
            this.units.push({
                id: 'unit_' + Date.now() + '_' + nextUnitNum,
                title: 'Unit ' + nextUnitNum + ': Study Track',
                blocks: []
            });
        },

        insertUnit(index) {
            const nextUnitNum = this.units.length + 1;
            this.units.splice(index, 0, {
                id: 'unit_' + Date.now() + '_' + nextUnitNum,
                title: 'Unit ' + nextUnitNum + ': Study Track',
                blocks: []
            });
        },

        removeUnit(uIdx) {
            if (this.units.length <= 1) return;
            if (confirm('Delete Unit ' + (uIdx + 1) + ' and all blocks inside it?')) {
                this.units.splice(uIdx, 1);
            }
        },

        moveUnitUp(uIdx) {
            if (uIdx <= 0) return;
            const temp = this.units[uIdx];
            this.units[uIdx] = this.units[uIdx - 1];
            this.units[uIdx - 1] = temp;
        },

        moveUnitDown(uIdx) {
            if (uIdx >= this.units.length - 1) return;
            const temp = this.units[uIdx];
            this.units[uIdx] = this.units[uIdx + 1];
            this.units[uIdx + 1] = temp;
        },

        addBlockToUnit(uIdx, type) {
            let initialData = {};
            if (type === 'text') {
                initialData = { title: '', scert_reference: '', body: '' };
            } else if (type === 'hook_mcq' || type === 'practice_mcq') {
                initialData = {
                    question_text: '',
                    question_text_malayalam: '',
                    option_a: '',
                    option_b: '',
                    option_c: '',
                    option_d: '',
                    correct_option: 'A',
                    trap_warning: '',
                    explanation: '',
                    explanation_malayalam: ''
                };
            } else if (type === 'image') {
                initialData = { url: '', title: '', caption: '' };
            } else if (type === 'audio') {
                initialData = { url: '', title: '', duration: '' };
            } else if (type === 'video') {
                initialData = { url: '', title: '' };
            } else if (type === 'map_globe') {
                initialData = { mode: '3d_globe', title: '', title_malayalam: '', center_lat: 10.85, center_lng: 76.27, zoom: 2.0, markers: [] };
            } else if (type === 'html') {
                initialData = { html: '' };
            }

            this.units[uIdx].blocks.push({
                id: 'block_' + Date.now() + '_' + Math.random(),
                type: type,
                content_data: initialData
            });
        },

        removeBlock(uIdx, bIdx) {
            this.units[uIdx].blocks.splice(bIdx, 1);
        },

        moveBlockUp(uIdx, bIdx) {
            if (bIdx <= 0) return;
            const blocks = this.units[uIdx].blocks;
            const temp = blocks[bIdx];
            blocks[bIdx] = blocks[bIdx - 1];
            blocks[bIdx - 1] = temp;
        },

        moveBlockDown(uIdx, bIdx) {
            const blocks = this.units[uIdx].blocks;
            if (bIdx >= blocks.length - 1) return;
            const temp = blocks[bIdx];
            blocks[bIdx] = blocks[bIdx + 1];
            blocks[bIdx + 1] = temp;
        },

        getBlockLabel(type) {
            const map = {
                'hook_mcq': '🎣 Hook MCQ (Opener)',
                'practice_mcq': '🎯 Practice MCQ',
                'text': '📝 Text Block',
                'image': '🖼️ Image Block',
                'audio': '🎙️ Audio Stream',
                'video': '🎥 Video Block',
                'map_globe': '🌐 3D Globe / Map',
                'html': '⚡ HTML Widget'
            };
            return map[type] || (type.toUpperCase() + ' Block');
        },

        insertFormatting(uIdx, bIdx, action) {
            const el = document.getElementById('block_text_' + uIdx + '_' + bIdx);
            if (!el) return;
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const val = el.value || '';
            const sel = val.substring(start, end);

            let prefix = '';
            let suffix = '';

            if (action === 'bold') { prefix = '**'; suffix = '**'; }
            else if (action === 'italic') { prefix = '*'; suffix = '*'; }
            else if (action === 'h2') { prefix = '\n\n## '; suffix = '\n'; }
            else if (action === 'h3') { prefix = '\n\n### '; suffix = '\n'; }
            else if (action === 'p') { prefix = '\n\n'; suffix = '\n'; }
            else if (action === 'ul') { prefix = '\n- '; suffix = ''; }
            else if (action === 'ol') { prefix = '\n1. '; suffix = ''; }
            else if (action === 'align-left') { prefix = '\n<div class="text-left">\n'; suffix = '\n</div>\n'; }
            else if (action === 'align-center') { prefix = '\n<div class="text-center">\n'; suffix = '\n</div>\n'; }
            else if (action === 'align-right') { prefix = '\n<div class="text-right">\n'; suffix = '\n</div>\n'; }
            else if (action === 'color-blue') { prefix = '<span class="text-[#0052FF] font-bold">'; suffix = '</span>'; }
            else if (action === 'color-green') { prefix = '<span class="text-emerald-700 font-bold">'; suffix = '</span>'; }
            else if (action === 'color-red') { prefix = '<span class="text-red-700 font-bold">'; suffix = '</span>'; }
            else if (action === 'note') { prefix = '\n> 📌 **PSC Key Note:** '; suffix = '\n'; }
            else if (action === 'trap') { prefix = '\n> ⚠️ **PSC Trap Warning:** '; suffix = '\n'; }
            else if (action === 'exam') { prefix = '\n> 🎯 **Previous Exam Question:** '; suffix = '\n'; }

            const insertText = prefix + (sel || (action.startsWith('color') ? 'highlighted' : 'text')) + suffix;
            el.value = val.substring(0, start) + insertText + val.substring(end);
            el.selectionStart = start + prefix.length;
            el.selectionEnd = start + prefix.length + (sel || (action.startsWith('color') ? 'highlighted' : 'text')).length;
            el.focus();
            this.units[uIdx].blocks[bIdx].content_data.body = el.value;
        },

        applyGlobePreset(block, preset) {
            if (!preset) return;
            const presets = {
                'pacific_reality': { title: 'Pacific Reality (USA & Asia Neighbors)', center_lat: 20.0, center_lng: -160.0, zoom: 1.5 },
                'german_invasion': { title: 'German Blitzkrieg (WWII 1939-1941)', center_lat: 52.52, center_lng: 13.40, zoom: 2.2 },
                'red_sea': { title: 'Red Sea & Choke Points', center_lat: 20.0, center_lng: 38.0, zoom: 2.5 },
                'kerala_rivers': { title: 'Kerala Rivers & Western Ghats', center_lat: 10.5, center_lng: 76.5, zoom: 3.5 }
            };
            if (presets[preset]) {
                Object.assign(block.content_data, presets[preset]);
            }
        },

        getCompiledUnitQuestions() {
            const list = [];
            this.units.forEach((u, uIdx) => {
                const uOrder = uIdx + 1;
                const uTitle = u.title || ('Unit ' + uOrder);
                u.blocks.forEach(b => {
                    if ((b.type === 'hook_mcq' || b.type === 'practice_mcq') && b.content_data && b.content_data.question_text && b.content_data.question_text.trim()) {
                        list.push({
                            unitTitle: uTitle,
                            type: b.type,
                            question_text: b.content_data.question_text,
                            correct_option: b.content_data.correct_option || 'A'
                        });
                    }
                });
            });
            return list;
        },

        addExtraOmrQuestion() {
            this.extraOmrQuestions.push({
                question_text: '',
                question_text_malayalam: '',
                option_a: '',
                option_b: '',
                option_c: '',
                option_d: '',
                correct_option: 'A',
                trap_warning: '',
                explanation: '',
                explanation_malayalam: ''
            });
        },

        removeExtraOmrQuestion(idx) {
            this.extraOmrQuestions.splice(idx, 1);
        },

        totalOmrCount() {
            let count = this.extraOmrQuestions.filter(q => q.question_text && q.question_text.trim()).length;
            this.units.forEach(u => {
                u.blocks.forEach(b => {
                    if ((b.type === 'hook_mcq' || b.type === 'practice_mcq') && b.content_data && b.content_data.question_text && b.content_data.question_text.trim()) {
                        count++;
                    }
                });
            });
            return count;
        },

        updateAutoOrder() {
            if (this.categoryId && this.nextOrdersByCategory && this.nextOrdersByCategory[this.categoryId]) {
                this.order = this.nextOrdersByCategory[this.categoryId];
            } else {
                this.order = this.defaultNextOrder || 1;
            }
        },

        onCategoryChange(newCatId) {
            this.categoryId = newCatId;
            if (!this.isEdit || !this.order) {
                this.updateAutoOrder();
            }
        },

        getCategoryNameById(catId) {
            const cat = this.availableCategories.find(c => c.id == catId);
            return cat ? cat.name : '';
        },

        openMediaPickerForCover(type) {
            this.activeMediaTarget = { target: 'cover', type: type };
            this.fetchMediaItems();
            this.showMediaModal = true;
        },

        openMediaPickerForBlock(uIdx, bIdx, type) {
            this.activeMediaTarget = { target: 'block', uIdx: uIdx, bIdx: bIdx, type: type };
            this.fetchMediaItems();
            this.showMediaModal = true;
        },

        uploadBlockMedia(event, uIdx, bIdx, type) {
            const file = event.target.files[0];
            if (!file) return;

            const block = this.units[uIdx].blocks[bIdx];
            block._uploading = true;
            block._uploadSuccess = false;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                block._uploading = false;
                if (data.success && data.media) {
                    block.content_data.url = data.media.url;
                    if (!block.content_data.title) {
                        block.content_data.title = data.media.name;
                    }
                    block._uploadSuccess = true;
                    setTimeout(() => { block._uploadSuccess = false; }, 4000);
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                block._uploading = false;
                alert('Upload failed: ' + err.message);
            });
        },

        uploadCoverMedia(event, type) {
            const file = event.target.files[0];
            if (!file) return;

            this._coverUploading = true;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                this._coverUploading = false;
                if (data.success && data.media) {
                    if (type === 'image') {
                        this.featureImage = data.media.url;
                    } else {
                        this.featureVideo = data.media.url;
                    }
                } else {
                    alert('Cover upload failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                this._coverUploading = false;
                alert('Cover upload failed: ' + err.message);
            });
        },

        fetchMediaItems() {
            this.isLoadingMedia = true;
            fetch('{{ route('admin.media.api-list') }}')
                .then(r => r.json())
                .then(data => {
                    this.mediaItems = data.files || data;
                    this.isLoadingMedia = false;
                })
                .catch(() => {
                    this.isLoadingMedia = false;
                });
        },

        selectMedia(item) {
            if (!this.activeMediaTarget) return;

            if (this.activeMediaTarget.target === 'cover') {
                if (this.activeMediaTarget.type === 'image') {
                    this.featureImage = item.url;
                } else {
                    this.featureVideo = item.url;
                }
            } else if (this.activeMediaTarget.target === 'block') {
                const block = this.units[this.activeMediaTarget.uIdx].blocks[this.activeMediaTarget.bIdx];
                block.content_data.url = item.url;
                if (!block.content_data.title) {
                    block.content_data.title = item.name;
                }
            }
            this.showMediaModal = false;
        },

        saveQuickCategory() {
            if (!this.quickCategoryName) return;
            fetch('/admin/categories/quick-store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: this.quickCategoryName,
                    name_malayalam: this.quickCategoryNameMl
                })
            })
            .then(r => r.json())
            .then(cat => {
                this.availableCategories.push(cat);
                this.categoryId = cat.id;
                this.showQuickCategoryModal = false;
                this.quickCategoryName = '';
                this.quickCategoryNameMl = '';
                this.updateAutoOrder();
            });
        },

        prepareFormData() {
            const allBlocks = [];
            const allQuestions = [];

            this.units.forEach((u, uIdx) => {
                const uOrder = uIdx + 1;
                const uTitle = u.title || ('Unit ' + uOrder);

                u.blocks.forEach((b, bIdx) => {
                    allBlocks.push({
                        type: b.type,
                        unit_order: uOrder,
                        unit_title: uTitle,
                        content_data: b.content_data,
                        order: allBlocks.length + 1
                    });

                    // If block is a Hook MCQ or Practice MCQ, also collect into questions payload
                    if ((b.type === 'hook_mcq' || b.type === 'practice_mcq') && b.content_data && b.content_data.question_text) {
                        const phase = (b.type === 'hook_mcq') ? 'diagnostic' : 'reinforcement';
                        allQuestions.push({
                            phase_type: phase,
                            question_text: b.content_data.question_text,
                            question_text_malayalam: b.content_data.question_text_malayalam || '',
                            option_a: b.content_data.option_a || '',
                            option_b: b.content_data.option_b || '',
                            option_c: b.content_data.option_c || '',
                            option_d: b.content_data.option_d || '',
                            correct_option: b.content_data.correct_option || 'A',
                            trap_warning: b.content_data.trap_warning || '',
                            trap_warning_text: b.content_data.trap_warning || '',
                            explanation: b.content_data.explanation || '',
                            explanation_malayalam: b.content_data.explanation_malayalam || ''
                        });
                    }
                });
            });

            // Add extra OMR questions
            this.extraOmrQuestions.forEach(q => {
                if (q.question_text && q.question_text.trim()) {
                    allQuestions.push({
                        phase_type: 'omr',
                        question_text: q.question_text,
                        question_text_malayalam: q.question_text_malayalam || '',
                        option_a: q.option_a || '',
                        option_b: q.option_b || '',
                        option_c: q.option_c || '',
                        option_d: q.option_d || '',
                        correct_option: q.correct_option || 'A',
                        trap_warning: q.trap_warning || '',
                        trap_warning_text: q.trap_warning || '',
                        explanation: q.explanation || '',
                        explanation_malayalam: q.explanation_malayalam || ''
                    });
                }
            });

            this.serializedContents = allBlocks;
            this.serializedQuestions = allQuestions;
        },

        submitMainForm() {
            this.prepareFormData();
            
            // Force hidden inputs to update immediately to avoid AlpineJS reactivity race conditions
            // before calling form.submit() synchronously.
            const contentsInput = document.querySelector('input[name="contents_json"]');
            if (contentsInput) {
                contentsInput.value = JSON.stringify(this.serializedContents);
            }
            
            const questionsInput = document.querySelector('input[name="questions_json"]');
            if (questionsInput) {
                questionsInput.value = JSON.stringify(this.serializedQuestions);
            }

            const form = document.getElementById('admin-session-form');
            if (form) {
                form.submit();
            }
        }
    };
}
</script>
@endpush
@endsection
