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
    
    <div class="d-flex gap-4 flex-wrap">
    <!-- Charts -->
        <div class="section-card flex-fill">
            <div class="chart-header">
                <h5>Statistik Desa</h5>
            </div>
            <div class="d-flex gap-4">
                <!-- Donut Chart -->
                <div>
                    <h6 class="text-center">Statistik Penduduk</h6>
                    <canvas id="donutChart" style="width: 75%"></canvas>
                </div>
                <!-- Bar Chart -->
                <div>
                    <h6 class="text-center">Perkembangan Desa</h6>
                    <canvas id="barChart" height="300" style="width: 75%"></canvas>
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

            <div class="d-flex gap-4 flex-wrap">
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
                // responsive: true,
                // maintainAspectRatio: false,
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
                maintainAspectRatio: true,
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