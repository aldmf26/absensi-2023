# RENCANA & DOKUMENTASI — Sistem Absen Karyawan Mandiri (Departemen 1 "Anak Laki")

> Status: **Perencanaan final** — dokumentasi lengkap sebelum & selama eksekusi.

---

## Ringkasan Alur (Final)

1. **Admin** set PIN di `Karyawan?id_departemen=1` (hanya departemen 1). PIN disimpan `bcrypt`, unik.
2. **Karyawan** buka `/absen/login`, input **PIN saja** → session tersimpan (tahan lama, tak perlu login ulang).
3. Buka `/absen`: pilih **tanggal** (default hari ini, boleh pilih tanggal lalu) + **jenis pekerjaan** (dropdown/tombol besar).
4. **Upload foto masuk** → tersimpan, status **bekerja**.
5. Setelah selesai → klik **"Selesaikan Kejaan"** + **upload foto selesai** → status **selesai**.
6. **Hanya LEMBUR** yang mencatat **`jam_masuk` / `jam_selesai`** (datetime penuh, **WITA**). Jenis lain tidak mencatat jam.
7. **Admin** cek per tanggal di halaman ABSENSI: lihat `tanggal` kerja **dan** `created_at` (tanggal input) berdampingan → **mudah ketahuan bila salah input tanggal**, tinggal edit/hapus.
8. **Karyawan tidak bisa** akses dashboard/menu admin.
9. **Mobile-first** — desain sederhana, tombol besar, ramah pengguna yang kurang paham teknologi.

---

## Keputusan Final

| Aspek | Keputusan |
|---|---|
| **Login** | Input **PIN saja**, unik per karyawan, `bcrypt` |
| **Session** | Tahan lama (remember), terpisah dari session admin |
| **Jenis pekerjaan** | LEMBUR/JAM2AN/MINGGU GERBANG (8), absen harian (9), JGM (10), MTD (16), LIBUR DIBAYAR (12) |
| **Frekuensi** | Boleh multi jenis per hari. Cek duplikat `(karyawan + tanggal + jenis)` |
| **Tanggal** | Default hari ini, boleh tanggal lalu (bukan masa depan). Admin lihat `created_at` sbg pembanding |
| **Jam lembur** | Hanya LEMBUR: `jam_masuk`/`jam_selesai` datetime **WITA** |
| **Foto** | Simpan `storage/app/public/absen/`, path di DB. Timestamp dirender pada foto |
| **Ambil foto** | Langsung kamera (dari halaman web) ATAU dari galeri (untuk kendala jaringan) |
| **Alur foto** | Ambil → pratinjau → SIMPAN / FOTO ULANG / BATAL |
| **Akses** | Karyawan dilarang dashboard/menu admin (middleware) |
| **Departemen** | Hanya departemen 1 (Anak Laki). Departemen 4 (agri laras) tidak tersentuh |
| **Desain** | Mobile-first, 1 layar = 1 aksi, tombol besar |

---

## 1. Perubahan Database (1 migration baru)

**Tabel `karyawan`:**
| Kolom | Tipe | Ket |
|---|---|---|
| `pin_absen` | string, nullable, **unique** | PIN absensi, `bcrypt` |

**Tabel `absensi`:**
| Kolom | Tipe | Ket |
|---|---|---|
| `foto_masuk` | text, nullable | path foto masuk |
| `foto_selesai` | text, nullable | path foto selesai |
| `status` | string, default `bekerja` | `bekerja` / `selesai` |
| `jam_masuk` | datetime, nullable | **hanya lembur** (WITA) |
| `jam_selesai` | datetime, nullable | **hanya lembur** (WITA) |

`tanggal` = tanggal kerja (pilihan karyawan). `created_at` (sudah ada) = tanggal saat input → untuk deteksi salah input tanggal.

---

## 2. Route Baru

| Method | URL | Fungsi | Middleware |
|---|---|---|---|
| GET | `/absen/login` | Form login PIN | — |
| POST | `/absen/login` | Verifikasi + set session | throttle |
| POST | `/absen/logout` | Logout | absen-karyawan |
| GET | `/absen` | Form/status absen | absen-karyawan |
| POST | `/absen` | Simpan foto masuk + jenis + tanggal | absen-karyawan |
| POST | `/absen/selesai` | Upload foto selesai + jam_selesai (lembur) | absen-karyawan |

Path foto: `storage/app/public/absen/`.

---

## 3. File yang Dibuat / Diubah

**Baru:**
- Middleware `EnsureKaryawanSession`, `BlockKaryawanFromAdmin`
- `app/Http/Controllers/KaryawanAbsenController.php`
- `resources/views/absen/login.blade.php`
- `resources/views/absen/absen.blade.php`

**Diubah:**
- `app/Http/Kernel.php` (daftar middleware + alias)
- `app/Models/Karyawan.php`, `app/Models/Absensi.php`
- `resources/views/karyawan/karyawan.blade.php` (input PIN)
- `resources/views/absensi/absensi.blade.php` (tampil foto + created_at)
- `routes/web.php`

---

## 4. Keamanan (Anti-Hacker & Anti-Bug)

### Keamanan
- PIN `bcrypt`; tidak ditampilkan di list karyawan (hanya saat buat sekali).
- **Rate limit** login PIN (mis. 5× gagal → blokir 1 menit).
- Session karyawan & admin **dipisah key**; `session()->regenerate()` setelah login.
- **Eloquent / query builder** (parameterized) — hindari concat SQL manual.
- Tampilkan data pakai `{{ }}` (escape XSS).
- **Upload**: validasi mime/ext/ukuran (`jpg,jpeg,png`, max 5MB), nama file **random**, simpan di `storage` (bukan public root), file di-serve via disk `public`.
- Validasi `unique` PIN. Setiap method absen cek session.
- Middleware `BlockKaryawanFromAdmin` + dashboard require `Auth::user()`.
- CSRF aktif (laravel `@csrf`).

### Anti-Bug
- Cek duplikat **server-side** `(karyawan + tanggal + jenis + status!=selesai)`.
- `tanggal` default hari ini, larang masa depan, boleh tanggal lalu.
- Tampilkan record "sedang bekerja" → tombol "Selesaikan"; cegah mulai jenis sama sebelum selesai.
- `jam_masuk`/`jam_selesai` datetime penuh WITA (aman lintas tengah malam).
- Pakai `Storage::disk('public')`, path relatif.
- Waktu WITA eksplisit (bukan default server).
- `try/catch` pada simpan, pesan ramah, tanpa expose stack trace.

---

## 5. Flowchart Alur

```
┌───────────────────────────────┐
│ Admin set PIN di              │
│ Karyawan?id_departemen=1      │
└──────────────┬────────────────┘
               ▼
┌───────────────────────────────┐
│ Karyawan buka /absen/login    │
│ input PIN                     │
└──────────────┬────────────────┘
               ▼
        ┌──────────────┐
        │ PIN cocok?   │──Tidak──▶ "PIN salah"
        └──────┬───────┘
               │ Ya
               ▼
┌───────────────────────────────┐
│ Set session absen_karyawan    │
│ (ingat, tak perlu login lagi) │
└──────────────┬────────────────┘
               ▼
┌───────────────────────────────┐
│ Buka /absen                   │
│ • cek record "sedang bekerja" │
│   utk jenis yg dipilih        │
└──────────────┬────────────────┘
               ▼
        ┌──────────────────────────┐
        │ sedang BEKERJA utk jenis?│
        └──────┬─────────┬─────────┘
           Tidak      Ya (belum selesai)
              │           │
              ▼           ▼
  ┌────────────────┐   ┌─────────────────────────┐
  │ FORM FOTO      │   │ TOMBOL "SELESAIKAN      │
  │ MASUK          │   │  KERJAAN" + foto selesai│
  │ • tanggal      │   └───────────┬─────────────┘
  │ • jenis        │               │
  │ • foto masuk   │               │
  └────────┬───────┘               │
           ▼                       ▼
  ┌────────────────┐   ┌─────────────────────────┐
  │ simpan:        │   │ update jam_selesai +    │
  │ status=bekerja │   │ foto_selesai,           │
  │ foto_masuk     │   │ status=selesai          │
  │ (jam_masuk utk │   │ (jam utk lembur, WITA)  │
  │  lembur)       │   └───────────┬─────────────┘
  └────────┬───────┘               │
           └──────────┬────────────┘
                      ▼
  ┌──────────────────────────────────────────────┐
  │ Admin lihat per tgl: jam masuk/keluar, foto, │
  │ tanggal vs created_at (deteksi salah tgl)    │
  │ + edit/hapus                                 │
  └──────────────────────────────────────────────┘

 Karyawan TIDAK bisa buka dashboard / menu admin.
```

---

## 6. Desain UI (Mobile-First)

- 1 layar = 1 aksi. Tombol besar (min 48px). Font besar, kontras jelas.
- Tanpa sidebar admin.
- **Login**: logo + input PIN besar + tombol "MASUK" besar.
- **Pilih jenis**: tombol besar (bukan dropdown kecil).
- **Foto**: tombol "AMBIL FOTO" besar → buka kamera → pratinjau → SIMPAN / FOTO ULANG / BATAL.
- **Dari galeri**: pilihan saat jaringan buruk.
- **Status**: badge "SEDANG BEKERJA" (hijau) / "SELESAI".
- **Selesai**: tombol "SELESAIKAN KERJAAN" besar hijau.
- **Sukses**: konfirmasi besar ✅.

---

## 7. Langkah Eksekusi

1. Update MD (ini).
2. Migration `pin_absen`, `foto_masuk`, `foto_selesai`, `status`, `jam_masuk`, `jam_selesai` → `migrate`.
3. Update model Karyawan & Absensi.
4. Middleware + daftarkan di `app/Http/Kernel.php`.
5. `KaryawanAbsenController` + rate limit.
6. Views `absen/login`, `absen/absen`.
7. Update `karyawan.blade.php`, `absensi.blade.php`.
8. Route di `web.php`.
9. `php artisan storage:link`, test.

---

## 8. Catatan Teknis

- Laravel 9.52, register route middleware di `app/Http/Kernel.php` ($routeMiddleware).
- Auth admin via tabel `users` (guard bawaan). Session karyawan terpisah (`absen_karyawan`).
- Waktu lembur: `Carbon` dengan timezone **WITA** (`Asia/Makassar`, UTC+8).
