@extends('layouts.display')

@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.table > tbody > tr:hover {
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(50,50,50);
		padding: 8px;
		vertical-align: middle;
		/*height: 40px;*/
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(50,50,50);
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Visitor Control</span>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			<br>
			<br>
			<a href="{{ url('visitor_registration') }}" class="btn btn-lg btn-success btn-block" style="font-size: 80px; padding: 0; font-weight: bold;">MASUK</a>	
			<a href="{{ url('visitor_list') }}" class="btn btn-lg btn-danger btn-block" style="font-size: 80px; padding: 0; font-weight: bold;margin-top: 30px">KELUAR</a>
			<a href="{{ url('index/human_resource/leave_request/security') }}" class="btn btn-lg btn-warning btn-block" style="font-size: 60px; padding-top: 20px;padding-bottom: 20px; font-weight: bold;margin-top: 30px">IZIN MENINGGALKAN PABRIK</a>
			<button class="btn btn-lg btn-info btn-block" style="font-size: 60px; padding-top: 20px; padding-bottom: 20px; font-weight: bold; margin-top: 30px; white-space: normal;" onclick="modalForm();">IZIN MEMBAWA LAPTOP PERUSAHAAN</button>
			<!-- <a href="{{ url("visitor_getvisitSc") }}" class="btn btn-lg btn-warning btn-block" style="font-size: 80px; padding: 0; font-weight: bold;">KONFIRMASI</a> -->
		</div>
	</div>
</section>

<div class="modal fade" id="modalForm">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body" style="min-height: 100px;">
					<div class="form-group">
						<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Pilih Security<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="selectSecurity" placeholder="Pilih Security">
								<option value=""></option>
								@foreach($employee_syncs as $employee_sync)
								@if($employee_sync->group == 'Security Group')
								<option value="{{ $employee_sync->employee_id }}_{{ $employee_sync->name }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
								@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-xs-12" style="padding-top: 15px;">
						<table id="tableForm" class="table table-bordered table-hover table-striped">
							<thead style="">
								<tr>
									<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Form ID</th>
									<th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
									<th style="width: 1.5%; text-align: left; background-color: #605ca8; color: white;">Employee</th>
									<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>Security</th>
								</tr>
							</thead>
							<tbody id="tableFormBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$(function () {
		$('#selectSecurity').select2({
			dropdownParent: $('#modalForm')
		});
	});

	function modalForm(){
		$('#selectSecurity').prop('selectedIndex', 0).change();
		var data = {

		}
		$.get('{{ url("fetch/mis/form_security") }}', data, function(result, status, xhr){
			if(result.status){
				var tableFormBody = "";
				$('#tableFormBody').html("");
				for (var i = 0; i < result.ticket_forms.length; i++) {
					tableFormBody += '<tr id="'+result.ticket_forms[i].form_id+'">';
					tableFormBody += '<td style="width: 1%; text-align: center;">'+result.ticket_forms[i].form_id+'</td>';
					tableFormBody += '<td style="width: 1%; text-align: center;">'+result.ticket_forms[i].status+'</td>';
					tableFormBody += '<td style="width: 1%; text-align: left;">'+result.ticket_forms[i].employee_id+'<br>'+result.ticket_forms[i].employee_name+'</td>';
					tableFormBody += '<td style="width: 1%; text-align: center;"><button class="btn btn-success" onclick="confirmForm(\''+result.ticket_forms[i].form_id+'\')">Confirm</button></td>';
					tableFormBody += '</tr>';
				}
				$('#tableFormBody').append(tableFormBody);
				$('#modalForm').modal('show');
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				return false;
			}
		});
	}


	function confirmForm(form_id){
		if($('#selectSecurity').val() == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'Pilih sekuriti terlebih dahulu');
			audio_error.play();
			return false;
		}

		var security = $('#selectSecurity').val().split('_');

		var formData = new FormData();
		formData.append('form_id', form_id);
		formData.append('approver', 'Security Member');
		formData.append('security_id', security[0]);
		formData.append('security_name', security[1]);

		$.ajax({
			url:"{{ url('confirm/mis/form') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#'+data.form_id).remove();
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					audio_ok.play();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!', data.message);
					audio_error.play();
					return false;
				}

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