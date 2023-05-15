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
		<a class="btn btn-info btn-sm pull-right" href="{{url('index/maintenance/tbm/'.$mp_ut)}}">
			<i class="fa fa-bar-chart"></i>&nbsp;&nbsp;Monitoring
		</a>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px;">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point Check
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
							<table id="tablePointCheck" class="table table-bordered table-striped table-hover">
								<thead style="background-color: lightgrey;">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">Category</th>
										<th style="width: 3%">Loc</th>
										<th style="width: 3%">Machine</th>
										<th style="width: 3%">Note</th>
										<th style="width: 2%">Spec</th>
										<th style="width: 2%">Schedule</th>
										<th style="width: 2%">Priority</th>
										<th style="width: 2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTablePointCheck">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_location" placeholder="Location" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Machine</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_point_check" placeholder="Machine" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Note</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_scan_index" placeholder="Note" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Specification</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_specification" placeholder="Specification" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule (Bulan / Hari)</label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="edit_image_reference" placeholder="Schedule (Isi dengan Angka saja)" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Priority</label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="edit_priority" placeholder="Priority" required>
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
						<h2 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Point Check</h2>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_location" placeholder="Location" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Machine</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_point_check" placeholder="Machine" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Note</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_scan_index" placeholder="Note" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Specification</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_specification" placeholder="Specification" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Schedule (Bulan / Hari)</label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="add_image_reference" placeholder="Schedule (Isi dengan Angka saja)" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Priority</label>
									<div class="col-sm-5">
										<input type="number" class="form-control" id="add_priority" placeholder="Priority" required>
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
		// $('.datepicker').datepicker({
		// 	autoclose: true,
		//     format: "yyyy-mm",
		//     todayHighlight: true,
		//     startView: "months", 
		//     minViewMode: "months",
		//     autoclose: true,
		// });
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		// $('#add_document').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divAddDocument'),
		// });
		// $('#select_add_auditor').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divAddAuditor'),
		// });
		// $('#add_auditee').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divAddAuditee'),
		// });

		// $('#edit_document').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divEditDocument'),
		// });
		// $('#select_edit_auditor').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divEditAuditor'),
		// });
		// $('#edit_auditee').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#divEditAuditee'),
		// });
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fillData(){
		$('#loading').show();
		
		$.get('{{ url("fetch/maintenance/point_check/tbm/".$category) }}', function(result, status, xhr){
			if(result.status){
				if (result.point_check != null) {
					$('#tablePointCheck').DataTable().clear();
					$('#tablePointCheck').DataTable().destroy();
					$('#bodyTablePointCheck').html("");
					var tablePointCheck = "";
					
					var index = 1;

					$.each(result.point_check, function(key, value) {
						tablePointCheck += '<tr style="background-color:white">';
						tablePointCheck += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+value.category.toUpperCase()+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.location || '')+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.point_check || '')+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.scan_index || '')+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.specification || '')+'</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.image_reference || '')+' Bulan / Hari</td>';
						tablePointCheck += '<td style="text-align:left;padding-left:7px;">'+(value.priority || '')+'</td>';
						tablePointCheck += '<td style="text-align:center"><button class="btn btn-warning btn-sm" onclick="editPointCheck(\''+value.id+'\',\''+value.category+'\',\''+(value.location || '')+'\',\''+(value.point_check || '')+'\',\''+(value.scan_index || '')+'\',\''+(value.image_reference || '')+'\',\''+(value.specification || '')+'\',\''+(value.priority || '')+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deletePointCheck(\''+value.id+'\')"><i class="fa fa-trash"></i></button></td>';
						tablePointCheck += '</tr>';
						index++;
					});
					$('#bodyTablePointCheck').append(tablePointCheck);

					var table = $('#tablePointCheck').DataTable({
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

	function editPointCheck(id,category,location,point_check,scan_index,image_reference,specification,priority) {
		$('#id').val(id);
		$('#edit_location').val(location);
		$('#edit_point_check').val(point_check);
		$('#edit_scan_index').val(scan_index);
		$('#edit_specification').val(specification);
		$('#edit_image_reference').val(image_reference);
		$('#edit_priority').val(priority);
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			if ($('#edit_image_reference').val() == '' || $('#edit_priority').val() == '') {
				openErrorGritter('Error!','Schedule & Priority');
				$('#loading').hide();
				return false;
			}
			var data = {
				location:$('#edit_location').val(),
				point_check:$('#edit_point_check').val(),
				scan_index:$('#edit_scan_index').val(),
				specification:$('#edit_specification').val(),
				image_reference:$('#edit_image_reference').val(),
				priority:$('#edit_priority').val(),
				id:$('#id').val()
			}

			$.post('{{ url("update/maintenance/point_check/tbm/".$category) }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Update Point Check');
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
			if ($('#add_image_reference').val() == '' || $('#add_priority').val() == '') {
				openErrorGritter('Error!','Schedule & Priority');
				$('#loading').hide();
				return false;
			}
			var data = {
				location:$('#add_location').val(),
				point_check:$('#add_point_check').val(),
				scan_index:$('#add_scan_index').val(),
				specification:$('#add_specification').val(),
				image_reference:$('#add_image_reference').val(),
				priority:$('#add_priority').val(),
				mp_ut:'{{$mp_ut}}',
			}

			$.post('{{ url("input/maintenance/point_check/tbm/".$category) }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Tambah Point Check');
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

	function deletePointCheck(id) {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			var data = {
				id:id,
			}

			$.get('{{ url("delete/maintenance/point_check/tbm/".$category) }}',data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Sukses Delete Point Check');
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