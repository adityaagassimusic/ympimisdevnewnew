@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('AssemblyProcessController@indexAssemblyResume') }}">
					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>
					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<select style="width: 100%;" data-placeholder="Pilih Lokasi" class="form-control select2" id="location" name="location">
							<option value=""></option>
						</select>
					</div>
					<input type="hidden" class="form-control" id="origin_group" name="origin_group" value="{{$_GET['origin_group']}}">
					<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px;">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-10" style="margin-top: 5px;padding-left: 0px;padding-right: 5px;" id="tableResume">
				<div id="container" class="container" style="width: 100%;height: 85vh"></div>
			</div>
			<div class="col-xs-2" style="padding-right: 0;padding-left:5px;padding-top: 10px;">
		      <div class="small-box" style="background: #ffc400; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('kensa_process')">
		        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
		          <h3 style="margin-bottom: 0px;font-size: 1.2vw;"><b>KENSA PROCESS</b></h3>
		          <!-- <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b></b></h3> -->
		          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="kensa_process">0</h5>
		          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="pres">0 %</h4> -->
		        </div>
		        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
		          <i class="fa fa-list"></i>
		        </div>
		      </div>

		      <div class="small-box" style="background: #42a5f5; height: 20vh; margin-bottom: 5px;cursor: pointer;"  onclick="ShowModalAll('qa_alto')" id="divAltoAll">
		        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
		          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>QA ALTO</b></h3>
		          <!-- <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査の頻度</b></h3> -->
		          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="qa_alto">0</h5>
		          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
		        </div>
		        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
		          <i class="fa fa-list"></i>
		        </div>
		      </div>
		      <div class="small-box" style="background: #cc0000; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('qa_tenor')">
		        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
		          <h3 style="margin-bottom: 0px;font-size: 1.5vw;color: white" id="title_tenor_all"><b>QA TENOR</b></h3>
		          <!-- <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;text-shadow: 1px 1px 9px #fff;"><b>監査未実施み</b></h3> -->
		          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;color: white" id="qa_tenor">0</h5>
		          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;" id="persen_belum_vaksin">0 %</h4> -->
		        </div>
		        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
		          <i class="fa fa-list"></i>
		        </div>
		      </div>
		      <div class="small-box" style="background: #009945; height: 20vh; margin-bottom: 5px;cursor: pointer;" onclick="ShowModalAll('packing')">
		        <div class="inner" style="padding-bottom: 0px;padding-top: 3vh;">
		          <h3 style="margin-bottom: 0px;font-size: 1.5vw;"><b>PRINT LABEL</b></h3>
		          <!-- <h3 style="margin-bottom: 0px;font-size: 1.3vw;color: #0d47a1;"><b>監査実施済</b></h3> -->
		          <h5 style="font-size: 3vw; font-weight: bold;margin-bottom: 0px;margin-top: 0px;" id="packing">0</h5>
		          <!-- <h4 style="font-size: 2.5vw; font-weight: bold;padding-top: 0px;margin-top: 0px;color: red;" id="persen_belum_vaksin">0 %</h4> -->
		        </div>
		        <div class="icon" style="padding-top: 12vh;font-size:8vh;">
		          <i class="fa fa-list"></i>
		        </div>
		      </div>
		    </div>
		</div>
	</div>

	<div class="modal fade" id="modalResume">
		<div class="modal-dialog modal-sm" style="width: 800px">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body">
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;background-color: orange;text-align: center;">
							<span style="padding: 15px;font-weight: bold;font-size: 25px;" id="judulResume"></span>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 5px;" class="col-xs-6">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 1px solid black">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #91caff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black;" id="judul_alto">ALTO</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeAlto">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_alto"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 0px;" class="col-xs-6" id="divTenor">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #ffb3ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black">TENOR</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeTenor">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_tenor"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;text-align: center;">
							<button class="btn btn-danger pull-right" onclick="$('#modalResume').modal('hide')"><b><i class="fa fa-close"></i> CLOSE</b></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalResumeAll">
		<div class="modal-dialog modal-sm" style="width: 800px">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body">
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;background-color: orange;text-align: center;">
							<span style="padding: 15px;font-weight: bold;font-size: 25px;" id="judulResumeAll"></span>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 5px;display: none;" class="col-xs-6" id="divAltoFungsi">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 1px solid black">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #91caff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black;" id="judul_alto_all">ALTO</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeAltoAll">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_alto_all"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 0px;display: none;" class="col-xs-6" id="divTenorFungsi">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #ffb3ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black" id="judul_tenor_all">TENOR</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeTenorAll">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_tenor_all"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 5px;display: none" class="col-xs-6" id="divAltoVisual">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 1px solid black">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #91caff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black;">ALTO VISUAL</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeAltoVisual">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_alto_visual"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div style="padding-top: 5px;padding-left: 0px;padding-right: 0px;display: none;" class="col-xs-6" id="divTenorVisual">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="background-color: #ffb3ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;border: 1px solid black">TENOR VISUAL</th>
									</tr>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Model</th>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Qty</th>
									</tr>
								</thead>
								<tbody id="bodyResumeTenorVisual">
								</tbody>
								<tfoot>
									<tr>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;">Total</th>
										<th style="width:15%; background-color: #8aff8e; text-align: center; color: black; padding:0;font-size: 20px;border: 1px solid black;" id="total_tenor_visual"></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;text-align: center;">
							<button class="btn btn-danger pull-right" onclick="$('#modalResumeAll').modal('hide')"><b><i class="fa fa-close"></i> CLOSE</b></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2({
			allowClear:true
		});
		fillChart();
		setInterval(fillChart, 300000);
		$("#divTenorVisual").hide();
		$("#divAltoVisual").hide();
		$("#divAltoFungsi").hide();
		$("#divTenorFungsi").hide();

	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
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

	var resumes = null;

	function fillChart() {
		// $('#loading').show();
		// var tanggal_from = $('#tanggal_from').val();
		// var tanggal_to = $('#tanggal_to').val();

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			tanggal_from:"{{$_GET['date_from']}}",
			tanggal_to:"{{$_GET['date_to']}}",
			origin_group_code:"{{$_GET['origin_group']}}",
			location:"{{$_GET['location']}}"
		}

		$.get('{{ url("fetch/assembly/resume") }}', data, function(result, status, xhr) {
			if(result.status){

				var categories = [];
				var series = [];
				var data = [];

				var operator = [];

				var kensa_process = 0;
				var qa_alto = 0;
				var qa_tenor = 0;
				var packing = 0;

				if ("{{$_GET['origin_group']}}" == '043') {
					var titles = 'SAXOPHONE';
					$('#divAltoAll').show();
					$('#title_tenor_all').html('QA TENOR');
				}else if("{{$_GET['origin_group']}}" == '042'){
					var titles = 'CLARINET';
					$('#divAltoAll').hide();
					$('#title_tenor_all').html('QA KENSA');
				}else if("{{$_GET['origin_group']}}" == '041'){
					var titles = 'FLUTE';
				}

				resumes = null;
				resumes = result.resume;

				for(var i = 0; i < result.resume.length;i++){
					operator.push(result.resume[i].operator_id);
				}

				var op_unik = operator.filter(onlyUnique);

				for(var j = 0; j < op_unik.length;j++){
					var name = '';
					var perolehan = 0;
					for(var i = 0; i < result.resume.length;i++){
						if (result.resume[i].operator_id == op_unik[j]) {
							name = result.resume[i].name;
							perolehan = perolehan + parseInt(result.resume[i].qty);
							if (result.resume[i].location.match(/kensa-process/gi)) {
								kensa_process = kensa_process + parseInt(result.resume[i].qty);
							}else if (result.resume[i].location.match(/qa-kensa/gi)) {
								qa_tenor = qa_tenor + parseInt(result.resume[i].qty);
							}else if (result.resume[i].location.match(/qa-fungsi/gi)) {
								qa_alto = qa_alto + parseInt(result.resume[i].qty);
							}else if (result.resume[i].location.match(/qa-visual/gi)) {
								qa_alto = qa_alto + parseInt(result.resume[i].qty);
							}else if (result.resume[i].location.match(/packing/gi)) {
								packing = packing + parseInt(result.resume[i].qty);
							}
						}
					}
					// series.push({y:perolehan,key:op_unik[j],xAxis:op_unik[j]+'-'+name.replace(/(.{13})..+/, "$1&hellip;")});
					var xAxis = name.replace(/(.{13})..+/, "$1&hellip;");
					data.push([xAxis, perolehan, op_unik[j]]);
					// categories.push(op_unik[j]+'-'+name.replace(/(.{13})..+/, "$1&hellip;"));
				}

				data.sort(function(a, b) {
                    return b[1] - a[1]
                });

                for(var i = 0; i < data.length;i++){
                	series.push({y:data[i][1],key:data[i][2]});
					categories.push(data[i][0]);
                }

				$('#kensa_process').html(kensa_process);
				$('#qa_alto').html(qa_alto);
				$('#qa_tenor').html(qa_tenor);
				$('#packing').html(packing);

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						// height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'PRODUCTION RESULT',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.monthTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: categories,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '14px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty Perolehan Pc(s)',
							style: {
								color: '#eee',
								fontSize: '13px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						
					}
					],
					// tooltip: {
					// 	headerFormat: '<span>Perolehan</span><br/>',
					// 	pointFormat: '<span style="font-weight: bold;">{this.category} : <b>{point.y}</b></span><br/>',
					// },
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'16px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,this.options.key);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					{
						type: 'column',
						data: series,
						name: 'Total Perolehan',
						colorByPoint: false,
						animation: false,
						color:'#3f51b5',
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
						dataSorting: {
					        enabled: true,
					    },
					}
					]
				});

				$('#tanggal_from').val("{{$_GET['date_from']}}");
				$('#tanggal_to').val("{{$_GET['date_to']}}");

				$('#location').html('');
				var loc = '';

				loc += '<option value=""></option>';
				for(var i = 0; i < result.location.length;i++){
					if (result.location[i].match(/Line/gi)) {
						loc += '<option value="'+result.location[i].split(' ')[1]+'">'+result.location[i]+'</option>';
					}else{
						loc += '<option value="'+result.location[i]+'">'+result.location[i]+'</option>';
					}
				}
				$('#location').append(loc);

				$('#location').val("{{$_GET['location']}}").trigger('change');

				$('#loading').hide();

				
			}

		});
	}

	function ShowModal(employee,employee_id) {
		$('#bodyResumeAlto').html('');
		$('#bodyResumeTenor').html('');
		$('#total_alto').html('');
		$('#total_tenor').html('');

		if ("{{$_GET['origin_group']}}" == '042') {
			$('#divTenor').hide();
			$('#judul_alto').html('CLARINET');
		}else if ("{{$_GET['origin_group']}}" == '041') {
			$('#divTenor').hide();
			$('#judul_alto').html('FLUTE');
		}else{
			$('#divTenor').show();
			$('#judul_alto').html('ALTO');
		}

		var alto = '';
		var tenor = '';
		var total_alto = 0;
		var total_tenor = 0;
		var name = '';
		for(var i = 0; i < resumes.length;i++){
			if (resumes[i].operator_id == employee_id) {
				name = resumes[i].name;
				if (resumes[i].model.match(/YAS/gi)) {
					alto += '<tr>';
					alto += '<td style="color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].model+'</td>';
					alto += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].qty+'</td>';
					alto += '</tr>';
					total_alto = total_alto + parseInt(resumes[i].qty);
				}else if (resumes[i].model.match(/YTS/gi)) {
					tenor += '<tr>';
					tenor += '<td style="color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].model+'</td>';
					tenor += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].qty+'</td>';
					tenor += '</tr>';
					total_tenor = total_tenor + parseInt(resumes[i].qty);
				}else if (resumes[i].model.match(/YCL/gi)) {
					alto += '<tr>';
					alto += '<td style="color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].model+'</td>';
					alto += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border:1px solid black;">'+resumes[i].qty+'</td>';
					alto += '</tr>';
					total_alto = total_alto + parseInt(resumes[i].qty);
				}
			}
		}
		$('#bodyResumeAlto').append(alto);
		$('#bodyResumeTenor').append(tenor);
		$('#total_alto').html(total_alto);
		$('#total_tenor').html(total_tenor);
		$('#judulResume').html('PRODUCTION RESULT '+employee_id+' - '+name);
		$('#modalResume').modal('show');
	}

	function ShowModalAll(location) {
		$('#bodyResumeAltoAll').html('');
		$('#bodyResumeTenorAll').html('');
		$('#bodyResumeAltoVisual').html('');
		$('#bodyResumeTenorVisual').html('');
		$('#total_alto_all').html('');
		$('#total_tenor_all').html('');
		$('#total_alto_visual').html('');
		$('#total_tenor_visual').html('');

		$('#divAltoVisual').hide();
		$('#divTenorVisual').hide();
		$('#divAltoFungsi').hide();
		$('#divTenorFungsi').hide();

		$('#judul_alto_all').html('ALTO');
		$('#judul_tenor_all').html('TENOR');

		if ("{{$_GET['origin_group']}}" == '042') {
			$('#judul_alto_all').html('CLARINET');
			$('#divAltoFungsi').show();
		}else if ("{{$_GET['origin_group']}}" == '041') {
			$('#judul_alto_all').html('FLUTE');
			$('#divAltoFungsi').show();
		}else{
			if (location == 'qa_alto') {
				$('#divAltoVisual').show();
				$('#divTenorVisual').hide();
				$('#divAltoFungsi').show();
				$('#divTenorFungsi').hide();
				$('#judul_alto_all').html('ALTO FUNGSI');
				$('#judul_tenor_all').html('TENOR FUNGSI');
			}else if (location == 'qa_tenor') {
				$('#divAltoVisual').hide();
				$('#divTenorVisual').hide();
				$('#divAltoFungsi').hide();
				$('#divTenorFungsi').show();
				$('#judul_tenor_all').html('TENOR');
			}else{
				$('#divAltoFungsi').show();
				$('#divTenorFungsi').show();
			}
		}

		var alto_all = '';
		var tenor_all = '';
		var total_alto_all = 0;
		var total_tenor_all = 0;

		var alto_visual = '';
		var tenor_visual = '';
		var total_alto_visual = 0;
		var total_tenor_visual = 0;

		if ("{{$_GET['origin_group']}}" == '043') {
			if (location == 'packing') {
				var locations = 'PRINT LABEL';
			}else{
				var locations = location;
			}
		}else{
			if (location == 'packing') {
				var locations = 'PRINT LABEL';
			}else if (location == 'qa_tenor'){
				var locations = 'QA KENSA';
			}else{
				var locations = location;
			}
		}
		if (location == 'kensa_process') {
			for(var i = 0; i < resumes.length;i++){
				if (resumes[i].location.match(/kensa-process/gi)) {
					if (resumes[i].model.match(/YAS/gi)) {
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}else if (resumes[i].model.match(/YTS/gi)){
						tenor_all += '<tr>';
						tenor_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						tenor_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						tenor_all += '</tr>';
						total_tenor_all = total_tenor_all + parseInt(resumes[i].qty);
					}else if (resumes[i].model.match(/YCL/gi)){
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}
				}
			}
		}else if (location == 'qa_tenor') {
			for(var i = 0; i < resumes.length;i++){
				if (resumes[i].location.match(/qa-kensa/gi)) {
					if (resumes[i].model.match(/YAS/gi)) {
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}else if (resumes[i].model.match(/YTS/gi)){
						tenor_all += '<tr>';
						tenor_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						tenor_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						tenor_all += '</tr>';
						total_tenor_all = total_tenor_all + parseInt(resumes[i].qty);
					}else if (resumes[i].model.match(/YCL/gi)){
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}
				}
			}
		}else if (location == 'packing') {
			for(var i = 0; i < resumes.length;i++){
				if (resumes[i].location.match(/packing/gi)) {
					if (resumes[i].model.match(/YAS/gi)) {
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}else if(resumes[i].model.match(/YTS/gi)){
						tenor_all += '<tr>';
						tenor_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						tenor_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						tenor_all += '</tr>';
						total_tenor_all = total_tenor_all + parseInt(resumes[i].qty);
					}else if(resumes[i].model.match(/YCL/gi)){
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}
				}
			}
		}else if (location == 'qa_alto') {
			for(var i = 0; i < resumes.length;i++){
				if (resumes[i].location == 'qa-fungsi') {
					if (resumes[i].model.match(/YAS/gi)) {
						alto_all += '<tr>';
						alto_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_all += '</tr>';
						total_alto_all = total_alto_all + parseInt(resumes[i].qty);
					}else{
						tenor_all += '<tr>';
						tenor_all += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						tenor_all += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						tenor_all += '</tr>';
						total_tenor_all = total_tenor_all + parseInt(resumes[i].qty);
					}
				}
				if (resumes[i].location == 'qa-visual') {
					if (resumes[i].model.match(/YAS/gi)) {
						alto_visual += '<tr>';
						alto_visual += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						alto_visual += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						alto_visual += '</tr>';
						total_alto_visual = total_alto_visual + parseInt(resumes[i].qty);
					}else{
						tenor_visual += '<tr>';
						tenor_visual += '<td style="color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].model+'</td>';
						tenor_visual += '<td style="text-align:right;padding-right:7px;color:black;background-color:#fff0ba;border: 1px solid black;">'+resumes[i].qty+'</td>';
						tenor_visual += '</tr>';
						total_tenor_visual = total_tenor_visual + parseInt(resumes[i].qty);
					}
				}
			}
		}
		$('#bodyResumeAltoAll').append(alto_all);
		$('#bodyResumeTenorAll').append(tenor_all);
		$('#total_alto_all').html(total_alto_all);
		$('#total_tenor_all').html(total_tenor_all);

		$('#bodyResumeAltoVisual').append(alto_visual);
		$('#bodyResumeTenorVisual').append(tenor_visual);
		$('#total_alto_visual').html(total_alto_visual);
		$('#total_tenor_visual').html(total_tenor_visual);

		$('#judulResumeAll').html('PRODUCTION RESULT '+locations.replace('_',' ').toUpperCase());
		$('#modalResumeAll').modal('show');
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}



</script>
@endsection