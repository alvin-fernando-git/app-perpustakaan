# Skenario Demo - Olib (Perpustakaan Digital Kampus)

Urutan demo untuk UAS (Pertemuan 11). Total durasi sekitar 15 menit.

## Persiapan sebelum demo

- [ ] Branch `main` sudah berisi hasil merge dari `dev` dan sudah di-push.
- [ ] `.env` sudah benar (database, `INTERNAL_API_URL=http://127.0.0.1:8011`).
- [ ] Reset data: `php artisan migrate:fresh --seed`.
- [ ] Jalankan **dua server** di dua terminal:
  - Terminal 1: `php artisan serve --port=8000` (browser)
  - Terminal 2: `php artisan serve --port=8011` (API internal)
- [ ] Browser terbuka di `http://127.0.0.1:8000`, Postman terbuka dengan base URL `http://127.0.0.1:8011/api` dan header `Accept: application/json`.
- [ ] Akun login siap (tidak perlu membuka `.env`/database saat demo):

| Role | Email | Password |
|---|---|---|
| Admin | admin@pens.ac.id | password |
| Petugas | petugas1@pens.ac.id | password |

Gunakan akun **admin** untuk seluruh demo karena halaman Kategori hanya bisa diakses admin.

## Langkah demo

### 1. Login
1. Buka `/login`, coba dulu password salah dan tunjukkan pesan error "Email atau password salah.".
2. Login dengan `admin@pens.ac.id` / `password`.
3. **Yang ditunjukkan:** redirect ke Dashboard dan pesan sukses; nama + role ("Bambang Sutrisno (Admin)") tampil di navbar.

### 2. Dashboard
1. Tunjukkan 3 kartu statistik: Total Buku, Total Anggota, Peminjaman Aktif.
2. **Jelaskan:** angka diambil lewat `Http::get()` ke `GET /api/stats` di server port 8011, bukan query langsung ke database. Ini sebabnya dibutuhkan dua server.

### 3. Tambah kategori
1. Buka `/categories`, klik tambah, isi misalnya nama "Ekonomi" dan deskripsi singkat.
2. **Yang ditunjukkan:** flash message sukses dan kategori baru muncul di tabel. Coba submit kosong untuk menunjukkan validasi.

### 4. Tambah buku
1. Buka `/books`, tambah buku baru dan pilih kategori "Ekonomi" dari dropdown.
2. **Yang ditunjukkan:** buku tersimpan dan kolom kategori menampilkan **nama** kategori, bukan `category_id`.

### 5. Tambah anggota
1. Buka `/members`, tambah anggota baru (isi nama, NIM, email, telepon, alamat, status).
2. Tambah lagi dengan NIM atau email yang sama dan tunjukkan validasi unik ("NIM ini sudah terdaftar.").
3. Tunjukkan juga pencarian nama anggota (kolom "Cari nama anggota...") dan pagination.

### 6. Buat peminjaman
1. Buka `/loans/create`, pilih anggota baru (langkah 5) dan buku baru (langkah 4), isi tanggal pinjam dan tanggal kembali.
2. **Yang ditunjukkan:** transaksi tersimpan, dan petugas terisi otomatis dari sesi login (tidak ada input petugas di form).

### 7. Daftar peminjaman aktif
1. Buka `/loans`.
2. **Yang ditunjukkan:** transaksi baru tampil dengan badge kuning **Dipinjam**, lengkap dengan nama anggota, nama petugas, dan judul buku (hasil eager loading, bukan ID mentah).

### 8. Proses pengembalian buku
1. Pada transaksi baru, klik tombol **Kembalikan**.
2. **Yang ditunjukkan:** flash message "Buku berhasil dikembalikan.", badge berubah menjadi hijau **Dikembalikan**, dan tombol Kembalikan hilang. Buka Detail untuk menunjukkan `Tanggal Dikembalikan` terisi tanggal hari ini.
3. Cadangan jika tombol web bermasalah: `PUT /api/loans/{id}/kembalikan` di Postman.

### 9. Laporan peminjaman
1. Buka `/loans/report`.
2. **Yang ditunjukkan:** seluruh transaksi tampil termasuk yang baru diproses.
3. **Jelaskan:** data diambil dari `GET /api/loans` lewat HTTP Client, bukan query Eloquent dari Controller web.

### 10. Demo API di Postman
1. `GET /api/books`: tunjukkan response terstruktur (`BookResource`), kategori ter-nested, dan blok `links`/`meta` untuk pagination.
2. `GET /api/stats`: tunjukkan `total_buku`, `total_anggota`, `peminjaman_aktif` sesuai data terkini (peminjaman aktif berkurang setelah langkah 8).
3. (Opsional) `POST /api/books` dengan body `{}` untuk menunjukkan `422` beserta pesan error validasi.

### 11. Fitur tambahan (jika waktu cukup)
- **Profil petugas** (`/profil`): tunjukkan nama, email, role, lalu ganti password (password lama salah menampilkan error; berhasil menampilkan pesan sukses).
- **Middleware admin:** logout, login sebagai `petugas1@pens.ac.id`, lalu buka `/categories` dan `/admin/info`. Keduanya ditolak dengan `403`.

## Jika terjadi masalah

| Gejala | Penyebab | Solusi |
|---|---|---|
| Dashboard menampilkan angka 0 / Laporan kosong | Server port 8011 tidak jalan | Jalankan `php artisan serve --port=8011` di terminal kedua |
| Data kacau akibat uji coba manual | Sisa data lama | `php artisan migrate:fresh --seed` lalu login ulang |
| Redirect terus ke `/login` | Sesi habis | Login ulang |