@extends('admin.layouts.index')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/simdesa/dashboard.css') }}" />
    <style>
        .catatan-scroll {
            height: 400px;
            overflow-y: scroll;
        }

        @media (max-width: 576px) {
            .komunikasi-opendk {
                display: none !important;
            }
        }

        /* Beranda: header konten lebih ringkas (one-screen dashboard) */
        @media (min-width: 992px) {
            body.sd-admin .content-header {
                padding: 8px 16px 0;
            }

            body.sd-admin .content-header > h1 {
                font-size: 18px;
            }

            body.sd-admin .content-header > .breadcrumb {
                top: 5px;
                right: 16px;
                padding: 4px 12px;
            }

            body.sd-admin .content {
                padding: 8px 16px 12px;
            }
        }
    </style>
@endpush

@section('title')
    <h1>
        Tentang
        <?= config_item('nama_aplikasi') ?>
    </h1>
@endsection

@section('breadcrumb')
    <li class="active">Tentang
        <?= config_item('nama_aplikasi') ?>
    </li>
@endsection

@section('content')
    @include('admin.layouts.components.notifikasi')

    @include('admin.home.saas')

    @include('admin.home.premium')

    @include('admin.home.rilis')
    
    @include('admin.home.percobaan')

    @php
        // Data dashboard: hanya membaca data existing (lihat App\Services\DashboardBeranda).
        $dash = app(\App\Services\DashboardBeranda::class)->data();

        $aksesPenduduk    = can('b', 'penduduk');
        $aksesLayanan     = can('b', 'arsip-layanan');
        $aksesPengaduan   = can('b', 'pengaduan');
        $aksesPembangunan = can('b', 'pembangunan');
        $aksesKeuangan    = can('b', 'laporan') || can('b', 'laporan-manual');
        $aksesLapak       = can('b', 'lapak');

        $rupiah = static fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');

        $statusPengaduan = [
            \App\Enums\StatusPengaduanEnum::MENUNGGU_DIPROSES => ['label' => 'Baru', 'kelas' => 'primary'],
            \App\Enums\StatusPengaduanEnum::SEDANG_DIPROSES   => ['label' => 'Diproses', 'kelas' => 'warning'],
            \App\Enums\StatusPengaduanEnum::SELESAI_DIPROSES  => ['label' => 'Selesai', 'kelas' => 'success'],
        ];

        $aktivitas = collect($dash['aktivitas'] ?? [])
            ->filter(static fn ($item) => can('b', $item['akses']))
            ->take(3);

        $aksenStat = ['blue', 'cyan', 'green', 'purple'];
    @endphp

    <div class="sd-dash">
        {{-- 1. Welcome header --}}
        <div class="sd-dash-hero">
            <div class="sd-dash-hero-main">
                <h2>Selamat Datang, {{ $auth->nama }} <span aria-hidden="true">👋</span></h2>
                <p>Kelola layanan, administrasi, pembangunan, dan informasi desa dari satu tempat.</p>
            </div>
            <div class="sd-dash-hero-meta"><i class="fa fa-calendar"></i>&nbsp; {{ tgl_indo(date('Y-m-d')) }}</div>
        </div>

        {{-- 2. Statistik utama (shortcut existing) --}}
        @if (collect($shortcut)->isEmpty())
            <div class="sd-panel">
                @include('admin.home.partials.kosong', ['ikon' => 'fa-th-large', 'judul' => 'Belum ada shortcut aktif', 'teks' => 'Shortcut beranda dapat diaktifkan melalui menu pengaturan shortcut.'])
            </div>
        @else
            <div class="sd-kpis">
                @foreach ($shortcut as $sc)
                    @can("{$sc['akses']}:baca")
                        <a href="{{ ci_route($sc['link'] ?? '#') }}" class="sd-stat sd-accent-{{ $aksenStat[$loop->index % count($aksenStat)] }}" title="Lihat detail {{ SebutanDesa($sc['judul']) }}">
                            <span class="sd-stat-icon"><i class="faa {!! $sc['icon'] !!}"></i></span>
                            <span class="sd-kpi-text">
                                <span class="sd-stat-label">{{ SebutanDesa($sc['judul']) }}</span>
                                <span class="sd-stat-value">{{ $sc['count'] ?? '0' }}</span>
                            </span>
                            <span class="sd-stat-link">Lihat Detail <i class="fa fa-arrow-right"></i></span>
                        </a>
                    @endcan
                @endforeach
            </div>
        @endif

        {{-- 3. Statistik desa + 4. Pengaduan terbaru --}}
        @if ($aksesPenduduk || $aksesLayanan || $aksesPengaduan)
            <div class="sd-dash-row sd-dash-row-60">
                @if ($aksesPenduduk || $aksesLayanan)
                    @include('admin.home.partials.statistik')
                @endif
                @if ($aksesPengaduan)
                    @include('admin.home.partials.pengaduan')
                @endif
            </div>
        @endif

        {{-- 6. Pembangunan + 7. Keuangan --}}
        @if ($aksesPembangunan || $aksesKeuangan)
            <div class="sd-dash-row sd-dash-row-50">
                @if ($aksesPembangunan)
                    @include('admin.home.partials.pembangunan')
                @endif
                @if ($aksesKeuangan)
                    @include('admin.home.partials.keuangan')
                @endif
            </div>
        @endif

        {{-- 5. Aktivitas + 8. Akses cepat (feature card) --}}
        <div class="sd-dash-row sd-dash-bottom">
            <div class="sd-panel sd-activity-panel">
                <h4 class="sd-activity-title" title="Aktivitas Terbaru"><i class="fa fa-clock-o"></i> <span class="sd-activity-label">Aktivitas Terbaru</span></h4>
                @if ($aktivitas->isNotEmpty())
                    <ul class="sd-activity">
                        @foreach ($aktivitas as $item)
                            <li class="sd-tone-{{ $item['warna'] }}">
                                <span class="sd-timeline-dot"><i class="fa {{ $item['ikon'] }}"></i></span>
                                <div class="sd-timeline-body">
                                    <strong title="{{ $item['judul'] }}">{{ $item['judul'] }}</strong>
                                    <span>
                                        @if ($item['rinci'])
                                            {{ $item['rinci'] }} &middot;
                                        @endif
                                        <time datetime="{{ $item['waktu']['iso'] }}" title="{{ $item['waktu']['tanggal'] }}">{{ $item['waktu']['relatif'] }}</time>
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="sd-inline-empty"><i class="fa fa-info-circle"></i> Belum ada aktivitas surat, peristiwa penduduk, layanan mandiri, atau pengaduan.</p>
                @endif
            </div>

            <section class="sd-features" aria-labelledby="sd-akses-cepat">
                <h4 id="sd-akses-cepat" class="sd-features-title">Akses Cepat</h4>
                <div class="sd-feature-grid">
                    @if ($aksesPengaduan)
                        <a href="{{ ci_route('pengaduan_admin') }}" class="sd-feature sd-feature-lapor">
                            <span class="sd-feature-icon"><i class="fa fa-bullhorn"></i></span>
                            <span class="sd-feature-text">
                                <strong>Lapor Pak Desa</strong>
                                <span>Pengaduan &amp; aspirasi warga</span>
                            </span>
                            <i class="fa fa-bullhorn sd-feature-deco" aria-hidden="true"></i>
                            <span class="sd-feature-go" aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        </a>
                    @endif

                    {{-- Smart Desa AI: generator artikel Website Desa (fitur internal, BYOK) --}}
                    @if (can('b', 'smart-desa-ai'))
                        <a href="{{ ci_route('smart_desa_ai') }}" class="sd-feature sd-feature-ai">
                            <span class="sd-feature-icon"><i class="fa fa-magic"></i></span>
                            <span class="sd-feature-text">
                                <strong>Smart Desa AI</strong>
                                <span class="sd-feature-sub">Generator Artikel Desa</span>
                                <small class="sd-feature-desc" title="Buat artikel berita desa dengan bantuan AI.">Buat artikel berita desa dengan bantuan AI.</small>
                                <span class="sd-feature-cta">Buat Artikel <i class="fa fa-arrow-right"></i></span>
                            </span>
                            <i class="fa fa-lightbulb-o sd-feature-deco" aria-hidden="true"></i>
                        </a>
                    @endif

                    @if ($aksesLapak)
                        <a href="{{ ci_route('lapak_admin') }}" class="sd-feature sd-feature-market">
                            <span class="sd-feature-icon"><i class="fa fa-shopping-cart"></i></span>
                            <span class="sd-feature-text">
                                <strong>Marketplace Desa</strong>
                                <span>Produk &amp; UMKM Desa</span>
                            </span>
                            <i class="fa fa-shopping-bag sd-feature-deco" aria-hidden="true"></i>
                            <span class="sd-feature-go" aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        </a>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/highcharts/highcharts.js') }}"></script>
    @php
        $dataGrafik = [
            'penduduk' => $aksesPenduduk ? $dash['penduduk'] : null,
            'layanan'  => $aksesLayanan ? $dash['layanan'] : null,
            'keuangan' => $aksesKeuangan ? $dash['keuangan'] : null,
        ];
    @endphp
    <script>
        window.SD_DASHBOARD = @json($dataGrafik);
    </script>
    <script src="{{ asset('js/simdesa/dashboard-charts.js') }}"></script>
@endpush
