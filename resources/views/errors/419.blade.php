@extends('layouts.app')

@section('title', 'Session Expired (419) — PSCRanker')

@section('content')
<div class="py-16 sm:py-24 bg-gradient-to-b from-blue-50/70 via-slate-50 to-white min-h-[75vh] flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4 sm:px-6 text-center">
        
        <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mx-auto mb-6 shadow-lg shadow-amber-200/50">
            ⏳
        </div>

        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300 mb-3">
            Session Expired (419)
        </span>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-2">
            Page Session Timed Out
        </h1>

        <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed max-w-sm mx-auto mb-8">
            Your security session expired after being idle. Simply refresh the page or log in to continue your Kerala PSC preparation.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a 
                href="javascript:window.location.reload();" 
                class="w-full sm:w-auto px-6 py-3 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg transition active:scale-95"
            >
                🔄 Refresh Page
            </a>
            <a 
                href="{{ route('login') }}" 
                class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider rounded-xl border border-slate-300 transition"
            >
                🔑 Log In Again
            </a>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-500 hover:text-[#0052FF]">
                ← Back to Homepage
            </a>
        </div>

    </div>
</div>
@endsection
