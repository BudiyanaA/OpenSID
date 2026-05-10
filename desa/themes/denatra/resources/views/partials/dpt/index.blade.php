@extends('theme::layouts.full-content')
@section('content')
@includeIf('theme::commons.constant')
@includeIf('core::admin.layouts.components.asset_numeral')

<div class="row">
    <div class="col-md-4 col-lg-4 text-hide-xs text-hide-sm">
        @includeIf('theme::partials.kependudukan.navigasi')
    </div>
    <div class="col-md-8 col-lg-8">
        <div class="card mb-2 fullscreen has-background-img ">
            <div class="card-header border-bottom">
                <div class="media">
                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                        <i class="material-icons">view_day</i>
                    </div>
                    <div class="media-body">
                        <h6 class="my-0 content-color-primary">{{ $heading }}</h6>
                        <p class="small mb-0">
                            <i class="material-icons icon-sm">local_offer</i> Tanggal Pemilihan {{ $tanggal_pemilihan }}
                        </p>
                    </div>
                    <a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
                        <i class="material-icons ">crop_free</i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tData" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ ucwords(setting('sebutan_dusun')) }}</th>
                                <th>RW</th>
                                <th>Jiwa</th>
                                <th>Laki-laki</th>
                                <th>Perempuan</th>
                            </tr>
                        </thead>
                        <tbody id="dpt-tbody">
                        </tbody>
                        <tfoot id="dpt-tfoot">
                            <tr style="font-weight:bold">
                                <td colspan="3" class="text-left">TOTAL</td>
                                <td class="total text-right"></td>
                                <td class="total_lk text-right"></td>
                                <td class="total_pr text-right"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div id="noData" style="display:none">
                        Belum ada data
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-4 text-hide-lg">
        @includeIf('theme::partials.kependudukan.navigasi')
    </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(event) {
    const _url = `{{ ci_route('internal_api.dpt') }}?tgl_pemilihan={{ $tanggal_pemilihan }}`
    const _tbody = document.getElementById('dpt-tbody')
    const _tfoot = document.getElementById('dpt-tfoot')
    var tData = $('#tData');
    var noData = $('#noData');
    
    jQuery.ajax({
        url: _url,
        type: 'GET',
        success: (response) => {
            let _trString = []
            let _total = {
                'laki': 0,
                'perempuan': 0
            }
            if (response.data.length) {
                const groupedData = groupingData(response.data)
                groupedData.forEach((element, key) => {
                    _trString.push(`<tr>
                        <td class="text-center">${key + 1}</td>
                        <td>${element.dusun}</td>
                        <td class="text-center">${element.rw}</td>
                        <td class="text-right">${element.totalLaki + element.totalPerempuan}</td>
                        <td class="text-right">${element.totalLaki}</td>
                        <td class="text-right">${element.totalPerempuan}</td>
                    </tr>`)
                    _total['laki'] += element.totalLaki
                    _total['perempuan'] += element.totalPerempuan
                });
                _tfoot.querySelector(`td.total`).innerHTML = numeral(_total['laki'] + _total['perempuan']).format('0,0')
                _tfoot.querySelector(`td.total_lk`).innerHTML = numeral(_total['laki']).format('0,0')
                _tfoot.querySelector(`td.total_pr`).innerHTML = numeral(_total['perempuan']).format('0,0')
                _tbody.innerHTML = _trString.join('')
            } else {
                _tfoot.remove()
                tData.hide()
                noData.show()
            }
        },
        dataType: 'json'
    })

    // Mengelompokkan data
    const groupingData = function(inputData) {
        let groupedData = []
        inputData.forEach(item => {
            const dusun = item.attributes.dusun;
            const rw = item.attributes.rw;
            const sex = item.attributes.sex;
            const total = item.attributes.total;

            // Membuat key unik berdasarkan dusun dan rw
            const key = `${dusun}-${rw}`;

            // Jika key belum ada, inisialisasi
            if (!groupedData[key]) {
                groupedData[key] = {
                    dusun: dusun,
                    rw: rw,
                    totalLaki: 0,
                    totalPerempuan: 0
                };
            }

            // Menjumlahkan total berdasarkan sex
            if (sex === 1) {
                groupedData[key].totalLaki += total;
            } else if (sex === 2) {
                groupedData[key].totalPerempuan += total;
            }
        });

        // Mengubah objek menjadi array untuk hasil akhir
        return Object.values(groupedData);
    }

});
</script>
@endpush
