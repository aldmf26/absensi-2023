<style>
    @media (max-width: 767px) {
        #thnama {
            white-space: nowrap;
            position: sticky;
            left: 2000;
            z-index: 999;
        }
    }

    .scrl {
        overflow: auto;
    }
</style>

<a href="" class="btn btn-primary btn-sm float-right mb-2" data-toggle="modal" data-target="#tambah_data"><i
        class="fas fa-plus"></i> Tambah Data</a>
<a href="{{route('exportLembur')}}" class="btn btn-primary btn-sm float-left mb-2"><i
        class="fas fa-file-excel"></i> Export</a>
<div class="table-responsive">
    <table class="table text-center" id="example1">
        <thead>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>Pekerjaan</th>
            <th>Waktu</th>
            <th>Waktu Lembur</th>
            <th>Aksi</th>
        </thead>
        <tbody>
            @php
            $i=1;
            @endphp
            @foreach ($absen_lembur as $a)
            <tr>
                <td>{{$i++}}</td>
                <td>{{date('d-m-Y',strtotime($a->tgl))}}</td>
                <td>{{$a->nama_karyawan}}</td>
                <td>{{$a->pekerjaan}}</td>
                <td>{{$a->j_awal}} ~ {{$a->j_akhir}}</td>
                @php
                $mulai = new DateTime($a->j_awal);
                $selesai = new DateTime($a->j_akhir);
                $menit = $selesai->diff($mulai);
                @endphp
                <td>{{$menit->h}} Jam : {{$menit->i}} Menit</td>
                <td>
                    <button type="button" data-toggle="modal" data-target="#edit"
                        id_absen_lembur="{{$a->id_absen_lembur}}" class="btn btn-warning btn-sm edit_absen"><i
                            class="fas fa-edit"></i></button>

                    <button type="button" id_absen_lembur="{{$a->id_absen_lembur}}"
                        class="btn btn-danger btn-sm hapus_absen"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>

