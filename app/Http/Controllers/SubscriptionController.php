<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // Don't show this page for subscribed users
        if ($user->subscribed()) {
            return redirect()->intended(route('dashboard'));
        }

        $hasPaymentMethod = $user->hasDefaultPaymentMethod();
        $setupIntent = $user->createSetupIntent();

        return view('subscribe', compact([
            'hasPaymentMethod',
            'setupIntent',
        ]));
    }

    public function handle()
    {
        $data = request()->validate([
            'payment_method' => ['required', 'string', 'starts_with:pm_']
        ], [
            'payment_method.required' => 'Ocorreu um erro ao validar seu cartão',
            'payment_method.starts_with' => 'Ocorreu um erro ao validar seu cartão',
        ]);
        /** @var \App\Models\User */
        $user = Auth::user();

        try {
            $user->updateDefaultPaymentMethod($data['payment_method']);
            $user->newSubscription('default')->meteredPrice('price_1Kz35gHcBcdIHl3NvZM24lPy')->add();
        } catch (\Exception $e) {
            return back()->withErrors([
                'generic-error' => 'Ocorreu um problema ao tentar iniciar sua assinatura. ' . $e->getMessage()
            ]);
        }

        return redirect()->intended(route('dashboard'))
            ->with(['success' => 'Assinatura iniciada com sucesso!']);
    }
}
