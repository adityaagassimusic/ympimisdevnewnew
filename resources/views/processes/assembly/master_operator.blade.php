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
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{$title}} <small><span class="text-purple">{{$title_jp}}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#add-modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Operator
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
							<table id="tableOperator" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
										<th style="width: 3%">Employee ID</th>
										<th style="width: 3%">Name</th>
										<th style="width: 3%">Location</th>
										<th style="width: 3%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableOperator">
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
									<label class="col-sm-4">Operator<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditOperator">
										<select class="form-control select3" data-placeholder="Select Operator" name="edit_operator" id="edit_operator" style="width: 100%">
											<option value=""></option>
											@foreach($emp2 as $emp2)
											<option value="{{ $emp2->employee_id }}">{{ $emp2->employee_id }} - {{ $emp2->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="edit_tag" class="form-control" id="edit_tag" placeholder="Tap ID Card Operator" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditLocation">
										<select class="form-control select3" data-placeholder="Select Location" name="edit_location" id="edit_location" style="width: 100%">
											<option value=""></option>
											@foreach($location2 as $location2)
											<option value="{{$location2}}">{{$location2}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Line<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divEditLine">
										<select class="form-control select3" data-placeholder="Select Line" name="edit_line" id="edit_line" style="width: 100%">
											<option value=""></option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
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
									<label class="col-sm-4">Operator<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddOperator">
										<select class="form-control select3" data-placeholder="Select Operator" name="add_operator" id="add_operator" style="width: 100%">
											<option value=""></option>
											@foreach($emp as $emp)
											<option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="add_tag" class="form-control" id="add_tag" placeholder="Tap ID Card Operator" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Location<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddLocation">
										<select class="form-control select3" data-placeholder="Select Location" name="add_location" id="add_location" style="width: 100%">
											<option value=""></option>
											@foreach($location as $location)
											<option value="{{$location}}">{{$location}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Line<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="divAddLine">
										<select class="form-control select3" data-placeholder="Select Line" name="add_line" id="add_line" style="width: 100%">
											<option value=""></option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		// $('#dateto').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });
		$('#add_operator').select2({
			allowClear:true,
			dropdownParent: $('#divAddOperator'),
		});
		$('#add_location').select2({
			allowClear:true,
			dropdownParent: $('#divAddLocation'),
		});
		$('#add_line').select2({
			allowClear:true,
			dropdownParent: $('#divAddLine'),
		});

		$('#edit_operator').select2({
			allowClear:true,
			dropdownParent: $('#divEditOperator'),
		});
		$('#edit_location').select2({
			allowClear:true,
			dropdownParent: $('#divEditLocation'),
		});
		$('#edit_line').select2({
			allowClear:true,
			dropdownParent: $('#divEditLine'),
		});
		fillData();
	});

	

	function clearConfirmation(){
		location.reload(true);
	}

	function fillData(){
		$('#loading').show();
		
		var data = {
			origin_group_code:'{{$origin_group_code}}',
		}
		$.get('{{ url("fetch/assembly/operator") }}',data, function(result, status, xhr){
			if(result.status){
				if (result.operator != null) {
					$('#tableOperator').DataTable().clear();
					$('#tableOperator').DataTable().destroy();
					$('#bodyTableOperator').html("");
					var tableOperator = "";
					
					var index = 1;

					$.each(result.operator, function(key, value) {
						tableOperator += '<tr>';
						tableOperator += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
						tableOperator += '<td style="text-align:left;padding-left:7px;">'+value.employee_id+'</td>';
						tableOperator += '<td style="text-align:left;padding-left:7px;">'+value.name+'</td>';
						tableOperator += '<td style="text-align:left;padding-left:7px;">'+value.location+'</td>';
						tableOperator += '<td style="text-align:center"><button class="btn btn-warning btn-sm" onclick="editOperator(\''+value.id+'\',\''+value.employee_id+'\',\''+value.name+'\',\''+value.tag+'\',\''+value.location+'\')"><i class="fa fa-edit"></i></button><button style="margin-left:7px;" class="btn btn-danger btn-sm" onclick="deleteOperator(\''+value.id+'\')"><i class="fa fa-trash"></i></button></td>';
						tableOperator += '</tr>';
						index++;
					});
					$('#bodyTableOperator').append(tableOperator);

					var table = $('#tableOperator').DataTable({
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

	function editOperator(id,employee_id,name,tag,location) {
		$('#id').val(id);
		$('#edit_operator').val(employee_id).trigger('change');
		$('#edit_location').val(location.split('-')[0]+'-'+location.split('-')[1]).trigger('change');
		$('#edit_line').val(location.split('-')[2]).trigger('change');
		if (hexToDecimal(tag).length == 9) {
			$('#edit_tag').val('0'+hexToDecimal(tag));
		}else{
			$('#edit_tag').val(hexToDecimal(tag));
		}
		$('#edit-modal').modal('show');
	}

	function update() {
		$("#loading").show();
		var data = {
			id:$('#id').val(),
			operator:$('#edit_operator').val(),
			location:$('#edit_location').val(),
			line:$('#edit_line').val(),
			tag:$('#edit_tag').val(),
			origin_group_code:'{{$origin_group_code}}',
		}

		$.get('{{ url("update/assembly/operator") }}',data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success','Sukses Update Operator');
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

	function add() {
		$("#loading").show();
		var data = {
			operator:$('#add_operator').val(),
			location:$('#add_location').val(),
			line:$('#add_line').val(),
			tag:$('#add_tag').val(),
			origin_group_code:'{{$origin_group_code}}',
		}

		$.get('{{ url("input/assembly/operator") }}',data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success','Sukses Tambah Operator');
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