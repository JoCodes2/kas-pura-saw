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
        Schema::create('penilaian_kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_kegiatan')->constrained('kegiatan')->onDelete('cascade');
            $table->foreignUuid('id_kriteria')->constrained('kriteria')->onDelete('cascade');
            $table->decimal('nilai', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_kegiatan');
    }
};
