@extends('theme::layouts.full-content')

@section('content')
@php
    $sebutan_desa = setting('sebutan_desa') ?? 'Desa';
@endphp
<div class="fullscreen has-background-img z-index-1">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-1 fullscreen">
                <div class="card-header border-bottom">
                    <div class="media">
                        <div class="icon-circle icon-40 bg-light-primary mr-3">
                            <i class="material-icons">map</i>
                        </div>
                        <div class="media-body mt-1">
                            <h6 class="my-0 content-color-primary">
                                SDGs {{ ucwords($sebutan_desa) }} {{ $desa['nama_desa'] ?? '' }} <span id="average"></span>
                            </h6>
                            <p class="small mb-0">
                                <i class="material-icons icon-sm">date_range</i> Kode Desa {{ $desa['kode_desa'] }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="sdgsContainer">
                    <div class="panel-group" role="tablist" aria-multiselectable="true">
                        <div class="row text-center" id="sdgsData">
                            <!-- Data akan diisi lewat JS -->
                        </div>
                        <div class="text-left mt-2">
                            <a class="btn btn-sm btn-pink pink-gradient align-items-center" href="https://sig.bps.go.id/bridging-kode" target="_blank">Sumber data : sid.kemendesa.go.id</a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-2" id="errorContainer" style="display:none;">
                    <img src="{{ theme_asset('assets/img/404.svg') }}" alt="" class="mw-100 mt-2 mb-2">
                    <h4 class="text-black">Kode Wilayah Kerja Statistik - BPS belum sesuai.</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal area -->
<div id="modalsContainer"></div>
@endsection

@push('script')
<script>
    $(function () {
        $.get("{{ route('api.sdgs') }}", function (response) {
            if (response.error_msg) {
                $('#errorContainer').show();
                $('#sdgsContainer').hide();
                return;
            }

            const result = response.data[0].attributes;
            const average = result.average;
            const data = result.data;
            const originalPath = "{{ theme_asset('images/sdgs') }}/";
            const path = originalPath.split('&')[0] + '/';


            $('#average').text(average);

            data.forEach(item => {
                const image = `${path}${item.image}`;

                $('#sdgsData').append(`
                    <div class="col-6 col-lg-2 col-md-2">
                        <div class="counter-box text-center mt-2 mb-2">
                            <a class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#descModal${item.goals}">
                                <img src="${image}" height="80" alt="${item.image}" />
                                <div class="bold"><h3>${item.score}</h3></div>
                            </a>
                        </div>
                    </div>
                `);

                $('#modalsContainer').append(`
                    <div class="modal fade" id="descModal${item.goals}" tabindex="-1" role="dialog" aria-labelledby="descModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Goals - ${average}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <td width="3%" style="text-align:center">Goals</td>
                                                    <td style="text-align:center">Indikator</td>
                                                    <td width="20%" style="text-align:center">Nilai</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="text-align:center">${item.goals}</td>
                                                    <td style="text-align:center">
                                                        <img src="${image}" height="150" alt="${item.image}" />
                                                    </td>
                                                    <td style="text-align:center">${item.score}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });
        });
    });
</script>
@endpush
