<div class="card mb-1">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="media-body">
                <h4 class="content-color-primary mb-0"><i class="material-icons icon-sm">work</i> {{ $judul_widget }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-body border-bottom">
                <table class="table border-bottom mb-0">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th class="text-center">Mulai</th>
                            <th class="text-center">Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jam_kerja as $value)
                        <tr>
                            <td>
                                <div class="media">
                                    <div class="media-body">
                                        <h6 class="my-0 mt-1">{{ $value->nama_hari }}</h6>
                                    </div>
                                </div>
                            </td>
                            @if ($value->status)
                                <td class="text-center">
                                    <span class="btn-sm btn-success px-2">{{ $value->jam_masuk }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="btn-sm btn-success px-2 btn-sm">{{ $value->jam_keluar }}</span>
                                </td>
                            @else
                                <td class="text-center" colspan="2">
                                    <span class="btn-sm btn-danger px-4">Libur</span>
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
