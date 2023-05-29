<div class="row mt-2" id="row{{$count}}">
    <div class="col-lg-3">
        <label for="">Pilih Karyawan</label>
        <Select class="form-control select2 id_karyawan" name="id_karyawan[]">
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $k)
            <option value="{{$k->id_karyawan}}">{{$k->nama_karyawan}}</option>
            @endforeach
        </Select>
    </div>
    <div class="col-lg-4">
        <label for="">Pekerjaan</label>
        <input type="text" class="form-control pekerjaan" name="pekerjaan[]">
    </div>
    <div class="col-lg-2">
        <label for="">Jam awal</label>
        <input type="time" class="form-control j_awal" name="j_awal[]" value="17:00:00" readonly>
    </div>
    <div class="col-lg-2 ">
        <label for="">Jam akhir</label>
        <input type="time" class="form-control j_akhir" name="j_akhir[]">
    </div>
    <div class="col-lg-1">
        <label for="">Aksi</label> <br>
        <button type="button" class="btn btn-danger btn-sm remove_monitoring" count="{{$count}}"><i
                class="fas fa-minus"></i></button>
    </div>
</div>