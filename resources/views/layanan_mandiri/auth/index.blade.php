<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        SIMDESA - Layanan Mandiri {{ ucwords(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="shortcut icon" href="{{ favico_desa() }}" />
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('css/login-form-elements.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('css/daftar-form-elements.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('css/siteman_mandiri.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.bar.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap-datetimepicker.min.css') }}">

    @if (is_file('desa/pengaturan/siteman/siteman_mandiri.css'))
        <link rel="stylesheet" href="{{ base_url('desa/pengaturan/siteman/siteman_mandiri.css') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('bootstrap/css/font-awesome.min.css') }}">

    @if (cek_koneksi_internet())
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @endif

    <script src="{{ asset('bootstrap/js/jquery.min.js') }}"></script>

    @if ($cek_anjungan)
        <link rel="stylesheet" href="{{ asset('css/keyboard.min.css') }}">
        <link rel="stylesheet" href="{{ asset('front/css/mandiri-keyboard.css') }}">
    @endif

    @include('admin.layouts.components.token')

    @php
        $logoSimdesa = asset('images/simdesa-logo.png');
        $versiSimdesa = preg_replace('/[^0-9]/', '', explode('.', AmbilVersi())[0] ?? '') ?: '2510';
    @endphp

    <style>
        html,
        body {
            min-height: 100%;
            background-color: #f4faff !important;
        }

        body.login {
            margin: 0 !important;
            min-height: 100vh !important;
            font-family: "Source Sans Pro", "Segoe UI", Arial, sans-serif !important;
            color: #0f172a !important;
            background:
                radial-gradient(circle at 10% 10%, rgba(0, 137, 255, 0.12), transparent 30%),
                radial-gradient(circle at 100% 0%, rgba(0, 137, 255, 0.16), transparent 32%),
                radial-gradient(circle at 90% 90%, rgba(0, 137, 255, 0.08), transparent 30%),
                linear-gradient(120deg, #ffffff 0%, #f4faff 52%, #e4f4ff 100%) !important;
            background-image:
                radial-gradient(circle at 10% 10%, rgba(0, 137, 255, 0.12), transparent 30%),
                radial-gradient(circle at 100% 0%, rgba(0, 137, 255, 0.16), transparent 32%),
                radial-gradient(circle at 90% 90%, rgba(0, 137, 255, 0.08), transparent 30%),
                linear-gradient(120deg, #ffffff 0%, #f4faff 52%, #e4f4ff 100%) !important;
            overflow-x: hidden;
        }

        body.login::before,
        body.login::after,
        .top-content::before,
        .top-content::after,
        .inner-bg::before,
        .inner-bg::after {
            display: none !important;
            content: none !important;
            background: transparent !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        .top-content,
        .inner-bg,
        .container,
        .row,
        .simdesa-wrapper {
            background: transparent !important;
            opacity: 1 !important;
            filter: none !important;
        }

        .top-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        .inner-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 50px;
        }

        .simdesa-wrapper {
            width: 100%;
            max-width: 1320px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(430px, 0.82fr);
            gap: 70px;
            align-items: center;
        }

        .simdesa-left {
            color: #0f172a;
            opacity: 1 !important;
            filter: none !important;
        }

        .simdesa-logo-wrap {
            margin-bottom: 42px;
        }

        .simdesa-logo-wrap img {
            width: 250px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .simdesa-logo-wrap .subtitle {
            margin-top: 8px;
            color: #334155;
            font-size: 17px;
            font-weight: 700;
        }

        .simdesa-left h1 {
            margin: 0;
            color: #0f172a !important;
            font-size: 58px;
            line-height: 1.06;
            font-weight: 900;
            letter-spacing: -1px;
            text-transform: none !important;
        }

        .simdesa-left h1 .blue {
            color: #0877d8 !important;
        }

        .simdesa-subheadline {
            margin-top: 12px;
            font-size: 30px;
            line-height: 1.25;
            color: #334155 !important;
            font-weight: 700;
        }

        .simdesa-line {
            width: 76px;
            height: 5px;
            border-radius: 999px;
            background: #0877d8;
            margin: 28px 0 24px;
        }

        .simdesa-desc {
            color: #334155 !important;
            font-size: 18px;
            line-height: 1.7;
            max-width: 720px;
        }

        .simdesa-features {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 40px;
            max-width: 720px;
        }

        .simdesa-feature-card {
            min-height: 132px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(8, 119, 216, 0.13);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 16px 12px;
        }

        .simdesa-feature-card i {
            width: 48px;
            height: 48px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0877d8;
            background: #eef7ff;
            font-size: 23px;
            margin-bottom: 12px;
        }

        .simdesa-feature-card span {
            color: #0f172a;
            font-size: 14px;
            line-height: 1.3;
            font-weight: 800;
        }

        .simdesa-benefit-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            max-width: 740px;
            margin-top: 38px;
            padding: 18px 22px;
            color: #ffffff;
            border-radius: 16px;
            background: linear-gradient(135deg, #16aef7 0%, #0868d8 100%);
            box-shadow: 0 18px 38px rgba(8, 119, 216, 0.22);
        }

        .simdesa-benefit-bar span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
        }

        .simdesa-visitor {
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
            text-align: center;
            max-width: 740px;
        }

        .simdesa-visitor a {
            color: #0877d8 !important;
        }

        .simdesa-right {
            width: 100%;
        }

        .simdesa-card {
            width: 100%;
            background: #ffffff !important;
            border-radius: 30px;
            padding: 42px 38px 34px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
            border: 1px solid rgba(8, 119, 216, 0.08);
            opacity: 1 !important;
            filter: none !important;
        }

        .simdesa-card-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .simdesa-card-header img {
            width: 155px;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 18px;
            object-fit: contain;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .simdesa-card-header h2 {
            margin: 0;
            color: #0877d8 !important;
            font-size: 31px;
            line-height: 1.1;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .simdesa-card-header h4 {
            margin: 10px 0 0;
            color: #0f172a !important;
            font-size: 21px;
            line-height: 1.2;
            font-weight: 900;
        }

        .simdesa-card-header p {
            margin: 8px 0 0;
            color: #475569 !important;
            font-size: 14px;
            line-height: 1.6;
            font-weight: 600;
        }

        .simdesa-card-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 22px;
        }

        .simdesa-card-divider::before,
        .simdesa-card-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #dbe7f5;
        }

        .simdesa-card-divider span {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #0877d8;
            display: block;
        }

        .alert {
            border-radius: 12px !important;
            margin-bottom: 18px !important;
            font-size: 14px !important;
        }

        .login-form {
            margin: 0 !important;
        }

        .form-group {
            margin-bottom: 14px !important;
        }

        .form-login {
            margin-bottom: 14px !important;
        }

        .simdesa-input-wrap {
            position: relative;
        }

        .simdesa-input-wrap i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #55708f;
            font-size: 18px;
            z-index: 2;
        }

        .simdesa-input-wrap .toggle-pin {
            left: auto;
            right: 18px;
            cursor: pointer;
        }

        .form-control,
        .login-form input[type="text"],
        .login-form input[type="password"],
        .login-form input[type="number"] {
            height: 56px !important;
            border-radius: 12px !important;
            border: 1px solid #cfe0f5 !important;
            background: #f4f8ff !important;
            color: #0f172a !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            padding-left: 52px !important;
            box-shadow: none !important;
            outline: none !important;
            text-align: left !important;
        }

        .form-control:focus,
        .login-form input:focus {
            border-color: #0b8fe8 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(11, 143, 232, 0.13) !important;
        }

        .login-form center {
            display: block;
            text-align: center;
            margin: 4px 0 16px;
        }

        .login-form label {
            color: #334155 !important;
            font-size: 14px !important;
            font-weight: 700 !important;
        }

        .login-form input[type="checkbox"] {
            accent-color: #0877d8;
        }

        .simdesa-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 6px 0 18px;
            color: #334155;
            font-size: 14px;
        }

        .simdesa-options label {
            margin: 0;
            font-size: 14px;
            color: #334155;
            font-weight: 700;
            cursor: pointer;
        }

        .simdesa-options input[type="checkbox"] {
            display: inline-block !important;
            margin: 0 6px 0 0;
            accent-color: #0877d8;
        }

        .simdesa-options a {
            color: #0877d8 !important;
            font-weight: 800;
            text-decoration: none;
        }

        .btn,
        .login-form .btn {
            height: 56px !important;
            border-radius: 12px !important;
            font-size: 15px !important;
            font-weight: 900 !important;
            letter-spacing: .4px !important;
            text-transform: uppercase !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: all .2s ease;
            box-shadow: none !important;
            width: 100%;
        }

        .simdesa-btn-primary,
        .login-form button[type="submit"] {
            color: #ffffff !important;
            border: 1px solid #0877d8 !important;
            background: linear-gradient(135deg, #17adf7 0%, #0868d8 100%) !important;
        }

        .simdesa-btn-primary:hover,
        .login-form button[type="submit"]:hover {
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(8, 119, 216, 0.25) !important;
        }

        .simdesa-btn-outline,
        .login-form .bg-green {
            color: #0877d8 !important;
            background: #ffffff !important;
            border: 2px solid #0877d8 !important;
            text-decoration: none !important;
        }

        .simdesa-btn-outline:hover,
        .login-form .bg-green:hover {
            color: #065db0 !important;
            background: #eef7ff !important;
            text-decoration: none !important;
        }

        .simdesa-btn-muted {
            color: #64748b !important;
            background: #ffffff !important;
            border: 1px solid #dbe7f5 !important;
            text-decoration: none !important;
        }

        .simdesa-btn-muted:hover {
            color: #0f172a !important;
            background: #f8fbff !important;
            text-decoration: none !important;
        }

        .login-footer-bottom {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #dbe7f5;
            text-align: center;
        }

        .login-footer-bottom span {
            color: #0877d8 !important;
            font-size: 16px;
            font-weight: 900;
            text-decoration: none !important;
        }

        .login-footer-bottom small {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .thumbnail {
            border: 1px solid #dbe7f5 !important;
            border-radius: 14px !important;
            background: #f8fbff !important;
            padding: 18px !important;
            margin-bottom: 16px !important;
        }

        .thumbnail img {
            margin: 0 auto;
        }

        @media (max-width: 1100px) {
            .inner-bg {
                padding: 28px 22px;
            }

            .simdesa-wrapper {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .simdesa-left h1 {
                font-size: 42px;
            }

            .simdesa-subheadline {
                font-size: 24px;
            }

            .simdesa-card {
                max-width: 560px;
                margin: 0 auto;
            }

            .simdesa-visitor {
                text-align: left;
            }
        }

        @media (max-width: 700px) {
            .inner-bg {
                padding: 18px 14px;
            }

            .simdesa-logo-wrap img {
                width: 185px;
            }

            .simdesa-logo-wrap .subtitle {
                font-size: 13px;
            }

            .simdesa-left h1 {
                font-size: 34px;
            }

            .simdesa-subheadline {
                font-size: 19px;
            }

            .simdesa-desc {
                font-size: 16px;
            }

            .simdesa-features {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .simdesa-benefit-bar {
                flex-wrap: wrap;
                justify-content: center;
                padding: 16px;
            }

            .simdesa-card {
                padding: 30px 20px 26px;
                border-radius: 24px;
            }

            .simdesa-card-header img {
                width: 128px;
            }

            .simdesa-card-header h2 {
                font-size: 24px;
            }

            .simdesa-card-header h4 {
                font-size: 19px;
            }
        }
    </style>
</head>

<body class="login">
    <div class="top-content">
        <div class="inner-bg">
            <div class="simdesa-wrapper">

                <div class="simdesa-left">
                    <div class="simdesa-logo-wrap">
                        <a href="{{ base_url('/') }}">
                            <img src="{{ $logoSimdesa }}" alt="Logo SIMDESA">
                        </a>
                        <div class="subtitle">Sistem Informasi Manajemen Desa</div>
                    </div>

                    <h1>
                        Digitalisasi Pelayanan Desa
                        <br>
                        <span class="blue">dalam Satu Sistem</span>
                    </h1>

                    <div class="simdesa-line"></div>

                    <div class="simdesa-desc">
                        SIMDESA hadir untuk membantu pemerintah desa mengelola administrasi,
                        pelayanan warga, data desa, dan informasi desa secara terintegrasi dan mudah.
                    </div>

                    <div class="simdesa-features">
                        <div class="simdesa-feature-card">
                            <i class="fa fa-users"></i>
                            <span>Administrasi<br>Kependudukan</span>
                        </div>
                        <div class="simdesa-feature-card">
                            <i class="fa fa-file-text-o"></i>
                            <span>Surat<br>Menyurat Desa</span>
                        </div>
                        <div class="simdesa-feature-card">
                            <i class="fa fa-globe"></i>
                            <span>Informasi &<br>Website Desa</span>
                        </div>
                        <div class="simdesa-feature-card">
                            <i class="fa fa-mobile"></i>
                            <span>Layanan<br>Digital Warga</span>
                        </div>
                    </div>

                    <div class="simdesa-benefit-bar">
                        <span><i class="fa fa-link"></i> Terintegrasi</span>
                        <span><i class="fa fa-shield"></i> Aman</span>
                        <span><i class="fa fa-bolt"></i> Cepat</span>
                        <span><i class="fa fa-line-chart"></i> Transparan</span>
                        <span><i class="fa fa-heart-o"></i> Melayani</span>
                    </div>

                    <div class="simdesa-visitor">
                        IP Address: {{ request()->ip() }}
                        <br>
                        ID Pengunjung:
                        <span id="pengunjung"></span>
                        <a href="#" class="copy" title="Copy"><i class="fa fa-copy"></i></a>

                        @if ($cek_anjungan)
                            @if ($cek_anjungan['mac_address'])
                                <br>Mac Address: {{ $cek_anjungan['mac_address'] }}
                            @endif
                            <br>Anjungan Mandiri
                            {!! jecho($cek_anjungan['keyboard'] == 1, true, ' | Virtual Keyboard : Aktif') !!}
                        @endif
                    </div>
                </div>

                <div class="simdesa-right">
                    <div class="simdesa-card">
                        <div class="simdesa-card-header">
                            <img src="{{ $logoSimdesa }}" alt="Logo SIMDESA">
                            <h2>LAYANAN MANDIRI</h2>
                            <h4>{{ ucwords(setting('sebutan_desa')) }} {{ $desa['nama_desa'] }}</h4>
                            <p>
                                {{ ucwords(setting('sebutan_kecamatan')) }} {{ $desa['nama_kecamatan'] }},
                                {{ ucwords(setting('sebutan_kabupaten')) }} {{ $desa['nama_kabupaten'] }}
                            </p>
                        </div>

                        <div class="simdesa-card-divider"><span></span></div>

                        @php
                            preg_match('/(\d+)/', $errors->first('email'), $matches);
                            $second = $matches[0] ?? 0;
                        @endphp

                        @if ($errors->any())
                            <div @if (!str_contains($errors->first('email'), 'Terlalu banyak upaya masuk.')) id="notif" @endif class="alert alert-danger">
                                @foreach ($errors->all() as $item)
                                    @if (str_contains($item, 'Terlalu banyak upaya masuk.'))
                                        <p id="countdown">{{ $item }}</p>
                                    @else
                                        <p>{{ $item }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if ($notif = $ci->session->flashdata('notif'))
                            <div id="notif" class="alert alert-danger">
                                <p>{{ $notif }}</p>
                            </div>
                        @endif

                        @yield('content')

                        <div class="login-footer-bottom">
                            <span>SIMDESA {{ $versiSimdesa }}</span>
                            <small>Layanan Digitalisasi Desa</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.layouts.components.konfirmasi_cookie', ['cookie_name' => 'pengunjung'])
    @include('admin.layouts.components.aktifkan_cookie')

    <script src="{{ asset('bootstrap/js/jquery.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/moment.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/moment-timezone.js') }}"></script>
    <script src="{{ asset('bootstrap/js/moment-timezone-with-data.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/id.js') }}"></script>
    <script src="{{ asset('bootstrap/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/fastclick.js') }}"></script>
    <script src="{{ asset('js/adminlte.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/validasi.js') }}"></script>
    <script src="{{ asset('js/localization/messages_id.js') }}"></script>

    @if ($cek_anjungan)
        <script src="{{ asset('js/jquery.keyboard.min.js') }}"></script>
        <script src="{{ asset('js/jquery.mousewheel.min.js') }}"></script>
        <script src="{{ asset('js/jquery.keyboard.extension-all.min.js') }}"></script>
        <script src="{{ asset('front/js/mandiri-keyboard.js') }}"></script>
    @endif

    <script src="{{ asset('js/id_browser.js') }}"></script>

    <script>
        function start_countdown() {
            let totalSeconds = {{ $second }};
            const timer = setInterval(function() {
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                if (totalSeconds <= 0) {
                    clearInterval(timer);
                    location.reload();
                } else {
                    document.getElementById("countdown").innerHTML = `Terlalu banyak upaya masuk. Silakan coba lagi dalam ${minutes} menit ${seconds} detik.`;
                    totalSeconds--;
                }
            }, 1000);
        }

        $(document).ready(function() {
            if ($('#pin').length) {
                $('#pin').focus();
            } else if ($('#tag').length) {
                $('#tag').focus();
            }

            if ($('#countdown').length) {
                start_countdown();
            }

            window.setTimeout(function() {
                $("#notif").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);

            $('.copy').on('click', function(e) {
                e.preventDefault();

                const text = $('#pengunjung').text();

                if (!text) {
                    return;
                }

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(text);
                }
            });
        });
    </script>

    @stack('script')
</body>

</html>
