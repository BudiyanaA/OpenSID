<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ setting('login_title') . ' ' . ucwords(setting('sebutan_desa')) . ($header['nama_desa'] ? ' ' . $header['nama_desa'] : '') . get_dynamic_title_page_from_path() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#0d47a1">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.bar.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/sweetalert2/sweetalert2.min.css') }}">
    {{-- SIMDESA design system: lihat assets/css/simdesa/README.md --}}
    <link rel="stylesheet" href="{{ asset('css/simdesa/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simdesa/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simdesa/login.css') }}">
    @if (is_file('desa/pengaturan/siteman/siteman.css'))
        <link rel='stylesheet' href="{{ base_url('desa/pengaturan/siteman/siteman.css') }}">
    @endif
    <link rel="shortcut icon" href="{{ favico_desa() }}" />
    <style type="text/css">
        .sd-auth-brand-photo {
            background-image: url('{{ $latar_login }}');
        }
    </style>
    <script src="{{ asset('bootstrap/js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/validasi.js') }}"></script>
    <script src="{{ asset('js/localization/messages_id.js') }}"></script>
    <script src="{{ asset('js/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @include('admin.layouts.components.token')
</head>

@php
    $namaAplikasi = config_item('nama_aplikasi');
    $logoAplikasi = asset('images/simdesa-logo.png');
    $sebutanDesa = ucwords(setting('sebutan_desa'));
    $namaDesa = trim($sebutanDesa . ' ' . $header['nama_desa']);
    $wilayahDesa = collect([
        $header['nama_kecamatan'] ? ucwords(setting('sebutan_kecamatan')) . ' ' . $header['nama_kecamatan'] : null,
        $header['nama_kabupaten'] ? ucwords(setting('sebutan_kabupaten')) . ' ' . $header['nama_kabupaten'] : null,
    ])->filter()->implode(', ');
    $alamatDesa = collect([
        $header['alamat_kantor'],
        $wilayahDesa,
        $header['kode_pos'] ? 'Kodepos ' . $header['kode_pos'] : null,
    ])->filter()->implode(', ');
    $fiturAplikasi = [
        ['icon' => 'fa-users', 'label' => 'Administrasi Kependudukan'],
        ['icon' => 'fa-file-text-o', 'label' => 'Surat Menyurat'],
        ['icon' => 'fa-globe', 'label' => 'Informasi & Website Desa'],
        ['icon' => 'fa-mobile', 'label' => 'Layanan Digital Warga'],
    ];
@endphp

<body class="login sd-auth">
    <div class="sd-auth-shell">
        {{-- ============ Branding (kiri) ============ --}}
        <aside class="sd-auth-brand">
            <div class="sd-auth-brand-photo" aria-hidden="true"></div>
            <svg class="sd-auth-brand-art" viewBox="0 0 800 900" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
                <defs>
                    <radialGradient id="sdGlow" cx="50%" cy="50%" r="50%">
                        <stop offset="0" stop-color="#90caf9" stop-opacity=".55" />
                        <stop offset="1" stop-color="#90caf9" stop-opacity="0" />
                    </radialGradient>
                    <pattern id="sdGrid" width="28" height="28" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1.3" fill="#fff" fill-opacity=".16" />
                    </pattern>
                </defs>
                {{-- cahaya lembut --}}
                <circle cx="690" cy="140" r="260" fill="url(#sdGlow)" />
                <circle cx="80" cy="820" r="220" fill="url(#sdGlow)" opacity=".6" />
                {{-- pola titik (peta digital) --}}
                <rect x="430" y="0" width="370" height="420" fill="url(#sdGrid)" />
                {{-- lingkar orbit --}}
                <g fill="none" stroke="#fff">
                    <circle cx="690" cy="140" r="150" stroke-opacity=".10" />
                    <circle cx="690" cy="140" r="230" stroke-opacity=".07" />
                    <circle cx="690" cy="140" r="310" stroke-opacity=".05" />
                </g>
                {{-- garis kontur lengkung --}}
                <g fill="none" stroke="#fff" stroke-width="1.2">
                    <path d="M-20 610 C 180 540, 360 660, 560 590 S 820 520, 860 560" stroke-opacity=".10" />
                    <path d="M-20 650 C 200 580, 380 700, 580 630 S 820 560, 860 600" stroke-opacity=".07" />
                </g>
                {{-- jaringan desa terhubung --}}
                <g stroke="#bbdefb" stroke-opacity=".32" stroke-width="1.2" fill="none">
                    <path d="M520 250 L610 205 L700 270 L640 350 L540 330 Z" />
                    <path d="M610 205 L640 350 M700 270 L760 220 M540 330 L480 400" />
                </g>
                <g fill="#e3f2fd">
                    <circle cx="520" cy="250" r="4" fill-opacity=".7" />
                    <circle cx="610" cy="205" r="5" fill-opacity=".85" />
                    <circle cx="700" cy="270" r="4" fill-opacity=".7" />
                    <circle cx="640" cy="350" r="6" fill-opacity=".9" />
                    <circle cx="540" cy="330" r="3.5" fill-opacity=".6" />
                    <circle cx="760" cy="220" r="3" fill-opacity=".5" />
                    <circle cx="480" cy="400" r="3" fill-opacity=".5" />
                </g>
                <circle cx="640" cy="350" r="14" fill="none" stroke="#e3f2fd" stroke-opacity=".35" />
                {{-- siluet desa: rumah & kantor desa --}}
                <g fill="#fff" fill-opacity=".07">
                    <path d="M0 900 V790 L40 760 L80 790 V900 Z" />
                    <path d="M90 900 V770 L150 725 L210 770 V900 Z" />
                    <path d="M225 900 V800 L260 772 L295 800 V900 Z" />
                    {{-- kantor desa (atap limas + tiang bendera) --}}
                    <path d="M320 900 V760 H500 V900 Z" />
                    <path d="M305 762 L410 700 L515 762 Z" />
                    <rect x="408" y="640" width="3" height="62" />
                    <path d="M411 642 H440 V660 H411 Z" />
                    <path d="M530 900 V795 L575 760 L620 795 V900 Z" />
                    <path d="M640 900 V780 L700 735 L760 780 V900 Z" />
                    <path d="M770 900 V805 L800 782 V900 Z" />
                </g>
                <g fill="#fff" fill-opacity=".10">
                    <rect x="350" y="790" width="22" height="26" rx="2" />
                    <rect x="398" y="790" width="22" height="26" rx="2" />
                    <rect x="446" y="790" width="22" height="26" rx="2" />
                    <rect x="130" y="800" width="18" height="22" rx="2" />
                    <rect x="680" y="805" width="18" height="22" rx="2" />
                </g>
            </svg>

            <div class="sd-auth-brand-inner">
                <div class="sd-auth-wordmark">
                    <span class="sd-auth-logo-tile">
                        <img src="{{ $logoAplikasi }}" alt="" />
                    </span>
                    <span>
                        <strong>{{ $namaAplikasi }}</strong>
                        <small>Sistem Informasi Manajemen Desa</small>
                    </span>
                </div>

                <div class="sd-auth-hero">
                    <h2>Digitalisasi <span class="sd-nowrap">Pelayanan Desa</span> <span class="sd-accent">dalam Satu Sistem</span></h2>
                    <p>Kelola administrasi, data kependudukan, pelayanan warga, dan informasi desa secara terintegrasi dalam satu platform.</p>

                    <ul class="sd-auth-features">
                        @foreach ($fiturAplikasi as $fitur)
                            <li>
                                <span class="sd-auth-feature-icon"><i class="fa {{ $fitur['icon'] }}" aria-hidden="true"></i></span>
                                <span>{{ $fitur['label'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sd-auth-desa">
                    <span class="sd-auth-desa-logo">
                        <img src="{{ gambar_desa($header['logo']) }}" alt="" />
                    </span>
                    <div>
                        <small>Pemerintah {{ $sebutanDesa }}</small>
                        <p class="sd-auth-desa-name">{{ $namaDesa }}</p>
                        <p class="sd-auth-desa-address"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ $alamatDesa }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ============ Form (kanan) ============ --}}
        <main class="sd-auth-panel">
            <div class="sd-auth-panel-deco" aria-hidden="true"></div>

            <div class="sd-auth-card sd-scope">
                <a href="{{ site_url() }}" class="sd-auth-card-brand">
                    <img src="{{ $logoAplikasi }}" alt="{{ $namaAplikasi }}" />
                    <span>
                        <strong>{{ $namaAplikasi }}</strong>
                        <small>Sistem Informasi Manajemen Desa</small>
                    </span>
                </a>

                <div class="sd-auth-heading">
                    <h1 class="sd-auth-title">@yield('auth_title', 'Masuk ke ' . $namaAplikasi)</h1>
                    <p class="sd-auth-subtitle">@yield('auth_subtitle', 'Gunakan akun Anda untuk mengakses layanan digitalisasi desa.')</p>
                </div>

                <div class="sd-auth-village">
                    <span class="sd-auth-village-logo">
                        <img src="{{ gambar_desa($header['logo']) }}" alt="" />
                    </span>
                    <div>
                        <strong>{{ $namaDesa }}</strong>
                        @if ($wilayahDesa)
                            <span>{{ $wilayahDesa }}</span>
                        @endif
                    </div>
                </div>

                @if ($errors?->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $item)
                            @if (str_contains($item, 'Terlalu banyak upaya masuk.'))
                                <p id="countdown">{{ $item }}</p>
                            @else
                                <p>{{ $item }}</p>
                            @endif
                        @endforeach
                    </div>
                @endif
                @if ($notif = session('notif'))
                    <div class="alert alert-danger" role="alert">
                        <p>{{ $notif }}</p>
                    </div>
                @endif

                @yield('content')

                @if (setting('tte'))
                    <div class="sd-auth-cert">
                        <span>Tanda tangan elektronik didukung oleh</span>
                        <img src="{{ $logo_bsre }}" alt="Balai Besar Sertifikasi Elektronik (BSrE)" />
                    </div>
                @endif
            </div>

            <footer class="sd-auth-footer">
                <strong>{{ $namaAplikasi }}</strong> v{{ AmbilVersi() }}
                <span aria-hidden="true">&middot;</span>
                Layanan Digitalisasi Desa
            </footer>
        </main>
    </div>

    @stack('js')

</body>

</html>
