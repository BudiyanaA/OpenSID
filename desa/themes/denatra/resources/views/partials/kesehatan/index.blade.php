@extends('theme::layouts.full-content')
@includeIf('theme::commons.constant')

@section('content')

<div class="card mt-0 mb-1 has-background-img">
  <div class="card-header border-bottom">
    <div class="media">
      <div class="icon-circle icon-40 bg-light-primary mr-3">
        <i class="material-icons icon-sm">assessment</i>
      </div>
      <div class="media-body">
        <h6 class="my-0 content-color-primary">Data Convergensi Stunting</h6>
        <p class="small mb-0">
          <i class="material-icons icon-sm">date_range</i>
          {{ ucwords(setting('sebutan_desa')) . ' ' . $desa['nama_desa'] }}
        </p>
      </div>
      <a href="{{ site_url('cetak/cetak?kuartal=' . $kuartal . '&tahun=' . $_tahun . '&id=' . $id) }}"
        class="btn btn-info visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"
        style="margin: 3px; float: right;" target="_blank">
        <i class="fa fa-print"></i> Cetak
      </a>
      <a href="{{ site_url('cetak/unduh?kuartal=' . $kuartal . '&tahun=' . $_tahun . '&id=' . $id) }}"
        class="btn btn-primary visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"
        style="margin: 3px; float: right;" target="_blank">
        <i class="fa fa-download"></i> Unduh
      </a>
    </div>
  </div>
  <form method="get" action="" class="form-inline text-center">
    <select class="form-control input-sm select2 ml-1 mr-1 mb-1 mt-1" id="kuartal" name="kuartal">
      <option selected value="">Pilih salah satu</option>
      @foreach (kuartal2() as $item)
      <option value="{{ $item['ke'] }}" @selected($item['ke']==$kuartal)>
        Kuartal ke {{ $item['ke'] }} ({{ $item['bulan'] }})
      </option>
      @endforeach
    </select>
    <select class="form-control input-sm select2 ml-1 mr-1 mb-1 mt-1" id="tahun" name="tahun">
      <option selected value="">Tahun</option>
      @foreach ($dataTahun as $item)
      <option value="{{ $item->tahun }}" @selected($item->tahun == $_tahun)>{{ $item->tahun }}</option>
      @endforeach
    </select>
    <select class="form-control input-sm select2 ml-1 mr-1 mb-1 mt-1" name="id_posyandu" id="id_posyandu">
      <option selected value="">Posyandu</option>
      @foreach ($posyandu as $item)
      <option value="{{ $item->id }}" @selected($item->id == $idPosyandu)>
        {{ $item->nama }}
      </option>
      @endforeach
    </select>
    <div class="input-group">
      <button type="submit" class="btn btn-info" id="cari">
        <i class="fa fa-search"></i> Cari
      </button>
    </div>
  </form>
  <div class="box-body text-sm py-2 space-y-4" id="stunting-list">
  </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(event) {
        const tahun = document.getElementById('tahun').value
        const kuartal = document.getElementById('kuartal').value
        const idPosyandu = document.getElementById('id_posyandu').value
        const widgetTemplate = `@includeIf('theme::partials.kesehatan.widget_item')`
        const templateStunting = document.createElement('template')
        templateStunting.innerHTML = `@includeIf('theme::partials.kesehatan.chart_stunting_umur')`
        const stuntingUmurNode = templateStunting.content.firstElementChild
        const templatePosyandu = document.createElement('template')
        templatePosyandu.innerHTML = `@includeIf('theme::partials.kesehatan.chart_stunting_posyandu')`
        const posyanduNode = templatePosyandu.content.firstElementChild
        const scorecardNode = document.createElement('div')
        scorecardNode.className = 'box-statis border-grey-soft bg-white'
        scorecardNode.style.marginTop = '20px'
        const listIcon = ['fa-female', 'fa-child', 'fa-female', 'fa-child', 'fa-child', 'fa-child', 'fa-child']
        const loadStunting = function(tahun, kuartal, idPosyandu) {                
            const stuntingList = document.getElementById('stunting-list');
            jQuery.ajax({
                url: `{{ ci_route('internal_api.stunting') }}`,
                data: {
                    'tahun': tahun,
                    'kuartal': kuartal,
                    'idPosyandu': idPosyandu
                },
                type: "GET",
                beforeSend: function() {
                },
                dataType: 'json',
                data: {

                },
                success: function(data) {
                    stuntingList.innerHTML = ''
                    const widgets = data.data[0]['attributes']['widgets']
                    const chartStuntingUmurData = data.data[0]['attributes']['chartStuntingUmurData']
                    const chartStuntingPosyanduData = data.data[0]['attributes']['chartStuntingPosyanduData']
                    const scorecard = data.data[0]['attributes']['scorecard']
                    const widgetList = document.createElement('div')
                    widgetList.className = `container row`
                    stuntingList.appendChild(widgetList)
                    stuntingList.appendChild(stuntingUmurNode)
                    stuntingList.appendChild(posyanduNode)
                    stuntingList.appendChild(scorecardNode)
                    widgets.forEach(element => {
                        widgetList.innerHTML +=
                            widgetTemplate.replace('@bg-color', (element['bg-color'] == 'bg-gray' ? 'bg-danger' : element['bg-color']))
                            .replace('@icon', listIcon[element.icon] ?? 'fa-female')
                            .replace('@title', element.title)
                            .replace('@total', element.total)

                    });

                    generateChart(chartStuntingUmurData)
                    generatePosyandu(chartStuntingPosyanduData)
                    generateScorecard(scorecard)
                }
            });
        }

        const generateChart = function(chartStuntingUmurData) {
            chartStuntingUmurData.forEach(function(item) {
                Highcharts.chart(item['id'], {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: item['title']
                    },
                    tooltip: {
                        valueSuffix: '%'
                    },
                    plotOptions: {
                        series: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            colors: ['blue', 'red'],
                            showInLegend: true,
                        },
                        pie: {
                            dataLabels: {
                                enabled: true,
                                distance: -50,
                                format: '{point.y:,.1f} %'
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'percentage',
                        colorByPoint: true,
                        data: item['data']
                    }]

                })
            })
        }

        const generatePosyandu = function(chartStuntingPosyanduData) {
            Highcharts.chart('chart_posyandu', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Grafik Kasus Stunting per-Posyandu'
                },
                xAxis: {
                    categories: chartStuntingPosyanduData['categories']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Angka Kasus Stunting'
                    }
                },
                colors: ['#028EFA', '#5EE497', '#FDB13B'],
                series: chartStuntingPosyanduData['data']

            })
        }
        const generateScorecard = function(scorecard) {
            const _url = `{{ ci_route('data-kesehatan.scorecard') }}`
            jQuery.post(_url, {
                scorecard: scorecard,
                sidcsrf: getCsrfToken(),
            }, (html) => scorecardNode.innerHTML = html)
        }
        loadStunting(tahun, kuartal, idPosyandu)
    });
</script>
@endpush