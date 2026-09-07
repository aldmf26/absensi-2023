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

    public function index()
    {
        $id = session('absen_karyawan.id');
        $karyawan = Karyawan::find($id);

        // Record hari ini yang masih "sedang bekerja" untuk jenis apapun
        $sedangBekerja = Absensi::where('id_karyawan', $id)
            ->where('status', 'bekerja')
            ->orderBy('id_absen', 'desc')
            ->with('karyawan')
            ->get();

        // Riwayat / yang sudah selesai hari ini
        $riwayat = Absensi::where('id_karyawan', $id)
            ->where('status', 'selesai')
            ->whereDate('tanggal', now('Asia/Makassar')->toDateString())
            ->orderBy('id_absen', 'desc')
            ->with('karyawan')
            ->get();

        // Jenis yang dipakai untuk foto mandiri (sembunyikan CUTI & LIBUR PULANG)
        $sembunyi = [12, 17];
        $jenis = Jenis::whereNotIn('id', $sembunyi)->get();

        return view('absen.absen', [
            'karyawan' => $karyawan,
            'sedangBekerja' => $sedangBekerja,
            'riwayat' => $riwayat,
            'jenis' => $jenis,
        ]);
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

        $fotoPath = $this->simpanFoto($request->file('foto'), 'masuk');

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

            // simpan jam selesai manual di ket bila tidak diisi nanti saat selesai
            $data['ket'] = trim(($request->ket ? $request->ket . ' | ' : '') . 'Mulai ' . $jamMulai . ' - Selesai ' . $jamSelesai);

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
        ]);

        $id = session('absen_karyawan.id');
        $absen = Absensi::where('id_absen', $request->id_absen)
            ->where('id_karyawan', $id)
            ->where('status', 'bekerja')
            ->first();

        if (! $absen) {
            return back()->with('error', 'Absen tidak ditemukan atau sudah selesai.');
        }

        $fotoPath = $this->simpanFoto($request->file('foto'), 'selesai');

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

        return redirect()->route('absen.index')->with('sukses', 'Absen selesai disimpan.');
    }

    private function simpanFoto($file, $jenis)
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

            // Watermark timestamp (anti-palsu): teks putih + latar gelap solid
            // agar selalu terlihat walau foto berlatar terang.
            $text = 'Absen ' . strtoupper($jenis) . ' | ' . $waktu->format('d-m-Y H:i:s') . ' WITA';
            try {
                $fontPath = $this->cariFontTtf($text);
                if ($fontPath) {
                    $fontSize = 32;
                    // perkiraan lebar/tinggi teks untuk kotak latar
                    $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
                    $tw = $bbox[2] - $bbox[0];
                    $th = $bbox[1] - $bbox[7];
                    $x = 18;
                    $y = 18;
                    $pad = 10;

                    $img->rectangle($x - $pad, $y - $pad, $x + $tw + $pad, $y + $th + $pad, function ($draw) {
                        $draw->background([30, 30, 45]);
                    });
                    $img->text($text, $x, $y, function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->color([255, 255, 255]);
                        $font->align('left');
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
