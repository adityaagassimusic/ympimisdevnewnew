@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:left;
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>
			<span> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div id="chart"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="modal_detail">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><b id="modal-title"></b></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered table-stripped table-responsive" style="width: 100%" id="tabelMaster">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>No</th>
									<th>Section</th>
									<th style="width: 1%">Total Kaizen</th>
								</tr>
							</thead>
							<tbody id="tabelDetail"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-xs-6">
						<a class="btn btn-sm btn-success pull-left" href="{{ url('index/kaizen') }}"><i class="fa fa-arrow-right"></i> Go To Verification Page</a>
					</div>
					<div class="col-xs-6">
						<button type="button" class="btn btn-danger btn-sm pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>

<script src="{{ url("js/highcharts.js")}}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		getData();

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	});

	function getData() {

		$.get('{{ url("fetch/kaizen/aproval/grafik/resume") }}', function(result, status, xhr){

			var arrays = result.datas;
			var new_arrays = [];
			var category = [];
			var series = [];

			totals = arrays.reduce(function (r, o) {
				(r[o.name])? r[o.name] += parseInt(o.ct) : r[o.name] = parseInt(o.ct);
				return r;
			}, {});


			$.each(totals, function(key, value) {
				new_arrays.push({'name' : key, 'kz' : value});
			})

			new_arrays.sort(function(a, b) {
				return b.kz - a.kz
			})

			$.each(new_arrays, function(key, value) {
				category.push(value.name);
				series.push(value.kz);
			})

			Highcharts.chart('chart', {
				chart: {
					type: 'column'
				},
				title: {
					text: 'Outstanding Kaizen Teian',
					style: {
						fontSize: '30px',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					type: 'category',
					categories: category
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Total Kaizen'
					}
				},
				plotOptions: {
					column : {
						cursor: 'pointer',
						minPointLength: 3,
						dataLabels: {
							allowOverlap: true,
							enabled: true,
							style: {
								color: 'black',
								fontSize: '13px',
								textOutline: false,
								fontWeight: 'bold',
							}
						},
						point: {
							events: {
								click: function () {
									detailModal(this.category);
								}
							}
						},
					}
				},
				legend: {
					enabled: false
				},
				tooltip: {
					pointFormat: 'Kaizen Teian : <b>{point.y}</b>'
				},
				credits:{
					enabled:false
				},
				series: [{
					name: 'Outstanding Kaizen',
					data: series,
				}]
			});

		})
	}

	function detailModal(foreman) {
		$("#loading").show();
		var data = {
			'foreman' : foreman
		}

		$.get('{{ url("fetch/kaizen/aproval/grafik/resume/detail") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#modal-title").text('Outstanding Kaizen Teian : '+foreman);
				$("#modal_detail").modal('show');
				$("#loading").hide();
				var body = '';

				$("#tabelDetail").empty();

				// tabelMaster
				
				$.each(result.datas, function(key, value) {
					body += '<tr>';
					body += '<td style="text-align: center">'+(key+1)+'</td>';
					body += '<td>'+value.area+'</td>';
					body += '<td style="text-align: right">'+value.ct+'</td>';
					body += '</tr>';
				})

				$("#tabelDetail").append(body);
			} else {
				openErrorGritter('Error', result.message);
			}
		})
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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

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

</script>
@endsection
