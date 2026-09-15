@extends('layouts.app')

@section('title', 'About Us — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">About PSCRanker</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kerala's High-Velocity Gamified Exam Preparation &amp; Ranking Ecosystem</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-8">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    Our Mission
                </h2>
                <p>
                    <strong>PSCRanker.com</strong> was founded with a singular purpose: to revolutionize how candidates prepare for competitive examinations conducted by the <strong>Kerala Public Service Commission (KPSC)</strong>. Traditional preparation often suffers from boring rote memorization, lack of exam-hall time discipline, and catastrophic negative marking traps.
                </p>
                <p class="mt-2">
                    We replace tedious cramming with rapid 3-minute interactive speed drills, 4-phase micro-learning capsules, real-time OMR bubble simulators with penalty calculations, and culturally rooted Malayalam meme mnemonics that make high-yield facts unforgettable.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FFD200]"></span>
                    Core Features &amp; Learning Engines
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                        <div class="text-2xl mb-2">⚡</div>
                        <h3 class="font-black text-slate-900 text-sm mb-1">3-Minute Rapid Speed Drills</h3>
                        <p class="text-xs text-slate-600">Train neural reaction times against countdown timers to master fast-paced PSC prelims &amp; mains papers.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100">
                        <div class="text-2xl mb-2">🎯</div>
                        <h3 class="font-black text-slate-900 text-sm mb-1">Real-Time OMR Simulator</h3>
                        <p class="text-xs text-slate-600">Strictly enforces Kerala PSC scoring standards (+1.00 for correct, -0.33 penalty for incorrect) to cure wild guessing habits.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100">
                        <div class="text-2xl mb-2">🎭</div>
                        <h3 class="font-black text-slate-900 text-sm mb-1">Malayalam Meme Mnemonics</h3>
                        <p class="text-xs text-slate-600">Visual memory hooks and iconic Malayalam cinematic humor engineered for instant factual recall.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                        <div class="text-2xl mb-2">🌐</div>
                        <h3 class="font-black text-slate-900 text-sm mb-1">3D Map &amp; Globe Lab</h3>
                        <p class="text-xs text-slate-600">Spatial visual cognition modules for Kerala geography, river basins, historical monuments, and world geography.</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Transparent &amp; Fair Pricing
                </h2>
                <p>
                    We believe premium competitive exam training should be universally accessible. All our paid passes are <strong>strictly prepaid</strong> (1 month, 2 months, 3 months, 6 months, or 12 months) with progressive rebates. There are no recurring auto-debit deductions or hidden renewal traps.
                </p>
                <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs font-bold text-slate-700">Explore affordable prepaid subscriptions starting at nominal rates:</span>
                    <a href="{{ route('pricing') }}" class="px-4 py-2 bg-[#FFD200] hover:bg-yellow-400 text-slate-950 font-black text-xs rounded-xl shadow-xs transition">
                        View Pro Pass &amp; Pricing →
                    </a>
                </div>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Operating &amp; Contact Details
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block font-bold uppercase mb-1">Support Email</span>
                        <a href="mailto:infopscranker@gmail.com" class="text-[#0052FF] font-black hover:underline font-mono text-sm">
                            infopscranker@gmail.com
                        </a>
                        <p class="text-[11px] text-slate-500 mt-1">Direct replies within 24 business hours.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <span class="text-slate-400 block font-bold uppercase mb-1">Helpline / WhatsApp</span>
                        <a href="tel:+919895204224" class="text-[#0052FF] font-black hover:underline font-mono text-sm">
                            +91 9895 204 224
                        </a>
                        <p class="text-[11px] text-slate-500 mt-1">Available Mon–Sat: 9:00 AM – 7:00 PM IST.</p>
                    </div>
                </div>

                <div class="mt-4 text-xs text-slate-500 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <strong class="text-slate-700">Operational Address:</strong> PSC Ranker, 3/109 Puthampurakkal, Nellukadavu, Fort Kochi, Kochi, Ernakulam, Kerala – 682001, India.
                </div>
            </section>

            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                <span>Independent learning portal for Kerala PSC aspirants.</span>
                <a href="{{ route('contact') }}" class="text-[#0052FF] font-bold hover:underline">Get in Touch with Us →</a>
            </div>

        </div>

    </div>
</div>
@endsection
