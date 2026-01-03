<script src="{{ asset('front/js/jquery.js') }}"></script>
<script src="{{ asset('front/js/layout.js') }}"></script>
<script src="{{ asset('front/js/jquery.cycle2.min.js') }}"></script>
<script src="{{ asset('front/js/jquery.cycle2.carousel.js') }}"></script>

<script src="{{ asset('js/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet-providers.js') }}"></script>
<script src="{{ asset('js/mapbox-gl.js') }}"></script>
<script src="{{ asset('js/leaflet-mapbox-gl.js') }}"></script>
<script src="{{ asset('js/peta.js') }}"></script>

<script src="{{ asset('js/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('js/highcharts/highcharts-3d.js') }}"></script>
<script src="{{ asset('js/highcharts/exporting.js') }}"></script>
<script src="{{ asset('js/highcharts/highcharts-more.js') }}"></script>
<script src="{{ asset('js/highcharts/sankey.js') }}"></script>
<script src="{{ asset('js/highcharts/organization.js') }}"></script>
<script src="{{ asset('js/highcharts/accessibility.js') }}"></script>

<script src="{{ asset('bootstrap/js/select2.full.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/moment.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap3-wysihtml5.all.min.js') }}"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

@includeIf('admin.layouts.components.validasi_form', ['web_ui' => true])

<script type="text/javascript">
    const BASE_URL = '{{ site_url() }}';
</script>

<link rel="stylesheet" href="{{ theme_asset('css/bootstrap-lazy-load.css') }}">
<link 
    rel="stylesheet" 
    href="{{ theme_asset('css/widget.min.css') }}?v={{ setting('THEME_TIMESTAMP') }}">
<script src="{{ theme_asset('js/widget.js') }}?v={{ setting('THEME_TIMESTAMP') }}"></script>

@includeIf('theme::partials.module_top')
@includeIf('core::admin.layouts.components.token')
