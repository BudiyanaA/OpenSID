@extends('admin.auth.index')

@php
    preg_match('/(\d+)/', $errors?->first('email'), $matches);
    $second = $matches[0] ?? 0;
@endphp

@section('content')
    <style>
        :root {
            --simdesa-primary: #0EA5E9;
            --simdesa-primary-dark: #0284C7;
            --simdesa-primary-deep: #0369A1;
            --simdesa-soft: #E0F2FE;
            --simdesa-bg: #F0F9FF;
            --simdesa-text: #0F172A;
            --simdesa-muted: #64748B;
        }

        body {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 45%, #BAE6FD 100%) !important;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif !important;
        }

        .login-logo,
        .login-box-msg,
        .login-title {
            display: none !important;
        }

        .login-box,
        .login-box-body,
        .card,
        .box {
            border-radius: 26px !important;
            overflow: hidden;
        }

        .login-box-body,
        .card-body,
        .box-body {
            box-shadow: 0 30px 80px rgba(14, 165, 233, 0.18) !important;
        }

        .simdesa-login-box {
            text-align: center;
        }

        .simdesa-logo {
            width: 125px;
            height: auto;
            margin: 0 auto 12px;
            display: block;
        }

        .simdesa-title {
            font-size: 34px;
            font-weight: 900;
            color: var(--simdesa-primary-dark);
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .simdesa-subtitle {
            font-size: 13px;
            font-weight: 700;
            color: var(--simdesa-text);
            text-transform: uppercase;
            margin-bottom: 22px;
        }

        .simdesa-desa-card {
            background: #F0F9FF;
            border: 1px solid #BAE6FD;
            border-radius: 18px;
            padding: 14px 16px;
            margin-bottom: 22px;
        }

        .simdesa-desa-name {
            font-size: 18px;
            font-weight: 900;
            color: var(--simdesa-text);
            margin-bottom: 4px;
        }

        .simdesa-desa-address {
            font-size: 13px;
            color: var(--simdesa-muted);
            line-height: 1.4;
        }

        .login-form .form-control {
            height: 50px !important;
            border-radius: 14px !important;
            border: 1px solid #CBD5E1 !important;
            box-shadow: none !important;
            font-size: 15px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
        }

        .login-form .form-control:focus {
            border-color: var(--simdesa-primary) !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15) !important;
        }

        .login-form .form-group {
            margin-bottom: 14px;
        }

        .login-form button[type="submit"],
        .login-form .btn[type="submit"] {
            width: 100%;
            height: 52px;
            border-radius: 14px !important;
            border: none !important;
            background: linear-gradient(135deg, #0EA5E9, #0284C7) !important;
            color: #ffffff !important;
            font-size: 16px;
            font-weight: 900;
            box-shadow: 0 14px 28px rgba(14, 165, 233, 0.28);
        }

        .login-form button[type="submit"]:hover,
        .login-form .btn[type="submit"]:hover {
            background: linear-gradient(135deg, #0284C7, #0369A1) !important;
            color: #ffffff !important;
        }

        .simdesa-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            color: var(--simdesa-muted);
            margin-top: 2px;
            margin-bottom: 18px !important;
        }

        .simdesa-options label {
            margin: 0;
            font-weight: 500 !important;
            color: var(--simdesa-muted);
            cursor: pointer;
        }

        .simdesa-options a {
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
            color: var(--simdesa-primary-dark) !important;
            font-weight: 700;
            text-decoration: none;
        }

        .simdesa-options a:hover {
            color: var(--simdesa-primary-deep) !important;
            text-decoration: underline;
        }

        .simdesa-footer {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #E2E8F0;
            text-align: center;
            color: var(--simdesa-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .simdesa-footer strong {
            color: var(--simdesa-primary-dark);
            font-size: 16px;
        }

        .simdesa-captcha img {
            border-radius: 12px;
            border: 1px solid #BAE6FD;
            padding: 6px;
            background: #ffffff;
        }

        .simdesa-alert {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 14px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .simdesa-logo {
                width: 96px;
            }

            .simdesa-title {
                font-size: 28px;
            }

            .simdesa-options {
                display: block;
                text-align: left;
            }

            .simdesa-options a {
                display: block;
                margin-top: 8px;
            }
        }
    </style>

    <div class="simdesa-login-box">
        <img src="{{ base_url('assets/images/simdesa-logo.jpg') }}" class="simdesa-logo" alt="SIMDESA" onerror="this.style.display='none'">

        <div class="simdesa-title">SIMDESA</div>
        <div class="simdesa-subtitle">Sistem Informasi Manajemen Desa</div>

        <div class="simdesa-desa-card">
            <div class="simdesa-desa-name">DESA RANTE ALANG</div>
            <div class="simdesa-desa-address">
                Kecamatan Larompong<br>
                Kabupaten Luwu
            </div>
        </div>

        @if ($second)
            <div id="countdown" class="simdesa-alert">
                Terlalu banyak upaya masuk. Silakan tunggu sebentar.
            </div>
        @endif

        <form id="validasi" class="login-form" action="{{ $form_action }}" method="post">
            <div class="form-group">
                <input
                    name="username"
                    type="text"
                    autocomplete="off"
                    placeholder="Nama pengguna"
                    @disabled($second)
                    class="form-username form-control required"
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="off"
                    placeholder="Kata sandi"
                    @disabled($second)
                    class="form-username form-control required"
                    maxlength="100"
                >
            </div>

            @if (setting('google_recaptcha'))
                {!! app('captcha')->display() !!}
            @else
                <div class="form-group simdesa-captcha">
                    <a href="#" id="b-captcha" onclick="event.preventDefault(); document.getElementById('captcha').src = '{{ site_url('captcha') }}?' + Math.random();" style="color: #000000;">
                        <img id="captcha" src="{{ site_url('captcha') }}" alt="CAPTCHA Image" />
                    </a>
                </div>

                <div class="form-group captcha">
                    <input
                        name="captcha_code"
                        type="text"
                        class="form-control required"
                        maxlength="6"
                        placeholder="Masukkan kode di atas"
                        @disabled($second)
                        autocomplete="off"
                    />
                </div>
            @endif

            <div class="form-group simdesa-options">
                <label for="checkbox">
                    <input @disabled($second) type="checkbox" id="checkbox" class="form-checkbox">
                    Tampilkan kata sandi
                </label>

                <a href="{{ site_url('siteman/lupa_sandi') }}" role="button" aria-pressed="true">
                    Lupa kata sandi?
                </a>
            </div>

            <div class="form-group">
                <button type="submit" class="btn" @disabled($second)>Masuk</button>
            </div>
        </form>

        <div class="simdesa-footer">
            <strong>SIMDESA v1.0</strong><br>
            Layanan Digitalisasi Desa
        </div>
    </div>
@endsection

@push('js')
    @if (setting('google_recaptcha'))
        {!! app('captcha')->renderJs('id', true, 'recaptchaCallback') !!}

        <script>
            var recaptchaCallback = function() {
                grecaptcha.render(document.querySelector('.g-recaptcha'), {
                    'sitekey': '{{ $list_setting->firstWhere('key', 'google_recaptcha_site_key')?->value }}',
                    'error-callback': function() {
                        $.ajax({
                            url: '{{ site_url('siteman/matikan-captcha') }}',
                            type: 'post',
                            success: function(response) {
                                window.location.href = '{{ site_url('siteman') }}';
                            },
                            error: function(xhr, status, error) {
                                console.error('Error in captcha disabling request:', error);
                            }
                        });
                    }
                });
            }
        </script>
    @endif

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
            var pass = $("#password");

            $('#checkbox').click(function() {
                if (pass.attr('type') === "password") {
                    pass.attr('type', 'text');
                } else {
                    pass.attr('type', 'password');
                }
            });

            if ($('#countdown').length) {
                start_countdown();
            }
        });
    </script>
@endpush
