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
					<div style="text-align: center;background-color: lightgreen;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: black;font-size: 20px">
							MASTER DATA MATERIAL
						</span>
					</div>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="col-xs-4" style="padding-right:0px;margin-top: 10px;">
								<span style="font-weight: bold;">Work Station</span>
								<div class="form-group">
									<select class="form-control select2" name="work_station" id="work_station" data-placeholder="Pilih WS" style="width: 100%;">
										<option></option>
										@foreach($work_station as $work_station)
										<option value="{{$work_station->work_station}}">{{$work_station->work_station}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-4" style="padding-right:0px;padding-left:5px;margin-top: 10px;">
								<span style="font-weight: bold;">Category</span>
								<div class="form-group">
									<select class="form-control select2" name="material_category" id="material_category" data-placeholder="Pilih Category" style="width: 100%;">
										<option></option>
										@foreach($material_category as $material_category)
										<option value="{{$material_category->material_category}}">{{$material_category->material_category}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-4" style="padding-left:5px;margin-top: 10px;">
								<span style="font-weight: bold;">Type</span>
								<div class="form-group">
									<select class="form-control select2" name="material_type" id="material_type" data-placeholder="Pilih Type" style="width: 100%;">
										<option></option>
										@foreach($material_type as $material_type)
										<option value="{{$material_type->material_type}}">{{$material_type->material_type}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group pull-right">
								<a href="#" onclick="history.back()" class="btn btn-warning">Back</a>
								<a href="{{ url('index/body_parts_process/master_material/'.$location) }}" class="btn btn-danger">Clear</a>
								<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
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
							<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th style="text-align: center;width: 10%">Material</th>
										<th style="text-align: center;width: 2%">Alias</th>
										<th style="text-align: center;width: 1%">WS</th>
										<th style="text-align: center;width: 1%">Qty</th>
				                        <th style="text-align: center;width: 1%">Category</th>
				                        <th style="text-align: center;width: 1%">Type</th>
				                        <th style="text-align: center;width: 1%">Std Time</th>
				                        <th style="text-align: center;width: 3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableMaster">
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

	{{-- edit modal --}}
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Material</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<input type="hidden" name="id" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditMaterial">
										<select class="form-control select2" data-placeholder="Select Material" name="edit_material" id="edit_material" style="width: 100%">
											<option value=""></option>
											@foreach($materials as $materials)
											<option value="{{$materials->material_number}}">{{$materials->material_number}} - {{$materials->material_description}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Alias<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_material_alias" placeholder="Material Alias (Nama Pendek)" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Work Station<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditWorkStation">
										<select class="form-control select2" data-placeholder="Select WS" name="edit_work_station" id="edit_work_station" style="width: 100%">
											<option value=""></option>
											@foreach($ws as $ws)
											<option value="{{$ws->work_station}}">{{$ws->work_station}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="number" class="form-control numpad" id="edit_quantity" placeholder="Input Quantity" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Category<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditCategory">
										<select class="form-control select2" data-placeholder="Select Category" name="edit_material_category" id="edit_material_category" style="width: 100%">
											<option value=""></option>
											@foreach($category as $category)
											<option value="{{$category}}">{{$category}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Type<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditType">
										<select class="form-control select2" data-placeholder="Select Type" name="edit_material_type" id="edit_material_type" style="width: 100%">
											<option value=""></option>
											@foreach($type as $type)
											<option value="{{$type}}">{{$type}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Standard Time<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="number" class="form-control numpad" id="edit_standard_time" placeholder="Standard Time" required>
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
			material_category:$('#material_category').val(),
			work_station:$('#work_station').val(),
			material_type:$('#material_type').val(),
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/body_parts_process/master_material") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();
				$('#bodyTableMaster').html("");
				var tableData = "";
				var index = 1;
				$.each(result.material, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.material_number +' - '+ value.material_description +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_alias || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.work_station +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ (value.quantity || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_category || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_type || '') +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ (value.standard_time || '') +'</td>';
					tableData += '<td style="text-align:center;">';
					tableData += '<button class="btn btn-xs btn-warning" onclick="editMaterial(\''+value.id+'\',\''+value.material_number+'\',\''+value.material_alias+'\',\''+value.work_station+'\',\''+value.material_category+'\',\''+value.material_type+'\',\''+value.standard_time+'\',\''+value.quantity+'\')"><i class="fa fa-edit"></i></button>';
					// tableData += '<button class="btn btn-xs btn-danger" style="margin-left:5px;" onclick="deleteMaterial(\''+value.id+'\')"><i class="fa fa-trash"></i></button>';
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
					[ 20, 25, 50, -1 ],
					[ '20 rows', '25 rows', '50 rows', 'Show all' ]
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
					'pageLength': 20,
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
		$('#edit_material').val('').trigger('change');
		$('#edit_material_alias').val('');
		$('#edit_quantity').val('');
		$('#edit_work_station').val('').trigger('change');
		$('#edit_material_category').val('').trigger('change');
		$('#edit_material_type').val('').trigger('change');
		$('#edit_standard_time').val('');
	}

	function editMaterial(id,material_number,material_alias,work_station,material_category,material_type,standard_time,quantity) {		
		console.log(id,material_number,material_alias,work_station,material_category,material_type,standard_time,quantity);
		cancelAll();
		$('#id').val(id);
		$('#edit_material').val(material_number).trigger('change');
		if (material_alias != 'null') {
			$('#edit_material_alias').val(material_alias);
		}
		$('#edit_quantity').val(quantity);
		$('#edit_work_station').val(work_station).trigger('change');
		$('#edit_material_category').val(material_category).trigger('change');
		$('#edit_material_type').val(material_type).trigger('change');
		$('#edit_standard_time').val(standard_time);
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var data = {
				id:$('#id').val(),
				material:$('#edit_material').val(),
				material_alias:$('#edit_material_alias').val(),
				quantity:$('#edit_quantity').val(),
				work_station:$('#edit_work_station').val(),
				material_category:$('#edit_material_category').val(),
				material_type:$('#edit_material_type').val(),
				standard_time:$('#edit_standard_time').val(),
			}

			$.post('{{ url("update/body_parts_process/master_material") }}',data, function(result, status, xhr){
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



</script>
@endsection