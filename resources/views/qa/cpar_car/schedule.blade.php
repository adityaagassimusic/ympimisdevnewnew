@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">

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

	.containers {
  display: block;
  position: relative;
  padding-left: -20px;
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
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
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 20px;
  height: 25px;
  width: 25px;
  background-color: #eee;
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
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
		<a class="btn btn-primary btn-sm pull-right" style="margin-right: 5px" href="{{url('index/qa/cpar_car/')}}"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Monitoring</a>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#create_modal" style="margin-right: 5px" onclick="cancelAll()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Schedule</button>
			
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
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
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div style="text-align: center;background-color: orange;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: white;font-size: 20px">
							SCHEDULE
						</span>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Audit Title</span>
							<div class="form-group">
								<select class="form-control" name="audit_id" id="audit_id" data-placeholder="Pilih Audit Title" style="width: 100%;">
									<option></option>
									@foreach($audit_id as $audit_id)
									<option value="{{$audit_id->audit_id}}">{{$audit_id->audit_title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/cpar_car/') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/cpar_car/schedule/') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableSchedule" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Audit ID</th>
										<th width="2%">Claim Title</th>
										<th width="1%">Aduditor</th>
										<th width="1%">Schedule Date</th>
										<th width="1%">Status</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableSchedule">
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

	<div class="modal modal-default fade" id="create_modal" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Schedule</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Claim Title</label>
										<div class="col-sm-10" align="left" id="divAddAuditId">
											<select class="form-control" name="add_audit_id" id="add_audit_id" data-placeholder="Pilih Claim Title" style="width: 100%;">
												<option></option>
												@foreach($audit_id2 as $audit_id2)
												<option value="{{$audit_id2->audit_id}}">{{$audit_id2->audit_title}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Auditor</label>
										<div class="col-sm-10" align="left" id="divAddEmployee">
											<select class="form-control" name="add_employee_id" id="add_employee_id" data-placeholder="Pilih Auditor" style="width: 100%;">
												<option></option>
												@foreach($emp as $emp)
												<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Schedule Date</label>
										<div class="col-sm-5" align="left">
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="add_schedule_date" placeholder="Select Schedule Date" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#create_modal').modal('hide');"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_modal" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Schedule</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="col-xs-12">
									<input type="hidden" name="id" id="id">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Claim Title</label>
										<div class="col-sm-10" align="left" id="divEditAuditId">
											<select class="form-control" name="edit_audit_id" id="edit_audit_id" data-placeholder="Pilih Claim Title" style="width: 100%;">
												<option></option>
												@foreach($audit_id3 as $audit_id3)
												<option value="{{$audit_id3->audit_id}}">{{$audit_id3->audit_title}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Auditor</label>
										<div class="col-sm-10" align="left" id="divEditEmployee">
											<select class="form-control" name="edit_employee_id" id="edit_employee_id" data-placeholder="Pilih Auditor" style="width: 100%;">
												<option></option>
												@foreach($emp2 as $emp2)
												<option value="{{$emp2->employee_id}}">{{$emp2->employee_id}} - {{$emp2->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Schedule Date</label>
										<div class="col-sm-5" align="left">
											<div class="input-group date">
												<div class="input-group-addon bg-white">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control datepicker" id="edit_schedule_date" placeholder="Select Schedule Date" autocomplete="off">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" onclick="$('#edit_modal').modal('hide');"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('body').toggleClass("sidebar-collapse");

		$('#audit_id').select2({
			allowClear:true,
		});

		$('#add_audit_id').select2({
			allowClear:true,
			dropdownParent: $('#divAddAuditId'),
		});

		$('#add_employee_id').select2({
			allowClear:true,
			dropdownParent: $('#divAddEmployee'),
		});

		$('#edit_audit_id').select2({
			allowClear:true,
			dropdownParent: $('#divEditAuditId'),
		});

		$('#edit_employee_id').select2({
			allowClear:true,
			dropdownParent: $('#divEditEmployee'),
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		fillList();
	});

	function cancelAll() {
		$('#add_audit_id').val('').trigger('change');
		$('#add_employee_id').val('').trigger('change');
		$('#add_schedule_date').val('');

		$('#edit_audit_id').val('').trigger('change');
		$('#edit_employee_id').val('').trigger('change');
		$('#edit_schedule_date').val('');
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
	function fillList(){
		$('#loading').show();
		var data = {
			audit_id:$('#audit_id').val(),
		}
		$.get('{{ url("fetch/qa/cpar_car/schedule") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableSchedule').DataTable().clear();
				$('#tableSchedule').DataTable().destroy();
				$('#bodyTableSchedule').html("");
				var tableData = "";
				var index = 1;

				$.each(result.schedule, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.audit_id +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.audit_title +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.employee_id +' - '+value.name+'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.schedule_date +'</td>';
					if (value.schedule_status == 'Belum Dikerjakan') {
						var color = '#ffbeb8';
					}else{
						var color = '#b0ffb7';
					}
					tableData += '<td style="text-align:left;padding-left:7px;background-color:'+color+'">'+ value.schedule_status +'</td>';
					tableData += '</td>';
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editSchedule(\''+value.id+'\',\''+value.audit_id+'\',\''+value.employee_id+'\',\''+value.schedule_date+'\')">Edit</button><button class="btn btn-sm btn-danger" style="margin-left:5px;" onclick="deleteSchedule(\''+value.id+'\')">Delete</button></td>';
					index++;
				});
				$('#bodyTableSchedule').append(tableData);

				var table = $('#tableSchedule').DataTable({
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
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function editSchedule(id,audit_id,employee_id,schedule_date) {
		$('#id').val(id);
		$('#edit_audit_id').val(audit_id).trigger('change');
		$('#edit_employee_id').val(employee_id).trigger('change');
		$('#edit_schedule_date').val(schedule_date.split('-')[0]+'-'+schedule_date.split('-')[1]);
		$('#edit_modal').modal('show');
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var audit_id = $('#add_audit_id').val();
			var employee_id = $('#add_employee_id').val();
			var schedule_date = $('#add_schedule_date').val();

			if (audit_id == '' || employee_id == '' || schedule_date == '') {
				openErrorGritter('Error!','Isi semua data');
				$('#loading').hide();
				return false;
			}
			var data = {
				audit_id:audit_id,
				employee_id:employee_id,
				schedule_date:schedule_date,
			}

			$.post('{{ url("input/qa/cpar_car/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#create_modal').modal('hide');
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var id = $('#id').val();
			var audit_id = $('#edit_audit_id').val();
			var employee_id = $('#edit_employee_id').val();
			var schedule_date = $('#edit_schedule_date').val();

			if (audit_id == '' || employee_id == '' || schedule_date == '') {
				openErrorGritter('Error!','Isi semua data');
				$('#loading').hide();
				return false;
			}
			var data = {
				id:id,
				audit_id:audit_id,
				employee_id:employee_id,
				schedule_date:schedule_date,
			}

			$.post('{{ url("update/qa/cpar_car/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#edit_modal').modal('hide');
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function deleteSchedule(id) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			var data = {
				id:id,
			}

			$.get('{{ url("delete/qa/cpar_car/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}


</script>
@endsection