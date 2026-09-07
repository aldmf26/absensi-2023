<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AbsenKaryawanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan migration kolom baru sudah ada
        $this->artisan('migrate:fresh');
        Storage::fake('public');
    }

    public function test_karyawan_bisa_login_dengan_pin()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);

        $res = $this->get('/absen/login');
        $res->assertOk();

        $res = $this->post('/absen/login', ['pin' => '1234']);
        $res->assertRedirect('/absen');

        $this->assertTrue(session()->has('absen_karyawan.id'));
    }

    public function test_pin_salah_ditolak()
    {
        Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);

        $res = $this->post('/absen/login', ['pin' => '9999']);
        $res->assertSessionHas('error');
        $this->assertFalse(session()->has('absen_karyawan.id'));
    }

    public function test_absen_harian_normal_satu_kali_per_hari()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);

        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $foto = UploadedFile::fake()->image('masuk.jpg', 600, 800);

        $res = $this->post('/absen', [
            'id_jenis' => 9, // absen harian
            'tanggal' => now()->toDateString(),
            'foto' => $foto,
            'ket' => 'masuk pagi',
        ]);

        $res->assertRedirect('/absen');
        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 9,
            'status' => 'bekerja',
        ]);

        // coba lagi -> harus ditolak
        session()->forget('absen_karyawan');
        session(['absen_karyawan.id' => $kar->id_karyawan]);
        $res2 = $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('masuk2.jpg'),
            'ket' => 'coba dobel',
        ]);
        $res2->assertSessionHas('error');
    }

    public function test_tambahan_lembur_boleh_saat_harian_bekerja()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // absen harian
        $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('harian.jpg'),
        ]);

        // lembur (id 8) saat harian masih bekerja -> berlaku
        $res = $this->post('/absen', [
            'id_jenis' => 8,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('lembur.jpg'),
            'jam_mulai' => '18:00',
            'jam_selesai' => '21:30',
        ]);
        $res->assertRedirect('/absen');

        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 8,
            'status' => 'bekerja',
        ]);

        // lembur catat jam masuk dari input manual
        $lembur = Absensi::where('id_karyawan', $kar->id_karyawan)->where('id_jenis_pekerjaan', 8)->first();
        $this->assertNotNull($lembur->jam_masuk);
        $this->assertEquals('18:00', \Carbon\Carbon::parse($lembur->jam_masuk)->format('H:i'));
        $this->assertEquals('21:30', \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i'));
    }

    public function test_lembur_tanpa_jam_ditolak()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $res = $this->post('/absen', [
            'id_jenis' => 8,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('lembur.jpg'),
        ]);
        $res->assertSessionHas('error');
    }

    public function test_selesai_absen_update_status_dan_foto()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('masuk.jpg'),
        ]);

        $absen = Absensi::where('id_karyawan', $kar->id_karyawan)->first();

        $res = $this->post('/absen/selesai', [
            'id_absen' => $absen->id_absen,
            'foto' => UploadedFile::fake()->image('selesai.jpg'),
        ]);

        $res->assertRedirect('/absen');
        $absen->refresh();
        $this->assertEquals('selesai', $absen->status);
        $this->assertNotNull($absen->foto_selesai);
        $this->assertNotNull($absen->foto_masuk);
        $this->assertNotNull($absen->jam_masuk); // harian (9) mencatat jam otomatis
        $this->assertNotNull($absen->jam_selesai);
    }
}