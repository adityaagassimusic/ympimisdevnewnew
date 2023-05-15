@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }
	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #fff;
			color: black;
		}
		50%, 100% {
			background-color: #FF7474;
			color: black;
		}
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-2" style="padding-right: 0px;">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
			</div>
		</div>
		<div class="col-xs-2" style="padding-left: 5px;padding-right: 0px">
			<button class="btn btn-success" onclick="fillChart()"><b>Update Chart</b></button>
		</div>
		<div class="col-xs-3 pull-right">
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
		</div>
		<div class="col-xs-12" style="margin-top: 5px;">
			<!-- <div class="col-xs-9" style="padding: 0px;">
				<div id="container1" style="width: 100%; height: 690px"></div>					
			</div>
			<div class="col-xs-3" style="padding: 0px;padding-left: 1%;">
				
				 <div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#FF7474;">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Plan Balance Ratio</p>
						<p style="margin: 0px; font-size: 5vw;" id="balance_ratio_plan"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

				<div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#A9FF97;">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Actual Balance Ratio</p>
						<p style="margin: 0px; font-size: 5vw;" id="balance_ratio"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

				<div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#dea8ff;" id="box_unbalance">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Delayed Process</p>
						<p style="margin: 0px; font-size: 5vw;" id="unbalance_process"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

			</div> -->

			<div class="col-xs-9" style="padding: 0px;">
				<div id="container2" style="width: 100%; height: 690px"></div>					
			</div>
			<div class="col-xs-3" style="padding: 0px;padding-left: 1%;">
				
				 <div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#dea8ff;">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Plan Balance Ratio</p>
						<p style="margin: 0px; font-size: 5vw;" id="balance_ratio_plan_line"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

				<div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#A9FF97;">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Actual Balance Ratio</p>
						<p style="margin: 0px; font-size: 5vw;" id="balance_ratio_line"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

				<div class="small-box" style="font-size: 30px;font-weight: bold;height: 200px;background-color:#FF7474;" id="box_unbalance_line">
					<div class="inner" style="padding-bottom: 0px; color: #3c3c3c;">
						<p style="margin: 0px; font-size: 2vw;">Delayed Process</p>
						<p style="margin: 0px; font-size: 5vw;" id="unbalance_line"></p>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>

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
		setInterval(fillChart, 300000);

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

	function fillChart() {
		$('#loading').show();
		var tanggal = $('#tanggal').val();

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			tanggal:tanggal,
			origin_group_code:'{{$origin_group_code}}'
		}

		$.get('{{ url("fetch/assembly/group_balance") }}', data, function(result, status, xhr) {
			if(result.status){

				
				// var key = [];
				// var keys = [];
				// var key_value_plan = [];
				// var key_value = [];
				// var unbalance_line = [];

				// var line_process = [];

				// var total_value_plan = 0;
				// var total_value = 0;
				// var max_plan = 0;
				// var maxes = 0;

				// $('#unbalance_line').val('');
				// $('#box_unbalance').removeAttr('class');
				// $('#box_unbalance').prop('class','small-box');

				// keys.push('LINE ASSEMBLY PROCESS');
				// key.push('LINE ASSEMBLY PROCESS');

				// for (var j = 0; j < result.key.length; j++) {
				// 	// if (result.key[j].key != 'qa-audit') {
				// 		if (!['1','2','3','4','5'].includes(result.key[j].key)) {
				// 			keys.push(result.key[j].key.toUpperCase());
				// 			key.push(result.key[j].key.toUpperCase());
				// 		}
				// 		// if (isNaN(parseInt(result.key[j].key))) {
				// 		// 	keys.push(result.key[j].key.toUpperCase());
				// 		// }else{
				// 		// 	keys.push('LINE '+result.key[j].key.toUpperCase());
				// 		// }
				// 	// }
				// }

				// var plan_time = 0;
				// var actual_time = 0;
				// var jml = 0;
				// for (var i = 0; i < result.data.length; i++) {
				// 	for (var j = 0; j < result.key.length; j++) {
				// 		if(result.data[i].location == result.key[j].key){
				// 			if (['1','2','3','4','5'].includes(result.key[j].key)) {
				// 				plan_time = plan_time+parseInt(result.data[i].plan_time);
				// 				actual_time = actual_time+parseInt(result.data[i].actual_time);
				// 				jml = jml+parseInt(result.key[j].jml);
				// 				// key_value_plan.push(Math.ceil(result.data[i].plan_time / result.key[j].jml));
				// 				// key_value.push(Math.ceil(result.data[i].actual_time / result.key[j].jml));
								
				// 				// total_value_plan += Math.ceil(result.data[i].plan_time / result.key[j].jml);
				// 				// total_value += Math.ceil(result.data[i].actual_time / result.key[j].jml);
								
				// 				// if(Math.ceil(result.data[i].plan_time / result.key[j].jml) > max_plan){
				// 				// 	max_plan = Math.ceil(result.data[i].plan_time / result.key[j].jml);
				// 				// }

				// 				// if(Math.ceil(result.data[i].actual_time / result.key[j].jml) > maxes){
				// 				// 	maxes = Math.ceil(result.data[i].actual_time / result.key[j].jml);
				// 				// }
				// 			}
				// 		}

				// 		// if(result.data[i].location == result.key[j].key){

				// 		// 	// if (['1','2','3','4','5'].includes(result.key[j].key)) {
				// 		// 	// 	unbalance_line.push({line:result.key[j].key,actual:Math.ceil(result.data[i].actual_time / result.key[j].jml)});
				// 		// 	// }
							
							
				// 		// }
				// 	}
				// }

				// key_value_plan.push(Math.ceil((plan_time) / (jml)));
				// key_value.push(Math.ceil((actual_time) / (jml)));
				
				// total_value_plan += Math.ceil((plan_time) / (jml));
				// total_value += Math.ceil((actual_time) / (jml));
				
				// if(Math.ceil((plan_time) / (jml)) > max_plan){
				// 	max_plan = Math.ceil((plan_time) / (jml));
				// }

				// if(Math.ceil((actual_time) / (jml)) > maxes){
				// 	maxes = Math.ceil((actual_time) / (jml));
				// }

				// for (var i = 0; i < result.data.length; i++) {
				// 	for (var j = 0; j < result.key.length; j++) {
				// 		if(result.data[i].location == result.key[j].key){
				// 			if (!['1','2','3','4','5'].includes(result.key[j].key)) {
				// 				key_value_plan.push(Math.ceil(result.data[i].plan_time / result.key[j].jml));
				// 				key_value.push(Math.ceil(result.data[i].actual_time / result.key[j].jml));
								
				// 				total_value_plan += Math.ceil(result.data[i].plan_time / result.key[j].jml);
				// 				total_value += Math.ceil(result.data[i].actual_time / result.key[j].jml);
								
				// 				if(Math.ceil(result.data[i].plan_time / result.key[j].jml) > max_plan){
				// 					max_plan = Math.ceil(result.data[i].plan_time / result.key[j].jml);
				// 				}

				// 				if(Math.ceil(result.data[i].actual_time / result.key[j].jml) > maxes){
				// 					maxes = Math.ceil(result.data[i].actual_time / result.key[j].jml);
				// 				}
				// 			}
				// 		}

				// 		// if(result.data[i].location == result.key[j].key){

				// 		// 	// if (['1','2','3','4','5'].includes(result.key[j].key)) {
				// 		// 	// 	unbalance_line.push({line:result.key[j].key,actual:Math.ceil(result.data[i].actual_time / result.key[j].jml)});
				// 		// 	// }
							
							
				// 		// }
				// 	}
				// }

				// // unbalance_line.sort(dynamicSort('actual'));

				// // if (result.time > '08:00:00') {
				// // 	$('#box_unbalance').removeAttr('class');
				// // 	$('#box_unbalance').prop('class','small-box sedang');
				// // 	$('#unbalance_line').html('LINE '+unbalance_line[0].line);
				// // }

				// var balance_ratio_plan = total_value_plan / (max_plan * key.length);
				// var balance_ratio = total_value / (maxes * key.length);

				// $('#balance_ratio_plan').append().empty();

				// $('#balance_ratio_plan').append('0%');
				// if ((balance_ratio_plan*100).toFixed(2) != 'NaN') {
				// 	$('#balance_ratio_plan').html((balance_ratio_plan*100).toFixed(2) + "%");
				// }

				// $('#balance_ratio').append().empty();

				// $('#balance_ratio').append('0%');
				// if ((balance_ratio*100).toFixed(2) != 'NaN') {
				// 	$('#balance_ratio').html((balance_ratio*100).toFixed(2) + "%");
				// }

				// var chart = Highcharts.chart('container1', {
				// 	title: {
				// 		text: 'Group Work Balance '+result.titles.toUpperCase(),
				// 		style: {
				// 			fontSize: '30px',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	subtitle: {
				// 		text: 'on '+result.tanggal,
				// 		style: {
				// 			fontSize: '1vw',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	yAxis: {
				// 		title: {
				// 			text: 'Minutes'
				// 		},
				// 		style: {
				// 			fontSize: '26px',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	xAxis: {
				// 		categories: keys,
				// 		type: 'category',
				// 		gridLineWidth: 1,
				// 		gridLineColor: 'RGB(204,255,255)',
				// 		labels: {
				// 			style: {
				// 				// fontSize: '26px'
				// 			}
				// 		},
				// 	},
				// 	tooltip: {
				// 		headerFormat: '<span>{point.category}</span><br/>',
				// 		pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
				// 	},
				// 	credits: {
				// 		enabled:false
				// 	},
				// 	legend : {
				// 		align: 'center',
				// 		verticalAlign: 'bottom',
				// 		x: 0,
				// 		y: 0,

				// 		backgroundColor: (
				// 			Highcharts.theme && Highcharts.theme.background2) || 'white',
				// 		shadow: false
				// 	},
				// 	plotOptions: {
				// 		series:{
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y} Min',
				// 				style:{
				// 					textOutline: false,
				// 				}
				// 			},
				// 			animation: false,
				// 			// cursor: 'pointer'
				// 		}
				// 	},
				// 	series: [
				// 	{
				// 		name:'Plan',
				// 		color: 'rgb(255,116,116)',
				// 		type: 'column',
				// 		data: key_value_plan,
				// 	},
				// 	{
				// 		name:'Actual',
				// 		color: 'rgb(169,255,151)',
				// 		type: 'column',
				// 		data: key_value,
				// 	}
				// 	]

				// });

				var key = [];
				var keys = [];
				var key_value_plan = [];
				var key_value = [];
				var unbalance_line = [];

				var total_value_plan = 0;
				var total_value = 0;
				var max_plan = 0;
				var maxes = 0;

				$('#unbalance_line').val('');
				$('#box_unbalance').removeAttr('class');
				$('#box_unbalance').prop('class','small-box');

				for (var j = 0; j < result.key.length; j++) {
					// if (result.key[j].key != 'qa-audit') {
						if (['1','2','3','4','5'].includes(result.key[j].key)) {
							keys.push('LINE '+result.key[j].key.toUpperCase());
							key.push(result.key[j].key.toUpperCase());
						}
						// if (isNaN(parseInt(result.key[j].key))) {
						// 	keys.push(result.key[j].key.toUpperCase());
						// }else{
						// 	keys.push('LINE '+result.key[j].key.toUpperCase());
						// }
					// }
				}
				
				for (var i = 0; i < result.data.length; i++) {
					for (var j = 0; j < result.key.length; j++) {

						if(result.data[i].location == result.key[j].key){

							if (['1','2','3','4','5'].includes(result.key[j].key)) {
								unbalance_line.push({line:result.key[j].key,actual:Math.ceil(result.data[i].actual_time / result.key[j].jml)});
								key_value_plan.push(Math.ceil(result.data[i].plan_time / result.key[j].jml));
								key_value.push(Math.ceil(result.data[i].actual_time / result.key[j].jml));
								
								total_value_plan += Math.ceil(result.data[i].plan_time / result.key[j].jml);
								total_value += Math.ceil(result.data[i].actual_time / result.key[j].jml);
								
								if(Math.ceil(result.data[i].plan_time / result.key[j].jml) > max_plan){
									max_plan = Math.ceil(result.data[i].plan_time / result.key[j].jml);
								}

								if(Math.ceil(result.data[i].actual_time / result.key[j].jml) > maxes){
									maxes = Math.ceil(result.data[i].actual_time / result.key[j].jml);
								}
							}
						}
					}
				}

				unbalance_line.sort(dynamicSort('actual'));

				if (result.time > '08:00:00') {
					if (unbalance_line.length > 0) {
						$('#box_unbalance_line').removeAttr('class');
						$('#box_unbalance_line').prop('class','small-box sedang');
						$('#unbalance_line').html('LINE '+unbalance_line[0].line);
					}
				}

				var balance_ratio_plan = total_value_plan / (max_plan * key.length);
				var balance_ratio = total_value / (maxes * key.length);

				$('#balance_ratio_plan_line').append().empty();

				$('#balance_ratio_plan_line').append('0%');
				if ((balance_ratio_plan*100).toFixed(2) != 'NaN') {
					$('#balance_ratio_plan_line').html((balance_ratio_plan*100).toFixed(2) + "%");
				}

				$('#balance_ratio_line').append().empty();

				$('#balance_ratio_line').append('0%');
				if ((balance_ratio*100).toFixed(2) != 'NaN') {
					$('#balance_ratio_line').html((balance_ratio*100).toFixed(2) + "%");
				}

				var chart = Highcharts.chart('container2', {
					title: {
						text: 'Line Assembly Process Balance '+result.titles.toUpperCase(),
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.tanggal,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'Minutes'
						},
						style: {
							fontSize: '26px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: keys,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								// fontSize: '26px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
					},
					credits: {
						enabled:false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						shadow: false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y} Min',
								style:{
									textOutline: false,
								}
							},
							animation: false,
							// cursor: 'pointer'
						}
					},
					series: [
					{
						name:'Plan',
						color: 'rgb(255,116,116)',
						type: 'column',
						data: key_value_plan,
					},
					{
						name:'Actual',
						color: 'rgb(169,255,151)',
						type: 'column',
						data: key_value,
					}
					]

				});
				$('#loading').hide();
			}

		});

	}



</script>
@endsection