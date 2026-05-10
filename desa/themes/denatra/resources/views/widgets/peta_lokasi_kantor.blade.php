<div class="card mb-1 z-index-1">
    <div class="card-header p-0">
        <ul class="nav nav-tabs wizard-1" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabmaps-tab" data-toggle="tab" href="#tabmaps" role="tab" aria-controls="tabmaps" aria-selected="true">
                    <span>{{ "Lokasi Kantor " . ucwords(setting('sebutan_desa')) }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tabdetail-tab" data-toggle="tab" href="#tabdetail" role="tab" aria-controls="tabdetail" aria-selected="false">
                    <span>Detail</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tabmaps" role="tabpanel" aria-labelledby="tabmaps-tab">
                <div id="map_canvas" style="height:200px;"></div>
                <a href="https://www.google.com/maps/dir//{{ $desa['lat'] }},{{ $desa['lng'] }}" rel="noopener noreferrer" target="_blank">
                    <button class="btn btn-primary btn-block mb-1 mt-1">Titik Lokasi</button>
                </a>
            </div>
            <div class="tab-pane fade" id="tabdetail" role="tabpanel" aria-labelledby="tabdetail-tab">
                @if (is_file(FCPATH . LOKASI_LOGO_DESA . $desa['kantor_desa']))
                    <a data-fancybox="gallery" href="{{ gambar_desa($desa['kantor_desa'], true) }}">
                        <img src="{{ gambar_desa($desa['kantor_desa'], true) }}" width="100%" class="img-responsive cover" style="float:left; margin:0;" alt="">
                    </a>
                @endif
                <div class="info-desa">
                    <table width="100%">
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">Alamat</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%">{{ $desa['alamat_kantor'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">{{ ucwords(setting('sebutan_desa')) }}</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['nama_desa'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">{{ ucwords(setting('sebutan_kecamatan')) }}</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['nama_kecamatan'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">{{ ucwords(setting('sebutan_kabupaten')) }}</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['nama_kabupaten'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">Kodepos</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['kode_pos'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">Telepon</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['telepon'] }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td class="label-info-desa" width="25%" height="30px">No. HP</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa" width="70%" height="30px">{{ $desa['nomor_operator'] }}</td>
                        </tr>
                        <tr>
                            <td class="label-info-desa" width="25%" height="30px">Email</td>
                            <td width="5%" class="text-center">:</td>
                            <td class="isi-info-desa text-break" width="70%" height="30px">{{ $desa['email_desa'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @if (!empty($desa['lat']) && !empty($desa['lng']))
        var posisi = [{{ $desa['lat'] }}, {{ $desa['lng'] }}];
        var zoom = {{ $desa['zoom'] ?: 10 }};
    @else
        var posisi = [-1.0546279422758742,116.71875000000001];
        var zoom = 10;
    @endif

    var lokasi_kantor = L.map('map_canvas').setView(posisi, zoom);

    // Menampilkan BaseLayers Peta
    var baseLayers = getBaseLayers(lokasi_kantor, "{{ setting('mapbox_key') }}", "{{ setting('jenis_peta') }}");
    L.control.layers(baseLayers, null, {position: 'topright', collapsed: true}).addTo(lokasi_kantor);

    @if (!empty($desa['lat']) && !empty($desa['lng']))
        var kantor_desa = L.marker(posisi).addTo(lokasi_kantor);
    @endif
</script>
