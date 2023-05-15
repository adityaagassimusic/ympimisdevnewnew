@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	table {
		table-layout:fixed;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	merah {
		background-color: red;
	}

	biru {
		background-color: blue;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Check Transactions<span class="text-purple"> 取引確認</span>
		<small>Good Movement <span class="text-purple"> 品物移動</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="#" class="btn btn-info btn-md" data-toggle="modal" data-target="#modal-history">
				<i class="fa fa-print"></i>&nbsp;&nbsp;History
			</button>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 1%;">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="col-xs-12">
						<span class="pull-right" id="last-update" style="font-size: 18px;"></span>
					</div>
					<div class="row">
						<div class="col-xs-5 col-xs-offset-1">
							<button class="btn btn-primary btn-md" onclick="refresh()"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh</button>
							<table id="checkTable" name="resultTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th style="width: 15%">Material</th>
									<th style="width: 50%">Description</th>
									<th style="width: 15%">Uncheck</th>
									<th style="width: 20%">#</th>
								</thead>
								<tbody id="checkTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>

						<div class="col-xs-5">
							<span style="font-size: 20px;">Today Transaction:</span>							
							<table id="todayTable" name="resultTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<th style="width: 15%">Material</th>
									<th style="width: 55%">Description</th>
									<th style="width: 10%">GMS</th>
									<th style="width: 10%">Check</th>
									<th style="width: 10%">Uncheck</th>
								</thead>
								<tbody id="todayTableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
								</tfoot>
							</table>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modal-history">
	<div class="modal-dialog modal-lg" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="margin: 0px; text-align: center; font-weight: bold;">
					TRANSACTION HISTORY
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 col-xs-offset-2">
						<div class="col-xs-3">
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right datepicker" id="datefrom" placeholder="Trasaction From">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right datepicker" id="dateto" placeholder="Trasaction To">
							</div>
						</div>
						<div class="col-xs-2">
							<div class="form-group">
								<button onClick="searchHistory()" class="btn btn-success form-control">Search</button>
							</div>
						</div>
					</div>
					

					<div class="col-xs-12">
						<table id="historyTable" name="resultTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<th style="width: 15%">Transfer At</th>
								<th style="width: 10%">Material</th>
								<th style="width: 40%">Description</th>
								<th style="width: 10%">Status</th>
								<th style="width: 15%">Checked At</th>
								<th style="width: 10%">Serial Number</th>
							</thead>
							<tbody id="historyTableBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
							</tfoot>
						</table>					
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-check">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="margin: 0px; text-align: center; font-weight: bold;">
					TRANSACTION IN
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<input id="material_number" hidden>
						<input style="font-weight: bold; text-align: center; font-size: 1.5vw; width: 100%; height: 50px; background-color: #F2F2F2;" type="text" id="material_description" readonly>
					</div>
					<br>
					<br>
					<br>
					<center>
						<span style="font-size: 18px; font-weight: bold;">
							<i class="fa fa-angle-double-down "></i>
							&nbsp;&nbsp;&nbsp;SERIAL NUMBER&nbsp;&nbsp;&nbsp;
							<i class="fa fa-angle-double-down "></i>
						</span>
					</center>
					<div class="col-xs-12">
						<input style="font-weight: bold; text-align: center; font-size: 5vw; width: 100%; height: 150px; vertical-align: middle;" type="text" id="serial_number">						
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});


		$('body').toggleClass("sidebar-collapse");

		refresh();
		setInterval(refresh, 30 * 60 * 1000);


	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function refresh() {
		fillToday();
		fillCheck();
	}

	function searchHistory() {
		$.get('{{ url("fetch/history_transaction") }}', function(result, status, xhr){
			if(result.status){
				$('#historyTable').DataTable().clear();
				$('#historyTable').DataTable().destroy();
				$('#historyTableBody').html();

				var tableData = '';
				for (var i = 0; i < result.data.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+result.data[i].created_at+'</td>';
					tableData += '<td>'+result.data[i].material_number+'</td>';
					tableData += '<td>'+result.data[i].material_description+'</td>';
					if(result.data[i].status == 'CHECKED'){
						tableData += '<td style="vertical-align: middle;"><span class="label" style="margin-right: 1%; color: black; background-color: #CCFFFF; border: 1px solid black;">'+result.data[i].status+'</span></td>';				
					}else{
						tableData += '<td style="vertical-align: middle;"><span class="label" style="margin-right: 1%; color: black; background-color: #FFCCFF; border: 1px solid black;">'+result.data[i].status+'</span></td>';				
					}
					tableData += '<td>'+(result.data[i].checked_at || '-')+'</td>';
					tableData += '<td>'+(result.data[i].remark || '-')+'</td>';
					tableData += '</tr>';
				}

				$('#historyTableBody').append(tableData);

				var table = $('#historyTable').DataTable({
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
					'searching': false,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					"serverSide": false
				});

			}
		});

	}

	$('#modal-history').on('hidden.bs.modal', function () {
		$('#historyTable').DataTable().clear();
		$('#historyTable').DataTable().destroy();
		$('#historyTableBody').html();

		var table = $('#historyTable').DataTable({
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
			'searching': false,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": false
		});
	});

	$('#modal-history').on('shown.bs.modal', function () {
		$('#historyTable').DataTable().clear();
		$('#historyTable').DataTable().destroy();
		$('#historyTableBody').html();

		var table = $('#historyTable').DataTable({
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
			'searching': false,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": false
		});
	});

	function updateTrx() {
		var data = {
			material_number : $("#material_number").val(),
			serial_number : $("#serial_number").val()
		}
		$('#loading').show();

		$.post('{{ url("scan/check_transaction") }}', data, function(result, status, xhr){
			if(result.status){

				refresh();
				$('#modal-check').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success', result.message);

			}else{
				$('#serial_number').val('');
				$('#serial_number').focus();
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	$('#serial_number').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#serial_number").val().length == 8){
				updateTrx();
			}else{
				openErrorGritter('Error!', 'Serial Number Invalid.');
				audio_error.play();
				$("#serial_number").val("");
				$('#serial_number').focus();
			}
		}
	});

	$('#modal-check').on('shown.bs.modal', function () {
		$('#serial_number').val('');
		$('#serial_number').focus();
	});

	function trx(btn) {
		var data = btn.split('-');
		var material_number = data[1];
		var material_description = $('#'+material_number).find('td').eq(1).text();
		$('#material_number').val(material_number);
		$('#material_description').val(material_description);
		$('#modal-check').modal('show');

	}

	function fillCheck() {

		$.get('{{ url("fetch/check_transaction") }}', function(result, status, xhr){
			if(result.status){
				$('#checkTable').DataTable().clear();
				$('#checkTable').DataTable().destroy();
				$('#checkTableBody').html();

				var button_css = 'style="padding-top: 3px; padding-bottom: 3px;"';

				var tableData = '';
				for (var i = 0; i < result.data.length; i++) {
					tableData += '<tr id="'+result.data[i].material_number+'">';
					tableData += '<td>'+result.data[i].material_number+'</td>';
					tableData += '<td>'+result.data[i].material_description+'</td>';
					tableData += '<td>'+result.data[i].uncheck+'</td>';
					tableData += '<td><button '+button_css+' class="btn btn-success btn-md" id="btn-'+result.data[i].material_number+'" onclick="trx(id)"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Check</button></td>';
					tableData += '</tr>';
				}

				$('#checkTableBody').append(tableData);

				var table = $('#checkTable').DataTable({
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
					'searching': false,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					"serverSide": false
				});
			}
		});

	}

	function fillToday() {

		$.get('{{ url("fetch/today_transaction") }}', function(result, status, xhr){
			if(result.status){
				$('#todayTable').DataTable().clear();
				$('#todayTable').DataTable().destroy();
				$('#todayTableBody').html();
				$('#last-update').html('<i class="fa fa-info-circle"></i>&nbsp;&nbsp;Last Update : '+ result.now);

				var tableData = '';
				for (var i = 0; i < result.data.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+result.data[i].material_number+'</td>';
					tableData += '<td>'+result.data[i].material_description+'</td>';
					tableData += '<td>'+result.data[i].total+'</td>';
					tableData += '<td>'+result.data[i].check+'</td>';
					if(result.data[i].uncheck > 0){
						tableData += '<td style="background-color: #FFCCFF;">'+result.data[i].uncheck+'</td>';
					}else{
						tableData += '<td style="background-color: #CCFFFF;">'+result.data[i].uncheck+'</td>';
					}
					tableData += '</tr>';
				}

				$('#todayTableBody').append(tableData);


				var table = $('#todayTable').DataTable({
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
					'searching': false,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					"serverSide": false
				});
			}
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

	$('.datepicker').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
	});


</script>
@endsection