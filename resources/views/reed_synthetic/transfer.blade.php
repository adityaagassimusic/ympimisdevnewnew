@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
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
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 2vw;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: rgb(252, 248, 227);
		font-weight: bold;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
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

	<input type="hidden" id="employee_id">

	<div class="row">
		<div class="col-xs-offset-3 col-xs-6" style="color: white;">
			<div class="logopanel">
				<center>
					<h1>
						<span style="color: #d2322d;">[</span> REED SYNTHETIC TRANSACTION <span style="color: #d2322d;">]</span>
					</h1>
					<h4>
						Intergreted with <span style="color: #1caf9a;">KITTO</span>
					</h4>
					<br>
					<br>
				</center>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-4 col-xs-offset-4">
			<div class="col-xs-12" style="padding: 0px;">
				<div class="box">
					<div class="box-body">
						<div style="background-color: #d2322d; text-align: center;">
							<span style="font-weight: bold; color: white; font-size: 2vw;">TRANSFER</span><br>
						</div>
						<div class="col-xs-12" style="margin-top: 3%; padding: 5%;">
							<input type="text" style="text-align: center; width: 100%; font-size: 2.5vw;" id="kanban" placeholder="Scan Kanban">
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
				<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
					TRANSFER REED SYNTHETIC
				</h4>
			</div>
			<div class="modal-body table-responsive">
				<div class="form-group">
					<label for="exampleInputEmail1">Employee ID</label>
					<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
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

		$('#operator').val('');

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


	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length == 9){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/reed/operator") }}', data, function(result, status, xhr){
					if(result.status){
						$('#employee_id').val(result.employee.employee_id);

						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');

						$('#kanban').val('');
						$('#kanban').focus();
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


	$('#kanban').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			transfer($('#kanban').val());		
		}
	});

	function transfer(kanban) {

		var data = {
			employee_id : $('#employee_id').val(),
			kanban : kanban
		}

		$('#loading').show();
		$.post('{{ url("fetch/reed/transfer") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#kanban').val('');
				$('#kanban').focus();
				$('#loading').hide();
			}else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$('#kanban').val('');
				$('#kanban').focus();
				$('#loading').hide();
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

