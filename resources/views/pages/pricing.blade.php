@extends('layouts.app')

@section('title', 'Prepaid Subscription Plans & Pricing — PSCRanker.com')

@section('content')
<div class="py-10 sm:py-16 bg-gradient-to-b from-blue-50/50 via-white to-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Top Hero Badge & Heading -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-[#0052FF] text-xs font-black uppercase tracking-wider mb-4 border border-blue-200">
                <span>⚡ Prepaid Learning Pass</span>
                <span>•</span>
                <span>Progressive Rebates</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight leading-tight">
                Invest in Your Rank. <br class="hidden sm:inline">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#0052FF] via-blue-600 to-indigo-600">
                    Pay Once, Drill Unlimited.
                </span>
            </h1>
            <p class="text-sm sm:text-base text-slate-600 font-medium mt-3 leading-relaxed">
                Zero auto-debit traps. Choose your duration from 1 month to 1 year and enjoy progressive rebates up to 40% off!
            </p>
        </div>

        <!-- ============================================================= -->
        <!-- INTERACTIVE DURATION SELECTOR CARD (Alpine.js Powered) -->
        <!-- ============================================================= -->
        <div 
            x-data="pricingEngine({{ json_encode($tiers) }}, '{{ $razorpayKey }}')"
            class="max-w-2xl mx-auto mb-16"
        >
            <div class="bg-gradient-to-b from-slate-950 via-slate-900 to-blue-950 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border-2 border-yellow-400/80 relative overflow-hidden ring-8 ring-blue-500/10">
                
                <!-- Background Accent Glow -->
                <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#0052FF]/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-56 h-56 bg-yellow-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    
                    <!-- Header with Status Pill -->
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-6 pb-4 border-b border-slate-800">
                        <div>
                            <span class="text-xs font-bold text-yellow-400 uppercase tracking-wider block">Prepaid Plan Configurator</span>
                            <h2 class="text-xl sm:text-2xl font-black text-white">Select Your Prep Duration</h2>
                        </div>
                        <div class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-black uppercase">
                            Instant UPI Activation ⚡
                        </div>
                    </div>

                    <!-- The Interactive Dropdown Selector -->
                    <div class="mb-6">
                        <label for="durationSelect" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Choose Duration Cycle:
                        </label>
                        <div class="relative">
                            <select 
                                id="durationSelect"
                                x-model.number="selectedMonths" 
                                @change="updateSelection()"
                                class="w-full bg-slate-800 text-white font-black text-sm sm:text-base px-4 py-4 rounded-2xl border-2 border-yellow-400 focus:outline-hidden focus:ring-2 focus:ring-yellow-400 cursor-pointer appearance-none shadow-lg transition"
                            >
                                <template x-for="tier in tiers" :key="tier.months">
                                    <option 
                                        :value="tier.months" 
                                        x-text="formatDropdownOption(tier)"
                                    ></option>
                                </template>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-yellow-400 text-lg">
                                ▼
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 px-1">
                            <span>Base Rate: <strong class="text-white">₹<span x-text="currentTier.base_monthly_fee"></span> / month</strong></span>
                            <span x-show="currentTier.rebate_percent > 0" class="text-emerald-400 font-bold">
                                🎉 Progressive Discount Applied!
                            </span>
                        </div>
                    </div>

                    <!-- Visual Calculation Breakdown Card -->
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            <!-- Left: Pricing Breakdown -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-lg font-black text-white" x-text="currentTier.name"></h3>
                                    <span 
                                        x-show="currentTier.rebate_percent > 0" 
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-yellow-400 text-slate-950"
                                        x-text="currentTier.rebate_percent + '% REBATE'"
                                    ></span>
                                </div>
                                <p class="text-xs text-slate-300 mt-0.5" x-text="currentTier.description"></p>
                                
                                <div class="mt-3 flex items-center gap-3">
                                    <span 
                                        x-show="currentTier.discount_amount > 0"
                                        class="text-sm sm:text-base text-slate-400 line-through font-mono"
                                        x-text="'₹' + currentTier.base_total"
                                    ></span>
                                    <span class="text-3xl sm:text-4xl font-black text-yellow-400 font-mono" x-text="'₹' + currentTier.final_price"></span>
                                    <span class="text-xs text-slate-300">Total for <span x-text="currentTier.months"></span> month<span x-show="currentTier.months > 1">s</span></span>
                                </div>
                            </div>

                            <!-- Right: Effective Rate Badge -->
                            <div class="sm:text-right bg-blue-900/40 sm:bg-transparent p-3 sm:p-0 rounded-xl border border-blue-400/20 sm:border-0">
                                <div class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Effective Rate</div>
                                <div class="text-xl sm:text-2xl font-black text-emerald-400 font-mono">
                                    ₹<span x-text="currentTier.effective_per_month"></span> <span class="text-xs font-normal text-slate-300">/ mo</span>
                                </div>
                                <div x-show="currentTier.discount_amount > 0" class="text-xs font-black text-yellow-300 mt-0.5">
                                    You Save ₹<span x-text="currentTier.discount_amount"></span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Customer Info Prompt (If Guest) -->
                    @guest
                        <div class="mb-6 p-4 rounded-2xl bg-blue-950/60 border border-blue-500/30 text-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">Enter Your Details for Instant Activation:</span>
                                <a href="{{ route('login') }}" class="text-yellow-400 hover:underline font-bold">Already registered? Log in</a>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input 
                                    type="text" 
                                    x-model="customerName" 
                                    placeholder="Your Name" 
                                    class="w-full bg-slate-800 px-3.5 py-2.5 rounded-xl text-white placeholder-slate-400 text-xs border border-slate-700 focus:outline-hidden focus:border-yellow-400"
                                >
                                <input 
                                    type="email" 
                                    x-model="customerEmail" 
                                    placeholder="Email Address" 
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
                        class="w-full py-4 px-6 bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-300 hover:to-amber-300 active:scale-98 text-slate-950 font-black text-base sm:text-lg rounded-2xl shadow-xl transition-all flex items-center justify-center gap-2 border-2 border-yellow-300 disabled:opacity-50"
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

        <!-- ============================================================= -->
        <!-- COMPLETE COMPARISON SCHEDULE (All Tiers 1 Month to 1 Year) -->
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

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach($tiers as $tier)
                    <div class="bg-white rounded-3xl p-5 border {{ $tier['is_popular'] ? 'border-2 border-[#0052FF] shadow-lg ring-4 ring-blue-500/10' : ($tier['is_best_value'] ? 'border-2 border-amber-400 shadow-md ring-4 ring-amber-400/10' : 'border-slate-200 shadow-xs') }} flex flex-col justify-between relative">
                        
                        @if($tier['is_popular'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-[#0052FF] text-white text-[10px] font-black uppercase tracking-wider shadow">
                                Most Popular 🔥
                            </div>
                        @elseif($tier['is_best_value'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-black uppercase tracking-wider shadow">
                                Best Value 👑
                            </div>
                        @endif

                        <div>
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                                {{ $tier['months'] }} {{ $tier['months'] === 1 ? 'Month' : 'Months' }}
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
                                    <div class="text-xs text-slate-400 font-mono">Standard Rate</div>
                                @endif
                                <div class="text-3xl font-black text-slate-900 font-mono">
                                    ₹{{ $tier['final_price'] }}
                                </div>
                                <div class="text-[11px] font-bold text-emerald-600 mt-0.5">
                                    Effective: ₹{{ $tier['effective_per_month'] }} / month
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
                    <p class="text-slate-600 leading-relaxed">No. All plans are 100% prepaid. Your card or UPI will never be charged automatically. Once your prepaid cycle completes, access simply expires unless you manually renew.</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200">
                    <h4 class="font-black text-slate-900 mb-1">How fast is account activation after paying via UPI?</h4>
                    <p class="text-slate-600 leading-relaxed">Instant! Our Razorpay integration automatically credits your duration and unlocks all locked units the moment the payment is verified.</p>
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
    return {
        tiers: tiers,
        razorpayKey: razorpayKey,
        selectedMonths: 3, // Default to 3 months (Most Popular)
        currentTier: tiers.find(t => t.months === 3) || tiers[0],
        customerName: '{{ Auth::user()?->name ?? "" }}',
        customerEmail: '{{ Auth::user()?->email ?? "" }}',
        loading: false,
        paymentSuccess: false,

        formatDropdownOption(tier) {
            let label = `${tier.months} Month${tier.months > 1 ? 's' : ''} — ₹${tier.final_price}`;
            if (tier.rebate_percent > 0) {
                label += ` (Save ${tier.rebate_percent}% - ₹${tier.discount_amount} OFF)`;
            }
            if (tier.is_popular) {
                label += ' 🔥 POPULAR';
            } else if (tier.is_best_value) {
                label += ' 👑 BEST VALUE';
            }
            return label;
        },

        updateSelection() {
            const found = this.tiers.find(t => t.months === this.selectedMonths);
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
                        months: this.selectedMonths,
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
                    const confirmMock = confirm(`[Razorpay Test Mode]\n\nPlan: ${data.plan_name}\nPayable: ₹${data.amount_inr}\n\nClick OK to simulate instant UPI payment success & unlock course.`);
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
                        color: '#0052FF'
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
                    alert(res.error || 'Verification failed. Please contact admin@pscranker.com');
                }
            } catch (err) {
                console.error(err);
                alert('Verification error. Please contact support.');
            }
        }
    };
}
</script>
@endsection
