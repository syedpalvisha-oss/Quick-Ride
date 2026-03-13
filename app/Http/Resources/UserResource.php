<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = $this->resource->getAttributes();
        $vehiclesCount = $this->resource->vehicles_count ?? 0;

        return [
            'id' => $this->resource->id,
            'name' => $attributes['name'] ?? null,
            'email' => $attributes['email'] ?? null,
            'phone' => $attributes['phone'] ?? null,
            'vehicle_id' => $attributes['vehicle_id'] ?? null,
            'is_driver' => ! empty($attributes['vehicle_id']),
            'vehicles_count' => $vehiclesCount,
            'can_switch_to_driver_mode' => $vehiclesCount > 0,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'vehicles' => VehicleResource::collection($this->whenLoaded('vehicles')),
            'driver_profile' => new DriverProfileResource($this->whenLoaded('driverProfile')),
        ];
    }
}
