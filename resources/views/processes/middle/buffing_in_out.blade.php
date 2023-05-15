@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	#main tbody>tr>td {
		text-align: center;
	}
	table {
		border: 1px solid #333;
	}
	thead>tr>th {
		border: 1px solid #333;
		background-color: white;
		text-align: center;
	}
	tbody>tr>td {
		border: 1px solid #333;
		color: #333;
		font-weight: bold;
		text-align:center;
	}
	#loading {
		display: none;
	}
	#tag {
		vertical-align: middle;
		text-align: center;
		font-size: 30px;
		height: 70px;
	}
	.btn-btn {
		width: 100%;
		height: 60px;
		font-size: 25px;
		font-weight: bold;
		vertical-align: top;
		text-align: center;
	}
	.symbol{
		font-size: 50px;
		font-weight: bold;
	}
	#tag-group{
		margin-top: 1%;
		margin-bottom: 1%;
	}
	#box_inout{
		height: 450px;
		width: 100%;
	}
	#status {
		margin-top: 20px;
		margin-bottom: 0px;
		vertical-align: top;
		text-align: center;
		font-size: 70px;
		font-weight: bold;
	}
	#quantity{
		text-align: center;
		font-size: 100px;
		font-weight: bold;
	}
	#model_key{
		text-align: center;
		font-size: 60px;
		font-weight: bold;
	}
	#material_number, #material_description{
		text-align: center;
		vertical-align: bottom;
		font-size: 30px;
		font-weight: bold;
		margin: 0px;
	}
	.middle {
		vertical-align: middle;
	}


</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		
		<div class="col-xs-10 col-xs-offset-1">

			<div class="col-xs-4">
				<button id="last_trx" class="btn btn-btn btn-lg btn-default" onclick="showLastTrx()"><i class="fa fa-history"></i>&nbsp;&nbsp;&nbsp;Last Transaction</button>
			</div>
			<div class="col-xs-4">
				<button id="change" class="btn btn-btn btn-lg btn-success" onclick="changeTrx()"><i class="fa fa-exchange"></i>&nbsp;&nbsp;&nbsp;Change <span id="change_status"></span></button>
			</div>
			<div class="col-xs-4">
				<button id="store" class="btn btn-btn btn-lg btn-primary" onclick="fillStore()"><i class="fa fa-cubes"></i>&nbsp;&nbsp;&nbsp;Store</button>
			</div>

			<div id="tag-group" class="col-xs-12">
				<div class="input-group">
					<span class="input-group-addon symbol"><i class="fa fa-credit-card"></i></span>
					<input type="text" class="form-control" placeholder="Scan Kanban Buffing . . ." id="tag">
					<span class="input-group-addon symbol"><i class="fa fa-credit-card"></i></span>
				</div>
			</div>
			<div class="col-xs-12" id="box_inout">
				<h1 id="status"></h1>
				<hr>
				<h1 id="quantity"></h1>
				<h1 id="model_key"></h1>
				<h1 id="material_description"></h1>
				<h1 id="material_number"></h1>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modal_check">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="margin: 0px; text-align: center; font-weight: bold;">
					UPDATE KANBAN
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<input id="idx" hidden>
					<div class="col-xs-6" style="padding-right: 0px;">
						<input style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px;" type="text" id="model" readonly>					
					</div>
					<div class="col-xs-6" style="padding-left: 0px;">
						<input style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px;" type="text" id="key" readonly>					
					</div>
					<div class="col-xs-12">
						<input style="font-weight: bold; text-align: center; font-size: 5vw; width: 100%; height: 150px; vertical-align: middle; background-color: #F2F2F2;" type="text" class="numpad" id="no_kanban">						
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

<div class="modal fade" id="modal_store">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="margin: 0px; text-align: center; font-weight: bold;">
					STORE AFTER BUFFING
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<table id="kanbanTable" class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 1%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Material</th>
									<th>Desc</th>								
									<th>Model</th>
									<th>Key</th>
									<th>Quantity</th>
									<th>No.Kanban</th>
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
								</tr>
							</tbody>
						</table>
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_last">
	<div class="modal-dialog" style="width: 70%;">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="margin: 0px; text-align: center; font-weight: bold;">
					LAST TRANSACTION
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<table id="lastTable" class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 1%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th width="5%">Type</th>
									<th width="35%">Transaction At</th>
									<th width="5%">Material</th>
									<th width="40%">Desc</th>								
									<th width="5%">Model</th>
									<th width="5%">Key</th>
									<th width="5%">No.Kanban</th>
								</tr>
							</thead>
							<tbody id='lastBody'>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	jQuery(document).ready(function() {

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		changeTrx();

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var trx_status = 'OUT';
	var last_trx = [];

	function changeTrx() {
		
		if(trx_status == 'IN'){
			trx_status = 'OUT';
			$("#box_inout").css({"background-color" : "#FFB600"});
			$("#change").removeClass("btn-warning").addClass("btn-success");
			$("#change_status").text("IN");

		}else if(trx_status == 'OUT'){
			trx_status = 'IN';
			$("#box_inout").css({"background-color" : "#00D975"});
			$("#change").removeClass("btn-success").addClass("btn-warning");
			$("#change_status").text("OUT");

		}

		$("#status").text(trx_status);
		$("#quantity").html('');
		$("#model_key").html('');
		$("#material_description").html('');
		$("#material_number").html('');
		$("#tag").focus();
		$("#tag").val('');
		last_trx = [];
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var str = $("#tag").val();
			if(str.length == 10){
				scanTag(str);
				$("#tag").focus();
			}else{
				audio_error.play();
				openErrorGritter('Error', 'RFID Invalid');
				$("#tag").val("");
				$('#tag').focus();
			}
		}
	});

	function scanTag(tag) {
		var data = {
			tag : tag,
			trx_status : trx_status
		}

		$("#loading").show();
		$.get('{{ url("fetch/process_buffing_inout") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();

				$("#quantity").text(result.tags.material_qty + ' PCs');
				$("#model_key").text(result.material.model + ' ' + result.material.key);
				$("#material_description").text(result.material.material_description);
				$("#material_number").text(result.material.material_number);
				$('#tag').focus();
				$("#tag").val("");

				if(!result.tags.no_kanban){
					$("#idx").val(result.tags.idx);
					$("#model").val(result.material.model);
					$("#key").val(result.material.key);
					$("#no_kanban").val( (result.tags.no_kanban || '-') );

					if(result.tags.material_qty == 8){
						$("#model").css({"background-color" : "#07E493"});
						$("#key").css({"background-color" : "#07E493"});
					}else if(result.tags.material_qty == 10){
						$("#model").css({"background-color" : "#FFD10C"});
						$("#key").css({"background-color" : "#FFD10C"});
					}else if(result.tags.material_qty == 15){
						$("#model").css({"background-color" : "#4EB8F5"});
						$("#key").css({"background-color" : "#4EB8F5"});
					}


					$("#modal_check").modal('show');
				}

				last_trx.push({
					'type' : trx_status,
					'material_number' : result.material.material_number,
					'material_description' : result.material.material_description,
					'model' : result.material.model,
					'key' : result.material.key,
					'no_kanban' : (result.tags.no_kanban || '-'),
					'now' : result.now
				});

				openSuccessGritter('Success!', result.message);
			}else{
				$("#loading").hide();
				$("#quantity").html('');
				$("#model_key").html('');
				$("#material_description").html('');
				$("#material_number").html('');
				$('#tag').focus();
				$("#tag").val("");

				audio_error.play();
				openErrorGritter('Error', result.message);
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

		$("#loading").show();
		$.post('{{ url("update/middle/buffing_kanban") }}', data, function(result, status, xhr){
			$("#loading").hide();
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
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}

		});
	}

	function fillStore(){
		$("#loading").show();
		$('#kanbanTable').DataTable().clear();
		$('#kanbanTable').DataTable().destroy();

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
			"serverSide": false,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/process_buffing_store") }}"
			},
			"columns": [
			{ data: "material_num" },
			{ data: "material_description", width : "40%"},
			{ data: "model" },
			{ data: "key" },
			{ data: "quantity" },
			{ data: "kanbans"}
			]
		});

		$("#modal_store").modal('show');
		$("#loading").hide();

	}

	function showLastTrx() {
		$("#loading").show();
		$('#lastTable').DataTable().clear();
		$('#lastTable').DataTable().destroy();
		$('#lastBody').html();

		var tableData = '';
		for (var i = 0; i < last_trx.length; i++) {
			tableData += '<tr>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].type+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].now+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].material_number+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].material_description+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].model+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].key+'</td>';
			if(last_trx[i].no_kanban){
				tableData += '<td style="vertical-align: middle;"><span class="label" style="margin-right: 1%; color: black; background-color: #4189FC; border: 1px solid black;">&nbsp;</span></td>';				

			}else{
				tableData += '<td style="vertical-align: middle;"><span class="label" style="margin-right: 1%; color: black; background-color: #4189FC; border: 1px solid black;">'+ last_trx[i].no_kanban +'</span></td>';				
			}
			tableData += '</tr>';
		}

		$('#lastBody').append(tableData);


		var table = $('#lastTable').DataTable({
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

		$("#modal_last").modal('show');
		$("#loading").hide();

	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
@endsection