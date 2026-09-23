# MySQL database setup

This Laravel app uses MySQL. Locally, XAMPP's compatible MariaDB server can provide the database.

1. Start **MySQL** in the XAMPP Control Panel.
2. Create a database named `dani_portfolio` in phpMyAdmin.
3. Set the `DB_*` values in your local `.env` to match your database and credentials. The committed `.env.example` shows the required fields.
4. From this `laravel` directory, run `php artisan migrate` and then `composer run dev`.

Tests use a separate MySQL database named `dani_portfolio_test`. Create it before running `php artisan test`. **Never point tests at `dani_portfolio`**: tests reset their database tables.

For deployment, create a separate hosted MySQL database. Set its `DB_*` values as server environment variables and run `php artisan migrate --force` during deployment. Do not commit `.env` or publish your local XAMPP database.
