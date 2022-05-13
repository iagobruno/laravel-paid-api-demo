<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function redirect() {
        return Socialite::driver('github')->redirect();
    }

    public function callback() {
        $user = Socialite::driver('github')->user();

        $user = User::updateOrCreate([
            'github_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'github_token' => $user->token,
            'github_refresh_token' => $user->refreshToken,
        ]);
        $user->createOrGetStripeCustomer([
            'name' => $user->name,
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}
