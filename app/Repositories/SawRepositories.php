<?php

namespace App\Repositories;

use App\Interfaces\SawInterfaces;
use App\Models\HasilSawModel;
use App\Models\KegiatanModel;
use App\Models\KriteriaModel;
use App\Models\PenilaianModel;
use App\Traits\HttpResponseTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SawRepositories implements SawInterfaces
{
    use HttpResponseTraits;
    protected $nilaiModel;
    protected $kriteriaModel;
    protected $kegiatanModel;
    protected $hasilSaw;
    public function __construct(HasilSawModel $hasilSaw, PenilaianModel $nilaiModel, KegiatanModel $kegiatanModel, KriteriaModel $kriteriaModel)
    {
        $this->nilaiModel = $nilaiModel;
        $this->kriteriaModel = $kriteriaModel;
        $this->kegiatanModel = $kegiatanModel;
        $this->hasilSaw = $hasilSaw;
    }
    public function getAll()
    {
        $data = $this->nilaiModel::all();
        return $this->success($data);
    }
    public function batchStore(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $kegiatanId => $kriterias) {
                foreach ($kriterias as $kriteriaId => $nilai) {
                    $this->nilaiModel::updateOrCreate(
                        [
                            'id_kegiatan' => $kegiatanId,
                            'id_kriteria' => $kriteriaId,
                        ],
                        [
                            'nilai' => $nilai,
                        ]
                    );
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function calculateSaw()
    {
        // 1. Ambil data dasar
        $kriterias = $this->kriteriaModel::all();
        $kegiatans = $this->kegiatanModel::where('status_kegiatan', 'diproses')->get();
        $penilaians = $this->nilaiModel::all();

        if ($penilaians->isEmpty()) return [];

        // 2. Cari Nilai Max dan Min untuk setiap kriteria (Penting untuk Normalisasi)
        $minMax = [];
        foreach ($kriterias as $kri) {
            $values = $penilaians->where('id_kriteria', $kri->id)->pluck('nilai')->toArray();
            if (count($values) > 0) {
                $minMax[$kri->id] = [
                    'max' => max($values),
                    'min' => min($values)
                ];
            }
        }

        // 3. Proses Perhitungan
        $ranking = [];
        foreach ($kegiatans as $keg) {
            $skorAkhir = 0;
            $detailNormalisasi = [];

            foreach ($kriterias as $kri) {
                $nilaiInput = $penilaians->where('id_kegiatan', $keg->id)
                    ->where('id_kriteria', $kri->id)
                    ->first()->nilai ?? 0;

                // Rumus Normalisasi
                $r_ij = 0;
                if (isset($minMax[$kri->id])) {
                    if ($kri->tipe == 'benefit') {
                        // Benefit: Nilai / Max
                        $r_ij = $minMax[$kri->id]['max'] > 0 ? ($nilaiInput / $minMax[$kri->id]['max']) : 0;
                    } else {
                        // Cost: Min / Nilai
                        $r_ij = $nilaiInput > 0 ? ($minMax[$kri->id]['min'] / $nilaiInput) : 0;
                    }
                }

                // Kalikan dengan Bobot (V_i)
                $bobotDesimal = $kri->bobot / 100;
                $skorKriteria = $r_ij * $bobotDesimal;
                $skorAkhir += $skorKriteria;

                $detailNormalisasi[] = [
                    'nama_kriteria' => $kri->nama_kriteria,
                    'nilai_asli' => $nilaiInput,
                    'normalisasi' => round($r_ij, 4),
                    'skor_bobot' => round($skorKriteria, 4)
                ];
            }

            $ranking[] = [
                'id_kegiatan' => $keg->id,
                'nama_kegiatan' => $keg->nama_kegiatan,
                'details' => $detailNormalisasi,
                'skor_total' => round($skorAkhir, 4)
            ];
        }

        usort($ranking, fn($a, $b) => $b['skor_total'] <=> $a['skor_total']);

        return $ranking;
    }
    public function saveBatchRanking($rankingData)
    {
        return DB::transaction(function () use ($rankingData) {
            $idsKegiatan = collect($rankingData)->pluck('id_kegiatan')->toArray();

            foreach ($rankingData as $item) {
                $this->hasilSaw::updateOrCreate(
                    ['id_kegiatan' => $item['id_kegiatan']],
                    [
                        'id' => Str::uuid(),
                        'nilai_preferensi' => $item['skor_total'],
                        'peringkat' => $item['ranking'],
                        'tanggal_hitung' => now(),
                    ]
                );
            }

            $this->nilaiModel::whereIn('id_kegiatan', $idsKegiatan)->delete();

            return true;
        });
    }
}
