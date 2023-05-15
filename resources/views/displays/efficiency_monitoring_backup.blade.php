@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div id="period_title" class="col-xs-9" style="background-color: #ccff90;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center></div>
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: #ccff90;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" onchange="fetchChart()">
			</div>
		</div>
		<div class="col-xs-12" style="padding-left: 0; padding-right: 0; padding-top: 10px; height: 500px;" id="eff_all"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_final"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_middle"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_soldering"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_initial"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_rcassy"></div>
		<div class="col-xs-4" style="padding:0; height:300px;" id="eff_pnassy"></div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 1%;">Quantity</th>
								<th style="width: 1%;">Total Amount</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchChart();
		$('#datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
	});

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

	function fetchChart(){
		// $('#loading').show();
		var period = $('#datepicker').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/display/efficiency_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#title_text').text('Periode '+result.first+' - '+result.last);
				var h = $('#period_title').height();
				$('#datepicker').css('height', h);

				var data_final = [];
				var data_middle = [];
				var data_soldering = [];
				var data_initial = [];
				var data_rcassy = [];
				var data_pnassy = [];

				$.each(result.datas, function(key, value){
					if(value.cost_center_name == 'FINAL'){
						data_final.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
					if(value.cost_center_name == 'MIDDLE'){
						data_middle.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
					if(value.cost_center_name == 'SOLDERING'){
						data_soldering.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
					if(value.cost_center_name == 'INITIAL'){
						data_initial.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
					if(value.cost_center_name == 'RC ASSY'){
						data_rcassy.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
					if(value.cost_center_name == 'PN ASSY'){
						data_pnassy.push({
							week_date: value.week_date, 
							total_output:value.total_output/60, 
							total_input:value.total_input/60
						});
					}
				});

				var series_final_output = [];
				var series_final_input = [];
				var series_final_percentage = [];
				var output_final = 0;
				var input_final = 0;

				var series_middle_output = [];
				var series_middle_input = [];
				var series_middle_percentage = [];
				var output_middle = 0;
				var input_middle = 0;

				var series_soldering_output = [];
				var series_soldering_input = [];
				var series_soldering_percentage = [];
				var output_soldering = 0;
				var input_soldering = 0;

				var series_initial_output = [];
				var series_initial_input = [];
				var series_initial_percentage = [];
				var output_initial = 0;
				var input_initial = 0;

				var series_rcassy_output = [];
				var series_rcassy_input = [];
				var series_rcassy_percentage = [];
				var output_rcassy = 0;
				var input_rcassy = 0;

				var series_pnassy_output = [];
				var series_pnassy_input = [];
				var series_pnassy_percentage = [];
				var output_pnassy = 0;
				var input_pnassy = 0;

				var xCategories = [];

				$.each(data_final, function(key, value){
					output_final += value.total_output;
					input_final += value.total_input;
					xCategories.push(value.week_date);
					series_final_output.push(output_final);
					series_final_input.push(input_final);
					series_final_percentage.push((output_final/input_final)*100);
				});

				$.each(data_middle, function(key, value){
					output_middle += value.total_output;
					input_middle += value.total_input;
					series_middle_output.push(output_middle);
					series_middle_input.push(input_middle);
					series_middle_percentage.push((output_middle/input_middle)*100);
				});

				$.each(data_soldering, function(key, value){
					output_soldering += value.total_output;
					input_soldering += value.total_input;
					series_soldering_output.push(output_soldering);
					series_soldering_input.push(input_soldering);
					series_soldering_percentage.push((output_soldering/input_soldering)*100);
				});

				$.each(data_initial, function(key, value){
					output_initial += value.total_output;
					input_initial += value.total_input;
					series_initial_output.push(output_initial);
					series_initial_input.push(input_initial);
					series_initial_percentage.push((output_initial/input_initial)*100);
				});

				$.each(data_rcassy, function(key, value){
					output_rcassy += value.total_output;
					input_rcassy += value.total_input;
					series_rcassy_output.push(output_rcassy);
					series_rcassy_input.push(input_rcassy);
					series_rcassy_percentage.push((output_rcassy/input_rcassy)*100);
				});

				$.each(data_pnassy, function(key, value){
					output_pnassy += value.total_output;
					input_pnassy += value.total_input;
					series_pnassy_output.push(output_pnassy);
					series_pnassy_input.push(input_pnassy);
					series_pnassy_percentage.push((output_pnassy/input_pnassy)*100);
				});

				Highcharts.chart('eff_all', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'YMPI'
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: xCategories,
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: 'Hour(s)'
						}
					}, { 
						min: 0,
						title: {
							text: 'Percentage',
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_final_input

					}, {
						name: 'Output Hour(s)',
						data: series_final_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_final_percentage

					}]
				});

				Highcharts.chart('eff_final', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'FINAL'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_final_input

					}, {
						name: 'Output Hour(s)',
						data: series_final_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_final_percentage

					}]
				});

				Highcharts.chart('eff_middle', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'MIDDLE'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_middle_input

					}, {
						name: 'Output Hour(s)',
						data: series_middle_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_middle_percentage

					}]
				});

				Highcharts.chart('eff_soldering', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'SOLDERING'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_soldering_input

					}, {
						name: 'Output Hour(s)',
						data: series_soldering_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_soldering_percentage

					}]
				});

				Highcharts.chart('eff_initial', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'INITIAL'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_initial_input

					}, {
						name: 'Output Hour(s)',
						data: series_initial_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_initial_percentage

					}]
				});

				Highcharts.chart('eff_pnassy', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'PIANICA ASSY'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_pnassy_input

					}, {
						name: 'Output Hour(s)',
						data: series_pnassy_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_pnassy_percentage

					}]
				});

				Highcharts.chart('eff_rcassy', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'RECORDER ASSY'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_rcassy_input

					}, {
						name: 'Output Hour(s)',
						data: series_rcassy_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_rcassy_percentage

					}]
				});

			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');				
			}
		});		
}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
			]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);

</script>
@endsection