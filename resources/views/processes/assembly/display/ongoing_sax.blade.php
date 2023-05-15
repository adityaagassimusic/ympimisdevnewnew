@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
	input {
		line-height: 22px;
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
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-12" style="background-color: #735CDD">
		            <center>
		            	<span style="color: white; font-size: 2vw; font-weight: bold;">All Line</span>
		            </center>
				</div> -->
				<div class="col-xs-10" style="padding-left: 0px;padding-right: 5px;">
					<div id="container1" class="container1" style="width: 100%;height: 45vh;"></div>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 0px;height: 45vh;vertical-align: middle;display: table-cell;float: inherit;">
					<table style="width: 100%;height: 20vh">
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: rgb(255, 196, 79);height: 1.5vw" class="col-xs-2"></div>
								<div class="col-xs-10">
									Elapsed Real Time
								</div>
							</td>
						</tr>
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: rgb(60, 214, 75);height: 1.5vw" class="col-xs-2"></div>
								<div class="col-xs-10">
									Prev Elapsed Time
								</div>
							</td>
						</tr>
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: #0048ff;height: 0.4vw;margin-top: 10px;" class="col-xs-2"></div>
								<div class="col-xs-10">
									Tact Time : <span id="tact_time"></span> Minutes
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-xs-10" style="padding-left: 0px;padding-right: 5px;">
					<div id="container2" class="container2" style="width: 100%;height: 45vh;"></div>
				</div>
				<div class="col-xs-2" style="padding-left: 0px;padding-right: 0px;height: 45vh;vertical-align: middle;display: table-cell;float: inherit;">
					<table style="width: 100%;height: 20vh">
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: rgb(60, 214, 75);height: 1.5vw" class="col-xs-2"></div>
								<div class="col-xs-10">
									According to Tact Time
								</div>
							</td>
						</tr>
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: rgb(235, 82, 82);height: 1.5vw" class="col-xs-2"></div>
								<div class="col-xs-10">
									Exceeding Tact Time
								</div>
							</td>
						</tr>
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: rgb(61, 97, 227);height: 1.5vw" class="col-xs-2"></div>
								<div class="col-xs-10">
									Exceeding Tact Time and Trained
								</div>
							</td>
						</tr>
						<tr>
							<td style="color: white;font-weight: bold;text-align: left;">
								<div style="background-color: #0048ff;height: 0.4vw;margin-top: 10px;" class="col-xs-2"></div>
								<div class="col-xs-10">
									Tact Time : <span id="tact_time_average"></span> Minutes
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="background-color: orange">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Training Operator</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-xs-4" style="border: 1px solid black;padding-left: 10px;padding-right: 0px">
								<label style="font-weight: bold;font-size: 16px;">Employee ID : </label> 
								<span id="employee_id" style="font-weight: bold;font-size: 16px;"></span>
							</div>
							<div class="col-xs-8" style="border: 1px solid black;padding-left: 10px;padding-right: 0px">
								<label style="font-weight: bold;font-size: 16px;">Name : </label> 
								<span id="name" style="font-weight: bold;font-size: 16px;"></span>
							</div>
						</div>
						<div class="col-md-12" id="modal_hasil" style="margin-top: 20px;">
							<div id="hasil_training">
								<h5 style="background-color:green;color:white"><center style="padding: 5px;"><b style="font-size: 16px;">Hasil Training</b></center></h5>
								<label class="col-sm-12 control-label" style="padding:0;font-size: 18px;">Detail Penyebab Permasalahan</label>
								
								<div class="col-md-12" style="padding:0">
									<span id="detail_penyebab"></span>
								</div>
								
								<label for="detail_aksi" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;font-size: 18px;">Aksi yang dilakukan</label>

								<div class="col-md-12" style="padding: 0;">
									<span id="detail_aksi"></span>
								</div>

								<label for="detail_evidence" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;font-size: 18px;">Bukti Training</label>

								<div class="col-md-12" style="padding:0">
									<span id="detail_evidence"></span>
								</div>
							</div>
						</div>

						<div class="col-md-12" id="modal_training" style="margin-top: 20px;">
							<label for="penyebab" class="col-sm-12 control-label" style="padding:0">Detail Penyebab Permasalahan<span class="text-red">*</span></label>
							<!-- <input class="form-control" name="operator_name" id="operator_name" type="hidden" > -->

							<div class="col-sm-12" style="padding:0">
								<textarea class="form-control" name="penyebab" id="penyebab" data-placeholder="Deskripsi Permasalahan" style="width: 100%;"></textarea>
							</div>

							<label for="aksi" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;">Aksi yang dilakukan<span class="text-red">*</span></label>
							<div class="col-sm-12" style="padding:0">
								<textarea class="form-control" name="aksi" id="aksi" data-placeholder="Deskripsi Permasalahan" style="width: 100%;"></textarea>
							</div>

							<label for="evidence" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;">Bukti Training<span class="text-red">*</span></label>
							<div class="col-sm-12" style="padding:0">
								<input type="file" class="form-control" name="evidence" id="evidence">
							</div>

							<div class="col-md-12 " style="margin-top:30px;padding-left: 0px;padding-right: 0px;">
								<a class="btn btn-success pull-right" onclick="saveTraining()" style="width: 100%; font-weight: bold; font-size: 1.2vw;">SIMPAN</a>
							</div>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		training = null;
		CKEDITOR.replace('penyebab' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
	        height: '200px'
	    });
	    CKEDITOR.replace('aksi' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
	        height: '200px'
	    });
		$('#tanggal_from').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('#tanggal_to').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		setInterval(fetchChart, 10000);
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

	var training = null;

	function fetchChart(){
		// $('#loading').show();

		var data = {
			origin_group_code:'{{$origin_group_code}}',
			line:'{{$line}}',
		}

		$.get('{{ url("fetch/assembly/ongoing") }}', data, function(result, status, xhr) {
			if(result.status){

				var categories = [];
				var tact_times = [];
				var std = 420/50;
				if ('{{$line}}' == 5) {
					std = 420/32;
				}
				var target = [];

				$('#tact_time').html(std);

				var operator = [];

				training = result.training;

				var op_training = [];

				if (result.training.length > 0) {
					$.each(result.training, function(key, value){
						op_training.push(value.operator.split('-')[0]);
					});
				}

				var cycles = 1;

				$.each(result.tact_time, function(key, value){
					operator.push(value.operator_id);
					categories.push(value.name);
					var tact = parseFloat(0);
					var color = 'rgb(255, 196, 79)';
					var keys = value.operator_id;
					if (parseFloat(parseFloat(value.elapsed_time).toFixed(0)) != 0) {
						tact = parseFloat(parseFloat(value.elapsed_time).toFixed(2));
						color = 'rgb(255, 196, 79)';
						keys = value.operator_id;
					}else {
						// if (parseFloat(value.prev_time) < std) {
							tact = parseFloat(parseFloat(value.prev_time).toFixed(2));
							color = 'rgb(60, 214, 75)';
							keys = value.operator_id;
						// }else if (parseFloat(value.prev_time) > std) {
						// 	tact = parseFloat(parseFloat(value.prev_time).toFixed(2));
						// 	color = 'rgb(60, 214, 75)';
						// 	keys = value.operator_id;
						// }
					}

					for(var i = 0; i < result.cycle.length;i++){
						if (result.cycle[i].location == value.location) {
							if (result.cycle[i].cycle != value.cycle) {
								tact = parseFloat(parseFloat(value.prev_time).toFixed(2));
								color = 'rgb(60, 214, 75)';
								keys = value.operator_id;
							}else{
								tact = parseFloat(parseFloat(value.elapsed_time).toFixed(2));
								color = 'rgb(255, 196, 79)';
								keys = value.operator_id;
							}
							cycles = result.cycle[i].cycle;
						}
					}
					tact_times.push({y: tact, color: color,key:keys});
					target.push(std);
				});

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Elapsed Real Time Line {{$line}} (Cycle '+cycles+')',
						style: {
							fontSize: '18px',
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
								fontSize: '15px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Minutes',
							style: {
								color: '#eee',
								fontSize: '17px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"17px"
							}
						},
						type: 'linear',
						max:20
					}
					],
					legend: {
						enabled:false
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
										showModal(this.category,this.series.name,this.options.key);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1.2vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							// cursor: 'pointer'
						},
					},
					series: [
					{
						type: 'column',
						data: tact_times,
						name: 'Elapsed Time',
						color: 'none',
						colorByPoint: false,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1.2vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: target,
						name: 'Tact Time',
						colorByPoint: false,
						color: '#0048ff',
						lineWidth: 4,
						animation: false,
						dashStyle: 'shortdash',
						width: 3,
						zIndex: 5,
						// dataLabels: {
						// 	enabled: true,
						// 	format: '{point.y}' ,
						// 	style:{
						// 		fontSize: '1vw',
						// 		textShadow: false
						// 	},
						// },
						dataLabels: {
							enabled:false
						}
					},
					]
				});

				var categories = [];
				var tact_times = [];
				var std = 420/50;
				if ('{{$line}}' == 5) {
					std = 420/32;
				}
				var target = [];

				$('#tact_time_average').html(std);

				for(var i = 0; i < operator.length;i++){
					$.each(result.average, function(key, value){
						if (operator[i] == value.operator_id) {
							categories.push(value.name);
							if (parseFloat(value.tact_time) < std) {
								tact = parseFloat(parseFloat(value.tact_time).toFixed(2));
								color = 'rgb(60, 214, 75)';
								keys = value.operator_id;
							}else if (parseFloat(value.tact_time) > std) {
								tact = parseFloat(parseFloat(value.tact_time).toFixed(2));
								if (op_training.includes(value.operator_id)) {
									color = 'rgb(61, 97, 227)';
								}else{
									color = 'rgb(235, 82, 82)';
								}
								keys = value.operator_id;
							}
							tact_times.push({y: tact, color: color,key:keys});
							target.push(std);
						}
					});
				}

				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Previous Average Time Line {{$line}}',
						style: {
							fontSize: '18px',
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
								fontSize: '15px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Minutes',
							style: {
								color: '#eee',
								fontSize: '17px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"17px"
							}
						},
						type: 'linear',
						max:20
					}
					],
					legend: {
						enabled:false
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
										showModal(this.category,this.series.name,this.options.key);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1.2vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							// cursor: 'pointer'
						},
					},
					series: [
					{
						type: 'column',
						data: tact_times,
						name: 'Elapsed Time',
						color: 'none',
						colorByPoint: false,
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1.2vw',
								textShadow: false
							},
						},
					},{
						type: 'spline',
						data: target,
						name: 'Tact Time',
						colorByPoint: false,
						color: '#0048ff',
						lineWidth: 4,
						animation: false,
						dashStyle: 'shortdash',
						width: 3,
						zIndex: 5,
						// dataLabels: {
						// 	enabled: true,
						// 	format: '{point.y}' ,
						// 	style:{
						// 		fontSize: '1.2vw',
						// 		textShadow: false
						// 	},
						// },
						dataLabels: {
							enabled:false
						}
					},
					]
				});

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		});
}

function showModal(name,cat,employee_id) {
	$("#detail_penyebab").text("");
	$("#detail_aksi").text("");
	$("#detail_evidence").text("");

	cancelAll();

	$('#employee_id').html(employee_id);
	$('#name').html(name);

	var op_training = [];

	if (training.length > 0) {
		$.each(training, function(key, value){
			op_training.push(value.operator.split('-')[0]);
		});
	}

	if (op_training.includes(employee_id)) {
		$('#modal_hasil').show();
		$('#modal_training').hide();
		$.each(training, function(key, value){
			if (employee_id == value.operator.split('-')[0]) {
				photos = '<img style="width:400px" src="http://10.109.52.1:887/miraidev/public/images/training/'+value.evidence+'" class="user-image" alt="User image">';
				$("#detail_penyebab").append(value.detail);
				$("#detail_aksi").append(value.action);
				$("#detail_evidence").html(photos);
			}
		});
	}else{
		$('#modal_hasil').hide();
		$('#modal_training').show();
	}

	$('#myModal').modal('show');
}

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

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}

function saveTraining(){
	$("#loading").show();

	var formData = new FormData();
	formData.append('operator', $('#employee_id').text()+'-'+$('#name').text());
	formData.append('deskripsi',  CKEDITOR.instances.penyebab.getData());
	formData.append('action',  CKEDITOR.instances.aksi.getData());
	formData.append('evidence', $('#evidence').prop('files')[0]);

	$.ajax({
		url:"{{ url('post/assembly/eff/training') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success: function (response) {
			$("#loading").hide();
			openSuccessGritter('Success', response.message);
			fetchChart();
			cancelAll();
			$('#myModal').modal('hide');
		},
		error: function (response) {
			openErrorGritter('Error!', response.message);
		},
	})	
}

function cancelAll() {
	$("#aksi").html(CKEDITOR.instances.aksi.setData(''));
	$("#penyebab").html(CKEDITOR.instances.penyebab.setData(''));
	$('#evidence').val('');
}


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
@endsection