<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSawModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'hasil_saw';
    protected $fillable = ['id', 'id_kegiatan', 'nilai_preferensi', 'peringkat', 'tanggal_hitung', 'created_at', 'updated_at'];
}
