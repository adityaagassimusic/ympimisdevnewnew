@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	.nmpd-grid {
		border: none;
		padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Buffing Kanban
		<span class="text-purple"> ??</span>
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

	<div class="row">
		<div class="col-xs-6 col-xs-offset-3" style="margin-bottom: 3%; text-align: center;">
			<span style="font-size: 2vw;"><i class="fa fa-angle-double-down"></i> Check Kanban <i class="fa fa-angle-double-down"></i></span>

			<div class="input-group col-xs-12" style="text-align: center;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
				<input type="text" style="text-align: center; font-size: 2vw; height: 50px" class="form-control" id="tag" name="tag" placeholder="Scan Kanban Here" required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
			</div>
		</div>
		<br>
	</div>

	<div class="row">
		<div class="col-xs-2">
			<div class="form-group">
				<select class="form-control select2" id='location' data-placeholder="Select Location" style="width: 100%;">
					<option value=""></option>
					<option value="key">Key</option>
					<option value="body">Body</option>
				</select>
			</div>
		</div>
		<div class="col-xs-2" style="padding: 0px;">
			<button class="btn btn-success" onclick="fillTable()">Show</button>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-default">
				<div class="box-body">
					<table id="kanbanTable" class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 1%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th width="10%">Material</th>
								<th width="30%">Desc</th>								
								<th width="5%">Model</th>
								<th width="5%">Key</th>
								<th width="10%">Location</th>
								<th width="10%">Tag</th>
								<th width="5%">No.Kanban</th>
								<th width="10%">Last Updated</th>
								<th width="5%">Status</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody id='kanbanBody'>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	{{-- Modal Detail --}}
	<div class="modal fade" id="modal_check">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header" style="text-align: center; font-weight: bold;">
					<h2 style="margin: 0px;">
						Check Kanban 
					</h2>
				</div>
				<div class="modal-body">
					<div class="row">
						<input id="idx" hidden>
						<div class="col-xs-6" style="padding-right: 0px;">
							<input style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px; background-color: #a6a3f0;" type="text" id="model" readonly>					
						</div>
						<div class="col-xs-6" style="padding-left: 0px;">
							<input style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px; background-color: #a6a3f0;" type="text" id="key" readonly>					
						</div>
						<div class="col-xs-12">
							<input style="font-weight: bold; text-align: center; font-size: 5vw; width: 100%; height: 150px; vertical-align: middle; background-color: #ccff90;" type="text" class="numpad" id="no_kanban">						
						</div>
					</div>					
				</div>
				<div class="modal-footer">
					<div class="col-xs-6" style="padding-right: 0px;">
						<button style="width: 100%;" type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Close</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px;">
						<button style="width: 100%;" class="btn btn-success btn-lg" onclick="updateKanban()"><span><i class="fa fa-save"></i> &nbsp;&nbsp;Update</span></button>
					</div>

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
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$("#tag").val("");
		$('#tag').focus();

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.select2').select2({
			allowClear: true
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

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				scanTag();
			}else{
				openErrorGritter('Error!', 'Kanban invalid.');
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}
		}
	});

	function fillTable(){	

		$('#kanbanTable').DataTable().clear();
		$('#kanbanTable').DataTable().destroy();

		var location = $("#location").val();
		var data = {
			sloc : '{{ $sloc }}',
			location : location
		}

		var table = $('#kanbanTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default'
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/middle/buffing_kanban") }}",
				"data" : data
			},
			"columns": [
			{ "data": "material_num" },
			{ "data": "material_description" },
			{ "data": "model" },
			{ "data": "key" },
			{ "data": "loc" },
			{ "data": "material_tag_id" },
			{ "data": "no_kanban" },
			{ "data": "updated_at" },
			{ "data": "status" },
			{ "data": "action" }
			]
		});	
	}

	function scanTag(){
		var tag = $("#tag").val();
		
		var data = {
			sloc : '{{ $sloc }}',
			tag : tag
		}

		$.get('{{ url("fetch/middle/buffing_check_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$("#idx").val(result.kanban.idx);
				$("#model").val(result.material.model);
				$("#key").val(result.material.key);
				$("#no_kanban").val( (result.kanban.no_kanban || '-') );

				$("#modal_check").modal('show');

				

			} else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}

		});
	}

	function updateKanban(){
		var idx = $("#idx").val();
		var no_kanban = $("#no_kanban").val();
		
		var data = {
			idx : idx,
			no_kanban : no_kanban
		}

		$.post('{{ url("update/middle/buffing_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$("#tag").val('');
				$("#tag").focus();

				$("#idx").val('');
				$("#model").val('');
				$("#key").val('');
				$("#no_kanban").val('');

				$("#modal_check").modal('hide');

				openSuccessGritter('Success', result.message);

			} else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}

		});
	}


</script>
@endsection