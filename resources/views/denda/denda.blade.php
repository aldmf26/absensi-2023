@extends('template.master')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $title }}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <!-- Left col -->
                    <section class="col-lg-10 connectedSortable card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
                                + Tambah
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#view">
                                View
                            </button>
                        </div>

                        <div class="card-body">
                            @include('flash.flash')
                            <table id="example1" class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Karyawan</th>
                                        <th class="text-center">Nominal</th>
                                        <th>Keterangan</th>
                                        <th>Admin</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kasbon as $no => $d)
                                        <tr>
                                            <td>{{ $no + 1 }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->tgl)) }}</td>
                                            <td>{{ $d->nama_karyawan }}</td>
                                            <td align="right">{{ number_format($d->nominal, 0) }}</td>
                                            <td>{{ $d->ket }}</td>
                                            <td>{{ $d->admin }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-success edit" id_denda="{{ $d->id_denda }}"
                                                    data-toggle="modal" data-target="#edit"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus ?')"
                                                    href="{{ route('denda.delete', ['id_denda' => $d->id_denda, 'id_departemen' => request()->get('id_departemen')]) }}"><i
                                                        class="fas fa-trash"></i></a>
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
<form action="" method="GET">
            <!-- Modal tambah karyawan-->
            <div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">View {{ $title }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Dari</label>
                                        <input type="date" name="tgl1" class="form-control">
                                        <input type="hidden" value="{{$id_departemen}}" name="id_departemen" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Sampai</label>
                                        <input type="date" name="tgl2" class="form-control">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="simpan" value="Simpan" id="tombol" class="btn btn-primary">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="{{ route('denda.create') }}" method="post">
            @csrf
            <!-- Modal tambah karyawan-->
            <div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Tambah {{ $title }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <input type="hidden" name="id_departemen"
                                            value="{{ request()->get('id_departemen') }}">
                                        <label for="">Tanggal</label>
                                        <input required type="date" name="tgl" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label for="">Nama Karyawan</label>
                                        <select name="id_karyawan" class="form-control select2" id="">
                                            <option value="">- Pilih Karyawan -</option>
                                            @foreach ($karyawan as $k)
                                                <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="">Nominal</label>
                                        <input required type="text" name="nominal" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Keterangan</label>
                                        <input required type="text" name="keterangan" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="simpan" value="Simpan" id="tombol" class="btn btn-primary">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('denda.update') }}" method="post">
            @csrf
            <!-- Modal tambah karyawan-->
            <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit {{ $title }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="load_edit"></div>

                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="simpan" value="Simpan" id="tombol" class="btn btn-primary">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $(document).on('click', '.edit', function() {
                    var id_denda = $(this).attr('id_denda')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('denda.edit') }}",
                        data: {
                            id_denda: id_denda,
                            id_departemen: "{{ request()->get('id_departemen') }}",
                        },
                        success: function(response) {
                            $("#load_edit").html(response);
                            $(".select2").select2({
                                dropdownParent: $('#edit .modal-content')
                            })
                        }
                    });
                })
            });
        </script>
    @endsection
