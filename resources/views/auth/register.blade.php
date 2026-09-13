@extends('layouts.app')

@section('title', 'Create Free Account — PSCRanker.com')

@section('content')
<div class="py-12 sm:py-16 bg-gradient-to-b from-blue-50/70 via-slate-50 to-white min-h-[85vh] flex items-center justify-center">
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
                Create Free Account
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Unlock exclusive registered member sessions, track your rank &amp; save OMR tests.
            </p>
        </div>

        <!-- Registration Card -->
        <div 
            x-data="{ showPass: false, phone: '{{ old('phone') }}' }"
            class="bg-white rounded-3xl border-2 border-blue-100/90 shadow-2xl p-6 sm:p-8 relative overflow-hidden"
        >
            <!-- Decorative corner lightning -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-100/60 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Free Member Perk Badge -->
            <div class="mb-5 p-3 bg-blue-50/80 border border-blue-200/80 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-[#0052FF] text-white font-black flex items-center justify-center text-sm shrink-0 shadow-xs">
                    🎓
                </div>
                <div class="text-xs">
                    <span class="font-black text-blue-950 block">100% Free Member Access</span>
                    <span class="text-blue-700 font-medium text-[11px]">Instant unlock for Member Free capsules • No card needed</span>
                </div>
            </div>

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

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Full Name *
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        placeholder="e.g. Arun Kumar"
                        class="w-full px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900"
                    >
                </div>

                <!-- Phone Number (10 digits) -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Mobile Number (10 Digits) *
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-xs font-bold text-slate-500 select-none">
                            +91
                        </span>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            x-model="phone"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            required 
                            placeholder="9895012345"
                            class="w-full pl-12 pr-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900 font-mono"
                        >
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">You can log in later with either this mobile number or your email.</p>
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Email Address *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        placeholder="arun@example.com"
                        class="w-full px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900"
                    >
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Create Password (min 6 characters) *
                    </label>
                    <div class="relative">
                        <input 
                            :type="showPass ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required 
                            minlength="6"
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

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Confirm Password *
                    </label>
                    <input 
                        :type="showPass ? 'text' : 'password'" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        class="w-full px-4 py-3 text-sm font-semibold rounded-xl border-2 border-slate-200 focus:border-[#0052FF] focus:outline-none transition bg-slate-50/50 focus:bg-white text-slate-900"
                    >
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 active:scale-95 text-white font-black text-sm rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 border border-blue-400 cursor-pointer"
                    >
                        <span>CREATE FREE ACCOUNT</span>
                        <span class="text-yellow-300">⚡</span>
                    </button>
                </div>
            </form>

            <!-- Already have an account? -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-bold text-[#0052FF] hover:underline ml-1">
                        Log in here →
                    </a>
                </p>
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
