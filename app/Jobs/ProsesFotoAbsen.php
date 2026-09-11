<?php

namespace App\Jobs;

use App\Models\Absensi;
use App\Support\FotoAbsenProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

        $namaJenis = $absen->jenis ? $absen->jenis->jenis_pekerjaan : null;
        $kolomWaktu = $this->kolom === 'foto_masuk'
            ? ($absen->jam_masuk ?: $absen->created_at)
            : ($absen->jam_selesai ?: $absen->created_at);
        $waktu = $kolomWaktu ? \Carbon\Carbon::parse($kolomWaktu) : now('Asia/Makassar');

        FotoAbsenProcessor::proses($this->path, $this->kolom, $namaJenis, $waktu);
    }
}