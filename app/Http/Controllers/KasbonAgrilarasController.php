<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonAgrilarasController extends Controller
{
    protected $tbl;
    public function __construct()
    {
        $this->tbl = DB::table('kasbon');
    }

    public function cuti(Request $r)
    {
        $data = [
            'title' => 'Cuti'
        ];
        return view('cuti.cuti',$data);
    }

    public function cutiCreate(Request $r)
    {
        DB::table('cuti')->insert([
            'id_karyawan' => $r->id_karyawan,
            'tgl' => $r->tgl,
            'alasan' => $r->alasan,
        ]);
        return redirect()->route('cuti')->with('sukses', 'Data Berhasil');
    }

    public function index(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-0d');
        $data = [
            'title' => 'Data Kasbon',
            'aktif' => 1,
            'id_departemen' => $r->id_departemen,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'kasbon' => DB::table('kasbon as a')
                ->leftJoin('karyawan as b', 'a.id_karyawan', 'b.id_karyawan')
                ->select('b.nama_karyawan', 'a.tgl', 'a.nominal', 'a.admin', 'a.id_kasbon')
                ->whereBetween('a.tgl', [$tgl1, $tgl2])
                ->where('a.id_departemen', $r->id_departemen)
                ->get(),
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get()
        ];

        return view('kasbon.agrilaras.kasbon', $data);
    }
    
    public function print(Request $r)
    {
        $data = [
            'title' => 'Data Kasbon',
            'id_departemen' => $r->id_departemen,
            'tgl1' => $r->tgl1,
            'tgl2' => $r->tgl2,
            'kasbon' => DB::select("SELECT b.nama_karyawan, sum(a.nominal) as nominal FROM kasbon a
                LEFT JOIN karyawan b on a.id_karyawan = b.id_karyawan
                WHERE a.tgl BETWEEN '$r->tgl1' AND '$r->tgl2' AND a.id_departemen = '$r->id_departemen'
                GROUP BY a.id_karyawan"),
        ];

        return view('kasbon.agrilaras.print', $data);
    }

    public function create(Request $r)
    {
        for ($i=0; $i < count($r->id_karyawan); $i++) { 
            $this->tbl->insert([
                'tgl' => $r->tgl,
                'id_karyawan' => $r->id_karyawan[$i],
                'nominal' => $r->nominal[$i],
                'id_departemen' => $r->id_departemen,
                'admin' => auth()->user()->nama,
                'tgl_input' => date('Y-m-d'),
            ]);
        }
        return redirect()->route('kasbonAgrilaras', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function btn_tambah(Request $r)
    {
        $data = [
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get(),
            'count' => $r->count
        ];
        return view('kasbon.agrilaras.btn_tambah',$data);
    }

    public function edit(Request $r)
    {
        $data = [
            'kasbon' => $this->tbl->where('id_kasbon', $r->id_kasbon)->first(),
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get()
        ];
        return view('kasbon.agrilaras.edit',$data);
    }

    public function update(Request $r)
    {
        $this->tbl->where('id_kasbon', $r->id_kasbon)->update([
            'tgl' => $r->tgl,
            'id_karyawan' => $r->id_karyawan,
            'nominal' => $r->nominal,
            'admin' => auth()->user()->nama,
            'tgl_input' => date('Y-m-d'),
        ]);
        return redirect()->route('kasbonAgrilaras', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil di update');
    }

    public function delete(Request $r)
    {
        $this->tbl->where('id_kasbon', $r->id_kasbon)->delete();
        return redirect()->route('kasbonAgrilaras', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil dihapus');
    }
}
