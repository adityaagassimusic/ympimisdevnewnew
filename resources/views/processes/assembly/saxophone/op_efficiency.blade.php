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
		<div id="period_title" class="col-xs-9" style="background-color: rgba(248,161,63,0.9);">
            <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
        </div>
        <div class="col-xs-3">
            <div class="input-group date">
                <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right datepicker" id="tanggal" name="tanggal"
                    onchange="fillChart()">
            </div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
        </div>

		<!-- <div class="col-xs-2" style="padding-right: 0px;">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
			</div>
		</div> -->
		<!-- <div class="col-xs-1" style="padding-left: 5px;padding-right: 0px;">
			<button class="btn btn-success" onclick="fillChart()"><b>Update Chart</b></button>
		</div> -->
		<!-- <div class="col-xs-3 pull-right"> -->
		<!-- </div> -->

		<div class="col-xs-6">
			<div id="container1" style="width: 100%;height: 300px;"></div>
		</div>

		<div class="col-xs-6">
			<div id="container2" style="width: 100%;height: 300px;"></div>
		</div>

		<div class="col-xs-6">
			<div id="container3" style="width: 100%;height: 300px;"></div>
		</div>

		<div class="col-xs-6">
			<div id="container4" style="width: 100%;height: 300px;"></div>
		</div>

		<div class="col-xs-6 col-xs-offset-3">
			<div id="container5" style="width: 100%;height: 300px;"></div>
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
<!-- 								<h5 class="modal-title"><center><b>Operator Efficiency</b></center></h5>
								<h5 class="modal-title" id="op_eff"></h5> -->
							</div>
							<div class="col-md-8">
								<button type="button" class="btn btn-success pull-right" onclick="training(this.id)" id="lakukan_training"><i class="fa fa-users"></i> Lakukan Training</button>

								<button type="button" class="btn btn-danger pull-right" onclick="training(this.id)" id="close_training" style="display:none"><i class="fa fa-users"></i> Close Training</button>
							</div>
						</div>

						<div class="col-md-12" id="modal_operator">
							<table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Operator</th>
										<th>Model</th>
										<th>Location</th>
										<th style="width: 13%">Start</th>
										<th style="width: 13%">Finsih</th>
										<th style="width: 10%">Actual time (second)</th>
										<th style="width: 10%">Standart time (second)</th>
									</tr>
								</thead>
								<tbody id="data-log-body">
								</tbody>
							</table>

							<div id="hasil_training">
								<h5 style="background-color:green;color:white"><center><b>Hasil Training</b></center></h5>
								<label for="detail_penyebab" class="col-sm-12 control-label" style="padding:0">Detail Penyebab Permasalahan</label>
								
								<div class="col-md-12" style="padding:0">
									<span id="detail_penyebab"></span>
								</div>
								
								<label for="detail_aksi" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;">Aksi yang dilakukan</label>

								<div class="col-md-12" style="padding: 0;">
									<span id="detail_aksi"></span>
								</div>

								<label for="detail_evidence" class="col-sm-12 control-label" style="padding:0;margin-top: 20px;">Bukti Training</label>

								<div class="col-md-12" style="padding:0">
									<span id="detail_evidence"></span>
								</div>
							</div>
						</div>

						<div class="col-md-12" id="modal_training">
							<h5><center><b>Lakukan Training</b></center>
							<div class="col-md-12">
								<label for="deskripsi" class="col-sm-12 control-label" style="padding:0">Detail Penyebab Permasalahan<span class="text-red">*</span></label>
								<input class="form-control" name="operator_name" id="operator_name" type="hidden" >

								<div class="col-sm-12" style="padding:0">
									<textarea class="form-control" name="deskripsi" id="deskripsi" data-placeholder="Deskripsi Permasalahan" style="width: 100%;"></textarea>
								</div>

								<label for="action" class="col-sm-12 control-label" style="padding:0">Aksi yang dilakukan<span class="text-red">*</span></label>
								<div class="col-sm-12" style="padding:0">
									<textarea class="form-control" name="action" id="action" data-placeholder="Deskripsi Permasalahan" style="width: 100%;"></textarea>
								</div>

								<label for="action" class="col-sm-12 control-label" style="padding:0">Bukti Training<span class="text-red">*</span></label>
								<div class="col-sm-12" style="padding:0">
									<input type="file" class="form-control" name="evidence" id="evidence">
								</div>

								<div class="col-md-3 col-md-offset-9 " style="margin-top:30px">
									<a class="btn btn-success pull-right" onclick="save()" style="width: 100%; font-weight: bold; font-size: 1.2vw;">Simpan</a>
								</div>
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
	<!-- end modal -->


</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var detail = [];
	var training_op = [];

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		// setInterval(fillChart, 20000);
	});


	function fillChart() {
		$("#loading").show();
		var tanggal = $("#tanggal").val();

		var data = {
			tanggal: tanggal,
			origin_group: '{{ Request::segment(4) }}'
		}

		$.get('{{ url("fetch/assembly/eff") }}', data, function(result, status, xhr) {
			if(result.status){
				detail = result.detail;
				training_op = result.training;
				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

				var target = 100;
				var series_1 = [];
				var series_2 = [];
				var series_3 = [];
				var series_4 = [];
				var series_5 = [];
				var op_name_line_1 = [];
				var op_name_line_2 = [];
				var op_name_line_3 = [];
				var op_name_line_4 = [];
				var op_name_line_5 = [];


                $('#title_text').text('Operator Efficiency ' + result.date);
                var h = $('#period_title').height();

				$.each(result.datas, function(key, value) {
					// if (parseFloat(value.eff) < 150) {
						if (value.location == 1) {
							op_name_line_1.push(value.name);
							// console.log(result.datas);
							// sr = parseFloat(parseFloat(value.eff).toFixed(2));
							var eff = parseFloat(parseFloat(value.eff).toFixed(2));
							var color = 'rgb(144,238,126)';
							var keys = 'none';
							if(value.eff > parseInt(target)){
								eff = parseFloat(parseFloat(value.eff).toFixed(2));
								color = 'rgb(144,238,126)';
								keys = 'none';
							}else{
								var sudah = 0;
								$.each(result.training, function(key2, value2) {
									if (value.name == value2.operator) {
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(3, 128, 7)',key:'sudah_training'});
										sudah++;
									}
									// else{
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(255,116,116)',key:'none'});
									// }
								});
								if (sudah > 0) {
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(52, 99, 209)';
									keys = 'sudah_training';
								}else{
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(255,116,116)';
									keys = 'none';
								}
							}
							series_1.push({y: eff, color: color,key:keys});
						} else if (value.location == 2) {
							op_name_line_2.push(value.name);
							// console.log(result.datas);
							// sr = parseFloat(parseFloat(value.eff).toFixed(2));
							var eff = parseFloat(parseFloat(value.eff).toFixed(2));
							var color = 'rgb(144,238,126)';
							var keys = 'none';
							if(value.eff > parseInt(target)){
								eff = parseFloat(parseFloat(value.eff).toFixed(2));
								color = 'rgb(144,238,126)';
								keys = 'none';
							}else{
								var sudah = 0;
								$.each(result.training, function(key2, value2) {
									if (value.name == value2.operator) {
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(3, 128, 7)',key:'sudah_training'});
										sudah++;
									}
									// else{
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(255,116,116)',key:'none'});
									// }
								});
								if (sudah > 0) {
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(52, 99, 209)';
									keys = 'sudah_training';
								}else{
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(255,116,116)';
									keys = 'none';
								}
							}
							series_2.push({y: eff, color: color,key:keys});
						} else if (value.location == 3) {
							op_name_line_3.push(value.name);
							// console.log(result.datas);
							// sr = parseFloat(parseFloat(value.eff).toFixed(2));
							var eff = parseFloat(parseFloat(value.eff).toFixed(2));
							var color = 'rgb(144,238,126)';
							var keys = 'none';
							if(value.eff > parseInt(target)){
								eff = parseFloat(parseFloat(value.eff).toFixed(2));
								color = 'rgb(144,238,126)';
								keys = 'none';
							}else{
								var sudah = 0;
								$.each(result.training, function(key2, value2) {
									if (value.name == value2.operator) {
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(3, 128, 7)',key:'sudah_training'});
										sudah++;
									}
									// else{
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(255,116,116)',key:'none'});
									// }
								});
								if (sudah > 0) {
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(52, 99, 209)';
									keys = 'sudah_training';
								}else{
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(255,116,116)';
									keys = 'none';
								}
							}
							series_3.push({y: eff, color: color,key:keys});
						} else if (value.location == 4) {
							op_name_line_4.push(value.name);
							// console.log(result.datas);
							// sr = parseFloat(parseFloat(value.eff).toFixed(2));
							var eff = parseFloat(parseFloat(value.eff).toFixed(2));
							var color = 'rgb(144,238,126)';
							var keys = 'none';
							if(value.eff > parseInt(target)){
								eff = parseFloat(parseFloat(value.eff).toFixed(2));
								color = 'rgb(144,238,126)';
								keys = 'none';
							}else{
								var sudah = 0;
								$.each(result.training, function(key2, value2) {
									if (value.name == value2.operator) {
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(3, 128, 7)',key:'sudah_training'});
										sudah++;
									}
									// else{
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(255,116,116)',key:'none'});
									// }
								});
								if (sudah > 0) {
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(52, 99, 209)';
									keys = 'sudah_training';
								}else{
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(255,116,116)';
									keys = 'none';
								}
							}
							series_4.push({y: eff, color: color,key:keys});
						} else if (value.location == 5) {
							op_name_line_5.push(value.name);
							// console.log(result.datas);
							// sr = parseFloat(parseFloat(value.eff).toFixed(2));
							var eff = parseFloat(parseFloat(value.eff).toFixed(2));
							var color = 'rgb(144,238,126)';
							var keys = 'none';
							if(value.eff > parseInt(target)){
								eff = parseFloat(parseFloat(value.eff).toFixed(2));
								color = 'rgb(144,238,126)';
								keys = 'none';
							}else{
								var sudah = 0;
								$.each(result.training, function(key2, value2) {
									if (value.name == value2.operator) {
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(3, 128, 7)',key:'sudah_training'});
										sudah++;
									}
									// else{
										// series.push({y: parseFloat(parseFloat(value.eff).toFixed(2)), color: 'rgb(255,116,116)',key:'none'});
									// }
								});
								if (sudah > 0) {
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(52, 99, 209)';
									keys = 'sudah_training';
								}else{
									eff = parseFloat(parseFloat(value.eff).toFixed(2));
									color = 'rgb(255,116,116)';
									keys = 'none';
								}
							}
							series_5.push({y: eff, color: color,key:keys});
						// }
						// series.push(sr);
					}
				})

				var chart = Highcharts.chart('container1', {
				chart: {
					animation: false,
				},
				title: {
					text: 'Operator Line 1',
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
						text: "Overall Efficiency (%)"
					},
					min: 0,
					labels: {
						enabled: false
					},
					plotLines: [{
					color: '#FF0000',
					value: 100,
					dashStyle: 'shortdash',
					width: 2,
					zIndex: 5,
					label: {
						align:'right',
						text: 'Target '+parseInt(target)+'%',
						x:-7,
						style: {
							fontSize: '12px',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				},
				xAxis: {
					categories: op_name_line_1,
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
					// pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
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
									showDetail(result.date, event.point.category,this.options.key);
								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series_1,
					showInLegend: false
				}]

			});

				var chart = Highcharts.chart('container2', {
				chart: {
					animation: false,
				},
				title: {
					text: 'Operator Line 2',
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
						text: "Overall Efficiency (%)"
					},
					min: 0,
					labels: {
						enabled: false
					},
					plotLines: [{
					color: '#FF0000',
					value: 100,
					dashStyle: 'shortdash',
					width: 2,
					zIndex: 5,
					label: {
						align:'right',
						text: 'Target '+parseInt(target)+'%',
						x:-7,
						style: {
							fontSize: '12px',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				},
				xAxis: {
					categories: op_name_line_2,
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
					// pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
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
									showDetail(result.date, event.point.category,this.options.key);
								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series_2,
					showInLegend: false
				}]
			});

			var chart = Highcharts.chart('container3', {
				chart: {
					animation: false,
				},
				title: {
					text: 'Operator Line 3',
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
						text: "Overall Efficiency (%)"
					},
					min: 0,
					labels: {
						enabled: false
					},
					plotLines: [{
					color: '#FF0000',
					value: 100,
					dashStyle: 'shortdash',
					width: 2,
					zIndex: 5,
					label: {
						align:'right',
						text: 'Target '+parseInt(target)+'%',
						x:-7,
						style: {
							fontSize: '12px',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				},
				xAxis: {
					categories: op_name_line_3,
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
					// pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
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
									showDetail(result.date, event.point.category,this.options.key);
								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series_3,
					showInLegend: false
				}]
			});

			var chart = Highcharts.chart('container4', {
				chart: {
					animation: false,
				},
				title: {
					text: 'Operator Line 4',
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
						text: "Overall Efficiency (%)"
					},
					min: 0,
					labels: {
						enabled: false
					},
					plotLines: [{
					color: '#FF0000',
					value: 100,
					dashStyle: 'shortdash',
					width: 2,
					zIndex: 5,
					label: {
						align:'right',
						text: 'Target '+parseInt(target)+'%',
						x:-7,
						style: {
							fontSize: '12px',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				},
				xAxis: {
					categories: op_name_line_4,
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
					// pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
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
									showDetail(result.date, event.point.category,this.options.key);
								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series_4,
					showInLegend: false
				}]
			});

			var chart = Highcharts.chart('container5', {
				chart: {
					animation: false,
				},
				title: {
					text: 'Operator Line 5',
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
						text: "Overall Efficiency (%)"
					},
					min: 0,
					labels: {
						enabled: false
					},
					plotLines: [{
					color: '#FF0000',
					value: 100,
					dashStyle: 'shortdash',
					width: 2,
					zIndex: 5,
					label: {
						align:'right',
						text: 'Target '+parseInt(target)+'%',
						x:-7,
						style: {
							fontSize: '12px',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				},
				xAxis: {
					categories: op_name_line_5,
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
					// pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
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
									showDetail(result.date, event.point.category,this.options.key);
								}
							}
						},
					}
				},
				series: [{
					name:'OP Efficiency',
					type: 'column',
					data: series_5,
					showInLegend: false
				}]
			});

			$('#loading').hide();
			} else{
				$('#loading').hide();
				openErrorGritter('Error',result.message);
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


	function showDetail(tgl, nama,ket) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$("#detail_penyebab").text("");
		$("#detail_aksi").text("");
		$("#detail_evidence").text("");

		if (ket == "sudah_training") {
			$('#hasil_training').show();
			$('#lakukan_training').hide();

			photos = "";
			$.each(training_op, function(key2, value2) {
				if (nama == value2.operator) {
        			photos = '<img style="width:400px" src="http://10.109.52.1:887/miraidev/public/images/training/'+value2.evidence+'" class="user-image" alt="User image">';
					$("#detail_penyebab").text(value2.detail);
					$("#detail_aksi").text(value2.action);
					$("#detail_evidence").html(photos);
					// console.log(value2.operator);
					// console.log(nama);
				}
			});
		}else{
			$('#hasil_training').hide();
			$('#lakukan_training').show();
		}

		$('#myModal').modal('show');
		$('#modal_operator').show();
		$('#modal_training').hide();
		$('#data-log-body').append().empty();
		$('#op_eff').append().empty();
		$('#judul').append().empty();

		$('#judul').append('<b>'+nama+' on '+tgl+'</b>');
		$('#operator_name').val(nama);

		//Data Log
		var total_perolehan = 0;
		var total_std = 0;
		var total_act = 0;

		var body = '';
		$.each(detail, function(key, value) {
			if (value.name == nama) {
				body += '<tr>';
				body += '<td>'+value.name+'</td>';
				body += '<td>'+value.model+'</td>';
				body += '<td>'+value.location+'</td>';
				body += '<td>'+value.sedang_start_date+'</td>';
				body += '<td>'+value.sedang_finish_date+'</td>';
				body += '<td>'+value.act_time+'</td>';
				body += '<td>'+value.standard_time+'</td>';
				body += '</tr>';
				total_perolehan += parseInt(1);
				total_std += parseFloat(value.standard_time);
				total_act += parseFloat(value.act_time);
			}
		})

		body += '<tr>';
		body += '<td colspan="5" style="text-align: center;">Total</td>';
		body += '<td>'+total_act.toFixed(2)+'</td>';
		body += '<td>'+total_std.toFixed(2)+'</td>';
		body += '</tr>';
		$('#data-log-body').append(body);


		//Resume
		var op_eff = 100 * (total_act / total_std);
		var text_op_eff = '= <sup>Total Standart time</sup>/<sub>Total Actual time</sub>';
		text_op_eff += '<br>= <sup>'+ total_std.toFixed(2) +'</sup>/<sub>'+ total_act.toFixed(2) +'</sub>';
		text_op_eff += '<br>= <b>'+ op_eff.toFixed(2) +'%</b>';
		$('#op_eff').append(text_op_eff);
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}


	function training(elem) {
		if (elem == "lakukan_training") {
			$("#lakukan_training").hide();
			$("#close_training").show();
			$('#modal_operator').hide();
			$('#modal_training').show();
		}
		else if(elem == "close_training"){
			$("#lakukan_training").show();
			$("#close_training").hide();
			$('#modal_operator').show();
			$('#modal_training').hide();
		}
	}


		function save(){
			$("#loading").show();

			var formData = new FormData();
			formData.append('operator', $('#operator_name').val());
			formData.append('deskripsi',  $('#deskripsi').val());
			formData.append('action',  $('#action').val());
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
					fillChart();

				},
				error: function (response) {
					openErrorGritter('Error!', response.message);
				},
			})	
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