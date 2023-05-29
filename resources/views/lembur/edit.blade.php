<div class="row">
    <div class="col-lg-3">
        <label for="">Tanggal</label>
        <input type="date" class="form-control tanggal_edit" name="tanggal" value="{{$absen_edit->tgl}}">
        <input type="hidden" class="form-control id_absen_lembur_edit" name="tanggal"
            value="{{$absen_edit->id_absen_lembur}}">
    </div>

    <div class="col-lg-2">
        <label for="">Pilih Karyawan</label>
        <Select class="form-control select2 id_karyawan_edit" name="id_karyawan[]">
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $k)

            <option value="{{$k->id_karyawan}}" {{$absen_edit->id_karyawan == $k->id_karyawan ? 'selected' :
                ''}}>{{$k->nama_karyawan}}</option>
            @endforeach
        </Select>
    </div>
    <div class="col-lg-3">
        <label for="">Pekerjaan</label>
        <input type="text" class="form-control pekerjaan_edit" name="pekerjaan[]" value="{{$absen_edit->pekerjaan}}">
    </div>
    <div class="col-lg-2">
        <label for="">Jam awal</label>
        <input type="time" class="form-control j_awal_edit" name="j_awal[]" value="17:00:00" readonly>
    </div>
    <div class="col-lg-2 ">
        <label for="">Jam akhir</label>
        <input type="time" class="form-control j_akhir_edit" name="j_akhir[]" value="{{$absen_edit->j_akhir}}">
    </div>

</div>