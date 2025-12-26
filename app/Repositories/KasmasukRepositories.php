<?php

namespace App\Repositories;

use App\Http\Requests\JabatanRequest;
use App\Http\Requests\KasmasukRequest;
use App\Http\Requests\MasterRequest;
use App\Interfaces\JabatanInterfaces;
use App\Interfaces\KasmasukInterfaces;
use App\Interfaces\MasterInterfaces;
use App\Models\JabatanModel;
use App\Models\KasmasukModel;
use App\Models\MasterModel;
use App\Traits\HttpResponseTraits;

class KasmasukRepositories implements KasmasukInterfaces
{
    use HttpResponseTraits;
    protected $KasmasukModel;
    public function __construct(KasmasukModel $KasmasukModel)
    {
        $this->KasmasukModel = $KasmasukModel;
    }
    // public function getAllData()
    // {
    //     $data = $this->KasmasukModel::all();
    //     if (!$data) {
    //         return $this->dataNotFound();
    //     }
    //     return $this->success($data);
    // }

    public function getAllData()
    {
        $data = $this->KasmasukModel::with('kas')->get();

        if ($data->isEmpty()) {
            return $this->dataNotFound();
        }

        return $this->success($data);
    }



    public function getAllKas()
    {
        $data = MasterModel::select('id', 'nama_kas')->get();

        if ($data->isEmpty()) {
            return $this->dataNotFound();
        }

        return $this->success($data);
    }


    public function getDataById($id)
    {
        $data = $this->KasmasukModel::find($id);
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    public function createData(KasmasukRequest $request)
    {
        try {
            $data = new $this->KasmasukModel;
            $data->kas_id = $request->input('kas_id');
            $data->tanggal = $request->input('tanggal');
            $data->sumber = $request->input('sumber');
            $data->jumlah = $request->input('jumlah');
            $data->keterangan = $request->input('keterangan');
            $data->save();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }

    public function updateData(KasmasukRequest $request, $id)
    {
        try {
            $data = $this->KasmasukModel::where('id', $id)->first();
            $data->kas_id = $request->input('kas_id');
            $data->tanggal = $request->input('tanggal');
            $data->sumber = $request->input('sumber');
            $data->jumlah = $request->input('jumlah');
            $data->keterangan = $request->input('keterangan');
            $data->save();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
    public function deleteData($id)
    {
        try {
            $data = $this->KasmasukModel::where('id', $id)->first();
            $data->delete();
            return $this->success($data);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
        }
    }
}
