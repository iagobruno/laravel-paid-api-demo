<?php

use \App\Models\User;

uses(\Tests\TestCase::class);

test('Deve somar o total ', function () {
    $user = User::factory()->create();

    $user->reportApiUsage();
    $user->reportApiUsage(2);

    $this->assertDatabaseCount('api_usage_reports', 1);
    expect($user->apiUsageReports()->first()->total)->toEqual(3);
});

test('Deve calcular corretamente o "starts_at" e o "ends_at" quando criar um ApiUsageReport', function () {
    $user = User::factory()->create();
    $user->created_at = now()->subMonths(rand(1, 30))->subDays(rand(1, 30));

    $usageReport = $user->reportApiUsage();
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
