@php
date_default_timezone_set($desa['timezone']);

$namaProvinsi = preg_replace('/^(\d{2})(\d{2})(\d{2})(\d{4})$/', '$1.$2.$3.$4', $desa['kode_desa']);

$api_url = "https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=" . $namaProvinsi;
$response = @file_get_contents($api_url);
$data = $response ? json_decode($response, true) : null;

$today = date("d-m-Y");
$tomorrow = date("d-m-Y", strtotime("+1 day"));
$dayAfterTomorrow = date("d-m-Y", strtotime("+2 days"));

$labels = [
    $today => 'Hari Ini',
    $tomorrow => 'Besok',
    $dayAfterTomorrow => 'Lusa'
];

$kodeCuaca = [
    0 => 'Cerah', 1 => 'Cerah Berawan', 2 => 'Cerah Berawan', 3 => 'Berawan', 4 => 'Berawan Tebal',
    5 => 'Udara Kabur', 10 => 'Asap', 45 => 'Kabut', 60 => 'Hujan Ringan', 61 => 'Hujan Sedang',
    63 => 'Hujan Lebat', 80 => 'Hujan Lokal', 95 => 'Hujan Petir', 97 => 'Hujan Petir'
];

$renderCell = function($item) use ($kodeCuaca) {
    $waktu = strtotime($item['local_datetime']);
    $jam = date("H:i", $waktu);
    $ampm = date('H:i:s', $waktu);
    $weatherCode = (int)($item['weather_code'] ?? 0);
    $cuaca = $kodeCuaca[$weatherCode] ?? 'Tidak diketahui';
    $suhu = $item['t'] ?? 'N/A';

    $gambarCuaca = '';
    if ($weatherCode === 0) {
        $gambarCuaca = ($ampm >= '06:00:00' && $ampm < '18:00:00') ? 'cerah-am.png' : 'cerah-pm.png';
    } elseif (in_array($weatherCode, [1,2])) {
        $gambarCuaca = ($ampm >= '06:00:00' && $ampm < '18:00:00') ? 'cerahberawan-am.png' : 'cerahberawan-pm.png';
    } elseif ($weatherCode === 3) {
        $gambarCuaca = 'berawan.png';
    } elseif ($weatherCode === 4) {
        $gambarCuaca = 'berawantebal.png';
    } elseif (in_array($weatherCode, [5,10])) {
        $gambarCuaca = 'asap.png';
    } elseif ($weatherCode === 45) {
        $gambarCuaca = ($ampm >= '06:00:00' && $ampm < '18:00:00') ? 'kabut-am.png' : 'kabut-pm.png';
    } elseif ($weatherCode === 60) {
        $gambarCuaca = 'hujanringan.png';
    } elseif ($weatherCode === 61) {
        $gambarCuaca = 'hujansedang.png';
    } elseif ($weatherCode === 63) {
        $gambarCuaca = 'hujanlebat.png';
    } elseif ($weatherCode === 80) {
        $gambarCuaca = ($ampm >= '06:00:00' && $ampm < '18:00:00') ? 'hujanlokal-am.png' : 'hujanlokal-pm.png';
    } elseif (in_array($weatherCode, [95,97])) {
        $gambarCuaca = 'hujanpetir.png';
    }

    if ($gambarCuaca !== '') {
        $gambarCuaca = theme_asset("images/weather/$gambarCuaca");
    }

    return '<td width="25%" style="vertical-align: top;">
                '.$jam.'<br>
                <img src="'.$gambarCuaca.'" alt="" width="30px"><br>
                <small>'.$cuaca.'</small><br>
                '.$suhu.'&deg;C
            </td>';
};
@endphp

<div class="text-center" style="max-height: 400px; overflow-y: auto;">
    @if(empty($data) || empty($data['data']))
        Gagal menampilkan data.
    @else
    <p style="text-align: center;">
        Lokasi : {{ ucwords(setting('sebutan_desa')) . ' ' . ($data['lokasi']['desa'] ?? '') }}<br>
        Koordinat : {{ ($data['lokasi']['lat'] ?? 'N/A') . ',' . ($data['lokasi']['lon'] ?? 'N/A') }}<br>
        Diperbarui {{ date('d-m-Y H:i:s') }}
    </p>
    <table class="table table-striped table-inverse">
        <tbody>
        @php
            $cuacaPerTanggal = [];
            foreach ($data['data'][0]['cuaca'] as $listCuaca) {
                foreach ($listCuaca as $item) {
                    $tanggal = date("d-m-Y", strtotime($item['local_datetime']));
                    if(!isset($labels[$tanggal])) continue;
                    $cuacaPerTanggal[$tanggal][] = $item;
                }
            }
        @endphp
        @foreach($cuacaPerTanggal as $tanggal => $items)
            <tr>
                <td colspan="4"><i class="fa fa-clock-o"></i> {{ $labels[$tanggal] }}, {{ tgl_indo2(date("Y-m-d", strtotime($tanggal))) }}</td>
            </tr>
            @php
                $chunks = array_chunk($items, 4); // pecah per 4 item
            @endphp
            @foreach($chunks as $chunk)
                <tr>
                    @foreach($chunk as $item)
                        {!! $renderCell($item) !!}
                    @endforeach
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    @endif
    <p>Sumber : BMKG | <a href="https://www.ariandi.net" target="_blank" rel="noopener noreferrer">Tema DeNatra</a></p>
</div>