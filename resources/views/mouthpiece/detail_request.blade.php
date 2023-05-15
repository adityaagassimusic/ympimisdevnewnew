@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.status {
		font-size: 3vw;
		font-weight: bold;
		text-transform: uppercase;
	}

	#tableDetail>tbody>tr:hover {
		background-color: #7dfa8c !important;
	}

	tbody>tr>td {
		padding: 10px 5px 10px 5px;
	}

	table.table-bordered {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>thead>tr>th {
		border: 1px solid black;
		vertical-align: middle;
	}

	table.table-bordered>tbody>tr>td {
		border: 1px solid black;
		background-color: aliceblue;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
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
	<div class="col-xs-6 col-md-offset-3" id="detail_request">
		<div class="row" style="margin:0px;">
			<div class="box box-primary">
				<div class="box-body">
					<center>
						<h3 style="background-color: #ffd8b7; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 1%; color: black; border: 1px solid black; border-radius: 5px;">
							<i class="fa fa-angle-double-down"></i> DETAIL MATERIAL <i class="fa fa-angle-double-down"></i>
						</h3>
					</center>
					<div class="row">
						<div class="col-md-12" align="center">
							<div class="row">
								<div class="col-xs-6">
									<input type="hidden" id="request_id">
									<label style="font-weight: bold; font-size: 25px">GMC : </label>
									<input type="text" id="gmc" style="width: 100%; font-size: 30px; text-align: center" disabled>
								</div>
								<div class="col-xs-6">
									<label style="font-weight: bold; font-size: 25px;">To Location : </label>
									<input type="text" id="loc" style="width: 100%; font-size: 30px; text-align: center" disabled>
								</div>
								<div class="col-xs-12">
									<label style="font-weight: bold; font-size: 25px;">Material Description : </label>
									<input type="text" id="desc" style="width: 100%; font-size:30px; text-align: center" disabled>
								</div>

								<div class="col-xs-12">
									<div class="row">
										<div class="col-xs-3">
											<label style="font-weight: bold; font-size: 25px;">UOM : </label>
											<input type="text" id="uom" style="width: 100%; font-size: 30px; text-align: center" disabled value="">
											<input type="hidden" id="mrpc">
										</div>

										<div class="col-xs-3">
											<label style="font-weight: bold; font-size: 25px; color: red;">QTY Request : </label>
											<input type="text" id="quantity" style="width: 100%; font-size: 30px; text-align: center" disabled>
										</div>

										<div class="col-xs-6">
											<label style="font-weight: bold; font-size: 25px; color: red;">PIC : </label>
											<input type="text" id="pic" style="width: 100%; font-size: 30px; text-align: center" onchange="CekPICPengambilan(this.value)">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalConfirmation">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<div style="background-color: #00c0ef;">
							<center>
								<h3>Scan Print Slip Request</h3>
							</center>
						</div>
						<label>Scan Slip Request</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="slip_request" value="{{$req_id}}" onchange="ScanSlip(this.value)" placeholder="Scan In Here" required>
						<br><br>
						<a href="{{ url("/index/mouthpiece_process") }}" class="btn btn-warning" style="width: 100%; font-size: 1vw; font-weight: bold;"><i class="fa fa-hand-o-right"></i> Ke Halaman Utama</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bd-example-modal-lg" id="ModalNotification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="alrm-notif">
					<h4 style="padding-left: 10px; padding-top: 10px" id="title-notif"></h4>
					<p style="padding-left: 10px; padding-top: 10px">Berikut adalah list permintaan Mouthpiece dari Assemby :<br>
						<label style="color: black">(Klik tombol konfirmasi untuk membuka detail.)</label>
					</p>
				</div>   
			</div>
			<div class="modal-footer">
				<table class="table table-bordered table-stripped" id="tableListRequest" style="width: 100%;" align="center">
					<thead style="background-color: rgb(126,86,134); color: #FFD700;">
						<tr>
							<th style="width: 5%;">#</th>
							<th style="width: 15%;">GMC</th>
							<th style="width: 30%;">MATERIAL DESCRIPTION</th>
							<th style="width: 10%;">TO LOCATION</th>
							<th style="width: 10%;">QTY</th>
							<th style="width: 15%;">REQUEST BY</th>
							<th style="width: 15%;">#</th>
						</tr>                   
					</thead>
					<tbody id="tableBodyListRequest" style="background-color: white">
					</tbody>
				</table>
				<div class="col-xs-12">
					<div class="col-xs-4 pull-left">
						<button type="button" class="btn btn-warning" data-dismiss="modal" style="font-weight: bold; font-size: 1vw; width: 100%;"> <i class="fa fa-times" aria-hidden="true"></i> CLOSE</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_clock_lobby = new Audio('{{ url("sounds/railway_lobby.mp3") }}');
	var i = 0;

	var req_id = <?php echo json_encode($req_id); ?>;

	jQuery(document).ready(function() {
		// clearAll();
		// $('#finishPicking').prop('disabled', true);
		// $('#modalConfirmation').modal({
		// 	backdrop: 'static',
		// 	keyboard: false
		// });

		// $('#modalConfirmation').on('shown.bs.modal', function () {
		// 	$('#slip_request').focus();
		// });

		// $('#detail_request').show();
		ScanSlip();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/kd_mouthpiece/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);
						$('#data_op').text(" ("+result.employee.employee_id+" - "+result.employee.name+")")
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator').remove();
						$('#qr_checksheet').val('');
						$('#qr_item').val('');
						$('#qr_checksheet').focus();
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

	function ScanSlip(slip){
		$('#loading').show();
		var data = {
			slip:req_id
		}
		$.post('{{ url("scan/slip/request/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				var emp = result.data.created_by.split("/");
				openSuccessGritter('Success!', 'Periksa dan siapkan request berikut.');
				$('#slip_request').val('');
				$('#modalConfirmation').modal('hide');
				$('#detail_request').show();
				$('#request_id').val(result.data.request_id);
				$('#gmc').val(result.data.gmc);
				$('#loc').val(result.data.issue);
				$('#desc').val(result.data.desc);
				$('#uom').val(result.data.uom);
				$('#quantity').val(result.data.qty);
				$('#pic').focus();
			}
			else{
				openErrorGritter('Error', result.message);
				$('#slip_request').val('');
				$('#slip_request').focus();
			}
		});
	}

	function CekPICPengambilan(tag){
		var data = {
			tag:tag,
			request_id:$('#request_id').val()
		}
		$.post('{{ url("scan/id_card/request/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				if(confirm("PIC pengambil material sesuai, item ini otomatis ter GMS.")){
					TransferGMS($('#request_id').val());
				}
				else{
					return false;
				}
			}
			else{
				openErrorGritter('Error', result.message);
				$('#pic').val('');
				$('#pic').focus();
			}
		});
	}

	function TransferGMS(request_id) {
		var data = {
			value:request_id
		}
		$.post('{{ url("update/request/mouthpiece") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Material berhasil di GMS');
				window.close();
			}else{
				openErrorGritter('Error', 'Material gagal di GMS');
			}
		});
	}

</script>
@endsection

