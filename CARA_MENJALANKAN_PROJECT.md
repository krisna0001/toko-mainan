# 🚀 Cara Menjalankan Project Toko Mainan

## Masalah yang Ditemukan ❌

1. ✅ **Dependencies sudah terinstall** (sudah diperbaiki)
2. ❌ **MySQL belum running** (perlu dijalankan)
3. ❌ **Database belum dibuat/migrate** (perlu setup)

---

## 📋 Langkah-Langkah Setup

### 1️⃣ Jalankan Laragon

1. Buka **Laragon**
2. Klik **Start All** untuk menjalankan Apache & MySQL
3. Tunggu sampai statusnya hijau/running

### 2️⃣ Setup Backend (Laravel)

**A. Buat Database:**

```powershell
# Masuk ke direktori backend
cd c:\laragon\www\toko-mainan-backend

# Buat database (bisa lewat HeidiSQL/phpMyAdmin atau command line)
# Lewat Laragon Menu: Database > Create Database > Nama: toko_mainan
```

Atau lewat command line (jalankan di PowerShell):
```powershell
# Masuk ke folder bin MySQL di Laragon
cd C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin

# Buat database
.\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS toko_mainan;"
```

**B. Jalankan Migration:**

```powershell
cd c:\laragon\www\toko-mainan-backend

# Migrate database
php artisan migrate

# (Optional) Seed data jika ada
php artisan db:seed
```

**C. Generate JWT Secret:**

```powershell
# Generate JWT secret key
php artisan jwt:secret
```

**D. Jalankan Backend Server:**

```powershell
# Jalankan Laravel development server
php artisan serve

# Server akan berjalan di: http://localhost:8000
```

Atau gunakan script dev yang sudah ada:
```powershell
composer run dev
```

### 3️⃣ Setup Frontend (Next.js)

**A. Konfigurasi API URL:**

Pastikan file `.env.local` di folder frontend ada dan berisi:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

Jika belum ada, buat file `.env.local` di folder `toko-mainan-frontend`:

```powershell
cd c:\laragon\www\toko-mainan-frontend

# Buat file .env.local
@"
NEXT_PUBLIC_API_URL=http://localhost:8000/api
"@ | Out-File -FilePath .env.local -Encoding utf8
```

**B. Jalankan Frontend Server:**

```powershell
cd c:\laragon\www\toko-mainan-frontend

# Jalankan Next.js development server
npm run dev

# Server akan berjalan di: http://localhost:3000
```

---

## 🎯 Quick Start (Semua Command)

### Terminal 1 - Backend:
```powershell
cd c:\laragon\www\toko-mainan-backend
php artisan migrate
php artisan serve
```

### Terminal 2 - Frontend:
```powershell
cd c:\laragon\www\toko-mainan-frontend
npm run dev
```

---

## ✅ Checklist Sebelum Menjalankan

- [ ] Laragon sudah running (MySQL & Apache hijau)
- [ ] Database `toko_mainan` sudah dibuat
- [ ] File `.env` di backend sudah dikonfigurasi
- [ ] Migrations sudah dijalankan (`php artisan migrate`)
- [ ] JWT secret sudah digenerate (`php artisan jwt:secret`)
- [ ] Backend server running di `http://localhost:8000`
- [ ] File `.env.local` di frontend sudah ada
- [ ] Frontend server running di `http://localhost:3000`

---

## 🐛 Troubleshooting

### Error: "SQLSTATE[HY000] [2002] No connection"
**Solusi:** MySQL belum running. Start Laragon dan pastikan MySQL hijau.

### Error: "SQLSTATE[HY000] [1049] Unknown database"
**Solusi:** Database belum dibuat. Buat database `toko_mainan` lewat HeidiSQL/phpMyAdmin.

### Error: "Class 'Midtrans\Config' not found"
**Solusi:** Dependencies belum terinstall. Jalankan `composer install` di folder backend.

### Error: "Module not found" di Frontend
**Solusi:** Dependencies belum terinstall. Jalankan `npm install` di folder frontend.

### Port 8000 atau 3000 sudah digunakan
**Solusi:** 
```powershell
# Backend - gunakan port lain
php artisan serve --port=8001

# Frontend - gunakan port lain
npm run dev -- -p 3001
```

### Error "merah" di VS Code
**Solusi:** 
1. Pastikan dependencies sudah terinstall (composer install & npm install)
2. Restart VS Code
3. Untuk PHP: Install extension "PHP Intelephense"
4. Untuk TypeScript: Install extension "ESLint"

---

## 📚 Dokumentasi Tambahan

- **Backend API Docs:** `toko-mainan-backend/PROJECT_DOCUMENTATION.md`
- **Quick Start:** `toko-mainan-backend/QUICK_START.md`
- **Postman Collection:** `toko-mainan-backend/Postman_Collection.json`

---

## 🔗 URL Penting

- **Backend API:** http://localhost:8000/api
- **Frontend:** http://localhost:3000
- **phpMyAdmin:** http://localhost/phpmyadmin (via Laragon)
- **HeidiSQL:** Buka lewat Laragon menu

---

**Happy Coding! 🎉**
