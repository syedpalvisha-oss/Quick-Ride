import csv
import random
from datetime import datetime, timedelta

def generate_quickride_data(filename="quick_ride_data.csv", num_rows=500):
    locations = [
        "Downtown/CBD", 
        "Tech Park", 
        "Airport", 
        "Central Station", 
        "University Campus", 
        "Suburban Residential", 
        "Shopping Mall",
        "Hospital District"
    ]
    
    payment_methods = ["UPI", "Credit Card", "Debit Card", "Cash", "Wallet"]
    statuses = ["Completed", "Cancelled", "Completed", "Completed", "Completed"] # 20% base chance for cancel

    start_date = datetime(2023, 10, 1)
    
    data = []
    
    for i in range(1, num_rows + 1):
        ride_id = f"QR{10000 + i}"
        user_id = f"U{random.randint(100, 999)}"
        
        # Pick a random date within the last 3 months
        random_days = random.randint(0, 90)
        base_date = start_date + timedelta(days=random_days)
        
        # Generate time with peak hour bias (5 PM - 9 PM)
        is_peak = random.random() < 0.40 # 40% of rides in peak hours
        if is_peak:
            hour = random.randint(17, 21)
        else:
            # other times, avoid super early morning but some available
            hour = random.choice(list(range(6, 17)) + list(range(22, 24)) + [0, 1, 2])
            
        minute = random.randint(0, 59)
        second = random.randint(0, 59)
        ride_time = datetime(base_date.year, base_date.month, base_date.day, hour, minute, second)
        
        pickup_location = random.choice(locations)
        drop_location = random.choice([loc for loc in locations if loc != pickup_location])
        
        status = random.choice(statuses)
        
        # Cancellation bias: slightly higher cancellation at Airport and late nights
        if pickup_location == "Airport" and random.random() < 0.3:
            status = "Cancelled"
        if hour >= 22 or hour <= 2:
            if random.random() < 0.25:
                status = "Cancelled"

        # Fare calculation based on location "distance" concept
        # Let's assign rough distances
        base_fare = 5.0
        dist_factor = random.uniform(2.0, 15.0)
        fare_amount = round(base_fare + (dist_factor * 1.5), 2)
        
        # Peak time surge pricing
        if is_peak:
            fare_amount = round(fare_amount * random.uniform(1.2, 1.5), 2)
            
        # Payment method bias (More UPI/Card)
        pay_weights = [0.4, 0.25, 0.15, 0.1, 0.1] # UPI, CC, DC, Cash, Wallet
        payment_method = random.choices(payment_methods, weights=pay_weights, k=1)[0]
        
        # If cancelled, maybe no fare or cancellation fee. Let's make fare 0 for cancelled to make revenue analysis cleaner
        if status == "Cancelled":
            fare_amount = 0.0
            
        # Dates and Times formatting
        date_str = ride_time.strftime("%Y-%m-%d")
        time_str = ride_time.strftime("%H:%M:%S")
        day_of_week = ride_time.strftime("%A")
        
        data.append([
            ride_id, user_id, date_str, time_str, hour, day_of_week, 
            pickup_location, drop_location, fare_amount, status, payment_method
        ])
        
    # Write to CSV
    headers = [
        "ride_id", "user_id", "date", "time", "hour", "day_of_week", 
        "pickup_location", "drop_location", "fare_amount", "ride_status", "payment_method"
    ]
    
    with open(filename, mode='w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(headers)
        writer.writerows(data)
        
    print(f"Successfully generated {num_rows} rows of data in {filename}")

if __name__ == "__main__":
    generate_quickride_data("quick_ride_data.csv", 650)
