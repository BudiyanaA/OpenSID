@if (setting('covid_desa'))
<div class="row">
    <div class="col-12 mb-1 z-index-1">
        <div id="accordion2">
            <div class="card">
                <button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#collapseFour2" aria-expanded="true" aria-controls="collapseFour2">
                    <div class="card-header bg-primary font-weight-bold" id="headingFour2">
                        LIVE DATA STATUS COVID-19 
                        <i class="material-icons icon arrow">expand_more</i>
                    </div>
                </button>
                <div id="collapseFour2" class="collapse" aria-labelledby="headingFour2" data-parent="#accordion2">
                    <div class="card-body">
                        <div class="row">
                            <div id="covid-desa" class="col-12 mb-2">
                                <div class="media border-bottom">
                                    <div class="media-body">
                                        <span class="font-weight-bold" data-name="wilayah">
                                            <a href="">{{ strtoupper(setting('sebutan_desa') . ' ' . $desa['nama_desa']) }}</a>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($covid as $key => $val)
                                        @if ($key >= 7)
                                            @break
                                        @endif
                                        @if ($key >= 3)
                                            <br/>
                                        @endif
                                        <div class="col-6 col-lg-3 col-md-3 px-2 py-1">
                                            <div class="square covid odr">
                                                <span>{{ $val['nama'] }}</span>
                                                <span>{{ number_format($val['jumlah']) }}</span>
                                                <span class="small">Orang</span>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-6 col-lg-3 col-md-3 px-2 py-1">
                                        <div class="square covid odr">
                                            <span>Jumlah Terdata</span>
                                            <span>{{ number_format($val['jumlah']) }}</span>
                                            <span class="small">Orang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@include('admin.layouts.components.token')
