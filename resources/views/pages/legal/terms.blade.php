@extends('layouts.app')

@section('title', 'Terms & Conditions — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Terms and Conditions</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Last Updated: September 4, 2026 • Effective Immediately</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-6">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    1. Agreement to Terms
                </h2>
                <p>
                    Welcome to <strong>PSCRanker.com</strong> ("Platform", "we", "our", or "us"). By accessing or using our interactive speed drills, OMR bubble simulators, 4-phase micro-learning sessions, and subscription plans, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree with any part of these terms, you must not use our website.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    2. Description of Service & Educational Nature
                </h2>
                <p>
                    PSCRanker.com is an independent educational training platform designed to assist candidates preparing for competitive exams conducted by the Kerala Public Service Commission (KPSC) and related bodies. 
                </p>
                <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl text-amber-900 text-xs mt-2">
                    <strong>Official Disclaimer:</strong> PSCRanker.com is <em>not affiliated, endorsed, or officially connected</em> with the Kerala Public Service Commission (Thiruvananthapuram) or any government agency. The question sets, memory aids, and speed blitz modules are curated educational aids.
                </div>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    3. Prepaid Subscriptions & Account Access
                </h2>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Prepaid Model:</strong> Premium units and advanced OMR simulation tools are accessible on a prepaid subscription basis (e.g., 1 month, 2 months, 3 months, 6 months, or 1 year).</li>
                    <li><strong>Single User License:</strong> Accounts are strictly meant for individual learning. Sharing account credentials, automated web scraping, or redistributing questions and mnemonic media is strictly prohibited and subject to immediate account termination without refund.</li>
                    <li><strong>Duration:</strong> Access begins immediately upon successful payment confirmation via our payment partner Razorpay and remains valid for the selected calendar duration.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    4. Payment Processing
                </h2>
                <p>
                    All online payments are securely processed through <strong>Razorpay</strong> and authorized banking gateways. We support UPI (Google Pay, PhonePe, Paytm), Debit/Credit Cards, Net Banking, and Wallet options. We do not store your credit card numbers, CVV, or UPI PINs on our servers.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    5. Intellectual Property
                </h2>
                <p>
                    All micro-learning layouts, mnemonic illustrations, audio capsules, algorithms (including the negative marking penalty engine), and codebases are the intellectual property of PSCRanker.com.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    6. Governing Law & Jurisdiction
                </h2>
                <p>
                    These Terms are governed by and construed in accordance with the laws of India. Any disputes arising out of these terms shall be subject to the exclusive jurisdiction of the competent courts in Kerala, India.
                </p>
            </section>

            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                <span>Questions regarding these Terms? Contact us at: <a href="mailto:admin@pscranker.com" class="text-[#0052FF] font-bold">admin@pscranker.com</a></span>
                <a href="{{ route('pricing') }}" class="text-[#0052FF] font-bold hover:underline">View Pricing Plans →</a>
            </div>

        </div>

    </div>
</div>
@endsection
