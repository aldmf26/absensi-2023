<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class FotoAbsenProcessor
{
    /**
     * Proses satu foto absen: orientasi, resize, watermark besar di bawah, encode.
     *
     * @param string $path path relatif disk 'public', mis. "absen/xxx.jpg"
     * @param string $kolom  foto_masuk | foto_selesai
     * @param string|null $namaJenis label jenis pekerjaan
     * @param \Carbon\Carbon|null $waktu waktu yang dicetak di watermark
     */
    public static function proses(string $path, string $kolom, $namaJenis = null, $waktu = null): bool
    {
        $full = Storage::disk('public')->path($path);

        try {
            $img = \Intervention\Image\Facades\Image::make($full);
        } catch (\Exception $e) {
            return false;
        }

        $img->orientate();

        // Perkecil dulu: cepat & ringan, foto absen tak butuh megapixel penuh.
        $img->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $waktu = $waktu ?: now('Asia/Makassar');
        $label = $namaJenis ? strtoupper($namaJenis) : strtoupper($kolom);
        $barisWaktu = $waktu->setTimezone('Asia/Makassar')->format('d-m-Y H:i:s') . ' WITA';

        try {
            $fontPath = (new self())->cariFontTtf($label . ' ' . $barisWaktu);
            if ($fontPath) {
                self::gambarWatermark($img, $fontPath, $label, $barisWaktu);
            }
        } catch (\Exception $e) {
            // abaikan watermark bila gagal; foto tetap tersimpan
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            $img->encode('jpg', 80);
        } else {
            $img->encode('png');
        }

        Storage::disk('public')->put($path, $img->getEncoded());

        return true;
    }

    /**
     * Stempel besar di bawah foto (±1/3 tinggi) dengan band gelap transparan.
     */
    private static function gambarWatermark($img, string $fontPath, string $label, string $barisWaktu): void
    {
        // Ukuran font ~11% tinggi foto, hasil: blok 2 baris ≈ 1/3 tinggi gambar.
        $fontSize = max(64, (int) round($img->height() * 0.11));
        $centerX = (int) round($img->width() / 2);

        $h1 = self::tinggiTeks($fontSize, $fontPath, $label);
        $h2 = self::tinggiTeks($fontSize, $fontPath, $barisWaktu);
        $gap = (int) round($fontSize * 0.18);
        $pad = (int) round($fontSize * 0.6);
        $marginBawah = (int) round($img->height() * 0.04);

        $bandTop = $img->height() - $marginBawah - $pad - $h1 - $gap - $h2 - $pad;

        // Band gelap transparan agar teks selalu terbaca
        $img->rectangle(0, $bandTop, $img->width() - 1, $img->height() - 1, function ($draw) {
            $draw->background([0, 0, 0, 0.55]);
        });

        $y1 = $bandTop + $pad + $h1;
        $y2 = $y1 + $gap + $h2;

        self::gambarBaris($img, $fontPath, $fontSize, $label, $centerX, $y1);
        self::gambarBaris($img, $fontPath, $fontSize, $barisWaktu, $centerX, $y2);
    }

    private static function gambarBaris($img, string $fontPath, int $fontSize, string $teks, int $centerX, int $baseline): void
    {
        // Bayangan gelap tipis di belakang teks agar kontras di band
        $img->text($teks, $centerX + 2, $baseline + 2, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color([0, 0, 0, 0.45]);
            $font->align('center');
            $font->valign('bottom');
        });
        $img->text($teks, $centerX, $baseline, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->color([255, 255, 255]);
            $font->align('center');
            $font->valign('bottom');
        });
    }

    private static function tinggiTeks(int $fontSize, string $fontPath, string $teks): int
    {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $teks);
        return $bbox ? ($bbox[1] - $bbox[7]) : 0;
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