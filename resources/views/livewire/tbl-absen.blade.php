<div>
    <style>
        th {
            position: sticky;
            top: 0;
            z-index: 998;
        }

        .scrl {
            overflow: auto;
        }
    </style>
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <select wire:model="valBulan" id="bulan" class="form-control mb-3 " name="bulan">
                <option value="">--Pilih Bulan-- </option>
                @foreach($listBulan as $key => $value)
                    <option value="{{ $key }}" {{ (int) date('m') == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <select wire:model="valTahun" id="tahun" class="form-control mb-3 " name="tahun">
                <option value="">--Pilih Tahun--</option>
                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                @for ($i = date('Y'); $i <= date('Y') + 3; $i++)
                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-lg-4">
            <a id="download" target="_blank" class="btn btn-sm btn-success mb-3" href="#">
                <i class="fa fa-download"></i> DOWNLOAD
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            
            <div class="card">
                <table class="table table-md table-stripped table-bordered" width="100%">
                    <thead class="table-success">
                        <tr>

                            <th
                                style="white-space: nowrap;position: sticky;
                                left: 0;
                                z-index: 999;">
                                NAMA
                            </th>
                            @for ($i = 1; $i <= $totalTgl; $i++)
                                <th class="text-center">{{ $i }}</th>
                            @endfor
                            <th>M</th>
                            <th>CT</th>
                            <th>SP</th>
                            <th>OFF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawan as $d)
                            <tr>
                                <td class="bg-dark"
                                    style="white-space: nowrap;position: sticky;
                                    left: 0;
                                    z-index: 999;">
                                    <h5>{{ $d->nama_karyawan }}</h5>
                                </td>
                                @for ($i = 1; $i <= $totalTgl; $i++)
                                    @php
                                        $data = DB::table('absensi_agrilaras')
                                            ->select('absensi_agrilaras.*')
                                            ->where('id_karyawan', '=', $d->id_karyawan)
                                            ->whereDay('tanggal_masuk', '=', $i)
                                            ->whereMonth('tanggal_masuk', '=', $valBulan)
                                            ->whereYear('tanggal_masuk', '=', $valTahun)
                                            ->first();
                                    @endphp

                                    @if ($data)
                                        <td class="text-center m">
                                            @php
                                                $statusColorMap = [
                                                    'M' => 'success',
                                                    'CT' => 'warning',
                                                    'SP' => 'primary',
                                                ];
                                                $warna = $statusColorMap[$data->status];
                                            @endphp
                                            <div class="dropdown">
                                                <button class="btnHapus btn btn-block btn-{{ $warna }}"
                                                    id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    {{ $data->status }}
                                                </button>
                                                <ul class="dropdown-menu tutup" aria-labelledby="dropdownMenuButton1">
                                                    <li>
                                                        <a wire:click="clickEdit({{ $data->id }}, 'E')"
                                                            style="width:60px;"
                                                            class="btnUpdate btn text-center btn-warning mb-3">E</a>
                                                    </li>
                                                    <li>
                                                        <a wire:click="clickEdit({{ $data->id }}, 'SP')"
                                                            style="width:60px;"
                                                            class="btnUpdate btn text-center btn-primary mb-3">SP</a>
                                                    </li>
                                                    <li>
                                                        <a wire:click="clickEdit({{ $data->id }}, 'OFF')"
                                                            style="width:60px;"
                                                            class="btnUpdate btn text-center btn-info mb-3">OFF</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    @else
                                        <td class="bg-info m">
                                            <a wire:click="clickOff({{ $d->id_karyawan }}, {{ $i }})"
                                                class="btnInput btn btn-block  btn-info">
                                                OFF
                                            </a>
                                        </td>
                                    @endif
                                @endfor
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'M') }}</td>
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'E') }}</td>
                                <td class="bg-light">{{ $this->getTotal($d->id_karyawan, 'SP') }}</td>
                                <td class="bg-light">
                                    {{ $totalTgl - $this->getTotal($d->id_karyawan, 'M') + $this->getTotal($d->id_karyawan, 'E') + $this->getTotal($d->id_karyawan, 'SP') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                
            </div>
        </div>
    </div>
</div>
