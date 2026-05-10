@extends('theme::layouts.full-content')
@includeIf('theme::commons.constant')
@section('content')

<div class="card mb-2 fullscreen has-background-img ">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="icon-circle icon-40 bg-light-primary mr-3">
                <i class="material-icons">view_day</i>
            </div>
            <div class="media-body">
                <h6 class="my-0 content-color-primary titleKelompok"></h6>
                <p class="small mb-0">
                    <i class="material-icons icon-sm">local_offer</i> <span class="kategoriKelompok"></span>  {{ ucwords(setting('sebutan_desa')) . " " . $desa['nama_desa'] }}
                </p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-0 content-color-secondary">
            <div class="table-responsive">
                <h5 class="subtitle">Rincian Kelompok/Lembaga</h5>
                <table class="table table-bordered table-striped table-hover">
                    <tbody>
                        <tr>
                            <td width="20%">Kode</td>
                            <td width="1"> : </td>
                            <td id="kodeKelompok"></td>
                            <td width="20%" rowspan="5" style="text-align: center; vertical-align: middle;" id="logoKelompok">
                            </td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td> : </td>
                            <td class="titleKelompok"></td>
                        </tr>
                        <tr>
                            <td>Kategori <span class="kategoriKelompok"></span></td>
                            <td>:</td>
                            <td class="kategoriKelompok"></td>
                        </tr>
                        <tr>
                            <td>No. SK Pendirian</td>
                            <td>:</td>
                            <td id="no_sk"></td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td> : </td>
                            <td id="keterangan"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h5>Daftar Anggota</h5>
            <div class="table-responsive">
                <table id="tabel-data" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No. Anggota</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>{{ ucwords(setting('sebutan_dusun')) }}</th>
                            <th>RW</th>
                            <th>RT</th>
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

<script>
    $(document).ready(function() {
    var route = "{{ route('api.' . $tipe . '.detail', ['slug' => $slug]) }}";
    let anggotaElemen = '';
    $.ajax({
        url: route,
        method: 'GET',
        success: function (data) {
            var detail = data.data.attributes;
            console.log(detail);
            
            var pengurus = detail.pengurus;
            var tipe = detail.tipe;
            var gambar_desa = detail.logo;
            
            $('.titleKelompok').text(detail.nama);
            $('#kodeKelompok').text(detail.kode);
            $('#no_sk').text(detail.no_sk_pendirian);
            $('#keterangan').text(detail.keterangan);        
            if ($('#logoKelompok').length) {
                $('#logoKelompok').html(`<img src="${gambar_desa}" alt="Logo ${tipe}" height="120px;">`);
            }
            $('.kategoriKelompok').text(detail.kategori);

            pengurus.forEach((data, key) => {
            anggotaElemen += `
                    <tr>
                    <td>${data.no_anggota ?? '-'}</td>
                    <td nowrap>${data.nama_penduduk}</td>
                    <td>${data.nama_jabatan}</td>
                    <td>${data.anggota.jenis_kelamin.nama}</td>
                    <td>${data.anggota.usia}</td>
                    <td>${data.anggota.wilayah.dusun}</td>
                    <td>${data.anggota.wilayah.rw}</td>
                    <td>${data.anggota.wilayah.rt}</td>
                    </tr>`;
            });

            anggotaTable();
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
        }
    });

    const anggotaTable = () => {
        $.ajax({
            url: `{{ route('api.kelompok.anggota', ['slug' => $slug]) }}`,
            method: 'GET',
            success: function (data) {
                
                if (data.data.length === 0) {
                    $('#tabel-data tbody').html(anggotaElemen);
                        return;
                }
                
                data.data.forEach((data, key) => {
                    anggotaElemen += `
                        <tr>
                        <td>${data.attributes.no_anggota ?? '-'}</td>
                        <td nowrap>${data.attributes.nama_penduduk}</td>
                        <td>${data.attributes.nama_jabatan}</td>
                        <td>${data.attributes.sex}</td>
                        <td>${data.attributes.anggota.usia}</td>
                        <td>${data.attributes.anggota.wilayah.dusun}</td>
                        <td>${data.attributes.anggota.wilayah.rw}</td>
                        <td>${data.attributes.anggota.wilayah.rt}</td>
                        </tr>`;
                });

                $('#tabel-data tbody').html(anggotaElemen);
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
            }
        });
    }
});
</script>

@endpush