<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Data lama yang diinput admin (tanpa foto) bukan bagian alur "bekerja".
        // Tandai sebagai selesai agar tidak tampil sebagai absen berjalan.
        DB::statement("UPDATE absensi SET status = 'selesai' WHERE status = 'bekerja' AND foto_masuk IS NULL");
    }

    public function down()
    {
        // tidak bisa dikembalikan: tanpa snapshot status lama
    }
};