@extends('theme::layouts.full-content')
@includeIf('theme::commons.constant')

@section('content')
<style>
.team-one .row {
	display: flex;
	flex-wrap: wrap;
	align-items: stretch;
}

.single {
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	border-radius: 5px;
	box-shadow: 0px 10px 60px 0px rgba(46, 61, 98, 0.1);
	text-align: center;
	position: relative;
	padding: 20px;
	background: #fff;
	height: 100%;
	transform-origin: top;
	transition: all 500ms ease;
}

.inner {
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	flex-grow: 1;
	position: relative;
}

.image {
	margin: 0 auto 15px;
	width: 140px;
	height: 160px;
	border-radius: 10%;
	overflow: hidden;
	border: 5px solid #fff;
	transition: all 500ms ease;
	display: flex;
	align-items: center;
	justify-content: center;
}

.image img {
	object-fit: cover;
	width: 100%;
	height: 100%;
}

.single h5 {
	font-size: 18px;
	font-weight: bold;
	color: #252c4b;
	line-height: 1.2em;
	min-height: 42px;
	display: flex;
	align-items: center;
	justify-content: center;
	text-align: center;
}

.single p {
	color: #737789;
	font-size: 14px;
	margin: 5px 0;
	line-height: 1.4em;
}

.single::before {
	content: '';
	border-radius: 5px;
	background-image: linear-gradient(90deg, hotpink 0%, #9370DB 100%);
	position: absolute;
	inset: 0;
	transform: scale(1, 0);
	transform-origin: bottom;
	transition: transform 500ms ease;
	z-index: 0;
}

.single:hover::before {
	transform: scale(1, 1);
	transform-origin: top;
}

.single:hover h5,
.single:hover p {
	color: #fff;
}
</style>

<section class="team-one" id="team">
	<div class="card-header {{ theme_config('color', 'pink') }}-gradient font-weight-bold text-center">
		{{ strtoupper(setting('sebutan_pemerintah_desa')) }}
	</div>

	<div class="card mb-1 fullscreen has-background-img">
		<div id="pemerintah-list">
			@includeIf('theme::commons.loading')
		</div>
	</div>
</section>
@endsection
@push('script')
<script type="text/javascript">
$(document).ready(function() {

	const badgeHadir = `<span class="badge badge-info">{!! cekKehadiran('kehadiran', 'Ada di Kantor') !!}</span>`;
    const badgeTidakHadir = `<span class="badge badge-danger">{!! cekKehadiran('ketidakhadiran', 'Tidak Ada di Kantor') !!}</span>`;

	function loadSemuaPemerintah(url = '{{ route('api.pemerintah') }}', semuaData = []) {
		$('#pagination-container').hide();

		if (semuaData.length === 0) {
			$('#pemerintah-list').html(`@includeIf('theme::commons.loading')`);
		}

		$.get(url, function(response) {
			let dataPage = response.data || [];
			let nextPage = response.links?.next || null;
			semuaData = semuaData.concat(dataPage);

			if (nextPage) {
				loadSemuaPemerintah(nextPage, semuaData);
			} else {
				renderPemerintah(semuaData);
			}
		}).fail(function() {
			$('#pemerintah-list').html('<p class="text-center text-danger mt-4 mb-4">Gagal memuat data.</p>');
		});
	}

	function renderPemerintah(pemerintah) {
		const pemerintahList = $('#pemerintah-list');
		pemerintahList.empty();

		if (!pemerintah.length) {
			pemerintahList.html(`
				<div class="font-weight-bold text-center mt-4 mb-4">
					<h5>{{ setting('sebutan_pemerintah_desa') }} tidak tersedia.</h5>
				</div>
			`);
			return;
		}

		const mediaSosialPlatforms = {!! setting('media_sosial_pemerintah_desa') !!};
		const tampilkanKehadiran = {{ setting('tampilkan_kehadiran') ? 1 : 0 }};
		let html = '';

		pemerintah.forEach(item => {
			const attr = item.attributes;
			html += `
				<div class="col-12 col-lg-3 col-md-4 col-sm-6 mt-3 mb-3 d-flex">
					<div class="single w-100">
						<div class="inner">
							<h5>${attr.nama || '-'}</h5>
							<p><span class="font-weight-bold">${attr.nama_jabatan || ''}</span></p>
							<div class="image">
								<img src="${attr.foto || ''}" loading="lazy" alt="${attr.nama || ''}">
							</div>
							<p>${attr.pamong_nosk ? 'No. SK : ' + attr.pamong_nosk : ''}</p>
							<p>${attr.pamong_tglsk ? 'Tgl. SK : ' + attr.pamong_tglsk : ''}</p>
							<p>${attr.pamong_nip ? 'NIP : ' + attr.pamong_nip : (attr.pamong_niap ? 'NIAP : ' + attr.pamong_niap : '')}</p>

							${tampilkanKehadiran ? `
								${attr.status_kehadiran == 'hadir' ? badgeHadir : ''}
								${attr.tanggal == '{{ date('Y-m-d') }}' && attr.status_kehadiran != 'hadir'
									? `<span class="badge badge-warning">${attr.status_kehadiran}</span>` : ''}
								${attr.kehadiran == 1 && attr.tanggal != '{{ date('Y-m-d') }}'
									? badgeTidakHadir : ''}
							` : ''}

							<div class="mt-2">
								${mediaSosialPlatforms.length > 0 ? `
									${mediaSosialPlatforms.map(platform => `
										${attr.media_sosial && attr.media_sosial[platform] ? `
											<a href="${attr.media_sosial[platform]}" target="_blank" style="padding: 5px;">
												<i class="fa fa-${platform} fa-1x"></i>
											</a>
										` : `
											<a style="padding: 5px;">
												<span style="color:#ccc;"><i class="fa fa-${platform} fa-1x"></i></span>
											</a>
										`}
									`).join('')}
								` : ''}
							</div>
						</div>
					</div>
				</div>`;
		});

		pemerintahList.html(`<div class="container-fluid"><div class="row">${html}</div></div>`);
	}

	loadSemuaPemerintah();
});
</script>
@endpush
