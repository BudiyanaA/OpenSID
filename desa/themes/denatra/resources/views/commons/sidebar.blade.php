<!-- sidebar left -->
<div class="sidebar sidebar-left">
    <div class="container has-background-img">
        <figure class="background-img {{ cekKondisiPink() }}-gradient">
            <img src="{{ gambar_desa($desa['logo']) }}" alt="">
        </figure>
        <div class="media w-100 my-3">
            <figure class="avatar avatar-40 rounded-circle align-self-start">
                <label class="checkbox-user-check">
                    <input type="checkbox">
                    <i class="material-icons">check</i>
                </label>
                <img src="{{ gambar_desa($desa['logo']) }}" alt="">
            </figure>
            <div class="media-body mx-70">
                <h5 class="time-title mb-0 text-white">{{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}</h5>
                <p class="mb-0 text-truncate text-white">{{ ucwords(setting('sebutan_kecamatan_singkat')) . ' ' . $desa['nama_kecamatan'] }}</p>
            </div>
        </div>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ site_url('/') }}" class="nav-link"><i class="material-icons icon">home</i>
                <span>Beranda</span></a>
        </li>
        @includeIf('theme::partials.menu_kategori_sidebar')
        @includeIf('theme::partials.home.menu_sidebar')
    </ul>
</div>
<div class="backdrop"></div>
<!-- sidebar left ends -->

<!-- sidebar right -->
<div class="sidebar sidebar-right">
    <button type="button" class="btn close-sidebar"><i class="material-icons">settings</i></button>
    <div class="row mx-0 pt-2">
        <div class="col-12">
            <p class="sidebar-color-primary page-sub-title-small"><span class="icon-circle mr-2"><i
                        class="material-icons">settings</i></span>Info Sistem</p>
            <p class="sidebar-color-secondary"><small>{{ 'Tema ' . THEME_NAME . ' ' . THEME_VERSION }}</small></p>
            <div class="row">
                <ul class="list-group border-top border-bottom list-group-flush w-100">
                    <li class="list-group-item">
                        <span class="vm">Sistem Operasi</span>
                        <p class="sidebar-color-primary mt-2 mb-0">{{ $statistik_pengunjung['ip_address'] }}</p>
                    </li>
                    <li class="list-group-item">
                        <span class="vm">Browser</span>
                        <p class="sidebar-color-primary mt-2 mb-0">{{ $statistik_pengunjung['browser'] }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- sidebar right ends -->

<!-- setting sidebar -->
<div class="settings-sidebar close-settings-sidebar-backdrop">
    <button type="button" class="btn close-setting-sidebar {{ cekKondisiPink() }}-gradient"><i
            class="material-icons">keyboard_arrow_left</i></button>
    <ul class="nav nav-tabs row no-gutters {{ cekKondisiPink() }}-gradient" role="tablist">
        <li class="nav-item text-center col">
            <a class="nav-link active" id="tabhome3settings-tab" data-toggle="tab" href="#tabhome3settings" role="tab"
                aria-controls="tabhome3settings" aria-selected="false">
                <h5 class="content-color-primary mb-0"><i class="material-icons">notifications</i></h5>
                <p class="content-color-secondary mb-0 small">Updates</p>
            </a>
        </li>
        <li class="nav-item text-center col">
            <a class="nav-link" id="tabhome1settings-tab" data-toggle="tab" href="#tabhome1settings" role="tab"
                aria-controls="tabhome1settings" aria-selected="true">
                <h5 class="content-color-primary mb-0"><i class="material-icons">assignment_ind</i></h5>
                <p class="content-color-secondary mb-0 small">Aparatur</p>
            </a>
        </li>
        <li class="nav-item text-center col">
            <a class="nav-link" id="tabhome2settings-tab" data-toggle="tab" href="#tabhome2settings" role="tab"
                aria-controls="tabhome2settings" aria-selected="false">
                <h5 class="content-color-primary mb-0"><i class="material-icons">settings</i></h5>
                <p class="content-color-secondary mb-0 small">Settings</p>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Add your tab content here -->
        <div class="tab-pane fade mb-5" id="tabhome1settings" role="tabpanel" aria-labelledby="tabhome1settings-tab">
            <ul class="list-group list-group-flush mb-5" id="chat-list">
                @foreach($aparatur_desa['daftar_perangkat'] as $data)
                    <li class="list-group-item new">
                        <div class="media">
                            <figure class="avatar avatar-60 mr-3">
                                <img data-src="{{ $data['foto'] ? $data['foto'] : asset('assets/files/user_pict/kuser.png') }}" loading="lazy" class="lazyload" alt="" />
                            </figure>
                            <div class="media-body">
                                <h6 class="my-0">
                                    {{ $data['nama'] }}
                                </h6>
                                <p>
                                    {{ $data['jabatan'] }} <span class="float-right page-sub-title-small"></span>
                                </p>
                                @if(setting('tampilkan_kehadiran'))
                                    @if ($data['status_kehadiran'] == 'hadir')
                                        <span class="badge badge-success">
                                            {{ cekKehadiran('kehadiran', 'Ada di Kantor') }}
                                        </span>
                                    @endif
                                    @if ($data['tanggal'] == date('Y-m-d') && $data['status_kehadiran'] != 'hadir')
                                        <span class="badge badge-warning">
                                            {{ ucwords($data['status_kehadiran']) }}
                                        </span>
                                    @endif
                                    @if ($data['kehadiran'] == 1 && $data['tanggal'] != date('Y-m-d'))
                                        <span class="badge badge-danger">
                                            {{ cekKehadiran('ketidakhadiran', 'Tidak Ada di Kantor') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="tab-pane fade" id="tabhome2settings" role="tabpanel" aria-labelledby="tabhome2settings-tab">
            <div class="row mx-0">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible mt-2 p-2" role="alert" id="settingalert">
                        <strong>Berhasil!</strong><br>Perubahan telah diterapkan.
                        <button type="button" class="close btn-sm" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                </div>
            </div>
            <div class="row mx-0 mt-2">
                <div class="col-12">
                    <p class="page-sub-title-small"><span class="icon-circle mr-2"><i
                                class="material-icons">settings</i></span> Pengaturan Layar</p>
                </div>
            </div>
            <ul class="list-group list-group-flush w-100">
                <li class="list-group-item">
                    <label class="d-inline-block mr-2">Sembunyikan Latar</label>
                    <input type="checkbox" id="hidebackdrop" class="switch switch-sm">
                    <label for="hidebackdrop" class="{{ cekKondisiPink() }}-gradient float-right"></label>
                </li>
            </ul>
        </div>
        <div class="tab-pane active mb-5" id="tabhome3settings" role="tabpanel" aria-labelledby="tabhome3settings-tab">
            <div class="row mx-0 mt-0 bg-light">
                <div class="col-12">
                    <div class="card my-3">
                        <div class="card-body">
                            <div class="media">
                                <div class="icon-circle icon-50 bg-light-primary mr-3">
                                    @if(setting('tte'))
                                        <img src="{{ theme_asset('assets/images/bsre.png?v') }}" class="img-responsive"
                                            style="width: 50px;" alt="" />
                                    @else
                                        <i class="material-icons">
                                            {{ $statistik_pengunjung['hari_ini'] > $statistik_pengunjung['kemarin'] ? 'trending_up' : 'trending_down' }}
                                        </i>
                                    @endif
                                </div>
                                <div class="media-body">
                                    @php
                                        $persen = ($statistik_pengunjung['kemarin'] != 0) ? number_format(($statistik_pengunjung['hari_ini'] / $statistik_pengunjung['kemarin']) * 100, 2) : "N/A";
                                    @endphp
                                    <h4 class="content-color-primary mb-0">
                                        {{ ribuan($statistik_pengunjung['hari_ini']) }} visitors
                                    </h4>
                                    <p class="content-color-secondary mb-3">Pengunjung Hari Ini</p>
                                </div>
                            </div>
                            <div class="progress progress-bar-striped bg-danger progress-bar-animated" align="right"
                                style="background-color: #27b2c8">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: {{ $persen }}%" aria-valuenow="{{ $persen }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <span>
                                        {{ $persen }} %
                                    </span>
                                </div>
                            </div>
                            <div class="content-color-secondary text-right">
                                <small>Kemarin
                                    {{ ribuan($statistik_pengunjung['kemarin']) }} visitors
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mx-0 mt-1 bg-light">
                <div class="col-12">
                    <p class="page-sub-title-small">
                        <span class="icon-circle"><i class="material-icons">router</i></span>
                        SIMDESA {{ AmbilVersi() }}
                    </p>
                </div>
            </div>
            <a href="{{ site_url('siteman') }}" class="content-color-secondary" rel="noopener noreferrer" target="_blank">
                <ul class="list-group list-group-flush w-100 log-information bubble-sheet mt-2">
                    <li class="list-group-item">
                        <div class="avatar avatar-15 border-primary"></div>
                        <p class="content-color-primary">
                            <i class="material-icons">lock</i> Login Aplikasi<br>
                            <small class="content-color-secondary">Halaman Administrator</small>
                        </p>
                    </li>
                </ul>
            </a>
            @if(setting('tampilkan_kehadiran'))
                <a href="{{ site_url('kehadiran') }}" rel="noopener noreferrer" target="_blank">
                    <ul class="list-group list-group-flush w-100 log-information bubble-sheet mt-2 not-found">
                        <li class="list-group-item">
                            <div class="avatar avatar-15 border-danger"></div>
                            <p class="content-color-primary">
                                <i class="material-icons">fingerprint</i> Rekam Kehadiran<br>
                                <small class="content-color-secondary text-break">
                                    IP Address : {{ $statistik_pengunjung['ip_address'] }}
                                </small>
                            </p>
                        </li>
                    </ul>
                </a>
            @endif
            @if(setting('layanan_mandiri'))
                <a href="{{ site_url('layanan-mandiri/masuk') }}" rel="noopener noreferrer" target="_blank">
                    <ul class="list-group list-group-flush w-100 log-information bubble-sheet mt-2 not-found">
                        <li class="list-group-item">
                            <div class="avatar avatar-15 border-success"></div>
                            <p class="content-color-primary">
                                <i class="material-icons">print</i> Layanan Mandiri<br>
                                <small class="content-color-secondary">Permohonan Surat, Cetak KK, dll</small>
                            </p>
                        </li>
                    </ul>
                </a>
            @endif
            <a href="https://www.google.com/maps/dir//{{ $desa['lat'] }},{{ $desa['lng'] }}" rel="noopener noreferrer" target="_blank">
                <ul class="list-group list-group-flush w-100 log-information bubble-sheet mt-2">
                    <li class="list-group-item">
                        <div class="avatar avatar-15 border-warning"></div>
                        <p class="content-color-primary">
                            <i class="material-icons">room</i> Lokasi Kantor<br>
                            <small class="content-color-secondary">
                                {{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}
                            </small>
                        </p>
                    </li>
                </ul>
            </a>
        </div>
    </div>
</div>
<div class="settings-sidebar-backdrop {{ cekKondisiPink() }}-gradient"></div>
