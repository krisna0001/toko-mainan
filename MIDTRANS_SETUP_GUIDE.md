# 🚀 Panduan Setup Midtrans Payment Gateway

## 📋 Langkah-langkah Setup

### 1️⃣ Daftar Akun Midtrans Sandbox

1. Buka [https://dashboard.sandbox.midtrans.com/register](https://dashboard.sandbox.midtrans.com/register)
2. Daftar akun baru (GRATIS untuk testing)
3. Verifikasi email Anda
4. Login ke dashboard sandbox

### 2️⃣ Dapatkan API Keys

1. Di dashboard Midtrans, klik menu **Settings** → **Access Keys**
2. Anda akan menemukan 2 keys penting:
   - **Server Key** (contoh: `SB-Mid-server-xxxxxxxxxxxxx`)
   - **Client Key** (contoh: `SB-Mid-client-xxxxxxxxxxxxx`)
3. Copy kedua keys tersebut

### 3️⃣ Konfigurasi Backend (Laravel)

1. Buka file `.env` di folder `toko-mainan-backend`
2. Update konfigurasi Midtrans:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-YOUR_SERVER_KEY_HERE
MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**⚠️ PENTING:** Ganti `YOUR_SERVER_KEY_HERE` dan `YOUR_CLIENT_KEY_HERE` dengan keys yang Anda dapat dari dashboard Midtrans!

### 4️⃣ Konfigurasi Frontend (Next.js)

1. Buka file `.env.local` di folder `toko-mainan-frontend`
2. Update konfigurasi Midtrans:

```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api
NEXT_PUBLIC_MIDTRANS_CLIENT_KEY=SB-Mid-client-YOUR_CLIENT_KEY_HERE
```

**⚠️ PENTING:** Ganti `YOUR_CLIENT_KEY_HERE` dengan client key yang Anda dapat dari dashboard Midtrans!

### 5️⃣ Setup Callback URL (Opsional untuk Production)

Untuk production nanti, Anda perlu setup callback URL di dashboard Midtrans:

1. Di dashboard Midtrans, klik **Settings** → **Configuration**
2. Isi **Payment Notification URL** dengan:
   ```
   https://yourdomain.com/api/midtrans/callback
   ```
3. Untuk sandbox/testing, biarkan kosong dulu (callback akan otomatis work)

---

## 🧪 Testing Payment Flow

### Langkah Testing:

1. **Start Backend Server**
   ```bash
   cd c:\laragon\www\toko-mainan-backend
   php artisan serve
   ```

2. **Start Frontend Server**
   ```bash
   cd c:\laragon\www\toko-mainan-frontend
   npm run dev
   ```

3. **Login sebagai Customer**
   - Email: `customer@example.com`
   - Password: `password`

4. **Tambahkan produk ke cart** dan klik **Checkout**

5. **Isi alamat pengiriman:**
   - Gunakan map picker untuk pin lokasi ATAU
   - Klik "Tulis Manual" untuk input manual
   - Isi nomor telepon

6. **Klik "Proceed to Checkout"**

7. **Popup Midtrans akan muncul** dengan pilihan metode pembayaran

---

## 💳 Testing Cards (Sandbox)

Midtrans menyediakan testing cards untuk berbagai skenario:

### ✅ Successful Payment
- **Card Number:** `4811 1111 1111 1114`
- **Exp Date:** `01/25` (atau bulan/tahun di masa depan)
- **CVV:** `123`

### ❌ Failed Payment
- **Card Number:** `4911 1111 1111 1113`
- **Exp Date:** `01/25`
- **CVV:** `123`

### 🔒 3D Secure Challenge
- **Card Number:** `4811 1111 1111 1114` dengan flag 3DS enabled
- **OTP Code:** `112233`

### 💰 Other Payment Methods
Midtrans sandbox juga support testing untuk:
- **BCA VA:** Akan generate virtual account number
- **Mandiri VA:** Akan generate virtual account number
- **GoPay:** Scan QR di simulator GoPay
- **Alfamart/Indomaret:** Akan generate payment code

**Semua transaksi di sandbox adalah SIMULASI, tidak ada uang asli yang ditransfer!**

---

## 🔍 Monitoring & Debugging

### 1. Cek Status Order di Database
```sql
SELECT id, order_number, status, payment_status, snap_token, midtrans_transaction_id 
FROM orders 
ORDER BY created_at DESC;
```

### 2. Cek Log Backend
```bash
cd c:\laragon\www\toko-mainan-backend
tail -f storage/logs/laravel.log
```

### 3. Cek Midtrans Dashboard
- Login ke [https://dashboard.sandbox.midtrans.com](https://dashboard.sandbox.midtrans.com)
- Menu **Transactions** untuk melihat semua transaksi
- Klik transaction untuk detail lengkap

---

## 🎯 Flow Diagram

```
┌─────────────┐
│   Customer  │
│  Add to Cart│
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Checkout   │
│   (Cart)    │
└──────┬──────┘
       │ POST /api/orders/checkout
       ▼
┌─────────────────────┐
│  OrderController    │
│  - Create Order     │
│  - Generate Snap    │
│  Token from Midtrans│
└──────┬──────────────┘
       │ Return snap_token
       ▼
┌─────────────────────┐
│  Frontend Opens     │
│  Midtrans Snap      │
│  Payment Popup      │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Customer Selects   │
│  Payment Method &   │
│  Complete Payment   │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Midtrans Sends     │
│  Callback to:       │
│  POST /api/midtrans/│
│  callback           │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Update Order Status│
│  - pending → paid   │
│  - Save transaction │
│    ID               │
└─────────────────────┘
```

---

## ⚡ Status Flow

```
Order Status Flow:
pending → processing → paid → shipped → completed
                              ↓
                          cancelled

Payment Status Flow:
unpaid → paid
       → failed
       → refunded
```

---

## 🚨 Troubleshooting

### ❌ "snap_token is null"
**Penyebab:** Server key salah atau belum diisi
**Solusi:** 
1. Cek file `.env` backend
2. Pastikan `MIDTRANS_SERVER_KEY` sudah diisi dengan benar
3. Restart Laravel server: `php artisan serve`

### ❌ Popup Midtrans tidak muncul
**Penyebab:** Client key salah atau script tidak load
**Solusi:**
1. Cek file `.env.local` frontend
2. Pastikan `NEXT_PUBLIC_MIDTRANS_CLIENT_KEY` sudah diisi
3. Buka browser console (F12) untuk cek error
4. Restart Next.js server: `npm run dev`

### ❌ Callback tidak jalan
**Penyebab:** Route callback tidak terdaftar atau middleware block
**Solusi:**
1. Cek `routes/api.php` ada route `POST /api/midtrans/callback`
2. Pastikan route TIDAK pakai middleware auth (harus public)
3. Test manual dengan Postman ke endpoint callback

### ❌ Order status tidak update
**Penyebab:** Callback berhasil tapi ada error di controller
**Solusi:**
1. Cek log Laravel: `tail -f storage/logs/laravel.log`
2. Cek response dari Midtrans di dashboard
3. Cek signature verification

---

## 📚 Resources

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Midtrans Snap Integration](https://docs.midtrans.com/en/snap/integration-guide)
- [Testing Payment](https://docs.midtrans.com/en/technical-reference/sandbox-test)
- [Handling Notifications](https://docs.midtrans.com/en/after-payment/http-notification)

---

## ✅ Checklist Sebelum Go Live (Production)

- [ ] Ganti API keys dari sandbox ke production keys
- [ ] Update `MIDTRANS_IS_PRODUCTION=true` di `.env`
- [ ] Setup Payment Notification URL di dashboard Midtrans
- [ ] Test semua payment methods di production
- [ ] Setup SSL certificate (HTTPS required)
- [ ] Implement email notification setelah payment success
- [ ] Setup proper logging dan monitoring
- [ ] Test callback dengan real transactions

---

**🎉 Happy Testing! Jika ada masalah, cek log dan jangan ragu untuk debug!**
