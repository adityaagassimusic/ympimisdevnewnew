@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@stop

@section('header')
@stop
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
					</div>
				</div>
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
					</div>
				</div>
				<div class="col-xs-2" style="color:black;">
					<select class="form-control select2" id='origin_group' data-placeholder="Select Products" style="width: 100%;">
						<option value=""></option>
						<option value="041">Flute</option>
						<option value="042">Clarinet</option>
						<option value="043">Saxophone</option>				
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="drawChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%;"></div>
				<div id="container2" style="width: 100%;"></div>
				<div id="container3" style="width: 100%;"></div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
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
		$('.select2').select2();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			todayHighlight : true,
			autoclose: true,
			format: "yyyy-mm-dd",
			endDate: '<?php echo $tgl_max ?>'
		});
		
		drawChart();

	});



	function drawChart(){

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var origin_group = $('#origin_group').val();

		var products = {
			'041':'Flute',
			'042':'Clarinet',
			'043':'Saxophone'
		}

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			origin_group:origin_group
		}

		$.get('{{ url("fetch/production_achievement") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var date = [];
					var target = [];
					var assy = [];
					for (var i = 0; i < result.data2.length; i++) {
						date.push(result.data2[i].due_date);
						target.push(result.data2[i].target);
						assy.push(result.data2[i].result);
					}

					var chart = Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Assembly Process',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: products[result.origin_group] + ' on ' +result.datefrom+ '~' +result.dateto,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						legend: {
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 30,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						credits: {
							enabled: false
						},
						xAxis: {
							categories: date,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '26px'
									}
								},
								animation: false,
								cursor: 'pointer'
							}
						},
						series: [{
							name: 'Target',
							data: target
						}, {
							name: 'Result',
							data: assy
						}]			
					});

					var date = [];
					var target = [];
					var surface_treatment = [];
					var welding = [];

					for (var i = 0; i < result.data.length; i++) {
						date.push(result.data[i].due_date);
						target.push(result.data[i].target);
						surface_treatment.push(result.data[i].surface_treatment);
						welding.push(result.data[i].welding);
					}

					var chart = Highcharts.chart('container2', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Surface Treatment',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: products[result.origin_group] + ' on ' +result.datefrom+ '~' +result.dateto,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						legend: {
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 30,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						credits: {
							enabled: false
						},
						xAxis: {
							categories: date,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '26px'
									}
								},
								animation: false,
								cursor: 'pointer'
							}
						},
						series: [{
							name: 'Target',
							data: target
						}, {
							name: 'Surface Treatment Result',
							data: surface_treatment
						}]			
					});

					var chart = Highcharts.chart('container3', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Welding Process',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: products[result.origin_group] + ' on ' +result.datefrom+ '~' +result.dateto,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						legend: {
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 30,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							shadow: true
						},
						credits: {
							enabled: false
						},
						xAxis: {
							categories: date,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '26px'
									}
								},
								animation: false,
								cursor: 'pointer'
							}
						},
						series: [{
							name: 'Target',
							data: target
						}, {
							name: 'Welding Result',
							data: welding
						}]			
					});

				}
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
			stops: []
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

</script
@endsection