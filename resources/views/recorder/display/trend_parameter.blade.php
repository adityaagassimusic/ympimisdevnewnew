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
		<div id="period_title" class="col-xs-6" style="background-color: rgba(248,161,63,0.9);padding-left: 5px;padding-right: 5px;"><center><span style="color: black; font-size: 1.6vw; font-weight: bold;" id="title_text">TREND NG</span></center></div>
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
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		fetchData();
		// setInterval(fetchData, 1000*60*60);
	});

	function fetchData(){
		if ($('#date_from').val() != '' || $('#date_to').val() != '') {
			if ($('#date_from').val() == '') {
				openErrorGritter('Error!', 'Date From can not be null');
				return false;
			}
			if ($('#date_to').val() == '') {
				openErrorGritter('Error!', 'Date To can not be null');
				return false;
			}
		}
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/recorder/display/parameter/ng") }}',data,  function(result, status, xhr){
			if(result.status){
				$('#monitoring').html('');
				var monitoring = '';
				for(var i = 0; i < result.mesin.length;i++){
					monitoring += '<div class="col-xs-6" style="height: 25vw; margin-bottom: 30px;margin-top:5px;padding-left:10px;padding-right:10px;"><div id="'+result.mesin[i].mesin+'_'+result.mesin[i].part+'" style="width: 100%;">aaa</div></div>';
				}
				$('#monitoring').append(monitoring);

				for(var i = 0; i < result.mesin.length;i++){
					var ng_rate = [];
					var category = [];
					var shot = [];
					var perbaikan = [];
					// for(var j = 0; j < result.ngs.length;j++){
					// 	for(var k = 0; k < result.ngs[j].length;k++){
					// 		if (result.ngs[j][k].mesin == result.mesin[i].mesin && result.ngs[j][k].part == result.mesin[i].part) {
					// 			console.log(result.ngs[j][k].maintenance);
					// 				category.push(result.ngs[j][k].maintenance.split('_')[1]);
					// 			ng_rate.push(parseFloat(((result.ngs[j][k].ng/result.ngs[j][k].shot)*100).toFixed(2)));

					// 		}
					// 	}
					// }
					var plotBands = [];
					var shots = 0;
					for(var j = 0; j < result.ngs.length;j++){
						for(var k = 0; k < result.ngs[j].length;k++){
							if (result.ngs[j][k].mesin == result.mesin[i].mesin && result.ngs[j][k].part == result.mesin[i].part) {
								category.push(result.ngs[j][k].start);
								ng_rate.push(parseFloat(((result.ngs[j][k].ng/result.ngs[j][k].run_shot)*100).toFixed(2)));
								var per = 0;
								if (result.ngs[j][k].maintenance != null) {
									if ( typeof result.ngs[j][k+1] !== 'undefined') {
										if (result.ngs[j][k].maintenance != result.ngs[j][k+1].maintenance) {
											// perbaikan.push(1);
											per = 1;
										}else{
											// perbaikan.push(0);
											per = 0;
										}
									}else{
										// perbaikan.push(0);
										per = 0;
									}
								}
								perbaikan.push(per);
								if (per == 0) {
									// shot.push(parseInt(result.ngs[j][k].total_shot));
									shots = shots + parseInt(result.ngs[j][k].run_shot);
									
								}else{
									shots = 0;
								}
								shot.push(parseInt(shots));
							}
						}
					}

					for(var o = 0; o< perbaikan.length;o++){
						if (perbaikan[o] == 1) {
							plotBands.push({from: (o-0.5), to: (o+0.5), color: 'rgba(255, 116, 116 ,.3)'});
						}
					}

					Highcharts.chart(result.mesin[i].mesin+'_'+result.mesin[i].part, {
					    chart: {
					        zoomType: 'xy',
					    },
					    title: {
					        text: 'NG Rate',
					        style:{
					        	fontWeight:'bold',
					        	fontSize:'13px'
					        }
					    },
					    subtitle: {
					        text: result.mesin[i].mesin+' - '+result.mesin[i].part
					    },
					    xAxis: [{
					        categories: category,
					        crosshair: true,
					        // visible:true,
					        plotBands:plotBands
					    }],
					    yAxis: [{ 
					        labels: {
					            format: '{value}%',
					            style: {
					                color: '#fff'
					            }
					        },
					        title: {
					            text: 'NG',
					            style: {
					                color: '#fff'
					            }
					        }
					    },{ 
					        labels: {
					            format: '{value}',
					            style: {
					                color: '#fff'
					            }
					        },
					        title: {
					            text: 'Periodik',
					            style: {
					                color: '#fff'
					            }
					        }
					    },{ 
					        title: {
					            text: 'Total Shot',
					            style: {
					                color: '#fff'
					            }
					        },
					        labels: {
					            format: '{value}',
					            style: {
					                color: '#fff'
					            }
					        },
					        opposite: true
					    }],
					    credits:{
					    	enabled:false
					    },
					    tooltip: {
					        shared: true
					    },
					    legend: {
					        enabled:true
					    },
					    plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: true,
									format: '{point.y:.2f}%',
									style:{
										fontSize: '11px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								lineWidth: 1
							},
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: false,
									format: '{point.y}',
									style:{
										fontSize: '11px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
							},
							spline:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                    	showModalDetail(this.category);
				                    }
				                  }
				                },
								dataLabels: {
									enabled: false,
									format: '{point.y}',
									style:{
										fontSize: '11px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								cursor: 'pointer',
								borderColor: 'black',
							},
						},
					    series: [
					    {
					        name: 'NG',
					        type: 'spline',
					        data: ng_rate,
					        color: '#ed151d',
					        yAxis: 0,
					        tooltip: {
					            valueSuffix: '%'
					        }

					    },{
					        name: 'Periodik',
					        type: 'column',
					        data: perbaikan,
					        color: 'none',
					        yAxis: 1,
					    },
					     {
					        name: 'Total Shot',
					        type: 'spline',
					        data: shot,
					        color: '#2c5394',
					        yAxis: 2,
					        // tooltip: {
					        //     valueSuffix: '%'
					        // }
					    }]
					});
				}
			}
			else{
				alert('Attempt to retrieve data failed.');
				$('#loading').hide();
			}
		});
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

