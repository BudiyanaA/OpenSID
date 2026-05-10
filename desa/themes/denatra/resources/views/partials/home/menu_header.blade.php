<div class="container-fluid {{ cekKondisiPink() }}-gradient font-weight-bold text-hide-xs">
    <nav class="container{{ cekFluid() ? '-fluid' : '' }} navbar navbar-expand navbar-light">
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ site_url() }}" title="Beranda">
                        <i class="material-icons icon">home</i>
                    </a>
                </li>
                @if(menu_tema())
                    @php
                        function showChildrensh($items) {
                            $html = '';
                            foreach ($items as $item) {
                                $html .= '<ul style="padding:0px 10px 0px;">';
                                $html .= '<a href="' . $item["link_url"] . '" class="dropdown-item">' . $item['nama'];
                                if (!empty($item['childrens'])) {
                                    $html .= '<i class="material-icons icon">keyboard_arrow_down</i></a>';
                                    $html .= showChildrensh($item['childrens']);
                                } else {
                                    $html .= '</a>';
                                }
                                $html .= '</ul>';
                            }
                            return $html;
                        }
                    @endphp

                    @foreach(menu_tema() as $menu)
                        @php $has_dropdown = count($menu['childrens'] ?? []) > 0; @endphp
                        <li class="nav-item {{ $has_dropdown ? 'dropdown' : '' }}">
                            <a href="{{ $has_dropdown ? 'javascript:void(0);' : $menu['link_url'] }}" 
                               class="nav-link text-white {{ $has_dropdown ? 'dropdwown-toggle' : '' }}" 
                               id="navbarDropdownMenuLink" role="button" 
                               {{ $has_dropdown ? 'data-toggle=dropdown' : '' }} aria-haspopup="true" aria-expanded="false">
                                {{ $menu['nama'] }}
                                @if($has_dropdown)
                                    <i class="material-icons icon arrow">expand_more</i>
                                @endif
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                @if($has_dropdown)
                                    @foreach($menu['childrens'] as $submenu)
                                        @php $has_dropdown_sub = count($submenu['childrens'] ?? []) > 0; @endphp
                                        <a href="{{ $has_dropdown_sub ? 'javascript:void(0);' : $submenu['link_url'] }}" class="dropdown-item">
                                            <b>{{ $submenu['nama'] }}</b>
                                            @if($has_dropdown_sub)
                                                <i class="material-icons icon arrow">keyboard_arrow_right</i>
                                            @endif
                                        </a>
                                        <ul style="padding:0px 10px 0px;">
                                            @if($has_dropdown_sub)
                                                {!! showChildrensh($submenu['childrens']) !!}
                                            @endif
                                        </ul>
                                    @endforeach
                                @endif
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </nav>
</div>
