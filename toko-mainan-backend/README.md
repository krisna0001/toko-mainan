# 🧸 Toko Mainan - E-Commerce Platform

> Platform e-commerce toko mainan dengan Laravel (REST API) + Next.js frontend

## 🚀 Quick Start

```bash
# Backend
cd c:/laragon/www/toko-mainan-backend
php artisan serve
# API: http://localhost:8000

# Frontend (belum dibuat)
cd c:/laragon/www/toko-mainan-frontend
npm run dev
# Web: http://localhost:3000
```

## ✨ Features

- 🔐 JWT Authentication dengan role-based access (Admin & Customer)
- 🛒 Product management dengan categories
- 💳 Midtrans payment integration
- 📦 Order tracking & management
- 🏗️ Modular architecture (Repository Pattern)
- 🔒 Security-first approach (SQL injection protection, input validation)

## 📊 Tech Stack

**Backend**: Laravel 12 + MySQL + JWT  
**Frontend**: Next.js 14 + TypeScript + Tailwind CSS  
**Payment**: Midtrans

## 📁 Project Status

| Component | Status |
|-----------|--------|
| Backend Setup | ✅ Complete |
| Database Schema | ✅ Complete |
| Models & Repositories | ✅ Complete |
| JWT Auth | ✅ Complete |
| Controllers | ⏳ 30% (Auth done) |
| Midtrans | ⏳ Not started |
| Frontend | ⏳ Not started |

## 📚 Documentation

Untuk dokumentasi lengkap, lihat file **[PROJECT_DOCUMENTATION.md](PROJECT_DOCUMENTATION.md)**

Dokumentasi lengkap mencakup:
- 🏗️ Arsitektur detail
- 📊 Database schema lengkap
- 🔐 Security features
- 📝 Tugas yang tersisa
- 🚀 Deployment guide
- 📚 API documentation
- 💻 Code examples

## 🔑 Default Credentials (After Seeding)

```
Admin:
Email: admin@tokomainan.com
Password: admin123

Customer (test):
Email: customer@example.com
Password: customer123
```

## 🛠️ Next Steps

1. **Complete Controllers** - Product, Category, Order
2. **Install Midtrans** - `composer require midtrans/midtrans-php`
3. **Create Services** - MidtransService, OrderService
4. **Run Seeders** - Populate test data
5. **Create Frontend** - Next.js setup
6. **Testing** - Unit & Feature tests

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- 📧 Email: support@tokomainan.com
- 📖 Docs: [PROJECT_DOCUMENTATION.md](PROJECT_DOCUMENTATION.md)

---

**Last Updated**: December 27, 2025  
**Version**: 0.5.0 (In Development)

🟡 **Status**: Active Development | 📈 **Progress**: 50%
