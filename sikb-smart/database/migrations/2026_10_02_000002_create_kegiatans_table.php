<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->string('nama_pengirim');
            $table->string('jabatan_pengirim');
            $table->string('nama_penyuluh_pembina');
            $table->string('kelurahan', 120)->index();
            $table->string('judul_kegiatan', 180);
            $table->date('tanggal_kegiatan')->index();
            $table->string('lokasi_kegiatan');
            $table->text('deskripsi');
            $table->string('seksi_kegiatan')->index();
            $table->string('program_kegiatan');
            $table->string('kategori_peserta')->index();
            $table->text('opd_mitra');
            $table->text('dokumentasi_1')->nullable();
            $table->text('dokumentasi_2')->nullable();
            $table->foreignId('upload_batch_id')->constrained('upload_batches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['kelurahan', 'judul_kegiatan', 'tanggal_kegiatan'],
                'kegiatans_village_title_date_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};