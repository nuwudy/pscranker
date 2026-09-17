@extends('layouts.app')

@section('title', 'Candidate Accounts & Subscription Management — PSCRanker Admin')

@section('content')
<div class="py-8 bg-slate-50 min-h-[90vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Header & Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-[#0052FF]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs text-blue-300 hover:text-white font-bold flex items-center gap-1 transition">
                        <span>← Dashboard</span>
                    </a>
                    <span class="text-slate-600">•</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        👥 User Directory
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Candidates & Subscriptions</span>
                    <span class="text-emerald-400 text-xl">🛡️</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium mt-1 max-w-2xl">
                    Create candidate accounts manually, record offline UPI or cash payments, gift promotional PRO passes, and assign team administrators.
                </p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 relative z-10">
                <button 
                    type="button" 
                    onclick="openCreateUserModal()"
                    class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs rounded-xl shadow-lg transition flex items-center gap-2 border border-emerald-300 active:scale-95"
                >
                    <span>➕ Create Account</span>
                    <span>⚡</span>
                </button>
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-white/20"
                >
                    <span>📊 Mission Control</span>
                </a>
            </div>
        </div>

        <!-- Flash Success / Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span class="flex items-center gap-2">
                    <span class="text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </span>
                <span class="text-[10px] text-emerald-700 uppercase tracking-wider font-mono">Success</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <span class="flex items-center gap-2">
                    <span class="text-base">⚠️</span>
                    <span>{{ session('error') }}</span>
                </span>
                <span class="text-[10px] text-rose-700 uppercase tracking-wider font-mono">Notice</span>
            </div>
        @endif

        <!-- Auto-Generated Credentials Callout for Instant Copy / WhatsApp -->
        @if(session('new_user_credentials'))
            @php $creds = session('new_user_credentials'); @endphp
            <div class="mb-8 p-6 rounded-3xl bg-amber-50 border-2 border-amber-300 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🎉</span>
                        <div>
                            <h3 class="text-sm sm:text-base font-black text-amber-950">New Account Created Successfully!</h3>
                            <p class="text-xs text-amber-800 font-medium">Share these credentials with the candidate via WhatsApp or SMS so they can log in right away:</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        onclick="copyCredentialsText('{{ $creds['name'] }}', '{{ $creds['phone'] }}', '{{ $creds['password'] }}')"
                        class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black rounded-xl shadow transition flex items-center gap-1.5 shrink-0"
                    >
                        <span>📋 Copy Message</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-white/90 p-4 rounded-2xl border border-amber-200 text-xs font-mono">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Candidate Name</span>
                        <span class="font-bold text-slate-900">{{ $creds['name'] }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Mobile Phone</span>
                        <span class="font-bold text-slate-900">{{ $creds['phone'] }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Email</span>
                        <span class="font-bold text-slate-900">{{ $creds['email'] }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Password</span>
                        <span class="font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $creds['password'] }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- 4 Metric Counters -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Total Candidates</span>
                    <span>👥</span>
                </div>
                <div class="text-2xl font-black text-slate-900 font-mono">{{ number_format($totalUsers) }}</div>
                <div class="text-[10px] font-bold text-slate-400 mt-1">Registered in Database</div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-emerald-400/80 p-4 shadow-xs bg-emerald-50/20">
                <div class="flex items-center justify-between text-emerald-800 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Active PRO Passes</span>
                    <span>👑</span>
                </div>
                <div class="text-2xl font-black text-emerald-600 font-mono">{{ number_format($totalPro) }}</div>
                <div class="text-[10px] font-bold text-emerald-700 mt-1">Prepaid or Gifted PRO</div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                <div class="flex items-center justify-between text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Free Members</span>
                    <span>🆓</span>
                </div>
                <div class="text-2xl font-black text-slate-700 font-mono">{{ number_format($totalFree) }}</div>
                <div class="text-[10px] font-bold text-slate-400 mt-1">Limited Tier Access</div>
            </div>

            <div class="bg-white rounded-2xl border border-purple-200 p-4 shadow-xs bg-purple-50/20">
                <div class="flex items-center justify-between text-purple-900 text-[11px] font-bold uppercase tracking-wider mb-1">
                    <span>Administrators</span>
                    <span>🛡️</span>
                </div>
                <div class="text-2xl font-black text-purple-700 font-mono">{{ number_format($totalAdmins) }}</div>
                <div class="text-[10px] font-bold text-purple-600 mt-1">Full Dashboard Access</div>
            </div>
        </div>

        <!-- Filter & Search Bar Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 mb-6 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <a 
                        href="{{ route('admin.users.index', ['status' => 'all', 'q' => request('q')]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition {{ $filter === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        All Users ({{ $totalUsers }})
                    </a>
                    <a 
                        href="{{ route('admin.users.index', ['status' => 'pro', 'q' => request('q')]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition {{ $filter === 'pro' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}"
                    >
                        👑 Active PRO ({{ $totalPro }})
                    </a>
                    <a 
                        href="{{ route('admin.users.index', ['status' => 'free', 'q' => request('q')]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition {{ $filter === 'free' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}"
                    >
                        Free Candidates ({{ $totalFree }})
                    </a>
                    <a 
                        href="{{ route('admin.users.index', ['status' => 'admin', 'q' => request('q')]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-black transition {{ $filter === 'admin' ? 'bg-purple-700 text-white' : 'bg-purple-50 text-purple-900 hover:bg-purple-100' }}"
                    >
                        🛡️ Admins ({{ $totalAdmins }})
                    </a>
                </div>

                <!-- Search Input -->
                <div class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $filter }}">
                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ request('q') }}"
                            placeholder="Search by name, phone, email..."
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0052FF] bg-slate-50 focus:bg-white"
                        >
                    </div>
                    <button 
                        type="submit" 
                        class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition"
                    >
                        Search
                    </button>
                    @if(request()->filled('q'))
                        <a 
                            href="{{ route('admin.users.index', ['status' => $filter]) }}"
                            class="px-2.5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition"
                            title="Clear search"
                        >
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="p-4">Candidate</th>
                            <th class="p-4">Contact Info</th>
                            <th class="p-4 text-center">Role</th>
                            <th class="p-4">Subscription Status</th>
                            <th class="p-4 text-center">Joined</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($users as $user)
                            @php
                                $isSuperAdmin = ($user->email === 'admin@pscranker.com' || $user->phone === '9895940500');
                                $isAdmin = $user->isAdmin();
                                $isPro = $user->isSubscribed();
                                $daysRemaining = $user->subscriptionDaysRemaining();
                            @endphp
                            <tr class="hover:bg-blue-50/20 transition">
                                <!-- Candidate Name & Avatar -->
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-xs shrink-0 {{ $isAdmin ? 'bg-purple-100 text-purple-700 border border-purple-200' : ($isPro ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                                <span>{{ $user->name }}</span>
                                                @if($isSuperAdmin)
                                                    <span class="text-[10px] bg-amber-100 text-amber-900 px-1.5 py-0.2 rounded font-black">SUPER</span>
                                                @endif
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">ID: #{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Info -->
                                <td class="p-4">
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>📱 {{ $user->phone ?? '—' }}</span>
                                        @if($user->phone)
                                            <a 
                                                href="https://wa.me/91{{ $user->phone }}" 
                                                target="_blank" 
                                                class="text-emerald-600 hover:text-emerald-700 text-xs" 
                                                title="Open WhatsApp chat"
                                            >
                                                💬
                                            </a>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $user->email }}</div>
                                </td>

                                <!-- Role Badge -->
                                <td class="p-4 text-center">
                                    @if($isSuperAdmin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                            👑 Super Admin
                                        </span>
                                    @elseif($user->is_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-100 text-purple-900 border border-purple-200">
                                            🛡️ Administrator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Candidate
                                        </span>
                                    @endif
                                </td>

                                <!-- Subscription Status -->
                                <td class="p-4">
                                    @if($isPro)
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                👑 PRO PASS ACTIVE
                                            </span>
                                            @if($isAdmin)
                                                <span class="text-[10px] text-slate-400 font-mono">(Admin Perk)</span>
                                            @endif
                                        </div>
                                        @if($user->subscribed_until)
                                            <div class="text-[11px] text-slate-600 mt-0.5 font-medium">
                                                Valid until <strong>{{ $user->subscribed_until->format('d M Y') }}</strong>
                                                <span class="text-emerald-600 font-bold">({{ $daysRemaining }} days left)</span>
                                            </div>
                                            @if($user->subscription_plan)
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $user->subscription_plan }}</div>
                                            @endif
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500">
                                            Free Member
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">No active prepaid pass</div>
                                    @endif
                                </td>

                                <!-- Joined Date -->
                                <td class="p-4 text-center text-[11px] font-mono text-slate-500">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        
                                        <!-- Edit User Button -->
                                        <button 
                                            type="button"
                                            onclick="openEditUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->phone }}', '{{ $user->email }}', {{ $user->is_admin ? 'true' : 'false' }}, {{ $isSuperAdmin ? 'true' : 'false' }})"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition border border-blue-200 flex items-center gap-1 active:scale-95"
                                            title="Edit profile & reset password"
                                        >
                                            <span>✏️</span>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Gift / Offline Sub Button -->
                                        <button 
                                            type="button"
                                            onclick="openGiftModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->phone }}', '{{ $isPro ? 1 : 0 }}', '{{ $user->subscribed_until ? $user->subscribed_until->format('d M Y') : '' }}')"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 transition border border-amber-300 flex items-center gap-1 active:scale-95"
                                            title="Record Offline Payment or Gift Subscription"
                                        >
                                            <span>🎁</span>
                                            <span>Sub</span>
                                        </button>

                                        <!-- Toggle Admin Button -->
                                        @if(!$isSuperAdmin && $user->id !== auth()->id())
                                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" onsubmit="return confirm('{{ $user->is_admin ? "Revoke administrator privileges from {$user->name}?" : "Grant administrator privileges to {$user->name}? They will be able to access the admin dashboard and manage sessions." }}');">
                                                @csrf
                                                @if($user->is_admin)
                                                    <button 
                                                        type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 transition border border-rose-200"
                                                        title="Revoke Admin Rights"
                                                    >
                                                        Revoke Admin
                                                    </button>
                                                @else
                                                    <button 
                                                        type="submit" 
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 transition border border-purple-200"
                                                        title="Make Admin"
                                                    >
                                                        🛡️ Make Admin
                                                    </button>
                                                @endif
                                            </form>
                                        @elseif($isSuperAdmin)
                                            <span class="px-2 py-1 rounded text-[10px] font-bold text-slate-400 bg-slate-100 cursor-not-allowed">
                                                Root Admin
                                            </span>
                                        @else
                                            <span class="px-2 py-1 rounded text-[10px] font-bold text-slate-400 bg-slate-100 cursor-not-allowed">
                                                You
                                            </span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                    No candidate accounts found matching your query.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: Create Candidate Account Manually -->
<!-- ========================================== -->
<div id="createUserModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden max-h-[90vh] flex flex-col">
        
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">➕</span>
                <div>
                    <h3 class="text-base font-black text-white">Create Candidate Account</h3>
                    <p class="text-xs text-slate-300">Register candidate and optionally activate an offline or gifted subscription</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
        </div>

        <!-- Modal Form -->
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-5 sm:p-6 overflow-y-auto space-y-4">
            @csrf

            <!-- Candidate Full Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Candidate Full Name <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    required 
                    placeholder="e.g. Rahul Sharma"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                >
            </div>

            <!-- 10-Digit Mobile Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">10-Digit Mobile Phone <span class="text-rose-500">*</span></label>
                    <input 
                        type="tel" 
                        name="phone" 
                        required 
                        maxlength="10"
                        pattern="[0-9]{10}"
                        placeholder="e.g. 9876543210"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                    >
                    <span class="text-[10px] text-slate-400">Used for fast mobile login</span>
                </div>

                <!-- Email Address -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="e.g. rahul@example.com"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                    >
                </div>
            </div>

            <!-- Password (Mandatory) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Password <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(Minimum 6 characters)</span>
                </label>
                <input 
                    type="text" 
                    name="password" 
                    required
                    minlength="6"
                    placeholder="Enter login password for candidate"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                >
            </div>

            <!-- Admin Privilege Checkbox -->
            <div class="p-3.5 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-between">
                <div>
                    <div class="text-xs font-black text-purple-950 flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>Grant Administrator Access</span>
                    </div>
                    <div class="text-[11px] text-purple-800">Allows access to Admin Dashboard, Session Creator, and Question Banks.</div>
                </div>
                <input type="checkbox" name="is_admin" value="1" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
            </div>

            <!-- Subscription Checkbox & Toggle Box -->
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleSubFields()">
                    <div>
                        <div class="text-xs font-black text-emerald-950 flex items-center gap-1.5">
                            <span>👑</span>
                            <span>Activate PRO Subscription Immediately</span>
                        </div>
                        <div class="text-[11px] text-emerald-800">Select if candidate paid offline via Cash, GPay, UPI, or is receiving a gifted pass.</div>
                    </div>
                    <input type="checkbox" id="activateSubCheckbox" name="activate_subscription" value="1" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                </div>

                <!-- Sub details conditional section -->
                <div id="subFieldsContainer" class="hidden mt-4 pt-4 border-t border-emerald-200/80 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-900 mb-1">PRO Duration</label>
                            <select name="duration_months" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs bg-white">
                                <option value="1">1 Month Pass</option>
                                <option value="2">2 Months Rapid Revision</option>
                                <option value="3" selected>3 Months Exam Sprint (Recommended)</option>
                                <option value="6">6 Months Semester Pass</option>
                                <option value="12">12 Months (1 Year) Rank Pass</option>
                                <option value="24">24 Months (2 Years) Super Pass</option>
                                <option value="120">👑 Lifetime Pass</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-900 mb-1">Payment Method</label>
                            <select name="payment_mode" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs bg-white">
                                <option value="cash">Offline Cash</option>
                                <option value="gpay_upi">GPay / PhonePe / UPI</option>
                                <option value="bank_transfer">Bank Transfer / NEFT</option>
                                <option value="gift">Promotional Gift / Referral</option>
                                <option value="scholarship">Merit Scholarship</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-900 mb-1">Amount Collected (₹)</label>
                            <input 
                                type="number" 
                                step="1" 
                                name="amount" 
                                placeholder="e.g. 762 (or 0 for Gift)" 
                                class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs bg-white font-mono"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-900 mb-1">Notes / UPI Reference</label>
                            <input 
                                type="text" 
                                name="notes" 
                                placeholder="e.g. UPI Ref / Cash receipt # / Referral note" 
                                class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs bg-white"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeCreateUserModal()" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-950 bg-emerald-400 hover:bg-emerald-300 transition shadow"
                >
                    Save & Activate Account →
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL 2: Gift or Record Offline Subscription Payment -->
<!-- ==================================================== -->
<div id="giftModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-950 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎁</span>
                <div>
                    <h3 class="text-base font-black text-slate-950">Activate / Gift PRO Subscription</h3>
                    <p class="text-xs text-slate-900 font-medium">Add offline payment or scholarship pass for candidate</p>
                </div>
            </div>
            <button type="button" onclick="closeGiftModal()" class="text-slate-800 hover:text-black text-xl font-bold">✕</button>
        </div>

        <!-- Form -->
        <form id="giftForm" method="POST" class="p-5 sm:p-6 space-y-4">
            @csrf

            <!-- Candidate info pill -->
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Candidate</span>
                    <strong id="giftModalUserName" class="text-slate-900 text-sm font-black"></strong>
                    <span id="giftModalUserPhone" class="text-slate-500 font-mono ml-1"></span>
                </div>
                <div id="giftModalSubStatus" class="text-right"></div>
            </div>

            <!-- Duration Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Select Pass Duration <span class="text-rose-500">*</span>
                </label>
                <select name="duration_months" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="1">1 Month Pass</option>
                    <option value="2">2 Months Pass</option>
                    <option value="3" selected>3 Months Exam Sprint (🔥 Recommended)</option>
                    <option value="6">6 Months Semester Pass</option>
                    <option value="12">12 Months (1 Year) Full Pass</option>
                    <option value="24">24 Months (2 Years) Pass</option>
                    <option value="120">👑 Lifetime Access</option>
                </select>
            </div>

            <!-- Payment Mode -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Payment Method / Reason <span class="text-rose-500">*</span>
                </label>
                <select name="payment_mode" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-white font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="gpay_upi">GPay / PhonePe / UPI (Offline)</option>
                    <option value="cash">Direct Cash Payment</option>
                    <option value="bank_transfer">Direct Bank Transfer / NEFT</option>
                    <option value="gift">Promotional Gift / Free Complimentary</option>
                    <option value="scholarship">Merit Scholarship</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <!-- Amount Collected -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Amount Collected (₹)
                    </label>
                    <input 
                        type="number" 
                        step="1" 
                        name="amount" 
                        placeholder="0.00 if gifted" 
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-amber-500 focus:outline-none"
                    >
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Reference / Notes
                    </label>
                    <input 
                        type="text" 
                        name="notes" 
                        placeholder="e.g. UPI Ref / Cash slip" 
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none"
                    >
                </div>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 text-amber-900 text-[11px] font-medium border border-amber-200">
                ⚡ <em>Note:</em> If the candidate already has an active subscription, this will seamlessly <strong>extend</strong> their expiry date from their current end date!
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeGiftModal()" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-black text-slate-950 bg-amber-400 hover:bg-yellow-400 transition shadow"
                >
                    Grant Subscription ⚡
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: Edit Candidate Account / Profile -->
<!-- ========================================== -->
<div id="editUserModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden flex flex-col">
        
        <!-- Modal Header -->
        <div class="p-5 bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">✏️</span>
                <div>
                    <h3 class="text-base font-black text-white">Edit Candidate Profile</h3>
                    <p class="text-xs text-slate-300">Update name, mobile, email, admin role, or reset password</p>
                </div>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-white text-xl font-bold">✕</button>
        </div>

        <!-- Modal Form -->
        <form id="editUserForm" method="POST" class="p-5 sm:p-6 space-y-4">
            @csrf
            @method('PUT')

            <!-- Candidate Full Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Candidate Full Name <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    id="edit_name"
                    name="name" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                >
            </div>

            <!-- 10-Digit Mobile Phone & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">10-Digit Mobile <span class="text-rose-500">*</span></label>
                    <input 
                        type="tel" 
                        id="edit_phone"
                        name="phone" 
                        required 
                        maxlength="10"
                        pattern="[0-9]{10}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input 
                        type="email" 
                        id="edit_email"
                        name="email" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                    >
                </div>
            </div>

            <!-- Reset Password -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    New Password <span class="text-slate-400 font-normal">(Leave blank to keep existing password)</span>
                </label>
                <input 
                    type="text" 
                    name="password" 
                    minlength="6"
                    placeholder="Enter new password to reset, or leave empty"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono focus:ring-2 focus:ring-[#0052FF] focus:outline-none"
                >
            </div>

            <!-- Admin Privilege Checkbox -->
            <div id="edit_admin_wrapper" class="p-3.5 rounded-2xl bg-purple-50 border border-purple-200 flex items-center justify-between">
                <div>
                    <div class="text-xs font-black text-purple-950 flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>Administrator Access</span>
                    </div>
                    <div class="text-[11px] text-purple-800">Can access Admin Dashboard, Session Creator, and Question Banks.</div>
                </div>
                <input type="checkbox" id="edit_is_admin" name="is_admin" value="1" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    onclick="closeEditUserModal()" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-[#0052FF] hover:bg-blue-700 transition shadow"
                >
                    Save Changes →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateUserModal() {
    document.getElementById('createUserModal').classList.remove('hidden');
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').classList.add('hidden');
}

function openEditUserModal(userId, name, phone, email, isAdmin, isSuperAdmin) {
    const form = document.getElementById('editUserForm');
    form.action = `/admin/users/${userId}`;

    document.getElementById('edit_name').value = name;
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_email').value = email || '';
    
    const adminCheckbox = document.getElementById('edit_is_admin');
    const adminWrapper = document.getElementById('edit_admin_wrapper');
    
    adminCheckbox.checked = isAdmin;
    if (isSuperAdmin) {
        adminCheckbox.disabled = true;
        adminWrapper.classList.add('opacity-60');
    } else {
        adminCheckbox.disabled = false;
        adminWrapper.classList.remove('opacity-60');
    }

    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

function toggleSubFields() {
    const cb = document.getElementById('activateSubCheckbox');
    const container = document.getElementById('subFieldsContainer');
    if (cb.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

document.getElementById('activateSubCheckbox').addEventListener('change', function() {
    const container = document.getElementById('subFieldsContainer');
    if (this.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
});

function openGiftModal(userId, userName, userPhone, isPro, validUntil) {
    const form = document.getElementById('giftForm');
    form.action = `/admin/users/${userId}/gift-subscription`;

    document.getElementById('giftModalUserName').textContent = userName;
    document.getElementById('giftModalUserPhone').textContent = userPhone ? `(${userPhone})` : '';

    const statusEl = document.getElementById('giftModalSubStatus');
    if (isPro == 1 && validUntil) {
        statusEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">Active until ${validUntil}</span>`;
    } else {
        statusEl.innerHTML = `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">Free Member</span>`;
    }

    document.getElementById('giftModal').classList.remove('hidden');
}

function closeGiftModal() {
    document.getElementById('giftModal').classList.add('hidden');
}

function copyCredentialsText(name, phone, password) {
    const text = `Hello ${name}! Welcome to PSCRanker. 🚀\n\nYour account has been created:\n📱 Mobile / Login: ${phone}\n🔑 Password: ${password}\n\nLog in here: ${window.location.origin}/login\nHappy learning & rank high!`;
    navigator.clipboard.writeText(text).then(() => {
        alert('Credentials message copied to clipboard! You can now paste and send to the candidate on WhatsApp.');
    });
}
</script>
@endsection
