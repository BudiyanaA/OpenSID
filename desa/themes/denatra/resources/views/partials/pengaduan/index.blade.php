@extends('theme::layouts.full-content')

@section('content')

@push('style')
<style type="text/css">
	.list-group li {
		cursor: pointer;
	}

	.list-group li:hover {
		background: #eee;
	}

	#pengaduan-list li .list-group-item {
		cursor: pointer;
	}
</style>
@endpush

<div class="card mt-0 mb-1 has-background-img">
	<div class="form-inline text-center">
		<select class="form-control input-sm select2 ml-1 mr-1 mb-1 mt-1" id="caristatus" name="caristatus">
			<option value="">Semua Status</option>
			<option value="1">Menunggu Diproses</option>
			<option value="2">Sedang Diproses</option>
			<option value="3">Selesai Diproses</option>
		</select>
		<div class="input-group mb-1 small ml-1 mr-1 mt-1">
			<input type="text" name="cari-pengaduan" class="form-control" autocomplete="off" placeholder="Cari Pengaduan" aria-label="Cari Pengaduan">
			<div class="input-group-append">
				<button class="btn btn-info" id="btn-search" type="submit"><i class="fa fa-search"></i></button>
			</div>
		</div>
		<div class="input-group-append ml-1 mt-1 mb-1" style="display: none" id="resetFilterWrapper">
			<a href="#" class="btn btn-info" id="resetFilter"><i class="fa fa-times"></i></a>
		</div>
		<button type="button" class="btn btn-success ml-1 mr-1 mb-1 mt-1" data-toggle="modal" data-target="#newpengaduan">Formulir Pengaduan</button>
	</div>
</div>
<div class="mt-0 mb-1 z-index-2">
	<div class="card-header {{ theme_config('color', 'pink') }}-gradient font-weight-bold text-center">Halaman Pengaduan</div>
	<div class="card mb-0 fullscreen has-background-img">
		<div class="container-fluid mt-2">
			<!-- Notifikasi -->
			@if (($notif = session('notif')) && (!session('notif')['data']))
			<div class="alertpengaduan">
				<div id="notifikasi" class="alert alert-{{ $notif['status'] }}" role="alert">
					{{ $notif['pesan'] }}
				</div>
			</div>
			@endif

			<div id="pengaduan-list">
			</div>
			@includeIf('theme::commons.pagination')
		</div>
	</div>
</div>

<!-- Formulir Pengaduan -->
<div class="modal fade" id="newpengaduan" tabindex="-1" role="dialog" aria-labelledby="newpengaduan" aria-hidden="true">
	<div class="modal-wrapper">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-blue">
					<h6 class="modal-title"><i class="fa fa-pencil"></i> Buat Pengaduan Baru</h6>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				@php $data = session('data', []) @endphp
				<form action="{{ $form_action }}" method="POST" enctype="multipart/form-data">
					<div class="modal-body">
						@includeIf('theme::commons.notifikasi')
						<div class="form-group row">
							<div class="col-lg-6 col-md-6">
								<input name="nama" type="text" autocomplete="off" class="form-control" placeholder="*Nama" value="{{ $data['nama'] ?? '' }}" required>
							</div>
							<div class="col-lg-6 col-md-6">
								<input name="nik" type="text" autocomplete="off" maxlength="16" class="form-control" placeholder="NIK" value="{{ $data['nik'] ?? '' }}">
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6 col-md-6">
								<input name="email" type="email" autocomplete="off" class="form-control" placeholder="Email" value="{{ $data['email'] ?? '' }}">
							</div>
							<div class="col-lg-6 col-md-6">
								<input name="telepon" type="text" autocomplete="off" class="form-control" placeholder="Telepon" value="{{ $data['telepon'] ?? '' }}">
							</div>
						</div>
						<div class="form-group">
							<input name="judul" type="text" autocomplete="off" class="form-control" placeholder="*Judul" value="{{ $data['judul'] ?? '' }}" required>
						</div>
						<div class="form-group">
							<textarea name="isi" class="form-control" autocomplete="off" placeholder="*Isi Pengaduan" rows="5" required>{{ $data['isi'] ?? '' }}</textarea>
						</div>
						<div class="badge badge-danger mb-1">*wajib diisi</div>
						<div class="form-group row">
							<div class="col-lg-6 col-md-6">
								<br><img id="blah" style="display: none;" src="#" alt="gambar" width="100%"/>
							</div>
							<div class="col-lg-6 col-md-6">
								<small class="badge badge-info mb-1">Gambar: png, jpg, jpeg</small>
								<div class="custom-file">
									<input type="text" accept="image/*" onchange="readURL(this);" class="form-control" id="file_path" placeholder="Unggah Foto" name="foto" value="{{ $data['foto'] }}">
									<input type="file" accept="image/*" onchange="readURL(this);" class="custom-file-input" id="file" name="foto" value="{{ $data['foto'] }}">
									<label class="custom-file-label" for="validatedCustomFile" id="file_browser">Lampirkan Foto</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="captcha col-lg-6 col-md-6">
								<img id="captcha" src="{{ ci_route('captcha') }}" alt="CAPTCHA Image" width="100%"/>
								<a href="#" id="b-captcha" onclick="document.getElementById('captcha').src = '{{ ci_route('captcha') }}?'+Math.random();" style="color: #000000;">
									<i class="fa fa-refresh fa-lg" style="padding: 10px; color: #0b7d33"></i>
								</a>
							</div>
							<div class="col-lg-6 col-md-6">
								<input type="text" class="form-control" autocomplete="off" name="captcha_code" maxlength="6" placeholder="Ketik ulang kode" value="{{ $data['captcha_code'] ?? '' }}" required>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-sm btn-danger pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</a>
						<button type="submit" class="btn btn-sm btn-primary pull-right"><i class="fa fa-pencil"></i> Kirim</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@push('script')
<script type="text/javascript">
	$(function() {
		const pageSize = 5
		let pageNumber = 1
		let status = ''
		let cari = $('input[name=cari-pengaduan]').val()

		var notFound = `{{ theme_asset('images/nodata.png') }}`;
		const spinnerImg = `{{ theme_asset('images/icon/spinner.svg') }}`;
		const latarWebsite = `{{ $latar_website ? $latar_website : base_url('assets/front/css/images/latar_website.jpg'); }}{{ asset('front/css/images/latar_website.jpg') }}`;
		const fullscreenImg = `{{ theme_asset('images/icon/fullscreen.svg') }}`;

		window.setTimeout(function() {
			$("#notifikasi").fadeTo(500, 0).slideUp(500, function() {
				$(this).remove();
			});
		}, 2000);

		var data = 0;
		if (data) {
			$('#newpengaduan').modal('show');
		}

		$('#btn-search').click(function() {
			pageNumber = 1
			cari = $('input[name=cari-pengaduan]').val()
			status = $('#caristatus').val()
			loadPengaduan(pageNumber)
			$('#resetFilterWrapper').show()
		})

		$('#resetFilter').on('click', function() {
			$('#resetFilterWrapper').hide();
			$('input[name=cari-pengaduan').val('');
			$('#caristatus').val('');
			cari = $('input[name=cari-pengaduan]').val()
			status = $('#caristatus').val()
			loadPengaduan();
		});

		const loadPengaduan = function(pageNumber) {
			let _filter = []
			if (status) {
				$('#resetFilterWrapper').show()
				_filter.push('filter[status]=' + status)
			}

			if (cari) {
				$('#resetFilterWrapper').show()
				_filter.push('filter[search]=' + cari)
			}

			let _filterString = _filter.length ? _filter.join('&') : ''
			
			$.ajax({
				url: `{{ ci_route('internal_api.pengaduan') }}?sort=-created_at&page[number]=${pageNumber}&page[size]=${pageSize}&${_filterString}`,
				type: "GET",
				beforeSend: function() {
					$('#pengaduan-list').html(`@includeIf('theme::commons.loading')`);
				},
				dataType: 'json',
				data: {

				},
				success: function(data) {
					displayPengaduan(data);
					initPagination(data);
				}
			});
		}

		const displayPengaduan = function(dataPengaduan) {
			const pengaduanList = document.getElementById('pengaduan-list');
			pengaduanList.innerHTML = '';
			if (!dataPengaduan.data.length) {
				pengaduanList.innerHTML = `
				<div class="font-weight-bold text-center mt-4 mb-4">
					<h5>Data pengaduan belum tersedia pada halaman ini.</h5>
				</div>
				`
				return
			}
			
			dataPengaduan.data.forEach(item => {
				const card = document.createElement('a');
				card.href = `#pengaduan${item.id}`;
				card.className = `list-group-item status${item.attributes.status} allstatus mb-2 border-bottom`;
				card.setAttribute('data-toggle', 'modal');
				card.setAttribute('data-target', `#pengaduan${item.id}`);
				
				const fileFoto = item.attributes.foto ? item.attributes.foto : null;
				const fotoContent = fileFoto 
					? `<img src="${fileFoto}" width="100%" alt="">` 
					: `<i class="material-icons icon-sm">person</i>`;
				
				const statusBadge = item.attributes.status === 1 
					? `<span class="badge badge-danger">Menunggu Diproses</span>` 
					: item.attributes.status === 2 
					? `<span class="badge badge-info">Sedang Diproses</span>` 
					: `<span class="badge badge-success">Selesai Diproses</span>`;
				
				const isiPreview = item.attributes.isi.length > 50 
					? `${item.attributes.isi.substring(0, 50)} <span class="badge badge-info">...selengkapnya</span>` 
					: item.attributes.isi;

				const tanggapanBadge = item.attributes.child_count > 0 
					? `<span class="badge badge-primary pull-right mt-1"><i class="fa fa-comments"></i> ${item.attributes.child_count} Tanggapan</span>` 
					: `<span class="badge badge-danger pull-right mt-1"><i class="fa fa-comments"></i> ${item.attributes.child_count} Tanggapan</span>`;

				card.innerHTML = `
					<div class="avatar avatar-15 border-success"></div>
					<div class="media experience">
						<div class="icon-rounded icon-40 bg-light-success mr-3">
							${fotoContent}
						</div>
						<div class="media-body">
							<h6 class="my-0 content-color-primary">
								${item.attributes.nama}
							</h6>
							<p class="mb-2"><small class="content-color-secondary">
								${item.attributes.created_at} | ${statusBadge}
							</small></p>
							<p class="info">
								<span><b>${item.attributes.judul}</b></span><br>
								<span>${isiPreview}</span>
								${tanggapanBadge}
							</p>
						</div>
					</div>
				`;

				const modal = document.createElement('div');
				modal.className = 'modal fade';
				modal.id = `pengaduan${item.id}`;
				modal.tabIndex = -1;
				modal.setAttribute('role', 'dialog');
				modal.setAttribute('aria-labelledby', `pengaduan${item.id}`);
				modal.setAttribute('aria-hidden', 'true');

				modal.innerHTML = `
					<div class="modal-wrapper">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-blue">
									<h6 class="modal-title"><i class="fa fa-file"></i> ${item.attributes.judul}</h6>
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								</div>
								<div class="modal-body">
									<div class="row mb-2">
										<div class="${item.attributes.foto ? 'col-lg-7 col-md-7' : 'col-lg-12 col-md-12'}">
											<span class="small">${item.attributes.created_at} dari <span class="badge badge-info">${item.attributes.nama}</span></span>
											<p>${item.attributes.isi}</p>
										</div>
										${item.attributes.foto ? `
										<div class="col-lg-5 col-md-5">
											<a data-fancybox="gallery" href="${item.attributes.foto}">
												<img src="${item.attributes.foto}" class="img-responsive cover img-fluid border opacity-100 mt-2" alt="" />
											</a>
										</div>` : ''}
									</div>
									${item.attributes.child.map(reply => `
									<li class="list-group-item mb-2">
										<div class="avatar avatar-15 border-info"></div>
										<div class="media experience">
											<div class="icon-rounded icon-40 bg-light-success mr-3">
												<i class="material-icons icon-sm">person</i>
											</div>
											<div class="media-body">
												<span class="small">${reply.created_at} | Ditanggapi oleh <span class="badge badge-info">${reply.nama}</span></span>
												<p>${reply.isi}</p>
											</div>
										</div>
									</li>`).join('')}
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-sm btn-danger pull-left" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Tutup</button>
								</div>
							</div>
						</div>
					</div>
				`;

				document.body.appendChild(modal);

				pengaduanList.appendChild(card);
			});                
		}

		$('.pagination').on('click', '.btn-page', function() {
			var page = $(this).data('page');
			loadPengaduan(page);
		});

		loadPengaduan(pageNumber);
	});

	function caristatus() {
		loadPengaduan();
	}

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#blah').show();
				$('#blah').attr('src', e.target.result).width(150).height(auto);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
@endpush