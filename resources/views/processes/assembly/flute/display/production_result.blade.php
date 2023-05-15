@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td{
		padding: 1px;
	}
	table.table-bordered{
		border:1px solid black;
		margin-top:20px;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
	}
	.outer .chart-container {
		width: 100%;
		float: right;
		height: 250px;
	}
	.highcharts-yaxis-grid .highcharts-grid-line {
		display: none;
	}

	@media (max-width: 100%) {
		.outer {
			width: 100%;
			height: 400px;
		}
		.outer .chart-container {
			width: 100%;
			float: right;
			/*margin: 0 auto;*/
		}
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
	<div class="col-xs-2 pull-right" style="padding-right: 0;">
		<select class="form-control select2" id="locationSelect" data-placeholder="Select Locations" onchange="fillChartActual()" style="width: 100%;">
			<option value=""></option>
			@foreach($location_all as $location)
			<option value="{{$location}}">{{ trim($location, "'")}}</option>
			@endforeach
		</select>
		<input type="text" name="location" id="location" hidden>	
	</div>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-xs-8">
					<div id="container" style="width:100%; height:550px;"></div>
					<div class="row">
						<div class="col-xs-12">
							<div class="box box-widget">
								<div class="box-footer">
									<div class="row" id="resume"></div>
								</div>
							</div>
						</div>
					</div>					
				</div>
				<div class="col-xs-4 outer">
					<div id="container2" class="chart-container" style="width:100%;"></div>
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Last Output Time</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="lastOutput">-</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Target Efficiency</th>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Manpower</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="targetEff">100%</td>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="actManpower">-</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Average STD Time Per Set</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="stdTime">00:00:00</td>
									</tr>
								</tbody>
							</table>
							<table class="table table-bordered" style="width: 100%;">
								<thead>
									<tr>
										<th style="width:50%; background-color: null; text-align: center; color: black; font-size: 20px;">Average ACT Time Per Set</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(126,86,134); text-align: center; color: #FFD700; font-size: 3vw;" id="actTime">00:00:00</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fillChartActual();
		setInterval(function(){
			fillChartActual();
		}, 60000);
		$('body').toggleClass("sidebar-collapse");
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	function fillChartActual(){
		var data = {
			location:$('#locationSelect').val()
		}
		$.get('{{ url("fetch/assembly/production_result") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var title_location = result.title_location;
					var data = result.chartData;
					var xAxis = []
					, planCount = []
					, actualCount = []

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].model);
						planCount.push(data[i].plan);
						actualCount.push(parseInt(data[i].out_item));
					}

					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Daily Production Result - '+title_location+'<br><span style="color:rgba(96,92,168);">生産実績</span>'
						},
						exporting: { enabled: false },
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels:{
								rotation: -45,
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: 'Set(s)'
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: false
						},
						tooltip: {
							shared: true
						},
						plotOptions: {
							series:{
								minPointLength: 10,
								pointPadding: 0,
								groupPadding: 0,
								animation:{
									duration:0
								}
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0,
							}
						},
						series: [{
							name: 'Plan',
							data: planCount,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: actualCount,
							pointPadding: 0.2
						}]
					});

					var totalPlan = 0;
					var totalIn = 0;
					var totalOut = 0;
					
					$.each(result.chartData, function(key, value) {
						totalPlan += value.plan;
						totalIn = totalIn + parseInt(value.in_item);
						totalOut = totalOut + parseInt(value.out_item);
					});

					$('#resume').html("");
					var resumeData = '';
					resumeData += '<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-blue">'+ totalPlan.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Plan<br><span class="text-purple">計画の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-orange">'+ totalIn.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">In<br><span class="text-orange">受入数</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-4 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-olive">'+ totalOut.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Out<br><span class="text-olive">流し数</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					$('#resume').append(resumeData);

					var gaugeOptions = {

						chart: {
							type: 'solidgauge',
							backgroundColor: null,
							spacingTop: 0,
							spacingLeft: 0,
							spacingRight: 0,
							spacingBottom: 0
						},

						title: 'Efficiency',
						exporting: { enabled: false },
						pane: {
							center: ['50%', '85%'],
							size: '140%',
							startAngle: -90,
							endAngle: 90,
							background: {
								backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
								innerRadius: '60%',
								outerRadius: '100%',
								shape: 'arc'
							}
						},

						tooltip: {
							enabled: false
						},

						yAxis: {
							stops: [
							[0, '#FF0000'],
							[90/200, '#FF0000'],
							[92/200, '#FFD700'],
							[94/200, '#FFD700'],
							[96/200, '#FFD700'],
							[98/200, '#FFD700'],
							[100/200, '#55BF3B']
							],
							lineWidth: 0,
							minorTickInterval: null,
							tickAmount: 2,
							title: {
								y: -70
							},
							labels: {
								y: 16
							}
						},

						plotOptions: {
							solidgauge: {
								dataLabels: {
									y: 5,
									borderWidth: 0,
									useHTML: true
								}
							}
						}
					};

					var act_time = 0;
					var std_time = 0;
					var qty = 0;
					var actmanpower = 0;
					var lastInput = "";
					$.each(result.effData, function(key, value) {
						act_time += value.act_time;
						std_time += value.std_time;
						qty += value.quantity;
						actmanpower += value.manpower;
						lastInput += value.last_input;
					});

					if(qty == 0){
						qty = qty+1;
					}
					if(lastInput == ""){
						lastInput = "00-00-00 00:00:00";
					}

					var eff = 0;
					var act_time_set = 0;
					var std_time_set = 0;
					eff = Math.round((std_time/act_time)*100);
					act_time_set = Math.round(act_time/qty);
					std_time_set = Math.round(std_time/qty);

					$('#actTime').html("");
					$('#stdTime').html("");
					$('#actManpower').html("");
					$('#lastOutput').html("");
					$('#lastOutput').html("<center>"+lastInput+"</center>");
					$('#actTime').html("<center>"+secondsTimeSpanToHMS(act_time_set)+"</center>");
					$('#stdTime').html("<center>"+secondsTimeSpanToHMS(std_time_set)+"</center>");
					$('#actManpower').html("<center>"+Math.round(actmanpower,2)+"</center>");

					Highcharts.chart('container2', Highcharts.merge(gaugeOptions, {
						yAxis: {
							min: 0,
							max: 200,
							title: {
								text: 'Efficiency',
								y:-100
								// enabled:false
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'Efficiency',
							data: [eff],
							dataLabels: {
								format: '<span style="font-size:5vw;">{y}%</span>',
								// x: 0,
								y: 30
							}
						}]
					}));
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
	});
}

function secondsTimeSpanToHMS(s) {
    var h = Math.floor(s/3600); //Get whole hours
    s -= h*3600;
    var m = Math.floor(s/60); //Get remaining minutes
    s -= m*60;
    return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
}

</script>
@endsection