@includeIf('theme::partials.home.covid-data')
@includeIf('theme::partials.video.index')
<style>
  .background-img-kades {
    width: 100%;
    position: relative;
    left: 0;
    top: 0;
    height: 100%;
    overflow: hidden;
    z-index: 0;
  }
</style>

<div class="row">
    <div class="container-fluid mt-0 main-container z-index-1">
        <div class="row no-gutters box-shadow-large mb-1 bg-white rounded has-background-img mt-0">
            <div class="col-12 col-md-6 col-lg-7 has-background-img min-height-300">
                <div class="background-img background-img-kades {{ cekKondisiPink() }}-gradient">
                    <div class="align-self-center text-center mt-4">
                        <div class="logo-img-loader mb-2">
                            @if(!empty($aparatur_desa['daftar_perangkat']))
                                @php $kepala_desa = $aparatur_desa['daftar_perangkat'][0]; @endphp
                                <img class="yall_lazy opacity-100"
                                     src="{{ $kepala_desa['foto'] ? $kepala_desa['foto'] : base_url('assets/files/user_pict/kuser.png') }}"
                                     alt="{{ $kepala_desa['nama'] ?? 'Perangkat Desa' }}">
                            @endif
                        </div>
                        @if(!empty($aparatur_desa['daftar_perangkat']))
                            @php $kepala_desa = $aparatur_desa['daftar_perangkat'][0]; @endphp
                            <div style="font-size: 1.5rem; font-weight: 300; color: #fff; text-transform: uppercase; margin-bottom: 0.5rem;">
                                {{ $kepala_desa['nama'] }}
                            </div>
                            <div style="font-size: 1rem; font-weight: 300; color: #fff; text-transform: uppercase; margin-bottom: 0.7rem;">
                                SAMBUTAN {{ ucwords($kepala_desa['jabatan']. ' ' .$desa['nama_desa']) }}
                            </div>
                        @endif
                        <p class="mt-3 text-white" style="padding: 0 1.5rem; text-align: center;">{!! theme_config('sambutan', true) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-5 card mb-0 fullscreen">
                @if (!isset($_SESSION['mandiri']) || $_SESSION['mandiri'] != 1)
                    <div class="container mt-2 main-container">
                        <div class="media">
                            <div class="media-body">
                                <h4 class="content-color-primary mb-0">Layanan Mandiri</h4>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="tab-pane fade show active" id="tablogin" role="tabpanel" aria-labelledby="tablogin-tab">
                                            <div class="text-center mb-4">
                                                @php $photo_found = false; @endphp
                                                @foreach ($widgetAktif as $data)
                                                    @if (strtoupper(strip_tags($data['judul'])) == strtoupper('logo'))
                                                        @php
                                                            $photo_found = true;
                                                            break;
                                                        @endphp
                                                    @endif
                                                @endforeach
                                                @if ($photo_found)
                                                    @foreach ($widgetAktif as $data)
                                                        @if (strtoupper(strip_tags($data['judul'])) == strtoupper('logo'))
                                                            <img src="{{ to_base64(LOKASI_GAMBAR_WIDGET . $data['foto']) }}" alt="">
                                                        @endif
                                                    @endforeach
                                                @elseif(theme_config('logo_header') == TRUE)
                                                    <img src="{{ base_url(theme_config('logo_header')) }}" height="100px" alt="" {{ theme_config('rotate_logo') ? "class='rotating-image'" : "" }} />
                                                @else
                                                    <img src="{{ gambar_desa(identitas('logo')) }}" alt="" {{ theme_config('rotate_logo') ? "class='rotating-image'" : "" }} />
                                                @endif
                                                <p class="mt-2">Warga dapat membuat Permohonan Surat Keterangan melalui Website {{ ucwords(setting('sebutan_desa')) }}.<br>
                                                Silakan hubungi operator {{ ucwords(setting('sebutan_desa')) }} untuk mendapatkan kode PIN anda.</p>
                                            </div>
                                            <div class="form-group">
                                                <a href="{{ site_url('layanan-mandiri/masuk') }}" target="_blank" id="but" class="btn btn-primary btn-block">
                                                    <span style="color: white;">Login Layanan Mandiri</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-header border-bottom">
                        <div class="media">
                            <div class="media-body">
                                <h4 class="content-color-primary mb-0">Menu Layanan</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <a href="{{ site_url('layanan-mandiri/profil') }}"><button type="button" class="btn btn-primary btn-block mb-2">PROFIL</button></a>
                        <a href="{{ site_url('layanan-mandiri/pesan-masuk') }}"><button type="button" class="btn btn-primary btn-block mb-2">KOTAK PESAN</button></a>
                        <a href="{{ site_url('layanan-mandiri/surat/buat') }}"><button type="button" class="btn btn-primary btn-block mb-2">PERMOHONAN SURAT</button></a>
                        <a href="{{ site_url('layanan-mandiri/masuk') }}" class="btn btn-danger btn-block mb-2" rel="noopener noreferrer" target="_top">MASUK HALAMAN</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 mb-1">
        <section id="section-penduduk" class="rounded {{ cekKondisiPink() }}-gradient p-4">
            <div class="container-fluid overflow-hidden">
                <div class="row justify-content-between">
                    <div class="col-12 text-center mb-2 text-light align-items-center">
                        <div>
                            <h4 class="my-title-mini text-center">DATA PENDUDUK</h4>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <div class="row">
                            @php $i = 0; @endphp
                            @foreach ($stat_widget as $data)
                                @if ($data['nama'] == "BELUM MENGISI" || $data['nama'] == "JUMLAH") @continue @endif
                                <div class="col-4">
                                    @php
                                        $iconClass = 'fa-user'; // default
                                        if ($data['nama'] == 'LAKI-LAKI') $iconClass = 'fa-male';
                                        elseif ($data['nama'] == 'PEREMPUAN') $iconClass = 'fa-female';
                                        elseif ($data['nama'] == 'TOTAL') $iconClass = 'fa-users';
                                    @endphp
                                    <i class="fa {{ $iconClass }} fa-2x"></i>
                                    <h3 class="font-weight-bold mt-1 text-dark-me">{{ number_format($data['jumlah'], 0, '', '.') }}</h3>
                                    <p class="text-gray text-hide-xs">{{ $data['nama'] }}</p>
                                </div>
                                @php $i++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-12 col-lg-4 col-md-4">
        @includeIf('theme::widgets.komentar')
    </div>
    <div class="col-12 col-lg-4 col-md-4">
        @includeIf('theme::widgets.peta_lokasi_kantor')
    </div>
    <div class="col-12 col-lg-4 col-md-4">
        @includeIf('theme::widgets.peta_wilayah_desa')
    </div>
</div>
