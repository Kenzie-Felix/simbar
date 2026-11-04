# Requirements Document

## Introduction

SIMBAR adalah Sistem Manajemen Inventaris Barang Kantor yang dirancang untuk mengelola stok, peminjaman, dan pengembalian barang-barang kantor secara efisien. Sistem ini melayani dua tipe pengguna utama: Admin yang mengelola sistem secara keseluruhan, dan Staff yang melakukan peminjaman dan pengembalian barang. Sistem ini mengimplementasikan Role-Based Access Control (RBAC) yang ketat untuk memastikan setiap pengguna hanya dapat mengakses fitur dan data yang sesuai dengan peran mereka.

## Glossary

- **System**: SIMBAR (Sistem Manajemen Inventaris Barang Kantor)
- **Admin**: Pengguna dengan akses penuh ke semua fitur dan data sistem
- **Staff**: Pengguna yang dapat melihat barang, mengajukan peminjaman, dan mengelola pengembalian
- **Barang**: Item fisik yang dikelola dalam inventaris sistem
- **Kategori**: Pengelompokan jenis barang (mis: Elektronik, Furniture, Alat Tulis)
- **Lokasi**: Tempat penyimpanan atau penempatan barang di kantor
- **Stok**: Jumlah ketersediaan barang pada waktu tertentu
- **Peminjaman**: Proses pengajuan dan pemberian izin penggunaan barang oleh Staff
- **Pengembalian**: Proses Staff mengembalikan barang yang dipinjam
- **Persetujuan**: Keputusan Admin untuk menerima atau menolak pengajuan peminjaman
- **Verifikasi**: Proses Admin mengkonfirmasi penerimaan barang yang dikembalikan
- **Kondisi_Barang**: Status fisik barang (Baik, Rusak Ringan, Rusak Berat, Hilang)
- **RBAC**: Role-Based Access Control mekanisme kontrol akses berdasarkan peran pengguna
- **Akses_Langsung**: Upaya mengakses halaman atau fitur melalui URL tanpa autorisasi yang sesuai
- **Laporan**: Dokumen ringkasan yang menampilkan informasi inventaris dan peminjaman
- **Riwayat**: Catatan lengkap aktivitas peminjaman dan pengembalian seorang pengguna

---

## Requirements

### Requirement 1: Registrasi dan Autentikasi Pengguna

**User Story:** Sebagai pengguna baru, saya ingin dapat membuat akun dengan kredensial yang unik, sehingga saya dapat mengakses sistem dengan aman.

#### Acceptance Criteria

1. WHEN pengguna melengkapi formulir registrasi dengan email unik, password valid, dan nama lengkap, THE System SHALL menyimpan akun baru dengan password yang terenkripsi
2. WHEN pengguna mencoba mendaftar dengan email yang sudah terdaftar, THE System SHALL menolak dan menampilkan pesan error yang jelas
3. WHEN pengguna memasukkan kredensial yang benar pada formulir login, THE System SHALL membuat session pengguna dan mengarahkan ke dashboard sesuai role
4. WHEN pengguna memasukkan kredensial yang salah, THE System SHALL menolak login dan menampilkan pesan error tanpa mengungkap detail akun

### Requirement 2: Role-Based Access Control (RBAC) - Pembatasan Akses Admin

**User Story:** Sebagai administrator sistem, saya ingin Staff tidak dapat mengakses halaman admin melalui URL langsung, sehingga data dan fungsi sensitif terlindungi.

#### Acceptance Criteria

1. WHEN Staff mencoba mengakses URL halaman admin (mis: /admin, /admin/barang, /admin/laporan), THE System SHALL menolak akses dan mengarahkan ke halaman yang sesuai dengan role Staff atau menampilkan halaman error 403 Forbidden
2. WHEN Admin mencoba mengakses URL halaman admin, THE System SHALL memberikan akses penuh ke semua fitur admin
3. WHILE session pengguna belum ter-autentikasi, THE System SHALL menolak semua akses ke halaman admin dan Staff, dan mengarahkan ke halaman login
4. WHEN pengguna berhasil login, THE System SHALL menyimpan role pengguna dalam session dan menggunakan role tersebut untuk setiap validasi akses

### Requirement 3: Manajemen Data Barang oleh Admin

**User Story:** Sebagai Admin, saya ingin dapat menambah, mengubah, dan menghapus data barang beserta detail kategori dan lokasi penyimpanannya, sehingga inventaris selalu akurat.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman manajemen barang dan mengisi formulir tambah barang dengan kode barang unik, nama, kategori, lokasi, dan stok awal, THE System SHALL menyimpan barang baru ke database
2. WHEN Admin memasukkan kode barang yang sudah ada, THE System SHALL menolak dan menampilkan pesan error bahwa kode barang sudah terdaftar
3. WHEN Admin memilih barang dari daftar dan mengubah data (nama, kategori, lokasi, atau stok), THE System SHALL menyimpan perubahan dan menampilkan konfirmasi
4. WHEN Admin menghapus barang dari sistem, THE System SHALL menghapus data barang dan seluruh riwayat terkait atau menampilkan warning jika ada transaksi aktif
5. THE System SHALL menyimpan audit log untuk setiap perubahan data barang termasuk kapan, siapa, dan apa yang diubah

### Requirement 4: Manajemen Kategori Barang

**User Story:** Sebagai Admin, saya ingin dapat membuat dan mengelola kategori barang, sehingga inventaris dapat diorganisir dengan baik.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman manajemen kategori dan menambah kategori dengan nama unik, THE System SHALL menyimpan kategori baru
2. WHEN Admin mencoba menambah kategori dengan nama yang sudah ada, THE System SHALL menolak dan menampilkan pesan error
3. WHEN Admin mengubah nama kategori, THE System SHALL menyimpan perubahan dan memperbarui semua barang yang menggunakan kategori tersebut
4. WHEN Admin menghapus kategori yang masih digunakan barang, THE System SHALL menampilkan warning dan meminta konfirmasi atau mengarahkan untuk mengubah kategori barang terlebih dahulu

### Requirement 5: Manajemen Lokasi Penyimpanan

**User Story:** Sebagai Admin, saya ingin mengelola lokasi penyimpanan barang di kantor, sehingga saya dapat melacak di mana setiap barang berada.

#### Acceptance Criteria

1. WHEN Admin menambah lokasi dengan nama unik (mis: Ruang Kantor A, Gudang B), THE System SHALL menyimpan lokasi baru
2. WHEN Admin mengubah atau menghapus lokasi, THE System SHALL memperbarui atau menampilkan warning jika ada barang pada lokasi tersebut
3. THE System SHALL menampilkan daftar lengkap barang untuk setiap lokasi pada halaman detail lokasi

### Requirement 6: Manajemen Akun Staff

**User Story:** Sebagai Admin, saya ingin dapat membuat, menonaktifkan, dan mengelola akun staff, sehingga kontrol pengguna tetap terjaga.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman manajemen akun staff dan mengisi formulir dengan email, nama, dan departemen, THE System SHALL membuat akun staff baru dengan role staff
2. WHEN Admin menonaktifkan akun staff, THE System SHALL mencegah staff tersebut login dan mengakses sistem
3. WHEN Admin mengaktifkan kembali akun staff yang dinonaktifkan, THE System SHALL memungkinkan staff kembali login
4. THE System SHALL menampilkan daftar semua staff dengan status akun (aktif/nonaktif) dan tanggal pembuatan akun

### Requirement 7: Input Stok Barang Masuk

**User Story:** Sebagai Admin, saya ingin mencatat setiap penambahan stok barang baru yang masuk ke gudang, sehingga stok selalu ter-update dengan akurat.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman input stok masuk dan memilih barang, memasukkan jumlah, tanggal penerimaan, dan keterangan sumber, THE System SHALL menambah stok barang dan membuat record transaksi
2. WHEN Admin memasukkan jumlah yang tidak valid (negatif atau non-angka), THE System SHALL menolak dan menampilkan pesan validasi
3. THE System SHALL menampilkan riwayat lengkap setiap transaksi stok masuk termasuk tanggal, jumlah, sumber, dan admin yang mencatatnya
4. WHEN Admin ingin membatalkan transaksi stok masuk, THE System SHALL menampilkan warning bahwa stok akan berkurang dan meminta konfirmasi

### Requirement 8: Pengajuan Peminjaman oleh Staff

**User Story:** Sebagai Staff, saya ingin mengajukan peminjaman barang dengan memilih barang yang diinginkan dan tanggal pengembalian yang direncanakan, sehingga saya dapat menggunakan barang yang saya butuhkan.

#### Acceptance Criteria

1. WHEN Staff mengakses halaman pengajuan peminjaman dan memilih barang dengan stok tersedia, memasukkan jumlah yang diinginkan, tanggal rencana pengembalian, THE System SHALL menyimpan pengajuan dengan status "Menunggu Persetujuan"
2. WHEN Staff mencoba meminjam barang dengan jumlah melebihi stok tersedia, THE System SHALL menolak dan menampilkan jumlah stok yang tersedia
3. WHEN Staff memasukkan tanggal pengembalian yang lebih awal dari tanggal pengajuan, THE System SHALL menolak dan menampilkan pesan error validasi tanggal
4. WHEN Staff mengajukan peminjaman, THE System SHALL menampilkan notifikasi konfirmasi bahwa pengajuan telah dikirim

### Requirement 9: Persetujuan Peminjaman oleh Admin

**User Story:** Sebagai Admin, saya ingin meninjau setiap pengajuan peminjaman dari Staff dan menyetujui atau menolaknya dengan memberikan keterangan, sehingga saya dapat mengontrol distribusi barang.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman persetujuan peminjaman, THE System SHALL menampilkan daftar pengajuan dengan status "Menunggu Persetujuan" beserta detail barang, Staff, jumlah, dan tanggal
2. WHEN Admin mengklik tombol Setujui pada pengajuan peminjaman, THE System SHALL mengubah status menjadi "Disetujui", mengurangi stok barang, dan mengirim notifikasi ke Staff
3. WHEN Admin mengklik tombol Tolak dan memasukkan alasan penolakan, THE System SHALL mengubah status menjadi "Ditolak", menyimpan alasan, dan mengirim notifikasi ke Staff
4. WHEN Admin menyetujui pengajuan peminjaman, THE System SHALL membuat record peminjaman aktif dengan tanggal pengajuan, tanggal rencana pengembalian, dan status "Dipinjam"

### Requirement 10: Pengajuan Pengembalian oleh Staff

**User Story:** Sebagai Staff, saya ingin mengajukan pengembalian barang yang telah saya pinjam, sehingga barang dapat kembali ke inventaris dan dapat digunakan kembali.

#### Acceptance Criteria

1. WHEN Staff mengakses halaman riwayat peminjaman dan memilih peminjaman aktif dengan status "Dipinjam", kemudian mengklik tombol Ajukan Pengembalian, THE System SHALL menampilkan formulir pengembalian dengan detail barang
2. WHEN Staff mengisi form pengembalian dengan pilihan kondisi barang (Baik, Rusak Ringan, Rusak Berat, Hilang) dan keterangan tambahan, THE System SHALL menyimpan pengajuan pengembalian dengan status "Menunggu Verifikasi"
3. WHEN Staff mengajukan pengembalian barang dengan jumlah lebih sedikit dari yang dipinjam, THE System SHALL memungkinkan pengembalian sebagian dengan mencatat selisih yang belum dikembalikan
4. THE System SHALL menampilkan notifikasi kepada Staff bahwa pengajuan pengembalian telah dikirim

### Requirement 11: Verifikasi Pengembalian oleh Admin

**User Story:** Sebagai Admin, saya ingin memverifikasi setiap pengembalian barang dari Staff dan mencatat kondisi barang yang dikembalikan, sehingga inventaris dan kondisi barang tetap tercatat dengan akurat.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman verifikasi pengembalian, THE System SHALL menampilkan daftar pengajuan pengembalian dengan status "Menunggu Verifikasi" beserta detail peminjaman dan kondisi yang dilaporkan Staff
2. WHEN Admin mengklik tombol Verifikasi dan memilih status verifikasi (Terima atau Tolak), THE System SHALL menanyakan kondisi barang yang sebenarnya diterima
3. WHEN Admin mengkonfirmasi penerimaan barang dengan kondisi tertentu, THE System SHALL menambah stok barang, mengubah status peminjaman menjadi "Dikembalikan", dan menyimpan catatan kondisi barang
4. WHEN Admin menolak pengembalian dengan keterangan, THE System SHALL mengubah status menjadi "Pengembalian Ditolak", menyimpan alasan penolakan, dan mengirim notifikasi ke Staff
5. IF kondisi barang yang diterima menunjukkan kerusakan yang signifikan, THEN THE System SHALL memperbarui status kondisi barang menjadi "Rusak" dan memberikan notifikasi untuk perbaikan atau penghapusan

### Requirement 12: Manajemen Kondisi Barang

**User Story:** Sebagai Admin, saya ingin mengelola dan memperbaharui status kondisi setiap barang, sehingga saya dapat melacak barang mana saja yang masih layak pakai.

#### Acceptance Criteria

1. WHEN Admin mengakses detail barang, THE System SHALL menampilkan riwayat lengkap kondisi barang termasuk tanggal perubahan, status sebelum dan sesudah, serta alasan perubahan
2. WHEN Admin mengubah status kondisi barang (dari Baik menjadi Rusak Ringan atau status lainnya), THE System SHALL menyimpan perubahan dan mencatat siapa serta kapan perubahan dilakukan
3. WHEN status kondisi barang berubah menjadi "Rusak Berat" atau "Hilang", THE System SHALL secara otomatis mengurangi stok tersedia atau menghapus dari daftar barang yang dapat dipinjam
4. THE System SHALL menampilkan badge atau indikator visual pada barang dengan kondisi tidak baik (rusak atau hilang) di semua halaman yang menampilkan daftar barang

### Requirement 13: Daftar dan Pencarian Barang oleh Staff

**User Story:** Sebagai Staff, saya ingin dapat melihat daftar lengkap barang beserta ketersediaan stoknya, dan dapat mencari barang berdasarkan nama atau kategori, sehingga saya dapat menemukan barang yang saya butuhkan dengan mudah.

#### Acceptance Criteria

1. WHEN Staff mengakses halaman daftar barang, THE System SHALL menampilkan semua barang dengan kondisi "Baik" atau "Rusak Ringan" beserta nama, kategori, lokasi, stok tersedia, dan status ketersediaan
2. WHEN Staff menggunakan fitur pencarian dengan memasukkan keyword nama barang, THE System SHALL menampilkan hasil pencarian yang sesuai secara real-time atau setelah tombol cari ditekan
3. WHEN Staff memilih kategori tertentu, THE System SHALL memfilter dan menampilkan hanya barang dengan kategori terpilih
4. WHEN Staff melihat barang dengan stok tersedia 0, THE System SHALL menampilkan status "Tidak Tersedia" atau "Habis" pada barang tersebut
5. WHEN Staff mengklik detail barang, THE System SHALL menampilkan informasi lengkap termasuk deskripsi, lokasi, jumlah stok, dan tombol "Ajukan Peminjaman"

### Requirement 14: Riwayat Peminjaman Pribadi Staff

**User Story:** Sebagai Staff, saya ingin melihat riwayat lengkap semua peminjaman saya baik yang sedang berjalan maupun yang sudah selesai, sehingga saya dapat melacak aktivitas peminjaman saya.

#### Acceptance Criteria

1. WHEN Staff mengakses halaman riwayat peminjaman, THE System SHALL menampilkan daftar semua peminjaman Staff tersebut dengan status (Menunggu Persetujuan, Disetujui, Dipinjam, Dikembalikan, Ditolak)
2. WHEN Staff memfilter berdasarkan status peminjaman, THE System SHALL menampilkan hanya peminjaman dengan status terpilih
3. WHEN Staff memilih peminjaman dari daftar, THE System SHALL menampilkan detail lengkap termasuk barang, jumlah, tanggal peminjaman, tanggal rencana pengembalian, alasan penolakan (jika ada), dan catatan admin
4. WHEN peminjaman melewati tanggal rencana pengembalian dan belum dikembalikan, THE System SHALL menampilkan badge atau warning pada peminjaman tersebut

### Requirement 15: Laporan Inventaris

**User Story:** Sebagai Admin, saya ingin dapat melihat dan mencetak laporan komprehensif tentang stok barang, kategori, dan status inventaris, sehingga saya dapat membuat keputusan manajemen yang tepat.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman laporan inventaris, THE System SHALL menampilkan ringkasan total barang, total stok, barang per kategori, dan barang per lokasi
2. WHEN Admin menggunakan filter tanggal, THE System SHALL menampilkan laporan inventaris pada periode tanggal tertentu termasuk perubahan stok yang terjadi
3. WHEN Admin mengklik tombol Cetak atau Export, THE System SHALL menghasilkan dokumen laporan dalam format PDF atau Excel yang siap dicetak atau dibagikan
4. THE laporan harus mencakup informasi: nama barang, kode barang, kategori, lokasi, stok awal, stok masuk, stok terpinjam, stok tersedia, dan kondisi barang

### Requirement 16: Laporan Peminjaman

**User Story:** Sebagai Admin, saya ingin melihat laporan detail tentang semua aktivitas peminjaman dan pengembalian, sehingga saya dapat mengevaluasi penggunaan barang dan mengidentifikasi barang yang paling sering dipinjam.

#### Acceptance Criteria

1. WHEN Admin mengakses halaman laporan peminjaman, THE System SHALL menampilkan statistik total peminjaman, peminjaman disetujui, peminjaman ditolak, dan tingkat pengembalian tepat waktu
2. WHEN Admin memfilter berdasarkan periode tanggal, barang, atau staff tertentu, THE System SHALL menampilkan laporan peminjaman yang sesuai dengan filter
3. WHEN Admin mengklik tombol Cetak atau Export, THE System SHALL menghasilkan laporan dalam format PDF atau Excel dengan detail setiap transaksi peminjaman dan pengembalian
4. THE laporan harus mencakup: nama staff, barang yang dipinjam, jumlah, tanggal peminjaman, tanggal pengembalian rencana, tanggal pengembalian aktual, durasi peminjaman, dan status akhir

### Requirement 17: Keamanan Password

**User Story:** Sebagai pengguna sistem, saya ingin password saya aman dan tidak dapat dengan mudah ditebak atau diretas, sehingga akun saya terlindungi.

#### Acceptance Criteria

1. THE System SHALL mengenkripsi password menggunakan algoritma bcrypt atau hashing yang setara dengan minimal 10 rounds
2. THE System SHALL mewajibkan password memiliki minimal 8 karakter dan kombinasi huruf besar, huruf kecil, angka, dan simbol
3. WHEN pengguna mencoba login dengan password salah sebanyak 5 kali dalam 15 menit, THE System SHALL mengunci akun sementara selama 30 menit
4. THE System SHALL menyimpan password dalam database tanpa menyimpan password dalam log atau file temporary

### Requirement 18: Keamanan Akses HTTP

**User Story:** Sebagai pengguna sistem, saya ingin data saya dienkripsi saat dikirim melalui jaringan, sehingga data saya tidak dapat dicuri atau diubah.

#### Acceptance Criteria

1. THE System SHALL menggunakan HTTPS untuk semua komunikasi antara client dan server dengan minimal TLS 1.2
2. THE System SHALL menggunakan secure cookies dengan flag HttpOnly dan Secure untuk menyimpan session token
3. WHEN pengguna mengakses halaman melalui HTTP, THE System SHALL secara otomatis redirect ke HTTPS

### Requirement 19: Audit Logging

**User Story:** Sebagai Admin, saya ingin setiap aktivitas penting dalam sistem tercatat, sehingga saya dapat melacak siapa melakukan apa dan kapan untuk keperluan audit dan investigasi.

#### Acceptance Criteria

1. THE System SHALL mencatat log untuk setiap aktivitas berikut: login, logout, perubahan data barang, perubahan stok, persetujuan/penolakan peminjaman, verifikasi pengembalian, perubahan akun, dan perubahan role
2. THE System SHALL menyimpan log dengan detail: tanggal dan waktu, pengguna yang melakukan aksi, jenis aksi, data sebelum perubahan (jika ada), data sesudah perubahan, dan IP address jika memungkinkan
3. THE System SHALL menyimpan log secara permanen dan tidak dapat dihapus atau dimodifikasi oleh pengguna biasa (hanya super admin atau sistem yang dapat menghapus log yang sudah lama)
4. WHEN Admin mengakses halaman audit log, THE System SHALL menampilkan daftar lengkap semua aktivitas yang tercatat dengan fitur pencarian dan filter tanggal

### Requirement 20: Validasi Input

**User Story:** Sebagai pengembang sistem, saya ingin input dari pengguna divalidasi dengan ketat, sehingga sistem tidak mengalami error atau serangan injection.

#### Acceptance Criteria

1. THE System SHALL memvalidasi semua input dari form di sisi client dan server
2. THE System SHALL menolak input yang mengandung karakter khusus berbahaya atau pattern yang mencurigakan (SQL injection, XSS, dll)
3. THE System SHALL membatasi panjang maksimal input sesuai dengan tipe data (nama max 100 karakter, email max 255 karakter, dll)
4. THE System SHALL menampilkan pesan error yang jelas dan user-friendly ketika input tidak valid tanpa membocorkan detail teknis

### Requirement 21: Performa Sistem - Kecepatan Load

**User Story:** Sebagai pengguna, saya ingin halaman sistem dimuat dengan cepat, sehingga saya dapat bekerja dengan efisien tanpa delay.

#### Acceptance Criteria

1. WHEN pengguna mengakses halaman utama atau dashboard, THE System SHALL menampilkan halaman dalam waktu kurang dari 2 detik
2. WHEN pengguna melakukan pencarian atau filter pada daftar barang dengan data lebih dari 1000 item, THE System SHALL menampilkan hasil dalam waktu kurang dari 3 detik
3. WHEN Admin mengakses halaman laporan dengan data rentang bulan penuh, THE System SHALL menampilkan laporan dalam waktu kurang dari 5 detik
4. THE System SHALL mengimplementasikan pagination pada semua halaman yang menampilkan daftar data (minimal 20-50 item per halaman)

### Requirement 22: Performa Sistem - Kapasitas Data

**User Story:** Sebagai organisasi besar, saya ingin sistem dapat menangani jumlah data yang besar tanpa mengalami penurunan performa, sehingga sistem scalable seiring pertumbuhan organisasi.

#### Acceptance Criteria

1. THE System SHALL dapat menangani minimal 10,000 barang dalam database tanpa penurunan performa signifikan
2. THE System SHALL dapat menangani minimal 100,000 record transaksi peminjaman tanpa penurunan performa
3. THE System SHALL dapat melayani minimal 50 pengguna aktif secara bersamaan tanpa timeout atau error
4. WHEN database mencapai kapasitas maksimal, THE System SHALL memberikan notifikasi ke Admin bahwa perlu dilakukan maintenance atau backup

### Requirement 23: Backup dan Recovery Data

**User Story:** Sebagai Admin sistem, saya ingin data sistem di-backup secara teratur, sehingga jika terjadi disaster atau kehilangan data, sistem dapat di-recover.

#### Acceptance Criteria

1. THE System SHALL melakukan backup database secara otomatis setiap hari pada waktu yang ditentukan (minimal 1x sehari)
2. THE System SHALL menyimpan minimal 7 backup terakhir dan dapat di-restore kapan saja jika diperlukan
3. WHEN Admin mengakses halaman backup management, THE System SHALL menampilkan daftar backup yang tersedia dengan tanggal, ukuran, dan status integritas
4. THE System SHALL mengenkripsi file backup dan menyimpannya di lokasi yang terpisah dari server utama

### Requirement 24: Ketersediaan Sistem

**User Story:** Sebagai pengguna, saya ingin sistem selalu tersedia dan dapat diakses kapan saja saya membutuhkannya, sehingga pekerjaan saya tidak terganggu.

#### Acceptance Criteria

1. THE System SHALL memiliki uptime target minimal 99% per bulan (maksimal 7 jam downtime per bulan)
2. WHEN terjadi maintenance atau update sistem, THE System SHALL menampilkan pesan notifikasi terlebih dahulu kepada pengguna dan melakukan maintenance di luar jam kerja utama
3. THE System SHALL memiliki error handling yang baik sehingga jika terjadi error pada satu modul, modul lain tetap berfungsi normal

### Requirement 25: Kompatibilitas Browser

**User Story:** Sebagai pengguna, saya ingin dapat mengakses sistem dari berbagai browser dan device, sehingga saya fleksibel dalam bekerja.

#### Acceptance Criteria

1. THE System SHALL kompatibel dengan browser modern: Chrome versi terbaru, Firefox versi terbaru, Safari versi terbaru, dan Edge versi terbaru
2. THE System SHALL responsive dan dapat diakses dari perangkat dengan ukuran layar berbeda (desktop, tablet, mobile dengan minimal resolusi 320px)
3. WHEN pengguna mengakses dari mobile atau tablet, THE System SHALL menampilkan tampilan yang dioptimalkan dengan navigasi yang mudah digunakan

### Requirement 26: Dokumentasi Sistem

**User Story:** Sebagai pengguna baru atau administrator, saya ingin ada dokumentasi yang jelas tentang cara menggunakan sistem, sehingga saya dapat belajar dengan cepat tanpa kesulitan.

#### Acceptance Criteria

1. THE System SHALL menyediakan dokumentasi user manual dalam bahasa Indonesia yang mudah dipahami
2. THE System SHALL menyediakan tutorial video atau panduan langkah demi langkah untuk setiap fitur utama
3. WHEN pengguna mengklik tombol Help pada halaman tertentu, THE System SHALL menampilkan bantuan kontekstual yang relevan dengan halaman tersebut

### Requirement 27: Enforcement Role-Based Access Control

**User Story:** Sebagai administrator keamanan, saya ingin role-based access control diterapkan ketat di setiap level aplikasi, sehingga hanya pengguna dengan role yang tepat dapat mengakses data dan fitur tertentu.

#### Acceptance Criteria

1. WHEN pengguna dengan role Staff mencoba mengakses URL halaman admin (mis: /admin/barang, /admin/laporan, /admin/users), THE System SHALL menolak akses dengan status 403 Forbidden atau mengarahkan ke dashboard Staff
2. WHEN pengguna tanpa autentikasi mencoba mengakses halaman manapun selain login dan register, THE System SHALL mengarahkan ke halaman login
3. WHEN Admin mengakses halaman admin, THE System SHALL memverifikasi role = "Admin" dan memberikan akses penuh
4. WHEN Staff mengakses halaman staff, THE System SHALL memverifikasi role = "Staff" dan memberikan akses terbatas sesuai fitur Staff yang diizinkan

### Requirement 28: URL Direct Access Prevention

**User Story:** Sebagai pengguna Staff, saya ingin sistem mencegah saya mengakses halaman admin meskipun saya mengetahui URL-nya, sehingga data admin tetap aman.

#### Acceptance Criteria

1. THE System SHALL mengimplementasikan middleware authentication dan authorization di setiap route yang memerlukan akses khusus
2. WHEN Staff mengetik URL admin di address bar browser (mis: http://localhost/admin), THE System SHALL mendeteksi role Staff dan merespons dengan error 403 atau redirect tanpa menampilkan halaman admin
3. WHEN Admin mengetik URL admin, THE System SHALL menampilkan halaman admin yang sesuai
4. THE System SHALL mencatat setiap upaya akses tidak sah ke halaman admin dalam audit log untuk keperluan monitoring keamanan

### Requirement 29: Session Management

**User Story:** Sebagai pengguna, saya ingin session saya dikelola dengan aman sehingga akun saya tidak dapat diakses oleh orang lain.

#### Acceptance Criteria

1. WHEN pengguna berhasil login, THE System SHALL membuat session dengan token unik yang random dan tidak dapat ditebak
2. THE System SHALL mengatur session timeout setelah 30 menit inaktivitas, memaksa pengguna untuk login kembali
3. WHEN pengguna logout, THE System SHALL menghapus session token dan cookie yang terkait
4. THE System SHALL mencegah session hijacking dengan menggunakan secure cookies dan CSRF token validation

### Requirement 30: Permission Model

**User Story:** Sebagai administrator sistem, saya ingin model permission yang jelas untuk setiap role, sehingga akses pengguna dapat diprediksi dan dikelola dengan konsisten.

#### Acceptance Criteria

1. THE System SHALL mendefinisikan permission matrix yang jelas untuk Admin dan Staff dengan spesifikasi:
   - **Admin**: full access ke semua fitur (create, read, update, delete untuk barang, kategori, lokasi, users; approve/reject loans; verify returns; manage roles; view reports)
   - **Staff**: limited access (read barang dan kategori; submit loan requests; view own loan history; submit returns)
2. WHEN permission untuk suatu fitur berubah, THE System SHALL menerapkan perubahan secara otomatis ke semua pengguna dengan role terkait
3. THE System SHALL mencatat setiap akses ke fitur dalam log sehingga dapat diaudit siapa mengakses apa dan kapan
4. IF pengguna mencoba mengakses fitur di luar permission mereka, THE System SHALL menolak dan mencatat upaya akses tidak sah dalam security log
