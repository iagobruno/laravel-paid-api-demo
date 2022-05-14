<?php

use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Route;
use App\Models\ApiUsageRecord;
use \App\Models\User;

uses(\Tests\TestCase::class);

test('Deve somar o total chamadas corretamente para este mês no banco de dados', function () {
    Route::get('/test-route', fn () => 'works')->middleware('api-access');
    /** @var \App\Models\User */
    $user = User::factory()->create();

    actingAs($user)->getJson('/test-route')->assertOk();
    actingAs($user)->getJson('/test-route')->assertOk();
    actingAs($user)->getJson('/test-route')->assertOk();

    $this->assertDatabaseCount('api_usage_records', 1);
    expect($user->apiUsageRecords()->first()->total)->toEqual(3);
});

test('Deve calcular corretamente o "starts_at" e o "ends_at" quando criar um ApiUsageRecord', function () {
    Route::get('/test-route', fn () => 'works')->middleware('api-access');
    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->created_at = now()->subMonths(rand(1, 30))->subDays(rand(1, 30));

    actingAs($user)->getJson('/test-route')->assertOk();

    $usageReport = ApiUsageRecord::first();
    // dump($usageReport->toArray());

    $monthsSinceUserCreation = $user->created_at->diffInMonths(now());
    $expectedStartsAt = $user->created_at->addMonths($monthsSinceUserCreation);
    $expectedEndsAt = $user->created_at->addMonths($monthsSinceUserCreation + 1);

    expect($usageReport->starts_at->day)->toEqual($user->created_at->day);
    expect($usageReport->starts_at->month)->toEqual($expectedStartsAt->month);
    expect($usageReport->starts_at->year)->toEqual($expectedStartsAt->year);

    expect($usageReport->ends_at->day)->toEqual($user->created_at->day);
    expect($usageReport->ends_at->month)->toEqual($expectedEndsAt->month);
    expect($usageReport->ends_at->year)->toEqual($expectedEndsAt->year);
});

test('Todas as operações devem falhar se ocorrer algum erro dentro da solicitação', function () {
    Route::get('/test-route', function () {
        abort(500);
    })->middleware('api-access');

    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice('price_1Kz35gHcBcdIHl3NvZM24lPy')->add();

    for ($i = 0; $i < 11; $i++) {
        actingAs($user)->getJson('/test-route')->assertStatus(500);
    }

    $this->assertDatabaseCount('api_usage_records', 0);

    $upcomingInvoice = \Stripe\Invoice::upcoming(['customer' => $user->stripe_id]);
    expect($upcomingInvoice->total)->toEqual(4999);
});

test('Todas as operações devem ser finalizadas se não ocorrer nenhum erro', function () {
    Route::get('/test-route', fn () => 'works')->middleware('api-access');

    /** @var \App\Models\User */
    $user = User::factory()->create();
    $user->createAsStripeCustomer();
    $user->updateDefaultPaymentMethod(createTestCreditCard());
    $user->newSubscription('default')->meteredPrice('price_1Kz35gHcBcdIHl3NvZM24lPy')->add();

    for ($i = 0; $i < 11; $i++) {
        actingAs($user)->getJson('/test-route')->assertOk();
    }

    expect(\Illuminate\Support\Facades\DB::transactionLevel())->toEqual(1);

    $this->assertDatabaseCount('api_usage_records', 1);
    $this->assertDatabaseHas('api_usage_records', [
        'user_id' => $user->id,
        'total' => 11
    ]);

    $upcomingInvoice = \Stripe\Invoice::upcoming(['customer' => $user->stripe_id]);
    expect($upcomingInvoice->total)->toEqual(4999 + 25);
});
