@extends('layouts.app')

@section('title', 'PSC Special Lessons & Syllabus Units — PSCRanker')

@section('content')
<div 
    x-data="{ 
        activeSubject: new URLSearchParams(window.location.search).get('subject') || 'all',
        setSubject(slug) {
            this.activeSubject = slug;
            const url = new URL(window.location);
            if (slug === 'all') {
                url.searchParams.delete('subject');
            } else {
                url.searchParams.set('subject', slug);
            }
            window.history.replaceState({}, '', url);
        }
    }"
    class="py-8 sm:py-12 bg-gradient-to-b from-blue-50/60 via-slate-50 to-white min-h-[85vh]"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-3 shadow-xs">
                <span>⚡ 6 CORE PSC SUBJECTS • 4-PHASE UNIT ENGINE</span>
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-950 tracking-tight leading-tight">
                Kerala PSC Session Capsules &amp; Special Lessons
            </h1>
            <p class="text-base sm:text-xl font-bold text-[#0052FF] mt-2 font-['Noto_Sans_Malayalam']">
                ഇംഗ്ലീഷ് • കണക്ക് • സയൻസ് • ചരിത്രം • ഭൂമിശാസ്ത്രം • സമകാലികം
            </p>
            <p class="text-xs sm:text-sm text-slate-600 mt-3 leading-relaxed max-w-2xl mx-auto">
                Decluttered, high-impact learning. Each unit is structured with the complete 4-phase loop: 
                <strong>Test Question</strong> (Diagnostic) ➔ <strong>Lesson Capsule</strong> (Text, Images, Audio, Video) ➔ <strong>20s Speed Blitz</strong> ➔ <strong>Kerala PSC OMR Test</strong>.
            </p>
        </div>

        <!-- Interactive 6-Subject Filter Bar -->
        <div class="mb-10 sm:mb-12">
            <div class="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto pb-3 no-scrollbar">
                
                <!-- All Subjects Pill -->
                <button 
                    type="button"
                    @click="setSubject('all')"
                    class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-black transition-all shrink-0 flex items-center gap-2 border-2"
                    :class="activeSubject === 'all' 
                        ? 'bg-slate-950 text-white border-slate-950 shadow-md scale-105 ring-2 ring-blue-400' 
                        : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:bg-blue-50/40'"
                >
                    <span>⚡ All Subjects</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono" :class="activeSubject === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                        {{ $categories->sum(fn($c) => $c->sessions->count()) }} Units
                    </span>
                </button>

                @foreach($categories as $cat)
                    @php
                        $subjectIcons = [
                            'english' => '📖',
                            'maths' => '🔢',
                            'science' => '🔬',
                            'history' => '🏛️',
                            'geography' => '🌍',
                            'current-affairs' => '📰',
                        ];
                        $icon = $subjectIcons[$cat->slug] ?? '⚡';
                    @endphp
                    <button 
                        type="button"
                        @click="setSubject('{{ $cat->slug }}')"
                        class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-black transition-all shrink-0 flex items-center gap-2 border-2"
                        :class="activeSubject === '{{ $cat->slug }}' 
                            ? 'bg-[#0052FF] text-white border-[#0052FF] shadow-md scale-105 ring-2 ring-blue-300' 
                            : 'bg-white text-slate-700 border-slate-200 hover:border-blue-300 hover:bg-blue-50/40'"
                    >
                        <span>{{ $icon }}</span>
                        <span>{{ $cat->name }}</span>
                        <span 
                            class="px-2 py-0.5 rounded-full text-[10px] font-mono"
                            :class="activeSubject === '{{ $cat->slug }}' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ $cat->sessions->count() }}
                        </span>
                    </button>
                @endforeach

            </div>
        </div>

        <!-- Featured Flagship Unit Banner (Shown when on "all") -->
        @if($featuredSession)
            <div 
                x-show="activeSubject === 'all'" 
                x-transition
                class="mb-14 rounded-3xl bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 text-white p-6 sm:p-10 shadow-2xl relative overflow-hidden border border-blue-700/50"
            >
                <div class="absolute -right-16 -top-16 w-72 h-72 bg-yellow-400/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 max-w-3xl">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-400 text-slate-950 shadow-xs">
                            Featured Master Unit
                        </span>
                        <span class="text-xs text-blue-200 font-bold">
                            {{ $featuredSession->category ? $featuredSession->category->name : 'Kerala PSC' }}
                        </span>
                        @if($featuredSession->is_premium)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950">
                                👑 PRO PASS
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                                FREE FOR ALL
                            </span>
                        @endif
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                        {{ $featuredSession->title }}
                    </h2>
                    @if($featuredSession->title_malayalam)
                        <p class="text-base sm:text-xl font-bold text-yellow-300 mt-1 font-['Noto_Sans_Malayalam']">
                            {{ $featuredSession->title_malayalam }}
                        </p>
                    @endif

                    <!-- 4-Phase Micro-Feature Pills -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-6 text-center">
                        <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-lg">🎯</span>
                            <div class="text-xs font-bold text-slate-200 mt-1">1. Diagnostic Hook</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-lg">📚</span>
                            <div class="text-xs font-bold text-slate-200 mt-1">2. Multimedia Capsule</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-lg">⚡</span>
                            <div class="text-xs font-bold text-slate-200 mt-1">3. 20s Speed Blitz</div>
                        </div>
                        <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-lg">📝</span>
                            <div class="text-xs font-bold text-slate-200 mt-1">4. Timed OMR Sheet</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a 
                            href="{{ route('session.show', $featuredSession->slug) }}" 
                            class="px-8 py-4 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 font-black text-base rounded-2xl shadow-xl shadow-yellow-500/30 active:scale-95 transition flex items-center gap-2 border border-yellow-300"
                        >
                            <span>LAUNCH UNIT NOW</span>
                            <span>⚡</span>
                        </a>
                        <span class="text-xs text-slate-300 font-bold">
                            Reward: <strong class="text-yellow-300">+{{ $featuredSession->xp_reward }} XP</strong>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Sessions by Subject Sections -->
        <div class="space-y-16">
            @foreach($categories as $category)
                @php
                    $subjectIcons = [
                        'english' => '📖',
                        'maths' => '🔢',
                        'science' => '🔬',
                        'history' => '🏛️',
                        'geography' => '🌍',
                        'current-affairs' => '📰',
                    ];
                    $icon = $subjectIcons[$category->slug] ?? '⚡';
                @endphp

                <div 
                    x-show="activeSubject === 'all' || activeSubject === '{{ $category->slug }}'"
                    x-transition
                    class="transition-all"
                >
                    <!-- Subject Header Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b-2 border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200 text-2xl flex items-center justify-center shadow-xs">
                                {{ $icon }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl sm:text-2xl font-black text-slate-900">{{ $category->name }}</h3>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 font-mono">
                                        {{ $category->sessions->count() }} {{ Str::plural('Unit', $category->sessions->count()) }}
                                    </span>
                                </div>
                                @if($category->name_malayalam)
                                    <span class="text-xs sm:text-sm font-bold text-[#0052FF] font-['Noto_Sans_Malayalam'] block mt-0.5">
                                        {{ $category->name_malayalam }}
                                    </span>
                                @endif
                                @if($category->description)
                                    <p class="text-xs text-slate-500 mt-1 max-w-xl">
                                        {{ $category->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @auth
                            <a 
                                href="{{ route('admin.sessions.create', ['category_id' => $category->id]) }}" 
                                class="px-3.5 py-2 text-xs font-black text-[#0052FF] bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition flex items-center gap-1.5"
                            >
                                <span>+ Add {{ $category->name }} Unit</span>
                            </a>
                        @endauth
                    </div>

                    @if($category->sessions->isEmpty())
                        <!-- Empty Subject State Card -->
                        <div class="p-8 rounded-3xl bg-white border-2 border-dashed border-slate-300 text-center max-w-lg mx-auto my-6">
                            <div class="text-3xl mb-2">{{ $icon }}</div>
                            <h4 class="text-base font-black text-slate-900">
                                {{ $category->name }} Units Under Development
                            </h4>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                New multimedia capsules with SCERT questions, mnemonics, and OMR sheets for {{ $category->name }} are releasing this week!
                            </p>
                            @auth
                                <div class="mt-4">
                                    <a 
                                        href="{{ route('admin.sessions.create', ['category_id' => $category->id]) }}" 
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0052FF] text-white text-xs font-black rounded-xl hover:bg-blue-700 transition"
                                    >
                                        <span>+ Build Unit 1 for {{ $category->name }}</span>
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @else
                        <!-- Units Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($category->sessions as $session)
                                @php
                                    // Count content block types in this unit
                                    $hasImages = $session->contents->contains('type', 'image');
                                    $hasAudio = $session->contents->contains('type', 'audio');
                                    $hasVideo = $session->contents->contains('type', 'video');
                                    $hasText = $session->contents->contains('type', 'text');
                                @endphp

                                <div class="bg-white rounded-3xl border-2 border-slate-200 hover:border-blue-500 transition-all p-6 flex flex-col justify-between shadow-xs hover:shadow-xl group">
                                    <div>
                                        <!-- Card Top Bar: Unit Number, Pro/Free, XP -->
                                        <div class="flex items-center justify-between text-xs font-bold mb-3.5">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-1 rounded-xl bg-blue-50 text-[#0052FF] font-black border border-blue-200">
                                                    Unit #{{ $session->order }}
                                                </span>
                                                @if($session->is_premium)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 shadow-xs">
                                                        👑 PRO PASS
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        FREE
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-amber-600 flex items-center gap-1 font-mono font-black">
                                                ⚡ +{{ $session->xp_reward }} XP
                                            </span>
                                        </div>

                                        <!-- Unit Title -->
                                        <h4 class="text-lg font-black text-slate-950 group-hover:text-[#0052FF] transition leading-snug">
                                            <a href="{{ route('session.show', $session->slug) }}">
                                                {{ $session->title }}
                                            </a>
                                        </h4>
                                        @if($session->title_malayalam)
                                            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1 font-['Noto_Sans_Malayalam'] leading-relaxed">
                                                {{ $session->title_malayalam }}
                                            </p>
                                        @endif

                                        <!-- Multimedia Feature Badges -->
                                        <div class="flex flex-wrap items-center gap-1.5 mt-4 pt-3 border-t border-slate-100 text-[10px] font-bold">
                                            @if($hasText)
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">📝 Notes</span>
                                            @endif
                                            @if($hasImages)
                                                <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700">🖼️ Images</span>
                                            @endif
                                            @if($hasAudio)
                                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700">🎙️ Audio</span>
                                            @endif
                                            @if($hasVideo)
                                                <span class="px-2 py-0.5 rounded-md bg-red-50 text-red-700">🎥 Video</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800">📝 OMR Challenge</span>
                                        </div>

                                        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                                            <span>4-Phase Loop</span>
                                            <span>⏱️ ~10 Mins</span>
                                        </div>
                                    </div>

                                    <!-- Launch Unit CTA -->
                                    <div class="mt-5 pt-2">
                                        <a 
                                            href="{{ route('session.show', $session->slug) }}" 
                                            class="w-full py-3 px-4 font-black text-xs rounded-2xl transition-all text-center flex items-center justify-center gap-2 active:scale-95 {{ $session->is_premium ? 'bg-amber-100 hover:bg-amber-200 text-amber-950 border border-amber-300 shadow-xs' : 'bg-[#0052FF] hover:bg-blue-700 text-white shadow-md shadow-blue-500/20' }}"
                                        >
                                            @if($session->is_premium)
                                                <span>👑 Open PRO Unit (Prepaid Pass)</span>
                                            @else
                                                <span>Start Unit #{{ $session->order }}</span>
                                            @endif
                                            <span class="text-sm">➔</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
