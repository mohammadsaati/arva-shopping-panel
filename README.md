# Virava

Virava is a Persian, right-to-left storefront built with Laravel and Livewire. It includes a customer-facing shop, checkout, blog, and an admin panel for managing day-to-day store content and orders.

## Features

- Responsive storefront with category navigation, product search, filters, and sorting
- Product variants with colors, sizes, stock, images, and category specifications
- Shopping cart, customer addresses, checkout, order history, and city-based shipping rates
- Fixed and percentage discount codes with eligibility and usage limits
- Zarinpal payment integration and payment status tracking
- Admin tools for products, categories, brands, orders, reviews, banners, and sliders
- Blog publishing, newsletter signups, and editable store identity and footer content

## Requirements

- PHP 8.3 or newer and Composer
- Node.js and npm for building frontend assets
- A database supported by Laravel (SQLite is the default in `.env.example`)
- Redis when using the default queue configuration and Horizon

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

Set your database connection in `.env`. If you use SQLite, create the database file before migrating. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before creating the admin account.

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan storage:link
php artisan serve
```

The storefront is available at `/` and the admin login is at `/admin/login`. To add sample catalog data, run `php artisan db:seed` after configuring the admin credentials. This also runs the admin, location, and discount seeders.

## Configuration

Update the store name, logos, contact details, footer, and default SEO fields from the admin panel. Zarinpal payments require your merchant ID and an active gateway in the payment gateway settings. The default queue connection uses Redis; run Horizon and Laravel's scheduler if you need queued and scheduled jobs in production.

For deployment, configure the web server to serve the `public/` directory and set production values for `APP_URL`, `APP_ENV`, `APP_DEBUG`, mail, database, and queue settings. Keep credentials in `.env` and do not publish that file.
