@if (theme_config('rotate_logo'))
<style>
	@keyframes spin {
		0% {
			transform: rotateY(0deg);
		}

		100% {
			transform: rotateY(360deg);
		}
	}

	.rotating-image {
		display: block;
		margin: auto;
		animation: spin 5s linear infinite;
	}
</style>
@endif
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
	src="https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v14.0&appId={{ config('fbappid') }}&autoLogAppEvents=1"
	nonce="M5gMDuon"></script>
<!-- main header -->
<header class="main-header">
	<div class="container-fluid">
		<div class="row align-items-center">
			<div class="col-auto px-0">
				<button class="btn {{ cekKondisiPink() }}-gradient btn-icon" id="left-menu"><i
						class="material-icons">storage</i></button>
				<a href="{{ site_url('/') }}" class="logo"><img src="{{ gambar_desa($desa['logo']) }}" alt="">
					<span class="">
						<b class="text-hide-xs">{{ setting('website_title') }}</b> <b>{{
							ucwords(setting('sebutan_desa').' '.$desa['nama_desa']) }}</b><br>
						<span class="small">{{ ucwords(setting('sebutan_kecamatan').' '.$desa['nama_kecamatan'])
							}}</span> <span class="small text-hide-xs">{{ ucwords(setting('sebutan_kabupaten').'
							'.$desa['nama_kabupaten']) }}</span></span>
				</a>
			</div>
			<div class="col px-0 text-right">
				<div class="dropdown d-inline-block text-hide-xs">
					<a class="btn header-color-secondary btn-icon dropdown-toggle caret-none" href="javascript:void(0);"
						role="button" id="dropdownnotification" data-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false">
						<i class="material-icons">assessment</i>
					</a>
					<div class="dropdown-menu notification-dropdown" aria-labelledby="dropdownnotification">
						<div class="card">
							<div class="card-header border-bottom">
								<h5 class="content-color-primary">Statistik Pengunjung</h5>
							</div>
							<div class="small card-body text-center">
								<form method="get" action="{{ site_url('/') }}">
									<div class="input-group mb-0">
										<input type="text" name="cari" class="form-control" autocomplete="off"
											value="{{ $cari }}"
											placeholder="Cari Artikel" aria-label="Cari Artikel">
										<div class="input-group-append">
											<button class="btn btn-outline-secondary" type="submit">Cari</button>
										</div>
									</div>
								</form>
								<table class="table table-striped table-inverse mt-2">
									<tr>
										<td style="text-align:left">Hari ini</td>
										<td>:</td>
										<td style="text-align:right">{{ ribuan($statistik_pengunjung['hari_ini'] ?? 0) }}
										</td>
									</tr>
									<tr>
										<td style="text-align:left">Kemarin</td>
										<td>:</td>
										<td style="text-align:right">{{ ribuan($statistik_pengunjung['kemarin'] ?? 0) }}</td>
									</tr>
									<tr>
										<td style="text-align:left">Total Pengunjung</td>
										<td>:</td>
										<td style="text-align:right">{{ ribuan($statistik_pengunjung['total'] ?? 0) }}</td>
									</tr>
									<tr>
										<td style="text-align:left">Sistem Operasi</td>
										<td>:</td>
										<td style="text-align:right">{{ $statistik_pengunjung['os'] ?? 'Unknown' }}</td>
									</tr>
									<tr>
										<td style="text-align:left">IP Address</td>
										<td>:</td>
										<td style="text-align:right">{{ $statistik_pengunjung['ip_address'] ?? 'Unknown' }}</td>
									</tr>
									<tr>
										<td style="text-align:left">Browser</td>
										<td>:</td>
										<td style="text-align:right">{{ $statistik_pengunjung['browser'] ?? 'Unknown' }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<a href="{{ site_url('/feed') }}" class="content-color-secondary" rel="noopener noreferrer"
					target="_blank"><i class="text-hide-xs material-icons">rss_feed</i></a>
				<div class="dropdown d-inline-block text-hide-xs">
					@foreach ($sosmed as $data)
					@if (!empty($data["link"]))
					<a href="{{ $data['link'] }}" title="{{ $data['nama'] }}" rel="noopener noreferrer" target="_blank">
						<figure class="avatar avatar-24 vm d-inline-block border ml-1">
							<img src="{{ $data['icon'] }}" width="22" height="22" alt="{{ $data['nama'] }}">
						</figure>
					</a>
					@endif
					@endforeach
					<a class="btn header-color-secondary btn-icon dropdown-toggle caret-none" href="javascript:void(0);"
						role="button" id="dropdownmessage2" data-toggle="dropdown" aria-haspopup="true"
						aria-expanded="false">
						<figure class="avatar avatar-24 vm d-inline-block border"><img
								src="{{ asset('files/logo/opensid_logo.png') }}"></figure>
					</a>
					<div class="dropdown-menu" aria-labelledby="dropdownmessage2">
						<a class="dropdown-item {{ cekKondisiPink() }}-gradient-active" href="javascript:void(0);"
							id="open-right-sidebar">
							<div class="row align-items-center">
								<div class="col">Tema DeNatra {{ THEME_VERSION }}</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<div class="container-fluid">
	<div class="row align-items-center has-background-img min-height-200">
		<figure class="background-img {{ cekKondisiPink() }}-gradient">
			<img src="{{ $latar_website ?? theme_asset('img/header.jpg') }}" alt="">
		</figure>
		<div class="container{{ cekFluid() ? '-fluid' : '' }}">
			<div
				class="row align-items-center @if(empty(html_escape($cari)) && (in_array(request()->segment(1), ['', 'first', 'index'])) && !theme_config('menu', false)) mb-5 mt-0 @endif">
				<div class="col-12 col-sm-auto text-center mt-3 text-hide-xs">
					<figure class="avatar-120 mx-auto my-3">
						<img src="{{ gambar_desa($desa['logo']) }}" alt="" class="mh-100 {{ theme_config('rotate_logo') ? 'rotating-image' : '' }}" />
					</figure>
				</div>
				<div class="col-12 col-sm text-center mt-3 text-sm-left text-white">
					<h3 class="mb-0">
						<span class="text-hide-xs">{{ ucwords(setting('sebutan_desa')) }} {{ ucwords($desa['nama_desa']) }}</span>
					</h3>
					<p class="text-hide-xs">
						{{ $desa['alamat_kantor'] }}<br>
						{{ ucwords(setting('sebutan_kecamatan') . " " . $desa['nama_kecamatan']) }} 
						{{ ucwords(setting('sebutan_kabupaten') . " " . $desa['nama_kabupaten']) }} 
						Provinsi {{ $desa['nama_propinsi'] }}
						@if (!empty($desa['kode_pos']))
							, Kode Pos {{ $desa['kode_pos'] }}
						@endif
						@if (!empty(setting('motto_desa')))
							<br>Motto {{ ucwords(setting('sebutan_desa')) }}: {{ setting('motto_desa') }}
						@endif
					</p>
					<p class="small text-hide-xs">
						@php
							$contactInfo = [];
							if (!empty($desa['telepon'])) {
								$contactInfo[] = '<i class="fa fa-phone"></i> ' . $desa['telepon'];
							}
							if (!empty($desa['nomor_operator'])) {
								$contactInfo[] = '<i class="fa fa-mobile"></i> ' . $desa['nomor_operator'];
							}
							if (!empty($desa['email_desa'])) {
								$contactInfo[] = '<i class="material-icons">mail_outline</i> ' . $desa['email_desa'];
							}
						@endphp
						{!! implode('<span class="mx-2">|</span>', $contactInfo) !!}
					</p>
				</div>
				<div class="text-center text-sm-right col-12 col-sm-auto mt-0 mb-2">
					<div class="mt-2 text-hide-lg"></div>
					@includeIf('theme::partials.event.index')
					<div class="text-center mr-3 ml-3 mt-2 text-white">
						<a href="https://api.whatsapp.com/send?l=id&text={{ current_url() }}"
							rel='noopener noreferrer' target='_blank' title='Whatsapp'>
							<button type="button" class="btn btn-success btn-sm">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="27" fill="currentColor"
									class="bi bi-twitter" viewBox="0 0 24 24">
									<path
										d="M2.004 22l1.352-4.968A9.954 9.954 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.954 9.954 0 0 1-5.03-1.355L2.004 22zM8.391 7.308a.961.961 0 0 0-.371.1 1.293 1.293 0 0 0-.294.228c-.12.113-.188.211-.261.306A2.729 2.729 0 0 0 6.9 9.62c.002.49.13.967.33 1.413.409.902 1.082 1.857 1.971 2.742.214.213.423.427.648.626a9.448 9.448 0 0 0 3.84 2.046l.569.087c.185.01.37-.004.556-.013a1.99 1.99 0 0 0 .833-.231c.166-.088.244-.132.383-.22 0 0 .043-.028.125-.09.135-.1.218-.171.33-.288.083-.086.155-.187.21-.302.078-.163.156-.474.188-.733.024-.198.017-.306.014-.373-.004-.107-.093-.218-.19-.265l-.582-.261s-.87-.379-1.401-.621a.498.498 0 0 0-.177-.041.482.482 0 0 0-.378.127v-.002c-.005 0-.072.057-.795.933a.35.35 0 0 1-.368.13 1.416 1.416 0 0 1-.191-.066c-.124-.052-.167-.072-.252-.109l-.005-.002a6.01 6.01 0 0 1-1.57-1c-.126-.11-.243-.23-.363-.346a6.296 6.296 0 0 1-1.02-1.268l-.059-.095a.923.923 0 0 1-.102-.205c-.038-.147.061-.265.061-.265s.243-.266.356-.41a4.38 4.38 0 0 0 .263-.373c.118-.19.155-.385.093-.536-.28-.684-.57-1.365-.868-2.041-.059-.134-.234-.23-.393-.249-.054-.006-.108-.012-.162-.016a3.385 3.385 0 0 0-.403.004z" />
								</svg> WhatsApp
							</button>
						</a>
						<a name="fb_share" href="http://www.facebook.com/sharer.php?u={{ current_url() }}" rel='noopener noreferrer'
							target='_blank' title='Facebook'>
							<button type="button" class="btn btn-primary btn-sm">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="27" fill="currentColor"
									class="bi bi-twitter" viewBox="0 0 24 24">
									<path
										d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z" />
								</svg> Facebook
							</button>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="{{ in_array(request()->segment(1), ['']) ? 'mb-1' : '' }}">
	@if (theme_config('menu', true)) @includeIf('theme::partials.home.menu_header') @endif
</div>