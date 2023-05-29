<?php

namespace App\Http\Controllers;

use App\Models\Absensi_Agrilaras;
use App\Models\Karyawan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class tabelAgrilaras extends Controller
{
    //
    public function index(Request $request)
    {
        $id_departemen = $request->id_departemen;
        $id_user = Auth::user()->id;
        // select('absensi_agrilaras.*','karyawan.*')
        // ->join('karyawan','absensi_agrilaras.id_karyawan', '=', 'karyawan.id_karyawan')->orderBy('id', 'desc'),
        if (empty($request->bulan)) {
            $bulan = date('m');
         } else {
             $bulan = $request->bulan;
         }
         if (empty($request->tahun)) {
            $tahun = date('Y');
         } else {
             $tahun = $request->tahun;
         }

        $data = [
            'title' => 'Absensi',
            'absensi' => Absensi_Agrilaras::select('absensi_agrilaras.*', 'karyawan.nama_karyawan', 'users.nama')->join('karyawan', 'absensi_agrilaras.id_karyawan', '=', 'karyawan.id_karyawan')->join('users', 'absensi_agrilaras.admin', '=', 'users.id')->where('absensi_agrilaras.tanggal_masuk', 'LIKE', '%'.'2022-03-01'.'%')->orderBy('id', 'desc')->get(),
            'karyawan' => Karyawan::where('id_departemen', 'LIKE', '%'.'4'.'%')->orderBy('id_karyawan', 'desc')->get(),
            'status' => Status::all(),
            'tahun' => Absensi_Agrilaras::all(),
            'bulan' => $bulan,
            'tahun_2' => $tahun,
            's_tahun' => DB::select(DB::raw("SELECT YEAR(a.tanggal_masuk) as tahun FROM absensi_agrilaras as a group by YEAR(a.tanggal_masuk)")),
            'aktif' => 2,
            'id_departemen' => $id_departemen
        ];


        return view('tabelAgrilaras',['id_departemen' => 4], $data);
    }
    
    public function lembur(Request $request)
    {
        $id_departemen = $request->id_departemen;
        $id_user = Auth::user()->id;
        // select('absensi_agrilaras.*','karyawan.*')
        // ->join('karyawan','absensi_agrilaras.id_karyawan', '=', 'karyawan.id_karyawan')->orderBy('id', 'desc'),
        if (empty($request->bulan)) {
            $bulan = date('m');
        } else {
            $bulan = $request->bulan;
        }
        if (empty($request->tahun)) {
            $tahun = date('Y');
        } else {
            $tahun = $request->tahun;
        }

        $data = [
            'title' => 'Absensi',
            'absen_lembur' => DB::select("SELECT * FROM absen_lembur as a left join karyawan as b on b.id_karyawan = a.id_karyawan Order by a.id_absen_lembur DESC"),

            'aktif' => 2,
            'id_departemen' => $id_departemen,
            'karyawan' => Karyawan::where('id_departemen', '4')->get(),
        ];


        return view('lembur.index', ['id_departemen' => 4], $data);
    }

    public function edit_data(Request $r)
    {
        $data = [
            'absen_edit' => DB::selectOne("SELECT * FROM absen_lembur as a left join karyawan as b on b.id_karyawan = a.id_karyawan where a.id_absen_lembur = '$r->id_absen_lembur' Order by a.id_absen_lembur DESC"),
            'karyawan' => Karyawan::where('id_departemen', '4')->get(),
        ];
        return view('lembur.edit', ['id_departemen' => 4], $data);
    }

    public function save_absen_lembur(Request $r)
    {
        $id_karyawan = $r->id_karyawan;

        $tanggal = $r->tanggal;
        $pekerjaan = $r->pekerjaan;
        $j_awal = $r->j_awal;
        $j_akhir = $r->j_akhir;

        for ($i = 0; $i < sizeof($id_karyawan); $i++) {
            $id_krwn = $id_karyawan[$i];
            $gaji = DB::selectOne("SELECT * FROM tb_gaji as a where a.id_karyawan = '$id_krwn'");
            $data = [
                'id_karyawan' => $id_karyawan[$i],
                'tgl' => $tanggal,
                'pekerjaan' => $pekerjaan[$i],
                'j_awal' => $j_awal[$i],
                'j_akhir' => $j_akhir[$i],
                'bayaran' => $gaji->rp_m / 8
            ];
            DB::table('absen_lembur')->insert($data);
        }
    }
    public function edit_absen_lembur(Request $r)
    {


        $data = [
            'id_karyawan' => $r->id_karyawan,
            'tgl' => $r->tanggal,
            'pekerjaan' => $r->pekerjaan,
            'j_awal' => $r->j_awal,
            'j_akhir' => $r->j_akhir
        ];
        DB::table('absen_lembur')->where('id_absen_lembur', $r->id_absen_lembur)->update($data);
    }

    public function hapus_data(Request $r)
    {
        DB::table('absen_lembur')->where('id_absen_lembur', $r->id_absen_lembur)->delete();
    }

    public function tambah_lembur(Request $r)
    {
        $data = [
            'title' => 'Absensi',
            'count' => $r->count,
            'karyawan' => Karyawan::where('id_departemen', '4')->get(),
        ];
        return view('lembur.tambah_data', ['id_departemen' => 4], $data);
    }
    
    public function exportLembur(Request $r)
    {
        dd(1);
    }
}
