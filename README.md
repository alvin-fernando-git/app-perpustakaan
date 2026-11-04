# Olib - Perpustakaan Digital Kampus

## About

Nama aplikasinya adalah Olib singkatan dari Online Library, yang bertujuan sebagai perpustakaan digital yang berisi buku-buku ilmiah, novel, dan lain-lain.

Dibangun dengan Laravel 12 sebagai proyek mata kuliah. Fitur utama:

- Login/logout dengan dua role (admin dan petugas), plus halaman profil dan ganti password
- CRUD buku, kategori (khusus admin), anggota (dengan pencarian nama), dan peminjaman
- Pengembalian buku dan badge status peminjaman (dipinjam / dikembalikan / terlambat)
- REST API (`/api/...`) yang dikonsumsi oleh halaman Dashboard dan Laporan Peminjaman lewat Laravel HTTP Client
- Seeder dan Factory untuk data contoh

Dokumentasi endpoint API ada di [API.md](./API.md), dan skenario demo ada di [DEMO.md](./DEMO.md).

## Cara menjalankan aplikasinya secara lokal

### 1. Clone dan install dependency

```bash
git clone <url-repository-ini> app-perpustakaan
cd app-perpustakaan
git checkout dev        # atau main
composer install
```

### 2. Setup `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Lalu buka `.env` dan atur koneksi database (default: PostgreSQL):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_perpustakaan
DB_USERNAME=postgres
DB_PASSWORD=
```

Buat dulu database kosong dengan nama yang sama seperti `DB_DATABASE` (misalnya `db_perpustakaan`).

Pastikan juga baris berikut ada di `.env`. Nilai ini dipakai Dashboard dan Laporan untuk memanggil API internal (server kedua):

```env
INTERNAL_API_URL=http://127.0.0.1:8011
```

### 3. Migrate dan seed database

```bash
php artisan migrate --seed
```

Perintah ini membuat semua tabel dan mengisi data contoh: 3 akun login, 5 kategori, 20 buku, 15 anggota, dan 10 transaksi peminjaman. Untuk mengulang dari nol (semua data lama dihapus), pakai:

```bash
php artisan migrate:fresh --seed
```

### 4. Jalankan DUA server

Dashboard dan Laporan Peminjaman memanggil API lewat `Http::get()` ke server lain. Karena `php artisan serve` hanya memproses satu request dalam satu waktu, API internal harus berjalan di server kedua. Buka **dua terminal** di folder project:

```bash
# Terminal 1 - server utama (browser)
php artisan serve --port=8000

# Terminal 2 - server API internal (INTERNAL_API_URL)
php artisan serve --port=8011
```

Buka `http://127.0.0.1:8000` di browser. Kalau server kedua (port 8011) tidak jalan, halaman Dashboard menampilkan angka 0 dan halaman Laporan tampil kosong.

## Akun untuk login

| Role | Email | Password |
|---|---|---|
| Admin | admin@pens.ac.id | password |
| Petugas | petugas1@pens.ac.id | password |
| Petugas | petugas2@pens.ac.id | password |

Halaman Kategori dan `/admin/info` hanya bisa diakses oleh admin.

## Perbedaan Model, View, dan Controller

1. Model adalah bagian database dari aplikasinya
2. View adalah bagian tampilan dari aplikasinya
3. Controller adalah bagian yang menghubungkan antara Model dan View dengan instruksi pengguna