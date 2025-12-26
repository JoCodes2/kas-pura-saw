<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KegiatanModel extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'kegiatan';
    protected $fillable = [
        'id',
        'nama_pengaju',
        'no_hp',
        'nama_kegiatan',
        'tanggal_kegiatan',
        'estimasi_biaya',
        'file_proposal',
        'status_kegiatan',
        'saldo',
        'created_at',
        'updated_at'
    ];
}
