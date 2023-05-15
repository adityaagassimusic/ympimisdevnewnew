@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	.dot {
		height: 5%;
		width: 5%;
		position: absolute;
		z-index: 10;
	}

	.text {
		color: white;
		font-size: 1.2vw;
		font-weight: bold;
		display: inline-block;
		vertical-align: middle;
	}

	.text2 {
		color: white;
		font-size: 1.6vw;
		font-weight: bold;
	}

	.alarm {
		-webkit-animation: alarm_ani 1s infinite;  /* Safari 4+ */
		-moz-animation: alarm_ani 1s infinite;  /* Fx 5+ */
		-o-animation: alarm_ani 1s infinite;  /* Opera 12+ */
		animation: alarm_ani 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes alarm_ani {
		0%, 49% {
			background-color: #7335a2;
		}
		50%, 100% {
			background-color: #ed2f2f;
		}
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-4" id="map">
			<center>
				<img src="{{url("images/office_map.png")}}" style="height: 90vh;">
			</center>
		</div>
		<div class="col-xs-8">
			<div class="row">
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office0">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 1</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co0" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co0"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp0"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd0"></span> %<br>
							</center>
						</div>
					</div>
				</div>
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office1">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 2</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co1" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co1"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp1"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd1"></span> %<br>
							</center>
						</div>
					</div>
				</div>
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office2">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 3</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co2" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co2"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp2"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd2"></span> %<br>
							</center>
						</div>
					</div>
				</div>
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office3">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 4</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co3" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co3"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp3"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd3"></span> %<br>
							</center>
						</div>
					</div>
				</div>
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office4">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 5</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co4" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co4"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp4"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd4"></span> %<br>
							</center>
						</div>
					</div>
				</div>
				<div class="col-xs-6" style="border: 2px solid black; height: 30vh; padding: 0; background: #7335a2" id="office5">
					<center><span style="font-weight: bold; font-size: 3vh; color: white;">Office 6</span></center>
					<div class="col-xs-12" style="padding: 0;">
						<div class="col-xs-8" style="padding: 0;">
							<div id="chart_co5" style="height: 25vh;">
							</div>
						</div>
						<div class="col-xs-4 text">
							<center>
								<span style="color: #f3c612">CO2</span> <br><span  class="text2" id="co5"></span> ppm<br>
								<span style="color: #f3c612">Temp.</span> <br><span class="text2"  id="tmp5"></span> ℃<br>
								<span style="color: #f3c612">Humd.</span> <br><span class="text2"  id="humd5"></span> %<br>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>  
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

	jQuery(document).ready(function() {
		fetchData();
		setInterval(fetchData, 120000);
		
	});

	function fetchData(){
		$.get('{{ url("post/general/airvisual/data") }}', function(result, status, xhr){
			// ----------  MAP ------------

			$(".dot").remove();

			var point_data = "";
			var cls = "";
			if (result.last_data[0].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_1" class="dot '+cls+'" style="height: 15vh; width: 10vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.6vw; color: #f3c612;">Office 1</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[0].co+' ppm</span>';

			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_1');
			div.style.left = '13vh';
			div.style.top = '59vh';

			var point_data = "";
			var cls = "";
			if (result.last_data[1].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_2" class="dot '+cls+'" style="height: 15vh; width: 10vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: #f3c612;">Office 2</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[1].co+' ppm</span>';
			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_2');
			div.style.left = '13vh';
			div.style.top = '42vh';

			var point_data = "";
			var cls = "";
			if (result.last_data[2].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_3" class="dot '+cls+'" style="height: 15vh; width: 10vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: #f3c612;">Office 3</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[2].co+' ppm</span>';
			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_3');
			div.style.left = '13vh';
			div.style.top = '25vh';


			var point_data = "";
			var cls = "";
			if (result.last_data[3].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_4" class="dot '+cls+'" style="height: 15vh; width: 10vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: #f3c612;">Office 4</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[3].co+' ppm</span>';
			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_4');
			div.style.left = '7vh';
			div.style.top = '3vh';

			var point_data = "";
			var cls = "";
			if (result.last_data[4].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_5" class="dot '+cls+'" style="height: 15vh; width: 8vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: #f3c612;">Office 5</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[4].co+' ppm</span>';
			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_5');
			div.style.left = '26vh';
			div.style.top = '3vh';

			var point_data = "";
			var cls = "";
			if (result.last_data[5].co >= 1000) {
				cls = 'alarm';
			}
			point_data += '<div id="map_office_6" class="dot '+cls+'" style="height: 15vh; width: 8vw; background-color: rgba(112,48,160,0.8);">';
			point_data += '<center>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: #f3c612;">Office 6</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 1.5vw; color: white;">CO2 :</span><br>';
			point_data += '<span style="font-weight: bold; font-size: 2vw; color: white;">'+result.last_data[5].co+' ppm</span>';
			point_data += '</center>';
			point_data += '</div>';
			$('#map').append(point_data);

			var div = document.getElementById('map_office_6');
			div.style.left = '42vh';
			div.style.top = '3vh';

		// -----------------------------------------------------------------------------------
		var count_alarm = 0;
		$.each(result.last_data, function(index, value){
			categories = [];
			co = [];
			max = [];
			bottom = [];
			mid = [];
			upper = [];

			var table_content = "";
			var node_name = "";
			var current_co = value.co;
			var current_temp = value.temperature;
			var current_humd = value.humidity;

			$.each(result.datas, function(index2, value2){
				if (value.location == value2.location) {
					max.push(1000);
					categories.push(value2.data_time2);
					co.push(value2.co);
					node_name = value2.location;
					current_co = value2.co;
					current_temp = value2.temperature;
					current_humd = value2.humidity;
				}
			});

			if (value.co >= 1000) {
				count_alarm++;
				$("#office"+index).addClass('alarm');
			} else {
				$("#office"+index).removeClass('alarm');
			}

			$("#co"+index).text(current_co);
			$("#tmp"+index).text(current_temp);
			$("#humd"+index).text(current_humd);

			Highcharts.chart('chart_co'+index, {
				chart: {
					type: 'spline'
				},

				title: {
					text: ''
				},

				yAxis: {
					title: {
						text: 'CO2 rate'
					},
					gridLineWidth: 0,
					minorGridLineWidth: 0,
					max: 1200,
					min: 300,
					plotBands: [{
						from: 0,
						to: 799,
						color: '#57ff5c'
					}, {
						from: 800,
						to: 999,
						color: '#fcba03'
					}, {
						from: 1000,
						to: 5000,
						color: '#ed4545'
					}],
				},

				xAxis: {
					categories: categories,
					tickInterval: 15
				},

				legend: {
					enabled: false
				},

				credits:{
					enabled:false
				},

				plotOptions: {
					series: {
						label: {
							connectorAllowed: false
						},
						marker: {
							enabled: false
						},
						animation: false,
					},
					spline: {
						dataLabels: {
							enabled: true,
							formatter: function(){
								var isLast = false;
								if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
								if (isLast) {
									return this.x;
								} else {
									return '';
								}
							},
							allowOverlap: true
						},
					}
				},

				series: [
				{
					type: 'line',
					name: 'Max',
					color: 'red',
					data: max
				},
				{
					name: 'CO2',
					data: co,
					color: '#901aeb',
					lineWidth: 3
				}],

				responsive: {
					rules: [{
						condition: {
							maxWidth: 500
						},
					}]
				},

				exporting: {
					enabled: false
				}

			});
		})

			//Alarm
			if (count_alarm > 0) {

				var times = 5;
				var loop = setInterval(repeat, 2000);

				function repeat() {
					times--;
					if (times === 0) {
						clearInterval(loop);
					}

					audio_error.play();
				}

				repeat(); 
			}
		});
}

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

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#90ee7e', '#f45b5b', '#7798BF', '#e3311e', '#aaeeee', '#ff0066',
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