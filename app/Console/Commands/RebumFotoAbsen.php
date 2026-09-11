<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Support\FotoAbsenProcessor;
use Illuminate\Console\Command;

class RebumFotoAbsen extends Command
{
    protected $signature = 'absen:reburn-foto {--only-foto-masuk} {--only-foto-selesai}';

    protected $description = 'Proses ulang watermark/resize SEMUA foto absen yang sudah ada (sekali saja).';

    public function handle(): int
    {
        $rows = Absensi::with('jenis')->get()->filter(function ($a) {
            $ada = $a->foto_masuk && ! str_starts_with($a->foto_masuk, 'http');
            $ada2 = $a->foto_selesai && ! str_starts_with($a->foto_selesai, 'http');
            return $ada || $ada2;
        });

        $this->info('Total baris absen dengan foto: ' . $rows->count());

        $ok = $gagal = $skip = 0;

        foreach ($rows as $abs) {
            $namaJenis = $abs->jenis ? $abs->jenis->jenis_pekerjaan : null;

            foreach (['foto_masuk', 'foto_selesai'] as $kolom) {
                if ($this->option('only-foto-masuk') && $kolom !== 'foto_masuk') {
                    continue;
                }
                if ($this->option('only-foto-selesai') && $kolom !== 'foto_selesai') {
                    continue;
                }

                $path = $abs->{$kolom} ?? null;
                if (! $path || str_starts_with($path, 'http')) {
                    $skip++;
                    continue;
                }

                $storagePath = str_replace('storage/', '', $path);
                $waktu = $kolom === 'foto_masuk'
                    ? ($abs->jam_masuk ?: $abs->created_at)
                    : ($abs->jam_selesai ?: $abs->created_at);

                if (FotoAbsenProcessor::proses($storagePath, $kolom, $namaJenis, $waktu)) {
                    $ok++;
                } else {
                    $gagal++;
                    $this->warn("  Gagal: #{$abs->id_absen} {$kolom} {$storagePath}");
                }
            }
        }

        $this->info("Selesai. Diproses: {$ok}, gagal: {$gagal}, dilewati: {$skip}.");

        return self::SUCCESS;
    }
}