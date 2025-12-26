<?php

namespace App\Repositories;

use App\Http\Requests\JabatanRequest;
use App\Http\Requests\MasterRequest;
use App\Interfaces\JabatanInterfaces;
use App\Interfaces\MasterInterfaces;
use App\Models\JabatanModel;
use App\Models\MasterModel;
use App\Traits\HttpResponseTraits;

class MasterRepositories implements MasterInterfaces
{
    use HttpResponseTraits;
    protected $MasterModel;
    public function __construct(MasterModel $MasterModel)
    {
        $this->MasterModel = $MasterModel;
    }
    public function getAllData()
    {
        $data = $this->MasterModel::all();
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    public function getDataById($id)
    {
        $data = $this->MasterModel::find($id);
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    public function createData(MasterRequest $request)
    {
        try {
            $data = new $this->MasterModel;
            $data->nama_kas = $request->input('nama_kas');
            $data->saldo = $request->input('saldo');
            $data->save();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }

    public function updateData(MasterRequest $request, $id)
    {
        try {
            $data = $this->MasterModel::where('id', $id)->first();
            $data->nama_kas = $request->input('nama_kas');
            $data->saldo = $request->input('saldo');
            $data->save();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function deleteData($id)
    {
        try {
            $data = $this->MasterModel::where('id', $id)->first();
            $data->delete();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
}
