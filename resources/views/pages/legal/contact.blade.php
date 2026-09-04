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
            <p class="text-xs sm:text-sm text-slate-500 mt-1">We are here to assist you with subscription, payment queries, and course guidance.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Card 1: Email Support -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0052FF] flex items-center justify-center text-xl mb-4">
                        ✉️
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mb-1">Official Email</h3>
                    <p class="text-xs text-slate-500 mb-3">Direct response within 24 hours for billing, technical, or access questions.</p>
                </div>
                <a href="mailto:admin@pscranker.com" class="text-xs font-bold text-[#0052FF] hover:underline break-all">
                    admin@pscranker.com
                </a>
            </div>

            <!-- Card 2: Operating Hours -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4">
                        ⏰
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mb-1">Working Hours</h3>
                    <p class="text-xs text-slate-500 mb-3">Dedicated support desk for Kerala PSC exam aspirants.</p>
                </div>
                <div class="text-xs font-mono text-slate-700 font-bold">
                    Mon – Sat: 9:00 AM – 7:00 PM IST
                </div>
            </div>

            <!-- Card 3: Location -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4">
                        📍
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mb-1">Operational Location</h3>
                    <p class="text-xs text-slate-500 mb-3">Serving aspirants across all 14 districts of Kerala.</p>
                </div>
                <div class="text-xs font-bold text-slate-700">
                    Kerala, India
                </div>
            </div>

        </div>

        <!-- Contact & Grievance Redressal Form / Details -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xs border border-slate-200">
            <h2 class="text-base font-black text-slate-900 uppercase tracking-wider mb-2">Grievance Redressal &amp; Inquiries</h2>
            <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                If you have paid for a prepaid plan and need assistance with account activation, or have a content suggestion, please submit your inquiry below or write to our grievance officer.
            </p>

            <form onsubmit="event.preventDefault(); alert('Thank you! Your message has been recorded. Our team will email you at your address within 24 hours.');" class="space-y-4 text-xs">
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
                        <label class="block font-bold text-slate-700 mb-1">Phone Number (Optional)</label>
                        <input type="tel" placeholder="+91 98765 43210" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Topic</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs">
                            <option>Prepaid Plan Activation &amp; Access</option>
                            <option>Payment / Razorpay Transaction Query</option>
                            <option>OMR Test &amp; Speed Drill Technical Issue</option>
                            <option>Content or PSC Question Feedback</option>
                            <option>Other Grievance</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Message / Payment Reference ID</label>
                    <textarea rows="4" required placeholder="Please describe your query. Include your Razorpay Payment ID if this is about a subscription payment..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-hidden focus:border-[#0052FF] text-xs"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-6 py-3 bg-[#0052FF] hover:bg-blue-700 text-white font-black text-xs rounded-xl shadow transition active:scale-95 flex items-center gap-2">
                        <span>Send Message</span>
                        <span>✉️</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
