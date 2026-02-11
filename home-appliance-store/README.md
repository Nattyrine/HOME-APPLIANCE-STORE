# Home Appliance Store

Scaffold for a home appliance store with an ordering system.

Structure

- public/: Browser-facing pages (`index.php`, `login.php`, `register.php`, `cart.php`, `orders.php`)
- api/: Backend endpoints (`api/auth`, `api/products`, `api/orders`)
- config/: Configuration (`database.php`)
- assets/: Static assets (css, js, images)
- sql/: Database schema (`home_appliance_store.sql`)

Quick setup

1. Import `sql/home_appliance_store.sql` into MySQL.
2. Update `config/database.php` credentials.
3. Serve the `public/` folder from your webserver document root.

Next tasks you can ask me to implement:
- Wire real DB logic into the API endpoints
- Add session-based auth and JWT token support
- Build admin pages to manage products and orders
