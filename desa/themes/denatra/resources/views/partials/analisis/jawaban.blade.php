@extends('theme::layouts.full-content')
@section('content')

<style>
    tr#total {
        background: #fffdc5;
        font-size: 12px;
        white-space: nowrap;
        font-weight: bold;
    }

    h3 {
        margin-left: 10px;
    }
</style>

<div class="card mb-1">
    <div class="card">
        <div class="card-header border-bottom">
            <div class="media">
                <div class="icon-circle icon-40 bg-light-primary mr-3">
                    <i class="material-icons">assessment</i>
                </div>
                <div class="media-body">
                    <h6 class="my-0 content-color-primary" id="indikator">
                    </h6>
                    <p class="small mb-0">
                        <i class="material-icons icon-sm">local_offer</i> Pertanyaan/Indikator
                    </p>
                </div>
                <a class="btn btn-sm btn-purple {{ theme_config('color', 'pink') }}-gradient"
                    href="{{ ci_route('data_analisis') }}" style="float: right;">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-0 content-color-secondary">
                <div class="ui-layout-center" id="chart" style="padding: 5px;"></div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-0 content-color-secondary">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-jawaban">
                        <thead>
                            <tr>
                                <td width='3%' style='text-align:center'><b>No.</b></td>
                                <td style='text-align:center'><b>Jawaban</b></td>
                                <td width='20%' style='text-align:center'><b>Jumlah Responden</b></td>
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

@endsection

@push('script')
<script>
    $.get("{{ route('api.analisis.indikator') . '?filter[id]=' . $params['filter']['id_indikator'] }}", function(data) {
        const indikator = data?.data[0]?.attributes?.indikator;

        // Set the text dynamically
        $('#indikator').text(indikator);

        // Initialize the Highcharts with the fetched indikator value
        printChart(indikator);
    });

    var tabelData = $('#table-jawaban').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ordering: false,
        searching: false,
        ajax: {
            url: '{{ route('api.analisis.jawaban') }}',
            method: 'GET',
            data: row => ({
                ...@json($params),
                "page[size]": row.length,
                "page[number]": (row.start / row.length) + 1,
            }),
            dataSrc: json => {
                json.recordsTotal = json.meta.pagination.total;
                json.recordsFiltered = json.meta.pagination.total;
                return json.data;
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
            }
        },
        columnDefs: [{
            targets: '_all',
            className: 'text-nowrap'
        }],
        columns: [{
                data: null,
                searchable: false,
                orderable: false
            },
            {
                data: 'attributes.jawaban',
            },
            {
                data: 'attributes.jml',
            }
        ],
        drawCallback: function(settings) {
            var api = this.api();

            // Update row numbering
            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = api.page.info().start + i + 1;
            });

            // Extract data for the chart
            var chartCategories = [];
            var chartData = [];

            api.rows().data().each(function(row) {
                chartCategories.push(row.attributes.jawaban); // Add the "jawaban" as category
                chartData.push(row.attributes.jml); // Add the "jml" as data
            });

            // Update the chart with new data
            updateChart(chartCategories, chartData);
        }
    });

    printChart();

    function printChart(indikator) {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'chart',
                border: 0,
                defaultSeriesType: 'column'
            },
            title: {
                text: indikator
            },
            xAxis: {
                title: {
                    text: ''
                },
                categories: []
            },
            yAxis: {
                title: {
                    text: 'Jumlah Populasi'
                }
            },
            legend: {
                layout: 'vertical',
                enabled: false
            },
            plotOptions: {
                series: {
                    colorByPoint: true
                },
                column: {
                    pointPadding: 0,
                    borderWidth: 0
                }
            },
            series: [{
                shadow: 1,
                border: 0,
                data: []
            }]
        });
    }

    function updateChart(categories, data) {
        // Update the categories and data in the chart
        chart.xAxis[0].setCategories(categories);
        chart.series[0].setData(data);
    }
</script>
@endpush

@push('script')
<script type="text/javascript">
$(document).ready(function() {
    hiRes();
});

var chart;
function hiRes() {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'chart',
            border:0,
            defaultSeriesType: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            title: {
                text:''
            },
            categories: [
                @foreach($list_jawab as $data)
                    @if($data['nilai'] != '-')
                        '{{ $data['jawaban'] }}',
                    @endif
                @endforeach
            ]
        },
        yAxis: {
            title: {
                text: 'Jumlah Populasi'
            }
        },
        legend: {
            layout: 'vertical',
            enabled:false
        },
        plotOptions: {
            series: {
                colorByPoint: true
            },
            column: {
                pointPadding: 0,
                borderWidth: 0
            }
        },
        series: [{
            shadow:1,
            border:0,
            data: [
                @foreach($list_jawab as $data)
                    @if($data['jawaban'] != 'TOTAL' && $data['nilai'] != '-')
                        {{ $data['nilai'] }},
                    @endif
                @endforeach
            ]
        }]
    });
};
</script>
@endpush
