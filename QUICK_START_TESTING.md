# 🚀 QUICK START - Testing Guide

## ⚡ Setup (5 menit)

### 1. Daftar Midtrans Sandbox (GRATIS)
```
URL: https://dashboard.sandbox.midtrans.com/register
- Daftar dengan email Anda
- Verifikasi email
- Login
- Ke Settings → Access Keys
- Copy Server Key & Client Key
```

### 2. Update Environment Variables

**Backend: `c:\laragon\www\toko-mainan-backend\.env`**
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-[PASTE_YOUR_KEY_HERE]
MIDTRANS_CLIENT_KEY=SB-Mid-client-[PASTE_YOUR_KEY_HERE]
MIDTRANS_IS_PRODUCTION=false
```

**Frontend: `c:\laragon\www\toko-mainan-frontend\.env.local`**
```env
NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api
NEXT_PUBLIC_MIDTRANS_CLIENT_KEY=SB-Mid-client-[PASTE_YOUR_KEY_HERE]
```

⚠️ **IMPORTANT:** Ganti `[PASTE_YOUR_KEY_HERE]` dengan keys dari dashboard Midtrans!

### 3. Run Servers

**Terminal 1 (Backend):**
```powershell
cd c:\laragon\www\toko-mainan-backend
php artisan serve
```

**Terminal 2 (Frontend):**
```powershell
cd c:\laragon\www\toko-mainan-frontend
npm run dev
```

---

## 🧪 Test Scenario

### Scenario 1: Map Location Picker

1. Buka browser: `http://localhost:3000`
2. Login:
   - Email: `customer@example.com`
   - Password: `password`
3. Browse products → Add to Cart (minimal 1 produk)
4. Klik icon cart (top right) → View Cart
5. **Test Map:**
   - Klik di peta untuk drop pin
   - Address otomatis muncul ✅
   - Koordinat (lat, lng) muncul ✅
6. **Test Current Location:**
   - Klik "📍 Use Current Location"
   - Browser minta izin → Allow
   - Pin muncul di lokasi Anda ✅
7. **Test Manual Input:**
   - Klik "✏️ Tulis Manual"
   - Input text muncul
   - Ketik address manual ✅
8. Isi nomor telepon
9. Klik "Proceed to Checkout"

### Scenario 2: Midtrans Payment

**Lanjutan dari Scenario 1:**

10. Popup Midtrans muncul ✅
11. Pilih "Credit Card"
12. Isi test card:
    - Card Number: `4811 1111 1111 1114`
    - Expiry: `01/25`
    - CVV: `123`
13. Klik "Pay"
14. **Expected Result:**
    - Toast "Payment successful!" ✅
    - Cart cleared ✅
    - Redirect ke `/orders` ✅
15. Check database:
    ```sql
    SELECT order_number, status, payment_status, snap_token 
    FROM orders 
    ORDER BY created_at DESC 
    LIMIT 1;
    ```
    - status: `processing` ✅
    - payment_status: `paid` ✅

---

## 💳 Test Cards (Sandbox)

### ✅ Success
```
Card: 4811 1111 1111 1114
Exp:  01/25
CVV:  123
```

### ❌ Failed
```
Card: 4911 1111 1111 1113
Exp:  01/25
CVV:  123
```

### 🔐 3D Secure (with OTP)
```
Card: 4811 1111 1111 1114
Exp:  01/25
CVV:  123
OTP:  112233
```

---

## ✅ Checklist

**Setup:**
- [ ] Midtrans account created
- [ ] Server Key copied
- [ ] Client Key copied
- [ ] Backend .env updated
- [ ] Frontend .env.local updated
- [ ] Backend server running (port 8000)
- [ ] Frontend server running (port 3000)

**Map Features:**
- [ ] Map loads successfully
- [ ] Click to drop pin works
- [ ] Address auto-fills from coordinates
- [ ] Coordinates display (lat, lng)
- [ ] "Use Current Location" works
- [ ] Toggle to manual input works

**Payment Flow:**
- [ ] Checkout button creates order
- [ ] Midtrans popup opens
- [ ] Test card payment succeeds
- [ ] Order status updates to "paid"
- [ ] Cart clears after payment
- [ ] Redirect to orders page

---

## 🚨 Common Errors & Fixes

### ❌ snap_token is null
```bash
# Check backend .env
cat .env | grep MIDTRANS

# Restart backend
php artisan serve
```

### ❌ Popup tidak muncul
```bash
# Check frontend .env.local
cat .env.local | grep MIDTRANS

# Restart frontend
npm run dev
```

### ❌ Map tidak load
```
Solusi: Refresh browser (Ctrl + Shift + R)
Check console untuk error (F12)
```

### ❌ Order status tidak update
```bash
# Check Laravel log
cd c:\laragon\www\toko-mainan-backend
type storage\logs\laravel.log | Select-Object -Last 50
```

---

## 🔍 Debug Commands

### Check Database
```sql
-- Latest order
SELECT * FROM orders ORDER BY created_at DESC LIMIT 1;

-- Orders with payment info
SELECT 
    order_number, 
    status, 
    payment_status, 
    midtrans_transaction_id,
    created_at 
FROM orders 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

### Check Logs
```powershell
# Backend log (real-time)
cd c:\laragon\www\toko-mainan-backend
Get-Content storage\logs\laravel.log -Wait -Tail 20
```

### Test Callback Manually (Postman)
```
POST http://localhost:8000/api/midtrans/callback
Content-Type: application/json

{
    "order_id": "ORD-20250101120000-1",
    "transaction_status": "settlement",
    "fraud_status": "accept",
    "transaction_id": "test-transaction-123",
    "payment_type": "credit_card"
}
```

---

## 📊 Expected Flow

```
1. Login → ✅
2. Add to Cart → ✅
3. View Cart → ✅
4. Map loads → ✅
5. Click map / Use location → ✅
6. Address auto-fills → ✅
7. Fill phone → ✅
8. Click checkout → ✅
9. Midtrans popup → ✅
10. Enter test card → ✅
11. Payment success → ✅
12. Order status = paid → ✅
13. Cart cleared → ✅
14. Redirect to orders → ✅
```

---

## 🎯 Success Criteria

**Map Location Picker:**
- ✅ Map visible dan interactive
- ✅ Pin dapat di-drop di lokasi apapun
- ✅ Address otomatis dari koordinat
- ✅ Current location detection works

**Midtrans Payment:**
- ✅ Popup Midtrans muncul
- ✅ Test card payment berhasil
- ✅ Order status update otomatis
- ✅ Callback dari Midtrans diterima

**Overall:**
- ✅ Checkout flow tanpa error
- ✅ User experience smooth
- ✅ All features working as expected

---

## 📞 Need Help?

**Check these files:**
1. `IMPLEMENTASI_SUMMARY.md` - Full implementation details
2. `MIDTRANS_SETUP_GUIDE.md` - Complete Midtrans guide
3. `MAP_LOCATION_GUIDE.md` - Map picker documentation

**Check logs:**
- Backend: `storage/logs/laravel.log`
- Frontend: Browser Console (F12)
- Midtrans: Dashboard → Transactions

---

**🎉 Happy Testing!**

Jika semua checklist ✅, maka implementasi BERHASIL!
