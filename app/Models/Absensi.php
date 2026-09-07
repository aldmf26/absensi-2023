<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';
    protected $primaryKey = 'id_absen';
    protected $fillable = [
        'id_karyawan','id_jenis_pekerjaan','id_pemakai','tanggal', 'ket',
        'foto_masuk','foto_selesai','status','jam_masuk','jam_selesai','jumlah_hari'
    ];

    protected $casts = [
        'jam_masuk' => 'datetime',
        'jam_selesai' => 'datetime',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis_pekerjaan');
    }
}
