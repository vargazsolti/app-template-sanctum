# Laravel 12 + Sanctum + Breeze (Blade) + Spatie Permission Starter

A clean and production-ready starter template for building **Laravel 12** applications with:
- 🧩 Laravel Breeze (Blade) for web session authentication (admin area)
- 🔐 Sanctum for API token-based authentication (Bearer tokens)
- 🛡️ Spatie Permission for roles and permissions
- 🧑‍💼 Basic user management (CRUD + profile + password change)
- ⚙️ API endpoints (v1 prefix) with permission-based middleware
- 💾 Example seeders for demo users, base data, and roles/permissions

---

## 🚀 Features

| Feature | Description |
|----------|--------------|
| **Laravel 12 base** | Clean installation with modern directory structure |
| **Breeze (Blade)** | Web login/register/logout using session auth |
| **Sanctum API** | Token-based auth for `/api/v1/...` endpoints |
| **Spatie Roles & Permissions** | Fine-grained access control via middleware |
| **User CRUD API** | Fully protected endpoints with form requests |
| **Demo seeders** | Quickly populate with demo users & roles |
| **Admin panel (Blade)** | Manage users, roles, and permissions |
| **Password change** | Supports token invalidation + logging |
| **Postman collection** | For testing API endpoints (`postman/laravel12-sanctum-crud.postman_collection.json`) |

---

## 🧱 Folder Structure Highlights

```
app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── API/            # API controllers (Sanctum protected)
 │   │   └── Web/            # Breeze/Blade controllers (admin)
 │   ├── Requests/API/       # FormRequest validation for API
 │   └── Middleware/         # Middleware aliases in bootstrap/app.php
 ├── Models/
 │   ├── API/ApiUser.php     # API wrapper model for User
 │   └── UserBaseData.php    # Extended user base info
database/
 ├── seeders/
 │   ├── DemoUsersAndBaseDataSeeder.php
 │   ├── RolesAndPermissionsSeeder.php
 │   └── DatabaseSeeder.php
resources/
 └── views/
     ├── users/              # User management (Blade admin UI)
     └── admin/              # Roles/permissions management views
```

---

## ⚙️ Installation

### 1. Clone repository
```bash
git clone https://github.com/<your-username>/app-template-sanctum.git
cd app-template-sanctum
```

### 2. Install dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

Adjust your `.env` values (database, app URL, etc).

### 4. Run migrations and seeders
```bash
php artisan migrate:fresh --seed
```

Seeders include demo users, base data, and roles/permissions.

### 5. Run the server
```bash
php artisan serve
```

Visit `http://127.0.0.1:8000`.

---

## 🔐 Authentication Overview

| Layer | Type | Guard | Purpose |
|-------|------|--------|----------|
| **Web (Blade)** | Session | `web` | Admin area login/logout |
| **API (Sanctum)** | Bearer Token | `sanctum` | Token-based REST API |
| **Permissions** | Spatie | Middleware | Access control via `permission:` |

---

## 🧩 API Endpoints

| Endpoint | Method | Description | Middleware |
|-----------|---------|--------------|-------------|
| `/api/v1/auth/token` | POST | Get API token | Public |
| `/api/v1/auth/logout` | POST | Revoke current token | auth:sanctum |
| `/api/v1/me` | GET | Current user data | auth:sanctum |
| `/api/v1/users` | CRUD | Manage users | permission:users.* |
| `/api/v1/user-basedata` | CRUD | Manage user base data | permission:user_basedata.* |

All requests require:
```json
{ "Accept": "application/json" }
```
and for writes (POST/PUT/DELETE):
```json
{ "Content-Type": "application/json" }
```

---

## 🧑‍💻 Admin Panel (Blade)

After logging in (via Breeze session login), the admin menu provides:
- **Users**: CRUD and password management
- **Roles & Permissions**: Create/delete roles and permissions
- **User Access**: Assign roles/permissions to users

Access is controlled by `users.read` / `users.write` permissions.

---


## 🧰 Postman Collection

Import the file:
```
postman/laravel12-sanctum-crud.postman_collection.json
```
It includes:
- Auth (token / me / logout)
- CRUD for Users and UserBaseData
- Built-in variable management (`token`, `base_url`)

---

## 🪵 Logging

- Password changes are logged (`storage/logs/laravel.log`)
- When passwords are updated, **all Sanctum tokens are revoked** for that user.

---

## 🤝 Contributing

Pull requests and forks are welcome. Please keep all code PSR-12 compliant.

---

## 📄 License

This template is open-source under the **MIT License**.
