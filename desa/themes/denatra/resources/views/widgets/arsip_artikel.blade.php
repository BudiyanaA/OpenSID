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
                            @foreach (['Terbaru' => 'tabterkini', 'Populer' => 'tabpopuler', 'Acak' => 'tabacak'] as $label => $jenis)
                                <li class="nav-item">
                                    <a class="nav-link{{ ($jenis == 'tabterkini') ? ' active' : '' }}" 
                                       id="{{ $jenis }}-tab" 
                                       data-toggle="tab" 
                                       href="#{{ $jenis }}" 
                                       role="tab" 
                                       aria-controls="{{ $jenis }}" 
                                       aria-selected="{{ ($jenis == 'tabterkini') ? 'true' : 'false' }}">
                                        <span>{{ $label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            @php
                                $arsipMapping = [
                                    'tabterkini' => $arsip_terkini ?? [],
                                    'tabpopuler' => $arsip_populer ?? [],
                                    'tabacak' => $arsip_acak ?? []
                                ];
                            @endphp

                            @foreach ($arsipMapping as $jenis => $arsipList)
                                <div class="tab-pane fade show{{ ($jenis == 'tabterkini') ? ' active' : '' }}" 
                                     id="{{ $jenis }}" 
                                     role="tabpanel" 
                                     aria-labelledby="{{ $jenis }}-tab">
                                    @foreach ($arsipList as $arsip)
                                        <a href="{{ site_url('artikel/' . buat_slug($arsip)) }}">
                                            <ul class="list-group list-group-flush w-100 log-information bubble-sheet mt-2">
                                                <li class="list-group-item">
                                                    <div class="avatar avatar-15 border-primary"></div>
                                                    <p class="content-color-primary">
                                                        {!! $arsip['judul'] !!}<br>
                                                        <small class="content-color-secondary">
                                                            <i class="material-icons icon-sm">date_range</i> {{ tgl_indo($arsip['tgl_upload']) }}
                                                            <i class="material-icons icon-sm">favorite</i> {{ hit($arsip['hit']) }}
                                                        </small>
                                                    </p>
                                                </li>
                                            </ul>
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
