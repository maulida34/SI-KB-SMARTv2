<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use App\Models\UploadBatch;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Support\KegiatanOptions;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::firstOrCreate(
            ['email' => 'dalduk@sikbsmart.test'],
            [
                'name' => 'Petugas DALDUK',
                'password' => Hash::make('Dalduk#2026'),
            ],
        );

        $batch = UploadBatch::updateOrCreate(
            ['nama_file' => 'Seeder awal (200 kegiatan)'],
            [
                'diupload_oleh' => $petugas->id,
                'diupload_pada' => now(),
                'jumlah_baris' => 200,
            ],
        );

        $batch->kegiatan()->delete();

        mt_srand(20261002);
        $namaPetugas = ['Siti Rahmawati', 'Ahmad Fauzi', 'Nur Aisyah', 'Muhammad Rizky', 'Dewi Anggraini'];
        $namaPenyuluh = ['Rina Marlina', 'Budi Santoso', 'Fitri Handayani', 'M. Arif Hidayat'];
        $program = [
            'Penguatan ketahanan keluarga',
            'Pelayanan administrasi kependudukan',
            'Pencegahan stunting',
            'Pemberdayaan ekonomi keluarga',
            'Edukasi kesehatan reproduksi',
            'Pembinaan lingkungan sehat',
            'Pendampingan keluarga berisiko',
        ];
        $lokasi = ['Balai Kelurahan', 'Posyandu', 'Aula Kecamatan', 'Rumah DataKu', 'Puskesmas'];
        $mitra = [
            'Dinas Kesehatan, Puskesmas setempat',
            'Dinas Sosial, TP PKK',
            'Dinas Pendidikan, Bina Keluarga Balita',
            'Dinas Kependudukan dan Pencatatan Sipil',
            'Puskesmas setempat, kader Kampung KB',
            'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
            'Kecamatan, TP PKK, Karang Taruna',
        ];
        $titles = [
            'Kelas pengasuhan keluarga',
            'Edukasi pencegahan stunting',
            'Pelayanan dan pendampingan keluarga',
            'Penyuluhan kesehatan masyarakat',
            'Pembinaan kelompok kegiatan',
            'Sosialisasi administrasi kependudukan',
            'Kerja bakti lingkungan Kampung KB',
            'Pertemuan koordinasi lintas sektor',
            'Edukasi kesehatan remaja',
            'Posyandu keluarga berkualitas',
        ];
        $categories = KegiatanOptions::CATEGORIES;
        $dateStart = now()->startOfMonth()->subMonths(11);
        $dateSpan = (int) $dateStart->diffInDays(now());

        for ($index = 0; $index < 200; $index++) {
            $date = $dateStart->copy()->addDays((int) floor(($index * $dateSpan) / 199));
            $village = KegiatanOptions::VILLAGES[$index % count(KegiatanOptions::VILLAGES)];

            Kegiatan::create([
                'timestamp' => $date,
                'nama_pengirim' => $namaPetugas[array_rand($namaPetugas)],
                'jabatan_pengirim' => 'Petugas DALDUK',
                'nama_penyuluh_pembina' => $namaPenyuluh[array_rand($namaPenyuluh)],
                'kelurahan' => $village,
                'judul_kegiatan' => $titles[$index % count($titles)].' '.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'tanggal_kegiatan' => $date->format('Y-m-d'),
                'lokasi_kegiatan' => $lokasi[array_rand($lokasi)].' '.$village,
                'deskripsi' => 'Kegiatan Kampung KB untuk mendukung keluarga berkualitas di '.$village.'.',
                'seksi_kegiatan' => KegiatanOptions::SECTIONS[$index % count(KegiatanOptions::SECTIONS)],
                'program_kegiatan' => $program[array_rand($program)],
                'kategori_peserta' => $categories[array_rand($categories)],
                'opd_mitra' => $mitra[array_rand($mitra)],
                'dokumentasi_1' => null,
                'dokumentasi_2' => null,
                'upload_batch_id' => $batch->id,
            ]);
        }
    }
}
