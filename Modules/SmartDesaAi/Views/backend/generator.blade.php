@include('admin.layouts.components.asset_tinymce')
@extends('admin.layouts.index')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/simdesa/smart-desa-ai.css') }}" />
@endpush

@section('title')
    <h1>Smart Desa AI</h1>
@endsection

@section('breadcrumb')
    <li><a href="{{ site_url('web') }}">Artikel</a></li>
    <li class="active">Smart Desa AI</li>
@endsection

@section('content')
    @include('admin.layouts.components.notifikasi')

    <div class="sd-ai sd-scope">
        <div class="sd-ai-hero">
            <span class="sd-ai-hero-icon"><i class="fa fa-magic"></i></span>
            <div class="sd-ai-hero-text">
                <h2>Smart Desa AI <span class="sd-ai-hero-sub">Generator Artikel Desa</span></h2>
                <p>Tulis draf artikel Website Desa dari informasi kegiatan. AI hanya memakai informasi yang Anda berikan — periksa kembali sebelum dipublikasikan.</p>
            </div>
            <div class="sd-ai-chips">
                @foreach ($konteksDesa as $sebutan => $nama)
                    @if ($loop->first)
                        <span class="sd-ai-chip"><i class="fa fa-map-marker"></i> {{ $sebutan }} {{ $nama }}</span>
                    @endif
                @endforeach
                @if ($siap)
                    <span class="sd-ai-chip"><i class="fa fa-microchip"></i> {{ $providerLabel }} · {{ $model }}</span>
                @endif
                <span class="sd-ai-chip" id="sd-ai-kuota" title="Pemakaian hari ini untuk seluruh desa">
                    <i class="fa fa-tachometer"></i> <span>{{ $pemakaian['hari_ini'] }}/{{ $pemakaian['batas_harian'] }}</span> hari ini
                </span>
            </div>
        </div>

        @if (! $siap)
            <div class="sd-alert sd-alert-warning">
                <p>
                    <strong>Smart Desa AI belum diatur.</strong>
                    @if ($bolehPengaturan)
                        Isi token API dan model di <a href="{{ ci_route('smart_desa_ai_pengaturan') }}">Pengaturan → Smart Desa AI</a>, lalu jalankan Test Koneksi.
                    @else
                        Minta administrator mengisi token API desa di Pengaturan → Smart Desa AI.
                    @endif
                </p>
            </div>
        @elseif (! $terhubung)
            <div class="sd-alert sd-alert-info">
                <p>Koneksi belum diuji. @if ($bolehPengaturan)<a href="{{ ci_route('smart_desa_ai_pengaturan') }}">Jalankan Test Koneksi</a> untuk memastikan token dan model dapat dipakai.@endif</p>
            </div>
        @endif

        <div class="row sd-ai-layout">
            {{-- Formulir --}}
            <div class="col-lg-5 col-xs-12">
                <form id="sd-ai-form" class="sd-ai-card" autocomplete="off" onsubmit="return false">
                    <div class="sd-ai-card-head">
                        <h3><i class="fa fa-pencil-square-o"></i> Informasi Artikel</h3>
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="jenis">Jenis Artikel</label>
                        <select id="jenis" name="jenis" class="sd-select" required>
                            @foreach ($jenisArtikel as $kode => $label)
                                <option value="{{ $kode }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="topik">Topik / Judul <span class="sd-text-danger">*</span></label>
                        <input id="topik" name="topik" class="sd-input" required maxlength="{{ $batasInput['topik'] ?? 200 }}" placeholder="Contoh: Musyawarah Desa Penetapan RKP Desa">
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="informasi">Informasi kegiatan <span class="sd-text-danger">*</span></label>
                        <textarea id="informasi" name="informasi" class="sd-textarea" rows="7" required maxlength="{{ $batasInput['informasi'] ?? 6000 }}" placeholder="Tuliskan fakta kegiatan: apa yang dilakukan, siapa yang terlibat, hasil/keputusan, dan hal penting lainnya. AI tidak akan menambahkan fakta di luar informasi ini."></textarea>
                        <div class="sd-help">Semakin lengkap faktanya, semakin baik artikelnya. Nama, angka, dan tanggal hanya dipakai bila Anda tuliskan.</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="sd-field">
                                <label class="sd-label" for="tanggal">Tanggal</label>
                                <input id="tanggal" name="tanggal" class="sd-input" maxlength="{{ $batasInput['tanggal'] ?? 100 }}" placeholder="Contoh: Senin, 22 September 2026">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="sd-field">
                                <label class="sd-label" for="lokasi">Lokasi</label>
                                <input id="lokasi" name="lokasi" class="sd-input" maxlength="{{ $batasInput['lokasi'] ?? 200 }}" placeholder="Contoh: Aula Kantor Desa">
                            </div>
                        </div>
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="narasumber">Narasumber</label>
                        <input id="narasumber" name="narasumber" class="sd-input" maxlength="{{ $batasInput['narasumber'] ?? 300 }}" placeholder="Nama dan jabatan, bila ada">
                    </div>

                    <div class="sd-field">
                        <span class="sd-label">Gaya Artikel</span>
                        <div class="sd-ai-radios">
                            @foreach ($gayaArtikel as $kode => $label)
                                <label class="sd-ai-radio">
                                    <input type="radio" name="gaya" value="{{ $kode }}" @checked($loop->first)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div id="sd-ai-form-pesan" class="sd-alert sd-alert-danger" style="display:none"></div>

                    <button type="button" id="sd-ai-generate" class="sd-btn sd-btn-primary sd-btn-lg sd-btn-block sd-ai-generate" @disabled(! $siap)>
                        <i class="fa fa-magic"></i> <span>Generate Artikel</span>
                    </button>
                    <p class="sd-ai-muted sd-ai-kuota-user">
                        Pemakaian Anda: <span id="sd-ai-kuota-user">{{ $pemakaian['user_jam'] }}/{{ $pemakaian['batas_user_jam'] }}</span> dalam 1 jam terakhir.
                    </p>
                </form>
            </div>

            {{-- Hasil --}}
            <div class="col-lg-7 col-xs-12">
                <div class="sd-ai-card sd-ai-result">
                    <div id="sd-ai-kosong" class="sd-state">
                        <span class="sd-state-icon"><i class="fa fa-file-text-o"></i></span>
                        <p class="sd-state-title">Hasil artikel akan tampil di sini</p>
                        <p class="sd-state-text">Isi informasi di sebelah kiri lalu klik <strong>Generate Artikel</strong>. Anda dapat mengedit hasilnya sebelum menyimpan.</p>
                    </div>

                    <div id="sd-ai-memuat" class="sd-state" style="display:none">
                        <span class="sd-spinner sd-spinner-lg"></span>
                        <p class="sd-state-title">Menyusun artikel…</p>
                        <p class="sd-state-text">Biasanya membutuhkan 10–60 detik.</p>
                    </div>

                    <div id="sd-ai-hasil" style="display:none">
                        <div class="sd-ai-card-head">
                            <h3><i class="fa fa-file-text"></i> Hasil Artikel</h3>
                            <span id="sd-ai-status-artikel" class="sd-badge">Belum disimpan</span>
                        </div>

                        <div class="sd-field">
                            <label class="sd-label" for="hasil-judul">Judul</label>
                            <input id="hasil-judul" class="sd-input sd-ai-judul" maxlength="200">
                        </div>

                        <div class="sd-field">
                            <div class="sd-ai-label-row">
                                <label class="sd-label" for="hasil-isi">Isi Artikel</label>
                                <button type="button" id="sd-ai-edit" class="sd-btn sd-btn-ghost sd-btn-sm"><i class="fa fa-pencil"></i> <span>Edit</span></button>
                            </div>
                            <div id="sd-ai-pratinjau" class="sd-ai-preview"></div>
                            <div id="sd-ai-editor-wrap" style="display:none">
                                <textarea id="hasil-isi"></textarea>
                            </div>
                        </div>

                        <div class="sd-field">
                            <span class="sd-label">Ringkasan</span>
                            <p id="hasil-ringkasan" class="sd-ai-ringkasan"></p>
                        </div>

                        <div class="sd-ai-extras">
                            @foreach (['seo' => ['SEO Description', 'fa-search'], 'caption' => ['Caption Media Sosial', 'fa-share-alt'], 'hashtag' => ['Hashtag', 'fa-hashtag']] as $kunci => [$label, $ikon])
                                <div class="sd-ai-extra">
                                    <div class="sd-ai-extra-head">
                                        <span><i class="fa {{ $ikon }}"></i> {{ $label }}</span>
                                        <button type="button" class="sd-btn sd-btn-secondary sd-btn-sm" data-salin="#hasil-{{ $kunci }}"><i class="fa fa-copy"></i> Salin</button>
                                    </div>
                                    <p id="hasil-{{ $kunci }}"></p>
                                </div>
                            @endforeach
                        </div>

                        <div class="sd-field">
                            <label class="sd-label" for="hasil-kategori">Kategori Artikel</label>
                            <select id="hasil-kategori" class="sd-select">
                                <option value="-1">Tanpa kategori</option>
                                @foreach ($kategori as $idKategori => $namaKategori)
                                    <option value="{{ $idKategori }}">{{ $namaKategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="sd-ai-simpan-pesan" class="sd-alert" style="display:none"></div>

                        <div class="sd-ai-actions">
                            <button type="button" id="sd-ai-ulang" class="sd-btn sd-btn-outline"><i class="fa fa-refresh"></i> Generate Ulang</button>
                            <span class="sd-ai-actions-spacer"></span>
                            <button type="button" id="sd-ai-draft" class="sd-btn sd-btn-secondary"><i class="fa fa-save"></i> Simpan Draft</button>
                            @if ($bolehPublish)
                                <button type="button" id="sd-ai-publish" class="sd-btn sd-btn-primary"><i class="fa fa-globe"></i> Publish</button>
                            @else
                                <button type="button" class="sd-btn sd-btn-primary" disabled title="Akun Anda hanya dapat menyimpan draft"><i class="fa fa-globe"></i> Publish</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.SD_AI = {
            urlGenerate: @json(ci_route('smart_desa_ai.generate')),
            urlSimpan: @json(ci_route('smart_desa_ai.simpan')),
        };
    </script>
    <script src="{{ asset('js/simdesa/smart-desa-ai.js') }}"></script>
@endpush
