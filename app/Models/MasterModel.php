<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterModel extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'master_kas';
    protected $fillable = [
        'id',
        'nama_kas',
        'saldo',
        'created_at',
        'updated_at'
    ];

    public function kas_masuk()
    {
        return $this->hasMany(KasmasukModel::class, 'kas_id', 'id');
    }
}
