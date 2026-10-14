@extends('layouts.app')

@section('title', 'My Account & Learning Train — PSCRanker')

@section('content')
<div class="bg-gradient-to-b from-[#F0F5FF] via-slate-50 to-white min-h-screen py-8 sm:py-12" x-data="learningTrainHub()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Welcome Header Banner --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#0052FF] text-xs font-black uppercase tracking-wider">
                    <span>👋 Student Learning Hub</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Namaskaram, {{ $user->name }}!
                </h1>
                <p class="text-slate-500 text-sm font-medium">
                    Kerala PSC Sequential Practice Train • Daily 5-minute micro-learning routine.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('learner.pscm.continue') }}" class="px-5 py-3 rounded-2xl bg-[#0052FF] hover:bg-[#003ECC] text-white font-black text-sm shadow-md shadow-blue-500/20 active:scale-95 transition flex items-center gap-2">
                    <span>▶️ Resume Game</span>
                    <span class="text-xs">⚡</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Top Grid: Main Game Launch Hero & Plan/Subscription Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 1. Main Game Launcher Hero Card (2 Cols on Desktop) --}}
            <div class="lg:col-span-2 bg-gradient-to-br from-[#0052FF] via-indigo-600 to-[#1E1B4B] rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-600/20 relative overflow-hidden flex flex-col justify-between border-2 border-indigo-400/30">
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
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">
                                {{ $continueSession->title }}
                            </h2>
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-blue-100">
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">🏷️ {{ $continueSession->category ?? 'General Knowledge' }}</span>
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">⏱️ 5-Minute Unit</span>
                                <span class="px-2.5 py-1 rounded-lg bg-white/15 backdrop-blur-md">📝 OMR Evaluation Included</span>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2">
                            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                                🏆 Curriculum Completed!
                            </h2>
                            <p class="text-xs text-blue-100 font-medium">
                                You have practiced all published units on the curriculum train. Review or retake any unit below!
                            </p>
                        </div>
                    @endif

                    {{-- Curriculum Train Progress Gauge --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs font-bold text-blue-100 mb-1.5">
                            <span>Syllabus Train Progress</span>
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
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-6">
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
                                Full access to all 154+ Kerala PSC AI units, OMR evaluation, and unlimited speed drills.
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

                {{-- Candidate Details --}}
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

        {{-- Section 3: The Practicing Train (The Sequential Roadmap) --}}
        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6" id="practice-train">
            
            {{-- Train Header & Controls --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-black uppercase tracking-wider">
                        <span>🚂 Sequential Roadmap</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        The Practicing Train of Sessions
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Units ordered chronologically across all PSC subjects. Finish each carriage to advance the train!
                    </p>
                </div>

                {{-- Interactive Filter Buttons --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">
                        All Units ({{ $totalUnits }})
                    </button>
                    <button @click="filter = 'completed'" :class="filter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1">
                        <span>✅ Completed ({{ $completedCount }})</span>
                    </button>
                    <button @click="filter = 'upcoming'" :class="filter === 'upcoming' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition">
                        ⏳ Remaining ({{ max(0, $totalUnits - $completedCount) }})
                    </button>
                    <button @click="scrollToActive()" class="px-3 py-1.5 rounded-xl bg-yellow-400 hover:bg-yellow-300 text-slate-950 text-xs font-black shadow-xs transition flex items-center gap-1">
                        <span>📍 Jump to My Carriage</span>
                    </button>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="relative">
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Search unit by topic or category (e.g. Kathakali, History, Science)..." 
                    class="w-full px-4 py-3 pl-10 rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium placeholder-slate-400"
                >
                <span class="absolute left-3.5 top-3.5 text-slate-400 text-sm">🔍</span>
            </div>

            {{-- The Visual Train Track List --}}
            <div class="relative pl-6 sm:pl-8 space-y-4 before:absolute before:left-3 sm:before:left-4 before:top-4 before:bottom-4 before:w-1 before:bg-gradient-to-b before:from-blue-600 before:via-indigo-500 before:to-slate-200 before:rounded-full">
                
                @forelse($sessions as $index => $sess)
                    @php
                        $unitNum = $index + 1;
                        $isCompleted = isset($completedAttempts[$sess->id]);
                        $attempt = $isCompleted ? $completedAttempts[$sess->id] : null;
                        $score = $attempt && isset($attempt->gamification['score']) ? $attempt->gamification['score'] : null;
                        $isActive = $continueSession && $continueSession->id === $sess->id;
                    @endphp

                    <div 
                        id="carriage-unit-{{ $unitNum }}"
                        x-show="matchesFilter({{ $isCompleted ? 'true' : 'false' }}, {{ $isActive ? 'true' : 'false' }}, '{{ addslashes($sess->title) }}', '{{ addslashes($sess->category ?? '') }}')"
                        class="relative transition-all duration-300 group"
                    >
                        {{-- Station Node on the Railroad Spine --}}
                        <div class="absolute -left-6 sm:-left-8 top-5 -translate-x-1/2 flex items-center justify-center">
                            @if($isCompleted)
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-black shadow-md shadow-emerald-500/30 ring-4 ring-white">
                                    ✓
                                </div>
                            @elseif($isActive)
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#FFD200] text-slate-950 flex items-center justify-center text-sm font-black shadow-lg shadow-yellow-500/40 ring-4 ring-blue-500 animate-pulse">
                                    🚂
                                </div>
                            @else
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-100 text-slate-500 border-2 border-slate-300 flex items-center justify-center text-[10px] font-bold ring-4 ring-white">
                                    {{ $unitNum }}
                                </div>
                            @endif
                        </div>

                        {{-- Carriage Card --}}
                        <div class="rounded-2xl sm:rounded-3xl p-5 sm:p-6 transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-50/80 via-indigo-50/60 to-purple-50/40 border-2 border-blue-500 shadow-lg ring-4 ring-blue-100' : ($isCompleted ? 'bg-emerald-50/30 border border-emerald-200/80 hover:shadow-md' : 'bg-white border border-slate-200 hover:border-slate-300 hover:shadow-md') }} flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            
                            <div class="space-y-2 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black {{ $isActive ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                        Unit #{{ $unitNum }}
                                    </span>
                                    
                                    <span class="text-xs font-bold text-slate-400">•</span>
                                    
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
                                        <span>🏷️</span>
                                        <span>{{ $categories[$sess->category] ?? ($sess->category ?? 'GK') }}</span>
                                    </span>

                                    @if($isActive)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase tracking-wider text-blue-700 bg-blue-100 px-2.5 py-0.5 rounded-full border border-blue-200 animate-pulse">
                                            👉 Your Active Carriage
                                        </span>
                                    @elseif($isCompleted)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                            ✅ Mastered
                                        </span>
                                    @endif

                                    @if(($sess->access_level ?? 'premium') === 'premium')
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300">
                                            👑 PRO
                                        </span>
                                    @elseif($sess->access_level === 'registered')
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800 border border-blue-200">
                                            👤 Member
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            🎁 Free
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-blue-600 transition leading-snug">
                                    <a href="{{ route('learner.pscm.show', $sess->slug) }}">
                                        {{ $sess->title }}
                                    </a>
                                </h3>

                                @if($isCompleted && $score !== null)
                                    <div class="inline-flex items-center gap-2 text-xs font-bold text-emerald-800 bg-emerald-100/70 px-2.5 py-1 rounded-lg">
                                        <span>🎯 OMR Score: <strong>{{ number_format($score, 2) }} / 5</strong></span>
                                        @if($attempt && $attempt->completed_at)
                                            <span class="text-[10px] text-emerald-600 font-normal">({{ $attempt->completed_at->diffForHumans() }})</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Direct 1-Click Action Button --}}
                            <div class="flex-shrink-0 w-full md:w-auto flex items-center gap-2">
                                @if($isActive)
                                    <a href="{{ route('learner.pscm.show', $sess->slug) }}" class="w-full md:w-auto px-6 py-3 bg-[#0052FF] hover:bg-[#003ECC] text-white font-black text-sm rounded-xl shadow-md shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-2">
                                        <span>▶️ Play Unit #{{ $unitNum }}</span>
                                        <span>⚡</span>
                                    </a>
                                @elseif($isCompleted)
                                    <a href="{{ route('learner.pscm.show', $sess->slug) }}" class="w-full md:w-auto px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition text-center flex items-center justify-center gap-1.5">
                                        <span>🔁 Retake / Practice</span>
                                    </a>
                                @else
                                    <a href="{{ route('learner.pscm.show', $sess->slug) }}" class="w-full md:w-auto px-5 py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl transition text-center flex items-center justify-center gap-1.5">
                                        <span>Start Carriage →</span>
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200">
                        <p class="text-sm text-slate-500">No units published yet in the curriculum.</p>
                    </div>
                @endforelse

            </div>

        </section>

        {{-- Section 4: Gamified Practice Arenas & Shortcuts --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <a href="{{ route('learner.pscm.index') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-blue-300 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                    📚
                </div>
                <h3 class="text-base font-black text-slate-900">Explore by Category</h3>
                <p class="text-xs text-slate-500 mt-1">Browse all 9 core PSC subjects individually.</p>
            </a>

            <a href="{{ route('map.study') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-indigo-300 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">
                    🌐
                </div>
                <h3 class="text-base font-black text-slate-900">3D Map &amp; Globe Lab</h3>
                <p class="text-xs text-slate-500 mt-1">Spatial memory training for Kerala &amp; Indian geography.</p>
            </a>

            <a href="{{ route('leaderboard') }}" class="p-6 rounded-3xl bg-white border border-slate-200 hover:border-yellow-300 shadow-sm hover:shadow-md transition group">
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
    function learningTrainHub() {
        return {
            filter: 'all',
            searchQuery: '',
            activeUnitNumber: {{ $continueUnitNumber }},

            matchesFilter(isCompleted, isActive, title, category) {
                // Status filtering
                if (this.filter === 'completed' && !isCompleted) return false;
                if (this.filter === 'upcoming' && isCompleted) return false;

                // Search query filtering
                if (this.searchQuery && this.searchQuery.trim().length > 0) {
                    const q = this.searchQuery.toLowerCase();
                    const t = (title || '').toLowerCase();
                    const c = (category || '').toLowerCase();
                    return t.includes(q) || c.includes(q);
                }

                return true;
            },

            scrollToActive() {
                this.filter = 'all';
                this.searchQuery = '';
                this.$nextTick(() => {
                    const el = document.getElementById(`carriage-unit-${this.activeUnitNumber}`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        }
    }
</script>
@endsection
