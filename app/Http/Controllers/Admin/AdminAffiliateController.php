<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliateLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAffiliateController extends Controller
{
    /**
     * Display the Admin Mission Control Affiliate Hub.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'affiliates'); // 'affiliates', 'disbursements', 'leads'

        // 1. Core Summary Metrics
        $totalAffiliates = Affiliate::count();
        $activeAffiliates = Affiliate::where('status', 'active')->count();
        $totalLeads = AffiliateLead::count();
        $totalConversions = AffiliateLead::where('status', 'converted')->count();
        $lifetimeCommission = (float) AffiliateCommission::sum('total_amount');
        $totalDisbursed = (float) AffiliateCommission::where('status', 'disbursed')->sum('total_amount');
        $totalPending = (float) AffiliateCommission::where('status', 'pending')->sum('total_amount');

        // Month for disbursement ledger (default to previous month e.g. 2026-08 if today is Sep, or selectable)
        $selectedMonth = $request->input('month', now()->subMonth()->format('Y-m'));

        // 2. Affiliates List
        $affiliatesQuery = Affiliate::with(['user'])->withCount(['leads', 'commissions']);
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $cleanPhone = preg_replace('/\D/', '', $search);

            $affiliatesQuery->where(function ($q) use ($search, $cleanPhone) {
                $q->where('affiliate_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $cleanPhone) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                      if (!empty($cleanPhone)) {
                          $uq->orWhere('phone', 'like', "%{$cleanPhone}%");
                      }
                  });
            });
        }
        $affiliates = $affiliatesQuery->latest('id')->paginate(15)->withQueryString();

        // 3. Monthly Disbursement Summaries grouped by Affiliate
        $monthlyDisbursements = AffiliateCommission::where('period_month', $selectedMonth)
            ->select(
                'affiliate_id',
                DB::raw('COUNT(id) as total_sales_count'),
                DB::raw('SUM(course_amount) as total_sales_volume'),
                DB::raw('SUM(commission_amount) as total_commission'),
                DB::raw('SUM(bonus_amount) as total_bonus'),
                DB::raw('SUM(total_amount) as total_payable'),
                DB::raw("SUM(CASE WHEN status = 'pending' THEN total_amount ELSE 0 END) as pending_amount"),
                DB::raw("SUM(CASE WHEN status = 'disbursed' THEN total_amount ELSE 0 END) as disbursed_amount")
            )
            ->groupBy('affiliate_id')
            ->with(['affiliate.user'])
            ->get();

        // 4. Global Leads Pipeline
        $leadsQuery = AffiliateLead::with(['affiliate.user', 'convertedUser']);
        if ($request->filled('lead_q')) {
            $lSearch = trim($request->input('lead_q'));
            $cleanPhone = preg_replace('/\D/', '', $lSearch);

            $leadsQuery->where(function ($q) use ($lSearch, $cleanPhone) {
                $q->where('candidate_name', 'like', "%{$lSearch}%");
                if (!empty($cleanPhone)) {
                    $q->orWhere('candidate_phone', 'like', "%{$cleanPhone}%");
                }
            });
        }
        if ($request->filled('lead_status')) {
            $leadsQuery->where('status', $request->input('lead_status'));
        }
        $leads = $leadsQuery->latest('id')->paginate(20, ['*'], 'leads_page')->withQueryString();

        // Available Months list for dropdown (last 12 months)
        $availableMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $m = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('F Y');
            $availableMonths[$m] = $label;
        }

        return view('admin.affiliates.index', compact(
            'tab',
            'totalAffiliates',
            'activeAffiliates',
            'totalLeads',
            'totalConversions',
            'lifetimeCommission',
            'totalDisbursed',
            'totalPending',
            'affiliates',
            'selectedMonth',
            'monthlyDisbursements',
            'leads',
            'availableMonths'
        ));
    }

    /**
     * Update Affiliate status (active, suspended, pending).
     */
    public function updateStatus(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:active,suspended,pending'],
        ]);

        $affiliate->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', "Affiliate #{$affiliate->affiliate_code} status set to {$validated['status']}.");
    }

    /**
     * Update custom commission rate percentage.
     */
    public function updateCommissionRate(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $affiliate->update(['commission_rate' => $validated['commission_rate']]);

        return redirect()->back()->with('success', "Commission rate for {$affiliate->user->name} updated to {$validated['commission_rate']}%.");
    }

    /**
     * Add a discretionary performance bonus to an affiliate.
     */
    public function addBonus(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'bonus_amount' => ['required', 'numeric', 'min:1'],
            'period_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $bonusAmount = (float) $validated['bonus_amount'];

        AffiliateCommission::create([
            'affiliate_id' => $affiliate->id,
            'affiliate_lead_id' => null,
            'subscription_payment_id' => null,
            'user_id' => null,
            'course_amount' => 0,
            'commission_rate' => 0,
            'commission_amount' => 0,
            'bonus_amount' => $bonusAmount,
            'total_amount' => $bonusAmount,
            'period_month' => $validated['period_month'],
            'status' => 'pending',
            'admin_notes' => 'Bonus: ' . $validated['reason'],
        ]);

        return redirect()->back()->with('success', "Performance bonus of ₹{$bonusAmount} credited to {$affiliate->user->name} for {$validated['period_month']}.");
    }

    /**
     * Disburse all pending commissions for an affiliate in a specific month.
     */
    public function disburseMonthly(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'period_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'payout_reference' => ['required', 'string', 'max:100'], // Bank UTR or UPI Transaction ID
            'admin_notes' => ['nullable', 'string', 'max:255'],
        ], [
            'payout_reference.required' => 'Please enter the Bank UTR / UPI Transaction Reference for audit records.',
        ]);

        $periodMonth = $validated['period_month'];
        $ref = trim($validated['payout_reference']);

        $pendingCommissions = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('period_month', $periodMonth)
            ->where('status', 'pending')
            ->get();

        if ($pendingCommissions->isEmpty()) {
            return redirect()->back()->with('info', 'No pending earnings found for this promoter in ' . $periodMonth);
        }

        $totalDisbursed = $pendingCommissions->sum('total_amount');

        AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('period_month', $periodMonth)
            ->where('status', 'pending')
            ->update([
                'status' => 'disbursed',
                'disbursed_at' => now(),
                'payout_reference' => $ref,
                'admin_notes' => $validated['admin_notes'] ?? 'Disbursed via Admin Mission Control',
            ]);

        return redirect()->back()->with(
            'success',
            "🎉 Successfully disbursed ₹" . number_format($totalDisbursed, 2) . " to {$affiliate->user->name} for {$periodMonth}. UTR: {$ref}"
        );
    }
}
