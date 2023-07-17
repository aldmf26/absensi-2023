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
                    <section class="col-lg-8 connectedSortable card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
                                + Tambah
                            </button>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#view">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <a href="{{ route('kasbonAgrilaras.print', [
                                'id_departemen' => $id_departemen,
                                'tgl1' => $tgl1,
                                'tgl2' => $tgl2,
                            ]) }}"
                                class="btn btn-primary">
                                <i class="fas fa-print"></i> Print
                            </a>
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
                                            <td>{{ $d->admin }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-success edit" id_kasbon="{{ $d->id_kasbon }}"
                                                    data-toggle="modal" data-target="#edit"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-sm btn-danger" onclick="return confirm('Yakin dihapus ?')"
                                                    href="{{ route('kasbonAgrilaras.delete', ['id_kasbon' => $d->id_kasbon, 'id_departemen' => request()->get('id_departemen')]) }}"><i
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
                                        <input type="hidden" value="{{ $id_departemen }}" name="id_departemen"
                                            class="form-control">
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
        <form action="{{ route('kasbonAgrilaras.create') }}" method="post">
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
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <input type="hidden" name="id_departemen"
                                            value="{{ request()->get('id_departemen') }}">
                                        <label for="">Tanggal</label>
                                        <input type="date" name="tgl" value="{{ date('Y-m-d') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label for="">Nama Karyawan</label>
                                        <select name="id_karyawan[]" class="form-control select2" id="">
                                            <option value="">- Pilih Karyawan -</option>
                                            @foreach ($karyawan as $k)
                                                <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label for="">Nominal</label>
                                        <input type="text" name="nominal[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <div class="form-group">
                                        <label for="">Aksi</label><br>
                                        <a id_departemen="{{ Request::get('id_departemen') }}" class="btn btn-sm btn-primary btn_tambah"><i class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div id="btn_tambah"></div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" name="simpan" value="Simpan" id="tombol" class="btn btn-primary">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('kasbonAgrilaras.update') }}" method="post">
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
                plusRow(1, 'btn_tambah', 'kasbon/btn_tambah')

                $(document).on('click', '.edit', function() {
                    var id_kasbon = $(this).attr('id_kasbon')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('kasbonAgrilaras.edit') }}",
                        data: {
                            id_kasbon: id_kasbon,
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
