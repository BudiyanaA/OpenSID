@php
    $daftar_statistik = daftar_statistik();
    $slug_aktif = str_replace('_', '-', $slug_aktif);
    $s_links = [
        [
            'target' => 'statistikPenduduk',
            'label' => 'Statistik Penduduk',
            'icon' => 'fa-user iconpenduduk',
            'submenu' => $daftar_statistik['penduduk']
        ],
        [
            'target' => 'statistikKeluarga',
            'label' => 'Statistik Keluarga',
            'icon' => 'fa-user iconkeluarga',
            'submenu' => $daftar_statistik['keluarga']
        ],
        [
            'target' => 'statistikBantuan',
            'label' => 'Statistik Bantuan',
            'icon' => 'fa-user iconbantuan',
            'submenu' => $daftar_statistik['bantuan']
        ],
        [
            'target' => 'statistikLainnya',
            'label' => 'Statistik Lainnya',
            'icon' => 'fa-user iconlainnya',
            'submenu' => $daftar_statistik['lainnya']
        ]
    ];
@endphp

<div id="accordion">
    @foreach($s_links as $statistik)
        @php
            $is_active = in_array($slug_aktif, array_column($statistik['submenu'], 'slug'));
        @endphp
        <div class="card mb-1">
            <button class="btn btn-link text-white font-weight-bold p-0" data-toggle="collapse" data-target="#collapse{{ $statistik['target'] }}" aria-expanded="{{ $is_active ? 'true' : 'false' }}" aria-controls="collapse{{ $statistik['target'] }}">
                <div class="card-header bg-primary text-white" style="text-align:left" id="heading{{ $statistik['target'] }}">
                    <i class="fa {{ $statistik['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $statistik['label'] }}</span>
                    <i class="material-icons icon arrow">expand_more</i>
                </div>
            </button>
            <div id="collapse{{ $statistik['target'] }}" class="collapse {{ $is_active ? 'show' : '' }}" aria-labelledby="heading{{ $statistik['target'] }}">
                <ul class="nav flex-column">
                    @foreach($statistik['submenu'] as $submenu)
                        @php
                            $stat_slug = in_array($statistik['target'], ['statistikBantuan', 'statistikLainnya']) 
                                ? str_replace('first/', '', $submenu['url']) 
                                : 'statistik/' . $submenu['key'];
                        @endphp
                        @if(App\Models\Menu::where('link', $stat_slug)->where('enabled', 1)->exists())
                            <li class="nav-item">
                                <a href="{{ site_url($submenu['url']) }}" class="nav-link {{ $submenu['slug'] == $slug_aktif ? theme_config('color', 'pink') . '-gradient' : '' }}">
                                    <i class="fa fa-check" aria-hidden="true"></i> {{ $submenu['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>
