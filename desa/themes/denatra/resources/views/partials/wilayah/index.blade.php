@extends('theme::layouts.full-content')
@section('content')
@includeIf('theme::commons.constant')
@includeIf('theme::partials.wilayah.penduduk')
<div class="row">
    <div class="col-md-4 col-lg-4 text-hide-xs text-hide-sm">
        @includeIf('theme::partials.kependudukan.navigasi')
    </div>
    <div class="col-md-8 col-lg-8">
        <div class="card mb-1 fullscreen has-background-img ">
            <div class="card-header border-bottom">
                <div class="media">
                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                        <i class="material-icons">view_day</i>
                    </div>
                    <div class="media-body">
                        <h6 class="my-0 content-color-primary">
                            {{ $heading }}
                        </h6>
                        <p class="small mb-0">
                            <i class="material-icons icon-sm">local_offer</i> Demografi {{ ucwords(setting('sebutan_desa')) . ' ' . identitas('nama_desa') }}
                        </p>
                    </div>
                    <a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
                        <i class="material-icons ">crop_free</i>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-0 content-color-secondary">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tabelData">
                            <thead>
                                <tr class="{{ cekKondisiPink() }}-gradient">
                                    <th rowspan="2" class="text-center padat">No</th>
                                    <th rowspan="2" colspan="8" class="text-center">Wilayah, {{ ucwords(setting('sebutan_singkatan_kadus')) }}/Ketua</th>
                                    <th rowspan="2" class="text-center">KK</th>
                                    <th rowspan="2" class="text-center">Jiwa</th>
                                    <th colspan="2" class="text-center">Laki-Laki</th>
                                    <th colspan="2" class="text-center">Perempuan</th>
                                </tr>
                                <tr class="{{ cekKondisiPink() }}-gradient">
                                    <th class="text-center">Jiwa</th>
                                    <th class="text-center">%</th>
                                    <th class="text-center">Jiwa</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="font-weight-bold text-center mt-4 mb-4" id="noData" style="display: none;">
                    <h5>Data tidak tersedia.</h5>
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
$(document).ready(function() {
    var tabelData = $('#tabelData');
    var noData = $('#noData');
    var wilayahHTML = '';

    function ribuan(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function persen(bagian, total) {
        return total > 0 ? ((bagian / total) * 100).toFixed(2) : '0.00';
    }

    function loadWilayah() {
        var apiWilayah = '{{ route('api.wilayah.administratif') }}';

        jQuery.get(apiWilayah, function(response) {
            var wilayah = response.data;

            tabelData.find('tbody').empty();
            tabelData.find('tfoot').empty();

            if (!wilayah.length) {
                tabelData.hide();
                noData.show();
                return;
            }

            loadDusun(wilayah);
        });
    }

    function loadDusun(data) {
        let no = 1;
        let totalKK = 0;
        let totalPria = 0;
        let totalWanita = 0;

        data.forEach(function(item) {
            let totalJiwa = item.attributes.penduduk_pria_wanita_count;
            let pria = item.attributes.penduduk_pria_count;
            let wanita = item.attributes.penduduk_wanita_count;

            let row = `<tr>
                <td class="text-center">${no}</td>
                <td colspan="8">${item.attributes.sebutan_dusun + ' ' + item.attributes.dusun + item.attributes.kepala_nama}</td>
                <td class="angka text-center">${ribuan(item.attributes.keluarga_aktif_count)}</td>
                <td class="angka text-center">${ribuan(totalJiwa)}</td>
                <td class="angka text-center">${ribuan(pria)}</td>
                <td class="angka text-center">${persen(pria, totalJiwa)}%</td>
                <td class="angka text-center">${ribuan(wanita)}</td>
                <td class="angka text-center">${persen(wanita, totalJiwa)}%</td>
            </tr>`;

            wilayahHTML += row;
            totalKK += item.attributes.keluarga_aktif_count;
            totalPria += pria;
            totalWanita += wanita;
            no++;

            loadRW(item.attributes.rws);
        });

        tabelData.find('tbody').append(wilayahHTML);

        let totalJiwa = totalPria + totalWanita;
        var tfoot = `<tr style="font-weight:bold;">
            <td class="text-center" colspan="9">TOTAL</td>
            <td class="angka text-center">${ribuan(totalKK)}</td>
            <td class="angka text-center">${ribuan(totalJiwa)}</td>
            <td class="angka text-center">${ribuan(totalPria)}</td>
            <td class="angka text-center">${persen(totalPria, totalJiwa)}%</td>
            <td class="angka text-center">${ribuan(totalWanita)}</td>
            <td class="angka text-center">${persen(totalWanita, totalJiwa)}%</td>
        </tr>`;

        tabelData.find('tbody').after(tfoot);
    }

    function loadRW(data) {
        let no = 1;

        data.forEach(function(item) {
            if (item.rw !== '-') {
                let totalJiwa = item.penduduk_pria_wanita_count;
                let pria = item.penduduk_pria_count;
                let wanita = item.penduduk_wanita_count;

                let row = `<tr>
                    <td></td>
                    <td class="text-center">${no}</td>
                    <td colspan="7">${item.sebutan_rw + ' ' + item.rw + item.kepala_nama}</td>
                    <td class="angka text-center">${ribuan(item.keluarga_aktif_count)}</td>
                    <td class="angka text-center">${ribuan(totalJiwa)}</td>
                    <td class="angka text-center">${ribuan(pria)}</td>
                    <td class="angka text-center">${persen(pria, totalJiwa)}%</td>
                    <td class="angka text-center">${ribuan(wanita)}</td>
                    <td class="angka text-center">${persen(wanita, totalJiwa)}%</td>
                </tr>`;

                wilayahHTML += row;
                no++;
            }

            loadRT(item.rw, item.rts);
        });
    }

    function loadRT(rw, data) {
        let no = 1;

        data.forEach(function(item) {
            if (rw == item.rw && item.rt !== '-') {
                let totalJiwa = item.penduduk_pria_wanita_count;
                let pria = item.penduduk_pria_count;
                let wanita = item.penduduk_wanita_count;

                let row = `<tr>
                    <td></td>
                    <td></td>
                    <td class="text-center">${no}</td>
                    <td colspan="6">${item.sebutan_rt + ' ' + item.rt + item.kepala_nama}</td>
                    <td class="angka text-center">${ribuan(item.keluarga_aktif_count)}</td>
                    <td class="angka text-center">${ribuan(totalJiwa)}</td>
                    <td class="angka text-center">${ribuan(pria)}</td>
                    <td class="angka text-center">${persen(pria, totalJiwa)}%</td>
                    <td class="angka text-center">${ribuan(wanita)}</td>
                    <td class="angka text-center">${persen(wanita, totalJiwa)}%</td>
                </tr>`;

                wilayahHTML += row;
                no++;
            }
        });
    }

    loadWilayah();
});
</script>
@endpush
