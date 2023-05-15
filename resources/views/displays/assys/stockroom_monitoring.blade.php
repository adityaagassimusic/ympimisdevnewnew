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
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div id="period_title" class="col-xs-9" style="background-color: #2196f3;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center></div>
			<div class="col-xs-3" style="padding-right: 0;">
				<div class="input-group date">
					<div class="input-group-addon" style="background-color: #2196f3;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" onchange="fetchChart()">
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="condition" style="margin-top: 10px;">

	</div>
	<div class="row" id="monitoring" style="margin-top: 10px;">

	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 style="font-weight: bold;" class="modal-title" id="modalDetailTitle"></h4></center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<span style="font-weight: bold;">
						STOCK AVAILABILITY<br>
						Jika Plan &gt; 0 (Ava = Stock &divide; Plan)<br>
						Jika Plan &#8924; 0 (Ava = Stock &divide; Plan Avg (Rata2 plan s/d hari H))<br><br>
						PICKING AVAILABILITY<br>
						Jika ada kurang ambil saja yang ditampilkan
					</span>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 4%;">Key</th>
								<th style="width: 1%;">Plan Avg</th>
								<th style="width: 1%;">Plan Acc</th>
								<th style="width: 1%;">Pick Acc</th>
								<th style="width: 1%;">Return Acc</th>
								<th style="width: 1%;">Plan</th>
								<th style="width: 1%;">Pick</th>
								<th style="width: 1%;">Diff</th>
								<th style="width: 1%;">Stock</th>
								<th style="width: 1%;">Ava</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot id="tableDetailFoot">
						</tfoot>
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
<script src="{{ url("js/accessibility.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			autoclose: true,
			todayHighlight: true,
		});
		fetchChart();
		setInterval(fetchChart, 1000*60*10);
	});

	var key_details = "";

	function fetchChart(){
		// $('#loading').show();
		var period = $('#datepicker').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/display/stockroom_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#title_text').text('Stockroom Condition On '+result.now);
				var h = $('#period_title').height();
				$('#datepicker').css('height', h);

				key_details = result.stockroom_keys;
				var hpl = [];
				$('#monitoring').html("");
				$('#condition').html("");
				var monitoring = "";
				var condition = "";
				var new_group = [];
				var ava_new_group = [];

				key_details.reduce(function (res, value) {
					if(value.diff > 0){
						if (!res[value.hpl]) {
							res[value.hpl] = {
								safe: 0,
								unsafe: 0,
								zero: 0,
								hpl: value.hpl
							};
							new_group.push(res[value.hpl])
						}
						res[value.hpl].safe += value.safe
						res[value.hpl].unsafe += value.unsafe
						res[value.hpl].zero += value.zero
						return res;
					}
				}, {});

				key_details.reduce(function (ava_res, value) {
					if (!ava_res[value.hpl]) {
						ava_res[value.hpl] = {
							ava_ultra_safe: 0,
							ava_safe: 0,
							ava_unsafe: 0,
							ava_zero: 0,
							ava_hpl: value.hpl
						};
						ava_new_group.push(ava_res[value.hpl])
					}
					ava_res[value.hpl].ava_ultra_safe += value.ava_ultra_safe
					ava_res[value.hpl].ava_safe += value.ava_safe
					ava_res[value.hpl].ava_unsafe += value.ava_unsafe
					ava_res[value.hpl].ava_zero += value.ava_zero
					return ava_res;
				}, {});

				$.each(result.stockroom_keys, function(key, value){
					if(hpl.indexOf(value.hpl) === -1){
						hpl.push(value.hpl);
						condition = '<div style="height: 43vh;" class="col-xs-3" id="condition_'+value.hpl+'"></div>';
						$('#condition').append(condition);
						monitoring = '<div style="height: 43vh;" class="col-xs-3" id="'+value.hpl+'"></div>';
						$('#monitoring').append(monitoring);
					}
				});

				for (var i = 0; i < ava_new_group.length; i++) {
					Highcharts.chart('condition_'+ava_new_group[i].ava_hpl, {
						chart: {
							backgroundColor: 'rgb(80,80,80)',
							type: 'pie',
							options3d: {
								enabled: true,
								alpha: 45,
								beta: 0
							}
						},
						title: {
							text: 'STOCK AVAILABILITY FOR - '+ava_new_group[i].ava_hpl+' '
						},
						accessibility: {
							point: {
								valueSuffix: '%'
							}
						},
						legend: {
							enabled: true,
							symbolRadius: 1,
							borderWidth: 1
						},
						credits:{
							enabled:false
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								edgeWidth: 1,
								edgeColor: 'rgb(126,86,134)',
								depth: 35,
								dataLabels: {
									enabled: true,
									format: '<b>{point.y} item(s)</b><br>{point.percentage:.1f} %',
									// distance: -50,
									style:{
										fontSize:'0.8vw',
										textOutline:0
									},
									color:'white',
									connectorWidth: '3px'
								},
								showInLegend: true,
								point: {
									events: {
										click: function () {
											fetchDetail(this.series.name, this.name, 'stock');
										}
									}
								}
							}
						},
						series: [{
							type: 'pie',
							name: ava_new_group[i].ava_hpl,
							data: [{
								name: 'Stock > 2 Day',
								y: ava_new_group[i].ava_ultra_safe	,
								color: '#005005'
							}, {
								name: 'Stock 1-2 Day',
								y: ava_new_group[i].ava_safe,
								color: '#90ee7e'
							}, {
								name: 'Stock < 1 Day',
								y: ava_new_group[i].ava_unsafe,
								color: '#ffeb3b'
							}, {
								name: 'Stock Zero',
								y: ava_new_group[i].ava_zero,
								color: '#d32f2f'
							}]
						}]
					});
				}

				for (var i = 0; i < new_group.length; i++) {

					Highcharts.chart(new_group[i].hpl, {
						chart: {
							backgroundColor: 'rgb(80,80,80)',
							type: 'column',
							options3d: {
								enabled: true,
								alpha: 15,
								beta: 15,
								depth: 50,
								viewDistance: 25
							}
						},
						title: {
							text: 'PICKING AVAILABILITY FOR - '+new_group[i].hpl+' '
						},
						xAxis: {
							categories: ['Stock Zero', 'Stock < 1 Day', 'Stock >= 1 Day']
						},
						legend: {
							enabled: false,
							symbolRadius: 1,
							borderWidth: 1
						},
						plotOptions: {
							series: {
								depth: 25,
								colorByPoint: true
							},
							column: {
								edgeWidth: 1,
								edgeColor: 'rgb(126,86,134)',
								pointPadding: 0.05,
								groupPadding: 0.1,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											fetchDetail(this.series.name, this.name, 'pick');
										}
									}
								}
							}
						},						
						credits:{
							enabled:false
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						series: [{
							name: new_group[i].hpl,
							data: [{
								name: 'Stock Zero',
								y: new_group[i].zero,
								color: '#d32f2f',
								dataLabels: {
									enabled: true,
									style: {
										fontSize: '1.3vw'
									}
								}
							}, {
								name: 'Stock < 1 Day',
								y: new_group[i].unsafe,
								color: '#ffeb3b',
								dataLabels: {
									enabled: true,
									style: {
										fontSize: '1.3vw'
									}
								}
							}, {
								name: 'Stock >= 1 Day',
								y: new_group[i].safe,
								color: '#90ee7e',
								dataLabels: {
									enabled: true,
									style: {
										fontSize: '1.3vw'
									}
								}
							}]
						}]
					});
				}

			}
			else{
				alert('Unidentified ERROR!');
			}
		});
}

function fetchDetail(hpl, cat, c){
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html("");
	$('#modalDetailTitle').text('Stock Condition Of '+hpl+' With '+cat);
	var tableDetailBody = "";
	$.each(key_details, function(key, value){
		if(c == 'pick'){
			if(value.hpl == hpl){
				if(value.diff > 0){
					// if(cat == 'Stock > 2 Day'){
					// 	if(value.ultra_safe == 1){
					// 		tableDetailBody += '<tr>';
					// 		tableDetailBody += '<td>'+value.model+'</td>';
					// 		tableDetailBody += '<td>'+value.key+'</td>';
					// 		tableDetailBody += '<td>'+value.surface+'</td>';
					// 		tableDetailBody += '<td>'+value.plan_ori+'</td>';
					// 		tableDetailBody += '<td>'+value.plan+'</td>';
					// 		tableDetailBody += '<td>'+value.stock+'</td>';
					// 		tableDetailBody += '<td>'+value.ava+' Day(s)</td>';
					// 		tableDetailBody += '</tr>';
					// 	}				
					// }
					if(cat == 'Stock >= 1 Day'){
						if(value.safe == 1){
							tableDetailBody += '<tr>';
							tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
							tableDetailBody += '</tr>';
						}				
					}
					if(cat == 'Stock < 1 Day'){
						if(value.unsafe == 1){
							tableDetailBody += '<tr>';
							tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
							tableDetailBody += '</tr>';
						}
					}
					if(cat == 'Stock Zero'){
						if(value.zero == 1){
							tableDetailBody += '<tr>';
							tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
							tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
							tableDetailBody += '</tr>';
						}
					}
				}
			}
		}
		else{
			if(value.hpl == hpl){
				if(cat == 'Stock > 2 Day'){
					if(value.ava_ultra_safe == 1){
						tableDetailBody += '<tr>';
						tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
						tableDetailBody += '</tr>';
					}				
				}
				if(cat == 'Stock 1-2 Day'){
					if(value.ava_safe == 1){
						tableDetailBody += '<tr>';
						tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
						tableDetailBody += '</tr>';
					}				
				}
				if(cat == 'Stock < 1 Day'){
					if(value.ava_unsafe == 1){
						tableDetailBody += '<tr>';
						tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
						tableDetailBody += '</tr>';
					}
				}
				if(cat == 'Stock Zero'){
					if(value.ava_zero == 1){
						tableDetailBody += '<tr>';
						tableDetailBody += '<td style="width: 4%;">'+value.model+' '+value.key+' '+value.surface+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_schedule+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan_ori+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.minus+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.plan+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.picking+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.diff*-1+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.stock+'</td>';
						tableDetailBody += '<td style="width: 1%;">'+value.ava+' Day(s)</td>';
						tableDetailBody += '</tr>';
					}
				}
			}
		}
	});

$('#tableDetailBody').append(tableDetailBody);

$('#tableDetail').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'lengthMenu': [
	[ 10, 25, 50, -1 ],
	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	],
	'buttons': {
		buttons:[
		{
			extend: 'pageLength',
			className: 'btn btn-default',
		},
		{
			extend: 'copy',
			className: 'btn btn-success',
			text: '<i class="fa fa-copy"></i> Copy',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'excel',
			className: 'btn btn-info',
			text: '<i class="fa fa-file-excel-o"></i> Excel',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'print',
			className: 'btn btn-warning',
			text: '<i class="fa fa-print"></i> Print',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		]
	},
	'paging': false,
	'lengthChange': true,
	'searching': true,
	'ordering': true,
	'order': [6, 'asc'],
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true
});

$('#modalDetail').modal('show');
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