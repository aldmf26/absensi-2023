@extends('template.master')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">

                <div class="row mb-2">
                    <div class="col-sm-6">
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
                        <h1 class="m-0 text-nowrap">Absensi Anak Laki : {{ tgl_indo($dari) }} -
                            {{ tgl_indo($sampai) }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Absensi Anak Laki</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
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
                        <form action="{{ route('absensi') }}" method="get">
                            <input type="hidden" name="id_departemen" value="{{ $id_departemen ?? 1 }}">
                            <div class="row ml-3">
                                <div class="col-sm-2">
                                    <input type="date" class="form-control" id="dari" name="tglDari" value="{{ $dari ?? '' }}">
                                </div>
                                <span class="ml-2 mr-2 mt-2">-</span>
                                <div class="col-sm-2">
                                    <input type="date" class="form-control" id="sampai" name="tglSampai" value="{{ $sampai ?? '' }}">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="id_jenis" id="filterJenis">
                                        <option value="">Semua Jenis Pekerjaan</option>
                                        @foreach($jenis_pekerjaan as $j)
                                            <option value="{{ $j->id }}" @if(isset($filterJenis) && (int)$filterJenis === (int)$j->id) selected @endif>{{ $j->jenis_pekerjaan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mt-2">
                                    <button class="btn btn-sm btn-info" id="btnFilter" type="submit">view</button>
                                </div>
                            </div>
                        </form><br>
                        <form action="{{ route('addAbsensi') }}" method="post">
                            @csrf
                            <button type="button" class="btn btn-primary mb-3 ml-4" data-toggle="modal"
                                data-target="#tambahAbsensi">
                                + Tambah Absensi
                            </button>
                            <button type="button" class="btn btn-info mb-3" data-toggle="modal"
                                data-target="#tambahCuti">
                                + Tambah Cuti/Libur
                            </button>
                            <a href="{{ route('excel') }}" class="btn btn-success mb-3"><i class="fas fa-file-excel"></i>
                                Export All</a>
                            <a href="{{ route('exportPertanggal', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-success mb-3"><i class="fas fa-file-excel"></i>
                                Export Pertanggal
                            </a>
                            <a href="{{ route('backupDatabase') }}" class="btn btn-warning mb-3"><i class="fas fa-database"></i>
                                Backup Database
                            </a>
                            @if ($canHapusPertanggal)
                                <button type="button" class="btn btn-danger mb-3" data-toggle="modal"
                                    data-target="#hapusPertanggal"><i class="fa fa-trash"></i>
                                    Hapus Pertanggal
                                </button>
                            @endif
                            <br>
                            @if (session('info'))
                                <div class="alert alert-info alert-dismissible ml-4 mr-1">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('info') }}
                                </div>
                            @endif

                            <style>
                                .modal-lg-max {
                                    max-width: 900px;
                                }
                                .foto-thumb {
                                    width: 70px;
                                    height: 70px;
                                    object-fit: cover;
                                    border-radius: 8px;
                                    cursor: pointer;
                                    border: 1px solid #eee;
                                }
                            </style>

                            <!-- Modal -->
                            <div class="modal fade" id="tambahAbsensi" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Tambah Absensi</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-3">
                                                    <label for="">Tanggal</label>
                                                    <input value="{{ date('Y-m-d') }}" required type="date" name="tanggal" class="form-control mb-3">
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-group" data-select2-id="93">
                                                        <label>Karyawan</label>
                                                        <select required name="id_karyawan[]"
                                                            class="select2 select2-hidden-accessible" multiple=""
                                                            data-placeholder="Select a State" style="width: 100%;"
                                                            data-select2-id="7" tabindex="-1" aria-hidden="true">
                                                            @foreach ($karyawan as $d)
                                                                <option value="{{ $d->id_karyawan }}">
                                                                    {{ strtoupper($d->nama_karyawan) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-2">
                                                    <label for="">Pemakai Jasa</label>
                                                    <select class="form-control" name="id_pemakai" id="">
                                                        @foreach ($pemakai as $d)
                                                            <option value="{{ $d->id_pemakai }}">{{ $d->pemakai }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div> --}}
                                                <div class="col-3">
                                                    <label for="">Jenis Pekerjaan</label>
                                                    <select class="form-control mb-3" name="id_jenis" id="">
                                                        @foreach ($jenis_pekerjaan as $d)
                                                            <option value="{{ $d->id }}">
                                                                {{ $d->jenis_pekerjaan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-3">
                                                    <label for="">Keterangan / Waktu</label>
                                                    <input type="text" placeholder="keterangan / waktu" name="ket"
                                                        class="form-control mb-3">
                                                </div>
                                                <div id="detail_absensi">

                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" name="simpan" value="Simpan" id="tombol"
                                                    class="btn btn-primary mt-3">
                                                <button type="button" class="btn btn-secondary  mt-3"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- modal tambah cuti / libur --}}
                        <form action="{{ route('addCuti') }}" method="post">
                            @csrf
                            <div class="modal fade" id="tambahCuti" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tambah Cuti / Libur</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Karyawan</label>
                                                <select required name="id_karyawan" id="selectKaryawanCuti" class="form-control select2">
                                                    @foreach ($karyawan as $d)
                                                        <option value="{{ $d->id_karyawan }}">{{ strtoupper($d->nama_karyawan) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div id="infoSisaCuti" class="alert alert-secondary py-1 px-2 mb-3" style="display:none;"></div>
                                            <div class="form-group">
                                                <label>Jenis</label>
                                                <select required name="jenis_cuti" id="selectJenisCuti" class="form-control">
                                                    <option value="17">Cuti Tahunan (12 hari)</option>
                                                    <option value="12">Libur Pulang Luar Kota</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Cuti</label>
                                                <div id="tanggalCutiList">
                                                    <div class="input-group mb-2" style="display:flex;gap:8px;">
                                                        <input required type="date" name="tanggal_cuti[]" class="form-control tgl-cuti" onchange="hitungJumlahHariCuti()">
                                                        <button type="button" class="btn btn-danger btn-hapus-tgl" onclick="hapusTanggalCuti(this)">&times;</button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="tambahTanggalCuti()">+ Tambah Tanggal</button>
                                            </div>
                                            <div class="form-group">
                                                <label>Jumlah Hari</label>
                                                <input type="text" value="1" id="jumlah_hari" class="form-control" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <input type="text" name="ket" class="form-control" placeholder="misal: cuti tahunan / pulang ke luar kota">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="submit" name="simpan" value="Simpan" id="tombol"
                                                class="btn btn-primary">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- end export pertanggal --}}
                        {{-- modal hapus pertanggal --}}
                        @if ($canHapusPertanggal)
                        <form action="{{ route('hapusPertanggal') }}" method="post"
                            onsubmit="return confirm('Hapus permanen? Data yang dihapus TIDAK bisa dikembalikan.');">
                            @csrf
                            <div class="modal fade" id="hapusPertanggal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md-6" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Hapus Pertanggal</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger">
                                                <b>Langkah 1:</b> <a href="{{ route('backupDatabase') }}" class="alert-link" target="_blank"><i class="fas fa-database"></i> Download Backup Database</a> dulu sebelum hapus.
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="">Dari</label>
                                                    <input required type="date" name="dari" value="{{ $dari }}"
                                                        class="form-control mb-3">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="">Sampai</label>
                                                    <input required type="date" name="sampai" value="{{ $sampai }}"
                                                        class="form-control mb-3">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Jenis Pekerjaan</label>
                                                <select class="form-control mb-3" name="id_jenis" id="hapusIdJenis">
                                                    <option value="">Semua Jenis Pekerjaan</option>
                                                    @foreach ($jenis_pekerjaan as $j)
                                                        <option value="{{ $j->id }}" @if (isset($filterJenis) && (int) $filterJenis === (int) $j->id) selected @endif>{{ $j->jenis_pekerjaan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Kata Sandi Admin</label>
                                                <input required type="password" name="password"
                                                    class="form-control mb-3" placeholder="masukkan kata sandi admin" autocomplete="off">
                                            </div>
                                            <div class="modal-footer" style="padding-left:0;padding-right:0;">
                                                <input type="submit" name="simpan" value="Hapus Data" id="tombol"
                                                    class="btn btn-danger mt-3">
                                                <button type="button" class="btn btn-secondary  mt-3"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                        {{-- end hapus pertanggal --}}

                        @include('flash.flash')
                        <div class="card-body">
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Pekerjaan</th>
                                        {{-- <th>Pemakai Jasa</th> --}}
                                        <th>Keterangan</th>
                                        <th>Jam</th>
                                        <th>Foto Masuk</th>
                                        <th>Foto Selesai</th>
                                        <th>Dibuat Tgl</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                        
                                    @endphp
                                    @foreach ($absensi as $d)
                                        <tr align="center">
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td>{{ $d->tanggal }}</td>
                                            <td>{{ strtolower($d->jenis_pekerjaan) }}</td>
                                            {{-- <td>{{ $d->pemakai }}</td> --}}
                                            <td>{{ $d->ket }}</td>
                                            <td>
                                                @php
                                                    $jamMasuk = $d->jam_masuk ? \Carbon\Carbon::parse($d->jam_masuk)->format('H:i') : '';
                                                    $jamSelesai = $d->jam_selesai ? \Carbon\Carbon::parse($d->jam_selesai)->format('H:i') : '';
                                                @endphp
                                                @if($jamMasuk || $jamSelesai)
                                                    {{ $jamMasuk }}{{ $jamSelesai ? '–' . $jamSelesai : '' }}
                                                @else
                                                    <span style="color:#ccc">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($d->foto_masuk)
                                                    <img src="{{ asset($d->foto_masuk) }}" class="foto-thumb" alt="masuk" onclick="bukaFoto('{{ asset($d->foto_masuk) }}')">
                                                @else
                                                    <span style="color:#ccc">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($d->foto_selesai)
                                                    <img src="{{ asset($d->foto_selesai) }}" class="foto-thumb" alt="selesai" onclick="bukaFoto('{{ asset($d->foto_selesai) }}')">
                                                @else
                                                    <span style="color:#ccc">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $d->created_at ? \Carbon\Carbon::parse($d->created_at)->format('d-m-Y') : '-' }}
                                                @if($d->created_at && \Carbon\Carbon::parse($d->created_at)->format('Y-m-d') != $d->tanggal)
                                                    <span class="badge badge-warning">⚠ beda</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-success editRow" id_absen="{{ $d->id_absen }}" id="edit={{ $d->id_absen }}"
                                                    data-toggle="modal" data-target="#editAbsensi"><i
                                                        class="fas fa-edit"></i></a>
                                                <form class="d-inline-block"
                                                    action="{{ route('deleteAbsensi', ['tglDari' => $dari, 'tglSampai' => $sampai]) }}"
                                                    method="post">
                                                    @csrf
                                                    <input type="hidden" name="id_absen" value="{{ $d->id_absen }}">
                                                    <button onclick="return confirm('Apakah anda yakin ? ')"
                                                        class="btn btn-sm btn-danger"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>

                                            </td>
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
        <!-- /.content -->
        <form action="{{ route('editAbsensi', ['tglDari' => $dari, 'tglSampai' => $sampai]) }}" method="post">
            @csrf
            @method('patch')
            <!-- Modal -->
            <div class="modal fade" id="editAbsensi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="load-edit"></div>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Lightbox lihat foto -->
        <div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:min(95vw,900px);">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;right:10px;top:6px;z-index:5;color:#fff;text-shadow:0 0 4px #000;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-body text-center" style="background:#111;padding:6px;">
                        <img id="lightboxImg" src="" alt="foto absen" style="max-width:100%;max-height:85vh;height:auto;border-radius:6px;">
                    </div>
                </div>
            </div>
        </div>
    @section('script')
        <script>
            $(document).ready(function() {
                var sisaJatah = @json($sisaJatah);
                window.updateSisaCuti = function() {
                    var id = $('#selectKaryawanCuti').val();
                    var jenis = $('#selectJenisCuti').val();
                    var el = $('#infoSisaCuti');
                    if (jenis == 17 && id && sisaJatah[id] !== undefined) {
                        el.text('Sisa jatah cuti tahunan: ' + sisaJatah[id] + ' hari').removeClass('alert-danger').show();
                        if (sisaJatah[id] <= 0) el.addClass('alert-danger');
                    } else if (jenis == 12) {
                        el.text('Libur Pulang Luar Kota: tanpa jatah.').removeClass('alert-danger').show();
                    } else {
                        el.hide();
                    }
                };
                $('#selectKaryawanCuti, #selectJenisCuti').on('change', updateSisaCuti);
                window.bukaFoto = function(src) {
                    $('#lightboxImg').attr('src', src);
                    $('#lightboxModal').modal('show');
                };
                window.tambahTanggalCuti = function() {
                    var html =
                        '<div class="input-group mb-2" style="display:flex;gap:8px;">' +
                        '<input required type="date" name="tanggal_cuti[]" class="form-control tgl-cuti" onchange="hitungJumlahHariCuti()">' +
                        '<button type="button" class="btn btn-danger" onclick="hapusTanggalCuti(this)">&times;</button>' +
                        '</div>';
                    $('#tanggalCutiList').append(html);
                    hitungJumlahHariCuti();
                };
                window.hapusTanggalCuti = function(btn) {
                    $(btn).closest('.input-group').remove();
                    hitungJumlahHariCuti();
                };
                window.hitungJumlahHariCuti = function() {
                    var total = 0;
                    $('#tanggalCutiList .tgl-cuti').each(function() {
                        if ($(this).val()) total++;
                    });
                    $('#jumlah_hari').val(total || 1);
                };
                $(document).on('click', `.editRow`, function() {
                    var id = $(this).attr(`id_absen`)
                    $.ajax({
                        type: "GET",
                        url: `absensi_edit/${id}`,
                        success: function(r) {
                            $(`#load-edit`).html(r);
                            $('.select2').select2()
                        }
                    });
                })
                $('.select2').select2()
                //Initialize Select2 Elements
                $('.select2bs4').select2({
                    theme: 'bootstrap4'
                })

                function initSelect2Cuti() {
                    var $el = $('#selectKaryawanCuti');
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        dropdownParent: $('#tambahCuti .modal-content')
                    });
                }
                $('#tambahCuti').on('shown.bs.modal', initSelect2Cuti);
                initSelect2Cuti();

                var count_absensi = 1;
                $(function() {
                    $("#progressbar").progressbar({
                        value: 37
                    });
                });

            })
        </script>
    @endsection
@endsection
