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

                <!-- Desktop Navigation Links: Decluttered around PSC Special Lessons -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-3 text-sm font-bold text-slate-700">
                    <a href="{{ route('sessions.index') }}" class="px-4 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('sessions.*') || request()->routeIs('session.*') || request()->routeIs('courses') ? 'text-[#0052FF] bg-blue-50 font-extrabold shadow-xs' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        <span>PSC Special Lessons</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-[#0052FF]">7 Subjects</span>
                    </a>
                    <a href="{{ route('map.study') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 {{ request()->routeIs('map.study') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        <span>Map &amp; Globe Lab</span>
                        <span class="text-xs">🌐</span>
                    </a>
                    <a href="{{ route('pricing') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('pricing') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50' }}">
                        Pro Pass 👑
                    </a>
                    <a href="{{ route('leaderboard') }}" class="px-3.5 py-2 rounded-xl transition {{ request()->routeIs('leaderboard') ? 'text-[#0052FF] bg-blue-50 font-extrabold' : 'hover:text-[#0052FF] hover:bg-slate-50 text-slate-600' }}">
                        Leaderboard 🏆
                    </a>
                </nav>

                <!-- Action CTA Buttons (Guest vs Authenticated Admin) -->
                <!-- Action CTA Buttons (Guest vs Authenticated Candidate vs Admin) -->
                <div class="hidden sm:flex items-center gap-2 lg:gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-bold text-slate-700 hover:text-[#0052FF] rounded-lg transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 py-2 text-sm font-extrabold text-[#0052FF] hover:bg-blue-50 rounded-lg transition border border-blue-200">
                            Register Free
                        </a>
                        <a href="{{ route('sessions.index') }}" class="px-4 py-2.5 text-sm font-extrabold text-slate-950 bg-[#FFD200] hover:bg-[#F5C500] active:scale-95 rounded-full shadow-sm hover:shadow-md transition-all flex items-center gap-1.5 border border-yellow-400">
                            <span>Start Lessons</span>
                            <span class="text-xs">⚡</span>
                        </a>
                    @endguest

                    @auth
                        @php
                            $user = Auth::user();
                            $isAdmin = ($user->email === 'admin@pscranker.com' || $user->phone === '9895940500' || ($user->is_admin ?? false));
                        @endphp

                        @if($isAdmin)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 text-xs font-black text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-slate-300">
                                    📊 Dashboard
                                </a>
                                <a href="{{ route('admin.sessions.index') }}" class="px-3 py-1.5 text-xs font-black text-[#0052FF] bg-blue-50 hover:bg-blue-100 rounded-lg transition border border-blue-200">
                                    ⚙️ Lessons Manager
                                </a>
                                <a href="{{ route('admin.media.index') }}" class="px-3 py-1.5 text-xs font-black text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition border border-purple-200">
                                    📁 Media
                                </a>
                                <span class="text-xs font-bold text-slate-600 hidden lg:inline">{{ $user->name }}</span>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center gap-2.5">
                                @if($user->isSubscribed())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 shadow-xs">
                                        👑 PRO PASS
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-900 border border-blue-200">
                                        🎓 Free Member
                                    </span>
                                @endif

                                <span class="text-xs font-extrabold text-slate-800">{{ $user->name }}</span>

                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Mobile Hamburger Menu Toggle -->
                <div class="flex items-center gap-2 md:hidden" x-data="{ open: false }">
                    <a href="{{ route('sessions.index') }}" class="px-3 py-1.5 text-xs font-black text-slate-950 bg-[#FFD200] rounded-full border border-yellow-400">
                        Lessons ⚡
                    </a>
                    <button @click="open = !open" class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Mobile Drawer Menu: Decluttered around Special Lessons -->
                    <div 
                        x-show="open" 
                        @click.outside="open = false" 
                        x-transition 
                        class="absolute top-20 left-0 right-0 bg-white border-b border-slate-200 shadow-2xl p-4 flex flex-col gap-2 z-50 text-base font-bold"
                        style="display: none;"
                    >
                        <a href="{{ route('sessions.index') }}" class="px-4 py-3 rounded-xl bg-blue-50 text-[#0052FF] flex items-center justify-between font-black">
                            <span>🎓 PSC Special Lessons</span>
                            <span class="text-xs bg-[#0052FF] text-white px-2.5 py-0.5 rounded-full font-mono">7 Subjects</span>
                        </a>
                        <a href="{{ route('map.study') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800 flex items-center justify-between font-bold">
                            <span class="flex items-center gap-2"><span>🌐</span> 3D Globe &amp; Map Lab</span>
                            <span class="text-[10px] bg-blue-100 text-[#0052FF] px-2 py-0.5 rounded-full font-bold uppercase">Spatial</span>
                        </a>
                        <a href="{{ route('pricing') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800 flex items-center justify-between">
                            <span>👑 Pro Pass &amp; Pricing</span>
                            <span class="text-xs text-amber-600 font-black">Save up to 30%</span>
                        </a>
                        <a href="{{ route('leaderboard') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🏆 Daily Leaderboard</a>
                        
                        @guest
                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                                <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl hover:bg-blue-50 text-slate-800">🔐 Login to Account</a>
                                <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl bg-blue-50 text-[#0052FF] font-black">⚡ Register Free Account</a>
                                <a href="{{ route('sessions.index') }}" class="w-full text-center py-3 bg-[#FFD200] font-black text-slate-950 rounded-xl shadow-xs">
                                    Start Lessons Free ⚡
                                </a>
                            </div>
                        @endguest

                        @auth
                            @php
                                $mobileUser = Auth::user();
                                $isMobileAdmin = ($mobileUser->email === 'admin@pscranker.com' || $mobileUser->phone === '9895940500' || ($mobileUser->is_admin ?? false));
                            @endphp

                            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                                @if($isMobileAdmin)
                                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-black">📊 Admin Dashboard</a>
                                    <a href="{{ route('admin.sessions.index') }}" class="px-4 py-2.5 rounded-xl bg-blue-50 text-[#0052FF] font-black">⚙️ Lessons Manager</a>
                                    <a href="{{ route('admin.media.index') }}" class="px-4 py-2.5 rounded-xl bg-purple-50 text-purple-700 font-black">📁 Media Library</a>
                                @else
                                    <div class="px-4 py-2 flex items-center justify-between">
                                        <span class="text-sm font-bold text-slate-800">{{ $mobileUser->name }}</span>
                                        @if($mobileUser->isSubscribed())
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900">PRO PASS</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-800">FREE MEMBER</span>
                                        @endif
                                    </div>
                                @endif

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 rounded-xl hover:bg-red-50">
                                        🚪 Logout ({{ $mobileUser->name }})
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

                <!-- Column 2: PSC Special Subjects -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">PSC Special Subjects</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('sessions.index') }}?subject=english" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>📖</span> <span>English (ഇംഗ്ലീഷ്)</span></a></li>
                        <li><a href="{{ route('sessions.index') }}?subject=maths" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>🔢</span> <span>Maths &amp; Reasoning (ഗണിതം)</span></a></li>
                        <li><a href="{{ route('sessions.index') }}?subject=science" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>🔬</span> <span>General Science (സയൻസ്)</span></a></li>
                        <li><a href="{{ route('sessions.index') }}?subject=history" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>🏛️</span> <span>History &amp; Renaissance (ചരിത്രം)</span></a></li>
                        <li><a href="{{ route('sessions.index') }}?subject=geography" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>🌍</span> <span>Geography (ഭൂമിശാസ്ത്രം)</span></a></li>
                        <li><a href="{{ route('sessions.index') }}?subject=current-affairs" class="hover:text-yellow-400 transition flex items-center gap-1.5"><span>📰</span> <span>Current Affairs (സമകാലികം)</span></a></li>
                    </ul>
                </div>

                <!-- Column 3: Razorpay Mandatory Compliance Links -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Policy &amp; Legal</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li>
                            <a href="{{ route('about') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>About Us</span>
                            </a>
                        </li>
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
                            <a href="{{ route('shipping-policy') }}" class="hover:text-yellow-400 transition flex items-center gap-1.5">
                                <span>Shipping &amp; Delivery Policy</span>
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
                            <a href="mailto:infopscranker@gmail.com" class="text-yellow-400 hover:underline font-mono">infopscranker@gmail.com</a>
                        </div>
                        <div>
                            <span class="block text-slate-300 font-bold">Phone / WhatsApp:</span>
                            <div class="flex flex-col gap-0.5 mt-0.5">
                                <a href="tel:+919895204224" class="text-white hover:text-yellow-400 font-mono font-bold">+91 9895 204 224</a>
                                <a href="https://wa.me/919895204224" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:underline text-[11px]">Chat on WhatsApp ➔</a>
                            </div>
                        </div>
                        <div>
                            <span class="block text-slate-300 font-bold">Operating Hours:</span>
                            <span>Mon – Sat: 9:00 AM – 7:00 PM IST</span>
                        </div>
                        <div>
                            <span class="block text-slate-300 font-bold">Address:</span>
                            <span class="text-[11px] leading-tight block">Door No. 4/122, Civil Station Road, Kozhikode, Kerala - 673020, India</span>
                        </div>
                        <div class="pt-1 text-[10px] text-slate-500">
                            PSCRanker.com is an independent learning portal and is not affiliated with the official Kerala PSC.
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Legal Bar -->
            <div class="pt-6 border-t border-slate-800/80 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <span>&copy; {{ date('Y') }} PSCRANKER.com. All rights reserved.</span>
                    <a href="{{ route('about') }}" class="hover:underline">About</a>
                    <a href="{{ route('terms') }}" class="hover:underline">Terms</a>
                    <a href="{{ route('privacy') }}" class="hover:underline">Privacy</a>
                    <a href="{{ route('refund-policy') }}" class="hover:underline">Refunds</a>
                    <a href="{{ route('shipping-policy') }}" class="hover:underline">Shipping</a>
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

    function pscGlobalModal() {
        return {
            isOpen: false,
            modalData: {
                type: 'celebration',
                icon: '🏆',
                badge: '🎉 Capsule Completed!',
                title: 'Congratulations!',
                titleMalayalam: 'കലക്കി! മികച്ച മുന്നേറ്റം! 🚀',
                message: '',
                xp: 0,
                nextUrl: '',
                confirmText: 'Continue ➔',
                cancelText: 'Stay Here',
                showCancel: false,
                showRetake: false,
                retakeText: '🔄 Retake Unit (വീണ്ടും ചെയ്യുക)',
                allowBackdropClose: true,
                onConfirm: null,
                onRetake: null
            },
            init() {
                window.showPscModal = (opts) => {
                    const isCelebration = (opts.type === 'celebration') || (opts.xp && opts.xp > 0);
                    this.modalData = Object.assign({
                        type: isCelebration ? 'celebration' : (opts.type || 'info'),
                        icon: opts.icon || (opts.type === 'error' ? '⚠️' : (opts.type === 'success' ? '✅' : (isCelebration ? '🏆' : '⚡'))),
                        badge: opts.badge || (isCelebration ? '🎉 Unit Completed!' : 'PSC Ranker Notice'),
                        title: opts.title || (isCelebration ? 'Congratulations, PSC Ranker!' : 'Notice'),
                        titleMalayalam: opts.titleMalayalam || (isCelebration ? 'കലക്കി! മികച്ച മുന്നേറ്റം! 🚀' : ''),
                        message: opts.message || '',
                        xp: opts.xp || 0,
                        nextUrl: opts.nextUrl || '',
                        confirmText: opts.confirmText || (opts.nextUrl ? 'അടുത്ത പാഠത്തിലേക്ക് പോകാം (Next Unit) ➔' : 'Awesome, Got It! ⚡'),
                        cancelText: opts.cancelText || 'ഇവിടെ തുടരുക (Stay Here)',
                        showCancel: opts.showCancel !== undefined ? opts.showCancel : !!opts.nextUrl,
                        showRetake: opts.showRetake !== undefined ? opts.showRetake : isCelebration,
                        retakeText: opts.retakeText || '🔄 ഈ യൂണിറ്റ് വീണ്ടും ചെയ്യുക (Retake Unit)',
                        allowBackdropClose: opts.allowBackdropClose !== false,
                        onConfirm: opts.onConfirm || null,
                        onRetake: opts.onRetake || null
                    }, opts);

                    this.isOpen = true;

                    if (this.modalData.type === 'celebration' || this.modalData.xp > 0) {
                        if (window.confetti) {
                            window.confetti({ particleCount: 150, spread: 90, origin: { y: 0.55 } });
                        }
                        if (window.PscSound && window.PscSound.playFanfare) {
                            window.PscSound.playFanfare();
                        }
                    } else if (this.modalData.type === 'success' && window.PscSound && window.PscSound.playCorrect) {
                        window.PscSound.playCorrect();
                    }
                };

                // Gracefully override window.alert so even standard alerts look stunning and colorful!
                window.alert = (msg) => {
                    const isCelebration = typeof msg === 'string' && (msg.includes('Congratulations') || msg.includes('completed') || msg.includes('XP'));
                    const xpMatch = typeof msg === 'string' ? msg.match(/\+(\d+)\s*XP/i) : null;
                    const xp = xpMatch ? parseInt(xpMatch[1]) : (isCelebration ? 250 : 0);

                    window.showPscModal({
                        type: isCelebration ? 'celebration' : 'info',
                        icon: isCelebration ? '🏆' : '⚡',
                        badge: isCelebration ? '🎉 Unit Completed!' : 'PSC Ranker',
                        title: isCelebration ? 'Congratulations, PSC Ranker!' : 'Notification',
                        titleMalayalam: isCelebration ? 'കലക്കി! മികച്ച മുന്നേറ്റം! 🚀' : '',
                        message: typeof msg === 'string' ? msg.replace(/🎉\s*/g, '') : String(msg),
                        xp: xp,
                        confirmText: 'Awesome, Got It! ⚡',
                        showCancel: false
                    });
                };
            },
            confirm() {
                const url = this.modalData.nextUrl;
                const cb = this.modalData.onConfirm;
                this.isOpen = false;
                if (cb && typeof cb === 'function') {
                    cb();
                }
                if (url) {
                    window.location.href = url;
                }
            },
            retake() {
                const onRetakeCb = this.modalData.onRetake;
                this.isOpen = false;
                if (onRetakeCb && typeof onRetakeCb === 'function') {
                    onRetakeCb();
                } else if (window.PSCRanker && typeof window.PSCRanker.retakeSession === 'function') {
                    window.PSCRanker.retakeSession();
                }
            },
            close() {
                this.isOpen = false;
            }
        };
    }
    </script>
    <script src="{{ asset('js/psc-globe.js') }}"></script>

    <!-- ========================================================================= -->
    <!-- GLOBAL COLORFUL CELEBRATION & ALERT MODAL                                 -->
    <!-- ========================================================================= -->
    <div 
        x-data="pscGlobalModal()" 
        x-cloak
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="close()"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6 select-none"
        style="display: none;"
    >
        <!-- Backdrop with Blur -->
        <div 
            class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
            @click="modalData.allowBackdropClose ? close() : null"
        ></div>

        <!-- Colorful Modal Card -->
        <div 
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-90 translate-y-6"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-6"
            class="relative w-full max-w-md bg-white rounded-3xl border-2 shadow-2xl p-6 sm:p-8 text-center overflow-hidden z-10"
            :class="{
                'border-amber-400 ring-8 ring-yellow-400/25 shadow-yellow-500/30': modalData.type === 'celebration',
                'border-emerald-400 ring-8 ring-emerald-400/25 shadow-emerald-500/30': modalData.type === 'success',
                'border-blue-400 ring-8 ring-blue-400/25 shadow-blue-500/30': modalData.type === 'info',
                'border-red-400 ring-8 ring-red-400/25 shadow-red-500/30': modalData.type === 'error'
            }"
        >
            <!-- Glowing Background Orbs -->
            <div class="absolute -top-14 -right-14 w-40 h-40 bg-gradient-to-br from-yellow-300/40 via-amber-400/30 to-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-14 -left-14 w-40 h-40 bg-gradient-to-tr from-blue-400/30 via-indigo-400/20 to-purple-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Animated Header Badge / Trophy Icon -->
            <div class="relative mx-auto mb-3">
                <div 
                    class="w-20 h-20 rounded-3xl flex items-center justify-center text-4xl mx-auto shadow-xl transition transform hover:scale-105"
                    :class="{
                        'bg-gradient-to-tr from-amber-400 via-yellow-400 to-amber-500 shadow-yellow-400/50 text-slate-950 animate-bounce': modalData.type === 'celebration',
                        'bg-gradient-to-tr from-emerald-400 to-teal-500 shadow-emerald-400/40 text-white': modalData.type === 'success',
                        'bg-gradient-to-tr from-[#0052FF] to-indigo-600 shadow-blue-400/40 text-white': modalData.type === 'info',
                        'bg-gradient-to-tr from-red-500 to-rose-600 shadow-red-400/40 text-white': modalData.type === 'error'
                    }"
                    x-text="modalData.icon"
                ></div>
            </div>

            <!-- Top Pill Badge -->
            <template x-if="modalData.badge">
                <div class="mb-2">
                    <span 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border shadow-2xs"
                        :class="{
                            'bg-yellow-100 text-yellow-900 border-yellow-300': modalData.type === 'celebration',
                            'bg-emerald-100 text-emerald-900 border-emerald-300': modalData.type === 'success',
                            'bg-blue-100 text-blue-900 border-blue-300': modalData.type === 'info',
                            'bg-red-100 text-red-900 border-red-300': modalData.type === 'error'
                        }"
                        x-text="modalData.badge"
                    ></span>
                </div>
            </template>

            <!-- Main Heading -->
            <h3 class="text-xl sm:text-2xl font-black text-slate-950 tracking-tight leading-snug" x-text="modalData.title"></h3>

            <!-- Malayalam Cheer / Micro-copy -->
            <template x-if="modalData.titleMalayalam">
                <p class="text-sm sm:text-base font-bold text-[#0052FF] mt-1 font-['Noto_Sans_Malayalam']" x-text="modalData.titleMalayalam"></p>
            </template>

            <!-- XP Reward Banner (if XP > 0) -->
            <template x-if="modalData.xp && modalData.xp > 0">
                <div class="my-4 p-3.5 bg-gradient-to-r from-amber-50 via-yellow-100 to-amber-50 border-2 border-amber-300 rounded-2xl flex items-center justify-center gap-2.5 shadow-inner">
                    <span class="text-2xl animate-pulse">⚡</span>
                    <span class="font-mono font-black text-xl text-amber-950">+<span x-text="modalData.xp"></span> XP</span>
                    <span class="text-[10px] font-black uppercase tracking-wide px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900">Rank Bonus Earned</span>
                </div>
            </template>

            <!-- Description Body Message -->
            <p class="text-xs sm:text-sm text-slate-600 font-medium mt-2 leading-relaxed" x-text="modalData.message"></p>

            <!-- Action Buttons -->
            <div class="mt-6 space-y-2.5">
                <button 
                    type="button" 
                    @click="confirm()"
                    class="w-full py-3.5 px-5 font-black text-sm uppercase tracking-wider rounded-xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                    :class="{
                        'bg-gradient-to-r from-yellow-400 via-amber-400 to-yellow-500 hover:from-yellow-300 hover:to-amber-400 text-slate-950 shadow-yellow-500/30 border border-yellow-300': modalData.type === 'celebration',
                        'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white shadow-emerald-500/30': modalData.type === 'success',
                        'bg-gradient-to-r from-[#0052FF] to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white shadow-blue-500/30': modalData.type === 'info',
                        'bg-gradient-to-r from-red-600 to-rose-700 hover:from-red-700 hover:to-rose-800 text-white shadow-red-500/30': modalData.type === 'error'
                    }"
                >
                    <span x-text="modalData.confirmText"></span>
                </button>

                <template x-if="modalData.showRetake">
                    <button 
                        type="button" 
                        @click="retake()"
                        class="w-full py-3 px-4 font-black text-xs sm:text-sm rounded-xl bg-blue-50 hover:bg-blue-100 text-[#0052FF] border-2 border-blue-300 shadow-sm transition active:scale-95 flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span x-text="modalData.retakeText || '🔄 ഈ യൂണിറ്റ് വീണ്ടും ചെയ്യുക (Retake Unit)'"></span>
                    </button>
                </template>

                <template x-if="modalData.showCancel || modalData.nextUrl">
                    <button 
                        type="button" 
                        @click="close()"
                        class="w-full py-2.5 px-4 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition cursor-pointer"
                        x-text="modalData.cancelText"
                    ></button>
                </template>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
