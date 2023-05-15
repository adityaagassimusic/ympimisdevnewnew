@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
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
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 2vw;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: rgb(252, 248, 227);
		font-weight: bold;
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
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="employee_id">

		<input type="hidden" id="proc" value="{{ $process }}">
		<input type="hidden" id="order_id">
		<input type="hidden" id="gmc_kanban">
		<input type="hidden" id="gmc_store">
		<input type="hidden" id="gmc_hako">

		<div id="main">
			<div class="col-xs-3">
				<center>
					<button class="btn btn-warning btn-md" onclick="refreshInput()" style="font-size: 2vw; margin-bottom: 5%;">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</button>

					<div class="col-xs-12" style="padding: 0px;">
						<div class="box">
							<div class="box-body">
								<div style="background-color: #00c0ef;">
									<span style="font-weight: bold; color: white; font-size: 2vw;">1. SCAN STORE</span><br>
								</div>
								<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="store" placeholder="Scan QR Code">
							</div>
						</div>
					</div>

					<div class="col-xs-12" style="padding: 0px;">
						<div class="box">
							<div class="box-body">
								<div style="background-color: orange;">
									<span style="font-weight: bold; color: white; font-size: 2vw;">2. SCAN KANBAN</span><br>
								</div>
								<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="kanban" placeholder="Scan QR Code">
							</div>
						</div>
					</div>

					

					<div class="col-xs-12" style="padding: 0px;">
						<div class="box">
							<div class="box-body">
								<div style="background-color: #CE93D8;">
									<span style="font-weight: bold; color: white; font-size: 2vw;">3. SCAN HAKO</span><br>
								</div>
								<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="hako" placeholder="Scan QR Code">
							</div>
						</div>
					</div>

				</center>
			</div>
			<div class="col-xs-9">
				<div class="col-xs-6">
					<div class="box">
						<div class="box-body">
							<table id="operatorTable" class="table table-bordered table-stripped">
								<thead style="background-color: #00c0ef;">
									<tr>
										<th style="width: 50%; font-size: 1.5vw;" colspan="2">OPERATOR</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="width: 30%; font-size: 1.5vw;" id="emp_id"></td>
										<td style="width: 70%; font-size: 1.5vw;" id="emp_name"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-6" id="inventory">
					<div class="box">
						<div class="box-body">
							<table width="" id="inventoryTable" class="table table-bordered table-stripped">
								<thead style="background-color: #0FFF67;">
									<tr>
										<th colspan="2" style="width: 1%; font-size: 1.5vw;">INVENTORY</th>
									</tr>
								</thead>
								<thead id="inventoryBody" style="background-color: #FCF8E3;">
									<tr>
										<th style="width: 1%; font-size: 1.5vw;" id="inv_material"></th>
										<th style="width: 1%; font-size: 1.5vw;" id="inv_location"></th>
									</tr>
									<tr>
										<th colspan="2" style="width: 1%; font-size: 3vw;" id="inv_qty"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div class="col-xs-12" id="delivery">
					<div class="box">
						<div class="box-body">
							<h2 style="margin-top: 0px;">Last Transaction :</h2>
							<table id="deliveryTable" class="table table-bordered table-stripped">
								<thead style="background-color: orange; vertical-align: middle;">
									<tr>
										<th style="width: 1%; font-size: 1.2vw;">Kanban</th>
										<th style="width: 1%; font-size: 1.2vw;">Material</th>
										<th style="width: 6%; font-size: 1.2vw;">Deskripsi</th>
										<th style="width: 1%; font-size: 1.2vw;">Quantity</th>
										<th style="width: 1%; font-size: 1.2vw;">Delivery Status</th>
									</tr>
								</thead>
								<tbody id="deliveryBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header no-padding">
				<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
					AFTER {{ $process }} DELIVERY
				</h4>
			</div>
			<div class="modal-body table-responsive">
				<div class="form-group">
					<label for="exampleInputEmail1">Employee ID</label>
					<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					<br><br>
					<a href="{{ url("/index/reed") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-arrow-left"></i> Ke Halaman Reed</a>
				</div>
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
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {


		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});

	var last_trx = [];

	function clearAll(){
		$('#operator').val("");
		$('#gmc_kanban').val("");
		$('#gmc_store').val("");
		$('#gmc_hako').val("");

		$('#kanban').val("");
		$('#store').val("");
		$('#hako').val("");

		$('#operator').focus();

		$('#main').hide();
		$('#inventory').hide();
		$('#delivery').hide();
		
		last_trx = [];

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length == 9){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/reed/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#emp_id').text(result.employee.employee_id);
						$('#emp_name').text(result.employee.name);
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();

						$('#main').show();
						$('#store').val('');
						$('#store').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});


	$('#store').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if( ($("#store").val().length >= 13) && (($('#store').val().includes('-'))) ){

				var data = $("#store").val();
				var data = data.split('-');

				var text = data[0];
				var gmc_store = data[1].substring(0, 7);

				if(text.toUpperCase() == 'STORE'){
					showInventory(gmc_store);
					$('#gmc_store').val(gmc_store);

					$('#kanban').val('');
					$('#kanban').focus();
				}else{
					openErrorGritter('Error!', 'Store tidak valid.');
					audio_error.play();
					$('#store').val('');
					$('#store').focus();
				}
			}else{
				openErrorGritter('Error!', 'QR Code tidak terdaftar.');
				audio_error.play();
				$('#store').val('');
				$('#store').focus();
			}			
		}
	});

	function showInventory(gmc_store) {

		var data = {
			proc : $('#proc').val(),
			material_number : gmc_store
		}

		$('#loading').show();
		$.get('{{ url("fetch/reed/inventory") }}', data, function(result, status, xhr){
			if(result.status){
				if(result.inventory.length > 0){
					$('#inv_material').text(result.material.material_number);
					$('#inv_location').text(result.material.material_description);
					$('#inv_qty').text(result.inventory[0].quantity);
				}else{
					$('#inv_material').text(result.material.material_number);
					$('#inv_location').text(result.material.material_description);
					$('#inv_qty').text('0');
				}
				$('#inventory').show();

				$('#loading').hide();
			}else{
				openErrorGritter('Error!', result.message);
				audio_error.play();

				$('#store').val('');
				$('#store').focus();
				$('#loading').hide();
			}
		});
	}

	$('#kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kanban").val().length >= 10){
				checkKanban($("#kanban").val());
			}else{
				openErrorGritter('Error!', 'Kanban tidak valid.');
				audio_error.play();
				$('#kanban').val('');
				$('#kanban').focus();
			}			
		}
	});

	function checkKanban(kanban) {
		var data = {
			proc : $('#proc').val(),
			kanban : kanban
		}

		$('#loading').show();

		$.get('{{ url("fetch/reed/check_kanban") }}', data, function(result, status, xhr){
			if(result.status){
				$('#gmc_kanban').val(result.material.material_number);
				
				var gmc_store = $('#gmc_store').val();
				var gmc_kanban = $('#gmc_kanban').val();

				if(gmc_store.toUpperCase() != gmc_kanban.toUpperCase()){
					openErrorGritter('Error!', 'Kanban tidak sesuai dengan store');
					audio_error.play();

					$('#kanban').val('');
					$('#kanban').focus();
				}else{
					$('#hako').val('');
					$('#hako').focus();
				}

				$('#loading').hide();


			}else{
				openErrorGritter('Error!', result.message);
				audio_error.play();

				$('#kanban').val('');
				$('#kanban').focus();
				$('#loading').hide();
			}
		});
	}

	$('#hako').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if( ($('#hako').val().length >= 12) && ($('#hako').val().includes('-')) ){
				var gmc_kanban = $("#gmc_kanban").val();

				var data = $("#hako").val();
				var data = data.split('-');

				var text = data[0];
				var gmc_hako = data[1].substring(0, 7);

				if(text.toUpperCase() == 'HAKO'){
					if(gmc_kanban.toUpperCase() == gmc_hako.toUpperCase()){
						scanDelivery();
					}else{
						openErrorGritter('Error!', 'Hako salah.');
						audio_error.play();
						$('#hako').val('');
						$('#hako').focus();
					}
				}else{
					openErrorGritter('Error!', 'Hako tidak valid.');
					audio_error.play();
					$('#hako').val('');
					$('#hako').focus();
				}
			}else{
				openErrorGritter('Error!', 'QR Code tidak terdaftar.');
				audio_error.play();
				$('#hako').val('');
				$('#hako').focus();			
			}
		}
	});

	function scanDelivery() {
		var data = {
			employee_id : $('#employee_id').val(),
			proc : $('#proc').val(),
			kanban : $('#kanban').val()
		}

		$('#loading').show();

		$.post('{{ url("scan/reed/delivery") }}', data, function(result, status, xhr){
			if(result.status){
				$('#kanban').val('');
				$('#kanban').focus();

				$('#hako').val('');

				showInventory($('#gmc_store').val());

				last_trx.push({
					'kanban' : result.order_detail.kanban,
					'material_number' : result.order_detail.material_number,
					'material_description' : result.order_detail.material_description,
					'quantity' : result.order_detail.quantity,
					'status' : 'Success'
				});
				showLastTrx()

				audio_ok.play();
				openSuccessGritter('Success', result.message);

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();

				$('#kanban').val('');
				$('#kanban').focus();

				$('#hako').val('');
			}
		});
	}

	function showLastTrx() {
		$("#loading").show();
		$('#deliveryTable').DataTable().clear();
		$('#deliveryTable').DataTable().destroy();
		$('#deliveryBody').html();

		var tableData = '';
		for (var i = 0; i < last_trx.length; i++) {
			tableData += '<tr>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].kanban+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].material_number+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].material_description+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].quantity+'</td>';
			tableData += '<td style="vertical-align: middle;">'+last_trx[i].status+'</td>';
			tableData += '</tr>';
		}

		$('#deliveryBody').append(tableData);


		var table = $('#deliveryTable').DataTable({
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

		$('#delivery').show();
	}

	function refreshInput() {
		$('#inventory').hide();
		$('#delivery').hide();

		$('#kanban').val('');
		$('#store').val('');
		$('#hako').val('');

		$('#kanban').prop('disabled', false);
		$('#store').prop('disabled', false);
		$('#hako').prop('disabled', false);

		$('#store').focus();

		last_trx = [];
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

