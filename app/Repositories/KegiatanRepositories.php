<?php

namespace App\Repositories;


use App\Http\Requests\KegiatanRequest;

use App\Interfaces\KegiatanInterfaces;

use App\Models\KegiatanModel;
use App\Traits\HttpResponseTraits;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KegiatanRepositories implements KegiatanInterfaces
{
    use HttpResponseTraits;
    protected $KegiatanModel;
    public function __construct(KegiatanModel $KegiatanModel)
    {
        $this->KegiatanModel = $KegiatanModel;
    }
    public function getAllData()
    {
        $data = $this->KegiatanModel::all();
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    public function getDataById($id)
    {
        $data = $this->KegiatanModel::find($id);
        if (!$data) {
            return $this->dataNotFound();
        }
        return $this->success($data);
    }
    // public function createData(KegiatanRequest $request)
    // {
    //     try {
    //         $data = new $this->KegiatanModel;
    //         $data->nama_pengaju = $request->input('nama_pengaju');
    //         $data->no_hp = $request->input('no_hp');
    //         $data->nama_kegiatan = $request->input('nama_kegiatan');
    //         $data->tanggal_kegiatan = $request->input('tanggal_kegiatan');
    //         $data->estimasi_biaya = $request->input('estimasi_biaya');
    //         $data->file_proposal = $request->file('file_proposal');
    //         $data->status_kegiatan = $request->input('status_kegiatan');
    //         $data->save();
    //         return $this->success($data);
    //     } catch (\Throwable $th) {
    //         return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
    //     }
    // }

    public function createData(KegiatanRequest $request)
    {
        try {
            $data = new KegiatanModel();

            $data->nama_pengaju     = $request->nama_pengaju;
            $data->no_hp            = $request->no_hp;
            $data->nama_kegiatan    = $request->nama_kegiatan;
            $data->tanggal_kegiatan = $request->tanggal_kegiatan;
            $data->estimasi_biaya   = $request->estimasi_biaya;
            $data->status_kegiatan = $request->status_kegiatan ?? 'menunggu';

            if ($request->hasFile('file_proposal')) {
                $file = $request->file('file_proposal');
                $filename = 'file-proposal-' . Str::random(15) . '.' . $file->getClientOriginalExtension();

                if (!file_exists(public_path('uploads/file-proposal'))) {
                    mkdir(public_path('uploads/file-proposal'), 0755, true);
                }

                $file->move(public_path('uploads/file-proposal'), $filename);
                $data->file_proposal = $filename;
            }

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


    // public function updateData(KegiatanRequest $request, $id)
    // {
    //     try {
    //         $data = $this->KegiatanModel::where('id', $id)->first();
    //         $data->nama_pengaju = $request->input('nama_pengaju');
    //         $data->no_hp = $request->input('no_hp');
    //         $data->nama_kegiatan = $request->input('nama_kegiatan');
    //         $data->tanggal_kegiatan = $request->input('tanggal_kegiatan');
    //         $data->estimasi_biaya = $request->input('estimasi_biaya');
    //         $data->file_proposal = $request->file('file_proposal');
    //         $data->status_kegiatan = $request->input('status_kegiatan');
    //         $data->save();
    //         return $this->success($data);
    //     } catch (\Throwable $th) {
    //         return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
    //     }
    // }

    public function updateData(KegiatanRequest $request, $id)
    {
        try {
            $data = $this->KegiatanModel::findOrFail($id);

            $data->nama_pengaju     = $request->nama_pengaju;
            $data->no_hp            = $request->no_hp;
            $data->nama_kegiatan    = $request->nama_kegiatan;
            $data->tanggal_kegiatan = $request->tanggal_kegiatan;
            $data->estimasi_biaya   = $request->estimasi_biaya;
            $data->status_kegiatan = $request->status_kegiatan ?? $data->status_kegiatan;

            // HANDLE FILE PROPOSAL
            if ($request->hasFile('file_proposal')) {

                // hapus file lama jika ada
                if ($data->file_proposal && file_exists(public_path('uploads/file-proposal/' . $data->file_proposal))) {
                    unlink(public_path('uploads/file-proposal/' . $data->file_proposal));
                }

                $file = $request->file('file_proposal');
                $filename = 'file-proposal-' . Str::random(15) . '.' . $file->getClientOriginalExtension();

                if (!file_exists(public_path('uploads/file-proposal'))) {
                    mkdir(public_path('uploads/file-proposal'), 0755, true);
                }

                $file->move(public_path('uploads/file-proposal'), $filename);
                $data->file_proposal = $filename;
            }

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

    // public function deleteData($id)
    // {
    //     try {
    //         $data = $this->KegiatanModel::where('id', $id)->first();
    //         $data->delete();
    //         return $this->success($data);
    //     } catch (\Throwable $th) {
    //         return $this->error($th->getMessage(), 400, $th, class_basename($this), __FUNCTION__);
    //     }
    // }

    public function deleteData($id)
    {
        try {
            $data = $this->KegiatanModel::findOrFail($id);

            // hapus file proposal jika ada
            if ($data->file_proposal && file_exists(public_path('uploads/file-proposal/' . $data->file_proposal))) {
                unlink(public_path('uploads/file-proposal/' . $data->file_proposal));
            }

            $data->delete();

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
}
