# SI-KB SMART

Sistem Informasi Analitik Kampung KB untuk DPPKB. Beranda publik hanya menampilkan ringkasan agregat. Petugas DALDUK masuk untuk mengunggah data Excel/CSV; tidak tersedia form tambah atau edit kegiatan per baris.

## Stack

- PHP 8.4
- Laravel 13
- Blade dan Laravel Breeze
- PostgreSQL bawaan Replit (Laravel dapat memakai MySQL dengan konfigurasi lokal)
- Tailwind CSS melalui Laravel Vite plugin
- Chart.js untuk grafik
- maatwebsite/excel untuk impor dan template Excel

## Menjalankan di Replit

Environment Replit menyediakan `DATABASE_URL` dan rahasia `SESSION_SECRET`. Aplikasi memakai PostgreSQL saat `DATABASE_URL` tersedia.

```bash
cd sikb-smart
composer install
npm install
npm run build
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=5000
```

Jalankan workflow **SI-KB SMART (Laravel)** untuk membuka aplikasi. Untuk environment lokal, salin `.env.example` menjadi `.env`, atur koneksi database, lalu jalankan `php artisan key:generate`. Jangan commit `.env`.

## Akun awal petugas

- Email: `dalduk@sikbsmart.test`
- Kata sandi sementara: `Dalduk#2026`

Ganti kata sandi sementara sebelum aplikasi dipakai dengan data nyata.

## Format unggahan

Unduh **template-kegiatan-si-kb-smart.xlsx** dari halaman `/upload`. Baris pertama harus berisi nama kolom berikut dalam format snake_case.

Kolom wajib:

`nama_pengirim`, `jabatan_pengirim`, `nama_penyuluh_pembina`, `kelurahan`, `judul_kegiatan`, `tanggal_kegiatan`, `lokasi_kegiatan`, `deskripsi`, `seksi_kegiatan`, `program_kegiatan`, `kategori_peserta`, `opd_mitra`

Kolom opsional:

`timestamp`, `dokumentasi_1`, `dokumentasi_2`

Format yang diterima: `.xlsx` atau `.csv`, maksimal 5 MB. Setiap kolom wajib harus berisi nilai. `tanggal_kegiatan` menerima tanggal Excel atau tanggal yang dapat dibaca PHP. Dokumentasi harus berupa tautan yang valid. `kelurahan` harus salah satu dari Sungai Miai, Kuin Utara, Alalak Selatan, Pekapuran Raya, Basirih, atau Teluk Dalam; kapitalisasi dan spasi awal/akhir dinormalisasi. Kategori peserta berupa teks, contohnya Balita, Remaja, Lansia, atau PUS. Nilai `opd_mitra` dapat berisi beberapa nama instansi yang dipisahkan koma.

Unggahan diperiksa sebelum disimpan: judul kolom, kolom wajib, seksi resmi, tanggal, tautan dokumentasi, serta duplikat kelurahan + judul kegiatan + tanggal. Data yang disimpan menjadi satu batch dan dapat dihapus seluruhnya dari halaman riwayat unggahan.

## Data analitik publik

Seeder membuat 200 kegiatan contoh yang tersebar pada enam kelurahan dan 12 bulan terakhir. Beranda `/` menyediakan filter 6/12 bulan, grafik tren bulanan, kegiatan per seksi (dapat diklik untuk memfilter), kategori peserta, dan ringkasan per kelurahan. Halaman detail kelurahan hanya menampilkan jumlah kegiatan, mitra unik, seksi dominan, serta rincian agregat per seksi—bukan nama pengirim atau data pribadi.

Seksi resmi yang digunakan:

1. Penyediaan Data Keluarga dan Pelayanan Dokumen Kependudukan
2. Perubahan Perilaku Keluarga
3. Peningkatan Cakupan Layanan dan Rujukan pada Keluarga
4. Penataan Lingkungan Keluarga dan Masyarakat
5. Kesekretariatan