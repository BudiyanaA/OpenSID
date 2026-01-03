@if ($feed['items'])
<div class="row">
    <div id="feed" class="container{{ cekFluid() ? '-fluid' : '' }} main-container mt-0">
        <div class="row">
            @foreach ($feed['items'] as $data)
                @php
                    $deskripsi = potong_teks($data['DESCRIPTION'], 150);
                @endphp
                <div class="col-12 col-md-6">
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="text-truncate w-100">
                                <a href="{{ $data['LINK'] }}" rel="noopener noreferrer" target="_blank">
                                    {{ $data['TITLE'] }}
                                </a>
                            </h5>
                            <p align="justify" class="content-color-secondary">
                                {{ $deskripsi }} ...
                            </p>
                        </div>
                        <div class="card-footer border-top no-gutters content-color-secondary small">
                            <i class="material-icons vm content-color-primary ml-2">date_range</i> 
                            {{ gmdate("d-M-Y H:i:s", $data['PUBDATE']) }}
                            <i class="material-icons vm content-color-primary">flag</i> 
                            {{ $data['CATEGORY'] }}
                            <i class="material-icons vm content-color-primary">person</i> 
                            {{ $data["DC:CREATOR"] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
