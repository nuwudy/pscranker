@extends('layouts.app')

@section('title', 'Admin & Candidate Login — PSCRanker.com')

@section('content')
<div class="py-12 sm:py-16 bg-gradient-to-b from-blue-50/70 via-slate-50 to-white min-h-[80vh] flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4 sm:px-6">
        
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#0052FF] to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition">
                    <span class="text-2xl">⚡</span>
                </div>
                <span class="text-2xl font-black text-[#0052FF] tracking-tight">
                    PSC<span class="text-slate-900">RANKER</span><span class="text-amber-500">.com</span>
                </span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Welcome Back
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Log in to manage learning sessions, content blocks & question banks.
            </p>
        </div>

        <!-- Login Card -->
        <div 
            x-data="{ showPass: false, email: '{{ old('email') }}' }"
            class="bg-white rounded-3xl border-2 border-blue-100/90 shadow-2xl p-6 sm:p-8 relative overflow-hidden"
        >
            <!-- Decorative corner lightning -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-yellow-100/60 rounded-full blur-2xl pointer-events-none"></div>

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs font-bold text-red-700 flex items-start gap-2">
                    <span class="text-base">⚠️</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Email Address
                    </label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            x-model="email"
                            required 
                            autofocus
                            placeholder="admin@pscranker.com"
                            class="w-full px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900"
                        >
                    </div>
                </div>

                <!-- Password Input with Show/Hide -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <input 
                            :type="showPass ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="••••••••"
                            class="w-full px-4 py-3 pr-12 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900"
                        >
                        <button 
                            type="button" 
                            @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 p-1 text-xs font-bold"
                            tabindex="-1"
                        >
                            <span x-show="!showPass">👁️</span>
                            <span x-show="showPass">🙈</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            value="1" 
                            class="w-4 h-4 rounded text-[#0052FF] border-slate-300 focus:ring-[#0052FF]"
                        >
                        <span class="text-xs font-bold text-slate-600">Remember me</span>
                    </label>

                    <span class="text-[11px] text-slate-400 font-medium">PSC Ranker Secure Access</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 border border-blue-400"
                    >
                        <span>LOG IN TO ACCOUNT</span>
                        <span class="text-yellow-300">⚡</span>
                    </button>
                </div>
            </form>

            <!-- Quick Auto-fill for Admin -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 font-medium mb-2">Admin Quick Fill:</p>
                <button 
                    type="button"
                    @click="email = 'admin@pscranker.com'; document.getElementById('password').value = 'Amter9388$';"
                    class="text-[11px] font-bold text-[#0052FF] bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-full border border-blue-200 transition"
                >
                    Fill admin@pscranker.com credentials ✨
                </button>
            </div>

        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-xs font-bold text-slate-500 hover:text-[#0052FF] transition">
                ← Back to Homepage
            </a>
        </div>

    </div>
</div>
@endsection
