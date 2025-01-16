<?php

use App\Http\Controllers\Api\DataPegawaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('karyawan', function () {
    $query = '23';
    $data = [
        'karyawan' => $query,
    ];
    return response()->json($data, HttpFoundationResponse::HTTP_OK);
});
Route::get('/data-pegawai', [DataPegawaiController::class, 'index']);
