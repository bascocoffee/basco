# REST API uji_absens — MySQLi (Procedural)

## Struktur Folder

```
api_siswa_mysqli/
├── config/
│   ├── database.php   # Koneksi MySQLi
│   └── helper.php     # Fungsi bantu: response, sanitize, dll
├── api/
│   ├── kelas.php      # Endpoint CRUD tabel kelas
│   └── siswa.php      # Endpoint CRUD tabel siswa
├── database.sql       # Script buat tabel & data contoh
└── README.md
```

---

## Cara Instalasi

1. Letakkan folder `api_siswa_mysqli/` di dalam `htdocs/` (XAMPP).
2. Import `database.sql`:
   - Via phpMyAdmin, atau
   - Via terminal: `mysql -u root -p < database.sql`
3. Sesuaikan kredensial di `config/database.php` jika perlu.

---

## Endpoint Kelas

Base URL: `http://localhost/api_siswa_mysqli/api/kelas.php`

| Method | URL           | Keterangan                          |
|--------|---------------|-------------------------------------|
| GET    | `kelas.php`   | Ambil semua kelas + jumlah siswa    |
| GET    | `kelas.php?id=1` | Ambil kelas by ID               |
| POST   | `kelas.php`   | Tambah kelas baru                   |
| PUT    | `kelas.php?id=1` | Update nama kelas               |
| DELETE | `kelas.php?id=1` | Hapus kelas (jika tidak ada siswa)|

### Contoh POST / PUT kelas
```json
{ "nama_kelas": "XII RPL 1" }
```

### Contoh Response GET semua kelas
```json
{
  "status": "success",
  "total": 3,
  "data": [
    { "id_kelas": 1, "nama_kelas": "X RPL 1",  "jumlah_siswa": "2" },
    { "id_kelas": 2, "nama_kelas": "X RPL 2",  "jumlah_siswa": "1" },
    { "id_kelas": 3, "nama_kelas": "XI RPL 1", "jumlah_siswa": "1" }
  ]
}
```

---

## Endpoint Siswa

Base URL: `http://localhost/api_siswa_mysqli/api/siswa.php`

| Method | URL            | Keterangan              |
|--------|----------------|-------------------------|
| GET    | `siswa.php`    | Ambil semua siswa       |
| GET    | `siswa.php?id=1` | Ambil siswa by ID     |
| POST   | `siswa.php`    | Tambah siswa baru       |
| PUT    | `siswa.php?id=1` | Update data siswa     |
| DELETE | `siswa.php?id=1` | Hapus siswa           |

### Contoh POST / PUT siswa
```json
{ "nama_siswa": "Rizky Maulana", "id_kelas": 2 }
```

### Contoh Response GET semua siswa
```json
{
  "status": "success",
  "total": 4,
  "data": [
    { "id_siswa": 1, "nama_siswa": "Budi Santoso", "id_kelas": 1, "nama_kelas": "X RPL 1" },
    { "id_siswa": 2, "nama_siswa": "Siti Rahayu",  "id_kelas": 1, "nama_kelas": "X RPL 1" }
  ]
}
```

---

## HTTP Status Code

| Code | Keterangan                        |
|------|-----------------------------------|
| 200  | OK                                |
| 201  | Created                           |
| 400  | Bad Request (input tidak valid)   |
| 404  | Data tidak ditemukan              |
| 405  | Method Not Allowed                |
| 409  | Conflict (duplikat / masih ada FK)|
| 500  | Internal Server Error             |
