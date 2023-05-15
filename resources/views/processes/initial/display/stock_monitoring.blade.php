@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="location" value="{{ $location }}">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" multiple="multiple" id='locs' data-placeholder="Select Products" style="width: 100%;">
						@foreach($locs as $loc)
						<option value="{{$loc}}">{{ trim($loc, "'")}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-info" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="col-xs-2 col-xs-offset-6">
					<a class="btn btn-primary pull-right" href="{{ url("/index/initial/table_stock_monitoring", "mpro") }}"><i class="fa fa-book"></i> Resume M-PRO Stock</a>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div id="container" style="height: 690px;"></div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 1%;">Remark</th>
								<th style="width: 3%;">Stock/Day</th>
								<th style="width: 3%;">Act. Stock</th>
								<th style="width: 3%;">Stock</th>
								<th style="width: 3%;">Lot</th>
								<th style="width: 3%;">Kanban</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#locs').val('');
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 1000*60*10);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#a488aa', '#7798BF', '#aaeeee', '#f45b5b',
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

	function fillChart(){
		var location = $('#location').val();
		if($('#locs').val() != ""){
			location = $('#locs').val().toString();
		}
		var data = {
			location : location,
		}
		$.get('{{ url("fetch/initial/stock_monitoring") }}', data, function(result, status, xhr){
			if(result.status){
				var data = result.stocks;
				var categories = [];
				// var series = [];

				for(i = 0; i < data.length; i++){
					if (categories.indexOf(data[i].category) === -1) {
						categories.push(data[i].category);
					}
				}

				var series = [];
				var names = [];

				for (let i = 0; i < data.length; i++) {
					if (names.indexOf(data[i].remark) !== -1) {
						series[names.indexOf(data[i].remark)].data.push(
							data[i].material
							)
					} else {
						names.push(data[i].remark)
						series.push({
							name: data[i].remark,
							data: [data[i].material]
						})
					}
				}

				var chart = Highcharts.chart('container', {
					chart: {
						type: 'column'

					},
					title: {
						text: 'Realtime M-PRO Stock Monitoring',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Last Update: '+getActualFullDate(),
						style: {
							fontSize: '18px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories,
						plotBands: [{
							from: series.length-0.5-6,
							to: series.length-0.5-3,
							color: 'RGBA(235, 14, 62, 0.5)',
							label: {
								style: {
									text: 'Priority',
									color: '#ffffff'
								},
							}
						},
						{
							from: series.length-0.5-3,
							to: series.length-0.5-1,
							color: 'RGBA(240, 137, 58, 0.5)',
							label: {
								style: {
									text: 'Stabil',
									color: '#ffffff'
								},
							}
						},
						{
							from: series.length-0.5-1,
							to: series.length-0.5+3,
							color: 'RGBA(58, 240, 110, 0.5)',
							label: {
								style: {
									text: 'Stabil',
									color: '#ffffff'
								},
							}
						},
						{
							from: series.length-0.5+3,
							to: series.length-0.5+5,
							color: 'RGBA(87, 123, 242, 0.5)',
							label: {
								style: {
									text: 'Stabil',
									color: '#ffffff'
								},
							}
						}],
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '26px'
							}
						},
					},
					yAxis: {
						title: {
							text: 'Count Item'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								fontSize: '20px',
								textOutline: false,
								color: 'white'
							}
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 35,
						floating: true,
						borderWidth: 1,
						shadow: false
					},
					credits: {
						enabled: false
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						series:{
							stacking: 'normal',
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									textOutline: false
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchModal(this.category, this.series.name);
									}
								}
							}
						}
					},
					series: series
				});
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});

	}

	function fetchModal(category, name){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();
		var location = $('#location').val();
		if($('#locs').val() != ""){
			location = $('#locs').val().toString();
		}
		var data = {
			location : location,
			category:category,
			remark:name
		}
		$.get('{{ url("fetch/initial/stock_monitoring_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableDetailBody').html('');

				var index = 1;
				var resultData = "";

				$.each(result.stocks, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.material_number +'</td>';
					resultData += '<td>'+ value.description +'</td>';
					resultData += '<td>'+ value.remark +'</td>';
					resultData += '<td>'+ value.safety.toLocaleString() +'</td>';
					resultData += '<td>'+ parseInt(value.quantity).toLocaleString() +'</td>';
					resultData += '<td>'+ value.days.toFixed(2) +' Day(s)</td>';
					resultData += '<td>'+ value.lot +'</td>';
					resultData += '<td>'+ value.kanban +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Stock: "+category+"</span></center>");
				$('#loading').hide();
				$('#tableDetail').show();
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
	}
</script>
@endsection