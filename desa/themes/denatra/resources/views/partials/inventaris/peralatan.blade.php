@extends('theme::layouts.full-content')
@includeIf('core::admin.layouts.components.asset_numeral')
@section('content')

<div class="fullscreen has-background-img ">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mb-4 fullscreen">
                <div class="card-header border-bottom">
                    <div class="media">
                        <div class="icon-circle icon-40 bg-light-primary mr-3">
                            <i class="material-icons">account_balance</i>
                        </div>
                        <div class="media-body">
                            <h6 class="my-0 content-color-primary">Data {{ $judul }}</h6>
                            <p class="small mb-0">
                                <i class="material-icons icon-sm">date_range</i> {{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}
                            </p>
                        </div>
                        <a class="btn btn-sm btn-info" href="{{ site_url() }}inventaris">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="inventaris">
                            <thead class="bg-gray">
                                <tr>
                                    <th class="text-center" rowspan="2">No.</th>
                                    <th class="text-center" rowspan="2">Nama Barang</th>
                                    <th class="text-center" rowspan="2">Kode Barang / Nomor Registrasi</th>
                                    <th class="text-center" rowspan="2">Merk/Type</th>
                                    <th class="text-center" rowspan="2">Tahun Pembelian</th>
                                    <th class="text-center" colspan="2">Nomor</th>
                                    <th class="text-center" rowspan="2">Asal Usul</th>
                                    <th class="text-center" rowspan="2">Harga (Rp)</th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="1">Polisi</th>
                                    <th class="text-center" rowspan="1">BPKB</th>
                                </tr>
                            </thead>
                            <tbody id="inventaris-tbody">

                            </tbody>
                            <tfoot id="inventaris-tfoot">
                                <tr>
                                    <th colspan="8" class="text-right">Total:</th>
                                    <th class="total"></th>
                                </tr>
                            </tfoot>
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
    document.addEventListener("DOMContentLoaded", function(event) {
        const _url = `{{ ci_route('internal_api.inventaris-peralatan') }}`
        const _tbody = document.getElementById('inventaris-tbody')
        const _tfoot = document.getElementById('inventaris-tfoot')
        $.ajax({
            url: _url,
            type: 'GET',
            beforeSend: () => _tbody.innerHTML = `@includeIf('theme::commons.loading')`,
            success: (response) => {
                let _trString = []
                let _total = 0
                if (response.data.length) {
                    response.data.forEach((element, key) => {
                        _trString.push(`<tr>
                            <td>${key + 1}</td>
                            <td>${element.attributes.nama_barang}</td>
                            <td>${element.attributes.kode_barang}<br>${element.attributes.register}</td>
                            <td>${element.attributes.merk}</td>
                            <td>${element.attributes.tahun_pengadaan}</td>
                            <td>${element.attributes.no_polisi}</td>
                            <td>${element.attributes.no_bpkb}</td>
                            <td>${element.attributes.asal}</td>
                            <td>${element.attributes.harga_format}</td>
                        </tr>`)
                        _total += element.attributes.harga
                    });
                    _tfoot.querySelector(`th.total`).innerHTML = numeral(_total).format()
                    _tbody.innerHTML = _trString.join('')
                } else {
                    _tfoot.remove()
                    _tbody.innerHTML = ''
                }
                setTimeout(() => {
                    $('#inventaris').DataTable({
                        columnDefs: [{
                            targets: [0],
                            orderable: false
                        }],
                        order: [
                            [1, 'asc']
                        ]
                    });
                }, 1000);
            },
            dataType: 'json'
        })
    });
</script>
@endpush