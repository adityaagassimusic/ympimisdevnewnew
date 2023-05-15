@extends('layouts.master')
@section('stylesheets')
<?php use \App\Http\Controllers\AssemblyProcessController; ?>
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Schedule
		</button>
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<!-- <div class="box-header">
					<h3 class="box-title">Serial Number Report Filters</h3>
				</div> -->
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-12" style="overflow-x: scroll;">
							<table id="tableSchedule" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);color: white;">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 5%">Document</th>
										<th style="width: 1%">Version</th>
										<th style="width: 4%">Auditor</th>
										<th style="width: 4%">Auditee</th>
										<th style="width: 2%">Schedule Date</th>
										<th style="width: 2%">Status</th>
										<th style="width: 2%">PIC Cek Efektifitas</th>
										<th style="width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableSchedule">
								</tbody>
							</table>
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
									<label class="col-sm-4">Document<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditDocument">
										<select class="form-control select3" data-placeholder="Select Document" name="edit_document" id="edit_document" style="width: 100%">
											<option value=""></option>
											@foreach($document2 as $documents2)
											<option value="{{$documents2->document_number}}">{{$documents2->document_number}} - {{$documents2->title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Version<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="edit_version" placeholder="Document Version" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditor<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditAuditor">
										<select class="form-control select3" multiple data-placeholder="Select Auditor" name="select_edit_auditor" id="select_edit_auditor" style="width: 100%" onchange="changeEditAuditor()">
											@foreach($emp3 as $emps3)
											<option value="{{$emps3->employee_id}}">{{$emps3->employee_id}} - {{$emps3->name}}</option>
											@endforeach
										</select>
										<input type="hidden" name="edit_auditor" id="edit_auditor">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditee<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditAuditee">
										<select class="form-control select3" data-placeholder="Select Auditee" name="edit_auditee" id="edit_auditee" style="width: 100%">
											<option value=""></option>
											@foreach($emp4 as $emps4)
											<option value="{{$emps4->employee_id}}">{{$emps4->employee_id}} - {{$emps4->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule Date<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control datepicker" id="edit_schedule_date" placeholder="Schedule Date" required readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">PIC Cek Efektifitas<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditAuditorEffectivity">
										<select class="form-control select3" data-placeholder="Select PIC Cek Efektifitas" name="select_edit_auditor_effectivity" id="select_edit_auditor_effectivity" style="width: 100%" >
											<option value=""></option>
											@foreach($emp6 as $emps)
											<option value="{{$emps->employee_id}}">{{$emps->employee_id}} - {{$emps->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
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
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Schedule</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Document<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddDocument">
										<select class="form-control select3" data-placeholder="Select Document" name="add_document" id="add_document" style="width: 100%">
											<option value=""></option>
											@foreach($document as $documents)
											<option value="{{$documents->document_number}}">{{$documents->document_number}} - {{$documents->title}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Version<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="add_version" placeholder="Document Version" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditor<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddAuditor">
										<select class="form-control select3" multiple data-placeholder="Select Auditor" name="select_add_auditor" id="select_add_auditor" style="width: 100%" onchange="changeAddAuditor()">
											@foreach($emp as $emps)
											<option value="{{$emps->employee_id}}">{{$emps->employee_id}} - {{$emps->name}}</option>
											@endforeach
										</select>
										<input type="hidden" name="add_auditor" id="add_auditor">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Auditee<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddAuditee">
										<select class="form-control select3" data-placeholder="Select Auditee" name="add_auditee" id="add_auditee" style="width: 100%">
											<option value=""></option>
											@foreach($emp2 as $emps2)
											<option value="{{$emps2->employee_id}}">{{$emps2->employee_id}} - {{$emps2->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule Date<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control datepicker" id="add_schedule_date" placeholder="Schedule Date" required readonly="">
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">PIC Cek Efektifitas<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddAuditorEffectivity">
										<select class="form-control select3" data-placeholder="Select PIC Cek Efektifitas" name="select_add_auditor_effectivity" id="select_add_auditor_effectivity" style="width: 100%" >
											<option value=""></option>
											@foreach($emp5 as $emps)
											<option value="{{$emps->employee_id}}">{{$emps->employee_id}} - {{$emps->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js')}}"></script>
<script src="{{ url('js/buttons.flash.min.js')}}"></script>
<script src="{{ url('js/jszip.min.js')}}"></script>
{{-- <script src="{{ url('js/pdfmake.min.js')}}"></script> --}}
<script src="{{ url('js/vfs_fonts.js')}}"></script>
<script src="{{ url('js/buttons.html5.min.js')}}"></script>
<script src="{{ url('js/buttons.print.min.js')}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function changeAddAuditor() {
		$("#add_auditor").val($("#select_add_auditor").val());
	}

	function changeEditAuditor() {
		$("#edit_auditor").val($("#select_edit_auditor").val());
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.datepicker').datepicker({
			autoclose: true,
		    format: "yyyy-mm",
		    todayHighlight: true,
		    startView: "months", 
		    minViewMode: "months",
		    autoclose: true,
		});
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		$('#add_document').select2({
			allowClear:true,
			dropdownParent: $('#divAddDocument'),
		});
		$('#select_add_auditor_effectivity').select2({
			allowClear:true,
			dropdownParent: $('#divAddAuditorEffectivity'),
		});
		$('#select_edit_auditor_effectivity').select2({
			allowClear:true,
			dropdownParent: $('#divEditAuditorEffectivity'),
		});
		$('#select_add_auditor').select2({
			allowClear:true,
			dropdownParent: $('#divAddAuditor'),
		});
		$('#add_auditee').select2({
			allowClear:true,
			dropdownParent: $('#divAddAuditee'),
		});

		$('#edit_document').select2({
			allowClear:true,
			dropdownParent: $('#divEditDocument'),
		});
		$('#select_edit_auditor').select2({
			allowClear:true,
			dropdownParent: $('#divEditAuditor'),
		});
		$('#edit_auditee').select2({
			allowClear:true,
			dropdownParent: $('#divEditAuditee'),
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	var auditors = [];

	function fillData(){
		$('#loading').show();
		
		$.get('{{ url("fetch/qa/special_process/schedule") }}', function(result, status, xhr){
			if(result.status){
				if (result.schedule != null) {
					$('#tableSchedule').DataTable().clear();
					$('#tableSchedule').DataTable().destroy();
					$('#bodyTableSchedule').html("");
					var tableSchedule = "";
					
					var index = 1;

					$.each(result.schedule, function(key, value) {
						tableSchedule += '<tr>';
						tableSchedule += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableSchedule += '<td style="text-align:left;padding-left:7px;">'+value.document_number+'<br>'+value.document_name+'</td>';
						tableSchedule += '<td style="text-align:right;padding-right:7px;">'+value.document_version+'</td>';
						if (value.auditor_id.match(/,/gi)) {
							var auditor_id = value.auditor_id.split(',');
							var auditor_name = value.auditor_name.split(',');
							tableSchedule += '<td style="text-align:left;padding-left:7px;">';
							for(var i = 0; i < auditor_id.length;i++){
								tableSchedule += auditor_id[i]+'<br>'+auditor_name[i]+'<br>';
							}
							tableSchedule += '</td>';
						}else{
							tableSchedule += '<td style="text-align:left;padding-left:7px;">'+value.auditor_id+'<br>'+value.auditor_name+'</td>';
						}
						tableSchedule += '<td style="text-align:left;padding-left:7px;">'+value.auditee_id+'<br>'+value.auditee_name+'</td>';
						tableSchedule += '<td style="text-align:right;padding-right:7px;">'+value.schedule_date+'</td>';
						if (value.schedule_status == 'Sudah Dikerjakan') {
							var color = '#bdffce';
						}else{
							var color = '#ffbdbd';
						}
						tableSchedule += '<td style="text-align:left;padding-left:7px;background-color:'+color+'">'+value.schedule_status+'</td>';
						tableSchedule += '<td style="text-align:left;padding-left:7px;">'+(value.auditor_effectivity_id || '')+' - '+(value.auditor_effectivity_name || '')+'</td>';
						tableSchedule += '<td style="text-align:center"><button class="btn btn-warning btn-sm" onclick="editSchedule(\''+value.id+'\',\''+value.document_number+'\',\''+value.document_name+'\',\''+value.auditor_id+'\',\''+value.auditee_id+'\',\''+value.auditee_name+'\',\''+value.schedule_date+'\',\''+value.schedule_status+'\',\''+value.document_version+'\',\''+value.auditor_effectivity_id+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteSchedule(\''+value.id+'\')"><i class="fa fa-trash"></i></button></td>';
						tableSchedule += '</tr>';

						auditors.push({id:value.id,auditor_id:value.auditor_id});
						index++;
					});
					$('#bodyTableSchedule').append(tableSchedule);

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
						'searching': true,
						"processing": true,
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

				$('#loading').hide();

			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	const hexToDecimal = hex => parseInt(hex, 16);

	function editSchedule(id,document_number,document_name,auditor_id,auditee_id,auditee_name,schedule_date,schedule_status,version,auditor_effectivity_id) {
		$('#id').val(id);
		$('#edit_document').val(document_number).trigger('change');
		$('#edit_version').val(version);
		$('#id').val(id);
		$('#edit_auditee').val(auditee_id).trigger('change');
		$('#edit_auditor').val(auditor_id);
		$('#select_edit_auditor').val(auditor_id.split(',')).trigger('change');
		$('#edit_schedule_date').val(schedule_date.split('-')[0]+'-'+schedule_date.split('-')[1]).trigger('change');
		$('#select_edit_auditor_effectivity').val(auditor_effectivity_id).trigger('change');
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			if ($('#edit_document').val() == '' || $('#edit_version').val() == '' || $('#edit_auditor').val() == '' || $('#edit_auditee').val() == "" || $('#edit_schedule_date').val() == "") {
				openErrorGritter('Error!','Semua Harus Diisi');
				$('#loading').hide();
				return false;
			}
			var data = {
				document:$('#edit_document').val(),
				version:$('#edit_version').val(),
				auditor_id:$('#edit_auditor').val(),
				auditee_id:$('#edit_auditee').val(),
				schedule_date:$('#edit_schedule_date').val(),
				auditor_effectivity_id:$('#select_edit_auditor_effectivity').val(),
				id:$('#id').val()
			}

			$.post('{{ url("update/qa/special_process/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Update Schedule');
					$("#edit-modal").modal('hide');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			if ($('#add_document').val() == '' || $('#add_version').val() == '' || $('#add_auditor').val() == '' || $('#add_auditee').val() == "" || $('#add_schedule_date').val() == "" || $('#select_add_auditor_effectivity').val() == "") {
				openErrorGritter('Error!','Semua Harus Diisi');
				$('#loading').hide();
				return false;
			}
			var data = {
				document:$('#add_document').val(),
				version:$('#add_version').val(),
				auditor_id:$('#add_auditor').val(),
				auditee_id:$('#add_auditee').val(),
				schedule_date:$('#add_schedule_date').val(),
				auditor_effectivity_id:$('#select_add_auditor_effectivity').val(),
			}

			$.post('{{ url("input/qa/special_process/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Tambah Schedule');
					$("#add-modal").modal('hide');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
	}

	function deleteSchedule(id) {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			var data = {
				id:id,
			}

			$.get('{{ url("delete/qa/special_process/schedule") }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Delete Schedule');
					fillData();
				}else{
					$("#loading").hide();
					openErrorGritter('Error',result.message);
					audio_error.play();
					return false;
				}
			})
		}
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection