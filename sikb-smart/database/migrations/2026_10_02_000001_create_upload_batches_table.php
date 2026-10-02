<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nama_file');
            $table->foreignId('diupload_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamp('diupload_pada');
            $table->unsignedInteger('jumlah_baris');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_batches');
    }
};