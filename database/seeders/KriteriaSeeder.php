<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KriteriaModel; // Menggunakan model sesuai kode Anda
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {

        $kriterias = [
            [
                'id' => (string) Str::uuid(),
                'nama_kriteria' => 'Urgensi Kegiatan',
                'tipe' => 'benefit',
                'bobot' => 25.00, // Terkait jadwal Hari Raya/Piodalan
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_kriteria' => 'Estimasi Biaya',
                'tipe' => 'cost',
                'bobot' => 20.00, // Semakin mahal biaya, skor prioritas mengecil (efisiensi)
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_kriteria' => 'Jumlah Peserta',
                'tipe' => 'benefit',
                'bobot' => 20.00, // Mengukur cakupan umat yang terlibat
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_kriteria' => 'Nilai Adat',
                'tipe' => 'benefit',
                'bobot' => 20.00, // Seberapa penting kegiatan terhadap tradisi/budaya
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_kriteria' => 'Ketersediaan Dana',
                'tipe' => 'benefit',
                'bobot' => 15.00, // Kelayakan anggaran yang sudah tersedia di kas
            ],
        ];

        foreach ($kriterias as $kriteria) {
            KriteriaModel::create($kriteria);
        }
    }
}
