<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jenis;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanAbsenController extends Controller
{
    public function showLogin()
    {
        if (session()->has('absen_karyawan.id')) {
            return redirect()->route('absen.index');
        }
        return view('absen.login');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $karyawan = Karyawan::where('id_departemen', 1)
            ->whereNotNull('pin_absen')
            ->get()
            ->first(function ($k) use ($request) {
                return Hash::check($request->pin, $k->pin_absen);
            });

        if (! $karyawan) {
            return back()->with('error', 'PIN salah. Coba lagi.');
        }

        $request->session()->regenerate(true);
        $request->session()->put('absen_karyawan.id', $karyawan->id_karyawan);
        $request->session()->put('absen_karyawan.nama', $karyawan->nama_karyawan);

        return redirect()->route('absen.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('absen_karyawan');
        $request->session()->regenerate(true);
        return redirect()->route('absen.login');
    }

    public function index(Request $request)
    {
        $id = session('absen_karyawan.id');
        $karyawan = Karyawan::find($id);

        // Record hari ini yang masih "sedang bekerja" untuk jenis apapun
        $sedangBekerja = Absensi::where('id_karyawan', $id)
            ->where('status', 'bekerja')
            ->orderBy('id_absen', 'desc')
            ->with('karyawan')
            ->get();

        // Yang sudah selesai hari ini (untuk status "sudah absen atau belum")
        $hariIni = now('Asia/Makassar')->toDateString();
        $selesaiHariIni = Absensi::where('id_karyawan', $id)
            ->where('status', 'selesai')
            ->whereDate('tanggal', $hariIni)
            ->orderBy('id_absen', 'desc')
            ->with('jenis')
            ->get();

        // Hari ini ada kuota cuti/libur (jangan ajak user "mulai absen")
        $adaCutiHariIni = $selesaiHariIni->contains(fn($a) => in_array((int) $a->id_jenis_pekerjaan, [12, 17], true));

        // Jenis yang dipakai untuk foto mandiri (sembunyikan CUTI & LIBUR PULANG)
        $sembunyi = [12, 17];
        $jenis = Jenis::whereNotIn('id', $sembunyi)->get();

        // Sisa jatah Cuti Tahunan (12 hari/tahun, Jan-Des).
        // COALESCE(jumlah_hari,1) menghitung format lama (1 baris = N hari) & baru (1 baris = 1 hari).
        $terpakai = (int) Absensi::where('id_karyawan', $id)
            ->where('id_jenis_pekerjaan', 17)
            ->whereBetween('tanggal', [date('Y') . '-01-01', date('Y') . '-12-31'])
            ->selectRaw('COALESCE(SUM(COALESCE(jumlah_hari,1)),0) as total')
            ->value('total');
        $sisaJatah = max(0, 12 - $terpakai);

        // Riwayat per bulan (filter: ?bulan=..&tahun=..)
        $wita = now('Asia/Makassar');
        $bulan = min(12, max(1, (int) $request->query('bulan', $wita->month)));
        $tahun = min($wita->year, max(2020, (int) $request->query('tahun', $wita->year)));
        $awal = "{$tahun}-" . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $akhir = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $riwayatBulan = Absensi::where('id_karyawan', $id)
            ->whereBetween('tanggal', [$awal, $akhir])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_absen', 'desc')
            ->with('jenis')
            ->get();

        // Ringkasan per jenis (cuti menjumlahkan jumlah_hari, lainnya 1 per baris)
        $ringkasan = Absensi::where('id_karyawan', $id)
            ->whereBetween('tanggal', [$awal, $akhir])
            ->join('jenis_pekerjaan', 'jenis_pekerjaan.id', 'absensi.id_jenis_pekerjaan')
            ->selectRaw('jenis_pekerjaan.jenis_pekerjaan as nama, SUM(COALESCE(absensi.jumlah_hari, 1)) as jumlah')
            ->groupBy('jenis_pekerjaan.jenis_pekerjaan')
            ->orderBy('jumlah', 'desc')
            ->get();

        $prev = ['bulan' => $bulan === 1 ? 12 : $bulan - 1, 'tahun' => $bulan === 1 ? $tahun - 1 : $tahun];
        $next = ['bulan' => $bulan === 12 ? 1 : $bulan + 1, 'tahun' => $bulan === 12 ? $tahun + 1 : $tahun];
        $namaBulan = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');

        return view('absen.absen', [
            'karyawan' => $karyawan,
            'sedangBekerja' => $sedangBekerja,
            'selesaiHariIni' => $selesaiHariIni,
            'jenis' => $jenis,
            'sisaJatah' => $sisaJatah,
            'riwayatBulan' => $riwayatBulan,
            'ringkasan' => $ringkasan,
            'namaBulan' => $namaBulan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'prev' => $prev,
            'next' => $next,
            'adaCutiHariIni' => $adaCutiHariIni,
        ]);
    }

    public function addCuti(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:12,17',
            'tanggal_cuti' => 'nullable|array',
            'tanggal_cuti.*' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date',
            'ket' => 'nullable|string|max:255',
        ]);

        $id = session('absen_karyawan.id');
        $jenis_cuti = (int) $request->jenis_cuti;

        // Gabungkan: tanggal terpisah (tanggal_cuti[]) + rentang mulai-sampai.
        $tanggal = array_values(array_filter($request->tanggal_cuti ?? []));

        if ($request->tanggal_mulai) {
            $mulai = new \DateTime($request->tanggal_mulai);
            $sampai = new \DateTime($request->tanggal_sampai ?: $request->tanggal_mulai);
            if ($request->tanggal_sampai && $request->tanggal_sampai < $request->tanggal_mulai) {
                return redirect()->route('absen.index')
                    ->with('error', 'Tanggal sampai tidak boleh sebelum tanggal mulai.')
                    ->with('tab_pilihan', 'cuti');
            }
            while ($mulai <= $sampai) {
                $tanggal[] = $mulai->format('Y-m-d');
                $mulai->modify('+1 day');
            }
        }

        $tanggal = array_values(array_unique($tanggal));
        sort($tanggal);

        if (empty($tanggal)) {
            return redirect()->route('absen.index')
                ->with('error', 'Pilih minimal satu tanggal cuti.')
                ->with('tab_pilihan', 'cuti');
        }

        // Tanggal yang sudah terisi absen lain (harian/lembur/JGM/cuti jenis lain) = bentrok, tolak.
        $bentrok = Absensi::where('id_karyawan', $id)
            ->whereIn('tanggal', $tanggal)
            ->where('id_jenis_pekerjaan', '!=', $jenis_cuti)
            ->pluck('tanggal')->unique()->values()->all();

        if (! empty($bentrok)) {
            $bentrokTampil = array_map(fn($t) => date('d-m-Y', strtotime($t)), $bentrok);
            return redirect()->route('absen.index')
                ->with('error',
                    'Tidak bisa cuti: tanggal ' . implode(', ', $bentrokTampil) . ' sudah ada absen lain. Pilih tanggal lain.')
                ->with('tab_pilihan', 'cuti');
        }

        // Buang tanggal yang sudah tercatat untuk karyawan + jenis yang sama
        $sudahAda = Absensi::where('id_karyawan', $id)
            ->where('id_jenis_pekerjaan', $jenis_cuti)
            ->whereIn('tanggal', $tanggal)
            ->pluck('tanggal')->all();
        $baru = array_values(array_diff($tanggal, $sudahAda));

        if (empty($baru)) {
            return redirect()->route('absen.index')
                ->with('error', 'Semua tanggal cuti ini sudah tercatat.')
                ->with('tab_pilihan', 'cuti');
        }

        $jumlah_hari = count($baru);

        // Sistem mengikuti data lama: setiap tanggal cuti = 1 baris (jumlah_hari = null).
        // Cuti tahunan (17) dibayar maksimal 12 hari per tahun (Jan-Des);
        // hari kelebihan ditandai "TIDAK DIBAYAR" pada baris tanggal tsb.
        $terpakai = (int) Absensi::where('id_karyawan', $id)
            ->where('id_jenis_pekerjaan', 17)
            ->whereBetween('tanggal', [date('Y') . '-01-01', date('Y') . '-12-31'])
            ->selectRaw('COALESCE(SUM(COALESCE(jumlah_hari,1)),0) as total')
            ->value('total');
        $sisa_jatah = max(0, 12 - $terpakai);
        $hari_tidak_dibayar = $jenis_cuti === 17 ? max(0, $jumlah_hari - $sisa_jatah) : 0;

        foreach ($baru as $i => $tgl) {
            $ketRow = trim($request->ket ?? '');
            if ($jenis_cuti === 17 && $i >= $sisa_jatah) {
                $ketRow = trim(($ketRow ? $ketRow . ' | ' : '') . 'TIDAK DIBAYAR (jatah cuti 12 hari habis)');
            }
            Absensi::create([
                'id_karyawan' => $id,
                'id_jenis_pekerjaan' => $jenis_cuti,
                'id_pemakai' => 1,
                'tanggal' => $tgl,
                'jumlah_hari' => null,
                'ket' => $ketRow ?: null,
                'status' => 'selesai',
            ]);
        }

        return redirect()->route('absen.index')
            ->with('tab_pilihan', 'cuti')
            ->with('sukses',
                $hari_tidak_dibayar > 0
                    ? 'Cuti/Libur dicatat. Jatah habis: ' . $hari_tidak_dibayar . ' hari TIDAK DIBAYAR.'
                    : 'Cuti/Libur dicatat (' . $jumlah_hari . ' hari).');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jenis' => 'required|integer',
            'tanggal' => 'required|date',
            'foto' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'ket' => 'nullable|string|max:255',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
        ]);

        // Tanggal tidak boleh masa depan (WITA)
        if ($request->tanggal > now('Asia/Makassar')->toDateString()) {
            return back()->with('error', 'Tanggal tidak boleh di masa depan.');
        }

        $id = session('absen_karyawan.id');
        $jenisId = (int) $request->id_jenis;

        // Jenis "absen harian" (id 9) = normal, boleh 1x per hari (berapa pun statusnya).
        // Jenis lain (lembur/JGM/MTD/libur) = tambahan, boleh baris baru,
        // tapi tidak boleh dobel yang masih "bekerja".
        $cek = Absensi::where('id_karyawan', $id)
            ->where('tanggal', $request->tanggal)
            ->where('id_jenis_pekerjaan', $jenisId);

        if ($jenisId === 9) {
            $sudahAda = $cek->exists();
        } else {
            $sudahAda = (clone $cek)->where('status', 'bekerja')->exists();
        }

        if ($sudahAda) {
            return back()->with('error', 'Sudah ada absen jenis ini pada tanggal tersebut.');
        }

        // Tanggal yang sudah cuti/libur tidak boleh diisi absen lagi.
        $adaCuti = Absensi::where('id_karyawan', $id)
            ->whereIn('id_jenis_pekerjaan', [12, 17])
            ->where('tanggal', $request->tanggal)
            ->exists();
        if ($adaCuti) {
            return back()->with('error', 'Tanggal ini sudah tercatat cuti/libur.');
        }

        $namaJenis = Jenis::where('id', $jenisId)->value('jenis_pekerjaan');
        $fotoPath = $this->simpanFoto($request->file('foto'), 'masuk', $namaJenis);

        $data = [
            'id_karyawan' => $id,
            'id_jenis_pekerjaan' => $request->id_jenis,
            'id_pemakai' => 1,
            'tanggal' => $request->tanggal,
            'ket' => $request->ket,
            'foto_masuk' => $fotoPath,
            'status' => 'bekerja',
        ];

        // LEMBUR (id 8): jam awal/akhir diinput manual oleh karyawan.
        if ($jenisId === 8) {
            $jamMulai = $request->jam_mulai;
            $jamSelesai = $request->jam_selesai;

            if (! $jamMulai || ! $jamSelesai) {
                return back()->with('error', 'Untuk lembur, isi jam mulai dan jam selesai.');
            }

            $data['jam_masuk'] = $request->tanggal . ' ' . $jamMulai . ':00';

            // Keterangan diisi sendiri oleh karyawan; jam lembur tersimpan
            // di kolom jam_masuk / jam_selesai (bukan di ket).
            $data['ket'] = $request->ket;

            // tag agar saat tombol selesai tahu jam selesai manual
            $data['jam_selesai'] = $request->tanggal . ' ' . $jamSelesai . ':00';
        } else {
            // Semua jenis lain: jam masuk = waktu tekan tombol (WITA)
            $data['jam_masuk'] = now('Asia/Makassar')->format('Y-m-d H:i:s');
        }

        Absensi::create($data);

        return redirect()->route('absen.index')->with('sukses', 'Absen masuk tersimpan.');
    }

    public function selesai(Request $request)
    {
        $request->validate([
            'id_absen' => 'required|integer',
            'foto' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'foto_lembur' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'ket_lembur' => 'nullable|string|max:255',
        ]);

        $id = session('absen_karyawan.id');
        $absen = Absensi::where('id_absen', $request->id_absen)
            ->where('id_karyawan', $id)
            ->where('status', 'bekerja')
            ->first();

        if (! $absen) {
            return back()->with('error', 'Absen tidak ditemukan atau sudah selesai.');
        }

        $namaJenis = Jenis::where('id', $absen->id_jenis_pekerjaan)->value('jenis_pekerjaan');
        $fotoPath = $this->simpanFoto($request->file('foto'), 'selesai', $namaJenis);

        $update = [
            'foto_selesai' => $fotoPath,
            'status' => 'selesai',
        ];

        // LEMBUR (id 8): jam selesai sudah di-set manual saat absen masuk.
        if ((int) $absen->id_jenis_pekerjaan !== 8) {
            // Jenis lain: jam selesai = waktu tekan tombol (WITA)
            $update['jam_selesai'] = now('Asia/Makassar')->format('Y-m-d H:i:s');
        }

        $absen->update($update);

        $pesan = 'Absen selesai disimpan.';

        // Lembur dadakan setelah kerja normal: buat BARIS LEMBUR terpisah (jenis 8),
        // sesuai data lama (tidak digabung ke baris absen normal).
        if ((int) $request->lembur === 1 && (int) $absen->id_jenis_pekerjaan !== 8) {
            $wita = now('Asia/Makassar');
            $jamKlaim = $request->jam_lembur ?: $wita->format('H:i');

            // Foto selesai lembur wajib diambil di dialog.
            if (! $request->hasFile('foto_lembur')) {
                return back()->with('error', 'Ambil dulu foto selesai lembur.');
            }

            $fotoLembur = $this->simpanFoto($request->file('foto_lembur'), 'selesai', 'Lembur');

            Absensi::create([
                'id_karyawan' => $id,
                'id_jenis_pekerjaan' => 8,
                'id_pemakai' => $absen->id_pemakai ?? 1,
                'tanggal' => $absen->tanggal,
                'ket' => $request->ket_lembur,
                'status' => 'selesai',
                'jam_masuk' => $wita->format('Y-m-d H:i:s'),
                'jam_selesai' => $absen->tanggal . ' ' . $jamKlaim . ':00',
                'foto_selesai' => $fotoLembur,
                'jumlah_hari' => null,
            ]);

            $pesan = 'Absen selesai & baris Lembur baru ditambahkan.';
        }

        return redirect()->route('absen.index')->with('sukses', $pesan);
    }

    private function simpanFoto($file, $jenis, $namaJenis = null)
    {
        $nama = Karyawan::where('id_karyawan', session('absen_karyawan.id'))->value('nama_karyawan');

        try {
            $img = \Intervention\Image\Facades\Image::make($file);
        } catch (\Exception $e) {
            $img = null;
        }

        $waktu = now('Asia/Makassar');
        $stamp = $waktu->format('Ymd_His');
        $namaFile = 'absen/' . $stamp . '_' . $jenis . '_' . uniqid() . '.jpg';

        if ($img) {
            $img->orientate();

            // Watermark header (anti-palsu): jenis pekerjaan + timestamp di atas foto,
            // huruf besar tebal tanpa latar.
            $label = $namaJenis ? strtoupper($namaJenis) : 'ABSEN ' . strtoupper($jenis);
            $barisWaktu = $waktu->format('d-m-Y H:i:s') . ' WITA';
            try {
                $fontPath = $this->cariFontTtf($label . ' ' . $barisWaktu);
                if ($fontPath) {
                    // Ukuran font besar, proporsional dengan lebar foto (resolusi HP tinggi)
                    $fontSize = max(56, (int) round($img->width() * 0.03));
                    $centerX = (int) round($img->width() / 2);
                    $marginTop = (int) round($fontSize * 0.5);

                    $img->text($label, $centerX, $marginTop, function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->color([255, 255, 255]);
                        $font->align('center');
                        $font->valign('top');
                    });
                    $bbox = imagettfbbox($fontSize, 0, $fontPath, $label);
                    $th = $bbox[1] - $bbox[7];
                    $img->text($barisWaktu, $centerX, $marginTop + $th + (int) round($fontSize * 0.2), function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->color([255, 255, 255]);
                        $font->align('center');
                        $font->valign('top');
                    });
                }
            } catch (\Exception $e) {
                // abaikan watermark bila gagal, timestamp tetap di DB
            }

            $img->encode('jpg', 80);
            Storage::disk('public')->put($namaFile, $img->getEncoded());
        } else {
            $contents = file_get_contents($file->getRealPath());
            Storage::disk('public')->put($namaFile, $contents);
        }

        return 'storage/' . $namaFile;
    }

    private function cariFontTtf($teks)
    {
        $kandidat = [
            resource_path('fonts/arialbd.ttf'),
            resource_path('fonts/DejaVuSans-Bold.ttf'),
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
        ];

        foreach ($kandidat as $path) {
            if (! file_exists($path)) {
                continue;
            }
            // pastikan font benar-benar bisa menggambar glyph (bukan icon font)
            $bbox = @imagettfbbox(28, 0, $path, $teks);
            if ($bbox === false || ($bbox[2] - $bbox[0]) <= 5) {
                continue;
            }
            return $path;
        }

        return null;
    }
}
