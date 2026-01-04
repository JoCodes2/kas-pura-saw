<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasKeluar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kas_keluar';
    protected $fillable = [
        'id',
        'id_kegiatan',
        'id_kas',
        'jumlah',
        'keterangan',
        'tanggal',
        'created_at',
        'updated_at'
    ];
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(KegiatanModel::class, 'id_kegiatan', 'id');
    }
    public function kas(): BelongsTo
    {
        return $this->belongsTo(MasterModel::class, 'id_kas', 'id');
    }
}
