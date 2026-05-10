@extends('theme::layouts.full-content')
@section('content')

<div class="card mb-2 fullscreen has-background-img ">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="icon-circle icon-40 bg-light-primary mr-3">
                <i class="material-icons">view_day</i>
            </div>
            <div class="media-body">
                <h6 class="my-0 content-color-primary" id="judul"></h6>
                <p class="small mb-0">
                    <i class="material-icons icon-sm">local_offer</i> Sasaran Terdata : <span id="sasaran"></span>
                </p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-0">
            <div class="table-responsive">
                <h5>Data Suplemen</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td width="10%">Keterangan</td>
                            <td width="1%">:</td>
                            <td id="keterangan"></td>
                        </tr>
                    </thead>
                </table>
            </div>
            <h5 id="judul-anggota">Daftar Terdata</h5>
            <div class="table-responsive">
                <table class="table table-striped" id="tabelData">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Alamat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var apiSuplemen = `{{ route('api.suplemen') }}`;
        var params = {
            "filter[slug]": `{{ $slug }}`
        }

        $.get(apiSuplemen, params, function (response) {
            suplemen = response.data[0];

            if (!suplemen) {
                Swal.fire('Error', 'Data tidak ditemukan.', 'error');
                return;
            }

            $('#judul').text(suplemen.attributes.nama);
            $('#judul-anggota').text('Daftar '+suplemen.attributes.nama);
            $('#nama').text(suplemen.attributes.nama);
            $('#sasaran').text(suplemen.attributes.nama_sasaran);
            $('#keterangan').text(suplemen.attributes.keterangan);

            loadAnggota(suplemen.id);
        });

        function loadAnggota(id) {
            var routeSuplemenAnggota = `{{ route('api.suplemen') }}` + '/' + id;

            var tabelData = $('#tabelData').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: true,
                ajax: {
                    url: routeSuplemenAnggota,
                    method: 'GET',
                    data: row => ({
                        "page[size]": row.length,
                        "page[number]": (row.start / row.length) + 1,
                        "filter[search]": row.search.value,
                        "sort": `${row.order[0]?.dir === "asc" ? "" : "-"}${row.columns[row.order[0]?.column]?.name}`
                    }),
                    dataSrc: json => {
                        json.recordsTotal = json.meta.pagination.total;
                        json.recordsFiltered = json.meta.pagination.total;
                        return json.data;
                    },
                    error: function (xhr) {
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
                        data: "attributes.terdata_nama",
                        name: 'tweb_penduduk.nama',
                    },
                    {
                        data: "attributes.sex",
                        name: 'tweb_penduduk.sex',
                    },
                    {
                        data: "attributes.rt",
                        name: 'tweb_penduduk.rt',
                    },
                    {
                        data: "attributes.rw",
                        name: 'tweb_penduduk.rw',
                    },
                    {
                        data: "attributes.alamat",
                        name: 'tweb_penduduk.alamat',
                        orderable: false
                    },
                    {
                        data: "attributes.keterangan",
                        name: 'tweb_penduduk.keterangan',
                        orderable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function (settings) {
                    var api = this.api();
                    api.column(0, {
                        search: 'applied',
                        order: 'applied'
                    }).nodes().each(function (cell, i) {
                        cell.innerHTML = api.page.info().start + i + 1;
                    });
                }
            });
        }
    });
</script>
@endpush