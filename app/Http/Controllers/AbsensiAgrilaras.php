<?php

namespace App\Http\Controllers;

use App\Models\Absensi_Agrilaras;
use App\Models\Karyawan;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DateTime;

class AbsensiAgrilaras extends Controller
{
  public function editAbsen(Request $r)
  {
    if ($r->status == 'OFF') {
      DB::table('absensi_agrilaras')->where('id', $r->id_absen)->delete();
    } else {
      DB::table('absensi_agrilaras')->where('id', $r->id_absen)->update([
        'status' => $r->status
      ]);
    }
  }

  public function addAbsen(Request $r)
  {

    DB::table('absensi_agrilaras')->insert([
      'id_karyawan' => $r->id_karyawan,
      'status' => 'M',
      'tanggal_masuk' => 'M',
      'admin' => auth()->user()->id,
    ]);
  }

  public function detail_agrilaras(Request $request)
  {
    $agent = new Agent();
    $id_departemen = $request->id_departemen;
    $id_user = Auth::user()->id;

    $bulan = $request->bulan ?? date('m');

    $tahun = $request->tahun ?? date('Y');

    $tgl = $request->tgl ?? date('Y-m-d');

    $data = [
      'title' => 'Absensi',
      'absensi' => Absensi_Agrilaras::select('absensi_agrilaras.*', 'karyawan.nama_karyawan', 'users.nama')->join('karyawan', 'absensi_agrilaras.id_karyawan', '=', 'karyawan.id_karyawan')->join('users', 'absensi_agrilaras.admin', '=', 'users.id')->where('absensi_agrilaras.tanggal_masuk', 'LIKE', '%' . '2022-03-01' . '%')->orderBy('id', 'desc')->get(),
      'karyawan' => Karyawan::where('id_departemen', 'LIKE', '%' . '4' . '%')->get(),
      'tahun' => Absensi_Agrilaras::all(),
      'bulan' => $bulan,
      'tgl' => $tgl,
      'tahun_2' => $tahun,
      's_tahun' => DB::select(DB::raw("SELECT YEAR(a.tanggal_masuk) as tahun FROM absensi_agrilaras as a group by YEAR(a.tanggal_masuk)")),
      'status' => Status::all(),
      'id_departemen' => $id_departemen,
      'shift' => Status::all(),
    ];
    if ($agent->isMobile()) {
      return view('absenMobile.absen', ['id_departemen' => 4], $data);
    } else {
      return view('absensi_agrilaras.absensi_agrilaras_detail', ['id_departemen' => 4], $data);
    }
  }

  public function tabelAbsenM(Request $request)
  {
    $tg = $request->tgl;
    if (empty($tg)) {
      $tgl = date('Y-m-d');
    } else {
      $tgl = $tg;
    }
    $data = [
      'tb_karyawan' => Karyawan::where('id_departemen', 'LIKE', '%' . '4' . '%')->get(),
      'tgl' => $tgl,
      'shift' => Status::all(),
    ];
    return view('absenMobile.tabelAbsen', $data);
  }

  public function addAbsenM(Request $request)
  {
    $status = $request->ket;
    $id_karyawan = $request->id_karyawan;
    $tgl = $request->tgl;
    $ada = Absensi_Agrilaras::where([
      ['id_karyawan', $id_karyawan],
      ['status', $status],
      ['tanggal_masuk', $tgl],
    ])->delete();

    if ($ada) {
      return true;
    } else {
      $data =  [
        'status' => $request->ket,
        'id_karyawan' => $request->id_karyawan,
        'tanggal_masuk' => $request->tgl,
        'admin' => Auth::user()->id,
      ];
      Absensi_Agrilaras::create($data);
    }
  }

  public function deleteAbsenM(Request $request)
  {
    Absensi_Agrilaras::where('id', $request->id_absen)->delete();
  }

  public function updateAbsenM(Request $request)
  {
    $data = [
      'status' => $request->ket2,
    ];

    Absensi_Agrilaras::where('id', $request->id_absen_edit)->update($data);
    return true;
  }

  public function ubah_bulan(Request $request)
  {
    dd($request->bulan);
  }

  public function input_agrilaras(Request $request)
  {
    $status = $request->status;
    $id_karyawan = $request->id_karyawan;
    $tgl = $request->tanggal;
    $ada = Absensi_Agrilaras::where([
      ['id_karyawan', $id_karyawan],
      ['status', $status],
      ['tanggal_masuk', $tgl],
    ])->delete();

    if ($ada) {
      return true;
    } else {
      $data =  [
        'status' => $status,
        'id_karyawan' => $id_karyawan,
        'tanggal_masuk' => $tgl,
        'admin' => Auth::user()->id,
      ];
      Absensi_Agrilaras::create($data);
    }
  }

  public function delete_agrilaras(Request $request)
  {
    Absensi_Agrilaras::where('id', $request->id_absen)->delete();
    return true;
  }

  public function downloadAbsAgri(Request $request)
  {

    $bulan = $request->bulanDwn;
    $tahun = $request->tahunDwn;
    $data = [
      'absensi' => DB::select(DB::raw("SELECT a.*, COUNT(a.status) AS total_masuk,b.nama_karyawan
            FROM absensi_agrilaras AS a
            LEFT JOIN karyawan as b on b.id_karyawan = a.id_karyawan
            WHERE MONTH(a.tanggal_masuk) = $bulan AND YEAR(a.tanggal_masuk) = $tahun
            GROUP BY a.id_karyawan ORDER BY a.id_karyawan DESC")),
      'bulan' => $bulan,
      'tahun' => $tahun,
      'karyawan' => Karyawan::where('id_departemen', 'LIKE', '%' . '4' . '%')->orderBy('id_karyawan', 'DESC')->get(),

    ];

    return view('absensi_agrilaras.excel', $data);
  }

  public function queryGaji($tgl1, $tgl2)
  {
    return DB::select("SELECT 
                  a.id_karyawan, 
                  a.nama_karyawan, 
                  a.posisi, 
                  a.tanggal_masuk, 
                  b.g_bulanan, 
                  b.rp_m, 
                  SUM(d.qty_m) as qty_m, 
                  SUM(d.qty_e) as qty_e, 
                  SUM(d.qty_sp) as qty_sp, 
                  SUM(d.qty_m * b.rp_m) as ttl_gaji_m, 
                  SUM(d.qty_e * b.rp_e) as ttl_gaji_e, 
                  SUM(d.qty_sp * b.rp_sp) as ttl_gaji_sp, 
                  e.lama_lembur, 
                  e.bayaran, 
                  kasbon.kasbon, 
                  denda.denda 
                FROM 
                  `karyawan` as a 
                  LEFT JOIN tb_gaji as b ON a.id_karyawan = b.id_karyawan 
                  LEFT JOIN (
                    SELECT 
                      e.id_karyawan, 
                      sum(e.jam) as lama_lembur, 
                      e.bayaran 
                    FROM 
                      view_absen_lembur as e 
                    where 
                      e.tgl BETWEEN '$tgl1' 
                      and '$tgl2' 
                    group by 
                      e.id_karyawan
                  ) as e on e.id_karyawan = a.id_karyawan 
                  LEFT JOIN (
                    SELECT 
                      c.id_karyawan, 
                      c.status, 
                      IF(
                        c.status = 'M' OR c.status = 'CT', 
                        COUNT(c.status), 
                        0
                      ) as qty_m, 
                      IF(
                        c.status = 'E', 
                        COUNT(c.status), 
                        0
                      ) as qty_e, 
                      IF(
                        c.status = 'SP', 
                        COUNT(c.status), 
                        0
                      ) as qty_sp 
                    FROM 
                      absensi_agrilaras as c 
                    WHERE 
                      c.tanggal_masuk BETWEEN '$tgl1' 
                      AND '$tgl2' 
                    GROUP BY 
                      c.id_karyawan, 
                      c.status
                  ) as d on d.id_karyawan = a.id_karyawan 
                  LEFT JOIN (
                    SELECT 
                      a.id_karyawan, 
                      sum(a.nominal) as kasbon 
                    FROM 
                      kasbon as a 
                    WHERE 
                      a.tgl BETWEEN '$tgl1' 
                      AND '$tgl2' 
                    GROUP BY 
                      a.id_karyawan
                  ) as kasbon ON kasbon.id_karyawan = a.id_karyawan 
                  LEFT JOIN (
                    SELECT 
                      a.id_karyawan, 
                      sum(a.nominal) as denda 
                    FROM 
                      denda as a 
                    WHERE 
                      a.tgl BETWEEN '$tgl1' 
                      AND '$tgl2' 
                    GROUP BY 
                      a.id_karyawan
                  ) as denda ON denda.id_karyawan = a.id_karyawan 
                WHERE 
                  a.id_departemen = 4 
                GROUP BY 
                  a.id_karyawan 
                ORDER BY 
                  a.id_karyawan desc
");
  }

  public function gajiAgrilaras(Request $r)
  {
    $tgl1 = $r->tgl1 ?? date('Y-m-1');
    $tgl2 = $r->tgl2 ?? date('Y-m-d');

    $query = $this->queryGaji($tgl1, $tgl2);


    $data = [
      'title' => 'Gaji Agrilaras',
      'tgl1' => $tgl1,
      'tgl2' => $tgl2,
      'id_departemen' => 4,
      'shift' => Status::all(),
      'hasil' => $query,
    ];

    return view('gaji.gaji', $data);
  }

  public function exportGaji(Request $r)
  {
    $tgl1 = $r->tgl1 ?? date('Y-m-1');
    $tgl2 = $r->tgl2 ?? date('Y-m-d');

    $query = $this->queryGaji($tgl1, $tgl2);

    $status = Status::all();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->getColumnDimension('A')->setWidth(3);
    $sheet->getColumnDimension('B')->setWidth(21);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(8);
    $sheet->getColumnDimension('E')->setWidth(8);
    $sheet->getColumnDimension('F')->setWidth(8);
    $sheet->getColumnDimension('G')->setWidth(12);
    $sheet->getColumnDimension('H')->setWidth(12);
    $sheet->getColumnDimension('M')->setWidth(10);
    $sheet->getColumnDimension('N')->setWidth(10);
    $sheet->getColumnDimension('O')->setWidth(15);


    $sheet
      ->setCellValue('A1', 'NO')
      ->setCellValue('B1', 'NAMA KARYAWAN')
      ->setCellValue('C1', 'POSISI')
      ->setCellValue('D1', 'RP HARIAN')
      ->setCellValue('E1', 'RP BULANAN')
      ->setCellValue('F1', 'RP JAM LEMBUR')
      ->setCellValue('G1', 'TOTAL ABSEN HARIAN')
      ->setCellValue('H1', 'JAM LEMBUR')
      ->setCellValue('I1', 'TOTAL RP HARIAN')
      ->setCellValue('J1', 'RP LEMBUR')
      ->setCellValue('K1', 'TOTAL GAJI')
      ->setCellValue('L1', 'RP KASBON')
      ->setCellValue('M1', 'RP DENDA')
      ->setCellValue('N1', 'SISA GAJI')
      ->setCellValue('O1', 'TGL MASUK')
      ->setCellValue('P1', 'LAMA KERJA');
    // $sheet->getStyle("A$row:T$r   ow")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('92D050');
    $stylebgRed = [
      'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => array('argb' => '92D050')
      ]
    ];

    $kolom = 2;
    $no = 1;
    $ttlGajiM = 0;
    $ttlGajiE = 0;
    $ttlGajiSp = 0;
    $ttlAbsen = 0;
    $ttlBulanan = 0;
    $ttlGaji = 0;
    $ttlKasbon = 0;
    $ttlDenda = 0;
    $ttllembur = 0;
    $total_lembur_2 = 0;

    foreach ($query as $k) {
      $ttlAbsen = $k->qty_m + $k->qty_e + $k->qty_sp;
      $ttlGajiS = $k->ttl_gaji_m + $k->ttl_gaji_e + $k->ttl_gaji_sp + $k->g_bulanan;
      $total_lembur = $k->lama_lembur == '' ? 0 : ($k->rp_m / 8) * ($k->lama_lembur / 60);
      $totalKerja = new DateTime($k->tanggal_masuk);
      $today = new DateTime();
      $tKerja = $today->diff($totalKerja);

      $sheet
        ->setCellValue('A' . $kolom, $no++)
        ->setCellValue('B' . $kolom, $k->nama_karyawan)
        ->setCellValue('C' . $kolom, $k->posisi)
        ->setCellValue('D' . $kolom, $k->rp_m)
        ->setCellValue('E' . $kolom, $k->g_bulanan)
        ->setCellValue('F' . $kolom, $k->rp_m / 8)
        ->setCellValue('G' . $kolom, $k->qty_m == '' ? 0 : $k->qty_m)
        ->setCellValue('H' . $kolom, $k->lama_lembur == '' ? 0 : $k->lama_lembur / 60)
        ->setCellValue('I' . $kolom, $k->ttl_gaji_m == '' ? 0 : $k->ttl_gaji_m)
        ->setCellValue('J' . $kolom, $total_lembur)
        ->setCellValue('K' . $kolom, $ttlGajiS + $total_lembur)
        ->setCellValue('L' . $kolom, $k->kasbon)
        ->setCellValue('M' . $kolom, $k->denda)
        ->setCellValue('N' . $kolom, $ttlGajiS + $total_lembur - $k->kasbon - $k->denda)
        ->setCellValue('O' . $kolom, $k->tanggal_masuk)
        ->setCellValue('P' . $kolom, $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan');
      $ttlGajiM += $k->ttl_gaji_m;
      $ttlGajiE += $k->ttl_gaji_e;
      $ttlGajiSp += $k->ttl_gaji_sp;
      $ttllembur += $total_lembur;
      $ttlBulanan += $k->g_bulanan;
      $ttlKasbon += $k->kasbon;
      $ttlDenda += $k->denda;
      $ttlGaji += $k->ttl_gaji_m + $k->ttl_gaji_e + $k->ttl_gaji_sp + $k->g_bulanan;
      $total_lembur_2 += $total_lembur;
      if ($k->qty_m > 28) {
        // Set the background color to red
        $sheet->getStyle("G$kolom")
          ->applyFromArray($stylebgRed); // Red color
      }
      // if (29 > 28) {
      //   $sheet->getStyle("G1")
      //     ->getFill()
      //     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      //     ->getStartColor()
      //     ->setARGB('92D050');
      // }

      $kolom++;
    }
    $b = count($query) + 2;
    $sheet->setCellValue('E' . $b, $ttlBulanan);
    $sheet->setCellValue('H' . $b, 'TOTAL');
    $sheet->setCellValue('I' . $b, $ttlGajiM);
    $sheet->setCellValue('J' . $b, $ttllembur);
    $sheet->setCellValue('K' . $b, $ttlGaji + $total_lembur_2);
    $sheet->setCellValue('L' . $b, $ttlKasbon);
    $sheet->setCellValue('M' . $b, $ttlDenda);
    $sheet->setCellValue('N' . $b, $ttlGaji + $total_lembur_2 - $ttlKasbon - $ttlDenda);

    $sheet->getStyle('E' . $b)->getFont()->setBold(true);
    $sheet->getStyle('H' . $b)->getFont()->setBold(true);
    $sheet->getStyle('I' . $b)->getFont()->setBold(true);
    $sheet->getStyle('J' . $b)->getFont()->setBold(true);
    $sheet->getStyle('K' . $b)->getFont()->setBold(true);
    $sheet->getStyle('L' . $b)->getFont()->setBold(true);
    $sheet->getStyle('M' . $b)->getFont()->setBold(true);
    $sheet->getStyle('N' . $b)->getFont()->setBold(true);


    $writer = new Xlsx($spreadsheet);
  
    $style = [
      'borders' => [
        'alignment' => [
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
          'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'allBorders' => [
          'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ],
      ]
    ];

    $batas = count($query) + 1;
    $sheet->getStyle('A1:P' . $b)->applyFromArray($style);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Gaji Agrilaras.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }
}
