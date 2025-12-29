# 🧸 Toko Mainan - E-Commerce Platform

## 📋 Overview
Platform e-commerce toko mainan dengan **Laravel backend** (REST API) dan **Next.js frontend**. Project ini dibangun dengan fokus utama pada:
- 🔐 **Security** (SQL Injection Protection, XSS, CSRF)
- 🏗️ **Modular Architecture** (Repository Pattern)
- 🛠️ **Maintainability** (Clean Code, SOLID Principles)
- 💳 **Payment Integration** (Midtrans)

## 🏗️ Tech Stack

### Backend (Laravel)
| Component | Technology |
|-----------|-----------|
| Framework | Laravel 12.x |
| Database | MySQL (Laragon) |
| Authentication | JWT (php-open-source-saver/jwt-auth) |
| Payment Gateway | Midtrans |
| Architecture | Repository Pattern + Service Layer |
| API Style | RESTful API |

### Frontend (Next.js) - *Belum dibuat*
| Component | Technology |
|-----------|-----------|
| Framework | Next.js 14+ (App Router) |
| Language | TypeScript |
| State Management | Zustand / Context API |
| HTTP Client | Axios |
| UI Framework | Tailwind CSS |
| Forms | React Hook Form + Zod |

## 🔐 Security Features

### ✅ Sudah Diimplementasikan

1. **SQL Injection Protection**
   - Eloquent ORM dengan parameterized queries
   - Input validation di semua endpoints
   - Type hinting di Models

2. **Authentication & Authorization**
   - JWT-based authentication
   - Role-Based Access Control (RBAC)
   - Middleware protection (Admin & Customer)

3. **Password Security**
   - Bcrypt hashing
   - Password confirmation
   - Minimum 8 characters

4. **Database Transactions**
   - Rollback pada error
   - Data integrity protection

### ⏳ Akan Diimplementasikan

- CORS configuration
- Rate limiting
- XSS protection headers
- CSRF token
- Input sanitization
- File upload validation

## 📁 Project Structure

```
toko-mainan-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php          ✅ DONE
│   │   │       ├── ProductController.php       ⏳ TODO
│   │   │       ├── CategoryController.php      ⏳ TODO
│   │   │       └── OrderController.php         ⏳ TODO
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php             ✅ DONE
│   │       └── CustomerMiddleware.php          ✅ DONE
│   │
│   ├── Models/
│   │   ├── User.php                            ✅ DONE (JWT implemented)
│   │   ├── Category.php                        ✅ DONE
│   │   ├── Product.php                         ✅ DONE (with soft deletes)
│   │   ├── Order.php                           ✅ DONE
│   │   └── OrderItem.php                       ✅ DONE
│   │
│   ├── Repositories/                           ✅ DONE
│   │   ├── Interfaces/
│   │   │   ├── BaseRepositoryInterface.php
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   ├── CategoryRepositoryInterface.php
│   │   │   └── OrderRepositoryInterface.php
│   │   ├── BaseRepository.php
│   │   ├── ProductRepository.php
│   │   ├── CategoryRepository.php
│   │   └── OrderRepository.php
│   │
│   ├── Services/                               ⏳ TODO
│   │   ├── MidtransService.php
│   │   ├── OrderService.php
│   │   └── ProductService.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── RepositoryServiceProvider.php       ✅ DONE
│
├── config/
│   ├── auth.php                                ✅ DONE (JWT configured)
│   ├── jwt.php                                 ✅ DONE
│   ├── midtrans.php                            ⏳ TODO
│   └── cors.php
│
├── database/
│   ├── migrations/                             ✅ ALL DONE
│   │   ├── *_create_users_table.php
│   │   ├── *_add_role_to_users_table.php
│   │   ├── *_create_categories_table.php
│   │   ├── *_create_products_table.php
│   │   ├── *_create_orders_table.php
│   │   └── *_create_order_items_table.php
│   │
│   ├── seeders/                                ⏳ TODO
│   │   ├── DatabaseSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   ├── CategorySeeder.php
│   │   └── ProductSeeder.php
│   │
│   └── factories/
│       ├── UserFactory.php
│       └── ProductFactory.php
│
├── routes/
│   ├── api.php                                 ⏳ TODO (basic structure done)
│   ├── web.php
│   └── console.php
│
└── tests/
    ├── Feature/
    └── Unit/
```

## 📊 Database Schema

### Users Table
| Column | Type | Constraints |
|--------|------|------------|
| id | BIGINT | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL (hashed) |
| role | ENUM | 'admin', 'customer' (default: 'customer') |
| phone | VARCHAR(20) | NULLABLE |
| address | TEXT | NULLABLE |
| email_verified_at | TIMESTAMP | NULLABLE |
| remember_token | VARCHAR(100) | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Categories Table
| Column | Type | Constraints |
|--------|------|------------|
| id | BIGINT | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| slug | VARCHAR(255) | UNIQUE, NOT NULL |
| description | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Products Table
| Column | Type | Constraints |
|--------|------|------------|
| id | BIGINT | PK, AUTO_INCREMENT |
| category_id | BIGINT | FK (categories.id) ON DELETE CASCADE |
| name | VARCHAR(255) | NOT NULL |
| slug | VARCHAR(255) | UNIQUE, NOT NULL |
| description | TEXT | NULLABLE |
| price | DECIMAL(10,2) | NOT NULL |
| stock | INTEGER | DEFAULT 0 |
| image | VARCHAR(255) | NULLABLE |
| is_active | BOOLEAN | DEFAULT TRUE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | NULLABLE (soft delete) |

### Orders Table
| Column | Type | Constraints |
|--------|------|------------|
| id | BIGINT | PK, AUTO_INCREMENT |
| user_id | BIGINT | FK (users.id) ON DELETE CASCADE |
| order_number | VARCHAR(255) | UNIQUE, NOT NULL |
| total_amount | DECIMAL(10,2) | NOT NULL |
| status | ENUM | 'pending', 'processing', 'paid', 'shipped', 'completed', 'cancelled' |
| payment_status | ENUM | 'unpaid', 'paid', 'failed', 'refunded' |
| payment_type | VARCHAR(255) | NULLABLE |
| midtrans_transaction_id | VARCHAR(255) | NULLABLE |
| midtrans_order_id | VARCHAR(255) | NULLABLE |
| shipping_address | TEXT | NOT NULL |
| shipping_phone | VARCHAR(20) | NOT NULL |
| notes | TEXT | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Order_Items Table
| Column | Type | Constraints |
|--------|------|------------|
| id | BIGINT | PK, AUTO_INCREMENT |
| order_id | BIGINT | FK (orders.id) ON DELETE CASCADE |
| product_id | BIGINT | FK (products.id) ON DELETE CASCADE |
| quantity | INTEGER | NOT NULL |
| price | DECIMAL(10,2) | NOT NULL |
| subtotal | DECIMAL(10,2) | NOT NULL |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

## 🚀 Installation & Setup

### Prerequisites
- ✅ PHP 8.2 or higher
- ✅ Composer
- ✅ MySQL 8.0 or higher (Laragon)
- ✅ Node.js 18+ (untuk frontend)

### Backend Setup

1. **Clone or Navigate to Project**
```bash
cd c:/laragon/www/toko-mainan-backend
```

2. **Install Dependencies** *(Sudah done)*
```bash
composer install
```

3. **Environment Configuration** *(Sudah configured)*
File `.env` sudah dikonfigurasi dengan:
- Database MySQL (toko_mainan)
- JWT Secret (auto-generated)
- Midtrans placeholders

4. **Database Setup** *(Sudah done)*
```bash
# Database sudah dibuat
# Migrations sudah dijalankan
# Jika perlu reset:
php artisan migrate:fresh
```

5. **Run Development Server**
```bash
php artisan serve
# API akan jalan di: http://localhost:8000
```

### Frontend Setup *(Belum dibuat)*

```bash
cd c:/laragon/www
npx create-next-app@latest toko-mainan-frontend --typescript --tailwind --app
cd toko-mainan-frontend
npm install axios zustand react-hook-form @hookform/resolvers zod
```

## 📝 Tugas Yang Tersisa

### 🔴 HIGH PRIORITY

#### 1. Complete Controllers Implementation

**File: `app/Http/Controllers/Api/ProductController.php`**
- [ ] Implement all CRUD methods
- [ ] Add validation rules
- [ ] Add transaction handling
- [ ] Add error handling

**File: `app/Http/Controllers/Api/CategoryController.php`**
- [ ] Implement all CRUD methods
- [ ] Add cascade delete protection
- [ ] Add validation rules

**File: `app/Http/Controllers/Api/OrderController.php`**
- [ ] Implement order creation
- [ ] Implement checkout with Midtrans
- [ ] Add payment callback handler
- [ ] Add stock management

Template yang bisa digunakan:
```php
public function index(Request $request)
{
    try {
        // Logic here using repository
        $data = $this->repository->all();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error message',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

#### 2. Install & Configure Midtrans

**Install Package:**
```bash
composer require midtrans/midtrans-php
```

**Create Config: `config/midtrans.php`**
```php
<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];
```

**Create Service: `app/Services/MidtransService.php`**
```php
<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction($order, $items)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_phone,
            ],
            'item_details' => $items,
        ];

        return Snap::createTransaction($params);
    }

    public function getTransactionStatus($orderId)
    {
        return Transaction::status($orderId);
    }
}
```

#### 3. Complete API Routes

**File: `routes/api.php`**
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public product viewing
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::get('categories', [CategoryController::class, 'index']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class)
            ->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)
            ->except(['index']);
        Route::get('orders/all', [OrderController::class, 'allOrders']);
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
    });

    // Customer routes
    Route::middleware('customer')->group(function () {
        Route::post('orders/checkout', [OrderController::class, 'checkout']);
        Route::get('orders/my-orders', [OrderController::class, 'myOrders']);
    });

    // Both roles
    Route::get('orders/{id}', [OrderController::class, 'show']);
});

// Midtrans callback (no auth needed)
Route::post('midtrans/callback', [OrderController::class, 'midtransCallback']);
```

### 🟡 MEDIUM PRIORITY

#### 4. Create Seeders

```bash
php artisan make:seeder AdminUserSeeder
php artisan make:seeder CategorySeeder  
php artisan make:seeder ProductSeeder
```

**AdminUserSeeder.php:**
```php
public function run()
{
    User::create([
        'name' => 'Admin',
        'email' => 'admin@tokomainan.com',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
    ]);
}
```

**CategorySeeder.php:**
```php
public function run()
{
    $categories = [
        ['name' => 'Action Figures', 'description' => 'Mainan action figures'],
        ['name' => 'Boneka', 'description' => 'Boneka dan plushies'],
        ['name' => 'Puzzle', 'description' => 'Puzzle dan games'],
        ['name' => 'Mobil-mobilan', 'description' => 'Toy cars dan vehicles'],
    ];

    foreach ($categories as $category) {
        Category::create($category);
    }
}
```

#### 5. Add CORS Configuration

**Install Laravel CORS:**
```bash
# Sudah include di Laravel 12
```

**Update `config/cors.php`:**
```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000'], // Next.js dev server
```

#### 6. Add Request Validation Classes

```bash
php artisan make:request StoreProductRequest
php artisan make:request UpdateProductRequest
php artisan make:request CheckoutRequest
```

### 🟢 LOW PRIORITY

#### 7. Testing
- [ ] Unit tests untuk Models
- [ ] Feature tests untuk API endpoints
- [ ] Integration tests untuk Midtrans

#### 8. Documentation
- [ ] API documentation (Postman/Swagger)
- [ ] Code comments
- [ ] Deployment guide

## 📚 API Documentation

### Authentication

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "08123456789",
  "address": "Jakarta"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "customer"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer"
  }
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

### Products (Public)

#### Get All Products
```http
GET /api/products?q=toy&category_id=1
```

#### Get Single Product
```http
GET /api/products/1
```

### Products (Admin Only)

#### Create Product
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "category_id": 1,
  "name": "Toy Car Red",
  "description": "Red racing toy car",
  "price": 150000,
  "stock": 50,
  "is_active": true
}
```

### Orders (Customer)

#### Checkout
```http
POST /api/orders/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address": "Jl. Sudirman No. 123, Jakarta",
  "shipping_phone": "08123456789",
  "notes": "Tolong bungkus kado"
}
```

## 🔄 Deployment Guide

### Production Checklist

- [ ] Update `.env` untuk production
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Update database credentials
- [ ] Update Midtrans production keys
- [ ] Run migrations di production
- [ ] Run seeders (optional)
- [ ] Setup SSL certificate
- [ ] Configure web server (Nginx/Apache)
- [ ] Setup cron jobs untuk queue
- [ ] Configure file storage (S3/Cloudinary)

### Environment Variables untuk Production

```env
APP_NAME="Toko Mainan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-production-host
DB_PORT=3306
DB_DATABASE=toko_mainan_prod
DB_USERNAME=prod_user
DB_PASSWORD=strong_password

MIDTRANS_SERVER_KEY=your-production-server-key
MIDTRANS_CLIENT_KEY=your-production-client-key
MIDTRANS_IS_PRODUCTION=true
```

## 🤝 Contribution Guidelines

Jika ingin berkontribusi:
1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📞 Support & Contact

- **Email**: support@tokomainan.com
- **Documentation**: [Link to docs]
- **Issue Tracker**: [Link to issues]

## 📄 License

Private License - © 2025 Toko Mainan

---

**Project Status**: 🟡 In Development (50% Complete)

**Last Updated**: December 27, 2025

**Current Progress**:
- ✅ Backend Setup & Configuration
- ✅ Database Migrations & Models
- ✅ Repository Pattern Implementation
- ✅ JWT Authentication & RBAC
- ⏳ Controllers Implementation (30%)
- ⏳ Midtrans Integration (0%)
- ⏳ Frontend Setup (0%)
- ⏳ Testing (0%)

**Next Sprint**:
1. Complete all Controllers
2. Implement Midtrans Service
3. Create Seeders
4. Start Frontend Development
