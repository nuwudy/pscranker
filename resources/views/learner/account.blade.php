@extends('layouts.app')

@section('title', 'My Account & Practice Hub — PSCRanker')

@section('content')
<div class="bg-gradient-to-b from-[#F0F5FF] via-slate-50 to-white min-h-screen py-6 sm:py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Welcome Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#0052FF] text-xs font-black uppercase tracking-wider">
                    <span>⚡ Candidate Practice Hub</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Namaskaram, {{ $user->name }}! 👋
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm font-medium">
                    Kerala PSC Micro-Learning • Your daily 5-minute syllabus routine.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-900 hover:bg-black text-white font-bold text-xs transition">
                        Admin Dashboard
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition cursor-pointer">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Top Section: 1-Click Game Launch Hero + Plan Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 1. Main Game Launcher Hero Card (2 Cols) --}}
            <div style="background: linear-gradient(135deg, #0052FF 0%, #3730A3 50%, #1E1B4B 100%); color: #ffffff;" 
                 class="lg:col-span-2 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/20 relative overflow-hidden flex flex-col justify-between border-2 border-indigo-400/40">
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="space-y-4 relative z-10">
                    <div class="flex items-center justify-between gap-3">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-yellow-400 text-slate-950 text-xs font-black uppercase tracking-wider shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            <span>🎯 Next Mission To Play</span>
                        </span>

                        <span class="text-xs font-extrabold text-blue-100 bg-white/10 px-3 py-1 rounded-full backdrop-blur-md">
                            Unit {{ $continueUnitNumber }} of {{ $totalUnits }}
                        </span>
                    </div>

                    @if($continueSession)
                        <div class="space-y-2">
                            <h2 style="color: #ffffff;" class="text-xl sm:text-2xl font-black tracking-tight leading-snug">
                                {{ $continueSession->title }}
                            </h2>
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-blue-100">
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">🏷️ {{ $continueSession->category ?? 'General Knowledge' }}</span>
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">⏱️ 5-Minute Unit</span>
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">📝 OMR Test Included</span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2">
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                🏆 All Units Completed!
                            </h2>
                            <p class="text-xs text-blue-100 font-medium">
                                You have practiced all published units on the curriculum train. Review or retake any unit below!
                            </p>
                        </div>
                    @endif

                    {{-- Curriculum Train Progress Gauge --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs font-bold text-blue-100 mb-1.5">
                            <span>Curriculum Progress</span>
                            <span>{{ $completedCount }} / {{ $totalUnits }} Units Completed ({{ $progressPercent }}%)</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden backdrop-blur-xs">
                            <div class="bg-gradient-to-r from-yellow-400 to-amber-300 h-3 rounded-full transition-all duration-500 shadow-sm" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Action Play Button --}}
                <div class="mt-6 pt-4 border-t border-white/15 relative z-10 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <div class="text-xs text-blue-100">
                        @if($averageScore)
                            <span>⭐ Your Avg OMR Score: <strong class="text-yellow-300 font-black">{{ $averageScore }} / 5</strong></span>
                        @else
                            <span>⚡ Tap play to begin your daily streak</span>
                        @endif
                    </div>

                    @if($continueSession)
                        <a href="{{ route('learner.pscm.show', $continueSession->slug) }}" class="px-8 py-4 bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black text-base rounded-2xl shadow-lg shadow-black/20 hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-2.5 border border-yellow-300 group">
                            <span>PLAY UNIT #{{ $continueUnitNumber }} NOW</span>
                            <span class="text-xl group-hover:translate-x-1 transition-transform">➔</span>
                        </a>
                    @else
                        <a href="{{ route('learner.pscm.index') }}" class="px-8 py-4 bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black text-base rounded-2xl shadow-lg shadow-black/20 hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-2">
                            <span>Browse All Lessons 📚</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- 2. My Plan & Account Credentials Card --}}
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-400">Membership &amp; Plan</span>
                        @if($isSubscribed)
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 shadow-xs">
                                👑 PRO PASS
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-900 border border-blue-200">
                                🎓 Free Member
                            </span>
                        @endif
                    </div>

                    @if($isSubscribed)
                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-900">PRO Access Active</span>
                                <span class="text-xs font-black text-amber-800">{{ $subscriptionDays }} days left</span>
                            </div>
                            <p class="text-xs text-amber-800 leading-relaxed font-medium">
                                Unlimited access to all 154+ Kerala PSC AI units, OMR evaluation, and rapid drills.
                            </p>
                            @if($user->subscribed_until)
                                <div class="text-[11px] font-bold text-amber-700 pt-1">
                                    Valid until: {{ $user->subscribed_until->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-center gap-2 text-xs font-black text-slate-800">
                                <span>🔓 Standard Free Track</span>
                            </div>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                You can practice free foundation lessons. Upgrade to PRO to unlock all 154+ units, instant OMR bubble evaluation, and advanced drills.
                            </p>
                            <a href="{{ route('pricing') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-[#0052FF] to-indigo-600 text-white font-black text-xs shadow-md shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition">
                                <span>⚡ Upgrade to PRO Pass</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Candidate Credentials --}}
                <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Mobile:</span>
                        <strong class="text-slate-800 font-mono">{{ $user->phone ?? 'Not set' }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Email:</span>
                        <strong class="text-slate-800">{{ $user->email }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Candidate Since:</span>
                        <strong class="text-slate-800">{{ $user->created_at ? $user->created_at->format('M Y') : 'Active' }}</strong>
                    </div>
                </div>

                @if($user->affiliate)
                    <a href="{{ route('affiliate.dashboard') }}" class="w-full py-2.5 px-4 rounded-xl bg-yellow-50 hover:bg-yellow-100 text-amber-950 font-black text-xs text-center border border-yellow-200 transition">
                        🤝 Partner Promoter Portal →
                    </a>
                @endif
            </div>

        </div>

        {{-- Section 2: Compact Train Track Window (The Next 5 Stations) --}}
        @if(!empty($trainWindow))
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🚂</span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900 tracking-tight">
                            The Practicing Train • Your Immediate Station Window
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500">
                        Sequential units in chronological curriculum order. Advance carriage by carriage!
                    </p>
                </div>
                <a href="{{ route('learner.pscm.index') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1">
                    <span>View all {{ $totalUnits }} units</span>
                    <span>➔</span>
                </a>
            </div>

            {{-- Compact Horizontal Stations Track --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-2">
                @foreach($trainWindow as $item)
                    <a 
                        href="{{ route('learner.pscm.show', $item['session']->slug) }}"
                        class="p-4 rounded-2xl border transition-all duration-200 flex flex-col justify-between gap-3 group relative overflow-hidden {{ $item['is_active'] ? 'bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-500 ring-2 ring-blue-300 shadow-md' : ($item['is_completed'] ? 'bg-emerald-50/40 border-emerald-200 hover:border-emerald-300 hover:bg-emerald-50/70' : 'bg-slate-50/60 border-slate-200 hover:border-slate-300 hover:bg-white') }}"
                    >
                        {{-- Top status pill --}}
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black {{ $item['is_active'] ? 'text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md' : ($item['is_completed'] ? 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md' : 'text-slate-500') }}">
                                Unit #{{ $item['unit_number'] }}
                            </span>
                            @if($item['is_active'])
                                <span class="text-xs animate-pulse" title="Up Next">🚂</span>
                            @elseif($item['is_completed'])
                                <span class="text-xs text-emerald-600 font-bold" title="Completed">✓</span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <div class="space-y-1">
                            <h4 class="text-xs font-extrabold text-slate-800 line-clamp-2 group-hover:text-blue-600 leading-snug">
                                {{ $item['session']->title }}
                            </h4>
                            <span class="block text-[10px] font-medium text-slate-400">
                                {{ $item['session']->category ?? 'GK' }}
                            </span>
                        </div>

                        {{-- Bottom CTA / score badge --}}
                        <div class="pt-2 border-t border-slate-100/80 flex items-center justify-between text-[11px]">
                            @if($item['is_active'])
                                <span class="font-black text-blue-600">Play Now ➔</span>
                            @elseif($item['is_completed'])
                                <span class="font-bold text-emerald-700">
                                    {{ $item['score'] !== null ? number_format($item['score'], 1) . '/5' : 'Done ✓' }}
                                </span>
                            @else
                                <span class="text-slate-400 font-medium">Upcoming</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Section 2.5: Interactive Unit Search & Topic Finder --}}
        <div x-data="learnerSessionSearch()" class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#0052FF] text-xs font-black uppercase tracking-wider">
                        <span>🔍 Instant Unit Search</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                        Find &amp; Practice Any Syllabus Unit
                    </h3>
                    <p class="text-xs text-slate-500">
                        Search across all {{ $totalUnits }} micro-learning units by Malayalam topic, English keywords, or unit number.
                    </p>
                </div>

                <template x-if="query.trim().length > 0 || selectedCat !== ''">
                    <button @click="clearSearch()" class="text-xs font-bold text-slate-400 hover:text-rose-600 transition flex items-center gap-1 cursor-pointer self-start sm:self-auto">
                        <span>Clear filter</span>
                        <span class="font-mono">✕</span>
                    </button>
                </template>
            </div>

            {{-- Search Bar Input --}}
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    type="text" 
                    x-model="query" 
                    @keydown.escape="clearSearch()"
                    placeholder="Search any session: e.g. 'ഭരണഘടന', 'Active Passive Voice', 'Square Root', 'Unit 42'..."
                    class="w-full pl-11 pr-10 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0052FF] focus:border-transparent transition shadow-2xs"
                >
                <template x-if="query.trim().length > 0">
                    <button @click="query = ''" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </template>
            </div>

            {{-- Quick Category Filter Pills --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Quick Filter:</span>
                <button type="button" 
                        @click="setCategory('')" 
                        :class="selectedCat === '' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" 
                        class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer shrink-0">
                    All
                </button>
                <template x-for="cat in quickCategories" :key="cat.key">
                    <button type="button" 
                            @click="setCategory(cat.key)" 
                            :class="selectedCat === cat.key ? 'bg-[#0052FF] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap flex items-center gap-1.5 cursor-pointer shrink-0">
                        <span x-text="cat.icon"></span>
                        <span x-text="cat.label"></span>
                    </button>
                </template>
            </div>

            {{-- Matching Results Live Panel --}}
            <div x-show="query.trim().length > 0 || selectedCat !== ''" x-transition>
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-3">
                        <span>
                            Found <strong class="text-slate-900 font-black" x-text="filteredSessions.length"></strong> matching units
                            <template x-if="query.trim().length > 0">
                                <span> for "<span class="text-[#0052FF]" x-text="query"></span>"</span>
                            </template>
                            <template x-if="selectedCat">
                                <span> in <span class="text-indigo-600" x-text="selectedCat"></span></span>
                            </template>
                        </span>
                        <span class="text-[11px] text-slate-400 hidden sm:inline">Tap any card to play unit</span>
                    </div>

                    {{-- Cards Grid --}}
                    <template x-if="filteredSessions.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-[460px] overflow-y-auto pr-1">
                            <template x-for="item in filteredSessions" :key="item.id">
                                <a :href="item.url" 
                                   class="p-4 rounded-2xl border border-slate-200 hover:border-[#0052FF] bg-slate-50/50 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between gap-3 group relative overflow-hidden">
                                    
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-black text-[#0052FF] bg-blue-50 px-2 py-0.5 rounded-md">
                                            Unit #<span x-text="item.unit"></span>
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600" x-text="item.category"></span>
                                            <template x-if="item.access === 'free'">
                                                <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Free</span>
                                            </template>
                                        </div>
                                    </div>

                                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-[#0052FF] line-clamp-2 leading-snug" x-text="item.title"></h4>

                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                        <template x-if="item.completed">
                                            <span class="font-extrabold text-emerald-700 flex items-center gap-1">
                                                <span>✓ Passed</span>
                                                <template x-if="item.score !== null">
                                                    <span class="text-[10px] text-emerald-800 font-black" x-text="'(' + item.score + '/5)'"></span>
                                                </template>
                                            </span>
                                        </template>
                                        <template x-if="!item.completed">
                                            <span class="font-bold text-slate-400">Not attempted</span>
                                        </template>

                                        <span class="font-black text-[#0052FF] group-hover:translate-x-1 transition-transform flex items-center gap-1">
                                            <span>Start</span>
                                            <span class="text-sm">➔</span>
                                        </span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    {{-- Empty Results State --}}
                    <template x-if="filteredSessions.length === 0">
                        <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 px-4">
                            <span class="text-2xl">🔍</span>
                            <p class="text-sm font-bold text-slate-800 mt-2">No units match your search</p>
                            <p class="text-xs text-slate-400 mt-0.5 max-w-sm mx-auto">Try searching with a broader keyword, Malayalam/English term, or clear the category filter.</p>
                            <button type="button" @click="clearSearch()" class="mt-3 px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                                Clear Search
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Section 3: Interactive Category Tiles Grid (The Core Subject Roadmap) --}}
        <section class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-black uppercase tracking-wider">
                        <span>📚 Core Kerala PSC Syllabus</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Explore by Subject Categories
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Every PSC topic broken into high-yield 5-minute units. Track your subject mastery below.
                    </p>
                </div>

                <a href="{{ route('learner.pscm.index') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 self-start sm:self-auto">
                    <span>Browse complete catalog</span>
                    <span>➔</span>
                </a>
            </div>

            {{-- 3x3 Subject Category Tiles Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($categoryTiles as $tile)
                    <div class="bg-white rounded-3xl p-6 border {{ $tile['border_color'] }} shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1 relative overflow-hidden">
                        {{-- Subtle background tint --}}
                        <div class="absolute inset-0 bg-gradient-to-br {{ $tile['bg_gradient'] }} pointer-events-none"></div>

                        <div class="relative z-10 space-y-3">
                            {{-- Header: Icon + Unit count badge --}}
                            <div class="flex items-center justify-between">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    {{ $tile['icon'] }}
                                </div>
                                <span class="text-xs font-black px-2.5 py-1 rounded-full border {{ $tile['badge_color'] }}">
                                    {{ $tile['total_units'] }} Units
                                </span>
                            </div>

                            {{-- Title & Malayalam Subtitle --}}
                            <div>
                                <h3 class="text-base font-black text-slate-900 group-hover:text-[#0052FF] transition leading-snug">
                                    {{ $tile['name'] }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5 font-['Noto_Sans_Malayalam']">
                                    {{ $tile['malayalam'] }}
                                </p>
                            </div>

                            {{-- Category Mastery Bar --}}
                            <div class="pt-2">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-500 mb-1">
                                    <span>Subject Mastery</span>
                                    <span class="text-slate-800 font-extrabold">{{ $tile['completed_units'] }} / {{ $tile['total_units'] }} Done</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $tile['bar_gradient'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $tile['percent'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Footer CTA --}}
                        <div class="pt-4 mt-4 border-t border-slate-100 relative z-10 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-400">
                                @if($tile['completed_units'] >= $tile['total_units'] && $tile['total_units'] > 0)
                                    <span class="text-emerald-600 font-bold">🎉 Mastered</span>
                                @elseif($tile['completed_units'] > 0)
                                    <span>{{ $tile['percent'] }}% Complete</span>
                                @else
                                    <span>Ready to start</span>
                                @endif
                            </span>

                            <a href="{{ route('learner.pscm.index', ['category' => $tile['key']]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 group-hover:bg-[#0052FF] text-white text-xs font-bold transition shadow-xs">
                                <span>Practice Units</span>
                                <span class="text-xs">➔</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Section 4: Rapid Training Arenas & Shortcuts --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
            <a href="{{ route('learner.pscm.continue') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-blue-300 shadow-xs hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                    ⚡
                </div>
                <h3 class="text-base font-black text-slate-900">Next Daily Unit</h3>
                <p class="text-xs text-slate-500 mt-1">Jump right into your active unit in the sequential stream.</p>
            </a>

            <a href="{{ route('map.study') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-indigo-300 shadow-xs hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                    🌐
                </div>
                <h3 class="text-base font-black text-slate-900">3D Map &amp; Globe Lab</h3>
                <p class="text-xs text-slate-500 mt-1">Spatial memory drills for Kerala rivers, dams, and passes.</p>
            </a>

            <a href="{{ route('leaderboard') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-yellow-300 shadow-xs hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-2xl bg-yellow-50 text-amber-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                    🏆
                </div>
                <h3 class="text-base font-black text-slate-900">Live Leaderboard</h3>
                <p class="text-xs text-slate-500 mt-1">See your state rank against daily PSC drillers.</p>
            </a>
        </div>

    </div>
</div>

<script>
    function learnerSessionSearch() {
        return {
            query: '',
            selectedCat: '',
            allSessions: @js($searchIndex),
            quickCategories: [
                { key: 'History', label: 'History', icon: '🏛️' },
                { key: 'Geography', label: 'Geography', icon: '🌍' },
                { key: 'Constitution', label: 'Constitution', icon: '⚖️' },
                { key: 'Science', label: 'Science', icon: '🔬' },
                { key: 'Maths', label: 'Maths', icon: '📐' },
                { key: 'Current Affairs', label: 'Current Affairs', icon: '📰' },
                { key: 'English', label: 'English', icon: '🅰️' },
                { key: 'Malayalam', label: 'Malayalam', icon: '✍️' },
                { key: 'GK', label: 'GK', icon: '💡' },
            ],
            setCategory(cat) {
                this.selectedCat = this.selectedCat === cat ? '' : cat;
            },
            clearSearch() {
                this.query = '';
                this.selectedCat = '';
            },
            get filteredSessions() {
                const q = this.query.trim().toLowerCase();
                const cat = this.selectedCat;

                if (!q && !cat) {
                    return [];
                }

                return this.allSessions.filter(s => {
                    const matchCat = !cat || s.category === cat;
                    if (!matchCat) return false;

                    if (!q) return true;

                    // Match unit number e.g. "unit 12", "unit #12", or "12"
                    const unitClean = q.replace(/^unit\s*#?/i, '').trim();
                    if (unitClean !== '' && !isNaN(unitClean) && String(s.unit) === unitClean) {
                        return true;
                    }

                    // Match title, slug, or category (Malayalam unicode + English)
                    const titleMatch = (s.title || '').toLowerCase().includes(q);
                    const slugMatch = (s.slug || '').toLowerCase().includes(q);
                    const catMatch = (s.category || '').toLowerCase().includes(q);

                    return titleMatch || slugMatch || catMatch;
                });
            }
        };
    }
</script>
@endsection
