@extends('layouts.app')

@section('title', 'Daily Speed Duel Live Leaderboard — PSCRanker')

@section('content')
<div class="py-8 sm:py-14 bg-gradient-to-b from-blue-50/60 to-white min-h-[85vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center max-w-xl mx-auto mb-10">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-amber-400 to-yellow-500 text-slate-950 flex items-center justify-center text-3xl mx-auto mb-4 shadow-lg shadow-yellow-500/20">
                🏆
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight">
                Daily Speed Duel
            </h1>
            <p class="text-base font-bold text-[#0052FF] mt-1">
                Live State-Wide Kerala PSC Candidate Ranking
            </p>
            <p class="text-xs text-slate-500 mt-2">
                Scores calculated with Kerala PSC negative marking formula: (Correct × 1) - (Wrong × 0.33).
            </p>
        </div>

        <!-- Timeframe Filters -->
        <div class="flex items-center justify-center gap-2 mb-8">
            <a href="{{ route('leaderboard', ['timeframe' => 'today']) }}" class="px-5 py-2 rounded-full text-xs font-black transition {{ $timeframe === 'today' ? 'bg-[#0052FF] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                ⚡ Today's Duel
            </a>
            <a href="{{ route('leaderboard', ['timeframe' => 'all']) }}" class="px-5 py-2 rounded-full text-xs font-black transition {{ $timeframe === 'all' ? 'bg-[#0052FF] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100' }}">
                🌟 All-Time Legends
            </a>
        </div>

        <!-- Top 3 Podium (1st, 2nd, 3rd) -->
        @if($leaderboard->count() >= 3)
            <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-8 items-end max-w-2xl mx-auto text-center">
                
                <!-- 2nd Place -->
                <div class="bg-white rounded-3xl p-4 sm:p-5 border-2 border-slate-200 shadow-md flex flex-col items-center">
                    <span class="text-2xl sm:text-3xl mb-1">🥈</span>
                    <div class="w-12 h-12 rounded-full bg-slate-300 text-slate-800 flex items-center justify-center font-black text-base shadow-inner mb-2">
                        {{ substr($leaderboard[1]->candidate_name, 0, 1) }}
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 truncate max-w-full">{{ $leaderboard[1]->candidate_name }}</div>
                    <div class="text-base sm:text-lg font-black text-[#0052FF] mt-1">{{ number_format($leaderboard[1]->score, 2) }} pts</div>
                    <span class="text-[10px] font-bold text-slate-400">Rank #2</span>
                </div>

                <!-- 1st Place (Winner, elevated) -->
                <div class="bg-gradient-to-b from-amber-50 to-white rounded-3xl p-5 sm:p-6 border-2 border-amber-300 shadow-xl flex flex-col items-center -translate-y-4">
                    <span class="text-3xl sm:text-4xl mb-1">👑</span>
                    <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-amber-400 to-yellow-500 text-slate-950 flex items-center justify-center font-black text-xl shadow-md mb-2">
                        {{ substr($leaderboard[0]->candidate_name, 0, 1) }}
                    </div>
                    <div class="text-sm sm:text-base font-black text-slate-950 truncate max-w-full">{{ $leaderboard[0]->candidate_name }}</div>
                    <div class="text-lg sm:text-xl font-black text-amber-600 mt-1">{{ number_format($leaderboard[0]->score, 2) }} pts</div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900 mt-1">State Rank #1</span>
                </div>

                <!-- 3rd Place -->
                <div class="bg-white rounded-3xl p-4 sm:p-5 border-2 border-slate-200 shadow-md flex flex-col items-center">
                    <span class="text-2xl sm:text-3xl mb-1">🥉</span>
                    <div class="w-12 h-12 rounded-full bg-amber-700 text-white flex items-center justify-center font-black text-base shadow-inner mb-2">
                        {{ substr($leaderboard[2]->candidate_name, 0, 1) }}
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 truncate max-w-full">{{ $leaderboard[2]->candidate_name }}</div>
                    <div class="text-base sm:text-lg font-black text-[#0052FF] mt-1">{{ number_format($leaderboard[2]->score, 2) }} pts</div>
                    <span class="text-[10px] font-bold text-slate-400">Rank #3</span>
                </div>

            </div>
        @endif

        <!-- Full Leaderboard Table -->
        <div class="bg-white rounded-3xl border-2 border-slate-200/80 shadow-xl overflow-hidden">
            <div class="p-4 sm:p-5 bg-slate-900 text-white flex items-center justify-between font-bold text-xs uppercase tracking-wider">
                <div class="flex items-center gap-6">
                    <span>Rank</span>
                    <span>Candidate</span>
                </div>
                <div class="flex items-center gap-6 sm:gap-10">
                    <span class="hidden sm:inline">Accuracy</span>
                    <span class="hidden sm:inline">Speed</span>
                    <span>Net Score</span>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($leaderboard as $index => $row)
                    <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-blue-50/50 transition {{ $index < 3 ? 'bg-amber-50/20' : '' }}">
                        <div class="flex items-center gap-4">
                            <span class="w-7 h-7 rounded-xl {{ $index === 0 ? 'bg-amber-400 text-slate-950 font-black' : ($index === 1 ? 'bg-slate-300 text-slate-900 font-bold' : ($index === 2 ? 'bg-amber-600 text-white font-bold' : 'bg-slate-100 text-slate-600 font-semibold')) }} flex items-center justify-center text-xs">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                                    <span>{{ $row->candidate_name }}</span>
                                    @if($index === 0) 🏆 @endif
                                </div>
                                <div class="text-[11px] font-medium text-slate-400">
                                    {{ $row->correct_answers }} Correct • {{ $row->wrong_answers }} Negative Traps
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 sm:gap-10 text-right">
                            <div class="hidden sm:block text-xs font-bold text-slate-600">
                                {{ $row->accuracy_percentage }}%
                            </div>
                            <div class="hidden sm:block text-xs font-medium text-slate-400">
                                {{ $row->time_taken_seconds }}s
                            </div>
                            <div class="text-base font-black text-[#0052FF]">
                                {{ number_format($row->score, 2) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Join the Duel CTA -->
        <div class="mt-8 text-center">
            <a 
                href="{{ route('drill.show') }}" 
                class="inline-flex items-center gap-2 px-8 py-4 bg-[#FFD200] hover:bg-[#F5C500] text-slate-950 font-black rounded-2xl shadow-lg border border-yellow-400 active:scale-95 transition"
            >
                <span>Drill Now & Claim Your Rank</span>
                <span class="text-lg">⚡</span>
            </a>
        </div>

    </div>
</div>
@endsection
