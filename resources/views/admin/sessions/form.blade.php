@extends('layouts.app')

@section('title', ($isEdit ? 'Edit Session: ' . $session->title : 'Create New Learning Session') . ' — PSCRanker Admin')

@section('content')
<div 
    x-data="adminSessionBuilder({
        contents: @js($contents),
        diagnostic: @js($diagnosticQuestions->first()),
        reinforcement: @js($reinforcementQuestions->values()),
        omr: @js($omrQuestions->values())
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
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">PSC Subject Stream *</label>
                        <select 
                            name="category_id" 
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

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">Unit # (Sequential) *</label>
                            <input 
                                type="number" 
                                name="order" 
                                value="{{ old('order', $session->order ?? 1) }}" 
                                class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none"
                                required
                                min="1"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">XP Reward</label>
                            <input 
                                type="number" 
                                name="xp_reward" 
                                value="{{ old('xp_reward', $session->xp_reward ?? 250) }}" 
                                class="w-full px-3 py-2 text-xs font-bold rounded-lg border border-slate-300 focus:border-[#0052FF] focus:outline-none text-amber-600"
                            >
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

                        <!-- General Stream Concoction Settings -->
                        <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-xl bg-blue-50/80 border border-blue-200">
                            <div class="flex items-start gap-3">
                                <input 
                                    type="checkbox" 
                                    id="in_general_stream" 
                                    name="in_general_stream" 
                                    value="1" 
                                    {{ old('in_general_stream', $session->in_general_stream ?? true) ? 'checked' : '' }}
                                    class="w-4 h-4 mt-0.5 rounded text-[#0052FF] focus:ring-blue-500 cursor-pointer"
                                >
                                <div>
                                    <label for="in_general_stream" class="text-xs font-black text-blue-950 flex items-center gap-1.5 cursor-pointer">
                                        <span>🚂 Include in General Stream (Mixed Master Train)</span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-200 text-blue-900">All-Round Train</span>
                                    </label>
                                    <p class="text-[11px] text-blue-800 font-medium mt-0.5">
                                        When checked, this unit appears in the mixed all-round train when students launch <strong>[START COURSE UNITS]</strong>.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <label for="general_stream_order" class="text-xs font-bold text-blue-900 whitespace-nowrap">Train Order #:</label>
                                <input 
                                    type="number" 
                                    id="general_stream_order" 
                                    name="general_stream_order" 
                                    value="{{ old('general_stream_order', $session->general_stream_order ?? 1) }}" 
                                    min="1"
                                    placeholder="1"
                                    class="w-20 px-3 py-1.5 text-xs font-black rounded-lg border border-blue-300 bg-white focus:border-[#0052FF] focus:outline-none text-center"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-between py-6">
                <a href="{{ route('admin.sessions.index') }}" class="text-xs font-bold text-slate-600 hover:underline">
                    Cancel & Back
                </a>

                <button 
                    type="submit" 
                    class="px-8 py-3.5 bg-[#0052FF] hover:bg-blue-700 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg transition flex items-center gap-2"
                >
                    <span>Save Learning Session</span>
                    <span>⚡</span>
                </button>
            </div>

        </form>

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
            if (this.activeMediaTargetBlockIndex !== null && this.contentBlocks[this.activeMediaTargetBlockIndex]) {
                const block = this.contentBlocks[this.activeMediaTargetBlockIndex];
                block.content_data.url = item.url;
                if (!block.content_data.title && item.name) {
                    block.content_data.title = item.name;
                }
            }
            this.showMediaModal = false;
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
