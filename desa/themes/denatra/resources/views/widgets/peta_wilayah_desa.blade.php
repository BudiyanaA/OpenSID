<div class="card mb-1 z-index-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0">
                    <i class="material-icons icon-sm">map</i> {{ "Wilayah " . ucwords(setting('sebutan_desa')) }}
                </h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="map_wilayah" style="height:212px;"></div>
        <a href="https://www.openstreetmap.org/#map=15/{{ $desa['lat'] }}/{{ $desa['lng'] }}" rel="noopener noreferrer" target="_blank">
            <button class="btn btn-primary btn-block mt-1">Buka Peta</button>
        </a>
    </div>
</div>

<script>
    @if (!empty($desa['lat']) && !empty($desa['lng']))
        var posisi = [{{ $desa['lat'] }}, {{ $desa['lng'] }}];
        var zoom = {{ $desa['zoom'] ?: 10 }};
    @else
        var posisi = [-1.0546279422758742, 116.71875000000001];
        var zoom = 10;
    @endif

    // Style polygon
    var style_polygon = {
        stroke: true,
        color: '#FF0000',
        opacity: 1,
        weight: 2,
        fillColor: '#8888dd',
        fillOpacity: 0.5
    };

    var wilayah_desa = L.map('map_wilayah').setView(posisi, zoom);

    // Menampilkan BaseLayers Peta
    var baseLayers = getBaseLayers(wilayah_desa, "{{ setting('mapbox_key') }}", "{{ setting('jenis_peta') }}");
    L.control.layers(baseLayers, null, {position: 'topright', collapsed: true}).addTo(wilayah_desa);

    @if (!empty($desa['path']))
        var polygon_desa = {!! $desa['path'] !!};
        var kantor_desa = L.polygon(polygon_desa, style_polygon)
            .bindTooltip("Wilayah Desa")
            .addTo(wilayah_desa);
        wilayah_desa.fitBounds(kantor_desa.getBounds());
    @endif
</script>
