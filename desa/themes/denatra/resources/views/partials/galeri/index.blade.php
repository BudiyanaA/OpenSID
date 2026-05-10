@extends('theme::layouts.full-content')

@section('content')

<div class="card mb-2 fullscreen has-background-img ">
	<div class="card-header border-bottom">
		<div class="media">
			<div class="icon-circle icon-40 bg-light-primary mr-3">
				<i class="material-icons icon-sm">insert_photo</i>
			</div>
			<div class="media-body">
				<h6 class="my-0 content-color-primary">Album Galeri</h6>
				<p class="small mb-0"><i class="material-icons icon-sm">date_range</i>
					{{ ucwords(setting('sebutan_desa')) . " " . $desa['nama_desa'] }}
				</p>
			</div>
		</div>
	</div>
	<div class="card-body" id="galeri-list">
	</div>
	@includeIf('theme::commons.pagination')
</div>

@endsection

@push('script')
<script type="text/javascript">
	$(document).ready(function() {
		var parent = `{{ $parent }}`;
		var routeGaleri = `{{ ci_route('internal_api.galeri') }}`;
		let pageSizes = 5;
		let status = '';

		const imgNotFound = '{{ theme_asset('images/noimage.png') }}';
		const pinkGradient = '{{ theme_config('color', 'pink') }}-gradient';

		if (parent) {
			routeGaleri = `{{ ci_route('internal_api.galeri') }}/${parent}`;
			pageSizes = 10;
		}

		const loadGaleri = function(pageNumber) {
			$.ajax({
				url: routeGaleri + `?sort=-tgl_upload&page[number]=${pageNumber}&page[size]=${pageSizes}`,
				type: "GET",
				beforeSend: function() {
					const galeriList = document.getElementById('galeri-list');
				},
				dataType: 'json',
				data: {

				},
				success: function(data) {
					displayGaleri(data);
					initPagination(data);
				}
			});
		}

		const displayGaleri = function(dataGaleri) {
			const galeriList = document.getElementById('galeri-list');
			galeriList.innerHTML = '';
			if (!dataGaleri.data.length) {
				galeriList.innerHTML = `
				<div class="font-weight-bold text-center mt-4 mb-4">
					<h5>Album galeri belum tersedia.</h5>
				</div>
				`
				return
			}
			
			var galeriListHtml = '';
			dataGaleri.data.forEach(item => {
				var img = item.attributes.src_gambar ? item.attributes.src_gambar : imgNotFound;
				var detailAlbum = parent ? item.attributes.src_gambar : item.attributes.url_detail;

				const btnLihat = parent ? item.attributes.nama : `<a class="btn btn-sm btn-purple ${pinkGradient}" href="${detailAlbum}" title="Album : ${item.attributes.nama}">Lihat Album</a>`;

				galeriListHtml += `
				<div class="col-12 col-lg-6 col-md-6">
					<div class="card entry text-center mb-2">
						<a data-fancybox="gallery" href="${img}" title="Album : ${item.attributes.nama}">
							<img class="mw-100 border" height="200" loading="lazy" alt="" src="${img}">
						</a>
						${btnLihat}
					</div>
				</div>
				`;
				if (item.attributes.index % 2 === 0) {
					galeriListHtml += `<div class="clearboth"></div>`;
				}
			});

			var galeriListFinal = `
			<div class="container main-container mb-2">
				<div class="row">
					${galeriListHtml}
				</div>
			</div>
			`;
			galeriList.innerHTML = galeriListFinal;
		}

		$('.pagination').on('click', '.btn-page', function() {
			var params = {};
			var page = $(this).data('page');
			loadGaleri(page);
		});

		loadGaleri(1);
	});
</script>
@endpush