<div class="card mb-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0"><i class="material-icons icon-sm">contacts</i> {{ $judul_widget }}</h4>
            </div>
        </div>
    </div>
    <div class="card-body text-center">
        @foreach ($sosmed as $data)
            @if (!empty($data['link']))
                <a href="{{ $data['link'] }}" title="{{ $data['nama'] }}" rel="noopener noreferrer" target="_blank">
                    <img src="{{ $data['icon'] }}" class="img-responsive cover" style="width:40px;height:40px;" alt="">
                </a>
            @endif
        @endforeach
    </div>
</div>
