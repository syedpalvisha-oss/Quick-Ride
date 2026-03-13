<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\Order $resource
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'matched_at' => $this->resource->matched_at,
            'pickup_at' => $this->resource->pickup_at,
            'completed_at' => $this->resource->completed_at,
            'cancelled_at' => $this->resource->cancelled_at,
            'pickup_location' => [$this->resource->pickup_location->getY(), $this->resource->pickup_location->getX()],
            'dropoff_location' => [$this->resource->dropoff_location->getY(), $this->resource->dropoff_location->getX()],
            'driver_cancelled_at' => $this->resource->driver_cancelled_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'driver' => new UserResource($this->whenLoaded('driver')),
            'rate' => $this->resource->rate,
            'review' => $this->resource->review,
            'driver_rate' => $this->resource->driver_rate,
            'driver_review' => $this->resource->driver_review,
        ];
    }
}
