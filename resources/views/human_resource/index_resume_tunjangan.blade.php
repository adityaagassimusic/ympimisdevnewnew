@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #212121;  
		color:white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(54, 59, 56);
		background-color: #212121;
		color: white;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}
	table.table-condensed > thead > tr > th{   
		color: black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}

	table.table-striped > thead > tr > th{
		border:1px solid black !important;
		text-align: center;
		background-color: rgba(126,86,134,.7) !important;  
	}

	table.table-striped > tbody > tr > td{
		/*border: 1px solid #eeeeee !important;*/
		border-collapse: collapse;
		color: black;
		padding: 3px;
		vertical-align: middle;
		text-align: center;
		background-color: white;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }

	#container1 {
		height: 400px;
	}

	.highcharts-figure,
	.highcharts-data-table table {
		min-width: 310px;
		max-width: 800px;
		margin: 1em auto;
	}

	.highcharts-data-table table {
		font-family: Verdana, sans-serif;
		border-collapse: collapse;
		border: 1px solid #ebebeb;
		margin: 10px auto;
		text-align: center;
		width: 100%;
		max-width: 500px;
	}

	.highcharts-data-table caption {
		padding: 1em 0;
		font-size: 1.2em;
		color: #555;
	}

	.highcharts-data-table th {
		font-weight: 600;
		padding: 0.5em;
	}

	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
		padding: 0.5em;
	}

	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
		background: #f8f8f8;
	}

	.highcharts-data-table tr:hover {
		background: #f1f7ff;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
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
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="box-body">
						<form method="GET" action="{{ url("download/resume/tunjangan") }}">
							<div class="row">
								<!-- <div class="col-xs-12" align="center">
									<span style="font-size: 25px;color: black;width: 25%;">Menunggu Persetujuan</span>
								</div> -->
								<div class="col-md-12">
									<div id="container1" style="width: 100%;height: 400px;"></div>
								</div>
								<div class="col-xs-12" align="center" style="padding-bottom: 30px; padding-top: 50px">
									<span style="font-size: 25px;color: black;width: 25%;">Resume Uang Simpati & Tunjangan Keluarga Disetujui</span>
								</div>
								<div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Jenis Tunjangan</label>
											<select class="form-control select2" data-placeholder="Jenis Tunjangan" id="select_tunj" name="select_tunj" style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="Uang Simpati">Uang Simpati</option>
												<option value="Tunjangan Keluarga">Tunjangan Keluarga</option>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Dari Bulan</label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="datefrom" name="datefrom" data-placeholder="Select Date">
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Sampai Bulan</label>
											<div class="input-group date">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="dateto" name="dateto" data-placeholder="Select Date"  onchange="Search()">
											</div>
										</div>
									</div>
									<div class="col-md-1">
										<div class="form-group">
											<div class="input-group" style="padding-top: 25px">
												<button type="submit" class="btn btn-success"><i class="fa fa-list"></i> Download</button>
											</div>
										</div>
									</div>
									</form>
									<div class="col-md-1">
										<div class="form-group">
											<div class="input-group" style="padding-top: 25px; padding-left: 25px;">
												<a class="btn btn-info" data-toggle="tooltip" id="resume_tunjangan" onclick="resumeTunjangan(this.id)"><i class="fa fa-list"></i> Resume</a>
											</div>
										</div>
									</div>
									<div class="col-md-12" align="center">
										<div class="form-group">
											<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 5px;">
												<span style="font-weight: bold; font-size: 1.6vw;">BELUM DI KONFIRMASI</span>
											</div>
											<table id="tableKaryawanKontrak" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
												<thead style="background-color: rgb(126,86,134); color: #FFD700;">
													<tr>
														<th width="1%">No</th>
														<th width="1%">No Request</th>
														<th width="1%">NIK</th>
														<th width="2%">Nama</th>
														<th width="3%">Department</th>
														<th width="2%">Posisi</th>
														<th width="2%">Jenis Tunjangan</th>
														<th width="2%">Nominal</th>
														<th width="2%">Tanggal Dibuat</th>
														<th width="1%">Action</th>
													</tr>
												</thead>
												<tbody id="bodyTableKaryawanKontrak">
												</tbody>
												<tfoot>
												</tfoot>
											</table>

											<div class="col-xs-12" style="background-color: orange; text-align: center; margin-bottom: 5px;">
												<span style="font-weight: bold; font-size: 1.6vw;">RESUME</span>
											</div>
											<table id="tableDoneKonfirmasi" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
												<thead style="background-color: rgb(126,86,134); color: #FFD700;">
													<tr>
														<th width="1%">No</th>
														<th width="1%">No Request</th>
														<th width="1%">NIK</th>
														<th width="2%">Nama</th>
														<th width="3%">Department</th>
														<th width="2%">Posisi</th>
														<th width="2%">Jenis Tunjangan</th>
														<th width="2%">Nominal</th>
														<th width="2%">Tanggal Dibuat</th>
													</tr>
												</thead>
												<tbody id="BodyTableDoneKonfirmasi">
												</tbody>
												<tfoot>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="ModalDetailUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<div class="col-md-12" style="margin-top: 5px;background-color: #000;text-align: center;background-color: #1a237e;">
							<span style="font-size: 24px;font-weight: bold;color: white;">Update Kontrak Karyawan</span>
						</div>
						<div class="col-xs-6" style="padding-bottom: 1%; padding-top: 2%; width: 100%">
							<table class="table table-bordered table-striped table-hover" id="DetailKaryawanKontrak">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr style="color: black">
										<th>NIK</th>
										<th>Nama</th>
										<th>Department</th>
										<th>Section</th>
										<th>Group</th>
										<th>Sub Group</th>
									</tr>
								</thead>
								<tbody id="BodyDetailKaryawanKontrak"></tbody>
							</table>
						</div>
						<div class="col-xs-12" id="option">
							<center>
								<button class="btn btn-danger" style="font-weight: bold; width: 50%; font-size: 1.5vw; margin-bottom: 10px;" onclick="EndContrac()">
									<i class="fa fa-window-close"></i> Tidak Perpanjang Kontrak <i class="fa fa-window-close"></i>
								</buton>Jenis Tunjangan<button class="btn btn-success" style="font-weight: bold; width: 50%; font-size: 1.5vw; margin-bottom: 10px;" onclick="NextContrac()">
									<i class="fa fa-pencil-square-o"></i> Perpanjang  Kontrak <i class="fa fa-pencil-square-o"></i>
								</button>
							</center>
						</div>

						<div class="col-xs-12" id="jangka_waktu">
							<center>
								<div class="col-md-4">
									<div class="form-group">
										<label>Perpanjang Sampai</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right" id="month_next" name="month_next" data-placeholder="Select Date">
										</div>
									</div>
								</div>
								<div class="col-md-4 col-md-offset-2">
									<div class="form-group" style="padding-top: 20px">
										<!-- <input type="hidden" id="nik_update"> -->
										<button class="btn btn-success" style="font-weight: bold; width: 50%; font-size: 1.5vw; margin-bottom: 10px;" onclick="UpdateKaryawanContrac()">
											<i class="fa fa-check-square-o"></i> Simpan
										</button>
									</div>
								</div>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="modalUpdate">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="nav-tabs-custom tab-danger" align="center">
							<ul class="nav nav-tabs">
								<center><h3 style="background-color: #3f51b5; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Update Karyawan Kontrak</h3>
								</center>
							</ul>
						</div>
						<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
							<div class="col-xs-12">
								<div class="form-group row" align="right">
									<label for="" class="col-sm-4 control-label" style="color: black;">Masukkan File<span class="text-red"> :</span></label>
									<div class="col-sm-6">
										<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
									</div>
								</div>
							<!-- <div class="form-group row">
								<label for="" class="col-sm-4 control-label" style="color: black;">Contoh Template Upload<span class="text-red"> :</span></label>
								<div class="col-sm-6">
									<a href="{{ url('uploads/kebutuhan_mp/TemplateKebutuhanMp.xlsx') }}">TemplateMp.xlsx</a>
								</div>
							</div> -->
							<div class="modal-footer">
								<center>
									<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
									<button onclick="SubmitUpload()" class="btn btn-success">Submit</button>
								</center>
							</div>
						</div>
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
<!-- <script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/highcharts-3d.js")}}"></script> -->
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/highcharts-3d.js"></script>
	<script src="https://code.highcharts.com/modules/cylinder.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
		var arr = [];
		var arr2 = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			$('#datefrom').datepicker({
			// format: "yyyy-mm-dd",
			// autoclose: true,
			// todayHighlight: true
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});
			$('#dateto').datepicker({
			// format: "yyyy-mm-dd",
			// autoclose: true,
			// todayHighlight: true
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true
		});

			$('#month_next').datepicker({
				format: "yyyy-mm-dd",
				autoclose: true,
				todayHighlight: true
			// format: "yyyy-mm",
			// startView: "months", 
			// minViewMode: "months",
			// autoclose: true
		});
			$('.select2').select2({
				allowClear:true
			});

			Search();
			fillChart();
		});

		function resumeTunjangan(id){
			var jenis_tunjangan = $('#select_tunj').val();
			var dari_tanggal = $('#datefrom').val();
			var sampai_tanggal = $('#dateto').val();

			var data = {
				resume:id,
				jenis_tunjangan:jenis_tunjangan,
				dari_tanggal:dari_tanggal,
				sampai_tanggal:sampai_tanggal
			}
			$.get('{{ url("fetch/resume/tunjangan/karyawan") }}',data, function(result, status, xhr){
				if(result.status){
					$('#tableDoneKonfirmasi').DataTable().clear();
					$('#tableDoneKonfirmasi').DataTable().destroy();
					$('#BodyTableDoneKonfirmasi').html("");
					var tableData = "";
					var index = 1;
					$.each(result.data_resumes, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ index +'</td>';
						tableData += '<td>'+ value.request_id +'</td>';
						tableData += '<td>'+ value.employee +'</td>';
						tableData += '<td>'+ value.name +'</td>';
						tableData += '<td>'+ value.department+'</td>';
						tableData += '<td>'+ value.jabatan +'</td>';
						tableData += '<td>'+ value.permohonan +'</td>';
						tableData += '<td>'+ value.buat +'</td>';
						index++;
					});
					$('#BodyTableDoneKonfirmasi').append(tableData);

					var table = $('#tableDoneKonfirmasi').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function fillChart() {
			$("#loading").show();
			var dept = $('#select_dept').val();
			var data = {
				dept:dept
			}

			$.get('{{ url("fetch/grafik/tunjangan") }}', data,function(result, status, xhr) {
				if(xhr.status == 200){
					if(result.status){
						$("#loading").hide();

						var dept = [];
						var jumlah_kel = [];
						var jumlah_simp = [];
						var jumlah_kerja = [];

						$.each(result.all_kel, function(key, value) {
							jumlah_kel.push(value.jumlah);
						});

						$.each(result.all_simp, function(key, value) {
							jumlah_simp.push(value.jumlah);
						});

						$.each(result.tj_kerja, function(key, value) {
							jumlah_kerja.push(value.jumlah);
						});

						$.each(result.semua, function(key, value) {
							dept.push(value.department_shortname);
							// jumlah_kel.push(value.keluarga);
							// jumlah_simp.push(value.simpati);
						});

						var colors = ['#ffd733', '#E82C0C'];

						Highcharts.chart('container1', {
							chart: {
								type: 'column',
								options3d: {
									enabled: true,
									alpha: 15,
									beta: 7,
									depth: 50,
									viewDistance: 25
								}
							},
							title: {
								text: 'Menunggu Persetujuan'
							},
							xAxis: {
								categories: dept,
								type: 'category',
								gridLineWidth: 1,
								gridLineColor: 'RGB(204,255,255)',
								lineWidth:2,
								lineColor:'#9e9e9e',

								labels: {
									style: {
										fontSize: '13px'
									}
								},
							},yAxis: [{
								title: {
									text: 'Total',
									style: {
										color: '#eee',
										fontSize: '15px',
										fontWeight: 'bold',
										fill: '#6d869f'
									}
								},
								labels:{
									style:{
										fontSize:"15px"
									}
								},
								type: 'linear',
								opposite: true
							},
							],
							tooltip: {
								headerFormat: '<span>{series.name}</span><br/>',
								pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
							},
							legend: {
								layout: 'horizontal',
								backgroundColor:
								Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
								itemStyle: {
									fontSize:'10px',
								},
								enabled: true,
								reversed: true
							},  
							plotOptions: {
								series:{
									cursor: 'pointer',
									point: {
										events: {
											click: function () {
												ShowModal(this.category,this.series.name);
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
							},credits: {
								enabled: false
							},
							colors:colors,
							series: [{
								data: jumlah_kel,
								name: 'Tunjangan Keluarga',
								showInLegend: false
							},{
								data: jumlah_simp,
								name: 'Uang Simpati',
								showInLegend: false
							}]
						});


						// Highcharts.chart('container1', {
						// 	chart: {
						// 		type: 'column',
						// 		backgroundColor : '#ffffff'
						// 	},
						// 	title: {
						// 		text: ' ',
						// 		style: {
						// 			fontSize: '20px',
						// 			fontWeight: 'bold'
						// 		}
						// 	},
						// 	xAxis: {
						// 		categories: dept,
						// 		type: 'category',
						// 		gridLineWidth: 1,
						// 		gridLineColor: 'RGB(204,255,255)',
						// 		lineWidth:2,
						// 		lineColor:'#9e9e9e',

						// 		labels: {
						// 			style: {
						// 				fontSize: '13px'
						// 			}
						// 		},
						// 	},
						// 	yAxis: [{
						// 		title: {
						// 			text: 'Total',
						// 			style: {
						// 				color: '#eee',
						// 				fontSize: '15px',
						// 				fontWeight: 'bold',
						// 				fill: '#6d869f'
						// 			}
						// 		},
						// 		labels:{
						// 			style:{
						// 				fontSize:"15px"
						// 			}
						// 		},
						// 		type: 'linear',
						// 		opposite: true
						// 	},
						// 	],
						// 	tooltip: {
						// 		headerFormat: '<span>{series.name}</span><br/>',
						// 		pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						// 	},
						// 	legend: {
						// 		layout: 'horizontal',
						// 		backgroundColor:
						// 		Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
						// 		itemStyle: {
						// 			fontSize:'12px',
						// 		},
						// 		enabled: true,
						// 		reversed: true
						// 	},  
						// 	plotOptions: {
						// 		series:{
						// 			cursor: 'pointer',
						// 			point: {
						// 				events: {
						// 					click: function () {
						// 						ShowModal(this.category,this.series.name);
						// 					}
						// 				}
						// 			},
						// 			animation: false,
						// 			dataLabels: {
						// 				enabled: true,
						// 				format: '{point.y}',
						// 				style:{
						// 					fontSize: '1vw'
						// 				}
						// 			},
						// 			animation: false,
						// 			pointPadding: 0.93,
						// 			groupPadding: 0.93,
						// 			borderWidth: 0.93,
						// 			cursor: 'pointer'
						// 		},
						// 	},credits: {
						// 		enabled: false
						// 	},
						// 	series: [
						// 	{
						// 		type: 'column',
						// 		data: jumlah_kel,
						// 		name: 'Tunjangan Keluarga',
						// 		colorByPoint: false
						// 	},
						// 	{
						// 		type: 'column',
						// 		data: jumlah_simp,
						// 		name: 'Tunjangan Simpati',
						// 		colorByPoint: false
						// 	}
						// 	]
						// });
					}
				}
			});
}

function DownloadResumeExcel(){
	var tnj = $('#select_tunj').val()
	var on_month = $('#datefrom').val()
	var until_month = $('#dateto').val()

	var data = {
		tnj:tnj,
		on_month:on_month,
		until_month:until_month
	}

	$.post('{{ url("download/resume/tunjangan") }}',data, function(result, status, xhr){
		if(result.status){
			$('#section_tp').show();
			$('#section_tp').html("");
			var sections = "";
			sections += '<option value="">&nbsp;</option>';
			$.each(result.section, function(key, value) {
				sections += '<option value="'+value.section+'">'+value.section+'</option>';
			});

			$('#section_tp').append(sections);
		}
	});
}

function SubmitUpload(){
	$('#loading').show();
		// if($('#bulan').val() == ""){
		// 	openErrorGritter('Error!', 'Gagal Upload');
		// 	audio_error.play();
		// 	$('#loading').hide();
		// 	return false;	
		// }

		var formData = new FormData();
		var newAttachment  = $('#upload_file').prop('files')[0];
		var file = $('#upload_file').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('bulan', $("#bulan").val());

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/update/karyawan') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
					$('#bulan').val("");
					$('#upload_file').val("");
					$('#modalUpdate').modal('hide');
					$('#loading').hide();
					Search();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function NextContrac(){
		$("#jangka_waktu").show();
		$("#option").hide();
	}

	function UpdateKaryawanContrac(){
		var data = {
			month_next:$('#month_next').val(),
			employee_id:$('#nik_update').text(),
		}
		$.get('{{ url("update/karyawan/contract") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#ModalDetailUpdate').modal('hide');
				Search();
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function Download(){
		var data = {
			dateto:$('#dateto').val(),
			datefrom:$('#datefrom').val()
		}
		$.get('{{ url("download/data/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#ModalDetailUpdate').modal('hide');
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function CreatePdf(nik){
		var data = {
			nik:nik
		}
		$.get('{{ url("create/pdf/calon/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				Search();
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function Search(){
		var data = {
			dateto:$('#dateto').val(),
			datefrom:$('#datefrom').val(),
			tunjangan:$('#select_tunj').val()
		}
		$.get('{{ url("fetch/resume/tunjangan/karyawan") }}',data, function(result, status, xhr){
			if(result.status){
				$('#periode1').text(result.period);
				$('#periode2').text(result.period);
				$('#tableKaryawanKontrak').DataTable().clear();
				$('#tableKaryawanKontrak').DataTable().destroy();
				$('#bodyTableKaryawanKontrak').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {
					var jenis = value.permohonan.split('/');
					var int = parseInt(jenis[1]);
					var idr = int.toLocaleString('id', { style: 'currency', currency: 'IDR' });

					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.request_id +'</td>';
					tableData += '<td>'+ value.employee +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.department+'</td>';
					tableData += '<td>'+ value.jabatan +'</td>';
					tableData += '<td>'+ jenis[0] +'</td>';
					if (jenis[1] != null) {
						tableData += '<td>'+ idr +'</td>';
					}else{
						tableData += '<td>-</td>';
					}
					tableData += '<td>'+ value.buat +'</td>';
					tableData += '<td style="width: 0.1%; font-weight: bold;"><a href="javascript:void(0)" class="btn btn-success btn-xs" onclick="Nyobakseh(\''+value.request_id+'\',\''+value.project_name+'\')">Confirm</a></td>';
					index++;
				});
				$('#bodyTableKaryawanKontrak').append(tableData);

				var table = $('#tableKaryawanKontrak').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#tableDoneKonfirmasi').DataTable().clear();
				$('#tableDoneKonfirmasi').DataTable().destroy();
				$('#BodyTableDoneKonfirmasi').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data_done, function(key, value) {
					var jenis = value.permohonan.split('/');
					var int = parseInt(jenis[1]);
					var idr = int.toLocaleString('id', { style: 'currency', currency: 'IDR' });

					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.request_id +'</td>';
					tableData += '<td>'+ value.employee +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.department+'</td>';
					tableData += '<td>'+ value.jabatan +'</td>';
					tableData += '<td>'+ jenis[0] +'</td>';
					if (jenis[1] != null) {
						tableData += '<td>'+ idr +'</td>';
					}else{
						tableData += '<td>-</td>';
					}
					tableData += '<td>'+ value.buat +'</td>';
					index++;
				});
				$('#BodyTableDoneKonfirmasi').append(tableData);

				var table = $('#tableDoneKonfirmasi').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ModalKaryawan(){
		$('#ModalDetailUpdate').modal('show');
		$("#jangka_waktu").hide();
	}

	function ModalKaryawanKontrak(employee_id){
		var data = {
			employee_id:employee_id
		};
		$.get('<?php echo e(url("fetch/karyawan/kontrak")); ?>', data, function(result, status, xhr){
			if(result.status){
				ModalKaryawan();
				$('#DetailKaryawanKontrak').DataTable().clear();
				$('#DetailKaryawanKontrak').DataTable().destroy();
				var tableData = '';
				$('#BodyDetailKaryawanKontrak').html("");
				$('#BodyDetailKaryawanKontrak').empty();
				$.each(result.data, function(key, value) {
					tableData += '<tr>';
					tableData += '<td id="nik_update">'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.department +'</td>';
					tableData += '<td>'+ value.section +'</td>';
					tableData += '<td>'+ value.group +'</td>';
					tableData += '<td>'+ value.sub_group +'</td>';
					tableData += '</tr>';
				});
				$('#BodyDetailKaryawanKontrak').append(tableData);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function Nyobakseh(request_id, project_name){
		var data = {
			request_id:request_id,
			project_name:project_name
		}
		$.get('{{ url("update/status/confirm") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				Search();
			}
			else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function clearConfirmation(){
		location.reload(true);		
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
</script>
@endsection