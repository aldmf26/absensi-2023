<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//unpkg.com/alpinejs" defer></script>
    <title>Cuti</title>
</head>

<body class="container px-4 pt-5">
    @php
        $bulan = request()->get('bulan') ?? date('m');
        $tahun = request()->get('tahun') ?? date('Y');
    @endphp
    <form action="" method="GET">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Bulan</label>
                    <input class="form-control" value="{{ $bulan }}" type="text" name="bulan">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Tahun</label>
                    <input class="form-control" value="{{ $tahun }}" type="text" name="tahun">
                </div>
            </div>
            <div class="col-lg-3">
                <label for="">Aksi</label><br>
                <button class="btn btn-sm btn-primary" type="submit">Filter</button>
            </div>
        </div>

    </form>

    <hr>
    <form action="{{ route('cuti.create') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Nama</label>
                    <select class="form-control" name="id_karyawan" id="">
                        <option value="">Pilih nama</option>
                        @php
                            $nama = DB::table('karyawan')->where('id_departemen', 1)->get();
                        @endphp
                        @foreach ($nama as $d)
                            <option value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Tgl</label>
                    <input class="form-control" type="date" name="tgl">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Alasan</label>
                    <input class="form-control" type="text" placeholder="alasan" name="alasan">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                </div>
            </div>
        </div>



    </form>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Ttl</th>
            </tr>
        </thead>
        <tbody>
            @php

                $datas = DB::SELECT("SELECT b.nama_karyawan as nama, count(*) as ttl FROM cuti as a 
            JOIN karyawan as b on a.id_karyawan = b.id_karyawan
            WHERE month(a.tgl) = '$bulan' AND year(a.tgl) = '$tahun' GROUP BY a.id_karyawan");
            @endphp
            @foreach ($datas as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama }}</td>
                    <td>{{ $d->ttl }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</body>

</html>
