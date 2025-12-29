# Login Credentials - Toko Mainan

## 🔐 Admin Login (RAHASIA - CRUD Access)

**Cara Akses:**
1. Buka halaman mana saja di frontend
2. **Klik logo "🧸 Toko Mainan" di navbar sebanyak 3x berturut-turut**
3. Modal admin login akan muncul 🎉

**Credentials:**
- Email: `admin@tokomainan.com`
- Password: `admin123`

**Access:**
- Full CRUD for Products
- Full CRUD for Categories
- View all Orders
- Manage Users

**⚠️ PENTING:**
- Login admin TIDAK terlihat di UI biasa
- Hanya bisa diakses dengan trigger rahasia (klik logo 7x)
- Tidak ada link/button yang mengarah ke admin login

---

## 👤 Customer Login

**URL:** http://localhost:3000/login

**Credentials:**
- Register new account at: http://localhost:3000/register
- Or use test customer (if exists in database)

**Access:**
- Browse Products
- Add to Cart
- Place Orders
- View Order History

---

## 🚀 Quick Start

1. **Start Backend Server:**
   ```bash
   cd c:\laragon\www\toko-mainan-backend
   php artisan serve
   ```

2. **Start Frontend Server:**
   ```bash
   cd c:\laragon\www\toko-mainan-frontend
   npm run dev
   ```

3. **Access the Application:**
   - Frontend: http://localhost:3000
   - Backend API: http://localhost:8000
   - **Admin Login: Klik logo 7x untuk buka modal rahasia** 🔐
   - Customer Login: http://localhost:3000/login

---

## 📝 Notes

- Admin dan Customer login **TERPISAH**
- Admin login **RAHASIA** - trigger dengan klik logo 7x
- Admin tidak memiliki halaman login yang visible
- Customer tidak bisa akses halaman admin
- Database seeder sudah membuat akun admin otomatis

---

## 🎯 Easter Egg Feature

**Secret Admin Access:**
- Klik logo "🧸 Toko Mainan" tepat **3 kali berturut-turut**
- Modal login admin akan muncul otomatis
- Tidak ada link/button yang mengarah ke admin login
- Ini fitur rahasia untuk keamanan tambahan!
