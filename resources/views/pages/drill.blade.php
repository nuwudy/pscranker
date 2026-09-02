@extends('layouts.app')

@section('title', '3-Min Kerala PSC Rapid Fire Speed Drill — PSCRanker')

@section('content')
<div class="py-6 sm:py-12 bg-gradient-to-b from-blue-50/70 via-white to-slate-50 min-h-[90vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- App Back Breadcrumb & Install Banner -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-black text-[#0052FF] hover:underline bg-white px-3 py-1.5 rounded-full border border-blue-100 shadow-xs">
                <span>← Back to Home</span>
            </a>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                    🟢 Live Session
                </span>
                <span class="text-xs text-slate-400 font-bold hidden sm:inline">Negative Marking Active: -0.33</span>
            </div>
        </div>

        <!-- Standalone Speed Drill Component -->
        <x-speed-drill :quizId="$quiz ? $quiz->id : 1" :standalone="true" />

    </div>
</div>
@endsection
