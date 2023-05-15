@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { 
		display: none;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<input type="hidden" id="green">
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<form method="GET" action="{{ url("fetch/clinic_visit_log_excel") }}">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-md-3">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-12">
										<div class="form-group">
											<label>Kunjungan Mulai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="visitFrom" id="visitFrom">
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label>Kunjungan Sampai</label>
											<div class="input-group date" style="width: 100%;">
												<input type="text" placeholder="Pilih Tanggal" class="form-control pull-right" name="visitTo" id="visitTo">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-12">
										<div class="form-group">
											<label>Employee ID</label>
											<select class="form-control select2" multiple="multiple" data-placeholder="Pilih Employee ID" name="employee_id" id="employee_id" style="width: 100% height: 35px; font-size: 15px;">
												<option value=""></option>
												@foreach($employees as $employee)
												<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-md-12">
										<div class="form-group">
											<label>Department</label>
											<select class="form-control select2" multiple="multiple" data-placeholder="Pilih Department" name="department" id="department" style="width: 100% height: 35px; font-size: 15px;">
												<option value=""></option>
												@foreach($departments as $department)
												<option value="{{ $department->department }}">{{ $department->department }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="box box-primary box-solid">
								<div class="box-body">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Tujuan</label>
													<select style="width: 100%;" multiple="multiple" class="form-control select2" id="purpose" name="purpose"  data-placeholder="Pilih Tujuan">
														<option value=""></option>
														@foreach($purposes as $purpose)
														<option value="{{ $purpose }}">{{ $purpose }}</option>
														@endforeach
													</select>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Dokter</label>
													<select style="width: 100%;" multiple="multiple" class="form-control select2" id="doctor" name="doctor" data-placeholder="Pilih Dokter">
														<option value=""></option>
														@foreach($doctors as $doctor)
														<option value="{{ $doctor }}">{{ $doctor }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Paramedis</label>
													<select style="width: 100%;" multiple="multiple" class="form-control select2" id="paramedic" name="paramedic" data-placeholder="Pilih Paramedis">
														<option value=""></option>
														@foreach($paramedics as $paramedic)
														<option value="{{ $paramedic }}">{{ $paramedic }}</option>
														@endforeach
													</select>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label>Diagnosa</label>
													<select style="width: 100%;" multiple="multiple" class="form-control select2" id="diagnose" name="diagnose" data-placeholder="Pilih Diagnosa">
														<option value=""></option>
														@foreach($diagnoses as $diagnose)
														<option value="{{ $diagnose }}">{{ $diagnose }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								{{-- <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> Excel</button> --}}
								<a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
							</div>
						</div>
					</form>
					<div class="col-md-12" style="overflow-x: auto;">
						<table id="tableList" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%;">Visited At</th>
									<th style="width: 10%;">Employee ID</th>
									<th style="width: 20%;">Name</th>
									<th style="width: 20%;">Department</th>
									<th style="width: 15%;">Paramedic</th>
									<th style="width: 20%;">Purpose</th>
									<th style="width: 10%;">Diagnose</th>
									<th style="width: 5%;">Action</th>
								</tr>
							</thead>
							<tbody id="tableBodyList">
							</tbody>
							<tfoot>
								<tr style="color: black">
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #f39c12; margin-bottom: 2%;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Clinic Visit</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Visited At: </span>
						</div>
						<div class="col-xs-4">
							<input type="text" class="form-control" name="edit_visited_at" id="edit_visited_at" style="width: 100%; font-size: 15px;" readonly>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">NIK: </span>
						</div>
						<div class="col-xs-4">
							<input type="text" class="form-control" name="edit_employee_id" id="edit_employee_id" style="width: 100%; font-size: 15px;" readonly>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Name: </span>
						</div>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="edit_name" id="edit_name" style="width: 100%; font-size: 15px;" readonly>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Department: </span>
						</div>
						<div class="col-xs-7">
							<input type="text" class="form-control" name="edit_department" id="edit_department" style="width: 100%; font-size: 15px;" readonly>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Purpose: </span>
						</div>
						<div class="col-xs-4">
							<div class="form-group" style="margin-bottom: 0px;">
								<select style="width: 100%;" class="form-control select2" id="edit_purpose" name="edit_purpose" data-placeholder="Pilih Tujuan">
									<option value=""></option>
									@foreach($purposes as $purpose)
									<option value="{{ $purpose }}">{{ $purpose }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Paramedic: </span>
						</div>
						<div class="col-xs-4">
							<div class="form-group" style="margin-bottom: 0px;">
								<select style="width: 100%;" class="form-control select2" id="edit_paramedic" name="edit_paramedic" data-placeholder="Pilih Paramedis">
									<option value=""></option>
									@foreach($paramedics as $paramedic)
									<option value="{{ $paramedic }}">{{ $paramedic }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Doctor: </span>
						</div>
						<div class="col-xs-4">
							<div class="form-group" style="margin-bottom: 0px;">
								<select style="width: 100%;" class="form-control select2" id="edit_doctor" name="edit_doctor" data-placeholder="Pilih Dokter">
									<option value=""></option>
									@foreach($doctors as $doctor)
									<option value="{{ $doctor }}">{{ $doctor }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div id="show-diagnose">
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-4" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Diagnose: </span>
							</div>
							<div class="col-xs-6">
								<div class="form-group" style="margin-bottom: 0px;">
									<select style="width: 100%;" multiple="multiple" class="form-control select2" id="edit_diagnose" name="edit_diagnose" data-placeholder="Pilih Diagnosa">
										<option value=""></option>
										@foreach($diagnoses as $diagnose)
										<option value="{{ $diagnose }}">{{ $diagnose }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<div class="row">
					<div class="col-xs-12" style="padding-right: 12%;">
						<center>						
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button class="btn btn-success" onClick="edit()"><i class="fa fa-pencil"></i> Submit</button>
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


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
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#visitFrom').datepicker({
			autoclose: true,
			format: "dd-mm-yyyy",
			todayHighlight: true
		});
		$('#visitTo').datepicker({
			autoclose: true,
			format: "dd-mm-yyyy",
			todayHighlight: true
		});

		$('.select2').select2();

		$('#show-diagnose').hide();
		$('#show-medicines').hide();


		fillTable();

	});

	var med = 0;
	function addMedicine() {
		++med;

		$add = '<div class="col-xs-12" id="add_med_'+ med +'"><div class="col-xs-5" style="color: black; padding: 0px; padding-right: 1%;"><select style="width: 100%;" class="form-control select3" id="med_'+ med +'" data-placeholder="Select Medicine"><option value="">Select Medicine</option>@foreach($medicines as $medicine)<option value="{{ $medicine->medicine_name }}">{{ $medicine->medicine_name }}</option>@endforeach</select></div><div class="col-xs-2" style="color: black; padding: 0px; padding-right: 1%;"><div class="form-group"><input type="number" id="med_qty_'+ med +'" data-placeholder="Qty" style="width: 100%; height: 33px; font-size: 15px; text-align: center;"></div></div><div class="col-xs-1" style="padding: 0px;"><button class="btn btn-danger" onclick="removeMedicine(1)"><i class="fa fa-close"></i></button></div></div>';

		$('#medicine').append($add);

		$(function () {
			$('.select3').select2({
				dropdownParent: $('#medicine')
			});
		})
	}

	function removeMedicine(id) {
		$("#add_med_"+id).remove();

		if(med != id){
			for (var i = id; i < med; i++) {
				document.getElementById("add_med_"+ (i+1)).id = "add_med_"+ i;
				document.getElementById("med_"+ (i+1)).id = "med_"+ i;
				document.getElementById("med_qty_"+ (i+1)).id = "med_qty_"+ i;
			}		
		}
		med--;
	}

	function fillTable() {
		var visitFrom = $('#visitFrom').val();
		var visitTo = $('#visitTo').val();
		var employee_id = $('#employee_id').val();
		var department = $('#department').val();
		var purpose = $('#purpose').val();
		var doctor = $('#doctor').val();
		var paramedic = $('#paramedic').val();
		var diagnose = $('#diagnose').val();

		var data = {
			visitFrom:visitFrom,
			visitTo:visitTo,
			employee_id:employee_id,
			department:department,
			purpose:purpose,
			doctor:doctor,
			paramedic:paramedic,
			diagnose:diagnose,
		}

		$('#loading').show();
		$.get('{{ url("fetch/clinic_visit_log") }}', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";


				for (var i = 0; i < result.logs.length; i++) {

					tableData += '<tr id="row_'+ result.logs[i].visited_at.replace(/[^0-9]/g,'') + result.logs[i].employee_id + '">';
					tableData += '<td>'+ result.logs[i].visited_at +'</td>';
					tableData += '<td>'+ result.logs[i].employee_id +'</td>';
					tableData += '<td>'+ (result.logs[i].name || 'Not Found') +'</td>';
					tableData += '<td>'+ (result.logs[i].department || 'Not Found') +'</td>';
					tableData += '<td>'+ result.logs[i].paramedic +'</td>';
					tableData += '<td>'+ result.logs[i].purpose +'</td>';
					tableData += '<td>'+ (result.logs[i].diagnose || '-') +'</td>';
					tableData += '<td style="text-align: center;">';
					tableData += '<button id="'+ result.logs[i].visited_at.replace(/[^0-9]/g,'') + result.logs[i].employee_id + '" style="width: 80%; height: 100%;" class="btn btn-xs btn-warning form-control" onclick="showEdit(id)"><span><i class="fa fa-pencil-square-o"></i>&nbsp;Edit</span></button>';
					tableData += '</td>';
					tableData += '</tr>';


				}
				$('#tableBodyList').append(tableData);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				var table = $('#tableList').DataTable({
					'dom': 'Bfrtip',	
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]],
					'buttons':{
						buttons:
						[{
							extend: 'pageLength',
							className: 'btn btn-default',
						},{
							extend: 'copy',
							className: 'btn btn-success',
							text: '<i class="fa fa-copy"></i> Copy',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						}]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': false,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
				});

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					});
				});
				$('#tableList tfoot tr').appendTo('#tableList thead');

				$('#loading').hide();

			}
		});
	}

	function edit(){

		var visited_at = $("#edit_visited_at").val();
		var employee_id = $("#edit_employee_id").val();
		var purpose = $("#edit_purpose").val();
		var diagnose = $("#edit_diagnose").val();
		var paramedic = $("#edit_paramedic").val();
		var doctor = $("#edit_doctor").val();

		$("#loading").show();

		var data = {
			visited_at : visited_at,
			employee_id : employee_id,
			purpose : purpose,
			diagnose : diagnose,
			paramedic : paramedic,
			doctor : doctor
		}

		$.post('{{ url("edit/diagnose") }}', data,  function(result, status, xhr){
			if(result.status){
				$('#show-diagnose').hide();
				fillTable();
				$("#loading").hide();
				$('#editModal').modal('hide');
				openSuccessGritter('Success', result.message);

			}else{
				$("#loading").hide();
				openErrorGritter('Error!', result.message);
			}
		});		

	}

	function showEdit(id) {

		$("#edit_visited_at").val($('#row_'+id).find('td').eq(0).text());
		$("#edit_employee_id").val($('#row_'+id).find('td').eq(1).text());
		$("#edit_name").val($('#row_'+id).find('td').eq(2).text());
		$("#edit_department").val($('#row_'+id).find('td').eq(3).text());
		$("#edit_paramedic").val($('#row_'+id).find('td').eq(4).text()).trigger('change.select2');
		$("#edit_purpose").val($('#row_'+id).find('td').eq(5).text()).trigger('change.select2');


		var diagnose = $('#row_'+id).find('td').eq(6).text();
		if(diagnose != '-'){
			$("#edit_diagnose").val(diagnose.split(",")).trigger('change.select2');
			$('#show-diagnose').show();
		}else{
			$("#edit_diagnose").val('').trigger('change.select2');
			$('#show-diagnose').hide();
		}

		$('#editModal').modal('show');

	}


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