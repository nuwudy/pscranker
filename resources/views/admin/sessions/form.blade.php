@extends('layouts.app')

@section('title', ($isEdit ? 'Edit Session: ' . $session->title : 'Create New Learning Session') . ' — PSCRanker Admin')

@section('content')
<div 
    x-data="adminSessionBuilder({
        contents: @js($contents),
        diagnostic: @js($diagnosticQuestions->first()),
        reinforcement: @js($reinforcementQuestions->values()),
        omr: @js($omrQuestions->values()),
        creationMode: @js(old('creation_mode', $session->creation_mode ?? 'manual')),
        customHtml: @js(old('custom_html', $session->custom_html ?? '')),
        featureImage: @js(old('feature_image', $session->feature_image ?? '')),
        categoryId: @js(old('category_id', $session->category_id ?? (request('category_id') ?? ''))),
        nextOrdersByCategory: @js($nextOrdersByCategory ?? []),
        defaultNextOrder: @js($defaultNextOrder ?? 1),
        order: @js(old('order', $session->order ?? null)),
        inGeneralStream: @js((bool) old('in_general_stream', $session->in_general_stream ?? true)),
        generalStreamOrder: @js(old('general_stream_order', $session->general_stream_order ?? null)),
        nextTrainOrder: @js($nextTrainOrder ?? 1),
        isEdit: @js($isEdit)
    })"
    class="py-8 bg-slate-50 min-h-[90vh]"
>
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <!-- Top Header & Breadcrumb -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sessions.index') }}" class="text-xs font-bold text-slate-500 hover:text-[#0052FF]">
                    ← Sessions List
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-800">
                    {{ $isEdit ? 'Edit Session #' . $session->id : 'New Session' }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('admin.media.index') }}" 
                    target="_blank"
                    class="px-3.5 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-black rounded-lg transition border border-purple-200 flex items-center gap-1.5"
                >
                    <span>📁 Media Library ↗</span>
                </a>

                @if($isEdit)
                    <a 
                        href="{{ route('session.show', $session->slug) }}" 
                        target="_blank"
                        class="px-3.5 py-1.5 bg-blue-50 text-[#0052FF] hover:bg-blue-100 text-xs font-black rounded-lg transition border border-blue-200"
                    >
                        Preview Runner ↗
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-xl text-xs font-bold text-red-900">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form 
            action="{{ $isEdit ? route('admin.sessions.update', $session) : route('admin.sessions.store') }}" 
            method="POST"
            enctype="multipart/form-data"
            @submit="prepareJsonData()"
        >
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <input type="hidden" name="contents_json" :value="JSON.stringify(contentBlocks)">
            <input type="hidden" name="questions_json" :value="JSON.stringify(allQuestions)">

            <!-- 1. General Session Metadata Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-8">
                <h2 class="text-base font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">
                    1. Session Overview & Settings
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Session Title (English) *</label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title', $session->title) }}" 
                            required 
                            placeholder="e.g. Sree Narayana Guru & Aruvipuram Prathishta"
                            class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Session Title (Malayalam)</label>
                        <input 
                            type="text" 
                            name="title_malayalam" 
                            value="{{ old('title_malayalam', $session->title_malayalam) }}" 
                            placeholder="e.g. ശ്രീനാരായണഗുരുവും അരുവിപ്പുറം പ്രതിഷ്ഠയും"
                            class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none font-['Noto_Sans_Malayalam']"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">URL Slug</label>
                        <input 
                            type="text" 
                            name="slug" 
                            value="{{ old('slug', $session->slug) }}" 
                            placeholder="sree-narayana-guru-aruvipuram"
                            class="w-full px-3 py-2 text-xs font-mono rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                        >
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">PSC Subject Stream *</label>
                            <span class="text-[10px] font-bold text-blue-600" x-show="categoryId && nextOrdersByCategory[categoryId]">
                                Next: Unit #<span x-text="nextOrdersByCategory[categoryId]"></span>
                            </span>
                        </div>
                        <select 
                            name="category_id" 
                            x-model="categoryId"
                            @change="onCategoryChange($event.target.value)"
                            class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                            required
                        >
                            <option value="">-- Select PSC Subject --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (old('category_id', $session->category_id) == $cat->id || request('category_id') == $cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }} @if($cat->name_malayalam)({{ $cat->name_malayalam }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Featured Image Field (spans both columns) -->
                    <div class="sm:col-span-2 pt-3 border-t border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                            <div>
                                <label class="block text-xs font-black text-slate-800 uppercase tracking-wide">
                                    Featured Image (കവർ ചിത്രം / Poster Image)
                                </label>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    Appears prominently above the lesson in both <strong>Manual Builder (Phase 2)</strong> and <strong>Custom Code (HTML)</strong> capsules.
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2 shrink-0">
                                <button 
                                    type="button" 
                                    @click="openMediaPicker('feature_image', 'image')" 
                                    class="px-2.5 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold rounded-lg transition flex items-center gap-1 cursor-pointer"
                                >
                                    <span>🖼️ Choose from Media Library</span>
                                </button>
                                
                                <label class="cursor-pointer px-2.5 py-1.5 bg-blue-50 text-[#0052FF] hover:bg-blue-100 border border-blue-200 text-xs font-bold rounded-lg transition flex items-center gap-1">
                                    <span x-show="!isUploadingFeatureImage">⬆️ Upload Image</span>
                                    <span x-show="isUploadingFeatureImage" class="flex items-center gap-1">
                                        <span class="w-3 h-3 border-2 border-blue-600 border-t-yellow-400 rounded-full animate-spin"></span>
                                        <span>Uploading...</span>
                                    </span>
                                    <input 
                                        type="file" 
                                        class="hidden" 
                                        accept="image/*"
                                        :disabled="isUploadingFeatureImage"
                                        @change="uploadFeatureImageDirect($event)"
                                    >
                                </label>
                            </div>
                        </div>

                        <!-- Image URL Input -->
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                name="feature_image" 
                                x-model="featureImage" 
                                placeholder="Paste image URL (e.g. https://... or /storage/media/images/photo.png)" 
                                class="w-full px-3 py-2 text-xs font-mono rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                            >
                            <button 
                                type="button" 
                                x-show="featureImage" 
                                @click="featureImage = ''" 
                                class="px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs font-bold rounded-lg transition shrink-0 cursor-pointer"
                                title="Remove Image"
                            >
                                ✕ Clear
                            </button>
                        </div>

                        <!-- Live Featured Image Preview -->
                        <template x-if="featureImage">
                            <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-4">
                                <div class="w-24 h-20 sm:w-32 sm:h-24 rounded-lg overflow-hidden border border-slate-300 bg-white shrink-0 shadow-xs flex items-center justify-center">
                                    <img :src="featureImage" alt="Feature Image Preview" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                            Active Cover Banner
                                        </span>
                                        <span class="text-slate-400 text-xs">•</span>
                                        <span class="text-xs text-slate-600 font-bold truncate">Will be displayed above the lesson</span>
                                    </div>
                                    <p class="text-[11px] font-mono text-slate-500 truncate" x-text="featureImage"></p>
                                    <button 
                                        type="button" 
                                        @click="featureImage = ''" 
                                        class="mt-2 text-[11px] font-bold text-red-600 hover:text-red-800 hover:underline cursor-pointer"
                                    >
                                        ✕ Remove Image
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                                    Unit # (Subject Sequence) *
                                </label>
                                <button 
                                    type="button" 
                                    @click="updateAutoOrder()" 
                                    class="text-[10px] font-bold text-[#0052FF] hover:underline cursor-pointer flex items-center gap-1"
                                    title="Auto-calculate next sequential unit number in this subject"
                                >
                                    <span>⚡ Auto-Next</span>
                                </button>
                            </div>
                            <input 
                                type="number" 
                                name="order" 
                                x-model="order"
                                class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                                required
                                min="1"
                            >
                            <p class="text-[10px] text-slate-500 mt-1">
                                <span class="text-blue-600 font-bold">Auto-calculated:</span> Preserves separate subject sequence (Unit #1, #2...). Admin can edit if needed.
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">XP Reward</label>
                            <input 
                                type="number" 
                                name="xp_reward" 
                                value="{{ old('xp_reward', $session->xp_reward ?? 250) }}" 
                                class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none text-amber-600"
                            >
                            <p class="text-[10px] text-slate-400 mt-1">Default 250 XP earned on completion</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 pt-6 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                id="is_active" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', $session->is_active ?? true) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-[#0052FF]"
                            >
                            <label for="is_active" class="text-xs font-bold text-slate-800">
                                Active &amp; Published in Learner Catalog
                            </label>
                        </div>

                        <!-- Monetization Settings (Free vs Premium) -->
                        <div class="flex items-center gap-3 bg-amber-50/80 border border-amber-300/80 px-4 py-2.5 rounded-xl">
                            <input 
                                type="checkbox" 
                                id="is_premium" 
                                name="is_premium" 
                                value="1" 
                                {{ old('is_premium', $session->is_premium ?? false) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 cursor-pointer"
                            >
                            <div>
                                <label for="is_premium" class="text-xs font-black text-amber-950 flex items-center gap-1.5 cursor-pointer">
                                    <span>👑 Premium Unit</span>
                                    <span class="px-2 py-0.2 rounded-full text-[9px] font-black uppercase bg-amber-200 text-amber-900">Prepaid Pass</span>
                                </label>
                                <p class="text-[10px] text-amber-800 font-medium">Unchecked = Free Unit for all learners • Checked = Included in Prepaid Pass</p>
                            </div>
                        </div>

                        <!-- General Stream Concoction Settings (Auto Mixed Practice Train) -->
                        <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-xl bg-blue-50/80 border border-blue-200">
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    id="in_general_stream" 
                                    name="in_general_stream" 
                                    value="1" 
                                    x-model="inGeneralStream"
                                    class="w-4 h-4 mt-0.5 rounded text-[#0052FF] focus:ring-blue-500 cursor-pointer"
                                >
                                <div>
                                    <label for="in_general_stream" class="text-xs font-black text-blue-950 flex items-center gap-1.5 cursor-pointer">
                                        <span>🚂 Include in General Stream (Mixed Master Train)</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ⚡ Auto-Appended
                                        </span>
                                    </label>
                                    <p class="text-[11px] text-blue-800 font-medium mt-0.5">
                                        Automatically added to the end of the mixed practice train launched from the homepage. Admin can edit train order # below.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0" x-show="inGeneralStream">
                                <label for="general_stream_order" class="text-xs font-bold text-blue-900 whitespace-nowrap">Train Step #:</label>
                                <input 
                                    type="number" 
                                    id="general_stream_order" 
                                    name="general_stream_order" 
                                    x-model="generalStreamOrder"
                                    min="1"
                                    placeholder="Auto"
                                    class="w-20 px-3 py-1.5 text-xs font-black rounded-lg border border-blue-300 bg-white focus:border-[#0052FF] focus:outline-none text-center"
                                >
                                <button 
                                    type="button" 
                                    @click="generalStreamOrder = nextTrainOrder" 
                                    class="px-2 py-1.5 bg-white hover:bg-blue-100 border border-blue-300 text-blue-700 text-[10px] font-bold rounded-md transition cursor-pointer"
                                    title="Reset to next available train step"
                                >
                                    Auto Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authoring Mode Switcher Card -->
            <div class="bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 rounded-2xl p-5 sm:p-6 shadow-md mb-8 text-white relative overflow-hidden border border-blue-800/60">
                <div class="absolute -right-10 -top-10 w-48 h-48 bg-[#0052FF]/20 rounded-full blur-2xl pointer-events-none"></div>

                <input type="hidden" name="creation_mode" :value="creationMode">

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[10px] font-black uppercase tracking-wider">
                                Session Engine
                            </span>
                            <span class="text-xs text-slate-300 font-bold">Choose your authoring mode</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-white">How do you want to build this session?</h3>
                    </div>

                    <!-- Segmented Control Buttons -->
                    <div class="inline-flex p-1.5 rounded-xl bg-slate-950/80 border border-slate-700/80 gap-1.5 self-start sm:self-auto">
                        <button 
                            type="button" 
                            @click="setCreationMode('manual')" 
                            :class="creationMode === 'manual' ? 'bg-[#0052FF] text-white shadow-lg' : 'text-slate-400 hover:text-white'"
                            class="px-3.5 py-2 rounded-lg font-black text-xs transition-all flex items-center gap-2 cursor-pointer"
                        >
                            <span>🛠️ Manual Builder</span>
                            <span class="text-[10px] opacity-75 font-normal hidden sm:inline">(Hook + Lesson + MCQs)</span>
                        </button>

                        <button 
                            type="button" 
                            @click="setCreationMode('code')" 
                            :class="creationMode === 'code' ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg' : 'text-slate-400 hover:text-white'"
                            class="px-3.5 py-2 rounded-lg font-black text-xs transition-all flex items-center gap-2 cursor-pointer"
                        >
                            <span>⚡ Custom Code (HTML)</span>
                            <span class="text-[10px] opacity-75 font-normal hidden sm:inline">(Instant Magic Paste)</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- MODE A: MANUAL 4-PHASE BUILDER (Blocks & Questions)      -->
            <!-- ======================================================== -->
            <div x-show="creationMode === 'manual'" x-transition>

            <!-- 2. Multimedia Content Builder Blocks -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-8">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-900">
                            2. Multimedia Lesson Capsule Blocks (Phase 2)
                        </h2>
                        <p class="text-xs text-slate-500 font-medium">Add, configure, and reorder image mnemonic cards, audio explainers, videos, and text notes.</p>
                    </div>

                    <!-- Add Block Buttons -->
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="addContentBlock('image')" class="px-2.5 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold rounded-lg hover:bg-purple-100 transition">
                            + Image Block
                        </button>
                        <button type="button" @click="addContentBlock('audio')" class="px-2.5 py-1.5 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold rounded-lg hover:bg-blue-100 transition">
                            + Audio Block
                        </button>
                        <button type="button" @click="addContentBlock('video')" class="px-2.5 py-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-bold rounded-lg hover:bg-red-100 transition">
                            + Video Block
                        </button>
                        <button type="button" @click="addContentBlock('text')" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-lg hover:bg-emerald-100 transition">
                            + Text / SCERT Block
                        </button>
                        <button type="button" @click="addContentBlock('map_globe')" class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs font-bold rounded-lg hover:bg-indigo-100 transition">
                            + 🌐 3D Globe / Map Block
                        </button>
                    </div>
                </div>

                <!-- Dynamic Content Blocks List -->
                <div class="space-y-4">
                    <template x-for="(block, idx) in contentBlocks" :key="idx">
                        <div class="p-4 rounded-xl border-2 border-slate-200 bg-slate-50/60 relative transition hover:border-slate-300">
                            
                            <!-- Block Bar -->
                            <div class="flex items-center justify-between mb-3 border-b border-slate-200/80 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-slate-800 text-white text-xs font-black flex items-center justify-center" x-text="idx + 1"></span>
                                    <span class="text-xs font-black uppercase text-slate-700" x-text="block.type + ' Block'"></span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button 
                                        type="button" 
                                        @click="moveBlockUp(idx)" 
                                        :disabled="idx === 0"
                                        class="p-1 text-slate-500 hover:text-slate-900 disabled:opacity-30"
                                        title="Move Up"
                                    >
                                        ▲
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="moveBlockDown(idx)" 
                                        :disabled="idx === contentBlocks.length - 1"
                                        class="p-1 text-slate-500 hover:text-slate-900 disabled:opacity-30"
                                        title="Move Down"
                                    >
                                        ▼
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="removeContentBlock(idx)"
                                        class="p-1 text-red-500 hover:text-red-700 text-xs font-bold ml-2"
                                        title="Remove Block"
                                    >
                                        ✕ Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Fields for IMAGE block -->
                            <template x-if="block.type === 'image'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="font-bold text-slate-600">Image URL *</label>
                                            <button 
                                                type="button" 
                                                @click="openMediaPicker(idx, 'image')" 
                                                class="text-[11px] font-black text-[#0052FF] hover:underline flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded border border-blue-200"
                                            >
                                                <span>🖼️ Choose from Media Library</span>
                                            </button>
                                        </div>
                                        <input type="text" x-model="block.content_data.url" placeholder="https://... or /storage/media/images/..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Title / Label</label>
                                        <input type="text" x-model="block.content_data.title" placeholder="Mnemonic Infographic Timeline" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="font-bold text-slate-600 block mb-1">Caption (Malayalam / English)</label>
                                        <input type="text" x-model="block.content_data.caption" placeholder="അരുവിപ്പുറം ശിവപ്രതിഷ്ഠ - 1888" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']">
                                    </div>
                                    <!-- Live Image Preview -->
                                    <template x-if="block.content_data.url">
                                        <div class="sm:col-span-2 mt-1 p-2 bg-slate-100 rounded-xl border border-slate-200 flex items-center gap-3">
                                            <img :src="block.content_data.url" class="w-16 h-16 object-cover rounded-lg border border-slate-300" alt="Preview">
                                            <div class="text-[11px] text-slate-600 truncate">
                                                <span class="font-bold block text-slate-800">Preview:</span>
                                                <span class="font-mono text-[10px] text-slate-500" x-text="block.content_data.url"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fields for AUDIO block -->
                            <template x-if="block.type === 'audio'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="font-bold text-slate-600">Audio Stream URL (MP3) *</label>
                                            <button 
                                                type="button" 
                                                @click="openMediaPicker(idx, 'audio')" 
                                                class="text-[11px] font-black text-blue-700 hover:underline flex items-center gap-1 bg-blue-50 px-2 py-0.5 rounded border border-blue-200"
                                            >
                                                <span>🎙️ Choose from Media Library</span>
                                            </button>
                                        </div>
                                        <input type="text" x-model="block.content_data.url" placeholder="https://... or /storage/media/audios/..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Duration string</label>
                                        <input type="text" x-model="block.content_data.duration" placeholder="0:45" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Title</label>
                                        <input type="text" x-model="block.content_data.title" placeholder="30s Fast Spoken Audio Capsule" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Summary / Transcript (Malayalam)</label>
                                        <input type="text" x-model="block.content_data.transcript" placeholder="ശ്രീനാരായണഗുരുവിന്റെ പ്രധാന ചരിത്ര വസ്തുതകൾ..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']">
                                    </div>
                                    <!-- Live Audio Preview -->
                                    <template x-if="block.content_data.url">
                                        <div class="sm:col-span-2 mt-1 p-2 bg-blue-50/70 rounded-xl border border-blue-200 flex items-center gap-3">
                                            <span class="text-xl">🎙️</span>
                                            <div class="flex-grow">
                                                <audio controls class="w-full h-8" :src="block.content_data.url" preload="none"></audio>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fields for VIDEO block -->
                            <template x-if="block.type === 'video'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="font-bold text-slate-600">Video URL (YouTube or MP4) *</label>
                                            <button 
                                                type="button" 
                                                @click="openMediaPicker(idx, 'video')" 
                                                class="text-[11px] font-black text-red-700 hover:underline flex items-center gap-1 bg-red-50 px-2 py-0.5 rounded border border-red-200"
                                            >
                                                <span>🎬 Choose from Media Library</span>
                                            </button>
                                        </div>
                                        <input type="text" x-model="block.content_data.url" placeholder="https://youtube.com/... or /storage/media/videos/..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Video Title</label>
                                        <input type="text" x-model="block.content_data.title" placeholder="Aruvipuram Movement Explainer" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                    </div>
                                </div>
                            </template>

                            <!-- Fields for TEXT / SCERT block -->
                            <template x-if="block.type === 'text' || block.type === 'html'">
                                <div class="space-y-3 text-xs">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Block Heading</label>
                                            <input type="text" x-model="block.content_data.title" placeholder="പ്രധാന പോയിന്റുകൾ & SCERT പാഠഭാഗങ്ങൾ" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']">
                                        </div>
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">SCERT Reference</label>
                                            <input type="text" x-model="block.content_data.scert_reference" placeholder="SCERT Social Science Std 9, Chapter 4" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Content Body (HTML / Formatted Bullet Points)</label>
                                        <textarea x-model="block.content_data.body" rows="4" placeholder="<ul><li>പോയിന്റ് 1</li>...</ul>" class="w-full px-3 py-2 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']"></textarea>
                                    </div>
                                </div>
                            </template>

                            <!-- Fields for 3D GLOBE / MAP block -->
                            <template x-if="block.type === 'map_globe'">
                                <div class="space-y-3 text-xs bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-indigo-100 pb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">🌐</span>
                                            <span class="font-black text-indigo-950 uppercase tracking-wide">3D Globe &amp; Map Configuration</span>
                                        </div>
                                        
                                        <!-- Quick Presets Dropdown -->
                                        <div class="flex items-center gap-2">
                                            <label class="font-bold text-slate-600 text-[11px]">Load PSC Preset:</label>
                                            <select 
                                                @change="applyGlobePreset(block, $event.target.value)"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg border border-indigo-200 bg-white text-indigo-900 shadow-xs focus:ring-2 focus:ring-indigo-500"
                                            >
                                                <option value="">-- Choose Preset --</option>
                                                <option value="pacific_reality">🌏 Pacific Reality (USA &amp; Asia Neighbors)</option>
                                                <option value="german_invasion">🇩🇪 German Blitzkrieg (WWII 1939-1941)</option>
                                                <option value="red_sea">🌊 Red Sea &amp; Choke Points (Suez &amp; Bab-el-Mandeb)</option>
                                                <option value="mandela">🇿🇦 Nelson Mandela's Journey (Mvezo to Robben Island)</option>
                                                <option value="kerala_rivers">🌴 Kerala Rivers &amp; Western Ghats Gaps</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Display Mode</label>
                                            <select x-model="block.content_data.mode" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 bg-white font-bold text-slate-800">
                                                <option value="3d_globe">🌐 Interactive 3D Globe</option>
                                                <option value="2d_map">🗺️ 2D Cartographic Map</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Title (English)</label>
                                            <input type="text" x-model="block.content_data.title" placeholder="German Blitzkrieg Routes 1939-1941" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                        </div>
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Title (Malayalam)</label>
                                            <input type="text" x-model="block.content_data.title_malayalam" placeholder="ജർമ്മൻ അധിനിവേശ പാതകൾ" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Center Latitude</label>
                                            <input type="number" step="0.01" x-model.number="block.content_data.center_lat" placeholder="52.52" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                        </div>
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Center Longitude</label>
                                            <input type="number" step="0.01" x-model.number="block.content_data.center_lng" placeholder="13.40" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                        </div>
                                        <div>
                                            <label class="font-bold text-slate-600 block mb-1">Initial Zoom</label>
                                            <input type="number" step="0.1" min="0.5" max="5.0" x-model.number="block.content_data.zoom" placeholder="1.8" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Spatial Mentor Tip / Description (English)</label>
                                        <textarea x-model="block.content_data.description" rows="2" placeholder="Spatial memory context for PSC students..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300"></textarea>
                                    </div>

                                    <div>
                                        <label class="font-bold text-slate-600 block mb-1">Mentor Note (Malayalam)</label>
                                        <textarea x-model="block.content_data.notes_malayalam" rows="2" placeholder="മലയാളം വിശദീകരണം..." class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']"></textarea>
                                    </div>

                                    <!-- Markers List -->
                                    <div class="mt-3 pt-3 border-t border-indigo-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-black text-indigo-900 text-xs">📍 Pinpoints &amp; Geographic Markers</span>
                                            <button 
                                                type="button" 
                                                @click="addMarkerToBlock(block)" 
                                                class="px-2 py-1 bg-white border border-indigo-300 text-indigo-700 hover:bg-indigo-50 rounded text-[11px] font-bold shadow-xs transition"
                                            >
                                                + Add Marker Pin
                                            </button>
                                        </div>

                                        <div class="space-y-2">
                                            <template x-for="(marker, mIdx) in (block.content_data.markers || [])" :key="mIdx">
                                                <div class="p-2.5 bg-white rounded-lg border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-2 items-center">
                                                    <div class="sm:col-span-3">
                                                        <label class="text-[10px] text-slate-500 font-bold block">Label</label>
                                                        <input type="text" x-model="marker.label" placeholder="Poland (Warsaw)" class="w-full px-2 py-1 text-xs rounded border border-slate-300">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="text-[10px] text-slate-500 font-bold block">Lat</label>
                                                        <input type="number" step="0.01" x-model.number="marker.lat" placeholder="52.23" class="w-full px-2 py-1 text-xs rounded border border-slate-300">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="text-[10px] text-slate-500 font-bold block">Lng</label>
                                                        <input type="number" step="0.01" x-model.number="marker.lng" placeholder="21.01" class="w-full px-2 py-1 text-xs rounded border border-slate-300">
                                                    </div>
                                                    <div class="sm:col-span-4">
                                                        <label class="text-[10px] text-slate-500 font-bold block">Exam Note</label>
                                                        <input type="text" x-model="marker.note" placeholder="Invaded Sept 1, 1939 - WWII start" class="w-full px-2 py-1 text-xs rounded border border-slate-300">
                                                    </div>
                                                    <div class="sm:col-span-1 text-right">
                                                        <button 
                                                            type="button" 
                                                            @click="removeMarkerFromBlock(block, mIdx)"
                                                            class="p-1 text-red-500 hover:text-red-700 font-bold text-xs"
                                                            title="Remove Pin"
                                                        >
                                                            ✕
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>
            </div>

            <!-- 3. Question Bank: Diagnostic Hook & Unified MCQs -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-900 flex items-center gap-2">
                            <span>3. Questions by Phase (Diagnostic Hook &amp; Unified MCQs)</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-wider">
                                ⚡ Auto-Synced with OMR
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 font-medium mt-1">
                            Any question added below automatically powers both <strong>Phase 3 (Speed Blitz Practice)</strong> AND <strong>Phase 4 (Timed Kerala PSC OMR Sheet)</strong>. You only edit questions in this one place!
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="addQuestion('reinforcement')" class="px-4 py-2 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white text-xs font-black rounded-xl shadow-md transition flex items-center gap-1.5">
                            <span>+ Add Question</span>
                            <span>⚡</span>
                        </button>
                    </div>
                </div>

                <!-- A. Phase 1 Diagnostic Question -->
                <div class="mb-6 p-4 rounded-xl border-2 border-blue-200 bg-blue-50/40">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase text-blue-800">
                            🎯 Phase 1: Diagnostic Hook Question (Single Pre-Test MCQ)
                        </span>
                        <span class="text-[10px] font-bold bg-blue-100 text-blue-800 px-2 py-0.5 rounded">Diagnostic Phase</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Question (English)</label>
                                <input type="text" x-model="diagnosticQ.question_text" placeholder="In which year did Sree Narayana Guru perform Aruvipuram consecration?" class="w-full px-3 py-1.5 rounded-lg border border-slate-300">
                            </div>
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Question (Malayalam)</label>
                                <input type="text" x-model="diagnosticQ.question_text_malayalam" placeholder="ശ്രീനാരായണഗുരു അരുവിപ്പുറം ശിവപ്രതിഷ്ഠ നടത്തിയ വർഷം?" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-['Noto_Sans_Malayalam']">
                            </div>
                        </div>

                        <!-- 4 Options -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Option A</label>
                                <input type="text" x-model="diagnosticQ.option_a" class="w-full px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                            </div>
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Option B</label>
                                <input type="text" x-model="diagnosticQ.option_b" class="w-full px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                            </div>
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Option C</label>
                                <input type="text" x-model="diagnosticQ.option_c" class="w-full px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                            </div>
                            <div>
                                <label class="font-bold text-slate-600 block mb-1">Option D</label>
                                <input type="text" x-model="diagnosticQ.option_d" class="w-full px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="font-bold text-slate-700 block mb-1">Correct Option *</label>
                                <select x-model="diagnosticQ.correct_option" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 font-black">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="font-bold text-amber-800 block mb-1">PSC Trap Warning Text (Malayalam) *</label>
                                <input type="text" x-model="diagnosticQ.trap_warning_text" placeholder="1887-ൽ അല്ല, 1888-ലെ ശിവരാത്രി ദിനത്തിലാണ്..." class="w-full px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 font-['Noto_Sans_Malayalam']">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- B. Phase 3 & 4 Unified Questions List -->
                <div class="space-y-4">
                    <template x-for="(q, idx) in nonDiagnosticQuestions" :key="idx">
                        <div class="p-4 rounded-xl border border-slate-200 bg-white text-xs shadow-xs">
                            <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-[#0052FF] text-white font-black text-xs flex items-center justify-center shadow-xs" x-text="'Q' + (idx + 1)"></span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-[#0052FF] border border-blue-200 font-black text-[10px] tracking-wide flex items-center gap-1">
                                        <span>⚡ Synced: Phase 3 (Blitz MCQ) &amp; Phase 4 (OMR Sheet)</span>
                                    </span>
                                </div>
                                <button type="button" @click="removeQuestion(idx)" class="text-red-500 hover:text-red-700 font-bold hover:bg-red-50 px-2.5 py-1 rounded-lg transition">
                                    ✕ Remove
                                </button>
                            </div>

                            <div class="space-y-2">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <input type="text" x-model="q.question_text" placeholder="Question text (English)" class="w-full px-2.5 py-1 rounded border border-slate-300 font-medium">
                                    <input type="text" x-model="q.question_text_malayalam" placeholder="Question text (Malayalam)" class="w-full px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <input type="text" x-model="q.option_a" placeholder="A: ..." class="w-full px-2 py-1 rounded border border-slate-200 font-['Noto_Sans_Malayalam']">
                                    <input type="text" x-model="q.option_b" placeholder="B: ..." class="w-full px-2 py-1 rounded border border-slate-200 font-['Noto_Sans_Malayalam']">
                                    <input type="text" x-model="q.option_c" placeholder="C: ..." class="w-full px-2 py-1 rounded border border-slate-200 font-['Noto_Sans_Malayalam']">
                                    <input type="text" x-model="q.option_d" placeholder="D: ..." class="w-full px-2 py-1 rounded border border-slate-200 font-['Noto_Sans_Malayalam']">
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-600">Correct:</span>
                                        <select x-model="q.correct_option" class="px-2 py-0.5 rounded border border-slate-300 font-black">
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                    <input type="text" x-model="q.explanation_malayalam" placeholder="Explanation (Malayalam)" class="flex-grow px-2.5 py-1 rounded border border-slate-300 font-['Noto_Sans_Malayalam']">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            </div> <!-- /MODE A: MANUAL BUILDER -->

            <!-- ======================================================== -->
            <!-- MODE B: CUSTOM CODE STUDIO                               -->
            <!-- ======================================================== -->
            <div x-show="creationMode === 'code'" x-transition class="mb-8">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    
                    <!-- Studio Header Bar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-black text-slate-900">
                                    2. Custom HTML Session Studio
                                </h2>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-wider">
                                    Self-Contained Capsule
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-1">
                                Paste your complete custom HTML here. It can contain your diagnostic hook question, multimedia notes, rapid-fire MCQs, and OMR simulator in one single code block.
                            </p>
                        </div>

                        <!-- Action Controls -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Tab Switcher: Editor vs Live Preview -->
                            <div class="inline-flex p-1 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold">
                                <button 
                                    type="button" 
                                    @click="setTab('editor')" 
                                    :class="codeTab === 'editor' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5 cursor-pointer"
                                >
                                    <span>💻 Code Editor</span>
                                </button>
                                <button 
                                    type="button" 
                                    @click="setTab('preview')" 
                                    :class="codeTab === 'preview' ? 'bg-white text-[#0052FF] shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-3 py-1.5 rounded-md transition flex items-center gap-1.5 cursor-pointer"
                                >
                                    <span>👁️ Live Preview</span>
                                </button>
                            </div>

                            <!-- Boilerplate Generator -->
                            <button 
                                type="button" 
                                @click="insertCapsuleBoilerplate()" 
                                class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-black rounded-lg transition flex items-center gap-1.5 cursor-pointer"
                                title="Insert a pre-built 4-phase PSC template"
                            >
                                <span>🪄 Insert Capsule Boilerplate</span>
                            </button>

                            <!-- Clear Button -->
                            <button 
                                type="button" 
                                @click="clearCustomCode()" 
                                x-show="customHtml && customHtml.length > 0"
                                class="px-2.5 py-1.5 text-slate-400 hover:text-red-600 text-xs font-bold transition cursor-pointer"
                                title="Clear Editor"
                            >
                                <span>🗑️</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: CODE EDITOR -->
                    <div x-show="codeTab === 'editor'" class="space-y-3">
                        <div class="relative rounded-xl overflow-hidden border border-slate-800 bg-slate-950 shadow-inner">
                            <!-- Code Editor Header Bar -->
                            <div class="flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-800 text-[11px] text-slate-400 font-mono">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-yellow-500/80"></span>
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                                    <span class="ml-2 text-slate-300 font-bold">session_capsule.html</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span x-text="(customHtml ? customHtml.length : 0) + ' characters'"></span>
                                    <span>•</span>
                                    <span x-text="(customHtml ? customHtml.split('\n').length : 0) + ' lines'"></span>
                                </div>
                            </div>

                            <!-- Textarea -->
                            <textarea 
                                name="custom_html" 
                                x-model="customHtml" 
                                rows="22" 
                                placeholder="<!-- Paste your custom HTML, CSS, and JS here. You can include hook question, lesson cards, MCQs, and OMR simulator! -->&#10;<div class='psc-capsule'>&#10;   ...&#10;</div>"
                                class="w-full p-4 bg-slate-950 text-emerald-300 font-mono text-xs leading-relaxed focus:outline-none resize-y selection:bg-blue-600 selection:text-white border-0"
                                spellcheck="false"
                            ></textarea>
                        </div>

                        <!-- Integration Guide Alert -->
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="font-black text-blue-950 flex items-center gap-1.5">
                                    <span>⚡ PSCRanker JavaScript Bridge Available</span>
                                </div>
                                <p class="text-[11px] text-slate-600">
                                    You can include standard HTML, Tailwind CSS classes, &lt;style&gt;, and &lt;script&gt; tags. To trigger unit completion & claim XP from your custom buttons, call <code class="bg-blue-100/80 px-1.5 py-0.5 rounded font-mono font-bold text-[#0052FF]">window.PSCRanker?.completeSession()</code>.
                                </p>
                            </div>
                            <button 
                                type="button" 
                                @click="setTab('preview')" 
                                class="shrink-0 px-3.5 py-1.5 bg-[#0052FF] text-white rounded-lg text-xs font-black shadow-xs hover:bg-blue-700 transition cursor-pointer"
                            >
                                Test In Live Preview →
                            </button>
                        </div>
                    </div>

                    <!-- TAB 2: LIVE INTERACTIVE PREVIEW -->
                    <div x-show="codeTab === 'preview'" class="space-y-3">
                        <div class="p-3 bg-slate-100 rounded-xl flex items-center justify-between text-xs text-slate-600 font-bold border border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Interactive Sandbox Preview</span>
                            </div>
                            <button 
                                type="button" 
                                @click="setTab('editor')" 
                                class="text-xs font-black text-[#0052FF] hover:underline cursor-pointer"
                            >
                                ← Back to Code Editor
                            </button>
                        </div>

                        <div class="rounded-2xl border-2 border-slate-200 bg-slate-50/50 p-4 sm:p-6 min-h-[400px]">
                            <iframe 
                                x-ref="previewIframe"
                                class="w-full min-h-[600px] rounded-xl border border-slate-200 bg-white shadow-sm"
                            ></iframe>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-between py-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.sessions.index') }}" class="text-xs font-bold text-slate-600 hover:underline">
                        ← Cancel & Back
                    </a>

                    @if($isEdit)
                        <button 
                            type="button" 
                            onclick="if(confirm('Are you sure you want to permanently delete session #{{ $session->id }} (\'{{ addslashes($session->title) }}\')? All contents, questions, and student progress for this session will be permanently deleted.')) { document.getElementById('admin-delete-session-form-{{ $session->id }}').submit(); }"
                            class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 text-xs font-black rounded-lg transition border border-red-200 cursor-pointer flex items-center gap-1.5"
                        >
                            <span>🗑️ Delete Session</span>
                        </button>
                    @endif
                </div>

                <button 
                    type="submit" 
                    class="px-8 py-3.5 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer"
                >
                    <span>Save Learning Session</span>
                    <span>⚡</span>
                </button>
            </div>

        </form>

        @if($isEdit)
            <form 
                id="admin-delete-session-form-{{ $session->id }}" 
                action="{{ route('admin.sessions.destroy', $session) }}" 
                method="POST" 
                class="hidden"
            >
                @csrf
                @method('DELETE')
            </form>
        @endif

    </div>

    <!-- Media Library Picker Modal -->
    <div 
        x-show="showMediaModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.outside="showMediaModal = false"
            class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden relative"
        >
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">📁</span>
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-slate-900">
                            Select Media from Library
                        </h3>
                        <p class="text-[11px] text-slate-500 font-medium">
                            Choose an existing file or upload a new photo, audio, or video directly.
                        </p>
                    </div>
                </div>

                <button 
                    type="button"
                    @click="showMediaModal = false" 
                    class="w-8 h-8 rounded-full bg-white hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center font-bold text-sm transition"
                >
                    ✕
                </button>
            </div>

            <!-- Modal Subheader: Filter & Direct Upload Bar -->
            <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-white">
                
                <!-- Type Tabs -->
                <div class="flex items-center gap-1.5 text-xs font-bold">
                    <button 
                        type="button" 
                        @click="mediaFilterType = 'all'; fetchMediaItems()"
                        :class="mediaFilterType === 'all' ? 'bg-slate-900 text-white font-black' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        All
                    </button>
                    <button 
                        type="button" 
                        @click="mediaFilterType = 'image'; fetchMediaItems()"
                        :class="mediaFilterType === 'image' ? 'bg-purple-600 text-white font-black' : 'bg-purple-50 text-purple-700 hover:bg-purple-100'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        🖼️ Photos
                    </button>
                    <button 
                        type="button" 
                        @click="mediaFilterType = 'audio'; fetchMediaItems()"
                        :class="mediaFilterType === 'audio' ? 'bg-blue-600 text-white font-black' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        🎙️ Audio
                    </button>
                    <button 
                        type="button" 
                        @click="mediaFilterType = 'video'; fetchMediaItems()"
                        :class="mediaFilterType === 'video' ? 'bg-red-600 text-white font-black' : 'bg-red-50 text-red-700 hover:bg-red-100'"
                        class="px-3 py-1.5 rounded-lg transition"
                    >
                        🎬 Videos
                    </button>
                </div>

                <!-- Instant Upload Input & Button -->
                <div class="flex items-center gap-2">
                    <label class="cursor-pointer px-3.5 py-1.5 bg-[#0052FF] hover:bg-blue-700 text-white text-xs font-black rounded-lg transition flex items-center gap-1.5 shadow-sm">
                        <span x-show="!isUploadingInModal">⬆️ Upload & Use</span>
                        <span x-show="isUploadingInModal" class="flex items-center gap-1">
                            <span class="w-3 h-3 border-2 border-white border-t-yellow-400 rounded-full animate-spin"></span>
                            <span>Uploading...</span>
                        </span>
                        <input 
                            type="file" 
                            class="hidden" 
                            accept="image/*,audio/*,video/*"
                            :disabled="isUploadingInModal"
                            @change="uploadDirectFromModal($event)"
                        >
                    </label>
                </div>

            </div>

            <!-- Media Grid Body -->
            <div class="p-4 sm:p-5 overflow-y-auto flex-grow bg-slate-50/50 min-h-[300px]">
                
                <!-- Loading state -->
                <template x-if="isLoadingMedia">
                    <div class="py-12 text-center">
                        <div class="w-8 h-8 border-3 border-blue-600 border-t-yellow-400 rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-xs text-slate-500 font-bold">Loading media items...</p>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="!isLoadingMedia && mediaItems.length === 0">
                    <div class="py-12 text-center text-slate-500">
                        <span class="text-3xl block mb-2">📁</span>
                        <p class="text-xs font-bold text-slate-700">No media found for this category</p>
                        <p class="text-[11px] text-slate-400 mt-1">Use the "Upload & Use" button above to upload a file directly.</p>
                    </div>
                </template>

                <!-- Items Grid -->
                <template x-if="!isLoadingMedia && mediaItems.length > 0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <template x-for="item in mediaItems" :key="item.id">
                            <div 
                                @click="selectMediaItem(item)"
                                class="p-2.5 rounded-xl border-2 border-slate-200 bg-white hover:border-[#0052FF] hover:shadow-md cursor-pointer transition flex flex-col justify-between group active:scale-95"
                            >
                                <div class="h-28 rounded-lg bg-slate-100 overflow-hidden flex items-center justify-center relative mb-2">
                                    <template x-if="item.file_type === 'image'">
                                        <img :src="item.url" class="w-full h-full object-cover" loading="lazy">
                                    </template>
                                    <template x-if="item.file_type === 'audio'">
                                        <div class="text-3xl text-blue-600">🎙️</div>
                                    </template>
                                    <template x-if="item.file_type === 'video'">
                                        <div class="text-3xl text-red-600">🎬</div>
                                    </template>
                                    <template x-if="item.file_type === 'document'">
                                        <div class="text-3xl text-slate-400">📄</div>
                                    </template>
                                    <span 
                                        class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase text-white"
                                        :class="{
                                            'bg-purple-600': item.file_type === 'image',
                                            'bg-blue-600': item.file_type === 'audio',
                                            'bg-red-600': item.file_type === 'video',
                                            'bg-slate-600': item.file_type === 'document'
                                        }"
                                        x-text="item.file_type"
                                    ></span>
                                </div>

                                <div class="text-[11px] font-bold text-slate-800 truncate" x-text="item.name"></div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="item.formatted_size"></div>

                                <div class="mt-2 text-center">
                                    <span class="text-[10px] font-black text-[#0052FF] group-hover:underline">
                                        ✓ Select Item
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-3.5 bg-white border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Tip: Click any media card to instantly insert it into the active block.</span>
                <button 
                    type="button" 
                    @click="showMediaModal = false"
                    class="px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition"
                >
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function adminSessionBuilder(initial) {
    return {
        creationMode: initial.creationMode || 'manual',
        customHtml: initial.customHtml || '',
        codeTab: 'editor',
        featureImage: initial.featureImage || '',
        isUploadingFeatureImage: false,

        // Auto-sequencing & Mixed Practice Train state
        categoryId: initial.categoryId || '',
        nextOrdersByCategory: initial.nextOrdersByCategory || {},
        defaultNextOrder: initial.defaultNextOrder || 1,
        order: (initial.order !== null && initial.order !== '') ? initial.order : '',
        inGeneralStream: initial.inGeneralStream !== undefined ? Boolean(initial.inGeneralStream) : true,
        generalStreamOrder: (initial.generalStreamOrder !== null && initial.generalStreamOrder !== '') ? initial.generalStreamOrder : '',
        nextTrainOrder: initial.nextTrainOrder || 1,
        isEdit: Boolean(initial.isEdit),

        init() {
            if (!this.isEdit) {
                if (!this.order) {
                    this.updateAutoOrder();
                }
                if (!this.generalStreamOrder) {
                    this.generalStreamOrder = this.nextTrainOrder;
                }
            }
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

        setCreationMode(mode) {
            this.creationMode = mode;
        },

        setTab(tab) {
            this.codeTab = tab;
            if (tab === 'preview') {
                this.renderPreview();
            }
        },

        renderPreview() {
            this.$nextTick(() => {
                const iframe = this.$refs.previewIframe;
                if (iframe) {
                    iframe.srcdoc = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><script src="https://cdn.tailwindcss.com"><\/script><style>body { font-family: sans-serif; background-color: transparent; padding: 1rem; }</style></head><body>' + (this.customHtml || '<p style="color:#888;text-align:center;padding:2rem;">No custom code entered yet.</p>') + '</body></html>';
                }
            });
        },

        insertCapsuleBoilerplate() {
            if (this.customHtml && this.customHtml.trim().length > 0) {
                if (!confirm('This will replace your current custom code with the 4-Phase Capsule Boilerplate (Hook Question + Lesson + MCQs + OMR Simulator). Continue?')) {
                    return;
                }
            }
            this.customHtml = this.getCapsuleBoilerplateCode();
        },

        clearCustomCode() {
            if (confirm('Clear custom HTML code?')) {
                this.customHtml = '';
            }
        },

        getCapsuleBoilerplateCode() {
            return `<!-- KERALA PSC 4-SCREEN SEQUENTIAL CAPSULE -->
<div id="psc-capsule-container" class="psc-container">

    <!-- TOP BAR: PROGRESS & XP -->
    <div class="psc-topbar">
        <div class="psc-title-tag">
            <span class="psc-badge-icon">⚡</span>
            <span>PSC Capsule • Sports Autobiographies</span>
        </div>
        <div class="psc-xp-counter">
            <span>🏆</span>
            <span id="psc-xp-val">0</span> XP
        </div>
    </div>

    <!-- SEQUENTIAL STEPPER -->
    <div class="psc-stepper">
        <button type="button" id="psc-pill-hook" class="psc-step-pill active" onclick="window.pscGoTo('hook')">
            <span class="psc-pill-num">1</span> Hook Question
        </button>
        <button type="button" id="psc-pill-lesson" class="psc-step-pill" onclick="window.pscGoTo('lesson')">
            <span class="psc-pill-num">2</span> Lessons
        </button>
        <button type="button" id="psc-pill-mcqs" class="psc-step-pill" onclick="window.pscGoTo('mcqs')">
            <span class="psc-pill-num">3</span> Practice MCQs
        </button>
        <button type="button" id="psc-pill-omr" class="psc-step-pill" onclick="window.pscGoTo('omr')">
            <span class="psc-pill-num">4</span> OMR Sheet
        </button>
    </div>

    <!-- PROGRESS LINE -->
    <div class="psc-progress-track">
        <div id="psc-progress-bar" class="psc-progress-fill" style="width: 25%;"></div>
    </div>

    <!-- ================================================================= -->
    <!-- SCREEN 1: HOOK QUESTION FIRST                                     -->
    <!-- ================================================================= -->
    <div id="psc-screen-hook" class="psc-screen" style="display: block;">
        <div class="psc-card">
            <div class="psc-q-meta">
                <span class="psc-tag psc-tag-pyq">Kerala PSC Previous Year Question</span>
                <span class="psc-tag psc-tag-trap">Trap Detector</span>
            </div>

            <h3 class="psc-question-en">
                Whose autobiography is "Stumped, Life behind and beyond Twenty Two Yards"?
            </h3>
            <h4 class="psc-question-ml">
                ''സ്റ്റംപ്ഡ്, ലൈഫ് ബിഹൈൻഡ് ആൻഡ് ബിയോണ്ട്, ട്വന്റി ടു യാർഡ്സ്'' - ഇത് ആരുടെ ആത്മകഥയാണ്?
            </h4>

            <div class="psc-options-grid" id="psc-hook-opts">
                <button type="button" class="psc-opt-btn" onclick="window.pscSelectHook('A')">
                    <span class="psc-opt-badge">A</span>
                    <span class="psc-opt-label">
                        <strong>Mahendra Singh Dhoni</strong>
                        <small>മഹേന്ദ്രസിംഗ് ധോണി</small>
                    </span>
                </button>

                <button type="button" class="psc-opt-btn" onclick="window.pscSelectHook('B')">
                    <span class="psc-opt-badge">B</span>
                    <span class="psc-opt-label">
                        <strong>Syed Kirmani</strong>
                        <small>സയിദ് കിർമാനി</small>
                    </span>
                </button>

                <button type="button" class="psc-opt-btn" onclick="window.pscSelectHook('C')">
                    <span class="psc-opt-badge">C</span>
                    <span class="psc-opt-label">
                        <strong>Nayan Mongia</strong>
                        <small>നയൻ മോംഗിയ</small>
                    </span>
                </button>

                <button type="button" class="psc-opt-btn" onclick="window.pscSelectHook('D')">
                    <span class="psc-opt-badge">D</span>
                    <span class="psc-opt-label">
                        <strong>Kiran More</strong>
                        <small>കിരൺ മോറെ</small>
                    </span>
                </button>
            </div>

            <!-- Hook Feedback -->
            <div id="psc-hook-feedback" class="psc-feedback" style="display: none;">
                <div id="psc-hook-feedback-content"></div>
                
                <div class="psc-actions-row">
                    <button type="button" class="psc-btn-primary psc-pulse" onclick="window.pscGoTo('lesson')">
                        അടുത്ത സ്‌ക്രീൻ: പാഠം പഠിക്കാം (Next Screen: Lessons) ➔
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SCREEN 2: HIGH-YIELD LESSONS                                      -->
    <!-- ================================================================= -->
    <div id="psc-screen-lesson" class="psc-screen" style="display: none;">
        <div class="psc-card">
            <div class="psc-q-meta">
                <span class="psc-tag psc-tag-lesson">📖 High-Yield Micro-Lesson</span>
                <span class="psc-tag psc-tag-scert">Rank Maker Facts</span>
            </div>

            <h3 class="psc-lesson-title">
                കായിക താരങ്ങളും പ്രശസ്തമായ ആത്മകഥകളും (Sports Autobiographies)
            </h3>

            <!-- Syed Kirmani Spotlight Card -->
            <div class="psc-spotlight-box">
                <div class="psc-spotlight-header">
                    <span class="psc-spotlight-avatar">🏏</span>
                    <div>
                        <h4 class="psc-spotlight-name">സയിദ് കിർമാനി (Syed Kirmani)</h4>
                        <p class="psc-spotlight-sub">1983 ലോകകപ്പ് ചാമ്പ്യൻ വിക്കറ്റ് കീപ്പർ</p>
                    </div>
                </div>
                <ul class="psc-spotlight-points">
                    <li>1983-ൽ കപിൽ ദേവിന്റെ നേതൃത്വത്തിൽ ഇന്ത്യ ലോകകപ്പ് നേടുമ്പോൾ ഇന്ത്യയുടെ വിക്കറ്റ് കീപ്പറായിരുന്നു.</li>
                    <li>ടൂർണമെന്റിലെ മികച്ച വിക്കറ്റ് കീപ്പർക്കുള്ള പുരസ്കാരം (Best Wicket-keeper) നേടി.</li>
                    <li>അദ്ദേഹത്തിന്റെ പ്രശസ്തമായ ആത്മകഥയാണ് <strong>"Stumped: Life Behind and Beyond the Twenty-Two Yards"</strong>.</li>
                    <li>1982-ൽ പത്മശ്രീയും, 2015-ൽ സി.കെ. നായിഡു ലൈഫ് ടൈം അച്ചീവ്മെന്റ് അവാർഡും ലഭിച്ചു.</li>
                </ul>
                <div class="psc-mnemonic-pill">
                    💡 <strong>PSC ഓർമ്മക്കൂട്ട് (Mnemonic):</strong> വിക്കറ്റിന് പിന്നിൽ <em>'സ്റ്റംപ്ഡ്'</em> ആകുന്നത് കീപ്പറായ <strong>കിർമാനി</strong>!
                </div>
            </div>

            <!-- High-Yield PSC Repeated Table -->
            <div class="psc-table-title">🔥 കേരള PSC ആവർത്തിച്ച് ചോദിക്കുന്ന മറ്റ് സ്പോർട്സ് ആത്മകഥകൾ:</div>
            <div class="psc-table-container">
                <table class="psc-data-table">
                    <thead>
                        <tr>
                            <th>ആത്മകഥ (Autobiography)</th>
                            <th>കായിക താരം (Sports Person)</th>
                            <th>വിഭാഗം</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Playing It My Way</strong></td>
                            <td>സച്ചിൻ തെണ്ടുൽക്കർ (Sachin Tendulkar)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>Straight from the Heart</strong></td>
                            <td>കപിൽ ദേവ് (Kapil Dev)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>Sunny Days / Idols</strong></td>
                            <td>സുനിൽ ഗവാസ്കർ (Sunil Gavaskar)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>The Test of My Life</strong></td>
                            <td>യുവരാജ് സിംഗ് (Yuvraj Singh)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>281 and Beyond</strong></td>
                            <td>വി. വി. എസ്. ലക്ഷ്മൺ (V.V.S. Laxman)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>A Century is Not Enough</strong></td>
                            <td>സൗരവ് ഗാംഗുലി (Sourav Ganguly)</td>
                            <td>ക്രിക്കറ്റ്</td>
                        </tr>
                        <tr>
                            <td><strong>Golden Girl</strong></td>
                            <td>പി. ടി. ഉഷ (P. T. Usha)</td>
                            <td>അത്‌ലറ്റിക്സ്</td>
                        </tr>
                        <tr>
                            <td><strong>The Race of My Life</strong></td>
                            <td>മിൽഖാ സിംഗ് (Milkha Singh)</td>
                            <td>അത്‌ലറ്റിക്സ്</td>
                        </tr>
                        <tr>
                            <td><strong>Unbreakable</strong></td>
                            <td>എം. സി. മേരി കോം (Mary Kom)</td>
                            <td>ബോക്സിംഗ്</td>
                        </tr>
                        <tr>
                            <td><strong>Ace Against Odds</strong></td>
                            <td>സാനിയ മിർസ (Sania Mirza)</td>
                            <td>ടെന്നീസ്</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="psc-nav-buttons">
                <button type="button" class="psc-btn-secondary" onclick="window.pscGoTo('hook')">
                    ⬅ തിരികെ ചോദ്യത്തിലേക്ക് (Back to Hook)
                </button>
                <button type="button" class="psc-btn-primary" onclick="window.pscGoTo('mcqs')">
                    അടുത്ത സ്‌ക്രീൻ: MCQs പരീക്ഷിക്കാം (Next Screen: MCQs) ➔
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SCREEN 3: RETENTION MCQS (ONE QUESTION AT A TIME)                 -->
    <!-- ================================================================= -->
    <div id="psc-screen-mcqs" class="psc-screen" style="display: none;">
        <div class="psc-card">
            
            <!-- MCQ 1: Single Screen -->
            <div id="psc-mcq-card-1" class="psc-mcq-single-card" style="display: block;">
                <div class="psc-q-meta">
                    <span class="psc-tag psc-tag-quiz">⚡ Rapid Practice MCQ</span>
                    <span class="psc-tag">Question 1 of 2</span>
                </div>

                <div class="psc-drill-header">
                    <span class="psc-drill-num">Q1</span>
                    <div>
                        <h3 class="psc-question-en" style="font-size: 16px; margin-bottom: 4px;">
                            Whose autobiography is "Straight from the Heart"?
                        </h3>
                        <h4 class="psc-question-ml" style="font-size: 15px; margin-bottom: 16px;">
                            'സ്ട്രെയിറ്റ് ഫ്രം ദി ഹാർട്ട്' (Straight from the Heart) ആരുടെ ആത്മകഥയാണ്?
                        </h4>
                    </div>
                </div>

                <div class="psc-options-grid" id="psc-drill-opts-1">
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(1, 'A', 'B')">
                        <span class="psc-opt-badge">A</span>
                        <span class="psc-opt-label">
                            <strong>Sunil Gavaskar</strong>
                            <small>സുനിൽ ഗവാസ്കർ</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(1, 'B', 'B')">
                        <span class="psc-opt-badge">B</span>
                        <span class="psc-opt-label">
                            <strong>Kapil Dev</strong>
                            <small>കപിൽ ദേവ്</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(1, 'C', 'B')">
                        <span class="psc-opt-badge">C</span>
                        <span class="psc-opt-label">
                            <strong>Ravi Shastri</strong>
                            <small>രവി ശാസ്ത്രി</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(1, 'D', 'B')">
                        <span class="psc-opt-badge">D</span>
                        <span class="psc-opt-label">
                            <strong>Mohinder Amarnath</strong>
                            <small>മൊഹീന്ദർ അമർനാഥ്</small>
                        </span>
                    </button>
                </div>

                <div id="psc-drill-fb-1" class="psc-feedback" style="display:none;"></div>

                <div class="psc-nav-buttons">
                    <button type="button" class="psc-btn-secondary" onclick="window.pscGoTo('lesson')">
                        ⬅ പാഠത്തിലേക്ക് (Back to Lessons)
                    </button>
                    <button type="button" id="psc-next-mcq-btn-1" class="psc-btn-primary" onclick="window.pscGoToMcq(2)" style="display: none;">
                        അടുത്ത ചോദ്യം (Next Question 2/2) ➔
                    </button>
                </div>
            </div>

            <!-- MCQ 2: Single Screen -->
            <div id="psc-mcq-card-2" class="psc-mcq-single-card" style="display: none;">
                <div class="psc-q-meta">
                    <span class="psc-tag psc-tag-quiz">⚡ Rapid Practice MCQ</span>
                    <span class="psc-tag">Question 2 of 2</span>
                </div>

                <div class="psc-drill-header">
                    <span class="psc-drill-num">Q2</span>
                    <div>
                        <h3 class="psc-question-en" style="font-size: 16px; margin-bottom: 4px;">
                            Whose autobiography is titled "The Test of My Life"?
                        </h3>
                        <h4 class="psc-question-ml" style="font-size: 15px; margin-bottom: 16px;">
                            ക്യാൻസറിനെ അതിജീവിച്ച് തിരിച്ചുവന്ന കഥ പറയുന്ന 'The Test of My Life' ആരുടെ പുസ്തകമാണ്?
                        </h4>
                    </div>
                </div>

                <div class="psc-options-grid" id="psc-drill-opts-2">
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(2, 'A', 'A')">
                        <span class="psc-opt-badge">A</span>
                        <span class="psc-opt-label">
                            <strong>Yuvraj Singh</strong>
                            <small>യുവരാജ് സിംഗ്</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(2, 'B', 'A')">
                        <span class="psc-opt-badge">B</span>
                        <span class="psc-opt-label">
                            <strong>Gautam Gambhir</strong>
                            <small>ഗൗതം ഗംഭീർ</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(2, 'C', 'A')">
                        <span class="psc-opt-badge">C</span>
                        <span class="psc-opt-label">
                            <strong>Suresh Raina</strong>
                            <small>സുരേഷ് റെയ്ന</small>
                        </span>
                    </button>
                    <button type="button" class="psc-opt-btn" onclick="window.pscCheckMcq(2, 'D', 'A')">
                        <span class="psc-opt-badge">D</span>
                        <span class="psc-opt-label">
                            <strong>Harbhajan Singh</strong>
                            <small>ഹർഭജൻ സിംഗ്</small>
                        </span>
                    </button>
                </div>

                <div id="psc-drill-fb-2" class="psc-feedback" style="display:none;"></div>

                <div class="psc-nav-buttons">
                    <button type="button" class="psc-btn-secondary" onclick="window.pscGoToMcq(1)">
                        ⬅ മുൻപത്തെ ചോദ്യം (Question 1)
                    </button>
                    <button type="button" id="psc-to-omr-btn" class="psc-btn-primary" onclick="window.pscGoTo('omr')" style="display: none;">
                        അടുത്ത സ്‌ക്രീൻ: OMR എക്സാം ഷീറ്റ് (Next Screen: OMR Sheet) ➔
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================================================================= -->
    <!-- SCREEN 4: AUTHENTIC KERALA PSC OMR SIMULATOR                      -->
    <!-- ================================================================= -->
    <div id="psc-screen-omr" class="psc-screen" style="display: none;">
        <div class="psc-card psc-omr-card">
            
            <!-- OMR Header -->
            <div class="psc-omr-top">
                <div class="psc-omr-emblem">⚖️</div>
                <div>
                    <div class="psc-omr-govt">KERALA PUBLIC SERVICE COMMISSION</div>
                    <div class="psc-omr-subtitle">OMR ANSWER SHEET • CONFIDENTIAL EXAM SIMULATOR</div>
                </div>
            </div>

            <div class="psc-omr-neg-rule">
                ⚠️ <strong>Strict PSC Evaluation:</strong> Correct Bubble = <strong>+1.00 Mark</strong> | Wrong Bubble = <strong>-0.33 Mark</strong> | Unattempted = <strong>0.00</strong>
            </div>

            <!-- QUESTION PAPER BOOKLET WITH NORMAL CHOICES -->
            <div class="psc-booklet">
                <div class="psc-booklet-badge">QUESTION BOOKLET • SERIES A</div>

                <!-- OMR Question 1 -->
                <div class="psc-omr-q-item" id="psc-omr-q-item-1">
                    <div class="psc-omr-q-header">
                        <span class="psc-omr-q-num">1</span>
                        <div class="psc-omr-q-text">
                            <div class="psc-omr-q-en">Whose autobiography is "Stumped, Life behind and beyond Twenty Two Yards"?</div>
                            <div class="psc-omr-q-ml">''സ്റ്റംപ്ഡ്, ലൈഫ് ബിഹൈൻഡ് ആൻഡ് ബിയോണ്ട്, ട്വന്റി ടു യാർഡ്സ്'' - ഇത് ആരുടെ ആത്മകഥയാണ്?</div>
                        </div>
                    </div>

                    <!-- Normal Choices for Question 1 -->
                    <div class="psc-omr-choices-list">
                        <div class="psc-omr-choice-row" data-q="1" data-opt="A" onclick="window.pscBubble(1, 'A')">
                            <span class="psc-choice-key">(A)</span>
                            <span class="psc-choice-text">Mahendra Singh Dhoni <small class="psc-choice-sub">(മഹേന്ദ്രസിംഗ് ധോണി)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="1" data-opt="B" onclick="window.pscBubble(1, 'B')">
                            <span class="psc-choice-key">(B)</span>
                            <span class="psc-choice-text">Syed Kirmani <small class="psc-choice-sub">(സയിദ് കിർമാനി)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="1" data-opt="C" onclick="window.pscBubble(1, 'C')">
                            <span class="psc-choice-key">(C)</span>
                            <span class="psc-choice-text">Nayan Mongia <small class="psc-choice-sub">(നയൻ മോംഗിയ)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="1" data-opt="D" onclick="window.pscBubble(1, 'D')">
                            <span class="psc-choice-key">(D)</span>
                            <span class="psc-choice-text">Kiran More <small class="psc-choice-sub">(കിരൺ മോറെ)</small></span>
                        </div>
                    </div>

                    <!-- Integrated OMR Bubble Row -->
                    <div class="psc-omr-row-strip">
                        <span class="psc-strip-label">OMR Bubble Row 1:</span>
                        <div class="psc-omr-bubbles">
                            <button type="button" class="psc-bubble" data-q="1" data-opt="A" onclick="window.pscBubble(1, 'A')">A</button>
                            <button type="button" class="psc-bubble" data-q="1" data-opt="B" onclick="window.pscBubble(1, 'B')">B</button>
                            <button type="button" class="psc-bubble" data-q="1" data-opt="C" onclick="window.pscBubble(1, 'C')">C</button>
                            <button type="button" class="psc-bubble" data-q="1" data-opt="D" onclick="window.pscBubble(1, 'D')">D</button>
                        </div>
                    </div>
                </div>

                <!-- OMR Question 2 -->
                <div class="psc-omr-q-item" id="psc-omr-q-item-2">
                    <div class="psc-omr-q-header">
                        <span class="psc-omr-q-num">2</span>
                        <div class="psc-omr-q-text">
                            <div class="psc-omr-q-en">Who authored the autobiography "Straight from the Heart"?</div>
                            <div class="psc-omr-q-ml">'സ്ട്രെയിറ്റ് ഫ്രം ദി ഹാർട്ട്' (Straight from the Heart) ആരുടെ ആത്മകഥയാണ്?</div>
                        </div>
                    </div>

                    <!-- Normal Choices for Question 2 -->
                    <div class="psc-omr-choices-list">
                        <div class="psc-omr-choice-row" data-q="2" data-opt="A" onclick="window.pscBubble(2, 'A')">
                            <span class="psc-choice-key">(A)</span>
                            <span class="psc-choice-text">Sunil Gavaskar <small class="psc-choice-sub">(സുനിൽ ഗവാസ്കർ)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="2" data-opt="B" onclick="window.pscBubble(2, 'B')">
                            <span class="psc-choice-key">(B)</span>
                            <span class="psc-choice-text">Kapil Dev <small class="psc-choice-sub">(കപിൽ ദേവ്)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="2" data-opt="C" onclick="window.pscBubble(2, 'C')">
                            <span class="psc-choice-key">(C)</span>
                            <span class="psc-choice-text">Ravi Shastri <small class="psc-choice-sub">(രവി ശാസ്ത്രി)</small></span>
                        </div>
                        <div class="psc-omr-choice-row" data-q="2" data-opt="D" onclick="window.pscBubble(2, 'D')">
                            <span class="psc-choice-key">(D)</span>
                            <span class="psc-choice-text">Mohinder Amarnath <small class="psc-choice-sub">(മൊഹീന്ദർ അമർനാഥ്)</small></span>
                        </div>
                    </div>

                    <!-- Integrated OMR Bubble Row -->
                    <div class="psc-omr-row-strip">
                        <span class="psc-strip-label">OMR Bubble Row 2:</span>
                        <div class="psc-omr-bubbles">
                            <button type="button" class="psc-bubble" data-q="2" data-opt="A" onclick="window.pscBubble(2, 'A')">A</button>
                            <button type="button" class="psc-bubble" data-q="2" data-opt="B" onclick="window.pscBubble(2, 'B')">B</button>
                            <button type="button" class="psc-bubble" data-q="2" data-opt="C" onclick="window.pscBubble(2, 'C')">C</button>
                            <button type="button" class="psc-bubble" data-q="2" data-opt="D" onclick="window.pscBubble(2, 'D')">D</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Evaluate Action -->
            <div class="psc-omr-eval-wrap">
                <button type="button" class="psc-btn-omr-submit" onclick="window.pscEvaluateOmr()">
                    <span>Evaluate OMR Sheet ⚡</span>
                </button>
            </div>

            <!-- Evaluation Result Container -->
            <div id="psc-omr-result" class="psc-omr-result-box" style="display: none;"></div>

            <!-- Final Completion CTA -->
            <div class="psc-complete-card">
                <div class="psc-complete-icon">🚀</div>
                <h4>Capsule Completed!</h4>
                <p>You have mastered Kerala PSC Sports Autobiographies with negative marking mastery.</p>
                
                <button type="button" class="psc-btn-complete psc-pulse" onclick="window.pscFinishCapsule()">
                    സെഷൻ പൂർത്തിയാക്കി 250 XP നേടുക (Claim 250 XP &amp; Complete) 🚀
                </button>
            </div>

            <div class="psc-nav-buttons" style="margin-top: 15px;">
                <button type="button" class="psc-btn-secondary" onclick="window.pscGoTo('mcqs')">
                    ⬅ MCQs ലേക്ക് (Back to MCQs)
                </button>
            </div>
        </div>
    </div>

</div>

<!-- STYLES -->
<style>
.psc-container {
    max-width: 760px;
    margin: 0 auto;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Noto Sans Malayalam", sans-serif;
    color: #0f172a;
    line-height: 1.5;
}
.psc-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #0f172a;
    color: #fff;
    padding: 10px 18px;
    border-radius: 16px 16px 0 0;
}
.psc-title-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
}
.psc-badge-icon {
    background: #f59e0b;
    color: #000;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}
.psc-xp-counter {
    background: rgba(255,255,255,0.15);
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    color: #fcd34d;
}
.psc-stepper {
    display: flex;
    background: #1e293b;
    padding: 6px;
    gap: 6px;
    overflow-x: auto;
}
.psc-step-pill {
    flex: 1;
    min-width: 120px;
    border: none;
    background: rgba(255,255,255,0.08);
    color: #94a3b8;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.psc-step-pill:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
}
.psc-step-pill.active {
    background: #0052FF;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 82, 255, 0.35);
}
.psc-step-pill.completed {
    background: #059669;
    color: #ffffff;
}
.psc-pill-num {
    background: rgba(0,0,0,0.25);
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}
.psc-progress-track {
    height: 4px;
    background: #e2e8f0;
    overflow: hidden;
}
.psc-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0052FF, #10b981);
    transition: width 0.3s ease;
}
.psc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-top: none;
    border-radius: 0 0 16px 16px;
    padding: 24px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
}
.psc-q-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 14px;
}
.psc-tag {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 9px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #475569;
}
.psc-tag-pyq {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.psc-tag-trap {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.psc-tag-lesson {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.psc-tag-scert {
    background: #faf5ff;
    color: #6b21a8;
    border: 1px solid #e9d5ff;
}
.psc-tag-quiz {
    background: #fff1f2;
    color: #9f1239;
    border: 1px solid #fecdd3;
}
.psc-question-en {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px 0;
    line-height: 1.35;
}
.psc-question-ml {
    font-size: 16px;
    font-weight: 700;
    color: #0052FF;
    margin: 0 0 20px 0;
    line-height: 1.4;
}
.psc-options-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    margin-bottom: 16px;
}
@media (min-width: 640px) {
    .psc-options-grid {
        grid-template-columns: 1fr 1fr;
    }
}
.psc-opt-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    color: #1e293b;
}
.psc-opt-btn:hover {
    border-color: #0052FF;
    background: #eff6ff;
}
.psc-opt-badge {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 13px;
    color: #334155;
    flex-shrink: 0;
}
.psc-opt-label strong {
    display: block;
    font-size: 13px;
    font-weight: 800;
}
.psc-opt-label small {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-top: 2px;
}
.psc-opt-btn.correct {
    background: #ecfdf5 !important;
    border-color: #10b981 !important;
}
.psc-opt-btn.correct .psc-opt-badge {
    background: #10b981 !important;
    color: #ffffff !important;
}
.psc-opt-btn.wrong {
    background: #fef2f2 !important;
    border-color: #ef4444 !important;
}
.psc-opt-btn.wrong .psc-opt-badge {
    background: #ef4444 !important;
    color: #ffffff !important;
}
.psc-feedback {
    border-radius: 12px;
    padding: 16px;
    margin-top: 15px;
    animation: pscFadeIn 0.3s ease;
}
.psc-fb-correct {
    background: #ecfdf5;
    border: 1px solid #6ee7b7;
    color: #065f46;
}
.psc-fb-trap {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #991b1b;
}
.psc-actions-row {
    margin-top: 16px;
    display: flex;
    justify-content: flex-end;
}
.psc-btn-primary {
    background: linear-gradient(135deg, #0052FF, #1d4ed8);
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(0, 82, 255, 0.3);
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.psc-btn-primary:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
}
.psc-btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}
.psc-btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.psc-nav-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e2e8f0;
}
.psc-pulse {
    animation: pscPulse 2s infinite;
}
@keyframes pscPulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 82, 255, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(0, 82, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 82, 255, 0); }
}
@keyframes pscFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Screen 2 Styles */
.psc-lesson-title {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 14px 0;
}
.psc-spotlight-box {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 20px;
}
.psc-spotlight-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.psc-spotlight-avatar {
    font-size: 32px;
    background: #dcfce7;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.psc-spotlight-name {
    font-size: 16px;
    font-weight: 800;
    color: #14532d;
    margin: 0;
}
.psc-spotlight-sub {
    font-size: 12px;
    color: #166534;
    margin: 2px 0 0 0;
    font-weight: 600;
}
.psc-spotlight-points {
    margin: 0 0 12px 0;
    padding-left: 18px;
    font-size: 13px;
    color: #166534;
    line-height: 1.6;
}
.psc-spotlight-points li {
    margin-bottom: 6px;
}
.psc-mnemonic-pill {
    background: #ffffff;
    border: 1px solid #bbf7d0;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    color: #15803d;
}
.psc-table-title {
    font-size: 14px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 10px;
}
.psc-table-container {
    overflow-x: auto;
    margin-bottom: 15px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}
.psc-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    text-align: left;
}
.psc-data-table th {
    background: #f8fafc;
    padding: 10px 12px;
    font-weight: 800;
    color: #475569;
    border-bottom: 1px solid #e2e8f0;
}
.psc-data-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.psc-data-table tr:last-child td {
    border-bottom: none;
}
.psc-data-table tr:hover td {
    background: #f8fafc;
}

/* Single Screen MCQ Styles */
.psc-mcq-single-card {
    animation: pscFadeIn 0.3s ease;
}
.psc-drill-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 8px;
}
.psc-drill-num {
    background: #0052FF;
    color: #fff;
    font-size: 12px;
    font-weight: 900;
    padding: 3px 9px;
    border-radius: 8px;
    flex-shrink: 0;
    margin-top: 2px;
}

/* Screen 4 OMR Styles */
.psc-omr-card {
    background: #fffdf5;
    border: 2px solid #1e293b;
}
.psc-omr-top {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 2px solid #1e293b;
    margin-bottom: 14px;
}
.psc-omr-emblem {
    font-size: 26px;
}
.psc-omr-govt {
    font-size: 14px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: 0.5px;
}
.psc-omr-subtitle {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 1px;
}
.psc-omr-neg-rule {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    margin-bottom: 16px;
}

/* Question Booklet Layout */
.psc-booklet {
    background: #ffffff;
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
}
.psc-booklet-badge {
    display: inline-block;
    background: #0f172a;
    color: #f8fafc;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 3px 8px;
    border-radius: 4px;
    margin-bottom: 14px;
}
.psc-omr-q-item {
    padding: 14px 0;
    border-bottom: 1px dashed #cbd5e1;
}
.psc-omr-q-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.psc-omr-q-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}
.psc-omr-q-num {
    background: #0f172a;
    color: #ffffff;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 900;
    flex-shrink: 0;
    margin-top: 2px;
}
.psc-omr-q-en {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
}
.psc-omr-q-ml {
    font-size: 13px;
    font-weight: 700;
    color: #0052FF;
    margin-top: 2px;
}
.psc-omr-choices-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 6px;
    margin: 10px 0 12px 32px;
}
@media (min-width: 640px) {
    .psc-omr-choices-list {
        grid-template-columns: 1fr 1fr;
    }
}
.psc-omr-choice-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 8px 10px;
    border-radius: 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.psc-omr-choice-row:hover {
    background: #eff6ff;
    border-color: #0052FF;
}
.psc-omr-choice-row.selected {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
.psc-omr-choice-row.selected .psc-choice-key {
    color: #f59e0b;
}
.psc-omr-choice-row.selected .psc-choice-sub {
    color: #cbd5e1;
}
.psc-choice-key {
    font-weight: 900;
    color: #0052FF;
}
.psc-choice-text {
    font-weight: 700;
}
.psc-choice-sub {
    color: #64748b;
    font-size: 11px;
}
.psc-omr-row-strip {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 8px 14px;
    border-radius: 8px;
    margin-left: 32px;
}
.psc-strip-label {
    font-size: 11px;
    font-weight: 800;
    color: #475569;
    letter-spacing: 0.5px;
}
.psc-omr-bubbles {
    display: flex;
    gap: 8px;
}
.psc-bubble {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid #334155;
    background: #ffffff;
    color: #334155;
    font-size: 11px;
    font-weight: 900;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    user-select: none;
}
.psc-bubble:hover {
    border-color: #000;
    background: #f1f5f9;
}
.psc-bubble.darkened {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
    box-shadow: inset 0 0 6px rgba(0,0,0,0.6);
}
.psc-omr-eval-wrap {
    text-align: center;
    margin-bottom: 18px;
}
.psc-btn-omr-submit {
    background: #0f172a;
    color: #ffffff;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
    transition: all 0.2s;
}
.psc-btn-omr-submit:hover {
    background: #334155;
    transform: translateY(-1px);
}
.psc-omr-result-box {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    animation: pscFadeIn 0.3s ease;
}
.psc-complete-card {
    background: linear-gradient(135deg, #1e1b4b, #0f172a);
    border-radius: 14px;
    color: #ffffff;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
.psc-complete-icon {
    font-size: 36px;
    margin-bottom: 8px;
}
.psc-complete-card h4 {
    font-size: 18px;
    font-weight: 900;
    margin: 0 0 6px 0;
}
.psc-complete-card p {
    font-size: 12px;
    color: #cbd5e1;
    margin: 0 0 16px 0;
}
.psc-btn-complete {
    background: linear-gradient(90deg, #f59e0b, #eab308);
    color: #0f172a;
    border: none;
    padding: 14px 24px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 900;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    transition: all 0.2s;
}
.psc-btn-complete:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
}
</style>

<!-- JAVASCRIPT LOGIC (Attached directly to window) -->
<script>
window.pscState = {
    xp: 0,
    hookSolved: false,
    mcqs: { 1: false, 2: false },
    omr: { 1: null, 2: null }
};

window.pscAddXp = function(points) {
    window.pscState.xp += points;
    const el = document.getElementById('psc-xp-val');
    if (el) el.innerText = window.pscState.xp;
};

// 1. SEQUENTIAL NAVIGATION WIZARD
window.pscGoTo = function(step) {
    const screens = ['hook', 'lesson', 'mcqs', 'omr'];
    const progressMap = { hook: '25%', lesson: '50%', mcqs: '75%', omr: '100%' };

    screens.forEach(s => {
        const screenEl = document.getElementById('psc-screen-' + s);
        const pillEl = document.getElementById('psc-pill-' + s);
        
        if (screenEl) {
            screenEl.style.display = (s === step ? 'block' : 'none');
        }
        if (pillEl) {
            if (s === step) {
                pillEl.classList.add('active');
            } else {
                pillEl.classList.remove('active');
            }
        }
    });

    // When going to MCQs, start at MCQ 1
    if (step === 'mcqs') {
        window.pscGoToMcq(1);
    }

    // Update Progress Bar
    const pBar = document.getElementById('psc-progress-bar');
    if (pBar && progressMap[step]) {
        pBar.style.width = progressMap[step];
    }

    // Scroll smoothly to top of capsule
    const container = document.getElementById('psc-capsule-container');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// Navigate between single MCQ screens
window.pscGoToMcq = function(mcqNum) {
    const q1 = document.getElementById('psc-mcq-card-1');
    const q2 = document.getElementById('psc-mcq-card-2');
    if (mcqNum === 1) {
        if (q1) q1.style.display = 'block';
        if (q2) q2.style.display = 'none';
    } else {
        if (q1) q1.style.display = 'none';
        if (q2) q2.style.display = 'block';
    }
    const container = document.getElementById('psc-capsule-container');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

// 2. HOOK QUESTION HANDLER
window.pscSelectHook = function(opt) {
    if (window.pscState.hookSolved) return;
    window.pscState.hookSolved = true;

    const btns = document.querySelectorAll('#psc-hook-opts .psc-opt-btn');
    btns.forEach(b => b.style.pointerEvents = 'none');

    const fbBox = document.getElementById('psc-hook-feedback');
    const fbContent = document.getElementById('psc-hook-feedback-content');
    fbBox.style.display = 'block';

    const pillHook = document.getElementById('psc-pill-hook');
    if (pillHook) pillHook.classList.add('completed');

    if (opt === 'B') {
        // Correct Answer
        btns[1].classList.add('correct');
        fbBox.className = 'psc-feedback psc-fb-correct';
        fbContent.innerHTML = \`
            <div style="font-size: 15px; font-weight: 800; margin-bottom: 6px;">
                ✅ ശരിയുത്തരം! സയിദ് കിർമാനി (Syed Kirmani) (+50 XP)
            </div>
            <p style="margin: 0; font-size: 13px; line-height: 1.5;">
                1983-ൽ ഇന്ത്യ പ്രഥമ ലോകകപ്പ് നേടുമ്പോൾ ടീമിലെ വിക്കറ്റ് കീപ്പറായിരുന്നു സയിദ് കിർമാനി. ആ ടൂർണമെന്റിലെ മികച്ച വിക്കറ്റ് കീപ്പറായി തിരഞ്ഞെടുക്കപ്പെട്ടതും അദ്ദേഹമായിരുന്നു. അദ്ദേഹത്തിന്റെ ആത്മകഥയാണ് <em>"Stumped: Life behind and beyond Twenty Two Yards"</em>.
            </p>
        \`;
        window.pscAddXp(50);
    } else {
        // Trap Answer
        const idxMap = { A: 0, B: 1, C: 2, D: 3 };
        btns[idxMap[opt]].classList.add('wrong');
        btns[1].classList.add('correct'); // Highlight correct answer

        fbBox.className = 'psc-feedback psc-fb-trap';

        let trapExplanation = '';
        if (opt === 'A') {
            trapExplanation = '<strong>⚠️ PSC Trap Warning!</strong> മഹേന്ദ്രസിംഗ് ധോണിയുടെ ആത്മകഥയല്ല ഇത്. ഭാരത് സുന്ദരേശൻ എഴുതിയ പുസ്തകമാണ് <em>"The Dhoni Touch"</em>. ശരിയുത്തരം: <strong>സയിദ് കിർമാനി</strong>.';
        } else if (opt === 'C') {
            trapExplanation = '<strong>⚠️ PSC Trap Warning!</strong> നയൻ മോംഗിയ മുൻ ഇന്ത്യൻ വിക്കറ്റ് കീപ്പറാണ്, എന്നാൽ ഈ പുസ്തകം അദ്ദേഹത്തിന്റേതല്ല. ശരിയുത്തരം: <strong>സയിദ് കിർമാനി</strong>.';
        } else {
            trapExplanation = '<strong>⚠️ PSC Trap Warning!</strong> കിരൺ മോറെ മുൻ ഇന്ത്യൻ വിക്കറ്റ് കീപ്പറാണ്, എന്നാൽ <em>"Stumped"</em> രചിച്ചത് 1983 ലോകകപ്പ് കീപ്പർ <strong>സയിദ് കിർമാനി</strong> ആണ്.';
        }

        fbContent.innerHTML = \`
            <div style="font-size: 14px; font-weight: 800; margin-bottom: 6px;">
                \${trapExplanation}
            </div>
            <p style="margin: 0; font-size: 12px; color: #7f1d1d;">
                കേരള PSC പരീക്ഷകളിൽ വിക്കറ്റ് കീപ്പർമാരുടെ പേരുകൾ ഓപ്ഷനിൽ നൽകി ചോദ്യങ്ങൾ ആവർത്തിക്കാറുണ്ട്. കൂടുതൽ വിവരങ്ങൾ പാഠത്തിൽ പഠിക്കാം (+15 XP).
            </p>
        \`;
        window.pscAddXp(15);
    }
};

// 3. RETENTION MCQS HANDLER (SINGLE SCREEN)
window.pscCheckMcq = function(qId, selected, correct) {
    const parent = document.getElementById('psc-drill-opts-' + qId);
    if (!parent) return;

    const btns = parent.querySelectorAll('.psc-opt-btn');
    btns.forEach(b => b.style.pointerEvents = 'none');

    const fb = document.getElementById('psc-drill-fb-' + qId);
    fb.style.display = 'block';

    const letters = ['A', 'B', 'C', 'D'];
    const chosenBtn = btns[letters.indexOf(selected)];
    const correctBtn = btns[letters.indexOf(correct)];

    if (selected === correct) {
        chosenBtn.classList.add('correct');
        fb.className = 'psc-feedback psc-fb-correct';
        fb.innerHTML = '✅ വളരെ ശരി! (+25 XP)';
        window.pscAddXp(25);
    } else {
        chosenBtn.classList.add('wrong');
        correctBtn.classList.add('correct');
        fb.className = 'psc-feedback psc-fb-trap';
        fb.innerHTML = '❌ തെറ്റിയാലും ഓർക്കുക: ശരിയുത്തരം ഓപ്ഷൻ <strong>' + correct + '</strong> ആണ് (+5 XP)';
        window.pscAddXp(5);
    }

    // Show Next Button
    if (qId === 1) {
        const nextBtn = document.getElementById('psc-next-mcq-btn-1');
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.classList.add('psc-pulse');
        }
    } else if (qId === 2) {
        const toOmrBtn = document.getElementById('psc-to-omr-btn');
        if (toOmrBtn) {
            toOmrBtn.style.display = 'inline-flex';
            toOmrBtn.classList.add('psc-pulse');
        }
    }

    window.pscState.mcqs[qId] = true;
    if (window.pscState.mcqs[1] && window.pscState.mcqs[2]) {
        const pill = document.getElementById('psc-pill-mcqs');
        if (pill) pill.classList.add('completed');
    }
};

// 4. OMR BUBBLING & CHOICE SYNC HANDLER
window.pscBubble = function(qNum, opt) {
    // 1. Update Bubbles
    const bubbles = document.querySelectorAll('.psc-bubble[data-q="' + qNum + '"]');
    bubbles.forEach(b => b.classList.remove('darkened'));

    const activeBubble = document.querySelector('.psc-bubble[data-q="' + qNum + '"][data-opt="' + opt + '"]');
    if (activeBubble) {
        activeBubble.classList.add('darkened');
    }

    // 2. Update Choice Rows
    const choiceRows = document.querySelectorAll('.psc-omr-choice-row[data-q="' + qNum + '"]');
    choiceRows.forEach(r => r.classList.remove('selected'));

    const activeChoiceRow = document.querySelector('.psc-omr-choice-row[data-q="' + qNum + '"][data-opt="' + opt + '"]');
    if (activeChoiceRow) {
        activeChoiceRow.classList.add('selected');
    }

    window.pscState.omr[qNum] = opt;
};

// 5. OMR EVALUATION
window.pscEvaluateOmr = function() {
    const answerKey = { 1: 'B', 2: 'B' };
    let correct = 0;
    let wrong = 0;
    let unattempted = 0;

    [1, 2].forEach(q => {
        const chosen = window.pscState.omr[q];
        if (!chosen) {
            unattempted++;
        } else if (chosen === answerKey[q]) {
            correct++;
        } else {
            wrong++;
        }
    });

    const netMarks = (correct * 1.0) - (wrong * 0.33);
    const formattedNet = Math.max(0, netMarks).toFixed(2);

    const resBox = document.getElementById('psc-omr-result');
    resBox.style.display = 'block';

    const pillOmr = document.getElementById('psc-pill-omr');
    if (pillOmr) pillOmr.classList.add('completed');

    if (correct === 2) {
        resBox.style.background = '#ecfdf5';
        resBox.style.border = '2px solid #10b981';
        resBox.style.color = '#065f46';
        resBox.innerHTML = \`
            <div style="font-size: 16px; font-weight: 900; margin-bottom: 4px;">🏆 State Rank 1 Grade! (+2.00 / 2.00 Net Marks)</div>
            <div style="font-size: 13px;">നിങ്ങൾ രണ്ട് ചോദ്യങ്ങളും കൃത്യമായി ബബിൾ ചെയ്തു. നെഗറ്റീവ് മാർക്കുകളില്ല (+100 Bonus XP)!</div>
        \`;
        window.pscAddXp(100);
    } else if (netMarks > 0) {
        resBox.style.background = '#fffbeb';
        resBox.style.border = '2px solid #f59e0b';
        resBox.style.color = '#92400e';
        resBox.innerHTML = \`
            <div style="font-size: 16px; font-weight: 900; margin-bottom: 4px;">🎯 OMR സ്കോർ: +\${formattedNet} Marks (Correct: \${correct}, Wrong: \${wrong}, Unattempted: \${unattempted})</div>
            <div style="font-size: 13px;">നെഗറ്റീവ് മാർക്കുകൾ ഒഴിവാക്കാൻ സംശയമുള്ള ചോദ്യങ്ങൾ ശ്രദ്ധയോടെ കൈകാര്യം ചെയ്യുക.</div>
        \`;
        window.pscAddXp(40);
    } else {
        resBox.style.background = '#fef2f2';
        resBox.style.border = '2px solid #ef4444';
        resBox.style.color = '#991b1b';
        resBox.innerHTML = \`
            <div style="font-size: 16px; font-weight: 900; margin-bottom: 4px;">⚠️ നെഗറ്റീവ് മാർക്ക് ഡിഡക്ഷൻ! (-0.33 Marks)</div>
            <div style="font-size: 13px;">തെറ്റായ ഉത്തരങ്ങൾക്ക് PSC 0.33 മാർക്ക് വീതം കുറയ്ക്കുന്നു. പാഠം വീണ്ടും റിവൈസ് ചെയ്യുക.</div>
        \`;
        window.pscAddXp(10);
    }
};

// 6. FINISH UNIT BRIDGE
window.pscFinishCapsule = function() {
    if (window.PSCRanker && typeof window.PSCRanker.completeSession === 'function') {
        window.PSCRanker.completeSession(window.pscState.xp || 250);
    } else {
        alert('🎉 Congratulations! You completed this Kerala PSC Capsule with ' + (window.pscState.xp || 250) + ' XP!');
    }
};
<\/script>`;
        },

        contentBlocks: (initial.contents || []).map(b => ({
            id: b.id,
            type: b.type,
            content_data: b.content_data || {},
            order: b.order
        })),

        diagnosticQ: initial.diagnostic || {
            phase_type: 'diagnostic',
            question_text: '',
            question_text_malayalam: '',
            option_a: '',
            option_b: '',
            option_c: '',
            option_d: '',
            correct_option: 'A',
            trap_warning_text: '',
            explanation: '',
            explanation_malayalam: '',
        },

        nonDiagnosticQuestions: [
            ...(initial.reinforcement || []).map(q => ({ ...q, phase_type: 'reinforcement' })),
            ...(initial.omr || []).map(q => ({ ...q, phase_type: 'omr' }))
        ],

        allQuestions: [],

        // Media Picker Modal State
        showMediaModal: false,
        activeMediaTargetBlockIndex: null,
        mediaFilterType: 'all',
        mediaItems: [],
        isLoadingMedia: false,
        isUploadingInModal: false,

        openMediaPicker(blockIdx, type) {
            this.activeMediaTargetBlockIndex = blockIdx;
            this.mediaFilterType = type || 'all';
            this.showMediaModal = true;
            this.fetchMediaItems();
        },

        async fetchMediaItems() {
            this.isLoadingMedia = true;
            try {
                const url = '{{ route("admin.media.api-list") }}?type=' + (this.mediaFilterType === 'all' ? '' : this.mediaFilterType);
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    this.mediaItems = data.files || [];
                }
            } catch (err) {
                console.error('Fetch media error:', err);
            } finally {
                this.isLoadingMedia = false;
            }
        },

        selectMediaItem(item) {
            if (this.activeMediaTargetBlockIndex === 'feature_image') {
                this.featureImage = item.url;
            } else if (this.activeMediaTargetBlockIndex !== null && this.contentBlocks[this.activeMediaTargetBlockIndex]) {
                const block = this.contentBlocks[this.activeMediaTargetBlockIndex];
                block.content_data.url = item.url;
                if (!block.content_data.title && item.name) {
                    block.content_data.title = item.name;
                }
            }
            this.showMediaModal = false;
        },

        async uploadFeatureImageDirect(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            const file = files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('title', file.name);

            this.isUploadingFeatureImage = true;
            try {
                const response = await fetch('{{ route("admin.media.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success && data.media) {
                    this.featureImage = data.media.url;
                } else {
                    alert('Upload failed: ' + (data.message || 'Please check file size/type.'));
                }
            } catch (err) {
                console.error('Direct feature image upload error:', err);
                alert('Upload failed. Please try again.');
            } finally {
                this.isUploadingFeatureImage = false;
                event.target.value = '';
            }
        },

        async uploadDirectFromModal(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            const file = files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('title', file.name);

            this.isUploadingInModal = true;
            try {
                const response = await fetch('{{ route("admin.media.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();
                if (data.success && data.media) {
                    // Auto select newly uploaded media into the block
                    this.selectMediaItem(data.media);
                } else {
                    alert('Upload failed: ' + (data.message || 'Please check file size/type.'));
                }
            } catch (err) {
                console.error('Direct upload error:', err);
                alert('Upload failed. Please try again.');
            } finally {
                this.isUploadingInModal = false;
                event.target.value = '';
            }
        },

        addContentBlock(type) {
            const defaults = {
                image: { url: '', title: 'Mnemonic Infographic', caption: '' },
                audio: { url: '', title: '30s Spoken Concept Summary', duration: '0:45', transcript: '' },
                video: { url: '', title: 'Explainer Reel Video', caption: '' },
                text: { title: 'Key Focus Points', body: '', scert_reference: '', tags: ['#KeralaRenaissance'] },
                map_globe: {
                    mode: '3d_globe',
                    preset: 'custom',
                    title: 'Geographic & Spatial Study',
                    title_malayalam: 'ഭൂമിശാസ്ത്ര വിശകലനം',
                    center_lat: 20.0,
                    center_lng: 78.0,
                    zoom: 1.5,
                    description: '',
                    notes_malayalam: '',
                    markers: []
                }
            };

            this.contentBlocks.push({
                type: type,
                content_data: defaults[type] || {},
                order: this.contentBlocks.length + 1
            });
        },

        globePresets: {
            pacific_reality: {
                mode: '3d_globe',
                preset: 'pacific_reality',
                title: 'The Pacific Reality: USA & Asia Neighbors',
                title_malayalam: 'ശാന്തസമുദ്ര അയൽപക്കങ്ങൾ: യു.എസും ഏഷ്യയും',
                center_lat: 30.0,
                center_lng: -170.0,
                zoom: 1.1,
                description: 'Dispel the flat map myth! Look how USA and Asia (China/Japan/Russia) are facing each other across the Pacific Ocean. Bering Strait is only 82 km wide.',
                notes_malayalam: 'പരന്ന മാപ്പുകളിൽ അമേരിക്കയും ചൈനയും ലോകത്തിന്റെ ഇരുവശത്താണെന്ന് തോന്നുമെങ്കിലും ഗ്ലോബിൽ അവർ ശാന്തസമുദ്രത്തിന് ഇരുവശമുള്ള അടുത്ത അയൽക്കാരാണ്. ബെയ്റിംഗ് കടലിടുക്കിന് 82 കി.മീ മാത്രമാണ് വീതി.',
                markers: [
                    { label: "Bering Strait (82 km)", lat: 65.7, lng: -168.9, note: "Separates Asia (Russia) & North America (Alaska, USA)" },
                    { label: "San Francisco, USA", lat: 37.77, lng: -122.42, note: "Key Pacific gateway port of USA" },
                    { label: "Tokyo, Japan", lat: 35.68, lng: 139.69, note: "Pacific Rim trade hub" },
                    { label: "Shanghai, China", lat: 31.23, lng: 121.47, note: "Busiest container port facing the Pacific" },
                    { label: "Pearl Harbor (Hawaii)", lat: 21.36, lng: -157.97, note: "Dec 7, 1941 attack brought USA into WWII" }
                ]
            },
            german_invasion: {
                mode: '3d_globe',
                preset: 'german_invasion',
                title: 'WWII German Blitzkrieg & Neighboring Invasions (1939-1941)',
                title_malayalam: 'രണ്ടാം ലോകമഹായുദ്ധം: ജർമ്മൻ അധിനിവേശ പാതകൾ',
                center_lat: 52.0,
                center_lng: 15.0,
                zoom: 2.2,
                description: 'Follow the exact geographic vectors of German Blitzkrieg from Berlin: invading Poland (1939), bypassing the Maginot Line into France (1940), and Operation Barbarossa towards USSR (1941).',
                notes_malayalam: '1939 സെപ്റ്റംബർ 1-ന് പോളണ്ടിലേക്കുള്ള അധിനിവേശത്തോടെയാണ് രണ്ടാം ലോകമഹായുദ്ധം ആരംഭിച്ചത്. തുടർന്ന് ബെൽജിയം, ഫ്രാൻസ്, തുടർന്ന് സോവിയറ്റ് യൂണിയനിലേക്കുള്ള ബാർബറോസ ഓപ്പറേഷൻ.',
                markers: [
                    { label: "Berlin (Nazi Germany)", lat: 52.52, lng: 13.41, note: "Capital & Command Center of Nazi Third Reich" },
                    { label: "Poland (Warsaw)", lat: 52.23, lng: 21.01, note: "Invaded Sept 1, 1939 (Official start of WWII)" },
                    { label: "Ardennes & France (Paris)", lat: 48.86, lng: 2.35, note: "Maginot Line bypassed; Paris captured June 1940" },
                    { label: "Moscow (USSR - Barbarossa)", lat: 55.75, lng: 37.62, note: "Operation Barbarossa launched June 22, 1941" }
                ]
            },
            red_sea: {
                mode: '3d_globe',
                preset: 'red_sea',
                title: 'Red Sea & Maritime Choke Points (Suez Canal to Bab-el-Mandeb)',
                title_malayalam: 'ചെങ്കടലും നിർണായക സമുദ്ര പാതകളും (സൂയസ് കനാൽ & ബാബ് അൽ മന്ദബ്)',
                center_lat: 20.0,
                center_lng: 40.0,
                zoom: 2.0,
                description: 'The most tested strategic maritime chokepoints in PSC exams: Suez Canal (connects Mediterranean with Red Sea) and Bab-el-Mandeb (Gate of Tears, connects Red Sea with Arabian Sea).',
                notes_malayalam: 'സൂയസ് കനാൽ (മെഡിറ്ററേനിയൻ - ചെങ്കടൽ ബന്ധിപ്പിക്കുന്നു, 1869-ൽ തുറന്നു), ബാബ് അൽ മന്ദബ് (കണ്ണീരിന്റെ വാതിൽ - ചെങ്കടലും ഏദൻ ഉൾക്കടലും ബന്ധിപ്പിക്കുന്നു).',
                markers: [
                    { label: "Suez Canal (Egypt)", lat: 30.7, lng: 32.34, note: "Opened 1869 by Ferdinand de Lesseps; Mediterranean - Red Sea link" },
                    { label: "Bab-el-Mandeb Strait", lat: 12.58, lng: 43.33, note: "'Gate of Tears' connecting Red Sea to Gulf of Aden" },
                    { label: "Strait of Hormuz", lat: 26.56, lng: 56.25, note: "Persian Gulf to Gulf of Oman oil choke point" },
                    { label: "Arabian Sea (India Coast)", lat: 15.0, lng: 70.0, note: "Historic spice trade route linking Kerala" }
                ]
            },
            mandela: {
                mode: '3d_globe',
                preset: 'mandela',
                title: "Nelson Mandela's Spatial Journey: Mvezo to Robben Island",
                title_malayalam: 'നെൽസൺ മണ്ടേലയുടെ ജീവിത പാത: എംവേസോ മുതൽ റോബൻ ദ്വീപ് വരെ',
                center_lat: -30.0,
                center_lng: 25.0,
                zoom: 2.0,
                description: "Trace Nelson Mandela's journey across South Africa: born in Mvezo, organized resistance in Soweto/Johannesburg, imprisoned on Robben Island off Cape Town, and inaugurated at Pretoria.",
                notes_malayalam: 'ജനനം എംവേസോ (1918), റിവോണിയ വിചാരണ ജൊഹാനസ്ബർഗ്, 27 വർഷത്തെ തടവിൽ 18 വർഷം റോബൻ ദ്വീപിൽ, 1994-ൽ പ്രിട്ടോറിയയിൽ പ്രസിഡന്റായി സത്യപ്രതിജ്ഞ.',
                markers: [
                    { label: "Mvezo (Transkei)", lat: -31.95, lng: 28.51, note: "Mandela born on July 18, 1918 (Madiba clan)" },
                    { label: "Johannesburg & Soweto", lat: -26.20, lng: 28.04, note: "ANC activist center, arrest & Rivonia Trial" },
                    { label: "Robben Island (Cape Town)", lat: -33.81, lng: 18.37, note: "Imprisoned 18 years in 8x7 foot cell (1964-1982)" },
                    { label: "Pretoria (Union Buildings)", lat: -25.74, lng: 28.21, note: "Inaugurated as first Black President of South Africa in May 1994" }
                ]
            },
            kerala_rivers: {
                mode: '2d_map',
                preset: 'kerala_rivers',
                title: 'Kerala Rivers, Western Ghats & Mountain Passes',
                title_malayalam: 'കേരളത്തിലെ നദികളും സഹ്യപർവ്വത ചുരങ്ങളും',
                center_lat: 10.5,
                center_lng: 76.5,
                zoom: 3.5,
                description: 'Kerala physical geography essentials: 44 rivers (41 west-flowing, 3 east-flowing: Kabani, Bhavani, Pambar), Palakkad Gap connecting Kerala with Tamil Nadu, and Western Ghats peaks.',
                notes_malayalam: '44 നദികൾ (41 പടിഞ്ഞാറോട്ട്, 3 കിഴക്കോട്ട്: കബനി, ഭവാനി, പാമ്പാർ). സഹ്യപർവ്വതത്തിലെ പ്രധാന വിടവ് പാലക്കാട് ചുരം (30-40 കി.മീ വീതി). ഏറ്റവും നീളമേറിയ നദി പെരിയാർ (244 കി.മീ).',
                markers: [
                    { label: "Periyar (244 km) & Idukki", lat: 9.85, lng: 76.97, note: "Longest river in Kerala; Sivagiri hills origin; Idukki Arch Dam" },
                    { label: "Bharathapuzha (209 km)", lat: 10.78, lng: 75.92, note: "Nila; 2nd longest river; originates from Anamalai hills" },
                    { label: "Palakkad Gap (Pass)", lat: 10.78, lng: 76.65, note: "Major geological break in Western Ghats connecting Palakkad to Coimbatore" },
                    { label: "Kabani (East-flowing)", lat: 11.83, lng: 76.12, note: "Originates in Wayanad; flows east to join Kaveri" },
                    { label: "Aryankavu Pass (Kollam)", lat: 8.98, lng: 77.15, note: "Connects Kollam to Shenkottai (Tamil Nadu)" }
                ]
            }
        },

        applyGlobePreset(block, presetKey) {
            if (!presetKey || !this.globePresets[presetKey]) return;
            const p = JSON.parse(JSON.stringify(this.globePresets[presetKey]));
            block.content_data = Object.assign({}, block.content_data, p);
        },

        addMarkerToBlock(block) {
            if (!block.content_data.markers) {
                block.content_data.markers = [];
            }
            block.content_data.markers.push({
                label: 'New Location',
                lat: block.content_data.center_lat || 20.0,
                lng: block.content_data.center_lng || 78.0,
                note: 'Key historical or PSC exam point'
            });
        },

        removeMarkerFromBlock(block, idx) {
            if (block.content_data.markers) {
                block.content_data.markers.splice(idx, 1);
            }
        },

        removeContentBlock(idx) {
            this.contentBlocks.splice(idx, 1);
        },

        moveBlockUp(idx) {
            if (idx > 0) {
                const temp = this.contentBlocks[idx];
                this.contentBlocks[idx] = this.contentBlocks[idx - 1];
                this.contentBlocks[idx - 1] = temp;
            }
        },

        moveBlockDown(idx) {
            if (idx < this.contentBlocks.length - 1) {
                const temp = this.contentBlocks[idx];
                this.contentBlocks[idx] = this.contentBlocks[idx + 1];
                this.contentBlocks[idx + 1] = temp;
            }
        },

        addQuestion(phase = 'reinforcement') {
            this.nonDiagnosticQuestions.push({
                phase_type: 'reinforcement',
                question_text: '',
                question_text_malayalam: '',
                option_a: '',
                option_b: '',
                option_c: '',
                option_d: '',
                correct_option: 'A',
                explanation: '',
                explanation_malayalam: '',
                trap_warning_text: '',
            });
        },

        removeQuestion(idx) {
            this.nonDiagnosticQuestions.splice(idx, 1);
        },

        prepareJsonData() {
            this.allQuestions = [];
            if (this.diagnosticQ && this.diagnosticQ.question_text) {
                this.diagnosticQ.phase_type = 'diagnostic';
                this.allQuestions.push(this.diagnosticQ);
            }
            this.nonDiagnosticQuestions.forEach(q => {
                q.phase_type = 'reinforcement';
            });
            this.allQuestions.push(...this.nonDiagnosticQuestions);
        }
    };
}
</script>
@endpush
@endsection
