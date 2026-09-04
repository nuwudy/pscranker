@extends('layouts.app')

@section('title', 'Cancellation & Refund Policy — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Cancellation &amp; Refund Policy</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Last Updated: September 4, 2026 • Razorpay Merchant Compliance</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-6">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    1. Digital Course &amp; Prepaid Subscription Access
                </h2>
                <p>
                    PSCRanker.com offers instant digital educational services, including 4-phase micro-learning capsules, OMR test simulators, speed drills, and PSC question banks. Because access to premium digital content is delivered <strong>instantaneously</strong> upon payment confirmation, standard physical product return policies do not apply.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    2. 7-Day Fair Refund Policy for Technical Issues
                </h2>
                <p>
                    We want every student to be satisfied with their preparation experience. You are entitled to a full refund within <strong>7 calendar days</strong> of purchase under the following circumstances:
                </p>
                <ul class="list-disc list-inside space-y-1.5 mt-2">
                    <li><strong>Technical Inaccessibility:</strong> In the rare event that your account failed to unlock premium units following payment and our technical support team was unable to resolve the issue within 48 hours.</li>
                    <li><strong>Duplicate Payment:</strong> If your bank account was debited multiple times for a single transaction due to network timeout or gateway glitched sessions. Any accidental duplicate debit is refunded automatically or within 3-5 business days.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    3. Non-Refundable Scenarios
                </h2>
                <ul class="list-disc list-inside space-y-1.5 mt-2">
                    <li>Change of mind after extensive usage and downloading of question sets or test attempts.</li>
                    <li>Failure to appear for the official Kerala PSC exam or personal postponement of study plans.</li>
                    <li>Account suspension resulting from violation of Terms & Conditions (e.g. sharing login credentials with other individuals).</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    4. Cancellation Policy
                </h2>
                <p>
                    Because all subscriptions on PSCRanker.com are <strong>strictly prepaid</strong> (for 1 month, 2 months, 3 months, 6 months, or 12 months), <em>there are no recurring auto-debits on your bank account</em>. Your access will automatically conclude at the end of the prepaid period unless you manually choose to purchase a renewal. You are not locked into any recurring contract.
                </p>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    5. Refund Processing Timeline
                </h2>
                <p>
                    Approved refunds will be processed directly through <strong>Razorpay</strong> back to the original source account (UPI / Bank Account / Card) within <strong>5 to 7 business days</strong> as per banking network turnaround times.
                </p>
                <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="font-bold text-slate-900 text-xs">Need assistance with a payment?</div>
                        <div class="text-xs text-slate-500">Email your Razorpay Payment ID to: <strong class="text-slate-800">admin@pscranker.com</strong></div>
                    </div>
                    <a href="{{ route('contact') }}" class="px-4 py-2 bg-[#0052FF] text-white text-xs font-bold rounded-xl hover:bg-blue-600 transition shrink-0">
                        Contact Support
                    </a>
                </div>
            </section>

        </div>

    </div>
</div>
@endsection
