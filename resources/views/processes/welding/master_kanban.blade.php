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
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Kanban
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
				<div class="box-body">
					<div style="text-align: center;background-color: lightskyblue;margin-bottom: 20px">
						<span style="padding: 15px;font-weight: bold;color: black;font-size: 20px">
							MASTER DATA KANBAN
						</span>
					</div>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="col-xs-4" style="padding-right:0px;margin-top: 10px;">
								<span style="font-weight: bold;">Material</span>
								<div class="form-group">
									<select class="form-control select2" name="material" id="material" data-placeholder="Pilih Material" style="width: 100%;">
										<option></option>
										@foreach($material as $material)
										<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
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
								<a href="{{ url('index/process_welding_sx/') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/welding/master_kanban/'.$location) }}" class="btn btn-danger">Clear</a>
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
										<th style="text-align: center;width: 2%">Tag</th>
										<th style="text-align: center;width: 1%">No. Kanban</th>
										<th style="text-align: center;width: 1%">Barcode</th>
										<th style="text-align: center;width: 10%">Material</th>
				                        <th style="text-align: center;width: 1%">Category</th>
				                        <th style="text-align: center;width: 1%">Type</th>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Kanban</h1>
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
									<div class="col-sm-7" align="left" id="divEditMaterial">
										<select class="form-control select2" data-placeholder="Select Material" name="edit_material" id="edit_material" style="width: 100%">
											<option value=""></option>
											@foreach($material2 as $material2)
											<option value="{{$material2->material_number}}_{{$material2->material_category}}_{{$material2->material_type}}">{{$material2->material_number}} - {{$material2->material_description}} ({{$material2->material_category}} - {{$material2->material_type}})</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_tag" placeholder="Scan Kanban di Sini" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">No. Kanban<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="number" class="form-control numpad" id="edit_no_kanban" placeholder="No. Kanban" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Barcode</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="edit_barcode" placeholder="Scan Barcode di Sini" required>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add Kanban</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-8">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-7" align="left" id="divAddMaterial">
										<select class="form-control select2" data-placeholder="Select Material" name="add_material" id="add_material" style="width: 100%" onchange="getNoKanban()">
											<option value=""></option>
											@foreach($material3 as $material3)
											<option value="{{$material3->material_number}}_{{$material3->material_category}}_{{$material3->material_type}}">{{$material3->material_number}} - {{$material3->material_description}} ({{$material3->material_category}} - {{$material3->material_type}})</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_tag" placeholder="Scan Kanban di Sini" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">No. Kanban<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="number" class="form-control numpad" id="add_no_kanban" placeholder="No. Kanban" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Barcode</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_barcode" placeholder="Scan Barcode di Sini" required>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-2">
							<table id="tableNoKanban" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">No. Kanban</th>
										<th width="1%">Barcode</th>
									</tr>
								</thead>
								<tbody id="bodyNoKanban">
									
								</tbody>
							</table>
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
	var all_kanban = [];
	function fillList(){
		$('#loading').show();

		var data = {
			material_category:$('#material_category').val(),
			material:$('#material').val(),
			material_type:$('#material_type').val(),
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/welding/kanban") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableMaster').DataTable().clear();
				$('#tableMaster').DataTable().destroy();
				$('#bodyTableMaster').html("");
				var tableData = "";
				var index = 1;
				$.each(result.kanban, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:center;">'+ index +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ value.tags +'</td>';
					tableData += '<td style="text-align:right;padding-right:7px;">'+ (value.no_kanban || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.barcode || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ value.material_number +' - '+ value.material_description +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_category || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:7px;">'+ (value.material_type || '') +'</td>';
					tableData += '<td style="text-align:center;">';
					tableData += '<button class="btn btn-xs btn-warning" onclick="editKanban(\''+value.id+'\',\''+value.material_number+'\',\''+value.tags+'\',\''+value.barcode+'\',\''+value.material_category+'\',\''+value.material_type+'\',\''+value.no_kanban+'\')"><i class="fa fa-edit"></i></button>';
					tableData += '<button class="btn btn-xs btn-danger" style="margin-left:5px;" onclick="deleteKanban(\''+value.id+'\')"><i class="fa fa-trash"></i></button>';
					tableData += '</td>';
					tableData += '</tr>';
					index++;

					all_kanban.push({
						material_number:value.material_number,
						material_category:value.material_category,
						material_type:value.material_type,
						barcode:value.barcode,
						no_kanban:value.no_kanban,
					});
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

	function getNoKanban() {
		$('#bodyNoKanban').html('');
		var bodyNo = '';

		var material = $('#add_material').val();

		var no_kanban = [];
		var barcode = [];
		for(var i = 0; i < all_kanban.length;i++){
			if (all_kanban[i].material_number == material.split('_')[0] && all_kanban[i].material_category == material.split('_')[1] && all_kanban[i].material_type == material.split('_')[2]) {
				no_kanban.push(all_kanban[i].no_kanban);
				barcode.push(all_kanban[i].barcode);
			}
		}

		var last_no = 0;

		if (no_kanban.length > 0) {
			for(var i = 0; i < no_kanban.length;i++){
				bodyNo += '<tr>';
				bodyNo += '<td style="text-align:center;">'+ no_kanban[i] +'</td>';
				bodyNo += '<td style="text-align:center;">'+ barcode[i] +'</td>';
				bodyNo += '</tr>';
				last_no = no_kanban[i];
			}
		}

		$('#add_no_kanban').val(parseInt(last_no)+1);

		$('#add_barcode').val('');
		if (material.split('_')[1] == 'HSA') {
			$('#add_barcode').val('{{strtoupper($location)}}'+'21'+material.split('_')[0]+(parseInt(last_no)+1));
		}

		$('#bodyNoKanban').append(bodyNo);
	}

	function cancelAll() {
		$('#id').val('');
		$('#edit_material').val('').trigger('change');
		$('#edit_tag').val('');
		$('#edit_no_kanban').val('');
		$('#edit_barcode').val('');

		$('#id').val('');
		$('#add_material').val('').trigger('change');
		$('#add_tag').val('');
		$('#add_no_kanban').val('');
		$('#add_barcode').val('');
	}

	function editKanban(id,material_number,tags,barcode,material_category,material_type,no_kanban) {
		cancelAll();
		$('#id').val(id);
		$('#edit_material').val(material_number+'_'+material_category+'_'+material_type).trigger('change');
		$('#edit_tag').val(tags);
		$('#edit_no_kanban').val(no_kanban);
		if (barcode != 'null') {
			$('#edit_barcode').val(barcode);
		}
		$('#edit-modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();

			if ($('#edit_material').val() == '' || $('#edit_tag').val() == '' || $('#edit_no_kanban').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!',"Isi Material, Tag, dan No. Kanban");
				return false;
			}
			var data = {
				id:$('#id').val(),
				material:$('#edit_material').val(),
				tag:$('#edit_tag').val(),
				no_kanban:$('#edit_no_kanban').val(),
				barcode:$('#edit_barcode').val(),
			}

			$.post('{{ url("update/welding/kanban") }}',data, function(result, status, xhr){
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

			if ($('#add_material').val() == '' || $('#add_tag').val() == '' || $('#add_no_kanban').val() == '') {
				$('#loading').hide();
				openErrorGritter('Error!',"Isi Material, Tag, dan No. Kanban");
				return false;
			}
			var data = {
				material:$('#add_material').val(),
				tag:$('#add_tag').val(),
				location:'{{$location}}',
				no_kanban:$('#add_no_kanban').val(),
				barcode:$('#add_barcode').val(),
			}

			$.post('{{ url("input/welding/kanban") }}',data, function(result, status, xhr){
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