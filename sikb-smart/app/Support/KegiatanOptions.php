<?php

namespace App\Support;

final class KegiatanOptions
{
    public const SECTIONS = [
        'Penyediaan Data Keluarga dan Pelayanan Dokumen Kependudukan',
        'Perubahan Perilaku Keluarga',
        'Peningkatan Cakupan Layanan dan Rujukan pada Keluarga',
        'Penataan Lingkungan Keluarga dan Masyarakat',
        'Kesekretariatan',
    ];

    public const VILLAGES = [
        'Sungai Miai',
        'Kuin Utara',
        'Alalak Selatan',
        'Pekapuran Raya',
        'Basirih',
        'Teluk Dalam',
    ];

    public const CATEGORIES = [
        'Balita',
        'Remaja',
        'Lansia',
        'PUS',
        'Keluarga',
        'Masyarakat umum',
    ];

    public const REQUIRED_IMPORT_HEADERS = [
        'nama_pengirim',
        'jabatan_pengirim',
        'nama_penyuluh_pembina',
        'kelurahan',
        'judul_kegiatan',
        'tanggal_kegiatan',
        'lokasi_kegiatan',
        'deskripsi',
        'seksi_kegiatan',
        'program_kegiatan',
        'kategori_peserta',
        'opd_mitra',
    ];

    public const OPTIONAL_IMPORT_HEADERS = [
        'timestamp',
        'dokumentasi_1',
        'dokumentasi_2',
    ];

    public static function normalizeVillage(string $name): ?string
    {
        $name = mb_strtolower(trim($name));

        foreach (self::VILLAGES as $village) {
            if (mb_strtolower($village) === $name) {
                return $village;
            }
        }

        return null;
    }

    public static function normalizeSection(string $name): ?string
    {
        $name = mb_strtolower(trim($name));

        foreach (self::SECTIONS as $section) {
            if (mb_strtolower($section) === $name) {
                return $section;
            }
        }

        return null;
    }
}