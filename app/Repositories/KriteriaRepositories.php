<?php

namespace App\Repositories;


use App\Http\Requests\KegiatanRequest;
use App\Http\Requests\KriteriaRequest;
use App\Interfaces\KegiatanInterfaces;
use App\Interfaces\KriteriaInterfaces;
use App\Models\KegiatanModel;
use App\Models\KriteriaModel;
use App\Traits\HttpResponseTraits;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KriteriaRepositories implements KriteriaInterfaces
{
    use HttpResponseTraits;
    protected $KriteriaModel;
    public function __construct(KriteriaModel $KriteriaModel)
    {
        $this->KriteriaModel = $KriteriaModel;
    }
    public function getAllData()
    {
        $data = $this->KriteriaModel::all();
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    public function getDataById($id)
    {
        $data = $this->KriteriaModel::find($id);
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }


    public function createData(KriteriaRequest $request)
    {
        try {
            $data = new KriteriaModel();

            $data->nama_kriteria     = $request->nama_kriteria;
            $data->tipe            = $request->tipe;
            $data->bobot    = $request->bobot;

            $data->save();

            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                400,
                $th,
                class_basename($this),
                __FUNCTION__
            );
        }
    }


    public function updateData(KriteriaRequest $request, $id)
    {
        try {
            $data = $this->KriteriaModel::findOrFail($id);

            $data->nama_kriteria     = $request->nama_kriteria;
            $data->tipe            = $request->tipe;
            $data->bobot    = $request->bobot;

            $data->save();

            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error(
                $th->getMessage(),
                400,
                $th,
                class_basename($this),
                __FUNCTION__
            );
        }
    }

    public function deleteData($id)
    {
        try {
            $data = $this->KriteriaModel::where('id', $id)->first();
            $data->delete();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
}
