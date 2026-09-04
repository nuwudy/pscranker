<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'PSCRanker.com — Crack Kerala PSC with Super Speed!')</title>
    <meta name="description" content="Gamified Kerala PSC exam prep with 3-minute rapid speed drills, Malayalam meme mnemonics, OMR bubble simulator, and real-time negative marking training.">
    <meta name="theme-color" content="#0052FF">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="48x48" href="/images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- PWA Web App Manifest & Apple Mobile Tags -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="PSCRanker">

    <!-- Social Share & Open Graph Meta Tags (WhatsApp, Telegram, Facebook, Twitter) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="PSCRanker.com">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'PSCRanker.com — Crack Kerala PSC with Super Speed!')">
    <meta property="og:description" content="Gamified Kerala PSC exam prep with 3-minute rapid speed drills, Malayalam meme mnemonics, OMR bubble simulator, and real-time negative marking training.">
    <meta property="og:image" content="{{ asset('images/og-share.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'PSCRanker.com — Crack Kerala PSC with Super Speed!')">
    <meta name="twitter:description" content="Gamified Kerala PSC exam prep with 3-minute rapid speed drills, Malayalam meme mnemonics, OMR bubble simulator, and real-time negative marking training.">
    <meta name="twitter:image" content="{{ asset('images/og-share.png') }}">

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
                
                <!-- Logo: Official Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group transition-transform active:scale-95 py-1">
                    <img src="{{ asset('images/logo.png') }}" alt="PSCRanker.com" class="h-9 sm:h-11 w-auto object-contain">
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
                    <a href="{{ route('pricing') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('pricing') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        Pricing ⚡
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
                        <a href="{{ route('pricing') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-[#0052FF] font-black">⚡ Pricing &amp; Prepaid Plans</a>
                        
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

    <!-- ============================================================= -->
    <!-- FOOTER WITH RAZORPAY COMPLIANCE, POLICIES & PAYMENT BADGES -->
    <!-- ============================================================= -->
    <footer class="bg-slate-950 text-slate-300 border-t border-slate-800 pt-14 pb-10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
                
                <!-- Brand Info & Mission (Col 1-2) -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-2xl text-yellow-400">⚡</span>
                        <span class="text-2xl font-black text-white">PSC<span class="text-blue-400">RANKER</span><span class="text-amber-400">.com</span></span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-400 max-w-sm leading-relaxed mb-4">
                        Kerala's gamified competitive exam training ecosystem. Real-time OMR simulation with negative marking penalty practice, Malayalam meme mnemonics, and speed drills.
                    </p>

                    <!-- Razorpay Accepted Payments Badge -->
                    <div class="p-3.5 bg-slate-900 rounded-2xl border border-slate-800 max-w-md">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-300 mb-2">
                            <span>100% Secure Payments</span>
                            <span class="text-blue-400 flex items-center gap-1 font-mono">
                                🔒 Razorpay Verified
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[10px] font-mono">
                            <span class="px-2 py-1 bg-slate-800 text-yellow-400 rounded-md font-black">UPI</span>
                            <span class="px-2 py-1 bg-slate-800 text-emerald-400 rounded-md font-black">PhonePe</span>
                            <span class="px-2 py-1 bg-slate-800 text-blue-400 rounded-md font-black">Google Pay</span>
                            <span class="px-2 py-1 bg-slate-800 text-amber-300 rounded-md font-black">Paytm</span>
                            <span class="px-2 py-1 bg-slate-800 text-slate-200 rounded-md font-black">RuPay</span>
                            <span class="px-2 py-1 bg-slate-800 text-slate-200 rounded-md font-black">Visa/MC</span>
                            <span class="px-2 py-1 bg-slate-800 text-purple-300 rounded-md font-black">NetBanking</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Gamified Modules -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Training Engine</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="{{ route('courses') }}" class="hover:text-yellow-400 transition">4-Phase Course Units</a></li>
                        <li><a href="{{ route('drill.show') }}" class="hover:text-yellow-400 transition">3-Min Speed Blitz</a></li>
                        <li><a href="{{ route('omr.simulator') }}" class="hover:text-yellow-400 transition">OMR Bubble Simulator</a></li>
                        <li><a href="{{ route('memebank') }}" class="hover:text-yellow-400 transition">Meme Mnemonics Vault</a></li>
                        <li><a href="{{ route('leaderboard') }}" class="hover:text-yellow-400 transition">Daily Speed Duel</a></li>
                    </ul>
                </div>

                <!-- Column 3: Razorpay Mandatory Compliance Links -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Policy &amp; Legal</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li>
                            <a href="{{ route('terms') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Terms &amp; Conditions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Privacy Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('refund-policy') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Cancellation &amp; Refund Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pricing') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Prepaid Plans &amp; Pricing</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Contact Us &amp; Grievance</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Contact & Grievance Details -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Support Desk</h4>
                    <div class="space-y-2.5 text-xs text-slate-400">
                        <div>
                            <span class="block text-slate-300 font-bold">Email Support:</span>
                            <a href="mailto:admin@pscranker.com" class="text-[#0052FF] hover:underline font-mono">admin@pscranker.com</a>
                        </div>
                        <div>
                            <span class="block text-slate-300 font-bold">Operating Hours:</span>
                            <span>Mon – Sat: 9:00 AM – 7:00 PM</span>
                        </div>
                        <div>
                            <span class="block text-slate-300 font-bold">Location:</span>
                            <span>Kerala, India</span>
                        </div>
                        <div class="pt-2 text-[10px] text-slate-500">
                            PSCRanker.com is an independent learning portal and is not affiliated with the official Kerala PSC.
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Legal Bar -->
            <div class="pt-6 border-t border-slate-800/80 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex flex-wrap items-center gap-4">
                    <span>&copy; {{ date('Y') }} PSCRANKER.com. All rights reserved.</span>
                    <a href="{{ route('terms') }}" class="hover:underline">Terms</a>
                    <a href="{{ route('privacy') }}" class="hover:underline">Privacy</a>
                    <a href="{{ route('refund-policy') }}" class="hover:underline">Refunds</a>
                    <a href="{{ route('contact') }}" class="hover:underline">Contact</a>
                </div>
                <div class="text-slate-400 font-semibold font-['Noto_Sans_Malayalam']">
                    പഠിക്കാം, ജയിക്കാം, ജോലി വാങ്ങാം! 🚀
                </div>
            </div>

        </div>
    </footer>

    <!-- ============================================================= -->
    <!-- PWA INSTALL FLOATING BANNER / BUTTON (Auto-hides if installed)-->
    <!-- ============================================================= -->
    <div 
        x-data="pwaInstaller()"
        x-init="initPwa()"
        x-show="shouldShow()"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-12 opacity-0 scale-95"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100 scale-100"
        x-transition:leave-end="translate-y-12 opacity-0 scale-95"
        class="fixed bottom-4 left-3 right-3 sm:left-auto sm:right-6 sm:bottom-6 z-50 max-w-md"
        style="display: none;"
    >
        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 text-white rounded-2xl p-3.5 sm:p-4 shadow-2xl border-2 border-yellow-400/90 flex items-center justify-between gap-3 relative overflow-hidden ring-4 ring-black/10">
            
            <!-- Glow Accent -->
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-500/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="flex items-center gap-3 relative z-10 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-[#0052FF] to-blue-600 text-white flex items-center justify-center text-xl font-black shrink-0 shadow-md border border-white/20">
                    ⚡
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs sm:text-sm font-black text-white truncate">Install PSCRanker App</h4>
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-yellow-400 text-slate-950">Free</span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-300 font-medium truncate mt-0.5">
                        <span x-show="!isIos">One-click launch &amp; offline practice</span>
                        <span x-show="isIos">Tap Share <span class="text-yellow-300 font-bold">⎋</span> &amp; <span class="text-yellow-300 font-bold">"Add to Home Screen"</span></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 relative z-10">
                <button 
                    type="button"
                    @click="installApp()" 
                    class="px-4 py-2 bg-[#FFD200] hover:bg-yellow-400 active:scale-95 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5 border border-yellow-300"
                >
                    <span x-text="isIos ? 'How to Add 📲' : 'Install 📲'"></span>
                </button>

                <button 
                    type="button"
                    @click="dismiss()" 
                    class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 text-slate-400 hover:text-white flex items-center justify-center text-xs transition"
                    title="Dismiss"
                >
                    ✕
                </button>
            </div>

        </div>
    </div>

    <!-- iOS / Desktop Manual Install Helper Modal -->
    <div 
        x-data="{ showModal: false }"
        @open-install-guide.window="showModal = true"
        x-show="showModal"
        x-transition 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
        style="display: none;"
    >
        <div @click.outside="showModal = false" class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl border border-slate-200">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 text-[#0052FF] flex items-center justify-center text-2xl mx-auto mb-3">
                📲
            </div>
            <h3 class="text-base font-black text-slate-900">Install PSCRanker</h3>
            <p class="text-xs text-slate-600 mt-1">
                Install as a native application on your PC, Android, or iPhone for instant access and zero distraction.
            </p>

            <div class="my-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-left text-xs font-medium space-y-2 text-slate-700">
                <div class="flex items-start gap-2">
                    <span class="font-bold text-[#0052FF]">iPhone/iPad:</span>
                    <span>Tap <strong>Share ⎋</strong> at bottom of Safari, then scroll down and tap <strong>"Add to Home Screen ⊞"</strong>.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-[#0052FF]">PC / Mac:</span>
                    <span>Click the <strong>Install (⊕ or 📥)</strong> icon in the right corner of your browser's address bar.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="font-bold text-[#0052FF]">Android:</span>
                    <span>Tap <strong>Install</strong> on the banner or tap the 3-dots menu <strong>⋮</strong> ➔ <strong>"Add to Home screen"</strong>.</span>
                </div>
            </div>

            <button type="button" @click="showModal = false" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs rounded-xl transition">
                Got It! 👍
            </button>
        </div>
    </div>

    <script>
    function pwaInstaller() {
        return {
            deferredPrompt: null,
            isInstalled: false,
            isDismissed: false,
            isIos: false,
            canInstallPrompt: false,

            initPwa() {
                // Remove legacy persistent localStorage flag that blocked reappearing after uninstall
                try {
                    localStorage.removeItem('pscranker_pwa_installed');
                } catch (e) {}

                // 1. Detect if currently running inside the installed standalone PWA window
                const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
                    || window.navigator.standalone === true 
                    || (document.referrer && document.referrer.includes('android-app://'))
                    || window.location.search.includes('source=pwa');

                if (isStandalone) {
                    this.isInstalled = true;
                    return; // Inside standalone app window, do not show install prompt
                }

                // 2. Check if user dismissed recently in this browser session
                if (sessionStorage.getItem('pscranker_install_dismissed') === 'true') {
                    this.isDismissed = true;
                }

                // 3. Detect iOS Safari
                const ua = window.navigator.userAgent.toLowerCase();
                this.isIos = /iphone|ipad|ipod/.test(ua) && !window.MSStream;

                // 4. Capture native beforeinstallprompt (Android Chrome, Windows Edge/Chrome, etc.)
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.canInstallPrompt = true;
                    // Prompt is actively available, which means app is NOT currently installed!
                    this.isInstalled = false;
                    this.isDismissed = false; // Always re-show when installable
                });

                // 5. Query modern getInstalledRelatedApps API if supported
                if ('getInstalledRelatedApps' in navigator) {
                    navigator.getInstalledRelatedApps().then((relatedApps) => {
                        if (relatedApps && relatedApps.length > 0) {
                            if (!this.deferredPrompt) {
                                this.isInstalled = true;
                            }
                        } else {
                            this.isInstalled = false;
                        }
                    }).catch(() => {});
                }

                // 6. When app installation completes, hide immediately
                window.addEventListener('appinstalled', () => {
                    this.isInstalled = true;
                    this.deferredPrompt = null;
                });
            },

            shouldShow() {
                // If running inside the standalone app, never show!
                if (this.isInstalled) return false;
                if (this.isDismissed) return false;

                return true;
            },

            async installApp() {
                if (this.deferredPrompt) {
                    this.deferredPrompt.prompt();
                    const choiceResult = await this.deferredPrompt.userChoice;
                    if (choiceResult && choiceResult.outcome === 'accepted') {
                        this.isInstalled = true;
                    }
                    this.deferredPrompt = null;
                } else {
                    // Open visual guide for iOS or desktop address bar install
                    window.dispatchEvent(new CustomEvent('open-install-guide'));
                }
            },

            dismiss() {
                this.isDismissed = true;
                sessionStorage.setItem('pscranker_install_dismissed', 'true');
            }
        };
    }
    </script>

    @stack('scripts')
</body>
</html>
