<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use \App\Models\ApiUsageRecord;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function apiUsageRecords()
    {
        return $this->hasMany(ApiUsageRecord::class);
    }

    /**
     * Records an API usage in the database for monthly quota limit checking.
     * @return ApiUsageRecord API usage record in this month.
     */
    public function recordApiUsage($quantity = 1)
    {
        $user = $this;
        $recordOfThisMonth = $user->apiUsageRecords()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if ($recordOfThisMonth) {
            $recordOfThisMonth->increment('total', $quantity);
        } else {
            $monthsSinceAccountCreation = $user->created_at->diffInMonths(now());
            $recordOfThisMonth = $user->apiUsageRecords()->create([
                'total' => $quantity,
                'starts_at' => $user->created_at->addMonths($monthsSinceAccountCreation),
                'ends_at' => $user->created_at->addMonths($monthsSinceAccountCreation + 1),
            ]);
        }

        // Send to Stripe too
        $user->subscription('default')?->reportUsage($quantity);

        return $recordOfThisMonth;
    }
}
