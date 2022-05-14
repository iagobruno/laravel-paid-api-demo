<?php

use App\Models\User;
use function Pest\Laravel\{getJson, actingAs};

$priceId = 'price_1Kz35gHcBcdIHl3NvZM24lPy';

test('Deve retornar um erro se a solicitação não conter um token', function () {
    getJson(route('api.paid-route'))
        ->assertUnauthorized();
});

test('Deve permitir que usuários não pagantes usem a rota até atingir a cota de solicitações grátis', function () {
    /** @var \App\Models\User */
    $user = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        actingAs($user, 'sanctum')
            ->getJson(route('api.paid-route'))
            ->assertOk()
            ->assertDontSee('exceeded your free request quota')
            ->assertSee('WORKS!');
    }

    actingAs($user, 'sanctum')
        ->getJson(route('api.paid-route'))
        ->assertForbidden()
        ->assertSee('You have exceeded your free request quota for this API');
});

test('Deve permitir que usuários pagantes usem a rota', function () use ($priceId) {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice($priceId)->add();

    actingAs($user, 'sanctum')
        ->getJson(route('api.paid-route'))
        ->assertOk()
        ->assertSee('WORKS!');
});

test('Deve informar ao Stripe que a rota foi usada', function () use ($priceId) {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice($priceId)->add();

    actingAs($user, 'sanctum')
        ->getJson(route('api.paid-route'))
        ->assertOk();
    actingAs($user, 'sanctum')
        ->getJson(route('api.paid-route'))
        ->assertOk();

    expect($user->subscription()->usageRecords()[0]['total_usage'])->toEqual(2);
});

test('Deve impedir que usuários inadimplentes usem a api após o atingir a cota grátis', function () use ($priceId) {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice($priceId)->add();

    $user->subscription()->update([
        'stripe_status' => \Stripe\Subscription::STATUS_UNPAID,
    ]);

    for ($i = 0; $i < 10; $i++) {
        actingAs($user, 'sanctum')
            ->getJson(route('api.paid-route'))
            ->assertOk();
    }

    actingAs($user, 'sanctum')
        ->getJson(route('api.paid-route'))
        ->assertForbidden()
        ->assertSee('There was a problem with your account subscription');
});

test('Deve aumentar a fatura do usuário corretamente', function () use ($priceId) {
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice($priceId)->add();

    for ($i = 0; $i < 26; $i++) {
        actingAs($user, 'sanctum')
            ->getJson(route('api.paid-route'))
            ->assertOk();
    }

    $fixedPrice = 4999; // In cents
    $pricePerCall1 = 25; // In cents
    $pricePerCall2 = 12; // In cents
    $expectedTotal = $fixedPrice + ($pricePerCall1 * 10) + ($pricePerCall2 * 6);

    $upcomingInvoice = \Stripe\Invoice::upcoming([
        'customer' => $user->stripe_id,
    ]);
    expect($upcomingInvoice->total)->toEqual($expectedTotal);
});
