@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}
	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
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

	#table_11 > thead > tr > th {
		border: 1px solid black;
		color: black
	}

	#table_91 > thead > tr > th {
		border: 1px solid black;
		color: black
	}

	#tableResume > thead > tr > th {
		font-size: 11px;
		font-weight: bold;
		vertical-align: middle;
	}

	#tableResume2 > thead > tr > th {
		font-size: 11px;
		font-weight: bold;
		vertical-align: middle;
	}
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;padding-left: 0px">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" id='color' data-placeholder="Select Color" style="width: 100%;">
						<option value=""></option>
						<option value="All">All</option>
						@foreach($color as $color)
						<option value="{{$color->color}}">{{$color->color}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
			</div>
			<!-- <div class="col-xs-1" style="margin-top: 5px;">
				<div class="row">
					<div class="col-xs-12" style="padding-right:0;">
						<div class="small-box" style="background: #7dfa8c; height: 42vh; margin-bottom: 5px;">
							<table style="width: 100%;height: 100%;border: 1px solid black" id="table_11">
								<thead>
									<tr>
										<th colspan="2" style="font-size: 25px;padding: 0px;height: 4vh;border-bottom: 3px solid black">RC11<br>STORE</th>
									</tr>
									<tr>
										<th style="font-size: 18px;padding: 0px;height: 3vh;">Plan (Days)</th>
									</tr>
									<tr>
										<th style="font-size: 3.5vw;padding: 0 !important;border-bottom: 3px solid black">2</th>
									</tr>
									<tr>
										<th style="font-size: 18px;padding: 0px;height: 3vh">Act (Days)</th>
									</tr>
									<tr>
										<th style="font-size: 3.5vw;padding: 0 !important;" id="act_11">0</th>
									</tr>
								</thead>
							</table>
						</div>

						<div class="small-box" style="background: #a3f3ff; height: 42vh; margin-bottom: 5px;">
							<table style="width: 100%;height: 100%;border: 1px solid black" id="table_91">
								<thead>
									<tr>
										<th colspan="2" style="font-size: 25px;padding: 0px;height: 4vh;border-bottom: 3px solid black">RC91<br>ASSY RCD</th>
									</tr>
									<tr>
										<th style="font-size: 18px;padding: 0px;height: 3vh">Plan (Days)</th>
									</tr>
									<tr>
										<th style="font-size: 3.5vw;padding: 0 !important;border-bottom: 3px solid black">1</th>
									</tr>
									<tr>
										<th style="font-size: 18px;padding: 0px;height: 3vh">Act (Days)</th>
									</tr>
									<tr>
										<th style="font-size: 3.5vw;padding: 0 !important;" id="act_91">0</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-xs-8" style="margin-top: 5px;padding-right: 0px;padding-left: 0px;">
					<div id="container1" style="width: 100%;height: 65vh;"></div>
			</div>
			<div class="col-xs-4" style="margin-top: 5px;padding-left: 0px;padding-right: 0px;">
					<div id="container2" style="width: 100%;height: 65vh;"></div>
			</div> -->
			<div class="col-xs-9" style="padding-right: 0px;padding-top: 10px">
				<table class="table table-bordered" id="tableResume">
					<!-- <tr>
						<th rowspan="2">RC91</th>
						<th>Plan</th>
					</tr>
					<tr>
						<th>Act</th>
						<th>Act</th>
					</tr> -->
					<!-- <tr>
						<th>Plan</th>
						<th>Act</th>
					</tr>
					<tr>
						<th>Plan</th>
						<th>Act</th>
					</tr> -->
				</table>
			</div>
			<div class="col-xs-3" style="padding-left: 5px;padding-top: 10px;padding-right: 5px">
				<table class="table table-bordered" id="tableResume2">
					<!-- <tr>
						<th rowspan="2">RC91</th>
						<th>Plan</th>
					</tr>
					<tr>
						<th>Act</th>
						<th>Act</th>
					</tr> -->
					<!-- <tr>
						<th>Plan</th>
						<th>Act</th>
					</tr>
					<tr>
						<th>Plan</th>
						<th>Act</th>
					</tr> -->
				</table>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 60000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}

	function fillChart() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		var data = {
			color:$('#color').val()
		}
		$.get('{{ url("fetch/injection/stock_monitoring") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//Chart Skeleton
					var part_skeleton = [];
					var jml_skeleton = [];
					var jml_assy_skeleton = [];
					var series_skeleton = [];
					var series2_skeleton = [];
					var series3_skeleton = [];
					var colors_skeleton = [];
					var colors2_skeleton = [];
					var plan_skeleton = [];

					// var act11 = 0;
					// var act91 = 0;

					// for (var i = 0; i < result.datas_skeleton.length; i++) {
					// 	part_skeleton.push(result.datas_skeleton[i].part);
					// 	jml_skeleton.push(parseInt(result.datas_skeleton[i].stock));
					// 	jml_assy_skeleton.push(parseInt(result.datas_skeleton[i].stock_assy));
					// 	var color1_skeleton = result.datas_skeleton[i].part.split(' ');
					// 	var color2_skeleton = color1_skeleton.slice(-1);
					// 	if (result.datas_skeleton[i].color == 'BLUE)') {
					// 		colors_skeleton.push('#4287f5');
					// 		colors2_skeleton.push('#a6c8ff');
					// 	}else if(result.datas_skeleton[i].color == 'PINK)'){
					// 		colors_skeleton.push('#f542dd');
					// 		colors2_skeleton.push('#ffa3f3');
					// 	}else if(result.datas_skeleton[i].color == 'GREEN)'){
					// 		colors_skeleton.push('#7bff63');
					// 		colors2_skeleton.push('#adff9e');
					// 	}else if(result.datas_skeleton[i].color == 'RED)'){
					// 		colors_skeleton.push('#ff7575');
					// 		colors2_skeleton.push('#ff8787');
					// 	}else if(result.datas_skeleton[i].color == 'IVORY)'){
					// 		colors_skeleton.push('#fff5a6');
					// 		colors2_skeleton.push('#fffce6');
					// 	}else if(result.datas_skeleton[i].color == 'BROWN)'){
					// 		colors_skeleton.push('#856111');
					// 		colors2_skeleton.push('#ccae6c');
					// 	}else if(result.datas_skeleton[i].color == 'BEIGE)'){
					// 		colors_skeleton.push('#e0b146');
					// 		colors2_skeleton.push('#e8c066');
					// 	}else{
					// 		colors_skeleton.push('#000');
					// 		colors2_skeleton.push('#000');
					// 	}
					// 	series_skeleton.push({y: jml_skeleton[i],name:part_skeleton[i], color: colors_skeleton[i], key: 'RC11'});
					// 	series3_skeleton.push({y: jml_assy_skeleton[i],name:part_skeleton[i], color: colors2_skeleton[i], key: 'RC91'});
					// 	plan_skeleton.push(result.datas_skeleton[i].plan);
					// 	series2_skeleton.push({y: plan_skeleton[i],name:part_skeleton[i], key: 'Plan'});

					// 	act11 = act11 + jml_skeleton[i];
					// 	act91 = act91 + jml_assy_skeleton[i];
					// }

					// // Chart Ivory
					var part_ivory = [];
					var jml_ivory = [];
					var jml_assy_ivory = [];
					var series_ivory = [];
					var series2_ivory = [];
					var series3_ivory = [];
					var colors_ivory = [];
					var colors2_ivory = [];
					var plan_ivory = [];

					// for (var i = 0; i < result.datas_ivory.length; i++) {
					// 	part_ivory.push(result.datas_ivory[i].part);
					// 	jml_ivory.push(parseInt(result.datas_ivory[i].stock));
					// 	jml_assy_ivory.push(parseInt(result.datas_ivory[i].stock_assy));
					// 	var color1_ivory = result.datas_ivory[i].part.split(' ');
					// 	var color2_ivory = color1_ivory.slice(-1);
					// 	if (result.datas_ivory[i].color == 'BLUE)') {
					// 		colors_ivory.push('#4287f5');
					// 		colors2_ivory.push('#a6c8ff');
					// 	}else if(result.datas_ivory[i].color == 'PINK)'){
					// 		colors_ivory.push('#f542dd');
					// 		colors2_ivory.push('#ffa3f3');
					// 	}else if(result.datas_ivory[i].color == 'GREEN)'){
					// 		colors_ivory.push('#7bff63');
					// 		colors2_ivory.push('#adff9e');
					// 	}else if(result.datas_ivory[i].color == 'RED)'){
					// 		colors_ivory.push('#ff7575');
					// 		colors2_ivory.push('#ff8787');
					// 	}else if(result.datas_ivory[i].color == 'IVORY)'){
					// 		colors_ivory.push('#fff5a6');
					// 		colors2_ivory.push('#fffce6');
					// 	}else if(result.datas_ivory[i].color == 'BROWN)'){
					// 		colors_ivory.push('#856111');
					// 		colors2_ivory.push('#ccae6c');
					// 	}else if(result.datas_ivory[i].color == 'BEIGE)'){
					// 		colors_ivory.push('#e0b146');
					// 		colors2_ivory.push('#e8c066');
					// 	}else{
					// 		colors_ivory.push('#000');
					// 		colors2_ivory.push('#000');
					// 	}
					// 	series_ivory.push({y: jml_ivory[i],name:part_ivory[i], color: colors_ivory[i], key: 'RC11'});
					// 	series3_ivory.push({y: jml_assy_ivory[i],name:part_ivory[i], color: colors2_ivory[i], key: 'RC91'});
					// 	plan_ivory.push(result.datas_ivory[i].plan);
					// 	series2_ivory.push({y: plan_ivory[i],name:part_ivory[i], key: 'Plan'});

					// 	act11 = act11 + jml_ivory[i];
					// 	act91 = act91 + jml_assy_ivory[i];
					// }

					// var day11 = 0;
					// var day91 = 0;


					// for(var i = 0; i < result.plan_day.length;i++){
					// 	if (result.plan_day[i].location == 'RC11') {
					// 		day11 = ((act11)/parseInt(result.plan_day[i].qty)).toFixed(1);
					// 	}
					// 	if (result.plan_day[i].location == 'RC91') {
					// 		day91 = ((act91)/parseInt(result.plan_day[i].qty)).toFixed(1);
					// 	}
					// }


					// $('#act_11').html(day11);
					// $('#act_91').html(day91);

					var tableResume = '';
					$('#tableResume').html('');

					var parts11 = [];
					var parts91 = [];
					var parts11_next = [];
					var parts91_next = [];

					for(var j =0; j < result.plan_day.length;j++){
						if (result.plan_day[j].location == 'RC11') {
							if (result.plan_day[j].late_stock == 'No') {
								parts11.push(result.plan_day[j].part);
							}else{
								parts11_next.push(result.plan_day[j].part);
							}
						}else{
							if (result.plan_day[j].late_stock == 'No') {
								parts91.push(result.plan_day[j].part);
							}else{
								parts91_next.push(result.plan_day[j].part);
							}
						}
					}

					tableResume += '<thead>';
					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">Item</th>';
					for (var i = 0; i < result.datas_skeleton.length; i++) {
						var color1_skeleton = result.datas_skeleton[i].part.split(' ');
						var color2_skeleton = color1_skeleton.slice(-1);
						if (result.datas_skeleton[i].color == 'BLUE)') {
							colors_skeleton.push('#4287f5');
							colors2_skeleton.push('#a6c8ff');
						}else if(result.datas_skeleton[i].color == 'PINK)'){
							colors_skeleton.push('#f542dd');
							colors2_skeleton.push('#ffa3f3');
						}else if(result.datas_skeleton[i].color == 'GREEN)'){
							colors_skeleton.push('#7bff63');
							colors2_skeleton.push('#adff9e');
						}else if(result.datas_skeleton[i].color == 'RED)'){
							colors_skeleton.push('#ff7575');
							colors2_skeleton.push('#ff8787');
						}else if(result.datas_skeleton[i].color == 'IVORY)'){
							colors_skeleton.push('#fff5a6');
							colors2_skeleton.push('#fffce6');
						}else if(result.datas_skeleton[i].color == 'BROWN)'){
							colors_skeleton.push('#856111');
							colors2_skeleton.push('#ccae6c');
						}else if(result.datas_skeleton[i].color == 'BEIGE)'){
							colors_skeleton.push('#e0b146');
							colors2_skeleton.push('#e8c066');
						}else{
							colors_skeleton.push('#000');
							colors2_skeleton.push('#000');
						}
						tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+result.datas_skeleton[i].part+'</th>';
					}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">RC11 Plan</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts11.join();
							var partjoin_next = parts11_next.join();

							if (partjoin.includes(result.datas_skeleton[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock == 'No') {
										if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC11') {
											tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else if(partjoin_next.includes(result.datas_skeleton[i].part)){
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock != 'No') {
										if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC11') {
											tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else{
								tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">0</th>';
							}
						}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">RC11</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts11.join();

							tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">'+result.datas_skeleton[i].stock+'</th>';
						}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">Avail (Day)</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts11.join();
							var partjoin_next = parts11_next.join();

							if (result.datas_skeleton[i].stock > 0) {
								if (partjoin.includes(result.datas_skeleton[i].part)) {
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock == 'No') {
											if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC11') {
												var qty_diff = (((parseInt(result.datas_skeleton[i].stock))/parseInt(result.plan_day[j].qty))).toFixed(1);
												if (result.plan_day[j].qty == 0) {
													qty_diff = ' >1 M';
												}
												tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">'+qty_diff+'</th>';
											}
										}
									}
								}else if(partjoin_next.includes(result.datas_skeleton[i].part)){
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock != 'No') {
											if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC11') {
												var qty_diff = (((parseInt(result.datas_skeleton[i].stock))/parseInt(result.plan_day[j].late_stock))).toFixed(1);
												// if (qty_diff == 'Infinity'|| qty_diff == 'NaN') {
												// 	qty_diff = 0;
												// }
												tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">'+result.plan_day[j].late_stock+'</th>';
											}
										}
									}
								}else{
									tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black"> >1 M</th>';
								}
							}else{
								tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_skeleton[i]+';color:black">0</th>';
							}
						}
					tableResume += '</tr>';

					

					tableResume += '<tr>';
					tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left"><div style="transform: rotate(90deg);-moz-transform: rotate(90deg) !important;">Stock</div></th>';
					for (var i = 0; i < result.datas_skeleton.length; i++) {
						var color1_skeleton = result.datas_skeleton[i].part.split(' ');
						var color2_skeleton = color1_skeleton.slice(-1);
						if (result.datas_skeleton[i].color == 'BLUE)') {
							colors_skeleton.push('#4287f5');
							colors2_skeleton.push('#a6c8ff');
						}else if(result.datas_skeleton[i].color == 'PINK)'){
							colors_skeleton.push('#f542dd');
							colors2_skeleton.push('#ffa3f3');
						}else if(result.datas_skeleton[i].color == 'GREEN)'){
							colors_skeleton.push('#7bff63');
							colors2_skeleton.push('#adff9e');
						}else if(result.datas_skeleton[i].color == 'RED)'){
							colors_skeleton.push('#ff7575');
							colors2_skeleton.push('#ff8787');
						}else if(result.datas_skeleton[i].color == 'IVORY)'){
							colors_skeleton.push('#fff5a6');
							colors2_skeleton.push('#fffce6');
						}else if(result.datas_skeleton[i].color == 'BROWN)'){
							colors_skeleton.push('#856111');
							colors2_skeleton.push('#ccae6c');
						}else if(result.datas_skeleton[i].color == 'BEIGE)'){
							colors_skeleton.push('#e0b146');
							colors2_skeleton.push('#e8c066');
						}else{
							colors_skeleton.push('#000');
							colors2_skeleton.push('#000');
						}

						var stock = parseInt(result.datas_skeleton[i].stock);
						var stock_assy = parseInt(result.datas_skeleton[i].stock_assy);
						var stock_max = 3000;
						var stock_all = stock + stock_assy;


						var persen_stock = stock / stock_max;
						var persen_stock_assy = stock_assy / stock_max;
						var height_stock = persen_stock*58;
						var height_stock_assy = persen_stock_assy*58;

						var displaynone = '';
						var displaynone_assy = '';
						var displaynone_plan = '';
						if (stock == 0) {
							displaynone = 'display:none';
						}

						if (stock_assy == 0) {
							displaynone_assy = 'display:none';
						}

						if (result.datas_skeleton[i].plan == 0) {
							displaynone_plan = 'display:none';
						}

						var persen_plan = (result.datas_skeleton[i].plan) / stock_max;
						var height_plan = persen_plan*58;

						var persen_plan_all = (result.datas_skeleton[i].stock+result.datas_skeleton[i].stock_assy) / stock_max;
						var height_plan_all = persen_plan_all*58;
						

						// tableResume += '<th style="padding:1px;font-size:12px;color:white;height:58vh;vertical-align:bottom;text-align:center;background-size: 90% '+parseInt(80)+'%;background-color: white;background-repeat: no-repeat;"><center>';
						// tableResume += '<div style=" text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors_skeleton[i]+'; height: '+height_stock+'vh;'+displaynone+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC11<br>'+result.datas_skeleton[i].stock+'</span></div>';
						// tableResume += '<div style="line-height: 60%; text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors2_skeleton[i]+'; height: '+height_stock_assy+'vh;'+displaynone_assy+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC91<br>'+result.datas_skeleton[i].stock_assy+'</span></div>';
						// tableResume += '</div>';
						// tableResume += '</center></th>';
						tableResume += '<th style="font-size:12px;vertical-align:bottom;text-align:center;height:58vh;padding-bottom:0px;padding:0px">';
						tableResume += '<div style="vertical-align:top;line-height: 100%;padding-bottom:'+(54-height_plan)+'vh;padding-top:0px;'+displaynone_plan+'">';
						tableResume += '<span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">Plan<br>';
						tableResume += result.datas_skeleton[i].plan;
						tableResume += '</span>';
						tableResume += '</div>';
						tableResume += '<table style="position:relative;width:100%;height:'+height_plan+
						'vh;margin-bottom:-'+height_plan_all+'vh">';
						tableResume += '<tr>';
						tableResume += '<td style="border-top:4px solid red;vertical-align:top;padding:0px;margin: 0px 0px 0px 0px;">';
						tableResume += '</td>';
						tableResume += '</tr>';
						tableResume += '</table>';
						tableResume += '<div style="line-height: 100%;text-align: center; margin:0px 5px 0px 5px; background-color: '+colors_skeleton[i]+'; height: '+height_stock+'vh;'+displaynone+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC11<br>'+result.datas_skeleton[i].stock+'</span></div>';
						tableResume += '<div style="line-height: 100%; text-align: center; margin:0px 5px 0px 5px;background-color: '+colors2_skeleton[i]+'; height: '+height_stock_assy+'vh;'+displaynone_assy+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC91<br>'+result.datas_skeleton[i].stock_assy+'</span></div>';
						tableResume += '</th>';
					}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left;">Item</th>';
					for (var i = 0; i < result.datas_skeleton.length; i++) {
						var color1_skeleton = result.datas_skeleton[i].part.split(' ');
						var color2_skeleton = color1_skeleton.slice(-1);
						if (result.datas_skeleton[i].color == 'BLUE)') {
							colors_skeleton.push('#4287f5');
							colors2_skeleton.push('#a6c8ff');
						}else if(result.datas_skeleton[i].color == 'PINK)'){
							colors_skeleton.push('#f542dd');
							colors2_skeleton.push('#ffa3f3');
						}else if(result.datas_skeleton[i].color == 'GREEN)'){
							colors_skeleton.push('#7bff63');
							colors2_skeleton.push('#adff9e');
						}else if(result.datas_skeleton[i].color == 'RED)'){
							colors_skeleton.push('#ff7575');
							colors2_skeleton.push('#ff8787');
						}else if(result.datas_skeleton[i].color == 'IVORY)'){
							colors_skeleton.push('#fff5a6');
							colors2_skeleton.push('#fffce6');
						}else if(result.datas_skeleton[i].color == 'BROWN)'){
							colors_skeleton.push('#856111');
							colors2_skeleton.push('#ccae6c');
						}else if(result.datas_skeleton[i].color == 'BEIGE)'){
							colors_skeleton.push('#e0b146');
							colors2_skeleton.push('#e8c066');
						}else{
							colors_skeleton.push('#000');
							colors2_skeleton.push('#000');
						}
						tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:none;padding-top:20px">'+result.datas_skeleton[i].part+'</th>';
					}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">RC91 Plan</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts91.join();
							var partjoin_next = parts91_next.join();

							if (partjoin.includes(result.datas_skeleton[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock == 'No') {
										if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC91') {
											tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else if (partjoin_next.includes(result.datas_skeleton[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock != 'No') {
										if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC91') {
											tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else{
								tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">0</th>';
							}
						}
					tableResume += '</tr>';


					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">RC91</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts91.join();

							tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+result.datas_skeleton[i].stock_assy+'</th>';
						}
					tableResume += '</tr>';

					tableResume += '<tr>';
						tableResume += '<th style="padding:1px;width:60px;background-color:white;color:black;text-align:left">Avail (Day)</th>';
						for (var i = 0; i < result.datas_skeleton.length; i++) {
							var color1_skeleton = result.datas_skeleton[i].part.split(' ');
							var color2_skeleton = color1_skeleton.slice(-1);
							if (result.datas_skeleton[i].color == 'BLUE)') {
								colors_skeleton.push('#4287f5');
								colors2_skeleton.push('#a6c8ff');
							}else if(result.datas_skeleton[i].color == 'PINK)'){
								colors_skeleton.push('#f542dd');
								colors2_skeleton.push('#ffa3f3');
							}else if(result.datas_skeleton[i].color == 'GREEN)'){
								colors_skeleton.push('#7bff63');
								colors2_skeleton.push('#adff9e');
							}else if(result.datas_skeleton[i].color == 'RED)'){
								colors_skeleton.push('#ff7575');
								colors2_skeleton.push('#ff8787');
							}else if(result.datas_skeleton[i].color == 'IVORY)'){
								colors_skeleton.push('#fff5a6');
								colors2_skeleton.push('#fffce6');
							}else if(result.datas_skeleton[i].color == 'BROWN)'){
								colors_skeleton.push('#856111');
								colors2_skeleton.push('#ccae6c');
							}else if(result.datas_skeleton[i].color == 'BEIGE)'){
								colors_skeleton.push('#e0b146');
								colors2_skeleton.push('#e8c066');
							}else{
								colors_skeleton.push('#000');
								colors2_skeleton.push('#000');
							}

							var partjoin = parts91.join();
							var partjoin_next = parts91_next.join();

							if (result.datas_skeleton[i].stock_assy > 0) {
								if (partjoin.includes(result.datas_skeleton[i].part)) {
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock == 'No') {
											if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC91') {
												var qty_diff = (((parseInt(result.datas_skeleton[i].stock_assy))/parseInt(result.plan_day[j].qty))).toFixed(1);
												if (result.plan_day[j].qty == 0) {
													qty_diff = ' >1 M';
												}
												tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+qty_diff+'</th>';
											}
										}
									}
								}else if (partjoin_next.includes(result.datas_skeleton[i].part)) {
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock != 'No') {
											if (result.plan_day[j].part == result.datas_skeleton[i].part && result.plan_day[j].location == 'RC91') {
												var qty_diff = (((parseInt(result.datas_skeleton[i].stock_assy))/parseInt(result.plan_day[j].qty))).toFixed(1);
												// if (qty_diff == 'Infinity'|| qty_diff == 'NaN') {
												// 	qty_diff = 0;
												// }
												tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">'+result.plan_day[j].late_stock+'</th>';
											}
										}
									}
								}else{
									tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black"> >1 M</th>';
								}
							}else{
								tableResume += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_skeleton[i]+';color:black">0</th>';
							}
						}
					tableResume += '</tr>';

					tableResume += '</thead>';

					$('#tableResume').append(tableResume);

					var parts11 = [];
					var parts91 = [];
					var parts11_next = [];
					var parts91_next = [];

					for(var j =0; j < result.plan_day.length;j++){
						if (result.plan_day[j].location == 'RC11') {
							if (result.plan_day[j].late_stock == 'No') {
								parts11.push(result.plan_day[j].part);
							}else{
								parts11_next.push(result.plan_day[j].part);
							}
						}else{
							if (result.plan_day[j].late_stock == 'No') {
								parts91.push(result.plan_day[j].part);
							}else{
								parts91_next.push(result.plan_day[j].part);
							}
						}
					}

					var tableResume2 = '';
					$('#tableResume2').html('');

					tableResume2 += '<thead>';
					tableResume2 += '<tr>';

						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}
							tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+result.datas_ivory[i].part+'</th>';
						}
					tableResume2 += '</tr>';
					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts11.join();
							var partjoin_next = parts11_next.join();

							if (partjoin.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock == 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC11') {
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else if (partjoin_next.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock != 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC11') {
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else{
								tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">0</th>';
							}
						}
					tableResume2 += '</tr>';
					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts11.join();

							tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+result.datas_ivory[i].stock+'</th>';
						}
					tableResume2 += '</tr>';

					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts11.join();
							var partjoin_next = parts11_next.join();

							if (result.datas_ivory[i].stock > 0) {
								if (partjoin.includes(result.datas_ivory[i].part)) {
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock == 'No') {
											if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC11') {
												var qty_diff = (((parseInt(result.datas_ivory[i].stock))/parseInt(result.plan_day[j].qty))).toFixed(1);
												if (result.plan_day[j].qty == 0) {
													qty_diff = ' >1 M';
												}
												tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+qty_diff+'</th>';
											}
										}
									}
								}else if (partjoin_next.includes(result.datas_ivory[i].part)) {
									for(var j =0; j < result.plan_day.length;j++){
										if (result.plan_day[j].late_stock != 'No') {
											if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC11') {
												var qty_diff = (((parseInt(result.datas_ivory[i].stock))/parseInt(result.plan_day[j].qty))).toFixed(1);
												tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">'+result.plan_day[j].late_stock+'</th>';
											}
										}
									}
								}else{
									tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black"> >1 M</th>';
								}
							}else{
								tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors_ivory[i]+';color:black">0</th>';
							}
						}
					tableResume2 += '</tr>';

					

					tableResume2 += '<tr>';
					for (var i = 0; i < result.datas_ivory.length; i++) {
						var color1_ivory = result.datas_ivory[i].part.split(' ');
						var color2_ivory = color1_ivory.slice(-1);
						if (result.datas_ivory[i].color == 'BLUE)') {
							colors_ivory.push('#4287f5');
							colors2_ivory.push('#a6c8ff');
						}else if(result.datas_ivory[i].color == 'PINK)'){
							colors_ivory.push('#f542dd');
							colors2_ivory.push('#ffa3f3');
						}else if(result.datas_ivory[i].color == 'GREEN)'){
							colors_ivory.push('#7bff63');
							colors2_ivory.push('#adff9e');
						}else if(result.datas_ivory[i].color == 'RED)'){
							colors_ivory.push('#ff7575');
							colors2_ivory.push('#ff8787');
						}else if(result.datas_ivory[i].color == 'IVORY)'){
							colors_ivory.push('#fff5a6');
							colors2_ivory.push('#fffce6');
						}else if(result.datas_ivory[i].color == 'BROWN)'){
							colors_ivory.push('#856111');
							colors2_ivory.push('#ccae6c');
						}else if(result.datas_ivory[i].color == 'BEIGE)'){
							colors_ivory.push('#e0b146');
							colors2_ivory.push('#e8c066');
						}else{
							colors_ivory.push('#000');
							colors2_ivory.push('#000');
						}

						var stock = parseInt(result.datas_ivory[i].stock);
						var stock_assy = parseInt(result.datas_ivory[i].stock_assy);
						var stock_max = 40000;
						var stock_all = stock + stock_assy;


						var persen_stock = stock / stock_max;
						var persen_stock_assy = stock_assy / stock_max;
						var height_stock = persen_stock*58;
						var height_stock_assy = persen_stock_assy*58;

						var displaynone = '';
						var displaynone_assy = '';
						var displaynone_plan = '';
						if (stock == 0) {
							displaynone = 'display:none';
						}

						if (stock_assy == 0) {
							displaynone_assy = 'display:none';
						}
						if (parseInt(result.datas_ivory[i].plan) == 0) {
							displaynone_plan = 'display:none';
						}

						var persen_plan = (result.datas_ivory[i].plan / stock_max)*100;

						var persen_plan = (result.datas_ivory[i].plan) / stock_max;
						var height_plan = persen_plan*58;

						var persen_plan_all = (result.datas_ivory[i].stock+result.datas_ivory[i].stock_assy) / stock_max;
						var height_plan_all = persen_plan_all*58;


						

						tableResume2 += '<th style="font-size:12px;vertical-align:bottom;text-align:center;height:58vh;padding-bottom:0px;padding:0px">';
						tableResume2 += '<div style="vertical-align:top;line-height: 100%;padding-bottom:'+(54-height_plan)+'vh;padding-top:0px;'+displaynone_plan+'">';
						tableResume2 += '<span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">Plan<br>';
						tableResume2 += result.datas_ivory[i].plan;
						tableResume2 += '</span>';
						tableResume2 += '</div>';
						tableResume2 += '<table style="position:relative;width:100%;height:'+height_plan+
						'vh;margin-bottom:-'+height_plan_all+'vh">';
						tableResume2 += '<tr>';
						tableResume2 += '<td style="border-top:4px solid red;vertical-align:top;padding:0px;margin: 0px 0px 0px 0px;">';
						tableResume2 += '</td>';
						tableResume2 += '</tr>';
						tableResume2 += '</table>';
						tableResume2 += '<div style="line-height: 100%;text-align: center; margin:0px 5px 0px 5px; background-color: '+colors_ivory[i]+'; height: '+height_stock+'vh;'+displaynone+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC11<br>'+result.datas_ivory[i].stock+'</span></div>';
						tableResume2 += '<div style="line-height: 100%; text-align: center; margin:0px 5px 0px 5px;background-color: '+colors2_ivory[i]+'; height: '+height_stock_assy+'vh;'+displaynone_assy+'"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC91<br>'+result.datas_ivory[i].stock_assy+'</span></div>';
						tableResume2 += '</th>';

						// tableResume2 += '<th style="padding:1px;font-size:12px;vertical-align:bottom;text-align:center;background-image: linear-gradient(to left, rgba(255, 255, 25) 0%, rgba(255, 255, 25) 17%, rgba(255, 255, 25) 33%, rgba(255, 255, 25) 67%, rgba(255, 255, 25) 83%, rgba(255, 255, 25) 100%);background-position: 100% 100%;background-repeat: no-repeat;background-size: 100% '+persen_plan+'%;height:58vh;"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">Plan<br>'+result.datas_ivory[i].plan+'</span>';
						// tableResume2 += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors_ivory[i]+'; height: '+height_stock+'vh;'+displaynone+';border-top:1px solid white"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC11<br>'+result.datas_ivory[i].stock+'</span></div>';
						// tableResume2 += '<div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors2_ivory[i]+'; height: '+height_stock_assy+'vh;'+displaynone_assy+';border-top:1px solid white"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC91<br>'+result.datas_ivory[i].stock_assy+'</span></div>';
						// tableResume2 += '</th>';

						// tableResume2 += '<th style="padding:1px;font-size:12px;color:white;height:58vh;vertical-align:bottom;text-align:center;"><center><div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors_ivory[i]+'; height: '+height_stock+'vh;'+displaynone+';border-top:1px solid white"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC11<br>'+result.datas_ivory[i].stock+'</span></div><div style="line-height: 80%; text-align: center; margin: 0px 3px 0px 3px; background-color: '+colors2_ivory[i]+'; height: '+height_stock_assy+'vh;'+displaynone_assy+';border-top:1px solid white"><span style="width:100%;text-shadow:-1px 0 black, 0 1px black, 1px 0 black, 0 -1px black">RC91<br>'+result.datas_ivory[i].stock_assy+'</span></div></center></th>';
					}
					
					tableResume2 += '</tr>';

					tableResume2 += '<tr>';

						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}
							tableResume2 += '<th style="padding:1px;font-size:12px;color:white !important;background-color:none;padding-top:20px">'+result.datas_ivory[i].part+'</th>';
						}
					tableResume2 += '</tr>';

					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts91.join();
							var partjoin_next = parts91_next.join();

							if (partjoin.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock == 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC91') {
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else if (partjoin_next.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock != 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC91') {
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">'+result.plan_day[j].qty+'</th>';
										}
									}
								}
							}else{
								tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">0</th>';
							}
						}
					tableResume2 += '</tr>';

					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts91.join();

							tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">'+result.datas_ivory[i].stock_assy+'</th>';
						}
					tableResume2 += '</tr>';

					tableResume2 += '<tr>';
						for (var i = 0; i < result.datas_ivory.length; i++) {
							var color1_ivory = result.datas_ivory[i].part.split(' ');
							var color2_ivory = color1_ivory.slice(-1);
							if (result.datas_ivory[i].color == 'BLUE)') {
								colors_ivory.push('#4287f5');
								colors2_ivory.push('#a6c8ff');
							}else if(result.datas_ivory[i].color == 'PINK)'){
								colors_ivory.push('#f542dd');
								colors2_ivory.push('#ffa3f3');
							}else if(result.datas_ivory[i].color == 'GREEN)'){
								colors_ivory.push('#7bff63');
								colors2_ivory.push('#adff9e');
							}else if(result.datas_ivory[i].color == 'RED)'){
								colors_ivory.push('#ff7575');
								colors2_ivory.push('#ff8787');
							}else if(result.datas_ivory[i].color == 'IVORY)'){
								colors_ivory.push('#fff5a6');
								colors2_ivory.push('#fffce6');
							}else if(result.datas_ivory[i].color == 'BROWN)'){
								colors_ivory.push('#856111');
								colors2_ivory.push('#ccae6c');
							}else if(result.datas_ivory[i].color == 'BEIGE)'){
								colors_ivory.push('#e0b146');
								colors2_ivory.push('#e8c066');
							}else{
								colors_ivory.push('#000');
								colors2_ivory.push('#000');
							}

							var partjoin = parts91.join();
							var partjoin_next = parts91_next.join();

							if (partjoin.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock == 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC91') {
											var qty_diff = (((parseInt(result.datas_ivory[i].stock_assy))/parseInt(result.plan_day[j].qty))).toFixed(1);
											if (result.plan_day[j].qty == 0) {
												qty_diff = ' >1 M';
											}
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">'+qty_diff+'</th>';
										}
									}
								}
							}else if (partjoin_next.includes(result.datas_ivory[i].part)) {
								for(var j =0; j < result.plan_day.length;j++){
									if (result.plan_day[j].late_stock != 'No') {
										if (result.plan_day[j].part == result.datas_ivory[i].part && result.plan_day[j].location == 'RC91') {
											var qty_diff = (((parseInt(result.datas_ivory[i].stock_assy))/parseInt(result.plan_day[j].qty))).toFixed(1);
											if (result.plan_day[j].qty == 0) {
												qty_diff = ' >1 M';
											}
											tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">'+result.plan_day[j].late_stock+'</th>';
										}
									}
								}
							}else{
								tableResume2 += '<th style="padding:1px;font-size:12px;color:white;background-color:'+colors2_ivory[i]+';color:black">0</th>';
							}
						}
					tableResume2 += '</tr>';

					tableResume2 += '</thead>';

					$('#tableResume2').append(tableResume2);
				}
			}
		});

	}

	function ShowModal(mesin,tanggal) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/press/detail_press") }}",
          "data" : {
            mesin : mesin,
            tanggal : tanggal
          }
        },
      "columns": [
          { "data": "date" },
          { "data": "name" },
          { "data": "machine" },
          { "data": "material_number" },
          { "data": "data_ok" }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                ''+pageTotal +' ('+ total +' total)'
            );
        }     
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center>Perolehan di <b>'+mesin+' Pada '+tanggal+'</center></b>');
    
  }

  function ShowModalpic(pic,tanggal) {
    tabel = $('#example2').DataTable();
    tabel.destroy();

    $("#myModal").modal("show");

    var table = $('#example2').DataTable({
      'dom': 'Bfrtip',
      'responsive': true,
      'lengthMenu': [
      [ 10, 25, 50, -1 ],
      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
      ],
      'buttons': {
        buttons:[
        {
          extend: 'pageLength',
          className: 'btn btn-default',
          // text: '<i class="fa fa-print"></i> Show',
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
      'searching': true,
      'ordering': true,
      'order': [],
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": true,
      "serverSide": true,
      "ajax": {
          "type" : "get",
          "url" : "{{ url("index/press/detail_pic") }}",
          "data" : {
            pic : pic,
            tanggal : tanggal
          }
        },
      "columns": [
          { "data": "date" },
          { "data": "name" },
          { "data": "machine" },
          { "data": "material_number" },
          { "data": "data_ok" }
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                ''+pageTotal +' ('+ total +' total)'
            );
        }   
    });

    $('#judul_table').append().empty();
    $('#judul_table').append('<center>Perolehan <b>'+pic+' Pada '+tanggal+'</center></b>');
    
  }


</script>
@endsection