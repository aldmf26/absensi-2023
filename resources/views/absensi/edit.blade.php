    <input type="hidden" name="id_absen" value="{{ $d->id_absen }}">
    <label for="">Nama Karyawan</label>
    <select class="form-control" name="id_karyawan" id="">
        @foreach ($karyawan as $p)
            <option value="{{ $p->id_karyawan }}" {{ $p->id_karyawan == $d->id_karyawan ? 'selected' : '' }}>
                {{ $p->nama_karyawan }}</option>
        @endforeach
    </select>
    <label for="">Tanggal</label>
    <input type="date" value="{{ $d->tanggal }}" name="tanggal" class="form-control mb-3">
    <label for="">Jenis Pekerjaan</label>
    <select class="form-control" name="id_jenis" id="">
        @foreach ($jenis_pekerjaan as $p)
            <option value="{{ $p->id }}" {{ $p->id == $d->id_jenis_pekerjaan ? 'selected' : '' }}>
                {{ $p->jenis_pekerjaan }}</option>
        @endforeach
    </select>
    <label for="">Pemakai Jasa</label>
    <select class="form-control" name="id_pemakai" id="">
        @foreach ($pemakai as $p)
            <option value="{{ $p->id_pemakai }}" {{ $p->id_pemakai == $d->id_pemakai ? 'selected' : '' }}>
                {{ $p->pemakai }}</option>
        @endforeach
    </select>
    <label for="">Keterangan</label>
    <input type="text" value="{{ $d->ket }}" name="keterangan" class="form-control mb-3">
    <input type="submit" name="simpan" value="Simpan" id="tombol" class="btn btn-primary mt-3">
    <button type="button" class="btn btn-secondary  mt-3" data-dismiss="modal">Close</button>
