<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Absen Karyawan</title>
    <link rel="shortcut icon" href="{{ asset('adminlte') }}/images/eabs.ico">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f2f4f8; min-height: 100vh; padding-bottom: 40px;
        }
        .topbar {
            background: linear-gradient(135deg, #1a2980, #26d0ce); color: #fff;
            padding: 18px 18px 22px; border-radius: 0 0 24px 24px;
        }
        .topbar .hello { font-size: 16px; opacity: .9; }
        .topbar h2 { font-size: 24px; margin-top: 2px; }
        .topbar .logout {
            float: right; background: rgba(255,255,255,.18); border: none; color: #fff;
            padding: 8px 14px; border-radius: 20px; font-size: 14px; cursor: pointer; text-decoration: none;
        }
        .container { max-width: 460px; margin: 0 auto; padding: 0 16px; }
        .card {
            background: #fff; border-radius: 16px; padding: 18px; margin-top: 16px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
        }
        .card h3 { font-size: 17px; color: #1a2980; margin-bottom: 12px; }
        .badge {
            display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
        }
        .badge-bekerja { background: #fff3cd; color: #7a5b00; }
        .badge-selesai { background: #d4edda; color: #155724; }
        .item {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 12px 0; border-top: 1px solid #f0f0f0;
        }
        .item:first-of-type { border-top: none; }
        .item .nama { font-weight: 600; font-size: 15px; }
        .item .info { color: #777; font-size: 13px; margin-top: 2px; }
        .btn {
            border: none; border-radius: 12px; cursor: pointer; font-weight: 700;
        }
        .btn:active { transform: scale(.98); }
        .btn-green { background: #28a745; color: #fff; font-size: 16px; padding: 14px 18px; width: 100%; }
        .btn-yellow { background: #ffc107; color: #333; font-size: 15px; padding: 12px 18px; white-space: nowrap; }
        .btn-blue { background: #1a2980; color: #fff; font-size: 16px; padding: 15px 18px; width: 100%; }
        .btn-ghost { background: #f0f0f0; color: #666; padding: 13px 18px; width: 100%; font-size: 15px; }
        .btn-sm { background: #eef2ff; color: #1a2980; font-size: 13px; padding: 8px 14px; }
        .alert { padding: 14px; border-radius: 12px; margin-top: 16px; font-size: 15px; }
        .alert-error { background: #fee; color: #b00020; }
        .alert-sukses { background: #efd; color: #155724; }
        .jenis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .jenis-btn {
            border: 2px solid #ddd; background: #fff; border-radius: 14px; padding: 16px 8px;
            font-size: 15px; font-weight: 700; color: #333; cursor: pointer; text-align: center; min-height: 62px;
            display: flex; align-items: center; justify-content: center;
        }
        .jenis-btn.selected { border-color: #1a2980; background: #eef2ff; color: #1a2980; }
        input[type=date], input[type=text], input[type=time] {
            width: 100%; padding: 14px; font-size: 16px; border: 2px solid #ddd; border-radius: 12px; outline: none;
        }
        input:focus { border-color: #1a2980; }
        label { display: block; font-size: 14px; font-weight: 600; color: #555; margin: 14px 0 6px; }
        .photo-box {
            border: 2px dashed #bbb; border-radius: 14px; padding: 24px; text-align: center; cursor: pointer;
            background: #fafafa;
        }
        .photo-box img { max-width: 100%; max-height: 200px; border-radius: 10px; }
        .photo-preview { display: none; }
        .photo-box.terpilih { padding: 10px; border-style: solid; }
        .photo-box.terpilih .photo-placeholder { display: none; }
        .photo-placeholder { cursor: pointer; }
        .preview-actions { display: flex; gap: 8px; margin-top: 10px; }
        .preview-actions .btn { flex: 1; padding: 12px; font-size: 15px; }
        .hidden { display: none; }
        .empty { color: #999; font-size: 14px; text-align: center; padding: 14px 0; }
        .foto-mini { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; flex-shrink: 0; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="container">
        <a class="logout" href="{{ route('absen.logout') }}" onclick="return confirm('Keluar?')">Keluar</a>
        <div class="hello">Selamat datang 👋</div>
        <h2>{{ strtoupper($karyawan->nama_karyawan) }}</h2>
    </div>
</div>

<div class="container">
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('sukses'))
        <div class="alert alert-sukses">{{ session('sukses') }}</div>
    @endif

    {{-- ===== SEDANG BEKERJA (tombol selesaikan) ===== --}}
    @if($sedangBekerja->isNotEmpty())
        <div class="card">
            <h3>⏰ Sedang Bekerja</h3>
            @foreach($sedangBekerja as $a)
                <div class="item">
                    <div>
                        <div class="nama">{{ $a->karyawan && $a->karyawan->nama_karyawan ? $a->karyawan->nama_karyawan : 'Absen' }}</div>
                        <div class="info">{{ optional($a->jenis)->jenis_pekerjaan ?? 'Pekerjaan' }} — {{ $a->tanggal }}</div>
                        <div><span class="badge badge-bekerja">SEDANG BEKERJA</span></div>
                    </div>
                    <button type="button" class="btn btn-yellow btn-selesai" data-id="{{ $a->id_absen }}">SELESAIKAN</button>
                </div>

                {{-- Form selesaikan: upload foto selesai --}}
                <form id="selesai-form-{{ $a->id_absen }}" method="POST" action="{{ route('absen.selesai') }}" enctype="multipart/form-data" class="hidden" style="margin-top:10px;">
                    @csrf
                    <input type="hidden" name="id_absen" value="{{ $a->id_absen }}">
                    <div class="photo-box" id="selesai-box-{{ $a->id_absen }}">
                        <div class="photo-placeholder">
                            <i class="fas fa-camera fa-2x"></i>
                            <div>Ambil Foto Selesai</div>
                        </div>
                        <img id="img-{{ $a->id_absen }}" src="" alt="pratinjau" class="hidden">
                        <input type="file" name="foto" id="file-{{ $a->id_absen }}" accept="image/*"  class="hidden">
                        <div class="preview-actions hidden" id="actions-{{ $a->id_absen }}">
                            <button type="button" class="btn btn-ghost" onclick="batalkanFoto('{{ $a->id_absen }}')">FOTO ULANG</button>
                            <button type="submit" class="btn btn-green">✓ SIMPAN</button>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    @endif

    {{-- ===== FORM ABSEN BARU ===== --}}
    <div class="card">
        <h3>➕ Absen Baru</h3>
        <form id="absen-form" method="POST" action="{{ route('absen.store') }}" enctype="multipart/form-data">
            @csrf
            <label>Jenis Pekerjaan</label>
            <div class="jenis-grid" id="jenisGrid">
                @foreach($jenis as $j)
                    <button type="button" class="jenis-btn" data-id="{{ $j->id }}" data-nama="{{ $j->jenis_pekerjaan }}">{{ $j->jenis_pekerjaan }}</button>
                @endforeach
            </div>
            <input type="hidden" name="id_jenis" id="id_jenis" value="">

            <div id="jamLembur" class="hidden">
                <div style="display:flex;gap:12px;">
                    <div style="flex:1;">
                        <label>Jam Mulai Lembur</label>
                        <input type="time" name="jam_mulai" id="jam_mulai">
                    </div>
                    <div style="flex:1;">
                        <label>Jam Selesai Lembur</label>
                        <input type="time" name="jam_selesai" id="jam_selesai">
                    </div>
                </div>
            </div>

            <label>Tanggal</label>
            <input type="date" name="tanggal" max="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}" value="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}">
            <div class="info" style="color:#aaa;font-size:12px;margin-top:4px;">Otomatis hari ini. Bisa ubah ke tanggal lalu bila lupa absen.</div>

            <label>Foto Masuk</label>
            <div class="photo-box" id="box-masuk">
                <div class="photo-placeholder">
                    <i class="fas fa-camera fa-2x"></i>
                    <div>Ambil Foto Masuk</div>
                </div>
                <img id="img-masuk" src="" alt="pratinjau" class="hidden">
                <input type="file" name="foto" id="file-masuk" accept="image/*"  class="hidden" required>
                <div class="preview-actions hidden" id="actions-masuk">
                    <button type="button" class="btn btn-ghost" onclick="batalkanFoto('masuk')">FOTO ULANG</button>
                </div>
            </div>

            <label>Keterangan (opsional)</label>
            <input type="text" name="ket" maxlength="255" placeholder="contoh: lembur 2 jam, jaga malam, dll">

            <button type="submit" id="btn-submit-absen" class="btn btn-blue" style="margin-top:18px;">MULAI ABSEN</button>
        </form>
    </div>

    {{-- ===== RIWAYAT HARI INI ===== --}}
    <div class="card">
        <h3>📋 Riwayat Hari Ini</h3>
        @if($riwayat->isEmpty())
            <div class="empty">Belum ada absen selesai hari ini.</div>
        @else
            @foreach($riwayat as $a)
                <div class="item">
                    @if($a->foto_masuk)
                        <img class="foto-mini" src="{{ asset($a->foto_masuk) }}" alt="masuk">
                    @endif
                    <div style="flex:1">
                        <div class="nama">{{ optional($a->jenis)->jenis_pekerjaan ?? 'Pekerjaan' }}</div>
                        <div class="info">{{ $a->tanggal }}</div>
                        <div><span class="badge badge-selesai">SELESAI</span></div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>

<script>
    // Pilih jenis pekerjaan
    document.querySelectorAll('.jenis-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.jenis-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            document.getElementById('id_jenis').value = btn.dataset.id;
            // Tampilkan input jam khusus lembur (id 8)
            const jamLembur = document.getElementById('jamLembur');
            if (btn.dataset.id === '8') {
                jamLembur.classList.remove('hidden');
                document.getElementById('jam_mulai').required = true;
                document.getElementById('jam_selesai').required = true;
            } else {
                jamLembur.classList.add('hidden');
                document.getElementById('jam_mulai').required = false;
                document.getElementById('jam_selesai').required = false;
            }
        });
    });

    // Selesaikan: tampilkan form upload foto selesai + buka kamera
    document.querySelectorAll('.btn-selesai').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const form = document.getElementById('selesai-form-' + id);
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                const fileInput = document.getElementById('file-' + id);
                if (fileInput) fileInput.click();
            }
        });
    });

    // Foto selesai: pratinjau
    document.querySelectorAll('input[type=file]').forEach(input => {
        input.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const key = this.id === 'file-masuk'
                ? 'masuk'
                : this.id.replace('file-', '');
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('img-' + key);
                if (!img) return;
                img.src = e.target.result;
                img.classList.remove('hidden');
                const box = key === 'masuk' ? document.getElementById('box-masuk')
                                           : document.getElementById('selesai-box-' + key);
                box.classList.add('terpilih');
                const actions = document.getElementById('actions-' + key);
                if (actions) actions.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    });

    // Klik placeholder kamera -> buka file input
    document.querySelectorAll('.photo-placeholder').forEach(ph => {
        ph.addEventListener('click', () => {
            const box = ph.closest('.photo-box');
            const fileInput = box.querySelector('input[type=file]');
            if (fileInput) fileInput.click();
        });
    });

    // FOTO ULANG / BATAL: kembalikan ke state awal
    function batalkanFoto(key) {
        const img = document.getElementById('img-' + key);
        if (img) { img.src = ''; img.classList.add('hidden'); }
        const box = key === 'masuk' ? document.getElementById('box-masuk')
                                   : document.getElementById('selesai-box-' + key);
        if (box) box.classList.remove('terpilih');
        const actions = document.getElementById('actions-' + key);
        if (actions) actions.classList.add('hidden');
        const input = key === 'masuk' ? document.getElementById('file-masuk')
                                     : document.getElementById('file-' + key);
        if (input) input.value = '';
    }
</script>

</body>
</html>
