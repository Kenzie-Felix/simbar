# SIMBAR Logo Guide

## Logo Overview

SIMBAR (Sistem Manajemen Barang / Inventory Management System) sekarang memiliki logo profesional yang mewakili:
- **S** - Singkatan SIMBAR
- **Kotak-kotak** - Mewakili inventory/barang yang tersimpan
- **Lingkaran biru** - Profesionalisme dan kepercayaan

## Logo Files

### 1. **logo-simbar.svg** (32x32px)
- **Lokasi:** `public/logo-simbar.svg`
- **Ukuran:** 32x32 piksel
- **Penggunaan:** Navbar, favicon, icon aplikasi
- **Format:** SVG (scalable)

```blade
<img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR Logo" style="width: 32px; height: 32px;" />
```

### 2. **logo-simbar-large.svg** (200x200px)
- **Lokasi:** `public/logo-simbar-large.svg`
- **Ukuran:** 200x200 piksel
- **Penggunaan:** Landing page, welcome screen, dokumentasi
- **Format:** SVG (scalable)

```blade
<img src="{{ asset('logo-simbar-large.svg') }}" alt="SIMBAR Logo" style="width: 200px; height: 200px;" />
```

## Karakteristik Logo

### Warna
- **Warna Utama:** #0066cc (Biru profesional)
- **Warna Sekunder:** #1e40af (Biru lebih gelap)
- **Gradient:** Linear dari #0066cc ke #1e40af

### Elemen Desain

#### 1. Lingkaran Outer
- Merepresentasikan kesatuan sistem
- Memberikan kesan profesional dan terpercaya

#### 2. Huruf "S"
- Mewakili huruf pertama dari SIMBAR
- Desain modern dan elegan

#### 3. Kotak-kotak (Inventory)
- Tiga kotak bertumpuk
- Mewakili penyimpanan dan manajemen barang
- Berbagai tingkat opacity untuk kedalaman visual

#### 4. Elemen Dekoratif
- Lingkaran-lingkaran kecil
- Mewakili pergerakan inventory
- Menunjukkan dinamika sistem

## Penempatan Logo

### Navbar
```blade
<img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR Logo" />
<span>SIMBAR</span>
```

### Welcome Page / Landing
```blade
<img src="{{ asset('logo-simbar-large.svg') }}" alt="SIMBAR" />
```

### Favicon
```blade
<link rel="icon" href="{{ asset('logo-simbar.svg') }}" type="image/svg+xml" />
```

## Spesifikasi Teknis

### SVG Properties
- **Viewbox:** 0 0 100 100 (small), 0 0 200 200 (large)
- **Format:** SVG (Scalable Vector Graphics)
- **Keuntungan:**
  - Dapat di-scale tanpa kehilangan kualitas
  - File size kecil
  - Support di semua browser modern
  - Dapat diwarnai dengan CSS

### Kompatibilitas
- ✅ Chrome, Firefox, Safari, Edge
- ✅ Mobile browsers
- ✅ Print-ready
- ✅ Accessible

## Cara Menggunakan Logo

### 1. Di Navbar (Sudah Implemented)
```blade
<a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: var(--spacing-sm);">
    <img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR Logo" style="width: 32px; height: 32px;" />
    <span>SIMBAR</span>
</a>
```

### 2. Di Halaman Welcome
```blade
<div class="welcome-logo">
    <img src="{{ asset('logo-simbar-large.svg') }}" alt="SIMBAR Logo" />
</div>
```

### 3. Di Email / Dokumentasi
```blade
<img src="{{ asset('logo-simbar-large.svg') }}" alt="SIMBAR" width="150" height="150" />
```

### 4. Sebagai Favicon
```blade
<link rel="icon" href="{{ asset('logo-simbar.svg') }}" type="image/svg+xml" />
```

## Styling Logo

### Dengan CSS
```css
.simbar-logo {
    width: 32px;
    height: 32px;
    display: inline-block;
}

.simbar-logo-large {
    width: 200px;
    height: 200px;
    margin: 20px auto;
}
```

### Dengan Tailwind CSS
```blade
<img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR" class="w-8 h-8" />
<img src="{{ asset('logo-simbar-large.svg') }}" alt="SIMBAR" class="w-50 h-50" />
```

## Perubahan Terbaru

### Dari: Logo Dunia (bi-bi-boxes)
```blade
<i class="bi bi-boxes" style="margin-right: var(--spacing-sm);" />
```

### Ke: Logo SIMBAR Custom
```blade
<img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR Logo" style="width: 32px; height: 32px;" />
```

## Customization

Jika perlu mengubah logo:

1. **Edit warna:** Ubah #0066cc dengan warna yang diinginkan
2. **Edit ukuran:** Ubah viewBox untuk memperbesar/memperkecil elemen
3. **Edit bentuk:** Modifikasi path untuk desain yang berbeda

### File SVG Location
```
public/
├── logo-simbar.svg (32x32)
└── logo-simbar-large.svg (200x200)
```

## Best Practices

✅ Selalu gunakan logo dengan background yang kontras  
✅ Jangan distorsi aspek ratio  
✅ Gunakan ukuran minimal 24x24 piksel  
✅ Berikan padding/spacing yang cukup  
✅ Gunakan warna solid untuk area kecil  
✅ Test di berbagai device dan browser  

## Troubleshooting

### Logo tidak muncul
- Pastikan file SVG di folder `public/`
- Gunakan `asset()` helper untuk path
- Check file permissions

### Logo blur di mobile
- Gunakan `image-rendering: crisp-edges` di CSS
- Pastikan ukuran dalam CSS match dengan SVG

### Warna berbeda di browser lain
- SVG menggunakan warna absolut, konsisten di semua browser
- Jika butuh perubahan warna, edit SVG file

## Next Steps

### Saran Pengembangan
1. Buat favicon dari logo SIMBAR
2. Gunakan logo di email template
3. Buat logo versi monochrome untuk print
4. Tambahkan logo di welcome page
5. Buat animated version untuk loading screen

---

**Logo Status:** ✅ Active  
**Last Updated:** November 9, 2026  
**File Format:** SVG (Scalable Vector Graphics)  
**Location:** `public/` directory
