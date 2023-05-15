@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">


	.nmpd-grid {border: none; padding: 20px; top: 100px !important}
	.nmpd-grid>tbody>tr>td {border: none;}

	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	thead>tr>th{
		font-size: 16px;
	}

	#div_pallet{
		overflow:auto;
		/*width: 500px;*/
		height: 400px;
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

	.input {
		text-align: center;
		font-weight: bold;
	}

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
	<div class="col-xs-12" >
		<div class="box box-solid">
			<div class="box-body">
				<div class="col-md-12" id="detail_tabel1">
					<div class="col-xs-12" style="background-color: #6A5ACD;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CHECKS MATERIALS</h1>
					</div>
					<div class="col-md-12" id="total1">
						<span style="font-size: 20px; font-weight: bold;">Total Check:</span>
					</div>
					<div class="col-md-12" style="padding-top: 8px" id="progres1">
						<div class="progress-group" id="progress_div">
							<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;">

								<div class="progress-bar" id="progress_bar_production1" style="font-size: 30px; padding-top: 10px;">
									<span class="progress-text" id="progress_text_production1" style="font-size: 25px; padding-top: 10px;" hidden></span>
								</div>
								<span id="Progress_te" style="font-size: 20px; font-weight: bold;"></span>

							</div>
						</div>
					</div>

					<div class="col-md-12" id="tab" style="padding-top: 8px; overflow-x: scroll;">
						<table class="table table-hover table-striped" id="tableList"  style="font-size: 15px; width: 100%; font-weight: bold; ">
							<thead>
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 1%;">No Pallet</th>
									<th style="width: 1%;">GMC</th>
									<th style="width: 2%;">Description</th>
									<th style="width: 2%;">Quantity</th>
									<th style="width: 1%;">Status</th>
									<th id="qc" style="width: 1%;">Quantity Check</th>
								</tr>					
							</thead>
							<tbody id="tableBodyList">
							</tbody>
							<tfoot>
								<tr>
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


					<div class="col-xs-12">
						<button onclick="final(this.id, this.value)"  id="pallet" class="btn btn-success" class="btn btn-danger btn-sm" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px">
							MOVE PALLET
						</button>
					</div>
				</div>

				<div class="col-md-12" id="detail_tabel2">

					<div class="col-xs-12" id="btn_jud" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: black">MOVE MATERIAL TO STORAGE</h1>
					</div>
					<div class="col-md-12" id="tab_2" style="padding-top: 8px; overflow-x: scroll;">
						<table class="table table-hover table-striped" id="tableList2"  style="font-size: 15px; width: 100%; font-weight: bold; ">
							<thead>
								<tr>
									<th style="width: 1%;">GMC</th>
									<th style="width: 1%;">Description</th>
									<th style="width: 1%;">No Pallet</th>
									<th style="width: 1%;">Quantity</th>
									<th style="width: 2%;">Area</th>
									<th style="width: 2%;">Action</th>

								</tr>					
							</thead>
							<tbody id="tableBodyList2">
							</tbody>
							<tfoot>
								<tr>
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


					<div class="col-xs-12">
						<button onclick="end_move()" id="pallet2" class="btn btn-danger btn-sm" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px">
							END MOVE 
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modal_detail" style="color: black;">
		<div class="modal-dialog " style="width: 1250px">

			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Detail Pallet</b></h4>

					<h5 class="modal-title" style="text-align: center;" id="judul-operator"></h5>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="tabelDataDetail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="operator-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>GMC</th>
										<th>Description</th>
										<th>No Pallet</th>
										<th>Quantity</th>
										<th>Area</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="tabelBodyDetail">
								</tbody>
							</table>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modalScan" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<center><h3 style="background-color: #ff851b; font-weight: bold;">SCAN QR CODE HERE</h3></center>
				</div>
				<div class="modal-body">
					<div class="input-group col-md-12">
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
							<i class="glyphicon glyphicon-barcode"></i>
						</div>
						<input type="text" style="text-align: center; font-size: 15" class="form-control" id="scan_qrcode_material" name="scan_qrcode_material" placeholder="Scan Material Request" required>
						<div class="input-group-addon" id="icon-serial">
							<i class="glyphicon glyphicon-ok"></i>
						</div>
					</div>
					<br>
					<div class="modal-body table-responsive no-padding">
						<div id='scanner' class="col-xs-12">
							<div class="col-xs-12">
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
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="col-md-6" hidden>
		<div class="input-group input-group-lg">
			<div class="input-group-addon" id="icon-serial" style="font-weight: bold;">
				<i class="fa fa-qrcode"></i>
			</div>
			<input type="hidden" class="form-control" placeholder="SCAN QR AREA" id="item_scan">
			<div class="input-group-btn">
				<button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#modalScan"><i class="fa fa-qrcode"></i> Scan QR</button>
			</div>
		</div>
		<br>
		<input type="hidden" id="code">
		<input type="hidden" id="loc">
		<input type="hidden" id="id_button">
		<input type="hidden" id="id_button1">
		<input type="hidden" id="login_time">
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Konfirmasi Pengecekan</h4>
				</div>
				<div class="modal-body">
					Apakah anda yakin sudah sesuai?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button onclick="finalConfirm()" href="#" type="button" class="btn btn-success">Konfirmasi</a>
					</div>
				</div>
			</div>
		</div>

		@endsection
		@section('scripts')
		<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
		<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
		<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
		<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>


		<script>


			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%; "></table>';
			$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
			$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
			$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
			$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
			$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};



			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
			var button = 1;
			var len = 0;
			var val = 0;
			var persen = 0;
			var area;
			var loc;
			var kouta = [];


			$('#pallet').hide();
			$('#pallet2').hide();
			$('#detail_tabel1').show();
			$('#detail_tabel2').hide();
			$('#btn_jud').hide();
			$('#tds').hide();
			$('#scan_qrcode_material').val("");


			jQuery(document).ready(function() {

				$('body').toggleClass("sidebar-collapse");
				$('.select2').select2({
					dropdownAutoWidth : true,
					allowClear:true
				});



			// setInterval(joblist, 5000);

			// joblist();
			// get_job_now();
			selectJob();


			$('#gmc').val("");
			$('#country').val("");

			$("#item_scan").val("");
			$("#scan_por").focus();

			$("#code").val("");
			$("#loc").val("");
			$("#login_time").val("");

			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});



			$("#op_name").val("{{ Auth::user()->name }}");

			area = <?php echo json_encode($area); ?>;


        // fetchTable();

        fetchjob();
    });


			function cancelRequest(){
				$('#tableList2').DataTable().clear();
				$('#tableList2').DataTable().destroy();
				$('#tableBodyList2').html("");
			}

			function checkQty(qty,value,id) {
				var index = id;
				var i = index.replace('qty_','');

				if (value > qty ) {
					alert("Quantity check more than quantity request")
			// $(id).val();
			$('#qty_'+i).val("");
		}
	}

	function stopScan() {
		$('#modalScan').modal('hide');
	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	$('#scan_qrcode_material').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {

			var video = document.createElement("video");
			vdo = video;
			var loadingMessage = document.getElementById("loadingMessage");

			var outputContainer = document.getElementById("output");
			var outputMessage = document.getElementById("outputMessage");
			if($("#scan_qrcode_material").val().length > 3){
				checkCode(video,$("#scan_qrcode_material").val());
				return false;
			}
			else{
				openErrorGritter('Error!', 'QR Code Tidak Cocok');
				$("#scan_qrcode_material").val("");
				audio_error.play();
			}
		}
	});

	$('#modalScan').on('hidden.bs.modal', function () {
		videoOff();
	});

	function showCheck() {
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
				// videoOff();
				checkCode(video, code.data);
			}else{
				outputMessage.hidden = false;
			}
			setTimeout(function() {tick();},tickDuration);
		}
	}

	function checkCode(video, code) {
		stat = false;
		var loc = "";

		if (code == $("#loc").val()) {
			$('#scanner').hide();
			videoOff();
			openSuccessGritter('Success', 'QR Code Successfully');
			$("#modalScan").modal('hide');
			var id = $('#id_button').val();
			var ids = id.split('_');

			$('#stat_'+ids[1]).html('');
			$('#stat_'+ids[1]).html('SESUAI');
			$('#tdtext_'+ids[1]).val('');
			$('#tdtext_'+ids[1]).val('SESUAI');
			$('#btnscan_'+ids[1]).hide();
			$('#scan_qrcode_material').val("");
		}else{
			$('#scan_qrcode_material').val("");
			openErrorGritter('Error', 'QR Code Not Registered');
			audio_error.play();
		}

	}

		// function joblist(){
		// 	var data = {
		// 		no_invoice : "{{Request::segment(3)}}"
		// 	}

		// 	$.get('{{ url("fetch/list/internal") }}', data,function(result, status, xhr){
		// 		if(result.status){	
		// 			$('#div_pallet').html("");
		// 			var btn_pallet = "";
		// 			button = 0;

		// 			for (var i = 0; i < result.job.length; i++) {					
		// 				if (result.status_emp.length > 0) {
		// 					if (result.job[i].pic_job == '{{$name}}') {
		// 						btn_pallet += '<button  id="but_'+button+'"onclick="selectJob('+i+')" class="btn btn-primary"  class="btn btn-danger btn-sm" style="font-size: 2vw; width: 100%; overflow-x: scroll; font-weight: bold; ">No Packing : <span id="test'+i+'">'+(result.job[i].no_case || "" || "")+'</span> <span id="pic'+i+'">'+(result.job[i].FIRSTTNAME || "" )+'</span> ';
		// 						btn_pallet += '</button> <br><br>';
		// 					}else{
		// 						btn_pallet += '<button id="but_'+button+'"onclick="selectJob('+i+')" class="btn btn-primary"  class="btn btn-danger btn-sm" style="font-size: 2vw; width: 100%; overflow-x: scroll; font-weight: bold; " disabled>No Packing : <span id="test'+i+'">'+(result.job[i].no_case || "" || "")+'</span> <span id="pic'+i+'">'+(result.job[i].FIRSTTNAME || "" )+'</span> ';
		// 						btn_pallet += '</button> <br><br>';
		// 					}

		// 				}else{

		// 					if (result.job[i].pic_job == null) {
		// 						btn_pallet += '<button id="but_'+button+'"onclick="selectJob('+i+')" class="btn btn-primary"  class="btn btn-danger btn-sm" style="font-size: 2vw; width: 100%; overflow-x: scroll; font-weight: bold; " >No Packing : <span id="test'+i+'">'+(result.job[i].no_case || "" || "")+'</span> <span id="pic'+i+'">'+(result.job[i].FIRSTTNAME || "" )+'</span> ';
		// 						btn_pallet += '</button> <br><br>';
		// 					}else if (result.job[i].pic_job == '{{$name}}') {
		// 						btn_pallet += '<button id="but_'+button+'"onclick="selectJob('+i+')" class="btn btn-primary"  class="btn btn-danger btn-sm" style="font-size: 2vw; width: 100%; overflow-x: scroll; font-weight: bold; ">No Packing : <span id="test'+i+'">'+(result.job[i].no_case || "" || "")+'</span> <span id="pic'+i+'">'+(result.job[i].FIRSTTNAME || "" )+'</span> ';
		// 						btn_pallet += '</button> <br><br>';
		// 					} else {
		// 						btn_pallet += '<button id="but_'+button+'"onclick="selectJob('+i+')" class="btn btn-primary"  class="btn btn-danger btn-sm" style="font-size: 2vw; width: 100%; overflow-x: scroll; font-weight: bold; " disabled>No Packing : <span id="test'+i+'">'+(result.job[i].no_case || "" || "")+'</span> <span id="pic'+i+'">'+(result.job[i].FIRSTTNAME || "" )+'</span></button> <br><br>';
		// 					}
		// 				}


		// 				button += 1;
		// 			}
		// 			$('#div_pallet').append(btn_pallet);
		// 		}else{
		// 			openErrorGritter('Error!', result.message);
		// 		}
		// 	});
		// }

		// function get_job_now() {
		// 	$.get('{{ url("index/import/get_job_now") }}',  function(result, status, xhr){
		// 		if(result.status){
		// 			if(result.datasu.length != 0){
		// 				$('#tableList').DataTable().clear();
		// 				$('#tableList').DataTable().destroy();
		// 				$('#tableBodyList').html("");
		// 				var tableData = "";
		// 				var count = 1;
		// 				counts = 1;
		// 				var total ="";

		// 				$.each(result.datasu, function(key, value) {
		// 					tableData += '<tr id='+count+'>';
		// 					tableData += '<td> <input type="hidden" id="idis_'+count+'" value="'+value.id+'">'+ count +'</td >';

		// 					tableData += '<td id="cob_'+count+'">'+ value.no_case +'</td>';
		// 					tableData += '<td >'+ value.gmc +'</td>';
		// 					tableData += '<td >'+ value.description +'</td>';
		// 					tableData += '<td id="qty_awal_'+count+'" style="text-align:center;">'+ value.quantity +'</td>';
		// 					tableData += '<td>';
		// 					tableData += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" class="numpad'+count+' input" id="qty_'+count+'>';
		// 					tableData += '</td>'
		// 					tableData += '<td>';
		// 					if (value.quantity_check == null) {
		// 						tot = "";
		// 						tableData += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" class="numpad'+count+' input" id="qty_'+count+'" onchange="checkQuantity('+count+',this.value,this.id,'+value.quantity+')" >';
		// 					}else{
		// 						tot = value.quantity_check;
		// 						tableData += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" class="numpad'+count+' input" value="'+tot+'" id="qty_'+count+'" onchange="checkQuantity('+count+',this.value,this.id)">';
		// 					}
		// 					tableData += '<td >'+ value.gmc +'</td>';
		// 					tableData += '</td>';

		// 					tableData += '</tr>';

		// 					counts++;
		// 				});

		// 				$('#tableBodyList').append(tableData);
		// 				$('.numpad'+count).numpad({
		// 					hidePlusMinusButton : false,
		// 					decimalSeparator : '.'
		// 				});
		// 				count += 1;
		// 				var tableList = $('#tableList').DataTable({
		// 					'dom': 'Bfrtip',
		// 					'responsive':true,
		// 					'lengthMenu': [
		// 					[ 5, 10, 25, -1 ],
		// 					[ '5 rows', '10 rows', '25 rows', 'Show all' ]
		// 					],
		// 					'buttons': {
		// 						buttons:[
		// 						{
		// 							extend: 'pageLength',
		// 							className: 'btn btn-default',
		// 						}
		// 						]
		// 					},
		// 					'paging': true,
		// 					'lengthChange': true,
		// 					'pageLength': 5,
		// 					'searching': false,
		// 					'ordering': true,
		// 					'order': [],
		// 					'info': true,
		// 					'autoWidth': true,
		// 					"sPaginationType": "full_numbers",
		// 					"bJQueryUI": true,
		// 					"bAutoWidth": false,
		// 					"processing": true
		// 				});

		// 				tableList.columns().every( function () {
		// 					var that = this;

		// 					$( 'input', this.footer() ).on( 'keyup change', function () {
		// 						if ( that.search() !== this.value ) {
		// 							that
		// 							.search( this.value )
		// 							.draw();
		// 						}
		// 					} );
		// 				} );

		// 				openSuccessGritter('Success!', result.message);
		// 				$('#pallet').show();
		// 				var count2 = 1;

		// 				$.each(result.detail, function(key, value) {
		// 					$('#case_'+count2).html("");
		// 					var option = "";
		// 					$.each(result.job, function(key2, value2) {
		// 						if (value2.no_case == value.no_case) {
		// 							option += '<option selected value="'+value2.no_case+'">'+value2.no_case+'</option>';
		// 						}else{
		// 							option += '<option value="'+value2.no_case+'">'+value2.no_case+'</option>';
		// 						}
		// 					});
		// 					$('#case_'+count2).append(option);
		// 					count2++;
		// 				});
		// 			}else{

		// 			}
		// 			openSuccessGritter('Success!', result.message);
		// 		}
		// 		else{
		// 			openErrorGritter('Error!', result.message);
		// 			audio_error.play();

		// 		}
		// 	});
		// }

		var counts;
		function selectJob(no){
			var data = {
				no_case : "{{Request::segment(3)}}"
			}

			val = 0;
			num = 1;

			persen = 0;

			$.get('{{ url("index/import/get_job_now") }}',data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#tableList').DataTable().clear();
						$('#tableList').DataTable().destroy();
						$('#tableBodyList').html("");
						var tableData = "";
						var count = 1;
						var num = "";
						var tot = "";
						var ok = "";
						var no = "";
						counts = 1;
						len = result.datasu.length;
						$('#Progress_te').text('Total Check:'+val+"/"+len);
						
						$.each(result.datasu, function(key, value) {
							$("#kondisi_"+count).val(value.status).trigger('change.select2');
							$('#qc').hide();
							tableData += '<tr id='+count+'>';
							tableData += '<td> <input type="hidden" id="idis_'+count+'" value="'+value.id+'">'+ count +'</td >';

							tableData += '<td> <input type="hidden" id="test'+count+'" value="'+value.no_case+'">'+ value.no_case +'</td>';
							tableData += '<td >'+ value.gmc +'</td>';
							tableData += '<td >'+ value.description +'</td>';
							tableData += '<td id="qty_awal_'+count+'">'+ value.quantity +'</td>';

							tableData += '<td id ="status_mt"><select class="form-control select2" id="kondisi_'+count+'" name="kondisi" style="width: 100%;" data-placeholder="Status" onChange="checkselect(this.id,\''+value.quantity+'\',\''+value.quantity_check+'\',\''+value.status+'\','+count+')" required="" ><option></option><option value="OK" >OK</option><option value="NO">NO</option></select></td>';


							tableData += '<td id="tds_'+count+'" style="width:5%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;" hidden>';
							tot = "";

							tableData += '<input type="text" style="font-size:20px; width: 100%; height: 40px;" class="numpad'+count+' input" value="'+tot+'" id="qty_'+count+'" onchange="checkQuantity('+count+',this.value,this.id,'+value.quantity+')">';

							tableData += '</td>';
							tableData += '</tr>';

							$('#qty_'+value.id).addClass('numpad');

							$('#tableBodyList').append(tableData);

							$(".select2").select2();

							$("#kondisi_"+count).val(value.status).trigger('change');



							$('.numpad'+count).numpad({
								hidePlusMinusButton : true,
								decimalSeparator : '.'
							});

							count += 1;
							counts++;
							tableData = "";
						});
						$('#pallet').show();

						

						var tableList = $('#tableList').DataTable({
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
								}
								]
							},
							'paging': true,
							'lengthChange': true,
							'pageLength': 5,
							'searching': false,
							'ordering': true,
							'order': [],
							'info': true,
							'autoWidth': true,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
							"bAutoWidth": false,
							"processing": true
						});

						tableList.columns().every( function () {
							var that = this;

							$( 'input', this.footer() ).on( 'keyup change', function () {
								if ( that.search() !== this.value ) {
									that
									.search( this.value )
									.draw();
								}
							} );
						} );

						openSuccessGritter('Success!', result.message);


						var count2 = 1;

						$.each(result.detail, function(key, value) {
							$('#case_'+count2).html("");
							var option = "";
							$.each(result.job, function(key2, value2) {
								if (value2.no_case == value.no_case) {
									option += '<option selected value="'+value2.no_case+'">'+value2.no_case+'</option>';
								}else{
									option += '<option value="'+value2.no_case+'">'+value2.no_case+'</option>';
								}
							});
							$('#case_'+count2).append(option);
							count2++;
						});
					}
					else{
						openErrorGritter('Error!', result.message);
						audio_error.play();
					}
				}
				else{
					alert('Disconnected from server');
				}
			});


}

function checkselect(elem,qty,qty_check,st,nm){
	var id = elem;
	var baris = id.split("_");
	if ($('#kondisi_'+baris[1]).val() != "") {
		val++;
		if(val > len){
			val = len;
		}
		var item = document.getElementById( nm );
		if (item.style.backgroundColor != 'rgb(100, 149, 237)') {
			persen = val / parseInt(len) * 100;
			kouta.push(persen);
			$('#Progress_te').hide();
			$('#progress_text_production1').show();
			$('#Progress_text').hide();
			$('#progress_bar_production1').css('width', persen+'%');
			$('#progress_text_production1').html('Total Check :' +val+"/"+len);

			var style2 = 'red';
			if (val == len) {

				style2 = 'green';
			}else{
				style2 = 'red';   
			}
			$('#progress_bar_production1').removeAttr('class');
			$('#progress_bar_production1').addClass('progress-bar');
			$('#progress_bar_production1').addClass('progress-bar-'+style2);
			$('#progress_bar_production1').addClass('progress-bar-striped');
		}
		if (parseFloat(qty) < parseFloat($('#qty_awal_'+nm).text())) {

			for(var l = 1; l <= $("#tableList > tbody > tr").length; l++){
				$('#case_'+nm).show();
				$('#'+nm).css("background-color", '#59d141');
			}
		}else{
			for(var l = 1; l <= $("#tableList > tbody > tr").length; l++){
				$('#case_'+nm).hide();
				$('#'+nm).css("background-color",'#59d141');
			}
		}
	}

	if ($('#kondisi_'+baris[1]).val() == "OK") {
		$('#qc').show();
		$('#kondisi_'+baris[1]).show();
		$('#tds_'+baris[1]).show();
		$('#qty_'+baris[1]).val(qty);
	}else if (st == "NO" && qty_check != null){
		$('#qc').show();
		$('#kondisi_'+baris[1]).show();
		$('#tds_'+baris[1]).show();
		$('#qty_'+baris[1]).val(qty);
	}else if (st == "OK" && qty_check != null){
		$('#qc').show();
		$('#kondisi_'+baris[1]).show();
		$('#tds_'+baris[1]).show();
		$('#qty_'+baris[1]).val(qty);
	}else {
		$('#kondisi_'+baris[1]).show();
		$('#tds_'+baris[1]).show();
		$('#qc').show();
		$('#qty_'+baris[1]).val("");
	}


}

function checkQuantity(id,value,num,qty){
	var indexs = num;
	var i = indexs.replace('qty_','');

	if ($('#kondisi_'+i).val() == "OK" && value > qty) {
		alert("Quantity check more than quantity request")
			// $(id).val();
			$('#qty_'+i).val("");
		}
		else{
		}
	}


	function coba(gmc,id){
		$('#modalScan').modal('show');
		$("#scan_por").focus();
		$("#loc").val(gmc);
		$("#id_button").val(id);
		showCheck();
	}



	function final(id){

		if ($("#tableList > tbody > tr").length == 0) {
				// 
				alert('data belum dipilih');
				$('#myModal').modal('hidden');

			}else {
				$('#myModal').modal('show');

			}
		}

		function finalConfirm(){
			var status_false = 0;
			var id = [];
			var qty = [];
			var kds = [];
			var no_gmc = [];
			persen = 0;

			for(var l = 1; l <= $("#tableList > tbody > tr").length; l++){
				if ($('#qty_'+l).val() == "") {
					openErrorGritter('Error!', "Gagal");
					$('#myModal').modal('show');
					status_false = 0;
				}else {
					status_false = 1;
				}

				if (status_false == 1) {

					id.push($('#idis_'+l).val());
					qty.push($('#qty_'+l).val());
					kds.push($('#kondisi_'+l).val());
					no_gmc.push($('#test'+l).val());

					$('#detail_tabel1').hide();
					$('#detail_tabel2').show();
					$('#pallet').hide();
					$('#pallet2').show();
					$('#total1').hide();
					$('#progres1').hide();


					$('#progress_bar_production1').css('width', persen+'%');
					$('#progress_text_production1').html('');

					var data = {
						id : id,
						counts:counts,
						qty:qty,
						kds:kds,
						no_gmc:no_gmc,
						no_mt : "{{Request::segment(3)}}"

					}
					$.post('{{ url("post/detail/save") }}', data, function(result, status, xhr){
						if(result.status){		
							openSuccessGritter('Success', result.message);
							$('#tableList2').DataTable().clear();
							$('#tableList2').DataTable().destroy();
							$('#tableBodyList2').html("");
							$('#myModal').modal('hide');
							$('#btn_jud').show();
							var tableData = "";
							var indx = 1;
							for (var i = 0; i < result.detail_mod.length; i++) {
								tableData += '<tr>';
								tableData += '<td class="gmc">'+ result.detail_mod[i].gmc +'</td>';
								tableData += '<td>'+ result.detail_mod[i].description +'</td>';
								tableData += '<td class="case_pallet">'+ result.detail_mod[i].no_case +'</td>';
								tableData += '<td>'+ result.detail_mod[i].quantity +'<input type="hidden" class="id" value="'+result.detail_mod[i].id+'"></td>'
								tableData += '<td>'+ result.detail_mod[i].store +'</td>';
								tableData += '<td class="status" id="stat_'+indx+'">'+ "<button id='btnscan_"+indx+"'  onclick='coba(\""+result.detail_mod[i].gmc+"\",this.id);' class='btn btn-info'><i class='fa fa-camera-retro'></i> </button>" +'<input type="hidden" id="tdtext_'+indx+'"></td>';
								tableData += '</tr>';
								indx++;
							}

							$('#tableBodyList2').append(tableData);

							var table = $('#tableList2').DataTable({

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
								'pageLength': 7,
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

						}
						else{
							openErrorGritter('Error!', result.message);
						}
					});
				}
			}
		}


		function fetchjob(){

			$.get('{{ url("fetch/finish/job") }}', function(result, status, xhr){
				if(result.status){
					$('#tabelDataHistory').DataTable().clear();
					$('#tabelDataHistory').DataTable().destroy();
					$('#tablehistory').html("");
					var tableData = "";
					for (var i = 0; i < result.datam.length; i++) {

						tableData += '<tr>';
						tableData += '<td>'+ result.datam[i].no_case +'</td>';
						tableData += '<td>'+ result.datam[i].gmc +'</td>';
						tableData += '<td>'+ result.datam[i].description +'</td>';
						tableData += '<td>'+ result.datam[i].quantity +'</td>';
						tableData += '<td>'+ result.datam[i].quantity_check +'</td>';
						tableData += '<td>'+ result.datam[i].status+'</td>';
						tableData += '<td>'+ result.datam[i].minute+":"+result.datam[i].second+ '</td>';
						tableData += '</tr>';
					}


					$('#tablehistory').append(tableData);

					var table = $('#tabelDataHistory').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 7, 25, 50, -1 ],
						[ '7 rows', '25 rows', '50 rows', 'Show all' ]
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
						'pageLength': 7,
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
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}

		function changeVal(id){
			var qty = $("#qty_"+id).val();
		}

		function getBtnBlock(id) {
			$('#check_'+id).hide();
			$('#cancel_'+id).show();

		}
		function getBtncancel(id) {
			$('#check_'+id).show();
			$('#cancel_'+id).hide();

		}

		function end_move() {
			var gmc1 = [];
			var case_pallet1 = [];
			var status1 = [];
			var ids = [];

			$('.id').each(function(index, value) {
				ids.push($(value).val());
			});

			$('.gmc').each(function(index, value) {
				gmc1.push($(value).html());
			});
			$('.case_pallet').each(function(index, value) {
				case_pallet1.push($(value).html());
			});

			$('.status').each(function(index, value) {
				status1.push($(value).html());
			});

			for(var l = 1; l <= $("#tableList2 > tbody > tr").length; l++){
				if ($('#tdtext_'+l).val() == "") {
					alert("Data Tidak Boleh Kosong");
					$('#myModal').modal('hide');
				}else{
					var data = {
						gmc :gmc1,
						case_pallet:case_pallet1,
						ids:ids
					}
					$.post('{{ url("post/finish_inter") }}',data, function(result, status, xhr){
						if(result.status == true){
							openSuccessGritter('Success','Tambah Data Berhasil');	
							cancelRequest();
							window.location.href = "{{secure_url('index/monitoring/internal')}}";
							$('#pallet2').hide();
							$('#pallet').hide();
							$('#btn_jud').hide();
							$('#detail_tabel1').hide();
							$('#total1').hide();
							$('#progres1').hide();

						}else{
							openErrorGritter('Error!','Tambah Data Gagal');
						}
					})

				}
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