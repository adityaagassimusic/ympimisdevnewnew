@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead > tr > th{
		padding-right: 3px;
		padding-left: 3px;
	}
	tbody > tr > td{
		padding-right: 3px;
		padding-left: 3px;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div id="period_title" class="col-xs-6" style="background-color: rgba(248,161,63,0.9);padding-left: 5px;padding-right: 5px;"><center><span style="color: black; font-size: 1.6vw; font-weight: bold;" id="title_text">TREND NG BY MESIN</span></center></div>
		<div class="col-xs-3" style="padding-left: 5px;padding-right: 0px;">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right datepicker" id="date_from" name="date_from" onchange="fetchData()" placeholder="Date From">
			</div>
		</div>
		<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px;">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right datepicker" id="date_to" name="date_to" onchange="fetchData()" placeholder="Date To">
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row" id="monitoring">
			</div>
		</div>		
	</div>
</section>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/data.js"></script>
<script src="https://code.highcharts.com/stock/modules/accessibility.js"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		fetchData();
		// setInterval(fetchData, 1000*60*60);
	});

	function fetchData(){
		$('#loading').show();
		if ($('#date_from').val() != '' || $('#date_to').val() != '') {
			if ($('#date_from').val() == '') {
				openErrorGritter('Error!', 'Date From can not be null');
				$('#loading').hide();
				return false;
			}
			if ($('#date_to').val() == '') {
				openErrorGritter('Error!', 'Date To can not be null');
				$('#loading').hide();
				return false;
			}
		}
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/recorder/display/ng/mesin") }}',data,  function(result, status, xhr){
			if(result.status){
				// $('#monitoring').html('');
				var monitoring = '';
				for(var i = 0; i < result.molding.length;i++){
					monitoring += '<div class="col-xs-4" id="div_'+result.molding[i].molding+'" style="height: 25vw; margin-bottom: 30px;margin-top:5px;padding-left:10px;padding-right:10px;"><div id="'+result.molding[i].molding+'" style="width: 100%;"></div></div>';
				}
				var colorArray = [ '#00B3E6', 
				  '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
				  '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
				  '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
				  '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
				  '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
				  '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
				  '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
				  '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
				  '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF','#FFB399','#FF6633',  '#FF33FF', '#FFFF99'];
				$('#monitoring').append(monitoring);

				for(var k = 0; k < result.molding.length;k++){
					var ng_rate = [];
					var category = [];
					var shot = [];
					var perbaikan = [];

					var series = [];
					var a = 95654400;
					var mesinss = [];
						// var datas = [];
						var mesins = result.molding[k].mesin.split(',');
						for(var j = 0; j < mesins.length;j++){
							var data = [];
							for(var i = 0; i < result.date.length; i++){
								var date = new Date(result.date[i].date);
		                        var milliseconds = date.getTime();
		                        var datalabel = true;
		                        for(var l = 0; l < result.part_all[k].length;l++){
									var date = new Date(result.date[i].date);
			                        var milliseconds = date.getTime();
			                        if (result.date[i].date == result.part_all[k][l].date && mesins[j] == result.part_all[k][l].mesin) {
			                        	// if (parseInt(result.part_all[k][l].ng) == 0) {
			                        	// 	datalabel = false;
			                        	// }else{
			                        	// 	data.push([milliseconds,parseInt(result.part_all[k][l].ng)]);
			                        	// }
			                        	data.push([milliseconds,parseInt(result.part_all[k][l].ng)]);
			                        }
								}
							}
							mesinss.push(data);
						}
							// if (data.length > 0) {
							// 	series.push({
						 //            type: 'column',
						 //            enableMouseTracking:datalabel,
						 //            name: mesins[j].mesin,
						 //            data: data,
						 //        });
							// }
					// console.log(mesinss);
					var mesins = result.molding[k].mesin.split(',');
					for(var u = 0; u < mesins.length;u++){
						series.push({
				            // enableMouseTracking:datalabel,
				            name: mesins[u],
				            data: mesinss[u],
				            type:'spline',
				            colorByPoint: false,
				            color:colorArray[u],
				    //         tooltip:{
								// pointFormat: '<span style="color:#fff;font-weight: bold;">Tanggal </span>: <b>{point.x:%d-%b-%Y}</b><br/><span style="color:#fff;font-weight: bold;">Molding </span>: <b>'+molds[u]+'</b><br/><span style="color:{point.color};font-weight: bold;">NG </span>: <b>{point.y} PC(s)</b><br/>',
				    //         }
				        });
					}
					if (series.length > 0) {
						document.getElementById('div_'+result.molding[k].molding).style.display = "block";
						Highcharts.stockChart(result.molding[k].molding, {
					        chart: {
					            alignTicks: false
					        },

					        rangeSelector: {
					            selected: 1
					        },

					        title: {
					            text: result.molding[k].molding,
					            style:{
					            	fontSize:'15px',
					            	fontWeight:'bold'
					            }
					        },

					        series: series
					    });
					}else{
						document.getElementById('div_'+result.molding[k].molding).style.display = "none";
					}
				}
				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed.');
				$('#loading').hide();
			}
		});
}

function randomDate(start, end) {
    return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
}

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

