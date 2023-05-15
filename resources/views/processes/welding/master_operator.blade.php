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
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
	}
	tfoot>tr>th{
		/*text-align:center;*/
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
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
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
  /*padding-left: 20px;*/
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

#tableMaster > tbody > tr > td > p > img {
      width: 100px !important;
    }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} ~ {{strtoupper($location)}} <small class="text-purple">{{ $title_jp }}</small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" onclick="cancelAll()" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Operator
		</button>
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
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th style="text-align: center;width: 2%">Tag</th>
										<th style="text-align: center;width: 2%">ID</th>
										<th style="text-align: center;width: 5%">Name</th>
										<th style="text-align: center;width: 1%">Shift</th>
				                        <th style="text-align: center;width: 1%">Remark</th>
				                        <th style="text-align: center;width: 3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableMaster">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Operator</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Employee<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left">
										<select class="form-control select2" data-placeholder="Select Employee" name="edit_employee" id="edit_employee" style="width: 100%">
											<option value=""></option>
											@foreach($emp as $emp)
											<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_tag" placeholder="Scan ID Card di Sini" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Shift<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Shift" name="edit_shift" id="edit_shift" style="width: 100%">
											<option value=""></option>
											<option value="A">A</option>
											<option value="B">B</option>
											<option value="C">C</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Remark<br><span class="text-red">Kosongi jika bukan operator Handatsuke</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Remark" name="edit_remark" id="edit_remark" style="width: 100%">
											<option value=""></option>
											<option value="Handatsuke">Handatsuke</option>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Operator</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Employee<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left">
										<select class="form-control select2" data-placeholder="Select Employee" name="add_employee" id="add_employee" style="width: 100%">
											<option value=""></option>
											@foreach($emp2 as $emp)
											<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_tag" placeholder="Scan ID Card di Sini" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Shift<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Shift" name="add_shift" id="add_shift" style="width: 100%">
											<option value=""></option>
											<option value="A">A</option>
											<option value="B">B</option>
											<option value="C">C</option>
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Remark<br><span class="text-red">Kosongi jika bukan operator Handatsuke</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Remark" name="add_remark" id="add_remark" style="width: 100%">
											<option value=""></option>
											<option value="Handatsuke">Handatsuke</option>
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

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillList();

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

	function fillList(){
		$('#loading').show();

		var data = {
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/welding/operator") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();
				$('#bodyTableMaster').html("");
				var tableData = "";
				var index = 1;
				$.each(result.lists, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.tags +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.employee_id || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.name +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.shift || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.remark || '') +'</td>';
					tableData += '<td style="text-align:center;">';
					tableData += '<button class="btn btn-xs btn-warning" onclick="editOperator(\''+value.id+'\',\''+value.tags+'\',\''+value.employee_id+'\',\''+value.shift+'\',\''+value.remark+'\')"><i class="fa fa-edit"></i></button>';
					tableData += '<button class="btn btn-xs btn-danger" style="margin-left:5px;" onclick="deleteOperator(\''+value.id+'\')"><i class="fa fa-trash"></i></button>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;
				});

				safety = result.safety;
				$('#bodyTableMaster').append(tableData);

				var table = $('#tableMaster').DataTable({
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

	function cancelAll() {
		$('#id').val('');
		$('#edit_employee').val('').trigger('change');
		$('#edit_tag').val('');
		$('#edit_shift').val('').trigger('change');
		$('#edit_remark').val('').trigger('change');

		$('#add_employee').val('').trigger('change');
		$('#add_tag').val('');
		$('#add_shift').val('').trigger('change');
		$('#add_remark').val('').trigger('change');
	}

	function editOperator(id,tags,employee_id,shift,remark) {
		cancelAll();
		$('#id').val(id);
		$('#edit_employee').val(employee_id).trigger('change');
		$('#edit_tag').val(tags);
		$('#edit_shift').val(shift).trigger('change');
		$('#edit_remark').val(remark).trigger('change');
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			if ($('#edit_employee').val() == '' || $('#edit_tag').val() == '' || $('#edit_shift').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!',"Isi Employee, Tag, dan Shift");
				return false;
			}
			var data = {
				id:$('#id').val(),
				employee_id:$('#edit_employee').val(),
				tag:$('#edit_tag').val(),
				shift:$('#edit_shift').val(),
				remark:$('#edit_remark').val(),
			}

			$.post('{{ url("update/welding/operator") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Edit Data');
					$('#loading').hide();
					$('#edit-modal').modal('hide');
					fillList();
					cancelAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			if ($('#add_employee').val() == '' || $('#add_tag').val() == '' || $('#add_shift').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!',"Isi Employee, Tag, dan Shift");
				return false;
			}
			var data = {
				employee_id:$('#add_employee').val(),
				tag:$('#add_tag').val(),
				location:'{{$location}}',
				shift:$('#add_shift').val(),
				remark:$('#add_remark').val(),
			}

			$.post('{{ url("input/welding/operator") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Add Data');
					$('#loading').hide();
					$('#add-modal').modal('hide');
					fillList();
					cancelAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function deleteKanban(id) {
		if (confirm('Apakah Anda yakin akan menghapus data?')) {
			$('#loading').show();
			var data = {
				id:id,
			}

			$.get('{{ url("delete/welding/kanban") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Success Delete Data');
					$('#loading').hide();
					fillList();
					cancelAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}



</script>
@endsection