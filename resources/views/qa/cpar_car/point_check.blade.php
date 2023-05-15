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
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#create_modal" style="margin-right: 5px" onclick="cancelAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Point Check</button>
			
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
							POINT CHECK
						</span>
					</div>
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Claim Title</span>
							<div class="form-group">
								<select class="form-control" name="audit_id" id="audit_id" data-placeholder="Pilih Claim Title" style="width: 100%;">
									<option></option>
									@foreach($audit_id2 as $audit_id2)
									<option value="{{$audit_id2->audit_id}}">{{$audit_id2->audit_title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/cpar_car/') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/cpar_car/point_check/') }}" class="btn btn-danger">Clear</a>
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
							<table id="tablePointCheck" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Audit ID</th>
										<th width="1%">Index</th>
										<th width="2%">Claim Title</th>
										<th width="1%">Periode</th>
										<th width="1%">Email Date</th>
										<th width="1%">Incident Date</th>
										<th width="1%">Origin</th>
										<th width="1%">Dept</th>
										<th width="1%">Area</th>
										<th width="2%">Product</th>
										<th width="3%">Process</th>
										<th width="10%">Point Check</th>
										<th width="1%">Images</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTablePointCheck">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="add_count_point" id="add_count_point" value="1">
								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<label class="col-sm-2">Pilih Claim</label>
										<div class="col-sm-10">
											<div class="col-xs-6" style="text-align: center;">
												<label class="containers">NEW
												  <input type="radio" name="claim_condition" id="claim_condition" value="NEW" onclick="checkCondition(this)">
												  <span class="checkmark"></span>
												</label>
											</div>
											<div class="col-xs-6" style="text-align: center;">
												<label class="containers">EXISTING
												  <input type="radio" name="claim_condition" id="claim_condition" value="EXISTING" onclick="checkCondition(this)">
												  <span class="checkmark"></span>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group row" align="right" style="display: none;" id="div_audit_title_before_all">
										<label class="col-sm-2">Claim Title</label>
										<div class="col-sm-10" align="left" id="div_audit_title_before">
											<select class="form-control" name="add_audit_title_before" id="add_audit_title_before" data-placeholder="Pilih Claim Title" style="width: 100%;">
												<option></option>
												@foreach($audit_id as $audit_id)
												<option value="{{$audit_id->audit_id}}_{{$audit_id->audit_title}}">{{$audit_id->audit_title}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div id="div_audit_title" style="display: none">
										<div class="form-group row" align="right">
											<label class="col-sm-2">Claim Title <span class="text-red">*</span></label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_audit_title" id="add_audit_title" class="form-control" style="width: 100%" placeholder="Input Claim Title">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Periode <span class="text-red">*</span></label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_periode" id="add_periode" class="form-control" style="width: 100%" placeholder="Input Periode (Ex: FY199)">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Email Date</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_email_date" id="add_email_date" class="form-control datepicker" style="width: 100%" placeholder="Input Email Date" readonly="">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Incident Date</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_incident_date" id="add_incident_date" class="form-control datepicker" style="width: 100%" placeholder="Input Incident Date" readonly="">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Origin</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_origin" id="add_origin" class="form-control" style="width: 100%" placeholder="Input Origin (Ex: YMMJ)">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Department</label>
											<div class="col-sm-10" align="left" id="divAddDepartment">
												<select class="form-control" name="add_department" id="add_department" data-placeholder="Pilih Department" style="width: 100%;">
													<option value=""></option>
													@foreach($department as $department)
													<option value="{{$department->department_name}}">{{$department->department_name}}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Area</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_area" id="add_area" class="form-control" style="width: 100%" placeholder="Input Area (Ex: Buffing)">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Process</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_proses" id="add_proses" class="form-control" style="width: 100%" placeholder="Input Process (Ex: Buffing Body Flute)">
											</div>
										</div>
										<div class="form-group row" align="right">
											<label class="col-sm-2">Product</label>
											<div class="col-sm-10" align="left">
												<input type="text" name="add_product" id="add_product" class="form-control" style="width: 100%" placeholder="Input Product (Ex: YAS26)">
											</div>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Add Point</label>
										<div class="col-sm-10" align="left">
											<button class="btn btn-success btn-sm" onclick="addPoint()">
												<i class="fa fa-plus"></i>
											</button>
											&nbsp;Qty Point : <span style="font-weight: bold;" id="qty_point">0</span>
										</div>
									</div>
								</div>
								<div class="row" id="divAddPoint">
									
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Point Check</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<input type="hidden" name="filenames" id="filenames">
								<div class="form-group col-xs-12" align="right">
									<label class="col-sm-2">Point Check</label>
									<div class="col-sm-10" align="left">
										<textarea id="edit_audit_point" style="width: 100%"></textarea>
									</div>
								</div>
								<div class="form-group col-xs-12" align="right">
									<label class="col-sm-2">Audit Images</label>
									<div class="col-sm-10" align="left">
										<input id="edit_audit_images" type="file" class="form-control" style="width: 100%">
									</div>
									<label class="col-sm-2"></label>
									<div class="col-sm-10" align="left" id="audit_images_before">
										
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

	var safety = null;

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
		CKEDITOR.replace('edit_audit_point' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('body').toggleClass("sidebar-collapse");

		$('#add_count_point').val(1);

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		$('#add_audit_title_before').select2({
			allowClear:true,
			dropdownParent: $('#div_audit_title_before'),
		});

		$('#audit_id').select2({
			allowClear:true,
		});

		$('#add_department').select2({
			allowClear:true,
			dropdownParent: $('#divAddDepartment'),
		});

		// $('#add_department').select2({
		// 	allowClear:true,
		// 	dropdownParent: $('#div_audit_title'),
		// });

		fillList();
	});

	function checkCondition(param) {
		$("#div_audit_title").hide();
		$("#div_audit_title_before_all").hide();
		if (param.value == 'NEW') {
			$("#div_audit_title").show();
		}else{
			$("#div_audit_title_before_all").show();
		}
	}

	function cancelAll() {
		$('#divAddPoint').html('');
		$("#qty_point").html(0);
		$('#add_count_point').val(1);
		$("#add_audit_title_before").val('').trigger('change');
		$("#add_audit_title").val('');
		$("#add_periode").val('');
		$("#add_email_date").val('');
		$("#add_incident_date").val('');
		$("#add_origin").val('');
		$("#add_department").val('').trigger('change');
		$("#add_area").val('');
		$("#add_proses").val('');
		$("#add_product").val('');
	}

	function addPoint() {
		var id = $('#add_count_point').val();
		var points = '';
		points += '<div class="col-xs-12 point_check_class" id="divPoint_'+id+'" style="border: 1px solid black;padding-left: 0px;padding-right: 0px;margin-bottom:10px;">';
			points += '<div class="col-xs-10" style="background-color: orange;text-align: center;padding-left: 0px;padding-right: 0px;margin-bottom: 5px;color: white;height:34px;">';
				points += '<span id="divTitle" style="font-weight: bold;padding: 10px;font-size:20px;">Point Check</span>';
			points += '</div>';
			points += '<div class="col-xs-2" style="text-align: center;padding-left: 2px;padding-right: 0px;margin-bottom: 5px;">';
				points += '<button class="btn btn-danger" style="width:100%" onclick="minus(\''+id+'\')"><i class="fa fa-minus"></i>';
				points += '</button>';
			points += '</div>';
			points += '<div class="form-group col-xs-12" align="right">';
				points += '<label class="col-sm-2">Point Check</label>';
				points += '<div class="col-sm-10" align="left">';
					points += '<textarea id="add_audit_point_'+id+'" style="width: 100%"></textarea>';
				points += '</div>';
			points += '</div>';
			points += '<div class="form-group col-xs-12" align="right">';
				points += '<label class="col-sm-2">Add Audit Images</label>';
				points += '<div class="col-sm-10" align="left">';
					points += '<input id="audit_images_'+id+'" type="file" class="form-control" style="width: 100%">';
				points += '</div>';
			points += '</div>';
		points += '</div>';

		$('#divAddPoint').append(points);

		CKEDITOR.replace('add_audit_point_'+id ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

		$('#add_count_point').val(parseInt(id)+1);

		$('#qty_point').html($('.point_check_class').length);
	}

	function minus(id) {
		$('#divPoint_'+id).remove();
		$('#qty_point').html($('.point_check_class').length);
	}


	$(function () {
		// $('.select2').select2({
		// 	allowClear:true,
		// });
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
			audit_id:$('#audit_id').val(),		}
		$.get('{{ url("fetch/qa/cpar_car/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tablePointCheck').DataTable().clear();
				$('#tablePointCheck').DataTable().destroy();
				$('#bodyTablePointCheck').html("");
				var tableData = "";
				var index = 1;

				$.each(result.point_check, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ index +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.audit_id +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.audit_index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.audit_title +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.periode +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.email_date +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.incident_date +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.origin +'</td>';
					var department_shortname = '';
					for(var i = 0; i < result.department.length;i++){
						if (value.department == result.department[i].department_name) {
							department_shortname = result.department[i].department_shortname;
						}
					}
					tableData += '<td style="text-align:left;padding-left:7px;">'+ department_shortname +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.area +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.product +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.proses +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.audit_point +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">';
					if (value.audit_images != null) {
						var url = '{{url("data_file/qa/ng_jelas_point/")}}/'+value.audit_images;
						tableData += '<a href="'+url+'" target="_blank" style="cursor:pointer"><img src="'+url+'" style="width:100px"></a>';
					}
					tableData += '</td>';
					
					tableData += '<td><button class="btn btn-sm btn-warning" onclick="editPointCheck(\''+value.id+'\')">Edit</button><button class="btn btn-sm btn-danger" style="margin-left:5px;" onclick="deletePointCheck(\''+value.id+'\')">Delete</button></td>';
					index++;
				});
				$('#bodyTablePointCheck').append(tableData);

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

	function deletePointCheck(id) {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("delete/qa/cpar_car/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!','Delete Data Succeeded');
					$('#loading').hide();
					fillList();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
					audio_error.play();
					return false;
				}
			});
		}
	}

	function editPointCheck(id) {
		$('#loading').show();
		var data = {
			id:id
		}

		$.get('{{ url("edit/qa/cpar_car/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#id').val(result.audit.id);
				$("#edit_audit_point").html(CKEDITOR.instances.edit_audit_point.setData(result.audit.audit_point));
				$('#audit_images_before').html('');
				if (result.audit.audit_images != null) {
					var url = '{{url("data_file/qa/ng_jelas_point")}}/'+result.audit.audit_images;
					$('#audit_images_before').append('<img src="'+url+'" style="width:100px">');
				}
				$('#filenames').val(result.audit.audit_images);
				$('#edit_modal').modal('show');
				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var fileData  = $('#edit_audit_images').prop('files')[0];

			file=$('#edit_audit_images').val().replace(/C:\\fakepath\\/i, '').split(".");

			var audit_point = CKEDITOR.instances['edit_audit_point'].getData();

			var id = $('#id').val();
			var filenames = $('#filenames').val();

			var formData = new FormData();
			formData.append('fileData', fileData);
			formData.append('id',id);
			formData.append('audit_point',audit_point);
			formData.append('filenames',filenames);
			formData.append('extension', file[1]);

			$.ajax({
				url:"{{ url('update/qa/cpar_car/point_check') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status == false) {
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
					}else if(data.status == true){
						$("#edit_audit_point").html(CKEDITOR.instances.edit_audit_point.setData(''));
						$('#edit_audit_images').val('');
						$('#loading').hide();
						openSuccessGritter('Success!','Save Data Success');
						fillList();
						$('#edit_modal').modal('hide');
					}
				},
				error: function(data) {
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					return false;
				}
			});
		}
	}

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			var claim_condition = '';
			$("input[name='claim_condition']:checked").each(function (i) {
				claim_condition = $(this).val();
	        });
	        if (claim_condition == 'NEW') {
	        	var audit_title = $('#add_audit_title').val();
				var periode = $('#add_periode').val();
				var email_date = $('#add_email_date').val();
				var incident_date = $('#add_incident_date').val();
				var origin = $('#add_origin').val();
				var department = $('#add_department').val();
				var area = $('#add_area').val();
				var proses = $('#add_proses').val();
				var product = $('#add_product').val();

	        	var stat = 0;

	        	for(var i = 0; i < parseInt($('#add_count_point').val());i++){
					if ($('#divPoint_'+i).text() != '') {

						var fileData  = $('#audit_images_'+i).prop('files')[0];

						file=$('#audit_images_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");

						var audit_point = CKEDITOR.instances['add_audit_point_'+i].getData();

						var formData = new FormData();
						formData.append('fileData', fileData);
						formData.append('audit_title',audit_title);
						formData.append('periode',periode);
						formData.append('email_date',email_date);
						formData.append('incident_date',incident_date);
						formData.append('origin',origin);
						formData.append('department',department);
						formData.append('area',area);
						formData.append('proses',proses);
						formData.append('product',product);
						formData.append('audit_point',audit_point);
						formData.append('claim_condition', claim_condition);
						formData.append('extension', file[1]);

						$.ajax({
							url:"{{ url('input/qa/cpar_car/point_check') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								if (data.status == false) {
									$('#loading').hide();
									openErrorGritter('Error!',data.message);
								}else if(data.status == true){
									stat++;
								}
								if (stat == parseInt($('#qty_point').text())) {
									$('#loading').hide();
									openSuccessGritter('Success!','Save Data Success');
									fillList();
									$('#create_modal').modal('hide');
								}
							},
							error: function(data) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
								return false;
							}
						});
					}
				}
	        }else{
	        	var audit_id = $('#add_audit_title_before').val().split('_')[0];
	        	var audit_title = $('#add_audit_title_before').val().split('_')[1];

	        	var stat = 0;

	        	for(var i = 0; i < parseInt($('#add_count_point').val());i++){
					if ($('#divPoint_'+i).text() != '') {

						var fileData  = $('#audit_images_'+i).prop('files')[0];

						file=$('#audit_images_'+i).val().replace(/C:\\fakepath\\/i, '').split(".");

						var audit_point = CKEDITOR.instances['add_audit_point_'+i].getData();

						var formData = new FormData();
						formData.append('fileData', fileData);
						formData.append('audit_id', audit_id);
						formData.append('audit_title', audit_title);
						formData.append('audit_point', audit_point);
						formData.append('claim_condition', claim_condition);
						formData.append('extension', file[1]);

						$.ajax({
							url:"{{ url('input/qa/cpar_car/point_check') }}",
							method:"POST",
							data:formData,
							dataType:'JSON',
							contentType: false,
							cache: false,
							processData: false,
							success:function(data)
							{
								if (data.status == false) {
									$('#loading').hide();
									openErrorGritter('Error!',data.message);
								}else if(data.status == true){
									stat++;
								}
								if (stat == parseInt($('#qty_point').text())) {
									$('#loading').hide();
									openSuccessGritter('Success!','Save Data Success');
									fillList();
									$('#create_modal').modal('hide');
								}
							},
							error: function(data) {
								$('#loading').hide();
								openErrorGritter('Error!',data.message);
								return false;
							}
						});
					}
				}
	        }
		}
	}


</script>
@endsection