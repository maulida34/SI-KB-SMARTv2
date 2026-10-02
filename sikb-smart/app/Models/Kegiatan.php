<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kegiatan extends Model
{
    protected $fillable = [
        'timestamp',
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
        'dokumentasi_1',
        'dokumentasi_2',
        'upload_batch_id',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
            'tanggal_kegiatan' => 'date',
        ];
    }

    public function uploadBatch(): BelongsTo
    {
        return $this->belongsTo(UploadBatch::class);
    }
}