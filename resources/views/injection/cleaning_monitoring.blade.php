@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	input {
		line-height: 22px;
	}
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
	#loading, #error { display: none; }

	.highcharts-color-done {
	  fill: green;
	}
	.highcharts-color-notyet {
	  fill: red;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>	
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<!-- <form method="GET" action="{{ action('RecorderProcessController@indexNgRateKensa') }}"> -->
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<!-- <div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div> -->
				<!-- </form> -->
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #52c9ed; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <span class="text-purple">検査数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <span class="text-purple">良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <span class="text-purple">不良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>% <span class="text-purple">不良率</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div> -->
				<div class="col-xs-12">
					<div id="container" class="container" style="width: 100%;"></div>
				</div>
				<div class="col-xs-12" style="overflow-x: scroll;">
					<table class="table table-hover table-bordered" id="tableTrend" style="padding-top: 10px">
						<thead style="background-color: #605ca8;color: white" id="tableTrendHead">
							<!-- <tr> -->
								<!-- <th style="width: 1%;">Date</th> -->
								<!-- <th style="width: 1%;">Product</th>
								<th style="width: 1%;">Part</th>
								<th style="width: 1%;">Color</th>
								<th style="width: 1%;">Cav</th> -->
								<!-- <th style="width: 2%;">OP Molding</th>
								<th style="width: 1%;">Molding</th>
								<th style="width: 3%;">OP Injeksi</th>
								<th style="width: 1%;">Mesin</th>
								<th style="width: 3%;">OP Resin</th>
								<th style="width: 1%;">Resin</th>
								<th style="width: 1%;">Dryer</th> -->
								<!-- <th style="width: 1%;">OP Kensa</th>
								<th style="width: 1%;">NG Kensa</th> -->
							<!-- </tr> -->
						</thead>
						<tbody id="tableTrendBody" style="background-color: white">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body" style="min-height: 100px;margin-top: 10px">
					<table class="table table-hover table-bordered table-responsive no-padding" id="tableTrendDetail">
						<thead style="background-color: rgba(126,86,134,.7)">
							<tr>
								<th style="width: 1%;">Machine</th>
								<th style="width: 1%;background-color:#a2ff8f">&#9711;</th>
								<th style="width: 1%;background-color:#fff68f">&#8420;</th>
								<th style="width: 1%;background-color:#ff8f8f">&#9747;</th>
								<th style="width: 1%;">By</th>
							</tr>
						</thead>
						<tbody id="tableTrendDetailBody">
						</tbody>
					</table>
					<table class="table table-hover table-bordered table-responsive no-padding" id="tableBelum">
						<thead style="background-color: rgba(126,86,134,.7)">
							<tr>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Machine</th>
							</tr>
						</thead>
						<tbody id="tableBelumBody">
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

	jQuery(document).ready(function(){
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
		fetchChart();
		setInterval(fetchChart, 30000);
	});

	function topFunction() {
	  document.body.scrollTop = 0;
	  document.documentElement.scrollTop = 0;
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
			backgroundColor: null,
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

	var detail_all = [];
	var date_all = [];
	var schedules_belum = [];

	function fetchChart(){
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/injection/cleaning/monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

				var categories = [];
				
				var totalbyday = [];
				date_all = [];
				var weekdate = [];
				var not_yet = [];
				var done = [];
				for(var k = 0; k < result.week_date.length; k++){
					categories.push(result.week_date[k].week_date);
					if (result.cleaning[k][0].week_date == result.week_date[k].week_date) {
						done.push(parseInt(result.cleaning[k][0].qty_act));
						not_yet.push(parseInt(result.cleaning[k][0].qty_check)-parseInt(result.cleaning[k][0].qty_act));
					}
				}
				// schedules_belum = [];
				// for(var l = 0; l < result.visual_check.length; l++){
				// 	var schedules = result.visual_check[l].schedules.split(',');
				// 	if (result.visual_check[l].hour_check != null) {
				// 		for(var m = 0; m < schedules.length; m++){
				// 			if (result.visual_check[l].hour_check.includes(schedules[m])) {

				// 			}else{
				// 				schedules_belum.push({date:result.visual_check[l].date,hour:schedules[m]});
				// 			}
				// 		}
				// 	}else{
				// 		for(var o = 0; o < schedules.length;o++){
				// 			schedules_belum.push({date:result.visual_check[l].date,hour:schedules[o]});
				// 		}
				// 	}
				// }


				Highcharts.chart('container', {
					chart: {
						type: 'column',
						height: '500',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "INJECTION CLEANING MONITORING",
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories:categories,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '16px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Total Check / Day',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						
					}
					],
					tooltip: {
						headerFormat: '<span>Cleaning Check</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						enabled:true
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {
										showHighlight(this.series.name,this.category);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					// {
					// 	type: 'column',
					// 	data: hour0608,
					// 	name: "06:00:00 - 08:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour0810,
					// 	name: "08:00:00 - 10:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour1012,
					// 	name: "10:00:00 - 12:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour1214,
					// 	name: "12:00:00 - 14:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour1416,
					// 	name: "14:00:00 - 16:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour1618,
					// 	name: "16:00:00 - 18:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour1820,
					// 	name: "18:00:00 - 20:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour2022,
					// 	name: "20:00:00 - 22:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour2200,
					// 	name: "22:00:00 - 00:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour0002,
					// 	name: "00:00:00 - 02:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour0204,
					// 	name: "02:00:00 - 04:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// },
					// {
					// 	type: 'column',
					// 	data: hour0406,
					// 	name: "04:00:00 - 06:00:00",
					// 	colorByPoint: false,
					// 	animation: false,
					// 	stacking:true,
						
					// }
					{
						type: 'column',
						data: not_yet,
						name: "Belum Dilakukan",
						colorByPoint: false,
						color:'rgba(255, 61, 61,0.8)',
						animation: false,
						stacking:true,
					},
					{
						type: 'column',
						data: done,
						name: "Sudah Dilakukan",
						colorByPoint: false,
						color:'rgba(42, 191, 92,0.8)',
						animation: false,
						stacking:true,
					},
					]
				});

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}
function showHighlight(name,date) {
		$('#loading').show();
		$('#tableTrendDetail').DataTable().clear();
		$('#tableTrendDetail').DataTable().destroy();
		$('#tableBelum').DataTable().clear();
		$('#tableBelum').DataTable().destroy();

		if (name === 'Sudah Dilakukan') {
			var data = {
				date:date,
				name:name,
			}

			$.get('{{ url("fetch/injection/cleaning/monitoring/detail") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#tableTrendDetailBody').html('');
					var detailBody = '';
					var ng_counts = 0;

					for (var i = 0; i < result.detail.length; i++) {
						detailBody += '<tr>';
						detailBody += '<td>'+result.detail[i].machine+'</td>';
						detailBody += '<td>'+result.detail[i].ok+' Point(s)</td>';
						detailBody += '<td>'+result.detail[i].ns+' Point(s)</td>';
						detailBody += '<td>'+result.detail[i].ng+' Point(s)</td>';
						detailBody += '<td>'+result.detail[i].employee_id+'<br>'+result.detail[i].name.split(' ').slice(0,2).join(' ')+'</td>';
						detailBody += '</tr>';
					}

					$('#tableTrendDetailBody').append(detailBody);

					var table = $('#tableTrendDetail').DataTable({
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
							]
						},
						'paging': true,
						'lengthChange': true,
						'searching': true,
						'ordering': true,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true,
					});
					$('#modalDetailTitle').html('DETAIL CLEANING ON '+result.dateTitle.toUpperCase());
					$('#loading').hide();
					$('#modalDetail').modal('show');
					$('#tableTrendDetail').show();
					$('#tableBelum').hide();
					$('#tableBelumBody').html('');
				}else{
					$('#loading').hide();
					alert('Attempt to retrieve data failed');
				}
			});
		}else{
			var data = {
				date:date,
				name:name,
			}

			$.get('{{ url("fetch/injection/cleaning/monitoring/detail") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#tableBelum').DataTable().clear();
					$('#tableBelum').DataTable().destroy();
					$('#tableBelumBody').html('');
					var belum = '';

					for(var i = 0; i < result.detail.length;i++){
						belum += '<tr>';
						belum += '<td style="font-size:16px">'+date+'</td>';
						belum += '<td style="font-size:16px">'+result.detail[i]+'</td>';
						belum += '</tr>';
					}
					$('#tableBelumBody').append(belum);
					var table = $('#tableBelum').DataTable({
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
							]
						},
						'paging': true,
						'lengthChange': true,
						'searching': true,
						'ordering': true,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true,
					});
					$('#modalDetailTitle').html('DETAIL CLEANING NOT CHECK ON '+date.toUpperCase());
					$('#loading').hide();
					$('#modalDetail').modal('show');
					$('#tableTrendDetail').hide();
					$('#tableTrendDetailBody').html('');
					$('#tableBelum').show();
				}else{
					$('#loading').hide();
					alert('Attempt to retrieve data failed');
				}
			});
			
		}
}


function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

	function perbandingan(a,b){
		return a-b;
	}
	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}




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


</script>
@endsection
