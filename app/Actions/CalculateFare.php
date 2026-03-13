<?php

namespace App\Actions;

use App\Enums\Currency;
use App\Enums\VehicleType;
use Illuminate\Support\Facades\DB;

class CalculateFare
{
    /**
     * @return array<int, object>
     */
    public function __invoke(
        array $pickupLocation,
        array $dropoffLocation,
        ?VehicleType $vehicleType = null,
        ?Currency $currency = null,
    ): array {
        $sql = <<<'SQL'
with points as (
    select
        ST_SetSRID(ST_MakePoint(:pickup_lng, :pickup_lat), 4326)::geography as pickup_geography,
        ST_SetSRID(ST_MakePoint(:dropoff_lng, :dropoff_lat), 4326)::geography as dropoff_geography
),
rate_card as (
    select *
    from (
        values
            ('IDR', 0, 5000::numeric(19,2), 2200::numeric(19,2), 1500::numeric(19,2), 7000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('IDR', 1, 9000::numeric(19,2), 3500::numeric(19,2), 2500::numeric(19,2), 12000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('USD', 0, 2::numeric(19,2), 1::numeric(19,2), 0.3::numeric(19,2), 5::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('USD', 1, 5::numeric(19,2), 1.5::numeric(19,2), 0.5::numeric(19,2), 7::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('EUR', 0, 2::numeric(19,2), 1::numeric(19,2), 0.3::numeric(19,2), 5::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('EUR', 1, 5::numeric(19,2), 1.5::numeric(19,2), 0.5::numeric(19,2), 7::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('SGD', 0, 2::numeric(19,2), 1::numeric(19,2), 0.3::numeric(19,2), 5::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('SGD', 1, 5::numeric(19,2), 1.5::numeric(19,2), 0.5::numeric(19,2), 7::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('MYR', 0, 2::numeric(19,2), 1::numeric(19,2), 0.3::numeric(19,2), 5::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('MYR', 1, 5::numeric(19,2), 1.5::numeric(19,2), 0.5::numeric(19,2), 7::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('PHP', 0, 2::numeric(19,2), 1::numeric(19,2), 0.3::numeric(19,2), 5::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('PHP', 1, 5::numeric(19,2), 1.5::numeric(19,2), 0.5::numeric(19,2), 7::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('THB', 0, 5000::numeric(19,2), 2200::numeric(19,2), 1500::numeric(19,2), 7000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('THB', 1, 9000::numeric(19,2), 3500::numeric(19,2), 2500::numeric(19,2), 12000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('VND', 0, 5000::numeric(19,2), 2200::numeric(19,2), 1500::numeric(19,2), 7000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2)),
            ('VND', 1, 9000::numeric(19,2), 3500::numeric(19,2), 2500::numeric(19,2), 12000::numeric(19,2), 0::numeric(19,2), 1::numeric(6,2))
        ) as rates(
            currency_id,
            vehicle_type,
            base_fare,
            per_km_fare,
            booking_fee,
            minimum_fare,
            safety_fee,
            surge_multiplier
    )
),
components as (
    select
        r.vehicle_type,
        r.currency_id,
        r.base_fare,
        r.per_km_fare,
        r.booking_fee,
        r.minimum_fare,
        r.safety_fee,
        r.surge_multiplier,
        ST_Distance(p.pickup_geography, p.dropoff_geography) as distance_meters
    from points p
    join rate_card r on true
    where (:currency_id::text is null or r.currency_id = :currency_id::text)
        and (:vehicle_type::integer is null or r.vehicle_type = :vehicle_type::integer)
),
totals as (
    select
        vehicle_type,
        currency_id,
        round(distance_meters::numeric, 2) as distance_meters,
        round((distance_meters / 1000)::numeric, 3) as distance_km,
        base_fare,
        per_km_fare,
        booking_fee,
        minimum_fare,
        safety_fee,
        surge_multiplier,
        round(((distance_meters / 1000) * per_km_fare)::numeric, 2) as distance_fare,
        round((base_fare + ((distance_meters / 1000) * per_km_fare) + booking_fee + safety_fee)::numeric, 2) as subtotal_before_minimum
    from components
),
final_totals as (
    select
        vehicle_type,
        currency_id,
        distance_meters,
        distance_km,
        base_fare,
        per_km_fare,
        booking_fee,
        minimum_fare,
        safety_fee,
        surge_multiplier,
        distance_fare,
        subtotal_before_minimum,
        greatest(subtotal_before_minimum, minimum_fare) as subtotal,
        greatest(minimum_fare - subtotal_before_minimum, 0) as minimum_fare_adjustment
    from totals
)
select
    vehicle_type,
    currency_id,
    distance_meters,
    distance_km,
    base_fare,
    per_km_fare,
    distance_fare,
    booking_fee,
    safety_fee,
    minimum_fare,
    minimum_fare_adjustment,
    subtotal,
    surge_multiplier,
    round((subtotal * surge_multiplier - subtotal)::numeric, 2) as surge_amount,
    round((subtotal * surge_multiplier)::numeric, 2) as total_fare
from final_totals
order by currency_id asc
SQL;

        return DB::select($sql, [
            'pickup_lat' => $pickupLocation[0],
            'pickup_lng' => $pickupLocation[1],
            'dropoff_lat' => $dropoffLocation[0],
            'dropoff_lng' => $dropoffLocation[1],
            'vehicle_type' => $vehicleType?->value,
            'currency_id' => $currency?->value,
        ]);
    }
}
