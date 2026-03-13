<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stripe_account_id' => $this->resource->stripe_account_id,
            'details_submitted' => $this->resource->details_submitted,
            'charges_enabled' => $this->resource->charges_enabled,
            'payouts_enabled' => $this->resource->payouts_enabled,
            'onboarding_completed_at' => $this->resource->onboarding_completed_at,
            'onboarding_requirements' => $this->resource->onboarding_requirements,
        ];
    }
}
