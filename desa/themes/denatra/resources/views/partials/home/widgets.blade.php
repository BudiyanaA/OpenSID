<form method="get" action="{{ site_url('/') }}">
	<div class="card col-12 mb-1">
		<div class="row no-gutters">
			<div class="col mt-2 mb-2">
				<div class="input-group input-group-sm">
					<input type="text" name="cari" class="form-control form-control-rounded" value="{{ $cari }}" autocomplete="off" placeholder="Cari Artikel" aria-label="Cari Artikel">
				</div>
			</div>
			<div class="col-auto mt-2 mb-2">
				<button class="btn btn-outline-primary btn-rounded btn-sm ml-2" type="submit"><span class="text-hide-xs">Cari</span> <i class="material-icons">send</i></button>
			</div>
		</div>
	</div>
</form>
@if (!empty($widgetAktif))
	@foreach ($widgetAktif as $data)
		@php
			$judul_widget = ['judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($data['judul']))];
			$widget = trim($data['isi']);
		@endphp
		@if ($data['jenis_widget'] == 1)

			@if($widget == 'menu_kategori')
				<div class="card mb-1">
					<div class="card-header border-bottom">
						<div class="media">
							<div class="media-body">
								<h4 class="content-color-primary mb-0"><i class="material-icons icon">widgets</i> <span>Menu Kategori</span></h4>
							</div>
						</div>
					</div>
					<div class="card-body">
						@includeIf("theme::widgets.{$widget}", $judul_widget)
					</div>
				</div>
			@else
				@includeIf("theme::widgets.{$widget}", $judul_widget)
			@endif

		@else
			@if (!in_array(strtoupper(strip_tags($data['judul'])), [strtoupper('event'), strtoupper('logo')]))
				<div class="card mb-1">
					<div class="card-header border-bottom">
						<div class="media">
							<div class="media-body">
								<h4 class="content-color-primary mb-0"><i class="material-icons icon-sm">reorder</i> {!! $judul_widget['judul_widget'] !!}</h4>
							</div>
						</div>
					</div>
					<div class="card-body text-center">
						<div class="align-items-center no-gutters ml-3 mr-3">
							<img class="img-responsive mw-100" src="{{ theme_asset('images/' . $data['foto']) }}" alt="">
							@includeIf("theme::widgets.{$widget}", $judul_widget)
						</div>
					</div>
				</div>
			@endif
		@endif
	@endforeach
@endif
