<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasmasukModel extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'kas_masuk';
    protected $fillable = [
        'id',
        'kas_id',
        'tanggal',
        'sumber',
        'jumlah',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    public function master()
    {
        return $this->belongsTo(MasterModel::class, 'kas_id');
    }
}
