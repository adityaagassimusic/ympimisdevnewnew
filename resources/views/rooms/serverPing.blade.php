@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-header {
    background-color: #61258e !important;
    padding: 10px;
    color: white;
  }

  .content-header > h1 {
    margin: 0;
    font-size: 80px;
    text-align: center;
    font-weight: bold;
  }

  .content-header > h2 {
    margin: 0;
    font-size: 3vw;
    text-align: center;
    font-weight: bold;
  }

  .content-header .keterangan {
    margin: 0;
    font-size: 3vw;
    text-align: center;
    vertical-align: middle;
  }


  .content-header .isi_internet {
    margin: 0;
    font-size: 150px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-header .isi_vpn {
    margin: 0;
    font-size: 150px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-header .isi_vpn_yamaha {
    margin: 0;
    font-size: 150px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-wrapper{
  	padding: 0 !important;
  }


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
	<div class="row" style="padding: 0">
		<div class="col-xs-12">
			<div class="row">

				<div class="col-md-12 content-header" style="background-color: #424242 !important;color: #fff">
					<h2>REPLY ROUND TRIP TIME</h2>
				</div>

				<div class="col-md-6 content-header" style="background-color: #4a148c !important;border-right: 10px solid #424242">
	          		<h1>INTERNET</h1>
				</div>
				<div class="col-md-6 content-header" style="background-color: #ffeb3b !important;border-left: 10px solid #424242;color: #000">
	          		<h1>VPN YCJ</h1>  
				</div>
				<!-- <div class="col-md-4 content-header" style="background-color: #fc5603 !important;border-left: 10px solid #424242;color: #000">
	          		<h1>VPN Yamaha</h1>  
				</div> -->

				<div class="col-md-6 content-header" style="color: #fff;padding: 0;border-right: 10px solid #424242">
  				<div id="isi_internet" class="isi_internet">
    				<span id="time_internet"> 0</span> <span style="font-size: 100px">ms</span>
    			</div>
  			</div>

  			<div class="col-md-6 content-header" style="background-color: yellow !important;color: #000;padding: 0;border-left: 10px solid #424242;">
  				<div id="isi_vpn" class="isi_vpn">
    				<span id="time_vpn"> 0</span> <span style="font-size: 100px">ms</span>
    			</div>
  			</div>

  			<!-- <div class="col-md-4 content-header" style="background-color: orange !important;color: #000;padding: 0;border-left: 10px solid #424242;">
  				<div id="isi_vpn_yamaha" class="isi_vpn_yamaha">
    				<span id="time_vpn_yamaha"> 0</span> <span style="font-size: 100px" id="keterangan_time_vpn_yamaha">ms</span>
    			</div>
  			</div> -->

				<div class="col-md-6" style="padding: 0">
					<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
						<div class="box-body" style="padding: 0px">
							<div class="div_name" style="border-right: 10px solid #424242; padding-left: 10px; padding-right: 10px; text-align: center;">
								<div style="font-weight: bold; font-size: 30px; display: inline-block; ">Internet Connection Today</div>
								<div id="chart_internet" style="margin-top: 5px;margin-left: 10px"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6" style="padding: 0">
					<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
						<div class="box-body" style="padding: 0px">
							<div class="div_name" style="border-left: 10px solid #424242;padding-right: 20px; text-align: center;">
								<div style="font-weight: bold; font-size: 30px; display: inline-block; ">VPN Connection Today</div>
								<div id="chart_vpn" style="margin-top: 5px;margin-left: 10px"></div>
							</div>
						</div>
					</div>
				</div>

				<!-- <div class="col-md-4" style="padding: 0">
					<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
						<div class="box-body" style="padding: 0px">
							<div class="div_name" style="border-left: 10px solid #424242;padding-right: 20px; text-align: center;">
								<div style="font-weight: bold; font-size: 30px; display: inline-block; ">VPN Yamaha Connection</div>
								<div id="chart_vpn_yamaha" style="margin-top: 5px;margin-left: 10px"></div>
							</div>
						</div>
					</div>
				</div> -->


			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		postData();
		setInterval(postData, 60000);
	});

	function postData() {

		var ip = 'www.google.com';
		var remark = 'Internet';

		var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+ip;

			$.get(url, function(result, status, xhr){
				var time;

				if (result.sta == 0) {
					if (result.output.length == 8) {
						timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
					if(timearray[1] != undefined){
						time = timearray[1];
					}else if(timearray[2] != undefined){
						time = timearray[2];
					}
					status = "Alive";
				}
				else{
					time = 0;
					status = "Host Unreachable";
				}
			}
			else{
				time = 0;
				status = "Timed Out";
			}
			
			var data = {	
				ip : ip,
				remark : remark,
				hasil_hit : time,
				status : status
			}

			$.post('{{ url("post/display/ip_log") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter("Success","IP Log Created");
				} else {
					openErrorGritter('Error',result.message);
				}
			});

			$('#time_internet').append().empty();
			$('#time_internet').html(time);

			if(time > 0 && time < 120) {
				$("#isi_internet").addClass("bg-green");	
				$("#isi_internet").removeClass('bg-orange');	
				$("#isi_internet").removeClass('bg-red');

			}
			else if(time > 120){
				$("#isi_internet").addClass("bg-orange");
				$("#isi_internet").removeClass('bg-green');	
				$("#isi_internet").removeClass('bg-red');
			}
			else if(time == 0){
				$("#isi_internet").addClass("bg-red");
				$("#isi_internet").removeClass('bg-green');	
				$("#isi_internet").removeClass('bg-orange');
			}
		});

		var ip2 = '133.176.54.20';
		var remark2 = 'VPN';

			var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+ip2;

			$.get(url, function(result, status, xhr){
				var time;

				if (result.sta == 0) {
					if (result.output.length == 8) {
						timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
					if(timearray[1] != undefined){
						time = timearray[1];
					}else if(timearray[2] != undefined){
						time = timearray[2];
					}
					status = "Alive";
				}
				else{
					time = 0;
					status = "Host Unreachable";
				}
			}
			else{
				time = 0;
				status = "Timed Out";
			}
			
			var data = {	
				ip : ip2,
				remark : remark2,
				hasil_hit : time,
				status : status
			}

			$.post('{{ url("post/display/ip_log") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter("Success","IP Log Created");
				} else {
					openErrorGritter('Error',result.message);
				}
			});

			$('#time_vpn').append().empty();
			$('#time_vpn').html(time);

			if(time > 0 && time < 120) {
				$("#isi_vpn").addClass("bg-green");	
				$("#isi_vpn").removeClass('bg-orange');	
				$("#isi_vpn").removeClass('bg-red');

			}
			else if(time > 120){
				$("#isi_vpn").addClass("bg-orange");
				$("#isi_vpn").removeClass('bg-green');	
				$("#isi_vpn").removeClass('bg-red');
			}
			else if(time == 0){
				$("#isi_vpn").addClass("bg-red");
				$("#isi_vpn").removeClass('bg-green');	
				$("#isi_vpn").removeClass('bg-orange');
			}
		});

		// var ip3 = '10.110.52.5';
		// var remark3 = 'VPN Yamaha';

		// 	var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+ip3;

		// 	$.get(url, function(result, status, xhr){
		// 		var time;

		// 		if (result.sta == 0) {
		// 			if (result.output.length == 8) {
		// 				timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
		// 			if(timearray[1] != undefined){
		// 				time = timearray[1];
		// 			}else if(timearray[2] != undefined){
		// 				time = timearray[2];
		// 			}
		// 			status = "Alive";
		// 		}
		// 		else{
		// 			time = 0;
		// 			status = "Host Unreachable";
		// 		}
		// 	}
		// 	else{
		// 		time = 0;
		// 		status = "Timed Out";
		// 	}
			
		// 	var data = {	
		// 		ip : ip3,
		// 		remark : remark3,
		// 		hasil_hit : time,
		// 		status : status
		// 	}

		// 	$.post('{{ url("post/display/ip_log") }}', data, function(result, status, xhr){
		// 		if(result.status){
		// 		} else {
		// 			openErrorGritter('Error',result.message);
		// 		}
		// 	});

		// 	$('#time_vpn_yamaha').append().empty();
		// 	$('#time_vpn_yamaha').html(time);

		// 	if(time > 0 && time < 120) {
		// 		$("#isi_vpn_yamaha").addClass("bg-green");	
		// 		$("#isi_vpn_yamaha").removeClass('bg-orange');	
		// 		$("#isi_vpn_yamaha").removeClass('bg-red');

		// 	}
		// 	else if(time > 120){
		// 		$("#isi_vpn_yamaha").addClass("bg-orange");
		// 		$("#isi_vpn_yamaha").removeClass('bg-green');	
		// 		$("#isi_vpn_yamaha").removeClass('bg-red');
		// 	}
		// 	else if(time == 0){
		// 		$("#isi_vpn_yamaha").addClass("bg-red");
		// 		$("#isi_vpn_yamaha").removeClass('bg-green');	
		// 		$("#isi_vpn_yamaha").removeClass('bg-orange');

		// 		$('#time_vpn_yamaha').html("Disconnected");
		// 		$('#time_vpn_yamaha').css("font-size", "90px");

		// 		$('#keterangan_time_vpn_yamaha').append().empty();
		// 		$('#keterangan_time_vpn_yamaha').html();
		// 	}
		// });

		$.get('{{ url("post/server_room/ping/trend") }}', function(result, status, xhr){
			if (result.status) {

				categories_internet = [];
				ping_time_internet = [];

				categories_vpn = [];
				ping_time_vpn = [];

				categories_vpn_yamaha = [];
				ping_time_vpn_yamaha = [];
				max = [];

				$.each(result.data_ping, function(index, value){
					categories_internet.push(value.data_time);
					ping_time_internet.push(value.time);
					max.push(120);
				})

				$.each(result.data_vpn, function(index, value){
					categories_vpn.push(value.data_time);
					ping_time_vpn.push(value.time);
				})

				// $.each(result.data_vpn_yamaha, function(index, value){
				// 	categories_vpn_yamaha.push(value.data_time);
				// 	ping_time_vpn_yamaha.push(value.time);
				// })

				Highcharts.chart('chart_internet', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: ''
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: categories_internet,
						tickInterval: 30
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
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
						name: 'Minimum Ping',
						color: 'red',
						data: max
					},
					{
						name: 'Time',
						data: ping_time_internet,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});

				Highcharts.chart('chart_vpn', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: ''
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: categories_vpn,
						tickInterval: 30
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
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
						name: 'Minimum Ping',
						color: 'red',
						data: max
					},
					{
						name: 'Time',
						data: ping_time_vpn,
						color: 'yellow',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});

				// Highcharts.chart('chart_vpn_yamaha', {
				// 	chart: {
				// 		type: 'spline',
				// 		height: '250px'
				// 	},

				// 	title: {
				// 		text: ''
				// 	},

				// 	yAxis: {
				// 		title: {
				// 			text: 'Time (ms)'
				// 		},
				// 		gridLineWidth: 1,
				// 	},

				// 	xAxis: {
				// 		categories: categories_vpn_yamaha,
				// 		tickInterval: 30
				// 	},

				// 	legend: {
				// 		enabled: false
				// 	},

				// 	credits:{
				// 		enabled:false
				// 	},

				// 	exporting: {
				// 		enabled: false
				// 	},

				// 	plotOptions: {
				// 		series: {
				// 			label: {
				// 				connectorAllowed: false
				// 			},
				// 			marker: {
				// 				enabled: false
				// 			},
				// 			animation: false,
				// 		},
				// 		spline: {
				// 			dataLabels: {
				// 				enabled: true,
				// 				formatter: function(){
				// 					var isLast = false;
				// 					if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
				// 					if (isLast) {
				// 						return this.x;
				// 					} else {
				// 						return '';
				// 					}
				// 				},
				// 				allowOverlap: true
				// 			},
				// 		}
				// 	},

				// 	series: [
				// 	{
				// 		type: 'line',
				// 		name: 'Minimum Ping',
				// 		color: 'red',
				// 		data: max
				// 	},
				// 	{
				// 		name: 'Time',
				// 		data: ping_time_vpn_yamaha,
				// 		color: 'yellow',
				// 		lineWidth: 3
				// 	}],

				// 	responsive: {
				// 		rules: [{
				// 			condition: {
				// 				maxWidth: 500
				// 			},
				// 		}]
				// 	}

				// });
			}
		})
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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



</script>
@endsection