# 🧸 TOKO MAINAN - QUICK START GUIDE

## 📂 File-File Penting

| File | Keterangan |
|------|------------|
| `README.md` | Overview & quick start |
| `PROJECT_DOCUMENTATION.md` | 📖 Dokumentasi lengkap project |
| `CODE_TEMPLATES.md` | 📝 Template code siap pakai |
| `NEXT_STEPS.md` | 🚀 Panduan melanjutkan development |

## ⚡ Quick Commands

```bash
# Start backend
cd c:/laragon/www/toko-mainan-backend
php artisan serve
# API: http://localhost:8000

# Reset database
php artisan migrate:fresh

# Run seeders
php artisan db:seed

# Create seeder
php artisan make:seeder NamaSeeder

# Create controller
php artisan make:controller Api/NamaController
```

## 🔑 Default Login (After Seeding)

```
Admin:
Email: admin@tokomainan.com
Password: admin123

Customer:
Email: customer@example.com
Password: customer123
```

## 📊 Project Status

**Backend**: 50% ✅
- Setup ✅
- Database ✅  
- Models & Repositories ✅
- Auth ✅
- Controllers ⏳ (AuthController done, others need template copy)

**Frontend**: 0% ⏳
- Belum dibuat

## 🎯 Next 3 Steps

1. **Copy Templates**
   - Buka `CODE_TEMPLATES.md`
   - Copy ProductController template → `app/Http/Controllers/Api/ProductController.php`
   - Copy CategoryController template → `app/Http/Controllers/Api/CategoryController.php`
   - Copy OrderController template → `app/Http/Controllers/Api/OrderController.php`

2. **Install Midtrans**
   ```bash
   composer require midtrans/midtrans-php
   ```
   - Create `config/midtrans.php` (copy dari CODE_TEMPLATES.md)
   - Create `app/Services/MidtransService.php` (copy dari CODE_TEMPLATES.md)

3. **Create Seeders**
   ```bash
   php artisan make:seeder AdminUserSeeder
   php artisan make:seeder CategorySeeder
   php artisan make:seeder ProductSeeder
   ```
   - Copy template dari `CODE_TEMPLATES.md`
   - Run: `php artisan db:seed`

## 🛠️ Tools Needed

- ✅ Laragon (sudah ada)
- ✅ Composer (sudah ada)
- ✅ PHP 8.2+ (sudah ada)
- ⏳ Postman / Thunder Client (untuk testing)
- ⏳ Node.js 18+ (untuk frontend)

## 📱 API Endpoints (After Complete)

### Public
- `POST /api/auth/register` - Register
- `POST /api/auth/login` - Login
- `GET /api/products` - List products
- `GET /api/categories` - List categories

### Customer
- `POST /api/orders/checkout` - Checkout
- `GET /api/orders/my-orders` - My orders

### Admin
- `POST /api/products` - Create product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `POST /api/categories` - Create category
- `GET /api/orders/all` - All orders
- `PUT /api/orders/{id}/status` - Update order status

## 🔐 Security Features

✅ **Implemented:**
- SQL Injection protection (Eloquent ORM)
- JWT Authentication
- Role-based access control (Admin/Customer)
- Password hashing (bcrypt)
- Input validation
- Database transactions

## 📚 Learning Resources

- **Laravel**: https://laravel.com/docs
- **JWT**: https://github.com/PHP-Open-Source-Saver/jwt-auth
- **Midtrans**: https://docs.midtrans.com/
- **Next.js**: https://nextjs.org/docs
- **Tailwind**: https://tailwindcss.com/docs

## 🆘 Common Issues

**Problem: JWT token tidak valid**
```bash
php artisan jwt:secret
```

**Problem: Database connection failed**
- Check MySQL Laragon running
- Check `.env` DB credentials

**Problem: Migration error**
```bash
php artisan migrate:fresh
```

**Problem: Class not found**
```bash
composer dump-autoload
```

## 📞 Support

Jika stuck:
1. Baca `PROJECT_DOCUMENTATION.md` untuk detail
2. Lihat `CODE_TEMPLATES.md` untuk template
3. Follow `NEXT_STEPS.md` untuk panduan step-by-step

## ✅ Daily Checklist

**Hari 1-2:**
- [ ] Copy semua templates dari CODE_TEMPLATES.md
- [ ] Test semua endpoints dengan Postman
- [ ] Install Midtrans package

**Hari 3-4:**
- [ ] Setup MidtransService
- [ ] Create dan run seeders
- [ ] Test checkout flow

**Hari 5-7:**
- [ ] Create Next.js project
- [ ] Build auth pages
- [ ] Build product pages

**Hari 8-10:**
- [ ] Build cart & checkout
- [ ] Build admin dashboard
- [ ] Testing & bug fixes

**Hari 11-14:**
- [ ] Polish UI/UX
- [ ] Documentation
- [ ] Prepare deployment

## 🎉 Success Criteria

Project selesai ketika:
- ✅ Customer bisa register/login
- ✅ Customer bisa browse products
- ✅ Customer bisa checkout dengan Midtrans
- ✅ Admin bisa CRUD products
- ✅ Admin bisa manage orders
- ✅ Payment callback berfungsi
- ✅ All security measures implemented

---

**Start Now! 🚀**

Begin dengan membaca `NEXT_STEPS.md` untuk detailed guide!

**Last Updated**: Dec 27, 2025
