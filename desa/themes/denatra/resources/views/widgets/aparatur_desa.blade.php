<link type='text/css' href="{{ base_url() }}assets/front/css/slider.css" rel='Stylesheet' />
<style type="text/css">
    #aparatur_desa .cycle-pager span {
        height: 10px;
        width: 10px;
    }
    .cycle-slideshow {
        max-height: none;
        margin-bottom: 0px;
        border: 0px;
    }
    .cycle-next, .cycle-prev {
        mix-blend-mode: difference;
    }
</style>

<div class="card mb-1 z-index-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0"><i class="material-icons icon-sm">account_circle</i> {{ ucwords(setting('sebutan_pemerintah_desa')) }}</h4>
            </div>
        </div>
    </div>
    <div class="card-body text-center">
        <div id="aparatur_desa" class="cycle-slideshow"
             data-cycle-pause-on-hover="true"
             data-cycle-fx="scrollHorz"
             data-cycle-timeout="2000"
             data-cycle-caption-plugin="caption2"
             data-cycle-overlay-fx-out="slideUp"
             data-cycle-overlay-fx-in="slideDown">

            @if(getWidgetSetting('aparatur_desa', 'overlay') == true)
                <span class="cycle-prev"><img data-src="{{ base_url() }}assets/images/back_button.png" loading="lazy" class="lazyload" alt=""></span>
                <span class="cycle-next"><img data-src="{{ base_url() }}assets/images/next_button.png" loading="lazy" class="lazyload" alt=""></span>
                <div class="cycle-caption"></div>
                <div class="cycle-overlay"></div>
            @else
                <div class="cycle-pager"></div>
            @endif

            @foreach($aparatur_desa['daftar_perangkat'] as $data)
                <img src="{{ $data['foto'] ? $data['foto'] : base_url('assets/files/user_pict/kuser.png') }}" alt="" class="img-responsive cover"
                     data-cycle-title="<span class='cycle-overlay-title'>{{ $data['nama'] }}</span>"
                     data-cycle-desc="@if(setting('tampilkan_kehadiran'))
                        @if($data['status_kehadiran'] == 'hadir')
                            <span class='badge badge-success'>{{ cekKehadiran('kehadiran', 'Ada di Kantor') }}</span>
                        @endif
                        @if($data['tanggal'] == date('Y-m-d') && $data['status_kehadiran'] != 'hadir')
                            <span class='badge badge-warning'>{{ ucwords($data['status_kehadiran']) }}</span>
                        @endif
                        @if($data['kehadiran'] == 1 && $data['tanggal'] != date('Y-m-d'))
                            <span class='badge badge-danger'>{{ cekKehadiran('ketidakhadiran', 'Tidak Ada di Kantor') }}</span>
                        @endif
                     @endif">
            @endforeach
        </div>
    </div>
</div>
