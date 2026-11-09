# 🚀 PETUNJUK PENGGUNAAN AKUN SIMBAR

## 📝 Data Akun yang Telah Dibuat

### ✅ Akun Admin
```
Email:    admin@simbar.local
Password: Admin@1234
Role:     Admin
Status:   Aktif ✓
```

### ✅ Akun Staff
```
Email:    staff@simbar.local
Password: Staff@1234
Role:     Staff
Status:   Aktif ✓
```

---

## 🌐 Cara Login di Aplikasi SIMBAR

### Langkah 1: Buka Login Page
Akses URL di browser:
```
http://localhost:8000/login
```

### Langkah 2: Masukkan Email
Ketikkan salah satu email akun:
- `admin@simbar.local` → untuk login sebagai Admin
- `staff@simbar.local` → untuk login sebagai Staff

### Langkah 3: Masukkan Password
- Admin: `Admin@1234`
- Staff: `Staff@1234`

### Langkah 4: Klik Tombol Masuk
Tekan "Masuk" untuk masuk ke aplikasi

### Langkah 5: Dashboard Muncul
Anda akan melihat dashboard sesuai role:
- **Admin**: Dashboard admin dengan menu kelola barang, persetujuan peminjaman, laporan
- **Staff**: Dashboard staff dengan menu ajukan peminjaman, riwayat peminjaman

---

## 👨‍💼 Menu & Fitur Admin

Setelah login dengan akun admin@simbar.local:

### Dashboard Admin
- Statistik inventaris
- Ringkasan peminjaman
- Data terbaru

### Kelola Inventaris
- **Kategori**: Tambah/edit/hapus kategori barang
- **Lokasi**: Tambah/edit/hapus lokasi penyimpanan
- **Barang**: Tambah/edit/hapus barang, kelola stok

### Peminjaman
- **Persetujuan Peminjaman**: Setujui/tolak permintaan peminjaman dari staff
- **Verifikasi Pengembalian**: Verifikasi pengembalian barang yang dikembalikan
- **Laporan Peminjaman**: Lihat riwayat semua peminjaman

### Laporan & Analisis
- **Peminjaman Terlambat**: Laporan barang yang belum dikembalikan tepat waktu
- **Laporan Inventaris**: Laporan stok semua barang
- **Audit Log**: Riwayat semua aktivitas di sistem

---

## 👨‍💻 Menu & Fitur Staff

Setelah login dengan akun staff@simbar.local:

### Dashboard Staff
- Ringkasan peminjaman Anda
- Barang yang sedang dipinjam
- Deadline pengembalian

### Peminjaman
- **Ajukan Peminjaman Baru**: Buat permintaan peminjaman barang
- **Peminjaman Saya**: Lihat daftar peminjaman yang telah diajukan
  - Lihat status (Menunggu Persetujuan, Disetujui, Ditolak)
  - Lihat detail peminjaman
  - Lihat deadline kembali

### Pengembalian
- **Ajukan Pengembalian**: Kembalikan barang yang sedang dipinjam
- **Riwayat Pengembalian**: Lihat daftar barang yang sudah dikembalikan

### Pencarian
- **Cari Barang**: Cari barang yang bisa dipinjam
- **Filter** berdasarkan kategori, lokasi, ketersediaan

---

## 💡 Contoh Alur Penggunaan

### Scenario 1: Admin Menyiapkan Data Barang

1. **Login sebagai Admin**
   ```
   Email: admin@simbar.local
   Password: Admin@1234
   ```

2. **Kelola Kategori**
   - Menu: Admin → Kelola → Kategori
   - Tambahkan kategori barang (Elektronik, Furniture, dll)

3. **Kelola Lokasi**
   - Menu: Admin → Kelola → Lokasi
   - Tambahkan lokasi penyimpanan (Ruang A, Ruang B, dll)

4. **Kelola Barang**
   - Menu: Admin → Kelola → Barang
   - Tambahkan barang ke inventaris
   - Tentukan kategori, lokasi, stok

5. **Monitor Peminjaman**
   - Menu: Admin → Peminjaman → Persetujuan Peminjaman
   - Review permintaan peminjaman dari staff
   - Setujui atau tolak

### Scenario 2: Staff Meminjam Barang

1. **Login sebagai Staff**
   ```
   Email: staff@simbar.local
   Password: Staff@1234
   ```

2. **Cari Barang yang Tersedia**
   - Menu: Staff → Cari Barang
   - Lihat barang apa yang bisa dipinjam
   - Lihat ketersediaan dan lokasi

3. **Ajukan Peminjaman**
   - Menu: Staff → Peminjaman → Ajukan Peminjaman
   - Pilih barang yang ingin dipinjam
   - Tentukan tanggal kembali
   - Tambahkan catatan/tujuan

4. **Tunggu Persetujuan**
   - Admin akan review permintaan Anda
   - Status akan berubah dari "Menunggu Persetujuan" → "Disetujui"

5. **Lihat Peminjaman Aktif**
   - Menu: Staff → Peminjaman Saya
   - Lihat barang yang sedang dipinjam
   - Lihat deadline kembali

6. **Kembalikan Barang**
   - Menu: Staff → Pengembalian → Ajukan Pengembalian
   - Pilih barang yang akan dikembalikan
   - Konfirmasi kondisi barang
   - Submit

7. **Verifikasi Admin**
   - Admin menerima permintaan pengembalian
   - Verifikasi barang yang dikembalikan
   - Status berubah menjadi "Selesai"

---

## 🔒 Keamanan Password

### Persyaratan Password (Untuk Akun Baru)
- Minimal 8 karakter
- Mengandung huruf besar (A-Z) dan kecil (a-z)
- Mengandung minimal 1 angka (0-9)
- Mengandung minimal 1 simbol (!@#$%^&* dll)

### Password yang Disediakan
- Admin: `Admin@1234` (sudah memenuhi semua persyaratan)
- Staff: `Staff@1234` (sudah memenuhi semua persyaratan)

### Ganti Password (Opsional)
Jika ingin mengubah password:
1. Login dengan akun Anda
2. Menu Profile / Pengaturan (jika ada)
3. Klik "Ubah Password"
4. Masukkan password lama dan password baru
5. Pastikan password baru memenuhi persyaratan
6. Simpan

---

## 🆘 Jika Lupa Password

Hubungi Administrator untuk:
1. Reset password Anda
2. Atau, Administrator dapat membuat akun baru untuk Anda

**Command untuk reset password (Admin saja)**:
```bash
php artisan user:create
# Pilih email yang sudah ada
# Pilih "Ubah password"
# Masukkan password baru
```

---

## ⚠️ Penting Diingat

1. **Jangan bagikan password** akun Anda kepada orang lain
2. **Gunakan password yang kuat** jika membuat akun baru
3. **Logout setelah selesai** menggunakan aplikasi
4. **Verifikasi identitas** sebelum memberikan akses admin

---

## 📞 Support

Jika ada masalah:

### Problem: Tidak Bisa Login
**Solusi**:
- Pastikan email dan password benar
- Cek Caps Lock keyboard
- Clear browser cache
- Reload halaman

### Problem: Dashboard Tidak Muncul
**Solusi**:
- Pastikan database MySQL running
- Reload halaman (F5 atau Ctrl+R)
- Buka terminal baru dan jalankan:
  ```bash
  php artisan serve
  ```

### Problem: Menu Tidak Sesuai Role
**Solusi**:
- Logout dan login lagi
- Periksa role akun di database:
  ```bash
  php artisan verify:accounts
  ```

### Verifikasi Cepat
```bash
# Check database connection
php artisan tinker
DB::connection()->getPDO()

# Check users table
User::all()

# Check specific account
User::where('email', 'admin@simbar.local')->first()
```

---

## ✅ Checklist Siap Pakai

- [x] Akun Admin sudah dibuat (admin@simbar.local)
- [x] Akun Staff sudah dibuat (staff@simbar.local)
- [x] Password sudah tersimpan dan terenkripsi
- [x] Role sudah benar di database
- [x] Kedua akun sudah aktif
- [x] Database MySQL sudah terkoneksi
- [x] Aplikasi SIMBAR siap diakses

---

**Selamat menggunakan SIMBAR!** 🎉

Untuk mulai: Buka http://localhost:8000/login dan login dengan salah satu akun di atas.
