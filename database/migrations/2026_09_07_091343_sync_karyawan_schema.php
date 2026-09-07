<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Menyelaraskan skema tabel karyawan dgn kondisi DB production
        // (kolom id_departemen & posisi ada; kolom lama "departemen" dihapus).
        Schema::table('karyawan', function (Blueprint $table) {
            if (! Schema::hasColumn('karyawan', 'id_departemen')) {
                $table->integer('id_departemen')->nullable()->after('tanggal_masuk');
            }
            if (! Schema::hasColumn('karyawan', 'posisi')) {
                $table->string('posisi', 200)->nullable()->after('id_departemen');
            }
            if (Schema::hasColumn('karyawan', 'departemen')) {
                $table->dropColumn('departemen');
            }
        });

        // Menyelaraskan tabel absensi dengan struktur production
        Schema::table('absensi', function (Blueprint $table) {
            // Kolom legacy migrasi awal dihapus
            foreach (['nama_karyawan', 'username', 'password'] as $kolom) {
                if (Schema::hasColumn('absensi', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
            // Production memakai PK 'id_absen'. Migrasi awal memakai 'id'.
            // Untuk DB segar (test), samakan dengan production bila 'id' masih ada.
            if (! Schema::hasColumn('absensi', 'id_absen') && Schema::hasColumn('absensi', 'id')) {
                $driver = DB::getDriverName();
                if ($driver === 'sqlite') {
                    DB::statement('ALTER TABLE absensi RENAME COLUMN id TO id_absen');
                } elseif ($driver === 'mysql') {
                    DB::statement('ALTER TABLE absensi CHANGE id id_absen INT UNSIGNED NOT NULL AUTO_INCREMENT');
                }
            }
            if (! Schema::hasColumn('absensi', 'id_jenis_pekerjaan')) {
                $table->integer('id_jenis_pekerjaan')->nullable();
            }
            if (! Schema::hasColumn('absensi', 'id_pemakai')) {
                $table->integer('id_pemakai')->nullable();
            }
            if (! Schema::hasColumn('absensi', 'tanggal')) {
                $table->date('tanggal')->nullable();
            }
            if (! Schema::hasColumn('absensi', 'ket')) {
                $table->string('ket', 255)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn(['id_departemen', 'posisi']);
        });
    }
};