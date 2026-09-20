<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Console — PSCRanker')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon.png">

    <!-- Google Fonts: Outfit & Noto Sans Malayalam -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Noto+Sans+Malayalam:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100 selection:bg-[#0052FF] selection:text-white" x-data="{ mobileSidebarOpen: false }">

    <!-- Main Flex App Wrapper: Desktop 2-Column Side-by-Side -->
    <div class="min-h-screen flex flex-row">

        <!-- ================================================================= -->
        <!-- 1. DESKTOP SIDEBAR (Permanent Left Fixed-Width Column)            -->
        <!-- ================================================================= -->
        <aside 
            class="hidden lg:flex flex-col shrink-0 sticky top-0 h-screen overflow-y-auto text-slate-300 border-r border-slate-800 z-30"
            style="width: 260px; min-width: 260px; background-color: #0F172A;"
        >
            <!-- Brand Logo Header -->
            <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800 shrink-0" style="background-color: #090E1A;">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo.png') }}" alt="PSCRanker" class="h-7 w-auto object-contain brightness-110">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] font-black uppercase tracking-wider text-blue-400 bg-blue-500/20 px-1.5 py-0.5 rounded border border-blue-400/30">ADMIN</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-6 text-xs font-semibold overflow-y-auto">
                
                <!-- Group: OVERVIEW -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Overview
                    </div>
                    <div class="space-y-1">
                        <a 
                            href="{{ route('admin.dashboard') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">📊</span>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Group: CONTENT & TRACKS -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Tracks &amp; Sessions
                    </div>
                    <div class="space-y-1">
                        <a 
                            href="{{ route('admin.sessions.index') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.sessions.index') || (request()->routeIs('admin.sessions.*') && !request()->routeIs('admin.sessions.create')) ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">📚</span>
                            <span>Learning Sessions</span>
                        </a>

                        <!-- Highlighted Button: + Create Session -->
                        <a 
                            href="{{ route('admin.sessions.create') }}" 
                            class="flex items-center gap-2.5 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.sessions.create') ? 'bg-amber-400 text-slate-950 font-black shadow' : 'text-amber-300 bg-amber-400/10 hover:bg-amber-400/20 border border-amber-400/20 font-bold' }}"
                        >
                            <span class="text-amber-400 font-black text-base leading-none">＋</span>
                            <span>Create Session</span>
                        </a>

                        <a 
                            href="{{ route('admin.categories.index') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">🏷️</span>
                            <span>Subject Tracks</span>
                        </a>

                        <a 
                            href="{{ route('admin.media.index') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.media.*') ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">📁</span>
                            <span>Media Library</span>
                        </a>

                        <a 
                            href="{{ route('map.study') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition text-slate-300 hover:bg-slate-800 hover:text-white"
                        >
                            <span class="text-sm">🌐</span>
                            <span>Map &amp; Globe Lab</span>
                        </a>
                    </div>
                </div>

                <!-- Group: USERS & REVENUE -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Monetization &amp; Members
                    </div>
                    <div class="space-y-1">
                        <a 
                            href="{{ route('admin.users.index') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">👥</span>
                            <span>Candidates &amp; Subs</span>
                        </a>

                        <a 
                            href="{{ route('admin.affiliates.index') }}" 
                            class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.affiliates.*') ? 'bg-[#0052FF] text-white font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                        >
                            <span class="text-sm">🤝</span>
                            <span>Affiliate Promoters</span>
                        </a>
                    </div>
                </div>

                <!-- Group: CANDIDATE PORTAL -->
                <div>
                    <div class="px-3 mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        Candidate Portal
                    </div>
                    <div class="space-y-1">
                        <a 
                            href="{{ route('home') }}" 
                            target="_blank"
                            class="flex items-center justify-between px-3 py-1.5 rounded-xl transition text-slate-400 hover:bg-slate-800/60 hover:text-white"
                        >
                            <span class="flex items-center gap-2.5">
                                <span>🏠</span>
                                <span>Public Website</span>
                            </span>
                            <span class="text-[10px] text-slate-400">↗</span>
                        </a>
                        <a 
                            href="{{ route('sessions.index') }}" 
                            target="_blank"
                            class="flex items-center justify-between px-3 py-1.5 rounded-xl transition text-slate-400 hover:bg-slate-800/60 hover:text-white"
                        >
                            <span class="flex items-center gap-2.5">
                                <span>⚡</span>
                                <span>Student Runner</span>
                            </span>
                            <span class="text-[10px] text-slate-400">↗</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Admin Profile Footer -->
            <div class="p-3 border-t border-slate-800 shrink-0" style="background-color: #090E1A;">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900 border border-slate-800">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-[#0052FF] to-blue-400 text-white font-black text-xs flex items-center justify-center shrink-0">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <div class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Super Admin
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 text-slate-400 hover:text-red-400 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================================================================= -->
        <!-- 2. MOBILE DRAWER (Only rendered on <1024px screens when toggled)   -->
        <!-- ================================================================= -->
        <div 
            x-show="mobileSidebarOpen" 
            class="fixed inset-0 z-50 lg:hidden flex"
            style="display: none;"
        >
            <!-- Backdrop -->
            <div 
                x-show="mobileSidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/70 backdrop-blur-xs" 
                @click="mobileSidebarOpen = false"
            ></div>

            <!-- Slide-out Drawer Panel -->
            <div 
                class="relative flex flex-col w-72 max-w-[80vw] h-full text-slate-300 z-10 shadow-2xl"
                style="background-color: #0F172A;"
            >
                <div class="h-16 px-5 flex items-center justify-between border-b border-slate-800" style="background-color: #090E1A;">
                    <span class="text-sm font-black text-white">PSCRanker Admin</span>
                    <button type="button" @click="mobileSidebarOpen = false" class="p-2 text-slate-400 hover:text-white text-lg">✕</button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3 text-sm font-semibold">
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-xl text-white bg-[#0052FF]">📊 Dashboard</a>
                    <a href="{{ route('admin.sessions.index') }}" class="block px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800">📚 Learning Sessions</a>
                    <a href="{{ route('admin.sessions.create') }}" class="block px-3 py-2 rounded-xl text-amber-300 bg-amber-400/10 font-black">+ Create Session</a>
                    <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800">🏷️ Subject Tracks</a>
                    <a href="{{ route('admin.media.index') }}" class="block px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800">📁 Media Library</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800">👥 Candidates &amp; Subs</a>
                    <a href="{{ route('admin.affiliates.index') }}" class="block px-3 py-2 rounded-xl text-slate-300 hover:bg-slate-800">🤝 Affiliate Promoters</a>
                    <div class="pt-4 border-t border-slate-800">
                        <a href="{{ route('home') }}" target="_blank" class="block px-3 py-2 text-slate-400 text-xs">Public Website ↗</a>
                        <a href="{{ route('sessions.index') }}" target="_blank" class="block px-3 py-2 text-slate-400 text-xs">Student Runner ↗</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- 3. MAIN CONTENT CONTAINER (Always Beside Sidebar on Desktop)      -->
        <!-- ================================================================= -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen bg-slate-50">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20 shadow-2xs">
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button 
                        type="button" 
                        @click="mobileSidebarOpen = true"
                        class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div>
                        <h1 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-none">
                            @yield('page_title', 'Admin Console')
                        </h1>
                        <p class="text-[11px] font-medium text-slate-400 mt-0.5 hidden sm:block">
                            @yield('page_subtitle', 'Kerala PSC Track Management & Mission Control')
                        </p>
                    </div>
                </div>

                <!-- Right Quick Actions -->
                <div class="flex items-center gap-2.5">
                    <a 
                        href="{{ route('admin.sessions.create') }}" 
                        class="px-3.5 py-2 rounded-xl bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs shadow transition flex items-center gap-1.5 active:scale-95"
                    >
                        <span>＋ New Session</span>
                    </a>

                    <a 
                        href="{{ route('home') }}" 
                        target="_blank"
                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition hidden sm:flex items-center gap-1"
                    >
                        <span>View Web App</span>
                        <span class="text-[10px] text-slate-400">↗</span>
                    </a>
                </div>
            </header>

            <!-- Main Page Content Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

    @stack('scripts')
</body>
</html>
