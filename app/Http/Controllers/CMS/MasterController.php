<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterRequest;
use App\Repositories\MasterRepositories;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    protected $MasterRepo;
    public function __construct(MasterRepositories $MasterRepo)
    {
        $this->MasterRepo = $MasterRepo;
    }
    public function getAllData()
    {
        return $this->MasterRepo->getAllData();
    }
    public function getDataById($id)
    {
        return $this->MasterRepo->getDataById($id);
    }
    public function createData(MasterRequest $request)
    {
        return $this->MasterRepo->createData($request);
    }
    public function updateData(MasterRequest $request, $id)
    {
        return $this->MasterRepo->updateData($request, $id);
    }
    public function deleteData($id)
    {
        return $this->MasterRepo->deleteData($id);
    }
}
