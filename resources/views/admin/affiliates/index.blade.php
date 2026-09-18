@extends('layouts.app')

@section('title', 'Affiliate Promoters & Monthly Commission Hub — Admin Mission Control')

@section('content')
<div class="py-8 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Mission Control Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-[#0052FF]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-400/20 text-yellow-300 border border-yellow-400/30">
                        🤝 Zero-Coupon Tele-Promoter Engine
                    </span>
                    <span class="text-xs text-slate-400 font-bold">Kerala PSC Mission Control</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Affiliate Promoters &amp; Settlements</span>
                    <span class="text-yellow-400 text-2xl">⚡</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1">
                    Manage field promoters, track phone-attributed candidate enrollments, and disburse monthly commissions on the 1st of each month.
                </p>
            </div>

            <!-- Quick Navigation Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 relative z-10">
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-white/20"
                >
                    <span>← Dashboard</span>
                </a>
                <a 
                    href="{{ route('admin.users.index') }}" 
                    class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-white/20"
                >
                    <span>👥 Candidates</span>
                </a>
                <a 
                    href="{{ route('affiliate.join') }}" 
                    target="_blank"
                    class="px-4 py-2.5 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 font-black text-xs rounded-xl shadow transition flex items-center gap-1.5 border border-yellow-300 active:scale-95"
                >
                    <span>Partner Join Form ↗</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-sm">
                <span class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </span>
                <span class="text-[10px] text-emerald-700 uppercase tracking-wider font-mono">Completed</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-300 text-blue-900 text-xs font-bold flex items-center justify-between shadow-sm">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- 5 Summary Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Promoters</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $totalAffiliates }}</span>
                <span class="text-[10px] text-emerald-600 block mt-1 font-semibold">{{ $activeAffiliates }} Active</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Prospect Leads</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">{{ $totalLeads }}</span>
                <span class="text-[10px] text-slate-400 block mt-1">Logged by promoters</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Joined Students</span>
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-mono">{{ $totalConversions }}</span>
                <span class="text-[10px] text-emerald-700 block mt-1 font-semibold">Attributed Conversions</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-amber-200 bg-amber-50/50 shadow-sm">
                <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block mb-1">Pending Payout</span>
                <span class="text-2xl sm:text-3xl font-black text-amber-600 font-mono">₹{{ number_format($totalPending, 2) }}</span>
                <span class="text-[10px] text-amber-700 block mt-1 font-semibold">To Disburse on 1st</span>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block mb-1">Total Disbursed</span>
                <span class="text-2xl sm:text-3xl font-black text-blue-600 font-mono">₹{{ number_format($totalDisbursed, 2) }}</span>
                <span class="text-[10px] text-slate-400 block mt-1">Lifetime settlements</span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 mb-6 pb-2">
            <a 
                href="{{ route('admin.affiliates.index', ['tab' => 'affiliates']) }}" 
                class="px-4 py-2 rounded-xl text-xs font-black transition {{ $tab === 'affiliates' ? 'bg-slate-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
            >
                👥 Promoters Directory
            </a>
            <a 
                href="{{ route('admin.affiliates.index', ['tab' => 'slabs']) }}" 
                class="px-4 py-2 rounded-xl text-xs font-black transition {{ $tab === 'slabs' ? 'bg-slate-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
            >
                🎯 Target vs Payout Slabs
            </a>
            <a 
                href="{{ route('admin.affiliates.index', ['tab' => 'disbursements', 'month' => $selectedMonth]) }}" 
                class="px-4 py-2 rounded-xl text-xs font-black transition {{ $tab === 'disbursements' ? 'bg-slate-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
            >
                💰 Monthly Disbursement Hub (1st of Month)
            </a>
            <a 
                href="{{ route('admin.affiliates.index', ['tab' => 'leads']) }}" 
                class="px-4 py-2 rounded-xl text-xs font-black transition {{ $tab === 'leads' ? 'bg-slate-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
            >
                📞 Global Candidate Leads Log
            </a>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 1: PROMOTERS DIRECTORY                                    -->
        <!-- ============================================================= -->
        @if($tab === 'affiliates')
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Promoters &amp; Affiliates Directory</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Manage promoter commission rates, assign performance bonuses, and view payout channels.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Platform Default Commission Rate Widget -->
                        <form action="{{ route('admin.affiliates.default-rate') }}" method="POST" class="flex items-center gap-2 bg-amber-50/80 border border-amber-300 rounded-xl px-3 py-1.5 shadow-2xs">
                            @csrf
                            <span class="text-[11px] font-bold text-amber-900 flex items-center gap-1">
                                <span>⚡</span> Default Rate:
                            </span>
                            <div class="flex items-center gap-1">
                                <input 
                                    type="number" 
                                    name="default_commission_rate" 
                                    value="{{ $defaultCommissionRate }}" 
                                    step="0.5" 
                                    min="0" 
                                    max="100"
                                    class="w-14 bg-white border border-amber-300 text-amber-950 font-mono font-black text-xs rounded-lg px-2 py-1 text-center focus:outline-none focus:ring-1 focus:ring-amber-500"
                                >
                                <span class="text-xs font-bold text-amber-800 font-mono">%</span>
                            </div>
                            <button 
                                type="submit" 
                                class="px-2.5 py-1 bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-[10px] rounded-lg shadow-2xs transition active:scale-95 cursor-pointer"
                                title="Update default commission rate for all new promoters"
                            >
                                Save Default
                            </button>
                        </form>

                        <!-- Search Form -->
                        <form action="{{ route('admin.affiliates.index') }}" method="GET" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="affiliates">
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}" 
                                placeholder="Search name, phone, code..."
                                class="bg-slate-50 border border-slate-300 text-slate-900 text-xs rounded-xl px-3.5 py-2 focus:outline-none focus:border-blue-600 transition"
                            >
                            <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition cursor-pointer">
                                Search
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                                <th class="py-3 px-4">Promoter</th>
                                <th class="py-3 px-4">Mobile &amp; Email</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Commission %</th>
                                <th class="py-3 px-4">Leads / Enrolled</th>
                                <th class="py-3 px-4">Pending Payout</th>
                                <th class="py-3 px-4">Payout Method (UPI/Bank)</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($affiliates as $affiliate)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs">
                                                {{ strtoupper(substr($affiliate->user->name ?? 'A', 0, 1)) }}
                                            </span>
                                            <div>
                                                <span class="block">{{ $affiliate->user->name ?? 'Unknown' }}</span>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded" title="Affiliate Code">🔗 {{ $affiliate->affiliate_code }}</span>
                                                    <span class="text-[10px] text-indigo-600 font-mono font-bold" title="Referral Link Clicks">👆 {{ number_format($affiliate->referral_clicks) }} clicks</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-700">
                                        <span class="block font-bold">{{ $affiliate->user->phone ?? '—' }}</span>
                                        <span class="text-[10px] text-slate-400 font-sans">{{ $affiliate->user->email ?? '—' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($affiliate->status === 'active')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                Active
                                            </span>
                                        @elseif($affiliate->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                                Pending Review
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800">
                                                Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                        <button 
                                            type="button" 
                                            onclick="openCommissionModal({{ $affiliate->id }}, '{{ addslashes($affiliate->user->name) }}', {{ $affiliate->commission_rate }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-900 border border-yellow-300 rounded-lg text-xs font-mono font-bold transition shadow-2xs group cursor-pointer"
                                            title="Click to change commission percentage"
                                        >
                                            <span>{{ $affiliate->commission_rate }}%</span>
                                            <span class="text-[10px] text-yellow-700 opacity-60 group-hover:opacity-100 transition">✏️</span>
                                        </button>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-800">{{ $affiliate->leads_count }} leads</span>
                                        <span class="block text-[10px] text-emerald-600 font-semibold">{{ $affiliate->convertedLeadsCount() }} joined</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-black text-amber-600 text-sm">
                                        ₹{{ number_format($affiliate->pendingPayout(), 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700">
                                        <div class="space-y-1">
                                            @if(!empty(data_get($affiliate->payout_details, 'upi_id')))
                                                <div class="font-mono text-xs font-bold text-blue-700 flex items-center gap-1">
                                                    <span>📱</span> <span>{{ data_get($affiliate->payout_details, 'upi_id') }}</span>
                                                </div>
                                            @endif
                                            @if(!empty(data_get($affiliate->payout_details, 'account_number')))
                                                <div class="text-[11px] bg-slate-100 p-2 rounded-xl border border-slate-200">
                                                    <div class="font-bold text-slate-800">{{ data_get($affiliate->payout_details, 'bank_name', 'Bank') }}</div>
                                                    <div class="font-mono font-bold text-slate-900">A/c: {{ data_get($affiliate->payout_details, 'account_number') }}</div>
                                                    <div class="font-mono text-[10px] text-slate-500">IFSC: <span class="font-bold uppercase text-slate-700">{{ data_get($affiliate->payout_details, 'ifsc_code') }}</span></div>
                                                    @if(!empty(data_get($affiliate->payout_details, 'account_holder')))
                                                        <div class="text-[10px] text-slate-500 truncate">Holder: {{ data_get($affiliate->payout_details, 'account_holder') }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                            @if(empty(data_get($affiliate->payout_details, 'upi_id')) && empty(data_get($affiliate->payout_details, 'account_number')))
                                                <span class="text-rose-500 italic text-[11px] font-semibold">⚠️ Not provided</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Edit Payout Info Button -->
                                            <button 
                                                type="button" 
                                                onclick="openAdminPayoutModal({{ $affiliate->id }}, '{{ addslashes($affiliate->user->name) }}', '{{ addslashes(data_get($affiliate->payout_details, 'upi_id', '')) }}', '{{ addslashes(data_get($affiliate->payout_details, 'bank_name', '')) }}', '{{ addslashes(data_get($affiliate->payout_details, 'account_holder', '')) }}', '{{ addslashes(data_get($affiliate->payout_details, 'account_number', '')) }}', '{{ addslashes(data_get($affiliate->payout_details, 'ifsc_code', '')) }}')"
                                                class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-800 rounded-lg text-[11px] font-bold transition flex items-center gap-1"
                                                title="Edit UPI / Bank Account / IFSC"
                                            >
                                                <span>💳</span> Payout
                                            </button>

                                            <!-- Edit Commission Rate Button -->
                                            <button 
                                                type="button" 
                                                onclick="openCommissionModal({{ $affiliate->id }}, '{{ addslashes($affiliate->user->name) }}', {{ $affiliate->commission_rate }})"
                                                class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer"
                                                title="Edit Commission %"
                                            >
                                                <span>⚡</span> Commission %
                                            </button>

                                            <!-- Add Bonus Button -->
                                            <button 
                                                type="button" 
                                                onclick="openBonusModal({{ $affiliate->id }}, '{{ addslashes($affiliate->user->name) }}')"
                                                class="px-2.5 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-900 rounded-lg text-[11px] font-bold transition"
                                                title="Credit Bonus"
                                            >
                                                + Bonus
                                            </button>

                                            <!-- Toggle Status -->
                                            <form action="{{ route('admin.affiliates.status', $affiliate) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $affiliate->status === 'active' ? 'suspended' : 'active' }}">
                                                <button 
                                                    type="submit" 
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ $affiliate->status === 'active' ? 'bg-rose-50 hover:bg-rose-100 text-rose-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}"
                                                    onclick="return confirm('Change status for {{ $affiliate->user->name }}?')"
                                                >
                                                    {{ $affiliate->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-500">
                                        No affiliate promoters registered yet. Promoters can sign up via the <a href="{{ route('affiliate.join') }}" target="_blank" class="text-blue-600 underline font-bold">Partner Onboarding Page</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $affiliates->links() }}
                </div>
            </div>
        @endif

        <!-- ============================================================= -->
        <!-- TAB 2: MONTHLY DISBURSEMENT HUB (1ST OF THE MONTH)            -->
        <!-- ============================================================= -->
        @if($tab === 'disbursements')
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>Monthly Commission Disbursement Hub</span>
                            <span class="text-yellow-500">💰</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Batch aggregates for the 1st of each month. Review total earnings per promoter and mark them as disbursed with Bank UTR / UPI reference.
                        </p>
                    </div>

                    <!-- Month Selection Dropdown -->
                    <form action="{{ route('admin.affiliates.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="disbursements">
                        <label class="text-xs font-bold text-slate-600">Select Month:</label>
                        <select 
                            name="month" 
                            onchange="this.form.submit()" 
                            class="bg-slate-50 border border-slate-300 text-slate-900 font-bold text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-blue-600"
                        >
                            @foreach($availableMonths as $mKey => $mLabel)
                                <option value="{{ $mKey }}" {{ $selectedMonth === $mKey ? 'selected' : '' }}>
                                    {{ $mLabel }} ({{ $mKey }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                                <th class="py-3 px-4">Promoter</th>
                                <th class="py-3 px-4">Payout Destination</th>
                                <th class="py-3 px-4">Sales Count</th>
                                <th class="py-3 px-4">Course Volume</th>
                                <th class="py-3 px-4">Commission</th>
                                <th class="py-3 px-4">Bonus</th>
                                <th class="py-3 px-4">Total Payable</th>
                                <th class="py-3 px-4">Settlement Status</th>
                                <th class="py-3 px-4 text-right">Disbursement Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($monthlyDisbursements as $item)
                                @php
                                    $aff = $item->affiliate;
                                    $isFullyDisbursed = ($item->pending_amount == 0 && $item->disbursed_amount > 0);
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <span class="block text-sm">{{ $aff->user->name ?? 'Unknown' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $aff->user->phone ?? '' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-700">
                                        <div class="space-y-1">
                                            @if(!empty(data_get($aff->payout_details, 'upi_id')))
                                                <div class="text-blue-700 font-bold flex items-center gap-1">
                                                    <span>📱</span> <span>{{ data_get($aff->payout_details, 'upi_id') }}</span>
                                                </div>
                                            @endif
                                            @if(!empty(data_get($aff->payout_details, 'account_number')))
                                                <div class="text-[11px]">
                                                    <span class="font-bold text-slate-800">{{ data_get($aff->payout_details, 'bank_name', 'Bank') }}:</span>
                                                    <span class="text-slate-900 font-bold">A/c {{ data_get($aff->payout_details, 'account_number') }}</span>
                                                    <div class="text-[10px] text-slate-500 font-sans">
                                                        IFSC: <span class="font-mono font-bold text-slate-800">{{ data_get($aff->payout_details, 'ifsc_code') }}</span>
                                                        @if(!empty(data_get($aff->payout_details, 'account_holder')))
                                                            • {{ data_get($aff->payout_details, 'account_holder') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            @if(empty(data_get($aff->payout_details, 'upi_id')) && empty(data_get($aff->payout_details, 'account_number')))
                                                <span class="text-rose-500 italic text-[11px] font-semibold">⚠️ No payout details provided</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                        {{ $item->total_sales_count }} students
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-slate-700">
                                        ₹{{ number_format($item->total_sales_volume, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">
                                        ₹{{ number_format($item->total_commission, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-yellow-600">
                                        {{ $item->total_bonus > 0 ? '+₹' . number_format($item->total_bonus, 2) : '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-black text-slate-900 text-sm">
                                        ₹{{ number_format($item->total_payable, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($isFullyDisbursed)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                ✅ Fully Disbursed
                                            </span>
                                        @elseif($item->pending_amount > 0)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                                ⏳ Pending (₹{{ number_format($item->pending_amount, 2) }})
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">Nil</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        @if($item->pending_amount > 0)
                                            <button 
                                                type="button" 
                                                onclick="openDisburseModal({{ $aff->id }}, '{{ addslashes($aff->user->name) }}', '{{ $selectedMonth }}', {{ $item->pending_amount }}, '{{ addslashes(data_get($aff->payout_details, 'upi_id', '')) }}', '{{ addslashes(data_get($aff->payout_details, 'bank_name', '')) }}', '{{ addslashes(data_get($aff->payout_details, 'account_holder', '')) }}', '{{ addslashes(data_get($aff->payout_details, 'account_number', '')) }}', '{{ addslashes(data_get($aff->payout_details, 'ifsc_code', '')) }}')"
                                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs rounded-xl shadow transition active:scale-95 flex items-center gap-1 ml-auto"
                                            >
                                                <span>Disburse ₹{{ number_format($item->pending_amount, 2) }}</span>
                                                <span>➔</span>
                                            </button>
                                        @else
                                            <span class="text-[11px] text-slate-400 font-bold">Settled</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-500">
                                        No commissions or bonus transactions found for {{ $selectedMonth }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ============================================================= -->
        <!-- TAB 3: GLOBAL CANDIDATE LEADS LOG                             -->
        <!-- ============================================================= -->
        @if($tab === 'leads')
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Global Candidate Leads Pipeline</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Search any candidate name or phone number to see who introduced them to PSCRanker.</p>
                    </div>

                    <!-- Search Form -->
                    <form action="{{ route('admin.affiliates.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="leads">
                        <input 
                            type="text" 
                            name="lead_q" 
                            value="{{ request('lead_q') }}" 
                            placeholder="Search candidate name or phone..."
                            class="bg-slate-50 border border-slate-300 text-slate-900 text-xs rounded-xl px-3.5 py-2 focus:outline-none focus:border-blue-600 transition"
                        >
                        <select 
                            name="lead_status" 
                            onchange="this.form.submit()" 
                            class="bg-slate-50 border border-slate-300 text-slate-700 text-xs rounded-xl px-3 py-2"
                        >
                            <option value="">All Statuses</option>
                            <option value="lead" {{ request('lead_status') === 'lead' ? 'selected' : '' }}>In Follow-up</option>
                            <option value="converted" {{ request('lead_status') === 'converted' ? 'selected' : '' }}>Joined Course</option>
                        </select>
                        <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                                <th class="py-3 px-4">Candidate Name</th>
                                <th class="py-3 px-4">Phone Number</th>
                                <th class="py-3 px-4">Promoter</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Promoter Follow-up Notes</th>
                                <th class="py-3 px-4">Registered Date</th>
                                <th class="py-3 px-4">Enrolled User Account</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($leads as $lead)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $lead->candidate_name }}</span>
                                            @if($lead->source === 'referral_link')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" title="Source: Affiliate Referral Link">
                                                    🔗 Link
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="Source: Direct Phone Follow-up">
                                                    📞 Phone
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">
                                        {{ $lead->candidate_phone }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-800 block">{{ $lead->affiliate->user->name ?? 'Unknown' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $lead->affiliate->affiliate_code }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($lead->status === 'converted')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                ✅ Joined Course
                                            </span>
                                            @if($lead->converted_at)
                                                <span class="block text-[10px] text-slate-400 mt-0.5 font-mono">{{ $lead->converted_at->format('d M Y') }}</span>
                                            @endif
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800">
                                                🟡 In Follow-up
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                        {{ $lead->notes ?: '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ $lead->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($lead->convertedUser)
                                            <span class="font-bold text-slate-900 block">{{ $lead->convertedUser->name }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $lead->convertedUser->email }}</span>
                                        @else
                                            <span class="text-slate-400 italic">Not joined yet</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-500">
                                        No leads recorded in the system matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $leads->links() }}
                </div>
            </div>
        @endif

        <!-- ============================================================= -->
        <!-- TAB 4: TARGET VS PAYOUT SLABS CONFIGURATION (EDITABLE)         -->
        <!-- ============================================================= -->
        @if($tab === 'slabs')
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8">
                <!-- Top Header & Recalculate Action -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-yellow-100 text-yellow-800 border border-yellow-300">
                                📊 Progressive Commission Engine
                            </span>
                            <span class="text-xs text-slate-400 font-mono">Formula: Sales × (Basic % + Bonus %)</span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-slate-900">Target vs Payout Slabs Configuration</h2>
                        <p class="text-xs text-slate-500 mt-1 max-w-2xl">
                            Configure the <strong>Basic Payout %</strong> and <strong>Bonus %</strong> for each monthly sales volume bracket. Slabs apply retroactively to all sales within that calendar month. Saving updates automatically resyncs pending commissions.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <form action="{{ route('admin.affiliates.slabs.recalculate') }}" method="POST" class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-2xl p-2">
                            @csrf
                            <input 
                                type="month" 
                                name="period_month" 
                                value="{{ now()->format('Y-m') }}" 
                                class="bg-white border border-slate-300 text-slate-900 font-mono text-xs rounded-xl px-3 py-1.5 focus:outline-none focus:border-blue-600"
                            >
                            <button 
                                type="submit" 
                                class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs rounded-xl shadow-sm transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                                title="Recalculate monthly commission amounts for all promoters based on active slabs"
                            >
                                <span>🔄 Recalculate Month</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Spreadsheet Example Card Callout -->
                <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 text-slate-800 text-xs">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">💡</span>
                            <div>
                                <strong class="text-slate-900 font-bold block">Spreadsheet Payout Logic:</strong>
                                <span class="text-slate-600">
                                    Example: Sales Value <strong class="font-mono text-slate-900">₹32,456</strong> falls in Slab 4 (<strong class="font-mono text-slate-900">₹30,001 - ₹40,000</strong>) with Basic Payout 10% + Bonus 9% = <strong class="font-mono text-emerald-700 font-black">19% Total</strong>.
                                    The promoter earns <strong class="font-mono text-emerald-800 font-black">₹6,166.64</strong> (<span class="font-mono">₹3,245.60 Base + ₹2,921.04 Bonus</span>).
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable Form for Slabs -->
                <form action="{{ route('admin.affiliates.slabs.update') }}" method="POST">
                    @csrf

                    <div class="overflow-x-auto rounded-2xl border border-slate-200 mb-6">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-bold text-[10px]">
                                    <th class="py-3 px-4">Slab Code &amp; Tier</th>
                                    <th class="py-3 px-4">Min Target (₹)</th>
                                    <th class="py-3 px-4">Max Target (₹)</th>
                                    <th class="py-3 px-4 text-center">Basic Payout %</th>
                                    <th class="py-3 px-4 text-center">Bonus %</th>
                                    <th class="py-3 px-4 text-center">Total Payout %</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($slabs as $index => $slab)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3.5 px-4">
                                            <input type="hidden" name="slabs[{{ $index }}][id]" value="{{ $slab->id }}">
                                            <div class="font-mono font-black text-slate-900">{{ $slab->slab_code }}</div>
                                            <span class="text-[10px] text-slate-500">{{ $slab->slab_name }}</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">₹</span>
                                                <input 
                                                    type="number" 
                                                    step="1" 
                                                    min="0"
                                                    name="slabs[{{ $index }}][min_target]" 
                                                    value="{{ (int) $slab->min_target }}" 
                                                    required 
                                                    class="w-28 bg-white border border-slate-300 font-mono text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                                >
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-1">
                                                <span class="text-slate-400 font-mono">₹</span>
                                                <input 
                                                    type="number" 
                                                    step="1" 
                                                    min="0"
                                                    name="slabs[{{ $index }}][max_target]" 
                                                    value="{{ $slab->max_target ? (int) $slab->max_target : '' }}" 
                                                    placeholder="No upper limit"
                                                    class="w-28 bg-white border border-slate-300 font-mono text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                                >
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="inline-flex items-center gap-1">
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    max="100"
                                                    name="slabs[{{ $index }}][basic_payout_percentage]" 
                                                    value="{{ $slab->basic_payout_percentage }}" 
                                                    required 
                                                    class="w-20 text-center bg-white border border-slate-300 font-mono font-bold text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                                >
                                                <span class="font-mono text-slate-500 font-bold">%</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="inline-flex items-center gap-1">
                                                <input 
                                                    type="number" 
                                                    step="0.01" 
                                                    min="0" 
                                                    max="100"
                                                    name="slabs[{{ $index }}][bonus_percentage]" 
                                                    value="{{ $slab->bonus_percentage }}" 
                                                    required 
                                                    class="w-20 text-center bg-emerald-50 border border-emerald-300 text-emerald-800 font-mono font-black text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                                >
                                                <span class="font-mono text-emerald-700 font-bold">%</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono font-black text-slate-900">
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 text-xs">
                                                {{ rtrim(rtrim(number_format($slab->total_payout_percentage, 2), '0'), '.') }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-500">
                                            No target vs payout slabs configured yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Add New Tier Section -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 mb-6">
                        <h3 class="text-xs font-black uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                            <span>➕</span> Add Next Higher Slab Tier (Optional)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Min Target (₹)</label>
                                <input 
                                    type="number" 
                                    step="1" 
                                    min="0"
                                    name="new_slab[min_target]" 
                                    placeholder="e.g. 200001" 
                                    class="w-full bg-white border border-slate-300 font-mono text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                >
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Max Target (₹, empty for open end)</label>
                                <input 
                                    type="number" 
                                    step="1" 
                                    min="0"
                                    name="new_slab[max_target]" 
                                    placeholder="Leave empty for unlimited" 
                                    class="w-full bg-white border border-slate-300 font-mono text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                >
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Basic Payout %</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100"
                                    name="new_slab[basic_payout_percentage]" 
                                    placeholder="e.g. 10.00" 
                                    class="w-full bg-white border border-slate-300 font-mono text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                >
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Bonus %</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="100"
                                    name="new_slab[bonus_percentage]" 
                                    placeholder="e.g. 25.00" 
                                    class="w-full bg-white border border-slate-300 font-mono text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-600"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Save Slabs Button -->
                    <div class="flex items-center justify-end gap-3">
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl shadow-md transition active:scale-95 cursor-pointer flex items-center gap-2"
                        >
                            <span>💾 Save Target vs Payout Slabs</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL: EXECUTE MONTHLY DISBURSEMENT                           -->
<!-- ============================================================= -->
<div id="disburseModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeDisburseModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-1 flex items-center gap-2">
            <span>Disburse Monthly Commission</span>
            <span class="text-emerald-500">💸</span>
        </h3>
        <p class="text-xs text-slate-500 mb-6">
            Record the bank transfer / UPI transaction reference once you have transferred the funds.
        </p>

        <form id="disburseForm" action="" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" id="disburse_period_month" name="period_month" value="">

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <div class="text-xs text-slate-500">Promoter:</div>
                <div id="disburse_promoter_name" class="text-sm font-black text-slate-900 mb-2"></div>
                
                <div class="text-xs text-slate-500">Payout Amount:</div>
                <div id="disburse_amount_display" class="text-2xl font-black text-emerald-600 font-mono"></div>
            </div>

            <!-- Payout Target Details (UPI / Bank Account & IFSC) -->
            <div class="p-3.5 bg-blue-50/80 border border-blue-200 rounded-2xl">
                <div class="text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>Payment Destination:</span>
                    <span id="disburse_target_badge" class="text-[10px] px-2 py-0.5 rounded-md bg-blue-200 text-blue-900 font-bold">Transfer To</span>
                </div>

                <!-- UPI Block -->
                <div id="disburse_upi_section" class="mb-2 hidden">
                    <span class="text-[10px] text-slate-500 font-bold uppercase">UPI ID:</span>
                    <div class="flex items-center justify-between bg-white px-3 py-1.5 rounded-xl border border-blue-200 mt-0.5">
                        <span id="disburse_upi_val" class="font-mono font-bold text-blue-800 text-xs select-all"></span>
                        <button type="button" onclick="copyTextValue(document.getElementById('disburse_upi_val').innerText, this)" class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200 transition">Copy</button>
                    </div>
                </div>

                <!-- Bank Account Block -->
                <div id="disburse_bank_section" class="space-y-1.5 hidden">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 text-[11px]">Bank:</span>
                        <span id="disburse_bank_val" class="font-bold text-slate-900"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 text-[11px]">A/c Holder:</span>
                        <span id="disburse_holder_val" class="font-semibold text-slate-800"></span>
                    </div>
                    <div class="flex items-center justify-between bg-white px-3 py-1.5 rounded-xl border border-blue-200">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase block font-bold">Account Number</span>
                            <span id="disburse_acc_val" class="font-mono font-black text-slate-900 text-xs select-all"></span>
                        </div>
                        <button type="button" onclick="copyTextValue(document.getElementById('disburse_acc_val').innerText, this)" class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200 transition">Copy A/c</button>
                    </div>
                    <div class="flex items-center justify-between bg-white px-3 py-1.5 rounded-xl border border-blue-200">
                        <div>
                            <span class="text-[9px] text-slate-400 uppercase block font-bold">IFSC Code</span>
                            <span id="disburse_ifsc_val" class="font-mono font-black text-slate-900 text-xs uppercase select-all"></span>
                        </div>
                        <button type="button" onclick="copyTextValue(document.getElementById('disburse_ifsc_val').innerText, this)" class="px-2 py-0.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200 transition">Copy IFSC</button>
                    </div>
                </div>

                <div id="disburse_no_payout_warning" class="text-rose-600 text-xs font-semibold hidden">
                    ⚠️ This affiliate has not configured any UPI or Bank Account details.
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <span>Bank UTR / UPI Transaction Reference</span> <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="payout_reference" 
                    required 
                    placeholder="e.g. UTR1234567890 or UPI-Ref-98950"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 font-mono transition"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Admin Notes (Optional)</label>
                <input 
                    type="text" 
                    name="admin_notes" 
                    placeholder="e.g. Disbursed via GPay Business / Federal Bank NEFT"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-600 transition"
                >
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeDisburseModal()"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black rounded-xl transition shadow"
                >
                    Confirm Disbursement
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL: EDIT COMMISSION RATE                                   -->
<!-- ============================================================= -->
<div id="commissionModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeCommissionModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-lg font-black text-slate-900 mb-1">Edit Commission Rate</h3>
        <p id="comm_promoter_name" class="text-xs text-slate-500 mb-4 font-bold"></p>

        <form id="commissionForm" action="" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Commission Percentage (%)</label>
                <input 
                    type="number" 
                    step="0.5" 
                    min="0" 
                    max="100"
                    id="edit_commission_rate" 
                    name="commission_rate" 
                    required 
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-600 transition"
                >
                <p class="text-[10px] text-slate-400 mt-1">Default is 15%. Custom percentage applies to future conversions.</p>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeCommissionModal()"
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-black rounded-xl transition shadow"
                >
                    Save Rate
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL: ADD BONUS                                              -->
<!-- ============================================================= -->
<div id="bonusModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeBonusModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-lg font-black text-slate-900 mb-1 flex items-center gap-1.5">
            <span>Credit Bonus</span>
            <span class="text-yellow-500">⭐</span>
        </h3>
        <p id="bonus_promoter_name" class="text-xs text-slate-500 mb-4 font-bold"></p>

        <form id="bonusForm" action="" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Bonus Amount (₹)</label>
                <input 
                    type="number" 
                    step="10" 
                    min="1" 
                    name="bonus_amount" 
                    required 
                    placeholder="e.g. 500"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-blue-600 transition"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Period Month (YYYY-MM)</label>
                <input 
                    type="text" 
                    name="period_month" 
                    value="{{ now()->format('Y-m') }}" 
                    required 
                    placeholder="e.g. {{ now()->format('Y-m') }}"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs font-mono text-slate-900 focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Reason / Campaign</label>
                <input 
                    type="text" 
                    name="reason" 
                    required 
                    placeholder="e.g. Target Achiever Bonus (10+ students)"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600"
                >
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeBonusModal()"
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-yellow-400 hover:bg-yellow-300 text-slate-950 text-xs font-black rounded-xl transition shadow"
                >
                    Credit Bonus
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL: ADMIN EDIT AFFILIATE PAYOUT DETAILS                    -->
<!-- ============================================================= -->
<div id="adminPayoutModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button 
            type="button" 
            onclick="closeAdminPayoutModal()"
            class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 text-lg font-bold"
        >
            ✕
        </button>

        <h3 class="text-lg font-black text-slate-900 mb-1 flex items-center gap-2">
            <span>Edit Promoter Payout Details</span>
            <span class="text-blue-600">💳</span>
        </h3>
        <p id="admin_payout_promoter_name" class="text-xs text-slate-500 mb-4 font-bold"></p>

        <form id="adminPayoutForm" action="" method="POST" class="space-y-4">
            @csrf

            <!-- UPI ID -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    <span>UPI ID</span>
                    <span class="text-[10px] text-slate-400 font-normal ml-1">(GPay / PhonePe / Paytm / BHIM)</span>
                </label>
                <input 
                    type="text" 
                    id="admin_payout_upi"
                    name="upi_id" 
                    placeholder="e.g. anu@okaxis or 9895000000@paytm"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs font-mono text-slate-900 focus:outline-none focus:border-blue-600 transition"
                >
            </div>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-1">
                <div class="border-t border-slate-200 w-full"></div>
                <span class="bg-white px-2.5 text-[10px] uppercase tracking-wider font-bold text-slate-400">Bank Account Transfer</span>
                <div class="border-t border-slate-200 w-full"></div>
            </div>

            <!-- Bank Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Bank Name</label>
                <input 
                    type="text" 
                    id="admin_payout_bank_name"
                    name="bank_name" 
                    placeholder="e.g. Federal Bank / SBI / Canara Bank"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition"
                >
            </div>

            <!-- Account Holder Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Account Holder Name</label>
                <input 
                    type="text" 
                    id="admin_payout_account_holder"
                    name="account_holder" 
                    placeholder="As per bank passbook"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 transition"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Account Number -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Number</label>
                    <input 
                        type="text" 
                        id="admin_payout_account_number"
                        name="account_number" 
                        placeholder="Bank Account Number"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono text-slate-900 focus:outline-none focus:border-blue-600 transition"
                    >
                </div>

                <!-- IFSC Code -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">IFSC Code</label>
                    <input 
                        type="text" 
                        id="admin_payout_ifsc_code"
                        name="ifsc_code" 
                        placeholder="e.g. FDRL0001234"
                        maxlength="20"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-mono uppercase text-slate-900 focus:outline-none focus:border-blue-600 transition"
                    >
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button 
                    type="button" 
                    onclick="closeAdminPayoutModal()"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl transition shadow"
                >
                    Save Payout Details
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function copyTextValue(text, btn) {
        if (!text) return;
        const originalText = btn.innerText;

        function showSuccess() {
            btn.innerText = 'Copied!';
            btn.classList.add('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.remove('bg-emerald-100', 'text-emerald-800', 'border-emerald-300');
            }, 2000);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(showSuccess).catch(() => {
                prompt('Copy this value:', text);
            });
        } else {
            prompt('Copy this value:', text);
        }
    }

    function openDisburseModal(affiliateId, promoterName, month, amount, upi, bankName, accountHolder, accountNum, ifsc) {
        document.getElementById('disburseForm').action = '/admin/affiliates/' + affiliateId + '/disburse';
        document.getElementById('disburse_period_month').value = month;
        document.getElementById('disburse_promoter_name').innerText = promoterName;
        document.getElementById('disburse_amount_display').innerText = '₹' + Number(amount).toLocaleString('en-IN', {minimumFractionDigits: 2});

        const upiBox = document.getElementById('disburse_upi_section');
        const bankBox = document.getElementById('disburse_bank_section');
        const emptyBox = document.getElementById('disburse_no_payout_warning');
        const badge = document.getElementById('disburse_target_badge');

        let hasUpi = upi && upi.trim() !== '';
        let hasBank = accountNum && accountNum.trim() !== '';

        if (hasUpi) {
            document.getElementById('disburse_upi_val').innerText = upi;
            upiBox.classList.remove('hidden');
        } else {
            upiBox.classList.add('hidden');
        }

        if (hasBank) {
            document.getElementById('disburse_bank_val').innerText = bankName || 'Bank';
            document.getElementById('disburse_holder_val').innerText = accountHolder || '—';
            document.getElementById('disburse_acc_val').innerText = accountNum;
            document.getElementById('disburse_ifsc_val').innerText = ifsc || '—';
            bankBox.classList.remove('hidden');
        } else {
            bankBox.classList.add('hidden');
        }

        if (!hasUpi && !hasBank) {
            emptyBox.classList.remove('hidden');
            badge.innerText = 'Not Provided';
            badge.className = 'text-[10px] px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-bold';
        } else {
            emptyBox.classList.add('hidden');
            badge.innerText = hasBank && hasUpi ? 'UPI & Bank Available' : (hasBank ? 'Bank Account' : 'UPI VPA');
            badge.className = 'text-[10px] px-2 py-0.5 rounded-md bg-blue-200 text-blue-900 font-bold';
        }

        document.getElementById('disburseModal').classList.remove('hidden');
    }
    function closeDisburseModal() {
        document.getElementById('disburseModal').classList.add('hidden');
    }

    function openAdminPayoutModal(affiliateId, promoterName, upi, bankName, accountHolder, accountNum, ifsc) {
        document.getElementById('adminPayoutForm').action = '/admin/affiliates/' + affiliateId + '/payout-details';
        document.getElementById('admin_payout_promoter_name').innerText = 'Promoter: ' + promoterName;
        document.getElementById('admin_payout_upi').value = upi || '';
        document.getElementById('admin_payout_bank_name').value = bankName || '';
        document.getElementById('admin_payout_account_holder').value = accountHolder || '';
        document.getElementById('admin_payout_account_number').value = accountNum || '';
        document.getElementById('admin_payout_ifsc_code').value = ifsc || '';
        document.getElementById('adminPayoutModal').classList.remove('hidden');
    }
    function closeAdminPayoutModal() {
        document.getElementById('adminPayoutModal').classList.add('hidden');
    }

    function openCommissionModal(affiliateId, promoterName, currentRate) {
        document.getElementById('commissionForm').action = '/admin/affiliates/' + affiliateId + '/commission-rate';
        document.getElementById('comm_promoter_name').innerText = 'Promoter: ' + promoterName;
        document.getElementById('edit_commission_rate').value = currentRate;
        document.getElementById('commissionModal').classList.remove('hidden');
    }
    function closeCommissionModal() {
        document.getElementById('commissionModal').classList.add('hidden');
    }

    function openBonusModal(affiliateId, promoterName) {
        document.getElementById('bonusForm').action = '/admin/affiliates/' + affiliateId + '/bonus';
        document.getElementById('bonus_promoter_name').innerText = 'Promoter: ' + promoterName;
        document.getElementById('bonusModal').classList.remove('hidden');
    }
    function closeBonusModal() {
        document.getElementById('bonusModal').classList.add('hidden');
    }
</script>
@endsection
