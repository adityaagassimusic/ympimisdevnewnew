@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>
	<div class="row">

		<div class="col-xs-2" style="padding-top: 0.25%;">
			<div class="input-group date">
				<div class="input-group-addon bg-purple">
					<i class="fa fa-calendar-o" style="color:white"></i>
				</div>
        <input type="text" class="form-control datepicker" id="date_from" placeholder="Date From" style="width: 100%;">
			</div>
		</div>

		<div class="col-xs-2" style="padding-top: 0.25%;">
			<div class="input-group date">
				<div class="input-group-addon bg-purple">
					<i class="fa fa-calendar-o" style="color:white"></i>
				</div>
        <input type="text" class="form-control datepicker" id="date_to" placeholder="Date To" style="width: 100%;">
			</div>
		</div>

		<div class="col-xs-2" style="padding-top: 0.25%;">
				<select class="form-control select2" name="location" id='location' data-placeholder="Select Location" style="width: 100%;">
					<option value="">Select Location</option>
					@foreach($location as $loc)
					<option value="{{$loc->location}}">{{$loc->location}}</option>
					@endforeach
					<option value="seasoning cl">Seasoning Room</option>
					<option value="warehouse lt1">Warehouse Lt 1</option>
					<option value="warehouse lt2">Warehouse Lt 2</option>
				</select>
		</div>

    <div class="col-xs-2" style="padding-top: 0.25%;">
      <button class="btn btn-success btn-md" onclick="fetchChart()">Update Chart</button>
    </div>


		<div id="chart_title" class="col-xs-12" style="margin-top:10px;background-color: #673ab7;">
			<center>
				<span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
			</center>
		</div>

		<div class="col-xs-12" id="container_emc" style="margin-top: 1%; height: 80vh;"></div>

		<div class="col-xs-12" id="container_scatter" style="margin-top: 1%; height: 60vh;"></div>
		
		<div class="col-xs-12" id="container_temp_hum" style="margin-top: 1%; height: 60vh;"></div>

		
	</div>
</section>

@endsection
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
		fetchChart();

		$('.select2').select2({
			allowClear : true,
		});
	});

	$('.datepicker').datepicker({
    format: "dd/mm/yyyy",
    autoclose: true,
    todayHighlight: true
  });

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

	function fetchChart(){
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();
		var location = $('#location').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
			location:location
		}

		$('#loading').show();

		$.get('{{ url("fetch/temperature/log") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();

				$('#title_text').text('MONITORING '+result.location.toUpperCase()+' ON ' + result.date);
				var h = $('#chart_title').height();
				$('.select').css('height', h);

				var data = [];

				var created = [], 
				hum = [],
				temp = [];
				emc = [];

        $.each(result.data, function(key, value) {

        	$.each(result.data_emc, function(key2, value2) {
						if (value2.temp == value.temp && value2.hum == value.hum) {
							emc.push(parseFloat(value2.emc));
						}
        	});

          created.push(value.created_at);
          hum.push(parseFloat(value.hum));
          temp.push(parseFloat(value.temp));
        });

				

				Highcharts.chart('container_emc', {
					chart: {
						type: 'spline',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Total EMC '+result.location.toUpperCase()+''
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						type: 'category',
            categories: created,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: {
						title: {
							text: 'Total EMC'
						},
						plotLines: [{
              value: 8,
              width: 5,
              color: '#ff3030',
              dashStyle: 'ShortDash',
              label: {
                text: 'Min EMC',
                style: {
                  color: '#ff3030',
                  fontWeight: 'bold'
                }         
              }
            },{
              value: 10,
              width: 5,
              color: '#ff3030',
              dashStyle: 'ShortDash',
              label: {
                text: 'Max EMC',
                style: {
                  color: '#ff3030',
                  fontWeight: 'bold'
                }             
              }
            }]
					},
					tooltip: {
						enabled: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'EMC',
            data: emc,
            color : '#ffffff' //f5f500
          }]
				});

				Highcharts.chart('container_scatter', {
					chart: {
						type: 'scatter',
            zoomType: 'xy',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Log Humidity Temperature '+result.location.toUpperCase()+' '
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						type: 'category',
            categories: created,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: [{ 
				        labels: {
				            format: '{value}°C',
				            style: {
				                color: '#fff'
				            }
				        },
				        title: {
				            text: 'Temperature',
				            style: {
				                color: '#fff'
				            }
				        }
				    }, { 
				        title: {
				            text: 'Humidity',
				            style: {
				                color: '#fff'
				            }
				        },
				        labels: {
				            format: '{value}%',
				            style: {
				                color: '#fff'
				            }
				        },
				        opposite: true
				    }],

					// yAxis: {
					// 	title: {
					// 		text: 'Total Humidity Temperature'
					// 	},
          // 	tickInterval: 10,  
					// },
					
	        tooltip: {
	            pointFormat: 'Value : {point.y}'
	        },
					plotOptions: {
						scatter: {
                marker: {
                    radius: 4.5,
                    symbol: 'circle',
                    states: {
                        hover: {
                            enabled: true,
                            lineColor: 'rgb(100,100,100)'
                        }
                    }
                },
                states: {
                    hover: {
                        marker: {
                            enabled: false
                        }
                    }
                }
            }
					},
					series: [{
            name: 'Humidity',
            data: hum,
				    yAxis: 1,
				    tooltip: {
				    	valueSuffix: '%'
				    },
            color : '#448aff' //f5f500
          },{
            name: 'Temperature',
            data: temp,
				    tooltip: {
				    	valueSuffix: '°C'
				    },
            color : '#ffffff' //f5f500
          }]
				});

				Highcharts.chart('container_temp_hum', {
					chart: {
						type: 'spline',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: 'Log Humidity Temperature '+result.location.toUpperCase()+' '
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						type: 'category',
            categories: created,
            lineColor:'#9e9e9e',
            labels: {
              formatter: function (e) {
                return ''+ this.value +'';
              }
            }
					},
					yAxis: [{ 
				        labels: {
				            format: '{value}°C',
				            style: {
				                color: '#fff'
				            }
				        },
				        title: {
				            text: 'Temperature',
				            style: {
				                color: '#fff'
				            }
				        }
				    }, { 
				        title: {
				            text: 'Humidity',
				            style: {
				                color: '#fff'
				            }
				        },
				        labels: {
				            format: '{value}%',
				            style: {
				                color: '#fff'
				            }
				        },
				        opposite: true
				    }],

					// yAxis: {
					// 	title: {
					// 		text: 'Total Humidity Temperature'
					// 	},
          // 	tickInterval: 10,  
					// },
					tooltip: {
						enabled: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);
									}
								}
							},
						},
					},
					series: [{
            name: 'Humidity',
            data: hum,
				    yAxis: 1,
				    tooltip: {
				    	valueSuffix: '%'
				    },
            color : '#448aff' //f5f500
          },{
            name: 'Temperature',
            data: temp,
				    tooltip: {
				    	valueSuffix: '°C'
				    },
            color : '#ffffff' //f5f500
          }]
				});

				$('#loading').hide();
			}else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');				
			}
		});		
}


Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#D10000', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
    time: '2000'
  });
}

</script>
@endsection