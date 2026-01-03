@extends('theme::layouts.right-sidebar')
@section('content')
@includeIf('theme::commons.constant')

@if($single_artikel["id"])
<div class="card fullscreen has-background-img">
    <div id="printableArea">
        <div class="card-header border-bottom">
            <div class="media">
                <div class="icon-circle icon-40 bg-light-primary mr-3">
                    <i class="material-icons">fingerprint</i>
                </div>
                <div class="media-body">
                    <h5 class="my-0 content-color-primary">{{ $single_artikel["judul"] }}</h5>
                    <p class="small mb-0">
                        <i class="fa fa-calendar" aria-hidden="true"></i> {{ tgl_indo($single_artikel['tgl_upload']) }}
                        <i class="fa fa-heart" aria-hidden="true"></i> {{ hit($single_artikel['hit']) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-0 content-color-secondary">
                @if ($single_artikel['tipe'] == 'agenda')
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="card mb-2 success-gradient">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-white mb-0 small">Tanggal & Jam</p>
                                        <p class="text-white mb-0">{{ tgl_indo2($detail_agenda['tgl_agenda']) }}</p>
                                    </div>
                                    <div class="icon-circle icon-50 bg-light-white">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card mb-2 warning-gradient">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-white mb-0 small">Lokasi</p>
                                        <p class="text-white mb-0">{{ $detail_agenda['lokasi_kegiatan'] }}</p>
                                    </div>
                                    <div class="icon-circle icon-50 bg-light-white">
                                        <i class="fa fa-location-arrow" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card mb-2 {{ cekKondisiPink() }}-gradient">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-white mb-0 small">Koordinator</p>
                                        <p class="text-white mb-0">{{ $detail_agenda['koordinator_kegiatan'] }}</p>
                                    </div>
                                    <div class="icon-circle icon-50 bg-light-white">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($single_artikel['gambar'] != '' && is_file(LOKASI_FOTO_ARTIKEL."sedang_".$single_artikel['gambar']))
                <a data-fancybox="gallery" href="{{ AmbilFotoArtikel($single_artikel['gambar'],'sedang') }}">
                    <img src="{{ AmbilFotoArtikel($single_artikel['gambar'],'sedang') }}" width="100%" class="cover img-fluid border opacity-100" style="border-radius:5px;" alt="" />
                </a>
                @endif

                <div class="fb-like" data-href="{{ site_url('artikel/'.buat_slug($single_artikel)) }}" data-width="" data-layout="button_count" data-action="like" data-size="small" data-share="true"></div>
                <style>.text-responsive img {max-width:100%;height: auto;}</style>
                <div class="text-responsive content-color-primary mt-1">{!! $single_artikel["isi"] !!}</div>

                @foreach(['gambar1','gambar2','gambar3'] as $img)
                    @if($single_artikel[$img] != '' && is_file(LOKASI_FOTO_ARTIKEL."sedang_".$single_artikel[$img]))
                    <a data-fancybox="gallery" href="{{ AmbilFotoArtikel($single_artikel[$img],'sedang') }}">
                        <img src="{{ AmbilFotoArtikel($single_artikel[$img],'sedang') }}" class="img-responsive cover img-fluid border opacity-100 {{ $loop->index > 0 ? 'mt-1' : '' }}" style="border-radius:5px;" alt="" />
                    </a>
                    @endif
                @endforeach

                @if($single_artikel['dokumen'] != '' && is_file(LOKASI_DOKUMEN.$single_artikel['dokumen']))
                <div class="card mt-1 bg-light-primary border border-primary no-shadow">
                    <div class="card-body p-2">
                        <div class="media">
                            <div class="media-body">
                                <p class="mb-0">{{ $single_artikel['link_dokumen'] }}</p>
                                <h5 class="mb-0">{{ fsize(LOKASI_DOKUMEN.$single_artikel['dokumen']) }}</h5>
                            </div>
                            <a href="{{ site_url("first/unduh_dokumen_artikel/{$single_artikel['id']}") }}" class="icon-rounded btn btn-primary text-white mx-auto mt-0" rel='noopener noreferrer' target='_blank'>
                                <i class="material-icons text-white">cloud_download</i> Unduh
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card-footer border-top border-bottom">
            <div class="row">
                <div class="col">
                    <div class="media">
                        <div class="media-body">
                            <p class="content-color-secondary mb-0 small">
                                @if(trim($single_artikel['kategori']) != '')
                                <a href="{{ site_url('artikel/kategori/'.$single_artikel['id_kategori']) }}" class="content-color-secondary">
                                    <i class="fa fa-flag" aria-hidden="true"></i> {{ $single_artikel['kategori'] }}
                                </a>
                                @else
                                <i class="fa fa-flag" aria-hidden="true"></i> Berita Desa
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-right pr-3">
                    <div class="media">
                        <div class="media-body">
                            <p class="content-color-secondary mb-0 small">
                                <i class="fa fa-user" aria-hidden="true"></i> {{ $single_artikel['owner'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="sharethis-inline-reaction-buttons"></div>

    @php
        $share = ['link' => site_url('artikel/' . buat_slug($single_artikel)), 'judul' => $single_artikel["judul"]];
    @endphp
    @includeIf('theme::commons.share', $share)

    @if ($single_artikel['boleh_komentar'] == 1)
    <div class="ml-2 mr-2">
        <div class="fb-comments" data-href="{{ site_url('artikel/'.buat_slug($single_artikel)) }}" width="100%" data-numposts="5"></div>
    </div>
    @endif
</div>

<div class="card mb-1 fullscreen has-background-img">
    <div class="mb-0">
        @if(!empty($komentar))
        <div class="card-header border-bottom">
            <div class="media">
                <div class="icon-circle icon-40 bg-light-primary mr-3">
                    <i class="material-icons">chat</i>
                </div>
                <div class="media-body">
                    <h6 class="my-0 content-color-primary">Kiriman Komentar</h6>
                    <p class="small mb-0">Pada artikel ini</p>
                </div>
            </div>
        </div>
        <div class="card-body border-bottom">
            @foreach($komentar as $data)
            <ul class="list-group list-group-flush w-100 log-information mt-2">
                <li class="list-group-item">
                    <div class="avatar avatar-15 border-success"></div>
                    <div class="media experience">
                        <div class="icon-rounded icon-40 bg-light-success mr-3">
                            <i class="material-icons icon-sm">person</i>
                        </div>
                        <div class="media-body mb-3">
                            <h6 class="my-0 content-color-primary">{{ $data['pengguna']['nama'] }}</h6>
                            <p class="mb-2"><small class="content-color-secondary">{{ tgl_indo2($data['tgl_upload']) }}</small></p>
                            <div class="card-text">{{ $data['komentar'] }}</div>
                        </div>
                        @if(count($data['children']) > 0)
                        <div class="icon-circle icon-40 bg-light-primary mr-3">
                            <i class="material-icons icon-sm">chat</i>
                        </div>
                        <div class="media-body">
                            @foreach ($data['children'] as $children)
                            <h6 class="my-0 content-color-primary">Tanggapan : {{ $children['pengguna']['nama'] }} <code>({{ $children['pengguna']['level'] }})</code></h6>
                            <p class="mb-2"><small class="content-color-secondary">{{ tgl_indo2($children['tgl_upload']) }}</small></p>
                            <div class="card-text">{{ $children['komentar'] }}</div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </li>
            </ul>
            @endforeach
        </div>
        @endif
    </div>

    @if ($single_artikel['boleh_komentar'] == 1)
    <div id="kolom-komentar" class="card-header border-bottom">
        <div class="media">
            <div class="icon-circle icon-40 bg-light-danger mr-3">
                <i class="material-icons">chat</i>
            </div>
            <div class="media-body">
                <h6 class="my-0 content-color-primary">Kirim Komentar</h6>
                <p class="small mb-0">Untuk artikel ini</p>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-0 main-container z-index-1">
        <div class="row">
            <div class="col-12 p-4">
                @php
                    $notif = session('notif');
                    $label = ($notif['status'] == -1) ? 'alert-danger' : 'alert-success';
                @endphp
                @if ($notif)
                <div class="alert {{ $label }}" role="alert">{{ $notif['pesan'] }}</div>
                @endif
                <form class="contact_form form-validasi" id="validasi" name="form" action="{{ site_url("add_comment/$single_artikel[id]") }}" method="POST" onsubmit="return validasi(this);">
                    <div class="row">
                        <div class="col-12 col-lg-6 col-md-6 mb-2">
                            <label class="sr-only">Your Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">person</i>
                                    </span>
                                </div>
                                <input class="form-control required" type="text" name="owner" autocomplete="off" maxlength="100" placeholder="Nama Anda*" value="{{ $notif['data']['owner'] }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-md-6 mb-2">
                            <label class="sr-only">No. Hp</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">stay_current_portrait</i>
                                    </span>
                                </div>
                                <input class="form-control number required" type="text" name="no_hp" autocomplete="off" maxlength="15" placeholder="Nomor Hp Anda*" value="{{ $notif['data']['no_hp'] }}">
                            </div>
                        </div>
                    </div>
                    <label class="sr-only">Email address</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">mail</i>
                            </span>
                        </div>
                        <input class="form-control email" type="text" name="email" autocomplete="off" maxlength="100" placeholder="Alamat Email Anda" value="{{ $notif['data']['email'] }}">
                    </div>
                    <label class="sr-only">Message</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="material-icons">chat</i>
                            </span>
                        </div>
                        <textarea rows="5" class="form-control required" placeholder="Tulis Pesan Anda*" name="komentar">{{ $notif['data']['komentar'] }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 col-lg-4 col-md-4 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <img id="captcha" src="{{ site_url('captcha') }}" class="img-responsive cover" alt="">
                                    <a href="javascript:void(0);" onclick="document.getElementById('captcha').src = '{{ ci_route('captcha') }}' + '?' + Math.random(); return false;" alt="">
                                        <i class="fa fa-refresh fa-lg" style="padding: 10px; color: #0b7d33"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 col-md-3 mb-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <input type="text" class="form-control required" autocomplete="off" name="captcha_code" maxlength="6" placeholder="Ketik ulang kode*" value="{{ $notif['data']['captcha_code'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2 col-md-2 mb-2">
                            <button class="btn {{ cekKondisiPink() }}-gradient text-uppercase">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@else
<div class="card fullscreen has-background-img">
    <div class="card-header border-bottom">
        <div class="media">
            <div class="icon-circle icon-40 bg-light-primary mr-3">
                <i class="material-icons">business</i>
            </div>
            <div class="media-body">
                <h6 class="my-0 content-color-primary">Error 404</h6>
                <p class="small mb-0">
                    <i class="material-icons icon-sm">date_range</i> {{ date("d F Y H:i:s", gmdate(now())) }}
                </p>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="col-12 mx-auto text-center">
            <img src="{{ theme_asset("img/404.svg") }}" alt="" class="mw-100 mt-2">
            <h2 class="text-black">404! ARTIKEL TIDAK DITEMUKAN</h2>
            <p class="lead mb-4 text-black">Anda telah terdampar di halaman yang datanya tidak ada lagi di web ini.<br>Mohon periksa kembali, atau laporkan kepada kami.</p>
            <a href="{{ site_url() }}">
                <button class="btn btn-success success-gradient btn-rounded mb-4" type="button">
                    <span class="icon fa fa-angle-double-left mr-2"></span> Kembali ke Halaman Utama
                </button>
            </a>
        </div>
    </div>
</div>
@endif

@if($single_artikel["id"])
@includeIf('theme::partials.home.banner')
@endif
@endsection

@push('script')
<script>
$(document).ready(function() {
	if (typeof bantuanUrl !== 'undefined') {

		const $table = $('#peserta_program');

		const $loading = $('<div id="loading-overlay" style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;">Sedang memuat data...</div>');
		$table.parent().css('position', 'relative').append($loading);

		async function fetchAllData(url) {
			let allData = [];
			let nextUrl = url;

			while (nextUrl) {
				const response = await fetch(nextUrl);
				const json = await response.json();
				allData = allData.concat(json.data);
				nextUrl = json.links.next;
			}

			return allData.map(item => ({
				nama_peserta: item.attributes.kartu_nama,
				alamat: item.attributes.kartu_alamat,
				program: item.attributes.nama
			}));
		}

		fetchAllData(bantuanUrl + new Date().getFullYear())
		.then(allData => {
			$loading.remove();

			const dataTable = $table.DataTable({
				processing: true,
				serverSide: false,
				pageLength: 10,
				lengthMenu: [
					[10, 25, 50, 100, -1],
					[10, 25, 50, 100, "Semua"]
				],
				language: {
					url: BASE_URL + '/assets/bootstrap/js/dataTables.indonesian.lang',
					processing: "Sedang memuat..."
				},
				columns: [
					{ data: null, orderable: false },
					{ data: 'program' },
					{ data: 'nama_peserta' },
					{ data: 'alamat' }
				],
				order: [],
				rowCallback: function(row, data, displayIndex) {
					var api = this.api();
					var pageInfo = api.page.info();
					$('td:eq(0)', row).html(displayIndex + 1 + pageInfo.start);
				},
				drawCallback: function() {
					$table.find('tfoot').hide();
				},
				data: allData
			});
		})
		.catch(err => {
			$loading.text('Gagal memuat data');
			console.error(err);
		});
	}
});
</script>
@endpush

