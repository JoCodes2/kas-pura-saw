<?php

namespace App\Interfaces;

use App\Http\Requests\MasterRequest;

interface MasterInterfaces
{
    public function getAllData();
    public function getDataById($id);
    public function createData(MasterRequest $request);
    public function updateData(MasterRequest $request, $id);
    public function deleteData($id);
}
