# OpenJek

OpenJek is a ride-hailing web app built with Laravel 12. It supports rider booking, driver matching, order lifecycle tracking, and Stripe Connect onboarding for drivers.

## App URLs

- Main app: https://openjek.com.test
- Dashboard: `/home`
- Profile: `/profile`
- API docs (Scramble): `/docs/api`

## Core Features

### Rider
- Search pickup and dropoff addresses (OpenStreetMap Nominatim).
- Pick locations directly from map center pin.
- Get fare estimates before booking.
- Book rides and track active ride status.
- Cancel rides.
- View order history from dashboard with tabs:
  - `Active`
  - `Past`
  - `Date Range`

### Driver
- Switch between rider and driver modes.
- Select active vehicle.
- View incoming compatible orders.
- Match incoming orders.
- View assigned driver orders.
- Preview route on map.

### Profile
- Update account details.
- Manage vehicles.
- Start Stripe Connect onboarding for payouts.

## Tech Stack

- PHP 8.5+
- Laravel 12
- Alpine.js
- Tailwind CSS v4
- Vite
- PostgreSQL + PostGIS (via Magellan)
- Redis + Horizon
- Laravel Sanctum (token auth)
- Stripe Connect

## Setup

### Prerequisites
- PHP 8.5+
- Composer
- Node.js + npm
- PostgreSQL with PostGIS enabled
- Redis

### Install

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Or run the bundled setup script:

```bash
composer run setup
```

## Run Locally

Use one command for app server, queue worker, logs, and Vite:

```bash
composer run dev
```

Or run only frontend dev server:

```bash
npm run dev
```

Build frontend assets:

```bash
npm run build
```

## API Overview

Authentication uses Sanctum bearer tokens.

### Auth and User
- `POST /api/personal-access-tokens` login
- `DELETE /api/personal-access-token` logout
- `POST /api/users` register
- `GET /api/user` current user
- `PUT /api/users` update profile
- `PUT /api/users/mode` switch rider/driver mode

### Orders
- `GET /api/orders` list orders
- `POST /api/orders` create order
- `GET /api/orders/{uuid}` order detail
- `POST /api/orders/{uuid}/cancel` cancel order
- `POST /api/orders/{uuid}/match` driver match order
- `POST /api/orders/{uuid}/pickup` mark pickup
- `POST /api/orders/{uuid}/complete` complete order
- `POST /api/orders/{uuid}/review` submit review

### Order list filters (`GET /api/orders`)
- `role=driver` for driver assigned orders
- `role=driver_incoming` for matchable incoming orders
- `status=active|past` for rider history tabs
- `from=YYYY-MM-DD&to=YYYY-MM-DD` for rider date range filter
- `vehicle_type` optional vehicle filter

`from` and `to` are interpreted using `X-Timezone` request header.

### Fare and Driver Tools
- `GET /api/calculate-fare`
- `POST /api/vehicles`
- `POST /api/driver/stripe/onboarding-link`
- `POST /api/stripe/webhooks/connect`

## Testing and Quality

Run tests:

```bash
php artisan test --compact
```

Run code formatting:

```bash
vendor/bin/pint --format agent
```

## Notes

- If frontend changes are not visible, run `npm run dev` or `npm run build`.
- The app is served by Laravel Herd at `https://openjek.com.test`.
