@extends('template.master')
@section('content')
    @php
        function tgl_indo($tanggal)
        {
            $bulan = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            ];
            $pecahkan = explode('-', $tanggal);

            // variabel pecahkan 0 = tanggal
            // variabel pecahkan 1 = bulan
            // variabel pecahkan 2 = tahun

            return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
        }
    @endphp
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0">Gaji Agrilaras {{ tgl_indo(date($tgl1)) }} ~ {{ tgl_indo(date($tgl2)) }}</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <form method="get" action="">


                            <div class="row form-group mt-4">
                                <div class="col-lg-2">
                                    <label for="">Dari</label>
                                    <input required type="date" class="form-control" name="tgl1">
                                </div>
                                <div class="col-lg-2">
                                    <label for="">Sampai</label>
                                    <input type="date" class="form-control" name="tgl2">
                                </div>
                                <div class="col-lg-1">
                                    <label for=""> </label>
                                    <button type="submit" class="btn btn-md btn-primary"
                                        style="margin-top: 33px">View</button>
                                </div>
                                <div class="col-lg-2">
                                    <label for=""> </label>
                                    <a href="{{ route('exportGaji', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" target="_blank"
                                        style="margin-top: 33px" class="btn btn-md btn-success"><i
                                            class="fa fa-file-pdf"></i> Export</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- /.row -->
                <!-- Main row -->
                <div class="row justify-content-center">
                    <!-- Left col -->
                    <section class="col-lg-12 connectedSortable">
                        <!-- Custom tabs (Charts with tabs)-->
                        <!-- Button trigger modal -->

                        <!-- INFO KARYAWAN YANG HARUSNYA NAIK GAJI -->
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-dark" style="cursor: pointer;" onclick="toggleNaik()">
                                <i class="fa fa-chevron-right" id="iconNaik"></i>
                                <strong>⚠️ KARYAWAN YANG HARUSNYA NAIK GAJI (Belum Dijalankan)</strong>
                            </div>
                            <div class="card-body" id="infoNaikBody" style="display: none;">
                                @if ($harus_naik_gaji && count($harus_naik_gaji) > 0)
                                    <div class="alert alert-warning">
                                        <h5 style="margin-top: 0;"><strong>📊 Total: {{ count($harus_naik_gaji) }} orang
                                                yang harusnya naik gaji bulan ini</strong></h5>
                                        <table class="table table-sm table-hover table-bordered">
                                            <thead style="background-color: #fff3cd;">
                                                <tr>
                                                    <th style="text-align: center;">No</th>
                                                    <th>Nama</th>
                                                    <th>Posisi</th>
                                                    <th style="text-align: center;">Masa Kerja</th>
                                                    <th style="text-align: right;">Gaji Saat Ini (Rp)</th>
                                                    <th style="text-align: right;">Gaji Seharusnya (Rp)</th>
                                                    <th style="text-align: right;">Selisih (Rp)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach ($harus_naik_gaji as $h)
                                                    <tr style="background-color: #fffacd;">
                                                        <td style="text-align: center; font-weight: bold;">
                                                            {{ $no++ }}</td>
                                                        <td><strong>{{ $h['nama'] }}</strong></td>
                                                        <td>{{ $h['posisi'] }}</td>
                                                        <td style="text-align: center;">{{ $h['masa_kerja'] }} Tahun</td>
                                                        <td style="text-align: right;">
                                                            {{ number_format($h['gaji_saat_ini'], 0, ',', '.') }}</td>
                                                        <td style="text-align: right; color: green; font-weight: bold;">
                                                            {{ number_format($h['gaji_seharusnya'], 0, ',', '.') }}</td>
                                                        <td style="text-align: right; color: red; font-weight: bold;">↑
                                                            {{ number_format($h['selisih'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">✓ Semua karyawan sudah sesuai aturan masa bakti.</div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            @include('flash.flash')
                            <table id="example1" class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Posisi</th>
                                        <th>Satuan/Hari</th>
                                        <th>M</th>
                                        <th>Rp M</th>
                                        <th>Bulanan</th>
                                        <th>Jam Lembur</th>
                                        <th>Rp Lembur</th>
                                        <th>Total Gaji</th>
                                        <th>Rp Kasbon</th>
                                        <th>Rp Denda</th>
                                        <th>Sisa Gaji</th>
                                        <th>Lama Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($hasil as $h)
                                        @php
                                            $ttlGajiS =
                                                $h->ttl_gaji_m + $h->ttl_gaji_e + $h->ttl_gaji_sp + $h->g_bulanan;
                                            $total_lembur =
                                                $h->lama_lembur == '' ? 0 : ($h->lama_lembur / 60) * $h->bayaran;
                                            $totalKerja = new DateTime($h->tanggal_masuk);
                                            $today = new DateTime();
                                            $tKerja = $today->diff($totalKerja);
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $h->nama_karyawan }}</td>
                                            <td>{{ $h->posisi }}</td>
                                            <td>{{ number_format($h->rp_m, 0) }}</td>
                                            <td>{{ number_format($h->qty_m == '' ? 0 : $h->qty_m, 0) }}</td>
                                            <td>{{ number_format($h->ttl_gaji_m == '' ? 0 : $h->ttl_gaji_m, 0) }}</td>
                                            <td>{{ number_format($h->g_bulanan, 0) }}</td>
                                            <td>{{ $h->lama_lembur == '' ? 0 : $h->lama_lembur / 60 }}</td>
                                            <td>{{ number_format($h->lama_lembur == '' ? 0 : ($h->lama_lembur / 60) * $h->bayaran, 0) }}
                                            </td>
                                            <td>{{ number_format($ttlGajiS + $total_lembur, 0) }}</td>
                                            <td>{{ number_format($h->kasbon, 0) }}</td>
                                            <td>{{ number_format($h->denda, 0) }}</td>
                                            <td>{{ number_format($ttlGajiS + $total_lembur - $h->kasbon - $h->denda, 0) }}
                                            </td>
                                            <td>{{ $tKerja->y . ' Tahun ' . $tKerja->m . ' Bulan' }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </section>
                    <!-- right col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>

        <script>
            function toggleNaik() {
                const body = document.getElementById('infoNaikBody');
                const icon = document.getElementById('iconNaik');

                if (body.style.display === 'none') {
                    body.style.display = 'block';
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-down');
                } else {
                    body.style.display = 'none';
                    icon.classList.add('fa-chevron-right');
                    icon.classList.remove('fa-chevron-down');
                }
            }
        </script>

    @endsection
