<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\KriteriaRequest;
use App\Repositories\KriteriaRepositories;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    protected $KriteriaRepo;
    public function __construct(KriteriaRepositories $KriteriaRepo)
    {
        $this->KriteriaRepo = $KriteriaRepo;
    }
    public function getAllData()
    {
        return $this->KriteriaRepo->getAllData();
    }
    public function getDataById($id)
    {
        return $this->KriteriaRepo->getDataById($id);
    }
    public function createData(KriteriaRequest $request)
    {
        return $this->KriteriaRepo->createData($request);
    }
    public function updateData(KriteriaRequest $request, $id)
    {
        return $this->KriteriaRepo->updateData($request, $id);
    }
    public function deleteData($id)
    {
        return $this->KriteriaRepo->deleteData($id);
    }
}
