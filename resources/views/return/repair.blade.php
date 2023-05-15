@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	#loading {
		display: none;
	}
	#qr_item:hover{
		color:#ffffff
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#modalQr" class="btn btn-primary btn-lg" style="color:white;">
				&nbsp;<i class="glyphicon glyphicon-qrcode"></i>&nbsp;&nbsp;&nbsp;Scan Scanner&nbsp;
			</a>

			<a data-toggle="modal" data-target="#modalScan" class="btn btn-success btn-lg" style="color:white;">
				&nbsp;<i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan Camera&nbsp;
			</a>			
		</li>
	</ol>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-5">
			<div class="box">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Material</th>
								<th style="width: 7%;">Description</th>
								<th style="width: 1%;">Kirim</th>
								<th style="width: 1%;">Terima</th>
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
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Issue Location:</span>
							<input type="text" id="issue" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Receive Location:</span>
							<input type="text" id="receive" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Add Count:</span>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
								</div>
								<input id="quantity" style="font-size: 3vw; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
								</div>
							</div>
						</div>
						<div class="col-xs-5" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="printRepair()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								<i class="fa fa-print"></i> CETAK
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">REPAIR BELUM DI KONFIRMASI (<?php echo e(date('d-M-Y')); ?>)</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead>
									<tr>
										<th style="width: 1%;">#</th>
										<th style="width: 1%;">Material</th>
										<th style="width: 6%;">Description</th>
										<th style="width: 1%;">Issue</th>
										<th style="width: 1%;">Receive</th>
										<th style="width: 1%;">Qty</th>
										<th style="width: 1%;">Creator</th>
										<th style="width: 1%;">Created</th>
										<th style="width: 1%;">Delete</th>
										<th style="width: 1%;">Reprint</th>
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalLocation">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #dd4b39; color:  white; font-weight: bold;">Repair & After Repair<br>Pilih Lokasi Anda</h3></center>
				<div class="modal-body">
					<div class="form-group">
						<center>
							@foreach($storage_locations as $storage_location)
							<div class="col-lg-2">
								<div class="row">
									<button style="margin-top: 20px; width: 80%; font-weight: bold;" class="btn btn-danger" onclick="fetchRepairList('{{ $storage_location }}')">{{ $storage_location }}</button>
								</div>
							</div>
							@endforeach
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalScan">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan Slip Repair</h3></center>
			</div>
			<div class="modal-body">
				<div class="row">
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
					<div class="receiveRepair" style="width:100%; padding-left: 2%; padding-right: 2%;">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalQr">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan Slip Repair</h3></center>
			</div>
			<div class="modal-body" style="padding-bottom: 75px;">
				<div class="row">
					<div class="col-xs-12">
						<center>
							<div id="div_qr_item">
								<input id="qr_item" type="text" style="border:0; width: 100%; text-align: center; color: #3c3c3c; font-size: 2vw;">
							</div>
						</center>							
					</div>
					<div class="receiveRepair" style="width:100%; padding-left: 2%; padding-right: 2%;">
					</div>
				</div>
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

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 37.5%; z-index: 1000; border: 2px solid grey;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-default" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){
		$(this).find('.del').addClass('btn-default');
		$(this).find('.clear').addClass('btn-default');
		$(this).find('.cancel').addClass('btn-default');		
		$(this).find('.done').addClass('btn-success');
	};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear : true,
		});
		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	$('#modalLocation').on('hidden.bs.modal', function () {
		$(".modal-backdrop").remove();
	});

	var video;

	function stopScan() {
		$('#modalScan').modal('hide');
	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	$("#modalScan").on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#modalScan').on('hidden.bs.modal', function () {
		videoOff();
		$('.receiveRepair').html("");
	});

	$('#modalQr').on('shown.bs.modal', function () {
		$('#qr_item').show();
		$('#qr_item').val('');
		$('#qr_item').focus();
	});

	$('#modalQr').on('hidden.bs.modal', function () {
		$('.receiveRepair').html("");		
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
					receiveRepair(video, code.data);

				}else{
					outputMessage.hidden = false;
				}
			} catch (t) {
				console.log("PROBLEM: " + t);
			}

			setTimeout(function() {tick();},tickDuration);
		}

		
	}

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}

	$('#qr_item').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var qr_item = $('#qr_item').val();
			receiveRepair(video, qr_item);
		}
	});

	function receiveRepair(video, data){
		$('#scanner').hide();
		$(".modal-backdrop").remove();

		var x = {
			id:data
		}
		$.get('{{ url("fetch/repair") }}', x, function(result, status, xhr){
			if(result.status){
				var location = $('#location').val();

				if(result.repair.receive_location == location){
					var re = "";
					$('.receiveRepair').html("");
					re += '<table style="text-align: center; width:100%;"><tbody>';
					re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.repair.material_number+'</td></tr>';
					re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.repair.issue_location+' -> '+result.repair.receive_location+'</td></tr>';
					re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.repair.material_description+'</td></tr>';
					re += '<tr><td style="font-size: 50px; font-weight: bold; background-color:black; color:white;" colspan="2">'+result.repair.quantity+' PC(s)</td></tr>';
					re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.user.name+'</td></tr>';
					re += '<tr>';
					re += '<td><button id="reject+'+result.repair.id+'" class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TOLAK</button></td>';
					re += '<td><button id="receive+'+result.repair.id+'" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TERIMA</button></td>';
					re += '</tr>';
					re += '</tbody></table>';

					$('.receiveRepair').append(re);
					$('#qr_item').val('');
					$('#qr_item').hide();
				}else{
					$('.receiveRepair').html("");
					$('#qr_item').val('');
					$('#qr_item').focus();

					showCheck();
					$('#loading').hide();
					openErrorGritter('Error!', 'Lokasi Repair Salah');
				}

			}
			else{
				$('.receiveRepair').html("");
				$('#qr_item').val('');
				$('#qr_item').focus();

				showCheck();
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function confirmReceive(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('{{ url("confirm/repair") }}', data, function(result, status, xhr){
			if(result.status){
				$('.receiveRepair').html("");
				showCheck();

				$('#qr_item').show();
				$('#qr_item').focus();

				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
			}else{
				$('#qr_item').show();
				$('#qr_item').focus();

				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function printRepair(){
		$('#loading').show();
		var material = $('#material_number').val();
		var issue = $('#issue').val();
		var receive = $('#receive').val();
		var description = $('#material_description').val();
		var quantity = $('#quantity').val();

		if(material == ''){
			$('#loading').hide();
			openErrorGritter('Error!', 'Pilih material yang akan di repair');
			return false;
		}
		if(quantity == '' || quantity < 1){
			$('#loading').hide();
			openErrorGritter('Error!', 'Isikan quantity yang akan di repair');
			return false;
		}

		var data = {
			material:material,
			issue:issue,
			receive:receive,
			quantity:quantity,
			description:description
		}
		$.post('{{ url("print/repair") }}', data, function(result, status, xhr){
			if(result.status){
				fetchResume(issue);
				$('#material_number').val("");
				$('#issue').val("");
				$('#receive').val("");
				$('#material_description').val("");
				$('#quantity').val(0);

				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchRepair(id){

		var material = $('#'+id).find('td').eq(1).text();
		var description = $('#'+id).find('td').eq(2).text();
		var issue = $('#'+id).find('td').eq(3).text();
		var receive = $('#'+id).find('td').eq(4).text();

		$('#material_number').val(material);
		$('#material_description').val(description);
		$('#issue').val(issue);
		$('#receive').val(receive);
	}

	function reprint(id){
		var data = {
			id:id
		}
		$.get('{{ url("reprint/repair") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchResume(loc){
		var data = {
			loc:loc
		}
		$.get('{{ url("fetch/repair/resume") }}', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.issue_location +'</td>';
				tableData += '<td>'+ value.receive_location +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '<td>'+ value.name +'</td>';
				tableData += '<td>'+ value.created_at +'</td>';
				tableData += '<td><center><button class="btn btn-danger" onclick="deleteRepair('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
				tableData += '<td><center><button class="btn btn-primary" onclick="reprint('+value.id+')"><i class="fa fa-print"></i></button></center></td>';
				tableData += '</tr>';
				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}

	function deleteRepair(id){

		if(confirm("Apa Anda yakin anda akan mendelete slip repair?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/repair") }}', data, function(result, status, xhr){
				if(result.status){
					fetchResume(result.issue);
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
		else{
			return false;
		}
	}

	function fetchRepairList(loc){
		fetchResume(loc);
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('{{ url("fetch/repair/list") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.lists, function(key, value) {
					var str = value.description;
					var desc = str.replace("'", "");

					var css = '';
					if(value.receive_location == value.issue_location){
						css = 'style="background-color: #ccffff;"';
					}
					tableData += '<tr id="'+value.material_number+'_'+value.receive_location+'_'+value.issue_location+'" onclick="fetchRepair(id)">';
					tableData += '<td '+css+'>'+ count +'</td>';
					tableData += '<td '+css+'>'+ value.material_number +'</td>';
					tableData += '<td '+css+'>'+ desc +'</td>';
					tableData += '<td '+css+'>'+ value.issue_location +'</td>';
					tableData += '<td '+css+'>'+ value.receive_location +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyList').append(tableData);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var tableList = $('#tableList').DataTable({
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
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
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

				$('#tableList tfoot tr').appendTo('#tableList thead');

				openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

</script>
@stop