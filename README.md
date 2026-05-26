# Online Clothing E-Commerce Website

This project is an online clothing e-commerce website developed for the courses **Website Development** and **Introduction to Cybersecurity**.
It allows users to browse fashion products, view product details, manage a shopping cart, and place orders through a simple and user-friendly interface.

## Project Purpose

This project is built to practice:

- Full-stack web development
- Database design and management
- Building web APIs and integrating frontend-backend flows
- Applying basic cybersecurity concepts in a web system

## Features

- User registration and login
- User profile management (personal info, address book, password update)
- Product browsing, filtering, and product detail pages
- Shopping cart and checkout flow
- Order creation, tracking, and reorder/cancel request flows
- Promotion/voucher support in checkout
- Admin management area for products, categories, users, promotions, and orders
- Session-based authentication with password hashing and basic secure cookie settings

## Technology Stack

### Actual implementation in this repository

- **Frontend:** HTML, CSS, JavaScript, PHP-rendered pages
- **Backend:** PHP (custom MVC-style structure with controller/model routing)
- **Database:** MySQL / MariaDB (SQL dump included in `database.sql`)
- **Web server:** Apache (URL rewriting via `public/.htaccess`)
- **Version control:** Git & GitHub

### Course-oriented target stack (project proposal context)

- Frontend: HTML, CSS, JavaScript
- Backend: Python / Flask
- Database: SQLite

> Note: The repository you are viewing is currently implemented with **PHP + MySQL**, not Flask + SQLite.

## Project Structure

- `public/` - API entrypoint (`index.php`) and rewrite rules
- `app/` - backend logic (`controllers/`, `models/`, `routes.php`, `Database.php`)
- `fe/` - customer-facing pages
- `admin-main/` - admin dashboard and management pages
- `images/` - product and UI image assets
- `database.sql` - database schema and sample data

## Getting Started (3 steps)

### 1) Prerequisites

- XAMPP (or equivalent Apache + PHP + MySQL stack)
- PHP 8.x recommended
- MySQL/MariaDB running locally

### 2) Clone and place in htdocs

```bash
cd /Applications/XAMPP/xamppfiles/htdocs        # macOS XAMPP
git clone git@github.com:porukodo/ecom_clothes_web.git
```

Then fix `.env` permissions so Apache can read the encryption keys:

```bash
chmod 644 ecom_clothes_web/.env
```

### 3) Import the database

1. Start XAMPP (Apache + MySQL).
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create a new database named `ecom_clothes_web`.
4. Click **Import** → select `database.sql` → click **Go**.

That's it. The database dump already includes the V2 schema migrations, 4 000 benchmark users with encrypted PII, and 2 000 benchmark orders.

### Access the app

| Page | URL |
|------|-----|
| Frontend (shop) | `http://localhost/ecom_clothes_web/fe/` |
| Admin panel | `http://localhost/ecom_clothes_web/admin-main/` |
| API health check | `http://localhost/ecom_clothes_web/public/api/health` |
| Benchmark report | `http://localhost/ecom_clothes_web/public/benchmark_report.html` |

**Admin login:** `admin@gmail.com` / `123456789`

### (Optional) Re-run the benchmark yourself

```bash
/Applications/XAMPP/xamppfiles/bin/php scripts/run_query_benchmark.php
```

This regenerates `public/benchmark_report.html` with fresh results from your machine.

### Default DB connection

- Host: `127.0.0.1` / `localhost`
- DB name: `ecom_clothes_web`
- User: `root`
- Password: empty (`""`)

If your local configuration is different, update `app/Database.php` and `admin-main/includes/db.php`.

## Security Notes

### Encryption (V2 — Introduction to Cybersecurity)

- Customer PII (name, phone, birthday, address) is encrypted at rest using **AES-256-GCM**
- The AES data key is wrapped with **RSA-2048 OAEP** — only the encrypted key is stored in `.env`
- Encryption is applied across three tiers: `nguoi_dung` (Tier A), `dia_chi` (Tier B), `don_hang` (Tier C)
- See `app/security/` for the full implementation and `public/benchmark_report.html` for performance analysis

> **Note on `.env`:** The `.env` file containing encryption keys is intentionally committed to this
> repository so that classmates and lecturers can clone and run the project without generating their
> own keys. **In a real production system, `.env` must never be committed** — it should be listed in
> `.gitignore` and each deployment should generate its own key set via
> `php scripts/generate_encryption_keys.php`.

### General web security

- Passwords are stored as bcrypt hashes
- Session cookies are configured with `HttpOnly` and `SameSite` options
- Database access uses prepared statements (PDO) to prevent SQL injection
- Output escaping (`htmlspecialchars`) on all user-supplied data rendered in admin panel
- Role-based separation between normal users and administrators
- `.env` and `scripts/` directory blocked from web access via `.htaccess`

## Future Improvements

- Online payment integration
- Product recommendation system
- Admin dashboard UX and analytics improvements
- Enhanced security features (CSRF protection, stricter validation, rate limiting, audit logs)

## Author

Developed by **porokodo** (Phat Bui) as a university course project.
