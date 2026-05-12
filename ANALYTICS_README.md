# Quick Ride: Product & Operations Analytics

## Overview
This analytical project evaluates the operational and revenue data of **Quick Ride**, a ride-booking application. The goal of this analysis is to identify key business drivers, optimize pricing strategies, and reduce operational friction (cancellations) by translating raw transactional data into actionable product insights.

A professional Power BI dashboard was built to visualize KPIs and provide dynamic filtering for operational deep dives.

![Power BI Dashboard Screenshot Placeholder - Top KPIs ]() <!-- Add your Power BI screenshot here -->

## The Data Model
The analysis is based on application database exports containing ride-level transactional data:
* **Volume:** ~650 recent rides across 8 distinct city zones.
* **Key Dimensions:** Time (Hour, Day of Week), Location (Pickup/Drop), Payment Method.
* **Key Metrics:** Ride Status (Completed/Cancelled), Fare Amount (Revenue).

## Key Business Insights

Based on the dashboard analysis, the following strategic insights were uncovered:

### 1. Operations: Concentrated Peak Demand
**Finding:** Over **40% of all ride volume** occurs within a tight 4-hour window (5:00 PM - 9:00 PM).
**Business Impact:** This massive concentration of demand leads to driver supply shortages. 
**Recommendation:** Implement dynamic surge pricing algorithms starting slightly earlier (4:30 PM) to incentivize driver staging and smooth the demand curve.

### 2. Pain Point: Location-Specific Cancellations
**Finding:** While the baseline cancellation rate sits around 15-20%, pickups at the **Airport zone** experience a significantly higher cancellation rate (upwards of 30%). Furthermore, overall cancellations spike sharply between 10:00 PM and 2:00 AM.
**Business Impact:** High cancellation rates during late hours at critical transport hubs represent massive lost revenue and poor user experience.
**Recommendation:** Introduce a non-refundable "Airport Staging Fee" for drivers to guarantee their earnings and penalize driver-initiated late-night cancellations.

### 3. Revenue Drivers: The Corporate Corridor
**Finding:** The "Downtown/CBD" and "Tech Park" zones are the undisputed anchors of overall revenue.
**Business Impact:** These areas generate the highest average fares and the most consistent daytime volume.
**Recommendation:** Launch B2B (Business-to-Business) corporate ride packages specifically targeting companies located in the Tech Park and CBD to lock in recurring revenue.

### 4. Product: Digital Payment Dominance
**Finding:** **UPI and Credit Cards** account for the vast majority (approx. 65%+) of all transactions, whereas Cash sits at barely 10%.
**Business Impact:** Cash handling is no longer a core operational requirement.
**Recommendation:** Deprioritize cash-collection infrastructure and focus product engineering resources on streamlining one-click UPI checkouts and digital wallet integrations. 

![Power BI Dashboard Screenshot Placeholder - Charts ]() <!-- Add your Power BI screenshot here -->

## Technical Execution
- **Data Engineering:** Extracted and cleaned raw application data (CSV format), transforming timestamps and normalizing location strings.
- **Data Modeling:** Built DAX measures in Power BI for advanced KPI tracking (`Cancellation Rate`, `Average Fare`).
- **Visualization:** Developed interactive visual reports emphasizing data-to-ink ratio best practices (Clustered Bar Charts, Area Demand Curves).

## How to use this project
To view the dataset:
`quick_ride_data.csv` is located in the root directory.

To recreate the Power BI file:
Please follow the exact steps outlined in `POWER_BI_GUIDE.md`.
