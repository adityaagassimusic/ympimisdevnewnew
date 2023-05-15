@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:  2px 5px 2px 5px;
	}

	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	#loading, #error { display: none; }
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
	<div class="row">
		<div class="col-xs-4" style="padding: 0 0 0 10px;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #3d9970;background-color: white">
					<i class="fa fa-arrow-down"></i> TOTAL PRINT <i class="fa fa-arrow-down"></i>
				</div>
				<div class="col-xs-12" style="padding-left: 0; padding-top: 5px;">
					<table id="table-model" class="table table-bordered table-hover">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th colspan="2" style="background-color: #56f580;text-align: center;color: black;font-size: 20px;padding: 0px;">PACKING</th>
							</tr>
							<tr>
								<th>Model</th>
								<th>Production</th>
							</tr>
						</thead>
						<tbody id="body-model">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<tr>
								<th>Total</th>
								<th id="foot-model" style="text-align: right;"></th>
							</tr>
						</tfoot>
					</table>

					<table id="table-model-kensa" class="table table-bordered table-hover">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th colspan="3" style="background-color: #f5a356;text-align: center;color: black;font-size: 20px;padding: 0px;">QA KENSA</th>
							</tr>
							<tr>
								<th>Emp</th>
								<th>Model</th>
								<th>Production</th>
								<!-- <th>Action</th> -->
							</tr>
						</thead>
						<tbody id="body-model-kensa">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<tr>
								<th colspan="2">Total</th>
								<th id="foot-model-kensa" style="text-align: right;"></th>
							</tr>
						</tfoot>
					</table>

					<table id="table-model-kensa-process" class="table table-bordered table-hover">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th colspan="3" style="background-color: #a1deff;text-align: center;color: black;font-size: 20px;padding: 0px;">KENSA PROCESS</th>
							</tr>
							<tr>
								<th>Emp</th>
								<th>Model</th>
								<th>Production</th>
								<!-- <th>Action</th> -->
							</tr>
						</thead>
						<tbody id="body-model-kensa-process">
						</tbody>
						<!-- <tfoot style="background-color: RGB(252, 248, 227);">
							<tr>
								<th colspan="2">Total</th>
								<th id="foot-model-kensa-process" style="text-align: right;"></th>
							</tr>
						</tfoot> -->
					</table>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 0 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500; background-color: white;">
					<i class="fa fa-arrow-down"></i> PRINT <i class="fa fa-arrow-down"></i>
				</div>
				<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw; margin-top: 5px" border="1">
					<tbody>
						<tr>
							<td style="width: 2%;" id="op_id">-</td>
							<td style="width: 8%;" id="op_name">-</td>
						</tr>
					</tbody>
				</table>
				<span style="font-size: 2vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID Card LOWER</span><br>
				<input id="tagBody" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 2vw; width: 100%;">
				<span style="font-size: 2vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID Card UPPER</span><br>
				<input id="tagBodyUpper" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 2vw; width: 100%;">
				<div class="col-xs-12">
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,127,80);">Serial Number</i></span>
				</div>
				<input id="serialNumber" type="text" style="border:0; font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 4vw" onkeyup="fetchModel();">
				<input id="model" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="Not Found">
				<span style="font-size: 2vw; font-weight: bold; color: white;">Pilih Model </span><b id="japan" style="font-size: 2vw; font-weight: bold; color: #83c8e4;"></b><br>
				<div class="col-xs-12" id="listModel" style="padding-top: 15px; padding-bottom: 15px; background-color: white;">

				</div>
				<div class="col-xs-6" style="padding-left: 0;">
					<button class="btn btn-danger" id="print" onclick="clearAll()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;">CANCEL</button>
				</div>
				<div class="col-xs-6" style="padding-right: 0; padding-left: 0;">
					<button class="btn btn-success" id="print" onclick="print()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-print"></i> PRINT</button>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 10px 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; text-align: center; color: #3c3c3c;background-color: white">
					<i class="fa fa-arrow-down"></i> PRINT LOG <i class="fa fa-arrow-down"></i>
				</div>
				<div style="padding-left: 10px">
					<button class="btn btn-danger btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw" data-toggle="modal" data-target="#modalReprint">
						<i class="fa fa-print"></i>&nbsp;Reprint
					</button>
					<a href="{{ url("/stamp/resumes_sx") }}" class="btn btn-primary btn-sm" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw"><i class="fa fa-calendar-check-o"></i>&nbsp;Record</a>
					<table id="logTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgb(240,240,240);">
							<tr>
								<th style="width: 1%">Serial</th>
								<th style="width: 1%">Model</th>
								<th style="width: 2%">By</th>
								<th style="width: 1%">At</th>
							</tr>
						</thead>
						<tbody id="logTableBody">
						</tbody>
						<tfoot>
						</tfoot>						
					</table>
				</div>
			</center>
		</div>
	</div>
	<input type="text" id="japan2" hidden="">
	<input type="text" id="gmc" hidden="">
	<input type="hidden" id="employee_id">
	<input type="hidden" id="started_at">
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Employee ID</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalReprint">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h2 class="modal-title">Reprint Label</h2>
			</div>
			<div class="modal-body">
				<center><span style="font-size: 24px">Ketik Serial Number:</span></center><br>
				<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  id="reprint_serial_number" ><br>
				<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  id="reprint_model"  disabled>
				<input type="text" id="reprint_gmc" hidden>
				<input type="text" id="reprint_status_material" hidden>
			</div>
			<div class="modal-footer">
				<div id="reprint-button">
					<center>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintBesar();">Label GMC</button>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintKecil();">Label No.Seri</button>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintDeskripsi();">Label Deskripsi</button>
						<br><br>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black; padding-left: 5%; padding-right: 5%;" onclick="reprintOuter();">Label Outer</button>
						<button class="btn btn-lg btn-primary" onclick="reprintOuterJp();">Label Outer (Japan)</button>
						<br><br>
						<button id="reprint-carb" class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintCARB();">Label CARB</button>
						<br><br>
						<br><br>
						<button type="button" class="btn btn-danger" onclick="cancelReprint()">Cancel</button>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-print">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h2 class="modal-title">Print Label</h2>
			</div>
			<div class="modal-body">
				<input type="text" style="font-weight: bold; background-color: rgb(255,255,204);; width: 100%; text-align: center; font-size: 4vw"  id="print_serial_number"  disabled><br>
				<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  id="print_model"  disabled>
				<BR><BR>
				<center>
					<button class="btn btn-lg btn-success" onclick="printAll();">Print All Labels</button>
					<br><br>
					<button class="btn btn-lg btn-success" onclick="printOuter();" style="padding-left: 10%; padding-right: 10%;">Label Outer</button>
					<button class="btn btn-lg btn-primary" onclick="printOuterJp();">Label Outer (Japan)</button>		
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-detail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h2 class="modal-title">Detail Kensa</h2>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped table-hover" id="tableKensa">
					<thead style="background-color: cyan">
						<tr>
							<th style="width: 1%;text-align: right;padding-right: 7px;">#</th>
							<th style="width: 2%">Serial</th>
							<th style="width: 2%">Model</th>
							<th style="width: 3%">Emp</th>
							<th style="width: 15%">Timing</th>
						</tr>
					</thead>
					<tbody id="body-detail-kensa">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var kensa;
	var kensa_process;

	jQuery(document).ready(function() {
		// fetchResult();
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').val("");
			$('#operator').focus();
		});
		kensa = null;
		kensa_process = null;
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var logs = [];

	function printOuter(){
		var serial_number = $("#serialNumber").val();
		var gmc = $("#gmc").val();

		window.open('{{ url("index/assembly/clarinet/label_outer") }}'+'/'+serial_number+'/'+gmc+'/NJ/P', '_blank');
	}

	function printOuterJp(){
		var serial_number = $("#serialNumber").val();
		var gmc = $("#gmc").val();

		window.open('{{ url("index/assembly/clarinet/label_outer") }}'+'/'+serial_number+'/'+gmc+'/J/P', '_blank');
	}

	function printAll(){
		var serial_number = $("#serialNumber").val();
		var gmc = $("#gmc").val();
		var employee_id = $("#employee_id").val();

		window.open('{{ url("index/assembly/clarinet/label_besar") }}'+'/'+serial_number+'/'+gmc+'/P/'+employee_id, '_blank');

		var data ={
			tag : $('#tagBodyUpper').val(),
			serial_number:$("#serialNumber").val()
		};


		$.get('{{ url("index/assembly/clear_card") }}', data, function(result, status, xhr){
			if (result.status) {
				$('#tagBodyUpper').val('');
				$('#tagBodyUpper').removeAttr('disabled');

			}else{
				openErrorGritter('Error', result.message);
				$('#tagBodyUpper').val('');
				$('#tagBodyUpper').removeAttr('disabled');
			}
		});

		fetchResult();						
		fillModelResult();

		clearAll();

		$('#reprint_serial_number').focus();


	}

	function print(){

		var serial_number = $("#serialNumber").val();
		var model = $("#model").val();
		var gmc = $("#gmc").val();

		if(gmc){
			$('#print_serial_number').val(serial_number);
			$('#print_model').val(model);
			$('#modal-print').modal('show');
		}else{
			openErrorGritter('Error!','Pilih Model Dulu.');
		}
	}

	function model(name, id){
		$('#model').val(name);
		$('#gmc').val(id);
	}

	$("#modalReprint").on("shown.bs.modal", function () {
		$('#reprint_serial_number').focus();
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		$('#reprint-button').hide();
	});

	$("#modalReprint").on("hidden.bs.modal", function () {
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		cancelReprint();
		$('#tag').focus();
		
	});

	$('#reprint_serial_number').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			reprint();
		}
	});

	function cancelReprint(){
		$('#reprint_serial_number').focus();
		$('#reprint_serial_number').val('');
		$('#reprint_model').val('');
		$('#reprint-button').hide();
	}

	function reprint() {
		var serial_number = $("#reprint_serial_number").val();

		var data ={
			serial_number : serial_number,
			origin_group : '042'
		};


		$.get('{{ url("fetch/assembly/flute/fetchCheckReprint") }}', data, function(result, status, xhr){
			if (result.status) {

				$('#reprint_model').val(result.log.model);
				$('#reprint_gmc').val(result.log.material_number);
				$('#reprint-button').show();

				$('#reprint-carb').hide();

				// VAM5150	YCL-255ES//ID
				// WZ00120	YCL-200ADII//ID
				// WZ00160	YCL-255//U ID
				// WZ44910	YCL-255//ID
				// WZ00180	YCL-255S//ID
				// WZ00170	YCL-255E//ID

				const carb_model = ["YCL-255ES//ID", "YCL-255S//ID", "YCL-255//ID"];
				if ( carb_model.includes(result.log.model) ) {
					$('#reprint-carb').show();
				}

			}else{
				$('#reprint_serial_number').val('');
				$('#reprint_model').val('');
				openErrorGritter('Error', result.message);
			}
		});
		
	}

	function reprintOuter() {
		var serial_number = $('#reprint_serial_number').val();
		var gmc = $('#reprint_gmc').val();

		window.open('{{ url("index/assembly/clarinet/label_outer") }}'+'/'+serial_number+'/'+gmc+'/NJ/RP', '_blank');
		
	}

	function reprintOuterJp() {
		var serial_number = $('#reprint_serial_number').val();
		var gmc = $('#reprint_gmc').val();

		window.open('{{ url("index/assembly/clarinet/label_outer") }}'+'/'+serial_number+'/'+gmc+'/J/RP', '_blank');
		
	}

	function reprintBesar() {
		var serial_number = $('#reprint_serial_number').val();
		var gmc = $('#reprint_gmc').val();
		var employee_id = $("#employee_id").val();

		window.open('{{ url("index/assembly/clarinet/label_besar") }}'+'/'+serial_number+'/'+gmc+'/RP/'+employee_id, '_blank');

	}

	function reprintKecil() {
		var serial_number = $('#reprint_serial_number').val();

		window.open('{{ url("index/assembly/clarinet/label_kecil") }}'+'/'+serial_number+'/RP', '_blank');

	}

	function reprintDeskripsi() {
		var serial_number = $('#reprint_serial_number').val();

		window.open('{{ url("index/assembly/clarinet/label_deskripsi") }}'+'/'+serial_number+'/RP', '_blank');

	}

	function reprintCARB() {
		var serial_number = $('#reprint_serial_number').val();
		window.open('{{ url("index/assembly/clarinet/label_carb") }}'+'/'+serial_number, '_blank');

	}

	$('#tagBody').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBody").val().length == 10){
				var data = {
					employee_id : $("#employee_id").val(),
					tag : $("#tagBody").val(),
					origin_group : '042'
				}
				$.get('{{ url("fetch/assembly/fetchCheckTag") }}', data, function(result, status, xhr){
					if(result.status){
						$('#serialNumber').val(result.assembly_inventory.serial_number);
						$('#model').val(result.assembly_inventory.model);

						$('#listModel').html("");
						var planData = '';

						for (var i = 0; i < result.model.length; i++) {
							var color = 'bg-olive';
							var colorj = 'bg-blue';
							var notif = '';

							planData += '<button type="button" class=" test btn '+color+' btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; height: 60px; font-size: 1vw; font-weight: bold;" id="'+result.model[i].material_number+'" name="'+result.model[i].material_description+'" onclick="model(name, id)">'+result.model[i].material_description+'<br>'+'</button>';

						}
						$('#listModel').append(planData);

						$('#gmc').val("");
						$('#tagBody').val("");
						$('#tagBodyUpper').val("");
						$('#tagBodyUpper').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tagBody').val('');
						$('#tagBody').focus();
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
				$('#tagBody').val('');
				$('#tagBody').focus();	
			}
		}
	});

	$('#tagBodyUpper').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBodyUpper").val().length == 10 || $("#tagBodyUpper").val().length == 8){
				$('#tagBodyUpper').prop('disabled',true);
				openSuccessGritter('Success',"Sukses Scan Tag");
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
				$('#tagBodyUpper').val('');
				$('#tagBodyUpper').removeAttr('disabled');
				$('#tagBodyUpper').focus();	
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				$.get('{{ url("scan/assembly/operator") }}', data, function(result, status, xhr){
					if(result.status){
						audio_ok.play();
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op_id').html(result.employee.employee_id);
						$('#op_name').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						clearAll();

						fetchResult();						
						fillModelResult();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				$("#operator").val("");
			}			
		}
	});

	function clearAll(){
		kensa = null;
		kensa_process = null;
		$('#tagBodyUpper').val("");
		$('#tagBodyUpper').removeAttr('disabled');
		$('#modal-print').modal("hide");
		$('#modalReprint').modal("hide");
		$('#reprint_model').val("");
		$('#reprint_gmc').val("");
		$('#reprint_serial_number').val("");
		$('#reprint_status_material').val("");
		$('#reprint_status_material').val("");
		$('#reprint-button').hide();
		$('#model').val("");
		$('#gmc').val("");
		$('#serialNumber').val("");
		$('#serialNumber').prop("disabled", true);
		$('#model').val("");
		$('#model').prop("disabled", true);
		$('#tagBody').val("");
		$('#tagBody').focus();

		$('#listModel').html("");
	}


	function fillModelResult() {
		var data = {
			origin_group : '042'
		}


		// $.get('{{ url("fetch/assembly/clarinet/fillModelResult") }}', data, function(result, status, xhr){
		// 	if (result.status) {

		// 		$("#body-model").empty();
		// 		$("#foot-model").empty();
		// 		array_target = [];
		// 		model_target = [];
		// 		var model_act = [];

		// 		var body = '';
		// 		var quantity = 0;
		// 		var target = 0;
		// 		for (var j = 0; j < result.target.length; j++) {
		// 			var qty_act = 0;
		// 			for (var i = 0; i < result.data.length; i++) {
		// 				if (result.data[i].model == result.target[j].material_description) {
		// 					quantity += result.data[i].quantity;
		// 					qty_act = result.data[i].quantity;
		// 				}
		// 			}
		// 			model_target.push(result.target[j].material_description);
		// 			if (parseInt(qty_act) < parseInt(result.target[j].quantity)) {
		// 				var color = 'RGB(255,204,255)'; 
		// 			}else{
		// 				var color = 'RGB(204,255,255)';
		// 			}
		// 			if (parseInt(result.target[j].quantity) == 0) {
		// 				var color_target = "background-color:RGB(255,204,255)";
		// 			}else{
		// 				var color_target = '';
		// 			}
		// 			body += '<tr>';
		// 			body += '<td style="background-color:rgb(245, 245, 245)">'+result.target[j].material_description+'</td>';
		// 			body += '<td style="'+color_target+'">'+result.target[j].quantity+'</td>';
		// 			body += '<td style="background-color:'+color+'">'+qty_act+'</td>';
		// 			body += '</tr>';

		// 			array_target.push({model:result.target[j].material_description,quantity:(parseInt(result.target[j].quantity)-parseInt(qty_act))});
		// 			target += result.target[j].quantity;
		// 		}

		// 		for(var k = 0; k < result.data.length;k++){
		// 			if (!model_target.includes(result.data[k].model)) {
		// 				body += '<tr>';
		// 				body += '<td style="background-color:rgb(245, 245, 245)">'+result.data[k].model+'</td>';
		// 				body += '<td style="background-color:RGB(255,204,255)">0</td>';
		// 				body += '<td style="background-color:RGB(204,255,255)">'+result.data[k].quantity+'</td>';
		// 				body += '</tr>';
		// 			}
		// 		}

		// 		$("#body-model").append(body);
		// 		$("#foot-model").html(quantity);
		// 		$("#foot-model-target").html(target);

		// 	}
		// });
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fetchResult(){
		var data = {
			origin_group_code : '042',
			location : 'packing'
		}
		$.get('{{ url("fetch/assembly/stamp_result") }}', data, function(result, status, xhr){
			if(result.status){
				$('#logTable').DataTable().clear();
				$('#logTable').DataTable().destroy();
				$('#logTableBody').html('');

				logs = result.logs;
				var tableData = '';
				var no = 1

				var models = [];
				var todays = [];

				kensa = null;
				kensa_process = null;

				// var models_kensa = [];
				// var todays_kensa = [];

				$.each(logs, function(key, value){
					if (no % 2 === 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}
					tableData += '<tr '+color+'>';
					tableData += '<td style="vertical-align:middle">'+value.serial_number+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.model+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.name+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.created_at+'</td>';
					tableData += '</tr>';
					no += 1;

					var re = new RegExp('{{date("Y-m-d")}}', 'g');
					if (value.created_at.match(re)) {
						models.push(value.model);
						todays.push(value.model);
					}
				});

				// $.each(logs_kensa, function(key, value){
				// 	var re = new RegExp('{{date("Y-m-d")}}', 'g');
				// 	if (value.created_at.match(re)) {
				// 		models_kensa.push(value.model);
				// 		todays_kensa.push(value.model);
				// 	}
				// });

				var model_unik = models.filter(onlyUnique);
				// var model_unik_kensa = models_kensa.filter(onlyUnique);

				$('#body-model').html('');
				var body_model = '';
				var count = 0;
				$('#foot-model').html(0);

				for(var i = 0; i < model_unik.length;i++){
					body_model += '<tr style="background-color: #fffcb7">';
					body_model += '<td style="text-align: left; font-size: 1.2vw;">'+model_unik[i]+'</td>';
					var qty = 0;
					for(var j = 0; j < todays.length;j++){
						if (todays[j] == model_unik[i]) {
							qty++;
						}
					}
					body_model += '<td style="text-align: right; font-size: 1.2vw;">'+qty+'</td>';
					body_model += '</tr>';
					count = count+qty;
				}
				$('#body-model').append(body_model);
				$('#foot-model').html(count);

				$('#body-model-kensa').html('');
				var body_model_kensa = '';
				var count_kensa = 0;
				$('#foot-model-kensa').html(0);
				var operator_kensa = [];

				for(var i = 0; i < result.logs_kensa.length;i++){
					operator_kensa.push(result.logs_kensa[i].operator_id);
				}

				var op_unik = operator_kensa.filter(onlyUnique);

				for(var i = 0; i < op_unik.length;i++){
					for(var j = 0; j < result.model.length;j++){
						body_model_kensa += '<tr style="background-color: #fffcb7">';
						var name = '';
						for(var k = 0; k < result.emp.length;k++){
							if (result.emp[k].employee_id == op_unik[i]) {
								name = result.emp[k].name;
							}
						}
						body_model_kensa += '<td style="text-align: left; font-size: 1.2vw;">'+name.split(' ').slice(0,2).join(' ')+'</td>';
						body_model_kensa += '<td style="text-align: left; font-size: 1.2vw;">'+result.model[j].model+'</td>';
						var count = 0;
						for(var k = 0; k < result.logs_kensa.length;k++){
							if (result.model[j].model == result.logs_kensa[k].model && op_unik[i] == result.logs_kensa[k].operator_id) {
								count = result.logs_kensa[k].count;
							}
						}
						body_model_kensa += '<td style="text-align: right; font-size: 1.2vw;">'+count+'</td>';
						body_model_kensa += '</tr>';
						count_kensa = count_kensa+parseInt(count);
					}
				}
				// for(var i = 0; i < result.logs_kensa.length;i++){
				// 	for(var j = 0; j < result.model.length;j++){
				// 		var name = '';
				// 		var count = 0;
				// 		body_model_kensa += '<tr style="background-color: #fffcb7">';
				// 		for(var k = 0; k < result.emp.length;k++){
				// 			if (result.emp[k].employee_id == result.logs_kensa[i].operator_id) {
				// 				name = result.emp[k].name;
				// 			}
				// 		}
				// 		body_model_kensa += '<td style="text-align: left; font-size: 1.2vw;">'+name.split(' ').slice(0,2).join(' ')+'</td>';
				// 		body_model_kensa += '<td style="text-align: left; font-size: 1.2vw;">'+result.model[j].model+'</td>';
				// 		if (result.model[j].model == result.logs_kensa[i].model) {
				// 			count = result.logs_kensa[i].count;
				// 		}
				// 		body_model_kensa += '<td style="text-align: right; font-size: 1.2vw;">'+count+'</td>';
				// 		body_model_kensa += '</tr>';
				// 		count_kensa = count_kensa+parseInt(count);
				// 	}
				// 	// var code = 'qa';
				// 	// body_model_kensa += '<td style="text-align: right; font-size: 1.2vw;"><button class="btn btn-info" onclick="detailKensa(\''+result.logs_kensa[i].operator_id+'\',\''+result.logs_kensa[i].model+'\',\''+code+'\')">Detail</button></td>';
				// }
				$('#body-model-kensa').append(body_model_kensa);
				$('#foot-model-kensa').html(count_kensa);

				$('#body-model-kensa-process').html('');
				var body_model_kensa_process = '';
				var count_kensa_process = 0;
				// $('#foot-model-kensa-process').html(0);

				var operator_kensa = [];

				for(var i = 0; i < result.logs_kensa_process.length;i++){
					operator_kensa.push(result.logs_kensa_process[i].operator_id);
				}

				var op_unik = operator_kensa.filter(onlyUnique);

				var count_op = [];

				for(var i = 0; i < op_unik.length;i++){
					var count_ops = 0;
					for(var j = 0; j < result.model.length;j++){
						body_model_kensa_process += '<tr style="background-color: #fffcb7">';
						var name = '';
						for(var k = 0; k < result.emp.length;k++){
							if (result.emp[k].employee_id == op_unik[i]) {
								name = result.emp[k].name;
							}
						}
						body_model_kensa_process += '<td style="text-align: left; font-size: 1.2vw;">'+name.split(' ').slice(0,2).join(' ')+'</td>';
						body_model_kensa_process += '<td style="text-align: left; font-size: 1.2vw;">'+result.model[j].model+'</td>';
						var count = 0;
						for(var k = 0; k < result.logs_kensa_process.length;k++){
							if (result.model[j].model == result.logs_kensa_process[k].model && op_unik[i] == result.logs_kensa_process[k].operator_id) {
								count = result.logs_kensa_process[k].count;
							}
						}
						body_model_kensa_process += '<td style="text-align: right; font-size: 1.2vw;">'+count+'</td>';
						body_model_kensa_process += '</tr>';
						count_kensa_process = count_kensa_process+parseInt(count);
						count_ops = count_ops+parseInt(count);
					}
					count_op.push(count_ops);
					body_model_kensa_process += '<tr>';
					body_model_kensa_process += '<th style="background-color:RGB(252, 248, 227);border:1px solid black;" colspan="2">Total</th>';
					body_model_kensa_process += '<th id="count_op_'+i+'" style="text-align: right;background-color:RGB(252, 248, 227);border:1px solid black;"></th>';
					body_model_kensa_process += '</tr>';
				}
				$('#body-model-kensa-process').append(body_model_kensa_process);
				for(var i = 0; i < op_unik.length;i++){
					$('#count_op_'+i).html(count_op[i]);
				}
				// $('#foot-model-kensa-process').html(count_kensa_process);

				$('#logTableBody').append(tableData);

				$('#logTable').DataTable({
					"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					'paging'      	: true,
					'lengthChange'	: false,
					'searching'   	: true,
					'ordering'		: false,
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"infoCallback": function( settings, start, end, max, total, pre ) {
						return "<b>Total "+ total +" pc(s)</b>";
					}
				});

				// var array = result.logs;
				// var result = [];
				// array.reduce(function(res, value) {
				// 	if (!res[value.model]) {
				// 		res[value.model] = { model: value.model, count: 0 };
				// 		result.push(res[value.model])
				// 	}
				// 	res[value.model].count += 1;
				// 	return res;
				// }, {});

				// kensa_process = result.logs_kensa_process_detail;
				// kensa = result.logs_kensa_detail;
			}
			else{
				audio_error.play();
				openErrorGritter(result.message);				
			}
		});
}

function detailKensa(employee_id,model,code) {
	$('#body-detail-kensa').html('');
	var detail = '';

	if (code == 'qa') {
		var index = 1;
		for(var i = 0; i < kensa.length;i++){
			if (kensa[i].operator_id == employee_id && kensa[i].model == model) {
				detail += '<tr>';
				detail += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
				detail += '<td>'+kensa[i].serial_number+'</td>';
				detail += '<td>'+kensa[i].model+'</td>';
				detail += '<td>'+kensa[i].operator_id+' - '+kensa[i].name+'</td>';
				if (kensa[i].sedang_start_date.match(/,/gi)) {
					var start = kensa[i].sedang_start_date.split(',');
					var finish = kensa[i].sedang_finish_date.split(',');
					detail += '<td>';
					for(var j = 0; j < start.length;j++){
						detail += start[j]+' - '+finish[j]+'<br>';
					}
					detail += '</td>';
				}else{
					detail += '<td>'+kensa[i].sedang_start_date+' - '+kensa[i].sedang_finish_date+'</td>';
				}
				detail += '</tr>';
				index++;
			}
		}
	}else{
		var index = 1;
		for(var k = 0; k < kensa_process.length;k++){
			if (kensa_process[k].operator_id == employee_id && kensa_process[k].model == model) {
				console.log(kensa_process[k].serial_number);
				detail += '<tr>';
				detail += '<td style="text-align:right;padding-right:7px;">'+index+'</td>';
				detail += '<td>'+kensa_process[k].serial_number+'</td>';
				detail += '<td>'+kensa_process[k].model+'</td>';
				detail += '<td>'+kensa_process[k].operator_id+' - '+kensa_process[k].name+'</td>';
				if (kensa_process[k].sedang_start_date.match(/,/gi)) {
					var start = kensa_process[k].sedang_start_date.split(',');
					var finish = kensa_process[k].sedang_finish_date.split(',');
					detail += '<td>';
					for(var j = 0; j < start.length;j++){
						detail += start[j]+' - '+finish[j]+'<br>';
					}
					detail += '</td>';
				}else{
					detail += '<td>'+kensa_process[k].sedang_start_date+' - '+kensa_process[k].sedang_finish_date+'</td>';
				}
				detail += '</tr>';
				index++;
			}
		}
	}

	$('#body-detail-kensa').append(detail);
	$('#modal-detail').modal('show');
}

function onlyUnique(value, index, self) {
	return self.indexOf(value) === index;
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

