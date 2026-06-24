@extends('admin.layouts_new.index')

@section('title')
    <h2>
        Tentang <?= config_item('nama_aplikasi') ?>
    </h2>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Tentang <?= config_item('nama_aplikasi') ?></li>
@endsection

@section('content')
<style>
    .content {
        padding: 18px 22px !important;
        background: #f4f8fd !important;
    }

    .content-header {
        padding: 16px 22px 6px !important;
        background: #f4f8fd !important;
    }

    .content-header h2,
    h2 {
        font-size: 26px !important;
        font-weight: 900 !important;
        color: #0f2747 !important;
        margin: 0 !important;
    }

    .simdesa-dashboard-v2 {
        display: block;
        width: 100%;
    }

    .stats-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 14px !important;
        margin-bottom: 18px !important;
    }

    .stat-card {
        background: #ffffff !important;
        border-radius: 14px !important;
        padding: 16px 18px !important;
        min-height: 105px !important;
        box-shadow: 0 8px 24px rgba(15, 39, 71, 0.08) !important;
        border: 1px solid #e8eef7 !important;
        overflow: visible !important;
    }

    .stat-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        gap: 12px !important;
    }

    .stat-info h6 {
        font-size: 13px !important;
        color: #475569 !important;
        font-weight: 800 !important;
        margin-bottom: 8px !important;
    }

    .stat-number {
        font-size: 28px !important;
        line-height: 1 !important;
        color: #0f2747 !important;
        font-weight: 900 !important;
        margin-bottom: 8px !important;
    }

    .stat-change {
        font-size: 12px !important;
        color: #059669 !important;
        font-weight: 700 !important;
        text-decoration: none !important;
    }

    .stat-icon {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        border-radius: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 18px !important;
    }

    .dashboard-row {
        display: grid !important;
        grid-template-columns: 1fr 0.92fr !important;
        gap: 16px !important;
        margin-bottom: 16px !important;
        align-items: stretch !important;
    }

    .dashboard-row-2 {
        grid-template-columns: 1fr 0.48fr !important;
    }

    .section-card {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 18px 20px !important;
        box-shadow: 0 8px 24px rgba(15, 39, 71, 0.08) !important;
        border: 1px solid #e8eef7 !important;
        min-height: 245px !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .section-header,
    .chart-header {
        padding-bottom: 12px !important;
        margin-bottom: 14px !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .section-header h5,
    .chart-header h5,
    .section-card h5 {
        font-size: 18px !important;
        font-weight: 900 !important;
        color: #0f2747 !important;
        margin: 0 !important;
    }

    .chart-grid {
        display: grid !important;
        grid-template-columns: 0.9fr 1.1fr !important;
        gap: 18px !important;
        align-items: center !important;
    }

    .chart-grid > div {
        min-height: 180px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .chart-grid h6 {
        font-size: 13px !important;
        font-weight: 800 !important;
        color: #334155 !important;
        margin-bottom: 10px !important;
    }

    #donutChart {
        width: 160px !important;
        height: 160px !important;
        max-width: 160px !important;
        max-height: 160px !important;
    }

    #barChart {
        width: 230px !important;
        height: 160px !important;
        max-width: 230px !important;
        max-height: 160px !important;
    }

    .complaint-item {
        display: flex !important;
        gap: 12px !important;
        padding: 12px 0 !important;
        border-bottom: 1px solid #eef2f7 !important;
    }

    .complaint-icon {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        border-radius: 9px !important;
        background: #e0f2fe !important;
        color: #0284c7 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .complaint-content h6,
    .project-header h6 {
        font-size: 14px !important;
        font-weight: 900 !important;
        color: #0f2747 !important;
        margin-bottom: 5px !important;
    }

    .complaint-meta,
    .project-info {
        font-size: 12px !important;
        color: #64748b !important;
    }

    .project-item {
        background: #f8fafc !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 12px !important;
        padding: 14px 16px !important;
        margin-bottom: 12px !important;
    }

    .project-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
        margin-bottom: 10px !important;
    }

    .project-budget {
        background: #dcfce7 !important;
        color: #16a34a !important;
        font-weight: 900 !important;
        padding: 6px 10px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        white-space: nowrap !important;
    }

    .section-card .py-5 {
        padding-top: 38px !important;
        padding-bottom: 38px !important;
    }

    .menu-cards {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 16px !important;
        margin-top: 4px !important;
    }

    .menu-card {
        min-height: 86px !important;
        border-radius: 14px !important;
        padding: 20px 22px !important;
        color: #ffffff !important;
        text-decoration: none !important;
        background: linear-gradient(135deg, #1d4ed8, #0ea5e9) !important;
        box-shadow: 0 10px 24px rgba(15, 39, 71, 0.12) !important;
    }

    .menu-card.green {
        background: linear-gradient(135deg, #059669, #34d399) !important;
    }

    .menu-card.orange {
        background: linear-gradient(135deg, #ea580c, #f59e0b) !important;
    }

    .menu-card-icon {
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        font-size: 28px !important;
        color: #ffffff !important;
    }

    .menu-card h5 {
        font-size: 18px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        margin-bottom: 4px !important;
    }

    .menu-card p {
        font-size: 13px !important;
        color: rgba(255,255,255,0.9) !important;
        margin: 0 !important;
    }

    @media (max-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }

    @media (max-width: 991px) {
        .stats-grid,
        .dashboard-row,
        .dashboard-row-2,
        .menu-cards {
            grid-template-columns: 1fr !important;
        }

        .chart-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
    <div class="simdesa-dashboard-v2">
    @include('admin.layouts.components.notifikasi')

    @include('admin.home.saas')

    @include('admin.home.premium')

    {{-- @include('admin.home.rilis') --}}

    <!-- Stats Cards -->
    <div class="stats-grid">
        @foreach ($shortcut as $sc)
            @can("{$sc['akses']}:baca")
                <div class="stat-card" style="color: {!! $sc['warna'] !!};">
                    <div class="stat-header">
                        <div class="stat-info">
                            <h6>{{ SebutanDesa($sc['judul']) }}</h6>
                            <div class="stat-number">{{ $sc['count'] ?? '0' }}</div>
                            <a href="{{ ci_route($sc['link'] ?? '#') }}" class="stat-change">
                                Lihat Detail
                            </a>
                        </div>
                        <div class="stat-icon" style="background-color: {!! $sc['warna'] !!}; color: white;">
                            <i class="fa {!! $sc['icon'] !!}"></i>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    
    <div class="dashboard-row">
    <!-- Charts -->
        <div class="section-card flex-fill">
            <div class="chart-header">
                <h5>Statistik Desa</h5>
            </div>
            <div class="chart-grid">
                <!-- Donut Chart -->
                <div>
                    <h6 class="text-center">Statistik Penduduk</h6>
                    <canvas id="donutChart" width="150" height="150"></canvas>
                </div>
                <!-- Bar Chart -->
                <div>
                    <h6 class="text-center">Perkembangan Desa</h6>
                    <canvas id="barChart" width="190" height="150"></canvas>
                </div>
            </div>
        </div>

            <!-- Pengaduan Terbaru -->
            <div class="section-card flex-fill">
                <div class="section-header">
                    <h5>Pengaduan Terbaru</h5>
                    <!-- <a href="#" class="view-all">Lihat Semua <i class="bi bi-arrow-right"></i></a> -->
                </div>

                <?php foreach ($pengaduan as $item): ?>
                <div class="complaint-item">
                    <div class="complaint-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="complaint-content">
                        <h6><?= $item->judul ?></h6>
                        <div class="complaint-meta">
                            <span><i class="bi bi-person"></i><?= $item->nama ?></span>
                            <span><i class="bi bi-clock"></i> <?= \Carbon\Carbon::parse($item->created_at)->locale('id')->diffForHumans() ?></span>
                            <span class="status-badge <?= \App\Enums\StatusPengaduanEnum::label()[$item->status] ?>"><?= ucwords(\App\Enums\StatusPengaduanEnum::valueOf($item->status)) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            </div>

            <div class="dashboard-row dashboard-row-2">
            <!-- Proyek Pembangunan -->
            <div class="section-card flex-fill">
                <div class="section-header">
                    <h5>Proyek Pembangunan</h5>
                    <!-- <a href="#" class="view-all">Lihat Semua <i class="bi bi-arrow-right"></i></a> -->
                </div>
                
                <?php foreach ($pembangunan as $item): ?>
                    <div class="project-item">
                        <div class="project-header">
                            <h6><?= $item->judul ?></h6>
                            <div class="project-budget">Rp <?= number_format($item->anggaran, 0, ',', '.') ?></div>
                        </div>
                        <div class="project-info">
                            <i class="bi bi-geo-alt"></i> Lokasi: <?= $item->lokasi ?>
                        </div>
                        <!-- <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                        </div>
                        <div class="project-footer">
                            <span>Progress: 75%</span>
                            <span>Target: 30 Des 2024</span>
                        </div> -->
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Agenda Desa -->
            <div class="section-card flex-fill">
                <div class="section-header">
                    <h5>Agenda Desa</h5>
                    <!-- <a href="#" class="view-all">Lihat Semua <i class="bi bi-arrow-right"></i></a> -->
                </div>

                <!-- <div class="agenda-item">
                    <div class="agenda-date">
                        <div class="day">25</div>
                        <div class="month">MAR</div>
                    </div>
                    <div class="agenda-content">
                        <h6>Rapat Musrenbang</h6>
                        <div class="agenda-time">
                            <i class="bi bi-clock"></i> 09:00 - 12:00 WIB
                            <i class="bi bi-geo-alt ms-3"></i> Balai Desa
                        </div>
                    </div>
                </div>

                <div class="agenda-item">
                    <div class="agenda-date">
                        <div class="day">25</div>
                        <div class="month">MAR</div>
                    </div>
                    <div class="agenda-content">
                        <h6>Posyandu Balita</h6>
                        <div class="agenda-time">
                            <i class="bi bi-clock"></i> 08:00 - 11:00 WIB
                            <i class="bi bi-geo-alt ms-3"></i> Posyandu RT 05
                        </div>
                    </div>
                </div>

                <div class="agenda-item">
                    <div class="agenda-date">
                        <div class="day">02</div>
                        <div class="month">APR</div>
                    </div>
                    <div class="agenda-content">
                        <h6>Pelatihan UMKM</h6>
                        <div class="agenda-time">
                            <i class="bi bi-clock"></i> 07:00 - 10:00 WIB
                            <i class="bi bi-geo-alt ms-3"></i> Seluruh RT
                        </div>
                    </div>
                </div> -->

                <div class="d-flex flex-column justify-content-center align-items-center py-5 text-center">
                    <i class="bi bi-tools" style="font-size: 48px; color: #6c757d;"></i>
                    <h5 class="mt-3 mb-1">Coming Soon</h5>
                    <p class="text-muted mb-0">
                        Fitur agenda desa sedang dalam pengembangan.
                    </p>
                </div>

            </div>
            </div>

            <!-- Menu Cards -->
            <div class="menu-cards">
                <a href="<?= ci_route('/pengaduan_admin') ?>" class="menu-card d-flex align-items-start gap-3">
                    <div class="menu-card-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div>
                        <h5>Lapor Pak Desa</h5>
                        <p>Lapor Darurat</p>
                    </div>
                </a>

                <a href="<?= ci_route('/web/form/2') ?>" class="menu-card green d-flex align-items-start gap-3">
                    <div class="menu-card-icon">
                        <i class="bi bi-cpu"></i>
                    </div>
                    <div>
                        <h5>Smart Desa AI</h5>
                        <p>Asisten Cerdas</p>
                    </div>
                </a>

                <a href="<?= ci_route('/lapak_admin/produk') ?>" class="menu-card orange d-flex align-items-start gap-3">
                    <div class="menu-card-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div>
                        <h5>Marketplace Desa Nasional</h5>
                        <p>Lapak Desa</p>
                    </div>
                </a>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        // Current Date
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const today = new Date();
        document.getElementById('currentDate').textContent = 
            `${days[today.getDay()]}, ${today.getDate()} ${months[today.getMonth()]} ${today.getFullYear()}`;

        // Donut Chart
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($label_jenis_kelamin) ?>,
                datasets: [{
                    data: <?= json_encode($data_jenis_kelamin) ?>,
                    backgroundColor: [
                        '#3b82f6',
                        '#f59e0b',
                        '#10b981'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($label_pekerjaan) ?>,
                datasets: [
                    {
                        label: 'Jumlah',
                        data: <?= json_encode($data_pekerjaan) ?>,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
