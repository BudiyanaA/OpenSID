@extends('admin.auth.index')

@php
    preg_match('/(\d+)/', $errors?->first('email'), $matches);
    $second = $matches[0] ?? 0;
    $isProduction = app()->isProduction();
@endphp

@section('content')
    <form id="validasi" class="login-form" action="{{ $form_action }}" method="post">
        <div class="form-group">
            <div class="sd-input-icon">
                <i class="fa fa-user" aria-hidden="true"></i>
                <input
                    id="username"
                    name="username"
                    type="text"
                    autocomplete="off"
                    placeholder="Nama pengguna"
                    aria-label="Nama pengguna"
                    @disabled($second)
                    class="form-username form-control required"
                    maxlength="100"
                >
            </div>
        </div>
        <div class="form-group">
            <div class="sd-input-icon">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="off"
                    placeholder="Kata sandi"
                    aria-label="Kata sandi"
                    @disabled($second)
                    class="form-username form-control required"
                    maxlength="100"
                >
            </div>
        </div>

        @if ($isProduction && setting('google_recaptcha'))
            <div class="form-group">
                {!! app('captcha')->display() !!}
            </div>
        @elseif ($isProduction)
            <div class="form-group">
                <div class="sd-auth-captcha">
                    <a href="#" id="b-captcha" title="Muat ulang kode" onclick="event.preventDefault(); document.getElementById('captcha').src = '{{ site_url('captcha') }}?' + Math.random();">
                        <img id="captcha" src="{{ site_url('captcha') }}" alt="CAPTCHA Image" />
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </a>
                    <div class="sd-input-icon captcha">
                        <i class="fa fa-shield" aria-hidden="true"></i>
                        <input
                            id="captcha_code"
                            name="captcha_code"
                            type="text"
                            class="form-control required"
                            maxlength="6"
                            placeholder="Ketik kode"
                            aria-label="Kode keamanan"
                            @disabled($second)
                            autocomplete="off"
                        />
                    </div>
                </div>
            </div>
        @endif

        <div class="sd-auth-row">
            <label for="checkbox" class="sd-check">
                <input @disabled($second) type="checkbox" id="checkbox" class="form-checkbox">
                Tampilkan kata sandi
            </label>
            <a href="{{ site_url('siteman/lupa_sandi') }}" class="sd-link">Lupa kata sandi?</a>
        </div>
        <div class="form-group">
            <button type="submit" class="btn sd-btn sd-btn-primary sd-btn-block sd-auth-submit" @disabled($second)>Masuk</button>
        </div>

        @if (setting('login_otp'))
            <div class="sd-auth-divider">atau</div>
            <a href="{{ ci_route('siteman.otp.form_login_otp') }}" class="btn sd-btn sd-btn-outline sd-btn-block sd-auth-alt" role="button">
                <i class="fa fa-mobile"></i> Masuk dengan OTP
            </a>
        @endif
    </form>
@endsection

@push('js')
    @if ($isProduction && setting('google_recaptcha'))
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
                                // Redirect to the 'siteman' URL after disabling captcha
                                window.location.href = '{{ site_url('siteman') }}';
                            },
                            error: function(xhr, status, error) {
                                // Log the error for debugging
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
                    pass.attr('type', 'password')
                }
            });
            if ($('#countdown').length) {
                start_countdown();
            }

            // Hapus localStorage untuk timer OTP login setiap kali halaman login utama dimuat.
            // Ini untuk memastikan timer direset jika pengguna kembali ke halaman ini
            // setelah gagal OTP atau membatalkan proses.
            localStorage.removeItem('otpLoginExpiry');
        });
    </script>
@endpush
