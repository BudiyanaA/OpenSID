<div class="sd-panel">
    <div class="sd-panel-head">
        <div>
            <h4 class="sd-panel-title"><i class="fa fa-road"></i> Pembangunan Desa</h4>
            <p class="sd-panel-sub">{{ number_format($dash['pembangunan']['total'] ?? 0, 0, ',', '.') }} kegiatan tercatat</p>
        </div>
        <a href="{{ ci_route('admin_pembangunan') }}" class="sd-link-more">Lihat Semua <i class="fa fa-arrow-right"></i></a>
    </div>

    <div class="sd-list">
        @forelse (array_slice($dash['pembangunan']['proyek'] ?? [], 0, 4) as $proyek)
            <div class="sd-project">
                <div class="sd-project-head">
                    <strong title="{{ $proyek['judul'] }}">{{ $proyek['judul'] }}</strong>
                    @if ($proyek['progres'] === 100)
                        <span class="sd-badge sd-badge-success">Selesai</span>
                    @elseif ($proyek['aktif'])
                        <span class="sd-badge sd-badge-primary">Aktif</span>
                    @else
                        <span class="sd-badge">Tidak aktif</span>
                    @endif
                </div>
                <div class="sd-project-foot">
                    <span class="sd-project-meta">
                        @if ($proyek['lokasi'])
                            <i class="fa fa-map-marker"></i> {{ $proyek['lokasi'] }}
                        @endif
                        @if ($proyek['tahun'])
                            &middot; TA {{ $proyek['tahun'] }}
                        @endif
                        @if ($proyek['anggaran'] > 0)
                            &middot; {{ $rupiah($proyek['anggaran']) }}
                        @endif
                    </span>
                    @if ($proyek['progres'] !== null)
                        @php
                            $nadaProgres = $proyek['progres'] >= 100 ? 'green' : ($proyek['progres'] >= 50 ? 'blue' : 'amber');
                        @endphp
                        <span class="sd-progress-row">
                            <span class="sd-progress"><span class="sd-bg-{{ $nadaProgres }}" style="width: {{ $proyek['progres'] }}%"></span></span>
                            <em>{{ $proyek['progres'] }}%</em>
                        </span>
                    @else
                        <span class="sd-project-note">Belum ada progres</span>
                    @endif
                </div>
            </div>
        @empty
            @include('admin.home.partials.kosong', ['ikon' => 'fa-road', 'judul' => 'Belum ada data pembangunan', 'teks' => 'Kegiatan pembangunan yang dicatat akan tampil di sini.'])
        @endforelse
    </div>
</div>
