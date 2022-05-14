<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ApiUsageRecord;

class ApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        DB::beginTransaction();
        try {
            /** @var \App\Models\User */
            $user = auth()->user();
            $isPremiumUser = $user->subscription() !== null;
            $usageReport = ApiUsageRecord::recordUsage();
            $freeRequestsQuotaExceeded = $usageReport->total > 10;

            if (!$isPremiumUser && $freeRequestsQuotaExceeded) {
                return abort(Response::HTTP_FORBIDDEN, 'You have exceeded your free request quota for this API.');
            }
            if ($isPremiumUser && !$user->subscription()->valid() && $freeRequestsQuotaExceeded) {
                return abort(Response::HTTP_FORBIDDEN, 'There was a problem with your account subscription.');
            }

            $response = $next($request);
            // "Re-throw" errors
            if ($response->exception) {
                DB::rollBack();
                return $response;
            }

            // Report usage to Stripe
            $user->subscription()?->reportUsage(1);

            DB::commit();
            return $response;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
