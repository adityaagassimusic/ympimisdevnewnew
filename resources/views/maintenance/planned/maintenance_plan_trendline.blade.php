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
	#div_title {
		color: white;
		font-size: 24px;
		text-align: center;
		margin-top: 20px;
	}

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

			<div class="col-xs-2">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl" placeholder="Pilih Bulan">
				</div>
			</div>

			<div class="col-xs-3">
				<select class="select2 form-control" style="width: 100%" id="select_group" data-placeholder="Select Group Machine" onchange="selectGroup()">
				</select>
			</div>

			<div class="col-xs-3">
				<select class="select2 form-control" style="width: 100%" id="select_machine" data-placeholder="Select Machine">
				</select>
			</div>

			<div class="col-xs-2">
				<button class="btn btn-success" id="btn_search" onclick="loadData()"><i class="fa fa-search"></i> Search</button>
			</div>
		</div>

		<div class="col-xs-12" id="div_chart">
		</div>

		<div class="col-xs-12">
			<div id="chart2"></div>
		</div>
	</div>
</section>

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
<script src="{{ url("js/dataTables.fixedHeader.min.js") }}"></script>
<script src="{{ url("js/dataTables.responsive.min.js") }}"></script>

<!-- <script src="{{ url("js/highcharts-gantt.js")}}"></script> -->
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var mons = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march']

	var machines = <?php echo json_encode($machine_groups); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$(".select2").select2();
		// console.log(machines);

		var grouped = groupBy(machines, 'machine_group');


		var mc_group = "<option value=''></option>";

		for ( var property in grouped ) {
			mc_group += "<option value='"+property+"'>"+property+"</option>";
		}

		$("#select_group").append(mc_group);

	})

	Date.prototype.addDays = function(days) {
		var date = new Date(this.valueOf());
		date.setDate(date.getDate() + days);
		return date;
	}

	function loadData() {

		var data = {
			mon : $("#tgl").val(),
			machine_group : $("#select_machine").val(),
		}

		$.get('{{ url("fetch/maintenance/pm/trendline") }}', data, function(result, status, xhr){
			var data_detail = result.datas;

			data_detail = groupBy(data_detail, 'cek');

			var master = [];

			var dt_arr = [];

			var low_arr = [];
			var high_arr = [];
			for ( var property in data_detail ) {
				var avaibility = 0;


				$.each(result.cek_datas, function(key, value) {
					var cek_item = value.item_check+" - "+value.substance;
					if (value.machine_name == $("#select_machine").val() && property == cek_item) {
						avaibility = 1;

						low_arr.push(parseFloat(value.lower_limit));
						high_arr.push(parseFloat(value.upper_limit));
						return false;
					}
				})

				if (avaibility == 1) {
					var data_temp = [];

					$.each(result.datas, function(key, value) {

						if (property == (value.item_check+" - "+value.substance)) {
							data_temp.push(parseFloat(value.check_value));

							if(dt_arr.indexOf(value.dt) === -1){
								dt_arr[dt_arr.length] = value.dt;
							}
						}
					})

					master.push({'name' : property, 'datas' : data_temp});
				}
			}

			$("#div_chart").empty();
			$("#div_chart").append("<div id='div_title'>"+$("#select_machine option:selected").text()+"<br> on "+$("#tgl").val()+"</div>");
			$.each(master, function(key, value) {
				$("#div_chart").append("<div id='chart_"+key+"' style='margin-top: 20px'></div>");

				console.log(low_arr[key]);

				Highcharts.chart("chart_"+key, {

					title: {
						text: value.name,
						style: {
							fontSize: '15px'
						}
					},

					yAxis: {
						title: {
							text: 'Value'
						},
						min : low_arr[key]-1,
						max : high_arr[key],
						plotLines: [{
							value: low_arr[key],
							width: 3,
							color: '#ff3030',
							dashStyle: 'ShortDash',
							label: {
								text: 'Min',
								style: {
									color: '#ff3030',
									fontWeight: 'bold'
								}							
							}
						},{
							value: high_arr[key],
							width: 3,
							color: '#ff3030',
							dashStyle: 'ShortDash',
							label: {
								text: 'Max',
								style: {
									color: '#ff3030',
									fontWeight: 'bold'
								}							
							}
						}]
					},

					xAxis: {
						categories: dt_arr,
					},

					legend: {
						layout: 'horizontal',
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							}
						},
						line: {
							width : 2
						}
					},

					series: [{
						name: value.name,
						data: value.datas
					}],

					credits:{
						enabled:false
					},

				});

			})
		})
	}

	function selectGroup() {
		$("#select_machine").empty();

		var select = "<option value=''></option>";
		$.each(machines, function(key, value) {
			if (value.machine_group == $("#select_group").val()) {
				select += "<option value='"+value.machine_name+"'>"+value.machine_name+" - "+value.description+"</option>";
			}
		})

		$("#select_machine").append(select);
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

	var groupBy = function(xs, key) {
		return xs.reduce(function(rv, x) {
			(rv[x[key]] = rv[x[key]] || []).push(x);
			return rv;
		}, {});
	};


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