<script>
    const KODE_KOTA = "{{ theme_config('kode_kota', true) }}";
    const TANGGAL = "{{ date('Y/m/d') }}";
</script>

<div class="mb-1 z-index-2">
    <div id="shalat">
        <div class="card">
            <button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#jadwal_shalat" aria-expanded="true" aria-controls="jadwal_shalat">
                <div class="card-header {{ cekKondisiPink() }}-gradient font-weight-bold" id="headshalat">
                    <i class="material-icons icon arrow">brightness_4</i>
                    <span data-name="kota">KAB. KOTAWARINGIN BARAT</span>
                    <i class="material-icons icon arrow">expand_more</i>
                </div>
            </button>

            <div id="jadwal_shalat" class="collapse" aria-labelledby="headshalat" data-parent="#shalat">
                <div class="container-fluid">
                    <section id="jadwal-shalat" class="py-0 bg-white">
                        <div class="container-fluid mt-2 main-container">
                            <div class="media mb-0">
                                <div class="media-body">
                                    <span class="font-weight-bold">
                                        <a href="https://www.ariandi.net/kode" rel="noopener noreferrer" target="_blank">
                                            {{ hr(date('Y-m-d')) }}
                                        </a>
                                    </span>
                                </div>
                            </div>

                            <div class="row mt-0">
                                <div class="card-body">
                                    <div class="row">
                                        @foreach (['imsak','subuh','terbit','dhuha','dzuhur','ashar','maghrib','isya'] as $shalat)
                                            <div class="col-6 col-lg-3 col-md-3 px-2 py-1 {{ in_array($shalat,['terbit','dhuha','dzuhur','ashar','maghrib','isya']) ? 'col-4' : '' }}">
                                                <div class="square shalat shimmer" data-name="{{ $shalat }}"></div>
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
