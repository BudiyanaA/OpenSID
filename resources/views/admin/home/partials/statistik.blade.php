@php
    $totalJk  = ($dash['penduduk']['laki'] ?? 0) + ($dash['penduduk']['perempuan'] ?? 0);
    $adaUmur  = collect($dash['penduduk']['umur'] ?? [])->sum(static fn ($r) => $r['laki'] + $r['perempuan']) > 0;
    $adaSurat = ($dash['layanan']['total'] ?? 0) > 0;
@endphp

<div class="sd-panel">
    <div class="sd-panel-head">
        <div>
            <h4 class="sd-panel-title"><i class="fa fa-pie-chart"></i> Statistik Desa</h4>
        </div>
        <div class="sd-seg" role="tablist" aria-label="Pilih grafik">
            @if ($aksesPenduduk)
                <button type="button" class="sd-seg-btn is-active" role="tab" aria-selected="true" data-sd-tab="sd-tab-umur">Rentang Umur</button>
            @endif
            @if ($aksesLayanan)
                <button type="button" class="sd-seg-btn {{ $aksesPenduduk ? '' : 'is-active' }}" role="tab" aria-selected="{{ $aksesPenduduk ? 'false' : 'true' }}" data-sd-tab="sd-tab-surat">Layanan Surat</button>
            @endif
        </div>
    </div>

    <div class="sd-stat-body {{ $aksesPenduduk ? '' : 'is-single' }}">
        @if ($aksesPenduduk)
            <div class="sd-gender">
                @if ($totalJk > 0)
                    <div class="sd-donut-wrap">
                        <div id="sd-chart-gender" class="sd-chart-donut"></div>
                        <div class="sd-donut-center">
                            <strong>{{ number_format($totalJk, 0, ',', '.') }}</strong>
                            <span>Penduduk</span>
                        </div>
                    </div>
                    <ul class="sd-legend">
                        <li>
                            <span class="sd-legend-dot sd-bg-blue"></span>Laki-laki
                            <em>{{ round($dash['penduduk']['laki'] / $totalJk * 100, 1) }}%</em>
                        </li>
                        <li>
                            <span class="sd-legend-dot sd-bg-cyan"></span>Perempuan
                            <em>{{ round($dash['penduduk']['perempuan'] / $totalJk * 100, 1) }}%</em>
                        </li>
                    </ul>
                @else
                    @include('admin.home.partials.kosong', ['ikon' => 'fa-users', 'judul' => 'Data penduduk belum tersedia'])
                @endif
            </div>
        @endif

        <div class="sd-stat-charts">
            @if ($aksesPenduduk)
                <div id="sd-tab-umur" class="sd-tab-pane is-active" role="tabpanel">
                    <div class="sd-chart-caption">Penduduk per rentang umur (tahun)</div>
                    @if ($adaUmur)
                        <div id="sd-chart-umur" class="sd-chart sd-chart-fit"></div>
                    @else
                        @include('admin.home.partials.kosong', ['ikon' => 'fa-bar-chart', 'judul' => 'Data rentang umur belum tersedia'])
                    @endif
                </div>
            @endif
            @if ($aksesLayanan)
                <div id="sd-tab-surat" class="sd-tab-pane {{ $aksesPenduduk ? '' : 'is-active' }}" role="tabpanel">
                    <div class="sd-chart-caption">
                        Surat layanan, 12 bulan terakhir
                        @if ($adaSurat)
                            &middot; {{ number_format($dash['layanan']['total'], 0, ',', '.') }} surat
                        @endif
                    </div>
                    @if ($adaSurat)
                        <div id="sd-chart-layanan" class="sd-chart sd-chart-fit"></div>
                    @else
                        @include('admin.home.partials.kosong', ['ikon' => 'fa-envelope-o', 'judul' => 'Belum ada surat dalam 12 bulan terakhir'])
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
