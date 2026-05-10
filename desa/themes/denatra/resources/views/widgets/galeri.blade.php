<!-- widget Galeri -->
<div class="card mb-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0">
                    <i class="material-icons icon-sm">insert_photo</i>
                    <a href="{{ site_url() }}galeri">{{ $judul_widget }}</a>
                </h4>
            </div>
        </div>
    </div>
    <div class="card-body text-center">
        @foreach ($w_gal as $data)
            <a href="{{ site_url("galeri/{$data['id']}") }}" title="Album : {{ $data['nama'] }}">
                <img class="mw-100 border mb-1" width="120" loading="lazy"
                     src="{{ is_file(AmbilGaleri($data['gambar'], 'sedang')) ? AmbilGaleri($data['gambar'], 'sedang') : theme_asset('images/noimage.png') }}"
                     alt="">
            </a>
        @endforeach
    </div>
</div>
