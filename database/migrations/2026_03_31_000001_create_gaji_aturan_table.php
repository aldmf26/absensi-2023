<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGajiAturanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gaji_aturan', function (Blueprint $table) {
            $table->id();
            $table->string('posisi'); // Anak Kandang, Supir, Admin, dll
            $table->integer('masa_kerja'); // 0, 1, 2, 3+ tahun
            $table->decimal('rp_harian', 15, 0);
            $table->decimal('rp_bulanan', 15, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gaji_aturan');
    }
}
