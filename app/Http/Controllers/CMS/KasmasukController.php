<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\KasmasukRequest;
use App\Repositories\KasmasukRepositories;
use Illuminate\Http\Request;

class KasmasukController extends Controller
{
    protected $KasmasukRepo;
    public function __construct(KasmasukRepositories $KasmasukRepo)
    {
        $this->KasmasukRepo = $KasmasukRepo;
    }
    public function getAllData()
    {
        return $this->KasmasukRepo->getAllData();
    }

    public function getAllKas()
    {
        return $this->KasmasukRepo->getAllKas();
    }
    public function getDataById($id)
    {
        return $this->KasmasukRepo->getDataById($id);
    }
    public function createData(KasmasukRequest $request)
    {
        return $this->KasmasukRepo->createData($request);
    }
    public function updateData(KasmasukRequest $request, $id)
    {
        return $this->KasmasukRepo->updateData($request, $id);
    }
    public function deleteData($id)
    {
        return $this->KasmasukRepo->deleteData($id);
    }
}
