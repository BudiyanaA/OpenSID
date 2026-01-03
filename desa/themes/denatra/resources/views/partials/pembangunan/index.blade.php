@extends('theme::layouts.full-content')

@section('content')

<div class="mt-0 mb-1 z-index-2">
	<div class="card-header {{ theme_config('color', 'pink') }}-gradient font-weight-bold text-center">
		Pembangunan {{ ucwords(setting('sebutan_desa')) }}
	</div>
	<div class="card mb-0 fullscreen has-background-img">
		<div class="container-fluid">
			<div id="pembangunan-list" class="row"></div>
			@includeIf('theme::commons.pagination')
		</div>
	</div>
</div>

@endsection

@push('script')
<script type="text/javascript">
	$(document).ready(function() {
		function loadPembangunan(params = {}) {

			const pageSize = 5;
			var apiPembangunan = `{{ route('api.pembangunan') }}?page[size]=${pageSize}`;

			$('#pagination-container').hide();

			jQuery.get(apiPembangunan, params, function(data) {
				var pembangunan = data.data;
				var pembangunanList = $('#pembangunan-list');

				pembangunanList.empty();

				if (!pembangunan.length) {
					pembangunanList.html(`
					<div class="font-weight-bold text-center mt-4 mb-4">
						<h5>Data pembangunan belum tersedia pada halaman ini.</h5>
					</div>
					`);
					return;
				}

				const imgNotFound = `{{ theme_asset('images/nodata.png') }}`;

				pembangunan.forEach(function(item) {
					var url = SITE_URL + 'pembangunan/' + item.attributes.slug;
					let foto = item.attributes.foto ? item.attributes.foto : `{{ theme_asset('images/pembangunan.png')`;
					var fotoHTML = `<img class="yall_lazy" src="${foto}" data-src="${foto}">`;                        

					var pembangunanHTML = `
					<div class="col-12 col-lg-4 col-md-4 order-2 order-md-1">
						<div class="card mb-2 mt-2">
							<div class="card-header border-bottom">
								<div class="media">
									<div class="media-body">
										<h4 class="mb-0 header-color-primary">${item.attributes.judul}</h4>
									</div>
								</div>
							</div>
							<a data-fancybox="gallery" href="${item.attributes.foto}">
								<center><img class="mw-100" style="max-height:220px" loading="lazy" src="${foto}" alt=""></center>
							</a>
							<div class="table-responsive">
								<table class="table table-striped">
									<tbody>
										<tr><td>Lokasi</td><td>:</td><td>${item.attributes.alamat === "=== Lokasi Tidak Ditemukan ===" ? 'Lokasi tidak diketahui' : item.attributes.alamat}</td></tr>
										<tr><td>Volume</td><td>:</td><td>${item.attributes.volume}</td></tr>
										<tr><td>Anggaran</td><td>:</td><td>Rp. ${new Intl.NumberFormat('id-ID').format(item.attributes.anggaran)}</td></tr>
										<tr><td>Tahun</td><td>:</td><td>${item.attributes.tahun_anggaran}</td></tr>
									</tbody>
								</table>
							</div>
							<div class="card-body">
								<div class="d-flex justify-content-between align-items-center">
									<div class="btn-group">
										<a class="btn btn-sm btn-danger" href="https://www.google.com/maps/dir//${item.attributes.lat},${item.attributes.lng}" rel="noopener noreferrer" target="_blank" title="Titik Lokasi"><i class="fa fa-map-marker"></i> Titik Lokasi</a>
										<a href="${url}" class="btn btn-sm btn-primary text-white" title="Selengkapnya"><i class="fa fa-map"></i> Selengkapnya</a>
									</div>
								</div>
							</div>
						</div>
					</div>`;

					pembangunanList.append(pembangunanHTML);
				});

				initPagination(data);
			});
		}

		$('.pagination').on('click', '.btn-page', function() {
			var params = {};
			var page = $(this).data('page');

			params['page[number]'] = page;

			loadPembangunan(params);
		});

		loadPembangunan();
	});
</script>
@endpush