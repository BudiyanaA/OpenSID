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
            margin: 0 !important;
            overflow: hidden !important;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif !important;
            background: #F0F9FF !important;
        }

        .simdesa-full-login {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 55%, #BAE6FD 100%);
            overflow: auto;
        }

        .simdesa-left {
            width: 52%;
            min-height: 100vh;
            position: relative;
            padding: 70px 65px 45px;
            box-sizing: border-box;
            color: #0F172A;
            background-image:
                linear-gradient(90deg, rgba(240, 249, 255, 0.98) 0%, rgba(240, 249, 255, 0.88) 48%, rgba(240, 249, 255, 0.52) 100%),
                url("{{ base_url('assets/images/bg-login-desa.jpg') }}");
            background-size: cover;
            background-position: center bottom;
            overflow: hidden;
        }

        .simdesa-left::after {
            content: "";
            position: absolute;
            left: -160px;
            bottom: -120px;
            width: 680px;
            height: 230px;
            background: linear-gradient(135deg, #0EA5E9, #0284C7);
            border-radius: 0 100% 0 0;
            opacity: 0.95;
        }

        .simdesa-brand-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 58px;
            position: relative;
            z-index: 2;
        }

        .simdesa-brand-row img {
            width: 110px;
            height: auto;
            display: block;
        }

        .simdesa-brand-title {
            font-size: 58px;
            font-weight: 900;
            color: #0875D1;
            line-height: 1;
            letter-spacing: 1px;
        }

        .simdesa-brand-subtitle {
            margin-top: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #334155;
        }

        .simdesa-headline {
            position: relative;
            z-index: 2;
            max-width: 680px;
            font-size: 44px;
            line-height: 1.18;
            font-weight: 900;
            color: #0F172A;
            margin-bottom: 20px;
        }

        .simdesa-headline span {
            color: #0875D1;
        }

        .simdesa-line {
            position: relative;
            z-index: 2;
            width: 88px;
            height: 4px;
            background: #0875D1;
            border-radius: 100px;
            margin-bottom: 24px;
        }

        .simdesa-desc {
            position: relative;
            z-index: 2;
            max-width: 620px;
            font-size: 19px;
            line-height: 1.55;
            color: #334155;
            margin-bottom: 35px;
        }

        .simdesa-features {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            max-width: 660px;
        }

        .simdesa-feature {
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(14,165,233,0.18);
            border-radius: 14px;
            padding: 17px 10px;
            text-align: center;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            min-height: 112px;
            box-sizing: border-box;
        }

        .simdesa-feature-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 10px;
            border-radius: 12px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0875D1;
            font-size: 24px;
            font-weight: 900;
            box-shadow: 0 8px 18px rgba(14, 165, 233, 0.12);
        }

        .simdesa-feature-text {
            font-size: 13px;
            line-height: 1.25;
            color: #0F172A;
            font-weight: 700;
        }

        .simdesa-bottom-badge {
            position: absolute;
            left: 65px;
            right: 65px;
            bottom: 32px;
            z-index: 3;
            min-height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, #075985, #0369A1);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 16px;
            padding: 0 22px;
            box-sizing: border-box;
            box-shadow: 0 16px 30px rgba(3, 105, 161, 0.25);
            font-size: 14px;
            font-weight: 700;
        }

        .simdesa-right {
            width: 48%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 38px 55px;
            box-sizing: border-box;
            position: relative;
        }

        .simdesa-right::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 260px;
            height: 260px;
            background: rgba(14, 165, 233, 0.20);
            clip-path: polygon(100% 0, 0 0, 100% 100%);
        }

        .simdesa-card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border-radius: 28px;
            padding: 45px 45px 36px;
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.16);
            position: relative;
            z-index: 2;
            box-sizing: border-box;
        }

        .simdesa-card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .simdesa-card-logo {
            width: 112px;
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }

        .simdesa-card-title {
            font-size: 36px;
            line-height: 1;
            font-weight: 900;
            color: #0875D1;
            letter-spacing: 1px;
        }

        .simdesa-card-subtitle {
            margin-top: 8px;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
        }

        .simdesa-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 26px 0 22px;
        }

        .simdesa-divider::before,
        .simdesa-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #CBD5E1;
        }

        .simdesa-divider span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0EA5E9;
            display: block;
        }

        .simdesa-village {
            text-align: center;
            margin-bottom: 24px;
        }

        .simdesa-village-name {
            font-size: 22px;
            font-weight: 900;
            color: #0F172A;
            text-transform: uppercase;
        }

        .simdesa-village-address {
            margin-top: 7px;
            font-size: 15px;
            line-height: 1.4;
            color: #475569;
        }

        .simdesa-input-wrap {
            position: relative;
            margin-bottom: 15px;
        }

        .simdesa-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 18px;
            z-index: 2;
        }

        .login-form .form-control {
            width: 100% !important;
            height: 58px !important;
            border-radius: 13px !important;
            border: 1px solid #CBD5E1 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            font-size: 16px !important;
            color: #0F172A !important;
            padding-left: 56px !important;
            padding-right: 18px !important;
            box-sizing: border-box !important;
        }

        .login-form .form-control:focus {
            border-color: #0EA5E9 !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.14) !important;
            outline: none !important;
        }

        .simdesa-captcha-row {
            display: flex;
            gap: 14px;
            margin-bottom: 15px;
        }

        .simdesa-captcha-box {
            flex: 1;
            height: 58px;
            border-radius: 13px;
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .simdesa-captcha-box img {
            max-height: 48px;
            max-width: 100%;
        }

        .simdesa-refresh {
            width: 92px;
            height: 58px;
            border-radius: 13px;
            border: 1px solid #CBD5E1;
            background: #ffffff;
            color: #334155;
            font-size: 24px;
            cursor: pointer;
        }

        .simdesa-refresh:hover {
            border-color: #0EA5E9;
            color: #0284C7;
        }

        .simdesa-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 3px 0 22px;
            color: #475569;
            font-size: 14px;
        }

        .simdesa-options label {
            margin: 0 !important;
            font-weight: 500 !important;
            cursor: pointer;
        }

        .simdesa-options input {
            margin-right: 6px;
        }

        .simdesa-options a {
            color: #0875D1 !important;
            font-weight: 700;
            text-decoration: none !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }

        .simdesa-options a:hover {
            color: #0369A1 !important;
            text-decoration: underline !important;
        }

        .simdesa-submit {
            width: 100% !important;
            height: 58px !important;
            border: none !important;
            border-radius: 13px !important;
            background: linear-gradient(135deg, #0EA5E9, #0875D1) !important;
            color: #ffffff !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 16px 28px rgba(14, 165, 233, 0.30);
        }

        .simdesa-submit:hover {
            background: linear-gradient(135deg, #0284C7, #0369A1) !important;
            color: #ffffff !important;
        }

        .simdesa-footer {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid #E2E8F0;
            text-align: center;
            color: #64748B;
            font-size: 15px;
            line-height: 1.5;
        }

        .simdesa-footer strong {
            color: #0875D1;
            font-size: 18px;
            font-weight: 900;
        }

        .simdesa-alert {
            background: #FEF3C7;
            border: 1px solid #F59E0B;
            color: #92400E;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .simdesa-full-login {
                display: block;
            }

            .simdesa-left {
                width: 100%;
                min-height: auto;
                padding: 35px 24px 30px;
                text-align: center;
            }

            .simdesa-brand-row {
                justify-content: center;
                margin-bottom: 25px;
            }

            .simdesa-brand-row img {
                width: 78px;
            }

            .simdesa-brand-title {
                font-size: 38px;
            }

            .simdesa-headline {
                font-size: 30px;
                margin-left: auto;
                margin-right: auto;
            }

            .simdesa-desc {
                font-size: 16px;
                margin-left: auto;
                margin-right: auto;
            }

            .simdesa-features,
            .simdesa-bottom-badge {
                display: none;
            }

            .simdesa-right {
                width: 100%;
                min-height: auto;
                padding: 10px 18px 35px;
            }

            .simdesa-card {
                max-width: 470px;
                padding: 34px 24px;
            }
        }

        @media (max-width: 520px) {
            .simdesa-card-logo {
                width: 86px;
            }

            .simdesa-card-title {
                font-size: 30px;
            }

            .simdesa-village-name {
                font-size: 18px;
            }

            .simdesa-captcha-row {
                gap: 8px;
            }

            .simdesa-refresh {
                width: 70px;
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

    <div class="simdesa-full-login">
        <div class="simdesa-left">
            <div class="simdesa-brand-row">
                <img src="{{ base_url('assets/images/simdesa-logo.png') }}" alt="SIMDESA" onerror="this.style.display='none'">
                <div>
                    <div class="simdesa-brand-title">SIMDESA</div>
                    <div class="simdesa-brand-subtitle">Sistem Informasi Manajemen Desa</div>
                </div>
            </div>

            <div class="simdesa-headline">
                Digitalisasi Pelayanan Desa<br>
                <span>dalam Satu Sistem</span>
            </div>

            <div class="simdesa-line"></div>

            <div class="simdesa-desc">
                SIMDESA hadir untuk membantu pemerintah desa mengelola administrasi,
                pelayanan warga, data desa, dan informasi desa secara terintegrasi dan mudah.
            </div>

            <div class="simdesa-features">
                <div class="simdesa-feature">
                    <div class="simdesa-feature-icon">👥</div>
                    <div class="simdesa-feature-text">Administrasi<br>Kependudukan</div>
                </div>

                <div class="simdesa-feature">
                    <div class="simdesa-feature-icon">📄</div>
                    <div class="simdesa-feature-text">Surat<br>Menyurat</div>
                </div>

                <div class="simdesa-feature">
                    <div class="simdesa-feature-icon">🌐</div>
                    <div class="simdesa-feature-text">Informasi &<br>Website Desa</div>
                </div>

                <div class="simdesa-feature">
                    <div class="simdesa-feature-icon">📱</div>
                    <div class="simdesa-feature-text">Layanan<br>Digital Warga</div>
                </div>
            </div>

            <div class="simdesa-bottom-badge">
                <span>🔗 Terintegrasi</span>
                <span>🛡️ Aman</span>
                <span>⚡ Cepat</span>
                <span>📈 Transparan</span>
                <span>♡ Melayani dengan Hati</span>
            </div>
        </div>

        <div class="simdesa-right">
            <div class="simdesa-card">
                <div class="simdesa-card-header">
                    <img src="{{ base_url('assets/images/simdesa-logo.png') }}" class="simdesa-card-logo" alt="SIMDESA" onerror="this.style.display='none'">

                    <div class="simdesa-card-title">SIMDESA</div>
                    <div class="simdesa-card-subtitle">Sistem Informasi Manajemen Desa</div>

                    <div class="simdesa-divider"><span></span></div>

                    <div class="simdesa-village">
                        <div class="simdesa-village-name">DESA RANTE ALANG</div>
                        <div class="simdesa-village-address">
                            Kecamatan Larompong, Kabupaten Luwu
                        </div>
                    </div>
                </div>

                @if ($second)
                    <div id="countdown" class="simdesa-alert">
                        Terlalu banyak upaya masuk. Silakan tunggu sebentar.
                    </div>
                @endif

                <form id="validasi" class="login-form" action="{{ $form_action }}" method="post">
                    <div class="simdesa-input-wrap">
                        <span class="simdesa-input-icon">👤</span>
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

                    <div class="simdesa-input-wrap">
                        <span class="simdesa-input-icon">🔒</span>
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
                        <div class="simdesa-captcha-row">
                            <div class="simdesa-captcha-box">
                                <a href="#" id="b-captcha" onclick="event.preventDefault(); document.getElementById('captcha').src = '{{ site_url('captcha') }}?' + Math.random();" style="color: #000000;">
                                    <img id="captcha" src="{{ site_url('captcha') }}" alt="CAPTCHA Image" />
                                </a>
                            </div>

                            <button
                                type="button"
                                class="simdesa-refresh"
                                onclick="document.getElementById('captcha').src = '{{ site_url('captcha') }}?' + Math.random();"
                                title="Refresh Captcha"
                            >
                                ↻
                            </button>
                        </div>

                        <div class="simdesa-input-wrap">
                            <span class="simdesa-input-icon">🛡️</span>
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

                    <div class="simdesa-options">
                        <label for="checkbox">
                            <input @disabled($second) type="checkbox" id="checkbox" class="form-checkbox">
                            Tampilkan kata sandi
                        </label>

                        <a href="{{ site_url('siteman/lupa_sandi') }}" role="button" aria-pressed="true">
                            Lupa kata sandi?
                        </a>
                    </div>

                    <button type="submit" class="simdesa-submit btn" @disabled($second)>
                        🔒 MASUK
                    </button>
                </form>

                <div class="simdesa-footer">
                    <strong>SIMDESA v1.0</strong><br>
                    Layanan Digitalisasi Desa
                </div>
            </div>
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
