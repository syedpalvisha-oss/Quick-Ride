<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverProfile extends Model
{
    protected $fillable = [
        'stripe_account_id',
        'details_submitted',
        'charges_enabled',
        'payouts_enabled',
        'onboarding_completed_at',
        'onboarding_requirements',
    ];

    protected function casts(): array
    {
        return [
            'details_submitted' => 'boolean',
            'charges_enabled' => 'boolean',
            'payouts_enabled' => 'boolean',
            'onboarding_completed_at' => 'datetime',
            'onboarding_requirements' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
