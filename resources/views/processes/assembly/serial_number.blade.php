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
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add JAN / EAN / UPC
		</button>
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
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="overflow-x: scroll;">
					<div class="col-xs-12">
						<div class="row">
							<table id="tableSerial" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Base Model</th>
										<th width="2%">Material</th>
										<th width="1%">JAN / EAN</th>
										<th width="1%">UPC</th>
										<th width="1%">Remark</th>
										<th width="2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableSerial">
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

	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add JAN / EAN / UPC</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-3">Based Model<span class="text-red">*</span></label>
									<div class="col-sm-9" align="left">
										<input type="text" class="form-control" id="model" placeholder="Based Model" required>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">Material<span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="selectMaterial">
										<select class="form-control selectMaterial" data-placeholder="Pilih Material Finished" name="finished" id="finished" style="width: 100%">
											<option value=""></option>
											@foreach($mpdl as $mpdl)
											<option value="{{$mpdl->material_number}}">{{$mpdl->material_number}}  - {{$mpdl->material_description}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">JAN / EAN</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="janean" placeholder="JAN / EAN">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">UPC</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="upc" placeholder="UPC">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">Remark</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="remark" placeholder="Remark ( J / SP )">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
					<button class="btn btn-success" onclick="addJanEan()"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit JAN / EAN / UPC</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-3">Based Model<span class="text-red">*</span></label>
									<div class="col-sm-9" align="left">
										<input type="hidden" class="form-control" id="id" placeholder="id" required>
										<input type="text" class="form-control" id="edit_model" placeholder="Based Model" required>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">Material<span class="text-red">*</span></label>
									<div class="col-sm-9" align="left" id="selectMaterialEdit">
										<select class="form-control selectMaterialEdit" data-placeholder="Pilih Material Finished" name="edit_finished" id="edit_finished" style="width: 100%">
											<option value=""></option>
											@foreach($mpdl2 as $mpdl)
											<option value="{{$mpdl->material_number}}">{{$mpdl->material_number}}  - {{$mpdl->material_description}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">JAN / EAN</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="edit_janean" placeholder="JAN / EAN">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">UPC</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="edit_upc" placeholder="UPC">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-3">Remark</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="edit_remark" placeholder="Remark ( J / SP )">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

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
	});


	$(function () {
		$('.selectMaterial').select2({
			dropdownParent: $('#selectMaterial'),
			allowClear:true
		});

		$('.selectMaterialEdit').select2({
			dropdownParent: $('#selectMaterialEdit'),
			allowClear:true
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
			point_check:$('#point_check').val(),
		}
		$.get('{{ url("fetch/serial_number") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableSerial').DataTable().clear();
				$('#tableSerial').DataTable().destroy();
				$('#bodyTableSerial').html("");
				var tableData = "";
				var index = 1;
				$.each(result.janean, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="text-align:right;padding-right:10px;">'+ index +'</td>';
					tableData += '<td style="text-align:left;padding-left:10px;">'+ value.model +'</td>';
					tableData += '<td style="text-align:left;padding-left:10px;">'+ value.finished +' - '+ value.material_description +'</td>';
					tableData += '<td style="text-align:right;padding-right:10px;">'+ value.janean+'</td>';
					tableData += '<td style="text-align:right;padding-right:10px;">'+ value.upc +'</td>';
					tableData += '<td style="text-align:left;padding-left:10px;">'+ (value.remark || '') +'</td>';
					tableData += '<td style="text-align:left;padding-left:10px;padding:2px;"><button class="btn btn-warning btn-sm" onclick="edit(\''+value.id+'\',\''+value.model+'\',\''+value.finished+'\',\''+value.janean+'\',\''+value.upc+'\',\''+value.remark+'\')"><i class="fa fa-edit"></i></button></td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableSerial').append(tableData);

				var table = $('#tableSerial').DataTable({
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

	function addJanEan() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#model').val() == '' || $('#finished').val() == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Isi Model dan Material');
				return false;
			}
			var model = $('#model').val();
			var finished = $('#finished').val();
			if ($('#janean').val() == '') {
				var janean = '';
			}else{
				var janean = $('#janean').val();
			}
			if ($('#upc').val() == '') {
				var upc = '';
			}else{
				var upc = $('#upc').val();
			}
			if ($('#remark').val() == '') {
				var remark = null;
			}else{
				var remark = $('#remark').val();
			}
			var data = {
				model:model,
				finished:finished,
				janean:janean,
				upc:upc,
				remark:remark
			}

			$.post('{{ url("input/serial_number") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success!','Sukses Add JAN / EAN / UPC')
					fillList();
					$('#create_modal').modal('hide');
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function edit(id,model,finished,janean,upc,remark) {
		$('#id').val(id);
		$('#edit_model').val(model);
		$('#edit_finished').val(finished).trigger('change');
		$('#edit_janean').val(janean);
		$('#edit_upc').val(upc);
		if (remark == 'null') {
			$('#edit_remark').val('');
		}else{
			$('#edit_remark').val(remark);
		}
		$('#edit_modal').modal('show');
	}

	function update() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if ($('#edit_model').val() == '' || $('#edit_finished').val() == '') {
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!','Isi Model dan Material');
				return false;
			}
			var model = $('#edit_model').val();
			var finished = $('#edit_finished').val();
			if ($('#edit_janean').val() == '') {
				var janean = '';
			}else{
				var janean = $('#edit_janean').val();
			}
			if ($('#edit_upc').val() == '') {
				var upc = '';
			}else{
				var upc = $('#edit_upc').val();
			}
			if ($('#edit_remark').val() == '') {
				var remark = null;
			}else{
				var remark = $('#edit_remark').val();
			}
			var data = {
				id:$('#id').val(),
				model:model,
				finished:finished,
				janean:janean,
				upc:upc,
				remark:remark
			}

			$.post('{{ url("update/serial_number") }}',data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#edit_modal').modal('hide');
					openSuccessGritter('Success!','Sukses Update JAN / EAN / UPC')
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