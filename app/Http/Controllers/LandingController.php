<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function __invoke()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Don't show this page for subscribed users
        if ($user?->subscribed()) {
            return redirect()->intended(route('dashboard'));
        }

        // $plans = Cache::rememberForever('plans', function () {
        //     $plans = \Stripe\Product::all([
        //         'active' => true,
        //         // 'ids' => []
        //     ])->data;
        //     foreach ($plans as $plan) {
        //         $plan->prices = \Stripe\Price::all([
        //             'active' => true,
        //             'product' => $plan->id,
        //             'type' => 'recurring',
        //             'recurring' => [
        //                 'usage_type' => 'metered'
        //             ],
        //         ])->data;
        //     }
        //     return $plans;
        // });

        return view('landing');
    }
}
