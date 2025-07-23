<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPegawaiController extends Controller
{
    public function index()
    {
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

    public function absen($nama)
    {
        $absensi = DB::table('absensi as a')
            ->leftJoin('karyawan as k', 'a.id_karyawan', '=', 'k.id_karyawan')
            ->selectRaw("
                        k.id_karyawan,    
                        k.nama_karyawan as nama,
                        a.tanggal as tgl,
                        a.ket
                        ")
            ->where('k.nama_karyawan', "Muhammad Fahrizaldi")
            ->whereYear('a.tanggal', date('Y'))
            ->get();

        $datas = [
            'sumber_data' => 'absensi',
            'absensi' => $absensi
        ];
        return response()->json($datas, 200);
    }
}
