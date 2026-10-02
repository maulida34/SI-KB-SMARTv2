# SI-KB SMART

Sistem Informasi Analitik Kampung KB untuk DPPKB. Aplikasi analitik publik dan impor data privat petugas DALDUK.

## Run & Operate

- `cd sikb-smart && php artisan serve --host=0.0.0.0 --port=5000` — jalankan aplikasi
- `cd sikb-smart && php artisan migrate --seed` — siapkan skema dan 200 kegiatan contoh
- `cd sikb-smart && npm run build` — kompilasi aset Laravel Vite
- Workflow utama: **SI-KB SMART (Laravel)**
- Database Replit menggunakan `DATABASE_URL`; rahasia `SESSION_SECRET` digunakan sebagai sumber kunci aplikasi jika `APP_KEY` tidak diatur.

## Stack

- PHP 8.4 dan Laravel 13
- Blade, Laravel Breeze, Eloquent ORM
- PostgreSQL Replit
- Tailwind CSS dengan Laravel Vite plugin
- Chart.js dan maatwebsite/excel

## Ruang lingkup produk

- Beranda publik hanya menampilkan data agregat; jangan tampilkan nama pengirim atau data pribadi.
- Kegiatan hanya masuk melalui unggahan Excel/CSV petugas DALDUK; jangan menambahkan form CRUD kegiatan per baris.
- Riwayat unggahan menyediakan penghapusan satu batch beserta seluruh kegiatannya.
- Antarmuka menggunakan Bahasa Indonesia dan mendukung tema terang/gelap.

## Peta kode

- `sikb-smart/app/Http/Controllers/HomeController.php` — agregat beranda dan detail kelurahan.
- `sikb-smart/app/Http/Controllers/UploadController.php` — validasi, pratinjau, simpan, hapus batch, dan unduh template.
- `sikb-smart/app/Support/KegiatanOptions.php` — konstanta seksi resmi dan nama kelurahan.
- `sikb-smart/database/migrations/` dan `sikb-smart/database/seeders/` — skema serta data contoh.
- `sikb-smart/resources/views/` — antarmuka Blade.
- `README.md` — instruksi lokal, akun awal, dan format unggahan.

## Keputusan implementasi

- Aplikasi produk berada di direktori `sikb-smart`; server Laravel menjadi workflow utama. Paket Node di direktori Laravel hanya menjalankan Vite resmi untuk mengompilasi aset Blade.
- Dashboard publik mengambil hanya kolom analitik yang diperlukan dan menghitung ringkasan melalui model Eloquent.
- Unggahan divalidasi ulang saat pratinjau dan saat penyimpanan agar duplikat maupun data tidak valid tidak lolos setelah pratinjau.
