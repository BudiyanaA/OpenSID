@extends('admin.layouts.index')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/simdesa/smart-desa-ai.css') }}" />
@endpush

@section('title')
    <h1>Pengaturan Smart Desa AI</h1>
@endsection

@section('breadcrumb')
    <li class="active">Pengaturan Smart Desa AI</li>
@endsection

@section('content')
    @include('admin.layouts.components.notifikasi')

    @php
        $labelStatus = [
            'terhubung' => ['Terhubung', 'success', 'fa-check-circle'],
            'gagal'     => ['Gagal Terhubung', 'danger', 'fa-times-circle'],
            'belum'     => ['Belum Terhubung', 'muted', 'fa-circle-o'],
        ][$status['status']] ?? ['Belum Terhubung', 'muted', 'fa-circle-o'];
    @endphp

    <div class="sd-ai sd-scope">
        <div class="sd-ai-hero">
            <span class="sd-ai-hero-icon"><i class="fa fa-magic"></i></span>
            <div>
                <h2>Smart Desa AI</h2>
                <p>Hubungkan akun AI milik desa (BYOK). Token disimpan terenkripsi di server instalasi ini dan tidak pernah ditampilkan ulang.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 col-xs-12">
                <div class="sd-ai-card">
                    <div class="sd-ai-card-head">
                        <h3><i class="fa fa-plug"></i> Koneksi Penyedia AI</h3>
                        <span id="sd-ai-status" class="sd-ai-status sd-ai-status-{{ $labelStatus[1] }}">
                            <i class="fa {{ $labelStatus[2] }}"></i> <span>{{ $labelStatus[0] }}</span>
                        </span>
                    </div>

                    {!! form_open(ci_route('smart_desa_ai_pengaturan.update'), 'id="sd-ai-form-pengaturan" autocomplete="off"') !!}
                    <div class="sd-field">
                        <label class="sd-label" for="provider">Provider AI</label>
                        <select id="provider" name="provider" class="sd-select" @disabled(! can('u', 'smart-desa-ai-pengaturan'))>
                            @foreach ($pilihanProvider as $kode => $label)
                                <option value="{{ $kode }}" data-model-bawaan="{{ $modelBawaan[$kode] ?? '' }}" @selected($kode === $provider)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="token">API Token</label>
                        <input
                            id="token"
                            name="token"
                            type="password"
                            class="sd-input"
                            autocomplete="new-password"
                            spellcheck="false"
                            placeholder="{{ $tokenTersamar ? $tokenTersamar . '  (tersimpan — isi hanya bila ingin mengganti)' : 'Tempel API token milik desa' }}"
                        >
                        <div class="sd-help">
                            @if ($tokenTersamar)
                                <i class="fa fa-lock"></i> Token tersimpan terenkripsi. Kosongkan kolom ini untuk tetap memakai token yang ada.
                            @else
                                Buat token di konsol penyedia AI (Anthropic Console atau OpenAI Platform) menggunakan akun milik desa.
                            @endif
                        </div>
                    </div>

                    <div class="sd-field">
                        <label class="sd-label" for="model">Model</label>
                        <select id="model" name="model" class="sd-select">
                            @if ($model && ! in_array($model, $daftarModel, true))
                                <option value="{{ $model }}" selected>{{ $model }}</option>
                            @endif
                            @foreach ($daftarModel as $idModel)
                                <option value="{{ $idModel }}" @selected($idModel === $model)>{{ $idModel }}</option>
                            @endforeach
                            @if (! $model && empty($daftarModel))
                                <option value="">— jalankan Test Koneksi untuk memuat daftar model —</option>
                            @endif
                        </select>
                        <div class="sd-help">Daftar model dimuat dari akun penyedia AI saat Test Koneksi.</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="sd-field">
                                <label class="sd-label" for="batas_user_jam">Batas generate per pengguna / jam</label>
                                <input id="batas_user_jam" name="batas_user_jam" type="number" min="1" max="1000" class="sd-input" value="{{ $batasJam }}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="sd-field">
                                <label class="sd-label" for="batas_harian">Batas generate per hari (desa)</label>
                                <input id="batas_harian" name="batas_harian" type="number" min="1" max="10000" class="sd-input" value="{{ $batasHari }}">
                            </div>
                        </div>
                    </div>

                    <div id="sd-ai-uji-hasil" class="sd-alert" style="display:none"></div>

                    <div class="sd-ai-actions">
                        @if (can('u', 'smart-desa-ai-pengaturan'))
                            <button type="button" id="sd-ai-uji" class="sd-btn sd-btn-outline" @disabled(! $tokenTersamar)>
                                <i class="fa fa-refresh"></i> Test Koneksi
                            </button>
                            <button type="submit" class="sd-btn sd-btn-primary"><i class="fa fa-check"></i> Simpan Pengaturan</button>
                        @endif
                    </div>
                    {!! form_close() !!}

                    @if ($tokenTersamar)
                        @if (can('h', 'smart-desa-ai-pengaturan'))
                            {!! form_open(ci_route('smart_desa_ai_pengaturan.hapus_token'), 'class="sd-ai-hapus-token" onsubmit="return confirm(\'Hapus token API? Smart Desa AI tidak dapat dipakai sampai token baru disimpan.\')"') !!}
                            <button type="submit" class="sd-btn sd-btn-ghost sd-btn-sm sd-text-danger"><i class="fa fa-trash-o"></i> Hapus token tersimpan</button>
                            {!! form_close() !!}
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-lg-5 col-xs-12">
                <div class="sd-ai-card">
                    <div class="sd-ai-card-head">
                        <h3><i class="fa fa-bar-chart"></i> Pemakaian</h3>
                        <span class="sd-ai-muted">bulan ini</span>
                    </div>
                    <div class="sd-ai-stats">
                        <div><strong>{{ number_format($statistik['generate'], 0, ',', '.') }}</strong><span>Artikel digenerate</span></div>
                        <div><strong>{{ number_format($statistik['publish'], 0, ',', '.') }}</strong><span>Dipublikasikan</span></div>
                        <div><strong>{{ number_format($statistik['input_tokens'], 0, ',', '.') }}</strong><span>Token masuk</span></div>
                        <div><strong>{{ number_format($statistik['output_tokens'], 0, ',', '.') }}</strong><span>Token keluar</span></div>
                    </div>
                    <div class="sd-ai-meter">
                        <div class="sd-ai-meter-row">
                            <span>Hari ini (desa)</span>
                            <em>{{ $pemakaian['hari_ini'] }} / {{ $pemakaian['batas_harian'] }}</em>
                        </div>
                        <div class="sd-progress"><span style="width: {{ min(100, round($pemakaian['hari_ini'] / max(1, $pemakaian['batas_harian']) * 100)) }}%"></span></div>
                    </div>
                    @if ($statistik['gagal'] > 0)
                        <p class="sd-ai-muted"><i class="fa fa-exclamation-triangle"></i> {{ $statistik['gagal'] }} permintaan gagal bulan ini.</p>
                    @endif
                    <p class="sd-ai-muted">Biaya ditagihkan langsung oleh penyedia AI ke akun desa. Log pemakaian tidak menyimpan token maupun isi artikel.</p>
                </div>

                <div class="sd-ai-card sd-ai-card-soft">
                    <h3 class="sd-ai-card-title"><i class="fa fa-shield"></i> Keamanan</h3>
                    <ul class="sd-ai-list">
                        <li>Token terenkripsi dengan kunci unik instalasi desa ini.</li>
                        <li>Token hanya dipakai di server; tidak dikirim ke browser.</li>
                        <li>AI hanya menerima isian formulir dan nama wilayah desa — tanpa akses database.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.SD_AI_PENGATURAN = {
            urlUji: @json(ci_route('smart_desa_ai_pengaturan.uji')),
        };
    </script>
    <script src="{{ asset('js/simdesa/smart-desa-ai.js') }}"></script>
@endpush
