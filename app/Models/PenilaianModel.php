<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'penilaian_kegiatan';

    protected $fillable = [
        'id',
        'id_kegiatan',
        'id_kriteria',
        'nilai',
        'created_at',
        'updated_at'
    ];
}
