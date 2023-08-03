<?php

use App\Http\Controllers\AbsensiAgrilaras;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiRestoController;
use App\Http\Controllers\AbsensiSalonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InputJqController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KasbonAgrilarasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PemakaiController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\tabelAgrilaras;
use App\Http\Controllers\TabelRestoController;
use App\Http\Controllers\TabelSalonController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth')->group(function () {
    Route::controller(KaryawanController::class)
        ->group(function () {
            Route::get('/karyawan', 'index')->name('karyawan');
            Route::post('/karyawan', 'addKaryawan')->name('addKaryawan');
            Route::patch('/karyawan', 'editKaryawan')->name('editKaryawan');
            Route::post('/delete-karyawan', 'deleteKaryawan')->name('deleteKaryawan');
            Route::get('/excelKaryawan', 'excelKaryawan')->name('excelKaryawan');
            Route::get('/excelKaryawanAgrilaras', 'excelKaryawanAgrilaras')->name('excelKaryawanAgrilaras');
            Route::post('/importKaryawan', 'importKaryawan')->name('importKaryawan');
            Route::get('/karyawanAgrilaras', 'karyawanAgrilaras')->name('karyawanAgrilaras');
        });

    Route::controller(JenisController::class)
        ->group(function () {
            Route::get('/jenis', 'index')->name('jenis');
            Route::post('/jenis', 'addJenis')->name('addJenis');
            Route::patch('/jenis', 'editJenis')->name('editJenis');
            Route::post('/delete-jenis', 'deleteJenis')->name('deleteJenis');
        });

    Route::controller(ShiftController::class)
        ->group(function () {
            Route::get('/shift',  'index')->name('shift');
            Route::post('/shift',  'addShift')->name('addShift');
            Route::patch('/shift',  'editShift')->name('editShift');
            Route::post('/delete-shift',  'deleteShift')->name('deleteShift');
        });

    Route::controller(PemakaiController::class)
        ->group(function () {
            Route::get('/pemakai',  'index')->name('pemakai');
            Route::post('/pemakai',  'addPemakai')->name('addPemakai');
            Route::patch('/pemakai',  'editPemakai')->name('editPemakai');
            Route::post('/delete-pemakai',  'deletePemakai')->name('deletePemakai');
        });

    Route::controller(AbsensiController::class)
        ->group(function () {
            Route::get('/absensi',  'index')->name('absensi');
            Route::get('/absensi_edit/{id}',  'absensi_edit')->name('absensi_edit');
            Route::get('/detail-absensi',  'detailAbsensi')->name('detailAbsensi');
            Route::post('/absensi',  'addAbsensi')->name('addAbsensi');
            Route::patch('/absensi',  'editAbsensi')->name('editAbsensi');
            Route::post('/delete-absensi',  'deleteAbsensi')->name('deleteAbsensi');
            Route::get('/excel',  'excel')->name('excel');
            Route::get('/exportPertanggal',  'exportPertanggal')->name('exportPertanggal');
            Route::get('/hapusPertanggal',  'hapusPertanggal')->name('hapusPertanggal');
        });

    Route::controller(AbsensiRestoController::class)
        ->group(function () {
            Route::get('/absensi_resto',  'detail_resto')->name('absensi_resto');
            Route::post('/input_resto',  'input_resto')->name('input_resto');
            Route::post('/update_resto',  'update_resto')->name('update_resto');
            Route::get('/delete_resto',  'delete_resto')->name('delete_resto');
            Route::get('/detail_resto',  'detail_resto')->name('detail_resto');
            Route::post('/add_resto',  'add_resto')->name('add_resto');
            Route::patch('/edit_resto',  'edit_resto')->name('edit_resto');
            Route::post('/delete_resto',  'delete_resto')->name('delete_resto');
            Route::get('/add_edit_resto',  'add_edit_resto')->name('add_edit_resto');
        });

    Route::controller(AbsensiSalonController::class)
        ->group(function () {
            Route::get('/absensi_salon',  'detail_salon')->name('absensi_salon');
            Route::post('/input_salon',  'input_salon')->name('input_salon');
            Route::get('/update_salon',  'update_salon')->name('update_salon');
            Route::get('/delete_salon',  'delete_salon')->name('delete_salon');
            Route::get('/detail_salon',  'detail_salon')->name('detail_salon');
            Route::post('/add_salon',  'add_salon')->name('add_salon');
            Route::patch('/edit_salon',  'edit_salon')->name('edit_salon');
            Route::post('/delete_salon',  'delete_salon')->name('delete_salon');
            Route::get('/add_edit_salon',  'add_edit_salon')->name('add_edit_salon');
        });

    Route::controller(AbsensiAgrilaras::class)
        ->group(function () {
            Route::get('/tabelAbsenM',  'tabelAbsenM')->name('tabelAbsenM');
            Route::get('/addAbsenM',  'addAbsenM')->name('addAbsenM');
            Route::post('/updateAbsenM',  'updateAbsenM')->name('updateAbsenM');
            Route::post('/deleteAbsenM',  'deleteAbsenM')->name('deleteAbsenM');
            Route::get('/absensi_agrilaras',  'detail_agrilaras')->name('absensi_agrilaras');
            Route::get('/exportGajiNANDA',  'exportGajiNANDA')->name('exportGajiNANDA');
            Route::get('/downloadAbsAgri',  'downloadAbsAgri')->name('downloadAbsAgri');
            Route::get('/detail_agrilaras',  'detail_agrilaras')->name('detail_agrilaras');
            Route::post('/delete_agrilaras',  'delete_agrilaras')->name('delete_agrilaras');
            Route::post('/update_agrilaras',  'update_agrilaras')->name('update_agrilaras');

            Route::post('/input_agrilaras',  'input_agrilaras')->name('input_agrilaras');
            Route::get('/ubah_bulan',  'ubah_bulan')->name('ubah_bulan');

            Route::get('/editAbsen',  'editAbsen')->name('editAbsen');
            Route::get('/addAbsen',  'addAbsen')->name('addAbsen');
        });

    Route::controller(UserController::class)
        ->group(function () {
            Route::get('/users',  'index')->name('users');
            Route::post('/users',  'addUser')->name('addUser');
            Route::patch('/users',  'editUser')->name('editUser');
            Route::post('/delete-users',  'deleteUser')->name('deleteUser');
        });

    Route::controller(LoginController::class)
        ->group(function () {
            Route::get('/',  'index')->name('login');
            Route::post('/askiLogin',  'aksiLogin')->name('aksiLogin');
            Route::get('/register',  'register')->name('register');
            Route::post('/aksiReg',  'aksiReg')->name('aksiReg');
            Route::get('/aksiLogout',  'aksiLogout')->name('aksiLogout');
        });

    Route::controller(tabelAgrilaras::class)
        ->group(function () {
            Route::get('/lembur', 'lembur')->name('lembur');
            Route::get('/tambah_lembur', 'tambah_lembur')->name('tambah_lembur');
            Route::get('/edit_data', 'edit_data')->name('edit_data');
            Route::get('/save_absen_lembur', 'save_absen_lembur')->name('save_absen_lembur');
            Route::get('/edit_absen_lembur', 'edit_absen_lembur')->name('edit_absen_lembur');
            Route::get('/delete_absen_lembur', 'hapus_data')->name('delete_absen_lembur');
            Route::get('/exportLembur', 'exportLembur')->name('exportLembur');
        });

    Route::controller(KasbonAgrilarasController::class)
        ->group(function () {
            Route::get('/kasbonAgrilaras', 'index')->name('kasbonAgrilaras');
            Route::post('/kasbonAgrilaras', 'create')->name('kasbonAgrilaras.create');
            Route::get('/kasbon/btn_tambah', 'btn_tambah');
            Route::get('/kasbonAgrilaras/delete', 'delete')->name('kasbonAgrilaras.delete');
            Route::get('/kasbonAgrilaras/edit', 'edit')->name('kasbonAgrilaras.edit');
            Route::get('/kasbonAgrilaras/print', 'print')->name('kasbonAgrilaras.print');
            Route::post('/kasbonAgrilaras/update', 'update')->name('kasbonAgrilaras.update');
        });
        
    Route::controller(DendaController::class)
        ->group(function () {
            Route::get('/denda', 'index')->name('denda');
            Route::post('/denda', 'create')->name('denda.create');
            Route::get('/denda/btn_tambah', 'btn_tambah');
            Route::get('/denda/delete', 'delete')->name('denda.delete');
            Route::get('/denda/edit', 'edit')->name('denda.edit');
            Route::get('/denda/print', 'print')->name('denda.print');
            Route::post('/denda/update', 'update')->name('denda.update');
        });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tabelResto', [TabelRestoController::class, 'index'])->name('tabelResto');
    // tabel salon
    Route::get('/tabelSalon', [TabelSalonController::class, 'index'])->name('tabelSalon');
    // tabel agrilaras
    Route::get('/tabelAgrilaras', [tabelAgrilaras::class, 'index'])->name('tabelAgrilaras');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::post('/menu', [MenuController::class, 'tambahMenu'])->name('tambahMenu');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/input', [InputJqController::class, 'index'])->name('input');
    Route::post('/simpan', [InputJqController::class, 'simpan'])->name('simpan');

    Route::get('/gajiAgrilaras', [AbsensiAgrilaras::class, 'gajiAgrilaras'])->name('gajiAgrilaras');
    Route::get('/exportGaji', [AbsensiAgrilaras::class, 'exportGaji'])->name('exportGaji');
// });
// -----
