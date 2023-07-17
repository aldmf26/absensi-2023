<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DendaController extends Controller
{
    protected $tbl;
    public function __construct()
    {
        $this->tbl = DB::table('denda');
    }

    public function index(Request $r)
    {
        $data = [
            'title' => 'Data Denda',
            'aktif' => 1,
            'id_departemen' => $r->id_departemen,
            'kasbon' => DB::table('denda as a')
                ->leftJoin('karyawan as b', 'a.id_karyawan', 'b.id_karyawan')
                ->select('b.nama_karyawan', 'a.tgl', 'a.nominal', 'a.admin', 'a.id_denda', 'a.ket')
                ->whereBetween('a.tgl', [$r->tgl1 ?? date('Y-m-01'), $r->tgl2 ?? date('Y-m-d')])
                ->where('a.id_departemen', $r->id_departemen)
                ->get(),
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get()
        ];

        return view('denda.denda', $data);
    }

    public function create(Request $r)
    {
        for ($i=0; $i < count($r->id_karyawan); $i++) { 
            $this->tbl->insert([
                'tgl' => $r->tgl,
                'id_karyawan' => $r->id_karyawan[$i],
                'nominal' => $r->nominal[$i],
                'ket' => $r->keterangan[$i],
                'id_departemen' => $r->id_departemen,
                'admin' => auth()->user()->nama
            ]);
        }
        return redirect()->route('denda', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil ditambahkan');
    }

    public function btn_tambah(Request $r)
    {
        $data = [
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get(),
            'count' => $r->count
        ];
        return view('denda.btn_tambah',$data);
    }

    public function edit(Request $r)
    {
        $data = [
            'kasbon' => $this->tbl->where('id_denda', $r->id_denda)->first(),
            'karyawan' => DB::table('karyawan')->where('id_departemen', $r->id_departemen)->get()
        ];
        return view('denda.edit',$data);
    }

    public function update(Request $r)
    {
        $this->tbl->where('id_denda', $r->id_denda)->update([
            'tgl' => $r->tgl,
            'id_karyawan' => $r->id_karyawan,
            'nominal' => $r->nominal,
            'ket' => $r->keterangan,
            'admin' => auth()->user()->nama
        ]);
        return redirect()->route('denda', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil di update');
    }

    public function delete(Request $r)
    {
        $this->tbl->where('id_denda', $r->id_denda)->delete();
        return redirect()->route('denda', ['id_departemen' => $r->id_departemen])->with('sukses', 'Data Berhasil dihapus');
    }
}
