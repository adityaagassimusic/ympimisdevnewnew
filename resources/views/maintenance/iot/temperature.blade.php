@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	th {text-align:center}
	td {text-align:center}

	.alarm {
		-webkit-animation: alarm_ani 1s infinite;  /* Safari 4+ */
		-moz-animation: alarm_ani 1s infinite;  /* Fx 5+ */
		-o-animation: alarm_ani 1s infinite;  /* Opera 12+ */
		animation: alarm_ani 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes alarm_ani {
		0%, 49% {
			background-color: #57ff5c;
		}
		50%, 100% {
			background-color: #ed2f2f;
		}
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Monitoring Suhu Tandon Chiller
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
		<div class="col-md-2 col-md-offset-5" id="div_now" style="border: 2px solid black; background-color: #57ff5c; font-weight: bold; font-size: 18px">
			SUHU SAAT INI : <span id="now">0 &#8451;</span>
		</div>
		<div class="col-md-12">
			<div id="chart" style="margin-top: 10px"></div>
		</div>
		<div class="col-md-12">
			<div id="chart2" style="margin-top: 10px"></div>
		</div>

	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	var audio_alarm = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	var datas = [];
	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		// loadGraph();
		setInterval(loadGraph, 1000 * 60);
		
	});

	function getData() {
		
	}

	function loadGraph() {
		var data = {
			date : $("#date_select").val()
		}

		var series = [];
		var categories = [];
		var last_data = 0;

		$.get('{{ url("fetch/maintenance/tpm/temperature") }}', data, function(result, status, xhr){

			$.each(result.datas,function(index, value){
				// if(categories.indexOf(value2.data_time2) === -1){
					// categories[categories.length] = value2.data_time2;
					categories.push(value.data_time);
					series.push(value.value_sensor);
					last_data = value.value_sensor;
				// }
			});
			// $("#table_detail").empty();
			var body = "";

			var series2 = [];
			var categories2 = [];
			var series2_min = [];
			var series2_max = [];

			$.each(result.data_new,function(index, value){
				categories2.push(value.hari);
				series2.push(value.average);
				series2_min.push(value.minus);
				series2_max.push(value.maksimal);
			});

			// $("#table_detail").append(body);


			$("#now").html(last_data+" &#8451;");
			if (last_data >= 14.5) {
				audio_alarm.play();
				$("#div_now").addClass('alarm');
			} else {
				$("#div_now").removeClass('alarm');
			}

			Highcharts.chart('chart', {

				title: {
					text: 'Monitoring Suhu Tandon Chiller'
				},

				yAxis: {
					title: {
						text: 'Suhu (Celcius)'
					},
					plotBands: [{
						from: 6,
						to: 14.5,
						color: '#57ff5c'
					}, {
						from: 14.5,
						to: 50,
						color: '#ed4545'
					}],
					min : 6,
					max : 18
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
						},
						animation: false
					},

				},

				series: [{
					name: 'Temperature',
					data: series,
					color : '#001252'
				}],

				credits: {
					enabled : false
				},

				responsive: {
					rules: [{
						condition: {
							maxWidth: 500
						},
					}]
				}

			});

			Highcharts.chart('chart2', {

				title: {
					text: 'Monitoring Suhu Tandon Chiller'
				},

				yAxis: {
					title: {
						text: 'Suhu (Celcius)'
					},
					plotBands: [{
						from: 6,
						to: 14,
						color: '#57ff5c'
					}, {
						from: 14,
						to: 50,
						color: '#ed4545'
					}],
					min : 6,
					max : 18
				},

				xAxis: {
					categories: categories2
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
						},
						animation: false
					},

				},

				series: [{
					name: 'Temperature',
					data: series2,
					color : '#001252'
				},
				{
					name: 'Min',
					data: series2_min,
					color : '#fa5558'
				},
				{
					name: 'Max',
					data: series2_max,
					color : '#fa5558'
				}],

				credits: {
					enabled : false
				},

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