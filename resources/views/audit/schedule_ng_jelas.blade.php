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
		<button class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#modalUploadSchedule').modal('show')"><i class="fa fa-upload"></i> Upload Schedule</button>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="$('#add-modal').modal('show');$('#add_audit_id').val('').trigger('change');$('#add_employee_id').val('').trigger('change');$('#add_schedule_date').val('')"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Schedule</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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
					<center><h4 id="date_title" style="font-weight: bold;font-size: 20px"></h4></center>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Month From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Month From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Month To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_to" placeholder="Select Month To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Select Claim Title</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" id="audit_id" data-placeholder="Select Claim Title">
									<option value=""></option>
									@foreach($audit as $audit)
									<option value="{{$audit->audit_id}}_{{$audit->audit_title}}">{{$audit->audit_id}} - {{$audit->audit_title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/audit_ng_jelas') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/audit_ng_jelas/schedule') }}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableSchedule" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="3%">Claim Title</th>
										<th width="3%">Dept</th>
										<th width="1%">Area</th>
										<th width="1%">Product</th>
										<th width="1%">Schedule Date</th>
										<th width="3%">Auditor</th>
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


<div class="modal fade" id="modalUploadSchedule">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Schedule</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-8">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<input type="file" name="scheduleFile" id="scheduleFile">
						</div>
					</div>
				</div>
				<div class="col-xs-4">
					<div class="form-group row" align="right">
						<div class="col-sm-12" align="left">
							<a class="btn btn-info pull-right" href="{{url('download/audit_ng_jelas/schedule')}}">Example</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="uploadSchedule()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="edit-modal">
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
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Claim Title<span class="text-red">*</span></label>
									<input type="hidden" name="id" id="id">
									<div class="col-sm-5" align="left" id="divEditAuditId">
										<select class="form-control" data-placeholder="Select Claim Title" name="edit_audit_id" id="edit_audit_id" style="width: 100%">
											<option value=""></option>
											@foreach($audit2 as $audit2)
											<option value="{{$audit2->audit_id}}">{{$audit2->audit_id}} - {{$audit2->audit_title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditor<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditEmployee">
										<select class="form-control" data-placeholder="Select Auditor" name="edit_employee_id" id="edit_employee_id" style="width: 100%">
											<option value=""></option>
											@foreach($emp as $emp)
											<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule Date<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<div class="form-group">
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
					<button class="btn btn-danger pull-left" onclick="$('#edit-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="add-modal">
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
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Claim Title<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddAuditId">
										<select class="form-control" data-placeholder="Select Claim Title" name="add_audit_id" id="add_audit_id" style="width: 100%">
											<option value=""></option>
											@foreach($audit3 as $audit3)
											<option value="{{$audit3->audit_id}}">{{$audit3->audit_id}} - {{$audit3->audit_title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditor<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddEmployee">
										<select class="form-control" data-placeholder="Select Auditor" name="add_employee_id" id="add_employee_id" style="width: 100%">
											<option value=""></option>
											@foreach($emp2 as $emp2)
											<option value="{{$emp2->employee_id}}">{{$emp2->employee_id}} - {{$emp2->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule Date<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<div class="form-group">
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
					<button class="btn btn-danger pull-left" onclick="$('#add-modal').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		// $('.datepicker').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('.select2').select2({
			allowClear:true
		});

		$('#edit_audit_id').select2({
			allowClear:true,
			dropdownParent:$('#divEditAuditId')
		});

		$('#edit_employee_id').select2({
			allowClear:true,
			dropdownParent:$('#divEditEmployee')
		});

		$('#add_audit_id').select2({
			allowClear:true,
			dropdownParent:$('#divAddAuditId')
		});

		$('#add_employee_id').select2({
			allowClear:true,
			dropdownParent:$('#divAddEmployee')
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
	function fillList(){
		$('#loading').show();
		var data = {
			tanggal_from:$('#tanggal_from').val(),
			tanggal_to:$('#tanggal_to').val(),
			audit_id:$('#audit_id').val(),
		}
		$.get('{{ url("fetch/audit_ng_jelas/schedule") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableSchedule').DataTable().clear();
				$('#tableSchedule').DataTable().destroy();
				$('#bodyTableSchedule').html("");
				var tableData = "";
				var index = 1;
				$.each(result.schedule, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.audit_title +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.department +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.area+'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.product +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.schedule_dates +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.employee_id +'<br>'+ value.name +'</td>';
					if (value.schedule_status == 'Sudah Dikerjakan') {
						var color = '#abffab';
					}else{
						var color = '#ffc4c4';
					}
					tableData += '<td style="background-color:'+color+';text-align:left;padding-left:7px;">'+ value.schedule_status +'</td>';
					tableData += '<td>';
					if (value.schedule_status == 'Belum Dikerjakan') {
						tableData += '<button class="btn btn-warning btn-sm" onclick="editSchedule(\''+value.schedule_id+'\',\''+value.audit_id+'\',\''+value.employee_id+'\',\''+value.schedule_date+'\')"><i class="fa fa-edit"></i></button><button class="btn btn-danger btn-sm" onclick="deleteSchedule(\''+value.schedule_id+'\')" style="margin-left:10px;"><i class="fa fa-trash"></i></button>';
					}
					tableData += '</td>';
					tableData += '</tr>';
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

				var audit_title = '';
				if ($('#audit_id').val() != '') {
					audit_title = $('#audit_id').val().split('_')[1];
				}

				$('#date_title').html(result.dateTitleFirst+result.dateTitleLast+'<br>'+audit_title);
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
		$('#edit_schedule_date').val(schedule_date.split('-')[0]+'-'+schedule_date.split('-')[1]).trigger('change');
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var audit_id = $('#edit_audit_id').val();
			var id = $('#id').val();
			var employee_id = $('#edit_employee_id').val();
			var schedule_date = $('#edit_schedule_date').val();

			if (audit_id == '' || employee_id == '' || schedule_date == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Isi semua data.')
				return false;
			}

			var data = {
				id:id,
				audit_id:audit_id,
				employee_id:employee_id,
				schedule_date:schedule_date
			}

			$.post('{{ url("update/audit_ng_jelas/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Update Schedule');
					$('#edit-modal').modal('hide');
				}else{
					$('#loading').hide();
					audio_error.play();
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

			$.get('{{ url("delete/audit_ng_jelas/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Delete Schedule');
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			});
		}	
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var audit_id = $('#add_audit_id').val();
			var employee_id = $('#add_employee_id').val();
			var schedule_date = $('#add_schedule_date').val();

			if (audit_id == '' || employee_id == '' || schedule_date == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Isi semua data.')
				return false;
			}

			var data = {
				audit_id:audit_id,
				employee_id:employee_id,
				schedule_date:schedule_date
			}

			$.post('{{ url("input/audit_ng_jelas/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					fillList();
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Input Schedule');
					$('#add-modal').modal('hide');
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			});
		}	
	}

	function uploadSchedule() {
		$('#loading').show();
		// if($('#menuDate').val() == ""){
		// 	openErrorGritter('Error!', 'Please input period');
		// 	audio_error.play();
		// 	$('#loading').hide();
		// 	return false;	
		// }

		var formData = new FormData();
		var newAttachment  = $('#scheduleFile').prop('files')[0];
		var file = $('#scheduleFile').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/audit_ng_jelas/schedule') }}",
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
					$('#scheduleFile').val("");
					$('#modalUploadSchedule').modal('hide');
					$('#loading').hide();
					fillList();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}



</script>
@endsection