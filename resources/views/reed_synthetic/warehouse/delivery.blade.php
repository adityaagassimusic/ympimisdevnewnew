@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	tbody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1vw;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
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
		<input type="hidden" id="location" value="warehouse">
		<input type="hidden" id="proses" value="delivery">
		<input type="hidden" id="employee_id">
		<input type="hidden" id="material_number">

		<div class="col-xs-6 col-md-offset-3" id="field_kanban">
			<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
				<span style="font-size: 3vw; background-color: rgba(126,86,134,.7); padding-top: 6px;">
					&nbsp;
					<i class="glyphicon glyphicon-qrcode"></i>
					&nbsp;
				</span>
			</div>
			<input type="text" style="text-align: center; font-size: 3vw; height: 100px;" class="form-control" id="kanban" placeholder="Scan Kanban Permintaan">	
		</div>


		<div class="col-xs-12" id="picking">
			<div class="col-xs-3">
				<center>
					<div class="box">
						<div class="box-body">
							<table id="operatorTable" class="table table-bordered table-stripped">
								<thead style="background-color: #00c0ef;">
									<tr>
										<th style="width: 1%; font-size: 1.5vw;" id="emp_id"></th>
									</tr>
									<tr>
										<th style="width: 1%; font-size: 1.5vw;" id="emp_name"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>

					<button class="btn btn-danger btn-md" onclick="refreshPage()" style="font-size: 2vw; margin-bottom: 5%;">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</button>

					<div class="box">
						<div class="box-body">
							<div class="col-xs-12" style="padding: 0px;">
								<div style="background-color: orange;">
									<span style="font-weight: bold; color: white; font-size: 2vw;">1. SCAN STORE</span><br>
								</div>
								<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_store" placeholder="Scan QR Code">
							</div>
						</div>
					</div>

					<div class="box">
						<div class="box-body">
							<div class="col-xs-12" style="padding: 0px;">
								<div style="background-color: #00c0ef;">
									<span style="font-weight: bold; color: white; font-size: 2vw;">2. SCAN RESIN</span><br>
								</div>
								<input type="text" style="text-align: center; width: 100%; font-size: 2vw;" id="qr_label" placeholder="Scan QR Code">
							</div>
						</div>
					</div>

				</center>
			</div>
			<div class="col-xs-9">
				<div class="box">
					<div class="box-body">
						<div class="col-xs-12" id="label" style="padding: 0px;">
							<table id="labelTable" class="table table-bordered table-stripped" style="margin: 0px;">
								<thead style="background-color: orange;">
									<tr>
										<th style="width: 1%; font-size: 1.2vw;">Request Date</th>
										<th style="width: 1%; font-size: 1.2vw;">Material</th>
										<th style="width: 6%; font-size: 1.2vw;">Deskripsi</th>
										<th style="width: 1%; font-size: 1.2vw;">Quantity</th>
										<th style="width: 1%; font-size: 1.2vw;">Quantity Bag</th>
										<th style="width: 1%; font-size: 1.2vw;">Actual Bag</th>
										<th style="width: 1%; font-size: 1.2vw;">Diff</th>
									</tr>
								</thead>
								<tbody id="labelBody" style="background-color: white;">
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
				<h3 style="background-color: rgba(126,86,134,.7); text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
					RESIN DELIVERY
				</h3>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearAll();
		// $('#finishSetup').prop('disabled', true);

		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearAll(){
		$('#picking').hide();

		$('#employee_id').val('');
		$('#order_id').val('');
		$('#operator').val('');
		$('#qr_item').val('');
		$('#kanban').val('');
	}

	function refreshPage() {
		location.reload();
	}


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


						$('#kanban').val('');
						$('#kanban').focus();

					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});

			}else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}
		}
	});


	$('#kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#kanban").val().length >= 11){
				selectChecksheet($("#kanban").val());
			}
			else{
				openErrorGritter('Error!', 'Kanban tidak valid.');
				audio_error.play();
				$("#kanban").val("");
			}			
		}
	});


	function selectChecksheet(id){
		$('#loading').show();

		var location = $('#location').val();
		var proses = $('#proses').val();

		var data = {
			kanban : id, 
			location : location,
			proses : proses 
		}

		$.get('{{ url("scan/reed/warehouse_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				$('#field_kanban').hide();

				$('#picking').show();
				$('#labelBody').html("");

				var pickingData = "";

				var total_quantity = 0;
				var total_actual = 0;

				$('#material_number').val(result.data.material_number);

				pickingData += '<tr>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.request_at+'</td>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.material_number+'</td>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.material_description+'</td>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.quantity+'</td>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.bag_quantity+'</td>';
				pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px;">'+result.data.bag_delivered+'</td>';


				if(result.data.bag_quantity != result.data.bag_delivered){
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(255,204,255);">-</td>';
				}
				else{
					pickingData += '<td style="font-size: 1.8vw; height:2%; vertical-align:middle; height:40px; background-color: rgb(204,255,255);">OK</td>';						
				}
				pickingData += '</tr>';

				total_quantity += result.data.quantity;
				total_actual += result.data.actual_quantity;

				// if(total_quantity == total_actual){
				// 	$('#finishSetup').prop('disabled', false);
				// }

				$('#labelBody').append(pickingData);
				$('#loading').hide();
			}
			else{
				$('#kanban').val("");
				$('#kanban').focus();


				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}	

	$('#qr_store').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#qr_store").val() == $("#material_number").val()){

				$('#qr_label').val('');
				$('#qr_label').focus();
			}else{
				openErrorGritter('Error!', 'Store Salah');
				audio_error.play();
				$('#qr_store').val('');
				$('#qr_store').focus();
			}			
		}
	});

	$('#qr_label').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			updateDiff();
		}
	});

	function updateDiff() {
		$('#loading').show();
		var label = $("#qr_label").val();
		var kanban = $("#kanban").val();
		var employee_id = $("#employee_id").val();
		
		var data = {
			label : label,
			kanban : kanban,
			employee_id : employee_id
		}

		$.post('{{ url("update/reed/warehouse_delivery") }}', data, function(result, status, xhr){
			if(result.status){
				selectChecksheet(kanban)
				$('#loading').hide();
				$("#qr_store").val('');
				$("#qr_label").val('');

				audio_ok.play();
				openSuccessGritter('Success', result.message);

			}else{
				$('#loading').hide();
				$("#qr_label").val('');

				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
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

