@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
	}
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
		<small>{{ $subtitle }} <span class="text-purple"> {{ $subtitle_jp }}</span></small>
	</h1>

	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#licenseModal" class="btn btn-default btn-sm" style="color:black; background-color: #e7e7e7;">
				&nbsp;<i class="fa fa-file-o"></i>&nbsp;License Logs
			</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="font-size: 0.8vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters</h3>
					<h3 id="printer_name" class="box-title pull-right"></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-md-3 col-md-offset-3">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" placeholder="Select Date">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" placeholder="Select Date">
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="form-group">
								<label>Material</label>
								<select class="form-control select2" multiple="multiple" name="material_number" id='material_number' data-placeholder="Select Material" style="width: 100%;">
									<option style="color:grey;" value="">Select Material</option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-4 col-md-offset-4">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Logs</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="table-material" class="table table-bordered table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%">QR Code</th>
									<th style="width: 5%">In Date</th>
									<th style="width: 5%">Mfg Date</th>
									<th style="width: 5%">Exp Date</th>
									<th style="width: 10%">Material</th>
									<th style="width: 20%">Material Description</th>
									<th style="width: 5%">License</th>
									<th style="width: 5%">Storage Location</th>
									<th style="width: 5%">Remark</th>
									<th style="width: 5%">Qty</th>
									<th style="width: 5%">Balance</th>
									<th style="width: 5%">Bun</th>
									<th style="width: 10%">PIC</th>
									<th style="width: 10%">Created At</th>
								</tr>
							</thead>
							<tbody id="table-out">
							</tbody>
							<tfoot>
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
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="licenseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 90%;">            
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">License Logs</h4>
			</div>
			<div class="modal-body">

				<div class="row" style="margin-bottom: 2%;">
					<div class="col-xs-2" style="padding-right: 0px;">						
						<input type="text" class="form-control" name="license" id="license" placeholder="Fill license here ...">  
					</div>

					<div class="col-xs-2">
						<button onclick="searchLicense()" class="btn btn-primary">Search</button>
					</div>
				</div>

				<table class="table table-hover table-bordered table-striped" id="table-license" style="font-size: 0.8vw;">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 10%">QR Code</th>
							<th style="width: 5%">In Date</th>
							<th style="width: 5%">Mfg Date</th>
							<th style="width: 5%">Exp Date</th>
							<th style="width: 10%">Material</th>
							<th style="width: 20%">Material Description</th>
							<th style="width: 5%">License</th>
							<th style="width: 5%">Storage Location</th>
							<th style="width: 5%">Remark</th>
							<th style="width: 5%">Qty</th>
							<th style="width: 5%">Balance</th>
							<th style="width: 5%">Bun</th>
							<th style="width: 10%">PIC</th>
							<th style="width: 10%">Created At</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
					<tfoot>
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
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>          
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
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
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();

		fetchTable();
		
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	$('#licenseModal').on('shown.bs.modal', function(){
		$('#license').val('');
		$('#table-license').DataTable().destroy();
		$('#table-license tbody').html('');
	});

	function searchLicense() {

		var license = $('#license').val();

		console.log(license);

		if(license == ''){
			openErrorGritter('Error!', 'Fill license');
			return false;
		}

		var data = {
			license:license
		}

		$('#table-license').DataTable().destroy();
		$('#table-license tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table_material = $('#table-license').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": false,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/indirect_material_log") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "qr_code"},
			{ "data": "in_date"},
			{ "data": "mfg_date"},
			{ "data": "exp_date"},
			{ "data": "material_number"},
			{ "data": "material_description", "className" : "text-left"},
			{ "data": "license"},
			{ "data": "storage_location"},
			{ "data": "remark"},
			{ "data": "quantity"},
			{ "data": "balance_license"},
			{ "data": "bun"},
			{ "data": "name"},
			{ "data": "created_at"}
			]
		});
		table_material.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#table-license tfoot tr').appendTo('#table-license thead');

	}

	function fetchTable() {

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var material_number = $('#material_number').val();
		var print = $('#print').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			material_number:material_number,
			print:print
		}


		$('#table-material').DataTable().destroy();
		$('#table-material tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table_material = $('#table-material').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": false,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/indirect_material_log") }}",
				"data" : data		
			},
			"columns": [
			{ "data": "qr_code"},
			{ "data": "in_date"},
			{ "data": "mfg_date"},
			{ "data": "exp_date"},
			{ "data": "material_number"},
			{ "data": "material_description", "className" : "text-left"},
			{ "data": "license"},
			{ "data": "storage_location"},
			{ "data": "remark"},
			{ "data": "quantity"},
			{ "data": "balance"},
			{ "data": "bun"},
			{ "data": "name"},
			{ "data": "created_at"}
			]
		});
		table_material.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#table-material tfoot tr').appendTo('#table-material thead');
		
	}

	function print(qr_code) {
		var data = {
			qr_code : qr_code
		}

		window.open('{{ url("print/indirect_material_label") }}'+'/'+qr_code, '_blank');
		
		$('#table-material').DataTable().ajax.reload();	
		openSuccessGritter('Success!', '');


	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection

