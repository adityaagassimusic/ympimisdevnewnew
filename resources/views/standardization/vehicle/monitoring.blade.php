@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(100, 100, 100);
		padding: 3px;
		vertical-align: middle;
		height: 45px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(100, 100, 100);
		vertical-align: middle;
	}
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
{{-- 	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1> --}}
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div id="period_title" class="col-xs-6" style="background-color: #ccff90;"><center><span style="color: black; font-size: 2.2vw; font-weight: bold;" id="title_text"></span></center>
			</div>
			<div class="col-xs-2">
				<div class="form-group">
					<label style="padding-top: 0; padding-left: 0; color: white;" for="" class="col-xs-12 control-label">Cari Periode<span class="text-red"></span> :</label>
					<select class="form-control select2" id="fiscal_year" style="width: 100%; height: 100%;" data-placeholder="Select Fiscal Year" onchange="fetchInspection()" required>
						@foreach($periods as $period)
						<option value="{{ $period->period_date }}">{{ $period->period }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<a href="{{ url("index/standardization/form/roda_2") }}" class="btn btn-success pull-right" style="font-weight: bold; margin-left: 5px;"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Berita Acara Roda 2</a>
			<a href="{{ url("index/standardization/form/roda_4") }}" class="btn btn-primary pull-right" style="font-weight: bold; margin-left: 5px;"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Berita Acara Roda 4</a>
		</div>
		<div class="col-xs-6">
			<div id="container1"></div>
		</div>
		<div class="col-xs-6">
			<div id="container2"></div>
		</div>
		<div class="col-xs-12">
			<table id="tableInspection" class="table table-bordered table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 1%; text-align: right;">Periode</th>
						<th style="width: 1%; text-align: center;">Kategori</th>
						<th style="width: 1%; text-align: center;">Departemen</th>
						<th style="width: 1%; text-align: left;">NIK</th>
						<th style="width: 7%; text-align: left;">Nama</th>
						<th style="width: 3%; text-align: center;">No Pol</th>
						<!-- <th style="width: 3%; text-align: left;">No Pol Terdaftar</th> -->
						<th style="width: 7%; text-align: left;">Pelanggaran</th>
						<th style="width: 10%; text-align: left;">Catatan</th>
						<th style="width: 7%; text-align: left;">Pemeriksa</th>
					</tr>
				</thead>
				<tbody style="background-color: #fcf8e3;" id="tableInspectionBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalInspection">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<input type="hidden" id="updateId">
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">NIK<span class="text-red"></span> :</label>
						<div class="col-xs-4">
							<input class="form-control" placeholder="" type="text" id="updateEmployeeId" disabled>
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Nama Karyawan<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<input class="form-control" placeholder="" type="text" id="updateEmployeeName" disabled>
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Tanggal Pemeriksaan<span class="text-red"></span> :</label>
						<div class="col-xs-4">
							<input class="form-control" placeholder="" type="text" id="updateInspectionDate" disabled>
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Jenis Pelanggaran<span class="text-red"></span> :</label>
						<div class="col-xs-9">
							<input class="form-control" placeholder="" type="text" id="updateDescription" disabled>
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Unggah Bukti<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<input type="file" id="updateEvidence">
						</div>
					</div>
					<div class="form-group">
						<label style="padding-top: 0;" for="" class="col-xs-12 control-label">Bukti Foto<span class="text-red"></span> :</label>
						<div class="col-xs-12" id="showEvidence">

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success pull-right" onclick="updateInspection()" style="font-weight: bold; font-size: 1.5vw;">SIMPAN</button>
				<button class="btn btn-danger pull-left" onclick="deleteInspection()" style="font-weight: bold; font-size: 1.5vw;">HAPUS</button>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchInspection();
		$('.select2').select2();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var vehicle_inspections = [];
	var vehicle_inspection_details = [];
	var tables = [];

	function fetchInspection(){
		$('#loading').show();
		var period = $('#fiscal_year').val();
		var data = {
			period : period
		}
		$.get('{{ url("fetch/standardization/vehicle_monitoring") }}', data, function(result, status, xhr){
			if(result.status){
				$('#title_text').text('Pemeriksaan Kendaraan Periode - '+result.period);

				vehicle_inspections = result.vehicle_inspections;
				vehicle_inspection_details = result.vehicle_inspection_details;

				var charts = [];
				var tableInspectionBody = "";
				$('#tableInspectionBody').html("");
				$('#tableInspection').DataTable().clear();
				$('#tableInspection').DataTable().destroy();

				for (var i = 0; i < vehicle_inspections.length; i++) {
					tableInspectionBody += '<tr>';
					tableInspectionBody += '<td style="width: 1%; text-align: right;">'+vehicle_inspections[i].inspection_date+'</td>';
					tableInspectionBody += '<td style="width: 1%; text-align: center;">'+vehicle_inspections[i].category+'</td>';
					tableInspectionBody += '<td style="width: 1%; text-align: center;">'+vehicle_inspections[i].department_shortname+'</td>';
					tableInspectionBody += '<td style="width: 1%; text-align: left;">'+vehicle_inspections[i].employee_id+'</td>';
					tableInspectionBody += '<td style="width: 7%; text-align: left;">'+vehicle_inspections[i].employee_name+'</td>';
					tableInspectionBody += '<td style="width: 3%; text-align: center;">'+vehicle_inspections[i].vehicle_number+'</td>';
					// var registration_number = [];
					// if(vehicle_inspections[i].registration_number){
					// 	registration_number = vehicle_inspections[i].registration_number.split(',');
					// }
					// tableInspectionBody += '<td style="width: 3%; text-align: left;">';
					// if(registration_number.length > 0){
					// 	for (var k = 0; k < registration_number.length; k++) {
					// 		tableInspectionBody += registration_number[k]+'<br>';
					// 	}
					// }
					// else{
					// 	tableInspectionBody += '-';						
					// }
					// tableInspectionBody += '</td>';
					tableInspectionBody += '<td style="width: 7%; text-align: left;">';
					for (var j = 0; j < vehicle_inspection_details.length; j++) {
						if(vehicle_inspection_details[j].employee_id == vehicle_inspections[i].employee_id && vehicle_inspection_details[j].inspection_date == vehicle_inspections[i].inspection_date){
							var status = "";
							if(vehicle_inspection_details[j].status){
								tableInspectionBody += '<a href="javascript:void(0)" onclick="modalInspection(\''+vehicle_inspection_details[j].id+'\',\''+vehicle_inspections[i].employee_id+'\',\''+vehicle_inspections[i].employee_name+'\',\''+vehicle_inspections[i].inspection_date+'\',\''+vehicle_inspection_details[j].description+'\',\''+vehicle_inspection_details[j].status+'\',\''+vehicle_inspection_details[j].evidence_file+'\')" style="color: green;"><i class="fa fa-check"></i> '+vehicle_inspection_details[j].description+' ('+vehicle_inspection_details[j].status+')</a><br>';
								status = 'Close';
							}
							else{
								tableInspectionBody += '<a href="javascript:void(0)" onclick="modalInspection(\''+vehicle_inspection_details[j].id+'\',\''+vehicle_inspections[i].employee_id+'\',\''+vehicle_inspections[i].employee_name+'\',\''+vehicle_inspections[i].inspection_date+'\',\''+vehicle_inspection_details[j].description+'\',\''+vehicle_inspection_details[j].status+'\',\''+vehicle_inspection_details[j].evidence_file+'\')" style="color: red;"><i class="fa fa-times"></i> '+vehicle_inspection_details[j].description+'</a><br>';
								status = 'Open';
							}

							charts.push({
								department_shortname: vehicle_inspections[i].department_shortname,
								description: vehicle_inspection_details[j].description,
								status: status
							});

							tables.push({
								inspection_date: vehicle_inspections[i].inspection_date,
								category: vehicle_inspections[i].category,
								department_shortname: vehicle_inspections[i].department_shortname,
								employee_id: vehicle_inspections[i].employee_id,
								employee_name: vehicle_inspections[i].employee_name,
								vehicle_number: vehicle_inspections[i].vehicle_number,
								registration_number: vehicle_inspections[i].registration_number,
								id: vehicle_inspection_details[j].id,
								description: vehicle_inspection_details[j].description,
								status: vehicle_inspection_details[j].status,
								evidence_file: vehicle_inspection_details[j].evidence_file
							});
						}
					}
					tableInspectionBody += '</td>';
					if(vehicle_inspections[i].remark){
						tableInspectionBody += '<td style="width: 10%; text-align: left;">'+vehicle_inspections[i].remark+'</td>';
					}
					else{
						tableInspectionBody += '<td style="width: 10%; text-align: left;">-</td>';
					}
					tableInspectionBody += '<td style="width: 7%; text-align: left;">'+vehicle_inspections[i].created_by_name+'</td>';
					tableInspectionBody += '</tr>';
				}
				$('#tableInspectionBody').append(tableInspectionBody);
				$('#tableInspection').DataTable({
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
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				var array1 = charts;
				var result1 = [];
				array1.reduce(function(res, value) {
					if (!res[value.description]) {
						res[value.description] = { description: value.description, open: 0, close: 0 };
						result1.push(res[value.description])
					}
					if(value.status == 'Open'){
						res[value.description].open += 1;
					}
					if(value.status == 'Close'){
						res[value.description].close += 1;
					}
					return res;
				}, {});

				var array2 = charts;
				var result2 = [];
				array2.reduce(function(res, value) {
					if (!res[value.department_shortname]) {
						res[value.department_shortname] = { department_shortname: value.department_shortname, open: 0, close: 0 };
						result2.push(res[value.department_shortname])
					}
					if(value.status == 'Open'){
						res[value.department_shortname].open += 1;
					}
					if(value.status == 'Close'){
						res[value.department_shortname].close += 1;
					}
					return res;
				}, {});

				var categories1 = [];
				var categories2 = [];
				var seriesOpen1 = [];
				var seriesOpen2 = [];
				var seriesClose1 = [];
				var seriesClose2 = [];

				$.each(result1, function(key, value){
					categories1.push(value.description);
					seriesOpen1.push(value.open);
					seriesClose1.push(value.close);
				});

				$.each(result2, function(key, value){
					categories2.push(value.department_shortname);
					seriesOpen2.push(value.open);
					seriesClose2.push(value.close);
				});

				Highcharts.chart('container1', {
					chart: {
						backgroundColor: null,
						type: 'bar'
					},
					title: {
						text: '<b>Resume Pelanggaran</b>'
					},
					xAxis: {
						categories: categories1
					},
					credits: {
						enabled: false
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Jumlah Pelanggaran'
						},
						stackLabels: {
							enabled: true
						}
					},
					legend: {
						borderWidth: 1
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						series: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121',
							dataLabels: {
								enabled: true
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.category, 'description');
									}
								}
							}
						}
					},
					series: [{
						name: 'Open',
						data: seriesOpen1,
						color: 'RGB(255,204,255)'
					}, {
						name: 'Closed',
						data: seriesClose1,
						color: 'RGB(204,255,255)'
					}]
				});

				Highcharts.chart('container2', {
					chart: {
						backgroundColor: null,
						type: 'column'
					},
					title: {
						text: '<b>Resume Departemen</b>'
					},
					xAxis: {
						categories: categories2
					},
					credits: {
						enabled: false
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Jumlah Pelanggaran'
						},
						stackLabels: {
							enabled: true
						}
					},
					legend: {
						borderWidth: 1
					},
					tooltip: {
						headerFormat: '<b>{point.x}</b><br/>',
						pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121',
							dataLabels: {
								enabled: true
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchDetail(this.category, 'department_shortname');
									}
								}
							}
						}
					},
					series: [{
						name: 'Open',
						data: seriesOpen2,
						color: 'RGB(255,204,255)'
					}, {
						name: 'Closed',
						data: seriesClose2,
						color: 'RGB(204,255,255)'
					}]
				});

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				return false;
			}
		});
}

function updateInspection(){
	if(confirm("Apakah anda yakin akan menutup temuan pemeriksaan ini?")){
		$('#loading').show();
		var id = $('#updateId').val();
		if($('#updateEvidence').prop('files').length == 0){
			audio_error.play();
			openErrorGritter('Error!', 'Masukkan evidence terlebih dahulu');
			return false;
		}

		var formData = new FormData();
		formData.append('id', id);

		formData.append('attachment', $('#updateEvidence').prop('files')[0]);
		var file = $('#updateEvidence').val().replace(/C:\\fakepath\\/i, '').split(".");
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('update/standardization/vehicle_inspection') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data){
				if(data.status){
					$('#updateId').val("");
					$('#updateEmployeeId').val("");
					$('#updateEmployeeName').val("");
					$('#updateInspectionDate').val("");
					$('#updateDescription').val("");
					$('#updateEvidence').val("");
					$('#showEvidence').html("");
					$('#modalInspection').modal("hide");
					fetchInspection();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success!', data.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', data.message);
					return false;
				}
			}
		});
	}
	else{
		return false;
	}
}

function deleteInspection(){
	if(confirm("Apakah anda yakin akan menutup temuan pemeriksaan ini?")){
		var id = $('#updateId').val();

		var data = {
			id:id
		}

		$.post('{{ url("delete/standardization/vehicle_inspection") }}', data, function(result, status, xhr){
			if(result.status){
				$('#updateId').val("");
				$('#updateEmployeeId').val("");
				$('#updateEmployeeName').val("");
				$('#updateInspectionDate').val("");
				$('#updateDescription').val("");
				$('#updateEvidence').val("");
				$('#showEvidence').html("");
				$('#modalInspection').modal("hide");
				fetchInspection();
				$('#loading').hide();
				audio_ok.play();
				openSuccessGritter('Success!', data.message);
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', data.message);
				return false;
			}
		});
	}
	else{
		return false;
	}
}

function fetchDetail(category, type){

	if(type == 'description'){
		$('#loading').hide();
		audio_error.play();
		openErrorGritter('Error!', 'Gunakan filter pada grafik "RESUME DEPARTEMEN"');
		return false;
	}

	var tableInspectionBody = "";
	$('#tableInspectionBody').html("");
	$('#tableInspection').DataTable().clear();
	$('#tableInspection').DataTable().destroy();

	for (var i = 0; i < vehicle_inspections.length; i++) {
		if(type == 'department_shortname' && vehicle_inspections[i].department_shortname == category){
			tableInspectionBody += '<tr>';
			tableInspectionBody += '<td style="width: 1%; text-align: right;">'+vehicle_inspections[i].inspection_date+'</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: center;">'+vehicle_inspections[i].category+'</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: center;">'+vehicle_inspections[i].department_shortname+'</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: left;">'+vehicle_inspections[i].employee_id+'</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: left;">'+vehicle_inspections[i].employee_name+'</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: center;">'+vehicle_inspections[i].vehicle_number+'</td>';
			var registration_number = [];
			if(vehicle_inspections[i].registration_number){
				registration_number = vehicle_inspections[i].registration_number.split(',');
			}
			tableInspectionBody += '<td style="width: 1%; text-align: left;">';
			if(registration_number.length > 0){
				for (var k = 0; k < registration_number.length; k++) {
					tableInspectionBody += registration_number[k]+'<br>';
				}
			}
			else{
				tableInspectionBody += '-';						
			}
			tableInspectionBody += '</td>';
			tableInspectionBody += '<td style="width: 1%; text-align: left;">';
			for (var j = 0; j < vehicle_inspection_details.length; j++) {
				if(vehicle_inspection_details[j].employee_id == vehicle_inspections[i].employee_id && vehicle_inspection_details[j].inspection_date == vehicle_inspections[i].inspection_date){
					var status = "";
					if(vehicle_inspection_details[j].status){
						tableInspectionBody += '<a href="javascript:void(0)" onclick="modalInspection(\''+vehicle_inspection_details[j].id+'\',\''+vehicle_inspections[i].employee_id+'\',\''+vehicle_inspections[i].employee_name+'\',\''+vehicle_inspections[i].inspection_date+'\',\''+vehicle_inspection_details[j].description+'\',\''+vehicle_inspection_details[j].status+'\',\''+vehicle_inspection_details[j].evidence_file+'\')" style="color: green;"><i class="fa fa-check"></i> '+vehicle_inspection_details[j].description+' ('+vehicle_inspection_details[j].status+')</a><br>';
						status = 'Close';
					}
					else{
						tableInspectionBody += '<a href="javascript:void(0)" onclick="modalInspection(\''+vehicle_inspection_details[j].id+'\',\''+vehicle_inspections[i].employee_id+'\',\''+vehicle_inspections[i].employee_name+'\',\''+vehicle_inspections[i].inspection_date+'\',\''+vehicle_inspection_details[j].description+'\',\''+vehicle_inspection_details[j].status+'\',\''+vehicle_inspection_details[j].evidence_file+'\')" style="color: red;"><i class="fa fa-times"></i> '+vehicle_inspection_details[j].description+'</a><br>';
						status = 'Open';
					}
				}
			}
			tableInspectionBody += '</td>';
			if(vehicle_inspections[i].remark){
				tableInspectionBody += '<td style="width: 1%; text-align: left;">'+vehicle_inspections[i].remark+'</td>';
			}
			else{
				tableInspectionBody += '<td style="width: 1%; text-align: left;">-</td>';
			}
			tableInspectionBody += '<td style="width: 1%; text-align: left;">'+vehicle_inspections[i].created_by_name+'</td>';
			tableInspectionBody += '</tr>';
		}
	}
	$('#tableInspectionBody').append(tableInspectionBody);
	$('#tableInspection').DataTable({
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

function modalInspection(id, employee_id, employee_name, inspection_date, description, status, evidence_file){
	$('#updateId').val("");
	$('#updateEmployeeId').val("");
	$('#updateEmployeeName').val("");
	$('#updateInspectionDate').val("");
	$('#updateDescription').val("");
	$('#updateEvidence').val("");
	$('#showEvidence').html("");

	$('#updateId').val(id);
	$('#updateEmployeeId').val(employee_id);
	$('#updateEmployeeName').val(employee_name);
	$('#updateInspectionDate').val(inspection_date);
	if(status != 'null'){
		$('#updateDescription').val(description+' (Closed '+status+')');
	}
	else{
		$('#updateDescription').val(description+' (Open)');
	}
	if(evidence_file != 'null'){
		var url = "{{ url("files/vehicle_inspection") }}";
		var evidence = '<img style="width: 100%;" src="'+url+'/'+evidence_file+'">';
		$('#showEvidence').append(evidence);
	}
	$('#modalInspection').modal('show');
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

