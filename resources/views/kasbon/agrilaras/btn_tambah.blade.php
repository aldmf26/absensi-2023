<div class="row baris{{$count}}">
    <div class="col-lg-8">
        <div class="form-group">
            <select name="id_karyawan[]" class="form-control select2-tambah" id="">
                <option value="">- Pilih Karyawan -</option>
                @foreach ($karyawan as $k)
                    <option value="{{ $k->id_karyawan }}">{{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <input type="text" name="nominal[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-1">
        <div class="form-group">
            <a count="{{ $count }}" class="btn btn-sm btn-danger remove_baris"><i class="fas fa-minus"></i></a>
        </div>
    </div>
</div>