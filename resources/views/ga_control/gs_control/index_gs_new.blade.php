@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

<style type="text/css">

	
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}

	table.table-bordered > thead > tr > th{
		border:1px solid rgb(54, 59, 56) !important;
		text-align: center;
		background-color: #f0f0ff;  
		color:black;
	}

	table.table-bordered > tbody > tr > td{
		border-collapse: collapse !important;
		border:1px solid rgb(54, 59, 56)!important;
		background-color: #f0f0ff;
		color: black;
		vertical-align: middle;
		text-align: center;
		padding:3px;
	}

	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	#loading, #error { display: none; }


	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}


	.label__checkbox {
		display: none;
	}
	.label__check {
		display: inline-block;
		border-radius: 50%;
		border: 5px solid rgba(0,0,0,0.1);
		background: white;
		text-align: center;
		vertical-align: middle;
		margin-left: 35px;
		width: 2em;
		height: 2em;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		transition: border .3s ease;

		i.icon {
			opacity: 0.2;
			font-size: ~'calc(1rem + 1vw)';
			color: transparent;
			transition: opacity .3s .1s ease;
			-webkit-text-stroke: 3px rgba(0,0,0,.5);
		}

		&:hover {
			border: 5px solid rgba(0,0,0,0.2);
		}
	}

	.label__checkbox:checked + .label__text .label__check {
		animation: check .5s cubic-bezier(0.895, 0.030, 0.685, 0.220) forwards;

		.icon {
			opacity: 1;
			transform: scale(0);
			color: white;
			-webkit-text-stroke: 0;
			animation: icon .3s cubic-bezier(1.000, 0.008, 0.565, 1.650) .1s 1 forwards;
		}
	}

	@keyframes icon {
		from {
			opacity: 0;
			transform: scale(0.3);
		}
		to {
			opacity: 1;
			transform: scale(1)
		}
	}

	@keyframes check {
		0% {
			width: 1.5em;
			height: 1.5em;
			border-width: 5px;
		}
		10% {
			width: 1.5em;
			height: 1.5em;
			opacity: 0.1;
			background: rgba(0,0,0,0.2);
			border-width: 15px;
		}
		12% {
			width: 1.5em;
			height: 1.5em;
			opacity: 0.4;
			background: rgba(0,0,0,0.1);
			border-width: 0;
		}
		50% {
			width: 2em;
			height: 2em;
			background: #00d478;
			border: 0;
			opacity: 0.6;
		}
		100% {
			width: 2em;
			height: 2em;
			background: #00d478;
			border: 0;
			opacity: 1;
		}
	}

	.grid-container {
		/*display: grid;*/
		/*grid-template-columns: auto auto auto;*/
		border-style: dashed;
	}

	.patient-duration{
		margin: 0px;
		padding: 0px;
	}

	.green-color {
		color:green;
	}
</style>
@stop
@section('header')

<section class="content-header">
	
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding: 0 10 0 10px;">

			<center>
				<div style="padding-bottom:10px;">
					<table style="width: 100%; text-align: center; font-weight: bold; font-size: 1.5vw; margin-top: 5px" >
						<tbody>
							<tr>
								<td style="width: 2%; background-color: orange; border-style: solid; border-width: low;" id="op_id">-</td>
								<td style="width: 7%; background-color: orange; border-style: solid; border-width: low;" id="op_name">-</td>
								<td style="width: 1%;cursor:pointer " onclick="openHistory()"> <i style="font-size: 35px;" class='fa fa-plus-circle green-color'></i></td>
							</tr>
						</tbody>
					</table>
				</div>
			</center>
		</div>
		<div class="col-xs-6" style="padding: 0 0 0 10px;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #7e5686;background-color: white">
					<i class="fa fa-arrow-down"></i> DAILY JOB GS <i class="fa fa-arrow-down"></i>
				</div>
				<input type="hidden" id="nik_op" name="nik_op">
				<input type="hidden" id="name_op" name="name_op">

				<div class="col-xs-12" style="padding: 0;"> 
					<button class="btn btn-default btn-sm pull-right" style="margin-top:2px;" onclick="fetchTableGS($('#nik_op').val())">
						&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
					</button>
					<table id="tableResume" class="table table-hover table-bordered" style="width: 100%">
						<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
							<tr>
								<th style="width: 1%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">No</th>
								<!-- <th style="width: 6%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Kategori</th> -->
								<th style="width: 2%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Lokasi</th>
								<th style="width: 10%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Pekerjaan</th>
								<th style="width: 5%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Action</th>
							</tr>
						</thead>
						<tbody id="bodyResume">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
				
			</center>
			<div class="col-xs-12" id="btn_app" style="display:none;"> 
				<div class="col-xs-6" style="padding-left: 0;">
					<button class="btn btn-danger" id="print" onclick="clearAll()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;">CANCEL</button>
				</div>
				<div class="col-xs-6" style="padding-right: 0; padding-left: 0;">
					<button class="btn btn-success" id="print" onclick="createJob()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-plus"></i> SUBMIT</button>
				</div>
			</div>
		</div>
		
		<div class="col-xs-6" style="padding: 0 10px 0 10px;">
			<center>
				<div class="col-xs-12" style="font-weight: bold; font-size: 2.5vw; text-align: center; color: #008d4c;background-color: white;padding:0">
					<i class="fa fa-arrow-down"></i> Finished Job GS <i class="fa fa-arrow-down"></i>
				</div>
				<div class="col-xs-12" style="padding: 0;overflow-x: scroll;">
					<table id="tableFinishs" class="table table-hover table-bordered" style="width: 100%">
						<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
							<tr>
								<th style="width: 1%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">No</th>
								<th style="width: 1%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Lokasi</th>
								<th style="width: 5%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Pekerjaan</th>
								<th style="width: 3%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Request At</th>
								<th style="width: 3%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Finished At</th>
								<th style="width: 1%; padding: 5;vertical-align: middle;font-size: 16px;background-color: #7e5686;color: white">Status</th>
							</tr>
						</thead>
						<tbody id="bodyFinishs">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</center>
		</div>
	</div>
</section>

<div class="modal fade" id="modal-check">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
					UPLOAD FOTO
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<div class="col-xs-4">
							<label>Lokasi</label><br>

							<input style="font-weight: bold; text-align: center; font-size: 1.5vw; width: 100%; height: 30px; background-color: #F2F2F2;" type="text" id="area_jobs" readonly>
						</div>

						<div class="col-xs-8">
							<label>Pekerjaan</label><br>

							<input style="font-weight: bold; text-align: center; font-size: 1.5vw; width: 100%; height: 30px; background-color: #F2F2F2;" type="text" id="list_jobs" readonly>
						</div>
					</div>

					<div class="col-xs-12" style="padding-top:10px" id="img_view">
					</div>
				</div>					
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-upload-img">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="modalDetailTitle">
					
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12" style="padding-top:10px" id="img_view2">
					</div>
				</div>					
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
				<div class="form-group" id="selectOp">
					<select class="form-control selectOp" name="inputor" onchange="inputorInput()" id='inputor'
					data-placeholder="Pilih Operator" style="width: 100%;">
					<option value="">Select Inputor</option>
					@foreach ($pics as $employee)
					<option value="{{ $employee->employee_id }}-{{ $employee->employee_name }}">
						{{ $employee->employee_id }} - {{ $employee->employee_name }}</option>
						@endforeach
					</select>
				</div>
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

<div class="modal modal-default fade" id="modalStatus">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center" id="statusReason"><b>PAUSE</b></h4>
			</div>
			<div class="modal-body">
				<div class="form-group" id="selectJob">
					<select class="form-control selectJob" name="reasonPause" id='reasonPause' data-placeholder="Pilih Reason" style="width: 100%;" onchange="updateDetail(this.value)">
						<option value="">Pilih Reason</option>
						<option value="Istirahat">Istirahat</option>
						<option value="Pekerjaan Lain Urgent">Pekerjaan Lain Urgent</option>
					</select>
				</div>
				<div class="form-group" id="updateDetail" style="display:none">
					<input class="form-control" style="width: 100%; text-align: center;" type="text" id="reason_detail" placeholder="Reason">
				</div>
				<div class="modal-footer" id="pauses_btn">
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="modalFinish">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
					FOTO PEKERJAAN
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<label>Foto Before</label>
								: <div name="img_foto_before" id="img_foto_before"></div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label >Foto After</label>
								: <div name="img_foto_after" id="img_foto_after"></div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalImage">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="form-group">
					<div  name="image_show" id="image_show"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalHistory">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #1da12e; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Tambah Pekerjaan</h3>
				</center>
				<div class="modal-body">
					<div class="col-md-6">
						<div class="form-group" id="selectJob1">
							<label>Pilih Pekerjaan</label>
							<select class="form-control selectJob1" id="createGmc1" name="createGmc1" data-placeholder="Select GMC" style="width: 100%;" onchange="UpJob(this.value)">
								<option value=""></option>
								
							</select>
						</div>
					</div>
					
					<div class="col-md-3">
						<div class="form-group" id="selectJob3">
							<label>Category</label>
							<select class="form-control selectJob3" id="createGmc3" name="createGmc3" data-placeholder="Select GMC" style="width: 100%;">
								<option value=""></option>
							</select>
						</div>
					</div>
					
					<div class="col-md-3">
						<div class="form-group" id="selectJob2">
							<label>Lokasi</label>
							<select class="form-control selectJob2" id="createGmc2" name="createGmc2" data-placeholder="Select GMC" style="width: 100%;">
								<option value=""></option>
							</select>
						</div>
					</div>
					
					<div class="col-md-12">
						<div class="form-group">
							<div class="col-md-12">
								<label style="color: white;"> xxxxxxxxxxxxxxxxxx</label>
								<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="createLog1()" class="btn btn-success">CONFIRM</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal modal-default fade" id="modalProgress">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h2 style="background-color: #BA55D3; text-align: center; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
					FOTO PEKERJAAN
				</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label>Foto Before</label>
							: <div name="img_foto_before_progress" id="img_foto_before_progress"></div>
						</div>
					</div>
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
		startScan();
		// setTime();
		setInterval(setTime, 1000);

		$('.selectOp').select2({
			minimumInputLength: 3,
			dropdownParent: $('#selectOp'),
			allowClear:true,
			tags: true
		});


		$('.selectJob').select2({
			dropdownParent: $('#selectJob'),
			allowClear:true
		});

		$('.selectJob1').select2({
			dropdownParent: $('#selectJob1'),
			allowClear:true,
			tags: true
		});

		$('.selectJob2').select2({
			dropdownParent: $('#selectJob1'),
			allowClear:true,
			tags: true
		});
		$('.selectJob3').select2({
			dropdownParent: $('#selectJob1'),
			allowClear:true
		});


	});
	var cek_cate = <?php echo json_encode($cek_cate); ?>;
	var cek_listjobs = [];

	var data_job_check = [];
	
	var in_time = [];
	function setTime() {
		for (var i = 0; i < in_time.length; i++) {
			var duration = diff_seconds(new Date(), in_time[i]);
			document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
			var allowence = 60 * 15;
			if(duration >= allowence){
				$('#td_time_' + i).addClass('over');
			}

		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}


	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}


	function updateDetail(st){

		if (st == "Istirahat") {
			$('#reason_detail').val("");
			$('#reason_detail').val("Istirahat");
		}else{
			$('#reason_detail').val("");
		}
		$('#updateDetail').show();
	}

	function startScan() {
		$('#scanModal').modal('show');
	}

	function stopScan() {
		$('#scanModal').modal('hide');
	}

	function videoOff() {
		video.pause();
		video.src = "";
		video.srcObject.getTracks()[0].stop();
	}

	$("#scanModal").on('shown.bs.modal', function() {
		showCheck('123');
	});

	$('#scanModal').on('hidden.bs.modal', function() {
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

		navigator.mediaDevices.getUserMedia({
			video: {
				facingMode: "environment"
			}
		}).then(function(stream) {
			video.srcObject = stream;
			video.play();
			setTimeout(function() {
				tick();
			}, tickDuration);
		});

		function tick() {
			loadingMessage.innerText = "⌛ Loading video..."
			try {
				loadingMessage.hidden = true;
				video.style.position = "static";

				var canvasElement = document.createElement("canvas");
				var canvas = canvasElement.getContext("2d");
				canvasElement.height = video.videoHeight;
				canvasElement.width = video.videoWidth;
				canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
				var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
				var code = jsQR(imageData.data, imageData.width, imageData.height, {
					inversionAttempts: "dontInvert"
				});
				if (code) {
					outputMessage.hidden = true;
					videoOff();
					// document.getElementById('qr_code').value = code.data;
					checkCode(code.data);

				} else {
					outputMessage.hidden = false;
				}
			} catch (t) {
				
			}

			setTimeout(function() {
				tick();
			}, tickDuration);
		}

	}




	function inputorInput() {
		$('#scanModal').modal('hide');
		var auditor = $('#inputor').val().split("-");
		$('#op_name').text('');
		$('#op_id').text();
		$('#op_id').text(auditor[0]);
		$('#op_name').text(auditor[1]);
		$('#nik_op').val(auditor[0]);
		$('#name_op').val(auditor[1]);


		fetchTableGS(auditor[0]);
		fetchTableGSProgress(auditor[0]);

	}


	function UpJob(val){

		var job = val.split('_');

		if (job[1] != null) {

			$('#createGmc3').html('');
			var tableLogBody1 = "";
			tableLogBody1 += '<option value="'+job[1]+'">'+job[1]+'</option>';			
			$('#createGmc3').append(tableLogBody1);

			$('#createGmc2').html('');
			var tableLogBody = "";
			tableLogBody += '<option value="'+job[2]+'">'+job[2]+'</option>';			
			$('#createGmc2').append(tableLogBody);			

		}else{
			$('#createGmc3').html('');
			var tableLogBody = "";
			tableLogBody += '<option value="Area GS 1">Area GS 1</option>';
			tableLogBody += '<option value="Area GS 2">Area GS 2</option>';
			tableLogBody += '<option value="Area GS 3">Area GS 3</option>';
			$('#createGmc3').append(tableLogBody);

			var tableLogBody1 = "";
			$('#createGmc2').html('');

			$.each(cek_cate, function(key, value){
				if (value.category.match(job[2])) {
					tableLogBody1 += '<option>'+value.area+'</option>';
				}
			});
			$('#createGmc2').append(tableLogBody1);
		}

	}

	function checkCode(code) {
		var data = {
			id: code
		}

		$.get('{{ url("fetch/check/op") }}', data, function(result, status, xhr) {

			if (result.status) {

				if (result.cek_op.length == 0) {
					openErrorGritter('Error', 'NIK Tidak Terdaftar');
					$('#scanModal').modal('hide');
					return false;
				}else{
					openSuccessGritter('Success', 'NIK Terdaftar');
					$('#scanModal').modal('hide');
					$('#op_name').text('');
					$('#op_id').text();
					$('#op_id').text(result.cek_op.employee_id);
					$('#op_name').text(result.cek_op.employee_name);
					$('#nik_op').val(result.cek_op.employee_id);
					$('#name_op').val(result.cek_op.employee_name);

					fetchTableGS(result.cek_op.employee_id);

					fetchTableGSProgress(result.cek_op.employee_id);
				}

			} else {
				openErrorGritter('Error', 'NIK Tidak Terdaftar');
				$('#scanModal').modal('hide');
			}

		});

	}



	function fetchTableGS(emp_id){

		$('#loading').show();

		var data = {
			emp_id: emp_id
		}

		$.get('{{ url("fetch/joblist/index") }}', data, function(result, status, xhr) {
			if (result.status) {
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$("#bodyResume").empty();
				var body = '';
				var operator = 0;
				in_time = [];
				var st = '';

				cek_listjobs.push(result.cek_listjob);

				if (result.cek_dailyjob.length == 0) {
					$('#loading').hide();
					openErrorGritter('Error', 'Daily Pekerjaan Tidak Ditemukan');

				}else{
					$('#loading').hide();

					data_job_check.push(result.cek_dailyjob); 

					$.each(result.cek_dailyjob, function(index, value){
						body += "<tr>";
						body += "<td>"+(index+1)+"</td>";
						if (value.area == null) {
							st = '-';
						}else{
							st = value.area;
						}
						body += "<td>"+st+"</td>";

						if (value.status == 0 && value.img_before != null) {
							body += "<td onclick= 'modalProgressImg(\""+value.img_before+"\")' style='cursor:pointer'>"+value.list_job+"</td>";
						}else{
							body += "<td>"+value.list_job+"</td>";
						}	

						body += "<td>";
						if (value.status == 0) {
							var tanggal_fix = value.request_at.replace(/-/g,'/');
							in_time.push(new Date(tanggal_fix));
							body += "<label id='hours"+ operator +"'>"+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +"</label>:";
							body += "<label id='minutes"+ operator +"'>"+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +"</label>:";
							body += "<label id='seconds"+ operator +"'>"+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +"</label> <br>";
						}

						if (value.status == 0 && value.img_before != null) {
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-warning' id='btn_pause_"+value.id+"' onclick='pause("+value.id+",\"PAUSE\","+ operator +",this.id)'><i class='fa fa-pause'></i> Pause</a>";
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-danger' id='btn_stop_"+value.id+"' onclick='modaluploadImg("+value.id+",\"after\","+ operator +",this.id)'><i class='fa fa-stop'></i> Stop</a>";
						}else if (value.status == 0 && value.img_before == null) {
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-warning' id='btn_pause_"+value.id+"' onclick='pause("+value.id+",\"PAUSE\","+ operator +",this.id)'><i class='fa fa-pause'></i> Pause</a>";
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-danger' id='btn_stop_"+value.id+"' onclick='modaluploadImg("+value.id+",\"after_before\","+ operator +",this.id)'><i class='fa fa-stop'></i> Stop</a>";
						}
						else if (value.status == 4) {
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-primary' id='btn_lanjut_"+value.id+"' onclick='modalLanjut("+value.id+",\"lanjut\","+ operator +")'><i class='fa fa-forward'></i> Lanjut</a>";
						}else if (value.status == 0  && value.img_before == null && value.category == "Lain-Lain") {
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-info' id='btn_start_"+value.id+"' onclick='modaluploadImg("+value.id+",\"mix\","+ operator +",this.id)'><i class='fa fa-image'></i> Upload Photo</a>";
						}
						else{
							body += "<a style='margin-right:2px' type='button' class='btn btn-xs btn-success' id='btn_start_"+value.id+"' onclick='modaluploadImg("+value.id+",\"before\","+ operator +",this.id)'><i class='fa fa-play'></i> Start</a>";
						}

						body += "</td>";
						body += "</tr>";
						$('#btn_start_'+operator).hide();
						operator += 1;
					})
				}


				$("#bodyResume").append(body);
				$('#tableResume').DataTable({
					"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					'paging'      	: true,
					'lengthChange'	: false,
					'searching'   	: true,
					'ordering'		: true,
					'info'       	: true,
					'autoWidth'		: false,
					'pageLength' : 15,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"infoCallback": function( settings, start, end, max, total, pre ) {
						return "<b>Total "+ total +" Job </b>";
					}
				});
			}
		});
}

function openHistory(){

	$('#createGmc1').html('');
	var tableLogBody1 = "";

	$.each(cek_listjobs[0], function(indexs, value){
		if (value.op_nik == $('#nik_op').val()) {
			tableLogBody1 += '<option value="'+value.list_job+'_'+value.category+'_'+value.area+'">'+value.list_job+'</option>';			
		}
	})
	$('#createGmc1').append(tableLogBody1);
	$('#modalHistory').modal('show');
}

function showBtn(){
	$('#btn_app').show();
}


function pause(id,status,no,no_btn) {

	$('#typePause').val(status);
	$('#statusReason').html(status);	
	$('#reasonPause').val("").trigger("change");
	$("#updateDetail").hide();	

	$("#pauses_btn").empty();
	var bodys = '';
	bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='saveStatus(\""+id+"\",\"PAUSE\")'>CONFIRM</button></div>";

	$("#pauses_btn").append(bodys);
	$('#modalStatus').modal('show');
}


function saveStatus(id,st) {
		// $('#loading').show();

		var reason = $('#reasonPause').val();
		var reasondetail = $('#reason_detail').val();

		if (reason == '' || reasondetail == '') {
			$('#loading').hide();
			alert('Pilih Reason');
		}else{
			var data = {
				op_nik:$('#nik_op').val(),
				op_name:$('#op_name').html(),
				reasondetail:reasondetail,
				id:id,
				reason:reason
			}

			$.get('{{ url("input/reason_pause/gs") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					$('#modalStatus').modal('hide');
					openSuccessGritter('Success',result.message);
					fetchTableGS($('#nik_op').val());
					fetchTableGSProgress($('#nik_op').val());
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}


	function createLog1() {

		if (confirm('Apakah Anda ingin menambahkan pekerjaan?')) {
			
			$('#loading').show();
			var joblist1 = $('#createGmc1').val().split("_");
			var lokasi = $('#createGmc2').val();
			var category = $('#createGmc3').val();
			var op_nik=$('#op_id').html();
			var op_name=$('#op_name').html();

			if (createGmc1 == '') {
				$('#loading').hide();
				alert('Pilih Tambahan Pekerjaan');
			}else{
				var data = {
					joblist:joblist1[0],
					lokasi:lokasi,
					category:category,
					op_nik:op_nik,
					op_name:op_name
				}

				$.post('{{ url("create/job/new") }}', data, function(result, status, xhr){
					if(result.status){
						$('#loading').hide();
						$('#modalHistory').modal('hide');
						openSuccessGritter('Success',result.message);
						fetchTableGS(op_nik);
						fetchTableGSProgress(op_nik);
					}else{
						$('#loading').hide();
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}
	}

	function modalLanjut(id,st,no) {

		if (confirm('Pekerjaan dalam proses pause. Apakah Anda ingin melanjutkan?')) {
			
			var data = {
				op_nik:$('#op_id').val(),
				op_name:$('#op_name').val(),
				id:id
			}

			$.get('{{ url("update/pause/gs") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					openSuccessGritter('Success',result.message);
					fetchTableGS($('#nik_op').val());
					fetchTableGSProgress($('#nik_op').val());
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
				}
			});
		}
	}

	function fetchTableGSProgress(emp_ids){
		var data = {
			emp_id: emp_ids
		}
		$.get('{{ url("fetch/joblist/index") }}',data, function(result, status, xhr) {
			$('#tableFinishs').DataTable().clear();
			$('#tableFinishs').DataTable().destroy();
			$("#bodyFinishs").empty();
			var body_finish = "";
			var st = '';

			$.each(result.job_finished, function(indexs, value){
				body_finish += "<tr onclick= 'modalFinishImg(\""+value.img_before+"\",\""+value.img_after+"\")' style='cursor:pointer'>";
				body_finish += "<td>"+(indexs+1)+"</td>";
				
				if (value.lokasi == null) {
					st = '-';
				}else{
					st = value.lokasi;
				}

				body_finish += "<td>"+st+"</td>";
				body_finish += "<td>"+value.list_job+"</td>";
				body_finish += "<td>"+value.request_at+"</td>";
				body_finish += "<td>"+value.finished_at+"</td>";
				body_finish += "<td><span class='label label-success' style='color: white'>Selesai</span></td>";
				body_finish += "</tr>";
			})
			$("#bodyFinishs").append(body_finish);

			$('#tableFinishs').DataTable({
				"sDom": '<"top"i>rt<"bottom"flp><"clear">',
				'paging'      	: true,
				'lengthChange'	: false,
				'searching'   	: true,
				'ordering'		: true,
				'info'       	: true,
				'autoWidth'		: false,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"bDestroy": true,
				'pageLength' : 15,
				"infoCallback": function( settings, start, end, max, totals, pre ) {
					return "<b>Total "+ totals +" Job </b>";
				}
			});

		});
	}


	function modalFinishImg(before,after){
		var images_gs = "";
		var images_gs_after = "";



		$("#img_foto_after").html("");
		$("#img_foto_before").html("");
		$('#img_foto_before').show();
		$('#img_foto_after').show();

		if (before.length == 4) {
			$('#img_foto_before').hide();
		}else{
			images_gs += '<img src="{{ url("images/ga/gs_control") }}/'+before+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+before+'\')">';
			$("#img_foto_before").append(images_gs);
		}

		if (after.length == 4) {
			$('#img_foto_after').hide();
		}else{
			images_gs_after += '<img src="{{ url("images/ga/gs_control") }}/'+after+'" width="250px" height="180px" style="cursor: zoom-out" onclick="showImage(\''+after+'\')">';
			$("#img_foto_after").append(images_gs_after);
		}
		$('#modalFinish').modal('show');

	}

	function showImage(imgs) {
		$('#modalImage').modal('show');
		var images_show = "";
		$("#image_show").html("");
		images_show += '<img style="cursor:zoom-in" src="{{ url("images/ga/gs_control") }}/'+imgs+'" width="100%" >';
		$("#image_show").append(images_show);
	}

	function modalProgressImg(before){

		var images_gs = "";
		$("#img_foto_before_progress").html("");

		images_gs += '<img src="{{ url("images/ga/gs_control") }}/'+before+'" width="100%" height="400px" style="cursor: zoom-out">';
		
		$("#img_foto_before_progress").append(images_gs);
		$('#img_foto_before_progress').show();

		$('#modalProgress').modal('show');
	}

	function modaluploadImg(ids,status,no_op,st_btn,area){

		var resultText = "";

		if (status == "before") {
			$("#img_view2").empty();
			var bodys = "";
			bodys += "<div class='col-md-12'><div style='padding:10px'><label>Upload Photo Before</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"before\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_before"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img width='100%' id='blah"+ids+"' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";
			bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='UploadPhoto(\""+ids+"\",\"before\",\""+st_btn+"\")'>SIMPAN</button></div>";
			$("#img_view2").append(bodys);
			resultText = "UPLOAD FOTO BEFORE";

		}else if (status == "after")  {
			
			$("#img_view2").empty();
			var bodys = "";

			bodys += "<div class='col-md-6'> <div style='padding:10px'><label>Upload Photo After</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"after\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_after"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage_after"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img  id='blah_after"+ids+"' width='223%' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";
			bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='UploadPhoto(\""+ids+"\",\"after\")'>SIMPAN</button></div>";

			$("#img_view2").append(bodys);
			resultText = "UPLOAD FOTO AFTER";

		}else if (status == "after_before")  {
			
			$("#img_view2").empty();
			var bodys = "";
			bodys += "<div class='col-md-6'><div style='padding:10px'><label>Upload Photo Before</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"before\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_before"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img width='100%' id='blah"+ids+"' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";

			bodys += "<div class='col-md-6'> <div style='padding:10px'><label>Upload Photo After</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"after\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_after"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage_after"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img  id='blah_after"+ids+"' width='250px' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";

			bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='UploadPhoto(\""+ids+"\",\"after_before\")'>SIMPAN</button></div>";

			$("#img_view2").append(bodys);
			resultText = "UPLOAD FOTO AFTER";

		}
		else if (status == "mix") {
			
			$("#img_view2").empty();
			var bodys = "";
			bodys += "<div class='col-md-12'><div style='padding:10px'><label>Upload Photo Before</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"before\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_mix"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img width='100%' id='blah"+ids+"' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";
			bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='UploadPhoto(\""+ids+"\",\"mix\",\""+st_btn+"\")'>SIMPAN</button></div>";
			$("#img_view2").append(bodys);
			resultText = "UPLOAD FOTO BEFORE";

		}

		$('#modalDetailTitle').html(resultText);
		$('#modal-upload-img').modal('show');
	}

	function modalupload(ids,st,areas){
		$('#modal-check').modal('show');
		$('#list_jobs').val(st);
		$('#area_jobs').val(areas);
		$("#img_view").empty();
		var bodys = "";

		bodys += "<div class='col-md-6'><div style='padding:10px'><label>Upload Photo Before</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"before\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_before"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img width='250px' id='blah"+ids+"' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";

		bodys += "<div class='col-md-6'> <div style='padding:10px'><label>Upload Photo After</label> <i style='cursor: pointer;' onclick='btnshow(\""+ids+"\",\"after\")' class='fa fa-refresh'></i><br><input type='file' onchange='readURL(this,\"\");' id='update_after"+ids+"' style='display:none' accept='image/png, image/jpg, image/jpeg'></td><button class='btn btn-primary btn-lg' id='btnImage_after"+ids+"' value='Photo' onclick='buttonImage(this)'>Photo</button><br><img width='250px' id='blah_after"+ids+"' src='' style='display: none; padding-top:3px;' alt='your image'/></div></div>";

		bodys += "<div class='col-md-12' style='padding-top: 10px;'><button class='btn btn-danger pull-left' data-dismiss='modal' aria-label='Close' style='font-weight: bold; font-size: 1.3vw; width: 30%;'>BATAL</button><button class='btn btn-success pull-right' style='font-weight: bold; font-size: 1.3vw; width: 68%;' onclick='UploadPhoto(\""+ids+"\")'>SIMPAN</button></div>";

		$("#img_view").append(bodys);
	}

	function buttonImage(elem) {
		$(elem).closest("div").find("input").click();
	}

	function btnshow(id,st){
		if (st == "before") {
			$("#blah"+id).css("display",'none');
			$("#blah"+id).removeAttr('img');
			$("#btnImage"+id).show();
		}else{
			$("#blah_after"+id).css("display",'none');
			$("#blah_after"+id).removeAttr('img');
			$("#btnImage_after"+id).show();
		}
	}

	function readURL(input,idfile) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				var img = $(input).closest("div").find("img");
				$(img).show();
				$(img)
				.attr('src', e.target.result);
			};

			reader.readAsDataURL(input.files[0]);

		}
		$(input).closest("div").find("button").hide();
	}

	function createJob(){
		if (confirm('Apakah Anda yakin akan mengerjakan pekerjaan tersebut?')) {

			var tag = [];
			$("input[type=checkbox]:checked").each(function() {
				var id_print = this.id.split("_");
				tag.push(id_print[1]);
			});

			var data = {
				id : tag
			}

			$.post('{{ url("create/job/gs") }}',data, function(result, status, xhr){
				if(result.status){    
					openSuccessGritter('Success', result.message);
					fetchTableGS($('#nik_op').val());
					fetchTableGSProgress($('#nik_op').val());
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
	}

	function UploadPhoto(no,st,no_btn) {

		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			if (st == "before") {
				var attachment_foto_before = $('#update_before'+no).prop('files')[0];
				var attachment_foto_before1 = $('#update_before'+no).val();
				// if(attachment_foto_before1 == ""){
				// 	$('#loading').hide();
				// 	openErrorGritter('Error!', 'Semua foto pekerjaan harus di upload.');
				// 	audio_error.play();
				// 	return false;
				// }

				var formData = new FormData();
				formData.append('names', $('#name_op').val());
				formData.append('nik_op', $('#nik_op').val());
				formData.append('id', no);
				formData.append('attachment_foto_before', attachment_foto_before);
				formData.append('status1', st);
			}

			if (st == "after") {
				var attachment_foto_after = $('#update_after'+no).prop('files')[0];
				var attachment_foto_after1 = $('#update_after'+no).val();

				if(attachment_foto_after1 == ""){
					$('#loading').hide();
					openErrorGritter('Error!', 'Semua foto pekerjaan harus di upload.');
					audio_error.play();
					return false;
				}

				var formData = new FormData();
				formData.append('names', $('#name_op').val());
				formData.append('nik_op', $('#nik_op').val());
				formData.append('id', no);
				formData.append('attachment_foto_after',attachment_foto_after);	
				formData.append('status1', st);
			}


			if (st == "mix") {
				var attachment_foto_mix = $('#update_mix'+no).prop('files')[0];
				var attachment_foto_mix1 = $('#update_mix'+no).val();
				if(attachment_foto_mix1 == ""){
					$('#loading').hide();
					openErrorGritter('Error!', 'Semua foto pekerjaan harus di upload.');
					audio_error.play();
					return false;
				}

				var formData = new FormData();
				formData.append('names', $('#name_op').val());
				formData.append('nik_op', $('#nik_op').val());
				formData.append('id', no);
				formData.append('attachment_foto_mix', attachment_foto_mix);
				formData.append('status1', st);
			}


			if (st == "after_before") {

				var attachment_foto_before = $('#update_before'+no).prop('files')[0];
				var attachment_foto_before1 = $('#update_before'+no).val();
				var attachment_foto_after = $('#update_after'+no).prop('files')[0];
				var attachment_foto_after1 = $('#update_after'+no).val();

				if(attachment_foto_before1 == "" || attachment_foto_after == ""){
					$('#loading').hide();
					openErrorGritter('Error!', 'Semua foto pekerjaan harus di upload.');
					audio_error.play();
					return false;
				}

				if(attachment_foto_before1 == "" && attachment_foto_after1 == ""){
					$('#loading').hide();
					openErrorGritter('Error!', 'Semua foto pekerjaan harus di upload.');
					audio_error.play();
					return false;
				}

				var formData = new FormData();
				formData.append('names', $('#name_op').val());
				formData.append('nik_op', $('#nik_op').val());
				formData.append('id', no);
				formData.append('attachment_foto_before', attachment_foto_before);
				formData.append('attachment_foto_after',attachment_foto_after);	
				formData.append('status1', st);

			}

			$.ajax({
				url:"{{ url('update/gs/job') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success: function (response) {
					$("#loading").hide();
						// location.reload();
						$("#img_view").empty();						
						// $('#modal-check').modal('hide');

						if (response.status == false) {
							openErrorGritter("Error", response.message);
						}else{
							openSuccessGritter("Success", response.message);
							$("#"+no_btn).hide();
						}
						fetchTableGSProgress($('#nik_op').val());
						$('#modal-upload-img').modal('hide');
						fetchTableGS($('#nik_op').val());

					},
					error: function (response) {
						console.log(response.message);
					},
				})
		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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

