<div class="card mb-1">
    <div class="container mt-3 main-container">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0">
                    <i class="material-icons icon-sm">folder</i>
                    <a href="{{ site_url() }}arsip">{{ $judul_widget }}</a>
                </h4>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card mb-3">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs wizard-1" role="tablist">
                            @foreach (['ekologi' => 'Ekologi', 'internet' => 'Jaringan', 'status_adat' => 'Status ' . ucwords(setting('sebutan_desa'))] as $jenis => $label)
                            <li class="nav-item">
                                <a class="nav-link @if ($loop->first) active @endif" 
                                    id="{{ $jenis }}-tab" 
                                    data-toggle="tab" 
                                    href="#{{ $jenis }}" 
                                    role="tab" 
                                    aria-controls="{{ $jenis }}" 
                                    aria-selected="@if ($loop->first) true @else false @endif">
                                    <span>{{ $label }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @foreach (['ekologi' => 'profil_ekologi', 'internet' => 'profil_internet', 'status_adat' => 'profil_status'] as $jenis => $jenis_profil)
                            <div id="{{ $jenis }}" class="tab-pane fade show @if ($loop->first) active @endif" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm">
                                        <tbody>
                                            @forelse ($$jenis_profil as $profil)
                                            <tr>
                                                <th style="width: 30%; text-align: left;">{{ SebutanDesa($profil->judul) }}</th>
                                                <td style="width: 5%; text-align: center;">:</td>
                                                <td style="text-align: left;">
                                                    @php
                                                        $isImageOrFile = in_array($profil->key, ['struktur_adat', 'dokumen_regulasi_penetapan_kampung_adat']);
                                                        $filePath = LOKASI_DOKUMEN . $profil['value'];
                                                    @endphp
        
                                                    @if (!empty($profil['value']) && file_exists($filePath) && $isImageOrFile)
                                                        <a href="{{ base_url($filePath) }}" target="_blank" class="btn btn-sm btn-primary">
                                                            <i class="fa fa-eye"></i> Lihat
                                                        </a>
                                                    @else
                                                        {{ $isImageOrFile ? '-' : $profil['value'] }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3">Data tidak tersedia.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
