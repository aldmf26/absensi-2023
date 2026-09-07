<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahHariToAbsensi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('absensi', 'jumlah_hari')) {
            return;
        }
        Schema::table('absensi', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_hari')->nullable()->after('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('absensi', 'jumlah_hari')) {
            Schema::table('absensi', function (Blueprint $table) {
                $table->dropColumn('jumlah_hari');
            });
        }
    }
}