@extends('layouts.app')

@section('title', 'Micro-Learning Sessions & Syllabus Capsules — PSCRanker')

@section('content')
<div class="py-8 sm:py-12 bg-gradient-to-b from-blue-50/60 via-slate-50 to-white min-h-[85vh]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-3">
                <span>⚡ 4-Phase Micro-Learning Loop</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                Kerala PSC Session Capsules
            </h1>
            <p class="text-base sm:text-lg font-bold text-[#0052FF] mt-1 font-['Noto_Sans_Malayalam']">
                ഡയഗ്നോസ്റ്റിക് ഹുക്ക് • മൾട്ടിമീഡിയ കാപ്സ്യൂൾ • സ്പീഡ് ബ്ലിറ്റ്സ് • ഒ.എം.ആർ ചലഞ്ച്
            </p>
            <p class="text-xs sm:text-sm text-slate-600 mt-2">
                No boring 2-hour video lectures. Complete modular 10-minute sessions that train your instincts, explain traps, and simulate official Kerala PSC OMR conditions.
            </p>
        </div>

        <!-- Featured Flagship Session Banner -->
        @if($featuredSession)
            <div class="mb-12 rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-10 shadow-2xl relative overflow-hidden border border-blue-700/50">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-yellow-400/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 max-w-2xl">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-400 text-slate-950">
                            Featured Master Capsule
                        </span>
                        <span class="text-xs text-blue-200 font-bold">
                            {{ $featuredSession->category ? $featuredSession->category->name : 'Kerala Renaissance' }}
                        </span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        {{ $featuredSession->title }}
                    </h2>
                    @if($featuredSession->title_malayalam)
                        <p class="text-lg sm:text-xl font-bold text-yellow-300 mt-1 font-['Noto_Sans_Malayalam']">
                            {{ $featuredSession->title_malayalam }}
                        </p>
                    @endif

                    <p class="text-sm text-slate-300 mt-3 leading-relaxed">
                        Master the landmark Aruvipuram consecration, Guru's philosophical revolts, trap years, and SCERT Std 8/10 facts in under 10 minutes with instant OMR bubbling!
                    </p>

                    <!-- 4-Phase Micro-Feature Pills -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 my-6 text-center">
                        <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-base">🎯</span>
                            <div class="text-xs font-bold text-slate-200">Diagnostic Hook</div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-base">🖼️</span>
                            <div class="text-xs font-bold text-slate-200">Multimedia Capsule</div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-base">⚡</span>
                            <div class="text-xs font-bold text-slate-200">20s Speed Blitz</div>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10">
                            <span class="text-base">📝</span>
                            <div class="text-xs font-bold text-slate-200">Final OMR Sheet</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a 
                            href="{{ route('session.show', $featuredSession->slug) }}" 
                            class="px-8 py-4 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 font-black text-base rounded-2xl shadow-lg shadow-yellow-500/30 active:scale-95 transition flex items-center gap-2 border border-yellow-300"
                        >
                            <span>LAUNCH SESSION</span>
                            <span>⚡</span>
                        </a>
                        <span class="text-xs text-slate-300 font-bold">
                            Reward: <strong class="text-yellow-300">+{{ $featuredSession->xp_reward }} XP</strong>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Sessions by Category -->
        <div class="space-y-12">
            @foreach($categories as $category)
                <div>
                    <div class="flex items-center justify-between mb-5 border-b border-slate-200 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">⚡</span>
                            <div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900">{{ $category->name }}</h3>
                                @if($category->name_malayalam)
                                    <span class="text-xs font-bold text-[#0052FF] font-['Noto_Sans_Malayalam']">{{ $category->name_malayalam }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-500">
                            {{ $category->sessions->count() }} {{ Str::plural('Capsule', $category->sessions->count()) }}
                        </span>
                    </div>

                    @if($category->sessions->isEmpty())
                        <div class="p-8 rounded-2xl bg-white border border-slate-200/80 text-center text-slate-500 text-xs font-medium">
                            New micro-learning capsules for {{ $category->name }} launching this week!
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($category->sessions as $session)
                                <div class="bg-white rounded-2xl border-2 border-slate-200/90 hover:border-blue-500 transition-all p-5 sm:p-6 flex flex-col justify-between shadow-xs hover:shadow-lg group">
                                    <div>
                                        <div class="flex items-center justify-between text-xs text-slate-500 font-bold mb-3">
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-[#0052FF] font-black">
                                                Capsule #{{ $session->order }}
                                            </span>
                                            <span class="text-amber-600 flex items-center gap-1 font-mono">
                                                ⚡ +{{ $session->xp_reward }} XP
                                            </span>
                                        </div>

                                        <h4 class="text-lg font-black text-slate-900 group-hover:text-[#0052FF] transition leading-snug">
                                            <a href="{{ route('session.show', $session->slug) }}">
                                                {{ $session->title }}
                                            </a>
                                        </h4>
                                        @if($session->title_malayalam)
                                            <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1 font-['Noto_Sans_Malayalam']">
                                                {{ $session->title_malayalam }}
                                            </p>
                                        @endif

                                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                            <span>4-Phase Micro Loop</span>
                                            <span>⏱️ ~8-10 Mins</span>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <a 
                                            href="{{ route('session.show', $session->slug) }}" 
                                            class="w-full py-2.5 px-4 bg-blue-50 hover:bg-[#0052FF] text-[#0052FF] hover:text-white font-bold text-xs rounded-xl transition-all text-center flex items-center justify-center gap-1.5 group-hover:bg-[#0052FF] group-hover:text-white"
                                        >
                                            <span>Start Capsule</span>
                                            <span>→</span>
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
