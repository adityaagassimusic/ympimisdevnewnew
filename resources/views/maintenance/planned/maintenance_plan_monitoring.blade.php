@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/fixedHeader.dataTables.min.css") }}" rel="stylesheet">
<style type="text/css">
	html {
		transition: color 300ms, background-color 300ms;
	}

	.datepicker table tr td span.focused, .datepicker table tr td span:hover {
		background: #955da8;
	}

	.card-title {
		font-family: inherit;
		font-weight: 500;
		line-height: 1.2;
		font-size: 25px;
	}

	/*table.fixedHeader-floating{
		background-color: #212121 !important;
		color: white;
		}*/

		#loading, #error { display: none; }
	</style>
	@stop
	@section('header')
	<section class="content-header">
		<input type="hidden" id="green">
		<h1>
			{{ $page }}
		</h1>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<!-- <div class="col-xs-2 pull-left">
				<button type="button" class="btn btn-success btn-sm">History</button>
			</div> -->
			<div class="col-xs-2 pull-right">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl" onchange="getData()" placeholder="Pilih Bulan">
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div id="daily_chart"></div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #00a65a">
				<center><h2 style="margin: 0px; color: white" id="judul_modal"><b>Check Detail</b></h2></center>
			</div>
			<div class="modal-body">
				<table class="table table-bordered" id="detailTabel">
					<thead>
						<tr>
							<th>#</th>
							<th>Machine Group</th>
							<th>Machine Name</th>
							<th>Location</th>
							<th>Item Check</th>
							<th>Point Check</th>
							<th>Check Status</th>
							<th>Check Value</th>
							<th>Note</th>
							<th>Check Photo</th>
						</tr>
					</thead>
					<tbody id="detailBody">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success pull-left" onclick="orderWJO()"><i class="fa fa-check"></i> YES</button>
				<button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var mons = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march']

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();
	})

	function getData() {
		var today = new Date(),
		day = 1000 * 60 * 60 * 24,
		map = Highcharts.map,
		dateFormat = Highcharts.dateFormat;
		var series = [];
		var machines = [];

		today.setUTCHours(0);
		today.setUTCMinutes(0);
		today.setUTCSeconds(0);
		today.setUTCMilliseconds(0);
		today = today.getTime();

		var data = {
			mon : $("#tgl").val()
		}

		$.get('{{ url("fetch/maintenance/pm/schedule") }}', data, function(result, status, xhr){
			for (var i = 0; i < result.mch_daily.length; i++) {
				var deal = [];

				var unfilled = true;
				for (var j = 0; j < result.daily_data.length; j++) {
					var num = 0;
					if(result.mch_daily[i].machine_group == result.daily_data[j].machine_group){
						unfilled = false;
						var to = new Date(result.daily_data[j].dt);
						to = to.addDays(1);

						for (var z = 0; z < result.daily_summary.length; z++) {
							if (result.daily_data[j].machine_group == result.daily_summary[z].machine_group && result.daily_data[j].dt == result.daily_summary[z].check_date) {
								num = result.daily_summary[z].jml_cek;
							}
						}

						deal.push({
							cek_data: result.daily_data[j].item_code,
							mc_group : result.daily_data[j].machine_group,
							planned : num+' / '+result.mch_daily[i].jml_mesin,
							from : Date.parse(result.daily_data[j].dt),
							to : Date.parse(to)
						});

					}
				}

				if(unfilled){
					deal.push({
						mc_group : 0
					});
				}

				machines.push({
					name: result.mch_daily[i].machine_group,
					current: 0,
					deals: deal,
				});
			}

			series = machines.map(function(value, i) {
				var data = value.deals.map(function(deal) {
					return {
						id: 'deal-' + i,
						mc_group: deal.mc_group,
						cek_data: deal.cek_data,
						planned: deal.planned,
						start: deal.from,
						end: deal.to,
						y: i
					};
				});
				return {
					name: value.name,
					data: data,
					current: value.deals[value.current]
				};
			});

			// console.log(series);

			var date = new Date(result.now);
			var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
			var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

			firstDay.setUTCHours(0);
			firstDay.setUTCMinutes(0);
			firstDay.setUTCSeconds(0);
			firstDay.setUTCMilliseconds(0);
			firstDay = firstDay.getTime();

			lastDay.setUTCHours(0);
			lastDay.setUTCMinutes(0);
			lastDay.setUTCSeconds(0);
			lastDay.setUTCMilliseconds(0);
			lastDay = lastDay.getTime();


			Highcharts.ganttChart('daily_chart', {
				series: series,
				title: {
					text: 'Daily Planned Monitoring on '+result.mon
				},
				tooltip: {
					pointFormat: '<span>{point.mc_group}</span>'
				},
				xAxis: {
					type: 'datetime',
					tickInterval: day,
					labels: {
						format: '{value:%d}'
					},
					min: firstDay + 1 * day,
					max: lastDay + 2 * day,
					currentDateIndicator: {
						label: {
							format: '%d-%M-%Y',
							
						}
					}
				},
				yAxis: {
					type: 'category',
					grid: {
						columns: [{
							title: {
								text: 'Machine'
							},
							categories: map(series, function (s) {
								return s.name;
							})
						}]
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							enabled: true,
							format: '{point.planned}',
							style: {
								cursor: 'default',
								pointerEvents: 'none'
							}
						},
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									openModalDetail(this.mc_group, this.start);
								}
							}
						},
					}
				},
			});


		})
	}

	function openModalDetail(group_machine, date) {
		var date = date;
		var date_new = (new Date(date)).toISOString().split('T')[0];

		$("#judul_modal").html('Check Detail <b>'+group_machine+'</b> on '+date_new);

		$("#modalDetail").modal('show');

		var data = {
			date : date_new,
			group_machine : group_machine
		}

		$.get('{{ url("fetch/maintenance/pm/schedule/detail") }}', data, function(result, status, xhr){
			var body = '';

			$.each(result.data_details, function(key, value) {
				body += '<tr>';
				body += '<td>'+value.machine_group+'</td>';
				body += '<td>'+value.machine_group+'</td>';
				body += '</tr>';
			});


			$("#detailBody").append(body);
		})

	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
	});

	Date.prototype.addDays = function(days) {
		var date = new Date(this.valueOf());
		date.setDate(date.getDate() + days);
		return date;
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