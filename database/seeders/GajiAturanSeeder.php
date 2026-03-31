<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GajiAturan;

class GajiAturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Anak Kandang
            ['posisi' => 'Anak Kandang', 'masa_kerja' => 0, 'rp_harian' => 80000, 'rp_bulanan' => 2080000],
            ['posisi' => 'Anak Kandang', 'masa_kerja' => 1, 'rp_harian' => 90000, 'rp_bulanan' => 2340000],
            ['posisi' => 'Anak Kandang', 'masa_kerja' => 2, 'rp_harian' => 100000, 'rp_bulanan' => 2600000],
            ['posisi' => 'Anak Kandang', 'masa_kerja' => 3, 'rp_harian' => 110000, 'rp_bulanan' => 2860000],

            // Supir
            ['posisi' => 'Supir', 'masa_kerja' => 0, 'rp_harian' => 100000, 'rp_bulanan' => 2600000],
            ['posisi' => 'Supir', 'masa_kerja' => 1, 'rp_harian' => 115000, 'rp_bulanan' => 2990000],
            ['posisi' => 'Supir', 'masa_kerja' => 2, 'rp_harian' => 130000, 'rp_bulanan' => 3380000],
            ['posisi' => 'Supir', 'masa_kerja' => 3, 'rp_harian' => 145000, 'rp_bulanan' => 3770000],

            // Admin
            ['posisi' => 'admin', 'masa_kerja' => 0, 'rp_harian' => 100000, 'rp_bulanan' => 2600000],
            ['posisi' => 'admin', 'masa_kerja' => 1, 'rp_harian' => 120000, 'rp_bulanan' => 3120000],
            ['posisi' => 'admin', 'masa_kerja' => 2, 'rp_harian' => 140000, 'rp_bulanan' => 3640000],

            // Tukang Masak
            ['posisi' => 'Tukang Masak', 'masa_kerja' => 0, 'rp_harian' => 90000, 'rp_bulanan' => 2340000],
            ['posisi' => 'Tukang Masak', 'masa_kerja' => 1, 'rp_harian' => 105000, 'rp_bulanan' => 2730000],
            ['posisi' => 'Tukang Masak', 'masa_kerja' => 2, 'rp_harian' => 120000, 'rp_bulanan' => 3120000],

            // Kandang Asisten
            ['posisi' => 'Kandang asisten', 'masa_kerja' => 0, 'rp_harian' => 85000, 'rp_bulanan' => 2210000],
            ['posisi' => 'Kandang asisten', 'masa_kerja' => 1, 'rp_harian' => 95000, 'rp_bulanan' => 2470000],
            ['posisi' => 'Kandang asisten', 'masa_kerja' => 2, 'rp_harian' => 105000, 'rp_bulanan' => 2730000],

            // Kandang Manajer
            ['posisi' => 'kandang manajer', 'masa_kerja' => 0, 'rp_harian' => 150000, 'rp_bulanan' => 3900000],
            ['posisi' => 'kandang manajer', 'masa_kerja' => 1, 'rp_harian' => 170000, 'rp_bulanan' => 4420000],
            ['posisi' => 'kandang manajer', 'masa_kerja' => 2, 'rp_harian' => 190000, 'rp_bulanan' => 4940000],
        ];

        foreach ($data as $item) {
            GajiAturan::create($item);
        }
    }
}
