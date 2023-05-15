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

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff143c;
			color: rgb(255,255,150);

		}
		50%, 100% {
			background-color: #fff;
			color: black;
		}
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
					<i class="fa fa-arrow-down"></i> TOTAL REGISTRATION <i class="fa fa-arrow-down"></i>
				</div>
				<div class="col-xs-12" style="padding-left: 0; padding-top: 5px;">
					<div class="col-xs-6"  style="padding-left: 0; padding-right: 2.5px;">
						<table id="tableAlto" class="table table-bordered table-hover" border="1">
							<thead style="background-color: yellow;">
								<th style="text-align: left; font-size: 1.5vw;">Alto</th>
								<th style="text-align: right; font-size: 1.5vw;">Qty</th>
							</thead>
							<tbody id="tableAltoBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th style="text-align: left; font-size: 1.5vw;">Total</th>
								<th style="text-align: right; font-size: 1.5vw;" id="totalAlto"></th>
							</tfoot>
						</table>
					</div>
					<div class="col-xs-6" style="padding-left: 2.5px; padding-right: 0;">
						<table id="tableTenor" class="table table-bordered table-hover" border="1">
							<thead style="background-color: #3d9970;">
								<th style="text-align: left; font-size: 1.5vw;">Tenor</th>
								<th style="text-align: right; font-size: 1.5vw;">Qty</th>
							</thead>
							<tbody id="tableTenorBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th style="text-align: left; font-size: 1.5vw;">Total</th>
								<th style="text-align: right; font-size: 1.5vw;" id="totalTenor"></th>
							</tfoot>
						</table>
					</div>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 0 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500; background-color: white;">
					<i class="fa fa-arrow-down"></i> RECORD <i class="fa fa-arrow-down"></i>
				</div>
				<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw; margin-top: 5px" border="1">
					<tbody>
						<tr>
							<td style="width: 2%;" id="op_id">-</td>
							<td style="width: 8%;" id="op_name">-</td>
						</tr>
					</tbody>
				</table>
				<div class="col-xs-12 sedang" style="padding-left: 0; padding-right: 0;background-color: red" id="divTrial">
					<span style="font-size: 6vw; font-weight: bold;">ITEM TRIAL</span>
				</div>
				<span style="font-size: 2vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID Card</span><br>
				<div class="col-xs-9" style="padding-left: 0; padding-right: 0;">
					<input id="tagBody" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 4vw; width: 100%;">
				</div>
				<div class="col-xs-3" style="padding-right: 0;">
					<button class="btn btn-danger" id="resetTag" onclick="clearAll()" style="width: 100%; font-size: 3.4vw"><i class="fa fa-refresh"></i></button>
				</div>
				<div class="col-xs-12">
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,127,80);">Serial Number</i></span>
				</div>
				<input id="serialNumber" type="text" style="border:0; font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 4vw" onkeyup="fetchModel();">
				<input id="model" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="Not Found">
				<div class="col-xs-4" style="padding-left: 0;">
					<button class="btn btn-danger" id="btnChange" onclick="modalChange()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-wrench"></i></button>
				</div>
				<div class="col-xs-8" style="padding-left: 0; padding-right: 0;">
					<button class="btn btn-success" id="print" onclick="print()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-print"></i> PRINT</button>
				</div>
			</center>
		</div>
		<div class="col-xs-4" style="padding: 0 10px 0 0;">
			<center>
				<div style="font-weight: bold; font-size: 2.5vw; text-align: center; color: #3c3c3c;background-color: white">
					<i class="fa fa-arrow-down"></i> REGISTRATION LOG <i class="fa fa-arrow-down"></i>
				</div>
				<div style="padding-left: 10px">
					<button class="btn btn-danger btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw" data-toggle="modal" data-target="#modalReprint">
						<i class="fa fa-print"></i>&nbsp;Reprint
					</button>
					<a href="{{ url("/index/assembly/stamp_record/043") }}" class="btn btn-primary btn-sm" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 40%; font-size: 1.3vw"><i class="fa fa-calendar-check-o"></i>&nbsp;Record</a>
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
	<input type="hidden" id="employee_id">
	<input type="hidden" id="started_at">
</section>

<div class="modal modal-default fade" id="modalReprint" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Reprint Serial Number</h4>
			</div>
			<div class="modal-body" id="divReprint">
				<label>Serial Number</label>
				<select class="form-control select2" name="serial_number_reprint" style="width: 100%;" data-placeholder="Pilih Serial Number ..." id="serial_number_reprint" required>
					<option value=""></option>
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				<button onclick="reprintStamp()" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
			</div>
		</div>
	</div>
</div>

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

<div class="modal fade" id="modalChange">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h4 class="modal-title">Change Model</h4>
				</center>
			</div>
			<div class="modal-body">
				<center>
					<input id="newModel" type="text" style="border:0; font-weight: bold; background-color: #dd4b39; width: 100%; text-align: center; font-size: 4vw; color: yellow;" disabled>
					@foreach($models as $model)
					<button id="{{ $model->model }}" onclick="putModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw;">{{ $model->model }}</button>
					@endforeach
				</center>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Close</button>
				<button type="button" onclick="updateModel()" class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Change Model</button>
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

	var sn_trial = <?php echo json_encode($sn_trial) ?>;

	jQuery(document).ready(function() {
		$('#divTrial').hide();
		$('#serial_number_reprint').select2({
			allowClear:true,
			dropdownParent: $('#divReprint'),
		});
		clearAll();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').val("");
			$('#operator').focus();
		});
		fetchResult();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var logs = [];

	function print(){
		var origin_group_code = '043';
		var model = $('#model').val();
		var serial_number = $('#serialNumber').val();
		var tagName = $('#tagBody').val();
		var op_id = $('#employee_id').val();
		var started_at = $('#started_at').val();
		var location = 'registration-process';

		var data = {
			origin_group_code: origin_group_code,
			model:model,
			serial_number:serial_number,
			tagName:tagName,
			op_id:op_id,
			started_at:started_at,
			location:location
		}

		$.post('{{ url("input/assembly/registration_process") }}', data, function(result, status, xhr){
			if(result.status){
				fetchResult();
				audio_ok.play();
				openSuccessGritter('Success!', result.message);
				clearAll();
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
				return false;
			}
		});
	}

	function fetchResult(){
		var data = {
			origin_group_code : '043',
			location : 'registration-process'
		}
		$.get('{{ url("fetch/assembly/stamp_result") }}', data, function(result, status, xhr){
			if(result.status){
				$('#logTable').DataTable().clear();
				$('#logTable').DataTable().destroy();
				$('#logTableBody').html('');


				$('#serial_number_reprint').html("");
				var reprint = "";
				reprint += '<option value=""></option>';
				$.each(result.logsall, function(key, value){
					reprint += '<option value="'+value.serial_number+'">'+value.serial_number+'</option>';
				});
				$('#serial_number_reprint').append(reprint);

				logs = result.logs;
				var array = result.logs;
				var result = [];
				array.reduce(function(res, value) {
					if (!res[value.model]) {
						res[value.model] = { model: value.model, count: 0 };
						result.push(res[value.model])
					}
					res[value.model].count += 1;
					return res;
				}, {});

				var tableAltoBody = "";
				var tableTenorBody = "";
				var totalAlto = 0;
				var totalTenor = 0;
				$('#tableAltoBody').html("");
				$('#tableTenorBody').html("");

				$.each(result, function(key, value){
					var model = value.model;
					if (model.match("^YAS")) {
						tableAltoBody += '<tr style="background-color: #fffcb7">';
						tableAltoBody += '<td style="text-align: left; font-size: 1.2vw;">'+value.model+'</td>';
						tableAltoBody += '<td style="text-align: right; font-size: 1.2vw;">'+value.count+'</td>';
						tableAltoBody += '</tr>';
						totalAlto += value.count;
					}
					if (model.match("^YTS")) {
						tableTenorBody += '<tr style="background-color: #fffcb7">';
						tableTenorBody += '<td style="text-align: left; font-size: 1.2vw;">'+value.model+'</td>';
						tableTenorBody += '<td style="text-align: right; font-size: 1.2vw;">'+value.count+'</td>';
						tableTenorBody += '</tr>';
						totalTenor += value.count;
					}
				});
				$('#tableAltoBody').append(tableAltoBody);
				$('#tableTenorBody').append(tableTenorBody);
				$('#totalAlto').text(totalAlto);
				$('#totalTenor').text(totalTenor);

				var tableData = '';
				var no = 1

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
				});
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
			}
			else{
				audio_error.play();
				openErrorGritter(result.message);
				return false;
			}
		});
	}

	function reprintStamp() {
		var serial_number = $('#serial_number_reprint').val();
		if (serial_number == '') {
			alert('Pilih Serial Number');
		}else{
			var data = {
				serial_number:serial_number,
				origin_group_code:'043'
			}

			$.get('{{ url("reprint/assembly/stamp") }}', data, function(result, status, xhr){
				if(result.status){
					$('#serial_number_reprint').val("").trigger('change');
					$('#reprintModal').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchResult();
				}
				else{
					audio_error.play();
					openErrorGritter(result.message);
					return false;
				}
			});
		}
	}

	function fetchModel(){
		var serial_number = $("#serialNumber").val();
		var data = {
			serial_number : serial_number,
			process_code : '1',
			origin_group_code : '043'
		}
		if(serial_number.length == 8) {
			$.get('{{ url("fetch/assembly/model") }}', data, function(result, status, xhr){
				if(result.status){
					// if (result.stamp_inventory.model == $('#suggestion').text()) {
						$('#model').val(result.stamp_inventory.model);
						if (result.log_process.status_material == 'TRIAL') {
							$('#divTrial').show();
						}
						if (sn_trial.includes(serial_number)) {
							$('#divTrial').show();
						}
						if(result.stamp_inventory.model.length <= 3 || result.stamp_inventory.model == 'INDONESIA' || result.stamp_inventory.model == 'CHINA'){
							$('#btnChange').prop("disabled", false);
							$('#print').prop("disabled", true);
						}
						else{
							$('#btnChange').prop("disabled", false);
							$('#print').prop("disabled", false);
						}
					// }else{
					// 	audio_error.play();
					// 	openErrorGritter('Error!','Tipe Produk Tidak Sesuai Pengambilan.');
					// 	$('#model').val('Not Found');
					// 	$('#serialNumber').prop('disabled', false);
					// 	$('#serialNumber').val('');
					// 	$('#serialNumber').focus();
					// 	return false;
					// }
				}
				else{
					audio_error.play();
					openErrorGritter(result.message);
					$('#model').val('Not Found');
					$('#serialNumber').prop('disabled', false);
					$('#serialNumber').val('');
					$('#serialNumber').focus();
					return false;
				}
			});
		}
		else{
			$("#model").val('Not Found');
			$('#print').prop("disabled", true);
			$('#btnChange').prop("disabled", true);
		}
	}

	$('#tagBody').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBody").val().length == 10){
				var data = {
					tag : $("#tagBody").val(),
					origin_group_code : '043'
				}
				$.get('{{ url("scan/assembly/tag_stamp") }}', data, function(result, status, xhr){
					if(result.status){
						$('#tagBody').val(result.tag.remark);
						$('#started_at').val(result.started_at);
						$('#tagBody').prop('disabled', true);
						$('#serialNumber').prop('disabled', false);
						$('#serialNumber').val('');
						$('#serialNumber').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tagBody').val('');
						$('#tagBody').focus();
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
				$('#tagBody').val('');
				$('#tagBody').focus();
				return false;
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
						// intervalTag = setInterval(focusTag, 1000);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
						return false;
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				$("#operator").val("");
				return false;
			}			
		}
	});

	function clearAll(){
		$('#divTrial').hide();
		$('#newModel').val("");
		$('#started_at').val("");
		$('#serialNumber').val("");
		$('#serialNumber').prop("disabled", true);
		$('#print').prop("disabled", true);
		$('#btnChange').prop("disabled", true);
		$('#model').val("Not Found");
		$('#model').prop("disabled", true);
		$('#tagBody').val("");
		$('#tagBody').prop("disabled", false);
		$('#tagBody').focus();
	}

	function updateModel(){
		var model = $('#newModel').val();
		var serial_number = $('#serialNumber').val();
		var origin_group_code = '043';
		var location = 'registration-process';
		var op_id = $('#employee_id').val();
		var started_at = $('#started_at').val();

		if(model == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Pilih model terlebih dahulu.');
			return false;
		}

		var data = {
			model : model,
			serial_number : serial_number,
			origin_group_code : origin_group_code,
			location : location,
			op_id : op_id,
			started_at : started_at
		}

		$.post('{{ url("edit/assembly/model") }}', data, function(result, status, xhr){
			if(result.status){
				$('#model').val(result.model);
				$('#newModel').val('');
				$('#print').prop('disabled', false);

				audio_ok.play();
				openSuccessGritter('Success!', result.message);

				$('#modalChange').modal('hide');
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
				return false;
			}
		});
	}

	function modalChange(){
		$('#modalChange').modal('show');
	}

	function putModel(id){
		$('#newModel').val(id);
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

