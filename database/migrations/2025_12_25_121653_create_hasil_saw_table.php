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
        Schema::create('hasil_saw', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_kegiatan')->constrained('kegiatan')->onDelete('cascade');
            $table->decimal('nilai_preferensi', 8, 4);
            $table->integer('peringkat')->nullable();
            $table->date('tanggal_hitung');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_saw');
    }
};
