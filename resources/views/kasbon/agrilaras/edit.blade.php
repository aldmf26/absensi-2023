<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <input type="hidden" name="id_kasbon" value="{{ $kasbon->id_kasbon }}">
            <input type="hidden" name="id_departemen" value="{{ request()->get('id_departemen') }}">
            <label for="">Tanggal</label>
            <input value="{{ $kasbon->tgl }}" type="date" name="tgl" class="form-control">
        </div>
    </div>
    <div class="col-lg-5">
        <div class="form-group">
            <label for="">Nama Karyawan</label>
            <select name="id_karyawan" class="form-control select2" id="">
                <option value="">- Pilih Karyawan -</option>
                @foreach ($karyawan as $k)
                    <option {{$k->id_karyawan == $kasbon->id_karyawan ? 'selected' : ''}} value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label for="">Nominal</label>
            <input value="{{ $kasbon->nominal }}" type="text" name="nominal" class="form-control">
        </div>
    </div>
</div>