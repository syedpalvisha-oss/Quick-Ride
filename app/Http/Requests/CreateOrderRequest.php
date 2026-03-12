<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Clickbar\Magellan\Data\Geometries\Dimension;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'pickup_location' => ['required', 'array', 'size:2'],
            'pickup_location.*' => ['numeric'],
            'dropoff_location' => ['required', 'array', 'size:2'],
            'dropoff_location.*' => ['numeric'],
        ];
    }

    public function dropoffLocation() {return new Point(Dimension::DIMENSION_2D, $this->float('dropoff_location.0'), $this->float('dropoff_location.1'), null, null, 4326);}

    public function pickupLocation() {return new Point(Dimension::DIMENSION_2D, $this->float('pickup_location.0'), $this->float('pickup_location.1'), null, null, 4326);}
}
