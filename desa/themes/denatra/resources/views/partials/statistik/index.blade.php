@extends('theme::layouts.full-content')

@includeIf('theme::commons.constant')

@section('content')	
<script type="text/javascript">
	let chart;
	const type = '{{ $default_chart_type ?? 'pie' }}';
	const legend = Boolean({{ (bool)$tipe }});
	let categories = [];
	let data = [];
	let index = 1;  // Ganti nama variabel 'i' menjadi 'index' untuk menghindari konflik
	let status_tampilkan = true;

	function tampilkan_nol(tampilkan = false) {
		if (tampilkan) {
			$(".nol").parent().show();
		} else {
			$(".nol").parent().hide();
		}
	}

	// Toggle visibilitas baris dengan nilai nol
	function toggle_tampilkan() {
		$('#showData').click();
		tampilkan_nol(status_tampilkan);
		status_tampilkan = !status_tampilkan;
		$('#tampilkan').text(status_tampilkan ? 'Tampilkan Nol' : 'Sembunyikan Nol');
	}

	// Ganti tipe chart antara pie dan column
	function switchType(obj){
		const chartType = chart.series[0].type;
		chart.series[0].update({
			type: chartType === 'pie' ? 'column' : 'pie'
		});
		$(obj).toggleClass('btn-primary btn-default')
		$(obj).siblings().toggleClass('btn-primary btn-default')
	}

	// Inisialisasi chart
	$(document).ready(function () {
		tampilkan_nol(false);

		const chartOptions = {
			chart: {
				renderTo: 'container'
			},
			title: {
				text: null
			},
			yAxis: {
				showEmpty: false
			},
			xAxis: {
				categories: categories
			},
			plotOptions: {
				series: {
					colorByPoint: true
				},
				column: {
					pointPadding: -0.1,
					borderWidth: 0,
					showInLegend: false
				},
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					showInLegend: true,
					innerSize: 70
				}
			},
			legend: {
				enabled: legend
			},
			series: [{
				type: type,
				name: 'Jumlah Populasi',
				shadow: true,
				borderWidth: 1,
				data: data
			}]
		};

		if ({{ setting('statistik_chart_3d') }}) {
			chartOptions.chart.options3d = {
				enabled: true,
				alpha: 45
			};
			chartOptions.plotOptions.column.depth = 45;
			chartOptions.plotOptions.pie.depth = 45;
		}

		chart = new Highcharts.Chart(chartOptions);

		// Tampilkan data tambahan ketika tombol diklik
		$('#showData').click(function () {
			$('tr.lebih').show();
			$('#showData').hide();
			tampilkan_nol(false);
		});

		let dataStats = [];

		$.ajax({
			url: "{{ ci_route('internal_api.statistik', $key) }}?tahun={{ $selected_tahun ?? '' }}",
			method: 'get',
			data: {},
			beforeSend: function() {
				$('#showData').hide()
			},
			success: function(json) {
				
				dataStats = json.data.map(item => {
					const {
						id
					} = item;
					const {
						nama,
						jumlah,
						laki,
						perempuan,
						persen,
						persen1,
						persen2
					} = item.attributes;
					return {
						id,
						nama,
						jumlah,
						persen,
						laki,
						persen1,
						perempuan,
						persen2
					};
				});

				const table = document.getElementById('table-statistik')
				const tbody = table.querySelector('tbody')
				let _showBtnSelengkapnya = false
				let categories = [],
				data = []
				// Populate table rows
				dataStats.forEach((item, index) => {
					const row = document.createElement('tr');
					if (index > 11 && !['666', '777', '888'].includes(item['id'])) {
						row.className = 'lebih';
						_showBtnSelengkapnya = true
					}
					for (let key in item) {
						const cell = document.createElement('td');
						let text = item[key]
						let className = 'angka'
						if (key == 'id') {
							className = ''
							text = index + 1
							if (['666', '777', '888'].includes(item[key])) {
								text = ''
							}
						}
						if (key == 'nama') {
							className = ''
						}
						if (key == 'jumlah' && item[key] <= 0) {
							if (!['666', '777', '888'].includes(item['id'])) {
								className += ' nol'
							}

						}
						cell.className = className
						cell.textContent = text;
						row.appendChild(cell);
					}

					tbody.appendChild(row);
				});

				tampilkan_nol(false);

				if (_showBtnSelengkapnya) {
					$('#showData').show()
				}

				for (const stat of dataStats) {
					if (stat.nama !== 'TOTAL' && stat.nama !== 'JUMLAH' && stat.nama != 'PENERIMA') {
						let filteredData = [stat.nama, parseInt(stat.jumlah)];
						categories.push(index);
						data.push(filteredData);
						index++;
					}
				}

				chart.xAxis[0].update({
					categories: categories
				});

				chart.series[0].setData(data);
			},
		})
	});
</script>
<style>
	tr.lebih {
		display: none;
	}
	
	.input-sm {
		padding: 4px 4px;
	}

	@media (max-width:780px) {
		.btn-group-vertical {
			display: block;
		}
	}

	.table-responsive {
		min-height:275px;
	}
</style>
<div class="row">
    <div class="col-md-4 col-lg-4 text-hide-xs text-hide-sm">
        @includeIf('theme::partials.kependudukan.navigasi')
    </div>
	<div class="col-md-8 col-lg-8">
		<div class="card mb-1">
			<div id="accordiongrafik">
				<div class="card">
					<button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#collapseFourgrafik" aria-expanded="true" aria-controls="collapseFourgrafik">
						<div class="card-header bg-primary font-weight-bold" style="text-align:left" id="headingFourgrafik">{{ $link ? 'Sembunyikan' : 'Lihat' }} Grafik <i class="material-icons icon arrow">expand_more</i></div>
					</button>
					<div id="collapseFourgrafik" class="collapse {{ $link ? 'show' : '' }}" aria-labelledby="headingFourgrafik" data-parent="#accordiongrafik">
					<div class="card-header border-bottom">
						<div class="media">
							<div class="icon-circle icon-40 bg-light-primary mr-3">
								<i class="material-icons">assessment</i>
							</div>
							<div class="media-body">
								<h6 class="my-0 content-color-primary">Grafik Data {{ $heading }}<br>Tahun {{ date('Y') }}</h6>
								<p class="small mb-0">
									<i class="material-icons icon-sm">local_offer</i> {{ ucwords(setting('sebutan_desa')) . " " . $desa['nama_desa'] }}
								</p>
							</div>
							<a class="btn btn-primary btn-xs text-white" onclick="switchType();">Ubah Grafik</a>
						</div>
					</div>
					<div class="card-body">
						<div class="mb-0 content-color-secondary">
							<div id="container"></div>
							<div id="contentpane">
								<div class="ui-layout-north panel top"></div>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card mb-1 fullscreen has-background-img ">
			<div id="accordiontabel">
				<div class="card">
					<button class="btn btn-link text-white p-0" data-toggle="collapse" data-target="#collapseFourtabel" aria-expanded="true" aria-controls="collapseFourtabel">
						<div class="card-header bg-primary font-weight-bold" style="text-align:left" id="headingFourtabel">Sembunyikan Tabel <i class="material-icons icon arrow">expand_more</i></div>
					</button>
					<div id="collapseFourtabel" class="collapse show" aria-labelledby="headingFourtabel" data-parent="#accordiontabel">
						<div class="card-header border-bottom">
							<div class="media">
								<div class="icon-circle icon-40 bg-light-primary mr-3">
									<i class="material-icons">view_day</i>
								</div>
								<div class="media-body">
									<h6 class="my-0 content-color-primary">{{ $judul }}</h6>
									@if(isset($list_tahun))
										<form method="get" class="form-inline text-center">
											<select class="form-control input-sm select2" id="tahun" name="tahun">
												<option selected value="">Semua Tahun</option>
												@foreach ($list_tahun as $item_tahun)
													<option {{ $item_tahun == ($selected_tahun ?? null) ? 'selected' : '' }} value="{{ $item_tahun }}">
														{{ $item_tahun }}
													</option>
												@endforeach
											</select>
										</form>
									@endif
								</div>
								<a href="javascript:void(0);" class="icon-circle icon-30 content-color-secondary fullscreenbtn">
									<i class="material-icons">crop_free</i>
								</a>
								<div class="text-hide-xs">
								<a href="{{ site_url("data-statistik/{$slug_aktif}/cetak/cetak") }}?tahun={{ $selected_tahun }}" class="btn btn-primary btn-xs text-white ml-1" title="Cetak Laporan" target="_blank">
									<i class="fa fa-print"></i> Cetak
								</a>
								<a href="{{ site_url("data-statistik/{$slug_aktif}/cetak/unduh") }}?tahun={{ $selected_tahun }}" class="btn btn-success btn-xs text-white ml-1" title="Unduh Laporan" target="_blank">
									<i class="fa fa-print"></i> Unduh
								</a>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="mb-0 content-color-secondary">
								<div class="table-responsive">
									<table class="table table-striped" id="table-statistik">
										<thead>
										<tr>
											<th rowspan="2">No</th>
											<th rowspan="2" style='text-align:left;'>Kelompok</th>
											<th colspan="2" style='text-align:center'>Jumlah</th>
											<th colspan="2" style='text-align:center'>Laki-laki</th>
											<th colspan="2" style='text-align:center'>Perempuan</th>
										</tr>
										<tr>
											<th style='text-align:center'>Jiwa</th><th style='text-align:center'>%</th>
											<th style='text-align:center'>Jiwa</th><th style='text-align:center'>%</th>
											<th style='text-align:center'>Jiwa</th><th style='text-align:center'>%</th>
										</tr>
										</thead>
										<tbody>
											
										</tbody>
									</table>
									@if($hide == "lebih")
										<div style='float: left; margin-right: 10px;'>
											<button class='btn btn-sm btn-purple {{ theme_config('color', 'pink') }}-gradient' id='showData'>Selengkapnya...</button>
										</div>
									@endif
									<i class="fa fa-calendar" aria-hidden="true"></i> Diperbarui : {{ tgl_indo2($last_update) }}
									<div style="float: right;">
										<button id='tampilkan' onclick="toggle_tampilkan();" class="btn btn-sm btn-purple {{ theme_config('color', 'pink') }}-gradient">Tampilkan Nol</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		@if (setting('daftar_penerima_bantuan') && $bantuan)
		<section class="content" id="maincontent">
			<div class="card mb-1 fullscreen has-background-img ">
				<input id="stat" type="hidden" value="{{ $st }}">
				<div class="card-header border-bottom">
					<div class="media">
						<div class="icon-circle icon-40 bg-light-primary mr-3">
							<i class="material-icons">view_day</i>
						</div>
						<div class="media-body">
							<h6 class="my-0 content-color-primary">Daftar Penerima {{ $heading }}</h6>
							<p class="small mb-0">
								<i class="material-icons icon-sm">local_offer</i> {{ ucwords(setting('sebutan_desa')) . " " . $desa['nama_desa'] }}
							</p>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="mb-0 content-color-secondary">
						<div class="table-responsive">
							<table class="table table-striped table-bordered" id="peserta_program">
								<thead>
									<tr>
										<th>No</th>
										<th>Program</th>
										<th>Nama Peserta</th>
										<th>Alamat</th>
									</tr>
								</thead>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</section>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#tahun').change(function(){
					const current_url = window.location.href.split('?')[0];
					window.location.href = `${current_url}?tahun=${$(this).val()}`;
				});
				const bantuanUrl = '{{ ci_route('internal_api.peserta_bantuan', $key) }}?filter[tahun]={{ $selected_tahun ?? '' }}'
				let pesertaDatatable = $('#peserta_program').DataTable({
					processing: true,
					serverSide: true,
					order: [],
					ajax: {
						url: bantuanUrl,
						type: 'GET',
						data: function(row) {
							return {
								"page[size]": row.length,
								"page[number]": (row.start / row.length) + 1,
								"filter[search]": row.search.value,
								"sort": (row.order[0]?.dir === "asc" ? "" : "-") + row.columns[row.order[0]?.column]?.name,
							};
						},
						dataSrc: function(json) {
							json.recordsTotal = json.meta.pagination.total
							json.recordsFiltered = json.meta.pagination.total

							return json.data
						},
					},
					columns: [{
							data: null,
						},
						{
							data: 'attributes.nama',
							name: 'nama'
						},
						{
							data: 'attributes.kartu_nama',
							name: 'kartu_nama'
						},
						{
							data: 'attributes.kartu_alamat',
							name: 'kartu_alamat',
							orderable: false,
							searchable: false
						},
					],
					order: [1, 'asc'],
					language: {
						url: "".concat(BASE_URL, "/assets/bootstrap/js/dataTables.indonesian.lang")
					},
					drawCallback: function drawCallback() {
						$('.dataTables_paginate > .pagination').addClass('pagination-sm no-margin');
					}
				});

				pesertaDatatable.on('draw.dt', function() {
					var PageInfo = $('#peserta_program').DataTable().page.info();
					pesertaDatatable.column(0, {
						page: 'current'
					}).nodes().each(function(cell, i) {
						cell.innerHTML = i + 1 + PageInfo.start;
					});
				});
			});
		</script>
		@endif
	</div>
    <div class="col-md-4 col-lg-4 text-hide-lg">
        @includeIf('theme::partials.kependudukan.navigasi')
    </div>
</div>
@endsection