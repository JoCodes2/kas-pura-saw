<?php

namespace App\Interfaces;

interface SawInterfaces
{
    public function batchStore(array $data);
    public function getAll();
    public function calculateSaw();
    public function saveBatchRanking($rankingData);
    public function clearPenilaian();

    public function hasilSaw();
}
