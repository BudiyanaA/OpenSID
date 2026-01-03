@extends('theme::layouts.full-content')

@push('style')
<style>
.map {
    width: 100%;
    height: 70vh;
}

#qrcode .panel-body-lg {
    margin-right: 5px;
    margin-bottom: 20px;
    pointer-events: visiblePainted;
    pointer-events: auto;
    position: relative;
    z-index: 800;
}

.leaflet-popup-content {
    height: auto;
    width: 225px;
    overflow-y: scroll;
}
</style>
@endpush

@section('content')
<div class="card mt-0 mb-1 has-background-img">
    <div class="form-inline text-center">
        <select class="form-control input-sm select2 ml-1 mr-1 mb-1 mt-1" id="id_kategori" name="id_kategori">
            <option selected value="">Semua Kategori</option>
        </select>
        <div class="input-group mb-1 small ml-1 mr-1 mt-1">
            <input type="text" name="keyword" id="search" class="form-control" autocomplete="off" placeholder="Cari Produk" aria-label="Cari Produk">
            <div class="input-group-append">
                <button class="btn btn-info" id="btn-search" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>

        <div class="input-group-append ml-1 mt-1 mb-1" style="display: none;">
            <a href="#" class="btn btn-info" id="btn-semua">Tampilkan Semua</a>
        </div>
        </form>
    </div>
</div>
<div class="mt-0 mb-1 z-index-2">
    <div class="card-header {{ theme_config('color', 'pink') }}-gradient font-weight-bold text-center">
        Lapak Online {{ ucwords(setting('sebutan_desa')) }}
    </div>
    <div class="card mb-0 fullscreen has-background-img">
        <div class="container-fluid">
            <div class="row" id="produk-list">
            </div>
            @includeIf('theme::commons.pagination')
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetail" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title nama-produk">Produk</h6>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalLokasi" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title nama-produk">Produk</h6>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
$(document).ready(function() {
        var imgNotFound = `{{ theme_asset('images/noimage.png') }}`;

        var apiKategori = '{{ route('api.lapak.kategori') }}';
        jQuery.get(apiKategori, function(data) {
            var kategori = data.data;
            var select = $('#id_kategori');
            kategori.forEach(function(item) {
                select.append('<option value="' + item.id + '">' + item.attributes.kategori + '</option>');
            });
        });

        function loadProduk(params = {}) {

            var apiProduk = '{{ route('api.lapak.produk') }}';                
            $('#produk-list').html(`@includeIf('theme::commons.loading')`);

            const spinnerImg = `{{ theme_asset('images/icon/spinner.svg') }}`;
            const latarWebsite = `{{ $latar_website ? $latar_website : base_url('assets/front/css/images/latar_website.jpg'); }}`;
            const fullscreenImg = `{{ theme_asset('images/icon/fullscreen.svg') }}`;

            jQuery.get(apiProduk, params, function(data) {
                var produk = data.data;
                var produkList = $('#produk-list');

                produkList.empty();

                if (!produk.length) {
                    produkList.html(`
                    <div class="font-weight-bold text-center mt-4 mb-4">
                        <h5>Belum ada produk yang ditawarkan pada halaman ini.</h5>
                    </div>
                    `);
                    return;
                }

                produk.forEach(function(item) {
                    var fotoHTML = ``;
                    var fotoList = item.attributes.foto;
                    if(fotoList.length > 0) {
                        fotoHTML = `
                        <div class="col-md-12">
                            <div id="foto-produk-${item.id}" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">`;
                    }
                    
                    fotoList.forEach(function(fotoItem, index) {
                        fotoHTML += `
                        <div class="carousel-item ${!index ? 'active' : ''}" style="max-height:200px; min-height:200px">
                            <a data-fancybox="gallery" href="${fotoItem}">
                                <img loading="lazy" src="${fotoItem}" style="border-radius:3px;" alt="">
                            </a>
                        </div>
                        `;
                    });
                    if(fotoList.length > 0) {
                        fotoHTML += `
                        <a class="carousel-control-prev" href="#foto-produk-${item.id}" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#foto-produk-${item.id}" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                        `;
                    }
                    fotoHTML += `</div></div>`;

                    var hargaDiskon = formatRupiah(item.attributes.harga_diskon.toString(), 'Rp ');
                    var hargaAwal = formatRupiah(item.attributes.harga.toString(), 'Rp ');
                    var viewDiskon = (hargaAwal === hargaDiskon) ? `` : `<font style="color:red; text-decoration: line-through red;">${hargaAwal}</font>`;
                    var komentarImg = `{{ theme_asset('images/icon/komentar.svg') }}`;
                    var shopImg = `{{ theme_asset('images/icon/shop.svg') }}`;
                    var locationImg = `{{ theme_asset('images/icon/location.svg') }}`;
                    var harga_potongan = (item.attributes.tipe_potongan === 1) 
                        ? (item.attributes.harga * (item.attributes.potongan / 100)) 
                        : item.attributes.potongan;
                    var persen = (item.attributes.tipe_potongan === 1) 
                        ? item.attributes.potongan / 100 
                        : item.attributes.potongan / item.attributes.harga;
                    var pesanWa = replaceWord({
                        'nama_produk': item.attributes.nama,
                        'link_web': SITE_URL + 'lapak'
                    }, setting.pesan_singkat_wa);

                    var detailProdukHtml = `
                    <div class="card-body">
                        <div class="font-weight-bold">${item.attributes.nama}</div>
                        <span class="text-success">
                            <b>${hargaDiskon}
                                <small>/${item.attributes.satuan}</small>
                            </b>
                            ${harga_potongan > 0 ? `
                            <small style="color:red; text-decoration: line-through red;">
                                (${hargaAwal})
                            </small>` : ''}
                        </span>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="btn-group">
                                ${item.attributes.pelapak.telepon ? `
                                <a class="btn btn-sm btn-success" href="${item.attributes.pesan_wa}" rel="noopener noreferrer" target="_blank" title="WhatsApp ${item.attributes.pelapak.telepon}">
                                    <i class="fa fa-whatsapp"></i> Pesan
                                </a>` : ''}
                                <a class="btn btn-sm btn-danger text-white" data-remote="false" data-toggle="modal" data-target="#modalLokasi" data-product='${JSON.stringify(item.attributes)}' data-lat="${item.attributes.pelapak.lat}" data-lng="${item.attributes.pelapak.lng}" data-nama="${item.attributes.nama}" title="Area Lokasi">
                                    <i class="fa fa-map"></i> Lokasi
                                </a>
                                <a class="btn btn-sm btn-primary text-white" data-remote="false" data-toggle="modal" data-target="#modalDetail" data-product='${JSON.stringify(item.attributes)}' title="Deskripsi" data-pesanwa="${pesanWa}">
                                    <i class="fa fa-info-circle"></i> Deskripsi
                                </a>
                            </div>
                        </div>
                    </div>
                    `
                    var cekFluid = `{{ theme_config('fluid', false) }}`;
                    var fluid = cekFluid ? 'col-lg-3 col-md-3' : 'col-lg-4 col-md-4';
                    var produkHTML = `
                        <div class="col-12 ${fluid}">
                            <div class="card mb-2 mt-2">
                                <div class="card-body py-0">
                                    <div class="row border-bottom">
                                        ${fotoHTML}
                                        ${detailProdukHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    produkList.append(produkHTML);
                });                   

                initPagination(data);
            });
        }

        $('#btn-cari').on('click', function() {
            var params = {};
            var kategori = $('#id_kategori').val();
            var search = $('#search').val();

            if (kategori) {
                params['filter[id_produk_kategori]'] = kategori;
            }

            if (search) {
                params['filter[search]'] = search;
            }                
            loadProduk(params);

            $('#btn-semua').show();
        });

        $('.pagination').on('click', '.btn-page', function() {
            var params = {};
            
            var page = $(this).data('page');
            var kategori = $('#id_kategori').val();
            var search = $('#search').val();

            if (kategori) {
                params['filter[id_produk_kategori]'] = kategori;
            }

            if (search) {
                params['filter[search]'] = search;
            }

            params['page[number]'] = page;

            loadProduk(params);
        });

        $('#btn-semua').on('click', function() {
            loadProduk();
            $('#btn-semua').hide();
            $('#search').val('');
            $('#id_kategori').val('');
        });

        $('#search').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#btn-cari').trigger('click');
            }
        });

        $('#id_kategori').change(function() {
            $('#btn-semua').show();
        });

        loadProduk();

        $('#modalDetail').on('shown.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var product = button.data('product');
            var pesanWa = button.data('pesanwa');
            
            var modal = $(this);
            modal.find('.modal-body').html(`
                <h6>Deskripsi:</h6>
                ${product.deskripsi}
                <br>
                <br>
                <h6><i class="fa fa-user"></i>&nbsp;${product.pelapak.penduduk.nama}</h6>
                Kontak: ${product.pelapak.telepon}
                <div class="modal-footer">
                <div class="btn-group">
                    <a class="btn btn-sm btn-success" href="${product.pesan_wa}" rel="noopener noreferrer" target="_blank" title="WhatsApp ${product.pelapak.telepon}"><i class="fa fa-whatsapp"></i> Pesan</a>
                    <a class="btn btn-sm btn-danger" href="https://www.google.com/maps/dir//${product.pelapak.lat},${product.pelapak.lng}" rel="noopener noreferrer" target="_blank" title="Titik Lokasi"><i class="fa fa-map-marker"></i> Titik Lokasi</a>
                </div>
            </div>
            `);
        });

        $('#modalLokasi').on('shown.bs.modal', function (event) {
            const link = $(event.relatedTarget);
            const produk = link.data('product');
            const modal = $(this);

            let foto = '';
            if (produk.foto && produk.foto.length > 0) {
                foto += `<div id="foto-popup-${produk.id}" class="carousel slide" data-ride="carousel">`;
                foto += '<div class="carousel-inner">';
                produk.foto.forEach((fotoItem, index) => {
                    foto += `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <a data-fancybox="gallery" href="${fotoItem}">
                                <img alt="" src="${fotoItem}" class="mw-100" style="border-radius:1px;"/>
                            </a>
                        </div>
                    `;
                });
                foto += '</div>';
                foto += '</div>';
            } else {
                foto += `
                    <img alt="" src="${imgNotFound}" class="mw-100" style="border-radius:1px;"/>
                `;
            }

            const infoTempat = `
                <div id="content">
                    <h6><b style="color:red"><center>${produk.nama}</center></b></h6>
                    <div id="bodyContent" class="mb-2 text-center">${foto}</div>
                    <table>
                        <tr>
                            <td width="60px">Harga</td>
                            <td width="10px">:</td>
                            <td><span class="text-success"><b>${formatRupiah(produk.harga_diskon.toString(), 'Rp ')}</b>/<small>${produk.satuan}</small></span></td>
                        </tr>
                        <tr>
                            <td width="60px">Kontak</td>
                            <td width="10px">:</td>
                            <td>${produk.pelapak.telepon}</td>
                        </tr>
                        <tr>
                            <td width="60px" valign="top">Pelapak</td>
                            <td width="10px" valign="top">:</td>
                            <td><b style="color:red">${produk.pelapak.penduduk.nama}</b></td>
                        </tr>
                        <tr>
                            <td width="60px">Tujuan</td>
                            <td width="10px">:</td>
                            <td>
                                <a class="btn btn-sm btn-danger danger-gradient mt-0" target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/dir//${link.data('lat')},${link.data('lng')}">
                                    <i class="fa fa-map-marker"></i> Titik Lokasi
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            `;

            modal.find('.modal-title').text(link.data('title'));
            modal.find('.modal-body').html("<div id='map' style='width: 100%; height:350px'></div>");

            const posisi = [link.data('lat'), link.data('lng')];
            const zoom = link.data('zoom') || 10;
            const popupContent = link.closest('.this-product').find('.detail').html();

            const mapOptions = {
                maxZoom: setting.max_zoom_peta, 
                minZoom: setting.min_zoom_peta
            };
            modal.find('.nama-produk').text(link.data('nama'));

            $('#lat').val(posisi[0]);
            $('#lng').val(posisi[1]);

            if (window.pelapak) {
                window.pelapak.remove();
            }

            window.pelapak = L.map('map', mapOptions).setView(posisi, zoom);
            getBaseLayers(window.pelapak, setting.mapbox_key, setting.jenis_peta);

            const markerIcon = L.icon({
                    iconUrl: setting.icon_lapak_peta
                });
                
            L.marker(posisi, { icon: markerIcon }).addTo(window.pelapak);

            L.marker(posisi, {
                icon: markerIcon
            }).addTo(window.pelapak).bindPopup(`${infoTempat}`);

            L.control.scale().addTo(window.pelapak);

            window.pelapak.invalidateSize();
        });

    });
</script>
@endpush