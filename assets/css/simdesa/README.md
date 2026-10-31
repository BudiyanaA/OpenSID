# SIMDESA Design System

Standar visual SIMDESA. Halaman login admin (`/siteman`) adalah **implementasi referensi**:
bila ragu soal warna, jarak, atau bentuk komponen, samakan dengan halaman login.

## Lokasi file

| File | Isi | Dimuat di |
|---|---|---|
| `assets/css/simdesa/tokens.css` | Design tokens (CSS variables `--simdesa-*`): warna, tipografi, spasi, radius, shadow | Layout auth & layout admin |
| `assets/css/simdesa/components.css` | Komponen dasar berprefiks `sd-` | Layout auth & layout admin |
| `assets/css/simdesa/admin-theme.css` | Tema panel admin: override AdminLTE 2 / Bootstrap 3 (header, sidebar, breadcrumb, box, tombol, form, select2, tabel, pagination, label, alert/callout, modal, tabs, footer) + kartu dashboard (`.sd-dash-*`, `.sd-stat`, `.sd-feature-card`) | `resources/views/admin/layouts/index.blade.php` (body `sd-admin`) |
| `assets/css/simdesa/login.css` | Tata letak halaman auth (`.sd-auth-*`) + penyesuaian markup Bootstrap lama di dalam `.sd-auth` | `resources/views/admin/auth/index.blade.php` |
| `resources/views/admin/auth/index.blade.php` | Layout auth: login, lupa sandi, reset sandi, 2FA, login OTP | — |
| `assets/images/simdesa-logo.png` | Logo resmi SIMDESA (PNG transparan, biru). Di atas latar biru, letakkan di tile putih | `asset('images/simdesa-logo.png')` |

Urutan muat wajib: `tokens.css` → `components.css` → CSS halaman.
Di Blade: `<link rel="stylesheet" href="{{ asset('css/simdesa/tokens.css') }}">`.

## Aturan

1. **Jangan menulis warna hex, ukuran font, radius, atau shadow langsung.** Pakai `var(--simdesa-*)`.
2. Halaman/modul baru memakai kelas `sd-*`. Jangan membuat kelas tombol/kartu/tabel baru.
3. Komponen `sd-*` tidak menimpa Bootstrap 3 / AdminLTE, jadi aman dipakai berdampingan dengan modul lama.
4. Perlu varian baru? Tambahkan ke `components.css` (dengan token), bukan ke CSS halaman.
5. Bungkus area baru dengan `.sd-scope` untuk mendapat font, warna teks, dan `box-sizing` standar.

## Tokens

| Peran | Variabel | Nilai |
|---|---|---|
| Primary (SIMDESA Blue) | `--simdesa-primary` / `-dark` / `-darker` | `#1565c0` / `#0d47a1` / `#0a3478` |
| Secondary (Light Blue) | `--simdesa-primary-light`, `--simdesa-secondary` | `#e3f0fd`, `#42a5f5` |
| Teks (Dark Navy) | `--simdesa-text` / `-muted` / `-subtle` | `#0f1f3d` / `#5b6b85` / `#8a98ae` |
| Latar | `--simdesa-background` / `--simdesa-surface` | `#f5f9fe` / `#ffffff` |
| Border | `--simdesa-border` / `-strong` | `#dbe4f0` / `#c3d0e2` |
| Success | `--simdesa-success` / `-light` | `#2e7d32` / `#e8f5e9` |
| Warning | `--simdesa-warning` / `-light` | `#ef6c00` / `#fff3e0` |
| Error | `--simdesa-danger` / `-light` | `#c62828` / `#fdecea` |
| Gradient | `--simdesa-gradient-primary` (tombol utama), `--simdesa-gradient-brand` (panel branding) | biru SIMDESA |

- **Tipografi:** `--simdesa-font-sans` (Inter → font sistem, tanpa CDN agar tetap jalan offline); ukuran `--simdesa-font-size-xs` (12) s.d. `-2xl` (28), dasar 14px.
- **Spasi:** skala 4px, `--simdesa-space-1` (4) … `--simdesa-space-12` (48).
- **Radius:** `-sm` 6 (badge), default 8 (button/input), `-lg` 12 (card/modal/alert, input auth), `-xl` 16 (feature/info card), `-2xl` 24 (card utama login), `-pill`.
- **Shadow:** `--simdesa-shadow-sm` (card), `--simdesa-shadow-md` (dropdown), `--simdesa-shadow-lg` (modal), `--simdesa-focus-ring` (fokus keyboard).
- **Tinggi kontrol:** 32 (sm), 40 (default), 48 (lg), 54 (xl, form auth).
- **Ikon:** Font Awesome 4 (`fa fa-*`) yang sudah ada di aplikasi; jangan campur set ikon lain.

## Komponen

```html
<!-- Button: primary | secondary | outline | ghost | success | warning | danger; ukuran sm | lg; block -->
<button class="sd-btn sd-btn-primary">Simpan</button>
<a class="sd-btn sd-btn-outline sd-btn-sm" href="#"><i class="fa fa-download"></i> Unduh</a>

<!-- Input / Select / Textarea -->
<div class="sd-field">
  <label class="sd-label" for="nik">NIK</label>
  <input id="nik" class="sd-input" placeholder="16 digit NIK">
  <div class="sd-help">Sesuai KTP.</div>          <!-- atau .sd-error-text + .is-invalid di input -->
</div>
<div class="sd-input-icon"><i class="fa fa-user"></i><input class="sd-input" placeholder="Nama pengguna"></div>
<select class="sd-select">…</select>
<textarea class="sd-textarea"></textarea>
<label class="sd-check"><input type="checkbox"> Ingat saya</label>

<!-- Card -->
<div class="sd-card">
  <div class="sd-card-header"><h3 class="sd-card-title">Data Penduduk</h3></div>
  <div class="sd-card-body">…</div>
  <div class="sd-card-footer">…</div>
</div>

<!-- Table -->
<div class="sd-table-wrap"><table class="sd-table">…<td class="is-numeric">1.024</td>…</table></div>

<!-- Badge & Alert -->
<span class="sd-badge sd-badge-success">Aktif</span>
<div class="sd-alert sd-alert-warning"><p>Data belum lengkap.</p></div>

<!-- Modal -->
<div class="sd-modal-backdrop"><div class="sd-modal">
  <div class="sd-modal-header"><h4 class="sd-modal-title">Konfirmasi</h4></div>
  <div class="sd-modal-body">…</div>
  <div class="sd-modal-footer"><button class="sd-btn sd-btn-outline">Batal</button><button class="sd-btn sd-btn-danger">Hapus</button></div>
</div></div>

<!-- Dropdown, Tabs, Pagination, Breadcrumb -->
<div class="sd-dropdown"><ul class="sd-dropdown-menu"><li><a class="sd-dropdown-item" href="#">Ubah</a></li></ul></div>
<nav><a class="sd-tab is-active" href="#">Umum</a><a class="sd-tab" href="#">Lainnya</a></nav>   <!-- bungkus dengan .sd-tabs -->
<ul class="sd-pagination"><li><a class="sd-page is-active">1</a></li><li><a class="sd-page">2</a></li></ul>
<ol class="sd-breadcrumb"><li><a href="#">Beranda</a></li><li aria-current="page">Penduduk</li></ol>

<!-- Navbar & Sidebar (untuk halaman baru; admin lama masih AdminLTE) -->
<header class="sd-navbar"><a class="sd-navbar-brand">SIMDESA</a></header>
<aside class="sd-sidebar"><div class="sd-sidebar-heading">Kependudukan</div><a class="sd-sidebar-link is-active">Penduduk</a></aside>

<!-- Empty / Loading / Error state -->
<div class="sd-state">
  <span class="sd-state-icon"><i class="fa fa-inbox"></i></span>
  <p class="sd-state-title">Belum ada data</p>
  <p class="sd-state-text">Tambahkan data pertama untuk mulai.</p>
</div>
<div class="sd-state"><span class="sd-spinner sd-spinner-lg"></span><p class="sd-state-text">Memuat…</p></div>
<div class="sd-state sd-state-error">…</div>
<span class="sd-skeleton" style="width:60%"></span>
```

## Halaman auth

View anak cukup `@extends('admin.auth.index')` lalu mengisi:

```blade
@section('auth_title', 'Lupa kata sandi')      {{-- default: "Masuk ke SIMDESA" --}}
@section('auth_subtitle', 'Keterangan singkat')
@section('content') … form … @endsection
```

Markup Bootstrap lama (`.form-control`, `.btn`, `.alert`, `.callout`, `.otp-input`) di dalam halaman auth
otomatis ditata oleh `login.css`, tetapi markup baru sebaiknya langsung memakai kelas `sd-*`.
Gambar latar dari **Pengaturan > Aplikasi > Latar Login** tampil di panel biru kiri, dan
`desa/pengaturan/siteman/siteman.css` tetap dimuat terakhir untuk kustomisasi per desa.

Struktur halaman login (referensi pola "branding + card"):

- **Kiri `.sd-auth-brand`:** logo resmi di tile putih, headline + aksen biru muda (`.sd-accent`), 4 feature card glass (`.sd-auth-features`), kartu info desa (`.sd-auth-desa`). Ilustrasi desa/jaringan adalah SVG inline di layout, opacity rendah. Gambar *Latar Login* dilebur 10% di bawah gradient.
- **Kanan `.sd-auth-panel`:** card putih 480px radius 24, brand SIMDESA → judul → badge desa (`.sd-auth-village`) → form → BSrE kecil (hanya bila `setting('tte')`). Dekorasi blob/lingkaran/titik via pseudo-element.
- **Responsif:** ≤1199px kolom kiri 46%; ≤991px satu kolom, branding jadi header biru ringkas dan card menumpuk di atasnya; ≤575px deskripsi disembunyikan, feature card 2×2. Layar pendek (≤820px tinggi) dirapatkan agar muat satu layar di 1366×768.
- Logo/nama desa semuanya dari data aplikasi (`$header`), tidak ada yang di-hardcode.

## Tema admin (AdminLTE)

Panel admin tetap AdminLTE 2: menu, submenu, urutan, route, permission, dan JS **tidak diubah**.
`admin-theme.css` hanya menimpa tampilan kelas AdminLTE yang sudah ada.

- Aktif karena `<body class="... sd-admin ...">` di `resources/views/admin/layouts/index.blade.php`. Hapus kelas `sd-admin` untuk kembali ke tampilan AdminLTE lama.
- Selector diawali `body.sd-admin` agar menang atas skin `.skin-*` tanpa `!important`. `!important` hanya dipakai di tempat AdminLTE/admin-style.css sudah memakainya (`.alert-*`, `.callout-*`, `.btn.bg-*`, `.label-*`, header tabel).
- **Akibatnya pilihan *Warna Tema Admin* (setting `warna_tema_admin`) tidak lagi mengubah warna**, karena semua halaman admin memakai palet SIMDESA.
- Palet tombol diringkas: biru (`primary`, `info`, `bg-purple`, `bg-aqua`, `bg-blue`, `bg-teal`; `bg-navy` biru tua), hijau (`success`, `bg-olive`, `bg-green`), oranye (`warning`, `bg-orange`, `bg-yellow`), merah (`danger`, `bg-maroon`, `bg-red`).
- Sidebar: gradient biru SIMDESA, item aktif memakai latar putih transparan + garis aksen biru muda. Mode mini/collapse memakai ukuran asli AdminLTE agar flyout tetap presisi.
- Dashboard (`resources/views/admin/home/index.blade.php`): data shortcut, `@can`, dan tautan sama seperti sebelumnya; hanya markup kartu yang diganti ke `.sd-stat`. Kartu *Lapor Pak Desa* menuju route pengaduan yang sudah ada (`pengaduan_admin`) dan hanya tampil bila pengguna punya akses baca modul `pengaduan`. Kartu *Smart Desa AI* hanya informasi "Segera Hadir" (tanpa route/backend).
- CSS khusus halaman (`@push('css')`) tetap dimuat sesudah tema, seperti sebelumnya.

Pola untuk halaman baru di admin: pakai markup AdminLTE biasa (`.box`, `.table`, `.btn`) — sudah otomatis bergaya SIMDESA — atau komponen `sd-*` untuk elemen baru.

## Dashboard (Beranda)

"One-screen executive dashboard": pada desktop 1366×768, 1440×900, dan 1920×1080 seluruh ringkasan tampil tanpa scroll; tablet dan mobile memakai scroll normal.

| File | Isi |
|---|---|
| `app/Services/DashboardBeranda.php` | Pengambil data **baca-saja**. Memakai model/service/library existing: `LaporanPenduduk` (rentang umur & jenis kelamin, sama dengan menu Statistik), `LogSurat`, `Pengaduan`, `LogPenduduk`, `PermohonanSurat`, `Pembangunan` (+ dokumentasi progres), Library `Keuangan::grafik_keuangan_tema()` (tahun anggaran terbaru, sama dengan menu Keuangan > Laporan). Cache 5 menit per desa. Setiap bagian dibungkus try/catch → bila gagal, bagian itu tampil sebagai empty state dan error dicatat di log. |
| `resources/views/admin/home/index.blade.php` | Tata letak dashboard + cek hak akses per bagian (`can('b', '<slug>')`). |
| `resources/views/admin/home/partials/*.blade.php` | `statistik`, `pengaduan`, `pembangunan`, `keuangan`, `kosong` (empty state). |
| `assets/css/simdesa/dashboard.css` | Gaya dashboard; dimuat hanya di beranda. |
| `assets/js/simdesa/dashboard-charts.js` | Grafik Highcharts (library existing, hanya `highcharts.js`). Warna dari token `--simdesa-chart-*`. |

Catatan:
- Tidak ada angka contoh/dummy; bagian tanpa data menampilkan empty state.
- Setiap bagian hanya tampil bagi pengguna yang punya akses baca ke modul sumbernya (penduduk, arsip-layanan, pengaduan, pembangunan, laporan/laporan-manual, lapak; aktivitas disaring per jenis).
- `admin-style.css` mewarnai `.highcharts-color-0/1` secara global, maka grafik dashboard memakai `colorIndex` ≥ 10.
- Tinggi grafik mengikuti viewport (`clamp()` + flex), tanpa tinggi tetap pada panel, sehingga konten tidak pernah terpotong. Jumlah item daftar menyesuaikan tinggi layar (3 item; 5 pengaduan / 4 proyek pada layar ≥ 1000px tinggi); selengkapnya lewat "Lihat Semua".
- **Akses Cepat** (baris terbawah, di samping Aktivitas): 3 feature card `.sd-feature` — *Lapor Pak Desa* → route pengaduan existing (`pengaduan_admin`, tampil bila berhak), *Smart Desa AI* → placeholder "Segera Hadir" tanpa tautan/route/backend, *Marketplace Desa* → modul Lapak existing (`lapak_admin`, tampil bila berhak). Warna aksen per kartu lewat `--sd-accent` (amber/ungu/hijau di atas dasar biru SIMDESA).
- Pengaduan tidak memiliki kolom kategori, jadi yang ditampilkan: judul, pelapor, status, waktu.

## Audit branding (untuk tahap berikutnya)

Sudah SIMDESA: `config_item('nama_aplikasi')`, halaman login, header (logo resmi, mini & besar), sidebar, topbar, breadcrumb, dashboard, dan komponen AdminLTE umum di seluruh halaman admin.

Belum diubah (di luar cakupan tahap 1):

| Prioritas | Lokasi | Temuan |
|---|---|---|
| Browser title login | Pengaturan > Aplikasi > **Login Title** (setting `login_title`, seed awal `OpenSID`) | Diubah lewat menu pengaturan, bukan kode (nilai database) |
| Favicon | `favicon.ico` di root, via `favico_desa()` | Masih ikon lama |
| Error page | `donjo-app/views/errors/cli/*` (teks OpenSID/OpenDesa); `donjo-app/views/errors/html/*` belum memakai gaya SIMDESA | Rebranding + restyle |
| Info lisensi | `resources/views/admin/layouts/partials/control_sidebar.blade.php` | Menyebut OpenSID/OpenDesa — **atribusi lisensi GPL v3, jangan dihapus** |
| Layanan mandiri | `resources/views/layanan_mandiri/auth/index.blade.php`, `resources/views/layanan_mandiri/layouts/index.blade.php` | Layout terpisah, belum memakai tema SIMDESA |
| Halaman lain di luar layout admin | `resources/views/periksa/*`, `resources/views/token/index.blade.php`, `resources/views/tools/index.blade.php`, Anjungan, Kehadiran | Layout sendiri, belum memakai tema SIMDESA |
