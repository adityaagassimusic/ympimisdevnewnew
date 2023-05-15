@extends('layouts.display')

@section('stylesheets')
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
@stop
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-1">
					<label style="color: white;">Date From:</label>
				</div>
				<div class="col-xs-2">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
						</div>
					</div>
				</div>
				<div class="col-xs-1">
					<label style="color: white;">Date To:</label>
				</div>
				<div class="col-xs-2">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
						</div>
					</div>
				</div>
				<div class="col-xs-2">
					<button id="search" onClick="fillChart()" class="btn btn-primary bg-purple">Update Chart</button>
				</div>
			</div>
			<div id="container" style="min-width: 310px; height:480px; margin: 0 auto"></div>
			<div id="container2" style="min-width: 310px; height:480px; margin: 0 auto"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalProgress">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalProgressTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableModal">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Material</th>
								<th>Description</th>
								<th>Dest.</th>
								<th>Plan</th>
								<th>Actual</th>
								<th>Diff</th>
							</tr>
						</thead>
						<tbody id="modalProgressBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th id="modalProgressTotal1"></th>
							<th id="modalProgressTotal2"></th>
							<th id="modalProgressTotal3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

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

	jQuery(document).ready(function() {
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#datefrom').val("");
		$('#dateto').val("");
		fillChart();
		setInterval(function(){
			fillChart();
		}, 1000*60*10);
	});

	function fillModal(cat, name, prod){
		$('#modalProgress').modal('show');
		$('#loading').show();
		$('#modalProgressTitle').hide();
		$('#tableModal').hide();

		var hpl = name;
		if(name == 'VN'){
			var hpl = 'VENOVA';
		}
		if(name == 'FL'){
			var hpl = 'FLFG';
		}
		if(name == 'AS'){
			var hpl = 'ASFG';
		}
		if(name == 'TS'){
			var hpl = 'TSFG';
		}
		if(name == 'CL'){
			var hpl = 'CLFG';
		}


		if(name == 'MP'){
			var hpl = 'MP';
		}
		if(name == 'PN PART'){
			var hpl = 'PN-PART';
		}
		if(name == 'VN ASSY'){
			var hpl = 'VN-ASSY';
		}
		if(name == 'VN INJ'){
			var hpl = 'VN-INJECTION';
		}
		if(name == 'MP'){
			var hpl = 'MP';
		}
		if(name == 'MP'){
			var hpl = 'MP';
		}
		if(name == 'MPRO'){
			var hpl = 'MPRO';
		}
		if(name == 'ZPRO'){
			var hpl = 'ZPRO';
		}
		if(name == 'BPRO'){
			var hpl = 'BPRO';
		}
		if(name == 'WELDING'){
			var hpl = 'WELDING';
		}
		if(name == 'FLKEY'){
			var hpl = 'SUBASSY-FL';
		}
		if(name == 'CLKEY'){
			var hpl = 'SUBASSY-CL';
		}
		if(name == 'SXBODY'){
			var hpl = 'ASSY-SX';
		}
		if(name == 'SXKEY'){
			var hpl = 'SUBASSY-SX';
		}
		if(name == 'CASE'){
			var hpl = 'CASE';
		}
		if(name == 'CLBODY'){
			var hpl = 'CL-BODY';
		}
		if(name == 'TANPO'){
			var hpl = 'TANPO';
		}

		



		var data = {
			date:cat,
			hpl:hpl
		}

		if(prod == 'FG'){
			$.get('{{ url("fetch/display/modal_shipment_progress") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableModal').DataTable().destroy();
					$('#modalProgressBody').html('');
					var resultData = '';
					var resultTotal1 = 0;
					var resultTotal2 = 0;
					var resultTotal3 = 0;
					$.each(result.shipment_progress, function(key, value) {
						resultData += '<tr>';
						resultData += '<td style="width: 10%">'+ value.material_number +'</td>';
						resultData += '<td style="width: 40%">'+ value.material_description +'</td>';
						resultData += '<td style="width: 5%">'+ value.destination_shortname +'</td>';
						resultData += '<td style="width: 15%">'+ value.plan.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%">'+ value.actual.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%; font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal1 += value.plan;
						resultTotal2 += value.actual;
						resultTotal3 += value.diff;
					});
					$('#modalProgressBody').append(resultData);
					$('#modalProgressTotal1').html('');
					$('#modalProgressTotal1').append(resultTotal1.toLocaleString());
					$('#modalProgressTotal2').html('');
					$('#modalProgressTotal2').append(resultTotal2.toLocaleString());
					$('#modalProgressTotal3').html('');
					$('#modalProgressTotal3').append(resultTotal3.toLocaleString());
					$('#tableModal').DataTable({
						"paging": false,
						'searching': false,
						'order':[],
						'responsive': true,
						'info': false,
						"columnDefs": [{
							"targets": 5,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData.substring(0,1) ==  "-" ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
					$('#loading').hide();
					$('#tableModal').show();
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}
		if(prod == 'KD'){
			$.get('{{ url("fetch/kd_shipment_progress_detail") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableModal').DataTable().destroy();
					$('#modalProgressBody').html('');
					var resultData = '';
					var resultTotal1 = 0;
					var resultTotal2 = 0;
					var resultTotal3 = 0;
					$.each(result.shipment_progress, function(key, value) {
						resultData += '<tr>';
						resultData += '<td style="width: 10%">'+ value.material_number +'</td>';
						resultData += '<td style="width: 40%">'+ value.material_description +'</td>';
						resultData += '<td style="width: 5%">'+ value.destination_shortname +'</td>';
						resultData += '<td style="width: 15%">'+ value.plan.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%">'+ value.actual.toLocaleString() +'</td>';
						resultData += '<td style="width: 15%; font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal1 += value.plan;
						resultTotal2 += value.actual;
						resultTotal3 += value.diff;
					});
					$('#modalProgressBody').append(resultData);
					$('#modalProgressTotal1').html('');
					$('#modalProgressTotal1').append(resultTotal1.toLocaleString());
					$('#modalProgressTotal2').html('');
					$('#modalProgressTotal2').append(resultTotal2.toLocaleString());
					$('#modalProgressTotal3').html('');
					$('#modalProgressTotal3').append(resultTotal3.toLocaleString());
					$('#tableModal').DataTable({
						"paging": false,
						'searching': false,
						'order':[],
						'responsive': true,
						'info': false,
						"columnDefs": [{
							"targets": 5,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData.substring(0,1) ==  "-" ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
					});
					$('#loading').hide();
					$('#tableModal').show();
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}
	}

	function fillChart(){
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var data = {
			datefrom:datefrom,
			dateto:dateto
		};
		$.get('{{ url("fetch/display/shipment_progress") }}', data, function(result, status, xhr){
			if(result.status){

				var data = result.shipment_results;
				var xCategories = [];
				var planFL = [];
				var planCL = [];
				var planAS = [];
				var planTS = [];
				var planPN = [];
				var planRC = [];
				var planVN = [];
				var actualFL = [];
				var actualCL = [];
				var actualAS = [];
				var actualTS = [];
				var actualPN = [];
				var actualRC = [];
				var actualVN = [];
				var i, cat;
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};

				for(i = 0; i < data.length; i++){
					cat = data[i].st_date;
					if(xCategories.indexOf(cat) === -1){
						xCategories[xCategories.length] = cat;
					}
					if(data[i].hpl == 'FLFG'){
						planFL.push(data[i].plan-data[i].act);
						actualFL.push(data[i].act);
					}
					if(data[i].hpl == 'CLFG'){
						planCL.push(data[i].plan-data[i].act);
						actualCL.push(data[i].act);
					}
					if(data[i].hpl == 'ASFG'){
						planAS.push(data[i].plan-data[i].act);
						actualAS.push(data[i].act);
					}
					if(data[i].hpl == 'TSFG'){
						planTS.push(data[i].plan-data[i].act);
						actualTS.push(data[i].act);
					}
					if(data[i].hpl == 'PN'){
						planPN.push(data[i].plan-data[i].act);
						actualPN.push(data[i].act);
					}
					if(data[i].hpl == 'RC'){
						planRC.push(data[i].plan-data[i].act);
						actualRC.push(data[i].act);
					}
					if(data[i].hpl == 'VENOVA'){
						planVN.push(data[i].plan-data[i].act);
						actualVN.push(data[i].act);
					}
				}

				if(xCategories.length <= 4){
					var scrollMax = xCategories.length-1;
				}
				else{
					var scrollMax = 3;
				}


				var yAxisLabels = [0,25,50,75,100,110];
				var chart = Highcharts.chart('container', {

					chart: {
						type: 'column',
						backgroundColor: null
					},

					title: {
						text: 'Finished Goods Achievement For Shipment Progress',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					legend:{
						enabled: false,
						itemStyle: {
							fontSize:'20px'
						}
					},
					credits:{
						enabled:false
					},
					xAxis: {
						categories: xCategories,
						type: 'category',
						gridLineWidth: 5,
						gridLineColor: 'RGB(204,255,255)',
						labels: {	
							style: {
								fontSize: '20px'
							}
						},
						min: 0,
						max:scrollMax,
						scrollbar: {
							enabled: true
						}
					},
					yAxis: {
						title: {
							enabled:false,
						},
						tickPositioner: function() {
							return yAxisLabels;
						},
						labels: {
							enabled:false
						},
						stackLabels: {
							enabled: true,
							verticalAlign: 'left',
							align:'center',
							style: {
								fontSize: '20px',
								color: 'white',
								textOutline: false,
								fontWeight: 'bold',
							},
							formatter:  function() {
								return this.stack;
							}
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y;
								},
								y:-5,
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.userOptions.stack, 'FG');
									}
								}
							}
						}
					},
					series: [{
						name: 'Plan',
						data: planFL,
						stack: 'FL',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planCL,
						stack: 'CL',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planAS,
						stack: 'AS',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planTS,
						stack: 'TS',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planPN,
						stack: 'PN',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planRC,
						stack: 'RC',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planVN,
						stack: 'VN',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Actual',
						data: actualFL,
						stack: 'FL',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualCL,
						stack: 'CL',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualAS,
						stack: 'AS',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualTS,
						stack: 'TS',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualPN,
						stack: 'PN',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualRC,
						stack: 'RC',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualVN,
						stack: 'VN',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}]
				});



				// $('.highcharts-xaxis-labels text').on('click', function () {
				// 	fillModal(this.textContent, 'all', 'FG');
				// });

				// $.each(chart.xAxis[0].ticks, function(i, tick) {
				// 	$('.highcharts-xaxis-labels text').hover(function () {
				// 		$(this).css('fill', '#33c570');
				// 		$(this).css('cursor', 'pointer');

				// 	},
				// 	function () {
				// 		$(this).css('cursor', 'pointer');
				// 		$(this).css('fill', 'white');
				// 	});
				// });


			}
			else{
				alert('Attempt to retrieve data failed.')
			}
		});

$.get('{{ url("fetch/kd_shipment_progress") }}', data, function(result, status, xhr){
	if(result.status){

		var data = result.shipment_results;
		var xCategories = [];

		var planMP = [];
		var actualMP = [];

		var planPnPart = [];
		var actualPnPart = [];

		var planVnAssy = [];
		var actualVnAssy = [];

		var planVnInjection = [];
		var actualVnInjection = [];

		var planZPRO = [];
		var actualZPRO = [];

		var planMPRO = [];
		var actualMPRO = [];

		var planBPRO = [];
		var actualBPRO = [];

		var planWelding = [];
		var actualWelding = [];

		var planSubAssySX = [];
		var actualSubAssySX = [];

		var planAssySX = [];
		var actualAssySX = [];

		var planSubAssyCL = [];
		var actualSubAssyCL = [];

		var planSubAssyFL = [];
		var actualSubAssyFL = [];

		var planCase = [];
		var actualCase = [];

		var planClBody = [];
		var actualClBody = [];

		var planTanpo = [];
		var actualTanpo = [];


		var i, cat;
		var intVal = function ( i ) {
			return typeof i === 'string' ?
			i.replace(/[\$,]/g, '')*1 :
			typeof i === 'number' ?
			i : 0;
		};
		for(i = 0; i < data.length; i++){
			cat = data[i].st_date;
			if(xCategories.indexOf(cat) === -1){
				xCategories[xCategories.length] = cat;
			}
			if(data[i].hpl == 'MP'){
				planMP.push(data[i].plan-data[i].act);
				actualMP.push(data[i].act);
			}
			if(data[i].hpl == 'PN-PART'){
				planPnPart.push(data[i].plan-data[i].act);
				actualPnPart.push(data[i].act);
			}
			if(data[i].hpl == 'VN-ASSY'){
				planVnAssy.push(data[i].plan-data[i].act);
				actualVnAssy.push(data[i].act);
			}
			if(data[i].hpl == 'VN-INJECTION'){
				planVnInjection.push(data[i].plan-data[i].act);
				actualVnInjection.push(data[i].act);
			}
			if(data[i].hpl == 'ZPRO'){
				planZPRO.push(data[i].plan-data[i].act);
				actualZPRO.push(data[i].act);
			}
			if(data[i].hpl == 'MPRO'){
				planMPRO.push(data[i].plan-data[i].act);
				actualMPRO.push(data[i].act);
			}
			if(data[i].hpl == 'BPRO'){
				planBPRO.push(data[i].plan-data[i].act);
				actualBPRO.push(data[i].act);
			}
			if(data[i].hpl == 'WELDING'){
				planWelding.push(data[i].plan-data[i].act);
				actualWelding.push(data[i].act);
			}
			if(data[i].hpl == 'SUBASSY-SX'){
				planSubAssySX.push(data[i].plan-data[i].act);
				actualSubAssySX.push(data[i].act);
			}
			if(data[i].hpl == 'ASSY-SX'){
				planAssySX.push(data[i].plan-data[i].act);
				actualAssySX.push(data[i].act);
			}
			if(data[i].hpl == 'SUBASSY-CL'){
				planSubAssyCL.push(data[i].plan-data[i].act);
				actualSubAssyCL.push(data[i].act);
			}
			if(data[i].hpl == 'SUBASSY-FL'){
				planSubAssyFL.push(data[i].plan-data[i].act);
				actualSubAssyFL.push(data[i].act);
			}
			if(data[i].hpl == 'CASE'){
				planCase.push(data[i].plan-data[i].act);
				actualCase.push(data[i].act);
			}
			if(data[i].hpl == 'CL-BODY'){
				planClBody.push(data[i].plan-data[i].act);
				actualClBody.push(data[i].act);
			}
			if(data[i].hpl == 'TANPO'){
				planTanpo.push(data[i].plan-data[i].act);
				actualTanpo.push(data[i].act);
			}

		}

		console.log(xCategories.length);

		
		var scrollMax = 1;
		

		var yAxisLabels = [0,25,50,75,100,110];
		var chart = Highcharts.chart('container2', {

			chart: {
				type: 'column',
				backgroundColor: null
			},

			title: {
				text: 'KD Parts Achievement For Shipment Progress',
				style: {
					fontSize: '30px',
					fontWeight: 'bold'
				}
			},
			legend:{
				enabled: true,
				itemStyle: {
					fontSize:'20px'
				}
			},
			credits:{
				enabled:false
			},
			xAxis: {
				categories: xCategories,
				type: 'category',
				gridLineWidth: 5,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					style: {
						fontSize: '20px'
					}
				},
				min: 0,
				max:scrollMax,
				scrollbar: {
					enabled: true
				}
			},
			yAxis: {
				title: {
					enabled:false,
				},
				tickPositioner: function() {
					return yAxisLabels;
				},
				labels: {
					enabled:false
				},
				stackLabels: {
					enabled: true,
					verticalAlign: 'left',
					align:'center',
					style: {
						fontSize: '20px',
						color: 'white',
						textOutline: false,
						fontWeight: 'bold'
					},
					rotation: -90,
					formatter:  function() {
						return this.stack;
					}
				}
			},
			tooltip: {
				formatter: function () {
					return '<b>' + this.x + '</b><br/>' +
					this.series.name + ': ' + this.y + '<br/>' +
					'Total: ' + this.point.stackTotal;
				}
			},

			plotOptions: {
				column: {
					stacking: 'percent',
				},
				series:{
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						formatter: function() {
							return this.y;
						},
						y:-5,
						style: {
							fontSize:'14px',
							fontWeight: 'bold',
						}
					},
					point: {
						events: {
							click: function () {
								fillModal(this.category, this.series.userOptions.stack, 'KD');
							}
						}
					}
				}
			},
			series: [
			{
				name: 'Plan',
				data: planTanpo,
				stack: 'TANPO',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planCase,
				stack: 'CASE',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planSubAssySX,
				stack: 'SXKEY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planAssySX,
				stack: 'SXBODY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planSubAssyCL,
				stack: 'CLKEY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planClBody,
				stack: 'CLBODY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planSubAssyFL,
				stack: 'FLKEY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planWelding,
				stack: 'WELDING',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planBPRO,
				stack: 'BPRO',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			},{
				name: 'Plan',
				data: planZPRO,
				stack: 'ZPRO',
				color: 'rgba(255, 0, 0, 0.25)'
			}, {
				name: 'Plan',
				data: planMPRO,
				stack: 'MPRO',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planVnInjection,
				stack: 'VN INJ',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planVnAssy,
				stack: 'VN ASSY',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planPnPart,
				stack: 'PN PART',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			}, {
				name: 'Plan',
				data: planMP,
				stack: 'MP',
				color: 'rgba(255, 0, 0, 0.25)',
				showInLegend: false
			},


			{
				name: 'Actual',
				data: actualTanpo,
				stack: 'TANPO',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualCase,
				stack: 'CASE',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualSubAssySX,
				stack: 'SXKEY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualAssySX,
				stack: 'SXBODY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualSubAssyCL,
				stack: 'CLKEY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			},  {
				name: 'Actual',
				data: actualClBody,
				stack: 'CLBODY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			},  {
				name: 'Actual',
				data: actualSubAssyFL,
				stack: 'FLKEY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualWelding,
				stack: 'WELDING',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualBPRO,
				stack: 'BPRO',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualZPRO,
				stack: 'ZPRO',
				color: 'rgba(0, 255, 0, 0.90)'
			}, {
				name: 'Actual',
				data: actualMPRO,
				stack: 'MPRO',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualVnInjection,
				stack: 'VN INJ',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualVnAssy,
				stack: 'VN ASSY',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualPnPart,
				stack: 'PN PART',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}, {
				name: 'Actual',
				data: actualMP,
				stack: 'MP',
				color: 'rgba(0, 255, 0, 0.90)',
				showInLegend: false
			}
			]
		});

		// $('.highcharts-xaxis-labels text').on('click', function () {
		// 	fillModal(this.textContent, 'all', 'KD');
		// });

		// $.each(chart.xAxis[0].ticks, function(i, tick) {
		// 	$('.highcharts-xaxis-labels text').hover(function () {
		// 		$(this).css('fill', '#33c570');
		// 		$(this).css('cursor', 'pointer');

		// 	},
		// 	function () {
		// 		$(this).css('cursor', 'pointer');
		// 		$(this).css('fill', 'white');
		// 	});
		// });

	}
	else{
		alert('Attempt to retrieve data failed.')
	}
});
}

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate(){
	var d = new Date();
	var day = addZero(d.getDate());
	var month = addZero(d.getMonth()+1);
	var year = addZero(d.getFullYear());
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
}
</script>
@endsection