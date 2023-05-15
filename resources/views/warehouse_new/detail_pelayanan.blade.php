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
						<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CHECK TAKING MATERIALS</h1>
					</div>
					<div class="col-md-12" id="tab" style="padding-top: 8px;">
						<table class="table table-hover table-striped" id="tableLists"  style="font-size: 15px; width: 100%; font-weight: bold; ">
							<thead>
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 2%;">Kode Request</th>
									<th style="width: 1%;">No Hako</th>
									<th style="width: 1%;">GMC</th>
									<th style="width: 5%;">Description</th>
									<th style="width: 2%;">Lot</th>
									<th style="width: 2%;">Quantity Request</th>
									<th style="width: 2%;">No Rak</th>
									<th style="width: 3%;">Check</th>
									<th style="width: 3%;">Quantity Check</th>
								</tr>					
							</thead>
							<tbody id="tableBodyLists">
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

								</tr>
							</tfoot>
						</table>
					</div>


					<div class="col-xs-12">
						<button id="save" class="btn btn-success" class="btn btn-danger btn-sm" onclick="final(this.id)" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;margin-top: 20px">
							Save
						</button>
					</div>
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
	<input type="hidden" id="login_time">
	<input type="hidden" id="check_code2">


</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Konfirmasi Pengecekan Material</h4>
			</div>
			<div class="modal-body">
				Apakah data sudah sesuai?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button onclick="submitPelayanan(this.id)" href="#" type="button" class="btn btn-success">Save</a>
				</div>
			</div>
		</div>
	</div>
</div>

</section>


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
	var video;
	var code1 = [];



	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			dropdownAutoWidth : true,
			allowClear:true
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		fetchjob();
			// fetchHistory();
			// setInterval(fetchHistory, 10000);

			$('#gmc').val("");
			$('#country').val("");

			$("#item_scan").val("");
			$("#code").val("");
			$("#loc").val("");
			$("#login_time").val("");
			$("#scan_qrcode_material").val("");


			$("#op_name").val("{{ Auth::user()->name }}");

		});

	function showCam() {
		$('#modalScan').modal('show');
		$('#scanner').show();
		showCheck2();
		var cods = 1;
		$("#check_code2").val(cods);
	}

	function stopScan() {
		$('#modalScan').modal('hide');

	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	function final(id){

		if ($("#tableLists > tbody > tr").length == 0) {
				// 
				alert('data belum dipilih');
				$('#myModal').modal('hidden');

			}else {
				$('#myModal').modal('show');

			}
		}



		$('#scan_qrcode_material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {

				var video = document.createElement("video");
				vdo = video;
				var loadingMessage = document.getElementById("loadingMessage");

				var outputContainer = document.getElementById("output");
				var outputMessage = document.getElementById("outputMessage");
				if($("#scan_qrcode_material").val().length > 3){
					checkCode2(video,$("#scan_qrcode_material").val(),"1");
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
			$("#scanner").hide();
			$("#check_code2").val("");
			$("#scan_qrcode_material").val("");
		});

		function fetchjob(){
			var data = {
				kode_request : "{{Request::segment(3)}}"
			}

			$.get('{{ url("fetch/detail/request") }}',data, function(result, status, xhr){
				if(result.status){
					$('#tableLists').DataTable().clear();
					$('#tableLists').DataTable().destroy();
					$('#tableBodyLists').html("");
					var tableData = "";
					var num=1;

					$.each(result.job, function(key, value) {
						tableData += '<tr>';
						tableData += '<td> <input type="hidden" id="idis_'+num+'" value="'+value.id+'">'+ num +'</td >';
						tableData += '<td>'+ value.kode_request +'</td>';
						tableData += '<td>'+ value.no_hako +'</td>';
						tableData += '<td class="gmc">'+ value.gmc +'</td>';
						tableData += '<td>'+ value.description +'</td>';
						tableData += '<td>'+ value.lot +'</td>';
						tableData += '<td>'+ value.quantity_request +'</td>';
						if (value.rak == null) {
							tableData += '<td>-</td>';	
						}else{
							tableData += '<td>'+ value.rak +'</td>';
						}
						
						tableData += '<td id="tdscan_'+num+'">'+ "<button id='btnscan_"+num+"' onclick='coba(\""+value.gmc+"\",this.id);' class='btn btn-info'><i class='fa fa-camera-retro'></i> </button>" +'</td>';
					// tableData += '<td id ="status_mt"><select class="form-control select2" id="kondisi_'+num+'" name="kondisi" style="width: 100%; display: none;" data-placeholder="Status" onChange="checkselect(this.id,\''+value.quantity_request+'\','+num+',\''+value.status+'\')" required="" ><option></option><option value="OK" >OK</option><option value="NO">NO</option></select></td>';
					tableData += '<td class="numpad input qty" onchange="checkQty('+value.quantity_request+',this.value,this.id)" id="qty_'+num+'" style="display: none;" >'+ value.quantity_request +'</td>';

					// tableData += '<td style="width:5%; font-weight: bold; font-size: 20px; padding-bottom:2px; padding-top:2px;">';
					// tableData += '<input type="text" style="font-size:20px; width: 100%; height: 40px; display: none" class="numpad input" onchange="checkQty('+value.quantity_request+',this.value,this.id)" id="qty_'+num+'">';
					// tableData += '</td>'
					tableData += '</tr>';

					$('#qty_'+value.id).addClass('numpad');
					num += 1;
				});


					$('#tableBodyLists').append(tableData);
					$('.numpad').numpad({
						hidePlusMinusButton : true,
						decimalSeparator : '.'
					});

					$('#tableLists').DataTable({
		
		'paging': false,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'order': [],
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bAutoWidth": true,
		"processing": true
	});

				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}

		function checkselect(elem,qty,nm,st){
			var id = elem;
			var baris = id.split("_");

			if ($('#kondisi_'+baris[1]).val() == "OK") {
				$('#kondisi_'+baris[1]).show();
				$('#tds_'+baris[1]).show();
				$('#qty_'+baris[1]).val(qty);
			}else if (st == "NO" && qty_check != null){
				$('#kondisi_'+baris[1]).show();
				$('#tds_'+baris[1]).show();
				$('#qty_'+baris[1]).val(qty);
			}else if (st == "OK" && qty_check != null){
				$('#kondisi_'+baris[1]).show();
				$('#tds_'+baris[1]).show();
				$('#qty_'+baris[1]).val(qty);
			}else {
				$('#kondisi_'+baris[1]).show();
				$('#tds_'+baris[1]).show();
				$('#qty_'+baris[1]).val("");

			}


		}

		function checkQty(qty,value,id) {
			var index = id;
			var i = index.replace('qty_','');

			if ($('#kondisi_'+i).val() == "OK" && value > qty ) {
				alert("Quantity check more than quantity request")
			// $(id).val();
			$('#qty_'+i).val(qty);
		}else{

		}
	}


	// function fetchHistory(){
	// 	$.get('{{ url("fetch/history/pelayanan") }}', function(result, status, xhr){
	// 		if(result.status){
	// 			$('#tabelDataHistory1').DataTable().clear();
	// 			$('#tabelDataHistory1').DataTable().destroy();
	// 			$('#tablehistory1').html("");
	// 			var tableData = "";
	// 			for (var i = 0; i < result.history.length; i++) {

	// 				tableData += '<tr>';
	// 				tableData += '<td>'+ result.history[i].kode_request +'</td>';
	// 				tableData += '<td>'+ result.history[i].gmc +'</td>';
	// 				tableData += '<td>'+ result.history[i].description +'</td>';
	// 				tableData += '<td>'+ result.history[i].quantity_request +'</td>';
	// 				tableData += '<td>'+ result.history[i].status_pel +'</td>';

	// 				tableData += '</tr>';
	// 			}


	// 			$('#tablehistory1').append(tableData);

	// 			var table = $('#tabelDataHistory1').DataTable({
	// 				'dom': 'Bfrtip',
	// 				'responsive':true,
	// 				'lengthMenu': [
	// 				[ 7, 25, 50, -1 ],
	// 				[ '7 rows', '25 rows', '50 rows', 'Show all' ]
	// 				],
	// 				'buttons': {
	// 					buttons:[
	// 					{
	// 						extend: 'pageLength',
	// 						className: 'btn btn-default',
	// 					},
	// 					{
	// 						extend: 'copy',
	// 						className: 'btn btn-success',
	// 						text: '<i class="fa fa-copy"></i> Copy',
	// 						exportOptions: {
	// 							columns: ':not(.notexport)'
	// 						}
	// 					},
	// 					{
	// 						extend: 'excel',
	// 						className: 'btn btn-info',
	// 						text: '<i class="fa fa-file-excel-o"></i> Excel',
	// 						exportOptions: {
	// 							columns: ':not(.notexport)'
	// 						}
	// 					},
	// 					{
	// 						extend: 'print',
	// 						className: 'btn btn-warning',
	// 						text: '<i class="fa fa-print"></i> Print',
	// 						exportOptions: {
	// 							columns: ':not(.notexport)'
	// 						}
	// 					}
	// 					]
	// 				},
	// 				'paging': true,
	// 				'lengthChange': true,
	// 				'pageLength': 7,
	// 				'searching': true	,
	// 				'ordering': true,
	// 				'order': [],
	// 				'info': true,
	// 				'autoWidth': true,
	// 				"sPaginationType": "full_numbers",
	// 				"bJQueryUI": true,
	// 				"bAutoWidth": false,
	// 				"processing": true
	// 			});
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', result.message);
	// 		}
	// 	});
	// }


	function coba(gmc,id){
		$('#modalScan').modal('show');
		$("#loc").val(gmc);
		$("#id_button").val(id);
		$('#scan_qrcode_material').focus();
		showCheck2();
	}

	function showCheck2() {
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
				checkCode2(video, code.data, "2");
			}else{
				outputMessage.hidden = false;
			}
			setTimeout(function() {tick();},tickDuration);
		}
	}


	function checkCode2(video, code, num) {
		stat = false;
		var loc = "";
		if (code == $("#loc").val()) {
			if (num == "1") {
				$('#scanner').hide();
				videoOff();
				openSuccessGritter('Success', 'QR Code Successfully');
				$("#modalScan").modal('hide');
				var id = $('#id_button').val();
				var ids = id.split('_');
				$('#tdscan_'+ids[1]).html('');
				$('#tdscan_'+ids[1]).html('SESUAI MATERIAL');
				$('#qty_'+ids[1]).show();
				$('#kondisi_'+ids[1]).show();
			}else if(num == "2"){
				$('#scanner').hide();
				videoOff();
				openSuccessGritter('Success', 'QR Code Successfully');
				$("#modalScan").modal('hide');
				var id = $('#id_button').val();
				var ids = id.split('_');
				$('#tdscan_'+ids[1]).html('');
				$('#tdscan_'+ids[1]).html('SESUAI MATERIAL');
				$('#qty_'+ids[1]).show();
				$('#kondisi_'+ids[1]).show();


			}else{
				openSuccessGritter('Error', 'Error Data');
			}
		}else{
			openErrorGritter('Error', 'QR Code Not Registered');
			audio_error.play();
			$("#modalScan").modal('hide');
			videoOff();

		}
	}

	// function end_move() {
	// 	var gmc = [];
	// 	var case_pallet = [];

	// 	$('.gmc').each(function(index, value) {
	// 		gmc.push($(value).html());
	// 	});

	// 	$('.case_pallet').each(function(index, value) {
	// 		case_pallet.push($(value).html());
	// 	});

	// 	var data = {
	// 		gmc :gmc,
	// 		case_pallet:case_pallet
	// 	}
	// 	$.post('{{ url("post/finish_inter") }}',data, function(result, status, xhr){
	// 		if(result.status == true){
	// 			openSuccessGritter('Success','Tambah Data Berhasil');

	// 		}else{
	// 			openErrorGritter('Error!','Tambah Data Gagal');
	// 		}
	// 	})
	// }
	function cancelRequest(){
		$('#tableLists').DataTable().clear();
		$('#tableLists').DataTable().destroy();
		$('#tableBodyLists').html("");
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

	function submitPelayanan(id) {

		var gmc = [];
		var id = [];
		var qty = [];
		var st = [];
		var status_material = [];

		console.log($("#tableLists > tbody > tr").length);

		for(var l = 1; l <= $("#tableLists > tbody > tr").length; l++){
			if ($('#tdscan_'+l).text() != "SESUAI MATERIAL") {
				openErrorGritter('Error','Isi Semua Material');
				audio_error.play();
				return false;
			}
		}

		for(var l = 1; l <= $("#tableLists > tbody > tr").length; l++){
			// if ($('#tdscan_'+l).text() != "SESUAI MATERIAL") {
			// 	openErrorGritter('Error','Isi Semua Material');
			// 	audio_error.play();
			// 	return false;
			// }
			id.push($('#idis_'+l).val());
			qty.push($('#qty_'+l).html());
			status_material.push($('#status'+l).val());
			st.push($('#kondisi_'+l).val());
		}

		


		var data = {
			qty:qty,
			id:id,
			status_material:status_material,
			status:st
		}

		$.post('{{ url("update/pelayanan") }}', data, function(result, status, xhr){
			if(result.status){	
				openSuccessGritter('Success', result.message);	
				cancelRequest()
				window.location.href = "{{secure_url('index/joblist/operator')}}";
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);

			}
		})

	}


</script>
@endsection


