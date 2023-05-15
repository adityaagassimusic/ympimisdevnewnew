@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
	
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>

	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Scrap Penarikan Logs</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Dari Tanggal</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" data-placeholder="Select Date">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Sampai Tanggal</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" data-placeholder="Select Date">
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12" style="overflow-x: auto;">
							<table id="ListDetailPenarikan" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Slip Penarikan</th>
										<th>Slip Scrap</th>
										<th>GMC</th>
										<th>Deskripsi</th>
										<th>Qty</th>
										<th>Dari Lokasi</th>
										<th>Ke Lokasi</th>
										<th>No Invoice</th>
										<th>Reason</th>
										<th>Scrap</th>
										<th>Penarikan</th>
									</tr>
								</thead>
								<tbody id="BodyListDetailPenarikan">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

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
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		// $('#logTable').DataTable().clear();
		// $('#logTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto
		}		

		// var table = $('#logTable').DataTable({
		// 	'dom': 'Bfrtip',
		// 	'responsive': true,
		// 	'lengthMenu': [
		// 	[ 10, 25, 50, -1 ],
		// 	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		// 	],
		// 	'buttons': {
		// 		buttons:[
		// 		{
		// 			extend: 'pageLength',
		// 			className: 'btn btn-default'
		// 		},
		// 		{
		// 			extend: 'copy',
		// 			className: 'btn btn-success',
		// 			text: '<i class="fa fa-copy"></i> Copy',
		// 			exportOptions: {
		// 				columns: ':not(.notexport)'
		// 			}
		// 		},
		// 		{
		// 			extend: 'excel',
		// 			className: 'btn btn-info',
		// 			text: '<i class="fa fa-file-excel-o"></i> Excel',
		// 			exportOptions: {
		// 				columns: ':not(.notexport)'
		// 			}
		// 		},
		// 		{
		// 			extend: 'print',
		// 			className: 'btn btn-warning',
		// 			text: '<i class="fa fa-print"></i> Print',
		// 			exportOptions: {
		// 				columns: ':not(.notexport)'
		// 			}
		// 		},
		// 		]
		// 	},
		// 	'paging': true,
		// 	'lengthChange': true,
		// 	'searching': true,
		// 	'ordering': true,
		// 	'order': [],
		// 	'info': true,
		// 	'autoWidth': true,
		// 	"sPaginationType": "full_numbers",
		// 	"bJQueryUI": true,
		// 	"bAutoWidth": false,
		// 	"processing": true,
		// 	"serverSide": true,
		// 	"ajax": {
		// 		"type" : "get",
		// 		"url" : "{{ url("fetch/penarikan/scrap/logs") }}",
		// 		"data" : data
		// 	},
		// 	"columns": [
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },	
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" },
		// 	{ "data": "slip_penarikan" }
		// 	]
		// });
		
		$.get('<?php echo e(url("fetch/penarikan/scrap/logs")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#ListDetailPenarikan').DataTable().clear();
				$('#ListDetailPenarikan').DataTable().destroy();
				var tableData = '';
				$('#BodyListDetailPenarikan').html("");
				$('#BodyListDetailPenarikan').empty();
				$.each(result.data_penarikan, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.slip_penarikan +'</td>';
					tableData += '<td>'+ value.slip +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.quantity +' '+ value.uom +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.withdrawal_to +'</td>';
					tableData += '<td>'+ value.no_invoice +'</td>';
					tableData += '<td>'+ value.reason +'</td>';
					tableData += '<td>'+ value.scrap_by +'</td>';
					tableData += '<td>'+ value.created_by +'</td>';
					tableData += '</tr>';
				});
				$('#BodyListDetailPenarikan').append(tableData);

				var table = $('#ListDetailPenarikan').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": false,
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function BatalPenarikanScrap(id) {
		// $("#loading").show();
		var data = {
			id : id
		}
		$.post('{{ url("cancel/penarikan/scrap") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				// $("#loading").hide();
				$('#logTable').DataTable().ajax.reload();
			}else{
				openErrorGritter('Error', result.message);
			}
		});
	}

	function deleteScrap(id){
		$("#loading").show();

		var data = {
			id:id
		}

		if(confirm("Apa anda yakin anda akan mendelete slip scrap?")){
			$.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$("#loading").hide();
					$('#logTable').DataTable().ajax.reload();
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();
		}
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