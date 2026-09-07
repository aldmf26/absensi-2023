<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Absen Karyawan</title>
    <link rel="shortcut icon" href="{{ asset('adminlte') }}/images/eabs.ico">
    <link rel="stylesheet" href="{{ asset('adminlte') }}/plugins/fontawesome-free/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: linear-gradient(160deg, #1a2980 0%, #26d0ce 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 380px;
            padding: 32px 28px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
            text-align: center;
        }
        .logo { width: 90px; margin-bottom: 14px; }
        h1 { font-size: 22px; color: #1a2980; margin-bottom: 4px; }
        .sub { color: #777; font-size: 14px; margin-bottom: 24px; }
        .btn-primary {
            width: 100%; padding: 16px; font-size: 18px; font-weight: 700; color: #fff;
            background: #1a2980; border: none; border-radius: 12px; cursor: pointer; margin-top: 16px;
        }
        .btn-primary:active { transform: scale(.98); }
        input[type=password] {
            width: 100%; padding: 16px; font-size: 22px; letter-spacing: 12px; text-align: center;
            border: 2px solid #ddd; border-radius: 12px; outline: none;
        }
        input[type=password]:focus { border-color: #1a2980; }
        .alert { padding: 14px; border-radius: 10px; margin-bottom: 16px; font-size: 15px; }
        .alert-error { background: #fee; color: #b00020; }
        .alert-sukses { background: #efd; color: #1b5e20; }
        .hint { margin-top: 16px; color: #999; font-size: 12px; }
        .btn-danger {
            margin-top: 18px; background: none; color: #888; border: 1px solid #ddd;
            border-radius: 10px; padding: 10px; width: 100%; cursor: pointer; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="card">
        <img class="logo" src="https://1.bp.blogspot.com/-efcrgVn7kNs/X3PuJdVayDI/AAAAAAAAG_s/gtyVvD55QSUJMI_zUF9ripq4VFWhu6bRQCLcBGAsYHQ/s512/EABS.png" alt="Absensi">
        <h1>Absen Karyawan</h1>
        <div class="sub">Masukkan PIN untuk masuk</div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('sukses'))
            <div class="alert alert-sukses">{{ session('sukses') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('absen.doLogin') }}">
            @csrf
            <input type="password" name="pin" inputmode="numeric" autofocus required placeholder="••••">
            <button type="submit" class="btn-primary">MASUK</button>
        </form>
        <div class="hint">PIN diberikan oleh admin</div>
    </div>
</body>
</html>
