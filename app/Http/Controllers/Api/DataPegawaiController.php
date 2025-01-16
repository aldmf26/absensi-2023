<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPegawaiController extends Controller
{
    public function index()
    {
        dd(1);
        // Ambil semua data pegawai
        $dataPegawai = DB::table('karyawan')
                        ->selectRaw("
                        id_karyawan as id_pegawai,
                        nama_karyawan as nama,
                        tanggal_masuk as tgl_masuk,
                        posisi
                        ")
                        ->where('id_departemen', 1)
                        ->get();

        $datas = [
            'sumber_data' => 'absensi',
            'pegawai' => $dataPegawai,
            'total' => count($dataPegawai)
        ];
        return response()->json($datas, 200);
    }
}
