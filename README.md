# NM System — ISP Management System

نظام إدارة مزود الخدمة (ISP Management System) مبني على Laravel 12 + Vue 3 + Inertia.js + MySQL.

## Stack

- **Backend:** Laravel 12 (PHP 8.3)
- **Database:** MySQL 8
- **Frontend:** Vue 3 + Inertia.js + Tailwind CSS
- **Auth:** Laravel Sanctum
- **Server:** Nginx + PHP-FPM
- **PDF:** barryvdh/laravel-dompdf

## Modules

- Dashboard (إحصائيات ورسوم بيانية)
- Customers (إدارة المشتركين الكاملة)
- Plans (الباقات)
- Billing (فواتير، مدفوعات، مصروفات، صندوق، ديون)
- Inventory (منتجات، تصنيفات، موردين، حركة مخزون)
- Employees (موظفين، أقسام، حضور، إجازات)
- Support Tickets (تذاكر الدعم)
- MikroTik Routers (إدارة الأجهزة)
- Reports (تقارير شاملة)
- Activity Logs (سجلات النشاط)
- Settings (الإعدادات)

## Quick Start (Local)

```bash
# 1. Install PHP dependencies
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Create MySQL database
mysql -u root -e "CREATE DATABASE nm_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Run migrations & seed
php artisan migrate --seed

# 5. Install frontend & build
npm install
npm run dev   # development
# npm run build  # production

# 6. Start dev server
php artisan serve
```

Visit http://localhost:8000

**Default login:** `admin@nm.iq` / `admin123`

## Production Deployment (Ubuntu Server)

```bash
# Clone the project to /var/www/nm-system
sudo git clone <repo> /var/www/nm-system
cd /var/www/nm-system

# Run the deployment script
chmod +x deploy/deploy.sh
sudo ./deploy/deploy.sh
```

The script will:
1. Install Nginx, MySQL, PHP 8.3, Composer, Node.js
2. Create the MySQL database and user
3. Install composer dependencies
4. Generate app key and configure .env
5. Run migrations and seeders
6. Build frontend assets
7. Configure Nginx and restart services

## REST API

All endpoints are under `/api/v1` and require a Bearer token (Sanctum):

```
POST   /api/login                    # Get API token
POST   /api/logout                    # Revoke token
GET    /api/me                        # Current user

GET    /api/customers                 # List (paginated, searchable)
POST   /api/customers                 # Create
GET    /api/customers/{id}             # Show
PUT    /api/customers/{id}             # Update
DELETE /api/customers/{id}             # Delete
POST   /api/customers/{id}/suspend     # Suspend
POST   /api/customers/{id}/activate   # Activate
POST   /api/customers/{id}/renew       # Renew subscription

GET    /api/plans ...                  # Full CRUD
GET    /api/invoices ...               # Full CRUD
POST   /api/invoices/{id}/pay          # Mark paid
GET    /api/expenses ...               # Full CRUD
GET    /api/products ...               # Inventory CRUD
GET    /api/employees ...              # Full CRUD
GET    /api/tickets ...                # Full CRUD
POST   /api/tickets/{id}/reply         # Add reply
GET    /api/routers ...                # Full CRUD
GET    /api/routers/{id}/test          # Test connection
GET    /api/reports                    # Report stats
GET    /api/logs                       # Activity logs
GET    /api/settings                   # Get settings
PUT    /api/settings                   # Update settings
```

## Roles & Permissions

| Role | Access |
|------|--------|
| super_admin | Everything |
| manager | All modules except system settings |
| accountant | Billing, reports, customers |
| technician | Tickets, routers, customers |
| employee | Customers, tickets |

## File Structure

```
app/
  Http/Controllers/        # Web controllers (Inertia)
  Http/Controllers/Api/    # REST API controllers
  Http/Middleware/         # Role check, Inertia
  Models/                  # Eloquent models
database/
  migrations/             # All schema migrations
  seeders/                 # Demo data
resources/
  js/Pages/                # Vue Inertia pages
  js/Components/           # Reusable Vue components
  js/Layouts/              # App layout
  css/app.css              # Tailwind
  views/pdf/               # PDF templates
routes/
  web.php                  # Web routes (Inertia)
  api.php                  # REST API routes
  auth.php                 # Auth routes
deploy/
  nginx.conf               # Nginx config
  deploy.sh                # One-command deployment
  nm-queue.service         # Systemd queue worker
```
