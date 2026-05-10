@php
$jsonUrl = "https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json";
$jsonString = @file_get_contents($jsonUrl);
$data = json_decode($jsonString, true);
@endphp

<div class="card-body text-center" style="overflow: auto; height: 300px;">
    @if ($data === null || empty($data['Infogempa']['gempa']))
        Gagal menampilkan data.
    @else
        @php $gempa = $data['Infogempa']['gempa']; @endphp
        <table class="table table-inverse">
            <thead>
                <tr>
                    <th colspan="4">
                        INFO GEMPA BUMI TERBARU<br>
                        {{ date('d-m-Y \j\a\m H:i:s', strtotime($gempa['DateTime'])) }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="4" colspan="2" width="100px">
                        <a data-fancybox="gallery" href="https://static.bmkg.go.id/{{ $gempa['Shakemap'] }}">
                            <img src="https://static.bmkg.go.id/{{ $gempa['Shakemap'] }}" 
                                    alt="Shakemap" width="100%" 
                                    oncontextmenu="return false;"
                                    onerror="this.onerror=null;this.src='{{ theme_asset('images/weather/cerah-am.png') }}';">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="50px">
                        <img src="{{ theme_asset('images/loc.png') }}" alt=""/>
                    </td>
                    <td class="border-grey-soft">{{ $gempa['Lintang'] }} ; {{ $gempa['Bujur'] }}</td>
                </tr>
                <tr>
                    <td width="50px">
                        <img src="{{ theme_asset('images/mag.png') }}" alt=""/>
                    </td>
                    <td class="border-grey-soft">Magnitude {{ $gempa['Magnitude'] }}</td>
                </tr>
                <tr>
                    <td width="50px">
                        <img src="{{ theme_asset('images/dep.png') }}" alt=""/>
                    </td>
                    <td class="border-grey-soft">Kedalaman {{ $gempa['Kedalaman'] }}</td>
                </tr>
                <tr>
                    <td width="50px">
                        <img src="{{ theme_asset('images/pos.png') }}" alt=""/>
                    </td>
                    <td colspan="3">{{ $gempa['Wilayah'] }}</td>
                </tr>
                <tr>
                    <td width="50px">
                        <img src="{{ theme_asset('images/tsu.png') }}" alt=""/>
                    </td>
                    <td colspan="3">{{ $gempa['Potensi'] }}</td>
                </tr>
                <tr>
                    <td colspan="4">Dirasakan {{ $gempa['Dirasakan'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif
    <p style="text-align: center;">
        Sumber : BMKG | 
        <a href="https://www.ariandi.net" target="_blank" rel="noopener noreferrer">Tema DeNatra</a>
    </p>
</div>