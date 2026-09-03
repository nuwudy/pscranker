<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'PSCRanker.com — Crack Kerala PSC with Super Speed!')</title>
    <meta name="description" content="Gamified Kerala PSC exam prep with 3-minute rapid speed drills, Malayalam meme mnemonics, OMR bubble simulator, and real-time negative marking training.">
    <meta name="theme-color" content="#0052FF">

    <!-- PWA Web App Manifest & Apple Mobile Tags -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/mascot.jpg">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="PSCRanker">

    <!-- Google Fonts: Outfit & Noto Sans Malayalam -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Noto+Sans+Malayalam:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gradient-to-b from-[#F0F5FF] via-white to-[#F6F9FE] text-slate-900 min-h-screen flex flex-col antialiased selection:bg-yellow-400 selection:text-slate-900">

    <!-- Top Announcement Live Ticker (Gamified Ticker) -->
    <div class="bg-slate-950 text-white text-xs font-semibold py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between overflow-hidden">
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-400 text-slate-950 uppercase tracking-wide">Live</span>
                <span class="text-slate-400 hidden sm:inline">Daily Speed Duel:</span>
            </div>
            <div class="overflow-x-auto no-scrollbar flex items-center gap-6 whitespace-nowrap text-slate-300 mx-4 text-xs font-medium">
                <span class="flex items-center gap-1.5"><span class="text-amber-400">🔥</span> <strong>1,420+</strong> candidates drilling right now</span>
                <span class="hidden md:flex items-center gap-1.5"><span class="text-blue-400">⚡</span> <strong>45,820</strong> PSC traps avoided today</span>
                <span class="hidden lg:flex items-center gap-1.5"><span class="text-emerald-400">🎯</span> Negative marking penalty prevented: <strong>-15,200 marks</strong></span>
                <span class="flex items-center gap-1.5 text-yellow-300">⏳ Today's Leaderboard resets at 11:59 PM</span>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <!-- Sound Mute Toggle -->
                <button 
                    x-data="{ isMuted: localStorage.getItem('pscranker_sound_muted') === 'true' }"
                    @click="isMuted = window.PscSound.toggleMute()" 
                    class="text-slate-400 hover:text-white transition flex items-center gap-1 text-[11px]"
                    title="Toggle sound effects"
                >
                    <span x-show="!isMuted" class="flex items-center gap-1">🔊 <span class="hidden sm:inline">Sound ON</span></span>
                    <span x-show="isMuted" class="flex items-center gap-1 text-slate-500">🔇 <span class="hidden sm:inline">Sound OFF</span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar (Matching Behance mockup: Electric Blue & White header) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-blue-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo: ⚡ PSCRANKER.com (Behance Exact Style) -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group transition-transform active:scale-95">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#0052FF] to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/25 group-hover:shadow-blue-500/40 transition">
                        <span class="text-2xl transform group-hover:rotate-12 transition">⚡</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center tracking-tight">
                            <span class="text-2xl sm:text-3xl font-black text-[#0052FF] tracking-tighter">PSC</span>
                            <span class="text-2xl sm:text-3xl font-black text-[#0052FF] tracking-tighter">RANKER</span>
                            <span class="text-lg sm:text-xl font-black text-amber-500">.com</span>
                            <span class="text-xs text-yellow-500 ml-0.5">★</span>
                        </div>
                        <span class="text-[9px] font-bold tracking-wider text-slate-500 uppercase -mt-1 hidden sm:block">Kerala PSC Gamified Drill</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Behance Mockup: Home, Courses, Speed Drills, Leaderboard, MemeBank, My Profile) -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 text-sm font-bold text-slate-700">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        Home
                    </a>
                    <a href="{{ route('courses') }}" class="px-3.5 py-2 rounded-lg transition hover:text-[#0052FF] hover:bg-slate-50 text-slate-600">
                        Courses
                    </a>
                    <a href="{{ route('drill.show') }}" class="px-3.5 py-2 rounded-lg transition relative {{ request()->routeIs('drill.*') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        Speed Drills
                        <span class="absolute -top-1 right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                    </a>
                    <a href="{{ route('leaderboard') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('leaderboard') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        Leaderboard
                    </a>
                    <a href="{{ route('memebank') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('memebank') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        MemeBank
                    </a>
                    <a href="{{ route('omr.simulator') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('omr.*') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        OMR Practice
                    </a>
                </nav>

                <!-- Action CTA Buttons (Guest vs Authenticated Admin) -->
                <div class="hidden sm:flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-700 hover:text-[#0052FF] rounded-lg transition">
                            Login
                        </a>
                        <a href="{{ route('drill.show') }}" class="px-5 py-2.5 text-sm font-extrabold text-slate-950 bg-[#FFD200] hover:bg-[#F5C500] active:scale-95 rounded-full shadow-sm hover:shadow-md transition-all flex items-center gap-1.5 border border-yellow-400">
                            <span>Sign Up Free</span>
                            <span class="text-xs">⚡</span>
                        </a>
                    @endguest

                    @auth
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 text-xs font-black text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-slate-300">
                                📊 Dashboard
                            </a>
                            <a href="{{ route('admin.sessions.index') }}" class="px-3 py-1.5 text-xs font-black text-[#0052FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition border border-blue-200">
                                ⚙️ Sessions
                            </a>
                            <a href="{{ route('admin.media.index') }}" class="px-3 py-1.5 text-xs font-black text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition border border-purple-200">
                                📁 Media Library
                            </a>
                            <span class="text-xs font-bold text-slate-600 hidden lg:inline">{{ Auth::user()->name }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-100 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Hamburger Menu Toggle -->
                <div class="flex items-center gap-2 md:hidden" x-data="{ open: false }">
                    <a href="{{ route('drill.show') }}" class="px-3 py-1.5 text-xs font-black text-slate-950 bg-[#FFD200] rounded-full border border-yellow-400">
                        Drill Now ⚡
                    </a>
                    <button @click="open = !open" class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Mobile Drawer Menu -->
                    <div 
                        x-show="open" 
                        @click.outside="open = false" 
                        x-transition 
                        class="absolute top-20 left-0 right-0 bg-white border-b border-slate-200 shadow-2xl p-4 flex flex-col gap-2 z-50 text-base font-bold"
                        style="display: none;"
                    >
                        <a href="{{ route('home') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🏠 Home</a>
                        <a href="{{ route('sessions.index') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🎓 Micro-Learning Sessions</a>
                        <a href="{{ route('drill.show') }}" class="px-4 py-2.5 rounded-xl bg-blue-50 text-[#0052FF] flex items-center justify-between font-black">
                            <span>⚡ 3-Min Speed Drills</span>
                            <span class="text-xs bg-amber-400 text-slate-950 px-2 py-0.5 rounded-full">Active</span>
                        </a>
                        <a href="{{ route('leaderboard') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🏆 Daily Leaderboard</a>
                        <a href="{{ route('memebank') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">😂 MemeBank Mnemonics</a>
                        <a href="{{ route('omr.simulator') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">📝 OMR Bubble Simulator</a>
                        
                        @guest
                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                                <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🔐 Login to Account</a>
                                <a href="{{ route('drill.show') }}" class="w-full text-center py-3 bg-[#FFD200] font-black text-slate-950 rounded-xl">
                                    Start Free Drill
                                </a>
                            </div>
                        @endguest

                        @auth
                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-black">📊 Admin Dashboard</a>
                                <a href="{{ route('admin.sessions.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-50 text-[#0052FF] font-black">⚙️ Manage Sessions</a>
                                <a href="{{ route('admin.media.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-50 text-purple-700 font-black">📁 Media Library</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 rounded-xl hover:bg-red-50">
                                        🚪 Logout ({{ Auth::user()->name }})
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer with Kerala PSC Disclaimer & Quick Links -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 pt-12 pb-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-2xl text-yellow-400">⚡</span>
                        <span class="text-2xl font-black text-white">PSC<span class="text-blue-400">RANKER</span><span class="text-amber-400">.com</span></span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        Kerala's first high-energy gamified exam preparation platform. We turn dry PSC textbooks into fast-paced speed drills, funny meme mnemonics, and real-time negative marking mastery!
                    </p>
                    <p class="text-xs text-slate-500 mt-2 font-mono">
                        Designed for LDC, Secretariat Assistant, CPO, LGS, KAS & Fireman aspirants.
                    </p>
                </div>

                <!-- Fast Links -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Gamified Prep</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('drill.show') }}" class="hover:text-yellow-400 transition">3-Min Rapid Fire Drill</a></li>
                        <li><a href="{{ route('omr.simulator') }}" class="hover:text-yellow-400 transition">OMR Bubble Simulator</a></li>
                        <li><a href="{{ route('memebank') }}" class="hover:text-yellow-400 transition">Meme Mnemonics Vault</a></li>
                        <li><a href="{{ route('leaderboard') }}" class="hover:text-yellow-400 transition">Daily Speed Duel</a></li>
                    </ul>
                </div>

                <!-- Disclaimer & Tech -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Community & Safety</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        PSCRanker.com is an independent community learning portal. Not affiliated with the official Kerala Public Service Commission (KPSC).
                    </p>
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>All systems operational on OpenLiteSpeed</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 text-center text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-3">
                <span>&copy; {{ date('Y') }} PSCRANKER.com. All rights reserved. Crafted with passion for Malayali exam warriors.</span>
                <span class="text-slate-400 font-semibold">പഠിക്കാം, ജയിക്കാം, ജോലി വാങ്ങാം! 🚀</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
