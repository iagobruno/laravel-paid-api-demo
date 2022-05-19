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
        $tokens = $user->tokens()->latest()->get();
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
            'tokens',
            'totalUsage',
            'freeQuota',
            'usedQuota',
            'usedQuotaPercentage',
        ]));
    }

    public function showCreateTokenForm(Request $request)
    {
        return view('create-token');
    }

    public function createToken(Request $request)
    {
        $request->validate([
            'token-name' => ['required', 'string', 'max:255']
        ]);

        /** @var \App\Models\User */
        $user = Auth::user();
        $token = $user->createToken($request->input('token-name'));

        return redirect()->route('dashboard')
            ->with('newToken', $token->plainTextToken);
    }
}
