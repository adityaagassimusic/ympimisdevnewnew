@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		border:2px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:2px solid black;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:2px solid black;
	}
	#loading, #error { display: none; }
	.bar {
		height:100px;
		display:inline-block;
		float:left;
		border: 1px solid black;
	}
	.text-rotasi {
		-ms-transform: rotate(-90deg); /* IE 9 */
		-webkit-transform: rotate(-90deg); /* Safari 3-8 */
		transform: rotate(-90deg);
		white-space: nowrap;
		font-size: 12px;
		vertical-align: middle;
		line-height: 100px;
	}
	#mc_head2 > th{
		padding: 0px;
		border-top: 0px;
		border-left: 1px solid black;
		border-right: 1px solid black;
		width: 10px;
		font-size: 1vw;
	}
	#mc_head > th{
		padding: 0px;
		border-bottom: 0px;
	}

	.containers {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #7e5686;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}
</style>
@endsection

@section('content')
<section class="content" style="overflow-y:hidden; overflow-x:scroll; padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="container" id="container" style="width: 100%"></div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Injection Schedule Adjustment</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id_schedule" id="id_schedule">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Mesin<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Machine" name="machine" id="machine" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Start Date<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control datepicker" id="start_date" placeholder="Start Date" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Start Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control timepicker" id="start_time" placeholder="Start Time" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="quantity_awal" placeholder="Quantity" required readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="quantity_adj" placeholder="Quantity" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Buat Schedule Baru<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<label class="containers">Buat Schedule Baru
										  <input type="radio" checked="true" name="new_schedule" value="Buat Schedule Baru">
										  <span class="checkmark"></span>
										</label>
										<label class="containers">Tidak Buat Schedule Baru
										  <input type="radio" checked="true" name="new_schedule" value="Tidak Buat Schedule Baru">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Reason<span class="text-red">*</span></label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="reason" placeholder="Reason" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="adjustSchedule()"><i class="fa fa-pencil-square-o"></i> Adjust Schedule</button>
				</div>
			</div>
		</div>
	</div>
</section>

</div>

@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		drawTable();
		$('.select2').select2({
			dropdownParent: $('#edit_modal')
		});
		setInterval(drawTable, 50000);
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.timepicker').timepicker({
			use24hours: true,
			showInputs: false,
			showMeridian: false,
			minuteStep: 1,
			defaultTime: '00:00:00',
			format: 'hh:mm:ss'
		})
		// $('.timepicker').timepicker({ 
		// 	timeFormat: 'HH:mm:ss',
		// 	showMeridian: false,
		// });
	});

	

	function drawTable() {
		$.get('{{ url("fetch/injection_schedule") }}',  function(result, status, xhr){
			if (result.status) {
				var today = new Date();
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var mesin = [];
				var series = [];
				var schedules = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();

				for (var i = 0; i < result.mesin.length; i++) {
					var deal = [];
					// var colors_skeleton = [];
					var unfilled = true;
						for(var j = 0; j < result.schedule.length;j++){
							if (result.schedule[j].machine == result.mesin[i]) {
								var colors_skeleton = "";
								if (result.schedule[j].type == 'molding') {
									unfilled = false;
									deal.push({
										id_schedule : result.schedule[j].id,
										machine : result.schedule[j].machine,
										materials : "",
										material : result.schedule[j].material_number+' - '+result.schedule[j].material_description,
										part : result.schedule[j].part+' - '+result.schedule[j].color,
										qty : result.schedule[j].qty,
										start_time : Date.parse(result.schedule[j].start_time),
										end_time : Date.parse(result.schedule[j].end_time),
										colors : '#8729c2'
									});
								}else{
									unfilled = false;
									if (result.schedule[j].color == 'BLUE') {
										var colors_skeleton = '#708aff';
									}else if(result.schedule[j].color == 'PINK'){
										var colors_skeleton = '#ff70e5';
									}else if(result.schedule[j].color == 'GREEN'){
										var colors_skeleton = '#afff8f';
									}else if(result.schedule[j].color == 'RED'){
										var colors_skeleton = '#ff8f8f';
									}else if(result.schedule[j].color == 'IVORY'){
										var colors_skeleton = '#fff5a6';
									}else if(result.schedule[j].color == 'BROWN'){
										var colors_skeleton = '#8a7063';
									}else if(result.schedule[j].color == 'BEIGE'){
										var colors_skeleton = '#dba286';
									}else{
										var colors_skeleton = '#000';
									}
									deal.push({
										id_schedule : result.schedule[j].id,
										machine : result.schedule[j].machine,
										materials : result.schedule[j].material_description.split(' ')[0]+' '+result.schedule[j].material_description.split(' ')[1],
										material : result.schedule[j].material_number+' - '+result.schedule[j].material_description,
										part : result.schedule[j].part+' - '+result.schedule[j].color,
										qty : result.schedule[j].qty,
										start_time : Date.parse(result.schedule[j].start_time),
										end_time : Date.parse(result.schedule[j].end_time),
										colors : colors_skeleton
									});
								}
							}
						}
						if (unfilled) {
							deal.push({
								id_schedule :0,
								machine : result.mesin[i],
								material : "",
								part : "",
								qty : 0,
								start_time : 0,
								end_time : 0,
								colors : ""
							});
						}


					schedules.push(
						{name: result.mesin[i],
						current: 0,
						deals: deal}
					);
				}

				series = schedules.map(function (car, i) {
				    var data = car.deals.map(function (deal) {
				        return {
				            id: 'deal-' + i,
				            id_schedule: deal.id_schedule,
				            machine: deal.machine,
				            material: deal.material,
				            materials: deal.materials,
				            part: deal.part,
				            qty: deal.qty,
				            start: deal.start_time,
				            end: deal.end_time,
				            color: deal.colors,
				            y: i
				        };
				    });
				    return {
				        name: car.name,
				        data: data,
				        current: car.deals[car.current]
				    };
				});

				var chart = Highcharts.ganttChart('container', {
				    series: series,
					chart: {
						backgroundColor: null
					},
					title: {
						text: null,
					},
					tooltip: {
						pointFormat: '<span>Mesin: <b>{point.machine}</b></span><br/><span>Material:<b> {point.material}</b></span><br/><span>Part: <b>{point.part}</b></span><br/><span>From: <b>{point.start:%e %b %Y, %H:%M}</b></span><br/><span>To: <b>{point.end:%e %b %Y, %H:%M}</b></span><br/><span>Qty: <b>{point.qty}</b></span>'
					},
					xAxis:
					[{
						tickInterval: 1000 * 60 * 60,
						min: today,
						max: today + 7 * day,
						currentDateIndicator:{
							enabled: true,
							width: 3,
				            dashStyle: 'dot',
				            color: 'red',
							label: {
								style: {
									fontSize: '14px',
									color: '#fff',
									fontWeight: 'bold'
								},
								x: -90,
								y: -4,
							},
						},
						scrollbar: {
							enabled: true,
							barBackgroundColor: 'gray',
							barBorderRadius: 7,
							barBorderWidth: 0,
							buttonBackgroundColor: 'gray',
							buttonBorderWidth: 0,
							buttonArrowColor: 'white',
							buttonBorderRadius: 7,
							rifleColor: 'white',
							trackBackgroundColor: '#3C3C3C',
							trackBorderWidth: 1,
							trackBorderColor: 'silver',
							trackBorderRadius: 7
						}
					},{
						tickInterval: 1000 * 60 * 60 * 24
					}],
					yAxis: {
						type: 'category',
						grid: {
							columns: [{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.name;
								})
							}]
						},
					},
					plotOptions: {
						gantt: {
							animation: false,
						},
						series:{
							cursor: 'pointer',
							dataLabels: {
						        enabled: true,
						        format: '{point.materials}',
						        style: {
						          cursor: 'default',
						          pointerEvents: 'none',
						          // fontSize:'13px'
						        }
						    },
						    pointPadding: -0.31,
						    point: {
								events: {
									click: function () {
										editSchedule(this.id_schedule);
									}
								}
							},
							borderWidth: 0
						}
					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					}
				});

				$.each(chart.yAxis[0].ticks, function(i, tick) {
					$('.highcharts-yaxis-labels text').hover(function () {
						$(this).css('fill', '#33c570');
						$(this).css('cursor', 'pointer');
					},
					function () {
						$(this).css('cursor', 'pointer');
						$(this).css('fill', 'white');
					});
				});
			}else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function editSchedule(id_schedule) {
		$('#loading').show();
		var role = '{{$role}}';
		var username = '{{$auth}}';
		if (role == 'MIS' || role == 'EDIN' || role == 'Leader&amp;Sub') {
			var data = {
				id_schedule:id_schedule
			}

			$.get('{{ url("fetch/injection_schedule/adjustment") }}',data,  function(result, status, xhr){
				if (result.status) {
					
					var machine = "";
					$('#machine').html("");
					machine += '<option value=""></option>';
					$.each(result.schedule, function(key, value){
						if (value.machine_1 != 0) {
							machine += '<option value="Mesin '+value.machine_1+'">Mesin '+value.machine_1+'</option>';
						}
						if (value.machine_2 != 0) {
							machine += '<option value="Mesin '+value.machine_2+'">Mesin '+value.machine_2+'</option>';
						}
						if (value.machine_3 != 0) {
							machine += '<option value="Mesin '+value.machine_3+'">Mesin '+value.machine_3+'</option>';
						}
					});
					$('#machine').append(machine);

					$.each(result.schedule, function(key, value){
						$('#id_schedule').val(id_schedule);
						$('#start_date').val(value.start_date).datepicker("setDate", new Date(value.start_date) );
						$('#start_time').val(value.start_times);
						$('#quantity_awal').val(value.qty);
						$('#quantity_adj').val(value.qty);
						$('#machine').val(value.machine).trigger('change.select2');
						$('#reason').val(value.reason);
					});

					$('#edit_modal').modal('show');
					$('#loading').hide();
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}else{
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!','Anda tidak memiliki Otoritas');
			return false;
		}
	}

	function adjustSchedule() {
		$('#loading').show();
		if ($('#reason').val() == '') {
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!', "Masukkan reason");
			return false;
		}else{
			var id_schedule = $('#id_schedule').val();
			var start_date = $('#start_date').val();
			var start_time = $('#start_time').val();
			var machine = $('#machine').val();
			var quantity_awal = $('#quantity_awal').val();
			var quantity_adj = $('#quantity_adj').val();
			var reason = $('#reason').val();
			var new_schedule = '';
			if (quantity_adj < quantity_awal) {
				$("input[name='new_schedule']:checked").each(function (i) {
		            new_schedule = $(this).val();
		        });
		        if (new_schedule == '') {
		        	$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', "Pilih Buat Schedule atau Tidak");
					return false;
		        }
			}

			var data = {
				id_schedule:id_schedule,
				start_date:start_date,
				start_time:start_time,
				machine:machine,
				quantity_awal:quantity_awal,
				quantity_adj:quantity_adj,
				reason:reason,
				new_schedule:new_schedule,
			}

			$.get('{{ url("adjust/injection_schedule/adjustment") }}',data,  function(result, status, xhr){
				if (result.status) {
					$('#edit_modal').modal('hide');
					openSuccessGritter('Success',result.message);
					drawTable();
					$('#loading').hide();
				}else{
					audio_error.play();
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}
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

	Highcharts.setOptions({
		global: {
			useUTC: true,
			timezoneOffset: -420
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

@stop