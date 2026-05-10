@php
function getRandomDoa()
{
    $base_url = "https://open-api.my.id/api/doa/";
    $response = @file_get_contents($base_url);
    $doas = json_decode($response, true);
    $total_doas = count($doas ?? []);
    if ($total_doas === 0) return [];
    $random_id = rand(1, $total_doas);
    $random_doa_url = $base_url . $random_id;
    $response = @file_get_contents($random_doa_url);
    $datadoa = json_decode($response, true);
    $datadoa['total_doas'] = $total_doas;
    $datadoa['id'] = $random_id;
    return $datadoa;
}
@endphp

<script>
    const KODE_KOTA = "{{ theme_config('kode_kota', true) }}";
    const TANGGAL = "{{ date('Y/m/d') }}";
</script>

<div class="mb-1 z-index-2">
    <div id="shalat">
        <div class="card">
            <button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#jadwal_shalat" aria-expanded="true" aria-controls="jadwal_shalat">
                <div class="card-header {{ cekKondisiPink() }}-gradient font-weight-bold" id="headshalat">
                    JADWAL SHALAT @if(theme_config('random_doa', true)) & DO'A @endif
                    <i class="material-icons icon arrow">expand_more</i>
                </div>
            </button>

            <div id="jadwal_shalat" class="collapse" aria-labelledby="headshalat" data-parent="#shalat">
                <div class="container-fluid">
                    <section id="jadwal-shalat" class="py-0 bg-white">
                        <div class="container-fluid mt-2 main-container">

                            @if(theme_config('random_doa', true))
                                @php $randomDoa = getRandomDoa(); @endphp
                                @if(!empty($randomDoa))
                                <div class="row media mb-2">
                                    <div class="media-body">
                                        <span class="font-weight-bold">
                                            <a href="https://www.ariandi.net/kode" target="_blank" rel="noopener noreferrer">
                                                <i class="fa fa-random" aria-hidden="true"></i> RANDOM DO'A - {{ $randomDoa['id'] }} dari {{ $randomDoa['total_doas'] }}
                                            </a>
                                        </span>
                                    </div>
                                </div>

                                <div class="row border-top media mb-4">
                                    <div class="media-body mt-2 ml-3 mr-3">
                                        <span class="font-weight-bold">{{ $randomDoa['judul'] }}</span><br>
                                        {{ $randomDoa['arab'] }}<br>
                                        {{ $randomDoa['latin'] }}
                                    </div>
                                    <div class="media-body mt-2 ml-3 mr-3">
                                        <span class="font-weight-bold">Terjemahan:</span><br>
                                        {{ $randomDoa['terjemah'] }}
                                    </div>
                                </div>
                                @endif
                            @endif

                            <div class="row media mb-2">
                                <div class="media-body">
                                    <span class="font-weight-bold">
                                        <a href="https://www.ariandi.net/kode" target="_blank" rel="noopener noreferrer">
                                            <i class="fa fa-building" aria-hidden="true"></i> <span data-name="kota">...</span>
                                        </a>
                                    </span>
                                </div>
                            </div>

                            <div class="row border-top mt-0">
                                <div class="card-body">
                                    <div class="font-weight-bold">{{ hr(date('Y-m-d')) }}</div>
                                    <div class="row">
                                        @php
                                            $shalat_times = ['imsak', 'terbit', 'subuh', 'dhuha', 'dzuhur', 'ashar', 'maghrib', 'isya'];
                                        @endphp
                                        @foreach($shalat_times as $time)
                                            @php
                                                $column_class = ($time === 'imsak' || $time === 'terbit') ? 'col-6' : 'col-4';
                                            @endphp
                                            <div class="{{ $column_class }} px-2 py-1">
                                                <div class="square shalat shimmer" data-name="{{ $time }}"></div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>
                </div>
            </div>

        </div>
    </div>
</div>
