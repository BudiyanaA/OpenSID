@extends('theme::layouts.full-content')
@include('theme::commons.constant')
@section('content')

<div class="mt-0 mb-1 z-index-2">
    <div class="card-header {{ theme_config('color', 'pink') }}-gradient font-weight-bold text-center">
        Detail Pembangunan
    </div>

    <div id="printableArea" class="card mb-0 fullscreen has-background-img">
        <div class="row">
            <div class="col-12 col-lg-6 col-md-6">
                <div class="card-header border-bottom">
                    <div class="media">
                        <div class="icon-circle icon-40 bg-light-primary mr-3">
                            <i class="material-icons">account_balance</i>
                        </div>
                        <div class="media-body">
                            <h6 class="my-0 content-color-primary judul-pembangunan"></h6>
                            <p class="small mb-0">
                                <i class="material-icons icon-sm">local_offer</i> {{ ucwords(setting('sebutan_desa'))." ".$desa['nama_desa'] }}
                            </p>
                        </div>
                        <a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
                            <i class="material-icons ">crop_free</i>
                        </a>
                    </div>
                </div>
                <div class="card-body" id="detail-pembangunan"></div>
            </div>

            <div class="col-12 col-lg-6 col-md-6" id="dokumentasiWrapper" style="display:none;">
                <div class="card-header border-bottom">
                    <div class="media">
                        <div class="icon-circle icon-40 bg-light-primary mr-3">
                            <i class="material-icons">account_balance</i>
                        </div>
                        <div class="media-body">
                            <h6 class="my-0 content-color-primary">Progres Pembangunan</h6>
                            <p class="small mb-0">
                                <i class="material-icons icon-sm">local_offer</i> <span id="judul-pembangunan-text">Memuat...</span>
                            </p>
                        </div>
                        <a class="btn btn-sm btn-info" href="{{ site_url() }}pembangunan">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="album-pembangunan"></div>
                </div>
                <div class="card-header border-bottom">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="my-0 content-color-primary">Lokasi Pembangunan</h4>
                        </div>
                        <a id="titik-lokasi-btn" class="btn btn-sm btn-danger" href="#" target="_blank" rel="noopener noreferrer" title="Titik Lokasi">
                            <i class="fa fa-map-marker"></i> Titik Lokasi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="map" class="z-index-1" style="height: 340px;"></div>
                </div>
            </div>

            <div id="shareWrapper"></div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
$(document).ready(function() {
    var slug = `{{ $slug }}`;
    var notFound = `{{ theme_asset('images/nodata.png') }}`;
    const spinnerImg = `{{ theme_asset('images/icon/spinner.svg') }}`;
    const latarWebsite = `{{ $latar_website ? $latar_website : base_url('assets/front/css/images/latar_website.jpg') }}`;
    const fullscreenImg = `{{ theme_asset('images/icon/fullscreen.svg') }}`;

    function loadPembangunan() {
        const apiPembangunan = `{{ route('api.pembangunan') }}`;
        const params = { 'filter[slug]': slug };

        jQuery.get(apiPembangunan, params, function(response) {
            var detailPembangunan = $('#detail-pembangunan');
            detailPembangunan.empty();

            if (response.data.length !== 1) {
                detailPembangunan.html(`
                    <div class="font-weight-bold text-center mt-4 mb-4">
                        <h5>Belum ada pembangunan pada halaman ini.</h5>
                    </div>
                `);
                return;
            }

            const pembangunan = response.data[0].attributes;
            const dokumentasi = pembangunan.pembangunan_dokumentasi;
            var fotoAlbum = pembangunan.foto;
            var judulPembangunan = $('<div>').text(pembangunan.judul).html();

            $('.judul-pembangunan').text(pembangunan.judul);
            $('#judul-pembangunan-text').text(pembangunan.judul);
            
            // Update link Google Maps sesuai koordinat
            if (pembangunan.lat && pembangunan.lng) {
                $('#titik-lokasi-btn').attr('href', `https://www.google.com/maps/dir//${pembangunan.lat},${pembangunan.lng}`);
            } else {
                $('#titik-lokasi-btn').attr('href', '#').addClass('disabled').text('Lokasi tidak diketahui');
            }

            var linkShare = SITE_URL + 'pembangunan/' + pembangunan.slug;
            var judulShare = pembangunan.judul;
            $('#shareWrapper').html(`@includeIf('theme::commons.share', ['link' => '${linkShare}', 'judul' => '${judulShare}'])`);

            var pembangunanHTML = '';
            var anggaran = formatRupiah(pembangunan.anggaran.toString(), 'Rp ');
            @if (IS_PREMIUM && IS_251010)
            var realisasi_anggaran = formatRupiah(pembangunan.realisasi_anggaran.toString(), 'Rp ');
            var silpa = formatRupiah(pembangunan.silpa.toString(), 'Rp ');
            @endif

            pembangunanHTML += `
                <a data-fancybox="gallery" href="${pembangunan.foto}">
                    <center><img title="${pembangunan.keterangan}" class="img-fluid mb-1 mw-100" style="max-height:280px" loading="lazy" src="${pembangunan.foto}" alt=""></center>
                </a>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr><td>Nama Kegiatan</td><td>:</td><td>${pembangunan.judul}</td></tr>
                            <tr><td>Lokasi</td><td>:</td><td>${pembangunan.alamat === "=== Lokasi Tidak Ditemukan ===" ? 'Lokasi tidak diketahui' : pembangunan.alamat}</td></tr>
                            <tr><td>Anggaran</td><td>:</td><td>${anggaran}</td></tr>
                            @if (IS_PREMIUM && IS_251010)
                            <tr><td style="width:25%;">Realisasi Anggaran</td><td style="width:10px;text-align:center;">:</td><td>${realisasi_anggaran}</td></tr>
                            <tr><td style="width:25%;">Sisa Anggaran (SiLPA)</td><td style="width:10px;text-align:center;">:</td><td>${silpa}</td></tr>
                            @endif
                            <tr><td>Volume</td><td>:</td><td>${pembangunan.volume}</td></tr>
                            <tr><td>Sumber Dana</td><td>:</td><td>${pembangunan.sumber_dana}</td></tr>
                            <tr><td>Tahun</td><td>:</td><td>${pembangunan.tahun_anggaran}</td></tr>
                            <tr><td>Pelaksana</td><td>:</td><td>${pembangunan.pelaksana_kegiatan}</td></tr>
                            <tr><td>Manfaat</td><td>:</td><td>${pembangunan.manfaat}</td></tr>
                            <tr><td>Keterangan</td><td>:</td><td>${pembangunan.keterangan}</td></tr>
                        </tbody>
                    </table>
                </div>`;

            var gambarDokumentasi = '';
            var pembangunanFotoDefault = `{{ theme_asset('images/pembangunan.png') }}`;
            if (dokumentasi && dokumentasi.length > 0) {
                dokumentasi.forEach((dok) => {
                    let fotoDokumentasi = dok.gambar ? `<img class="img-fluid border mw-100" style="max-height:120px" loading="lazy" src="${dok.gambar}" alt="Foto Pembangunan ${dok.persentase}"/>` : `<img class="img-fluid border mw-100" style="max-height:120px" loading="lazy" src="${pembangunanFotoDefault}" alt="Foto Pembangunan ${dok.persentase}"/>`;
                    gambarDokumentasi += `
                        <div class="col-12 col-lg-6 col-md-6 text-center" style="margin-bottom:10px">
                            <a data-fancybox="gallery" href="${dok.gambar}">
                                <center>${fotoDokumentasi}</center>
                            </a>
                            <b>${dok.persentase}</b>
                        </div>`;
                });
            } else {
                gambarDokumentasi += `
                    <div class="col-12 text-center">
                        <b>Belum ada progres.</b>
                    </div>`;
            }

            $('#dokumentasiWrapper').show();
            $('#album-pembangunan').html(gambarDokumentasi);
            detailPembangunan.append(pembangunanHTML);

            loadMap(pembangunan);
        });
    }

    function loadMap(pembangunan) {
        if (pembangunan.lat && pembangunan.lng) {
            let lat = pembangunan.lat || config.lat;
            let lng = pembangunan.lng || config.lng;
            let posisi = [lat, lng];
            let zoom = setting.default_zoom || 15;

            let logo = L.icon({
                iconUrl: setting.icon_pembangunan_peta,
                iconSize: [30, 40],
                iconAnchor: [15, 40]
            });

            let options = {
                maxZoom: setting.max_zoom_peta || 18,
                minZoom: setting.min_zoom_peta || 5,
                attributionControl: true
            };

            let map = L.map('map', options).setView(posisi, zoom);
            getBaseLayers(map, setting.mapbox_key, setting.jenis_peta);
            L.marker(posisi, { icon: logo }).addTo(map);
        }
    }

    loadPembangunan();
});
</script>
@endpush
