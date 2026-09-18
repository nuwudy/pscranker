@extends('layouts.app')

@section('title', 'Partner Mission Control — PSCRanker Promoter Portal')

@section('content')
<div class="py-8 bg-slate-950 text-slate-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Welcome & Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl relative overflow-hidden mb-8">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-yellow-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            🟢 Partner Status: {{ ucfirst($affiliate->status) }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">ID: {{ $affiliate->affiliate_code }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2">
                        <span>Welcome, {{ Auth::user()->name }}!</span>
                        <span class="text-yellow-400">⚡</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1">
                        Your commission rate is <span class="text-yellow-400 font-black">{{ $affiliate->commission_rate }}%</span> per converted student. Add your prospective students below; commissions are attributed automatically when they join.
                    </p>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <button 
                        type="button" 
                        onclick="openAddLeadModal()"
                        class="px-4 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs rounded-xl shadow transition flex items-center gap-1.5 active:scale-95"
                    >
                        <span>➕ Add Prospect Lead</span>
                    </button>
                    <button 
                        type="button" 
                        onclick="openPayoutModal()"
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-700"
                    >
                        <span>💳 Payout Settings</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <span class="text-[10px] uppercase font-mono">Success</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <span class="text-[10px] uppercase font-mono">Attention</span>
            </div>
        @endif

        @if(empty(data_get($affiliate->payout_details, 'upi_id')) && empty(data_get($affiliate->payout_details, 'account_number')))
            <div class="mb-6 p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/20 to-yellow-500/10 border-2 border-amber-400/40 text-amber-200 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl">
                <div class="flex items-center gap-3">
                    <span class="text-2xl sm:text-3xl">⚠️</span>
                    <div>
                        <span class="font-black text-white text-sm block">Action Required: Add Your Payout Details</span>
                        <span class="text-slate-300 text-xs mt-0.5 block">Please add your UPI ID or Bank Account &amp; IFSC code so admin can disburse your earned commissions on the 1st of every month.</span>
                    </div>
                </div>
                <button 
                    type="button" 
                    onclick="openPayoutModal()" 
                    class="px-4 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs rounded-xl shadow transition shrink-0 active:scale-95 cursor-pointer"
                >
                    Add Payout Details ➔
                </button>
            </div>
        @endif

        <!-- Your Shareable Affiliate Referral Link Card -->
        <div class="bg-gradient-to-r from-yellow-500/10 via-amber-500/10 to-blue-500/10 border-2 border-yellow-500/30 rounded-3xl p-5 sm:p-6 mb-8 shadow-xl relative overflow-hidden backdrop-blur-sm">
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-yellow-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wider bg-yellow-400 text-slate-950 shadow-sm">
                            <span>🔗</span> Your Personal Referral Link
                        </span>
                        <span class="text-xs text-yellow-300 font-bold flex items-center gap-1">
                            <span>⚡</span> {{ $affiliate->commission_rate }}% Commission on Enrollments
                        </span>
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-white">
                        Share your unique link with candidates &amp; students
                    </h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Anyone who visits PSCRanker via your link and signs up or purchases a subscription is automatically attributed to you. You earn a <span class="text-yellow-400 font-black">{{ $affiliate->commission_rate }}% commission</span> directly to your monthly payout!
                    </p>

                    <!-- Link Copy Bar -->
                    <div class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                id="affiliateReferralInput"
                                readonly 
                                value="{{ $affiliate->referral_url }}" 
                                class="w-full bg-slate-950/95 border border-yellow-500/40 text-yellow-300 font-mono text-xs sm:text-sm rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-400 select-all shadow-inner"
                            >
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                onclick="copyAffiliateLink()"
                                id="copyReferralBtn"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer"
                            >
                                <span id="copyIcon">📋</span>
                                <span id="copyText">Copy Link</span>
                            </button>
                            <a 
                                href="https://api.whatsapp.com/send?text={{ rawurlencode('Join PSCRanker with my link and start your Kerala PSC preparation: ' . $affiliate->referral_url) }}" 
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex-1 sm:flex-initial px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5 active:scale-95"
                            >
                                <span>💬</span>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Traffic & Stats Pill -->
                <div class="flex items-center gap-4 bg-slate-950/80 border border-slate-800 rounded-2xl p-4 lg:min-w-[190px] justify-between lg:justify-center lg:flex-col lg:items-start shadow-md">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Link Traffic</span>
                        <div class="text-2xl sm:text-3xl font-black text-white font-mono flex items-baseline gap-1.5 mt-0.5">
                            <span>{{ number_format($affiliate->referral_clicks) }}</span>
                            <span class="text-xs text-yellow-400 font-normal">clicks</span>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400 flex items-center gap-1">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>60-Day Cookie Auto-Tracking</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- 1. Total Leads -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Prospects</div>
                <div class="text-2xl sm:text-3xl font-black text-white font-mono">{{ $totalLeads }}</div>
                <div class="text-[10px] text-slate-500 mt-1">Logged in your pipeline</div>
            </div>

            <!-- 2. Converted Students -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                <div class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Enrolled Students</div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">{{ $convertedCount }}</div>
                <div class="text-[10px] text-slate-500 mt-1">Successfully joined courses</div>
            </div>

            <!-- 3. Pending Payout -->
            <div class="bg-slate-900/90 border border-yellow-500/30 rounded-2xl p-5 relative overflow-hidden">
                <div class="text-[11px] font-bold text-yellow-400 uppercase tracking-wider mb-1">Pending Payout</div>
                <div class="text-2xl sm:text-3xl font-black text-yellow-400 font-mono">₹{{ number_format($pendingPayout, 2) }}</div>
                <div class="text-[10px] text-yellow-300/70 mt-1">Disbursed on 1st of month</div>
            </div>

            <!-- 4. Total Disbursed -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
                <div class="text-[11px] font-bold text-blue-400 uppercase tracking-wider mb-1">Lifetime Received</div>
                <div class="text-2xl sm:text-3xl font-black text-blue-400 font-mono">₹{{ number_format($totalDisbursed, 2) }}</div>
                <div class="text-[10px] text-slate-500 mt-1">Direct to Bank / UPI</div>
            </div>
        </div>

        <!-- Section 1: Prospects Follow-up Pipeline -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-8 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-black text-white flex items-center gap-2">
                        <span>Candidate Follow-up Pipeline</span>
                        <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full font-mono font-normal">
                            {{ $leads->total() }} Candidates
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Students you have pitched. When they sign up or pay with this phone number, you get credited automatically.
                    </p>
                </div>

                <!-- Filters & Search -->
                <form action="{{ route('affiliate.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}" 
                        placeholder="Search name or phone..."
                        class="bg-slate-950 border border-slate-700 text-white text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-yellow-400 transition"
                    >
                    <select 
                        name="status" 
                        onchange="this.form.submit()"
                        class="bg-slate-950 border border-slate-700 text-slate-300 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-yellow-400"
                    >
                        <option value="">All Statuses</option>
                        <option value="lead" {{ request('status') === 'lead' ? 'selected' : '' }}>In Follow-up</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Joined / Converted</option>
                    </select>
                    @if(request('q') || request('status'))
                        <a href="{{ route('affiliate.dashboard') }}" class="text-xs text-slate-400 hover:text-white px-2">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Leads Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Candidate Name</th>
                            <th class="py-3 px-4">Registered Phone</th>
                            <th class="py-3 px-4">Attribution Status</th>
                            <th class="py-3 px-4">Follow-up Notes</th>
                            <th class="py-3 px-4">Added Date</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($leads as $lead)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-4 font-bold text-white">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $lead->candidate_name }}</span>
                                        @if($lead->source === 'referral_link')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30" title="Captured via personal affiliate link">
                                                🔗 Link
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Direct Phone Follow-up lead">
                                                📞 Pitch
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-semibold text-yellow-400">
                                    {{ $lead->candidate_phone }}
                                    @if($lead->alternate_phone)
                                        <span class="block text-[10px] text-slate-500 font-normal">Alt: {{ $lead->alternate_phone }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($lead->status === 'converted')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <span>✅</span> Joined Course
                                        </span>
                                        @if($lead->converted_at)
                                            <span class="block text-[10px] text-slate-500 mt-0.5">{{ $lead->converted_at->format('d M Y') }}</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-500/10 text-yellow-300 border border-yellow-500/20">
                                            <span>🟡</span> Follow-up Active
                                        </span>
                                        @if($lead->valid_until)
                                            <span class="block text-[10px] text-slate-500 mt-0.5">Valid until {{ $lead->valid_until->format('d M Y') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 max-w-xs truncate text-slate-300">
                                    {{ $lead->notes ?: '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                    {{ $lead->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button 
                                        type="button" 
                                        onclick="openEditLeadModal({{ $lead->id }}, '{{ addslashes($lead->candidate_name) }}', '{{ addslashes($lead->notes ?? '') }}')"
                                        class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-[11px] font-bold transition"
                                    >
                                        Edit Notes
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    <span class="text-2xl block mb-2">📞</span>
                                    No prospective students added yet. Click <span class="text-yellow-400 font-bold">"Add Prospect Lead"</span> to register your first candidate!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $leads->links() }}
            </div>
        </div>

        <!-- Section 2: Earnings & Commission History -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-black text-white flex items-center gap-2">
                        <span>Commission Earnings &amp; Monthly Disbursements</span>
                        <span class="text-yellow-400">💰</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Historical record of student conversions, earned commissions, and admin monthly settlements.
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 block mb-0.5">Payout Destination:</span>
                    <div class="text-xs font-mono font-bold text-yellow-400 space-y-0.5">
                        @if(!empty(data_get($affiliate->payout_details, 'upi_id')))
                            <div class="flex items-center justify-end gap-1">
                                <span class="text-slate-400 font-sans text-[11px]">📱 UPI:</span>
                                <span>{{ data_get($affiliate->payout_details, 'upi_id') }}</span>
                            </div>
                        @endif
                        @if(!empty(data_get($affiliate->payout_details, 'account_number')))
                            <div class="flex items-center justify-end gap-1 text-[11px] text-slate-300">
                                <span>🏦 {{ data_get($affiliate->payout_details, 'bank_name', 'Bank') }}: •••• {{ substr(data_get($affiliate->payout_details, 'account_number'), -4) }}</span>
                                <span class="text-yellow-400/80">({{ data_get($affiliate->payout_details, 'ifsc_code') }})</span>
                            </div>
                        @endif
                        @if(empty(data_get($affiliate->payout_details, 'upi_id')) && empty(data_get($affiliate->payout_details, 'account_number')))
                            <button type="button" onclick="openPayoutModal()" class="text-rose-400 hover:underline italic font-sans font-normal">Not configured (Click to set)</button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Period</th>
                            <th class="py-3 px-4">Student</th>
                            <th class="py-3 px-4">Course Amount</th>
                            <th class="py-3 px-4">Commission %</th>
                            <th class="py-3 px-4">Commission</th>
                            <th class="py-3 px-4">Bonus</th>
                            <th class="py-3 px-4">Total Payout</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($commissions as $comm)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    {{ $comm->period_month }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-white">
                                    {{ $comm->student?->name ?? ($comm->lead?->candidate_name ?? 'Bonus Credit') }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    ₹{{ number_format($comm->course_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-300">
                                    {{ $comm->commission_rate }}%
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-emerald-400">
                                    ₹{{ number_format($comm->commission_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-yellow-400">
                                    {{ $comm->bonus_amount > 0 ? '+₹' . number_format($comm->bonus_amount, 2) : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-black text-white text-sm">
                                    ₹{{ number_format($comm->total_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($comm->status === 'disbursed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            <span>✅</span> Disbursed
                                        </span>
                                        @if($comm->payout_reference)
                                            <span class="block text-[10px] text-slate-500 font-mono mt-0.5">Ref: {{ $comm->payout_reference }}</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            <span>⏳</span> Pending 1st of Month
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-500">
                                    No commission transactions recorded yet. When a candidate from your list enrols, your earnings will appear here!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL 1: ADD PROSPECT LEAD                                    -->
<!-- ============================================================= -->
<div id="addLeadModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeAddLeadModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-xl font-black text-white mb-1 flex items-center gap-2">
            <span>Add Prospect Lead</span>
            <span class="text-yellow-400">📞</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">
            Enter the student's details. Our system matches this phone number when they enroll to credit your commission.
        </p>

        <form action="{{ route('affiliate.leads.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Candidate Name <span class="text-rose-400">*</span></label>
                <input 
                    type="text" 
                    name="candidate_name" 
                    required 
                    placeholder="e.g. Santhosh Kumar"
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">
                    <span>10-Digit Mobile Number</span> <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="tel" 
                    name="candidate_phone" 
                    required 
                    maxlength="10"
                    placeholder="e.g. 9123456789"
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                >
                <p class="text-[10px] text-slate-500 mt-1">Digits only. Do not add +91 or leading zeros.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Alternate Phone (Optional)</label>
                <input 
                    type="tel" 
                    name="alternate_phone" 
                    maxlength="10"
                    placeholder="Backup mobile number"
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Call / Follow-up Notes</label>
                <textarea 
                    name="notes" 
                    rows="3" 
                    placeholder="e.g. Called on 17th Sep, preparing for LDC exam. Promised to join upcoming batch on weekend."
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeAddLeadModal()"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 text-xs font-black rounded-xl transition shadow"
                >
                    Save Prospect
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL 2: EDIT LEAD NOTES                                      -->
<!-- ============================================================= -->
<div id="editLeadModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeEditLeadModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-xl font-black text-white mb-1">Update Follow-up Notes</h3>
        <p class="text-xs text-slate-400 mb-5">Keep track of your conversations and reminder promises.</p>

        <form id="editLeadForm" action="" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Candidate Name</label>
                <input 
                    type="text" 
                    id="edit_candidate_name" 
                    name="candidate_name" 
                    required 
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Follow-up Notes</label>
                <textarea 
                    id="edit_notes" 
                    name="notes" 
                    rows="4" 
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-yellow-400 transition"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeEditLeadModal()"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 text-xs font-black rounded-xl transition shadow"
                >
                    Update Notes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL 3: PAYOUT SETTINGS (UPI / BANK)                         -->
<!-- ============================================================= -->
<div id="payoutModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closePayoutModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-white text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-xl font-black text-white mb-1 flex items-center gap-2">
            <span>Payout Destination Settings</span>
            <span class="text-yellow-400">💳</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">
            Where should the Admin send your accumulated commissions on the 1st of every month?
        </p>

        <form action="{{ route('affiliate.payout.update') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Payout Method Selector -->
            <div class="grid grid-cols-2 gap-3 mb-2">
                <label class="cursor-pointer border border-slate-700 rounded-xl p-3 flex items-center gap-2 hover:border-yellow-400 transition">
                    <input 
                        type="radio" 
                        name="payout_method" 
                        value="upi" 
                        {{ ($affiliate->payout_method ?? 'upi') === 'upi' ? 'checked' : '' }}
                        class="accent-yellow-400"
                    >
                    <div>
                        <div class="text-xs font-bold text-white">UPI Transfer</div>
                        <div class="text-[10px] text-slate-400">GPay, PhonePe, Paytm</div>
                    </div>
                </label>

                <label class="cursor-pointer border border-slate-700 rounded-xl p-3 flex items-center gap-2 hover:border-yellow-400 transition">
                    <input 
                        type="radio" 
                        name="payout_method" 
                        value="bank_transfer" 
                        {{ ($affiliate->payout_method ?? '') === 'bank_transfer' ? 'checked' : '' }}
                        class="accent-yellow-400"
                    >
                    <div>
                        <div class="text-xs font-bold text-white">Direct Bank NEFT/IMPS</div>
                        <div class="text-[10px] text-slate-400">Account + IFSC</div>
                    </div>
                </label>
            </div>

            <!-- UPI ID Field -->
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Your UPI ID (VPA)</label>
                <input 
                    type="text" 
                    name="upi_id" 
                    value="{{ data_get($affiliate->payout_details, 'upi_id', '') }}" 
                    placeholder="e.g. 9895000000@okaxis or anu@paytm"
                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono transition"
                >
            </div>

            <div class="border-t border-slate-800 pt-3">
                <span class="text-xs font-bold text-slate-400 block mb-2">Or Bank Account Details:</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Bank Name</label>
                        <input 
                            type="text" 
                            name="bank_name" 
                            value="{{ data_get($affiliate->payout_details, 'bank_name', '') }}" 
                            placeholder="e.g. Federal Bank / SBI"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Account Holder Name</label>
                        <input 
                            type="text" 
                            name="account_holder" 
                            value="{{ data_get($affiliate->payout_details, 'account_holder', '') }}" 
                            placeholder="As per passbook"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Account Number</label>
                        <input 
                            type="text" 
                            name="account_number" 
                            value="{{ data_get($affiliate->payout_details, 'account_number', '') }}" 
                            placeholder="Bank Account Number"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">IFSC Code</label>
                        <input 
                            type="text" 
                            name="ifsc_code" 
                            value="{{ data_get($affiliate->payout_details, 'ifsc_code', '') }}" 
                            placeholder="e.g. FDRL0001234"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-yellow-400 font-mono uppercase"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closePayoutModal()"
                    class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-300 text-slate-950 text-xs font-black rounded-xl transition shadow"
                >
                    Save Payout Details
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddLeadModal() {
        document.getElementById('addLeadModal').classList.remove('hidden');
    }
    function closeAddLeadModal() {
        document.getElementById('addLeadModal').classList.add('hidden');
    }

    function openEditLeadModal(id, name, notes) {
        document.getElementById('editLeadForm').action = '/affiliate/leads/' + id + '/update';
        document.getElementById('edit_candidate_name').value = name;
        document.getElementById('edit_notes').value = notes;
        document.getElementById('editLeadModal').classList.remove('hidden');
    }
    function closeEditLeadModal() {
        document.getElementById('editLeadModal').classList.add('hidden');
    }

    function copyAffiliateLink() {
        const input = document.getElementById('affiliateReferralInput');
        const copyText = document.getElementById('copyText');
        const copyIcon = document.getElementById('copyIcon');
        const btn = document.getElementById('copyReferralBtn');
        
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);

        const textToCopy = input.value;

        function setCopiedState() {
            if (copyText) copyText.textContent = 'Copied! ✅';
            if (copyIcon) copyIcon.textContent = '✓';
            if (btn) {
                btn.classList.remove('bg-yellow-400', 'hover:bg-yellow-300');
                btn.classList.add('bg-emerald-400', 'hover:bg-emerald-300');
            }
            setTimeout(() => {
                if (copyText) copyText.textContent = 'Copy Link';
                if (copyIcon) copyIcon.textContent = '📋';
                if (btn) {
                    btn.classList.remove('bg-emerald-400', 'hover:bg-emerald-300');
                    btn.classList.add('bg-yellow-400', 'hover:bg-yellow-300');
                }
            }, 2500);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(setCopiedState).catch(() => {
                try {
                    document.execCommand('copy');
                    setCopiedState();
                } catch (e) {
                    alert('Please manually copy this link: ' + textToCopy);
                }
            });
        } else {
            try {
                document.execCommand('copy');
                setCopiedState();
            } catch (e) {
                alert('Please manually copy this link: ' + textToCopy);
            }
        }
    }
</script>
@endsection
