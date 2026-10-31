@php
    $keuangan    = $dash['keuangan'];
    $linkLaporan = can('b', 'laporan') ? ci_route('keuangan/laporan') : ci_route('keuangan_manual/laporan_manual');
@endphp

<div class="sd-panel">
    <div class="sd-panel-head">
        <div>
            <h4 class="sd-panel-title"><i class="fa fa-money"></i> Ringkasan Keuangan Desa</h4>
            <p class="sd-panel-sub">{{ $keuangan ? 'APBDes Tahun Anggaran ' . $keuangan['tahun'] : 'Anggaran Pendapatan dan Belanja Desa' }}</p>
        </div>
        <a href="{{ $linkLaporan }}" class="sd-link-more">Lihat Transparansi <i class="fa fa-arrow-right"></i></a>
    </div>

    @if ($keuangan)
        <div class="sd-fin-body">
            <div class="sd-fin-kpis">
                @foreach ([['data' => $keuangan['pendapatan'], 'nada' => 'green'], ['data' => $keuangan['belanja'], 'nada' => 'blue']] as $baris)
                    @continue(! $baris['data'])
                    <div class="sd-fin-tile">
                        <span class="sd-fin-label"><span class="sd-legend-dot sd-bg-{{ $baris['nada'] }}"></span>{{ $baris['data']['judul'] }} <em>{{ $baris['data']['persen'] }}%</em></span>
                        <strong class="sd-fin-value" title="Anggaran {{ $rupiah($baris['data']['anggaran']) }}, realisasi {{ $rupiah($baris['data']['realisasi']) }}">{{ $rupiah($baris['data']['anggaran']) }}</strong>
                    </div>
                @endforeach
                @if ($keuangan['belanja'])
                    <div class="sd-fin-tile sd-fin-highlight">
                        <span class="sd-fin-label">Realisasi Anggaran <em>{{ $keuangan['belanja']['persen'] }}%</em></span>
                        <span class="sd-progress"><span style="width: {{ min(100, $keuangan['belanja']['persen']) }}%"></span></span>
                        <span class="sd-fin-meta">{{ $keuangan['belanja']['persen'] }}% terealisasi</span>
                    </div>
                @endif
            </div>
            <div id="sd-chart-keuangan" class="sd-chart sd-chart-fin"></div>
        </div>
    @else
        @include('admin.home.partials.kosong', ['ikon' => 'fa-money', 'judul' => 'Data keuangan belum tersedia', 'teks' => 'Impor atau input data APBDes melalui menu Keuangan untuk menampilkan ringkasan di sini.'])
    @endif
</div>
