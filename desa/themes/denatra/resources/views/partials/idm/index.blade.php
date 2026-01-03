@extends('theme::layouts.full-content')
@includeIf('theme::commons.asset_highcharts')

@push('style')
<style>
	.small-box {
		border-radius: 5px;
		position: relative;
		display: block;
		margin-bottom: 10px;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
	}

	.small-box>.inner {
		padding: 10px;
	}

	.small-box>.small-box-footer {
		position: relative;
		text-align: center;
		padding: 3px 0;
		color: #fff;
		color: rgba(255, 255, 255, 0.8);
		display: block;
		z-index: 10;
		background: rgba(0, 0, 0, 0.1);
		text-decoration: none;
	}

	.small-box>.small-box-footer:hover {
		color: #fff;
		background: rgba(0, 0, 0, 0.15);
	}

	.small-box h3 {
		font-size: 20px;
		font-weight: bold;
		margin: 0 0 5px 0;
		white-space: nowrap;
		padding: 0;
	}

	.small-box p {
		font-size: 15px;
	}

	.small-box p>small {
		display: block;
		color: #6f00ff;
		font-size: 13px;
		margin-top: 5px;
	}

	.small-box h3,
	.small-box p {
		z-index: 5;
	}

	.small-box .icon {
		-webkit-transition: all 0.3s linear;
		-o-transition: all 0.3s linear;
		transition: all 0.3s linear;
		position: absolute;
		top: 0px;
		right: 10px;
		z-index: 0;
		font-size: 90px;
		color: rgba(0, 0, 0, 0.15);
	}

	.small-box:hover {
		text-decoration: none;
		color: #6f00ff;
	}

	.small-box:hover .icon {
		font-size: 95px;
	}

	@media (max-width: 767px) {
		.small-box {
			text-align: center;
		}

		.small-box .icon {
			display: none;
		}

		.small-box p {
			font-size: 12px;
		}
	}
</style>
<style>
	.table-striped>tbody>tr.judul {
		background-color: lightgrey !important;
	}

	tr.judul>td,
	tr.judul>th {
		background-color: inherit !important;
	}

	.table>thead>tr>th,
	.table>tbody>tr>th,
	.table>tfoot>tr>th,
	.table>thead>tr>td,
	.table>tbody>tr>td,
	.table>tfoot>tr>td {
		font-size: 12px;
		padding: 5px;
	}

	@media (max-width:780px) {
		.btn-group-vertical {
			display: block;
		}
	}

	.table-responsive {
		min-height: 275px;
	}

	#container {
		height: 400px;
	}

	.highcharts-figure,
	.highcharts-data-table table {
		min-width: 310px;
		max-width: 800px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #EBEBEB;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}

	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}

	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
		padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}
</style>
<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/ionicons.min.css') }}">
@endpush
@section('content')
<div class="has-background-img mb-1">
	<div class="row">
		<div class="col-sm-12">
			<div class="card mb-0 fullscreen">
				<div class="card-header border-bottom">
					<div class="media">
						<div class="icon-circle icon-40 bg-light-primary mr-3">
							<i class="material-icons">map</i>
						</div>
						<div class="media-body">
							<h6 class="my-0 content-color-primary">Status IDM {{$tahun}}</h6>
							<p class="small mb-0">
								<i class="material-icons icon-sm">date_range</i> {{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}
							</p>
						</div>
						<a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
							<i class="material-icons ">crop_free</i>
						</a>
					</div>
				</div>
				<div id="status-error" style="display: none;">
					<div class="card-body">
						<div class="row">
							<div class="col-sm-12">
								<div class="alert alert-danger">
									<p id="error-message"></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="loading-idm" style="display:none; text-align:center; padding:10px;">
					<span>Memuat data IDM...</span>
				</div>


				<div class="card-body" id="data-idm" style="display:none">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<div class="col-6 status-idm-kiri">
													<div class="small-box primary-gradient">
														<div class="inner">
															<h3 id="skor-saat-ini"></h3>
															<p>SKOR IDM SAAT INI</p>
														</div>
														<div class="icon">
															<i class="ion ion-stats-bars"></i>
														</div>
													</div>
												</div>
												<div class="col-6  status-idm-kiri status-idm-kanan">
													<div class="small-box success-gradient">
														<div class="inner">
															<h3 id="status-saat-ini"></h3>
															<p>STATUS IDM</p>
														</div>
														<div class="icon">
															<i class="ion-ios-pulse-strong"></i>
														</div>
													</div>
												</div>
												<div class="col-6 status-idm-kanan">
													<div class="small-box danger-gradient">
														<div class="inner">
															<h3 id="skor-minimal"></h3>
															<p>SKOR MINIMAL</p>
														</div>
														<div class="icon">
															<i class="ion ion-ios-pie"></i>
														</div>
													</div>
												</div>
												<div class="col-6 status-idm-kiri status-idm-kanan">
													<div class="small-box warning-gradient">
														<div class="inner">
															<h3 id="target-status"></h3>
															<p>TARGET STATUS</p>
														</div>
														<div class="icon">
															<i class="ion ion-arrow-graph-up-right"></i>
														</div>
													</div>
												</div>
											</div>
											<!-- Tabel Data -->
											<table class="table table-bordered table-striped dataTable table-hover">
												<tbody>
													<tr>
														<th class="horizontal">TAHUN</th>
														<td> : {{$tahun}}</td>
													</tr>
													<tr>
														<th class="horizontal">{{ ucwords(setting('sebutan_desa')) }}</th>
														<td id="nama-desa"> : </td>
													</tr>
													<tr>
														<th class="horizontal">{{ ucwords(setting('sebutan_kecamatan')) }}</th>
														<td id="nama-kecamatan"></td>
													</tr>
													<tr>
														<th class="horizontal">KABUPATEN</th>
														<td nowrap id="nama-kabupaten"></td>
													</tr>
													<tr>
														<th class="horizontal">PROVINSI</th>
														<td id="nama-provinsi"></td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="col-md-6">
											<figure class="highcharts-figure">
												<div id="container"></div>
											</figure>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="table-responsive">
								<table class="table table-bordered table-striped dataTable table-hover" id="tabel-daftar">
									<thead class="bg-gray color-palette">
										<tr>
											<th rowspan="2" class="padat">NO</th>
											<th rowspan="2">INDIKATOR IDM</th>
											<th rowspan="2">SKOR</th>
											<th rowspan="2">KETERANGAN</th>
											<th rowspan="2" nowrap>KEGIATAN YANG DAPAT DILAKUKAN</th>
											<th rowspan="2">+NILAI</th>
											<th colspan="6" class="text-center">YANG DAPAT MELAKSANAKAN KEGIATAN</th>
										</tr>
										<tr>
											<th>PUSAT</th>
											<th>PROVINSI</th>
											<th>KABUPATEN</th>
											<th>DESA</th>
											<th>CSR</th>
											<th>LAINNYA</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('script')
<script type="text/javascript">
	$(document).ready(function() {
		var tahun = '{{ $tahun }}';
		var route = "{{ route('api.idm', $tahun) }}";
		$('#loading-idm').show();
		jQuery.get(route, function(data) {

			if (data['error_msg']) {
				$('#status-error').show();
				$('#status-idm').hide();
				$('#error-message').text(data['error_msg']);

				return;
			}

			$('#status-idm').show();
			$('#status-error').hide();
			$('#data-idm').show();
			$('#loading-idm').hide();


			var summaries = data['data'][0]['attributes']['SUMMARIES'];
			var row = data['data'][0]['attributes']['ROW'];
			var identitas = data['data'][0]['attributes']['IDENTITAS'][0];
			var iks = parseFloat(row[35].SKOR ?? 0);
			var ike = parseFloat(row[48].SKOR ?? 0);
			var ikl = parseFloat(row[52].SKOR ?? 0);


			// Skor
			$('#skor-saat-ini').text(parseFloat(summaries.SKOR_SAAT_INI).toFixed(4));
			$('#status-saat-ini').text(summaries.STATUS);
			$('#skor-minimal').text(parseFloat(summaries.SKOR_MINIMAL).toFixed(4));
			$('#target-status').text(summaries.TARGET_STATUS);

			// Highcharts
			loadHighcharts(tahun, iks, ike, ikl);

			// Identitas
			$('#nama-provinsi').text(' : ' + (identitas.nama_provinsi || '-'));
			$('#nama-kabupaten').text(' : ' + (identitas.nama_kab_kota || '-'));
			$('#nama-kecamatan').text(' : ' + (identitas.nama_kecamatan || '-'));
			$('#nama-desa').text(' : ' + (identitas.nama_desa || '-'));


			// Tabel
			row.forEach(item => {
				var classRow = !item.NO ? 'judul' : '';
				var tr = `
		<tr class="${classRow}">
			<td class="text-center">${item.NO ?? ''}</td>
			<td style="min-width: 150px;">${item.INDIKATOR ?? ''}</td>
			<td class="padat">${item.SKOR ?? ''}</td>
			<td style="min-width: 250px;">${item.KETERANGAN ?? ''}</td>
			<td>${item.KEGIATAN ?? ''}</td>
			<td class="padat">${item.NILAI ?? ''}</td>
			<td>${item.PUSAT ?? ''}</td>
			<td>${item.PROV ?? ''}</td>
			<td>${item.KAB ?? ''}</td>
			<td>${item.DESA ?? ''}</td>
			<td>${item.CSR ?? ''}</td>
			<td>${item.LAINNYA ?? ''}</td>
		</tr>
	`;

				$('#tabel-daftar tbody').append(tr);
			});

		}).fail(function(xhr, status, error) {
			$('#status-error').show();
			$('#status-idm').hide();
			$('#error-message').text('Data IDM tahun ' + tahun + ' tidak ditemukan.');
			$('#data-idm').hide();
			$('#loading-idm').hide();

		});

		// Highcharts
		function loadHighcharts(tahun, iks, ike, ikl) {
			Highcharts.chart('container', {
				chart: {
					type: 'pie',
					options3d: {
						enabled: true,
						alpha: 45
					}
				},
				title: {
					text: 'Indeks Desa Membangun (IDM) ' + tahun
				},
				subtitle: {
					text: 'SKOR : IKS, IKE, IKL'
				},
				plotOptions: {
					series: {
						colorByPoint: true
					},
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						showInLegend: true,
						depth: 45,
						innerSize: 70,
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.y:,.2f} / {point.percentage:.1f} %'
						}
					}
				},
				series: [{
					name: 'SKOR',
					shadow: 1,
					border: 1,
					data: [
						['IKS', parseFloat(iks)],
						['IKE', parseFloat(ike)],
						['IKL', parseFloat(ikl)]
					]
				}]
			});
		}
	});
</script>
@endpush