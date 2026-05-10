@if ($paging->num_rows > $paging->per_page)
<div class="pagination_area mt-3">
    <p>Halaman {{ $paging->page }} dari {{ $paging->end_link }}</p>
    <nav>
        <ul class="pagination pagination-sm no-margin">
            @if ($paging->start_link)
                <li class="page-item">
                    <a class="page-link" href="{{ site_url("$paging_page/$paging->start_link" . $paging->suffix) }}" title="Halaman Pertama">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            @endif

            @foreach ($pages as $i)
                <li class="page-item {{ $paging->page == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ site_url("$paging_page/$i" . $paging->suffix) }}" title="Halaman {{ $i }}">{{ $i }}</a>
                </li>
            @endforeach

            @if ($paging->end_link)
                <li class="page-item">
                    <a class="page-link" href="{{ site_url("$paging_page/$paging->end_link" . $paging->suffix) }}" title="Halaman Terakhir">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
