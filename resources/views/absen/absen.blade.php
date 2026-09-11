<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Absen Karyawan</title>
    <link rel="shortcut icon" href="{{ asset('adminlte') }}/images/eabs.ico">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#198754">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Absensi">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f2f4f8;
            min-height: 100vh;
            padding-bottom: 90px;
            touch-action: manipulation;
        }

        .topbar {
            background: linear-gradient(135deg, #1a2980, #26d0ce);
            color: #fff;
            padding: 18px 18px 22px;
            border-radius: 0 0 24px 24px;
        }

        .topbar .hello {
            font-size: 16px;
            opacity: .9;
        }

        .topbar h2 {
            font-size: 24px;
            margin-top: 2px;
        }

        .topbar .logout {
            float: right;
            background: rgba(255, 255, 255, .18);
            border: none;
            color: #fff;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .container {
            max-width: 460px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 18px;
            margin-top: 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .06);
        }

        .card h3 {
            font-size: 17px;
            color: #1a2980;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-bekerja {
            background: #fff3cd;
            color: #7a5b00;
        }

        .badge-selesai {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #e7f1ff;
            color: #0a5395;
        }

        .tabbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 460px;
            margin: 0 auto;
            display: flex;
            background: #fff;
            border-top: 1px solid #e6e6e6;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, .06);
            z-index: 900;
        }

        .tabbtn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px 0 14px;
            cursor: pointer;
            color: #888;
            font-weight: 700;
            font-size: 11px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .tabbtn .tab-ico {
            font-size: 19px;
        }

        .tabbtn.active {
            color: #1a2980;
        }

        .tabbtn.active .tab-ico {
            transform: translateY(-2px);
        }

        .chip {
            border: 1.5px solid #ddd;
            background: #fff;
            color: #555;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .chip.active {
            border-color: #1a2980;
            background: #eef2ff;
            color: #1a2980;
        }

        .grup-nama {
            font-size: 12px;
            font-weight: 800;
            color: #1a2980;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin: 16px 0 4px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .cuti-jenis {
            display: flex;
            gap: 8px;
        }

        .jenis-btn {
            flex: 1;
            border: 2px solid #ddd;
            background: #fff;
            color: #555;
            border-radius: 12px;
            padding: 12px 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .jenis-btn.active {
            border-color: #1a2980;
            background: #eef2ff;
            color: #1a2980;
        }

        .in-cuti {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 12px;
        }

        .hint {
            color: #aaa;
            font-size: 12px;
            font-weight: 400;
        }

        .info-cuti {
            font-size: 14px;
            font-weight: 700;
            color: #1a2980;
            margin-top: 6px;
            min-height: 20px;
        }

        .cuti-lanjutan {
            margin-top: 14px;
        }

        .cuti-lanjutan summary {
            cursor: pointer;
            color: #555;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 0;
        }

        .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 0;
            border-top: 1px solid #f0f0f0;
        }

        .item:first-of-type {
            border-top: none;
        }

        .item .nama {
            font-weight: 600;
            font-size: 15px;
        }

        .item .info {
            color: #777;
            font-size: 13px;
            margin-top: 2px;
        }

        .btn {
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .btn:active {
            transform: scale(.98);
        }

        .btn[disabled] {
            opacity: .5;
            pointer-events: none;
        }

        .btn-green {
            background: #28a745;
            color: #fff;
            font-size: 16px;
            padding: 14px 18px;
            width: 100%;
        }

        .btn-yellow {
            background: #ffc107;
            color: #333;
            font-size: 15px;
            padding: 12px 18px;
            white-space: nowrap;
        }

        .btn-blue {
            background: #1a2980;
            color: #fff;
            font-size: 16px;
            padding: 15px 18px;
            width: 100%;
        }

        .btn-ghost {
            background: #f0f0f0;
            color: #666;
            padding: 13px 18px;
            width: 100%;
            font-size: 15px;
        }

        .btn-sm {
            background: #eef2ff;
            color: #1a2980;
            font-size: 13px;
            padding: 8px 14px;
        }

        .btn-ghost-outline {
            background: transparent;
            border: 2px solid #ffc107;
            color: #ffc107;
            font-size: 13px;
            padding: 11px 14px;
            white-space: nowrap;
        }

        .alert {
            padding: 14px;
            border-radius: 12px;
            margin-top: 16px;
            font-size: 15px;
        }

        .alert-error {
            background: #fee;
            color: #b00020;
        }

        .alert-sukses {
            background: #efd;
            color: #155724;
        }

        .jenis-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .jenis-btn {
            border: 2px solid #ddd;
            background: #fff;
            border-radius: 14px;
            padding: 12px 8px;
            font-size: 15px;
            font-weight: 700;
            color: #333;
            cursor: pointer;
            text-align: center;
            min-height: 62px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .jenis-sub {
            font-size: 11px;
            font-weight: 400;
            color: #888;
            margin-top: 3px;
        }

        .jenis-btn.selected {
            border-color: #1a2980;
            background: #eef2ff;
            color: #1a2980;
        }

        .jenis-btn.selected .jenis-sub {
            color: #555;
        }

        input[type=date],
        input[type=text],
        input[type=time] {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 12px;
            outline: none;
        }

        input:focus {
            border-color: #1a2980;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            margin: 14px 0 6px;
        }

        .photo-box {
            border: 2px dashed #bbb;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            background: #fafafa;
        }

        .photo-box img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
        }

        .photo-preview {
            display: none;
        }

        .photo-box.terpilih {
            padding: 10px;
            border-style: solid;
        }

        .photo-box.terpilih .photo-placeholder {
            display: none;
        }

        .photo-placeholder {
            cursor: pointer;
        }

        .preview-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .preview-actions .btn {
            flex: 1;
            padding: 12px;
            font-size: 15px;
        }

        .hidden {
            display: none;
        }

        .empty {
            color: #999;
            font-size: 14px;
            text-align: center;
            padding: 14px 0;
        }

        .foto-mini {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            flex-shrink: 0;
        }

        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1200;
        }

        #loading-overlay.show {
            display: flex;
        }

        .spinner {
            width: 42px;
            height: 42px;
            border: 5px solid #eef2ff;
            border-top-color: #1a2980;
            border-radius: 50%;
            margin: 0 auto;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
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
        @php
            $awalTab = session('tab_pilihan', request('tab', ''));
            if (!in_array($awalTab, ['tambah', 'cuti', 'riwayat'], true)) {
                $awalTab = request()->has('bulan') || request()->has('tahun') ? 'riwayat' : 'tambah';
            }
            $tampilBtnCepat = $sedangBekerja->isEmpty() && $selesaiHariIni->isEmpty() && !$adaCutiHariIni;
        @endphp
        @if (session('error'))
            <div class="alert alert-error">⚠️ {{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">⚠️ {{ $errors->first() }}</div>
        @endif
        @if (session('sukses'))
            <div class="alert alert-sukses">✅ {{ session('sukses') }}</div>
        @endif

        {{-- ===== STATUS HARI INI ===== --}}
        <div class="card">
            <h3>📌 Status Hari Ini</h3>
            @if ($sedangBekerja->isEmpty() && $selesaiHariIni->isEmpty())
                @if ($adaCutiHariIni)
                    <div class="empty">🏖️ Hari ini cuti/libur. Tidak perlu absen.</div>
                @else
                    <div class="empty" style="margin-bottom:12px;">Belum absen hari ini.</div>
                    <button type="button" id="btn-absen-cepat" class="btn btn-green">📸 ABSEN SEKARANG (MASUK)</button>

                    {{-- Form cepat: 1 ketuk -> kamera -> submit otomatis sebagai Absen Harian hari ini --}}
                    <form id="form-absen-cepat" method="POST" action="{{ route('absen.store') }}"
                        enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="hidden" name="tanggal"
                            value="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}">
                        <input type="hidden" name="id_jenis" value="9">
                        <input type="file" name="foto" id="foto-absen-cepat" accept="image/*" class="hidden"
                            required>
                    </form>
                @endif
            @else
                @foreach ($sedangBekerja as $a)
                    <div class="item">
                        <div>
                            <div class="nama">{{ optional($a->jenis)->jenis_pekerjaan ?? 'Pekerjaan' }}</div>
                            <div class="info">{{ \Carbon\Carbon::parse($a->tanggal)->format('d-m-Y') }}, masuk
                                {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</div>
                            @if ($a->id_jenis_pekerjaan == 8 && ! $a->jam_selesai)
                                <div class="info" style="color:#b7791f;">⏱️ Jam selesai lembur belum diisi (isi saat menekan SELESAI)</div>
                            @endif
                            <div><span class="badge badge-bekerja">SEDANG BEKERJA</span></div>
                        </div>
                        <button type="button" class="btn btn-yellow btn-selesai" data-id="{{ $a->id_absen }}">SELESAI
                            BEKERJA (PULANG)</button>
                    </div>

                    {{-- Form selesaikan: upload foto selesai --}}
                    <form id="selesai-form-{{ $a->id_absen }}" method="POST" action="{{ route('absen.selesai') }}"
                        enctype="multipart/form-data" class="hidden" style="margin-top:10px;"
                        data-lembur="{{ $a->id_jenis_pekerjaan == 8 ? '1' : '0' }}"
                        data-butuh-jam="{{ $a->id_jenis_pekerjaan == 8 && ! $a->jam_selesai ? '1' : '0' }}">
                        @csrf
                        <input type="hidden" name="id_absen" value="{{ $a->id_absen }}">
                        <input type="hidden" name="lembur" value="0">
                        <input type="hidden" name="jam_lembur" value="">
                        <input type="hidden" name="ket_lembur" value="">
                        <div class="photo-box" id="selesai-box-{{ $a->id_absen }}">
                            <div class="photo-placeholder">
                                <i class="fas fa-camera fa-2x"></i>
                                <div>Ambil Foto Selesai</div>
                            </div>
                            <img id="img-{{ $a->id_absen }}" src="" alt="pratinjau" class="hidden">
                            <input type="file" name="foto" id="file-{{ $a->id_absen }}" accept="image/*"
                                class="hidden">
                            <div class="preview-actions hidden" id="actions-{{ $a->id_absen }}">
                                <button type="button" class="btn btn-ghost"
                                    onclick="batalkanFoto('{{ $a->id_absen }}')">FOTO ULANG</button>
                                <button type="submit" class="btn btn-green">✓ SIMPAN</button>
                            </div>
                        </div>
                    </form>
                @endforeach
                @foreach ($selesaiHariIni as $a)
                    <div class="item">
                        @if ($a->foto_masuk)
                            <img class="foto-mini" src="{{ asset($a->foto_masuk) }}" alt="masuk">
                        @elseif($a->foto_selesai)
                            <img class="foto-mini" src="{{ asset($a->foto_selesai) }}" alt="selesai">
                        @endif
                        <div style="flex:1">
                            <div class="nama">{{ optional($a->jenis)->jenis_pekerjaan ?? 'Pekerjaan' }}</div>
                            <div class="info">
                                @if ($a->jam_masuk)
                                    {{ \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') }}
                                @endif
                                @if ($a->jam_selesai)
                                    – {{ \Carbon\Carbon::parse($a->jam_selesai)->format('H:i') }}
                                @endif
                            </div>
                            <div><span class="badge badge-selesai">SELESAI</span></div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ===== PANEL TAMBAH ABSEN ===== --}}
        <div id="tab-tambah" class="tab-panel {{ $awalTab !== 'tambah' ? 'hidden' : '' }}">
            <div class="card">
                <h3 style="display:flex;justify-content:space-between;align-items:center;gap:8px;">➕ Tambah Absen
                    <button type="button" class="btn btn-sm" id="btn-toggle-tambah">▲ Sembunyikan</button>
                </h3>
                <form id="absen-form" method="POST" action="{{ route('absen.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <label>Tanggal</label>
                    <input type="date" name="tanggal"
                        max="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}"
                        value="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}">
                    <div style="color:#aaa;font-size:12px;margin-top:4px;">Otomatis hari ini. Bisa ubah ke tanggal lalu
                        bila lupa absen.</div>

                    <label>Jenis Pekerjaan</label>
                    <div class="jenis-grid" id="jenisGrid">
                        @foreach ($jenis as $j)
                            <button type="button" class="jenis-btn" data-id="{{ $j->id }}"
                                data-nama="{{ $j->jenis_pekerjaan }}">
                                {{ $labelJenis[$j->id] ?? $j->jenis_pekerjaan }}
                                @if ($j->id == 8)
                                    <div class="jenis-sub">termasuk Minggu jaga gerbang</div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="id_jenis" id="id_jenis" value="">

                    <div id="jamLembur" class="hidden">
                        <div style="display:flex;gap:12px;">
                            <div style="flex:1;">
                                <label>Jam Mulai Lembur</label>
                                <input type="time" name="jam_mulai" id="jam_mulai" required>
                            </div>
                            <div style="flex:1;">
                                <label>Jam Selesai Lembur (opsional)</label>
                                <input type="time" name="jam_selesai" id="jam_selesai">
                            </div>
                        </div>
                        <div style="color:#aaa;font-size:12px;margin-top:4px;">Kosongkan jika belum tahu jam selesainya. Nanti diisi saat menekan tombol SELESAI.</div>
                    </div>

                    <label>Foto Masuk</label>
                    <div class="photo-box" id="box-masuk">
                        <div class="photo-placeholder">
                            <i class="fas fa-camera fa-2x"></i>
                            <div>Ambil Foto Masuk</div>
                        </div>
                        <img id="img-masuk" src="" alt="pratinjau" class="hidden">
                        <input type="file" name="foto" id="file-masuk" accept="image/*" class="hidden"
                            required>
                        <div class="preview-actions hidden" id="actions-masuk">
                            <button type="button" class="btn btn-ghost" onclick="batalkanFoto('masuk')">FOTO
                                ULANG</button>
                        </div>
                    </div>

                    <label>Keterangan (opsional)</label>
                    <input type="text" name="ket" maxlength="255"
                        placeholder="contoh: lembur 2 jam, jaga malam, dll">

                    <button type="submit" id="btn-submit-absen" class="btn btn-blue" style="margin-top:18px;">MULAI
                        ABSEN</button>
                </form>
            </div>
        </div>

        {{-- ===== PANEL CUTI ===== --}}
        <div id="tab-cuti" class="tab-panel hidden">
            <div class="card">
                <h3>🏖️ Cuti / Libur
                    <span class="badge badge-selesai" style="float:right;">Sisa Cuti Tahunan: {{ $sisaJatah }} hari</span>
                </h3>
                <form id="form-cuti" method="POST" action="{{ route('absen.cuti') }}">
                    @csrf
                    <input type="hidden" name="jenis_cuti" id="jenisCutiValue" value="17">
                    <input type="hidden" name="mode_cuti" id="modeCuti" value="">

                    <label>Jenis</label>
                    <div class="cuti-jenis" id="jenisCuti">
                        <button type="button" class="jenis-btn active" data-nilai="17">🏖️ Cuti Tahunan</button>
                        <button type="button" class="jenis-btn" data-nilai="12">🛫 Pulang Luar Kota</button>
                    </div>

                    <label style="margin-top:16px;">Cara Pilih Tanggal</label>
                    <div class="cuti-jenis" id="modeCutiBtn">
                        <button type="button" class="jenis-btn" data-mode="rentang">📅 Rentang Tanggal</button>
                        <button type="button" class="jenis-btn" data-mode="terpisah">📝 Tanggal Terpisah</button>
                    </div>

                    <div id="blok-rentang" class="hidden" style="margin-top:12px;">
                        <div style="display:flex;gap:8px;">
                            <input type="date" name="tanggal_mulai" id="tgl-mulai" class="in-cuti"
                                value="{{ \Carbon\Carbon::now('Asia/Makassar')->toDateString() }}">
                            <input type="date" name="tanggal_sampai" id="tgl-sampai" class="in-cuti">
                        </div>
                        <div class="hint" style="margin-top:4px;">Hari ini = cukup kirim begitu saja. Isi tanggal
                            sampai untuk beberapa hari berurutan (mis. 05–08).</div>
                    </div>

                    <div id="blok-terpisah" class="hidden" style="margin-top:12px;">
                        <div id="daftar-tanggal-cuti">
                            <div style="display:flex;gap:8px;margin-bottom:8px;">
                                <input type="date" name="tanggal_cuti[]" class="tgl-cuti" style="flex:1;">
                                <button type="button" class="btn btn-sm"
                                    onclick="hapusTanggalCuti(this);hitungHariCuti()">✕</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm" id="btn-tambah-tgl" style="width:100%;">+ TAMBAH
                            TANGGAL</button>
                    </div>

                    <div id="info-cuti" class="info-cuti" style="margin-top:8px;"></div>

                    <label>Keterangan (opsional)</label>
                    <input type="text" name="ket" maxlength="255" placeholder="contoh: acara keluarga">
                    <button type="submit" id="btn-simpan-cuti" class="btn btn-blue" style="margin-top:18px;"
                        disabled>SIMPAN CUTI</button>
                </form>
            </div>
        </div>

        {{-- ===== PANEL RIWAYAT ===== --}}
        <div id="tab-riwayat" class="tab-panel {{ $awalTab !== 'riwayat' ? 'hidden' : '' }}">
            <div class="card">
                <h3>📋 Riwayat Absen</h3>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:12px;">
                    <a class="btn btn-sm" href="{{ route('absen.index', $prev) }}">◀</a>
                    <input type="month" id="pilih-bulan"
                        value="{{ $tahun }}-{{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }}"
                        style="flex:1;max-width:170px;padding:8px 10px;border:2px solid #ddd;border-radius:10px;font-size:15px;font-weight:700;color:#1a2980;text-align:center;">
                    <a class="btn btn-sm" href="{{ route('absen.index', $next) }}">▶</a>
                </div>

                @if ($riwayatBulan->isEmpty())
                    <div class="empty">Tidak ada absen pada bulan ini.</div>
                @else
                    {{-- Filter chip per jenis (sekaligus ringkasan total) --}}
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;" id="filterJenis">
                        <button type="button" class="chip active" data-jenis="all">Semua ({{ $riwayatBulan->sum(fn($a) => $a->jumlah_hari ?? 1) }})</button>
                        @foreach ($riwayatBulan->groupBy('id_jenis_pekerjaan') as $jid => $grup)
                            <button type="button" class="chip"
                                data-jenis="{{ $jid }}">{{ $labelJenis[(int) $jid] ?? (optional($grup->first()->jenis)->jenis_pekerjaan ?? 'Jenis ' . $jid) }} ({{ $grup->sum(fn($a) => $a->jumlah_hari ?? 1) }})</button>
                        @endforeach
                    </div>

                    @foreach ($riwayatBulan->groupBy('id_jenis_pekerjaan') as $jid => $grup)
                        <div class="grup-jenis" data-jenis="{{ $jid }}">
                            <div class="grup-nama">
                                {{ $labelJenis[(int) $jid] ?? (optional($grup->first()->jenis)->jenis_pekerjaan ?? 'Jenis ' . $jid) }}
                            </div>
                            @foreach ($grup as $a)
                                <div class="item">
                                    @if ($a->foto_masuk)
                                        <img class="foto-mini" src="{{ asset($a->foto_masuk) }}" alt="masuk">
                                    @elseif($a->foto_selesai)
                                        <img class="foto-mini" src="{{ asset($a->foto_selesai) }}" alt="selesai">
                                    @endif
                                    <div style="flex:1">
                                        <div class="info">
                                            {{ \Carbon\Carbon::parse($a->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                            @if ($a->jam_masuk)
                                                · {{ \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') }}
                                            @endif
                                            @if ($a->jam_selesai)
                                                – {{ \Carbon\Carbon::parse($a->jam_selesai)->format('H:i') }}
                                            @endif
                                        </div>
                                        <span
                                            class="badge {{ $a->status === 'bekerja' ? 'badge-bekerja' : 'badge-selesai' }}">{{ strtoupper($a->status) }}</span>
                                        @if ($a->jumlah_hari && $a->jumlah_hari > 1)
                                            <span class="badge badge-info">{{ $a->jumlah_hari }} hari</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

    {{-- Bottom tab bar --}}
    <nav class="tabbar">
        <button type="button" class="tabbtn {{ $awalTab === 'tambah' ? 'active' : '' }}" data-tab="tambah"><span
                class="tab-ico">➕</span>Tambah Absen</button>
        <button type="button" class="tabbtn {{ $awalTab === 'cuti' ? 'active' : '' }}" data-tab="cuti"><span
                class="tab-ico">🏖️</span>Cuti</button>
        <button type="button" class="tabbtn {{ $awalTab === 'riwayat' ? 'active' : '' }}" data-tab="riwayat"><span
                class="tab-ico">📋</span>Riwayat</button>
    </nav>

    {{-- Pilih sumber foto: kamera / galeri (biar pilihan konsisten di Android & iPhone) --}}
    <input type="file" id="input-kamera" accept="image/*" capture="environment" class="hidden">
    <div id="sheet-sumber-foto">
        <div class="sheet-mask" onclick="tutupSheetSumber()"></div>
        <div class="sheet-panel">
            <div class="sheet-title">Pilih sumber foto</div>
            <button type="button" class="btn btn-green" onclick="pilihSumber('kamera')">📷 Ambil Foto</button>
            <button type="button" class="btn btn-blue" onclick="pilihSumber('galeri')">🗂️ Dari Galeri</button>
            <button type="button" class="btn btn-ghost" onclick="tutupSheetSumber()">Batal</button>
        </div>
    </div>
    <style>
        #sheet-sumber-foto {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 998;
        }
        #sheet-sumber-foto.show {
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        .sheet-mask {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .5);
        }
        .sheet-panel {
            position: relative;
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 22px 18px calc(22px + env(safe-area-inset-bottom));
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 -8px 30px rgba(0, 0, 0, .25);
        }
        .sheet-title {
            text-align: center;
            font-weight: 700;
            font-size: 15px;
            color: #333;
            margin-bottom: 4px;
        }
        .btn-blue {
            background: #2563eb;
            color: #fff;
        }
    </style>

    {{-- Dialog tanya lembur saat selesai --}}
    <div id="lembur-dialog">
        <div style="background:#fff;color:#111;padding:20px;border-radius:10px;width:90%;max-width:340px;">
            <h4 style="margin:0 0 8px;" id="lembur-judul">Selesai hari ini? 👋</h4>
            <p style="margin:0 0 12px;font-size:14px;" id="lembur-desc">Kalau sudah selesai bekerja, tekan tombol hijau. Kalau mau
                lembur lagi, tekan tombol kuning.</p>
            <div id="lembur-jam-box" class="hidden" style="margin-bottom:12px;">
                <label for="lembur-jam" style="font-size:13px;">Jam Selesai Lembur</label>
                <input type="time" id="lembur-jam"
                    style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
            </div>
            <div id="lembur-foto-box" class="hidden" style="margin-bottom:12px;">
                <label style="font-size:13px;">Foto Selesai Lembur</label>
                <div class="photo-box" id="lembur-photo-box">
                    <div class="photo-placeholder" id="lembur-photo-placeholder">
                        <i class="fas fa-camera fa-2x"></i>
                        <div>Ambil Foto Lembur</div>
                    </div>
                    <img id="img-lembur-selesai" src="" alt="pratinjau" class="hidden" style="width:100%;">
                    <input type="file" name="foto_lembur" id="file-lembur-selesai" accept="image/*"
                        class="hidden">
                </div>
            </div>
            <div id="lembur-ket-box" class="hidden" style="margin-bottom:12px;">
                <label for="lembur-ket" style="font-size:13px;">Keterangan (opsional)</label>
                <input type="text" id="lembur-ket" maxlength="255" placeholder="contoh: lembur proyek"
                    style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="button" id="lembur-ya" class="btn btn-ghost-outline">➕ LANJUT LEMBUR</button>
                <button type="button" id="lembur-no" class="btn btn-green"
                    style="flex:1 1 0;width:auto;padding:16px 18px;font-size:17px;">✅ SELESAI (PULANG)</button>
            </div>
        </div>
    </div>
    <style>
        #lembur-dialog {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        #lembur-dialog.show {
            display: flex;
        }
    </style>

    {{-- Overlay loading --}}
    <div id="loading-overlay">
        <div style="background:#fff;border-radius:12px;padding:24px 32px;text-align:center;">
            <div class="spinner"></div>
            <div id="loading-text" style="margin-top:12px;font-weight:700;color:#1a2980;">Menyimpan...</div>
        </div>
    </div>

    <script>
        // Pilih jenis pekerjaan (HANYA di panel Tambah Absen)
        document.querySelectorAll('#jenisGrid .jenis-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#jenisGrid .jenis-btn').forEach(b => b.classList.remove(
                    'selected'));
                btn.classList.add('selected');
                document.getElementById('id_jenis').value = btn.dataset.id;
                // Tampilkan input jam khusus lembur (id 8)
                const jamLembur = document.getElementById('jamLembur');
                if (btn.dataset.id === '8') {
                    jamLembur.classList.remove('hidden');
                    document.getElementById('jam_mulai').required = true;
                    document.getElementById('jam_selesai').required = false;
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

        // Kompres foto di HP sebelum dikirim agar upload cepat (max 1600px, JPEG 80)
        function kompresGambar(file, cb) {
            if (!file || !/^image\/(png|jpe?g|heic|heif)$/i.test(file.type) || file.size <= 300 * 1024) {
                cb(null);
                return;
            }
            const img = new Image();
            const url = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(url);
                const MAX = 1600;
                const sc = Math.min(1, MAX / Math.max(img.width, img.height));
                const w = Math.max(1, Math.round(img.width * sc));
                const h = Math.max(1, Math.round(img.height * sc));
                const cv = document.createElement('canvas');
                cv.width = w;
                cv.height = h;
                cv.getContext('2d').drawImage(img, 0, 0, w, h);
                cv.toBlob(function (blob) {
                    if (!blob) { cb(null); return; }
                    const nama = file.name.replace(/\.[^.]+$/i, '.jpg');
                    cb(new File([blob], nama, { type: 'image/jpeg' }));
                }, 'image/jpeg', 0.8);
            };
            img.onerror = function () { URL.revokeObjectURL(url); cb(null); };
            img.src = url;
        }

        // Ganti file yang terpilih dengan versi terkompresi (kalau menguntungkan)
        function gantiDenganKompresi(input, next) {
            const file = input.files && input.files[0];
            if (!file) { if (next) next(); return; }
            kompresGambar(file, function (hasil) {
                if (hasil) {
                    try {
                        const dt = new DataTransfer();
                        dt.items.add(hasil);
                        input.files = dt.files;
                    } catch (e) { /* kirim file asli bila tidak didukung */ }
                }
                if (next) next();
            });
        }

        // Pilih sumber foto (kamera / galeri) dari satu sheet agar konsisten di semua HP
        let targetFotoInput = null;

        function bukaSheetSumber(targetInput) {
            targetFotoInput = targetInput;
            document.getElementById('sheet-sumber-foto').classList.add('show');
        }

        function tutupSheetSumber() {
            document.getElementById('sheet-sumber-foto').classList.remove('show');
            targetFotoInput = null;
        }

        function pilihSumber(mode) {
            const input = targetFotoInput;
            tutupSheetSumber();
            if (!input) return;
            if (mode === 'galeri') {
                input.click();
                return;
            }
            // Kamera: pakai input capture lingkungan; hasilnya disalin ke input tujuan
            const kam = document.getElementById('input-kamera');
            kam.onchange = function() {
                const f = kam.files[0];
                kam.value = '';
                if (!f) return;
                const dt = new DataTransfer();
                dt.items.add(f);
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            };
            kam.click();
        }

        // Overlay loading
        function tampilkanLoading(teks) {
            document.getElementById('loading-text').textContent = teks || 'Menyimpan...';
            document.getElementById('loading-overlay').classList.add('show');
        }

        // ABSEN SEKARANG (MASUK): 1 ketuk -> kamera -> kirim otomatis sebagai Absen Harian hari ini
        @if ($tampilBtnCepat)
            const btnCepat = document.getElementById('btn-absen-cepat');
            const fotoCepat = document.getElementById('foto-absen-cepat');
            const formCepat = document.getElementById('form-absen-cepat');
            if (btnCepat && fotoCepat && formCepat) {
                btnCepat.addEventListener('click', () => bukaSheetSumber(fotoCepat));
                fotoCepat.addEventListener('change', function() {
                    if (!this.files.length) return;
                    // Kompres dulu (biar cepat), baru kirim
                    gantiDenganKompresi(this, () => {
                        const fd = new FormData(formCepat);
                        tampilkanLoading('Menyimpan...');
                        fetch(formCepat.action, {
                                method: 'POST',
                                body: fd
                            })
                            .then(r => {
                                if (r.redirected) {
                                    window.location.href = r.url;
                                    return;
                                }
                                window.location.reload();
                            })
                            .catch(() => window.location.reload());
                    });
                });
            }
        @endif

        // Kirim form selesai via fetch (agar foto lembur ikut terlampir walau di luar form)
        let fileLembur = null;

        function kirimSelesai(f) {
            const fd = new FormData(f);
            if (fileLembur) {
                fd.append('foto_lembur', fileLembur);
                fileLembur = null;
            }
            tampilkanLoading('Menyimpan...');
            fetch(f.action, {
                    method: 'POST',
                    body: fd
                })
                .then(r => {
                    if (r.redirected) {
                        window.location.href = r.url;
                        return;
                    }
                    window.location.reload();
                })
                .catch(() => window.location.reload());
        }

        // Tanya lembur saat SIMPAN selesai
        let pendingForm = null;
        let lemburStep = 0; // 0 = tanya, 1 = konfirmasi jam
        function tutupLemburDialog() {
            document.getElementById('lembur-dialog').classList.remove('show');
            document.getElementById('lembur-jam-box').classList.add('hidden');
            document.getElementById('lembur-foto-box').classList.add('hidden');
            document.getElementById('lembur-ket-box').classList.add('hidden');
            document.getElementById('lembur-ket').value = '';
            document.getElementById('lembur-no').style.display = '';
            document.getElementById('lembur-judul').textContent = 'Selesai hari ini? 👋';
            document.getElementById('lembur-desc').textContent =
                'Kalau sudah selesai bekerja, tekan tombol hijau. Kalau mau lembur lagi, tekan tombol kuning.';
            const limg = document.getElementById('img-lembur-selesai');
            if (limg) {
                limg.src = '';
                limg.classList.add('hidden');
            }
            const lbox = document.getElementById('lembur-photo-box');
            if (lbox) lbox.classList.remove('terpilih');
            const lf = document.getElementById('file-lembur-selesai');
            if (lf) lf.value = '';
            document.getElementById('lembur-ya').textContent = '➕ LANJUT LEMBUR';
            lemburStep = 0;
            pendingForm = null;
        }
        document.querySelectorAll('form[id^="selesai-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // Baris yang sudah LEMBUR (jenis 8) dengan jam selesai sudah ada: langsung selesai, tanpa tanya lembur.
                if (form.dataset.lembur === '1' && form.dataset.butuhJam !== '1') {
                    tampilkanLoading('Menyimpan...');
                    form.submit();
                    return;
                }
                pendingForm = form;
                lemburStep = 0;
                document.getElementById('lembur-jam').value = new Date().toTimeString().slice(0, 5);
                if (form.dataset.lembur === '1') {
                    // Lembur tanpa jam selesai: dialog hanya minta jam selesai lembur.
                    document.getElementById('lembur-judul').textContent = 'Selesai lembur? 👋';
                    document.getElementById('lembur-desc').textContent = 'Isi jam selesai lembur, lalu simpan.';
                    document.getElementById('lembur-jam-box').classList.remove('hidden');
                    document.getElementById('lembur-no').style.display = 'none';
                    document.getElementById('lembur-ya').textContent = '✓ SIMPAN';
                    lemburStep = 1;
                }
                document.getElementById('lembur-dialog').classList.add('show');
            });
        });
        document.getElementById('lembur-dialog').addEventListener('click', function(e) {
            if (e.target === this) tutupLemburDialog();
        });
        document.getElementById('lembur-no').addEventListener('click', () => {
            if (!pendingForm) return;
            const f = pendingForm;
            f.querySelector('input[name="lembur"]').value = '0';
            f.querySelector('input[name="jam_lembur"]').value = '';
            f.querySelector('input[name="ket_lembur"]').value = '';
            tutupLemburDialog();
            kirimSelesai(f);
        });
        document.getElementById('lembur-ya').addEventListener('click', () => {
            if (!pendingForm) return;
            const f = pendingForm;
            if (f.dataset.lembur === '1') {
                // Baris lembur (jenis 8) tanpa jam selesai: isi jam selesai saja, foto sudah lewat form inline.
                const jam = document.getElementById('lembur-jam').value;
                if (!jam) {
                    alert('Isi dulu jam selesai lembur.');
                    return;
                }
                f.querySelector('input[name="lembur"]').value = '0';
                f.querySelector('input[name="jam_lembur"]').value = jam;
                f.querySelector('input[name="ket_lembur"]').value = '';
                tutupLemburDialog();
                kirimSelesai(f);
                return;
            }
            if (lemburStep === 0) {
                lemburStep = 1;
                document.getElementById('lembur-jam-box').classList.remove('hidden');
                document.getElementById('lembur-foto-box').classList.remove('hidden');
                document.getElementById('lembur-ket-box').classList.remove('hidden');
                document.getElementById('lembur-ya').textContent = '✓ SIMPAN LEMBUR';
                return;
            }
            const jam = document.getElementById('lembur-jam').value;
            if (!jam) {
                alert('Isi dulu jam selesai lembur.');
                return;
            }
            const lf = document.getElementById('file-lembur-selesai');
            if (!lf.files.length) {
                alert('Ambil dulu foto selesai lembur.');
                return;
            }
            fileLembur = lf.files[0];
            f.querySelector('input[name="lembur"]').value = '1';
            f.querySelector('input[name="jam_lembur"]').value = jam;
            f.querySelector('input[name="ket_lembur"]').value = document.getElementById('lembur-ket').value.trim();
            tutupLemburDialog();
            kirimSelesai(f);
        });

        // Tab navigasi
        function pilihTab(nama) {
            ['tambah', 'cuti', 'riwayat'].forEach(t => {
                document.getElementById('tab-' + t).classList.toggle('hidden', t !== nama);
            });
            document.querySelectorAll('.tabbtn').forEach(b => {
                b.classList.toggle('active', b.dataset.tab === nama);
            });
        }
        document.querySelectorAll('.tabbtn').forEach(b => {
            b.addEventListener('click', () => pilihTab(b.dataset.tab));
        });

        // Toggle sembunyi/tampil form Tambah Absen
        const btnToggleTambah = document.getElementById('btn-toggle-tambah');
        btnToggleTambah.addEventListener('click', () => {
            const form = document.getElementById('absen-form');
            form.classList.toggle('hidden');
            btnToggleTambah.textContent = form.classList.contains('hidden') ? '▼ Tampilkan' : '▲ Sembunyikan';
        });

        // Lompat cepat ke bulan/tahun tertentu di riwayat
        document.getElementById('pilih-bulan').addEventListener('change', function() {
            const [th, bl] = this.value.split('-');
            if (bl && th) window.location = '{{ url('absen') }}?bulan=' + parseInt(bl, 10) + '&tahun=' + parseInt(
                th, 10);
        });

        // Filter riwayat per jenis
        document.querySelectorAll('#filterJenis .chip').forEach(c => {
            c.addEventListener('click', () => {
                document.querySelectorAll('#filterJenis .chip').forEach(x => x.classList.remove('active'));
                c.classList.add('active');
                const jenis = c.dataset.jenis;
                document.querySelectorAll('.grup-jenis').forEach(g => {
                    g.style.display = (jenis === 'all' || g.dataset.jenis === jenis) ? '' : 'none';
                });
            });
        });

        // Cuti / Libur mandiri
        const sisaJatah = {{ $sisaJatah }};
        let jenisCuti = 17;

        function ymd(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2,
                '0');
        }

        function tanggalCutiTerpilih() {
            const hasil = [];
            const mulai = document.getElementById('tgl-mulai').value;
            const sampai = document.getElementById('tgl-sampai').value;
            if (mulai) {
                hasil.push(mulai);
                if (sampai && sampai >= mulai) {
                    const d = new Date(mulai + 'T00:00:00');
                    const end = new Date(sampai + 'T00:00:00');
                    while (d <= end) {
                        hasil.push(ymd(d));
                        d.setDate(d.getDate() + 1);
                    }
                }
            }
            document.querySelectorAll('#daftar-tanggal-cuti .tgl-cuti').forEach(i => {
                if (i.value) hasil.push(i.value);
            });
            return [...new Set(hasil)];
        }

        function hitungHariCuti() {
            const info = document.getElementById('info-cuti');
            if (!document.getElementById('modeCuti').value) {
                info.textContent = '';
                return;
            }
            const n = tanggalCutiTerpilih().length;
            if (!n) {
                info.textContent = '';
                return;
            }
            let teks = 'Total: ' + n + ' hari';
            if (jenisCuti === 17) {
                const sisa = sisaJatah - n;
                teks += sisa >= 0 ?
                    ' · sisa jatah ' + sisa + ' hari' :
                    ' · <span style="color:#b00020;">kelebihan ' + (-sisa) + ' hari TIDAK DIBAYAR</span>';
            }
            info.innerHTML = teks;
        }

        document.querySelectorAll('#jenisCuti .jenis-btn').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('#jenisCuti .jenis-btn').forEach(x => x.classList.remove(
                    'active'));
                b.classList.add('active');
                jenisCuti = parseInt(b.dataset.nilai, 10);
                document.getElementById('jenisCutiValue').value = jenisCuti;
                hitungHariCuti();
            });
        });

        function aturRentang() {
            const mulai = document.getElementById('tgl-mulai');
            const sampai = document.getElementById('tgl-sampai');
            if (sampai.value && mulai.value && sampai.value < mulai.value) {
                const t = sampai.value;
                sampai.value = mulai.value;
                mulai.value = t;
            }
            hitungHariCuti();
        }

        document.getElementById('tgl-mulai').addEventListener('change', aturRentang);
        document.getElementById('tgl-sampai').addEventListener('change', aturRentang);
        document.querySelectorAll('#daftar-tanggal-cuti .tgl-cuti').forEach(i => i.addEventListener('change',
            hitungHariCuti));

        document.querySelectorAll('#modeCutiBtn .jenis-btn').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('#modeCutiBtn .jenis-btn').forEach(x => x.classList.remove(
                    'active'));
                b.classList.add('active');
                const mode = b.dataset.mode;
                document.getElementById('modeCuti').value = mode;
                document.getElementById('blok-rentang').classList.toggle('hidden', mode !== 'rentang');
                document.getElementById('blok-terpisah').classList.toggle('hidden', mode !== 'terpisah');
                document.getElementById('btn-simpan-cuti').removeAttribute('disabled');
                if (mode === 'rentang') {
                    document.querySelectorAll('#daftar-tanggal-cuti .tgl-cuti').forEach(i => i.value = '');
                } else {
                    document.getElementById('tgl-mulai').value = '';
                    document.getElementById('tgl-sampai').value = '';
                }
                hitungHariCuti();
            });
        });

        function hapusTanggalCuti(btn) {
            const row = btn.closest('div');
            const list = document.getElementById('daftar-tanggal-cuti');
            if (list.children.length <= 1) {
                row.querySelector('.tgl-cuti').value = '';
            } else {
                row.remove();
            }
            hitungHariCuti();
        }
        document.getElementById('btn-tambah-tgl').addEventListener('click', () => {
            const list = document.getElementById('daftar-tanggal-cuti');
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;';
            row.innerHTML = '<input type="date" name="tanggal_cuti[]" class="tgl-cuti" style="flex:1;">' +
                '<button type="button" class="btn btn-sm" onclick="hapusTanggalCuti(this)">✕</button>';
            list.appendChild(row);
            row.querySelector('.tgl-cuti').addEventListener('change', hitungHariCuti);
        });
        hitungHariCuti();

        document.getElementById('form-cuti').addEventListener('submit', function() {
            tampilkanLoading('Menyimpan...');
        });

        // Foto selesai: pratinjau + kompresi otomatis
        document.querySelectorAll('input[type=file]').forEach(input => {
            input.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                // Kompres file bila menguntungkan (kecuali alur ABSEN SEKARANG yang sudah mengkompres)
                if (this.id !== 'foto-absen-cepat') {
                    gantiDenganKompresi(this);
                }
                const key = this.id === 'file-masuk' ?
                    'masuk' :
                    this.id === 'file-lembur-selesai' ?
                    'lembur-selesai' :
                    this.id.replace('file-', '');
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('img-' + key);
                    if (!img) return;
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    const box = key === 'masuk' ? document.getElementById('box-masuk') :
                        key === 'lembur-selesai' ? document.getElementById('lembur-photo-box') :
                        document.getElementById('selesai-box-' + key);
                    if (box) box.classList.add('terpilih');
                    const actions = document.getElementById('actions-' + key);
                    if (actions) actions.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        });

        // Klik placeholder kamera -> pilih sumber foto (kamera / galeri)
        document.querySelectorAll('.photo-placeholder').forEach(ph => {
            ph.addEventListener('click', () => {
                const box = ph.closest('.photo-box');
                const fileInput = box.querySelector('input[type=file]');
                if (fileInput) bukaSheetSumber(fileInput);
            });
        });

        // FOTO ULANG / BATAL: kembalikan ke state awal
        function batalkanFoto(key) {
            const img = document.getElementById('img-' + key);
            if (img) {
                img.src = '';
                img.classList.add('hidden');
            }
            const box = key === 'masuk' ? document.getElementById('box-masuk') :
                document.getElementById('selesai-box-' + key);
            if (box) box.classList.remove('terpilih');
            const actions = document.getElementById('actions-' + key);
            if (actions) actions.classList.add('hidden');
            const input = key === 'masuk' ? document.getElementById('file-masuk') :
                document.getElementById('file-' + key);
            if (input) input.value = '';
        }

        // MULAI ABSEN: tampilkan loading
        document.getElementById('absen-form').addEventListener('submit', function(e) {
            if (!document.getElementById('id_jenis').value) {
                e.preventDefault();
                alert('Pilih dulu jenis pekerjaan (Harian / Lembur / Jaga Malam / dll).');
                return;
            }
            tampilkanLoading('Menyimpan...');
        });

        // Label tombol dinamis: tanggal hari ini = "MULAI ABSEN", tanggal lalu = "SIMPAN ABSEN"
        const inputTanggalAbsen = document.querySelector('#absen-form input[name=tanggal]');
        const btnAbsen = document.getElementById('btn-submit-absen');

        function perbaruiLabelAbsen() {
            const d = new Date();
            const hariIni = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate())
                .padStart(2, '0');
            btnAbsen.textContent = inputTanggalAbsen.value && inputTanggalAbsen.value < hariIni ? 'SIMPAN ABSEN' :
                'MULAI ABSEN';
        }
        if (inputTanggalAbsen && btnAbsen) {
            inputTanggalAbsen.addEventListener('change', perbaruiLabelAbsen);
            perbaruiLabelAbsen();
        }

        // Kalau ada pesan error/sukses, langsung gulir ke atas biar tidak terlewat
        if (document.querySelector('.alert')) {
            window.scrollTo(0, 0);
        }
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/serviceworker.js').catch(function (err) {
                    console.warn('Service worker gagal:', err);
                });
            });
        }
    </script>

</body>

</html>
