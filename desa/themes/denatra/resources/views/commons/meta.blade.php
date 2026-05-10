<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="google" content="notranslate">
<meta name="theme" content="{{ THEME_NAME }}">
<meta name="theme:version" content="{{ THEME_VERSION }}">
<meta name="designer" content="Ariandi Ryan Kahfi, S.Pd.">
<meta name="theme:designer" content="Ariandi Ryan Kahfi, S.Pd.">
<meta name="author" content="{{ $desa['nama_desa'] ?? 'Pemerintah Desa' }}">
<meta name="kode_desa" content="{{ $desa['kode_desa'] }}">
<meta name="keywords" content="{{ $desa_title.' '.ucwords(setting('sebutan_kecamatan')).' '.$desa['nama_kecamatan'].' '.ucwords(setting('sebutan_kabupaten')).' '.$desa['nama_kabupaten'] }}" />
<meta property="og:site_name" content="{{ $desa_title }}">
<meta property="og:type" content="article">
<meta property="og:locale" content="id_ID">
<meta property="fb:app_id" content="{{ config_item('fbappid') ?: '147912828718' }}">
<meta property="fb:admins" content="{{ config_item('fbadmin') ?: '1117950751' }}">
<meta property="og:image:width" content="400">
<meta property="og:image:height" content="225">

@if(isset($single_artikel))
    @if(isset($single_artikel["judul"]))
        <title>{{ $single_artikel["judul"] . " - $desa_title" }}</title>
        <meta name="description" content="{{ str_replace('"', "'", substr(strip_tags($single_artikel['isi']), 0, 400)) }}">
        <meta property="og:url" content="{{ current_url() }}">
        <meta property="og:title" content="{{ $single_artikel["judul"] }}">
        <meta property="og:description" content="{{ str_replace('"', "'", substr(strip_tags($single_artikel['isi']), 0, 400)) }}">
    @else
        <title>{{ "404 - " . trim(ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa']) }}</title>
        <meta name="description" content="{{ $desa_title . ' ' . ucwords(setting('sebutan_kecamatan')) . ' ' . $desa['nama_kecamatan'] . ' ' . ucwords(setting('sebutan_kabupaten')) . ' ' . $desa['nama_kabupaten'] }}">
        <meta property="og:title" content="{{ '404 - ' . trim(ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa']) }}">
    @endif
@elseif(in_array(request()->segment(1), ['pembangunan']) && !in_array(request()->segment(2), ['']))
    <title>{{ ucwords(str_replace('-', ' ', request()->segment(1))) . " - " . $pembangunan->judul . " - " . $desa_nama }}</title>
    <meta name="description" content="{{ $pembangunan->keterangan . ' - ' . $pembangunan->manfaat }}">
    <meta property="og:url" content="{{ current_url() }}">
    <meta property="og:title" content="{{ $pembangunan->judul . ' - ' . $desa_nama }}">
    <meta property="og:description" content="{{ $pembangunan->keterangan . ' - ' . $pembangunan->manfaat }}">
@elseif(in_array(request()->segment(1), ['data-statistik', 'data-wilayah', 'data-vaksinasi']) || in_array(request()->segment(2), ['dpt', 'statistik']))
    <title>{{ "Data " . $heading . " - " . $desa_nama }}</title>
    <meta name="description" content="{{ 'Data ' . $heading . ' - ' . $desa_nama . ' ' . $desa_wilayah }}">
    <meta property="og:url" content="{{ current_url() }}">
    <meta property="og:title" content="{{ 'Data ' . $heading . ' - ' . $desa_nama }}">
    <meta property="og:description" content="{{ 'Data ' . $heading . ' - ' . $desa_nama . ' ' . $desa_wilayah }}">
@elseif(in_array(request()->segment(1), ['status-idm']))
    <title>Status IDM {{ $idm->SUMMARIES->TAHUN . ' - ' . $desa_nama }}</title>
    <meta name="description" content="Status IDM {{ $idm->SUMMARIES->TAHUN . ' ' . $desa_nama . ' ' . $desa_wilayah }}">
    <meta property="og:url" content="{{ current_url() }}">
    <meta property="og:title" content="Status IDM {{ $desa_nama }} : {{ $idm->SUMMARIES->STATUS }}">
    <meta property="og:description" content="Skor IDM {{ number_format($idm->SUMMARIES->SKOR_SAAT_INI, 4) }} - {{ $desa_nama . ' ' . $desa_wilayah }}">
@elseif(!in_array(request()->segment(1), ['']))
    <title>{{ ucwords(str_replace('-', ' ', request()->segment(1))) . ' - ' . $desa_nama }}</title>
    <meta name="description" content="{{ $desa_title . ' ' . $desa_wilayah }}">
    <meta property="og:url" content="{{ current_url() }}">
    <meta property="og:title" content="{{ ucwords(str_replace('-', ' ', request()->segment(1))) . ' - ' . $desa_nama }}">
    <meta property="og:description" content="{{ $desa_title . ' ' . $desa_wilayah }}">
@else
    @php $tmp = ltrim(get_dynamic_title_page_from_path(), ' -'); @endphp
    <title>{{ trim($tmp) == '' ? $desa_title : "$tmp - $desa_title" }}</title>
    <meta name="description" content="{{ $desa_title . ' ' . $desa_wilayah }}">
    <meta property="og:url" content="{{ current_url() }}">
    <meta property="og:title" content="{{ trim($tmp) == '' ? $desa_nama : "$tmp - $desa_nama" }}">
    <meta property="og:description" content="{{ $desa_title . ' ' . $desa_wilayah }}">
@endif
@if(!empty(trim($single_artikel['gambar'] ?? '')))
    <meta property="og:image" content="{{ base_url() . LOKASI_FOTO_ARTIKEL . 'sedang_' . $single_artikel['gambar'] }}">
@elseif(in_array(request()->segment(1), ['pembangunan']) && !in_array(request()->segment(2), ['']))
    <meta property="og:image" content="{{ base_url() . LOKASI_GALERI . $pembangunan->foto }}">
@else
    <meta property="og:image" content="{{ gambar_desa($desa['kantor_desa'], true) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $single_artikel['judul'] ?? $desa_title }}">
<meta name="twitter:description" content="{{ str_replace('"', "'", substr(strip_tags($single_artikel['isi'] ?? $desa_title), 0, 400)) }}">
<meta name="twitter:image" content="{{ !empty($single_artikel['gambar']) ? base_url(LOKASI_FOTO_ARTIKEL . 'sedang_' . $single_artikel['gambar']) : gambar_desa($desa['kantor_desa'], true) }}">

<link rel="canonical" href="{{ current_url() }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="subject" content="{{ $desa_title . ' ' . $desa_wilayah }}">
<meta name="copyright" content="{{ $desa_title }}">
<meta name="language" content="Indonesia">
<meta name="Classification" content="Government">
<meta name="url" content="{{ site_url() }}">
<meta name="identifier-URL" content="{{ site_url() }}">
<meta name="category" content="{{ $desa_title }}">
<meta name="coverage" content="Worldwide">
<meta name="distribution" content="Global">
<meta name="rating" content="General">
<meta name="webcrawlers" content="all">
<meta name="spiders" content="all">

<link rel="alternate" type="application/rss+xml" title="Feed {{ $desa_title }}" href="{{ site_url('sitemap') }}">
<link rel="icon" href="{{ favico_desa() }}">
<link rel="apple-touch-icon" href="{{ favico_desa() }}">
<link rel="apple-touch-icon-precomposed" href="{{ favico_desa() }}">
<link rel="shortcut icon" href="{{ favico_desa() }}">

<script>
    var BASE_URL = '{{ base_url() }}';
    var SITE_URL = '{{ site_url() }}';
    var setting = @json(setting());
    var config = @json(identitas());

    function replaceWord(marking = {}, text) {
        for (const [key, value] of Object.entries(marking)) {
            text = text.replace(new RegExp(key, 'g'), value);
        }
        return text.replace(/[\[\]]/g, '');
    }
</script>