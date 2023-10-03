<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>{{$title}}</title>
    <style>
        .dhead {
            background-color: #435EBE !important;
            color: white;
        }

        .dborder {
            border-color: #435EBE
        }

    </style>
  </head>
  <body>
    <div class="container py-3 px-3">
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col text-start">
                        <h3>Data Denda</h3>
                    </div>
                    <div class="col text-end">
                        <span style="font-size:10px;"><em><b>Tanggal Cetak: {{ date('d-m-Y') }}, {{ ucwords(auth()->user()->name) }}</b></em></span>
                    </div>
                </div>
                <table class="table table-hover table-bordered dborder">
                    <thead>
                        <tr>
                            <th class="dhead" width="5">#</th>
                            <th class="dhead">Nama Karyawan</th>
                            <th class="dhead">Keterangan</th>
                            <th class="dhead" style="text-align: right" width="15%">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $ttlNominal = 0;
                        @endphp
                        @foreach($kasbon as $no => $d)
                            @php
                                $ttlNominal += $d->nominal;
                            @endphp
                            <tr>
                                <td>{{ $no+1 }}</td>
                                <td>{{ ucwords($d->nama_karyawan) }}</td>
                                <td>{{ ucwords($d->ket) }}</td>
                                <td style="text-align: right">{{ number_format($d->nominal,0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th style="text-align: center">Total</th>
                            <th style="text-align: right">{{ number_format($ttlNominal,0) }}</th>
                        </tr>
                    </tfoot>
        
                </table>
            </div>
        </div>
        
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>

