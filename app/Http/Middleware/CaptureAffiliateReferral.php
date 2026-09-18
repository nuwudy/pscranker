<?php

namespace App\Http\Middleware;

use App\Models\Affiliate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CaptureAffiliateReferral
{
    /**
     * Handle an incoming request and capture affiliate referral code from query or route.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $refCode = $request->query('ref');

        if (!empty($refCode)) {
            $cleanCode = strtoupper(trim((string) $refCode));

            $affiliate = Affiliate::where('affiliate_code', $cleanCode)
                ->where('status', 'active')
                ->first();

            if ($affiliate) {
                // Throttle click counting: only increment once per session for this affiliate
                if (session('affiliate_ref') !== $affiliate->affiliate_code) {
                    $affiliate->increment('referral_clicks');
                    session(['affiliate_ref' => $affiliate->affiliate_code]);
                }

                // Set 60-day persistent cookie (in minutes: 60 days * 24 hrs * 60 mins)
                Cookie::queue('affiliate_ref', $affiliate->affiliate_code, 60 * 24 * 60);
            }
        }

        return $next($request);
    }
}
