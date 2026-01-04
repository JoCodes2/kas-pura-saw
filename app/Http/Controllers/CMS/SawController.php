<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\SawRequest;
use App\Repositories\SawRepositories;
use App\Traits\HttpResponseTraits;
use Illuminate\Http\Request;

class SawController extends Controller
{
    use HttpResponseTraits;
    protected $nilaiRepo;

    public function __construct(SawRepositories $nilaiRepo)
    {
        $this->nilaiRepo = $nilaiRepo;
    }
    public function getAllData()
    {
        return $this->nilaiRepo->getAll();
    }
    public function getRankingResult()
    {
        try {
            $data = $this->nilaiRepo->calculateSaw();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function batchStore(SawRequest $request)
    {
        try {
            $data = $this->nilaiRepo->batchStore($request->all());
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function simpanHasilSaw(Request $request)
    {
        try {
            $ranking = $this->nilaiRepo->calculateSaw();
            foreach ($ranking as $index => &$val) {
                $val['ranking'] = $index + 1;
            }

            $this->nilaiRepo->saveBatchRanking($ranking);

            return $this->success($ranking);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function clearPenilaian()
    {
        try {
            $data = $this->nilaiRepo->clearPenilaian();
            return $this->delete($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function hasilSaw()
    {
        return $this->nilaiRepo->hasilSaw();
    }
}
