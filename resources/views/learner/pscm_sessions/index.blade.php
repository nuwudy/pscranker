<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSC MicroLearning - Explore Lessons</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Malayalam:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Noto Sans Malayalam', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-indigo-50/20 to-purple-50/20 min-h-screen text-slate-800 antialiased flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-xl shadow-md shadow-indigo-100 group-hover:scale-105 transition">
                        ⚡
                    </div>
                    <div>
                        <span class="text-lg font-black text-slate-900 tracking-tight">PSCRanker</span>
                        <span class="block text-[10px] font-extrabold uppercase tracking-widest text-indigo-600">Micro-Learning</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-3">
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                        Admin Dashboard
                    </a>
                @endif
                <a href="{{ route('courses') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                    All Courses
                </a>
            </div>
        </div>
    </header>

    <!-- Hero / Category Header -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pt-10 pb-6 w-full">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-3">
                <span>🎯 Bite-Sized High-Yield Units</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                Master Kerala PSC in <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">5-Minute Sessions</span>
            </h1>
            <p class="text-slate-500 text-sm mt-3">
                Each micro-lesson tackles a previous PSC question, breaks down the core SCERT facts, and finishes with an instant OMR simulation test.
            </p>
        </div>

        <!-- Category Filter Pills Bar -->
        <div class="flex items-center justify-center gap-2 overflow-x-auto pb-3 scrollbar-none">
            <a href="{{ route('learner.pscm.index') }}" 
               class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ !$selectedCategory ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                All Categories ({{ \App\Models\PscmSession::where('status', 'published')->count() }})
            </a>
            @foreach($categories as $key => $label)
                @php
                    $count = \App\Models\PscmSession::where('status', 'published')->where('category', $key)->count();
                @endphp
                <a href="{{ route('learner.pscm.index', ['category' => $key]) }}" 
                   class="px-4 py-2 rounded-2xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ $selectedCategory === $key ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100' }}">
                    <span>{{ $key }}</span>
                    <span class="text-[10px] opacity-75 font-mono">({{ $count }})</span>
                </a>
            @endforeach
        </div>
    </section>

    @if(isset($continueSession))
        <!-- Resume Learning Card (Always pick up where left off) -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 mb-8 w-full">
            <div style="background: linear-gradient(135deg, #0052FF 0%, #3730A3 50%, #1E1B4B 100%); color: #ffffff;" 
                 class="rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden border border-indigo-400/40">
                <div class="absolute -right-10 -bottom-10 w-52 h-52 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-2 max-w-xl relative z-10">
                    <div style="background-color: rgba(255, 255, 255, 0.2); color: #ffffff;" 
                         class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Continue Practice</span>
                    </div>
                    <h2 style="color: #ffffff;" class="text-xl sm:text-2xl font-black tracking-tight leading-snug">
                        Unit #{{ $continueUnitNumber }}: {{ $continueSession->title }}
                    </h2>
                    <p style="color: #E0E7FF;" class="text-xs sm:text-sm font-medium">
                        {{ $continueSession->category ?? 'GK' }} • Pick up seamlessly from where you left off.
                    </p>
                </div>
                <div class="relative z-10 flex-shrink-0 w-full md:w-auto">
                    <a href="{{ route('learner.pscm.continue') }}" 
                       style="background-color: #FFD200; color: #020617;"
                       class="w-full md:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 font-black text-sm sm:text-base rounded-2xl shadow-lg shadow-black/20 hover:scale-[1.02] active:scale-95 transition border border-yellow-300">
                        <span>Resume Unit #{{ $continueUnitNumber }}</span>
                        <span class="text-lg">➔</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Sessions Grid -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 pb-20 w-full flex-1">
        @if($sessions->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm max-w-md mx-auto my-10">
                <span class="text-5xl block mb-4">📖</span>
                <h3 class="text-lg font-bold text-slate-900">No lessons available yet</h3>
                <p class="text-xs text-slate-500 mt-2">Lessons in this category are being prepared by the tutors. Check back soon or browse another category!</p>
                <a href="{{ route('learner.pscm.index') }}" class="mt-6 inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs shadow-md shadow-indigo-100">
                    View All Categories
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($sessions as $session)
                    @php
                        $unitIndex = \App\Http\Controllers\Learner\PscmSessionController::getSessionUnitNumber($session);
                        $isCurrent = isset($continueSession) && $continueSession->id === $session->id;
                    @endphp
                    <div class="bg-white rounded-3xl border {{ $isCurrent ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-slate-200/80' }} shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group hover:-translate-y-1">
                        
                        <div class="p-6">
                            {{-- Header meta --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <span class="font-black text-indigo-950">Unit #{{ $unitIndex }}</span>
                                    <span class="text-indigo-300">•</span>
                                    <span>{{ $categories[$session->category] ?? ($session->category ?? 'GK') }}</span>
                                </span>
                                @if($isCurrent)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200 animate-pulse">
                                        🎯 Up Next
                                    </span>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 flex items-center gap-1">
                                        <span>⏱️ ~5 mins</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Access Badge --}}
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <h3 class="text-base font-black text-slate-900 group-hover:text-indigo-600 transition leading-snug flex-1">
                                    <a href="{{ route('learner.pscm.show', $session->slug) }}">
                                        {{ $session->title }}
                                    </a>
                                </h3>
                                @if(($session->access_level ?? 'premium') === 'premium')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shrink-0">
                                        👑 PRO
                                    </span>
                                @elseif($session->access_level === 'registered')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 shrink-0">
                                        👤 Member
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                        🎁 Free
                                    </span>
                                @endif
                            </div>

                            {{-- Hook Question Snippet --}}
                            @if(is_array($session->hook_question) && isset($session->hook_question['question']))
                                <p class="text-xs text-slate-500 line-clamp-2 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    "{{ $session->hook_question['question'] }}"
                                </p>
                            @endif
                        </div>

                        {{-- Footer CTA --}}
                        <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                                <span>📝 OMR Test included</span>
                            </div>
                            <a href="{{ route('learner.pscm.show', $session->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white font-bold text-xs shadow-sm transition {{ ($session->access_level ?? 'premium') === 'premium' ? 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700' }}">
                                @if(($session->access_level ?? 'premium') === 'premium')
                                    <span>👑 Open PRO Lesson</span>
                                @elseif($session->access_level === 'registered')
                                    <span>Start Lesson</span>
                                @else
                                    <span>Start Free</span>
                                @endif
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            @if($sessions->hasPages())
                <div class="mt-10">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} PSCRanker MicroLearning Engine. All Rights Reserved.
    </footer>

</body>
</html>
