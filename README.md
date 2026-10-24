# Basco — Bangkalan Specialty Coffee

Website statis (HTML, CSS, JavaScript). Buka `index.html` langsung atau pakai ekstensi Live Server di VS Code.

## Yang sering diubah
- **Harga, nama, deskripsi menu**: `js/data.js` (satu objek per item: `n` nama, `p` harga, `d` deskripsi, `i` nama file foto di `img/m-*.webp`)
- **Nomor WhatsApp, alamat, jam buka, email, tautan sosial**: objek `INFO` di bagian atas `js/site.js`
- **Warna dan font**: variabel `:root` di `css/style.css`
- **Foto galeri**: tambah file ke `img/` lalu daftarkan di `js/data.js` bagian `gallery`

## Struktur
index.html · menu.html · galeri.html · kontak.html
css/style.css · js/site.js · js/data.js · img/ (semua WebP, total sekitar 1 MB)
