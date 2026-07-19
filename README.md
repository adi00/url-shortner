# URL Shortener

A multi-company URL shortening service built with **Laravel 12** and **MySQL**.  
Each company has its own team of Admins and Members who can generate short links, with a SuperAdmin overseeing every company from a single dashboard.

---

## Table of Contents

- [Requirements](#requirements)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Roles & Permissions](#roles--permissions)
- [Step-by-Step Setup](#step-by-step-setup)
- [How the Application Works](#how-the-application-works)
  - [1. SuperAdmin Flow](#1-superadmin-flow)
  - [2. Admin Flow](#2-admin-flow)
  - [3. Member Flow](#3-member-flow)
- [URL Routes Reference](#url-routes-reference)
- [Running Tests](#running-tests)
- [Default Credentials](#default-credentials)

---

## Requirements

| Tool | Version |
|------|---------|
| PHP | ^8.2 |
| Composer | ^2.x |
| MySQL | 5.7+ / 8.x |
| Node.js | 18+ (optional, for asset builds) |

---

## Tech Stack

- **Framework** — Laravel 12
- **Database** — MySQL
- **Auth** — Laravel built-in session authentication (no Breeze/Jetstream)
- **Tests** — PHPUnit 11

---

## Project Structure

```
app/
├── Http/Controllers/
│   ├── AuthController.php         # login / logout
│   ├── DashboardController.php    # role-aware dashboard
│   ├── InvitationController.php   # send & accept invitations
│   ├── RedirectController.php     # public short URL redirect
│   └── ShortUrlController.php     # CRUD + CSV export
├── Models/
│   ├── Company.php
│   ├── Invitation.php
│   ├── ShortUrl.php
│   └── User.php
database/
├── factories/                     # CompanyFactory, ShortUrlFactory, UserFactory
├── migrations/                    # all schema migrations
└── seeders/DatabaseSeeder.php     # seeds the SuperAdmin account
resources/views/
├── auth/login.blade.php
├── dashboard.blade.php            # different panels per role
├── invitations/
│   ├── accept.blade.php
│   └── create.blade.php
├── layouts/app.blade.php
└── short-urls/
    ├── create.blade.php
    └── index.blade.php
routes/web.php
tests/Feature/ShortUrlTest.php     # 10 feature tests
```

---

## Roles & Permissions

| Action | SuperAdmin | Admin | Member |
|--------|-----------|-------|--------|
| Login / Logout | ✅ | ✅ | ✅ |
| Invite Admin to a **new** company | ✅ | ❌ | ❌ |
| Invite Admin/Member to **own** company | ❌ | ✅ | ❌ |
| Create short URL | ❌ | ✅ | ✅ |
| View **all** short URLs (every company) | ✅ | ❌ | ❌ |
| View **company** short URLs | ❌ | ✅ | ❌ |
| View **own** short URLs only | ❌ | ❌ | ✅ |
| Delete short URL | ❌ | ✅ (company) | ✅ (own) |
| Export URLs as CSV | ✅ | ✅ | ✅ |
| Public short URL redirect (`/s/{code}`) | ✅ everyone (no login needed) |

---

## Step-by-Step Setup

### Step 1 — Clone / open the project

```bash
cd path/to/url-shortener
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

### Step 3 — Copy the environment file

```bash
cp .env.example .env
```

### Step 4 — Generate the application key

```bash
php artisan key:generate
```

### Step 5 — Configure MySQL in `.env`

Open `.env` and set your database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

> The session and cache drivers are set to `file` by default, so no extra setup is needed for those.

### Step 6 — Create the MySQL database

Log into MySQL and run:

```sql
CREATE DATABASE url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 7 — Run migrations

```bash
php artisan migrate
```

This creates all tables: `companies`, `users` (with `role` + `company_id`), `invitations`, `short_urls`, plus the standard Laravel cache/jobs/sessions tables.

### Step 8 — Seed the SuperAdmin account

```bash
php artisan db:seed
```

This creates a single SuperAdmin user:

| Field | Value |
|-------|-------|
| Email | `superadmin@gmail.com` |
| Password | `password` |
| Role | `superadmin` |

### Step 9 — Start the development server

```bash
php artisan serve
```

The application is now available at **http://localhost:8000**.

---

## How the Application Works

### 1. SuperAdmin Flow

**Login**
1. Go to `http://localhost:8000/login`
2. Sign in with `superadmin@gmail.com` / `password`

**Invite a Client (creates a new company + admin)**
1. Click **Invite Client** in the top nav, or the **Invite** button on the dashboard
2. Fill in the **Company Name** and the **Admin's email address**
3. Click **Send Invitation**
4. A new company is created immediately, and the invitation token link is generated
5. Share the link `/invitations/{token}/accept` with the invited Admin
   > In production you would email this link. For now, copy it from the success message or look it up with `php artisan tinker` → `App\Models\Invitation::latest()->first()->token`

**Dashboard panels visible to SuperAdmin**
- **Clients table** — lists every company with user count and total URLs generated
- **Generated Short URLs table** — all short URLs across every company with company name and creator

---

### 2. Admin Flow

**Accept an invitation (first-time setup)**
1. Open the invitation link `/invitations/{token}/accept`
2. Enter your name and choose a password
3. Your account is created and you are logged in automatically

**Login (subsequent visits)**
1. Go to `http://localhost:8000/login`
2. Sign in with the email from your invitation

**Generate a Short URL**
1. On the dashboard, paste a long URL into the **Generate Short URL** box and click **Generate**
2. The new short link (e.g. `/s/aBcDeF`) appears in the table below

**Invite a Team Member or another Admin**
1. Click **Invite** in the nav or the **Invite** button in the Team Members panel
2. Enter the person's email and select their role (**Admin** or **Member**)
3. Click **Send Invitation** and share the token link with them

**Dashboard panels visible to Admin**
- **Generate Short URL** — quick-create form
- **Generated Short URLs** — all URLs created by anyone in their company
- **Team Members** — all users in their company with per-user URL counts

---

### 3. Member Flow

**Accept an invitation** — same process as Admin above.

**Generate a Short URL**
1. Log in and paste a URL into the **Generate Short URL** box
2. Click **Generate** — the short link is created instantly

**Dashboard panels visible to Member**
- **Generate Short URL** — quick-create form
- **Short URLs** — only the URLs that this member personally created

---

## URL Routes Reference

| Method | URL | Auth | Description |
|--------|-----|------|-------------|
| `GET` | `/` | — | Redirects to `/dashboard` |
| `GET` | `/login` | Guest | Login form |
| `POST` | `/login` | Guest | Authenticate |
| `POST` | `/logout` | Auth | Log out |
| `GET` | `/dashboard` | Auth | Role-aware dashboard |
| `GET` | `/s/{code}` | **Public** | Redirect to original URL |
| `GET` | `/short-urls` | Auth | List short URLs (scoped by role) |
| `GET` | `/short-urls/create` | Admin/Member | Create form |
| `POST` | `/short-urls` | Admin/Member | Store new short URL |
| `DELETE` | `/short-urls/{id}` | Admin/Member | Delete short URL |
| `GET` | `/short-urls/export` | Auth | Download CSV (scoped by role) |
| `GET` | `/invitations/create` | SuperAdmin/Admin | Invite form |
| `POST` | `/invitations` | SuperAdmin/Admin | Send invitation |
| `GET` | `/invitations/{token}/accept` | Guest | Accept invitation form |
| `POST` | `/invitations/{token}/accept` | Guest | Register via invitation |

---

## Running Tests

### Step 1 — Create the test database

```sql
CREATE DATABASE url_shortener_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The test database credentials are already configured in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="url_shortener_test"/>
<env name="DB_USERNAME" value="root"/>
<env name="DB_PASSWORD" value=""/>
```

Update the password value there if your MySQL root has a password.

### Step 2 — Run the test suite

```bash
php artisan test
```

### What is tested

| Test | Covers |
|------|--------|
| `admin_can_create_short_url` | Admin POST to `/short-urls` succeeds and persists to DB |
| `member_can_create_short_url` | Member POST to `/short-urls` succeeds and persists to DB |
| `superadmin_cannot_create_short_url` | SuperAdmin POST returns 403 |
| `superadmin_cannot_access_create_form` | SuperAdmin GET `/short-urls/create` returns 403 |
| `admin_can_only_see_short_urls_from_their_company` | Admin list excludes other companies' URLs |
| `member_can_only_see_their_own_short_urls` | Member list excludes other members' URLs |
| `superadmin_can_see_all_short_urls` | SuperAdmin sees URLs from all companies |
| `short_url_redirects_to_original_url` | `/s/{code}` issues a redirect to the original URL |
| `short_url_redirect_is_publicly_accessible_without_login` | Redirect works with no session |
| `short_url_returns_404_for_unknown_code` | Unknown code returns HTTP 404 |

Expected output:

```
PASS  Tests\Feature\ShortUrlTest
✓ admin can create short url
✓ member can create short url
✓ superadmin cannot create short url
✓ superadmin cannot access create form
✓ admin can only see short urls from their company
✓ member can only see their own short urls
✓ superadmin can see all short urls
✓ short url redirects to original url
✓ short url redirect is publicly accessible without login
✓ short url returns 404 for unknown code

Tests: 10 passed (23 assertions)
```

---

## Default Credentials

| Role | Email | Password | Notes |
|------|-------|----------|-------|
| SuperAdmin | `superadmin@gmail.com` | `password` | Created by seeder — no company |
| Admin | _(via invitation)_ | _(chosen on accept)_ | Belongs to a company |
| Member | _(via invitation)_ | _(chosen on accept)_ | Belongs to a company |

> **Change the SuperAdmin password** before deploying to any shared or production environment.
