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
<a id="download" class="btn btn-sm btn-success mb-3" {{-- {{ route('downloadAbsAgri', ['id_departemen' => 4]) }} --}}
    href="{{ route('downloadAbsAgri', ['id_departemen' => 4, 'bulanDwn' => $bulan, 'tahunDwn' => $tahun_2]) }}">
    <i class="fa fa-download"></i> DOWNLOAD
</a>
<div class="card">
    <table style="z-index: 999999;" class="table table-lg table-striped table-bordered" width="100%">
        <thead class="table-success">
            <tr>
                @php
                    $tgl = getdate();
                    
                    $bulan;
                    $tahun_2;
                    $tanggal = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun_2);
                    $total = $tanggal;
                @endphp
                <th class="bg-dark" id="thnama"
                    style="white-space: nowrap;
                position: sticky;
                left: 0;
                z-index: 999;">
                    NAMA
                </th>
                @for ($i = 1; $i <= $total; $i++)
                    <th class="text-center bg-dark tdTgl">{{ $i }}</th>
                @endfor
                <th>M</th>
                <th>OFF</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                
            @endphp

            @foreach ($karyawan as $d)
                @php
                    $totalOff = 0;
                    $totalM = 0;
                    $totalCT = 0;
                @endphp
                <tr>
                    <td class="bg-dark" id="nama"
                        style="white-space: nowrap;position: sticky;
                    left: 0;
                    margin-left:40%;
                    z-index: 999;">
                        <h5>{{ $d->nama_karyawan }}</h5>
                    </td>
                    @for ($i = 1; $i <= $total; $i++)
                        @php
                            $data = DB::table('absensi_agrilaras')
                                ->select('absensi_agrilaras.*')
                                ->where('id_karyawan', '=', $d->id_karyawan)
                                ->whereDay('tanggal_masuk', '=', $i)
                                ->whereMonth('tanggal_masuk', '=', $bulan)
                                ->whereYear('tanggal_masuk', '=', $tahun_2)
                                ->first();
                        @endphp
                        @if ($data)
                            @php
                                $statusColorMap = [
                                    'M' => 'success',
                                    'CT' => 'warning',
                                    'OFF' => 'info',
                                ];
                                $warna = $statusColorMap[$data->status];
                            @endphp
                            <td class="text-center m">
                                <button class="btnHapus btn btn-block btn-{{ $warna }}" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    {{ $data->status }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item clickEdit" id_absen="{{ $data->id }}" status="M"
                                        href="javascript:void(0);">M</a>
                                    <a class="dropdown-item clickEdit" id_absen="{{ $data->id }}" status="CT"
                                        href="javascript:void(0);">CT</a>
                                    <a class="dropdown-item clickEdit" id_absen="{{ $data->id }}" status="OFF"
                                        href="javascript:void(0);">OFF</a>
                                </div>
                            </td>
                        @else
                            <td class="bg-info m">
                                <a class="btnInput btn btn-block  btn-info" status="M"
                                id_karyawan="{{ $d->id_karyawan }}" tahun="{{ $tahun_2 }}"
                                bulan="{{ $bulan }}" tanggal="{{ $tahun_2 . '-' . $bulan . '-' . $i }}">
                                    OFF
                                </a>
                            </td>
                        @endif
                    @endfor

                </tr>
                @if ($d->id_karyawan == $d->id_karyawan)
                    @php
                        continue;
                    @endphp
                @else
                    @php
                        break;
                    @endphp
                @endif
            @endforeach
        </tbody>

    </table>
</div>
