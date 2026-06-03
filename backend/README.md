# GKI Menteng — PHP API Backend

REST API for the Vue frontend, built with plain PHP 8.1+, MySQL, and a middleware pipeline.

## Structure

```
backend/
├── bootstrap/app.php       # App bootstrap & middleware registration
├── config/                 # (reserved for future config files)
├── database/
│   ├── schema.sql          # Tables
│   └── seed.sql            # Sample data (matches frontend mock data)
├── public/
│   ├── index.php           # Front controller
│   ├── router.php          # PHP built-in server router
│   └── .htaccess           # Apache rewrite
├── routes/api.php          # API route definitions
└── src/
    ├── Bootstrap/          # Application entry
    ├── Config/             # Environment loader
    ├── Controllers/        # HTTP controllers
    ├── Core/               # Router, Request, Response, Database
    ├── Middleware/         # CORS, JSON, error handling
    └── Repositories/       # Database queries
```

## Middleware pipeline

Every request passes through (in order):

1. **ErrorHandlerMiddleware** — catches exceptions, returns JSON errors
2. **CorsMiddleware** — CORS headers from `APP_FRONTEND_URL`
3. **JsonResponseMiddleware** — ensures JSON `Content-Type`

## API endpoints

| Method | Path | Description |
|--------|------|-------------|
| GET | `/api/health` | Health check + DB status |
| GET | `/api/dashboard` | News, reflection, events, stats |
| GET | `/api/calendar/events` | Calendar events (`?year=&month=` optional) |
| GET | `/api/volunteers` | Volunteers, ministries, Sunday schedule |
| GET | `/api/about` | Church profile, pastoral team, activities |
| POST | `/api/auth/register` | Create account (name, email, password, passwordConfirmation) |
| POST | `/api/auth/login` | Sign in (email, password) |
| POST | `/api/auth/refresh` | Rotate session (httpOnly refresh cookie) |
| POST | `/api/auth/logout` | Revoke refresh token |
| GET | `/api/auth/me` | Current user (`Authorization: Bearer <access>`) |

### Authentication

- Passwords are hashed with PHP `password_hash()` (bcrypt/argon2).
- Short-lived **JWT access tokens** (default 15 minutes) sent in the `Authorization` header.
- Long-lived **refresh tokens** stored hashed in MySQL and delivered as an **httpOnly** cookie (`SameSite=Lax`).
- Refresh tokens rotate on each use; logout revokes the active token.

Run the `users` and `auth_refresh_tokens` tables from `database/schema.sql` if upgrading an existing database.

Set a strong `JWT_SECRET` in `.env` for production.

**Guest access:** `GET` endpoints are public. `POST` / `PUT` / `DELETE` on `/api/calendar/events` and `/api/volunteers` require a valid Bearer access token.

All successful responses:

```json
{ "success": true, "data": { ... } }
```

## Setup

### 1. Install PHP dependencies

```bash
cd backend
composer install
```

### 2. Configure environment

Copy `.env.example` to `.env` (or edit your existing `.env`):

```env
APP_URL=http://localhost:8000
APP_FRONTEND_URL=http://localhost:5173
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=gki_menteng
DB_USER=root
DB_PASS=your_password
```

### 3. Create database

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

### 4. Run the server

```bash
composer serve
```

API base URL: `http://localhost:8000`

## Frontend connection

The Vue app uses `VITE_API_BASE_URL=http://localhost:8000` in `frontend/.env`.
