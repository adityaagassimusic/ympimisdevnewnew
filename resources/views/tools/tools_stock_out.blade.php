@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<meta name="mobile-web-app-capable" content="yes"> 
<meta name="viewport" content="initial-scale=1"> 
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"> 

<style type="text/css">

	/*Start CSS Numpad*/
	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	/*End CSS Numpad*/

	thead>tr>th{
		font-size: 16px;
	}

	#tableMenuList td:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tablemenuu> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #e0e0e0;
	}

	#tableResult > thead > tr > th {
		border:rgba(126,86,134,.7);
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #ddd;
	}

	/*td:hover{
		cursor: pointer;
		background-color: #7dfa8c;
		}*/

		#tablehistory > tr:hover {
			cursor: pointer;
			background-color: #7dfa8c;
		}

		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			/* display: none; <- Crashes Chrome on hover */
			-webkit-appearance: none;
			margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
		}

		input[type=number] {
			-moz-appearance:textfield; /* Firefox */
		}
		input[type="radio"] {
		}

		#loading { display: none; }

		.radio {
			display: inline-block;
			position: relative;
			padding-left: 35px;
			margin-bottom: 12px;
			cursor: pointer;
			font-size: 16px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		/* Hide the browser's default radio button */
		.radio input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom radio button */
		.checkmark {
			position: absolute;
			top: 0;
			left: 0;
			height: 25px;
			width: 25px;
			background-color: #ccc;
			border-radius: 50%;
		}

		/* On mouse-over, add a grey background color */
		.radio:hover input ~ .checkmark {
			background-color: #ccc;
		}

		/* When the radio button is checked, add a blue background */
		.radio input:checked ~ .checkmark {
			background-color: #2196F3;
		}

		/* Create the indicator (the dot/circle - hidden when not checked) */
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the indicator (dot/circle) when checked */
		.radio input:checked ~ .checkmark:after {
			display: block;
		}

		/* Style the indicator (dot/circle) */
		.radio .checkmark:after {
			top: 9px;
			left: 9px;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: white;
		}

		#qr_code {
			text-align: center;
			font-weight: bold;
		}

/*
	img {
	    width: 100%;
	    }*/

	</style>
	@stop
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	    <p style="position: absolute; color: White; top: 45%; left: 35%;">
	      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
	    </p>
	  </div>
	@section('header')
	<section class="content-header">
		<h1>
			{{ $title }}
			<span class="text-purple"> ({{ $title_jp }})</span>
		</h1>
	</section>
	@stop
	@section('content')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<input type="hidden" id="data" value="data">
		<div class="row">
			<div class="col-xs-5">
				<div class="box">
					<div class="box-body">
						<span style="font-size: 20px; font-weight: bold;">Pilih Tools </span>
						<br>
						<!-- <div class="row">
							<div class="col-md-12 col-sm-12 col-xs-12" style="cursor: pointer;">
								<select class="form-control select2" style="width: 100%; height: 40px; font-size: 18px; text-align: center;" id="tools" name="tools" data-placeholder="Pilih Tools" onchange="selectTools(this)" required>
									<option></option>
									@foreach($tools as $tl)
									<option value="{{ $tl->rack_code }}_{{ $tl->item_code }}">{{ $tl->item_code }}_{{ $tl->description }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<hr> -->
						<span style="font-size: 20px; font-weight: bold;"></span>
						<div class="row">
							<div class="col-xs-12" style="margin-bottom: 2%;">
								<div class="input-group input-group-lg">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
										<i class="fa fa-qrcode"></i>
									</div>
									<input type="text" class="form-control" placeholder="SCAN QR CODE" id="qr_code">
									<span class="input-group-btn">
										<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;Scan QR</button>
									</span>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="row">
					<!-- <input type="hidden" id="data"> -->

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;color:white">Detail Operator</span>
						<input type="text" id="employee_detail" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
						<input type="hidden" id="employee_id">
						<input type="hidden" id="employee_name">
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;color:white">Tools</span>
						<input type="text" id="tool" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
						<input type="hidden" id="tool_id">
						<input type="hidden" id="tool_name">
					</div>

					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">Kode Rak</span>
						<input type="text" id="rack_code" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>

					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">No Kanban</span>
						<input type="text" id="no_kanban" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
						<!-- <input type="hidden" id="no_kanban"> -->
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;color:white">Kategori</span>
						<input type="text" id="kategori" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;color:white">Lifetime</span>
						<input type="text" id="lifetime" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>

					<div class="col-xs-6">
						<span style="font-weight: bold; font-size: 16px;color:white">Lokasi</span>
						<input type="text" id="loc" style="width: 100%; height: 40px; font-size: 25px; text-align: center;">

						<input type="hidden" id="location">
						<input type="hidden" id="group">
					</div>

					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">Lot Kanban</span>
						<input type="text" id="lot_kanban" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>
					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">Stock (Kanban) Total</span>
						<input type="text" id="stock_kanban" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>
					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">Sisa (Pcs) 1 Kanban</span>
						<input type="text" id="balance_kanban" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>
					<div class="col-xs-3">
						<span style="font-weight: bold; font-size: 16px;color:white">Sisa (Pcs) Total</span>
						<input type="text" id="sisa_kanban" style="width: 100%; height: 40px; font-size: 25px; text-align: center;" readonly="">
					</div>

					<div class="col-xs-12">
						<span style="font-weight: bold; font-size: 16px;color:white">Jumlah Pengambilan Tools</span><br>
						<!-- numpad -->
						<input type="number" id="qty" name="qty" class="form-control numpad" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d;">
					</div>

					<div class="col-xs-12">
						<button onclick="confirm()" class="btn btn-success" class="btn btn-danger btn-sm" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px">
							CONFIRM
						</button>
					</div>
				</div>
			</div>

			<div class="col-xs-12" style="margin-top: 20px">
				<div class="box">
					<div class="box-body">
						<span style="font-size: 20px; font-weight: bold;">Penggunaan Data Tools <?= date('d-m-Y') ?></span>
						<table class="table table-hover table-striped table-bordered" id="tabelDataHistory">
							<thead>
								<tr>
									<th style="width: 1%;">Nomor</th>
									<th style="width: 1%;">Tanggal</th>
									<th style="width: 1%;">Operator</th>
									<th style="width: 2%;">Tools</th>
									<th style="width: 2%;">Kode Rak</th>
									<th style="width: 2%;">Kategori</th>
									<th style="width: 2%;">Jumlah</th>
								</tr>
							</thead>
							<tbody id="tablehistory">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card Or Enter NIK" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Pesan Konfirmasi</h4>
				</div>
				<div class="modal-body">
					Apakah anda yakin ingin mengkonfirmasi ini?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button onclick="finalConfirm()" href="#" type="button" class="btn btn-success">Konfirmasi</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="scanModal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-center"><b>SCAN QR CODE HERE</b></h4>
					</div>
					<div class="modal-body">
						<div id='scanner' class="col-xs-12">
							<center>
								<div id="loadingMessage">
									🎥 Unable to access video stream
									(please make sure you have a webcam enabled)
								</div>
								<video autoplay muted playsinline id="video"></video>
								<div id="output" hidden>
									<div id="outputMessage">No QR code detected.</div>
								</div>
							</center>								
						</div>

						<p style="visibility: hidden;">camera</p>
						<input type="hidden" id="code">
					</div>
				</div>
			</div>
		</div>

	@endsection
	@section('scripts')
	<script src="{{ url("js/jsQR.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/jquery.numpad.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
		$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
		$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
		$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
		$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
		$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		jQuery(document).ready(function() {

			$('body').toggleClass("sidebar-collapse");
			$('.select2').select2({
    			dropdownAutoWidth : true,
    			allowClear:true
			});
			
			$('#modalOperator').modal({
				backdrop: 'static',
				keyboard: false
			});

			$('#modalOperator').on('shown.bs.modal', function () {
				$('#operator').focus();
			});

			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});

			$('#qr_code').blur();
			$('#qr_code').val("");
			$('#qr_code').prop('disabled',true);
			// fetchTable();
			cancelAll();
		});

		$('#qr_code').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				var id = $("#qr_code").val();
				scanKanbanTools(id);
			}
		});

		$('#operator').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#operator").val().length >= 8){
					var data = {
						employee_id : $("#operator").val(),
					}
					
					$.get('{{ url("tools/scan/operator") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$('#modalOperator').modal('hide');
							$('#employee_detail').val(result.employee.employee_id+' - '+result.employee.name);
							$('#employee_id').val(result.employee.employee_id);
							$('#employee_name').val(result.employee.name);
							$('#qr_code').removeAttr('disabled');
							$('#qr_code').focus();
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

		function plusCount(){
			$('#qty').val(parseInt($('#qty').val())+1);
		}

		function minusCount(){
			$('#qty').val(parseInt($('#qty').val())-1);
		}

		function cancelAll() {
			// $('#employee_detail').val('');
			// $('#employee_id').val('');
			// $('#employee_name').val('');
			$('#tool_name').val('');
			$('#rack_code').val('');
			$('#kategori').val('');
			$('#lifetime').val('');
			// $('#jumlah_kanban').val('');
			$('#lot_kanban').val('');
			$('#tool').val('');
			$('#tool_id').val('');
			$('#tool_name').val('');
			$('#loc').val('');
			$('#location').val('');
			$('#group').val('');
			$('#qty').val('1');
			$('#operator').val('');
			$('#lot_kanban').val('');
			$('#stock_kanban').val('');
			$('#balance_kanban').val('');
			$('#sisa_kanban').val('');
			$('#qr_code').val("");
			$('#qr_code').prop('disabled', false);
			$('#qr_code').focus();
		}

		function scanKanbanTools(id){
			// var tag = $("#qr_code").val();
			// console.log(id);
			var tool = id.split("_");
			
			var rack = tool[0];
			var item_code = tool[1];
			
			$("#no_kanban").val(tool[2]);
			
			var data = {
				rack : rack,
				item_code : item_code
			}

			$.get('{{ url("fetch/tools/data") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						
						$('#qr_code').prop('disabled', true);

						openSuccessGritter('Success!', result.message);

						$("#tool").val(result.tools.item_code +" - "+result.tools.description);
						$("#tool_id").val(result.tools.item_code);
						$("#tool_name").val(result.tools.description);
						$("#rack_code").val(result.tools.rack_code);
						$("#kategori").val(result.tools.remark);
						$("#lifetime").val(result.tools.lifetime);
						$("#loc").val(result.tools.location+" - "+result.tools.group);
						$("#location").val(result.tools.location);
						$("#group").val(result.tools.group);
						// $("#jumlah_kanban").val(result.tools.stock);
						$("#lot_kanban").val(result.tools.lot_kanban);
						$("#stock_kanban").val(result.tools.stock_kanban);
						$("#balance_kanban").val(result.tools.balance_kanban);

						var sisa_total = 0;
						sisa_total = parseInt(result.tools.lot_kanban) * parseInt(result.tools.stock_kanban) - parseInt(result.tools.lot_kanban) + parseInt(result.tools.balance_kanban);

						$("#sisa_kanban").val(sisa_total);

						// $("#no_kanban").val(result.tools.no_kanban);
						fetchTable();
					}
					else{
						openErrorGritter('Error!', result.message);
						audio_error.play();
						$("#qr_code").val("");
						$("#qr_code").focus();
					}

					$('#scanner').hide();
					$('#scanModal').modal('hide');
					$(".modal-backdrop").remove();
				}
				else{
					alert('Disconnected from server');
				}
			});
		}

		// function selectTools(elem){
			
		// 	var tool = elem.value.split("_");

		// 	var rack = tool[0];
		// 	var item_code = tool[1];
			
		// 	var data = {
		// 		rack : rack,
		// 		item_code : item_code
		// 	}

		// 	$.get('{{ url("fetch/tools/data") }}', data, function(result, status, xhr){
		// 		if(xhr.status == 200){
		// 			if(result.status){
		// 				openSuccessGritter('Success!', result.message);

		// 				$("#tool").val(result.tools.item_code +" - "+result.tools.description);
		// 				$("#tool_id").val(result.tools.item_code);
		// 				$("#tool_name").val(result.tools.description);
		// 				$("#rack_code").val(result.tools.rack_code);
		// 				$("#kategori").val(result.tools.remark);
		// 				$("#lifetime").val(result.tools.lifetime);
		// 				$("#loc").val(result.tools.location+" - "+result.tools.group);
		// 				$("#location").val(result.tools.location);
		// 				$("#group").val(result.tools.group);
		// 				// $("#jumlah_kanban").val(result.tools.stock);
		// 				$("#lot_kanban").val(result.tools.lot_kanban);
		// 				$("#stock_kanban").val(result.tools.stock_kanban);
		// 				$("#balance_kanban").val(result.tools.balance_kanban);
		// 				$("#no_kanban").val(result.tools.no_kanban);
		// 				fetchTable();
		// 			}
		// 			else{
		// 				openErrorGritter('Error!', result.message);
		// 				audio_error.play();
		// 			}
		// 		}
		// 		else{
		// 			alert('Disconnected from server');
		// 		}
		// 	});
		// }

		function confirm(){
			$('#myModal').modal('show');
		}

		function finalConfirm(){
			$("#loading").show();

			var data = {
				employee_id : $("#employee_id").val(),
				employee_name : $("#employee_name").val(),
				item_code : $("#tool_id").val(),
				description : $("#tool_name").val(),
				rack_code : $("#rack_code").val(),
				kategori : $('#kategori').val(),
				lifetime : $('#lifetime').val(),
				location : $('#location').val(),
				group : $('#group').val(),
				lot_kanban : $('#lot_kanban').val(),
				stock_kanban : $('#stock_kanban').val(),
				balance_kanban : $('#balance_kanban').val(),
				qty : $('#qty').val(),
				no_kanban : $('#no_kanban').val()
			}

			$.post('{{ url("post/tools/stock_out") }}', data, function(result, status, xhr){
				if(result.status){		
					$('#myModal').modal('hide');
					openSuccessGritter('Success', result.message);
					fetchTable();
					// scanKanbanTools(result.stock.rack_code+'_'+result.stock.item_code);
					cancelAll();
					$("#loading").hide();

				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}

		function fetchTable(){


			var tools = $('#qr_code').val().split("_");

			var rack = tools[0];
			var item_code = tools[1];

			var data = {
				item_code:item_code
			}

			$.get('{{ url("fetch/tools/order") }}', data, function(result, status, xhr) {
				if(result.status){
					var tableData = "";
					$('#tabelDataHistory').DataTable().clear();
			      	$('#tabelDataHistory').DataTable().destroy();
					$('#tablehistory').html("");

					if(result.order_data.length == 0){
						audio_error.play();
						openErrorGritter('Error!', 'No Tools Usage found');
						$('#loading2').hide();
						return false;
					}

					$.each(result.order_data, function(key, value) {
						tableData += "<tr id='row"+value.id+"'>";
						tableData += "<td>"+(key+1)+"</td>";
						tableData += "<td>"+value.tanggal+"</td>";
						tableData += "<td>"+value.employee_name+"</td>";
						tableData += "<td>"+value.item_code+" "+value.description+"</td>";
						tableData += "<td>"+value.rack_code+"</td>";
						tableData += "<td>"+value.kategori+"</td>";
						tableData += "<td>"+value.qty+"</td>";
						tableData += "</tr>";
					});

					$('#tablehistory').append(tableData);	

					var table = $('#tabelDataHistory').DataTable({
			        'dom': 'Bfrtip',
			        'responsive':true,
			        'lengthMenu': [
			        [ 5, 10, 25, -1 ],
			        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
			          },
			          ]
			        },
			        'paging': true,
			        'lengthChange': true,
			        'pageLength': 10,
			        'searching': true,
			        'ordering': true,
			        'order': [],
			        'info': true,
			        'autoWidth': true,
			        "sPaginationType": "full_numbers",
			        "bJQueryUI": true,
			        "bAutoWidth": false,
			        "processing": true
			      });


					$('#loading2').hide();			
				}
				else{
					$('#loading2').hide();
					audio_error.play();
					openErrorGritter('Error!', 'Attempt to retrieve data failed');
				}	
			});
		}

		function stopScan() {
			$('#scanModal').modal('hide');
		}

		function videoOff() {
			video.pause();
			video.src = "";
			video.srcObject.getTracks()[0].stop();
		}

		$( "#scanModal" ).on('shown.bs.modal', function(){
			showCheck('123');
		});

		$('#scanModal').on('hidden.bs.modal', function () {
			videoOff();
		});

		function showCheck(kode) {
			$(".modal-backdrop").add();
			$('#scanner').show();

			var vdo = document.getElementById("video");
			video = vdo;
			var tickDuration = 200;
			video.style.boxSizing = "border-box";
			video.style.position = "absolute";
			video.style.left = "0px";
			video.style.top = "0px";
			video.style.width = "400px";
			video.style.zIndex = 1000;

			var loadingMessage = document.getElementById("loadingMessage");
			var outputContainer = document.getElementById("output");
			var outputMessage = document.getElementById("outputMessage");

			navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
				video.srcObject = stream;
				video.play();
				setTimeout(function() {tick();},tickDuration);
			});

			function tick(){
				loadingMessage.innerText = "⌛ Loading video..."

				try{

					loadingMessage.hidden = true;
					video.style.position = "static";

					var canvasElement = document.createElement("canvas");            
					var canvas = canvasElement.getContext("2d");
					canvasElement.height = video.videoHeight;
					canvasElement.width = video.videoWidth;
					canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
					var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
					var code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
					if (code) {
						outputMessage.hidden = true;
						videoOff();
						document.getElementById('qr_code').value = code.data;
						scanKanbanTools(code.data);

					}else{
						outputMessage.hidden = false;
					}
				} catch (t) {
					console.log("PROBLEM: " + t);
				}

				setTimeout(function() {tick();},tickDuration);
			}

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

</script>
@endsection