@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ url("js/jsQR.js")}}"></script>
<style type="text/css">
	canvas{
		text-align: center;
	}
	.morecontent span {
		display: none;
	}
	.morelink {
		display: block;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
	.std {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(255,116,116);
		display: inline-block;
	}
	.act {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(144,238,126);
		display: inline-block;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" onclick="getData()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>	

			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
				<div id="container1" style="width: 100%;"></div>
			</div>

		</div>
	</div>

	<!-- start modal detail  -->
	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Efficiency Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						{{-- <h5 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Resume</b></h5> --}}

						<div class="col-md-12" style="margin-bottom: 20px;">
							<div class="col-md-4">
								<h5 class="modal-title">Operator Efficiency</h5>
								<h5 class="modal-title" id="op_eff"></h5>
							</div>
						</div>

						<div class="col-md-12">
							<table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="8" style="text-align: center;">WELDING RESULT</th>
									</tr>
									<tr>
										<th>Part Type</th>
										<th>Model</th>
										<th>Key</th>
										<th style="width: 13%">Start</th>
										<th style="width: 13%">Finsih</th>
										<th style="width: 10%">Standart time</th>
										<th style="width: 10%">Actual time</th>
										<th style="width: 10%">Material Qty</th>
									</tr>
								</thead>
								<tbody id="data-log-body">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- end modal -->


</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 20000);
	});

	function getData() {
		$("#loading").show();
		
		location.reload(); 
	}


	function fillChart() {
		var tanggal = $("#tanggal").val();

		var position = $(document).scrollTop();

		var data = {
			tanggal: tanggal,
			origin_group: '{{ Request::segment(4) }}'
		}

		$.get('{{ url("fetch/assembly/eff") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

				var series = [];
				var op_name = [];

				$.each(result.datas, function(key, value) {
					op_name.push(value.name);

					if (parseFloat(value.eff) < 150) {
						series.push(parseFloat(value.eff));
					}
					// sr = parseFloat((parseInt(value.actual) / value.std).toFixed(2));
				})

				var chart = Highcharts.chart('container1', {
					chart: {
						animation: false,
						height: 500,
					},
					title: {
						text: 'Operators Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							enabled: true,
							text: "Operator Efficiency (%)"
						},
						min: 0,
						labels: {
							enabled: false
						}
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							// rotation: -45,
							style: {
								fontSize: '13px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								// rotation: -90,
								style:{
									fontSize: '15px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(result.date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'OP Efficiency',
						type: 'column',
						data: series,
						showInLegend: false
					}]

				});

			}
		});
	}

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


	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#data-log-body').append().empty();
		$('#op_eff').append().empty();
		$('#judul').append().empty();



		$.get('{{ url("fetch/welding/op_ng_detail") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#judul').append('<b>'+result.nik+' - '+result.nama+' on '+tgl+'</b>');

				//Data Log
				var total_perolehan = 0;
				var total_std = 0;
				var total_act = 0;

				var body = '';
				for (var i = 0; i < result.data_log.length; i++) {
					body += '<tr>';
					body += '<td>'+result.data_log[i].part_type+'</td>';
					body += '<td>'+result.data_log[i].model+'</td>';
					body += '<td>'+result.data_log[i].key+'</td>';
					body += '<td>'+result.data_log[i].start+'</td>';
					body += '<td>'+result.data_log[i].finish+'</td>';
					body += '<td>'+result.data_log[i].std+'</td>';
					body += '<td>'+result.data_log[i].act+'</td>';
					body += '<td>'+result.data_log[i].perolehan_jumlah+'</td>';
					body += '</tr>';
					total_perolehan += parseInt(result.data_log[i].perolehan_jumlah);
					total_std += parseFloat(result.data_log[i].std);
					total_act += parseFloat(result.data_log[i].act);
				}
				body += '<tr>';
				body += '<td colspan="5" style="text-align: center;">Total</td>';
				body += '<td>'+total_std.toFixed(2)+'</td>';
				body += '<td>'+total_act.toFixed(2)+'</td>';
				body += '<td>'+total_perolehan+'</td>';
				body += '</tr>';
				$('#data-log-body').append(body);


				//Resume
				var op_eff = 100 * (total_std / total_act);
				var text_op_eff = '= <sup>Total Standart time</sup>/<sub>Total Actual time</sub> x Posh Rate';
				text_op_eff += '<br>= <sup>'+ total_std.toFixed(2) +'</sup>/<sub>'+ total_act.toFixed(2) +'</sub>';
				text_op_eff += '<br>= <b>'+ op_eff.toFixed(2) +'%</b>';
				$('#op_eff').append(text_op_eff);

			}

		});
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: null,
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