@extends('theme::layouts.full-content')
@section('content')
@include('theme::commons.constant')
<div class="card mb-4 fullscreen">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="icon-circle icon-40 bg-light-primary mr-3">
                <i class="material-icons">favorite</i>
            </div>
            <div class="media-body">
                <h6 class="my-0 content-color-primary">Produk Hukum</h6>
                <p class="small mb-0">
                    <i class="material-icons icon-sm">date_range</i> {{ ucwords(setting('sebutan_desa')) . " " . $desa['nama_desa'] }}
                </p>
            </div>
            <a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
                <i class="material-icons ">crop_free</i>
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-lg-3 col-md-3">
                        <select class="form-control input-sm" id="list_kategori" name="kategori">
                            <option selected="" value="">Semua Kategori</option>
                        </select>
                    </div>
                    <div class="col-12 col-lg-3 col-md-3">
                        <select class="form-control input-sm" id="list_tahun" name="tahun">
                            <option value="">Semua</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="margin-right: 1rem; margin-left: 1rem;">
        <div class="table-responsive">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered" id="tabelData">
                        <thead>
                            <tr role="row">
                                <th>No</th>
                                <th>Judul Produk Hukum</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('js/sweetalert2/sweetalert2.all.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('js/sweetalert2/sweetalert2.min.css') }}">
<script>
    $(document).ready(function() {
        var apiTahun = '{{ route("api.tahun-produk-hukum") }}';
        jQuery.get(apiTahun, function(data) {
            var dataTahun = data.data;
            var selectTahun = $('#list_tahun');
            dataTahun.forEach(function(item) {
                selectTahun.append('<option value="' + item + '">' + item + '</option>');
            });
        });

        var routeKategoriProdukHukum = '{{ route("api.kategori-produk-hukum") }}';
        jQuery.get(routeKategoriProdukHukum, function(data) {
            var dataKategori = data.data;
            var selectKategori = $('#list_kategori');
            dataKategori.forEach(function(item) {
                selectKategori.append('<option value="' + item.id + '">' + item.attributes.nama + '</option>');
            });
        });

        var routeProdukHukum = `{{ route('api.produk-hukum') }}`;

        var tabelData = $('#tabelData').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: true,
            ajax: {
                url: `{{ route('api.produk-hukum') }}`,
                method: 'GET',
                data: function(row) {
                    var tahun = $('#list_tahun').val();
                    var kategori = $('#list_kategori').val();
                    var params = {
                        "page[size]": row.length,
                        "page[number]": (row.start / row.length) + 1,
                        "filter[search]": row.search.value,
                        "sort": `${row.order[0]?.dir === "asc" ? "" : "-"}${row.columns[row.order[0]?.column]?.name}`
                    };

                    if (tahun) {
                        params['filter[tahun]'] = tahun;
                    }

                    if (kategori) {
                        params['filter[kategori]'] = kategori;
                    }

                    return params;
                },
                dataSrc: function(json) {
                    json.recordsTotal = json.meta.pagination.total;
                    json.recordsFiltered = json.meta.pagination.total;
                    return json.data;
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                }
            },
            columnDefs: [{
                targets: '_all',
                className: 'text-nowrap'
            }, ],
            columns: [{
                    data: null,
                    searchable: false,
                    orderable: false,
                    className: 'text-center'
                },
                {
                    data: 'nama',
                    name: 'nama',
                    render: (data, type, row) => row.attributes.nama
                },
                {
                    data: 'kategori',
                    name: 'kategori',
                    render: (data, type, row) => row.attributes.kategori
                },
                {
                    data: 'tahun',
                    name: 'tahun',
                    render: (data, type, row) => row.attributes.tahun,
                    className: 'text-center'
                },
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (data, type, row) => {
                        if (row.attributes.satuan || row.attributes.url) {
                            return `<button class="btn btn-success btn-xs lihat-dokumen"
                                    data-nama="${row.attributes.nama}"
                                    data-url="${row.attributes.url}"
                                    data-file="${row.attributes.satuan}">Lihat</button>`;
                        }
                        return '';
                    }
                }
            ],
            order: [
                [3, 'desc']
            ],
            'pageLength': 10,
            'lengthMenu': [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Semua"]
            ],
			'language': {
				'url': BASE_URL + '/assets/bootstrap/js/dataTables.indonesian.lang'
			},
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = api.page.info().start + i + 1;
                });
            }
        });

        $(document).on('change', '#list_tahun, #list_kategori', function() {
            tabelData.ajax.reload();
        });

        $(document).on('click', '.lihat-dokumen', function() {
            var nama = $(this).data('nama');
            var file = $(this).data('file') || $(this).data('url');

            nama = nama.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

            if (!file) {
                Swal.fire('Error', 'File tidak ditemukan.', 'error');

                return;
            }

            Swal.fire({
                title: '<h4 style="margin-bottom: 10px;">Lihat</h4>',
                html: `
                        <div style="display: flex; flex-direction: column; align-items: center; width: 100%; gap: 15px;">
                            <iframe src="${file}" style="width: 100%; min-height: 400px; border: 1px solid #ddd; border-radius: 5px; display: flex; align-items: center; justify-content: center;"></iframe>
                            <button class="btn btn-primary btn-sm unduh-dokumen" data-nama="${nama}" data-file="${file}"
                                style="padding: 8px 20px; font-size: 14px; border-radius: 5px; cursor: pointer;">
                                Unduh File
                            </button>
                        </div>
                    `,
                width: '60%',
                heightAuto: true,
                showCloseButton: true,
                showConfirmButton: false,
                showCancelButton: false,
                didOpen: () => {
                    $(".unduh-dokumen").on("click", function(e) {
                        e.preventDefault();
                        let pdfUrl = $(this).data("file");
                        let fileName = $(this).data("nama") || "document.pdf";
                        if (pdfUrl.includes("drive.google.com")) {
                            let fileId = '';
                            if (pdfUrl.includes('/d/')) {
                                fileId = pdfUrl.split('/d/')[1].split('/')[0];
                            } else if (pdfUrl.includes('id=')) {
                                const urlParams = new URLSearchParams(new URL(pdfUrl).search);
                                fileId = urlParams.get('id');
                            }
                            if (fileId) {
                                pdfUrl = `https://drive.google.com/uc?export=download&id=${fileId}`;
                            }
                        }

                        let link = $("<a>")
                            .attr("href", pdfUrl)
                            .attr("download", fileName)
                            .css("display", "none")
                            .appendTo("body");

                        link[0].click();
                        link.remove();
                    });
                }
            });
        });

        // TODO:: Pindahkan ke file terpisah
        function downloadFile(base64Data, fileName) {
            const mimeTypeMatch = base64Data.match(/^data:([^;]+);base64,/);
            const mimeType = mimeTypeMatch ? mimeTypeMatch[1] : 'application/octet-stream';

            const base64WithoutPrefix = base64Data.split(',')[1];

            const byteCharacters = atob(base64WithoutPrefix);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], {
                type: mimeType
            });

            const blobUrl = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = fileName;

            link.click();

            URL.revokeObjectURL(blobUrl);
        }
    });
</script>
@endpush