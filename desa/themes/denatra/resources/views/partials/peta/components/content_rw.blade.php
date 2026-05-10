@php
    $pengaturan = json_decode(setting('tampilkan_tombol_peta'), true);
@endphp

<div id="isi_popup_rw">
    @foreach ($rw_gis as $key_rw => $rw)
        @php
            $link = underscore($rw['dusun']) . '/' . underscore($rw['rw']);
            $data_title = " RW {$rw['rw']} {$wilayah} {$rw['dusun']}";
            $rw_heading = 'Wilayah RW ' . set_ucwords($rw['rw']) . ' ' . ucwords($this->setting->sebutan_dusun) . ' ' . set_ucwords($rw['dusun']);
        @endphp

        <div id="isi_popup_rw_{{ $key_rw }}" style="visibility: hidden;">
            <div id="content">
                <center>
                    <h5 id="firstHeading" class="firstHeading">{{ $rw_heading }}</h5>
                </center>
                <div id="bodyContent">

                    <!-- statistik penduduk -->
                    @if (in_array('Statistik Penduduk', $pengaturan))
                        <p>
                            <center>
                                <a href="#collapseStatPenduduk" 
                                   class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" 
                                   title="Statistik Penduduk" 
                                   data-toggle="collapse" 
                                   data-target="#collapseStatPenduduk" 
                                   aria-expanded="false" 
                                   aria-controls="collapseStatPenduduk">
                                    <i class="fa fa-bar-chart"></i>&nbsp;&nbsp;Statistik Penduduk&nbsp;&nbsp;
                                </a>
                            </center>
                        </p>
                        <div class="collapse box-body no-padding" id="collapseStatPenduduk">
                            <div class="card card-body">
                                @foreach ($list_ref as $key => $value)
                                    <li {!! jecho($lap, $key, 'class="active"') !!}>
                                        <a href="{{ site_url("statistik_web/chart_gis_rw/{$key}/{$link}") }}" 
                                           data-remote="false" 
                                           data-toggle="modal" 
                                           data-target="#modalSedang" 
                                           data-title="Statistik Penduduk RW {{ $data_title }}">
                                           {{ $value }}
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- statistik bantuan -->
                    @if (in_array('Statistik Bantuan', $pengaturan))
                        <p>
                            <center>
                                <a href="#collapseStatBantuan" 
                                   class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" 
                                   title="Statistik Bantuan" 
                                   data-toggle="collapse" 
                                   data-target="#collapseStatBantuan" 
                                   aria-expanded="false" 
                                   aria-controls="collapseStatBantuan">
                                    <i class="fa fa-heart"></i>&nbsp;&nbsp;Statistik Bantuan&nbsp;&nbsp;
                                </a>
                            </center>
                        </p>
                        <div class="collapse box-body no-padding" id="collapseStatBantuan">
                            <div class="card card-body">
                                @foreach ($list_bantuan as $key => $value)
                                    <li {!! jecho($lap, $key, 'class="active"') !!}>
                                        <a href="{{ site_url("statistik_web/chart_gis_rw/{$key}/{$link}") }}" 
                                           data-remote="false" 
                                           data-toggle="modal" 
                                           data-target="#modalSedang" 
                                           data-title="Statistik Bantuan RW {{ $data_title }}">
                                           {{ $value }}
                                        </a>
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- statistik kepala wilayah -->
                    @if (in_array('Kepala Wilayah', $pengaturan))
                        <p>
                            <center>
                                <a href="{{ site_url("load_aparatur_wilayah/{$rw['id_kepala']}/2") }}" 
                                   class="btn btn-social bg-navy btn-sm btn-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" 
                                   data-title="Ketua RW {{ set_ucwords($rw['rw']) }}" 
                                   data-remote="false" 
                                   data-toggle="modal" 
                                   data-target="#modalKecil">
                                    <i class="fa fa-user"></i>&nbsp;&nbsp;Ketua RW&nbsp;&nbsp;
                                </a>
                            </center>
                        </p>
                    @endif

                </div>
            </div>
        </div>
    @endforeach
</div>
