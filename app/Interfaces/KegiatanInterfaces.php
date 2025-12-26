<?php

namespace App\Interfaces;

use App\Http\Requests\KegiatanRequest;

interface KegiatanInterfaces
{
    public function getAllData();
    public function getDataById($id);
    public function createData(KegiatanRequest $request);
    public function updateData(KegiatanRequest $request, $id);
    public function deleteData($id);
}
