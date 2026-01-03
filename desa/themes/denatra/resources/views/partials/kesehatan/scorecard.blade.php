<div class="has-background-img mb-2">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card mb-0 fullscreen">
                <div class="table-responsive">
                    <table id="table-datas" class="table table-bordered table-striped dataTable table-hover">
                        <thead class="bg-gray color-palette">
                            <tr>
                                <th colspan="9" style="background-color:#efefef;">
                                    TABEL 1. JUMLAH SASARAN 1.000 HPK (IBU HAMIL DAN ANAK 0-23 BULAN)
                                </th>
                            </tr>
                            <tr>
                                <th width="15%" rowspan="2" colspan="3" class="text-center" style="vertical-align: middle;">
                                    Sasaran
                                </th>
                                <th width="45%" rowspan="2" colspan="2" class="text-center" style="vertical-align: middle;">
                                    JML TOTAL RUMAH TANGGA 1.000 HPK
                                </th>
                                <th width="20%" colspan="2" class="text-center" style="vertical-align: middle;">IBU HAMIL</th>
                                <th width="20%" colspan="2" class="text-center" style="vertical-align: middle;">ANAK 0 – 23 BULAN</th>
                            </tr>
                            <tr>
                                <th width="10%" class="text-center" style="vertical-align: middle;">TOTAL</th>
                                <th width="10%" class="text-center" style="vertical-align: middle;">KEK/RESTI</th>
                                <th width="10%" class="text-center" style="vertical-align: middle;">TOTAL</th>
                                <th width="10%" class="text-center" style="vertical-align: middle;">GIZI KURANG/ GIZI BURUK/STUNTING</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-center" style="vertical-align: middle;">Jumlah</th>
                                <td colspan="2" class="text-center" style="vertical-align: middle;">{{ $JTRT }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $ibu_hamil['dataFilter'] == null ? 0 : count($ibu_hamil['dataFilter']) }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $jumlahKekRisti }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $bulanan_anak['dataFilter'] == null ? 0 : count($bulanan_anak['dataFilter']) }}</td>
                                <td class="text-center" style="vertical-align: middle;">{{ $jumlahGiziBukanNormal }}</td>
                            </tr>

                            <tr>
                                <th colspan="9" style="background-color:#efefef;">
                                    TABEL 2. HASIL PENGUKURAN TIKAR PERTUMBUHAN (DETEKSI DINI STUNTING)
                                </th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-center" style="vertical-align: middle;">Sasaran</th>
                                <th colspan="1" width="22%" class="text-center" style="vertical-align: middle;">JUMLAH TOTAL ANAK USIA 0 – 23 BULAN</th>
                                <th colspan="1" width="23%" class="text-center" style="vertical-align: middle;">HIJAU (NORMAL)</th>
                                <th colspan="2" class="text-center" style="vertical-align: middle;">Kuning (Resiko Stunting)</th>
                                <th colspan="2" class="text-center" style="vertical-align: middle;">Merah Terindikasi Stunting</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-center" style="vertical-align: middle;">Jumlah</th>
                                <td colspan="1" class="text-center" style="vertical-align: middle;">{{ $bulanan_anak['dataFilter'] == null ? 0 : count($bulanan_anak['dataFilter']) }}</td>
                                <td colspan="1" class="text-center" style="vertical-align: middle;">{{ $tikar['H'] }}</td>
                                <td colspan="2" class="text-center" style="vertical-align: middle;">{{ $tikar['K'] }}</td>
                                <td colspan="2" class="text-center" style="vertical-align: middle;">{{ $tikar['M'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="9" style="background-color:#efefef;">
                                    TABEL 3. KELENGKAPAN KONVERGENSI PAKET LAYANAN PENCEGAHAN STUNTING BAGI 1.000 HPK
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-center" style="vertical-align: middle;">Sasaran</th>
                                <th colspan="1" class="text-center" style="vertical-align: middle;">No</th>
                                <th colspan="3" class="text-center" style="vertical-align: middle;">Indikator</th>
                                <th colspan="2" class="text-center" style="vertical-align: middle;">Jumlah</th>
                                <th colspan="1" class="text-center" style="vertical-align: middle;">%</th>
                            </tr>
                            @if($ibu_hamil['capaianKonvergensi'])
                                @foreach($ibu_hamil['capaianKonvergensi'] as $key => $val)
                                    <tr>
                                        <th colspan="1" class="text-center">{{ $loop->iteration }}</th>
                                        <td colspan="3" style="vertical-align: middle;">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                        <td colspan="2" class="text-center">{{ $val['Y'] ?? 0 }}</td>
                                        <td colspan="1" class="text-center">{{ $val['persen'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if($bulanan_anak['capaianKonvergensi'])
                                @foreach($bulanan_anak['capaianKonvergensi'] as $key => $val)
                                    <tr>
                                        <th colspan="1" class="text-center">{{ $loop->iteration }}</th>
                                        <td colspan="3" style="vertical-align: middle;">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                        <td colspan="2" class="text-center">{{ $val['Y'] ?? 0 }}</td>
                                        <td colspan="1" class="text-center">{{ $val['persen'] ?? 0 }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @php
                                $JLD_IbuHamil = $ibu_hamil['tingkatKonvergensiDesa']['jumlah_diterima'] ?? 0;
                                $JLD_Anak = $bulanan_anak['tingkatKonvergensiDesa']['jumlah_diterima'] ?? 0;
                                $JYSD_IbuHamil = $ibu_hamil['tingkatKonvergensiDesa']['jumlah_seharusnya'] ?? 0;
                                $JYSD_Anak = $bulanan_anak['tingkatKonvergensiDesa']['jumlah_seharusnya'] ?? 0;
                                $PERSEN_IbuHamil = $ibu_hamil['tingkatKonvergensiDesa']['persen'] ?? 0;
                                $PERSEN_Anak = $bulanan_anak['tingkatKonvergensiDesa']['persen'] ?? 0;

                                $JLD_TOTAL = $JLD_IbuHamil + $JLD_Anak;
                                $JYSD_TOTAL = $JYSD_IbuHamil + $JYSD_Anak;
                                $KONV_TOTAL = $JYSD_TOTAL != 0 ? number_format(($JLD_TOTAL / $JYSD_TOTAL) * 100, 2) : '0.00';
                            @endphp

                            <tr>
                                <th colspan="1" class="text-center">1</th>
                                <td colspan="3">Ibu Hamil</td>
                                <td colspan="1" class="text-center">{{ $JLD_IbuHamil }}</td>
                                <td colspan="2" class="text-center">{{ $JYSD_IbuHamil }}</td>
                                <td colspan="2" class="text-center">{{ $PERSEN_IbuHamil }}</td>
                            </tr>
                            <tr>
                                <th colspan="1" class="text-center">2</th>
                                <td colspan="3">Anak 0 - 23 Bulan</td>
                                <td colspan="1" class="text-center">{{ $JLD_Anak }}</td>
                                <td colspan="2" class="text-center">{{ $JYSD_Anak }}</td>
                                <td colspan="2" class="text-center">{{ $PERSEN_Anak }}</td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-center">TOTAL TINGKAT KONVERGENSI DESA</th>
                                <td colspan="1" class="text-center">{{ $JLD_TOTAL }}</td>
                                <td colspan="2" class="text-center">{{ $JYSD_TOTAL }}</td>
                                <td colspan="2" class="text-center">{{ $KONV_TOTAL }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
