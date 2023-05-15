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
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Product</span>
							<div class="form-group">
								<select class="form-control select2" name="product" id="product" data-placeholder="Pilih Product" style="width: 100%;">
									<option></option>
									@foreach($product as $prod)
									<option value="{{$prod->origin_group_name}}">{{$prod->origin_group_name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Material</span>
							<div class="form-group">
								<select class="form-control select2" name="select_material" id="select_material" data-placeholder="Pilih Material" style="width: 100%;">
									<option></option>
									@foreach($material as $mat)
									<option value="{{$mat->material_number}}_{{$mat->material_description}}">{{$mat->material_number}} - {{$mat->material_description}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/qa/packing/') }}" class="btn btn-warning">Back</a>
									<a href="{{ url('index/qa/packing/point_check/') }}" class="btn btn-danger">Clear</a>
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
										<th width="1%">Product</th>
										<th width="2%">Material</th>
										<th width="1%">Urutan</th>
										<th width="4%">Point Check</th>
										<th width="4%">Standard</th>
										<th width="2%">Point Check Type</th>
										<th width="2%">Point Check Details</th>
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
										<label class="col-sm-2">Product <span class="text-red">*</span></label>
										<div class="col-sm-10" align="left" id="divAddProduct">
											<select class="form-control" name="add_product" id="add_product" data-placeholder="Pilih Product" style="width: 100%;">
												<option value=""></option>
												@foreach($product2 as $prod)
												<option value="{{$prod->origin_group_name}}">{{$prod->origin_group_name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Material <span class="text-red">*</span></label>
										<div class="col-sm-10" align="left" id="divAddMaterial">
											<select class="form-control" name="add_material" id="add_material" data-placeholder="Pilih Material" style="width: 100%;" multiple="" onchange="changeMaterial()">
												<option value=""></option>
												@foreach($material2 as $mat)
												<option value="{{$mat->material_number}}_{{$mat->material_description}}">{{$mat->material_number}} - {{$mat->material_description}}</option>
												@endforeach
											</select>
											<input type="hidden" name="material" id="material">
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

	<div class="modal modal-default fade" id="edit_modal">
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
								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<input type="hidden" name="id" id="id">
										<label class="col-sm-2">Product <span class="text-red">*</span></label>
										<div class="col-sm-10" align="left" id="divEditProduct">
											<select class="form-control" name="edit_product" id="edit_product" data-placeholder="Pilih Product" style="width: 100%;" disabled="">
												<option value=""></option>
												@foreach($product3 as $prod)
												<option value="{{$prod->origin_group_name}}">{{$prod->origin_group_name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Material <span class="text-red">*</span></label>
										<div class="col-sm-10" align="left" id="divEditMaterial">
											<select class="form-control" name="select_edit_material" id="select_edit_material" data-placeholder="Pilih Material" style="width: 100%;" multiple="" onchange="changeMaterialEdit()" disabled="">
												<option value=""></option>
												@foreach($material3 as $mat)
												<option value="{{$mat->material_number}}_{{$mat->material_description}}">{{$mat->material_number}} - {{$mat->material_description}}</option>
												@endforeach
											</select>
											<input type="hidden" name="edit_material" id="edit_material">
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Point Check</label>
										<div class="col-sm-10" align="left">
											<textarea id="edit_point_check" style="width: 100%"></textarea>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Standard</label>
										<div class="col-sm-10" align="left">
											<textarea id="edit_standard" style="width: 100%"></textarea>
										</div>
									</div>
									<div class="form-group row" align="right">
										<label class="col-sm-2">Point Check Type</label>
										<div class="col-sm-10" align="left" id="divPointCheckType">
											<select class="form-control" name="edit_point_check_type" id="edit_point_check_type" data-placeholder="Pilih Point Check Type" style="width: 100%;" onchange="changePointTypeEdit(this.value)">
												<option></option>
												<option value="checklist">Pilihan (OK / NG)</option>
												<option value="input">Input</option>
											</select>
										</div>
									</div>
									<div class="form-group row" align="right" style="display: none" id="divDetails">
										<label class="col-sm-2">Add Details</label>
										<div class="col-sm-10" align="left" id="divPointCheckDetails">
											<select class="form-control" name="edit_point_check_details" id="edit_point_check_details" data-placeholder="Pilih Point Check Type" style="width: 100%;" multiple="" onchange="changePointDetailsEdit(this.value)">
												<option></option>
											</select>
											<input id="point_check_details" type="hidden" style="width: 100%">
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
	});

	function cancelAll() {
		$('#divAddPoint').html('');
		$("#qty_point").html(0);
		$('#add_count_point').val(1);
		$("#add_material").val([]).trigger('change');
		$("#add_product").val('').trigger('change');
		$("#edit_product").val('').trigger('change');
		$("#edit_material").val([]).trigger('change');
		$("#edit_point_check_type").val('').trigger('change');
		$("#edit_point_check_details").val([]).trigger('change');
		$("#edit_point_check").html(CKEDITOR.instances.edit_point_check.setData(''));
		$("#edit_standard").html(CKEDITOR.instances.edit_standard.setData(''));
	}

	function changePointDetails(id,type) {
		$('#point_check_details_'+id).val($('#add_point_check_details_'+id).val());
	}

	function changePointDetailsEdit(type) {
		$('#point_check_details').val($('#edit_point_check_details').val());
	}

	function changeMaterial() {
		$('#material').val($('#add_material').val());
	}

	function changeMaterialEdit() {
		$('#edit_material').val($('#select_edit_material').val());
	}

	function changePointType(id,type) {
		if (type == 'input') {
			$('#divDetails_'+id).show();
		}else{
			$('#divDetails_'+id).hide();
			$('#add_point_check_details_'+id).empty();
			$('#point_check_details_'+id).val('');
		}
	}
	function changePointTypeEdit(type) {
		if (type == 'input') {
			$('#divDetails').show();
		}else{
			$('#divDetails').hide();
			$('#edit_point_check_details').empty();
			$('#point_check_details').val('');
		}
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
					points += '<textarea id="add_point_check_'+id+'" style="width: 100%"></textarea>';
				points += '</div>';
			points += '</div>';
			points += '<div class="form-group col-xs-12" align="right">';
				points += '<label class="col-sm-2">Standard</label>';
				points += '<div class="col-sm-10" align="left">';
					points += '<textarea id="add_standard_'+id+'" style="width: 100%"></textarea>';
				points += '</div>';
			points += '</div>';
			points += '<div class="form-group col-xs-12" align="right">';
				points += '<label class="col-sm-2">Point Check Type</label>';
				points += '<div class="col-sm-10" align="left" id="divPointCheckType_'+id+'">';
					points += '<select class="form-control" name="add_point_check_type_'+id+'" id="add_point_check_type_'+id+'" data-placeholder="Pilih Point Check Type" style="width: 100%;" onchange="changePointType(\''+id+'\',this.value)">';
						points += '<option></option>';
						points += '<option value="checklist">Pilihan (OK / NG)</option>';
						points += '<option value="input">Input</option>';
					points += '</select>';
				points += '</div>';
			points += '</div>';
			points += '<div class="form-group col-xs-12" align="right" style="display: none" id="divDetails_'+id+'">';
				points += '<label class="col-sm-2">Add Details</label>';
				points += '<div class="col-sm-10" align="left" id="divPointCheckDetails_'+id+'">';
					points += '<select class="form-control" name="add_point_check_details_'+id+'" id="add_point_check_details_'+id+'" data-placeholder="Pilih Point Check Type" style="width: 100%;" multiple="" onchange="changePointDetails(\''+id+'\',this.value)">';
						points += '<option></option>';
					points += '</select>';
					points += '<input id="point_check_details_'+id+'" type="hidden" style="width: 100%">';
				points += '</div>';
			points += '</div>';
		points += '</div>';

		$('#divAddPoint').append(points);

		CKEDITOR.replace('add_point_check_'+id ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_standard_'+id ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    $('#add_point_check_type_'+id).select2({
			allowClear:true,
			dropdownParent: $('#divPointCheckType_'+id),
		});

		$('#add_point_check_details_'+id).select2({
			allowClear:true,
			dropdownParent: $('#divPointCheckDetails_'+id),
			tags: true,
		});

		$('#add_count_point').val(parseInt(id)+1);

		$('#qty_point').html($('.point_check_class').length);
	}

	function minus(id) {
		$('#divPoint_'+id).remove();
		$('#qty_point').html($('.point_check_class').length);
	}


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
		$('#add_product').select2({
			allowClear:true,
			dropdownParent: $('#divAddProduct'),
		});
		$('#add_material').select2({
			allowClear:true,
			dropdownParent: $('#divAddMaterial'),
		});
		$('#edit_product').select2({
			allowClear:true,
			dropdownParent: $('#divEditProduct'),
		});
		$('#select_edit_material').select2({
			allowClear:true,
			dropdownParent: $('#divEditMaterial'),
		});

		CKEDITOR.replace('edit_point_check' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_standard' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    $('#edit_point_check_type').select2({
			allowClear:true,
			dropdownParent: $('#divPointCheckType'),
		});

		$('#edit_point_check_details').select2({
			allowClear:true,
			dropdownParent: $('#divPointCheckDetails'),
			tags: true,
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
			product:$('#product').val(),
			material_number:$('#select_material').val().split('_')[0],
		}
		$.get('{{ url("fetch/qa/packing/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tablePointCheck').DataTable().clear();
				$('#tablePointCheck').DataTable().destroy();
				$('#bodyTablePointCheck').html("");
				var tableData = "";
				var index = 1;

				$.each(result.point_check, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.product +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">';
					if (value.material_number.match(/,/gi)) {
						var material_number = value.material_number.split(',');
						var material_description = value.material_description.split(',');
						for(var i = 0; i < material_number.length;i++){
							tableData += material_number[i]+' - '+material_description[i]+'<br>';
						}
					}else{
						tableData += value.material_number+' - '+value.material_description;
					}
					tableData += '</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.ordering +'</td>';
					tableData += '<td>'+ value.point_check +'</td>';
					tableData += '<td>'+ value.standard +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.point_check_type +'</td>';
					if (value.point_check_details != null) {
						if (value.point_check_details.match(/,/gi)) {
							tableData += '<td style="text-align:left;padding-left:7px;">'+ value.point_check_details.split(',').join('<br>') +'</td>';
						}else{
							tableData += '<td style="text-align:left;padding-left:7px;">'+ value.point_check_details +'</td>';
						}
					}else{
						tableData += '<td style="text-align:left;padding-left:7px;"></td>';
					}
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

	function add() {
		if (confirm('Apakah Anda yakin?')) {
			$("#loading").show();
			var product = $('#add_product').val();
			var material = $('#material').val();
			var qty_point = $('#qty_point').text();

			var point_check = [];
			var standard = [];
			var point_check_type = [];
			var point_check_details = [];

			for(var i = 1; i <= parseInt(qty_point);i++){
				if ($('#divPoint_'+i).text() != '') {
					point_check.push(CKEDITOR.instances['add_point_check_'+i].getData());
					standard.push(CKEDITOR.instances['add_standard_'+i].getData());
					point_check_type.push($('#add_point_check_type_'+i).val());
					point_check_details.push($('#point_check_details_'+i).val());
				}
			}

			var data = {
				product:product,
				material:material,
				qty_point:qty_point,
				point_check:point_check,
				standard:standard,
				point_check_type:point_check_type,
				point_check_details:point_check_details,
			}

			$.post('{{ url("input/qa/packing/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','Add Point Check Succeeded');
					$('#create_modal').modal('hide');
					fillList();
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
					audio_error.play();
					return false;					
				}
			})
			
		}
	}

	function editPointCheck(id) {
		cancelAll();
		$('#loading').show();
		var data = {
			id:id
		}

		$.get('{{ url("edit/qa/packing/point_check") }}',data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				$('#edit_modal').modal('show');
				$('#id').val(id);
				$('#edit_product').val(result.point.product).trigger('change');
				var material = [];
				if (result.point.material_number.match(/,/gi)) {
					var material_number = result.point.material_number.split(',');
					var material_description = result.point.material_description.split(',');
					for(var i = 0; i < material_number.length;i++){
						material.push(material_number[i]+'_'+material_description[i]);
					}
				}else{
					material.push(result.point.material_number+'_'+result.point.material_description);
				}
				$('#select_edit_material').val(material).trigger('change');
				$('#edit_material').val(material.join(','));
				$("#edit_point_check").html(CKEDITOR.instances.edit_point_check.setData(result.point.point_check));
				$("#edit_standard").html(CKEDITOR.instances.edit_standard.setData(result.point.standard));
				$('#edit_point_check_type').val(result.point.point_check_type).trigger('change');
				if (result.point.point_check_details != null) {
					var point_check_details = result.point.point_check_details.split(',');
					var detail = '';
					for(var i = 0; i < point_check_details.length;i++){
						detail += '<option value="'+point_check_details[i]+'">'+point_check_details[i]+'</option>';
					}
					$('#edit_point_check_details').append(detail);
					$('#edit_point_check_details').val(point_check_details).trigger('change');
				}
				$('#point_check_details').val(result.point.point_check_details);
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				$('#loading').hide();
			}
		});
	}

	function deletePointCheck(id) {
		if (confirm('Apakah Anda yakin akan menghapus Point Check?')) {
			$('#loading').show();
			var data = {
				id:id
			}

			$.get('{{ url("delete/qa/packing/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fillList();
					openSuccessGritter('Success','Sukses Menghapus Point Check');
				}else{
					openErrorGritter('Error!',result.message);
					audio_error.play();
					$('#loading').hide();
				}
			});
		}
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var id = $("#id").val();
			var point_check = CKEDITOR.instances['edit_point_check'].getData();
			var standard = CKEDITOR.instances['edit_standard'].getData();
			var point_check_type = $('#edit_point_check_type').val();
			var point_check_details = $('#point_check_details').val();

			var data = {
				id:id,
				point_check:point_check,
				standard:standard,
				point_check_type:point_check_type,
				point_check_details:point_check_details,
			}

			$.post('{{ url("update/qa/packing/point_check") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','Update Point Check Succeeded');
					$('#edit_modal').modal('hide');
					fillList();
					$('#loading').hide();
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
					audio_error.play();
					return false;					
				}
			})
		}
	}

</script>
@endsection