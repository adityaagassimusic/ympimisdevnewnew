@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<!-- <form method="GET" action="{{ action('InjectionsController@getDailyStock') }}"> -->
					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
						</div>
					</div>

					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
						</div>
					</div>
					
					<div class="col-xs-1" style="padding-left: 0px;padding-right: 0px;">
						<div class="form-group">
							<button class="btn btn-success" type="button" onclick="fillTable()">Search</button>
						</div>
					</div>
				<!-- </form> -->
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_line1" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_line2" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_line3" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_line4" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_line5" style="height: 83vh;"></div>
			</div>
			<!-- <div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container_month" style="height: 83vh;"></div>
			</div> -->
		</div>
	</div>

	<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
      <div class="modal-dialog modal-lg" style="width: 1200px">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="title_detail"></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12" id="data-activity">
             	<table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
			        <thead>
				        <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
				        	<th style="padding: 5px;text-align: center;width: 1%">#</th>
					        <th style="padding: 5px;text-align: center;width: 1%">NG Name</th>
					        <th style="padding: 5px;text-align: center;width: 1%">Line</th>
					        <th style="padding: 5px;text-align: center;width: 1%">Model</th>
					        <th style="padding: 5px;text-align: center;width: 1%">Loc</th>
					        <th style="padding: 5px;text-align: center;width: 1%">Reed</th>
					        <th style="padding: 5px;text-align: center;width: 10%">OP Tuning / Bensuki</th>
					        <th style="padding: 5px;text-align: center;width: 7%">By</th>
					        <th style="padding: 5px;text-align: center;width: 4%">At</th>
				        </tr>
			        </thead>
			        <tbody id="bodyTableDetail">
			        	
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
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
{{-- <script src="{{ url('js/pdfmake.min.js')}}"></script> --}}
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
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
		fillTable();
		setInterval(fillTable, 600000);
	});

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

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {
		$('#loading').show();
		var tgl1 = $('#tanggal').val();
		var tgl2 = $('#tanggal2').val();
		var data = {
			date_from:tgl1,
			date_to:tgl2,
			origin_group_code:'{{$origin_group_code}}'
		}

		$.get('{{ url("fetch/assembly/ng_trend") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var category = [];
					var total_ok = [];
					var altos = [];
					var tenors = [];
					var ng_alto_all = [];
					var ng_tenor_all = [];
					var ng_altos = [];
					var ng_tenors = [];
					var total_ng = [];
					var ng_rate = [];
					var date = [];

					for(var i = 0; i < result.ng.length;i++){
						if (result.ng[i].model == 'YAS') {
							ng_alto_all.push(result.ng[i].serial_number);
						}
						if (result.ng[i].model == 'YTS') {
							ng_tenor_all.push(result.ng[i].serial_number);
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas.length;i++){
						if (!calendar.includes(result.datas[i].dates)) {
							date.push(result.datas[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto = 0;
						var tenor = 0;
						var ng_alto = 0;
						var ng_tenor = 0;

						for(var j = 0; j < result.datas.length;j++){
							if (result.datas[j].dates == date_unik[i]) {
								if (result.datas[j].model == 'YAS') {
									alto++;
									if (ng_alto_all.includes(result.datas[j].serial_number)) {
										ng_alto++;
									}
								}
								if (result.datas[j].model == 'YTS') {
									tenor++;
									if (ng_tenor_all.includes(result.datas[j].serial_number)) {
										ng_tenor++;
									}
								}
							}
						}

						ng_altos.push(parseInt(ng_alto));
						ng_tenors.push(parseInt(ng_tenor));
						var total_check = parseInt(alto)+parseInt(tenor);
						total_ng.push(parseInt(ng_alto)+parseInt(ng_tenor));
						ng_rate.push(parseFloat(((parseFloat(total_ng[i])/total_check)*100).toFixed(2)));
					}


					// for(i = 0; i < result.ng.length; i++){
					// 	category.push(result.ng[i].date);
					// 	biri.push({y:parseInt(result.ng[i].biri),key:result.ng[i].date});
					// 	oktaf.push({y:parseInt(result.ng[i].oktaf),key:result.ng[i].date});
					// 	t_tinggi.push({y:parseInt(result.ng[i].t_tinggi),key:result.ng[i].date});
					// 	t_rendah.push({y:parseInt(result.ng[i].t_rendah),key:result.ng[i].date});
					// 	total_ng.push(parseInt(result.ng[i].biri)+parseInt(result.ng[i].oktaf)+parseInt(result.ng[i].t_tinggi)+parseInt(result.ng[i].t_rendah));
					// }

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate.length; i++){
						if (ng_rate[i] != 0) {
							regress.push(ng_rate[i]);
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

						for(var i = 0; i < ng_rate.length; i++){
							if (ng_rate[i] == 0) {
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

						for(var i = 0; i < ng_rate.length; i++){
							if (ng_rate[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate.length; i++){
						if (ng_rate[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE ASSEMBLY",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true
						},
						{ // Third yAxis
							title: {
								text: 'Trendline',
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
							opposite: true,
							max:100
						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
							yAxis:1,
							animation: false,
							dataLabels: {
								enabled: true,
								format: '{point.y}%' ,
								style:{
									fontSize: '1vw',
									textShadow: false
								}
							},
						},
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
							colorByPoint: false,
							color: "#fff",
							yAxis:2,
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

					var category = [];
					var ng_alto_all_line_1 = [];
					var ng_tenor_all_line_1 = [];
					var ng_altos_line_1 = [];
					var ng_tenors_line_1 = [];
					var total_ng_line_1 = [];
					var ng_rate_line_1 = [];
					var date = [];

					for(var i = 0; i < result.ng_line.length;i++){
						if (result.ng_line[i].model == 'YAS') {
							if (result.ng_line[i].location == 1) {
								ng_alto_all_line_1.push(result.ng_line[i].serial_number);
							}
						}
						if (result.ng_line[i].model == 'YTS') {
							if (result.ng_line[i].location == 1) {
								ng_tenor_all_line_1.push(result.ng_line[i].serial_number);
							}
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas_line.length;i++){
						if (!calendar.includes(result.datas_line[i].dates)) {
							date.push(result.datas_line[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);
					console.log(date_unik);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto_line_1 = 0;
						var tenor_line_1 = 0;
						var ng_alto_line_1 = 0;
						var ng_tenor_line_1 = 0;
						
						for(var j = 0; j < result.datas_line.length;j++){

							if (result.datas_line[j].location == 1) {
								if (result.datas_line[j].dates == date_unik[i]) {
									if (result.datas_line[j].model == 'YAS') {
										alto_line_1++;
										if (ng_alto_all_line_1.includes(result.datas_line[j].serial_number)) {
											ng_alto_line_1++;
										}
									}
									if (result.datas_line[j].model == 'YTS') {
										tenor_line_1++;
										if (ng_tenor_all_line_1.includes(result.datas_line[j].serial_number)) {
											ng_tenor_line_1++;
										}
									}
								}
							}

						}

						ng_altos_line_1.push(parseInt(ng_alto_line_1));
						ng_tenors_line_1.push(parseInt(ng_tenor_line_1));
						var total_check_line_1 = parseInt(alto_line_1)+parseInt(tenor_line_1);
						total_ng_line_1.push(parseInt(ng_alto_line_1)+parseInt(ng_tenor_line_1));
						ng_rate_line_1.push(parseFloat(((parseFloat(total_ng_line_1[i])/total_check_line_1)*100).toFixed(2)));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate_line_1.length; i++){
						if (ng_rate_line_1[i] != 0) {
							regress.push(ng_rate_line_1[i]);
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

						for(var i = 0; i < ng_rate_line_1.length; i++){
							if (ng_rate_line_1[i] == 0) {
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

						for(var i = 0; i < ng_rate_line_1.length; i++){
							if (ng_rate_line_1[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate_line_1.length; i++){
						if (ng_rate_line_1[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate_line_1[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate_line_1.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container_line1', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE LINE 1",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true

						},
						{ // Third yAxis
							title: {
								text: 'Trendline',
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
							opposite: true,
							max:100
						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos_line_1,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors_line_1,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate_line_1,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
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
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
							colorByPoint: false,
							color: "#fff",
							animation: false,
							dashStyle:'shortdash',
							lineWidth: 4,
							yAxis:2,
							marker: {
				                radius: 4,
				                lineColor: '#fff',
				                lineWidth: 1
				            },
						},
						]
					});

					var category = [];
					var ng_alto_all_line_2 = [];
					var ng_tenor_all_line_2 = [];
					var ng_altos_line_2 = [];
					var ng_tenors_line_2 = [];
					var total_ng_line_2 = [];
					var ng_rate_line_2 = [];
					var date = [];

					for(var i = 0; i < result.ng_line.length;i++){
						if (result.ng_line[i].model == 'YAS') {
							if (result.ng_line[i].location == 2) {
								ng_alto_all_line_2.push(result.ng_line[i].serial_number);
							}
						}
						if (result.ng_line[i].model == 'YTS') {
							if (result.ng_line[i].location == 2) {
								ng_tenor_all_line_2.push(result.ng_line[i].serial_number);
							}
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas_line.length;i++){
						if (!calendar.includes(result.datas_line[i].dates)) {
							date.push(result.datas_line[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto_line_2 = 0;
						var tenor_line_2 = 0;
						var ng_alto_line_2 = 0;
						var ng_tenor_line_2 = 0;
						
						for(var j = 0; j < result.datas_line.length;j++){

							if (result.datas_line[j].location == 2) {
								if (result.datas_line[j].dates == date_unik[i]) {
									if (result.datas_line[j].model == 'YAS') {
										alto_line_2++;
										if (ng_alto_all_line_2.includes(result.datas_line[j].serial_number)) {
											ng_alto_line_2++;
										}
									}
									if (result.datas_line[j].model == 'YTS') {
										tenor_line_2++;
										if (ng_tenor_all_line_2.includes(result.datas_line[j].serial_number)) {
											ng_tenor_line_2++;
										}
									}
								}
							}

						}

						ng_altos_line_2.push(parseInt(ng_alto_line_2));
						ng_tenors_line_2.push(parseInt(ng_tenor_line_2));
						var total_check_line_2 = parseInt(alto_line_2)+parseInt(tenor_line_2);
						total_ng_line_2.push(parseInt(ng_alto_line_2)+parseInt(ng_tenor_line_2));
						ng_rate_line_2.push(parseFloat(((parseFloat(total_ng_line_2[i])/total_check_line_2)*100).toFixed(2)));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate_line_2.length; i++){
						if (ng_rate_line_2[i] != 0) {
							regress.push(ng_rate_line_2[i]);
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

						for(var i = 0; i < ng_rate_line_2.length; i++){
							if (ng_rate_line_2[i] == 0) {
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

						for(var i = 0; i < ng_rate_line_2.length; i++){
							if (ng_rate_line_2[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate_line_2.length; i++){
						if (ng_rate_line_2[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate_line_2[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate_line_2.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container_line2', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE LINE 2",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true

						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos_line_2,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors_line_2,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate_line_2,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
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
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
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

					var category = [];
					var ng_alto_all_line_3 = [];
					var ng_tenor_all_line_3 = [];
					var ng_altos_line_3 = [];
					var ng_tenors_line_3 = [];
					var total_ng_line_3 = [];
					var ng_rate_line_3 = [];
					var date = [];

					for(var i = 0; i < result.ng_line.length;i++){
						if (result.ng_line[i].model == 'YAS') {
							if (result.ng_line[i].location == 3) {
								ng_alto_all_line_3.push(result.ng_line[i].serial_number);
							}
						}
						if (result.ng_line[i].model == 'YTS') {
							if (result.ng_line[i].location == 3) {
								ng_tenor_all_line_3.push(result.ng_line[i].serial_number);
							}
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas_line.length;i++){
						if (!calendar.includes(result.datas_line[i].dates)) {
							date.push(result.datas_line[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto_line_3 = 0;
						var tenor_line_3 = 0;
						var ng_alto_line_3 = 0;
						var ng_tenor_line_3 = 0;
						
						for(var j = 0; j < result.datas_line.length;j++){

							if (result.datas_line[j].location == 3) {
								if (result.datas_line[j].dates == date_unik[i]) {
									if (result.datas_line[j].model == 'YAS') {
										alto_line_3++;
										if (ng_alto_all_line_3.includes(result.datas_line[j].serial_number)) {
											ng_alto_line_3++;
										}
									}
									if (result.datas_line[j].model == 'YTS') {
										tenor_line_3++;
										if (ng_tenor_all_line_3.includes(result.datas_line[j].serial_number)) {
											ng_tenor_line_3++;
										}
									}
								}
							}

						}

						ng_altos_line_3.push(parseInt(ng_alto_line_3));
						ng_tenors_line_3.push(parseInt(ng_tenor_line_3));
						var total_check_line_3 = parseInt(alto_line_3)+parseInt(tenor_line_3);
						total_ng_line_3.push(parseInt(ng_alto_line_3)+parseInt(ng_tenor_line_3));
						ng_rate_line_3.push(parseFloat(((parseFloat(total_ng_line_3[i])/total_check_line_3)*100).toFixed(2)));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate_line_3.length; i++){
						if (ng_rate_line_3[i] != 0) {
							regress.push(ng_rate_line_3[i]);
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

						for(var i = 0; i < ng_rate_line_3.length; i++){
							if (ng_rate_line_3[i] == 0) {
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

						for(var i = 0; i < ng_rate_line_3.length; i++){
							if (ng_rate_line_3[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate_line_3.length; i++){
						if (ng_rate_line_3[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate_line_3[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate_line_3.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container_line3', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE LINE 3",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true

						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos_line_3,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors_line_3,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate_line_3,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
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
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
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

					var category = [];
					var ng_alto_all_line_4 = [];
					var ng_tenor_all_line_4 = [];
					var ng_altos_line_4 = [];
					var ng_tenors_line_4 = [];
					var total_ng_line_4 = [];
					var ng_rate_line_4 = [];
					var date = [];

					for(var i = 0; i < result.ng_line.length;i++){
						if (result.ng_line[i].model == 'YAS') {
							if (result.ng_line[i].location == 4) {
								ng_alto_all_line_4.push(result.ng_line[i].serial_number);
							}
						}
						if (result.ng_line[i].model == 'YTS') {
							if (result.ng_line[i].location == 4) {
								ng_tenor_all_line_4.push(result.ng_line[i].serial_number);
							}
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas_line.length;i++){
						if (!calendar.includes(result.datas_line[i].dates)) {
							date.push(result.datas_line[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto_line_4 = 0;
						var tenor_line_4 = 0;
						var ng_alto_line_4 = 0;
						var ng_tenor_line_4 = 0;
						
						for(var j = 0; j < result.datas_line.length;j++){

							if (result.datas_line[j].location == 4) {
								if (result.datas_line[j].dates == date_unik[i]) {
									if (result.datas_line[j].model == 'YAS') {
										alto_line_4++;
										if (ng_alto_all_line_4.includes(result.datas_line[j].serial_number)) {
											ng_alto_line_4++;
										}
									}
									if (result.datas_line[j].model == 'YTS') {
										tenor_line_4++;
										if (ng_tenor_all_line_4.includes(result.datas_line[j].serial_number)) {
											ng_tenor_line_4++;
										}
									}
								}
							}

						}

						ng_altos_line_4.push(parseInt(ng_alto_line_4));
						ng_tenors_line_4.push(parseInt(ng_tenor_line_4));
						var total_check_line_4 = parseInt(alto_line_4)+parseInt(tenor_line_4);
						total_ng_line_4.push(parseInt(ng_alto_line_4)+parseInt(ng_tenor_line_4));
						ng_rate_line_4.push(parseFloat(((parseFloat(total_ng_line_4[i])/total_check_line_4)*100).toFixed(2)));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate_line_4.length; i++){
						if (ng_rate_line_4[i] != 0) {
							regress.push(ng_rate_line_4[i]);
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

						for(var i = 0; i < ng_rate_line_4.length; i++){
							if (ng_rate_line_4[i] == 0) {
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

						for(var i = 0; i < ng_rate_line_4.length; i++){
							if (ng_rate_line_4[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate_line_4.length; i++){
						if (ng_rate_line_4[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate_line_4[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate_line_4.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container_line4', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE LINE 4",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true

						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos_line_4,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors_line_4,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate_line_4,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
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
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
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


					var category = [];
					var ng_alto_all_line_5 = [];
					var ng_tenor_all_line_5 = [];
					var ng_altos_line_5 = [];
					var ng_tenors_line_5 = [];
					var total_ng_line_5 = [];
					var ng_rate_line_5 = [];
					var date = [];

					for(var i = 0; i < result.ng_line.length;i++){
						if (result.ng_line[i].model == 'YAS') {
							if (result.ng_line[i].location == 5) {
								ng_alto_all_line_5.push(result.ng_line[i].serial_number);
							}
						}
						if (result.ng_line[i].model == 'YTS') {
							if (result.ng_line[i].location == 5) {
								ng_tenor_all_line_5.push(result.ng_line[i].serial_number);
							}
						}
					}

					var calendar = [];
					for(var i = 0; i < result.calendar.length;i++){
						calendar.push(result.calendar[i].week_date);
					}

					for(var i = 0; i < result.datas_line.length;i++){
						if (!calendar.includes(result.datas_line[i].dates)) {
							date.push(result.datas_line[i].dates);
						}
					}

					var date_unik = date.filter(onlyUnique);

					for(var i = 0; i < date_unik.length;i++){
						category.push(date_unik[i]);
						var alto_line_5 = 0;
						var tenor_line_5 = 0;
						var ng_alto_line_5 = 0;
						var ng_tenor_line_5 = 0;
						
						for(var j = 0; j < result.datas_line.length;j++){

							if (result.datas_line[j].location == 5) {
								if (result.datas_line[j].dates == date_unik[i]) {
									if (result.datas_line[j].model == 'YAS') {
										alto_line_5++;
										if (ng_alto_all_line_5.includes(result.datas_line[j].serial_number)) {
											ng_alto_line_5++;
										}
									}
									if (result.datas_line[j].model == 'YTS') {
										tenor_line_5++;
										if (ng_tenor_all_line_5.includes(result.datas_line[j].serial_number)) {
											ng_tenor_line_5++;
										}
									}
								}
							}

						}

						ng_altos_line_5.push(parseInt(ng_alto_line_5));
						ng_tenors_line_5.push(parseInt(ng_tenor_line_5));
						var total_check_line_5 = parseInt(alto_line_5)+parseInt(tenor_line_5);
						total_ng_line_5.push(parseInt(ng_alto_line_5)+parseInt(ng_tenor_line_5));
						ng_rate_line_5.push(parseFloat(((parseFloat(total_ng_line_5[i])/total_check_line_5)*100).toFixed(2)));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < ng_rate_line_5.length; i++){
						if (ng_rate_line_5[i] != 0) {
							regress.push(ng_rate_line_5[i]);
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

						for(var i = 0; i < ng_rate_line_5.length; i++){
							if (ng_rate_line_5[i] == 0) {
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

						for(var i = 0; i < ng_rate_line_5.length; i++){
							if (ng_rate_line_5[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < ng_rate_line_5.length; i++){
						if (ng_rate_line_5[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(ng_rate_line_5[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = ng_rate_line_5.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					Highcharts.chart('container_line5', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "TREND NG RATE LINE 5",
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: result.monthTitle,
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
								text: 'Qty NG',
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
						},
						{ // Secondary yAxis
							title: {
								text: 'NG Rate (%)',
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
							opposite: true

						}
						],
						// tooltip: {
						// 	headerFormat: '<span>NG Name</span><br/>',
						// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
						// },
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
											showHighlight(this.series.name,this.category,'All');
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
								stacking: 'normal'
							},
						},
						series: [
						{
							type: 'column',
							data: ng_altos_line_5,
							name: "Alto",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: ng_tenors_line_5,
							name: "Tenor",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: ng_rate_line_5,
							name: 'NG Rate',
							colorByPoint: false,
							color:'#d62d2d',
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
						// {
						// 	type: 'spline',
						// 	data: ng_rate,
						// 	name: "NG Rate",
						// 	colorByPoint: false,
						// 	color: "#d62d2d",
						// 	animation: false,
						// 	marker: {
				  //               radius: 4,
				  //               lineColor: '#ff0000',
				  //               lineWidth: 2
				  //           },
						// },
						{
							type: 'line',
							data: regressions_total,
							name: "Trendline Linear NG Rate",
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



					//MONTHLY
					// var category = [];
					// var total_ok = [];
					// var altos = [];
					// var tenors = [];
					// var ng_alto_all = [];
					// var ng_tenor_all = [];
					// var ng_altos = [];
					// var ng_tenors = [];
					// var total_ng = [];
					// var ng_rate = [];
					// var month = [];

					// for(var i = 0; i < result.ng.length;i++){
					// 	if (result.ng[i].model == 'YAS') {
					// 		ng_alto_all.push(result.ng[i].serial_number);
					// 	}
					// 	if (result.ng[i].model == 'YTS') {
					// 		ng_tenor_all.push(result.ng[i].serial_number);
					// 	}
					// }

					// var calendar = [];
					// for(var i = 0; i < result.calendar.length;i++){
					// 	calendar.push(result.calendar[i].week_date);
					// }

					// for(var i = 0; i < result.datas_monthly.length;i++){
					// 	if (!calendar.includes(result.datas_monthly[i].dates)) {
					// 		month.push(result.datas_monthly[i].dates);
					// 	}
					// }

					// var month_unik = month.filter(onlyUnique);

					// for(var i = 0; i < month_unik.length;i++){
					// 	category.push(month_unik[i]);
					// 	var alto = 0;
					// 	var tenor = 0;
					// 	var ng_alto = 0;
					// 	var ng_tenor = 0;
					// 	for(var j = 0; j < result.datas_monthly.length;j++){
					// 		if (result.datas_monthly[j].dates == month_unik[i]) {
					// 			if (result.datas_monthly[j].model == 'YAS') {
					// 				alto++;
					// 				if (ng_alto_all.includes(result.datas_monthly[j].serial_number)) {
					// 					ng_alto++;
					// 				}
					// 			}
					// 			if (result.datas_monthly[j].model == 'YTS') {
					// 				tenor++;
					// 				if (ng_tenor_all.includes(result.datas_monthly[j].serial_number)) {
					// 					ng_tenor++;
					// 				}
					// 			}
					// 		}
					// 	}

					// 	ng_altos.push(parseInt(ng_alto));
					// 	ng_tenors.push(parseInt(ng_tenor));
					// 	var total_check = parseInt(alto)+parseInt(tenor);
					// 	total_ng.push(parseInt(ng_alto)+parseInt(ng_tenor));
					// 	ng_rate.push(parseFloat(((parseFloat(total_ng[i])/total_check)*100).toFixed(2)));
					// }


					// for(i = 0; i < result.ng.length; i++){
					// 	category.push(result.ng[i].date);
					// 	biri.push({y:parseInt(result.ng[i].biri),key:result.ng[i].date});
					// 	oktaf.push({y:parseInt(result.ng[i].oktaf),key:result.ng[i].date});
					// 	t_tinggi.push({y:parseInt(result.ng[i].t_tinggi),key:result.ng[i].date});
					// 	t_rendah.push({y:parseInt(result.ng[i].t_rendah),key:result.ng[i].date});
					// 	total_ng.push(parseInt(result.ng[i].biri)+parseInt(result.ng[i].oktaf)+parseInt(result.ng[i].t_tinggi)+parseInt(result.ng[i].t_rendah));
					// }

					//TOTAL
					// var regress = [];

					// for(var i = 0; i < ng_rate.length; i++){
					// 	if (ng_rate[i] != 0) {
					// 		regress.push(ng_rate[i]);
					// 	}
					// }

					// if (regress.length % 2 == 0) {
					// 	var totalsmedian = regress;
					// 	var indexMedianBawah = (totalsmedian.length/2)-1;
					// 	var indexMedianAtas = (totalsmedian.length/2);
					// 	var indexMinus = -(indexMedianAtas+indexMedianBawah);
					// 	var indexPlus = 1;
					// 	var xLinear = [];
					// 	for(var i = 0; i < totalsmedian.length;i++){
					// 		if (totalsmedian[i] != 0) {
					// 			if (i == indexMedianBawah) {
					// 				xLinear.push(-1);
					// 				indexMinus = indexMinus + 2;
					// 			}else if(i == indexMedianAtas){
					// 				xLinear.push(indexPlus);
					// 				indexPlus = indexPlus + 2;
					// 			}else if(i < indexMedianBawah){
					// 				xLinear.push(indexMinus);
					// 				indexMinus = indexMinus + 2;
					// 			}else if(i > indexMedianAtas){
					// 				xLinear.push(indexPlus);
					// 				indexPlus = indexPlus + 2;
					// 			}
					// 		}
					// 	}

					// 	for(var i = 0; i < ng_rate.length; i++){
					// 		if (ng_rate[i] == 0) {
					// 			xLinear.push(indexPlus);
					// 			indexPlus = indexPlus + 2;
					// 		}
					// 	}
					// }else{
					// 	var totalsmedian = regress;
					// 	var indexMedian = Math.round(totalsmedian.length/2)-1;
					// 	var indexMinus = -indexMedian;
					// 	var indexPlus = 1;
					// 	var xLinear = [];
					// 	for(var i = 0; i < totalsmedian.length;i++){
					// 		if (totalsmedian[i] != 0) {
					// 			if (i == indexMedian) {
					// 				xLinear.push(0);
					// 			}else if(i < indexMedian){
					// 				xLinear.push(indexMinus);
					// 				indexMinus++;
					// 			}else if(i > indexMedian){
					// 				xLinear.push(indexPlus);
					// 				indexPlus++;
					// 			}
					// 		}
					// 	}

					// 	for(var i = 0; i < ng_rate.length; i++){
					// 		if (ng_rate[i] == 0) {
					// 			xLinear.push(indexPlus);
					// 			indexPlus++;
					// 		}
					// 	}
					// }

					// var xy = [];
					// var xkuadrat = [];

					// for(var i = 0; i < ng_rate.length; i++){
					// 	if (ng_rate[i] != 0) {
					// 		xy.push(parseInt(xLinear[i])*parseInt(ng_rate[i]));
					// 		xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
					// 	}
					// }

					// var sumy = ng_rate.reduce((a, b) => a + b, 0);
					// var sumxy = xy.reduce((a, b) => a + b, 0);
					// var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					// var a = sumy/totalsmedian.length;
					// var b = sumxy/sumxkuadrat;

					// var regressions_total = [];

					// for(var i = 0; i < regress.length; i++){
					// 	regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					// }

					// Highcharts.chart('container_month', {
					//     chart: {
					// 		type: 'column',
					// 		backgroundColor: "rgba(0,0,0,0)"
					// 	},
					// 	title: {
					// 		text: "NG RATE ASSEMBLY",
					// 		style: {
					// 			fontSize: '30px',
					// 			fontWeight: 'bold'
					// 		}
					// 	},
						// subtitle: {
						// 	text: result.monthTitle,
						// 	style: {
						// 		fontSize: '1vw',
						// 		fontWeight: 'bold'
						// 	}
						// },
					// 	xAxis: {
					// 		categories:category,
					// 		type: 'category',
					// 		gridLineWidth: 1,
					// 		gridLineColor: 'RGB(204,255,255)',
					// 		lineWidth:2,
					// 		lineColor:'#9e9e9e',
					// 		labels: {
					// 			style: {
					// 				fontSize: '16px',
					// 				fontWeight: 'bold'
					// 			}
					// 		},
					// 	},
					// 	yAxis: [{
					// 		title: {
					// 			text: 'Qty NG',
					// 			style: {
					// 				color: '#eee',
					// 				fontSize: '18px',
					// 				fontWeight: 'bold',
					// 				fill: '#6d869f'
					// 			}
					// 		},
					// 		labels:{
					// 			style:{
					// 				fontSize:"14px"
					// 			}
					// 		},
					// 		type: 'linear',
					// 	},
					// 	{ // Secondary yAxis
					// 		title: {
					// 			text: 'NG Rate (%)',
					// 			style: {
					// 				color: '#eee',
					// 				fontSize: '20px',
					// 				fontWeight: 'bold',
					// 				fill: '#6d869f'
					// 			}
					// 		},
					// 		labels:{
					// 			style:{
					// 				fontSize:"16px"
					// 			}
					// 		},
					// 		type: 'linear',
					// 		opposite: true

					// 	}
					// 	],
					// 	// tooltip: {
					// 	// 	headerFormat: '<span>NG Name</span><br/>',
					// 	// 	pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					// 	// },
					// 	legend: {
					// 		enabled:true
					// 	},	
					// 	credits: {
					// 		enabled: false
					// 	},
					// 	plotOptions: {
					// 		series:{
					// 			cursor: 'pointer',
					// 			point: {
					// 				events: {
					// 					click: function (e) {
					// 						showHighlight(this.series.name,this.category,'All');
					// 					}
					// 				}
					// 			},
					// 			dataLabels: {
					// 				enabled: true,
					// 				// format: '{point.y}',
					// 				style:{
					// 					fontSize: '1vw'
					// 				},
					// 				formatter: function(){
				 //                    	return (this.y!=0)?this.y:"";
				 //                    }
					// 			},
					// 			animation: {
					// 				enabled: true,
					// 				duration: 800
					// 			},
					// 			pointPadding: 0.93,
					// 			groupPadding: 0.93,
					// 			borderWidth: 0.93,
					// 			stacking: 'normal'
					// 		},
					// 	},
					// 	series: [
					// 	{
					// 		type: 'column',
					// 		data: ng_altos,
					// 		name: "Alto",
					// 		colorByPoint: false,
					// 		color: "#788cff",
					// 		animation: false,
					// 		stack:'GG'
					// 	},
					// 	{
					// 		type: 'column',
					// 		data: ng_tenors,
					// 		name: "Tenor",
					// 		colorByPoint: false,
					// 		color: "#aa6eff",
					// 		animation: false,
					// 		stack:'GG'
					// 	},
					// 	{
					// 		type: 'spline',
					// 		data: ng_rate,
					// 		name: 'NG Rate',
					// 		colorByPoint: false,
					// 		color:'#d62d2d',
					// 		yAxis:1,
					// 		animation: false,
					// 		dataLabels: {
					// 			enabled: true,
					// 			format: '{point.y}%' ,
					// 			style:{
					// 				fontSize: '1vw',
					// 				textShadow: false
					// 			},
					// 		},
					// 	},
					// 	// {
					// 	// 	type: 'spline',
					// 	// 	data: ng_rate,
					// 	// 	name: "NG Rate",
					// 	// 	colorByPoint: false,
					// 	// 	color: "#d62d2d",
					// 	// 	animation: false,
					// 	// 	marker: {
				 //  //               radius: 4,
				 //  //               lineColor: '#ff0000',
				 //  //               lineWidth: 2
				 //  //           },
					// 	// },
					// 	{
					// 		type: 'line',
					// 		data: regressions_total,
					// 		name: "Trendline Linear NG Rate",
					// 		colorByPoint: false,
					// 		color: "#fff",
					// 		animation: false,
					// 		dashStyle:'shortdash',
					// 		lineWidth: 4,
					// 		marker: {
				 //                radius: 4,
				 //                lineColor: '#fff',
				 //                lineWidth: 1
				 //            },
					// 	},
					// 	]
					// });

					$('#loading').hide();
				}else{
					alert('Fill Data Failed');
					$('#loading').hide();
				}
			}
		});
	}

	function showHighlight(ng_name,date,line) {
		$('#loading').show();
		if (ng_name == 'Terlalu Tinggi') {
			ng_name = 'T. Tinggi';
		}
		if (ng_name == 'Terlalu Rendah') {
			ng_name = 'T. Rendah';
		}
		var data = {
			ng_name:ng_name,
			date:date,
			line:line,
			location:'PN_Kensa_Awal'
		}

		$.get('{{ url("fetch/detailKensaAwalDaily") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();
				$('#bodyTableDetail').html('');
				var tableData = '';

				$.each(result.kensa, function(key, value){
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+(key+1)+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+value.ng_name+'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+value.line+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+value.model+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+value.location+'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+value.reed+'</td>';
					var name_tuning = '';
					var name_bensuki = '';
					var name_created = '';
					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.id_tuning) {
							name_tuning = result.emp[i].name;
						}
						if (result.emp[i].employee_id == value.id_bensuki) {
							name_bensuki = result.emp[i].name;
						}
						if (result.emp[i].employee_id == value.created_by) {
							name_created = result.emp[i].name;
						}
					}
					tableData += '<td style="text-align:left;padding-left:7px;">'+value.id_tuning+' - '+name_tuning+'<br>'+value.id_bensuki+' - '+name_bensuki+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+value.created_by+' - '+name_created+'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+value.created_at+'</td>';
					tableData += '</tr>';
				});

				$('#bodyTableDetail').append(tableData);

				var table = $('#tableDetail').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					"processing": true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#modalDetail').modal('show');
				$("#title_detail").html('Detail NG '+ng_name+' Tanggal '+date+' Line '+line);
				$('#loading').hide();
			}else{
				$('#loading').hide();
				alert('Failed Get Data');
			}
		});
	}

</script>
@stop