<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan; // Pastikan model Kegiatan sudah ada
use App\Models\KegiatanModel;
use Illuminate\Support\Str;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {

        $dataKegiatan = [
            [
                'id' => (string) Str::uuid(),
                'nama_pengaju' => 'I Wayan Sudira',
                'no_hp' => '081234567890',
                'nama_kegiatan' => 'Upacara Piodalan Padmasana',
                'tanggal_kegiatan' => '2024-05-20',
                'estimasi_biaya' => 15000000.00,
                'file_proposal' => 'proposal_piodalan.pdf',
                'status_kegiatan' => 'diproses', // Default
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_pengaju' => 'I Made Karta',
                'no_hp' => '081987654321',
                'nama_kegiatan' => 'Piodalan Purnama Kelima',
                'tanggal_kegiatan' => '2024-06-10',
                'estimasi_biaya' => 7500000.00,
                'file_proposal' => 'proposal_bale_kulkul.pdf',
                'status_kegiatan' => 'diproses', // Default
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_pengaju' => 'Ni Nyoman Rai',
                'no_hp' => '087766554433',
                'nama_kegiatan' => 'Pertandingan Bola Voli',
                'tanggal_kegiatan' => '2024-07-01',
                'estimasi_biaya' => 2000000.00,
                'file_proposal' => null,
                'status_kegiatan' => 'diproses', // Default
            ],
            [
                'id' => (string) Str::uuid(),
                'nama_pengaju' => 'I Ketut merta',
                'no_hp' => '081122334455',
                'nama_kegiatan' => 'Kegiatan Rahinan Galungan dan Kuningan',
                'tanggal_kegiatan' => '2024-08-15',
                'estimasi_biaya' => 12000000.00,
                'file_proposal' => 'proposal_seragam.pdf',
                'status_kegiatan' => 'diproses', // Default
            ],
        ];

        foreach ($dataKegiatan as $kegiatan) {
            KegiatanModel::create($kegiatan);
        }
    }
}
