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
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group" style="width: 100%"> 
							<select class="form-control select2"  id="select_part" data-placeholder="Select Part">
								<option value=""></option>
								<option value="HJ">HJ</option>
								<option value="MJ">MJ</option>
								<option value="FJ">FJ</option>
								<option value="BJ">BJ</option>
								<option value="A YRF H">A YRF H</option>
								<option value="A YRF B">A YRF B</option>
								<option value="A YRF S">A YRF S</option>
							</select>
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group" style="width: 100%"> 
							<select class="form-control select2"  id="select_ng_name" data-placeholder="Select NG Name">
								<option value=""></option>
								@foreach($ng_lists as $ng_lists)
								<option value="{{$ng_lists->ng_name}}">{{$ng_lists->ng_name}}</option>
								@endforeach
							</select>
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
				<div class="col-xs-9" style="padding-right: 2px">
					<div id="container" class="container" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 2px;height: 500px;padding-top: 40px">
					<div style="width: 100%;background-color: #ff4747;text-align: center;">
						<span style="font-weight: bold;font-size: 25px;color: white">PENGHASIL NG TERBESAR</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">PART</span><br>
					</div>
					<div style="width: 100%;background-color: #decbec;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="part">MJB</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">NG</span><br>
					</div>
					<div style="width: 100%;background-color: #80ffe8;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="ng_name">-</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">PRODUCT</span><br>
					</div>
					<div style="width: 100%;background-color: #eccbd9;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="product">YRS</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">MACHINE</span><br>
					</div>
					<div style="width: 100%;background-color: #97d2fb;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="machine">MESIN 1</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">MOLDING</span><br>
					</div>
					<div style="width: 100%;background-color: #83bcff;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="molding">MJB 01</span>
					</div>
					<div style="width: 100%;background-color: #e1eff6;text-align: center;">
						<span style="font-weight: bold;font-size: 20px;">PERSON</span><br>
					</div>
					<div style="width: 100%;background-color: #80ffe8;text-align: center;border-bottom: 2px solid black">
						<span style="font-weight: bold;font-size: 25px;color: black;" id="person">-</span>
					</div>
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
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Product</th>
								<th style="width: 1%;">Part</th>
								<th style="width: 1%;">Color</th>
								<th style="width: 1%;">Cav</th>
								<th style="width: 1%;">OP Molding</th>
								<th style="width: 1%;">Molding</th>
								<th style="width: 1%;">OP Injeksi</th>
								<th style="width: 1%;">Mesin</th>
								<th style="width: 1%;">Resin</th>
								<th style="width: 1%;">Dryer</th>
								<th style="width: 1%;">OP Kensa</th>
								<th style="width: 1%;">Kensa At</th>
								<th style="width: 1%;">NG Name Kensa</th>
								<th style="width: 1%;">NG Qty Kensa</th>
							</tr>
						</thead>
						<tbody id="tableTrendDetailBody">
						</tbody>
						<tfoot style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th colspan="14" style="text-align: center;font-weight: bold;">TOTAL</th>
								<th style="text-align: center;font-weight: bold;" id="totalNgCount"></th>
							</tr>
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
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 3600000);
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

	function fetchChart(){
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		var part = $('#select_part').val();
		var ng_name_select = $('#select_ng_name').val();

		var date_arr_save = [];
		var mesin_arr_save = [];
		var resin_arr_save = [];
		var dryer_arr_save = [];
		var person_injeksi_arr_save = [];
		var molding_arr_save = [];
		var person_arr_save = [];
		var product_arr_save = [];
		var part_arr_save = [];
		var ng_name_arr_save = [];
		var qty_ng_arr_save = [];

		var data = {
			date_from:date_from,
			date_to:date_to,
			part:part,
			ng_name:ng_name_select,
		}

		$.get('{{ url("fetch/recorder/display/ng_trend") }}', data, function(result, status, xhr) {
			if(result.status){
				//BLOCK
				var nghead = [];
				var ngmiddle = [];
				var ngfoot = [];
				var ngblock = [];
				var ngheadyrf = [];
				var ngbodyyrf = [];
				var ngstopperyrf = [];
				var category = [];
				var totals = [];
				var totals2 = 0;
				var totalRegression = [];
				
				var totalbyday = [];
				date_all = [];
				if ($('#select_ng_name').val() == '') {
					for (var i = 0; i < result.week_date.length; i++) {
						date_all.push(result.week_date[i].week_date);
						category.push(result.week_date[i].week_date);

						var ng_head = 0;
						var ng_middle = 0;
						var ng_foot = 0;
						var ng_block = 0;
						var ng_headyrf = 0;
						var ng_bodyyrf = 0;
						var ng_stopperyrf = 0;
						var total = 0;

						for(var j = 0; j< result.resumes.length;j++){
							if (result.week_date[i].week_date == result.resumes[j][0].week_date) {
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/HJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_head = ng_head + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/MJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_middle = ng_middle + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/BJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_block = ng_block + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/FJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_foot = ng_foot + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF H/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_headyrf = ng_headyrf + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF B/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_bodyyrf = ng_bodyyrf + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF S/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												ng_stopperyrf = ng_stopperyrf + parseInt(counts[l]);
												total = total + parseInt(counts[l]);
											}
										}
									}
								}
							}
						}
						nghead.push(ng_head);
						ngmiddle.push(ng_middle);
						ngfoot.push(ng_foot);
						ngblock.push(ng_block);
						ngheadyrf.push(ng_headyrf);
						ngbodyyrf.push(ng_bodyyrf);
						ngstopperyrf.push(ng_stopperyrf);
						totals.push(total);

						var arrday = [];
						arrday.push({y:ng_head,ng:'head'});
						arrday.push({y:ng_middle,ng:'middle'});
						arrday.push({y:ng_foot,ng:'foot'});
						arrday.push({y:ng_block,ng:'block'});
						arrday.push({y:ng_headyrf,ng:'headyrf'});
						arrday.push({y:ng_bodyyrf,ng:'bodyyrf'});
						arrday.push({y:ng_stopperyrf,ng:'stopperyrf'});

						arrday.sort(dynamicSort('y'));

						var high = "";

						for (var m = 0; m < arrday.length;m++) {
							high = arrday[m].ng;
						}

						var ng_count_high = 0;

						for (var w = 0; w < arrday.length;w++) {
							ng_count_high = arrday[w].y;
						}

						qty_ng_arr_save.push({item:ng_count_high,date:result.week_date[i].week_date});

						// qty_ng_arr_save.push(ng_count_high);

						totalbyday.push(high);
					}
				}else{
					for (var i = 0; i < result.week_date.length; i++) {
						date_all.push(result.week_date[i].week_date);
						category.push(result.week_date[i].week_date);

						var ng_head = 0;
						var ng_middle = 0;
						var ng_foot = 0;
						var ng_block = 0;
						var ng_headyrf = 0;
						var ng_bodyyrf = 0;
						var ng_stopperyrf = 0;
						var total = 0;

						for(var j = 0; j< result.resumes.length;j++){
							if (result.week_date[i].week_date == result.resumes[j][0].week_date) {
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/HJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_head = ng_head + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/MJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_middle = ng_middle + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}
								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/BJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_block = ng_block + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/FJ/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_foot = ng_foot + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF H/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_headyrf = ng_headyrf + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF B/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_bodyyrf = ng_bodyyrf + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}

								for(var k = 0; k< result.resumes[j].length;k++){
									if (result.resumes[j][k].part_code.match(/A YRF S/gi)) {
										if (result.resumes[j][k].ng_name != null) {
											var ngs = result.resumes[j][k].ng_name.split(',');
											var counts = result.resumes[j][k].ng_count.split(',');
											for (var l = 0; l < ngs.length; l++) {
												if (ngs[l] == $('#select_ng_name').val()) {
													ng_stopperyrf = ng_stopperyrf + parseInt(counts[l]);
													total = total + parseInt(counts[l]);
												}
											}
										}
									}
								}
							}
						}
						nghead.push(ng_head);
						ngmiddle.push(ng_middle);
						ngfoot.push(ng_foot);
						ngblock.push(ng_block);
						ngheadyrf.push(ng_headyrf);
						ngbodyyrf.push(ng_bodyyrf);
						ngstopperyrf.push(ng_stopperyrf);
						totals.push(total);

						var arrday = [];
						arrday.push({y:ng_head,ng:'head'});
						arrday.push({y:ng_middle,ng:'middle'});
						arrday.push({y:ng_foot,ng:'foot'});
						arrday.push({y:ng_block,ng:'block'});
						arrday.push({y:ng_headyrf,ng:'headyrf'});
						arrday.push({y:ng_bodyyrf,ng:'bodyyrf'});
						arrday.push({y:ng_stopperyrf,ng:'stopperyrf'});

						arrday.sort(dynamicSort('y'));

						var high = "";

						for (var m = 0; m < arrday.length;m++) {
							high = arrday[m].ng;
						}

						var ng_count_high = 0;

						for (var w = 0; w < arrday.length;w++) {
							ng_count_high = arrday[w].y;
						}

						qty_ng_arr_save.push({item:ng_count_high,date:result.week_date[i].week_date});

						// qty_ng_arr_save.push(ng_count_high);

						totalbyday.push(high);
					}
				}

				var regress = [];

				for(var i = 0; i < totals.length; i++){
					if (totals[i] != 0) {
						regress.push(totals[i]);
					}
				}

				if (regress.length % 2 == 0) {
					var totalsmedian = regress;
					var indexMedianBawah = (totalsmedian.length/2)-1;
					var indexMedianAtas = (totalsmedian.length/2);
					var indexMinus = -(indexMedianAtas+indexMedianBawah);
					var indexPlus = 1;
					var xLinear = [];
					for(var i = 0; i < totalsmedian.length;i++){
						if (totalsmedian[i] != 0) {
							if (i == indexMedianBawah) {
								xLinear.push(-1);
								indexMinus = indexMinus + 2;
							}else if(i == indexMedianAtas){
								xLinear.push(indexPlus);
								indexPlus = indexPlus + 2;
							}else if(i < indexMedianBawah){
								xLinear.push(indexMinus);
								indexMinus = indexMinus + 2;
							}else if(i > indexMedianAtas){
								xLinear.push(indexPlus);
								indexPlus = indexPlus + 2;
							}
						}
					}

					for(var i = 0; i < totals.length; i++){
						if (totals[i] == 0) {
							xLinear.push(indexPlus);
							indexPlus = indexPlus + 2;
						}
					}
				}else{
					var totalsmedian = regress;
					var indexMedian = Math.round(totalsmedian.length/2)-1;
					var indexMinus = -indexMedian;
					var indexPlus = 1;
					var xLinear = [];
					for(var i = 0; i < totalsmedian.length;i++){
						if (totalsmedian[i] != 0) {
							if (i == indexMedian) {
								xLinear.push(0);
							}else if(i < indexMedian){
								xLinear.push(indexMinus);
								indexMinus++;
							}else if(i > indexMedian){
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					for(var i = 0; i < totals.length; i++){
						if (totals[i] == 0) {
							xLinear.push(indexPlus);
							indexPlus++;
						}
					}
				}

				var xy = [];
				var xkuadrat = [];

				for(var i = 0; i < totals.length; i++){
					if (totals[i] != 0) {
						xy.push(parseInt(xLinear[i])*parseInt(totals[i]));
						xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
					}
				}

				var sumy = totals.reduce((a, b) => a + b, 0);
				var sumxy = xy.reduce((a, b) => a + b, 0);
				var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

				var a = sumy/totalsmedian.length;
				var b = sumxy/sumxkuadrat;

				var regressions = [];

				for(var i = 0; i < regress.length; i++){
					regressions.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
				}

				// var datas = [];

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						height: '500',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TREND & TRACEABILITY NG RECORDER",
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
						categories:category,
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
							text: 'Qty NG Pc(s)',
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
						headerFormat: '<span>NG Name</span><br/>',
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
								// format: '{point.y}',
								style:{
									fontSize: '1vw'
								},
								formatter: function(){
			                    	return (this.y!=0)?this.y:"";
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
					{
						type: 'column',
						data: nghead,
						name: "NG Head",
						colorByPoint: false,
						color: "#788cff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngmiddle,
						name: "NG Middle",
						colorByPoint: false,
						color: "#cc6eff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngfoot,
						name: "NG Foot",
						colorByPoint: false,
						color: "#93ff87",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngblock,
						name: "NG Block",
						colorByPoint: false,
						color: "#ffad4f",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngheadyrf,
						name: "NG Head YRF",
						colorByPoint: false,
						color: "#edff4f",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngbodyyrf,
						name: "NG Body YRF",
						colorByPoint: false,
						color: "#94eaff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngstopperyrf,
						name: "NG Stopper YRF",
						colorByPoint: false,
						color: "#ff9494",
						animation: false,
						stacking:true
					},
					{
						type: 'spline',
						data: totals,
						name: "Total NG",
						colorByPoint: false,
						color: "#d62d2d",
						animation: false,
						marker: {
			                radius: 4,
			                lineColor: '#ff0000',
			                lineWidth: 2
			            },
					},
					{
						type: 'line',
						data: regressions,
						name: "Trendline Linear",
						colorByPoint: false,
						color: "#fff",
						animation: false,
						dashStyle:'shortdash',
						lineWidth: 4,
						marker: {
			                radius: 4,
			                lineColor: '#fff',
			                lineWidth: 1
			            },
					},
					]
				});

				var tableTrendHead = "";
				var tableTrend = "";
				$('#tableTrendBody').html('');
				$('#tableTrendHead').html('');


				tableTrendHead += '<tr>';
				tableTrendHead += '<th style="border-bottom:2px solid red;width:1%;">Detail</th>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}

					tableTrendHead += '<th style="width:1%;border-bottom:2px solid red;'+colorss+'" id="'+result.week_date[i].week_date+'">'+result.week_date[i].date_title+'</th>';
				}
				tableTrendHead += '</tr>';

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">Mesin</td>';
				var mesin_arr = [];
				var molding_arr = [];
				var person_arr = [];
				var product_arr = [];
				var part_arr = [];
				var ng_name_arr = [];
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var mesin = "";
					var resin = "";
					var dryer = "";
					var person_injeksi = "";
					var molding = "";
					var operator_molding = "";
					var product = "";
					var part = "";
					var part_name = "";
					var part_type = "";
					var ng_names = [];
					var ng_counts = [];
					var ng_gabungan = [];
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									date_arr_save.push(result.week_date[i].week_date);
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												mesin = result.resume_trend[j][k].mesin;
												resin = result.resume_trend[j][k].lot_number_resin;
												dryer = result.resume_trend[j][k].dryer_resin;
												person_injeksi = result.resume_trend[j][k].name+'_'+result.resume_trend[j][k].start_injection;
											}
										}
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'head') {
												if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'middle') {
												if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'foot') {
												if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'headyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'bodyyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'stopperyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
									}
								}
							}
						}
						var ng_name_new = [];
						var ng_unik = ng_names.filter(onlyUnique);
						for(var p = 0; p < ng_unik.length;p++){
							var count_gabungan = 0;
							for(var q = 0; q < ng_gabungan.length;q++){
								if (ng_gabungan[q].ng_name == ng_unik[p]) {
									count_gabungan = count_gabungan + parseInt(ng_gabungan[q].ng_count);
								}
							}
							ng_name_new.push({ng_name:ng_unik[p],ng_count:count_gabungan});
						}
						ng_name_new.sort(dynamicSort('ng_count'));
						
						var ng_name_fix = '';
						if ($('#select_ng_name').val() == '') {
							for(var r = 0; r < ng_name_new.length;r++){
								ng_name_fix = ng_name_new[r].ng_name;
							}
						}else{
							for(var r = 0; r < ng_name_new.length;r++){
								if (ng_name_new[r].ng_name == $('#select_ng_name').val()) {
									ng_name_fix = ng_name_new[r].ng_name;
								}
							}
						}
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '<span class="label label-success">'+mesin+'</span>';
						mesin_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix,mesin:mesin});
						mesin_arr_save.push({item:mesin,date:result.week_date[i].week_date});
						resin_arr_save.push({item:resin,date:result.week_date[i].week_date});
						dryer_arr_save.push({item:dryer,date:result.week_date[i].week_date});
						person_injeksi_arr_save.push({item:person_injeksi,date:result.week_date[i].week_date});
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}
				tableTrend += '</tr>';

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">Molding</td>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var molding = "";
					var part_name = '';
					var part_type = '';
					var ng_names = [];
					var ng_counts = [];
					var ng_gabungan = [];
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												molding = result.resume_trend[j][k].molding;
											}
										}
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'head') {
												if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'middle') {
												if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'foot') {
												if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'headyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'bodyyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'stopperyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
									}
								}
							}
						}
						var ng_name_new = [];
						var ng_unik = ng_names.filter(onlyUnique);
						for(var p = 0; p < ng_unik.length;p++){
							var count_gabungan = 0;
							for(var q = 0; q < ng_gabungan.length;q++){
								if (ng_gabungan[q].ng_name == ng_unik[p]) {
									count_gabungan = count_gabungan + parseInt(ng_gabungan[q].ng_count);
								}
							}
							ng_name_new.push({ng_name:ng_unik[p],ng_count:count_gabungan});
						}
						ng_name_new.sort(dynamicSort('ng_count'));
						
						var ng_name_fix = '';
						if ($('#select_ng_name').val() == '') {
							for(var r = 0; r < ng_name_new.length;r++){
								ng_name_fix = ng_name_new[r].ng_name;
							}
						}else{
							for(var r = 0; r < ng_name_new.length;r++){
								if (ng_name_new[r].ng_name == $('#select_ng_name').val()) {
									ng_name_fix = ng_name_new[r].ng_name;
								}
							}
						}
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '<span class="label label-success">'+molding+'</span>';
						molding_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix,molding:molding});
						molding_arr_save.push({item:molding,date:result.week_date[i].week_date});
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}
				tableTrend += '</tr>';

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">OP Molding</td>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var operator_molding = "";
					var part_name = '';
					var part_type = '';
					var ng_names = [];
					var ng_counts = [];
					var ng_gabungan = [];
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												operator_molding = result.resume_trend[j][k].operator_molding;
											}
										}
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'head') {
												if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'middle') {
												if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'foot') {
												if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'headyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'bodyyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'stopperyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
									}
								}
							}
						}
						var ng_name_new = [];
						var ng_unik = ng_names.filter(onlyUnique);
						for(var p = 0; p < ng_unik.length;p++){
							var count_gabungan = 0;
							for(var q = 0; q < ng_gabungan.length;q++){
								if (ng_gabungan[q].ng_name == ng_unik[p]) {
									count_gabungan = count_gabungan + parseInt(ng_gabungan[q].ng_count);
								}
							}
							ng_name_new.push({ng_name:ng_unik[p],ng_count:count_gabungan});
						}
						ng_name_new.sort(dynamicSort('ng_count'));
						
						var ng_name_fix = '';
						if ($('#select_ng_name').val() == '') {
							for(var r = 0; r < ng_name_new.length;r++){
								ng_name_fix = ng_name_new[r].ng_name;
							}
						}else{
							for(var r = 0; r < ng_name_new.length;r++){
								if (ng_name_new[r].ng_name == $('#select_ng_name').val()) {
									ng_name_fix = ng_name_new[r].ng_name;
								}
							}
						}
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						var opmol = operator_molding.split(', ');
						if (opmol.length > 1) {
							for(var m = 0; m < opmol.length;m++){
								tableTrend += '<span class="label label-success">'+opmol[m].replace(/(.{12})..+/, "$1&hellip;")+'</span><br>';
								person_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix,person:opmol[m]});
							}
							person_arr_save.push({item:opmol.join(','),date:result.week_date[i].week_date});
						}else{
							tableTrend += '<span class="label label-success">'+operator_molding.replace(/(.{12})..+/, "$1&hellip;")+'</span><br>';
							person_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix,person:operator_molding});
							person_arr_save.push({item:operator_molding,date:result.week_date[i].week_date});
						}
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}
				tableTrend += '</tr>';

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">Product</td>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var product = "";
					var part_name = '';
					var part_type = '';
					var ng_names = [];
					var ng_counts = [];
					var ng_gabungan = [];
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												product = result.resume_trend[j][k].product;
											}
										}
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'head') {
												if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'middle') {
												if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'foot') {
												if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'headyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'bodyyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'stopperyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
									}
								}
							}
						}
						var ng_name_new = [];
						var ng_unik = ng_names.filter(onlyUnique);
						for(var p = 0; p < ng_unik.length;p++){
							var count_gabungan = 0;
							for(var q = 0; q < ng_gabungan.length;q++){
								if (ng_gabungan[q].ng_name == ng_unik[p]) {
									count_gabungan = count_gabungan + parseInt(ng_gabungan[q].ng_count);
								}
							}
							ng_name_new.push({ng_name:ng_unik[p],ng_count:count_gabungan});
						}
						ng_name_new.sort(dynamicSort('ng_count'));
						
						var ng_name_fix = '';
						if ($('#select_ng_name').val() == '') {
							for(var r = 0; r < ng_name_new.length;r++){
								ng_name_fix = ng_name_new[r].ng_name;
							}
						}else{
							for(var r = 0; r < ng_name_new.length;r++){
								if (ng_name_new[r].ng_name == $('#select_ng_name').val()) {
									ng_name_fix = ng_name_new[r].ng_name;
								}
							}
						}
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '<span class="label label-success">'+product+'</span>';
						product_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix,product:product});
						product_arr_save.push({item:product,date:result.week_date[i].week_date});
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}
				tableTrend += '</tr>';

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">Part</td>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var part_name = "";
					var part_type = "";
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
									}
								}
							}
						}
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '<span class="label label-success">'+part_name+'</span><br>';
						tableTrend += '<span class="label label-success">'+part_type+'</span>';
						part_arr.push({parts:part_name+' '+part_type,part:part_name+' '+part_type});
						part_arr_save.push({item:part_name+' '+part_type,date:result.week_date[i].week_date});
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}

				tableTrend += '<tr>';
				tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red">NG</td>';
				for (var i = 0; i < result.week_date.length; i++) {
					if (result.week_date[i].week_date == result.now) {
						var colorss = 'background-color:#a6ffc2;color:black';
					}else{
						var colorss = '';
					}
					var ng_names = [];
					var ng_counts = [];
					var ng_gabungan = [];
					var part_name = '';
					var part_type = '';
					if (result.resume_trend[i].length > 0) {
						for(var j = 0; j < result.resume_trend.length;j++){
							if (result.resume_trend[j].length > 0) {
								if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
									for(var k = 0; k < result.resume_trend[j].length;k++){
										if (totalbyday[i] == 'head') {
												if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'middle') {
												if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'block') {
												if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'foot') {
												if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'headyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'bodyyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}
											if (totalbyday[i] == 'stopperyrf') {
												if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
													var ng  = result.resume_trend[j][k].ng_name.split(';');
													for(var l = 0; l < ng.length;l++){
														ng_name = ng[l].split('=')[0].split(',');
														ng_count = ng[l].split('=')[1].split(',');
														for(var o = 0; o < ng_name.length;o++){
															ng_names.push(ng_name[o]);
															ng_counts.push(ng_count[o]);
															ng_gabungan.push({ng_name:ng_name[o],ng_count:ng_count[o]});
														}
													}
												}
											}

										if (totalbyday[i] == 'head') {
											if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'middle') {
											if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'block') {
											if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'foot') {
											if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'headyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'bodyyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
										if (totalbyday[i] == 'stopperyrf') {
											if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
												part_name = result.resume_trend[j][k].part_name;
												part_type = result.resume_trend[j][k].part_type;
											}
										}
									}
								}
							}
						}
						var ng_name_new = [];
						var ng_unik = ng_names.filter(onlyUnique);
						for(var p = 0; p < ng_unik.length;p++){
							var count_gabungan = 0;
							for(var q = 0; q < ng_gabungan.length;q++){
								if (ng_gabungan[q].ng_name == ng_unik[p]) {
									count_gabungan = count_gabungan + parseInt(ng_gabungan[q].ng_count);
								}
							}
							ng_name_new.push({ng_name:ng_unik[p],ng_count:count_gabungan});
						}
						ng_name_new.sort(dynamicSort('ng_count'));
						
						var ng_name_fix = '';
						if ($('#select_ng_name').val() == '') {
							for(var r = 0; r < ng_name_new.length;r++){
								ng_name_fix = ng_name_new[r].ng_name;
							}
						}else{
							for(var r = 0; r < ng_name_new.length;r++){
								if (ng_name_new[r].ng_name == $('#select_ng_name').val()) {
									ng_name_fix = ng_name_new[r].ng_name;
								}
							}
						}

						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '<span class="label label-success">'+ng_name_fix+'</span>';
						ng_name_arr.push({parts:part_name+' '+part_type,ng_name:ng_name_fix});
						ng_name_arr_save.push({item:ng_name_fix,date:result.week_date[i].week_date});
						tableTrend += '</td>';
					}else{
						tableTrend += '<td style="padding-top:2px;padding-bottom:2px;border-bottom:2px solid red;'+colorss+'">';
						tableTrend += '</td>';
					}
				}

				var ng_part = 0;
				var total_ng_part = 0;
				var ng_machine = 0;
				var total_ng_machine = 0;
				var ng_product = 0;
				var total_ng_product = 0;
				var ng_molding = 0;
				var total_ng_molding = 0;
				var ng_person = 0;
				var total_ng_person = 0;
				var ng_name = 0;
				var total_ng_name = 0;

				
				tableTrend += '</tr>';
				$('#tableTrendHead').append(tableTrendHead);
				$('#tableTrendBody').append(tableTrend);

				var mesin_arr_new = [];
				var product_arr_new = [];
				var molding_arr_new = [];
				var person_arr_new = [];
				var part_arr_new = [];
				var ng_name_arr_new = [];

				for(var i = 0; i < part_arr.length;i++){
					part_arr_new.push(part_arr[i].part);
				}

				var partfrequent = mode(part_arr_new);

				var part_arr_frequent = [];

				for(var i = 0; i < part_arr.length;i++){
					if (part_arr[i].parts == partfrequent) {
						part_arr_frequent.push(part_arr[i].part);
					}
				}

				for(var i = 0; i < ng_name_arr.length;i++){
					if (ng_name_arr[i].parts == partfrequent) {
						ng_name_arr_new.push(ng_name_arr[i].ng_name);
					}
				}

				var ngnamefrequent = mode(ng_name_arr_new);

				ng_name_arr_new = [];

				for(var i = 0; i < ng_name_arr.length;i++){
					if (ng_name_arr[i].parts == partfrequent) {
						ng_name_arr_new.push(ng_name_arr[i].ng_name);
					}
				}

				var ng_name_arr_frequent = [];

				for(var i = 0; i < ng_name_arr.length;i++){
					if (ng_name_arr[i].parts == partfrequent && ng_name_arr[i].ng_name == ngnamefrequent) {
						ng_name_arr_frequent.push(ng_name_arr[i].ng_name);
					}
				}

				for(var i = 0; i < mesin_arr.length;i++){
					if (mesin_arr[i].parts == partfrequent && mesin_arr[i].ng_name == ngnamefrequent) {
						mesin_arr_new.push(mesin_arr[i].mesin);
					}
				}

				var mesinfrequent = mode(mesin_arr_new);

				var mesin_arr_frequent = [];

				for(var i = 0; i < mesin_arr.length;i++){
					if (mesin_arr[i].parts == partfrequent && mesin_arr[i].ng_name == ngnamefrequent && mesin_arr[i].mesin == mesinfrequent) {
						mesin_arr_frequent.push(mesin_arr[i].mesin);
					}
				}

				mesin_arr_new = [];

				for(var i = 0; i < mesin_arr.length;i++){
					if (mesin_arr[i].parts == partfrequent && mesin_arr[i].ng_name == ngnamefrequent) {
						mesin_arr_new.push(mesin_arr[i].mesin);
					}
				}


				for(var i = 0; i < product_arr.length;i++){
					if (product_arr[i].parts == partfrequent && product_arr[i].ng_name == ngnamefrequent) {
						product_arr_new.push(product_arr[i].product);
					}
				}

				var productfrequent = mode(product_arr_new);

				var product_arr_frequent = [];

				for(var i = 0; i < product_arr.length;i++){
					if (product_arr[i].parts == partfrequent && product_arr[i].ng_name == ngnamefrequent && product_arr[i].product == productfrequent) {
						product_arr_frequent.push(product_arr[i].product);
					}
				}

				product_arr_new = [];

				for(var i = 0; i < product_arr.length;i++){
					if (product_arr[i].parts == partfrequent && product_arr[i].ng_name == ngnamefrequent) {
						product_arr_new.push(product_arr[i].product);
					}
				}


				for(var i = 0; i < molding_arr.length;i++){
					if (molding_arr[i].parts == partfrequent && molding_arr[i].ng_name == ngnamefrequent) {
						molding_arr_new.push(molding_arr[i].molding);
					}
				}

				var moldingfrequent = mode(molding_arr_new);

				var molding_arr_frequent = [];

				for(var i = 0; i < molding_arr.length;i++){
					if (molding_arr[i].parts == partfrequent && molding_arr[i].ng_name == ngnamefrequent && molding_arr[i].molding == moldingfrequent) {
						molding_arr_frequent.push(molding_arr[i].molding);
					}
				}

				molding_arr_new = [];

				for(var i = 0; i < molding_arr.length;i++){
					if (molding_arr[i].parts == partfrequent && molding_arr[i].ng_name == ngnamefrequent) {
						molding_arr_new.push(molding_arr[i].molding);
					}
				}

				for(var i = 0; i < person_arr.length;i++){
					if (person_arr[i].parts == partfrequent && person_arr[i].ng_name == ngnamefrequent) {
						person_arr_new.push(person_arr[i].person);
					}
				}

				var personfrequent = mode(person_arr_new);

				var person_arr_frequent = [];

				for(var i = 0; i < person_arr.length;i++){
					if (person_arr[i].parts == partfrequent && person_arr[i].ng_name == ngnamefrequent && person_arr[i].person == personfrequent) {
						person_arr_frequent.push(person_arr[i].person);
					}
				}

				person_arr_new = [];

				for(var i = 0; i < person_arr.length;i++){
					if (person_arr[i].parts == partfrequent && person_arr[i].ng_name == ngnamefrequent) {
						person_arr_new.push(person_arr[i].person);
					}
				}

				// if (result.resume_trend.length > 0) {
				// 	for(var j = 0; j < result.resume_trend.length;j++){
				// 		if (result.resume_trend[j].length > 0) {
				// 			for(var k = 0; k < result.resume_trend[j].length;k++){
				// 				if (result.resume_trend[j][k].part_name+' '+result.resume_trend[j][k].part_type == partfrequent) {
				// 					ng_part = ng_part + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_part = total_ng_part + parseInt(result.resume_trend[j][k].ng_count);

				// 				if (result.resume_trend[j][k].mesin == mesinfrequent) {
				// 					ng_machine = ng_machine + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_machine = total_ng_machine + parseInt(result.resume_trend[j][k].ng_count);

				// 				if (result.resume_trend[j][k].product == productfrequent) {
				// 					ng_product = ng_product + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_product = total_ng_product + parseInt(result.resume_trend[j][k].ng_count);

				// 				if (result.resume_trend[j][k].molding == moldingfrequent) {
				// 					ng_molding = ng_molding + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				if (result.resume_trend[j][k].ng_name == moldingfrequent) {
				// 					ng_molding = ng_molding + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_molding = total_ng_molding + parseInt(result.resume_trend[j][k].ng_count);
				// 				var re = new RegExp(personfrequent, 'g');
				// 				if (result.resume_trend[j][k].operator_molding.match(re)) {
				// 					ng_person = ng_person + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_person = total_ng_person + parseInt(result.resume_trend[j][k].ng_count);
				// 				var re = new RegExp(ngnamefrequent, 'g');
				// 				if (result.resume_trend[j][k].ng_name.match(re)) {
				// 					ng_name = ng_name + parseInt(result.resume_trend[j][k].ng_count);
				// 				}
				// 				total_ng_name = total_ng_name + parseInt(result.resume_trend[j][k].ng_count);
				// 			}
				// 		}
				// 	}
				// }
				

				var persen_part = ((part_arr_frequent.length/part_arr.length)*100).toFixed(1);
				var persen_machine = ((mesin_arr_frequent.length/mesin_arr_new.length)*100).toFixed(1);
				var persen_product = ((product_arr_frequent.length/product_arr_new.length)*100).toFixed(1);
				var persen_molding = ((molding_arr_frequent.length/molding_arr_new.length)*100).toFixed(1);
				var persen_person = ((person_arr_frequent.length/person_arr_new.length)*100).toFixed(1);
				var persen_ng_name = ((ng_name_arr_frequent.length/ng_name_arr_new.length)*100).toFixed(1);

				$("#machine").html(mesinfrequent+' ( '+persen_machine+'% )');
				$("#product").html(productfrequent+' ( '+persen_product+'% )');
				$("#part").html(partfrequent+' ( '+persen_part+'% )');
				$("#molding").html(moldingfrequent+' ( '+persen_molding+'% )');
				$("#person").html(personfrequent+' ( '+persen_person+'% )');
				$("#ng_name").html(ngnamefrequent+' ( '+persen_ng_name+'% )');

				var datas_input = {
					resin_arr_save:resin_arr_save,
					dryer_arr_save:dryer_arr_save,
					person_injeksi_arr_save:person_injeksi_arr_save,
					mesin_arr_save:mesin_arr_save,
					molding_arr_save:molding_arr_save,
					person_arr_save:person_arr_save,
					product_arr_save:product_arr_save,
					part_arr_save:part_arr_save,
					ng_name_arr_save:ng_name_arr_save,
					date_arr_save:date_arr_save,
					qty_ng_arr_save:qty_ng_arr_save,
				}

				// $.post('{{ url("input/recorder/display/ng_trend") }}', datas_input, function(result, status, xhr) {
				// 	if(result.status){

				// 	}else{
				// 		alert('Attempt to retrieve data failed');
				// 	}
				// });
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}
function showHighlight(name,date) {
	if (name != 'Trendline Linear' || name != 'Total NG') {
		$('#loading').show();
		for (var i = 0; i < date_all.length; i++) {
			if (document.getElementById(date_all[i]) != null) {
				if (date_all[i] == '{{date("Y-m-d")}}') {
					var elms = document.querySelectorAll("[id='"+date_all[i]+"']");
					for(var j = 0; j < elms.length; j++){
						elms[j].style.backgroundColor = '#a6ffc2';
						elms[j].style.color = '#000';
					}
				}else{
					var elms = document.querySelectorAll("[id='"+date_all[i]+"']");
					for(var j = 0; j < elms.length; j++){
						elms[j].style.backgroundColor = '#605ca8';
						elms[j].style.color = '#fff';
					}
				}
			}
		}
		var elms = document.querySelectorAll("[id='"+date+"']");
		for(var i = 0; i < elms.length; i++){
			elms[i].scrollIntoView({
			  behavior: 'smooth'
			});
			elms[i].style.backgroundColor = '#9ccaff';
			elms[i].style.color = '#000';
		}

		$('#tableTrendDetail').DataTable().clear();
		$('#tableTrendDetail').DataTable().destroy();

		var data = {
			name:name,
			date:date,
			ng_name:$('#select_ng_name').val(),
		}

		$.get('{{ url("fetch/recorder/display/detail_ng_trend") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableTrendDetailBody').html('');
				var detailBody = '';
				var ng_counts = 0;

				if ($('#select_ng_name').val() != '') {
					for (var i = 0; i < result.details.length; i++) {
						var ng_name = result.details[i].ng_name.split(',');
						var ng_count = result.details[i].ng_count.split(',');
						for(j = 0; j < ng_name.length; j++){
							if (ng_name[j] == $('#select_ng_name').val()) {
								detailBody += '<tr>';
								detailBody += '<td>'+result.details[i].week_date+'</td>';
								detailBody += '<td>'+result.details[i].product+'</td>';
								detailBody += '<td>'+result.details[i].material_number+'<br>'+result.details[i].part_name.split(' ').slice(0,2).join(' ')+'</td>';
								detailBody += '<td>'+result.details[i].color+'</td>';
								detailBody += '<td>'+result.details[i].cavity+'</td>';
								detailBody += '<td>'+result.details[i].operator_molding.replace(", ", "<br>");+'</td>';
								detailBody += '<td>'+result.details[i].molding+'</td>';
								detailBody += '<td>'+result.details[i].operator_injection+'<br>'+result.details[i].injeksi_name.split(' ').slice(0,2).join(' ')+'</td>';
								detailBody += '<td>'+result.details[i].mesin+'</td>';
								detailBody += '<td>'+result.details[i].lot_number_resin+'</td>';
								detailBody += '<td>'+result.details[i].dryer_resin+'</td>';
								detailBody += '<td>'+result.details[i].operator_kensa+'<br>'+result.details[i].kensa_name.split(' ').slice(0,2).join(' ')+'</td>';
								detailBody += '<td>'+result.details[i].created_at+'</td>';
								detailBody += '<td>'+ng_name[j]+'</td>';
								detailBody += '<td>'+ng_count[j]+'</td>';
								detailBody += '</tr>';
								ng_counts = ng_counts + parseInt(ng_count[j]);
							}
						}
					}
				}else{
					for (var i = 0; i < result.details.length; i++) {
						var ng_name = result.details[i].ng_name.split(',');
						var ng_count = result.details[i].ng_count.split(',');
						for(j = 0; j < ng_name.length; j++){
							detailBody += '<tr>';
							detailBody += '<td>'+result.details[i].week_date+'</td>';
							detailBody += '<td>'+result.details[i].product+'</td>';
							detailBody += '<td>'+result.details[i].material_number+'<br>'+result.details[i].part_name.split(' ').slice(0,2).join(' ')+'</td>';
							detailBody += '<td>'+result.details[i].color+'</td>';
							detailBody += '<td>'+result.details[i].cavity+'</td>';
							detailBody += '<td>'+result.details[i].operator_molding.replace(", ", "<br>");+'</td>';
							detailBody += '<td>'+result.details[i].molding+'</td>';
							detailBody += '<td>'+result.details[i].operator_injection+'<br>'+result.details[i].injeksi_name.split(' ').slice(0,2).join(' ')+'</td>';
							detailBody += '<td>'+result.details[i].mesin+'</td>';
							detailBody += '<td>'+result.details[i].lot_number_resin+'</td>';
							detailBody += '<td>'+result.details[i].dryer_resin+'</td>';
							detailBody += '<td>'+result.details[i].operator_kensa+'<br>'+result.details[i].kensa_name.split(' ').slice(0,2).join(' ')+'</td>';
							detailBody += '<td>'+result.details[i].created_at+'</td>';
							detailBody += '<td>'+ng_name[j]+'</td>';
							detailBody += '<td>'+ng_count[j]+'</td>';
							detailBody += '</tr>';
							ng_counts = ng_counts + parseInt(ng_count[j]);
						}
					}
				}

				$('#tableTrendDetailBody').append(detailBody);
				$('#totalNgCount').html(ng_counts);

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
				$('#modalDetailTitle').html('DETAIL '+name.toUpperCase()+' ON '+result.dateTitle.toUpperCase());
				$('#loading').hide();
				$('#modalDetail').modal('show');

			}else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}
}

function frequent(number){
    var count = 0;
    var sortedNumber = number.sort();
    var start = number[0], item;
    for(var i = 0 ;  i < sortedNumber.length; i++){
      if(start === sortedNumber[i] || sortedNumber[i] === sortedNumber[i+1]){
         item = sortedNumber[i]
      }
    }
    return item
  
}


function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
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

	function mode(arr){
	    return arr.sort((a,b) =>
	          arr.filter(v => v===a).length
	        - arr.filter(v => v===b).length
	    ).pop();
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