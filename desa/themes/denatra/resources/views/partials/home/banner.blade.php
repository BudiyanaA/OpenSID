@if (theme_config('banner', true))
<div class="card mb-1 has-background-img">
    <section class="py-0 bg-white">
        <div class="container-fluid mt-2 main-container">
            <div class="row">
                @php
                    $icons = [
                        'data-wilayah' => 'images/icon/statistik.png',
                        'lapak' => 'images/lapak.svg',
                        'status-idm/' . setting('tahun_idm') => 'images/idm.svg',
                        'peta' => 'images/icon/peta.png',
                        'pengaduan' => 'images/icon/pengaduan.png',
                        'pembangunan' => 'images/icon/pembangunan.png',
                    ];
                @endphp

                @foreach ($icons as $link => $icon)
                    @php
                        $text = '';
                        if ($link === 'status-idm/' . setting('tahun_idm')) {
                            $parts = explode('/', $link);
                            $text = ucfirst($parts[0]);
                            $parts = explode('-', $text);
                            $text = ucfirst($parts[0]) . ' ' . strtoupper($parts[1]);
                        } else {
                            $text = ucwords(str_replace('-', ' ', basename($link)));
                        }
                    @endphp
                    <div class="col-4 col-lg-2 col-md-2">
                        <div class="counter-box text-center mt-2 mb-2">
                            <a href="{{ site_url($link) }}" rel="noopener noreferrer" target="_top">
                                <img src="{{ module_asset('anjungan', '/'.$icon) }}" height="50" alt="" />
                                <div class="small">{{ $text }}</div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
@endif
