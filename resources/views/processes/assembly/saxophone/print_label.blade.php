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
					<i class="fa fa-arrow-down"></i> TOTAL PRINT <i class="fa fa-arrow-down"></i>
				</div>
				<div class="col-xs-12" style="padding-left: 0; padding-top: 5px;">
					<div class="col-xs-6"  style="padding-left: 0; padding-right: 2.5px;">
						<table id="tableAlto" class="table table-bordered table-hover table-striped" border="1">
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
						<table id="tableTenor" class="table table-bordered table-hover table-striped" border="1">
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
				<div class="col-xs-12 sedang" style="padding-left: 0; padding-right: 0;background-color: red" id="divTrial">
					<span style="font-size: 6vw; font-weight: bold;">ITEM TRIAL</span>
				</div>
				<span style="font-size: 2vw; font-weight: bold; color: rgb(255,255,150);">Tap RFID Card</span><br>
				<input id="tagBody" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); text-align: center; font-size: 2vw; width: 100%;">
				<div class="col-xs-12">
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,127,80);">Serial Number</i></span>
				</div>
				<input id="serialNumber" type="text" style="border:0; font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 4vw" onkeyup="fetchModel();">
				<input id="model" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" value="Not Found">
				<span style="font-size: 2vw; font-weight: bold; color: white;">Pilih Model </span><b id="japan" style="font-size: 2vw; font-weight: bold; color: #83c8e4;"></b><br>
				<div class="col-xs-12" id="listModel" style="padding-top: 15px; padding-bottom: 15px; background-color: white;">

				</div>
				<div class="col-xs-6" style="padding-left: 0;">
					<button class="btn btn-danger" onclick="clearAll()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;">CANCEL</button>
				</div>
				<div class="col-xs-6" style="padding-right: 0; padding-left: 0;">
					<button class="btn btn-success" onclick="print()" style="width: 100%; font-size: 3.4vw; font-weight: bold; margin-top: 5px;"><i class="fa fa-print"></i> PRINT</button>
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
				<input type="text" style="font-weight: bold; background-color: rgb(255,127,80);; width: 100%; text-align: center; font-size: 2vw"  id="reprint_model"  readonly>
				<input type="hidden" id="reprint_gmc" value="">
				<input type="hidden" id="reprint_status_material" value="">
			</div>
			<div class="modal-footer">
				<div id="reprint-button">
					<center>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintKecil();">Label No.Seri</button>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black;" onclick="reprintDeskripsi();">Label Deskripsi</button>
						<br><br>
						<button class="btn btn-lg btn-default" style="color: black; border-color: black; padding-left: 5%; padding-right: 5%;" onclick="reprintOuter();">Label Outer</button>
						<button class="btn btn-lg btn-primary" onclick="reprintOuterJp();">Label Outer (Japan)</button>
						{{-- <button class="btn btn-lg btn-primary" style="font-weight: bold; border-color: black;" onclick="reprint_all();">REPRINT ALL LABEL</button> --}}
						<br><br>
						<br><br>
						<button class="btn btn-lg btn-danger" onclick="clearAll()">Batal</button>
					</center>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#divTrial').hide();
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

	function fetchResult(){
		var data = {
			origin_group_code : '043',
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

			}
			else{
				audio_error.play();
				openErrorGritter(result.message);				
			}
		});
	}

	$('#tagBody').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tagBody").val().length == 10){
				var data = {
					tag : $("#tagBody").val(),
					origin_group : '043'
				}
				$.get('{{ url("fetch/assembly/fetchCheckTag") }}', data, function(result, status, xhr){
					if(result.status){
						$('#serialNumber').val(result.assembly_inventory.serial_number);
						$('#model').val(result.assembly_inventory.model);
						if (result.assembly_inventory.trial == 'TRIAL') {
							$('#divTrial').show();
						}
						$('#listModel').html("");
						var planData = '';

						for (var i = 0; i < result.model.length; i++) {
							var color = 'bg-olive';
							var colorj = 'bg-blue';
							var notif = '';

							if (result.assembly_inventory.remark.split('_')[1] == 'SP') {
								if(result.model[i].remark=="J") {
									planData += '<button type="button" class=" test btn '+colorj+' btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; height: 60px; font-size: 1vw; font-weight: bold;" id="'+result.model[i].material_number+'" name="'+result.model[i].material_description+'" onclick="model(name, id, \'J\');japan(\'(Japan)\')">'+result.model[i].material_description+'<br>Japan'+'</button>';
								}
							}else{
								if(result.model[i].remark=="") {
									planData += '<button type="button" class=" test btn '+color+' btn-lg" style="margin-top: 2px; margin-left: 1px; margin-right: 1px; width: 32%; height: 60px; font-size: 1vw; font-weight: bold;" id="'+result.model[i].material_number+'" name="'+result.model[i].material_description+'" onclick="model(name, id, \'NJ\');japan(\'\')">'+result.model[i].material_description+'<br>'+'</button>';
								}
							}
						}
						$('#listModel').append(planData);

						$('#gmc').val("");
						$('#japan').text("");
						$('#japan2').val("");
						$('#tagBody').val("");
						$('#tagBody').focus();
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
			origin_group : '043'
		};


		$.get('{{ url("fetch/assembly/flute/fetchCheckReprint") }}', data, function(result, status, xhr){
			if (result.status) {

				$('#reprint_model').val(result.log.model);
				$('#reprint_gmc').val(result.log.material_number);
				$('#reprint-button').show();	

			}else{
				$('#reprint_serial_number').val('');
				$('#reprint_model').val('');
				openErrorGritter('Error', result.message);
			}
		});
		
	}

	function reprintKecil(){
		var serial_number = $('#reprint_serial_number').val();
		var material_number = $('#reprint_gmc').val();
		var japan = $('#reprint_status_material').val();

		window.open('{{ url("index/assembly/saxophone/label_kecil") }}'+'/'+serial_number+'/RP', '_blank');
	}

	function reprintDeskripsi(){
		var serial_number = $('#reprint_serial_number').val();
		var material_number = $('#reprint_gmc').val();
		var japan = $('#reprint_status_material').val();

		window.open('{{ url("index/assembly/saxophone/label_des") }}'+'/'+serial_number, '_blank');
	}

	function reprintOuter(){
		var serial_number = $('#reprint_serial_number').val();
		var material_number = $('#reprint_gmc').val();
		var japan = $('#reprint_status_material').val();

		window.open('{{ url("index/assembly/saxophone/label_reprint") }}'+'/'+serial_number+'/'+material_number+'/NJR'+japan, '_blank');

	}

	function reprintOuterJp(){
		var serial_number = $('#reprint_serial_number').val();
		var material_number = $('#reprint_gmc').val();
		var japan = $('#reprint_status_material').val();

		window.open('{{ url("index/assembly/saxophone/label_reprint") }}'+'/'+serial_number+'/'+material_number+'/JR'+japan, '_blank');
		
	}

	function print(){	
		var serial_number = $("#serialNumber").val();
		var material_number = $("#gmc").val();
		var japan = $("#japan2").val();
		var operator_id = $("#employee_id").val();

		if(material_number.length <= 0){
			audio_error.play();
			openErrorGritter('Error!','Pilih Model Dulu.');
			return false;
		}

		window.open('{{ url("index/assembly/saxophone/label_besar") }}'+'/'+serial_number+'/'+material_number+'/'+japan+'/'+operator_id, '_blank');

		fetchResult();
		clearAll();

		location.reload();
	}

	function model(name, id, japan){
		$('#model').val(name);
		$('#gmc').val(id);
		$('#japan2').val(japan);
	}

	function japan(id) {
		$('#japan').text(id);
	}

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
		$('#divTrial').hide();
		$('#modalReprint').modal("hide");
		$('#reprint_model').val("");
		$('#reprint_gmc').val("");
		$('#reprint_serial_number').val("");
		$('#reprint_status_material').val("");
		$('#reprint_status_material').val("");
		$('#reprint-button').hide();
		$('#model').val("");
		$('#gmc').val("");
		$('#japan').text("");
		$('#japan2').val("");
		$('#serialNumber').val("");
		$('#serialNumber').prop("disabled", true);
		$('#model').val("");
		$('#model').prop("disabled", true);
		$('#tagBody').val("");
		$('#tagBody').focus();
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

