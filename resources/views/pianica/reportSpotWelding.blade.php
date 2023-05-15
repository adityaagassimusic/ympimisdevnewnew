@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		/*text-align: center;*/
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		/*text-align: center;*/
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		/*text-align: center;*/
	}
	.content{
		color: white;
		/*font-weight: bold;*/
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
				<div id="container1" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container2" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container3" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container4" style="height: 83vh;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container5" style="height: 83vh;"></div>
			</div>
		</div>
	</div>

<div class="modal fade" id="modalProgress">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
          <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;background-color: orange;margin-bottom: 20px;text-align: center;">
          	<span id="modalProgressTitle" style="font-weight: bold;font-size: 20px;padding: 10px;"></span>
          </div>
          <center>
            <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
          </center>
          <table class="table table-hover table-bordered table-striped" id="tableModal">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th style="text-align: center;">NG Name</th>
                <th style="text-align: center;">Total</th>              
              </tr>
            </thead>
            <tbody id="modalProgressBody">
            </tbody>
            <tfoot style="background-color: rgb(252, 248, 227);">
              <th style="color: black;font-size:12pt;text-align: left;padding-left: 4px;">Total</th>
              <th id="totalP" style="color: black;font-size:12pt;text-align: right;padding-right: 4px;"></th>
                           
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
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
			date_to:tgl2
		}

		$.get('{{ url("fetch/reportSpotWeldingData") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng.length; i++){
						category.push(result.ng[i].dates);
						mesin_1.push({y:parseInt(result.ng[i].mesin_1),key:result.ng[i].dates});
						mesin_2.push({y:parseInt(result.ng[i].mesin_2),key:result.ng[i].dates});
						mesin_3.push({y:parseInt(result.ng[i].mesin_3),key:result.ng[i].dates});
						mesin_4.push({y:parseInt(result.ng[i].mesin_4),key:result.ng[i].dates});
						mesin_5.push({y:parseInt(result.ng[i].mesin_5),key:result.ng[i].dates});
						mesin_6.push({y:parseInt(result.ng[i].mesin_6),key:result.ng[i].dates});
						total_all_ng.push(parseInt(result.ng[i].mesin_1)+parseInt(result.ng[i].mesin_2)+parseInt(result.ng[i].mesin_3)+parseInt(result.ng[i].mesin_4)+parseInt(result.ng[i].mesin_5)+parseInt(result.ng[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
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
							text: "DAILY NG SPOT WELDING",
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
											fillModal(this.category,this.series.name,'All');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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

					//LINE 1

					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng_line_1.length; i++){
						category.push(result.ng_line_1[i].dates);
						mesin_1.push({y:parseInt(result.ng_line_1[i].mesin_1),key:result.ng_line_1[i].dates});
						mesin_2.push({y:parseInt(result.ng_line_1[i].mesin_2),key:result.ng_line_1[i].dates});
						mesin_3.push({y:parseInt(result.ng_line_1[i].mesin_3),key:result.ng_line_1[i].dates});
						mesin_4.push({y:parseInt(result.ng_line_1[i].mesin_4),key:result.ng_line_1[i].dates});
						mesin_5.push({y:parseInt(result.ng_line_1[i].mesin_5),key:result.ng_line_1[i].dates});
						mesin_6.push({y:parseInt(result.ng_line_1[i].mesin_6),key:result.ng_line_1[i].dates});
						total_all_ng.push(parseInt(result.ng_line_1[i].mesin_1)+parseInt(result.ng_line_1[i].mesin_2)+parseInt(result.ng_line_1[i].mesin_3)+parseInt(result.ng_line_1[i].mesin_4)+parseInt(result.ng_line_1[i].mesin_5)+parseInt(result.ng_line_1[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					
					Highcharts.chart('container1', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "DAILY NG SPOT WELDING LINE 1",
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
											fillModal(this.category,this.series.name,'1');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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

					//LINE 2

					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng_line_2.length; i++){
						category.push(result.ng_line_2[i].dates);
						mesin_1.push({y:parseInt(result.ng_line_2[i].mesin_1),key:result.ng_line_2[i].dates});
						mesin_2.push({y:parseInt(result.ng_line_2[i].mesin_2),key:result.ng_line_2[i].dates});
						mesin_3.push({y:parseInt(result.ng_line_2[i].mesin_3),key:result.ng_line_2[i].dates});
						mesin_4.push({y:parseInt(result.ng_line_2[i].mesin_4),key:result.ng_line_2[i].dates});
						mesin_5.push({y:parseInt(result.ng_line_2[i].mesin_5),key:result.ng_line_2[i].dates});
						mesin_6.push({y:parseInt(result.ng_line_2[i].mesin_6),key:result.ng_line_2[i].dates});
						total_all_ng.push(parseInt(result.ng_line_2[i].mesin_1)+parseInt(result.ng_line_2[i].mesin_2)+parseInt(result.ng_line_2[i].mesin_3)+parseInt(result.ng_line_2[i].mesin_4)+parseInt(result.ng_line_2[i].mesin_5)+parseInt(result.ng_line_2[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					
					Highcharts.chart('container2', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "DAILY NG SPOT WELDING LINE 2",
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
											fillModal(this.category,this.series.name,'2');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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

					//LINE 3

					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng_line_3.length; i++){
						category.push(result.ng_line_3[i].dates);
						mesin_1.push({y:parseInt(result.ng_line_3[i].mesin_1),key:result.ng_line_3[i].dates});
						mesin_2.push({y:parseInt(result.ng_line_3[i].mesin_2),key:result.ng_line_3[i].dates});
						mesin_3.push({y:parseInt(result.ng_line_3[i].mesin_3),key:result.ng_line_3[i].dates});
						mesin_4.push({y:parseInt(result.ng_line_3[i].mesin_4),key:result.ng_line_3[i].dates});
						mesin_5.push({y:parseInt(result.ng_line_3[i].mesin_5),key:result.ng_line_3[i].dates});
						mesin_6.push({y:parseInt(result.ng_line_3[i].mesin_6),key:result.ng_line_3[i].dates});
						total_all_ng.push(parseInt(result.ng_line_3[i].mesin_1)+parseInt(result.ng_line_3[i].mesin_2)+parseInt(result.ng_line_3[i].mesin_3)+parseInt(result.ng_line_3[i].mesin_4)+parseInt(result.ng_line_3[i].mesin_5)+parseInt(result.ng_line_3[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					
					Highcharts.chart('container3', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "DAILY NG SPOT WELDING LINE 3",
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
											fillModal(this.category,this.series.name,'3');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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

					//LINE 4

					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng_line_4.length; i++){
						category.push(result.ng_line_4[i].dates);
						mesin_1.push({y:parseInt(result.ng_line_4[i].mesin_1),key:result.ng_line_4[i].dates});
						mesin_2.push({y:parseInt(result.ng_line_4[i].mesin_2),key:result.ng_line_4[i].dates});
						mesin_3.push({y:parseInt(result.ng_line_4[i].mesin_3),key:result.ng_line_4[i].dates});
						mesin_4.push({y:parseInt(result.ng_line_4[i].mesin_4),key:result.ng_line_4[i].dates});
						mesin_5.push({y:parseInt(result.ng_line_4[i].mesin_5),key:result.ng_line_4[i].dates});
						mesin_6.push({y:parseInt(result.ng_line_4[i].mesin_6),key:result.ng_line_4[i].dates});
						total_all_ng.push(parseInt(result.ng_line_4[i].mesin_1)+parseInt(result.ng_line_4[i].mesin_2)+parseInt(result.ng_line_4[i].mesin_3)+parseInt(result.ng_line_4[i].mesin_4)+parseInt(result.ng_line_4[i].mesin_5)+parseInt(result.ng_line_4[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					
					Highcharts.chart('container4', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "DAILY NG SPOT WELDING LINE 4",
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
											fillModal(this.category,this.series.name,'4');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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

					//LINE 5

					var category = [];
					var prod_result = [];
					var mesin_1 = [];
					var mesin_2 = [];
					var mesin_3 = [];
					var mesin_4 = [];
					var mesin_5 = [];
					var mesin_6 = [];
					var total_all_ng = [];
					var total_ng = [];


					for(i = 0; i < result.ng_line_5.length; i++){
						category.push(result.ng_line_5[i].dates);
						mesin_1.push({y:parseInt(result.ng_line_5[i].mesin_1),key:result.ng_line_5[i].dates});
						mesin_2.push({y:parseInt(result.ng_line_5[i].mesin_2),key:result.ng_line_5[i].dates});
						mesin_3.push({y:parseInt(result.ng_line_5[i].mesin_3),key:result.ng_line_5[i].dates});
						mesin_4.push({y:parseInt(result.ng_line_5[i].mesin_4),key:result.ng_line_5[i].dates});
						mesin_5.push({y:parseInt(result.ng_line_5[i].mesin_5),key:result.ng_line_5[i].dates});
						mesin_6.push({y:parseInt(result.ng_line_5[i].mesin_6),key:result.ng_line_5[i].dates});
						total_all_ng.push(parseInt(result.ng_line_5[i].mesin_1)+parseInt(result.ng_line_5[i].mesin_2)+parseInt(result.ng_line_5[i].mesin_3)+parseInt(result.ng_line_5[i].mesin_4)+parseInt(result.ng_line_5[i].mesin_5)+parseInt(result.ng_line_5[i].mesin_6));
					}

					//TOTAL
					var regress = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							regress.push(total_all_ng[i]);
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
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

						for(var i = 0; i < total_all_ng.length; i++){
							if (total_all_ng[i] == 0) {
								xLinear.push(indexPlus);
								indexPlus++;
							}
						}
					}

					var xy = [];
					var xkuadrat = [];

					for(var i = 0; i < total_all_ng.length; i++){
						if (total_all_ng[i] != 0) {
							xy.push(parseInt(xLinear[i])*parseInt(total_all_ng[i]));
							xkuadrat.push(parseInt(xLinear[i])*parseInt(xLinear[i]));
						}
					}

					var sumy = total_all_ng.reduce((a, b) => a + b, 0);
					var sumxy = xy.reduce((a, b) => a + b, 0);
					var sumxkuadrat = xkuadrat.reduce((a, b) => a + b, 0);

					var a = sumy/totalsmedian.length;
					var b = sumxy/sumxkuadrat;

					var regressions_total = [];

					for(var i = 0; i < regress.length; i++){
						regressions_total.push(parseInt((a+(b*xLinear[i])).toFixed(0)));
					}

					
					Highcharts.chart('container5', {
					    chart: {
							type: 'column',
							backgroundColor: "rgba(0,0,0,0)"
						},
						title: {
							text: "DAILY NG SPOT WELDING LINE 5",
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
											fillModal(this.category,this.series.name,'5');
										}
									}
								},
								dataLabels: {
									enabled: true,
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
							data: mesin_1,
							name: "Mesin 1",
							colorByPoint: false,
							color: "#788cff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_2,
							name: "Mesin 2",
							colorByPoint: false,
							color: "#aa6eff",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_3,
							name: "Mesin 3",
							colorByPoint: false,
							color: "#93ff87",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_4,
							name: "Mesin 4",
							colorByPoint: false,
							color: "#ffad4f",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_5,
							name: "Mesin 5",
							colorByPoint: false,
							color: "#ffa1f7",
							animation: false,
							stack:'GG'
						},
						{
							type: 'column',
							data: mesin_6,
							name: "Mesin 6",
							colorByPoint: false,
							color: "#ff8787",
							animation: false,
							stack:'GG'
						},
						{
							type: 'spline',
							data: total_all_ng,
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
							data: regressions_total,
							name: "Trendline Linear Total NG",
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
					$('#loading').hide();
				}else{
					alert('Fill Data Failed');
					$('#loading').hide();
				}
			}
		});
	}

	function fillModal(tgl, mesin,line){
	    $('#loading').show();
	    $('#tableModal').hide();

	    var data = {
	      tgl:tgl,
	      mesin:mesin,
	      line:line,
	    }
	    $.get('{{ url("fetch/reportSpotWeldingDataDetail") }}', data, function(result, status, xhr){
	      if(result.status){
	        $('#modalProgressBody').html('');
	        var resultData = '';
	        var total = 0;
	        
	        $.each(result.ng, function(key, value) {         
	          resultData += '<tr >';
	          resultData += '<td style="width: 50%;color:black;text-align:left;padding-left:4px;font-size:12pt;">'+ value.ng +'</td>';
	          resultData += '<td style="width: 50%;color:black;text-align:right;padding-right:4px;font-size:12pt;">'+ value.total +'</td>';                    
	          resultData += '</tr>';   
	          total += value.total;       
	        });
	        
	        $('#modalProgressBody').append(resultData);
	        $('#totalP').text(total);
	        $('#modalProgressTitle').text('DETAIL NG SPOT WELDING '+mesin.toUpperCase()+' TANGGAL '+tgl+' LINE '+line);
	        
	        // $('#modalProgressTitle').show();
	        $('#modalProgress').modal('show');
	        $('#tableModal').show();
	        $('#loading').hide();
	      }
	      else{
	      	$('#loading').hide();
	        alert('Attempt to retrieve data failed');
	      }
	    });
	  }

</script>
@stop