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
                            $bulan_2 = ['bulan', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            $bulan1 = (int) $bulan;
                        @endphp
                        <h1 class="m-0">Absensi Agrilaras : <span id="ketbul">{{ $bulan_2[$bulan1] }}</span> -
                            <span id="ketah">{{ $tahun_2 }}</span>
                        </h1><br>

                        <div class="row">
                            <div class="col-md-5">
                                <select id="bulan" class="form-control mb-3 " name="bulan">
                                    <option value="">--Pilih Bulan--</option>
                                    <option value="1" {{ (int) date('m') == 1 ? 'selected' : '' }}>Januari</option>
                                    <option value="2" {{ (int) date('m') == 2 ? 'selected' : '' }}>Februari</option>
                                    <option value="3" {{ (int) date('m') == 3 ? 'selected' : '' }}>Maret</option>
                                    <option value="4" {{ (int) date('m') == 4 ? 'selected' : '' }}>April</option>
                                    <option value="5" {{ (int) date('m') == 5 ? 'selected' : '' }}>Mei</option>
                                    <option value="6" {{ (int) date('m') == 6 ? 'selected' : '' }}>Juni</option>
                                    <option value="7" {{ (int) date('m') == 7 ? 'selected' : '' }}>Juli</option>
                                    <option value="8" {{ (int) date('m') == 8 ? 'selected' : '' }}>Agustus</option>
                                    <option value="9" {{ (int) date('m') == 9 ? 'selected' : '' }}>September</option>
                                    <option value="10" {{ (int) date('m') == 10 ? 'selected' : '' }}>Oktober</option>
                                    <option value="11" {{ (int) date('m') == 11 ? 'selected' : '' }}>November</option>
                                    <option value="12" {{ (int) date('m') == 12 ? 'selected' : '' }}>Desember</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control mb-3 " id="tahun" name="tahun">
                                    <option value="">--Pilih Tahun--</option>
                                    <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>

                                    @for ($i = date('Y'); $i <= date('Y') + 8; $i++)
                                        <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>

                            </div>
                            <div class="col-md-2">
                                <input type="submit" id="btntes" name="bulanAgrilaras"
                                    class="btn btn-primary btn-sm btn-block" value="Simpan">
                            </div>
                        </div>


                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Absensi Agrilaras</li>

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
                        <div class="card">
                            <div class="card-header">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active absen" id="pills-contact-tab" data-toggle="pill"
                                            href="#pills-contact" role="tab" aria-controls="pills-contact"
                                            aria-selected="false">Absen</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link  lembur" id_departemen={{ $id_departemen }} id="pills-home-tab"
                                            data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home"
                                            aria-selected="true">Absen Lembur</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div id="badan">
                                </div>
                                <div id="lembur">

                                </div>
                            </div>
                        </div>

                    </section>
                    <!-- right col -->
                </div>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>

        <style>
            .modal-lg-max {
                max-width: 1000px;
            }
        </style>
        <form id="save_absen">
            <div class="modal fade" id="tambah_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg-max" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tambah Absen Lembur</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <label for="">Tanggal</label>
                                    <input type="date" class="form-control tanggal" name="tanggal">
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-3">
                                    <label for="">Pilih Karyawan</label>
                                    <Select class="form-control select id_karyawan" name="id_karyawan[]">
                                        <option value="">Pilih Karyawan</option>
                                        @foreach ($karyawan as $k)
                                            <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                                        @endforeach
                                    </Select>
                                </div>
                                <div class="col-lg-4">
                                    <label for="">Pekerjaan</label>
                                    <input type="text" class="form-control pekerjaan" name="pekerjaan[]">
                                </div>
                                <div class="col-lg-2">
                                    <label for="">Jam awal</label>
                                    <input type="time" class="form-control j_awal" name="j_awal[]" value="17:00:00"
                                        readonly>
                                </div>
                                <div class="col-lg-2 ">
                                    <label for="">Jam akhir</label>
                                    <input type="time" class="form-control j_akhir" name="j_akhir[]">
                                </div>

                            </div>
                            <div id="tambah_input">

                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <button type="button" class="btn btn-info btn-sm float-right tambah_input"><i
                                            class="fas fa-plus"></i>
                                        Tambah</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="edit_absen">
            <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg-max" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edit">Edit Absen Lembur</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="form_edit"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <!-- /.content -->
    @endsection
    @section('script')
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {



                var url = "{{ route('tabelAgrilaras') }}?bulan=" + {{ date('m') }} + "&tahun=" +
                    {{ date('Y') }}

                getUrl(url)

                // chane select dbulan dan tahun
                $("#bulan").change(function(e) {
                    var bulan = $("#bulan").val();
                    var ketbul = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember'
                    ]
                    $("#ketbul").text(ketbul[bulan]);

                });
                $("#tahun").change(function(e) {
                    var tahun = $("#tahun").val();
                    var bulan = $("#bulan").val();
                    $("#ketah").text(tahun);

                });
                // ----------------------------------

                function getUrl(url) {
                    $("#badan").load(url, "data", function(response, status, request) {
                        this;
                    });
                }

                $("#btntes").click(function(e) {
                    var bulan = $("#bulan").val();
                    var tahun = $("#tahun").val();
                    var url = "{{ route('tabelAgrilaras') }}?bulan=" + bulan + "&tahun=" + tahun
                    getUrl(url)
                });

                $(document).on('click', '.btnInput', function() {
                    var id_karyawan = $(this).attr('id_karyawan')
                    var bulan = $(this).attr('bulan')
                    var tahun = $(this).attr('tahun')
                    var tanggal = $(this).attr('tanggal')
                    var status = $(this).attr('status')
                    var url = "{{ route('tabelAgrilaras') }}?bulan=" + bulan + "&tahun=" + tahun
                    if(confirm('Apakah anda yakin ?')) {
                        $.ajax({
                            type: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            url: "{{ route('input_agrilaras') }}?id_departemen=4&id_karyawan=" +
                                id_karyawan +
                                "&status=" + status + "&tanggal=" + tanggal,
                            success: function(response) {
                                getUrl(url)
                            }
                        });
                    }
                })

                $(document).on('click', '.btnDelete', function() {
                    var id_absen = $(this).attr('id_absen')
                    var bulan = $(this).attr('bulan')
                    var tahun = $(this).attr('tahun')
                    var status = $(this).attr('status')
                    var url = "{{ route('tabelAgrilaras') }}?bulan=" + bulan + "&tahun=" + tahun

                    $.ajax({
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        url: "{{ route('delete_agrilaras') }}?id_departemen=4&id_absen=" + id_absen,
                        success: function(response) {
                            getUrl(url)
                        }
                    });
                });

                $(document).on('click', '.lembur', function() {
                    // alert('berhasil');
                    $('#badan').hide();
                    $('#lembur').show();

                });
                $(document).on('click', '.absen', function() {
                    // alert('berhasil');
                    $('#badan').show();
                    $('#lembur').hide();

                });

                $(document).on('click', '.lembur', function() {
                    $.ajax({
                        url: "{{ route('lembur') }}",
                        type: "Get",
                        success: function(data) {
                            $('#lembur').html(data);
                            $('#example1').DataTable({
                                "paging": true,
                                "lengthChange": false,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "responsive": true,
                            });
                        }
                    });

                });
                $(document).on('click', '.edit_absen', function() {
                    var id_absen_lembur = $(this).attr('id_absen_lembur');

                    $.ajax({
                        url: "{{ route('edit_data') }}?id_absen_lembur=" + id_absen_lembur,
                        type: "Get",
                        success: function(data) {
                            $('#form_edit').html(data);
                        }
                    });

                });


                $(document).on('submit', '#save_absen', function(event) {
                    event.preventDefault();

                    var tanggal = $(".tanggal").val();
                    var id_karyawan = $(".id_karyawan").val();
                    var pekerjaan = $(".pekerjaan").val();
                    var j_awal = $(".j_awal").val();
                    var j_akhir = $(".j_akhir").val();

                    var pesanan_new = $("#save_absen").serialize()

                    $.ajax({
                        type: "GET",
                        url: "{{ route('save_absen_lembur') }}?" + pesanan_new,
                        success: function(response) {

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Berhasil menambahkan data'
                            });
                            $.ajax({
                                url: "{{ route('lembur') }}",
                                type: "Get",
                                success: function(data) {
                                    $('#lembur').html(data);
                                    $('#example1').DataTable({
                                        "paging": true,
                                        "lengthChange": false,
                                        "searching": true,
                                        "ordering": true,
                                        "info": true,
                                        "autoWidth": false,
                                        "responsive": true,
                                    });
                                }
                            });
                            $("#tambah_data .close").click()
                            // // $('#tambah_data').modal('toggle'); 
                            //  $('#tambah_data').modal('hide');
                        }
                    });

                });

                $(document).on('click', '.hapus_absen', function() {
                    var id_absen_lembur = $(this).attr('id_absen_lembur');
                    Swal.fire({
                        title: 'Apakah anda yakin ingin mengahpus data?',
                        text: "Pastikan data yang dihapus sudah benar !",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#1596AA',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Lanjutkan'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('delete_absen_lembur') }}",
                                data: {
                                    id_absen_lembur: id_absen_lembur
                                },
                                success: function(response) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        icon: 'success',
                                        title: 'Berhasil menghapus data'
                                    });
                                    $.ajax({
                                        url: "{{ route('lembur') }}",
                                        type: "Get",
                                        success: function(data) {
                                            $('#lembur').html(data);
                                            $('#example1').DataTable({
                                                "paging": true,
                                                "lengthChange": false,
                                                "searching": true,
                                                "ordering": true,
                                                "info": true,
                                                "autoWidth": false,
                                                "responsive": true,
                                            });
                                        }
                                    });
                                }
                            });
                        } else {
                            return false;
                        }
                    })





                });

                var count = 1;
                $(document).on('click', '.tambah_input', function() {
                    count = count + 1;

                    $.ajax({
                        url: "{{ route('tambah_lembur') }}?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $('#tambah_input').append(data);
                        }
                    });




                });
                $(document).on('click', '.remove_monitoring', function() {

                    var delete_row = $(this).attr('count');

                    $('#row' + delete_row).remove();
                });

                $(document).on('submit', '#edit_absen', function(event) {
                    event.preventDefault();

                    var tanggal = $(".tanggal_edit").val();
                    var id_absen_lembur = $(".id_absen_lembur_edit").val();
                    var id_karyawan = $(".id_karyawan_edit").val();
                    var pekerjaan = $(".pekerjaan_edit").val();
                    var j_awal = $(".j_awal_edit").val();
                    var j_akhir = $(".j_akhir_edit").val();



                    $.ajax({
                        type: "GET",
                        url: "{{ route('edit_absen_lembur') }}",
                        data: {
                            tanggal: tanggal,
                            id_karyawan: id_karyawan,
                            id_absen_lembur: id_absen_lembur,
                            pekerjaan: pekerjaan,
                            j_awal: j_awal,
                            j_akhir: j_akhir
                        },
                        success: function(response) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Berhasil mengedit data'
                            });
                            $.ajax({
                                url: "{{ route('lembur') }}",
                                type: "Get",
                                success: function(data) {
                                    $('#lembur').html(data);
                                    $('#example1').DataTable({
                                        "paging": true,
                                        "lengthChange": false,
                                        "searching": true,
                                        "ordering": true,
                                        "info": true,
                                        "autoWidth": false,
                                        "responsive": true,
                                    });
                                }
                            });
                            $("#edit .close").click()
                            // $('#edit').modal('toggle'); //or  $('#IDModal').modal('hide');
                        }
                    });

                });
                $(document).on('click', '.clickEdit', function() {
                    var id_absen = $(this).attr('id_absen')
                    var status = $(this).attr('status')

                    $.ajax({
                        type: "GET",
                        url: "{{ route('editAbsen') }}",
                        data: {
                            id_absen: id_absen,
                            status: status,
                        },
                        success: function(r) {
                            getUrl(url)
                        }
                    });
                })

                $(document).on('click', '.clickOff', function() {
                    var id_karyawan = $(this).attr('id_karyawan')
                    var tgl = $(this).attr('tgl')

                    $.ajax({
                        type: "GET",
                        url: "{{ route('addAbsen') }}",
                        data: {
                            id_karyawan: id_karyawan,
                            tgl: tgl,
                        },
                        success: function(r) {
                            getUrl(url)
                        }
                    });
                })
            });
        </script>
    @endsection
