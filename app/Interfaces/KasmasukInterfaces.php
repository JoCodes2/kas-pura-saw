<?php

namespace App\Interfaces;

use App\Http\Requests\KasmasukRequest;

interface KasmasukInterfaces
{
    public function getAllData();
    public function getAllKas();
    public function getDataById($id);
    public function createData(KasmasukRequest $request);
    public function updateData(KasmasukRequest $request, $id);
    public function deleteData($id);
}
