@extends('theme::layouts.full-content')
@section('content')
@php
$title = (!empty($judul_kategori)) ? $judul_kategori : 'Artikel Terkini';
$slug = 'terkini';
if (is_array($title)) {
    $slug = $title['slug'];
    $title = $title['kategori'];
}
@endphp
@includeIf('theme::commons.constant')
<div class="row">
    <div class="container{{ cekFluid() ? '-fluid' : '' }} main-container text-center mt-0 mb-1">
        <div class="card fullscreen {{ cekKondisiPink() }}-gradient">
            <h5 class="mt-2 mb-2"><b>{{ $title }}</b></h5>
        </div>
    </div>

    @if (count($artikel))
        <div class="container{{ cekFluid() ? '-fluid' : '' }} main-container mt-0">
            <div class="row">
                @foreach ($artikel as $data)
                    @php
                        $url = $data['url_slug'];
                        $abstract = potong_teks(strip_tags($data['isi']), 120);
                        $image = ($data['gambar'] && is_file(LOKASI_FOTO_ARTIKEL . 'sedang_' . $data['gambar'])) ? AmbilFotoArtikel($data['gambar'], 'sedang') : theme_asset("images/noimage.png");
                    @endphp
                    <div class="col-12 {{ cekFluid() ? 'col-lg-4 col-md-4' : 'col-lg-6 col-md-6' }}">
                        <div class="card mb-1">
                            <a href="{{ $url }}" title="Baca Selengkapnya">
                                <div class="background-img blog-header-img d-md-none d-lg-none d-xl-none" style="{{ cekFluid() ? 'max-height:220px' : 'max-height:300px' }}">
                                    <img src="{{ $image }}" class="@if($image !== theme_asset('images/noimage.png')) img-responsive cover img-fluid opacity-100 @endif" alt="">
                                </div>
                                <div class="background-img blog-header-img text-hide-xs" style="{{ cekFluid() ? 'max-height:220px; min-height:220px' : 'max-height:300px; min-height:300px' }}">
                                    <img src="{{ $image }}" class="@if($image !== theme_asset('images/noimage.png')) img-responsive cover img-fluid opacity-100 @endif" alt="">
                                </div>
                            </a>
                            <div class="card-header border-bottom" style="min-height:85px">
                                <div class="media">
                                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                                        <i class="material-icons">fingerprint</i>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="my-0 content-color-primary">
                                            <a href="{{ $url }}" title="Baca Selengkapnya">{{ $data["judul"] }}</a>
                                        </h6>
                                        <p class="small mb-0">
                                            <i class="fa fa-calendar" aria-hidden="true"></i> {{ tgl_indo2($data['tgl_upload']) }}
                                            <i class="fa fa-user" aria-hidden="true"></i> {{ $data['owner'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-hide-xs" style="min-height:100px">
                                <div class="mb-0">
                                    <p align="justify">{!! $abstract !!} ...</p>
                                </div>
                            </div>
                            <div class="card-footer border-top">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="content-color-secondary mb-0 small">
                                                    <i class="material-icons icon-sm">chat</i> 0 Komentar
                                                    @if (trim($data['kategori']) != '')
                                                        <i class="fa fa-flag" aria-hidden="true"></i> {{ $data['kategori'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right pr-3">
                                        <div class="media">
                                            <div class="media-body">
                                                <p class="content-color-secondary mb-0 small">
                                                    <i class="fa fa-heart" aria-hidden="true"></i> {{ hit($data['hit']) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="container{{ cekFluid() ? '-fluid' : '' }} main-container h-100">
            <div class="card align-items-center">
                <div class="col-12 mx-auto text-center">
                    <img src="{{ theme_asset('img/404.svg') }}" alt="" class="mw-100 mt-2">
                    <h2 class="text-black">404! ARTIKEL BELUM TERSEDIA</h2>
                    <p class="lead mb-4 text-black">Maaf, Artikel belum tersedia atau sedang dalam perbaikan.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
