<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Jenis;
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

    public function test_selesai_lembur_dadakan_buat_baris_lembur_terpisah()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // absen harian normal (baris 1)
        $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('masuk.jpg'),
        ]);

        $absen = Absensi::where('id_karyawan', $kar->id_karyawan)->first();

        // selesai + lembur=1 -> baris Lembur (jenis 8) terpisah dibuat
        $res = $this->post('/absen/selesai', [
            'id_absen' => $absen->id_absen,
            'foto' => UploadedFile::fake()->image('selesai.jpg'),
            'lembur' => 1,
            'jam_lembur' => '21:30',
            'foto_lembur' => UploadedFile::fake()->image('selesai-lembur.jpg'),
        ]);

        $res->assertRedirect('/absen');
        $res->assertSessionHas('sukses');

        $absen->refresh();
        $this->assertEquals('selesai', $absen->status);

        $lembur = Absensi::where('id_karyawan', $kar->id_karyawan)
            ->where('id_jenis_pekerjaan', 8)->first();
        $this->assertNotNull($lembur);
        $this->assertEquals('selesai', $lembur->status);
        $this->assertEquals('21:30', \Carbon\Carbon::parse($lembur->jam_selesai)->format('H:i'));
        // keterangan diisi sendiri oleh karyawan; tanpa isian -> kosong
        $this->assertNull($lembur->ket);
        $this->assertNotNull($lembur->foto_selesai);
        // total baris = 2 (harian + lembur) -> tidak digabung
        $this->assertEquals(2, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
    }

    public function test_selesai_lembur_tanpa_foto_lembur_ditolak()
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
            'lembur' => 1,
            'jam_lembur' => '21:30',
        ]);

        $res->assertSessionHas('error');
    }

    public function test_selesai_tanpa_lembur_tidak_buat_baris_tambahan()
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

        $this->post('/absen/selesai', [
            'id_absen' => $absen->id_absen,
            'foto' => UploadedFile::fake()->image('selesai.jpg'),
            'lembur' => 0,
        ]);

        $this->assertEquals(1, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
    }

    public function test_karyawan_bisa_input_cuti_sendiri()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // cek halaman absen menampilkan sisa jatah
        $this->get('/absen')->assertSee('Sisa Cuti Tahunan');

        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => ['2026-10-05', '2026-10-06'],
            'ket' => 'acara keluarga',
        ]);

        $res->assertRedirect('/absen?tab=cuti');
        $res->assertSessionHas('sukses');

        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'tanggal' => '2026-10-05',
            'status' => 'selesai',
            'jumlah_hari' => null,
        ]);
        $this->assertEquals(2, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
        $this->assertStringContainsString(
            'acara keluarga',
            Absensi::where('id_karyawan', $kar->id_karyawan)->where('tanggal', '2026-10-06')->first()->ket
        );
    }

    public function test_cuti_tanggal_doppel_tidak_jadi_duplicate()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => ['2026-10-05', '2026-10-06'],
        ]);

        // tanggal 05 sudah tercatat -> 06 & 07 dicatat sebagai baris baru
        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => ['2026-10-05', '2026-10-06', '2026-10-07'],
        ]);
        $res->assertSessionHas('sukses');
        $this->assertEquals(3, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'tanggal' => '2026-10-07',
            'jumlah_hari' => null,
        ]);
        $this->assertNull(Absensi::where('id_karyawan', $kar->id_karyawan)->where('tanggal', '2026-10-06')->first()->ket);
    }

    public function test_cuti_melebihi_jatah_ditandai_tidak_dibayar()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $tanggal = [];
        for ($d = 1; $d <= 13; $d++) {
            $tanggal[] = '2026-11-' . str_pad((string) $d, 2, '0', STR_PAD_LEFT);
        }

        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => $tanggal,
        ]);
        $res->assertSessionHas('sukses');

        $this->assertEquals(13, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
        $this->assertNull(Absensi::where('id_karyawan', $kar->id_karyawan)->where('tanggal', '2026-11-01')->first()->ket);
        $this->assertStringContainsString(
            'TIDAK DIBAYAR',
            Absensi::where('id_karyawan', $kar->id_karyawan)->where('tanggal', '2026-11-13')->first()->ket
        );
    }

    public function test_cuti_ditolak_jika_tanggal_sudah_ada_absen()
    {
        // jenis seeding agar dropdown absen normal tersedia
        Jenis::forceCreate(['id' => 9, 'jenis_pekerjaan' => 'Absen Harian', 'keterangan' => 'x']);

        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // absen harian hari ini
        $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now()->toDateString(),
            'foto' => UploadedFile::fake()->image('masuk.jpg'),
        ]);

        // cuti di tanggal yang sama harus ditolak
        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => [now('Asia/Makassar')->toDateString()],
        ]);
        $res->assertSessionHas('error');
        $this->assertEquals(0, Absensi::where('id_karyawan', $kar->id_karyawan)->where('id_jenis_pekerjaan', 17)->count());
    }

    public function test_cuti_boleh_untuk_tanggal_masa_depan()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $masaDepan = now('Asia/Makassar')->addDays(20)->toDateString();

        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => [$masaDepan],
        ]);
        $res->assertRedirect('/absen?tab=cuti');
        $res->assertSessionHas('sukses');
        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'tanggal' => $masaDepan,
        ]);
    }

    public function test_absen_ditolak_jika_tanggal_sudah_cuti()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // cuti hari ini dulu
        $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_cuti' => [now('Asia/Makassar')->toDateString()],
        ]);

        // absen harian di tanggal yang sudah cuti harus ditolak
        $res = $this->post('/absen', [
            'id_jenis' => 9,
            'tanggal' => now('Asia/Makassar')->toDateString(),
            'foto' => UploadedFile::fake()->image('masuk.jpg'),
        ]);
        $res->assertSessionHas('error');
        $this->assertEquals(0, Absensi::where('id_karyawan', $kar->id_karyawan)->where('id_jenis_pekerjaan', 9)->count());
    }

    public function test_cuti_rentang_mulai_sampai()
    {
        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        // rentang 21-25 -> 5 baris, termasuk kiriman baris tanggal terpisah kosong
        $res = $this->post('/absen/cuti', [
            'jenis_cuti' => 17,
            'tanggal_mulai' => '2026-10-21',
            'tanggal_sampai' => '2026-10-25',
            'tanggal_cuti' => ['', ''],
        ]);
        $res->assertRedirect('/absen?tab=cuti');
        $res->assertSessionHas('sukses');
        $this->assertEquals(5, Absensi::where('id_karyawan', $kar->id_karyawan)->count());
        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'tanggal' => '2026-10-21',
        ]);
        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'tanggal' => '2026-10-25',
        ]);
    }

    public function test_sisa_jatah_menghitung_format_lama_dan_baru()
    {
        Jenis::forceCreate(['id' => 9, 'jenis_pekerjaan' => 'Absen Harian', 'keterangan' => 'x']);

        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $tahun = now('Asia/Makassar')->year;

        // 1 baris format lama = 5 hari cuti
        Absensi::create([
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'id_pemakai' => 1,
            'tanggal' => "$tahun-01-05",
            'jumlah_hari' => 5,
            'status' => 'selesai',
        ]);
        // 1 baris format baru = 1 hari cutoff
        Absensi::create([
            'id_karyawan' => $kar->id_karyawan,
            'id_jenis_pekerjaan' => 17,
            'id_pemakai' => 1,
            'tanggal' => "$tahun-02-10",
            'jumlah_hari' => null,
            'status' => 'selesai',
        ]);

        // total terpakai 6 -> sisa jatah 6, bukan 12-2=10 (kalau hanya count baris)
        $this->get('/absen')->assertSee('Sisa Cuti Tahunan: 6 hari');
    }

    public function test_riwayat_per_bulan_dan_ringkasan()
    {
        // jenis seeding agar ringkasan punya nama
        // jenis seeding agar ringkasan punya nama
        Jenis::forceCreate(['id' => 9, 'jenis_pekerjaan' => 'Absen Harian', 'keterangan' => 'x']);
        Jenis::forceCreate(['id' => 17, 'jenis_pekerjaan' => 'Cuti Tahunan', 'keterangan' => 'x']);

        $kar = Karyawan::create([
            'nama_karyawan' => 'Budi',
            'tanggal_masuk' => '2020-01-01',
            'id_departemen' => 1,
            'posisi' => 'Satpam',
            'pin_absen' => Hash::make('1234'),
        ]);
        session(['absen_karyawan.id' => $kar->id_karyawan]);

        $bulanIni = now('Asia/Makassar')->month;
        $tahunIni = now('Asia/Makassar')->year;
        $bulanLalu = $bulanIni === 1 ? 12 : $bulanIni - 1;
        $tahunLalu = $bulanIni === 1 ? $tahunIni - 1 : $tahunIni;
        $tgl1 = now('Asia/Makassar')->startOfMonth()->addDays(4)->toDateString();
        $tgl2 = now('Asia/Makassar')->startOfMonth()->addDays(5)->toDateString();
        $tglCuti = \Carbon\Carbon::createFromDate($tahunLalu, $bulanLalu, 5)->toDateString();

        // 2 absen harian selesai di bulan ini + 1 cuti (2 hari) di bulan lalu
        Absensi::create(['id_karyawan' => $kar->id_karyawan, 'id_jenis_pekerjaan' => 9, 'id_pemakai' => 1, 'tanggal' => $tgl1, 'status' => 'selesai', 'jumlah_hari' => null]);
        Absensi::create(['id_karyawan' => $kar->id_karyawan, 'id_jenis_pekerjaan' => 9, 'id_pemakai' => 1, 'tanggal' => $tgl2, 'status' => 'selesai', 'jumlah_hari' => null]);
        Absensi::create(['id_karyawan' => $kar->id_karyawan, 'id_jenis_pekerjaan' => 17, 'id_pemakai' => 1, 'tanggal' => $tglCuti, 'status' => 'selesai', 'jumlah_hari' => 2]);

        $res = $this->get('/absen?bulan=' . $bulanIni . '&tahun=' . $tahunIni);
        $res->assertOk();
        $res->assertSee('Riwayat Absen');
        $res->assertSee('Absen Harian (2)');
        $res->assertDontSee('Cuti Tahunan (2)');

        // bulan lalu: hanya cuti
        $res2 = $this->get('/absen?bulan=' . $bulanLalu . '&tahun=' . $tahunLalu);
        $res2->assertOk();
        $res2->assertSee('Cuti Tahunan (2)');
        $res2->assertDontSee('Absen Harian (2)');
    }
}