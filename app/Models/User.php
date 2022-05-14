<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;

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

    public function apiUsageReports()
    {
        return $this->hasMany(\App\Models\ApiUsageReport::class);
    }

    public function reportApiUsage($quantity = 1)
    {
        $user = $this;
        $report = $user->apiUsageReports()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if ($report) {
            $report->increment('total', $quantity);
        } else {
            $monthsSinceAccountCreation = $user->created_at->diffInMonths(now());
            $report = $user->apiUsageReports()->create([
                'total' => $quantity,
                'starts_at' => $user->created_at->addMonth($monthsSinceAccountCreation),
                'ends_at' => $user->created_at->addMonth($monthsSinceAccountCreation + 1),
            ]);
        }

        // Send to Stripe too
        $user->subscription('default')?->reportUsage($quantity);

        return $report;
    }
}
