<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;

class ApiUsageRecord extends Model
{
    use HasFactory;
    use MassPrunable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total',
        'starts_at',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Records an API usage in the database for monthly quota limit checking.
     * @return ApiUsageRecord API usage record in this month.
     */
    public static function recordUsage($quantity = 1)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
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

        return $recordOfThisMonth;
    }

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('ends_at', '<=', now()->subMonth());
    }
}
