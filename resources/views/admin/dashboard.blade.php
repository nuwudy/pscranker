@extends('layouts.app')

@section('title', 'Admin Dashboard & Mission Control — PSCRanker')

@section('content')
<div class="py-8 bg-slate-50 min-h-[90vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Dashboard Top Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
            <!-- Decorative Glow -->
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-[#0052FF]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-yellow-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        🟢 Engine Online
                    </span>
                    <span class="text-xs text-slate-400 font-bold">Kerala PSC Mission Control</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Admin Dashboard</span>
                    <span class="text-yellow-400 text-2xl">⚡</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1">
                    Manage 4-phase micro-learning sessions, question banks, media assets, and track live candidate performance.
                </p>
            </div>

            <!-- Quick Action CTA Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 relative z-10">
                <a 
                    href="{{ route('admin.sessions.create') }}" 
                    class="px-4 py-2.5 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 font-black text-xs rounded-xl shadow transition flex items-center gap-1.5 border border-yellow-300 active:scale-95"
                >
                    <span>+ New Session</span>
                    <span>⚡</span>
                </a>
                <a 
                    href="{{ route('admin.media.index') }}" 
                    class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-white/20"
                >
                    <span>📁 Media Library</span>
                </a>
                <a 
                    href="{{ route('sessions.index') }}" 
                    target="_blank"
                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5"
                >
                    <span>View Portal ↗</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </span>
                <span class="text-[10px] text-emerald-700 uppercase tracking-wider font-mono">Updated</span>
            </div>
        @endif

        <!-- 6 KPI Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
            
            <!-- 1. Learning Sessions -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-300 transition">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Sessions</span>
                    <span>🎓</span>
                </div>
                <div class="text-2xl font-black text-slate-900 font-mono">
                    {{ $stats['active_sessions'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $stats['total_sessions'] }}</span>
                </div>
                <div class="text-[10px] font-bold text-emerald-600 mt-1">
                    ● Active Capsules
                </div>
            </div>

            <!-- 2. Question Bank -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-300 transition">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Questions</span>
                    <span>❓</span>
                </div>
                <div class="text-2xl font-black text-slate-900 font-mono">
                    {{ $stats['total_questions'] }}
                </div>
                <div class="text-[10px] font-medium text-slate-500 mt-1 truncate">
                    🎯 {{ $stats['diagnostic_questions'] }} • ⚡ {{ $stats['reinforcement_questions'] }} • 📝 {{ $stats['omr_questions'] }}
                </div>
            </div>

            <!-- 3. Active Subscribers -->
            <div class="bg-white rounded-2xl border-2 border-yellow-400/80 p-4 shadow-xs hover:border-yellow-500 transition bg-yellow-50/20">
                <div class="flex items-center justify-between text-slate-700 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Subscribers</span>
                    <span>👑</span>
                </div>
                <div class="text-2xl font-black text-slate-900 font-mono">
                    {{ $stats['active_subscribers'] }}
                </div>
                <div class="text-[10px] font-bold text-amber-700 mt-1">
                    ⚡ Active Prepaid Passes
                </div>
            </div>

            <!-- 4. Subscription Revenue -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-300 transition">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Prepaid Revenue</span>
                    <span>💳</span>
                </div>
                <div class="text-2xl font-black text-emerald-600 font-mono">
                    ₹{{ number_format($stats['total_revenue']) }}
                </div>
                <div class="text-[10px] font-bold text-slate-500 mt-1">
                    Razorpay Collected
                </div>
            </div>

            <!-- 5. Session Attempts -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-300 transition">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Attempts</span>
                    <span>🎯</span>
                </div>
                <div class="text-2xl font-black text-slate-900 font-mono">
                    {{ $stats['total_session_attempts'] }}
                </div>
                <div class="text-[10px] font-bold text-blue-600 mt-1">
                    {{ $stats['total_session_completions'] }} Mastered (100%)
                </div>
            </div>

            <!-- 6. Total XP Distributed -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs hover:border-blue-300 transition">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Total XP</span>
                    <span>⚡</span>
                </div>
                <div class="text-2xl font-black text-amber-500 font-mono">
                    {{ number_format($stats['total_xp_awarded']) }}
                </div>
                <div class="text-[10px] font-bold text-slate-600 mt-1">
                    Avg: {{ $stats['avg_omr_score'] }} pts
                </div>
            </div>

        </div>

        <!-- Section 1: Sessions Inventory & Content Completeness Table -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs mb-8">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                        <span>Learning Sessions & 4-Phase Completeness</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Verify that each session has a diagnostic hook, media blocks, speed blitz, and OMR challenge questions.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('admin.sessions.index') }}" 
                        class="text-xs font-bold text-[#0052FF] hover:underline"
                    >
                        View Full List →
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="p-4"># Order</th>
                            <th class="p-4">Session Title</th>
                            <th class="p-4">Category</th>
                            <th class="p-4 text-center">Phase 1 (Hook)</th>
                            <th class="p-4 text-center">Phase 2 (Capsule)</th>
                            <th class="p-4 text-center">Phase 3 (Blitz)</th>
                            <th class="p-4 text-center">Phase 4 (OMR)</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($sessions as $sess)
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="p-4 font-mono font-bold">{{ $sess->order }}</td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-900 leading-snug">{{ $sess->title }}</div>
                                    @if($sess->title_malayalam)
                                        <div class="text-[11px] text-[#0052FF] font-['Noto_Sans_Malayalam']">{{ $sess->title_malayalam }}</div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                                        {{ $sess->category ? $sess->category->name : 'Unassigned' }}
                                    </span>
                                </td>

                                <!-- Phase 1 Diagnostic Hook status -->
                                <td class="p-4 text-center">
                                    @if($sess->diagnostic_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✅ Ready
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            ⚠️ Missing
                                        </span>
                                    @endif
                                </td>

                                <!-- Phase 2 Media Blocks -->
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 font-mono">
                                        {{ $sess->contents_count }} blocks
                                    </span>
                                </td>

                                <!-- Phase 3 Reinforcement Blitz Questions -->
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-900 font-mono">
                                        {{ $sess->reinforcement_count }} MCQs
                                    </span>
                                </td>

                                <!-- Phase 4 OMR Questions -->
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-900 text-white font-mono">
                                        {{ $sess->omr_count }} Bubbles
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="p-4 text-center">
                                    @if($sess->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-600">Draft</span>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="p-4 text-right space-x-2">
                                    <a 
                                        href="{{ route('session.show', $sess->slug) }}" 
                                        target="_blank"
                                        class="text-blue-600 hover:underline font-bold"
                                    >
                                        Runner ↗
                                    </a>
                                    <a 
                                        href="{{ route('admin.sessions.edit', $sess) }}" 
                                        class="text-[#0052FF] hover:underline font-black"
                                    >
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-500">
                                    No sessions found. <a href="{{ route('admin.sessions.create') }}" class="text-[#0052FF] font-bold underline">Create one now</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- SECTION: PREPAID PRICING ENGINE & RAZORPAY CONFIGURATION -->
        <!-- ============================================================= -->
        <div 
            x-data="adminPricingSettings({
                baseFee: {{ $pricingSettings['course_base_monthly_fee'] }},
                r2: {{ $pricingSettings['rebate_2m'] }},
                r3: {{ $pricingSettings['rebate_3m'] }},
                r6: {{ $pricingSettings['rebate_6m'] }},
                r12: {{ $pricingSettings['rebate_12m'] }}
            })"
            class="bg-white rounded-3xl border-2 border-yellow-400/90 shadow-md p-6 sm:p-8 mb-8 relative overflow-hidden"
        >
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-yellow-400 to-amber-500 text-slate-950 flex items-center justify-center text-2xl font-black shadow-md">
                        💳
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-black text-slate-900">Prepaid Subscription Engine &amp; Progressive Rebates</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-yellow-400 text-slate-950">Active</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Set the base monthly fee and progressive rebates. Changes are dynamically computed across the candidate dropdown instantly.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a 
                        href="{{ route('pricing') }}" 
                        target="_blank" 
                        class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-[#0052FF] font-bold text-xs rounded-xl transition border border-blue-200 flex items-center gap-1"
                    >
                        <span>View Student Pricing Page ↗</span>
                    </a>
                </div>
            </div>

            <!-- Form: Base Fee, Rebates & Gateway Keys -->
            <form action="{{ route('admin.settings.pricing') }}" method="POST" class="mt-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                    
                    <!-- Base Fee Input -->
                    <div class="md:col-span-1 p-4 rounded-2xl bg-yellow-50/60 border border-yellow-300">
                        <label class="block text-xs font-black text-slate-900 uppercase tracking-wider mb-1">
                            Base Monthly Fee (₹)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-500 font-bold text-sm">₹</span>
                            <input 
                                type="number" 
                                name="course_base_monthly_fee" 
                                x-model.number="baseFee" 
                                min="1" 
                                step="1" 
                                required 
                                class="w-full pl-7 pr-3 py-2.5 rounded-xl border-2 border-yellow-400 bg-white font-black text-slate-900 text-base focus:outline-hidden"
                            >
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">1 Month standard fee</span>
                    </div>

                    <!-- 2 Months Rebate -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            2-Mo Rebate (%)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="rebate_2m" 
                                x-model.number="r2" 
                                min="0" 
                                max="100" 
                                required 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-black text-slate-900 text-sm focus:outline-hidden focus:border-[#0052FF]"
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 font-bold text-xs">%</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">Crash revision track</span>
                    </div>

                    <!-- 3 Months Rebate -->
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-black text-blue-950 uppercase tracking-wider">
                                3-Mo Rebate (%)
                            </label>
                            <span class="text-[9px] font-bold text-blue-600 uppercase">Popular</span>
                        </div>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="rebate_3m" 
                                x-model.number="r3" 
                                min="0" 
                                max="100" 
                                required 
                                class="w-full px-3 py-2.5 rounded-xl border border-blue-300 bg-white font-black text-slate-900 text-sm focus:outline-hidden focus:border-[#0052FF]"
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 font-bold text-xs">%</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">90-day sprint cycle</span>
                    </div>

                    <!-- 6 Months Rebate -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            6-Mo Rebate (%)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="rebate_6m" 
                                x-model.number="r6" 
                                min="0" 
                                max="100" 
                                required 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-black text-slate-900 text-sm focus:outline-hidden focus:border-[#0052FF]"
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 font-bold text-xs">%</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">Semester pass</span>
                    </div>

                    <!-- 12 Months Rebate -->
                    <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-black text-purple-950 uppercase tracking-wider">
                                12-Mo Rebate (%)
                            </label>
                            <span class="text-[9px] font-bold text-purple-600 uppercase">Best Value</span>
                        </div>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="rebate_12m" 
                                x-model.number="r12" 
                                min="0" 
                                max="100" 
                                required 
                                class="w-full px-3 py-2.5 rounded-xl border border-purple-300 bg-white font-black text-slate-900 text-sm focus:outline-hidden focus:border-[#0052FF]"
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 font-bold text-xs">%</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-1 block">Full Year Pass</span>
                    </div>

                </div>

                <!-- Razorpay Gateway Credentials -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 mb-6">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="text-base">🔒</span>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Razorpay Gateway API Keys</h3>
                        </div>
                        <span class="text-[11px] text-slate-500">Supports Live / Test Mode</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Razorpay Key ID</label>
                            <input 
                                type="text" 
                                name="razorpay_key_id" 
                                value="{{ $pricingSettings['razorpay_key_id'] }}" 
                                placeholder="rzp_live_... or rzp_test_..." 
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono text-xs focus:outline-hidden focus:border-[#0052FF]"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Razorpay Key Secret</label>
                            <input 
                                type="password" 
                                name="razorpay_key_secret" 
                                value="{{ $pricingSettings['razorpay_key_secret'] }}" 
                                placeholder="••••••••••••••••••••••••" 
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono text-xs focus:outline-hidden focus:border-[#0052FF]"
                            >
                        </div>
                    </div>
                </div>

                <!-- Live Computed Schedule Preview -->
                <div class="mb-6">
                    <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span>⚡ Live Recalculation Preview (What Students See in Dropdown):</span>
                    </div>
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs font-medium">
                            <thead class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th class="p-3">Duration</th>
                                    <th class="p-3">Base Cost</th>
                                    <th class="p-3">Progressive Rebate</th>
                                    <th class="p-3">Candidate Saves</th>
                                    <th class="p-3">Payable Amount</th>
                                    <th class="p-3">Effective Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono text-slate-800">
                                <tr>
                                    <td class="p-3 font-sans font-bold">1 Month</td>
                                    <td class="p-3">₹<span x-text="calc(1, 0).base"></span></td>
                                    <td class="p-3 font-bold text-slate-500">0%</td>
                                    <td class="p-3 text-slate-400">₹0</td>
                                    <td class="p-3 font-black text-slate-900 text-sm">₹<span x-text="calc(1, 0).final"></span></td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(1, 0).eff"></span> / mo</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-sans font-bold">2 Months</td>
                                    <td class="p-3">₹<span x-text="calc(2, r2).base"></span></td>
                                    <td class="p-3 font-bold text-blue-600"><span x-text="r2"></span>%</td>
                                    <td class="p-3 text-emerald-600">₹<span x-text="calc(2, r2).save"></span></td>
                                    <td class="p-3 font-black text-slate-900 text-sm">₹<span x-text="calc(2, r2).final"></span></td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(2, r2).eff"></span> / mo</td>
                                </tr>
                                <tr class="bg-blue-50/40">
                                    <td class="p-3 font-sans font-black text-[#0052FF]">3 Months (Popular 🔥)</td>
                                    <td class="p-3">₹<span x-text="calc(3, r3).base"></span></td>
                                    <td class="p-3 font-bold text-blue-600"><span x-text="r3"></span>%</td>
                                    <td class="p-3 text-emerald-600">₹<span x-text="calc(3, r3).save"></span></td>
                                    <td class="p-3 font-black text-[#0052FF] text-sm">₹<span x-text="calc(3, r3).final"></span></td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(3, r3).eff"></span> / mo</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-sans font-bold">6 Months</td>
                                    <td class="p-3">₹<span x-text="calc(6, r6).base"></span></td>
                                    <td class="p-3 font-bold text-purple-600"><span x-text="r6"></span>%</td>
                                    <td class="p-3 text-emerald-600">₹<span x-text="calc(6, r6).save"></span></td>
                                    <td class="p-3 font-black text-slate-900 text-sm">₹<span x-text="calc(6, r6).final"></span></td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(6, r6).eff"></span> / mo</td>
                                </tr>
                                <tr class="bg-yellow-50/40">
                                    <td class="p-3 font-sans font-black text-amber-700">12 Months (1 Year Pass 👑)</td>
                                    <td class="p-3">₹<span x-text="calc(12, r12).base"></span></td>
                                    <td class="p-3 font-bold text-amber-600"><span x-text="r12"></span>%</td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(12, r12).save"></span></td>
                                    <td class="p-3 font-black text-amber-700 text-sm">₹<span x-text="calc(12, r12).final"></span></td>
                                    <td class="p-3 text-emerald-600 font-bold">₹<span x-text="calc(12, r12).eff"></span> / mo</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition active:scale-95 flex items-center gap-2"
                    >
                        <span>Save Pricing Configuration 💾</span>
                    </button>
                </div>
            </form>
        </div>

        <script>
        function adminPricingSettings(cfg) {
            return {
                baseFee: cfg.baseFee,
                r2: cfg.r2,
                r3: cfg.r3,
                r6: cfg.r6,
                r12: cfg.r12,

                calc(months, rebate) {
                    const base = Math.round(this.baseFee * months);
                    const save = Math.round((base * rebate) / 100);
                    const finalVal = Math.max(1, base - save);
                    const eff = Math.round(finalVal / months);
                    return { base, save, final: finalVal, eff };
                }
            };
        }
        </script>

        <!-- Section 2: Two-Column Live Activity & Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
            
            <!-- Left: Recent Candidate Progress in Sessions (7 cols on lg) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm sm:text-base font-black text-slate-900">
                            Recent Session Activity
                        </h3>
                        <p class="text-[11px] text-slate-500">Candidate progress across 4-phase micro loops</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">Live</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentProgress as $prog)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-[#0052FF] font-black flex items-center justify-center text-xs shrink-0">
                                    {{ $prog->user ? substr($prog->user->name, 0, 1) : '👤' }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">
                                        {{ $prog->user ? $prog->user->name : 'Candidate (' . substr($prog->guest_token ?? 'Guest', 0, 8) . '...)' }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 truncate max-w-xs">
                                        {{ $prog->session ? $prog->session->title : 'Unknown Session' }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wide bg-blue-50 text-[#0052FF]">
                                        Phase: {{ $prog->current_phase }}
                                    </span>
                                    <span class="font-mono font-black text-emerald-600 text-xs">
                                        +{{ $prog->xp_earned }} XP
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    OMR: <strong>{{ $prog->net_marks }} pts</strong> • {{ $prog->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No session activity recorded yet. Launch a session to start collecting candidate telemetry!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Subscriptions & Categories (5 cols on lg) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Recent Prepaid Subscriptions / Payments -->
                <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-base">💳</span>
                            <h3 class="text-sm font-black text-slate-900">
                                Recent Subscriptions
                            </h3>
                        </div>
                        <span class="text-[10px] font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            Razorpay
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($recentPayments as $payment)
                            <div class="py-2.5 flex items-center justify-between gap-2">
                                <div>
                                    <div class="font-bold text-slate-900">
                                        {{ $payment->user ? $payment->user->name : ($payment->payment_metadata['customer_name'] ?? 'Candidate') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $payment->duration_months }} Months Pass • {{ $payment->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-mono font-black text-emerald-600">
                                        ₹{{ number_format($payment->amount) }}
                                    </div>
                                    <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold uppercase {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $payment->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs">
                                No prepaid subscriptions yet. Test the checkout on <a href="{{ route('pricing') }}" target="_blank" class="text-blue-600 font-bold underline">Pricing Page</a>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                    <h3 class="text-sm font-black text-slate-900 mb-3 pb-2 border-b border-slate-100">
                        Category Syllabus Coverage
                    </h3>
                    <div class="space-y-2.5 text-xs">
                        @foreach($categories as $cat)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $cat->name }}</div>
                                    @if($cat->name_malayalam)
                                        <div class="text-[10px] text-slate-500 font-['Noto_Sans_Malayalam']">{{ $cat->name_malayalam }}</div>
                                    @endif
                                </div>
                                <div class="text-right font-mono text-[11px]">
                                    <span class="font-bold text-purple-700">{{ $cat->sessions_count }} Capsules</span>
                                    <span class="text-slate-400 block text-[10px]">{{ $cat->questions_count }} questions</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Speed Drills -->
                <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                        <h3 class="text-sm font-black text-slate-900">
                            ⚡ 3-Min Speed Drills
                        </h3>
                        <a href="{{ route('leaderboard') }}" class="text-[11px] font-bold text-[#0052FF] hover:underline">
                            Leaderboard →
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($recentDrills as $drill)
                            <div class="py-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-amber-500 font-bold">⚡</span>
                                    <span class="font-bold text-slate-800">{{ $drill->candidate_name }}</span>
                                </div>
                                <div class="font-mono text-right">
                                    <span class="font-black text-emerald-600">{{ $drill->score }} / {{ $drill->total_questions }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $drill->time_taken_seconds }}s</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs">
                                No speed drills logged today.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- Section 3: Recent Media Assets Strip -->
        <div class="bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📁</span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Recent Media Library Uploads</h3>
                        <p class="text-[11px] text-slate-500">Quickly preview or grab URLs for photos, audios, and videos</p>
                    </div>
                </div>
                <a href="{{ route('admin.media.index') }}" class="text-xs font-bold text-[#0052FF] hover:underline">
                    View All Media ({{ $stats['total_media'] }}) →
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                @forelse($recentMedia as $med)
                    <div class="p-2 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between text-center group hover:border-blue-400 transition">
                        <div class="h-20 rounded-lg overflow-hidden bg-slate-200 flex items-center justify-center mb-1.5">
                            @if($med->file_type === 'image')
                                <img src="{{ $med->url }}" class="w-full h-full object-cover" loading="lazy">
                            @elseif($med->file_type === 'audio')
                                <span class="text-2xl text-blue-600">🎙️</span>
                            @elseif($med->file_type === 'video')
                                <span class="text-2xl text-red-600">🎬</span>
                            @else
                                <span class="text-2xl text-slate-500">📄</span>
                            @endif
                        </div>
                        <div class="text-[10px] font-bold text-slate-800 truncate" title="{{ $med->name }}">
                            {{ $med->name }}
                        </div>
                        <div class="text-[9px] text-slate-400 font-mono mt-0.5">
                            {{ $med->formatted_size }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-6 py-6 text-center text-xs text-slate-400">
                        No media uploaded yet. <a href="{{ route('admin.media.index') }}" class="text-blue-600 font-bold underline">Upload photos or audios</a>.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
