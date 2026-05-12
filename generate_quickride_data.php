<?php

$locations = [
    "Downtown/CBD", 
    "Tech Park", 
    "Airport", 
    "Central Station", 
    "University Campus", 
    "Suburban Residential", 
    "Shopping Mall",
    "Hospital District"
];

$payment_methods = ["UPI", "Credit Card", "Debit Card", "Cash", "Wallet"];
// UPI, CC, DC, Cash, Wallet
$pay_weights = [40, 25, 15, 10, 10]; 

function get_random_weighted($values, $weights) {
    $rand = mt_rand(1, (int)array_sum($weights));
    foreach ($values as $key => $value) {
        $rand -= $weights[$key];
        if ($rand <= 0) {
            return $value;
        }
    }
    return $values[0];
}

$statuses = ["Completed", "Cancelled", "Completed", "Completed", "Completed"]; // 20% base chance for cancel

$start_date = strtotime("2023-10-01");
$num_rows = 650;
$filename = "quick_ride_data.csv";

$fp = fopen($filename, 'w');
$headers = [
    "ride_id", "user_id", "date", "time", "hour", "day_of_week", 
    "pickup_location", "drop_location", "fare_amount", "ride_status", "payment_method"
];
fputcsv($fp, $headers);

for ($i = 1; $i <= $num_rows; $i++) {
    $ride_id = "QR" . (10000 + $i);
    $user_id = "U" . mt_rand(100, 999);
    
    $random_days = mt_rand(0, 90);
    $base_timestamp = $start_date + ($random_days * 86400);
    
    // Peak vs non-peak
    $is_peak = (mt_rand(1, 100) <= 40);
    if ($is_peak) {
        $hour = mt_rand(17, 21);
    } else {
        $non_peak_hours = array_merge(range(6, 16), range(22, 23), [0, 1, 2]);
        $hour = $non_peak_hours[array_rand($non_peak_hours)];
    }
    
    $minute = mt_rand(0, 59);
    $second = mt_rand(0, 59);
    
    $ride_time = mktime($hour, $minute, $second, date("m", $base_timestamp), date("d", $base_timestamp), date("Y", $base_timestamp));
    
    $pickup_location = $locations[array_rand($locations)];
    
    $available_drops = array_filter($locations, function($l) use ($pickup_location) { return $l !== $pickup_location; });
    $drop_location = $available_drops[array_rand($available_drops)];
    
    $status = $statuses[array_rand($statuses)];
    
    // Cancellation biases
    if ($pickup_location === "Airport" && mt_rand(1, 100) <= 30) {
        $status = "Cancelled";
    }
    if ($hour >= 22 || $hour <= 2) {
        if (mt_rand(1, 100) <= 25) {
            $status = "Cancelled";
        }
    }
    
    $base_fare = 5.0;
    $dist_factor = mt_rand(200, 1500) / 100; // 2.0 to 15.0
    $fare_amount = round($base_fare + ($dist_factor * 1.5), 2);
    
    if ($is_peak) {
        $surge_multiplier = mt_rand(120, 150) / 100;
        $fare_amount = round($fare_amount * $surge_multiplier, 2);
    }
    
    $payment_method = get_random_weighted($payment_methods, $pay_weights);
    
    if ($status === "Cancelled") {
        $fare_amount = 0.0;
    }
    
    $date_str = date("Y-m-d", $ride_time);
    $time_str = date("H:i:s", $ride_time);
    $day_of_week = date("l", $ride_time); // "l" stands for full textual representation of the day of the week
    
    fputcsv($fp, [
        $ride_id, $user_id, $date_str, $time_str, $hour, $day_of_week, 
        $pickup_location, $drop_location, $fare_amount, $status, $payment_method
    ]);
}

fclose($fp);
echo "Successfully generated $num_rows rows of data in $filename\n";

?>
