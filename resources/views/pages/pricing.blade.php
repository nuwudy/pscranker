@extends('layouts.app')

@section('title', 'Prepaid Subscription Plans & Pricing — PSCRanker.com')

@section('content')
<div class="py-10 sm:py-16 bg-gradient-to-b from-blue-50/50 via-white to-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Hero Badge & Heading -->
        <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-4 border border-blue-200 shadow-xs">
                <span>⚡ Kerala PSC Prep from just ₹{{ number_format($dailyBaseFee, 0) }}/Day</span>
                <span>•</span>
                <span>Zero Auto-Debits</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight leading-tight">
                Invest in Your Rank. <br class="hidden sm:inline">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#0052FF] via-blue-600 to-indigo-600">
                    Pay Once, Drill Unlimited.
                </span>
            </h1>
            <p class="text-sm sm:text-base text-slate-600 font-medium mt-3 leading-relaxed">
                Choose your learning pass directly below. No hidden fees, no auto-renewal traps. As your commitment scales from 1 day to 1 year, your effective daily cost drops up to 45%!
            </p>
        </div>

        <!-- ============================================================= -->
        <!-- 6-CARD INTERACTIVE PLAN SELECTOR & CHECKOUT ENGINE -->
        <!-- ============================================================= -->
        <div 
            x-data="pricingEngine({{ json_encode($tiers) }}, '{{ $razorpayKey }}')"
            class="mb-16"
        >
            <!-- SECTION 1: THE 6 VISUAL CARDS GRID (Client 2x3 Mockup Layout) -->
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4 px-1">
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-slate-500">Step 1</span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">Select Your Target Pass</h2>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                        ⚡ 1-Click Instant Activation
                    </span>
                </div>

                <!-- 6 Cards Grid: 1 Col on Mobile, 2 Col on Sm, 3 Col on Lg (Matching 2x3 Mockup) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <template x-for="tier in tiers" :key="tier.days">
                        <div 
                            @click="selectTier(tier.days)"
                            :class="{
                                'ring-4 ring-[#0052FF] shadow-2xl scale-[1.02] border-[#0052FF]': selectedDays === tier.days,
                                'border-slate-200 hover:border-slate-400 shadow-md hover:shadow-lg': selectedDays !== tier.days
                            }"
                            class="relative bg-white rounded-3xl border-2 transition-all duration-200 cursor-pointer overflow-hidden flex flex-col justify-between p-6 select-none group"
                        >
                            <!-- Top Colored Banner Header matching client mockup -->
                            <div 
                                class="absolute top-0 left-0 right-0 h-3"
                                :style="'background-color: ' + tier.color"
                            ></div>

                            <div>
                                <!-- Top Row: Duration Title & Selection Indicator -->
                                <div class="flex items-start justify-between gap-2 pt-1 mb-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span 
                                                class="w-3 h-3 rounded-full shrink-0"
                                                :style="'background-color: ' + tier.color"
                                            ></span>
                                            <h3 class="text-xl font-black text-slate-950 tracking-tight" x-text="tier.name"></h3>
                                        </div>
                                        <div class="text-xs font-bold text-slate-500 font-['Noto_Sans_Malayalam'] mt-0.5 ml-5" x-text="tier.name_malayalam"></div>
                                    </div>

                                    <!-- Selected Checkmark Badge -->
                                    <div 
                                        x-show="selectedDays === tier.days"
                                        class="px-2.5 py-1 rounded-full text-[11px] font-black uppercase text-white bg-[#0052FF] shadow-xs flex items-center gap-1 shrink-0"
                                    >
                                        <span>✓</span>
                                        <span>Selected</span>
                                    </div>
                                    <div 
                                        x-show="selectedDays !== tier.days"
                                        class="w-6 h-6 rounded-full border-2 border-slate-300 group-hover:border-[#0052FF] shrink-0 flex items-center justify-center transition"
                                    >
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 group-hover:bg-[#0052FF] opacity-0 group-hover:opacity-100 transition"></span>
                                    </div>
                                </div>

                                <!-- Popular / Best Value / Rebate Badges -->
                                <div class="flex flex-wrap items-center gap-1.5 my-3 ml-5">
                                    <span 
                                        x-show="tier.is_popular"
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-700 border border-orange-200"
                                    >
                                        🔥 Most Popular
                                    </span>
                                    <span 
                                        x-show="tier.is_best_value"
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-pink-100 text-pink-700 border border-pink-200"
                                    >
                                        👑 Best Value
                                    </span>
                                    <span 
                                        x-show="tier.rebate_percent > 0"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider text-white"
                                        :style="'background-color: ' + tier.color"
                                        x-text="tier.rebate_percent + '% REBATE'"
                                    ></span>
                                </div>

                                <!-- Main Price & Daily Breakdown Display -->
                                <div class="my-4 pt-3 border-t border-slate-100 ml-5">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-4xl sm:text-5xl font-black text-slate-950 font-mono tracking-tight" x-text="'₹' + tier.final_price"></span>
                                        <span 
                                            x-show="tier.discount_amount > 0"
                                            class="text-sm font-bold text-slate-400 line-through font-mono"
                                            x-text="'₹' + tier.base_total"
                                        ></span>
                                    </div>

                                    <!-- Prominent Per-Day Cost Anchor -->
                                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200">
                                        <span class="text-xs font-bold text-slate-600">Effective:</span>
                                        <span class="text-sm font-black text-emerald-600 font-mono">₹<span x-text="tier.per_day_cost"></span></span>
                                        <span class="text-[11px] font-medium text-slate-500">/ day</span>
                                    </div>

                                    <div x-show="tier.discount_amount > 0" class="text-xs font-black text-emerald-600 mt-2">
                                        🎉 Total Savings: ₹<span x-text="tier.discount_amount"></span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed ml-5 mt-2" x-text="tier.description"></p>
                            </div>

                            <!-- Bottom Action / Select Trigger -->
                            <div class="mt-6 pt-4 border-t border-slate-100 ml-5">
                                <button 
                                    type="button"
                                    :class="{
                                        'bg-slate-950 text-white font-black': selectedDays === tier.days,
                                        'bg-slate-100 group-hover:bg-blue-50 text-slate-700 group-hover:text-[#0052FF] font-bold': selectedDays !== tier.days
                                    }"
                                    class="w-full py-2.5 px-4 rounded-xl text-xs uppercase tracking-wider transition text-center"
                                >
                                    <span x-show="selectedDays === tier.days">Selected Pass ✓</span>
                                    <span x-show="selectedDays !== tier.days">Select This Plan →</span>
                                </button>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <!-- SECTION 2: HIGH-CONVERSION CHECKOUT DOCK -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-gradient-to-b from-slate-950 via-slate-900 to-blue-950 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border-2 border-yellow-400/90 relative overflow-hidden ring-8 ring-blue-500/10">
                    
                    <!-- Background Glow -->
                    <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#0052FF]/30 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-16 -bottom-16 w-56 h-56 bg-yellow-400/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10">
                        
                        <!-- Header with Status Pill -->
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-6 pb-4 border-b border-slate-800">
                            <div>
                                <span class="text-xs font-bold text-yellow-400 uppercase tracking-wider block">Checkout Summary</span>
                                <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                                    <span x-text="currentTier.name"></span>
                                    <span class="text-xs px-2.5 py-0.5 rounded-full text-slate-950 font-black uppercase" :style="'background-color: ' + currentTier.color" x-text="currentTier.days + ' Days'"></span>
                                </h2>
                            </div>
                            <div class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-black uppercase">
                                Instant UPI Activation ⚡
                            </div>
                        </div>

                        <!-- Calculation Breakdown Bar -->
                        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 mb-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs text-slate-300">Plan Duration: <strong class="text-white"><span x-text="currentTier.days"></span> Days</strong> (<span x-text="currentTier.name_malayalam"></span>)</div>
                                    <div class="text-xs text-slate-300 mt-1">
                                        Effective Daily Rate: <strong class="text-emerald-400 font-mono">₹<span x-text="currentTier.per_day_cost"></span> / day</strong>
                                        <span x-show="currentTier.rebate_percent > 0" class="text-yellow-400 font-bold ml-2">
                                            (<span x-text="currentTier.rebate_percent"></span>% off linear rate)
                                        </span>
                                    </div>
                                </div>
                                <div class="sm:text-right">
                                    <div class="text-[11px] font-bold text-slate-400 uppercase">Payable Lump Sum:</div>
                                    <div class="text-3xl sm:text-4xl font-black text-yellow-400 font-mono">
                                        ₹<span x-text="currentTier.final_price"></span>
                                    </div>
                                    <div x-show="currentTier.discount_amount > 0" class="text-xs font-bold text-emerald-400">
                                        You Save ₹<span x-text="currentTier.discount_amount"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Info Prompt (If Guest) -->
                        @guest
                            <div class="mb-6 p-4 rounded-2xl bg-blue-950/60 border border-blue-500/30 text-xs space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-white">Enter Your Details for Instant Account Activation:</span>
                                    <a href="{{ route('login') }}" class="text-yellow-400 hover:underline font-bold">Already registered? Log in</a>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <input 
                                        type="text" 
                                        x-model="customerName" 
                                        placeholder="Your Full Name" 
                                        class="w-full bg-slate-800 px-3.5 py-2.5 rounded-xl text-white placeholder-slate-400 text-xs border border-slate-700 focus:outline-hidden focus:border-yellow-400"
                                    >
                                    <input 
                                        type="email" 
                                        x-model="customerEmail" 
                                        placeholder="Email Address (for course login)" 
                                        class="w-full bg-slate-800 px-3.5 py-2.5 rounded-xl text-white placeholder-slate-400 text-xs border border-slate-700 focus:outline-hidden focus:border-yellow-400"
                                    >
                                </div>
                            </div>
                        @endguest

                        <!-- Pay with Razorpay / UPI Button -->
                        <button 
                            type="button"
                            @click="initiateRazorpayCheckout()" 
                            :disabled="loading"
                            class="w-full py-4 px-6 bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-300 hover:to-amber-300 active:scale-98 text-slate-950 font-black text-base sm:text-lg rounded-2xl shadow-xl transition-all flex items-center justify-center gap-2 border-2 border-yellow-300 disabled:opacity-50 cursor-pointer"
                        >
                            <span x-show="!loading" class="flex items-center gap-2">
                                <span>Proceed to Pay ₹<span x-text="currentTier.final_price"></span> via UPI / Razorpay</span>
                                <span>🚀</span>
                            </span>
                            <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                                <svg class="animate-spin h-5 w-5 text-slate-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Connecting Razorpay Gateway...</span>
                            </span>
                        </button>

                        <!-- Trust Bar & Payment Icons -->
                        <div class="mt-4 pt-4 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-400">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-400">🔒</span>
                                <span>256-Bit Encrypted Razorpay Gateway</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-slate-300 font-bold">UPI</span>
                                <span>•</span>
                                <span class="text-slate-300 font-bold">PhonePe</span>
                                <span>•</span>
                                <span class="text-slate-300 font-bold">GPay</span>
                                <span>•</span>
                                <span class="text-slate-300 font-bold">Cards</span>
                                <span>•</span>
                                <span class="text-slate-300 font-bold">NetBanking</span>
                            </div>
                        </div>

                        <!-- Success Message Alert -->
                        <div 
                            x-show="paymentSuccess" 
                            x-transition 
                            class="mt-4 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs text-center font-bold"
                            style="display: none;"
                        >
                            🎉 Payment verified successfully! Your account now has full access. Redirecting to course...
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================================= -->
        <!-- COMPLETE COMPARISON SCHEDULE (All 6 Tiers from 1 Day to 1 Year) -->
        <!-- ============================================================= -->
        <div class="mb-16">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                    Schedule of Progressive Rebates
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Transparent, pro-student pricing tailored for Kerala PSC preparation cycles.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @foreach($tiers as $tier)
                    <div class="bg-white rounded-3xl p-5 border {{ $tier['is_popular'] ? 'border-2 border-orange-500 shadow-lg ring-4 ring-orange-500/10' : ($tier['is_best_value'] ? 'border-2 border-pink-500 shadow-md ring-4 ring-pink-500/10' : 'border-slate-200 shadow-xs') }} flex flex-col justify-between relative overflow-hidden">
                        
                        <div 
                            class="absolute top-0 left-0 right-0 h-2"
                            style="background-color: {{ $tier['color'] }}"
                        ></div>

                        @if($tier['is_popular'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-orange-500 text-white text-[10px] font-black uppercase tracking-wider shadow">
                                Most Popular 🔥
                            </div>
                        @elseif($tier['is_best_value'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-pink-500 text-white text-[10px] font-black uppercase tracking-wider shadow">
                                Best Value 👑
                            </div>
                        @endif

                        <div class="pt-2">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                {{ $tier['days'] }} {{ $tier['days'] === 1 ? 'Day' : 'Days' }}
                            </div>
                            <h3 class="text-base font-black text-slate-900 leading-tight">
                                {{ $tier['name'] }}
                            </h3>
                            <div class="text-[11px] font-bold text-[#0052FF] font-['Noto_Sans_Malayalam'] mt-0.5">
                                {{ $tier['name_malayalam'] }}
                            </div>

                            <div class="my-4 pt-4 border-t border-slate-100">
                                @if($tier['discount_amount'] > 0)
                                    <div class="text-xs text-slate-400 line-through font-mono">₹{{ $tier['base_total'] }}</div>
                                @else
                                    <div class="text-xs text-slate-400 font-mono">Base Linear Rate</div>
                                @endif
                                <div class="text-3xl font-black text-slate-900 font-mono">
                                    ₹{{ $tier['final_price'] }}
                                </div>
                                <div class="text-[11px] font-bold text-emerald-600 mt-0.5">
                                    Effective: ₹{{ $tier['per_day_cost'] }} / day
                                </div>
                                @if($tier['rebate_percent'] > 0)
                                    <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Save {{ $tier['rebate_percent'] }}% (₹{{ $tier['discount_amount'] }})
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ $tier['description'] }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <ul class="text-xs text-slate-600 space-y-2 mb-4 font-medium">
                                <li class="flex items-center gap-1.5">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>All 4-Phase Micro Sessions</span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>Authentic OMR Bubble Sim</span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>Negative Penalty Analytics</span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="text-emerald-500 font-bold">✓</span>
                                    <span>Daily Kerala PSC Leaderboard</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- FAQ Section for Aspirants -->
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-black text-slate-900 text-center mb-6">Frequently Asked Questions</h2>
            <div class="space-y-3 text-xs sm:text-sm">
                
                <div class="bg-white rounded-2xl p-4 border border-slate-200">
                    <h4 class="font-black text-slate-900 mb-1">Is this a recurring subscription with auto-debit?</h4>
                    <p class="text-slate-600 leading-relaxed">No. All plans are 100% prepaid. Your card or UPI will never be charged automatically. Once your prepaid cycle completes, access simply expires unless you manually choose to renew.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200">
                    <h4 class="font-black text-slate-900 mb-1">How fast is account activation after paying via UPI?</h4>
                    <p class="text-slate-600 leading-relaxed">Instant! Our Razorpay integration automatically credits your duration and unlocks all locked units the moment the payment is verified.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200">
                    <h4 class="font-black text-slate-900 mb-1">Can I start with 1 Day and upgrade later?</h4>
                    <p class="text-slate-600 leading-relaxed">Yes! You can try out PSCRanker for 1 Day for just ₹10. Whenever you are ready to prepare seriously, you can upgrade to 1 Week, 1 Month, 3 Months, 6 Months, or 1 Year to lock in progressive discounts up to 45%.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200">
                    <h4 class="font-black text-slate-900 mb-1">What payment methods do you accept?</h4>
                    <p class="text-slate-600 leading-relaxed">All major Indian payment methods: UPI (Google Pay, PhonePe, Paytm, BHIM), Debit Cards, Credit Cards, Net Banking, and Wallets through Razorpay.</p>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Include Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
function pricingEngine(tiers, razorpayKey) {
    const defaultTier = tiers.find(t => t.days === 90) || tiers.find(t => t.is_popular) || tiers[0];
    return {
        tiers: tiers,
        razorpayKey: razorpayKey,
        selectedDays: defaultTier ? defaultTier.days : 90, // Default to 90 days (3 Months - Most Popular)
        currentTier: defaultTier,
        customerName: '{{ Auth::user()?->name ?? "" }}',
        customerEmail: '{{ Auth::user()?->email ?? "" }}',
        loading: false,
        paymentSuccess: false,

        selectTier(days) {
            this.selectedDays = days;
            const found = this.tiers.find(t => t.days === days);
            if (found) {
                this.currentTier = found;
            }
        },

        async initiateRazorpayCheckout() {
            this.loading = true;
            try {
                // Step 1: Request Order ID from backend
                const response = await fetch('{{ route("subscription.create-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        days: this.selectedDays,
                        months: this.currentTier.months,
                        name: this.customerName || 'Kerala PSC Aspirant',
                        email: this.customerEmail || 'candidate@pscranker.com'
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'Unable to create payment order. Please try again.');
                    this.loading = false;
                    return;
                }

                // If running in local/test demo mode without live Razorpay keys
                if (data.is_mock) {
                    const confirmMock = confirm(`[Razorpay Test Mode]\n\nPlan: ${data.plan_name}\nPayable: ₹${data.amount_inr}\nDuration: ${this.currentTier.days} Days\n\nClick OK to simulate instant UPI payment success & unlock course.`);
                    if (confirmMock) {
                        await this.verifyAndComplete(data.order_id, 'pay_mock_' + Math.random().toString(36).substring(7));
                    }
                    this.loading = false;
                    return;
                }

                // Step 2: Open Razorpay standard checkout popup
                const options = {
                    key: data.key,
                    amount: data.amount,
                    currency: data.currency,
                    name: 'PSCRanker.com',
                    description: `Prepaid Pass: ${data.plan_name}`,
                    image: '/images/mascot.jpg',
                    order_id: data.order_id,
                    handler: async (response) => {
                        await this.verifyAndComplete(response.razorpay_order_id, response.razorpay_payment_id, response.razorpay_signature);
                    },
                    prefill: {
                        name: this.customerName,
                        email: this.customerEmail,
                    },
                    theme: {
                        color: this.currentTier.color || '#0052FF'
                    }
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    alert('Payment could not be completed: ' + response.error.description);
                });
                rzp.open();

            } catch (err) {
                console.error(err);
                alert('Payment error. Please check your internet connection.');
            } finally {
                this.loading = false;
            }
        },

        async verifyAndComplete(orderId, paymentId, signature = '') {
            try {
                const response = await fetch('{{ route("subscription.verify-payment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        razorpay_order_id: orderId,
                        razorpay_payment_id: paymentId,
                        razorpay_signature: signature
                    })
                });

                const res = await response.json();
                if (res.success) {
                    this.paymentSuccess = true;
                    setTimeout(() => {
                        window.location.href = '{{ route("sessions.index") }}';
                    }, 1500);
                } else {
                    alert(res.error || 'Verification failed. Please contact support at infopscranker@gmail.com or +91 9895 204 224');
                }
            } catch (err) {
                console.error(err);
                alert('Verification error. Please contact infopscranker@gmail.com or +91 9895 204 224.');
            }
        }
    };
}
</script>
@endsection
