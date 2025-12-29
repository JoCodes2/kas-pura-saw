<?php

namespace App\Interfaces;

use App\Http\Requests\KriteriaRequest;

interface KriteriaInterfaces
{
    public function getAllData();
    public function getDataById($id);
    public function createData(KriteriaRequest $request);
    public function updateData(KriteriaRequest $request, $id);
    public function deleteData($id);
}
