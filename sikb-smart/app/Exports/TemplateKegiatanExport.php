<?php

namespace App\Exports;

use App\Support\KegiatanOptions;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateKegiatanExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return array_merge(
            KegiatanOptions::REQUIRED_IMPORT_HEADERS,
            KegiatanOptions::OPTIONAL_IMPORT_HEADERS,
        );
    }

    public function array(): array
    {
        return [[
            'Nama Petugas',
            'Petugas DALDUK',
            'Nama Penyuluh',
            'Sungai Miai',
            'Kelas Pengasuhan Keluarga',
            '2026-01-15',
            'Balai Kelurahan Sungai Miai',
            'Kegiatan edukasi keluarga dan pendampingan peserta.',
            KegiatanOptions::SECTIONS[1],
            'Penguatan ketahanan keluarga',
            'Balita',
            'Dinas Kesehatan, Puskesmas Sungai Miai',
            '2026-01-15 09:00:00',
            '',
            '',
        ]];
    }
}