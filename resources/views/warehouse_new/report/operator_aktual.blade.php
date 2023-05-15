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
	.button5 {border-radius: 50%;}
	.button1 {
		background-color: rgb(244,91,91);
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}
	.button2 {
		background-color: rgb(43,144,143);
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}
	.button3 {
		background-color: rgb(144,238,126);
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}
	.button4 {
		background-color: rgb(119,152,191);
		border: none;
		color: white;
		padding: 12px;
		text-align: center;
		text-decoration: none;
		font-size: 7px;
	}



	thead>tr>th{
		text-align:center;
		overflow:hidden;consok
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.urgent {
		background-color : #f56954; 
		color : white;
	}

</style>
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin:0px;">
			<div class="pull-right" id="last_update" style="color: white; margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
		</div>
			<div class="col-xs-12" style="margin-top: 20px;">
				<div class="row" style="margin:0px;">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-1">
						<button class="btn btn-success" onclick="fillChart1()">Update Chart</button>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
				</div>	
			</div>


		<div class="col-xs-12">

			<div class="col-xs-6" style="padding: 0px; display: none;">
				<div id="mc-workload-shift-3" style="width:100%;"></div>
			</div>
			<div class="col-xs-6" style="padding: 0px; display: none;">
				<div id="mc-workload-shift-2" style="width:100%;"></div>
			</div>


			<div class="col-xs-12" style="padding: 0px;">
				<div id="container" style="width:100%; margin-top: 1%;"></div>
			</div>
			<div class="col-md-12" style="padding-bottom: 10px;">
						<div class="col-md-12" style="text-align:center;">
							<label style="margin-top: 12px; text-align: center;"><button class="button1 button5" style="text-align: center;"></button>Idle</label>
							&nbsp;
							<label style="margin-top: 12px;"><button class="button2 button5"></button> Check Materials</label>
							&nbsp;

							<label style="margin-top: 12px;"><button class="button3 button5"></button> Delivery Materials</label>
							&nbsp;
							
							<label style="margin-top: 12px;"><button class="button4 button5"></button> Other Joblist</label>
						</div>
						
					</div>

		
			<div class="col-xs-12" style="padding: 0px;">
				<div id="op-workload" style="width:100%; margin-top: 1%;"></div>
			</div>

			<div class="col-xs-12" style="padding: 0px;">
				<div id="container2" style="width:100% padding-bottom: 0%;"></div>
			</div>

			<div class="col-md-12" style="margin-top: 5px; padding:0 !important">
				<div id="chart_op" style="width: 99%"></div>
			</div>

		</div>
	</div>

	<div class="modal fade" id="modal-operator" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Productivity Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul-operator"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="operator" class="table table-striped table-bordered" style="width: 100%; margin-bottom: 2%;"> 
								<thead id="operator-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Operator</th>
										<th>Status</th>
										<th id="Pallet" hidden>No Pallet</th>
										<th id="kode_request" hidden>Kode Request</th>
										<th>Kode Request</th>
										<th>Joblist</th>
										<th>Start</th>
										<th>End</th>

									</tr>
								</thead>
								<tbody id="operator-body">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		fillChart1();
		// setInterval(fillChart, 60000);
		$('#date').val("");

	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,
	});

	function fillChart1() {
		fillChart();
		var date = $("#date").val();

		var data = {
			date : date
		}

		$.get('{{ url("fetch/internal/operatoraktual") }}',data, function(result, status, xhr){
			if(result.status){
				var machine_name = [];
				var name = [];
				var pic = [];
				var time = [];
				var time1 = [];
				var time2 = [];
				var time3 = [];
				var time5 = [];
				var machines = [];
				var series = [];
				var unfilled = true;
				var tot_time1 = "";
				var tot_time2 = "";
				var tot_time5 = "";


				for (var i = 0; i < result.operators_time.length; i++) {
					name.push(result.operators_time[i].name);
					pic.push(result.operators_time[i].employee_id);

					// if (result.operators_time[i].time != 0) {
					// 	tot_time1 = result.operators_time[i].time;
					// 	tot_time2 = result.operators_time[i].time2;
					// }else if (result.operators_time[i].time1 != 0){
					// 	tot_time1 = result.operators_time[i].time1;
					// 	tot_time2 = result.operators_time[i].time3;
					// }else{
					// 	tot_time1 = 0;
					// 	tot_time2 = 0;
					// }
					tot_time1 = result.operators_time[i].st_check;
					tot_time3 = result.operators_time[i].st_deliv;
					tot_time4 = result.operators_time[i].st_idle;
					tot_time5 = result.operators_time[i].st_Lain;



					time.push(parseInt(tot_time1));
					time2.push(parseInt(tot_time3));
					time3.push(parseInt(tot_time4));
					time5.push(parseInt(tot_time5));


				}

				$('#chart_op').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Warehouse Operators Internal Productivity',
						style: {
							fontSize: '24px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'On '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: name,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					
					yAxis: {
						lineWidth:2,
						lineColor:'#fff',
						type: 'linear',
						title: {
							text: 'Minutes(s)'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							}
						},
						plotLines: [{
							color: '#FF0000',
							value: 460,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target 460 Minutes',
								x:-7,
								style: {
									fontSize: '13px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						max: 800
					},

					legend: {
						enabled:true,
						reversed: true,
						itemStyle:{
							color: "white",
							fontSize: "15px",
							fontWeight: "bold",

						},
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										showOperatorDetail(this.category, result.date, this.series.name);
									}
								}
							},
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							borderRadius: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},

					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [
					{
						name: 'Check Materials',
						data: time
					},
					{
						name: 'Delivery Materials',
						data: time2
					},
					{
						name: 'Idle',
						data: time3
					},
					{
						name: 'Others Joblist',
						data: time5
					}
					]
				});
			}
		});
	}


	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
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

	Highcharts.setOptions({
		global: {
			useUTC: true,
			timezoneOffset: -420

		}
	});

	function mode(array){
		var function_name = 'Untuk menghitung jumlah index yg sering muncul';

		if(array.length == 0)
			return null;
		var modeMap = {};
		var maxEl = array[0], maxCount = 1;
		for(var i = 0; i < array.length; i++){
			var el = array[i];
			if(modeMap[el] == null)
				modeMap[el] = 1;
			else
				modeMap[el]++;  
			if(modeMap[el] > maxCount){
				maxEl = el;
				maxCount = modeMap[el];
			}
		}
		return maxCount;
	}

	function fillChart(){
		var position = $(document).scrollTop();
		if ($("#date").val() == "") {
		var date = new Date().toISOString().slice(0, 10);
		}else{
		var date = $("#date").val();
		}

		var data = {
			date : date
		}


		$.get('{{ url("fetch/internal/operatoraktual") }}',data, function(result, status, xhr){
			if(result.status){

				var today = new Date(date);
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var series = [];
				var series2 = [];
				var machines = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();


				for (var i = 0; i < result.operators.length; i++) {

					var deal = [];

					var unfilled = true;
					// for (var j = 0; j < result.op_workloads.length; j++) {
					// 	if (result.op_workloads[j].end_check != null || result.op_workloads[j].end_move != null) {

					// 	if(result.operators[i].employee_id == result.op_workloads[j].employee_id){
					// 		unfilled = false;
					// 		deal.push({
					// 			mc_name: result.op_workloads[j].no_case,
					// 			wjo : result.op_workloads[j].name,
					// 			status : "Checking",
					// 			from : Date.parse(result.op_workloads[j].start_check),
					// 			to : Date.parse(result.op_workloads[j].end_check)
					// 		});
					// 		deal.push({
					// 			mc_name: result.op_workloads[j].no_case,
					// 			wjo : result.op_workloads[j].name,
					// 			status : "Storage",
					// 			from : Date.parse(result.op_workloads[j].start_move),
					// 			to : Date.parse(result.op_workloads[j].end_move)
					// 		});
					// 	}
					// }else{

					// }
					// }

					for (var j = 0; j < result.op_pelayanan.length; j++) {
						if (result.op_pelayanan[j].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[j].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[j].employee_id && result.op_pelayanan[j].STATUS == "idle"){
								unfilled = false;
								deal.push({
									mc_name: '-',
									wjo : result.operators[i].name,
									status : "idle",
									job : '-',
									from : Date.parse(result.op_pelayanan[j].dt),
									to : Date.parse(result.op_pelayanan[j].end_job),
									color : 'rgb(244,91,91)'
								});
							}
							
						}

					}

					for (var k = 0; k < result.op_pelayanan.length; k++) {
						if (result.op_pelayanan[k].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[k].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[k].employee_id && result.op_pelayanan[k].STATUS == "pengecekan"){
								unfilled = false;
								deal.push({
									mc_name: result.op_pelayanan[k].request_desc,
									wjo : result.operators[i].name,
									status : "Check Materials",
									job : '-',
									from : Date.parse(result.op_pelayanan[k].dt),
									to : Date.parse(result.op_pelayanan[k].end_job),
									color : 'rgb(43,144,143)'

								});
							}
						}

					}

					for (var m = 0; m < result.op_pelayanan.length; m++) {
						if (result.op_pelayanan[m].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[m].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[m].employee_id && result.op_pelayanan[m].STATUS == "penataan"){
								unfilled = false;
								deal.push({
									mc_name: result.op_pelayanan[m].request_desc,
									wjo : result.operators[i].name,
									status : "Storage",
									job : '-',
									from : Date.parse(result.op_pelayanan[m].start_mob),
									to : Date.parse(result.op_pelayanan[m].end_job),
									color : '#273c75'
								});
							}
						}

					}

					for (var n = 0; n < result.op_pelayanan.length; n++) {
						if (result.op_pelayanan[n].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[n].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[n].employee_id && result.op_pelayanan[n].STATUS == "check"){
								unfilled = false;
								deal.push({
									mc_name: result.op_pelayanan[n].request_desc,
									wjo : result.operators[i].name,
									status : "Check Material",
									job : '-',
									from : Date.parse(result.op_pelayanan[n].dt),
									to : Date.parse(result.op_pelayanan[n].end_job),
									color : 'rgb(43,144,143)'
								});
							}
						}

					}

					for (var o = 0; o < result.op_pelayanan.length; o++) {
						if (result.op_pelayanan[o].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[o].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[o].employee_id && result.op_pelayanan[o].STATUS == "Pengantaran"){
								unfilled = false;
								deal.push({
									mc_name: result.op_pelayanan[o].request_desc,
									wjo : result.operators[i].name,
									status : "Delivery Materials",
									job : '-',
									from : Date.parse(result.op_pelayanan[o].dt),
									to : Date.parse(result.op_pelayanan[o].end_job),
									color : 'rgb(144,238,126)'
								});
							}
						}

					}
					for (var o = 0; o < result.op_pelayanan.length; o++) {
						if (result.op_pelayanan[o].end_job == null) {

						}else{
							var to = new Date(result.op_pelayanan[o].dt);
							to = to.addDays(1);
							if(result.operators[i].employee_id == result.op_pelayanan[o].employee_id && result.op_pelayanan[o].STATUS == "Lain"){
								unfilled = false;
								deal.push({
									mc_name: '-',
									wjo : result.operators[i].name,
									status : "Other Joblist",
									job : result.op_pelayanan[o].joblist,
									from : Date.parse(result.op_pelayanan[o].dt),
									to : Date.parse(result.op_pelayanan[o].end_job),
									color : 'rgb(119,152,191)'
								});
							}
						}

					}
					// for (var j = 0; j < result.op_pengantaran.length; j++) {
					// 	if (result.op_pengantaran[j].end_pengantaran == null) {

					// 	}else{
					// 	if(result.operators[i].employee_id == result.op_pengantaran[j].employee_id){
					// 		unfilled = false;
					// 		deal.push({
					// 			mc_name: result.op_pengantaran[j].kode_request,
					// 			wjo : result.op_pengantaran[j].name,
					// 			status : "Delivery Materials",
					// 			from : Date.parse(result.op_pengantaran[j].start_pengantaran),
					// 			to : Date.parse(result.op_pengantaran[j].end_pengantaran)
					// 		});
					// 	}
					// 	}
					// }
					if(unfilled){
						deal.push({
							wjo : 0
						});
					}


					machines.push({
						name: result.operators[i].name,
						current: 0,
						deals: deal
					});
				}


				series = machines.map(function(value, i) {
					var data = value.deals.map(function(deal) {
						return {
							id: 'deal-' + i,
							wjo: deal.wjo,
							mc_name: deal.mc_name,
							status : deal.status, 
							job : deal.job, 
							start: deal.from,
							end: deal.to,
							color: deal.color,
							y: i
						};
					});
					return {
						name: value.name,
						data: data,
						current: value.deals[value.current]
					};
				});


				Highcharts.ganttChart('container', {
					series: series,
					title: {
						text: 'Warehouse Operators Internal Productivity Actuals',
						style: {
							fontSize: '24px',
							fontWeight: 'bold'
						}
					},
					tooltip: {
						pointFormat:'<span>Kode Request: {point.mc_name}</span><br/><span>Status: {point.status}</span><br/><span>Job: {point.job}</span><br/><span>From: {point.start:%e %b %Y, %H:%M}</span><br/><span>To: {point.end:%e %b %Y, %H:%M}</span>'
					},
					colors : ['#ffffff'],
					xAxis: {
						type: 'datetime',
						tickInterval: day / 24,
						labels: {
							format: '{value:%H}'
						},
						min: today,
						max: today + 1 * day,
						currentDateIndicator:{
							enabled: true,
							color : '#fff',
							label: {
								style: {
									fontSize: '14px',
									color: '#FFB300',
									fontWeight: 'bold'
								}
							}
						},
						scrollbar: {
							enabled: true,
							barBackgroundColor: 'gray',
							barBorderRadius: 7,
							barBorderWidth: 0,
							buttonBackgroundColor: 'gray',
							buttonBorderWidth: 0,
							buttonArrowColor: 'white',
							buttonBorderRadius: 7,
							rifleColor: 'white',
							trackBackgroundColor: 'black',
							trackBorderWidth: 1,
							trackBorderColor: 'silver',
							trackBorderRadius: 7
						},
						tickLength: 0
					},
					yAxis: {
						type: 'category',
						grid: {
							columns: [{
								title: {
									text: 'OPERATORS',
									style: {
										fontSize: '18px',
										fontWeight: 'bold'
									}
								},
								categories: map(series, function(s) {
									return s.name;
								}),
							}]
						}
					},
					plotOptions: {
						gantt: {
							animation: false
						},

					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					}
				});

				
				$(document).scrollTop(position);

			}

		});
}
Date.prototype.addDays = function(days) {
	var date = new Date(this.valueOf());
	date.setDate(date.getDate() + days);
	return date;
}

function showOperatorDetail(name,time, status1) {

	var st = "";
	if (status1 == "Check Materials") {
		st = "check";
	}else if(status1 == "Delivery Materials"){
		st = "Pengantaran";
	}else if (status1 == "Idle") {
		st = "idle";
	}else if (status1 == "Pekerjaan Tambahan") {
		st = "Lain";
	}


	var data = {
		name : name,
		status : st,
		time : time
	}


	$.get('{{ url("fetch/internal/detail") }}', data, function(result, status, xhr){
		if(result.status){
			$('#modal-operator').modal('show');
			$('#operator-body').append().empty();
			$('#judul-operator').append().empty();
			var body = '';
			var status = "";
			var stat = "";
			var ends = "";
			var stat1 = "";
			var ends1 = "";

				for (var i = 0; i < result.detail_op_pel_delivery.length; i++) {
					
					body += '<tr>';
					body += '<td>'+ result.detail_op_pel_delivery[i].name +'</td>';

					body += '<td>'+ result.detail_op_pel_delivery[i].sts +'</td>';
					if (st == 'idle') {
					body += '<td>-</td>';
					body += '<td>-</td>';
					}else if (st == 'Lain') {
					body += '<td>-</td>';
					body += '<td>'+ result.detail_op_pel_delivery[i].joblist +'</td>';
					}
					else{
					body += '<td>'+ result.detail_op_pel_delivery[i].request_desc +'</td>';
					body += '<td>-</td>';

					}
					body += '<td>'+ result.detail_op_pel_delivery[i].start_job +'</td>';
					body += '<td>'+ result.detail_op_pel_delivery[i].end_job +'</td>';
					body += '</tr>';
				}

			$('#operator-body').append(body);
		}
	});
}


</script>
@endsection