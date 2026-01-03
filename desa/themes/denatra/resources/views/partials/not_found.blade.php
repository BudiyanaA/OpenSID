<div class="row">
    <div class="container{{ cekFluid() ? '-fluid' : '' }} main-container h-100">
        <div class="col-12 mx-auto text-center">
            <img src="{{ theme_asset('img/404.svg') }}" alt="" class="mw-100 mt-2">
            <h2 class="text-black">404! {{ $judulPesan ?? 'Menu Tidak terdaftar' }}</h2>
            <p class="lead mb-4 text-black">
                {!! $isiPesan ?? "Silakan tambah menu terlebih dahulu.<br>Anda bisa melihat panduan membuat menu di link <a href='https://panduan.opendesa.id/opensid/halaman-administrasi/admin-web/menu' target='_blank'>Panduan</a>" !!}
            </p>
        </div>
    </div>
</div>
