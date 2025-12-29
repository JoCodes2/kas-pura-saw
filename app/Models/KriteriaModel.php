<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaModel extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'kriteria';
    protected $fillable = [
        'id',
        'nama_kriteria',
        'tipe',
        'bobot',
        'created_at',
        'updated_at'
    ];
}
