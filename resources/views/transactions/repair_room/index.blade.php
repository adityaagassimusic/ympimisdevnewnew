@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding-top: 0;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: white;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
	}
	table.table-hover > tbody > tr:hover {
		background-color: #7dfa8c !important;
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
	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}
	img {max-width:100%}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div class="row">
		<input type="hidden" id="location" value="{{ $location }}">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<center>
				<table style="width: 40%;" border="0">
					<thead>
						<tr>
							<th style="width: 1%;"><input style="text-align: center; font-weight: bold; background-color: #ffa500; color: black; font-size: 30px; border-color: black; height: 50px;" type="text" class="form-control" id="employee_id" disabled></th>
							<th style="width: 3%;"><input style="text-align: center; font-weight: bold; background-color: #ffa500; color: black; font-size: 30px; border-color: black; height: 50px;" type="text" class="form-control" id="employee_name" disabled></th>
						</tr>				
					</thead>
				</table>
			</center>
		</div>
		<div class="col-xs-3">
			<div style="font-weight: bold; font-size: 1.5vw; text-align: center; color: white; background-color: #DB3069; border-style: solid; border-color: black; border-width: 1px 1px 1px 1px; margin-bottom: 5px;">
				<i class="fa fa-arrow-down"></i> CLARINET <i class="fa fa-arrow-down"></i>
			</div>
			<table id="tableClarinet" class="table table-bordered table-hover">
				<thead style="">
					<tr>
						<th style="width: 1%; text-align: left; background-color: #DB3069; color: white;">Material</th>
						<th style="width: 0.1%; text-align: center; background-color: #DB3069; color: white;">#</th>
					</tr>
				</thead>
				<tbody id="tableClarinetBody">
				</tbody>
			</table>
		</div>
		<div class="col-xs-3">
			<div style="font-weight: bold; font-size: 1.5vw; text-align: center; color: white; background-color: #1446A0; border-style: solid; border-color: black; border-width: 1px 1px 1px 1px; margin-bottom: 5px;">
				<i class="fa fa-arrow-down"></i> FLUTE <i class="fa fa-arrow-down"></i>
			</div>
			<table id="tableFlute" class="table table-bordered table-hover">
				<thead style="">
					<tr>
						<th style="width: 1%; text-align: left; background-color: #1446A0; color: white;">Material</th>
						<th style="width: 0.1%; text-align: center; background-color: #1446A0; color: white;">#</th>
					</tr>
				</thead>
				<tbody id="tableFluteBody">
				</tbody>
			</table>
		</div>
		<div class="col-xs-3">
			<div style="font-weight: bold; font-size: 1.5vw; text-align: center; color: black; background-color: #F5D547; border-style: solid; border-color: black; border-width: 1px 1px 1px 1px; margin-bottom: 5px;">
				<i class="fa fa-arrow-down"></i> SAXOPHONE <i class="fa fa-arrow-down"></i>
			</div>
			<table id="tableSaxophone" class="table table-bordered table-hover">
				<thead style="">
					<tr>
						<th style="width: 1%; text-align: left; background-color: #F5D547; color: black;">Material</th>
						<th style="width: 0.1%; text-align: center; background-color: #F5D547; color: black;">#</th>
					</tr>
				</thead>
				<tbody id="tableSaxophoneBody">
				</tbody>
			</table>
		</div>
		<div class="col-xs-3">
			<div style="font-weight: bold; font-size: 1.5vw; text-align: center; color: white; background-color: #605ca8; border-style: solid; border-color: black; border-width: 1px 1px 1px 1px; margin-bottom: 5px;">
				<i class="fa fa-arrow-down"></i> LOG <i class="fa fa-arrow-down"></i>
			</div>
			<table id="tableLog" class="table table-bordered table-hover">
				<thead style="">
					<tr>
						<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Material</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Qty</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Tanggal</th>
					</tr>
				</thead>
				<tbody id="tableLogBody">
				</tbody>
			</table>
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
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalInput" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12" style="padding-top: 20px;">
						<div class="row">
							<center>
								<div class="col-md-3">
									<div class="form-group">
										<input style="font-weight: bold; font-size: 20px; width: 100%; text-align: center;" type="text" class="form-control" id="inputMaterial" disabled>
									</div>
								</div>
								<div class="col-md-9" style="padding-left: 0;">
									<div class="form-group">
										<input style="font-weight: bold; font-size: 20px; width: 100%; text-align: center;" type="text" class="form-control" id="inputDescription" disabled>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input style="font-weight: bold; font-size: 20px; width: 100%; text-align: center;" type="text" class="form-control" id="inputLocation" disabled>
									</div>
								</div>
								<div class="col-md-3" style="padding-left: 0;">
									<div class="form-group">
										<input style="font-weight: bold; font-size: 20px; width: 100%; text-align: center;" type="text" class="form-control" id="inputEmployeeId" disabled>
									</div>
								</div>
								<div class="col-md-6" style="padding-left: 0;">
									<div class="form-group">
										<input style="width: 100%; text-align: center;" type="text" class="form-control" id="inputEmployeeName" disabled>
									</div>
								</div>
								<div class="col-xs-12">
									<span style="font-weight: bold; font-size: 30px;">QUANTITY:</span>
								</div>
								<div class="col-xs-4">
									<button class="btn btn-danger" style="font-weight: bold; font-size: 1.3vw; width: 100%; height: 60px;" onclick="inputMaterial('minus', 'REPAIR')">KELUAR REPAIR&nbsp;&nbsp;&nbsp;<i class="fa fa-minus-square"></i></button>

									<button class="btn btn-danger" style="font-weight: bold; color: yellow; font-size: 1.3vw; width: 100%; height: 60px; margin-top: 10px;" onclick="inputMaterial('minus', 'SCRAP')">KELUAR SCRAP&nbsp;&nbsp;&nbsp;<i class="fa fa-minus-square"></i></button>
								</div>
								<div class="col-xs-4">
									<div class="form-group">
										<input style="font-weight: bold; font-size: 30px; width: 100%; height: 60px; text-align: center;" type="text" class="numpad form-control" id="inputQuantity">
									</div>
								</div>
								<div class="col-xs-4">
									<button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 100%; height: 60px;" onclick="inputMaterial('plus', '')">MASUK&nbsp;&nbsp;&nbsp;<i class="fa fa-plus-square"></i></button>
								</div>
							</center>
						</div>
					</div>
					<div class="col-md-12" style="margin-top: 20px;">
						<button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 100%;">BATAL</button>
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
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#employee_id').val("");
		$('#employee_name').val("");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').val("");
			$('#operator').focus();
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		fetchTable();
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = <?php echo json_encode($employees); ?>;
	var materials = <?php echo json_encode($materials); ?>;
	var logs = <?php echo json_encode($logs); ?>;

	function inputMaterial(cat, remark){
		if(confirm("Apakah anda yakin akan melakukan input material ini?")){
			$('#loading').show();
			var material_number = $('#inputMaterial').val();
			var material_description = $('#inputDescription').val();
			var location = $('#inputLocation').val();
			var employee_id = $('#inputEmployeeId').val();
			var employee_name = $('#inputEmployeeName').val();
			var quantity = $('#inputQuantity').val();

			if(quantity <= 0){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Isikan quantity material terlebih dahulu');
				return false;
			}

			var text = 'ditambahkan ke';
			if(cat == 'minus'){
				quantity = quantity*-1;
				text = 'dikeluarkan dari';
			}

			var data = {
				material_number:material_number,
				material_description:material_description,
				location:location,
				employee_id:employee_id,
				employee_name:employee_name,
				quantity:quantity,
				text:text,
				remark:remark,
			}

			$.post('{{ url("input/transaction/repair_room") }}', data, function(result, status, xhr){
				if(result.status){
					var tableBody = "";
					var color = "";
					if(result.quantity > 0){
						color = "color: #00a65a;";
					}
					else{
						color = "color: #dd4b39;";				
					}
					tableBody += '<tr>';
					tableBody += '<td style="width: 2%; text-align: left;"><b>'+result.material_number+' ('+result.remark+')</b><br>'+result.material_description+'</td>';
					tableBody += '<td style="width: 1%; text-align: center; font-weight: bold; font-size: 20px; '+color+'">'+result.quantity+'</td>';
					tableBody += '<td style="width: 0.1%; text-align: center;">'+result.created_at+'</td>';
					tableBody += '</tr>';
					$('#tableLog').prepend(tableBody);
					$('#modalInput').modal('hide');
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;				
				}
			});
		}
		else{
			return false;
		}
	}

	function modalInput(material_number, material_description){
		$('#inputMaterial').val(material_number);
		$('#inputDescription').val(material_description);
		$('#inputLocation').val($('#location').val());
		$('#inputEmployeeId').val($('#employee_id').val());
		$('#inputEmployeeName').val($('#employee_name').val());
		$('#inputQuantity').val("");

		$('#modalInput').modal('show');
	}

	function fetchTable(){
		$('#tableClarinet').DataTable().clear();
		$('#tableClarinet').DataTable().destroy();
		$('#tableFlute').DataTable().clear();
		$('#tableFlute').DataTable().destroy();
		$('#tableSaxophone').DataTable().clear();
		$('#tableSaxophone').DataTable().destroy();
		$('#tableLog').DataTable().clear();
		$('#tableLog').DataTable().destroy();
		$('#tableClarinetBody').html("");
		$('#tableFluteBody').html("");
		$('#tableSaxophoneBody').html("");
		$('#tableLogBody').html("");
		var tableBody = "";
		var color = "";

		$.each(materials, function(key, value){
			var material_description = value.material_description.replace("'", '');
			tableBody = "";
			tableBody += '<tr>';
			tableBody += '<td style="width: 1%; text-align: left;"><b>'+value.material_number+'</b><br>'+value.material_description+'</td>';
			tableBody += '<td style="width: 0.1%; text-align: center;"><button class="btn btn-success btn-md" style="margin-top: 5px;" onclick="modalInput(\''+value.material_number+'\',\''+material_description+'\')">Pilih</button></td>';
			tableBody += '</tr>';
			if(value.storage_location.indexOf("CL") >= 0){
				$('#tableClarinetBody').append(tableBody);
			}
			if(value.storage_location.indexOf("FL") >= 0){
				$('#tableFluteBody').append(tableBody);
			}
			if(value.storage_location.indexOf("SX") >= 0 || value.storage_location.indexOf("VN") >= 0){
				$('#tableSaxophoneBody').append(tableBody);
			}
		});

		$.each(logs, function(key, value){
			color = "";
			tableBody = "";
			if(value.quantity > 0){
				color = "color: #00a65a";
			}
			else{
				color = "color: #dd4b39";				
			}
			tableBody += '<tr>';
			tableBody += '<td style="width: 1%; text-align: left;"><b>'+value.material_number+'</b><br>'+value.material_description+'</td>';
			tableBody += '<td style="width: 0.1%; text-align: center; font-weight: bold; font-size: 20px; '+color+'">'+value.quantity+'</td>';
			tableBody += '<td style="width: 0.1%; text-align: center;">'+value.created_at+'</td>';
			tableBody += '</tr>';
			$('#tableLogBody').append(tableBody);
		});

		$('#tableClarinet').DataTable({
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
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "simple_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});

		$('#tableFlute').DataTable({
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
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "simple_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});

		$('#tableSaxophone').DataTable({
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
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "simple_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});

		$('#tableLog').DataTable({
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
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "simple_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
	}

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 9 && $("#operator").val().length <= 10){

				var employee_id = "";
				var employee_name = "";

				$.each(employees, function(key, value){
					if($("#operator").val().length == 9 && value.employee_id == $("#operator").val()){
						employee_id = value.employee_id;
						employee_name = value.name;
					}
					if($("#operator").val().length == 10 && value.tag == $("#operator").val()){
						employee_id = value.employee_id;
						employee_name = value.name;
					}
				});

				if(employee_id != ""){
					$('#modalOperator').modal('hide');
					$('#employee_id').val(employee_id);
					$('#employee_name').val(employee_name);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', 'Data karyawan tidak ditemukan');
					$('#operator').val('');
					$('#operator').focus();
					return false;
				}
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Data karyawan tidak valid');
				$('#operator').val('');
				$('#operator').focus();
				return false;
			}			
		}
	});

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
@stop