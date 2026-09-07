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

            $filterJenis = (int) $request->id_jenis;
            $whereJenis = $filterJenis > 0 ? " AND a.id_jenis_pekerjaan = $filterJenis" : '';

            $karyawan = Karyawan::where('id_departemen', '1')->get();
            $tahun = date('Y');
            $pakaiCuti = Absensi::selectRaw('id_karyawan, COALESCE(SUM(jumlah_hari),0) as total')
                ->where('id_jenis_pekerjaan', 17)
                ->whereBetween('tanggal', ["$tahun-01-01", "$tahun-12-31"])
                ->groupBy('id_karyawan')
                ->pluck('total', 'id_karyawan');
            $sisaJatah = [];
            foreach ($karyawan as $k) {
                $sisaJatah[$k->id_karyawan] = max(0, 12 - (int) ($pakaiCuti[$k->id_karyawan] ?? 0));
            }

            $data = [
                'title' => 'Absensi',
                'absensi' => DB::select("SELECT d.id_pemakai,a.id_jenis_pekerjaan, a.id_karyawan, b.nama_karyawan, a.tanggal, c.jenis_pekerjaan, d.pemakai, a.ket ,a.id_absen, a.foto_masuk, a.foto_selesai, a.created_at, a.jam_masuk, a.jam_selesai FROM absensi as a
                LEFT JOIN karyawan as b ON a.id_karyawan = b.id_karyawan
                LEFT JOIN jenis_pekerjaan as c ON a.id_jenis_pekerjaan = c.id
                LEFT JOIN pemakai_jasa as d ON a.id_pemakai = d.id_pemakai
                WHERE b.id_departemen = '1' AND a.tanggal BETWEEN '$dari' AND '$sampai' $whereJenis
                ORDER BY a.id_absen DESC
                "),
                'karyawan' => $karyawan,
                'pemakai' => Pemakai::all(),
                'jenis_pekerjaan' => Jenis::all(),
                'aktif' => 2,
                'dari' => $dari,
                'sampai' => $sampai,
                'id_departemen' => $id_departemen,
                'filterJenis' => $filterJenis,
                'sisaJatah' => $sisaJatah
            ];
            return view('absensi.absensi', $data);
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
                'status' => 'selesai',
            ];

            Absensi::create($data);
        }
        return redirect()->route('absensi', ['id_departemen' => 1]);
    }

    public function addCuti(Request $request)
    {
        $data = $request->validate([
            'id_karyawan' => 'required|integer',
            'jenis_cuti' => 'required|integer', // id jenis_pekerjaan (12 atau 17)
            'tanggal_cuti' => 'required|array|min:1',
            'tanggal_cuti.*' => 'required|date',
            'ket' => 'nullable|string|max:255',
        ]);

        $tanggal = array_values(array_unique($data['tanggal_cuti']));
        sort($tanggal);
        $jumlah_hari = count($tanggal);

        $ket = trim(($data['ket'] ?? '') . ' | ' . $jumlah_hari . ' hari: ' . implode(', ', $tanggal));

        $jenis_cuti = (int) $data['jenis_cuti'];
        $id_karyawan = (int) $data['id_karyawan'];

        // Cuti tahunan (17) dibayar maksimal 12 hari per tahun (Jan-Des).
        // Kelebihan hari otomatis ditandai "TIDAK DIBAYAR" di keterangan.
        $terpakai = $this->totalCutiTahun($id_karyawan);
        $sisa_jatah = max(0, 12 - $terpakai);
        $hari_tidak_dibayar = $jenis_cuti === 17 ? max(0, $jumlah_hari - $sisa_jatah) : 0;
        if ($hari_tidak_dibayar > 0) {
            $ket .= ' | ' . $hari_tidak_dibayar . ' hari TIDAK DIBAYAR (jatah cuti 12 hari habis)';
        }

        Absensi::create([
            'id_karyawan' => $id_karyawan,
            'id_jenis_pekerjaan' => $jenis_cuti,
            'id_pemakai' => 1,
            'tanggal' => $tanggal[0],
            'jumlah_hari' => $jumlah_hari,
            'ket' => $ket,
            'status' => 'selesai',
        ]);

        return redirect()->route('absensi', ['id_departemen' => 1])
            ->with('info', $hari_tidak_dibayar > 0
                ? 'Cuti tersimpan. Jatah habis: ' . $hari_tidak_dibayar . ' hari ditandai TIDAK DIBAYAR.'
                : 'Cuti tersimpan. Sisa jatah: ' . ($sisa_jatah - $jumlah_hari) . ' hari.');
    }

    private function totalCutiTahun(int $id_karyawan): int
    {
        $tahun = date('Y');
        return (int) Absensi::where('id_karyawan', $id_karyawan)
            ->where('id_jenis_pekerjaan', 17)
            ->whereBetween('tanggal', ["{$tahun}-01-01", "{$tahun}-12-31"])
            ->sum('jumlah_hari');
    }

    public function editAbsensi(Request $request)
    {
        $data = [
            'id_karyawan' => $request->id_karyawan,
            'id_jenis_pekerjaan' => $request->id_jenis,
            'id_pemakai' => $request->id_pemakai ?? 1,
            'tanggal' => $request->tanggal,
            'ket' => $request->keterangan,
            'status' => 'selesai',
        ];

        Absensi::where('id_absen', $request->id_absen)->update($data);


        return redirect()->route('absensi', ['tglDari' => $request->tglDari, 'tglSampai' => $request->tglSampai]);
    }

    public function deleteAbsensi(Request $request)
    {
        $absen = Absensi::where('id_absen', $request->id_absen)->first();

        if ($absen) {
            foreach (['foto_masuk', 'foto_selesai'] as $kolom) {
                if (! empty($absen->{$kolom})) {
                    $path = str_replace('storage/', '', $absen->{$kolom});
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
            $absen->delete();
        }

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
        return redirect()->route('absensi', ['id_departemen' => 1, 'tglDari' => $dari, 'tglSampai' => $sampai])->with('error', 'Berhasil hapus absen ' . $dari . ' - ' . $sampai);
    }
}
