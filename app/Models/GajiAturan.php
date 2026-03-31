<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiAturan extends Model
{
    use HasFactory;

    protected $table = 'gaji_aturan';
    protected $fillable = [
        'posisi',
        'masa_kerja',
        'rp_harian',
        'rp_bulanan'
    ];
}
