# Online Clothing E-Commerce Website — V1

> **Branch guide**
> | Branch | What it contains |
> |--------|-----------------|
> | `V0` | Original project snapshot — no encryption, no security hardening |
> | **`V1`** ← you are here | Encryption code applied; **keys not included** — generate your own |
> | `V2` | Fully ready-to-run — keys committed, 4 000 benchmark users pre-seeded |

This project is an online clothing e-commerce website developed for the courses **Website Development** and **Introduction to Cybersecurity**.

## What V1 adds over V0

- **AES-256-GCM field-level encryption** on all customer PII (name, phone, birthday, address, order recipient)
- **RSA-2048 OAEP key wrapping** — the raw AES key is never stored in plain text
- **bcrypt** password hashing, prepared statements (PDO), `htmlspecialchars` output escaping, `HttpOnly`/`SameSite` cookies
- Scripts to generate your own keys (`scripts/generate_encryption_keys.php`) and optionally seed benchmark data (`scripts/seed_test_data.php`)
- `.env` is **gitignored** on this branch — you must generate your own keys (see setup below)

## Features

- User registration and login
- User profile management (personal info, address book, password update)
- Product browsing, filtering, and product detail pages
- Shopping cart and checkout flow
- Order creation, tracking, reorder/cancel request flows
- Promotion/voucher support in checkout
- Admin management area for products, categories, users, promotions, and orders

## Technology Stack

- **Frontend:** HTML, CSS, JavaScript, PHP-rendered pages
- **Backend:** PHP (custom MVC-style structure with controller/model routing)
- **Database:** MySQL / MariaDB
- **Web server:** Apache (URL rewriting via `public/.htaccess`)

## Project Structure

- `public/` — API entrypoint (`index.php`) and rewrite rules
- `app/` — backend logic (`controllers/`, `models/`, `routes.php`, `Database.php`)
- `app/security/` — `EncryptionService`, `KeyManager`, `PiiFields`
- `fe/` — customer-facing pages
- `admin-main/` — admin dashboard and management pages
- `images/` — product and UI image assets
- `database.sql` — schema + real sample data (no benchmark seed records)
- `scripts/` — key generation, data seeding, benchmarking tools

## Getting Started

### 1) Prerequisites

- XAMPP (or equivalent Apache + PHP + MySQL stack) with **PHP 8.x**
- OpenSSL extension enabled in PHP (required for key generation)

### 2) Clone and place in htdocs

```bash
cd /Applications/XAMPP/xamppfiles/htdocs        # macOS XAMPP
git clone -b V1 git@github.com:porukodo/ecom_clothes_web.git
```

### 3) Generate encryption keys

This creates `.env` with a fresh RSA-2048 key pair and a wrapped AES-256 key:

```bash
/Applications/XAMPP/xamppfiles/bin/php ecom_clothes_web/scripts/generate_encryption_keys.php
```

Then fix permissions so Apache can read the file:

```bash
chmod 644 ecom_clothes_web/.env
```

### 4) Import the database

1. Start XAMPP (Apache + MySQL).
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create a new database named `ecom_clothes_web`.
4. Click **Import** → select `database.sql` → click **Go**.

### 5) (Optional) Seed benchmark data

```bash
/Applications/XAMPP/xamppfiles/bin/php ecom_clothes_web/scripts/seed_test_data.php
```

Inserts 4 000 users and 2 000 orders with encrypted PII for performance testing.

### Access the app

| Page | URL |
|------|-----|
| Frontend (shop) | `http://localhost/ecom_clothes_web/fe/` |
| Admin panel | `http://localhost/ecom_clothes_web/admin-main/` |
| API health check | `http://localhost/ecom_clothes_web/public/api/health` |

**Admin login:** `admin@gmail.com` / `123456789`

### Default DB connection

- Host: `127.0.0.1` / `localhost`
- DB name: `ecom_clothes_web`
- User: `root`
- Password: empty (`""`)

If your local configuration is different, update `app/Database.php` and `admin-main/includes/db.php`.

## Security Notes

### Encryption (V1 — Introduction to Cybersecurity)

- Customer PII (name, phone, birthday, address) is encrypted at rest using **AES-256-GCM**
- The AES data key is wrapped with **RSA-2048 OAEP** — only the encrypted key blob is stored in `.env`
- Encryption is applied across three tiers:
  - **Tier A** `nguoi_dung` — `ho_ten`, `so_dien_thoai`, `ngay_sinh`
  - **Tier B** `dia_chi` — all six address fields
  - **Tier C** `don_hang` — `nguoi_nhan`, `sdt_nguoi_nhan`, `dia_chi_giao_hang`
- See `app/security/` for the full implementation

> **Why `.env` is not committed here:**  
> On this branch, `.env` is gitignored as it should be in any real deployment.
> Each environment generates its own key pair via `generate_encryption_keys.php`.
> If you want a pre-keyed, clone-and-run version, switch to the **`V2`** branch.

### General web security

- Passwords stored as bcrypt hashes
- Session cookies with `HttpOnly` and `SameSite` options
- Database access via PDO prepared statements (SQL injection protection)
- Output escaping (`htmlspecialchars`) on all user-supplied data in the admin panel
- Role-based separation between normal users and administrators
- `.env` and `scripts/` blocked from web access via `.htaccess`

## Author

Developed by **porokodo** (Phat Bui) as a university course project.
