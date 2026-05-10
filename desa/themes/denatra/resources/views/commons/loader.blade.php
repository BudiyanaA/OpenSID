@if (in_array(request()->segment(1), ['']))
    <div class="loader preloader align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="preloader-circle"></div>
            <div class="preloader-img pere-text">
                <img src="{{ gambar_desa(setting('logo')) }}" alt="">
            </div>
        </div>
    </div>
@endif
