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
		color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: black !important;
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
			<div id="chart" style="height: 600px"></div>
		</div>

		<div class="col-xs-12">
			<table style="width: 100%" class="table table-bordered table-hover" id="table_master_detail">
				<thead style="background-color: rgba(126,86,134,.7); color: white;">
					<tr>
						<th rowspan="2" style="vertical-align: middle;">Department</th>
						<th rowspan="2" style="vertical-align: middle;">Section</th>
						<th colspan="3">Unverified by</th>
					</tr>
					<tr>
						<th>Foreman</th>
						<th>Chief</th>
						<th>Manager</th>
					</tr>
				</thead>
				<tbody id="body_master_detail"></tbody>
			</table>
		</div>
	</div>
</section>
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
		$.get('{{ url("fetch/kaizen/resume/grafik/resume") }}', function(result, status, xhr){

			var ctg = [];
			var chf = [];
			var frm = [];
			var mgr = [];

			$.each(result.datas, function(key, value) {

				chf.push(parseInt(value.chief));
				frm.push(parseInt(value.foreman));
				mgr.push(parseInt(value.manager));

				if(ctg.indexOf(value.department) === -1){
					ctg[ctg.length] = value.department;
				}
			})

			var body = "";

			$("#body_master_detail").empty();

			$.each(result.details, function(key, value) {

				body += '<tr>';
				body += '<td>'+value.department+'</td>';
				body += '<td>'+value.area+'</td>';
				if(value.atasan == 'chief') {
					body += '<td style="text-align: right">'+value.chf+'</td>';
					body += '<td style="text-align: right">0</td>';
				} else if (value.atasan == 'foreman') {
					body += '<td style="text-align: right">0</td>';
					body += '<td style="text-align: right">'+value.chf+'</td>';
				}
				body += '<td style="text-align: right">'+value.mngr+'</td>';
				body += '</tr>';
			})

			$("#body_master_detail").append(body);


			Highcharts.chart('chart', {
				chart: {
					type: 'column'
				},
				title: {
					text: 'Resume Outstanding Kaizen Teian',
					align: 'center',
					style: {
						fontWeight: 'bold',
					}
				},
				xAxis: {
					categories: ctg
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Count Kaien Teian'
					},
					stackLabels: {
						enabled: true,
						style: {
							fontWeight: 'bold',
							color: (
								Highcharts.defaultOptions.title.style &&
								Highcharts.defaultOptions.title.style.color
								) || 'gray',
							textOutline: 'none',
							fontSize: '20px'

						}
					}
				},
				legend: {
					align: 'center',
					borderColor: '#CCC',
					borderWidth: 1,
					shadow: false
				},
				tooltip: {
					headerFormat: '<b>{point.x}</b><br/>',
					pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
				},
				plotOptions: {
					column: {
						stacking: 'normal',
						dataLabels: {
							enabled: true,
							style: {
								fontSize: '14px'
							}
						},
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									modalTampil(this.category);
								}
							}
						},
					}, series: {
						minPointLength: 3
					}
				},
				series: [{
					name: 'Chief',
					data: chf
				}, {
					name: 'Foreman',
					data: frm
				}, {
					name: 'Manager',
					data: mgr
				}],
				credits: {
					enabled: false
				}
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
