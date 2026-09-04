@extends('layouts.app')

@section('title', 'Privacy Policy — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Privacy Policy</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Last Updated: September 4, 2026 • Compliant with Indian IT Rules & Razorpay Merchant Norms</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-6">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    1. Information We Collect
                </h2>
                <p>When you register, take speed drills, or purchase a prepaid subscription on PSCRanker.com, we collect:</p>
                <ul class="list-disc list-inside space-y-1.5 mt-2">
                    <li><strong>Personal Information:</strong> Name, email address, mobile number (required for Razorpay invoice generation & OTP verification).</li>
                    <li><strong>Learning Analytics:</strong> Speed drill reaction times, OMR answer selections, negative marks incurred, XP points, and leaderboard standings.</li>
                    <li><strong>Device & Log Information:</strong> IP address, browser type, and PWA display state for session continuity and security.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    2. Payment Security & Third-Party Gateway
                </h2>
                <p>
                    We prioritize payment confidentiality. When you make a prepaid subscription payment, all sensitive financial data (such as Card Numbers, CVV, or UPI handles) is encrypted using SSL/TLS and transmitted directly to <strong>Razorpay (Razorpay Software Private Limited)</strong>. 
                </p>
                <p class="mt-2 text-xs bg-slate-50 p-3 rounded-xl border border-slate-200">
                    🔒 <strong>No Card Data Stored:</strong> PSCRanker.com does not capture, view, or store payment instruments or credentials on its servers. Razorpay complies with the Payment Card Industry Data Security Standard (PCI-DSS Level 1).
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    3. How We Use Your Information
                </h2>
                <ul class="list-disc list-inside space-y-1 mt-2">
                    <li>To deliver prepaid course access, speed drills, and score reports.</li>
                    <li>To render your score and rank on the daily real-time PSC Leaderboard.</li>
                    <li>To issue tax invoices and subscription renewal receipts.</li>
                    <li>To prevent unauthorized account sharing or abusive bot activities.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    4. Data Protection & No-Spam Guarantee
                </h2>
                <p>
                    We will never sell, rent, or trade your personal email or phone number to third-party marketing telecallers. Your information is used exclusively to facilitate your Kerala PSC learning journey on our platform.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    5. Grievance Officer & Contact
                </h2>
                <p>
                    For any privacy inquiries or request for account deletion, please email our grievance officer:
                </p>
                <div class="mt-2 text-xs bg-blue-50 p-4 rounded-xl border border-blue-200 text-blue-900 space-y-1 font-mono">
                    <div><strong>Email:</strong> admin@pscranker.com</div>
                    <div><strong>Operating Hours:</strong> Monday – Saturday, 9:00 AM – 6:00 PM IST</div>
                    <div><strong>Location:</strong> Kerala, India</div>
                </div>
            </section>

        </div>

    </div>
</div>
@endsection
