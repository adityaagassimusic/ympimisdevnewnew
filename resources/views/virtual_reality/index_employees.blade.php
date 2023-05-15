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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
		<button class="btn btn-success btn-sm pull-right" style="margin-left: 5px" onclick="$('#modalAddUser').modal('show');cancelAll();"><i class="fa fa-plus"></i> Add User</button>
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>							
	<div class="row">
		<!-- <div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<h4>Filter</h4>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Periode</span>
							<div class="form-group">
								<select class="form-control select2" name="mcu_periode" id="mcu_periode" data-placeholder="Pilih Periode MCU" style="width: 100%;">
									<option></option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">MCU Group Code</span>
							<div class="form-group">
								<select class="form-control select2" name="mcu_group" id="mcu_group" data-placeholder="Pilih Kode MCU" style="width: 100%;">
									<option value=""></option>
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/injection/report_cleaning') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> -->
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableOculus" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">NIK</th>
										<th width="4%">Nama</th>
										<th width="2%">Dept</th>
										<th width="4%">Sect</th>
										<th width="4%">Group</th>
										<th width="4%">Sub Group</th>
										<th width="3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableOculus">
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

<div class="modal fade" id="modalAddUser">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Add User</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px" >
				<div class="col-xs-9">
					<div class="form-group row" align="right"  >
						<label for="" class="col-sm-4 control-label">Employee ID<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<select class="form-control select2" id="employee_id" style="width: 100%" data-placeholder="Pilih Nama Karyawan">
								<option value=""></option>
								@foreach($emp as $emp)
								<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-3" align="left">
					<button onclick="addEmp()" class="btn btn-success">
						<i class="fa fa-plus"></i>Add
					</button>
				</div>
				<div class="col-xs-12">
					<table class="table table-hover table-bordered table-striped" id="tableEmployeeEdit">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 5%;">Name</th>
								<th style="width: 1%;">Action</th>
							</tr>
						</thead>
						<tbody id="tableEmployeeBody">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="addEmployees()" class="btn btn-success pull-right"><i class="fa fa-check-square-o"></i> Confirm</button>
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

	var employees = [];
	var count = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		cancelAll();
	});

	function cancelAll() {
		$('#employee_id').val('').trigger('change')
		$('#tableEmployeeBody').html('');
		employees = [];
		count = 0;
	}


	$(function () {
		$('.select2').select2({
			allowClear:true,
		});
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

	function addEmp() {
		var str = $('#employee_id').val();
		var employee_id = str.split('_')[0];
		var name = str.split('_')[1];

		if (str == "") {
			audio_error.play();
			openErrorGritter('Error!','Pilih Karyawan');
			return false;
		}

		if($.inArray(employee_id, employees) != -1){
			audio_error.play();
			openErrorGritter('Error!','Karyawan sudah ada di list.');
			return false;
		}

		var tableEmployee = "";

		tableEmployee += "<tr id='"+employee_id+"'>";
		tableEmployee += "<td>"+employee_id+"</td>";
		tableEmployee += "<td>"+name+"</td>";
		tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployee(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableEmployee += "</tr>";

		employees.push(employee_id);
		count += 1;

		$('#tableEmployeeBody').append(tableEmployee);
		$('#employee_id').val('').trigger('change');
	}

	function remEmployee(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#'+id).remove();	
	}

	function addEmployees() {
		$('#loading').show();
		var data = {
			employees:employees,
		}

		$.post('{{ url("input/oculus/user") }}', data, function(result, status, xhr){
			if(result.status){
				cancelAll();

				$("#modalAddUser").modal('hide');
				fillList();
				$('#loading').hide();
				audio_ok.play();
				openSuccessGritter('Success','Add Employee Succeed with '+result.error_message+' Error(s).');
			} else {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function fillList(){
		$('#loading').show();
		// var data = {
		// 	mcu_periode:$('#mcu_periode').val(),
		// 	mcu_group:$('#mcu_group').val(),
		// }
		$.get('{{ url("fetch/oculus/user") }}', function(result, status, xhr){
			if(result.status){
				$('#tableOculus').DataTable().clear();
				$('#tableOculus').DataTable().destroy();
				$('#bodyTableOculus').html("");
				var tableData = "";
				var index = 1;
				$.each(result.users, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name+'</td>';
					tableData += '<td>'+ (value.department_shortname || '') +'</td>';
					tableData += '<td>'+ (value.section || '') +'</td>';
					tableData += '<td>'+ (value.group || '') +'</td>';
					tableData += '<td>'+ (value.sub_group || '') +'</td>';
					tableData += '<td><button class="btn btn-danger btn-xs" onclick="deleteEmp(\''+value.employee_id+'\')">Delete</button></td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableOculus').append(tableData);

				var table = $('#tableOculus').DataTable({
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

				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function deleteEmp(emp) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				employee_id:emp
			}

			$.get('{{ url("delete/oculus/user") }}', data, function(result, status, xhr){
				if(result.status){
					cancelAll();
					fillList();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Delete User Succeed');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})

		}
	}



</script>
@endsection