<div class="nav-item">
    <a href="javascript:void(0);" class="nav-link dropdwown-toggle">
        <i class="material-icons icon">widgets</i>
        <span>Menu Kategori</span>
        <i class="material-icons icon arrow">expand_more</i>
    </a>
    <ul class="nav flex-column">
        @foreach($menu_kiri as $data)
            @php $has_dropdown = !empty($data['submenu']) && count($data['submenu']) > 0; @endphp
            <li class="nav-item">
                <a href="{{ site_url("artikel/kategori/{$data['slug']}") }}" class="nav-link {{ cekKondisiPink() }}-gradient-active">
                    <i class="material-icons icon">{{ $has_dropdown ? 'keyboard_arrow_down' : 'keyboard_arrow_right' }}</i>
                    <span>{{ $data['kategori'] }}</span>
                </a>
                @if($has_dropdown)
                    <ul class="nav flex-column">
                        @foreach($data['submenu'] as $submenu)
                            <li class="nav-item" style="padding:0px 10px 0px;">
                                <a href="{{ site_url("artikel/kategori/{$submenu['slug']}") }}" class="nav-link {{ cekKondisiPink() }}-gradient-active">
                                    <i class="material-icons icon"></i>
                                    <span>{{ $submenu['kategori'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>