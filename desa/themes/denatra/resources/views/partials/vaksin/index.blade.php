@extends('theme::layouts.full-content')
@section('content')
@include('theme::commons.constant')
<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="card mb-2 fullscreen has-background-img">
            <div class="card-header border-bottom">
                <div class="media">
                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                        <i class="material-icons">assessment</i>
                    </div>
                    <div class="media-body">
                        <h6 class="my-0 content-color-primary">{{ $heading }} Covid-19</h6>
                        <p class="small mb-0">
                            <i class="material-icons icon-sm">local_offer</i> {{ ucwords(setting('sebutan_desa').' '.$desa['nama_desa']) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-0 content-color-secondary">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="tabel-data">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">No</th>
                                    <th rowspan="2" class="align-middle text-center">Nama</th>
                                    <th rowspan="2" class="align-middle text-center">Umur</th>
                                    <th colspan="2" class="text-center">Alamat</th>
                                    <th colspan="3" class="text-center">Vaksin</th>
                                </tr>
                                <tr>
                                    <th class="text-center">RT</th>
                                    <th class="text-center">RW</th>
                                    <th class="text-center">I</th>
                                    <th class="text-center">II</th>
                                    <th class="text-center">III</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($main as $data)
                                    @if($data->vaksin_1 == 1 || $data->vaksin_2 == 1 || $data->vaksin_3 == 1)
                                        <tr>
                                            <td class="angka text-center"></td>
                                            <td>{{ $data->nama }}</td>
                                            <td class="text-center">{{ $data->umur }}</td>
                                            <td class="text-center">{{ $data->rt }}</td>
                                            <td class="text-center">{{ $data->rw }}</td>
                                            <td class="text-center">
                                                @if($data->vaksin_1 == 1 && $data->tunda == 0)
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data->vaksin_2 == 1 && $data->tunda == 0)
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data->vaksin_3 == 1 && $data->tunda == 0)
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function(){
        var tabelData = $('#tabel-data').DataTable({
            'processing': false,
            'order': [[1, 'asc']],
            'pageLength': 10,
            'lengthMenu': [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
            'columnDefs': [
                {
                    'searchable': false,
                    'targets': [0, 4, 5, 6]
                },
                {
                    'orderable': false,
                    'targets': [0, 4, 5, 6]
                }
            ],
            'language': {
                'url': BASE_URL + '/assets/bootstrap/js/dataTables.indonesian.lang'
            },
        });

        tabelData.on( 'order.dt search.dt', function () {
            tabelData.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i + 1;
            } );
        } ).draw();
    });
    </script>
@endpush