@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	.nav-tabs-custom > .nav-tabs > li.active{
		border-top: 6px solid red;
	}
	.small-box{
		margin-bottom: 0;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="box box-primary">
					<div class="box-body">
						<div class="row">
							<div class="col-md-12" align="center">
								<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 7px" align="left">
									<button id="Add"class="btn btn-success" style="font-weight: bold; color: white; width: 100%" onclick="Target()"> Upload Target  <i class="fa fa-sign-in" aria-hidden="true"></i></button>
								</div>
								<!-- <div class="col-xs-2" style="padding-bottom: 10px; padding-top: 7px" align="center">
									<select class="form-control select3" onchange="fetchMonitoringShift(this.value)" id="shift" name="shift" data-placeholder="Filter By Shift" style="width: 100%">
										<option value=""></option>
										<option value="Shift 1">Shift 1</option>
										<option value="Shift 2">Shift 2</option>
									</select>
								</div> -->
								<div class="col-xs-8" style="padding-bottom: 10px; padding-top: 7px" align="right">
									<label>Select Date</label>
								</div>
								<div class="col-xs-2" style="padding-bottom: 10px" align="center">
									<div class="input-group">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" id="tanggal" class="form-control datepicker" style="width: 100%; text-align: center;" placeholder="Select Date" onchange="fetchMonitoringDate(this.value)">
									</div>
								</div>
								<!-- <div class="col-xs-12" style="padding-bottom: 10px">
									<div id="daily-bpro" style="width: 100%; height: 40vh; margin-bottom: 10px; border: 1px solid black;"></div>
								</div> -->
								<div class="col-xs-12" style="padding-bottom: 10px">
									<!-- <table id="TableMonitoringBpro" class="table table-bordered table-striped table-hover">
										<thead style="background-color: #BDD5EA; color: black;">
											<tr>
												<th width="30%" style="text-align: center">GMC</th>
												<th width="40%" style="text-align: center">Material Description</th>
												<th width="30%" style="text-align: center">Qty</th>
											</tr>
										</thead>
										<tbody id="BodyTableMonitoringBpro">
										</tbody>
									</table> -->
									<input type="hidden" name="dateHidden" value="{{ date('Y-m-d') }}" id="dateHidden">
									<div class="nav-tabs-custom">
										<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
											<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Production Result<br><span class="text-purple">生産実績</span></a></li>
											<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Production Result Daily<br><span class="text-purple">BI週次出荷</span></a></li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tab_1" style="height: 580px;">
												<div class="col-md-12">
													<div class="progress-group" id="progress_div">
														<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
															<span class="progress-text" id="progress_text_production1" style="font-size: 25px; padding-top: 10px;"></span>
															<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production1" style="font-size: 30px; padding-top: 10px;"></div>
														</div>
													</div>
												</div>
												<div id="container1" style="width:100%; height:530px;"></div>
											</div>
											<div class="tab-pane" id="tab_2" style="height: 580px;">
												<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 7px" align="center">
													<select class="form-control select3" onchange="fillChart(this.value)" id="shift" name="shift" data-placeholder="Filter By Shift" style="width: 100%">
														<option value=""></option>
														<option value="Shift A">Shift A</option>
														<option value="Shift B">Shift B</option>
													</select>
												</div>
												<div class="col-md-12">
													<div class="progress-group" id="progress_div">
														<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">
															<span class="progress-text" id="progress_text_production2" style="font-size: 25px; padding-top: 10px;"></span>
															<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production2" style="font-size: 30px; padding-top: 10px;"></div>
														</div>
													</div>
												</div>
												<div id="container2" style="width:100%; height:530px;"></div>
											</div>
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="ModalUpload" data-keyboard="false">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<center>
						<h3 style="background-color: rgb(126,86,134); font-weight: bold; padding: 3px; margin-top: 0; color: white;">
							Upload Target<br>
						</h3>
					</center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<form method="post" action="{{ url('upload/target/bpro') }}" enctype="multipart/form-data">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12" style="margin-bottom : 5px">
								<span>File Exel : </span><br>
								<input type="file" name="file_excel" id="file_excel" accept=".xls,.xlsx">
							</div>  
							<div class="col-md-12">
								<br>
								<button class="btn btn-success pull-right" type="submit">Simpan (保存)</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#tanggal').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select3').select2({
			dropdownAutoWidth : true,
			allowClear: true
		});
		// DataTable();
		Grafik();
		fillChart();
	});

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

	function DataTable(){
		$.get('{{ url("fetch/monitoring/daily/bpro") }}', function(result, status, xhr){
			if(result.status){
				$('#TableMonitoringBpro').DataTable().clear();
				$('#TableMonitoringBpro').DataTable().destroy();
				$('#BodyTableMonitoringBpro').html("");
				var tableData = "";
				var index = 1;

				$.each(result.data, function(key, value) {

					tableData += '<tr>';
					tableData += '<td id="operator-gmc" style="text-align: center">'+ value.material_number +'</td>';
					tableData += '<td id="operator-desc" style="text-align: center">'+ value.material_description +'</td>';
					tableData += '<td style="text-align: center">'+ value.quantity +'</td>';
					tableData += '</tr>';

				});
				$('#BodyTableMonitoringBpro').append(tableData);
				$('#TableMonitoringBpro').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 20, 50, -1 ],
						[ '10 rows', '20 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						{
							extend: 'copy',
							className: 'btn btn-success',
							text: '<i class="fa fa-copy"></i> Copy',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function Grafik(){
		$.get('{{ url("fetch/monitoring/daily/bpro") }}', function(result, status, xhr) {
			var categori = [];
			var series = [];
			var target = [];

			$.each(result.week, function(key, value){
				var isi = 0;
				categori.push(value.week_date);
				$.each(result.data_grafik, function(key2, value2){
					if (value.week_date == value2.date) {
						series.push({y:parseInt(value2.quantity), key: value.week_date});
						isi = 1;
					}
				});
				if (isi == 0) {
					series.push(0);
				}
			});

			$.each(result.week, function(key, value){
				var is = 0;
				categori.push(value.week_date);
				$.each(result.data_target, function(key2, value2){
					if (value.week_date == value2.date) {
						target.push(parseInt(value2.quantity));
						is = 1;
					}
				});
				if (is == 0) {
					target.push(0);
				}
			});

			console.log(series, target);

			Highcharts.chart('daily-bpro', {
				chart: {
					type: 'column',
					scrollablePlotArea: {
						minWidth: 700
					}
				},
				title: {
					text: 'Monitoring Daily Body Parts Process'
				},
				credits: {
					enabled: false
				},
				xAxis: {
					tickInterval: 1,
					gridLineWidth: 1,
					categories: categori,
					crosshair: true
				},
				yAxis: [{
					title: {
						text: ''
					},
					opposite: true
				}],
				legend: {
					borderWidth: 1
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0;text-shadow: -1px 0 #909090, 0 1px #909090, 1px 0 #909090, 0 -1px #909090;font-size: 16px;font-weight:bold;">{series.name}: </td>' +
					'<td style="padding:0;font-size:16px;"><b>{point.y:.1f}</b></td></tr>',
					footerFormat: '</table>',
				},
				plotOptions: {
					series:{
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									Detail(this.category, this.series.name);
								}
							}
						},
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								fontSize: '1vw'
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					},
				},
				series: [
				{
					name: 'Target',
					data: target,
					color: '#ff2824'
				},{
					name: 'Perolehan',
					data: series,
					color: '#f39c12'
				}
				]
			});
		});
	}

	function Target(){
		$('#ModalUpload').modal('show');
	}

	function Detail(category, name){
		console.log(category, name);
	}

	function fetchMonitoringShift(value){
		console.log(value);
	}

	function fetchMonitoringDate(value){
		console.log(value);
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

	function fillChart(shift){
		var id = $('#dateHidden').val();
		var now = new Date();
		var now_tgl = addZero(now.getFullYear())+'-'+addZero(now.getMonth()+1)+'-'+addZero(now.getDate());
		var req = new Date(id);
		var req_tgl = addZero(req.getFullYear())+'-'+addZero(req.getMonth()+1)+'-'+addZero(req.getDate());

		// if(id != 0){
		// 	$('#dateHidden').val(id);
		// }

		var date = $('#dateHidden').val();
		var shift = $('#shift').val();

		if (shift.length == 0) {
			p = 'all';
		}else{
			p = shift;
		}

		var data = {
			date:date,
			p:p
		};

		$.get('{{ url("fetch/daily/bpro") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					if(result.reason == null){
						$('#reason').text('');							
					}else{
						$('#reason').text(result.reason.reason);	
					}

					$('#production_month_jp').text(result.now.substring(5,7));
					$('#production_date_jp').text(result.now.substring(8,10));
					$('#production_date_id').text(result.dateTitle);

					$('#export_week_jp').text(result.weekTitle.replace(/[^0-9\.]/g, ''));
					$('#export_month_jp').text(result.now.substring(5,7));
					$('#export_date_jp').text(result.now.substring(8,10));
					$('#export_date_id').text(result.dateTitle);
					$('#export_week_id').text(result.weekTitle);

					$('#fl_jp').text("????");
					$('#fl_id').text("????");
					$('#cl_jp').text("????");
					$('#cl_id').text("????");
					$('#as_jp').text("????");
					$('#as_id').text("????");
					$('#ts_jp').text("????");
					$('#ts_id').text("????");
					$('#vn_jp').text("????");
					$('#vn_id').text("????");
					$('#rc_jp').text("????");
					$('#rc_id').text("????");
					$('#pn_jp').text("????");
					$('#pn_id').text("????");


						// Progres bar hari kerja/minggu
					for (var i = 1; i < 3; i++) {
						var persen = 0;

						if(req.getDay() == 0){
							persen = 20;
							$('#progress_bar_week'+i).css('font-size', '25px');
							$('#progress_bar_week'+i).addClass('active');		
						}
						else if(req.getDay() == 1){
							persen = 40;
							$('#progress_bar_week'+i).css('font-size', '25px');
							$('#progress_bar_week'+i).addClass('active');		
						}
						else if(req.getDay() == 2){
							persen = 60;
							$('#progress_bar_week'+i).css('font-size', '30px');
							$('#progress_bar_week'+i).addClass('active');
						}
						else if(req.getDay() == 3){
							persen = 80;
							$('#progress_bar_week'+i).css('font-size', '30px');
							$('#progress_bar_week'+i).addClass('active');
						}
						else if(req.getDay() == 4){
							persen = 100;
							$('#progress_bar_week'+i).css('font-size', '30px');
							$('#progress_bar_week'+i).removeClass('active');
						}
						else if(req.getDay() == 5){
							persen = 20;
							$('#progress_bar_week'+i).css('font-size', '30px');
							$('#progress_bar_week'+i).addClass('active');
						}	
						else if(req.getDay() == 6){
							persen = 20;
							$('#progress_bar_week'+i).css('font-size', '30px');
							$('#progress_bar_week'+i).addClass('active');
						}

						if(persen <= 20){
							$('#progress_bar_week'+i).html("Working Time : "+persen+"%");
						}
						else{
							$('#progress_bar_week'+i).html("Week's Working Time : "+persen+"%");
						}

						$('#progress_bar_week'+i).css('width', persen+'%');
						$('#progress_bar_week'+i).css('color', 'white');
						$('#progress_bar_week'+i).css('font-weight', 'bold');

					}

						// Progres bar jam kerja/hari
					for (var i = 1; i < 4; i++) {
						if(now_tgl == req_tgl){
							if(now.getHours() < 7){
								$('#progress_bar_production'+i).append().empty();
								$('#progress_text_production'+i).html("Today's Working Time : 0%");
								$('#progress_bar_production'+i).css('width', '0%');
								$('#progress_bar_production'+i).css('color', 'white');
								$('#progress_bar_production'+i).css('font-weight', 'bold');
							}
							else if((now.getHours() >= 16) && (now.getDay() != 5)){
								$('#progress_text_production'+i).append().empty();
								$('#progress_bar_production'+i).html("Today's Working Time : 100%");
								$('#progress_bar_production'+i).css('width', '100%');
								$('#progress_bar_production'+i).css('color', 'white');
								$('#progress_bar_production'+i).css('font-weight', 'bold');
								$('#progress_bar_production'+i).removeClass('active');
							}
							else if(now.getDay() == 5){
								$('#progress_text_production'+i).append().empty();
								var total = 570;
								var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
								var persen = (now_menit/total) * 100;
								if(now.getHours() >= 7 && now_menit < total){
									if(persen > 24){
										if(persen > 32){
											$('#progress_bar_production'+i).html("Today's Working Time : "+persen.toFixed(2)+"%");
										}
										else{
											$('#progress_bar_production'+i).html("Working Time : "+persen.toFixed(2)+"%");
										}	
									}
									else{
										$('#progress_bar_production'+i).html(persen.toFixed(2)+"%");
									}
									$('#progress_bar_production'+i).css('width', persen+'%');
									$('#progress_bar_production'+i).addClass('active');

								}
								else if(now_menit >= total){
									$('#progress_bar_production'+i).html("Today's Working Time : 100%");
									$('#progress_bar_production'+i).css('width', '100%');
									$('#progress_bar_production'+i).removeClass('active');

								}
								$('#progress_bar_production'+i).css('color', 'white');
								$('#progress_bar_production'+i).css('font-weight', 'bold');
							}
							else{
								$('#progress_text_production'+i).append().empty();
								var total = 540;
								var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
								var persen = (now_menit/total) * 100;
								if(now.getHours() >= 7 && now_menit < total){
									if(persen > 24){
										if(persen > 32){
											$('#progress_bar_production'+i).html("Today's Working Time : "+persen.toFixed(2)+"%");
										}
										else{
											$('#progress_bar_production'+i).html("Working Time : "+persen.toFixed(2)+"%");
										}	
									}
									else{
										$('#progress_bar_production'+i).html(persen.toFixed(2)+"%");
									}
									$('#progress_bar_production'+i).css('width', persen+'%');
									$('#progress_bar_production'+i).addClass('active');
								}
								else if(now_menit >= total){
									$('#progress_bar_production'+i).html("Today's Working Time : 100%");
									$('#progress_bar_production'+i).css('width', '100%');
									$('#progress_bar_production'+i).removeClass('active');
								}

								$('#progress_bar_production'+i).css('font-weight', 'bold');
								$('#progress_bar_production'+i).addClass('active');
							}
						}
						else if(now > req){
							$('#progress_text_production'+i).append().empty();
							$('#progress_bar_production'+i).html("Today's Working Time : 100%");
							$('#progress_bar_production'+i).css('width', '100%');
							$('#progress_bar_production'+i).css('color', 'white');
							$('#progress_bar_production'+i).css('font-weight', 'bold');
							$('#progress_bar_production'+i).removeClass('active');
						}
						else{
							$('#progress_bar_production'+i).append().empty();
							$('#progress_text_production'+i).html("Today's Working Time : 0%");
							$('#progress_bar_production'+i).css('width', '0%');
							$('#progress_bar_production'+i).css('color', 'white');
							$('#progress_bar_production'+i).css('font-weight', 'bold');
						}							
					}

					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					var data = result.chartResult1;
					var xAxis = []
					, planCount = []
					, actualCount = []
					, xAxisEI = []
					, planCountEI = []
					, actualCountEI = []
					, descFront = []

					$("#dateResult .btn").removeClass("btn-active");
					$("#"+date+"").addClass("btn-active");

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].gmc);
						planCount.push(data[i].plan);
						actualCount.push(data[i].actual);							
						descFront.push(data[i].series);
					}

					var yAxisLabels = [0,25,50,75,110];
					Highcharts.chart('container1', {
						colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="color: rgba(96, 92, 168);"> On '+ result.week +' ('+ result.week_min_max[0].min_date +'-'+ result.week_min_max[0].max_date +')</span>',
							style: {
								fontSize: '10px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: descFront,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '10px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							},
							stackLabels: {
								format: 'Total: {total:,.0f}set(s)',
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
								}
							}
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '25%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								}
							},
							series: {
								groupPadding: -0.2,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalResult(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '1vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planCount
						}, {
							name: 'Actual',
							data: actualCount
						}]
					});

					var data = result.chartResult2;
					var xAxis2 = []
					, planCount2 = []
					, actualCount2 = []
					, xAxisEI2 = []
					, planCountEI2 = []
					, actualCountEI2 = []
					, descFront2 = []

					$("#dateResult .btn").removeClass("btn-active");
					$("#"+date+"").addClass("btn-active");

					for (i = 0; i < data.length; i++) {
						xAxis2.push(data[i].gmc);
						planCount2.push(data[i].plan);
						actualCount2.push(data[i].actual);							
						descFront2.push(data[i].series);
					}

					var yAxisLabels = [0,25,50,75,110];
					Highcharts.chart('container2', {
						colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 2vw;">Production Result Daily ('+ result.dateTitle +')</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: descFront2,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							},
							stackLabels: {
								format: 'Total: {total:,.0f}set(s)',
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
								}
							}
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								}
							},
							series: {
								groupPadding: -0.2,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalResult(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planCount2
						}, {
							name: 'Actual',
							data: actualCount2
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}			
		});	
}
</script>
@endsection