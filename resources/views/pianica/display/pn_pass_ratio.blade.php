@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
					<div class="col-xs-2" style="padding-right: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 5px;padding-left: 0px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 5px;padding-left: 0px;">
						<select style="width: 100%" class="form-control select2" data-placeholder="Pilih Model" id="model">
							<option value=""></option>
							@foreach($model as $model)
							<option value="{{$model}}">{{$model}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-xs-2" style="padding-left: 0px;">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1vw;color: white"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row" id="ALL">
				<div class="col-xs-2" style="padding-right: 0;">
					<!-- <div style="width: 100%;background-color: white;text-align: center;">
						<span style="font-weight: bold;padding: 2px;font-size: 2vw">TODAY</span>
					</div> -->
					<div class="small-box" style="background: #2064bd; color: white;height: 110px; margin-bottom: 5px;cursor: pointer;" onclick="showModalAll('all')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <small><span style="color: white">検査数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a;color: white; height: 110px; margin-bottom: 5px;cursor: pointer;" onclick="showModalAll('ok')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <small><span style="color: white">良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #d62d2d;color: white; height: 110px; margin-bottom: 5px;cursor: pointer;" onclick="showModalAll('ng')">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <small><span style="color: white">不良品数</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(242, 159, 24); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>TARGET % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg_target">0.04<sup style="font-size: 30px"> %</sup></h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-area-chart"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 110px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>ACTUAL % <small><span >不良率</span></small></b></h3>
							<h5 style="font-size: 2.5vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;font-size: 3vw">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div>
				<div class="col-xs-10">
					<div id="container" class="container" style="width: 100%;"></div>
				</div>
			</div>

		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 100px">
				<!-- <center>
					<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
				</center> -->
				<div class="col-xs-12">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Index</th>
								<th style="width: 2%;">Date</th>
								<th style="width: 2%;">Model</th>
								<th style="width: 2%;">NG Name</th>
								<th style="width: 2%;">NG Loc</th>
								<th style="width: 2%;">Loc Kensa</th>
								<!-- <th style="width: 1%;">Loc</th> -->
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
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var logs = null;
	var ngs = null;

	jQuery(document).ready(function(){
		$('#date_from').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
		});
		$('#date_to').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 300000);
		logs = null;
		ngs = null;
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

	function showAll() {
		$("#ALL").show();
		$("#HEAD_1").show();
		$("#HEAD_2").show();
		$("#MIDDLE_1").show();
		$("#MIDDLE_2").show();
		$("#FOOT_1").show();
		$("#FOOT_2").show();
		$("#BLOCK_1").show();
		$("#BLOCK_2").show();
	}

	function hideAll() {
		$("#ALL").hide();
		$("#HEAD_1").hide();
		$("#HEAD_2").hide();
		$("#MIDDLE_1").hide();
		$("#MIDDLE_2").hide();
		$("#FOOT_1").hide();
		$("#FOOT_2").hide();
		$("#BLOCK_1").hide();
		$("#BLOCK_2").hide();
	}

	function fetchChart(){

		$('#loading').show();

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			model:$('#model').val(),
		}
		$.get('{{ url("fetch/pn/pass_ratio") }}',data, function(result, status, xhr){
			if(result.status){

				var date = [];
				var date_name = [];
				var model = [];

				for(var i = 0; i < result.prod_result.length;i++){
					date.push(result.prod_result[i].date);
					model.push(result.prod_result[i].model);
					date_name.push({date:result.prod_result[i].date,date_name:result.prod_result[i].date_name});
				}

				var date_unik = date.filter(onlyUnique);
				var model_unik = model.filter(onlyUnique);

				var count_set = [];

				for(var j = 0; j < date_unik.length;j++){
					var date_names = '';
					for(var o = 0; o < date_name.length;o++){
						if (date_name[o].date == date_unik[j]) {
							date_names = date_name[o].date_name;
						}
					}
					for(var k = 0; k < model_unik.length;k++){
						var count = 0;
						var ng = 0;
						for(var i = 0; i < result.prod_result.length;i++){
							if (result.prod_result[i].model == model_unik[k] && result.prod_result[i].date == date_unik[j]) {
								count++;
								for(var u = 0; u < result.ng.length;u++){
									if (result.ng[u].form_id == result.prod_result[i].form_id) {
										ng++;
									}
								}
							}
						}
						var ok = count - ng;
						var pass_ratio = 0;
						if (ok != 0 && count != 0) {
							var pass_ratio = ((ok/count)*100).toFixed(1);
						}
						count_set.push({date:date_unik[j],date_name:date_names,model:model_unik[k],qty:count,ng:ng,ok:ok,pass_ratio:pass_ratio});
					}
				}

				var index = 1;

				var daily_pass_ratio = [];

				for(var j = 0; j < date_unik.length;j++){
					var count = 0;
					var ok = 0;
					var ng = 0;
					var date_names = '';
					for(var i = 0; i < count_set.length;i++){
						if (count_set[i].date == date_unik[j]) {
							count = count + parseInt(count_set[i].qty);
							ok = ok + parseInt(count_set[i].ok);
							ng = ng + parseInt(count_set[i].ng);
							date_names = count_set[i].date_name;
						}
					}
					daily_pass_ratio.push({category:date_names,qty:count,ok:ok,ng:ng,date:date_unik[j]});
				}
				

				var categories = [];
				var series = [];
				var total_check = [];
				var ok = [];

				var total = 0;
				var total_ok = 0;
				var total_ng = 0;
				
				for(var i = 0; i < daily_pass_ratio.length;i++){
					categories.push(daily_pass_ratio[i].category);
					series.push({y:parseFloat(((daily_pass_ratio[i].ok/daily_pass_ratio[i].qty)*100).toFixed(1)),key:daily_pass_ratio[i].date});
					total_check.push({y:parseInt(daily_pass_ratio[i].qty),key:daily_pass_ratio[i].date});
					ok.push({y:parseInt(daily_pass_ratio[i].ok),key:daily_pass_ratio[i].date});
					total = total + parseInt(daily_pass_ratio[i].qty);
					total_ok = total_ok + parseInt(daily_pass_ratio[i].ok);
					total_ng = total_ng + parseInt(daily_pass_ratio[i].ng);
				}

				$('#total').append().empty();
				$('#total').html(total+ '');

				$('#ok').append().empty();
				$('#ok').html(total_ok + '');

				$('#ng').append().empty();
				$('#ng').html(total_ng + '');

				$('#pctg').append().empty();
				$('#pctg').html(((total_ok/total)*100).toFixed(3) + '<sup style="font-size: 30px"> %</sup>');

				$('#pctg_target').append().empty();
				$('#pctg_target').html(100 + '<sup style="font-size: 30px"> %</sup>');


				Highcharts.chart('container', {
					chart: {
						type: 'column',
						height: '600',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Pass Ratio',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.monthTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [
					 { // Secondary yAxis
						title: {
							text: 'Total Product (Pcs)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						type: 'linear',
						// opposite: true

					}, { // Secondary yAxis
						title: {
							text: 'Pass Ratio (%)',
							style: {
								color: '#eee',
								fontSize: '20px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"16px"
							}
						},
						// max: 5,
						type: 'linear',
						opposite: true

					}
					],
					tooltip: {
						headerFormat: '<span>Detail</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,this.series.name,this.options.key);
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
							// cursor: 'pointer'
						},
					},
					series: [
					
					{
						type: 'column',
						data: ok,
						name: 'Total OK',
						colorByPoint: false,
						color: '#009127',
						// yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},
					{
						type: 'column',
						data: total_check,
						name: 'Total Check',
						colorByPoint: false,
						color: '#2064bd',
						// yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: series,
						name: 'Pass Ratio',
						colorByPoint: false,
						color:'#fff',
						yAxis:1,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}%' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						
					},
					
					]
				});

				$('#loading').hide();

				logs = result.prod_result;
				ngs = result.ng;
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(date_name,condition,date) {
	$('#loading').show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var tableData = '';
	var form_id = [];
	for(var j = 0; j < ngs.length;j++){
		form_id.push(ngs[j].form_id);
	}
	var index = 1;
	var index2 = 1;
	for(var i = 0; i < logs.length;i++){
		if (logs[i].date == date) {
			var ng = '';
			for(var j = 0; j < ngs.length;j++){
				if (ngs[j].form_id == logs[i].form_id) {
					ng = ngs[j].ng_name;
				}
			}
			if (condition == 'Total Check') {
				if (ng != '') {
					var ngss = ng.split(',');
					for(var k = 0; k < ngss.length;k++){
						if (ngss[k].split(';')[1] == ngss[k].split(';')[2]) {
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}else{
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ").replace(/PN Kakuning Visual/g, " ");
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[2].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}
					}
				}else{
					tableData += '<tr>';
					tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
					tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
					tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
					tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
				index2++;
			}

			if (condition == 'Total OK') {
				if (!form_id.includes(logs[i].form_id)) {
					if (ng != '') {
						var ngss = ng.split(',');
						for(var k = 0; k < ngss.length;k++){
							if (ngss[k].split(';')[1] == ngss[k].split(';')[2]) {
								tableData += '<tr>';
								tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
								tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
								tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
								tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += ngss[k].split(';')[0];
								tableData += '</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += '</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += ngss[k].split(';')[1].replace(/_/g, " ");
								tableData += '</td>';
								tableData += '</tr>';
								index++;
							}else{
								tableData += '<tr>';
								tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
								tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
								tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
								tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += ngss[k].split(';')[0];
								tableData += '</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += ngss[k].split(';')[1].replace(/_/g, " ").replace(/PN Kakuning Visual/g, " ");
								tableData += '</td>';
								tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
								tableData += ngss[k].split(';')[2].replace(/_/g, " ");
								tableData += '</td>';
								tableData += '</tr>';
								index++;
							}
						}
					}else{
						tableData += '<tr>';
						tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
						tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
						tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
						tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += '</td>';
						tableData += '</tr>';
						index++;
					}
					index2++;
				}
			}
		}
	}

	$('#tableDetailBody').append(tableData);

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
          'paging': true,
          'lengthChange': true,
          'pageLength': 10,
          'searching': true ,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

	$('#modalDetailTitle').html('Detail '+condition+' On '+date_name);
	$('#modalDetail').modal('show');
	$('#loading').hide();
}

function showModalAll(condition) {
	$('#loading').show();
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var tableData = '';
	var form_id = [];
	for(var j = 0; j < ngs.length;j++){
		form_id.push(ngs[j].form_id);
	}
	var index = 1;
	var index2 = 1;
	for(var i = 0; i < logs.length;i++){
		var ng = '';
		for(var j = 0; j < ngs.length;j++){
			if (ngs[j].form_id == logs[i].form_id) {
				ng = ngs[j].ng_name;
			}
		}
		if (condition == 'all') {
			if (ng != '') {
				var ngss = ng.split(',');
				for(var k = 0; k < ngss.length;k++){
					if (ngss[k].split(';')[1] == ngss[k].split(';')[2]) {
						tableData += '<tr>';
						tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
						tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
						tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
						tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += ngss[k].split(';')[0];
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += ngss[k].split(';')[1].replace(/_/g, " ");
						tableData += '</td>';
						tableData += '</tr>';
						index++;
					}else{
						tableData += '<tr>';
						tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
						tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
						tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
						tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += ngss[k].split(';')[0];
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += ngss[k].split(';')[1].replace(/_/g, " ").replace(/PN Kakuning Visual/g, " ");
						tableData += '</td>';
						tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
						tableData += ngss[k].split(';')[2].replace(/_/g, " ");
						tableData += '</td>';
						tableData += '</tr>';
						index++;
					}
				}
			}else{
				tableData += '<tr>';
				tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
				tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
				tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
				tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
				tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
				tableData += '</td>';
				tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
				tableData += '</td>';
				tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
				tableData += '</td>';
				tableData += '</tr>';
				index++;
			}
			index2++;
		}

		if (condition == 'ok') {
			if (!form_id.includes(logs[i].form_id)) {
				if (ng != '') {
					var ngss = ng.split(',');
					for(var k = 0; k < ngss.length;k++){
						if (ngss[k].split(';')[1] == ngss[k].split(';')[2]) {
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}else{
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ").replace(/PN Kakuning Visual/g, " ");
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[2].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}
					}
				}else{
					tableData += '<tr>';
					tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
					tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
					tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
					tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
				index2++;
			}
		}

		if (condition == 'ng') {
			if (form_id.includes(logs[i].form_id)) {
				if (ng != '') {
					var ngss = ng.split(',');
					for(var k = 0; k < ngss.length;k++){
						if (ngss[k].split(';')[1] == ngss[k].split(';')[2]) {
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}else{
							tableData += '<tr>';
							tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
							tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
							tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
							tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[0];
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[1].replace(/_/g, " ").replace(/PN Kakuning Visual/g, " ");
							tableData += '</td>';
							tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
							tableData += ngss[k].split(';')[2].replace(/_/g, " ");
							tableData += '</td>';
							tableData += '</tr>';
							index++;
						}
					}
				}else{
					tableData += '<tr>';
					tableData += '<td style="width:1%;text-align:center;">'+index+'</td>';
					tableData += '<td style="width:1%;text-align:center;">'+index2+'</td>';
					tableData += '<td style="width:1%;text-align:right;padding-right:7px;">'+logs[i].date+'</td>';
					tableData += '<td style="width:1%;text-align:left;padding-left:7px;">'+logs[i].model+'</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '<td style="width:10%;text-align:left;padding-left:7px;">';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				}
				index2++;
			}
		}
	}

	$('#tableDetailBody').append(tableData);

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
          'paging': true,
          'lengthChange': true,
          'pageLength': 10,
          'searching': true ,
          'ordering': true,
          'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });

	$('#modalDetailTitle').html('Detail Product '+condition.toUpperCase());
	$('#modalDetail').modal('show');
	$('#loading').hide();
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

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}


var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function dynamicSortDesc(property) {
	    var sortOrder = -1;
	    if(property[0] === "-") {
	        sortOrder = -1;
	        property = property.substr(1);
	    }
	    return function (a,b) {
	        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
	        return result * sortOrder;
	    }
	}

</script>
@endsection