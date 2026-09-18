@extends('layouts.app')

@section('title', 'Become an Affiliate Partner — PSCRanker Promoter Program')

@section('content')
<div class="py-12 bg-slate-950 text-slate-100 min-h-screen relative overflow-hidden">
    <!-- Ambient Glow Backgrounds -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-yellow-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- Top Header Hero -->
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-yellow-400/20 text-yellow-300 border border-yellow-400/30 mb-4 shadow-sm">
                <span>🤝</span> PSCRANKER PARTNER PROGRAM
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                Guide Aspirants to Success. <br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-amber-300 to-yellow-500">
                    Earn Direct Monthly Commissions.
                </span>
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                No complicated coupon codes or discount links needed. Simply pitch Kerala PSC aspirants, record their phone numbers, and earn up to <span class="text-yellow-400 font-bold">15% - 20% commission</span> when they join!
            </p>
        </div>

        <!-- 4-Step Explanation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
            <div class="p-6 bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-slate-700 transition">
                <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-black text-xl mb-4 border border-blue-500/30">
                    1
                </div>
                <h3 class="font-bold text-white text-sm mb-1.5">You Call &amp; Pitch</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Connect with candidates, friends, or student groups and introduce PSCRanker's gamified OMR learning system.
                </p>
            </div>

            <div class="p-6 bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-slate-700 transition">
                <div class="w-12 h-12 rounded-xl bg-yellow-500/20 text-yellow-400 flex items-center justify-center font-black text-xl mb-4 border border-yellow-500/30">
                    2
                </div>
                <h3 class="font-bold text-white text-sm mb-1.5">Record Prospect Mobile</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Save the candidate's name and 10-digit mobile number in your Partner Dashboard. The lead is locked to you for 60 days.
                </p>
            </div>

            <div class="p-6 bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-slate-700 transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-xl mb-4 border border-emerald-500/30">
                    3
                </div>
                <h3 class="font-bold text-white text-sm mb-1.5">Student Enrols Normally</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    The student visits the site and signs up or pays online. Our engine detects their number and credits the commission to you!
                </p>
            </div>

            <div class="p-6 bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-slate-700 transition">
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-black text-xl mb-4 border border-purple-500/30">
                    4
                </div>
                <h3 class="font-bold text-white text-sm mb-1.5">Monthly Payout on 1st</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    All accumulated monthly earnings and performance bonuses are disbursed directly to your UPI ID or Bank account on the 1st of every month.
                </p>
            </div>
        </div>

        <!-- Registration Application Form Box -->
        <div class="max-w-2xl mx-auto bg-slate-900/90 rounded-3xl border border-slate-800 p-8 sm:p-10 shadow-2xl backdrop-blur-xl relative">
            <div class="mb-6 border-b border-slate-800 pb-5">
                <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                    <span>Apply to Become a Partner</span>
                    <span class="text-yellow-400">⚡</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Fill in your details below to activate your affiliate promoter account.
                </p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 rounded-xl bg-blue-500/20 border border-blue-500/40 text-blue-300 text-xs font-bold">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('affiliate.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Name Field -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Your Full Name <span class="text-rose-400">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', Auth::user()?->name ?? '') }}" 
                        required 
                        placeholder="e.g. Anu Krishna"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-yellow-400 transition"
                    >
                    @error('name')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone & Email Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">WhatsApp / Calling Mobile <span class="text-rose-400">*</span></label>
                        <input 
                            type="tel" 
                            name="phone" 
                            value="{{ old('phone', Auth::user()?->phone ?? '') }}" 
                            required 
                            placeholder="10-digit mobile number"
                            maxlength="10"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                        >
                        @error('phone')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Email Address <span class="text-rose-400">*</span></label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', Auth::user()?->email ?? '') }}" 
                            required 
                            placeholder="e.g. anu@gmail.com"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-yellow-400 transition"
                        >
                        @error('email')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @guest
                <!-- Password if not logged in -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Create Password for Dashboard Login <span class="text-rose-400">*</span></label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Minimum 6 characters"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-yellow-400 transition"
                    >
                    @error('password')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endguest

                <!-- Payout Details: UPI & Bank Account -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-5 space-y-4">
                    <div>
                        <div class="flex items-center gap-1.5 text-xs font-black text-yellow-400 uppercase tracking-wider mb-1">
                            <span>💳</span> Payout &amp; Commission Settlement Details
                        </div>
                        <p class="text-[11px] text-slate-400">
                            Enter your UPI ID or Bank Account details below. Monthly commissions are directly credited on the 1st of each month.
                        </p>
                    </div>

                    <!-- UPI ID Field -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            <span>UPI ID</span>
                            <span class="text-[11px] text-slate-500 font-normal ml-1">(GPay / PhonePe / Paytm / BHIM)</span>
                        </label>
                        <input 
                            type="text" 
                            name="upi_id" 
                            value="{{ old('upi_id') }}" 
                            placeholder="e.g. anu@okaxis or 9895000000@paytm"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                        >
                        @error('upi_id')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Divider -->
                    <div class="relative flex items-center justify-center my-2">
                        <div class="border-t border-slate-800 w-full"></div>
                        <span class="bg-slate-900 px-2.5 text-[10px] uppercase tracking-wider font-bold text-slate-500">And / Or Bank Account</span>
                        <div class="border-t border-slate-800 w-full"></div>
                    </div>

                    <!-- Bank Account Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">Bank Name</label>
                            <input 
                                type="text" 
                                name="bank_name" 
                                value="{{ old('bank_name') }}" 
                                placeholder="e.g. Federal Bank / SBI / Canara"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                            >
                            @error('bank_name')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">Account Holder Name</label>
                            <input 
                                type="text" 
                                name="account_holder" 
                                value="{{ old('account_holder') }}" 
                                placeholder="As per bank passbook"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                            >
                            @error('account_holder')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">Bank Account Number</label>
                            <input 
                                type="text" 
                                name="account_number" 
                                value="{{ old('account_number') }}" 
                                placeholder="Account Number"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                            >
                            @error('account_number')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">IFSC Code</label>
                            <input 
                                type="text" 
                                name="ifsc_code" 
                                value="{{ old('ifsc_code') }}" 
                                placeholder="e.g. FDRL0001234, SBIN0001234"
                                maxlength="20"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono uppercase transition"
                            >
                            @error('ifsc_code')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes / Context -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Tell us about yourself (Optional)</label>
                    <textarea 
                        name="notes" 
                        rows="2" 
                        placeholder="e.g. Tele-counselor / PSC coaching group admin / Student mentor"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                    >{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-300 hover:to-amber-400 text-slate-950 font-black text-sm rounded-xl shadow-lg hover:shadow-yellow-400/20 transition transform active:scale-98 flex items-center justify-center gap-2"
                >
                    <span>Activate My Partner Account</span>
                    <span class="text-lg">➔</span>
                </button>

                <p class="text-[11px] text-center text-slate-500">
                    Already an affiliate? <a href="{{ route('login') }}" class="text-yellow-400 hover:underline">Log in here</a> to access your dashboard.
                </p>
            </form>
        </div>

    </div>
</div>
@endsection
