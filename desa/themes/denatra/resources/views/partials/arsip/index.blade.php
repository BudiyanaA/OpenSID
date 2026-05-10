@extends('theme::layouts.full-content')
@section('content')

<div class="card mb-2 fullscreen has-background-img ">
	<div class="card-header border-bottom">
		<div class="media">
			<div class="icon-circle icon-40 bg-light-primary mr-3">
				<i class="material-icons">folder</i>
			</div>
			<div class="media-body">
				<h6 class="my-0 content-color-primary">Arsip</h6>
				<p class="small mb-0">
					<i class="material-icons icon-sm">date_range</i> {{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}
				</p>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="mb-0 content-color-secondary">
			<div class="table-responsive">
				<table class="table table-striped" id="dataTabel">
					<thead>
						<tr>
							<td width="3%"><b>No.</b></td>
							<td width="20%"><b>Tanggal Artikel</b></td>
							<td><b>Judul Artikel</b></td>
							<td width="20%"><b>Penulis</b></td>
							<td width="10%"><b>Dibaca</b></td>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@push('script')
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		var arsip = $('#dataTabel').DataTable({
			processing: true,
			serverSide: true,
			autoWidth: false,
			ordering: true,
			ajax: {
				url: `{{ ci_route('internal_api.arsip') }}`,
				method: 'get',
				data: function(row) {
					return {
						"page[size]": row.length,
						"page[number]": (row.start / row.length) + 1,
						"filter[search]": row.search.value,
						"sort": (row.order[0]?.dir === "asc" ? "" : "-") + row.columns[row.order[0]?.column]
							?.name,
					};
				},
				dataSrc: function(json) {
					json.recordsTotal = json.meta.pagination.total
					json.recordsFiltered = json.meta.pagination.total

					return json.data
				},
			},
			columnDefs: [{
				targets: '_all',
				className: 'text-nowrap',
			}, ],
			columns: [{
					data: null,
					orderable: false
				},
				{
					data: "attributes.tgl_upload_local",
					name: "tgl_upload",
				},
				{
					data: function(data) {
						return `<a href="${data.attributes.url_slug}">${data.attributes.judul}</a>`
					},
					class: 'text-wrap',
					name: "judul",
					orderable: false
				},

				{
					data: "attributes.author.nama",
					name: "owner",
				},
				{
					data: "attributes.hit",
					name: "hit",
					searchable: false,
				},
			],
			order: [
				[1, 'desc']
			]
		})

		arsip.on('draw.dt', function() {
			var PageInfo = $('#dataTabel').DataTable().page.info();
			arsip.column(0, {
				page: 'current'
			}).nodes().each(function(cell, i) {
				cell.innerHTML = i + 1 + PageInfo.start;
			});
		});
	});
</script>
@endpush