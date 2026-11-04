# Dokumentasi REST API - Olib (Perpustakaan Digital Kampus)

<!-- CATATAN PENGGUNA: contoh response di bawah disusun dari struktur Resource/Controller di kode. Sebelum dikumpulkan, jalankan tiap endpoint di Postman pada database hasil `migrate:fresh --seed`, lalu ganti nilai contoh dengan hasil copy-paste dari Postman (tugas Pertemuan 9 meminta response sungguhan). -->

**Base URL:** `http://127.0.0.1:8011/api`

**Header yang disarankan untuk semua request:**

```
Accept: application/json
Content-Type: application/json
```

Header `Accept: application/json` wajib untuk request `POST`/`PUT`. Tanpa header ini, error validasi dijawab dengan redirect (302), bukan JSON `422`.

Endpoint list (`GET /books`, `/members`, `/loans`) memakai pagination 10 data per halaman. Pindah halaman dengan `?page=2`. Format response list:

```json
{
  "data": [ ... ],
  "links": { "first": "...?page=1", "last": "...?page=2", "prev": null, "next": "...?page=2" },
  "meta": { "current_page": 1, "from": 1, "last_page": 2, "per_page": 10, "to": 10, "total": 20, "path": "...", "links": [ ... ] }
}
```

Pada contoh di bawah, `data` dipersingkat menjadi 1 item dan `meta.links` tidak ditampilkan.

## Ringkasan Endpoint

| No | Method | URL | Body | Status Code |
|---|---|---|---|---|
| 1 | GET | `/api/books` | - | 200 |
| 2 | GET | `/api/books/{id}` | - | 200, 404 |
| 3 | POST | `/api/books` | JSON (lihat di bawah) | 201, 422 |
| 4 | PUT | `/api/books/{id}` | JSON (lihat di bawah) | 200, 404, 422 |
| 5 | DELETE | `/api/books/{id}` | - | 200, 404 |
| 6 | GET | `/api/members` | - | 200 |
| 7 | GET | `/api/members/{id}` | - | 200, 404 |
| 8 | GET | `/api/loans` | - | 200 |
| 9 | POST | `/api/loans` | JSON (lihat di bawah) | 201, 422 |
| 10 | PUT | `/api/loans/{id}/kembalikan` | - | 200, 404 |
| 11 | GET | `/api/stats` | - | 200 |

---

## Books

### 1. GET `/api/books`

Daftar buku (dengan kategori). Query opsional: `?judul=` (pencarian sebagian judul) dan `?category_id=` (kategori persis).

**Contoh:** `GET /api/books?judul=Laskar`

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": 2005,
      "isbn": "9789793062792",
      "stok": 5,
      "sampul": null,
      "category": {
        "id": 1,
        "nama_kategori": "Novel"
      },
      "created_at": "2026-10-28T03:12:45.000000Z",
      "updated_at": "2026-10-28T03:12:45.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8011/api/books?page=1",
    "last": "http://127.0.0.1:8011/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

### 2. GET `/api/books/{id}`

Detail satu buku.

**Response `200 OK`** (`GET /api/books/1`):

```json
{
  "data": {
    "id": 1,
    "judul": "Laskar Pelangi",
    "penulis": "Andrea Hirata",
    "penerbit": "Bentang Pustaka",
    "tahun_terbit": 2005,
    "isbn": "9789793062792",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Novel"
    },
    "created_at": "2026-10-28T03:12:45.000000Z",
    "updated_at": "2026-10-28T03:12:45.000000Z"
  }
}
```

**Response `404 Not Found`** (`GET /api/books/999`, id tidak ada):

```json
{
  "message": "No query results for model [App\\Models\\Book] 999"
}
```

### 3. POST `/api/books`

Tambah buku baru.

**Body:**

```json
{
  "judul": "Bumi Manusia",
  "penulis": "Pramoedya Ananta Toer",
  "penerbit": "Hasta Mitra",
  "tahun_terbit": 1980,
  "isbn": "9789799731234",
  "stok": 3,
  "category_id": 1
}
```

Aturan: `judul` wajib (maks 200), `penulis` wajib (maks 100), `penerbit` wajib (maks 100), `tahun_terbit` wajib integer 1900 sampai tahun sekarang, `isbn` opsional (maks 20), `stok` wajib integer >= 0, `category_id` wajib dan harus ada di tabel `categories`.

**Response `201 Created`:**

```json
{
  "data": {
    "id": 21,
    "judul": "Bumi Manusia",
    "penulis": "Pramoedya Ananta Toer",
    "penerbit": "Hasta Mitra",
    "tahun_terbit": 1980,
    "isbn": "9789799731234",
    "stok": 3,
    "sampul": null,
    "category": {
      "id": 1,
      "nama_kategori": "Novel"
    },
    "created_at": "2026-11-04T02:30:10.000000Z",
    "updated_at": "2026-11-04T02:30:10.000000Z"
  }
}
```

**Response `422 Unprocessable Content`** (body kosong `{}`):

```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": ["Judul buku wajib diisi."],
    "penulis": ["Nama penulis wajib diisi."],
    "penerbit": ["Nama penerbit wajib diisi."],
    "tahun_terbit": ["Tahun terbit wajib diisi."],
    "stok": ["Stok wajib diisi."],
    "category_id": ["Kategori wajib dipilih."]
  }
}
```

### 4. PUT `/api/books/{id}`

Ubah data buku. Body dan aturan validasinya sama dengan `POST /api/books` (semua field wajib kecuali `isbn`).

**Body:**

```json
{
  "judul": "Bumi Manusia (Edisi Revisi)",
  "penulis": "Pramoedya Ananta Toer",
  "penerbit": "Hasta Mitra",
  "tahun_terbit": 1980,
  "isbn": "9789799731234",
  "stok": 7,
  "category_id": 1
}
```

**Response `200 OK`:** struktur sama dengan `GET /api/books/{id}`, berisi data terbaru (`"stok": 7`).

**Response `404 Not Found`:** id tidak ditemukan, format sama seperti endpoint 2.

**Response `422 Unprocessable Content`** (misalnya `"stok": -1` dan `"category_id": 999`):

```json
{
  "message": "The stok field must be at least 0. (and 1 more error)",
  "errors": {
    "stok": ["The stok field must be at least 0."],
    "category_id": ["The selected category id is invalid."]
  }
}
```

Catatan: pada `PUT`, pesan error memakai teks bawaan Laravel (bahasa Inggris) karena `Api\BookController@update` memakai `$request->validate()` tanpa pesan kustom.

### 5. DELETE `/api/books/{id}`

Hapus satu buku.

**Response `200 OK`:**

```json
{
  "message": "Buku berhasil dihapus."
}
```

**Response `404 Not Found`:** id tidak ditemukan, format sama seperti endpoint 2.

---

## Members (read-only)

### 6. GET `/api/members`

Daftar anggota, 10 per halaman.

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "nama": "Rizky Pratama",
      "nim": "3125600101",
      "email": "rizky.pratama@student.pens.ac.id",
      "nomor_telepon": "081234567890",
      "alamat": "Jl. Keputih Tegal No. 12, Surabaya",
      "status": "aktif",
      "created_at": "2026-10-28T03:12:46.000000Z",
      "updated_at": "2026-10-28T03:12:46.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8011/api/members?page=1",
    "last": "http://127.0.0.1:8011/api/members?page=2",
    "prev": null,
    "next": "http://127.0.0.1:8011/api/members?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

### 7. GET `/api/members/{id}`

Detail satu anggota.

**Response `200 OK`** (`GET /api/members/1`):

```json
{
  "data": {
    "id": 1,
    "nama": "Rizky Pratama",
    "nim": "3125600101",
    "email": "rizky.pratama@student.pens.ac.id",
    "nomor_telepon": "081234567890",
    "alamat": "Jl. Keputih Tegal No. 12, Surabaya",
    "status": "aktif",
    "created_at": "2026-10-28T03:12:46.000000Z",
    "updated_at": "2026-10-28T03:12:46.000000Z"
  }
}
```

**Response `404 Not Found`** (`GET /api/members/999`):

```json
{
  "message": "No query results for model [App\\Models\\Member] 999"
}
```

---

## Loans

### 8. GET `/api/loans`

Daftar transaksi peminjaman beserta anggota, petugas, dan buku, 10 per halaman.

**Response `200 OK`:**

```json
{
  "data": [
    {
      "id": 1,
      "member": {
        "id": 4,
        "nama": "Dewi Anggraini"
      },
      "petugas": {
        "id": 2,
        "name": "Siti Rahmawati"
      },
      "tanggal_pinjam": "2026-10-20",
      "tanggal_kembali": "2026-10-27",
      "tanggal_dikembalikan": null,
      "status": "dipinjam",
      "books": [
        { "id": 3, "judul": "Negeri 5 Menara" },
        { "id": 9, "judul": "Pengantar Basis Data" }
      ]
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8011/api/loans?page=1",
    "last": "http://127.0.0.1:8011/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 10,
    "total": 10
  }
}
```

### 9. POST `/api/loans`

Buat transaksi peminjaman baru. Status awal otomatis `dipinjam`. Di API ini `user_id` (petugas) dikirim lewat body karena API tidak memakai sesi login.

**Body:**

```json
{
  "member_id": 1,
  "user_id": 2,
  "tanggal_pinjam": "2026-11-04",
  "tanggal_kembali": "2026-11-11",
  "book_ids": [1, 2]
}
```

Aturan: `member_id` dan `user_id` wajib dan harus ada di tabelnya, `tanggal_pinjam` wajib tanggal, `tanggal_kembali` wajib dan tidak boleh sebelum `tanggal_pinjam`, `book_ids` wajib array minimal 1 buku (tiap id harus ada di tabel `books`).

**Response `201 Created`:**

```json
{
  "data": {
    "id": 11,
    "member": {
      "id": 1,
      "nama": "Rizky Pratama"
    },
    "petugas": {
      "id": 2,
      "name": "Siti Rahmawati"
    },
    "tanggal_pinjam": "2026-11-04",
    "tanggal_kembali": "2026-11-11",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      { "id": 1, "judul": "Laskar Pelangi" },
      { "id": 2, "judul": "Bumi Manusia" }
    ]
  }
}
```

**Response `422 Unprocessable Content`** (body kosong `{}`):

```json
{
  "message": "Anggota wajib dipilih. (and 4 more errors)",
  "errors": {
    "member_id": ["Anggota wajib dipilih."],
    "user_id": ["Petugas (user_id) wajib diisi."],
    "tanggal_pinjam": ["Tanggal pinjam wajib diisi."],
    "tanggal_kembali": ["Tanggal kembali wajib diisi."],
    "book_ids": ["Pilih minimal satu buku."]
  }
}
```

### 10. PUT `/api/loans/{id}/kembalikan`

Tandai transaksi sebagai dikembalikan: `status` menjadi `dikembalikan` dan `tanggal_dikembalikan` diisi tanggal hari ini. Tidak membutuhkan body.

**Response `200 OK`** (`PUT /api/loans/11/kembalikan`):

```json
{
  "data": {
    "id": 11,
    "member": {
      "id": 1,
      "nama": "Rizky Pratama"
    },
    "petugas": {
      "id": 2,
      "name": "Siti Rahmawati"
    },
    "tanggal_pinjam": "2026-11-04",
    "tanggal_kembali": "2026-11-11",
    "tanggal_dikembalikan": "2026-11-04",
    "status": "dikembalikan",
    "books": [
      { "id": 1, "judul": "Laskar Pelangi" },
      { "id": 2, "judul": "Bumi Manusia" }
    ]
  }
}
```

**Response `404 Not Found`** (`PUT /api/loans/999/kembalikan`):

```json
{
  "message": "No query results for model [App\\Models\\Loan] 999"
}
```

---

## Stats

### 11. GET `/api/stats`

Ringkasan statistik yang dipakai kartu Dashboard.

**Response `200 OK`:**

```json
{
  "total_buku": 20,
  "total_anggota": 15,
  "peminjaman_aktif": 3
}
```