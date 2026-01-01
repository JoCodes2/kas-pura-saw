<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\KegiatanRequest;
use App\Repositories\KegiatanRepositories;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    protected $KegiatanRepo;
    public function __construct(KegiatanRepositories $KegiatanRepo)
    {
        $this->KegiatanRepo = $KegiatanRepo;
    }
    public function getAllData()
    {
        return $this->KegiatanRepo->getAllData();
    }
    public function getDataById($id)
    {
        return $this->KegiatanRepo->getDataById($id);
    }
    public function createData(KegiatanRequest $request)
    {
        return $this->KegiatanRepo->createData($request);
    }
    public function updateData(KegiatanRequest $request, $id)
    {
        return $this->KegiatanRepo->updateData($request, $id);
    }
    public function deleteData($id)
    {
        return $this->KegiatanRepo->deleteData($id);
    }

    public function updateStatus($id, $status)
    {
        return $this->KegiatanRepo->updateStatus($id, $status);
    }
}
