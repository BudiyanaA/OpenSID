<div class="card mb-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary">
                    <i class="material-icons icon-sm">assessment</i> {{ $judul_widget }}
                </h4>
            </div>
        </div>
    </div>
    <div class="card-body text-center">
        <table class="table table-striped table-inverse">
            @php
                $dataRows = [
                    ['Hari ini', ribuan($statistik_pengunjung['hari_ini'])],
                    ['Kemarin', ribuan($statistik_pengunjung['kemarin'])],
                    ['Total Pengunjung', ribuan($statistik_pengunjung['total'])],
                    ['Sistem Operasi', $statistik_pengunjung['os']],
                    ['IP Address', $statistik_pengunjung['ip_address']],
                    ['Browser', $statistik_pengunjung['browser']]
                ];
            @endphp

            @foreach ($dataRows as $row)
                <tr>
                    <td style="text-align:left">{{ $row[0] }}</td>
                    <td>:</td>
                    <td style="text-align:right" class="{{ strpos($row[0], 'IP') !== false ? 'text-break' : '' }}">
                        {{ $row[1] }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
