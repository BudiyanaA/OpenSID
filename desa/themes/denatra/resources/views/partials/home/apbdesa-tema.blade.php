<style type="text/css">
    .progress-bar span
    {
        position: absolute;
        right: 20px;
        color: #FFFFFF;
    }
</style>

<div id="apbdesa" class="container{{ cekFluid() ? '-fluid' : '' }} mb-1 main-container">
    <div class="card-header {{ cekKondisiPink() }}-gradient font-weight-bold text-center">
        <i class="fa fa-info-circle"></i> TRANSPARANSI ANGGARAN <br><small>Sumber Data : Siskeudes</small>
    </div>
    <section class="py-0">
        <div class="card mb-0 mt-0 fullscreen has-background-img">
            <div class="container-fluid mt-0 mb-2 main-container">
                <div class="row">
                    @foreach ($data_widget as $subdata_name => $subdatas)
                        <div class="col-12 col-md-4 col-lg-4 mb-2">
                            <div class="card-header border-bottom">
                                <div class="media">
                                    <div class="icon-circle icon-40 bg-light-primary mr-3">
                                        <i class="material-icons">insert_chart</i>
                                    </div>
                                    <div class="media-body">
                                        @php
                                            $sebutan_desa = ucfirst(setting('sebutan_desa'));
                                            $laporan = $subdatas['laporan'];
                                            $replacement = ($sebutan_desa === 'Desa') ? 'Desa' : (($sebutan_desa === 'Kalurahan') ? 'Kal' : substr($sebutan_desa, 0, 1));
                                            $laporan = str_replace('Des', $replacement, $laporan);
                                        @endphp
                                        <h6 class="my-0 content-color-primary">{{ $laporan }}</h6>
                                        <p class="small mb-0">
                                            Realisasi | Anggaran
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @foreach ($subdatas as $key => $subdata)
                                @continue(!is_array($subdata))
                                @if($subdata['judul'] != null && $key != 'laporan' && ($subdata['realisasi'] != 0 || $subdata['anggaran'] != 0))
                                    <div class="progress-group mt-2">
                                        <small>
                                            {{
                                                \Illuminate\Support\Str::of($subdata['judul'])
                                                    ->title()
                                                    ->whenContains('Desa', function (\Illuminate\Support\Stringable $string) {
                                                        if (! in_array($string, ['Dana Desa'])) {
                                                            return $string->replace('Desa', setting('sebutan_desa'));
                                                        }
                                                    }, function (\Illuminate\Support\Stringable $string) {
                                                        if (! in_array($string, [
                                                            'Swadaya, Partisipasi dan Gotong Royong',
                                                            'Bagi Hasil Pajak Dan Retribusi', 
                                                            'Bantuan Keuangan Provinsi',
                                                            'Bantuan Keuangan Kabupaten/Kota',
                                                            'Penerimaan Dari Hasil Kerjasama dengan Pihak Ketiga',
                                                            'Koreksi Kesalahan Belanja Tahun-Tahun Sebelumnya',
                                                            'Bunga Bank',
                                                            'Hibah dan Sumbangan dari Pihak Ketiga',
                                                            'Lain-Lain Pendapatan Desa Yang Sah',
                                                            'Lain - Lain Pendapatan Asli Desa Yang Sah'
                                                        ])) {
                                                            return $string->append(' ' . setting('sebutan_desa'));
                                                        }
                                                    })
                                                    ->title()
                                            }}<br>
                                            <b>{{ rupiah24($subdata['realisasi'], 'Rp. ') }} | {{ rupiah24($subdata['anggaran'], 'Rp. ') }}</b>
                                        </small>
                                        <div class="progress progress-bar-striped bg-danger" align="right" style="background-color: #27b2c8">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: {{ $subdata['persen'] }}%" aria-valuenow="{{ $subdata['persen'] }}" aria-valuemin="0" aria-valuemax="100">
                                                <span>{{ $subdata['persen'] }} %</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
