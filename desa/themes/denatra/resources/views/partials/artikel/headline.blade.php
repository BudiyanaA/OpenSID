@php
    $url = $headline->url_slug;
    $abstrak_headline = potong_teks(strip_tags($headline['isi']), 300);
    $noimage = theme_asset('images/noimage.png');
    $image = ($headline['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $headline['gambar'])) 
        ? AmbilFotoArtikel($headline['gambar'], 'sedang') 
        : $noimage;
@endphp

<div class="card mb-1 fullscreen has-background-img">
    <div class="row no-gutters">
        <div class="col-12 col-sm-6 col-lg-5 has-background-img min-height-300">
            <figure class="background-img">
                <a href="{{ $url }}" title="Baca Selengkapnya">
                    <div class="background-img blog-header-img">
                        <img src="{{ $image }}" class="@if($image !== $noimage) img-responsive cover img-fluid opacity-100 @endif" alt="{{ $headline->judul }}">
                    </div>
                </a>
            </figure>
        </div>
        <div class="col-12 col-sm-6 col-lg-7">
            <div class="card-header border-bottom">
                <div class="media">
                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                        <i class="material-icons">favorite</i>
                    </div>
                    <div class="media-body">
                        <h6 class="my-0 content-color-primary">
                            <a href="{{ $url }}" title="Baca Selengkapnya">{{ $headline->judul }}</a>
                        </h6>
                        <p class="small mb-0">
                            <i class="material-icons icon-sm">date_range</i> {{ tgl_indo2($headline['tgl_upload']) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body text-hide-xs">
                <p align="justify">{{ $abstrak_headline }} ...</p>
            </div>
            <div class="card-footer border-top">
                <div class="row">
                    <div class="col">
                        <div class="media">
                            <div class="media-body">
                                <p class="content-color-secondary mb-0 small">
                                    <i class="material-icons icon-sm">flag</i> Berita Utama
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col text-right">
                        <div class="media">
                            <div class="media-body">
                                <p class="content-color-secondary mb-0 small">
                                    <i class="material-icons icon-sm">favorite</i> {{ hit($headline['hit']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
