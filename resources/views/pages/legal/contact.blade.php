@extends('layouts.app')

@section('title', 'Contact Us & Student Support — PSCRanker.com')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 text-center sm:text-left">
            <a href="{{ route('home') }}" class="text-xs font-bold text-[#0052FF] hover:underline inline-flex items-center gap-1 mb-2">
                ← Back to Home
            </a>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Contact Us &amp; Student Support</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">We are here to assist you with subscription, instant delivery, payment queries, and Kerala PSC course guidance.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            <!-- Card 1: Email Support -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#0052FF] flex items-center justify-center text-lg mb-3">
                        ✉️
                    </div>
                    <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider mb-1">Official Email</h3>
                    <p class="text-[11px] text-slate-500 mb-2">Direct response within 24 hours for billing, access, or technical questions.</p>
                </div>
                <a href="mailto:infopscranker@gmail.com" class="text-xs font-bold text-[#0052FF] hover:underline break-all font-mono">
                    infopscranker@gmail.com
                </a>
            </div>

            <!-- Card 2: Phone & WhatsApp -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-3">
                        📞
                    </div>
                    <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider mb-1">Phone &amp; WhatsApp</h3>
                    <p class="text-[11px] text-slate-500 mb-2">Call or chat directly with our student support helpdesk.</p>
                </div>
                <div class="flex flex-col gap-1 text-xs font-mono font-bold">
                    <a href="tel:+918089612287" class="text-slate-900 hover:text-[#0052FF] transition flex items-center gap-1.5">
                        <span class="text-emerald-600">●</span> +91 80896 12287
                    </a>
                    <a href="tel:+919895204224" class="text-slate-600 hover:text-[#0052FF] transition flex items-center gap-1.5 text-[11px]">
                        <span>●</span> +91 9895 204 224
                    </a>
                    <div class="pt-1 flex flex-col gap-1 text-[11px]">
                        <a href="https://wa.me/918089612287" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:underline flex items-center gap-1">
                            <span>Chat on WhatsApp (8089612287)</span> ➔
                        </a>
                        <a href="https://wa.me/919895204224" target="_blank" rel="noopener noreferrer" class="text-slate-500 hover:text-emerald-600 hover:underline flex items-center gap-1">
                            <span>WhatsApp (9895204224)</span> ➔
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Operating Hours -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg mb-3">
                        ⏰
                    </div>
                    <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider mb-1">Support Hours</h3>
                    <p class="text-[11px] text-slate-500 mb-2">Dedicated desk for Kerala PSC exam candidates.</p>
                </div>
                <div class="text-[11px] font-mono text-slate-700 font-bold leading-tight">
                    Mon – Sat: 9:00 AM – 7:00 PM IST
                </div>
            </div>

            <!-- Card 4: Location -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg mb-3">
                        📍
                    </div>
                    <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider mb-1">Operational Desk</h3>
                    <p class="text-[11px] text-slate-500 mb-2">3/109 Puthampurakkal, Nellukadavu, Fort Kochi, Kochi, Ernakulam, Kerala – 682001, India.</p>
                </div>
                <div class="text-[11px] font-bold text-slate-700">
                    Fort Kochi, Kerala
                </div>
            </div>

        </div>

        <!-- Registered Entity & Grievance Notice -->
        <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-4 mb-8 text-xs text-blue-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <strong class="font-black text-slate-900">Registered Business Name:</strong> PSC Ranker (PSCRanker.com)<br>
                <span class="text-slate-600">Postal Address: 3/109 Puthampurakkal, Nellukadavu, Fort Kochi, Kochi, Ernakulam, Kerala – 682001, India</span>
            </div>
            <div class="shrink-0 text-slate-500">
                Grievance Officer: <strong class="text-slate-800">Support Desk Head</strong>
            </div>
        </div>

        <!-- Contact & Grievance Redressal Form / Details -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200">
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wider mb-2">Grievance Redressal &amp; Inquiries</h2>
            <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                If you have paid for a prepaid plan and need assistance with account activation, instant delivery verification, refund processing, or have a question suggestion, please submit your inquiry below. You will receive an email reply from <strong class="text-slate-800">infopscranker@gmail.com</strong>.
            </p>

            <form onsubmit="event.preventDefault(); alert('Thank you! Your message has been recorded. Our team will contact you at your email within 24 business hours.');" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" required placeholder="e.g. Rahul Nair" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Registered Email Address</label>
                        <input type="email" required placeholder="e.g. rahul@example.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Phone Number / WhatsApp</label>
                        <input type="tel" placeholder="+91 80896 12287 / +91 98952 04224" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Topic</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                            <option>Prepaid Plan Activation &amp; Instant Access</option>
                            <option>Payment / Razorpay Transaction Query</option>
                            <option>Refund / Cancellation Request</option>
                            <option>OMR Test &amp; Speed Drill Technical Issue</option>
                            <option>Content or PSC Question Feedback</option>
                            <option>Other Grievance</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Message / Razorpay Order or Payment ID</label>
                    <textarea rows="4" required placeholder="Please describe your query. Include your Razorpay Payment ID if this is about a subscription payment..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-3 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow transition active:scale-95 flex items-center gap-2">
                        <span>Send Inquiry</span>
                        <span>✉️</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
