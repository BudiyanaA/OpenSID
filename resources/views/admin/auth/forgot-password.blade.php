@extends('admin.auth.index')

@section('auth_title', 'Lupa kata sandi')
@section('auth_subtitle', 'Masukkan email akun Anda untuk menerima tautan atur ulang kata sandi.')

@section('content')
    @if (empty(setting('email_notifikasi')) && empty(setting('telegram_notifikasi')))
        <div class="alert alert-warning mt-4 text-left">
            <h5 class="mb-3"><strong>Tautan lupa password tidak dapat digunakan karena email atau telegram belum diatur di sistem.</strong></h5>
            <p>
                Silakan hubungi administrator atau penyedia layanan {{ config_item('nama_aplikasi') }} untuk mengatur ulang kata sandi Anda.
            </p>
        </div>
    @else
        <form id="validasi" class="login-form mt-4" action="<?= site_url('siteman/kirim_lupa_sandi') ?>" method="post">
            <div class="form-group">
                <input name="email" type="text" placeholder="Email Pengguna" class="form-control required">
            </div>

            <div class="form-group">
                <a href="#" id="b-captcha" onclick="document.getElementById('captcha').src = '<?= site_url('captcha') ?>?' + Math.random();" style="color: #000;">
                    <img id="captcha" src="<?= site_url('captcha') ?>" alt="CAPTCHA Image" />
                </a>
            </div>

            <div class="form-group captcha">
                <input
                    name="captcha_code"
                    type="text"
                    class="form-control"
                    maxlength="6"
                    placeholder="Masukkan kode di atas"
                    autocomplete="off"
                    required
                />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary w-100">Kirim Lupa Sandi</button>
            </div>
        </form>
    @endif
@endsection

@push('js')
    <script>
        $('#b-captcha').click();
    </script>
@endpush
