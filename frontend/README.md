# GKI Menteng — Frontend

Vue 3 + Pinia app connected to the PHP API in `../backend`.

## API integration

| Route | Store | API endpoint |
|-------|-------|----------------|
| `/` | `dashboard` | `GET /api/dashboard` |
| `/calendar` | `calendar` | `GET /api/calendar/events?year=&month=` |
| `/volunteers` | `volunteers` | `GET /api/volunteers` |
| `/about` | `about` | `GET /api/about` |

Data is loaded automatically on each route via `src/router/loaders.js`.

## Development

1. Start backend: `cd ../backend && composer serve`
2. Copy env: `cp .env.example .env` (empty `VITE_API_BASE_URL` uses Vite proxy)
3. Start frontend: `npm run dev`

Open http://localhost:5173 — requests to `/api/*` are proxied to http://localhost:8000.

## Production build

Set the real API URL before building:

```env
VITE_API_BASE_URL=https://your-api-domain.com
```

```bash
npm run build
```
