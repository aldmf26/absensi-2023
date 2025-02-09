<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiAgriExport;
use App\Models\Absensi;
use App\Models\Jenis;
use App\Models\Karyawan;
use App\Models\Login;
use App\Models\Pemakai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use App\Exports\AbsensiPertanggalExport;

class AbsensiController extends Controller
{
    //
    public function index(Request $request)
    {
        // cek user ada menu apa aja
        $id_departemen = 1;
        $id_user = Auth::user()->id;
        $id_menu = DB::table('permisi')->select('id_menu')->where('id_user', $id_user)
            ->where('id_menu', 3)->first();

        if (empty($id_menu)) {
            // return redirect(route('login'));
            return view('login.login');
        } else {
            $tglDari = $request->tglDari;
            $tglSampai = $request->tglSampai;
            if (empty($tglDari)) {
                $dari = date('Y-m-1');
                $sampai = date('Y-m-d');
            } else {
                $dari = $tglDari;
                $sampai = $tglSampai;
            }

            $data = [
                'title' => 'Absensi',
                'absensi' => DB::select("SELECT d.id_pemakai,a.id_jenis_pekerjaan, a.id_karyawan, b.nama_karyawan, a.tanggal, c.jenis_pekerjaan, d.pemakai, a.ket ,a.id_absen FROM absensi as a
                LEFT JOIN karyawan as b ON a.id_karyawan = b.id_karyawan
                LEFT JOIN jenis_pekerjaan as c ON a.id_jenis_pekerjaan = c.id
                LEFT JOIN pemakai_jasa as d ON a.id_pemakai = d.id_pemakai
                WHERE b.id_departemen = '1' AND a.tanggal BETWEEN '$dari' AND '$sampai'
                ORDER BY a.id_absen DESC
                "),
                'karyawan' => Karyawan::where('id_departemen', '1')->get(),
                'pemakai' => Pemakai::all(),
                'jenis_pekerjaan' => Jenis::all(),
                'aktif' => 2,
                'dari' => $dari,
                'sampai' => $sampai,
                'id_departemen' => $id_departemen
            ];
            return view('absensi.absensi', ['tglDari => ' . $dari . ',' . 'tglSampai' => $sampai], $data);
        }
    }

    public function absensi_edit($id_absen)
    {
        $data = [
            'title' => '2',
            'karyawan' => Karyawan::where('id_departemen', '1')->get(),
            'pemakai' => Pemakai::all(),
            'jenis_pekerjaan' => Jenis::all(),
            'd' => Absensi::where('id_absen', $id_absen)->first()
        ];
        return view('absensi.edit', $data);
    }


    public function addAbsensi(Request $request)
    {
        $id_karyawan = $request->id_karyawan;
        $id_jenis = $request->id_jenis;
        $id_pemakai = $request->id_pemakai;
        $tanggal = $request->tanggal;
        $keterangan = $request->ket;
        for ($i = 0; $i < count($id_karyawan); $i++) {
            $data = [
                'id_karyawan' => $id_karyawan[$i],
                'id_jenis_pekerjaan' => $id_jenis,
                'id_pemakai' => $id_pemakai ?? 1,
                'tanggal' => $tanggal,
                'ket' => $keterangan,
            ];

            Absensi::create($data);
        }
        return redirect()->route('absensi', ['id_departemen' => 1]);
    }

    public function editAbsensi(Request $request)
    {
        $data = [
            'id_karyawan' => $request->id_karyawan,
            'id_jenis_pekerjaan' => $request->id_jenis,
            'id_pemakai' => $request->id_pemakai ?? 1,
            'tanggal' => $request->tanggal,
            'ket' => $request->keterangan,
        ];

        Absensi::where('id_absen', $request->id_absen)->update($data);


        return redirect()->route('absensi', ['tglDari' => $request->tglDari, 'tglSampai' => $request->tglSampai]);
    }

    public function deleteAbsensi(Request $request)
    {

        Absensi::where('id_absen', $request->id_absen)->delete();

        return redirect()->route('absensi', ['id_departemen' => 1, 'tglDari' => $request->tglDari, 'tglSampai' => $request->tglSampai]);
    }

    public function excel()
    {
        // $data = [
        //     'absensi' => Absensi::select('absensi.*', 'karyawan.nama_karyawan', 'karyawan.id_departemen', 'jenis_pekerjaan.jenis_pekerjaan', 'pemakai_jasa.pemakai')->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')->join('jenis_pekerjaan', 'absensi.id_jenis_pekerjaan', '=', 'jenis_pekerjaan.id')->join('pemakai_jasa', 'absensi.id_pemakai', '=', 'pemakai_jasa.id_pemakai')->where('id_departemen', 'LIKE', '%' . '1' . '%')->orderBy('id', 'desc')->get(),
        // ];

        // return view('absensi.excel', $data);
        return Excel::download(new AbsensiExport, 'Absensi Anak Laki.xlsx');
    }

    public function exportPertanggal(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;

        // $data = [
        //     'absensi' => Absensi::select('absensi.*', 'karyawan.nama_karyawan', 'karyawan.id_departemen', 'jenis_pekerjaan.jenis_pekerjaan', 'pemakai_jasa.pemakai')->join('karyawan', 'absensi.id_karyawan', '=', 'karyawan.id_karyawan')->join('jenis_pekerjaan', 'absensi.id_jenis_pekerjaan', '=', 'jenis_pekerjaan.id')->join('pemakai_jasa', 'absensi.id_pemakai', '=', 'pemakai_jasa.id_pemakai')->where('id_departemen', 1)->whereBetween('absensi.tanggal', [$dari, $sampai])->orderBy('id', 'desc')->get(),
        // ];

        // return view('absensi.excel', $data);
        return Excel::download(new AbsensiPertanggalExport($dari, $sampai), 'Absensi Anak Laki Pertanggal.xlsx');
    }

    public function hapusPertanggal(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;

        Absensi::whereBetween('absensi.tanggal', [$dari, $sampai])->delete();
        return redirect()->route('absensi', ['id_departemen' => 1, 'tglDari' => $request->tglDari, 'tglSampai' => $request->tglSampai])->with('error', 'Berhasil hapus absen ' . $dari . ' - ' . $sampai);
    }
}
