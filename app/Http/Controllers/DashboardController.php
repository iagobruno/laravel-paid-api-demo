<?php

namespace App\Http\Controllers;

use App\Models\ApiUsageRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $currentSubscription = $user->subscription('default');
        $upcomingInvoice = $user->upcomingInvoice();
        $invoices = $user->invoices();

        $totalUsage = ApiUsageRecord::getUsedQuota();
        $freeQuota = 10;
        $usedQuota = min([$totalUsage, $freeQuota]);
        $usedQuotaPercentage = ($usedQuota / $freeQuota) * 100;

        return view('dashboard', compact([
            'currentSubscription',
            'upcomingInvoice',
            'invoices',
            'totalUsage',
            'freeQuota',
            'usedQuota',
            'usedQuotaPercentage',
        ]));
    }
}
