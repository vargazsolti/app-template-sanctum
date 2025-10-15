
# Vue 3 Frontend – Setup Guide (ui.app.test)

This document describes how to configure and run the **Vue 3 + Vite + Tailwind + Laravel Sanctum** frontend environment
connected to a Laravel backend running on a separate domain (`app.test` and `ui.app.test`).

---

## 🧱 Overview

The project runs in two separate environments:

- **Backend (Laravel)** → `http://app.test`
- **Frontend (Vue 3 + Vite)** → `http://ui.app.test:5173`

Laravel **Sanctum** handles authentication, CSRF protection, and session-based login across domains.
All CORS and cookie settings are configured to support this setup.

---

## ⚙️ Requirements

- Node.js 20+
- NPM 9+
- PHP 8.2+
- Composer 2+
- WAMP / XAMPP / Valet (Apache)
- Host entries for both `app.test` and `ui.app.test`

---

## 📁 Folder Structure

```
frontend/
├─ src/
│  ├─ views/           # Page components (Login, Dashboard, etc.)
│  ├─ components/      # Reusable UI components
│  ├─ router.js        # Vue Router configuration
│  ├─ stores/          # Pinia stores (auth, user, etc.)
│  └─ services/api.js  # Axios wrapper (Sanctum-compatible)
├─ public/
├─ index.html
├─ package.json
├─ vite.config.js
└─ .env
```

---

## 🌐 Domain Configuration (WAMP)

### `C:\Windows\System32\drivers\etc\hosts`
```
127.0.0.1 app.test
127.0.0.1 ui.app.test
::1 app.test
::1 ui.app.test
```

### `httpd-vhosts.conf`
```apache
<VirtualHost *:80>
    ServerName app.test
    DocumentRoot "c:/wamp64/www/app-template-sanctum/public"
    <Directory "c:/wamp64/www/app-template-sanctum/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

*(You don’t need a separate VirtualHost for the frontend — the Vite dev server handles it.)*

---

## ⚙️ Frontend Configuration

### `.env`
```
VITE_BACKEND_URL=http://app.test
```

### `vite.config.js`
```js
export default defineConfig({
  server: {
    host: 'ui.app.test',
    port: 5173,
  },
  plugins: [vue()],
});
```

### `src/services/api.js`
Axios wrapper for Sanctum:

```js
import axios from 'axios';

export const api = axios.create({
  baseURL: import.meta.env.VITE_BACKEND_URL,
  withCredentials: true,
});

export async function ensureCsrf() {
  await api.get('/sanctum/csrf-cookie');
}
```

---

## 🚀 Run the Project

### 1️⃣ Install dependencies
```bash
cd frontend
npm install
```

### 2️⃣ Start the dev server
```bash
npm run dev
```

Open [http://ui.app.test:5173](http://ui.app.test:5173)

The Laravel backend must be running at `http://app.test`.

---

## 🔐 Sanctum Authentication Flow

1. The frontend calls `/sanctum/csrf-cookie`.
2. Laravel sets `XSRF-TOKEN` and `laravel_session` cookies (Domain: `.app.test`).
3. The frontend sends a POST request to `/spa/login`.
4. Laravel authenticates and creates a user session.
5. The frontend fetches user data from `/api/me`.

---

## 🧹 Troubleshooting

| Error | Cause | Solution |
|-------|--------|-----------|
| `CSRF token mismatch` | Missing or invalid cookie | Ensure `SESSION_DOMAIN=.app.test` |
| `CORS error` | CORS misconfiguration | `config/cors.php` → allowed_origins must include `ui.app.test:5173` |
| `ERR_CONNECTION_REFUSED` | Apache not serving app.test | Check vhost and hosts configuration |
| `Invalid credentials` | Wrong email/password | Use valid Laravel user (e.g. `user1@example.com`) |

---

## 🧭 Checklist

- [x] `app.test` loads in browser  
- [x] `ui.app.test:5173` runs (Vite dev server)  
- [x] `GET /sanctum/csrf-cookie` → 204  
- [x] `XSRF-TOKEN` and `laravel_session` cookies appear (Domain `.app.test`)  
- [x] Login works → `/api/me` returns user info  

---

## 🏁 Summary

This configuration sets up a fully functional **Vue 3 SPA + Laravel Sanctum** environment with separate domains that communicate through secure session-based authentication.

> For development: `npm run dev`  
> For production: `npm run build` → serve the `dist/` directory under `ui.app.test`
