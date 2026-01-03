@if (in_array(request()->segment(1), ['', 'first']) && in_array(request()->segment(2), ['']))
@push('style')
<style>
.textgambar {
    position: absolute;
    color: black;
    background-color: #ffffff;
    border: 1px solid purple;
    border-radius: 2px;
    padding: 2px;
    opacity: 0.7;
    bottom: 10px;
    left: 10px;
    z-index: 2;
}

#carouselDeNatra {
    position: relative;
    overflow: hidden;
}

#carouselDeNatra .carousel-item {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

#carouselDeNatra .carousel-item img {
    max-height: 100%;
    width: auto;
    object-fit: contain;
}
</style>
@endpush
@php
    $tinggi_slider = theme_config('tinggi_slider', '450px');
@endphp
<div class="carosel swiper-location-carousel h-100 h-sm-auto mb-2" style="max-height: {{ $tinggi_slider }};">
<div id="carouselDeNatra" class="carousel slide" data-ride="carousel" data-interval="2000">
    <div class="carousel-inner" style="max-height: {{ $tinggi_slider }};">
    @php $active = true; @endphp
    @foreach ($slider_gambar['gambar'] as $gambar)
        @php
        $img = str_replace('assets/', '', $slider_gambar['lokasi']) . 'sedang_' . $gambar['gambar'];
        @endphp
        <div class="carousel-item {{ $active ? 'active' : '' }}" style="max-height: {{ $tinggi_slider }};">
        <img src="{{ $img }}" class="d-block img-responsive cover img-thumbnail img-fluid opacity-100" alt="{{ $gambar['judul'] }}">
        @if ($slider_gambar['sumber'] != 3)
            <a href="{{ url('artikel/' . buat_slug($gambar)) }}">
            <div class="carousel-caption d-none d-md-block textgambar">
                <h5>{{ $gambar['judul'] }}</h5>
            </div>
            </a>
        @endif
        </div>
        @php $active = false; @endphp
    @endforeach
    </div>
    <a class="carousel-control-prev" href="#carouselDeNatra" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselDeNatra" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
    </a>
</div>
</div>
@push('script')
<script>
$(document).ready(function() {
    $('#carouselDeNatra').carousel({
    interval: 4000,
    ride: 'carousel',
    wrap: true
    });

    $('#carouselDeNatra .carousel-control-next').click(function(e){
    e.preventDefault();
    $('#carouselDeNatra').carousel('next');
    });

    $('#carouselDeNatra .carousel-control-prev').click(function(e){
    e.preventDefault();
    $('#carouselDeNatra').carousel('prev');
    });
});
</script>
@endpush
@endif
