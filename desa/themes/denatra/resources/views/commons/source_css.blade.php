<link rel="stylesheet" href="{{ theme_asset('vendor/materializeicon/material-icons.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/animatecss/animate.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/swiper/css/swiper.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/bootstrap-daterangepicker-master/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/footable-bootstrap/css/footable.bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/DataTables-1.10.18/css/responsive.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset('vendor/jquery-jvectormap/jquery-jvectormap-2.0.3.css') }}">
<link id="theme" rel="stylesheet" href="{{ theme_asset('css/purplesidebar.css') }}?v={{ setting('THEME_VERSION') }}" type="text/css">
<link rel="stylesheet" href="{{ theme_asset('css/loader.css') }}?v={{ setting('THEME_TIMESTAMP') }}">
<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" integrity="sha384-tKq+lCBgC8t4aEsy7MRhJx6L1KmM0r2g4pZtCn8QpPA7uAY0yImZBKiB5Xo7nEY1" crossorigin="anonymous">
@if (!empty($desa['nomor_operator']))
    <link rel="stylesheet" href="{{ theme_asset('plugin/czm-chat-support.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('css/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('css/mapbox-gl.css') }}">
@if (request()->segment(1) === 'peta')
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}">
@endif
