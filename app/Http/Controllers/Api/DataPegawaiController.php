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
            ->join('karyawan as k', 'a.id_karyawan', '=', 'k.id_karyawan')
            ->select(
                'k.id_karyawan',
                'k.nama_karyawan as nama',
                'a.tanggal as tgl',
                'a.ket'
            )
            ->whereYear('a.tanggal', date('Y'))
            ->where('k.nama_karyawan', $nama)
            ->get();

        $datas = [
            'sumber_data' => 'absensi',
            'absensi' => $absensi
        ];
        return response()->json($datas, 200);
    }
}
