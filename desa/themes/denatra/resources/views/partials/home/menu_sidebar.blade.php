@if(menu_tema())
    @php
        if (!function_exists('showChildrens')) {
            function showChildrens($items) {
                $html = '<ul class="nav flex-column">';
                foreach ($items as $item) {
                    $html .= '<li class="nav-item" style="padding:0px 10px 0px;">';
                    $html .= '<a href="' . $item["link_url"] . '" class="nav-link ' . cekKondisiPink() . '-gradient-active"><i class="material-icons icon"></i>' . $item['nama'];
                    if (!empty($item['childrens'])) {
                        $html .= '<i class="material-icons icon">keyboard_arrow_down</i></a>';
                        $html .= showChildrens($item['childrens']);
                    } else {
                        $html .= '</a>';
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>';
                return $html;
            }
        }
    @endphp

    @foreach(menu_tema() as $menu)
        @php $has_dropdown = count($menu['childrens'] ?? []) > 0; @endphp
        <li class="nav-item">
            <a href="{{ $has_dropdown ? 'javascript:void(0);' : $menu['link_url'] }}" 
               class="nav-link {{ $has_dropdown ? 'dropdwown-toggle' : cekKondisiPink().'-gradient-active' }}">
                <i class="material-icons icon">list_alt</i>
                <span>{{ $menu['nama'] }}</span>
                @if($has_dropdown)
                    <i class="material-icons icon arrow">expand_more</i>
                @endif
            </a>
            @if($has_dropdown)
                <ul class="nav flex-column">
                    @foreach($menu['childrens'] as $submenu)
                        @php $sub_has_dropdown = count($submenu['childrens'] ?? []) > 0; @endphp
                        <li class="nav-item">
                            <a href="{{ $sub_has_dropdown ? 'javascript:void(0);' : $submenu['link_url'] }}" 
                               class="nav-link {{ cekKondisiPink().'-gradient-active' }}">
                                <i class="material-icons icon">keyboard_arrow_right</i>
                                <span>{{ $submenu['nama'] }}</span>
                            </a>
                            @if($sub_has_dropdown)
                                {!! showChildrens($submenu['childrens']) !!}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
@endif
