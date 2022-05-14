<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/paid-route', function () {
        /** @var \App\Models\User */
        $user = auth()->user();
        $isPremiumUser = $user->subscription() !== null;
        $usageReport = $user->reportApiUsage();
        $freeRequestsQuotaExceeded = $usageReport->total > 10;

        if (!$isPremiumUser && $freeRequestsQuotaExceeded) {
            return abort(Response::HTTP_FORBIDDEN, 'You have exceeded your free request quota for this API.');
        }

        if ($isPremiumUser && !$user->subscription()->valid() && $freeRequestsQuotaExceeded) {
            return abort(Response::HTTP_FORBIDDEN, 'There was a problem with your account subscription.');
        }

        return 'WORKS!';
    })->name('api.paid-route');
});
