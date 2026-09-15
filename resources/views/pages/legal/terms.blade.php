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
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Last Updated: September 13, 2026 • Compliant with Indian Information Technology Act &amp; Razorpay Merchant Rules</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-6">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    1. Agreement to Terms
                </h2>
                <p>
                    Welcome to <strong>PSCRanker.com</strong> ("Platform", "we", "our", or "us"). By accessing or using our interactive speed drills, OMR bubble simulators, 4-phase micro-learning sessions, and prepaid subscription plans, you agree to be bound by these Terms and Conditions, our Privacy Policy, Cancellation &amp; Refund Policy, and Shipping &amp; Delivery Policy. If you do not agree with any part of these terms, you must not use our website.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    2. Description of Service &amp; Educational Nature
                </h2>
                <p>
                    PSCRanker.com is an independent educational training platform designed to assist candidates preparing for competitive exams conducted by the Kerala Public Service Commission (KPSC) and allied boards.
                </p>
                <div class="p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl text-amber-900 text-xs mt-2">
                    <strong>Official Disclaimer:</strong> PSCRanker.com is <em>not affiliated, endorsed, or officially connected</em> with the Kerala Public Service Commission (Thiruvananthapuram) or any government agency. All question sets, mnemonics, and speed drills are curated educational aids.
                </div>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    3. Prepaid Subscriptions &amp; Instant Digital Delivery
                </h2>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Prepaid Model:</strong> Premium units and advanced OMR simulation tools are accessible on a prepaid subscription basis (e.g., 1 month, 2 months, 3 months, 6 months, or 1 year). All prices are stated in <strong>Indian Rupees (INR)</strong>.</li>
                    <li><strong>Instant Delivery:</strong> Access begins <em>instantaneously</em> upon payment confirmation from our payment partner Razorpay. No physical goods or books are shipped.</li>
                    <li><strong>Single User License:</strong> Accounts are strictly meant for individual learning. Sharing account credentials, automated scraping, or redistributing questions and mnemonic media is strictly prohibited and subject to immediate account termination without refund.</li>
                    <li><strong>No Auto-Debit Traps:</strong> Subscriptions are strictly non-recurring prepaid passes; you will never be auto-debited without manual checkout.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    4. Payment Gateway &amp; Security
                </h2>
                <p>
                    All online payments are securely processed through <strong>Razorpay (Razorpay Software Private Limited)</strong> and authorized banking gateways. We support UPI (Google Pay, PhonePe, Paytm, BHIM), RuPay, Visa, MasterCard, Net Banking, and digital wallets. We do not store sensitive payment credentials, credit card numbers, CVVs, or UPI PINs on our servers.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    5. Cancellation &amp; Refund Terms
                </h2>
                <p>
                    Cancellation and refund requests are governed by our dedicated <a href="{{ route('refund-policy') }}" class="text-[#0052FF] font-bold hover:underline">Cancellation &amp; Refund Policy</a>. Where eligible (e.g. duplicate deduction or technical service failure unresolved within 48 hours), approved refunds are processed back to the original source account through Razorpay within <strong>5 to 7 business days</strong>.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    6. Intellectual Property
                </h2>
                <p>
                    All micro-learning layouts, mnemonic illustrations, audio capsules, algorithms (including the negative marking penalty engine), and codebases are the exclusive intellectual property of PSCRanker.com.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    7. Governing Law &amp; Jurisdiction
                </h2>
                <p>
                    These Terms are governed by and construed in accordance with the laws of India. Any disputes arising out of these terms shall be subject to the exclusive jurisdiction of the competent courts in Kochi / Ernakulam, Kerala, India.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    8. Contact Information
                </h2>
                <p>
                    For queries or grievance redressal regarding these Terms, please contact:
                </p>
                <div class="mt-2 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200 text-slate-800 space-y-1">
                    <div><strong>Business Name:</strong> PSC Ranker (PSCRanker.com)</div>
                    <div><strong>Email:</strong> <a href="mailto:infopscranker@gmail.com" class="text-[#0052FF] font-bold font-mono">infopscranker@gmail.com</a></div>
                    <div><strong>Phone / WhatsApp:</strong> <a href="tel:+919895204224" class="text-[#0052FF] font-bold font-mono">+91 9895 204 224</a></div>
                    <div><strong>Address:</strong> 3/109 Puthampurakkal, Nellukadavu, Fort Kochi, Kochi, Ernakulam, Kerala – 682001, India</div>
                </div>
            </section>

            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-500">
                <span>Questions regarding these Terms? Contact us at: <a href="mailto:infopscranker@gmail.com" class="text-[#0052FF] font-bold font-mono">infopscranker@gmail.com</a></span>
                <a href="{{ route('pricing') }}" class="text-[#0052FF] font-bold hover:underline">View Pricing Plans →</a>
            </div>

        </div>

    </div>
</div>
@endsection
