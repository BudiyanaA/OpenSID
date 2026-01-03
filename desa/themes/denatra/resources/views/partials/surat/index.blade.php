<!DOCTYPE html>

<html lang="en">
<head>
	<title>Verifikasi Surat {{ ucwords(setting('sebutan_desa')) . ((identitas('nama_desa')) ? ' ' . identitas('nama_desa'): '') . get_dynamic_title_page_from_path(); }}</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<meta name="description" content="{{ 'Verifikasi Surat '.ucwords(setting('sebutan_desa') . ' ' . identitas('nama_desa')).' '.ucwords(setting('sebutan_kecamatan') . ' ' . identitas('nama_kecamatan')).' '.ucwords(setting('sebutan_kabupaten') . ' ' . identitas('nama_kabupaten')).' Propinsi '.ucwords(identitas('nama_propinsi'));}}">
	<meta property='og:url' content="{{ site_url(); }}" />
	<meta property="og:title" content='{{ "Surat " . $surat->perihal." a.n. " . $surat->nama_penduduk ?? $surat->nama_non_warga; }}'/>
	<meta property='og:description' content="{{ 'Verifikasi Surat '.ucwords(setting('sebutan_desa') . ' ' . identitas('nama_desa')).' '.ucwords(setting('sebutan_kecamatan') . ' ' . identitas('nama_kecamatan')).' '.ucwords(setting('sebutan_kabupaten') . ' ' . identitas('nama_kabupaten')).' Propinsi '.ucwords(identitas('nama_propinsi'));}}" />
	<meta property="og:image" content="{{ gambar_desa(identitas('logo')); }}"/>
	@if(is_file(LOKASI_LOGO_DESA . "favicon.ico"))
		<link rel="icon" type="image/x-icon" href="{{ base_url()}}{{LOKASI_LOGO_DESA}}favicon.ico" />
	@else
		<link rel="icon" type="image/x-icon" href="{{ base_url()}}favicon.ico" />
	@endif
	<link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
	<link href='{{ theme_asset("css/styleWebFont.css"); }}' rel="stylesheet" type="text/css">
	<link href='{{ theme_asset("css/style.bundle.css"); }}' rel="stylesheet" type="text/css">
	<style>
		.message-box {
			text-align: center;
			padding: 1.5rem;
			margin: 1.5rem auto;
			border-radius: 8px;
			max-width: 600px;
			font-size: 16px;
			line-height: 1.6;
		}
		.m-portlet {
			box-shadow: none !important;
		}
		.message-loading {
			color: #333;
			background-color: #f8f9fa;
			border: 1px solid #e2e3e5;
		}
		.message-error {
			color: #842029;
			background-color: #f8d7da;
			border: 1px solid #f5c2c7;
		}
		.spinner {
			display: inline-block;
			width: 1.2rem;
			height: 1.2rem;
			border: 2px solid #3d3b56;
			border-top-color: transparent;
			border-radius: 50%;
			margin-right: 0.5rem;
			animation: spin 0.8s linear infinite;
			vertical-align: middle;
		}
		@keyframes spin { to { transform: rotate(360deg); } }

		/* Modal Popup */
		.modal-overlay {
			display: none;
			position: fixed;
			top: 0; left: 0;
			width: 100%; height: 100%;
			background: rgba(0,0,0,0.6);
			z-index: 9999;
			justify-content: center;
			align-items: center;
		}
		.modal-content {
			background: white;
			border-radius: 8px;
			width: 90%;
			max-width: 900px;
			padding: 1rem;
			box-shadow: 0 2px 10px rgba(0,0,0,0.3);
			position: relative;
			text-align: center;
		}
		.modal-close {
			font-size: 24px;
			color: #333;
			cursor: pointer;
			font-weight: bold;
		}
		iframe.pdf-frame {
			width: 100%;
			height: 80vh;
			border: none;
			border-radius: 5px;
		}
	</style>
</head>
<body class="m-page--wide m-header--fixed m-header--fixed-mobile m-footer--push m-aside--offcanvas-default m-header--minimize-off">
	<div id="info_bsre" class="m-content list_data">
		<div class="m-portlet m--margin-top-0 m--margin-bottom-10">
			<div class="m-portlet__head" style="background-color:#3d3b56;">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text" style="color:#FFF">VERIFIKASI SURAT</h3>
					</div>
				</div>
			</div>
			<div class="text-break">
				<div class="m-invoice-2">
					<div class="m-invoice__wrapper">
						<div class="m-invoice__head">
							<div class="m-invoice__container m-invoice__container--centered">
								<div class="m-invoice__logo" style="padding:2.5rem 0 2.5rem; text-align:center">
									<div style="padding:0 0 1.5rem 0;">
										<img src="{{ gambar_desa(identitas('logo')); }}" style="margin: 0px; max-height: 100px; vertical-align: top;" alt="">
									</div>
									<span class="m-invoice__subtitle" style="font-weight: bold;">
										Pemerintah {{ ucwords(setting('sebutan_kabupaten') . ' ' . identitas('nama_kabupaten')); }}<br/>
										{{ ucwords(setting('sebutan_kecamatan') . ' ' . identitas('nama_kecamatan')); }}<br/>
										{{ ucwords(setting('sebutan_desa') . ' ' . identitas('nama_desa')); }}
									</span>
								</div>
								<div id="message"></div>
								@if (theme_config('lampiran_surat_layanan', true))
								<div style="text-align:center; margin-top:1rem;">
									<button id="toggle-pdf" class="btn btn-sm bg-color2" style="display:none;">Buka PDF</button>
								</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="pdfModal" class="modal-overlay">
		<div class="modal-content">
			<span class="modal-close">&times;</span>
			<iframe id="pdf-viewer" class="pdf-frame"></iframe>
		</div>
	</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
	const messageEl = document.getElementById('message');
	const pdfViewer = document.getElementById('pdf-viewer');
	const toggleBtn = document.getElementById('toggle-pdf');
	const modal = document.getElementById('pdfModal');
	const closeModal = document.querySelector('.modal-close');

	const loadingHTML = `<div class="message-box message-loading"><span class="spinner"></span>Harap tunggu, sedang memproses data...</div>`;
	const notFoundHTML = `<div class="message-box message-error">Surat tidak ditemukan dalam sistem.</div>`;

	messageEl.innerHTML = loadingHTML;

	fetch("{{ route('api.verifikasi-surat') }}?filter[id]={{ $id }}")
	.then(r => {
		if (!r.ok) throw new Error("Response not OK");
		return r.json();
	})
	.then(response => {
		if (response.data && response.data.length > 0) {
			const _surat = response.data[0].attributes;
			let _html = `
				<div style="vertical-align:middle; font-size:16px;">
					<strong style="color:#333399">Info Dokumen</strong>
				</div>
				<div class="m-invoice__items" style="padding:0.5rem 0 2.5rem 0">
					<div class="m-invoice__item"><span class="m-invoice__subtitle">Nomor Surat</span>
					<span class="m-invoice__text">${_surat.nomor_surat}</span></div>
					<div class="m-invoice__item"><span class="m-invoice__subtitle">Tanggal Surat</span>
					<span class="m-invoice__text">${_surat.tanggal}</span></div>`;
			if (_surat.nama_penduduk) {
				_html += `
					<div class="m-invoice__item">
						<span class="m-invoice__subtitle">Nama Pemohon</span>
						<span class="m-invoice__text">a.n. ${_surat.nama_penduduk}</span>
					</div>`;
			}
			_html += `
					<div class="m-invoice__item">
						<span class="m-invoice__subtitle">Perihal</span>
						<span class="m-invoice__text">Surat ${_surat.perihal}</span>
					</div>
				</div>
				<div style="vertical-align:middle; font-size:16px">
					<strong style="color:#333399">Penandatangan</strong>
				</div>
				<div class="m-invoice__items" style="padding:0.5rem 0 1.5rem 0">
					<div class="m-invoice__item"><span class="m-invoice__subtitle">Nama</span>
					<span class="m-invoice__text">${_surat.pamong_nama}</span></div>
					<div class="m-invoice__item"><span class="m-invoice__subtitle">Jabatan</span>
					<span class="m-invoice__text">${_surat.pamong_jabatan}</span></div>
					<div class="m-invoice__item"><span class="m-invoice__subtitle">Pada Tanggal</span>
					<span class="m-invoice__text">${_surat.tanggal}</span></div>
				</div>
				<div class="m-invoice__logo text-break" style="padding:1rem 0; text-align:center">
					<strong style="color:#333399">Adalah benar dan tercatat dalam database sistem informasi kami.</strong>
					<div style="padding:1rem 0 1.0rem 0">
						Untuk memastikan kebenaran informasi ini pastikan URL pada browser anda adalah 
						<mark>{{ identitas('website') }}</mark>
					</div>
				</div>`;
			messageEl.innerHTML = _html;

			@if (theme_config('lampiran_surat_layanan', true))
			if (_surat.pdf) {
				toggleBtn.style.display = 'inline-block';
				toggleBtn.onclick = function() {
					pdfViewer.src = `data:application/pdf;base64,${_surat.pdf}`;
					modal.style.display = 'flex';
				}
			}
			@endif
		} else {
			messageEl.innerHTML = notFoundHTML;
			if (toggleBtn) toggleBtn.style.display = 'none';
		}
	})
	.catch(err => {
		console.error(err);
		messageEl.innerHTML = notFoundHTML;
		if (toggleBtn) toggleBtn.style.display = 'none';
	});

	if (closeModal) closeModal.onclick = () => { modal.style.display = 'none'; pdfViewer.src = ''; };
	window.onclick = (e) => { if (e.target === modal) { modal.style.display = 'none'; pdfViewer.src = ''; } };
});
</script>
</body>
</html>