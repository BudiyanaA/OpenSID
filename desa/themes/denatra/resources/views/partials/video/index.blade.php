@php
    $vid = [];
    $links = theme_config("link_youtube_video");
    if ($links) {
        $link_array = preg_split('/[\s,#]+/', $links, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($link_array as $link_youtube) {
            $youtube_id = extract_youtube_id(trim($link_youtube));
            if ($youtube_id) {
                $vid[] = [
                    'youtube' => $youtube_id,
                    'judul' => '',        // Bisa diisi jika ada judul di config
                    'deskripsi' => '',    // Bisa diisi jika ada deskripsi
                    'id' => uniqid()      // ID unik untuk modal
                ];
            }
        }
        shuffle($vid);
    }
@endphp

@if(theme_config('link_youtube', false))
<style>
.leaflet-popup-content {
    height: auto;
    width: 225px;
    overflow-y: scroll;
}
</style>
<div class="mt-0 mb-1 z-index-2">
    <div id="accordion4">
        <div class="card mb-1">
            <button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#galeri_video" aria-expanded="true" aria-controls="galeri_video">
                <div class="card-header {{ cekKondisiPink() }}-gradient font-weight-bold" id="heading_video4">
                    <i class="fa fa-video-camera"></i> GALERI VIDEO
                    <i class="material-icons icon arrow">expand_more</i>
                </div>
            </button>
            <div id="galeri_video" class="collapse" aria-labelledby="heading_video4" data-parent="#accordion4">
                <div class="container-fluid">
                    <div class="row">
                        @foreach($vid as $video)
                            <div class="col-12 col-lg-4 col-md-4 order-2 order-md-1">
                                <div class="card mb-2 mt-0">
                                    <div class="card-body py-0">
                                        <div class="row border-bottom">
                                            <div class="col-12 p-3 text-center">
                                                <iframe class="mw-100" height="210"
                                                    src="https://www.youtube.com/embed/{{ $video['youtube'] }}"
                                                    frameborder="0" loading="lazy"></iframe>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <div class="card no-shadow h-100">
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <div class="btn-group">
                                                            <a class="btn btn-sm btn-success"
                                                               href="https://youtu.be/{{ $video['youtube'] }}"
                                                               rel="noopener noreferrer" target="_blank">
                                                               Buka Video
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($vid as $video)
        <div class="modal fade" id="descGaleri{{ $video['id'] }}" tabindex="-1" role="dialog" aria-labelledby="descModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="descModalLabel">Video</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5><i class="fa fa-video-camera"></i>&nbsp;{{ $video['judul'] }}</h5>
                        {!! $video['deskripsi'] !!}<br><br>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group">
                            <a class="btn btn-sm btn-success" href="https://youtu.be/{{ $video['youtube'] }}" rel="noopener noreferrer" target="_blank">Buka Video</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
