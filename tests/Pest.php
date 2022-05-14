<?php

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function logDBQueries($callback, $showBindins = false)
{
    DB::enableQueryLog();
    $callback();
    if ($showBindins) {
        dump(DB::getQueryLog());
    } else {
        dump(
            array_map(fn ($i) => $i['query'], DB::getQueryLog())
        );
    }
    DB::disableQueryLog();
}

function createTestCreditCard(string $code = null)
{
    $number = match ($code) {
        'insufficient_funds' => '4000000000009995',
        'expired' => '4000000000000069',
        'card_declined' => '4000000000000002',
        'incorrect_cvc' => '4000000000000127',
        'incorrect_number' => '4242424242424241',
        default => '4242424242424242'
    };

    return \Stripe\PaymentMethod::create([
        'type' => 'card',
        'card' => [
            'number' => $number,
            'exp_month' => 12,
            'exp_year' => now()->addYear()->year,
            'cvc' => '314',
        ],
    ]);
}
