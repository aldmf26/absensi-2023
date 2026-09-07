<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('pin_absen', 250)->nullable()->unique()->after('posisi');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->text('foto_masuk')->nullable()->after('ket');
            $table->text('foto_selesai')->nullable()->after('foto_masuk');
            $table->string('status', 20)->default('bekerja')->after('foto_selesai');
            $table->dateTime('jam_masuk')->nullable()->after('status');
            $table->dateTime('jam_selesai')->nullable()->after('jam_masuk');
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropUnique(['pin_absen']);
            $table->dropColumn('pin_absen');
        });

        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['foto_masuk', 'foto_selesai', 'status', 'jam_masuk', 'jam_selesai']);
        });
    }
};
