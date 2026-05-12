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

## Getting Started

### 1) Prerequisites

- XAMPP (or equivalent Apache + PHP + MySQL stack)
- PHP 8.x recommended
- MySQL/MariaDB running locally

### 2) Clone the repository

```bash
git clone git@github.com:porukodo/ecom_clothes_web.git
cd ecom_clothes_web
```

### 3) Database setup

1. Open phpMyAdmin (or MySQL CLI).
2. Import `database.sql`.
3. Ensure database name is `PTUD_Final`.

Default DB connection in project files:

- Host: `127.0.0.1` / `localhost`
- DB name: `PTUD_Final`
- User: `root`
- Password: empty (`""`)

If your local configuration is different, update:

- `app/Database.php`
- `admin-main/includes/db.php`

### 4) Serve the project

Place the project folder under your web root (for XAMPP, typically `htdocs`) as:

`htdocs/PTUD_Final`

Then access:

- Frontend pages: `http://localhost/PTUD_Final/fe/`
- API health check: `http://localhost/PTUD_Final/public/api/health`
- Admin area: `http://localhost/PTUD_Final/admin-main/`

## Security Notes (Basic Cybersecurity Practices)

- Passwords are stored as hashes (not plaintext)
- Session cookies are configured with `HttpOnly` and `SameSite` options
- Database access uses prepared statements through PDO to reduce SQL injection risk
- Role-based separation between normal users and administrators

## Future Improvements

- Online payment integration
- Product recommendation system
- Admin dashboard UX and analytics improvements
- Enhanced security features (CSRF protection, stricter validation, rate limiting, audit logs)

## Author

Developed by **Phat Bui** as a university course project.
