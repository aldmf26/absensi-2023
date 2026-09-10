<?php

namespace App\Jobs;

use App\Models\Absensi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProsesFotoAbsen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param int $idAbsen
     * @param string $kolom kolom DB yang memegang path foto (foto_masuk / foto_selesai)
     * @param string $path path relatif disk 'public', mis. "absen/20260910_080000_masuk_x.jpg"
     */
    public function __construct(public int $idAbsen, public string $kolom, public string $path)
    {
    }

    public function handle(): void
    {
        $absen = Absensi::where('id_absen', $this->idAbsen)->first();
        if (! $absen) {
            return;
        }

        $full = Storage::disk('public')->path($this->path);

        try {
            $img = \Intervention\Image\Facades\Image::make($full);
        } catch (\Exception $e) {
            // Bukan gambar yang bisa diproses (mis. HEIC di luar Safari) -> simpan apa adanya.
            return;
        }

        $img->orientate();

        // Perkecil dulu sebelum diproses/encode: jauh lebih cepat, foto absen tak perlu megapixel penuh.
        $img->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Watermark header (anti-palsu): jenis pekerjaan + timestamp di atas foto.
        $namaJenis = $absen->jenis ? $absen->jenis->jenis_pekerjaan : null;
        $kolomWaktu = $this->kolom === 'foto_masuk'
            ? ($absen->jam_masuk ?: $absen->created_at)
            : ($absen->jam_selesai ?: $absen->created_at);
        $waktu = $kolomWaktu ? \Carbon\Carbon::parse($kolomWaktu) : now('Asia/Makassar');
        $label = $namaJenis ? strtoupper($namaJenis) : strtoupper($this->kolom);
        $barisWaktu = $waktu->setTimezone('Asia/Makassar')->format('d-m-Y H:i:s') . ' WITA';

        try {
            $fontPath = $this->cariFontTtf($label . ' ' . $barisWaktu);
            if ($fontPath) {
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
            // abaikan watermark bila gagal; foto tetap tersimpan
        }

        $ext = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            $img->encode('jpg', 80);
        } else {
            $img->encode('png');
        }

        Storage::disk('public')->put($this->path, $img->getEncoded());
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
            $bbox = @imagettfbbox(28, 0, $path, $teks);
            if ($bbox === false || ($bbox[2] - $bbox[0]) <= 5) {
                continue;
            }
            return $path;
        }

        return null;
    }
}