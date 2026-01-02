# Update Fitur Blokir Customer

## 📌 Perubahan yang Dilakukan

### Backend (AuthController.php)

Ditambahkan pengecekan `is_blocked` pada 2 endpoint login:

#### 1. Customer Login (`/api/auth/login`)
```php
// Setelah autentikasi berhasil, cek apakah user diblokir
if ($user->is_blocked) {
    auth()->logout();
    return response()->json([
        'success' => false,
        'message' => 'Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut.'
    ], 403);
}
```

#### 2. Admin Login (`/api/auth/admin/login`)
```php
// Setelah autentikasi berhasil, cek apakah admin diblokir
if ($user->is_blocked) {
    auth()->logout();
    return response()->json([
        'success' => false,
        'message' => 'Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut.'
    ], 403);
}
```

### Frontend
Frontend sudah otomatis menangani error message dari backend:
- File `app/login/page.tsx` - Customer login
- File `components/Navbar.tsx` - Admin login modal

Keduanya sudah menggunakan:
```typescript
toast.error(error.response?.data?.message || 'Login failed');
```

## ✅ Cara Kerja

1. **Admin memblokir customer** melalui halaman `/admin/customers`
2. **Customer yang diblokir mencoba login**
3. **Backend mengecek field `is_blocked`** setelah kredensial valid
4. **Jika `is_blocked = true`**:
   - Session langsung di-logout
   - Return error 403 dengan message: "Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut."
5. **Frontend menampilkan pesan error** via toast notification

## 🔒 Keamanan

- ✅ Customer yang diblokir **tidak bisa login**
- ✅ Session langsung di-logout jika user diblokir
- ✅ Pesan error yang jelas untuk user
- ✅ HTTP Status Code 403 (Forbidden) untuk akun yang diblokir
- ✅ Pengecekan berlaku untuk customer dan admin

## 📝 Testing

### Manual Test:
1. Login sebagai admin
2. Buka `/admin/customers`
3. Klik tombol "Block" pada salah satu customer
4. Logout dari admin
5. Coba login menggunakan akun customer yang sudah diblokir
6. Akan muncul pesan: **"Akun Anda di-suspend. Hubungi admin untuk informasi lebih lanjut."**

### Expected Result:
- ❌ Login gagal
- 🔔 Toast notification menampilkan pesan suspend
- 🚫 User tidak bisa masuk ke sistem
