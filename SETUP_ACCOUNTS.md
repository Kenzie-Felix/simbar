# 🔐 SIMBAR - Setup Akun Admin dan Staff

**Tanggal**: 2026-11-08  
**Status**: Ready untuk eksekusi  
**Database**: MySQL simbar (terkoneksi)  

---

## 📋 Overview

Dokumen ini menjelaskan cara membuat akun Admin dan Staff untuk SIMBAR dengan aman.

### Yang Akan Dibuat
- ✅ **Akun Admin**: admin@simbar.local
- ✅ **Akun Staff**: staff@simbar.local
- ✅ Kedua akun dengan password aman yang memenuhi persyaratan
- ✅ Keduanya berstatus aktif dan siap digunakan

### Persyaratan Password
- Minimal 8 karakter
- Mengandung huruf besar dan kecil (A-Z, a-z)
- Mengandung minimal 1 angka (0-9)
- Mengandung minimal 1 simbol (!@#$%^&* dll)

**Contoh password yang valid**: `Admin@1234`, `Staff#Pass99`, `Simbar!2026`

---

## 🚀 Cara Membuat Akun (Interactive)

### Step 1: Jalankan Command

```bash
php artisan user:create
```

### Step 2: Ikuti Prompt Interaktif

```
╔════════════════════════════════════════════════════════════╗
║        SIMBAR - CREATE USER ACCOUNT SAFELY                ║
╚════════════════════════════════════════════════════════════╝

Pilih role pengguna:
  [1] Admin
  [2] Staff
  > 1                              # Pilih 1 untuk Admin

Masukkan nama lengkap pengguna
> Administrator

Masukkan alamat email
> admin@simbar.local

🔐 Input Password:
   Password harus memenuhi persyaratan:
   • Minimal 8 karakter
   • Mengandung huruf besar dan kecil
   • Mengandung minimal 1 angka
   • Mengandung minimal 1 simbol (!@#$%^&* dll)

Masukkan password
> ••••••••••              # Password tidak ditampilkan

Konfirmasi password
> ••••••••••              # Password tidak ditampilkan

📝 Konfirmasi Data:
   Nama: Administrator
   Email: admin@simbar.local
   Role: Admin
   Status: Aktif

Lanjutkan membuat akun ini? (yes/no) [no]:
> yes

✅ Akun berhasil dibuat!

╔════════════════════════════════════════════════════════════╗
║        DETAIL AKUN YANG TELAH DIBUAT                      ║
╠════════════════════════════════════════════════════════════╣
║ ID: 1
║ Nama: Administrator
║ Email: admin@simbar.local
║ Role: Admin
║ Status: Aktif ✓
║ Dibuat: 08-11-2026 20:30:45
╚════════════════════════════════════════════════════════════╝

🔍 Verifikasi di Database...
✅ Data tersimpan di database MySQL simbar
   ID: 1
   Email: admin@simbar.local
   Role: Admin
   is_active: 1
```

### Step 3: Ulangi untuk Akun Staff

```bash
php artisan user:create
```

Pilih role **Staff** dan gunakan email `staff@simbar.local`

---

## 🛠️ Fitur Command

### ✅ Yang Dilakukan
- ✓ Menanyakan role (Admin atau Staff)
- ✓ Meminta nama lengkap
- ✓ Meminta email dengan validasi format
- ✓ Validasi email unik (tidak boleh duplikat)
- ✓ Input password interaktif (aman, tidak ditampilkan di layar)
- ✓ Konfirmasi password untuk menghindari typo
- ✓ Validasi password sesuai persyaratan
- ✓ Menampilkan konfirmasi sebelum menyimpan
- ✓ **Verifikasi langsung ke database setelah membuat akun**
- ✓ Menampilkan detail akun yang sudah tersimpan

### ⚠️ Proteksi Data
- ✓ **Tidak menimpa akun yang sudah ada** - jika email sudah terdaftar, command akan:
  - Menampilkan data akun yang ada
  - Menawarkan opsi untuk update password/role/status
  - TIDAK menghapus atau mengganti data tanpa persetujuan
- ✓ Password hashed dengan Laravel Hash (aman)
- ✓ Akun otomatis aktif saat dibuat
- ✓ Tidak membuat dummy data barang/kategori/peminjaman

---

## 📊 Verifikasi Akun di Database

Setelah membuat akun, command akan menampilkan verifikasi otomatis. Namun, Anda juga bisa memverifikasi secara manual:

### Via MySQL CLI

```sql
SELECT id, name, email, role, is_active, created_at FROM users WHERE email IN ('admin@simbar.local', 'staff@simbar.local');
```

**Output yang diharapkan**:
```
+----+---------------+--------------------+-------+-----------+---------------------+
| id | name          | email              | role  | is_active | created_at          |
+----+---------------+--------------------+-------+-----------+---------------------+
|  1 | Administrator | admin@simbar.local | Admin |         1 | 2026-11-08 20:30:45 |
|  2 | Staf          | staff@simbar.local | Staff |         1 | 2026-11-08 20:31:10 |
+----+---------------+--------------------+-------+-----------+---------------------+
```

### Via Laravel Tinker

```bash
php artisan tinker
```

```php
User::where('email', 'admin@simbar.local')->first();
User::where('email', 'staff@simbar.local')->first();
```

---

## 🔐 Menggunakan Akun Baru

### Login di Aplikasi

1. Buka http://localhost:8000/login
2. Masukkan email: `admin@simbar.local` atau `staff@simbar.local`
3. Masukkan password yang telah dibuat
4. Klik "Masuk"

### Akses Role-Based

- **Admin**: Dapat akses dashboard admin, kelola inventaris, persetujuan peminjaman
- **Staff**: Dapat akses dashboard staff, ajukan peminjaman, lihat riwayat peminjaman

---

## 🔄 Mengelola Akun Existing

Jika Anda membuat akun dengan email yang sudah ada, command akan menawarkan opsi:

1. **Ubah password** - Update password akun yang sudah ada
2. **Aktifkan/Nonaktifkan akun** - Ubah status is_active
3. **Ubah role** - Ubah role dari Admin ke Staff atau sebaliknya
4. **Batalkan** - Batal (tidak mengubah apa pun)

---

## ⚙️ Konfigurasi Database

**Verifikasi sebelum membuat akun**:

```bash
php artisan tinker
```

```php
DB::connection()->getPDO();  // Harus terhubung
DB::select('SELECT * FROM users LIMIT 1');  // Harus terhubung ke simbar
```

**Atau via .env**:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simbar
DB_USERNAME=root
DB_PASSWORD=
```

---

## ❌ Batasan & Proteksi

### Batasan Wajib
- ❌ **Tidak** menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`
- ❌ **Tidak** menjalankan seeder (TestDataSeeder, DatabaseSeeder)
- ❌ **Tidak** menghapus tabel atau data existing
- ❌ **Tidak** membuat dummy data barang/kategori/peminjaman
- ❌ **Tidak** mengubah konfigurasi database

### Proteksi Yang Diterapkan
- ✅ Command hanya membuat/update user, tidak mengubah tabel lain
- ✅ Email unik, tidak bisa duplikat
- ✅ Password selalu di-hash, tidak disimpan plain text
- ✅ Akun existing tidak dihapus, hanya bisa di-update jika user setuju
- ✅ Verifikasi langsung ke database setelah operasi

---

## 📝 Troubleshooting

### "Email sudah terdaftar"
Jika Anda ingin membuat akun dengan email yang berbeda:
- Gunakan email lain saat diminta
- Atau, pilih opsi untuk update password akun yang ada

### "Password tidak memenuhi persyaratan"
Pastikan password Anda:
- ✓ Minimal 8 karakter
- ✓ Ada huruf besar (A-Z)
- ✓ Ada huruf kecil (a-z)
- ✓ Ada angka (0-9)
- ✓ Ada simbol (!@#$%^&* dll)

**Contoh valid**: `Admin@1234`, `Staff#Pass99`

### "Data tidak tersimpan di database"
Periksa:
- Database MySQL simbar sedang berjalan: `mysql -u root simbar -e "SELECT 1"`
- File .env menggunakan MySQL simbar (bukan SQLite)
- Tidak ada constraint unik yang dilanggar

---

## ✅ Checklist Setelah Setup

- [ ] Kedua akun (Admin dan Staff) berhasil dibuat
- [ ] Verifikasi database menunjukkan kedua akun tersimpan
- [ ] Password tersimpan terenkripsi (hashed)
- [ ] Role akun sesuai (Admin untuk admin@simbar.local, Staff untuk staff@simbar.local)
- [ ] Kedua akun berstatus aktif (is_active = 1)
- [ ] Login dengan kedua akun berhasil
- [ ] Dashboard muncul sesuai role (Admin dashboard vs Staff dashboard)

---

## 📞 Bantuan

Jika ada masalah, periksa:
1. MySQL running: `tasklist | findstr mysqld`
2. Database terkoneksi: `php artisan tinker` → `DB::connection()->getPDO()`
3. Tabel users ada: `php artisan tinker` → `Schema::hasTable('users')`
4. Laravel logs: `tail -f storage/logs/laravel.log`

---

**Siap membuat akun? Jalankan: `php artisan user:create`**
