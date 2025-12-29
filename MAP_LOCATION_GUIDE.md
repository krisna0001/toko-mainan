# 📍 Map Location Picker - User Guide

## 🎯 Fitur Utama

Map location picker memungkinkan customer untuk:
- ✅ Memilih lokasi pengiriman dengan pin di peta interaktif
- ✅ Address otomatis terisi dari koordinat yang dipilih (reverse geocoding)
- ✅ Detect lokasi saat ini dengan GPS browser
- ✅ Toggle antara pilih dari peta atau tulis manual
- ✅ Koordinat tersimpan untuk tracking pengiriman

---

## 🗺️ Cara Menggunakan

### Metode 1: Pilih dari Peta (Recommended)

1. **Klik di peta** untuk drop pin di lokasi yang diinginkan
2. Address akan **otomatis terisi** berdasarkan lokasi yang Anda pilih
3. Koordinat (latitude, longitude) akan tersimpan
4. Preview address dan koordinat muncul di bawah peta

### Metode 2: Gunakan Current Location

1. Klik tombol **"📍 Use Current Location"**
2. Browser akan meminta izin akses lokasi
3. Klik **"Allow"** untuk memberikan izin
4. Pin akan otomatis muncul di lokasi Anda saat ini
5. Address otomatis terisi

### Metode 3: Input Manual

1. Klik tombol **"✏️ Tulis Manual"**
2. Form input text akan muncul
3. Ketik address secara manual
4. **Catatan:** Koordinat tidak tersimpan jika input manual

---

## 🛠️ Teknologi yang Digunakan

### Frontend (React/Next.js)
- **Leaflet** - Library peta interaktif open source
- **React Leaflet** - React wrapper untuk Leaflet
- **OpenStreetMap** - Provider tile peta gratis
- **Nominatim API** - Reverse geocoding (koordinat → address)

### Backend (Laravel)
- Field `shipping_address` menyimpan address text
- Field `phone` menyimpan nomor telepon
- (Future) Field `coordinates` untuk latitude & longitude

---

## 📦 Instalasi Dependencies

Dependencies sudah terinstall, tapi jika perlu reinstall:

```bash
# Frontend
cd toko-mainan-frontend
npm install leaflet react-leaflet @types/leaflet

# Backend (tidak perlu library tambahan untuk koordinat)
```

---

## 🔧 Konfigurasi

### MapLocationPicker Component

File: `components/MapLocationPicker.tsx`

```tsx
<MapLocationPicker
  onLocationSelect={(address, lat, lng) => {
    setShippingAddress(address);
    setCoordinates({ lat, lng });
  }}
  initialAddress={shippingAddress}
/>
```

**Props:**
- `onLocationSelect` - Callback function yang dipanggil saat user pilih lokasi
  - Parameters: `address: string`, `lat: number`, `lng: number`
- `initialAddress` - Address default untuk pre-fill (optional)

---

## 🌍 Reverse Geocoding API

MapLocationPicker menggunakan **Nominatim API** dari OpenStreetMap untuk convert koordinat ke address.

### API Endpoint
```
https://nominatim.openstreetmap.org/reverse?lat={lat}&lon={lng}&format=json
```

### Response Example
```json
{
  "address": {
    "road": "Jalan Sudirman",
    "suburb": "Senayan",
    "city": "Jakarta Selatan",
    "state": "DKI Jakarta",
    "postcode": "12190",
    "country": "Indonesia"
  },
  "display_name": "Jalan Sudirman, Senayan, Jakarta Selatan, DKI Jakarta 12190, Indonesia"
}
```

### Rate Limiting
- **Limit:** 1 request per second
- **Usage Policy:** Harus include custom User-Agent
- **Alternative:** Bisa ganti dengan Google Geocoding API (berbayar tapi lebih akurat)

---

## 🎨 UI/UX Features

### Loading States
- **Map Loading:** Skeleton dengan spinner saat map belum ready
- **Address Loading:** "Loading address..." saat reverse geocoding
- **Current Location:** Spinner pada tombol saat detecting location

### Error Handling
- Geolocation not supported → Toast error
- Permission denied → Toast error
- Reverse geocoding failed → Fallback ke koordinat saja
- Network error → Retry mechanism

### Visual Feedback
- 🎯 **Red Marker:** Lokasi yang dipilih user
- 📍 **Blue Circle:** Current location radius
- 🗺️ **Interactive Map:** Zoom, pan, click to pin

---

## 🔮 Future Enhancements

### 1️⃣ Simpan Koordinat di Database

**Migration:**
```php
Schema::table('orders', function (Blueprint $table) {
    $table->decimal('shipping_latitude', 10, 8)->nullable();
    $table->decimal('shipping_longitude', 11, 8)->nullable();
});
```

**Model Update:**
```php
protected $fillable = [
    // ... existing fields
    'shipping_latitude',
    'shipping_longitude',
];
```

**Controller Update:**
```php
$order = Order::create([
    // ... existing data
    'shipping_latitude' => $request->latitude,
    'shipping_longitude' => $request->longitude,
]);
```

### 2️⃣ Calculate Shipping Cost by Distance

Implementasi perhitungan ongkir berdasarkan jarak dari toko:

```php
// Haversine formula untuk calculate jarak
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    
    return $distance; // in km
}

// Calculate shipping cost
$distance = calculateDistance($storeLat, $storeLng, $orderLat, $orderLng);
$shippingCost = $distance * 5000; // Rp 5.000 per km
```

### 3️⃣ Show Delivery Tracking on Map

Implementasi fitur tracking kurir secara real-time:

```tsx
<MapContainer center={[orderLat, orderLng]} zoom={13}>
  <TileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
  
  {/* Customer Location */}
  <Marker position={[orderLat, orderLng]} icon={customerIcon}>
    <Popup>Delivery Address</Popup>
  </Marker>
  
  {/* Courier Location (real-time) */}
  <Marker position={[courierLat, courierLng]} icon={courierIcon}>
    <Popup>Courier Location</Popup>
  </Marker>
  
  {/* Polyline showing route */}
  <Polyline positions={routeCoordinates} color="blue" />
</MapContainer>
```

### 4️⃣ Multiple Addresses Management

Fitur untuk save multiple shipping addresses per user:

```php
// Migration
Schema::create('user_addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('label'); // Home, Office, etc
    $table->text('address');
    $table->decimal('latitude', 10, 8);
    $table->decimal('longitude', 11, 8);
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

### 5️⃣ Search Address by Name

Implementasi search address (geocoding - forward):

```typescript
const searchAddress = async (query: string) => {
  const response = await fetch(
    `https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=5`
  );
  const results = await response.json();
  return results; // Array of addresses with coordinates
};
```

---

## 🐛 Troubleshooting

### ❌ Map tidak muncul (blank)
**Penyebab:** Leaflet CSS belum diimport atau window object undefined
**Solusi:**
```tsx
// Gunakan dynamic import dengan ssr: false
const MapLocationPicker = dynamic(() => import('@/components/MapLocationPicker'), {
  ssr: false,
});
```

### ❌ "Cannot read property 'map' of undefined"
**Penyebab:** Component di-render di server-side (SSR)
**Solusi:** Sudah diatasi dengan dynamic import

### ❌ Reverse geocoding lambat
**Penyebab:** Nominatim API free tier dengan rate limiting
**Solusi:** 
1. Implement caching untuk koordinat yang sama
2. Debounce requests
3. Upgrade ke Google Geocoding API (berbayar)

### ❌ Current location tidak detect
**Penyebab:** 
1. Browser tidak support geolocation
2. User deny permission
3. HTTPS required (di production)

**Solusi:**
1. Check browser support: `if (!navigator.geolocation)`
2. Minta user allow permission
3. Deploy dengan SSL certificate untuk production

---

## 📊 Analytics & Tracking

### Metrics yang bisa ditrack:

1. **Usage Statistics**
   - Berapa % user pakai map vs manual input
   - Average time untuk select location
   - Most selected areas/zones

2. **Data Quality**
   - Address accuracy rate
   - Koordinat valid vs invalid
   - Failed reverse geocoding rate

3. **Performance**
   - Map load time
   - Reverse geocoding response time
   - Error rate

### Implementation Example:

```typescript
const trackMapUsage = (action: string) => {
  // Google Analytics
  gtag('event', 'map_interaction', {
    event_category: 'Map Location Picker',
    event_action: action,
  });
};

// Usage
trackMapUsage('pin_dropped');
trackMapUsage('current_location_used');
trackMapUsage('manual_input_selected');
```

---

## 📚 Resources

- [Leaflet Documentation](https://leafletjs.com/reference.html)
- [React Leaflet](https://react-leaflet.js.org/)
- [OpenStreetMap](https://www.openstreetmap.org/)
- [Nominatim API](https://nominatim.org/release-docs/develop/api/Overview/)
- [Google Maps Platform](https://developers.google.com/maps) (alternative)

---

## ✅ Testing Checklist

- [ ] Map muncul dengan benar di browser
- [ ] Click di map untuk drop pin
- [ ] Address otomatis terisi setelah click
- [ ] Koordinat tersimpan dengan benar
- [ ] "Use Current Location" button work
- [ ] Toggle "Tulis Manual" work
- [ ] Map responsive di mobile
- [ ] Loading states terlihat jelas
- [ ] Error handling work (permission denied, etc)

---

**🎉 Happy Mapping! Fitur ini akan membuat checkout process lebih user-friendly!**
