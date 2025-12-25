<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_pengaju');
            $table->string('no_hp');
            $table->string('nama_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->decimal('estimasi_biaya', 15, 2);
            $table->string('file_proposal')->nullable();
            $table->enum('status_kegiatan', ['menunggu', 'diproses', 'ditolak', 'ditunda', 'diadakan'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
