@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	th {text-align:center}
	td {text-align:center}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Trial Censors
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-3">
			<input type="text" id="date_select" class="form-control datepicker" placeholder="pilih tanggal" value="{{ date('Y-m-d') }}">
		</div>
		<div class="col-md-2">
			<button class="btn btn-success" onclick="loadGraph()"><i class="fa fa-search"></i> Search</button>
		</div>
		<div class="col-md-12">
			<div id="chart" style="margin-top: 10px"></div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	var datas = [];
	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		loadGraph();
		// getData();
	});

	function getData() {
		
	}

	function loadGraph() {
		var data = {
			date : $("#date_select").val()
		}

		var series = [];
		var categories = [];

		$.get('{{ url("fetch/ph/data") }}', data, function(result, status, xhr){
			datas.push(result.data_sensor);


			$.each(datas,function(index, value){

				$.each(value,function(index2, value2){
					if (value2.sensor_value >= 4.0 && value2.sensor_value <= 4.5) {
						if(categories.indexOf(value2.data_time2) === -1){
							categories[categories.length] = value2.data_time2;
							series.push(value2.sensor_value - 0.07);
						}
				}
			})
			});


			Highcharts.chart('chart', {

				title: {
					text: 'Ph Meter Monitoring'
				},

				yAxis: {
					title: {
						text: 'Ph Value'
					},
					plotBands: [{
						from: 0,
						to: 4.4,
						color: '#57ff5c'
					}, {
						from: 4.4,
						to: 7,
						color: '#ed4545'
					}],
					max : 5,
					min : 4,
				},

				xAxis: {
					categories: categories
				},

				legend: {

				},

				tooltip: {
					crosshairs: true,
					shared: true
				},

				plotOptions: {
					series: {
						label: {
							connectorAllowed: false
						},
						marker: {
							enabled: true,
							symbol: 'circle',
							radius: 2
						}
					}
				},

				series: [{
					name: 'Ph Value',
					data: series,
					color : '#001252'
				}],

				responsive: {
					rules: [{
						condition: {
							maxWidth: 500
						},
					}]
				}

			});
		})

		
	}
	function readCensor(){
		$.get('{{ url("fetch/fibration/data2/old") }}', function(result, status, xhr){
			if(result.status){
				var xCategories = [];
				var xData = [];
				var xData2 = [];
				$.each(result.records, function( index, value ) {
					xCategories.push(value.data_time);
					xData.push(parseInt(value.sensor_value));
					xData2.push(parseInt(value.remark));
				});


				Highcharts.chart('chart', {
					chart: {
						type: 'line'
					},
					title: {
						text: 'Fibration Sensor Data'
					},
					xAxis: {
						categories: xCategories,
						tickInterval: 30
					},
					yAxis: {
						min: -4,
						max: 8,
						title: {
							text: 'Fibration'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: ( 
									Highcharts.defaultOptions.title.style &&
									Highcharts.defaultOptions.title.style.color
									) || 'gray'
							}
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 25,
						floating: true,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || 'white',
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					tooltip: {

					},
					plotOptions: {
						series : {
							animation: false,
							marker: {
								enabled: false
							},
						}
					},
					series: [{
						name: 'Fibration 1',
						data: xData
					},{
						name: 'Fibration 2',
						data: xData2
					}]
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}
</script>
@endsection