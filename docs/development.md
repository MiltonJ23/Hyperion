# Development

## Prerequisites

- PHP 8.2+
- Composer
- Node.js (for Vite, optional)
- MySQL 8 or Docker

## Clone and configure

```bash
git clone https://github.com/MiltonJ23/Hyperion.git
cd Hyperion/server/HyperionServer
cp .env.example .env
```

Edit `.env` and set the database connection:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Hyperion
DB_USERNAME=root
DB_PASSWORD=<password>
```

## Install dependencies

```bash
composer install
```

## Generate keys

```bash
php artisan key:generate
php artisan jwt:secret
```

## Database

Create the database and run migrations:

```bash
mysql -u root -p -e "CREATE DATABASE Hyperion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

The reference schema is in `Schemas.sql` at the repository root. It can be applied directly if preferred over running migrations:

```bash
mysql -u root -p Hyperion < ../../Schemas.sql
```

## Run the development server

```bash
php artisan serve
```

The API is available at `http://localhost:8000`.

## Frontend

The frontend is static HTML. Open `client/src/index.html` directly in a browser or serve it with any static file server. The HTML files reference Bootstrap from the `server/HyperionServer/public/Library/` directory, so the Laravel server must be running for the linked assets to resolve correctly.

## Running with Docker Compose

If you prefer not to install PHP locally, use Docker Compose:

```bash
# From the repository root
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan jwt:secret
docker compose exec app php artisan migrate
```

See [deployment.md](deployment.md) for environment variable details.

## Tests

```bash
cd server/HyperionServer
php artisan test
```

PHPUnit is configured in `phpunit.xml`. The test suite is located in `tests/`.

## Directory reference (Laravel application)

```
server/HyperionServer/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      JWT login, logout, refresh, me
│   │   │   ├── UserController.php      User CRUD and relations
│   │   │   ├── EventController.php     Event CRUD, booking, images
│   │   │   ├── CardController.php      Payment card management
│   │   │   ├── CartController.php      Cart add/remove/show
│   │   │   ├── CheckoutController.php  Checkout processing
│   │   │   └── BookingController.php   Booking retrieval
│   │   └── Middleware/
│   └── Models/
│       ├── User.php
│       ├── Event.php
│       ├── Book.php
│       ├── Ticket.php
│       ├── Card.php
│       ├── Cart.php
│       └── Images.php
├── routes/
│   └── web.php         All API route definitions
├── database/
│   └── migrations/     Schema migration files
└── public/
    └── Library/        Vendored Bootstrap assets
```
