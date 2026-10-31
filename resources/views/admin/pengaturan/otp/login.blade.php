@extends('admin.auth.index')

@section('auth_title', 'Masuk dengan OTP')
@section('auth_subtitle', 'Masukkan email atau username Anda. Kode sekali pakai akan dikirim ke email atau Telegram akun Anda.')

@section('content')
        <form id="validasi" class="login-form" action="{{ $form_action }}" method="POST">
            <div class="form-group">
                <input
                    id="identifier"
                    name="identifier"
                    type="text"
                    autocomplete="off"
                    placeholder="Email atau Username"
                    class="form-username form-control required"
                    maxlength="100"
                    required autofocus
                >
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Kirim Kode OTP</button>
            </div>

        </form>

        <a href="{{ site_url('siteman') }}" class="btn">
            <i class="fa fa-mobile"></i> Kembali ke Login Password
        </a>
@endsection
