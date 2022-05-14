<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($next, $request) {
            /** @var \App\Models\User */
            $user = auth()->user();
            $isPremiumUser = $user->subscription() !== null;
            $usageReport = $user->recordApiUsage();
            $freeRequestsQuotaExceeded = $usageReport->total > 10;

            if (!$isPremiumUser && $freeRequestsQuotaExceeded) {
                return abort(Response::HTTP_FORBIDDEN, 'You have exceeded your free request quota for this API.');
            }

            if ($isPremiumUser && !$user->subscription()->valid() && $freeRequestsQuotaExceeded) {
                return abort(Response::HTTP_FORBIDDEN, 'There was a problem with your account subscription.');
            }

            return $next($request);
        });
    }
}
