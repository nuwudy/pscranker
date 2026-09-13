@extends('layouts.app')

@section('title', 'Shipping & Delivery Policy — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Shipping &amp; Delivery Policy</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Last Updated: September 13, 2026 • Compliant with Razorpay Merchant Guidelines</p>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200 text-slate-700 text-sm leading-relaxed space-y-6">
            
            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    1. Nature of Products &amp; Services
                </h2>
                <p>
                    <strong>PSCRanker.com</strong> provides 100% digital educational services and software-based learning tools for Kerala Public Service Commission (KPSC) exam aspirants. Our offerings include interactive 4-phase micro-learning lessons, 3-minute rapid speed drills, OMR test simulators, Malayalam mnemonic visual memory aids, and 3D map study labs.
                </p>
                <div class="p-4 bg-blue-50 border-l-4 border-[#0052FF] rounded-r-xl text-blue-950 text-xs mt-2">
                    <strong>Digital Goods Only:</strong> No physical goods, printed books, or parcels are dispatched. All study modules, test papers, and learning analytics are delivered exclusively online via our web application.
                </div>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    2. Delivery / Fulfillment Timeline
                </h2>
                <p>
                    Because all our products are digital, access is delivered <strong>instantaneously</strong>:
                </p>
                <ul class="list-disc list-inside space-y-2 mt-2">
                    <li><strong>Instant Activation:</strong> Upon successful payment completion via <strong>Razorpay</strong>, your selected prepaid subscription pass (1, 2, 3, 6, or 12 months) is unlocked automatically on your account within seconds.</li>
                    <li><strong>Confirmation &amp; Receipt:</strong> A payment confirmation receipt, order reference ID, and activation summary will be displayed on screen and sent to your registered email address (<span class="font-mono text-xs">infopscranker@gmail.com</span> automated delivery system).</li>
                    <li><strong>Zero Waiting Period:</strong> You can immediately begin taking premium lessons, speed drills, and full-length OMR mock tests with no courier delays.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    3. Shipping Charges &amp; Hidden Fees
                </h2>
                <p>
                    Since all fulfillment is performed electronically over the internet:
                </p>
                <ul class="list-disc list-inside space-y-1.5 mt-2">
                    <li><strong>Shipping Fee:</strong> <strong>₹0.00 (Nil / Free)</strong>. We do not levy any shipping, packaging, handling, or dispatch charges.</li>
                    <li><strong>Transparent Pricing:</strong> The price displayed at the checkout is inclusive of all educational platform access rights for the duration chosen.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-black text-slate-900 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0052FF]"></span>
                    4. Access Troubleshooting &amp; Delivery Support
                </h2>
                <p>
                    In rare cases of intermittent bank network lags or delayed gateway webhooks, if your account does not reflect the unlocked status within 15 minutes of payment:
                </p>
                <ul class="list-disc list-inside space-y-1.5 mt-2">
                    <li>Please refresh your browser session or log out and log back in.</li>
                    <li>If the issue persists, reach out immediately to our dedicated support desk with your Razorpay Payment ID or registered mobile number.</li>
                    <li>Our technical support team will manually verify and unlock your access within <strong>2 to 4 business hours</strong>.</li>
                </ul>

                <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="font-bold text-slate-900 text-xs">Need help with instant access delivery?</div>
                        <div class="text-xs text-slate-600 mt-0.5">
                            Email: <a href="mailto:infopscranker@gmail.com" class="text-[#0052FF] font-bold hover:underline">infopscranker@gmail.com</a> | 
                            Phone / WhatsApp: <a href="tel:+919895204224" class="text-[#0052FF] font-bold hover:underline">+91 9895 204 224</a>
                        </div>
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
