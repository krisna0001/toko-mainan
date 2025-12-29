# 🧸 Toko Mainan - Full Stack E-Commerce

Proyek tugas kuliah: Web e-commerce toko mainan dengan Laravel backend & Next.js frontend

## 🚀 Teknologi Stack

### Backend (Laravel 12)
- **Framework**: Laravel 12.x
- **Authentication**: JWT (php-open-source-saver/jwt-auth)
- **Database**: MySQL
- **Architecture**: Repository Pattern
- **Payment**: Midtrans (installed, ready to implement)

### Frontend (Next.js 14+)
- **Framework**: Next.js 14+ with App Router
- **Language**: TypeScript
- **Styling**: Tailwind CSS
- **State Management**: Zustand
- **HTTP Client**: Axios
- **UI Components**: Heroicons, React Hot Toast

## 📁 Project Structure

```
c:/laragon/www/
├── toko-mainan-backend/      # Laravel API Backend
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Models/
│   │   ├── Repositories/
│   │   └── ...
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/api.php
│
└── toko-mainan-frontend/     # Next.js Frontend
    ├── app/                   # Pages (App Router)
    │   ├── page.tsx          # Homepage
    │   ├── login/
    │   ├── register/
    │   ├── products/
    │   ├── cart/
    │   ├── orders/
    │   └── admin/
    ├── components/           # Reusable Components
    ├── services/             # API Services
    ├── store/                # Zustand Stores
    └── lib/                  # Utilities
```

## 🎯 Fitur Lengkap

### Customer Features
- ✅ Register & Login dengan JWT
- ✅ Browse products dengan search & filter
- ✅ View product details
- ✅ Add to cart
- ✅ Checkout dengan shipping address
- ✅ View order history
- ✅ Real-time cart counter
- ✅ Toast notifications

### Admin Features
- ✅ Admin dashboard
- ✅ Manage products (view, delete)
- ✅ Manage categories (view, delete)
- ✅ Manage orders (view, update status)
- ✅ Role-based access control

### Security Features
- ✅ JWT Authentication
- ✅ Role-based middleware (Admin & Customer)
- ✅ SQL Injection protection (Eloquent ORM)
- ✅ Password hashing (bcrypt)
- ✅ Input validation
- ✅ CORS configuration
- ✅ Protected routes

## 🏃 Getting Started

### Backend (Running)
```bash
# Backend: http://127.0.0.1:8000
# Database: toko_mainan (MySQL)
# JWT configured
```

### Frontend (Running)
```bash
# Frontend: http://localhost:3000
# Connected to backend API
```

## 👥 Test Accounts

### Admin Account
- Email: `admin@tokomainan.com`
- Password: `admin123`

### Customer Account
- Email: `customer@example.com`
- Password: `customer123`

## 🎨 UI Pages

1. **Homepage** (`/`) - Hero, categories, products
2. **Products** (`/products`) - All products
3. **Login** (`/login`) - Authentication
4. **Register** (`/register`) - Registration
5. **Cart** (`/cart`) - Shopping cart
6. **Orders** (`/orders`) - Order history
7. **Admin Panel** (`/admin`) - Management dashboard

---

**Status**: ✅ READY TO DEMO!
