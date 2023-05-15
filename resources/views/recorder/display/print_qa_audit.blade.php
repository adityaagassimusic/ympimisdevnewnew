@extends('layouts.master')
@section('header')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    <!-- <small>it all starts here</small> -->
    @if($type == 'assy')
	    @if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Chief'))
		    @if($audit->car_approver_id_rc == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/recorder/qa_audit/assy/chief/'.$id)}}" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-warning pull-right" onclick="openModalEdit('assy')" style="font-weight:bold;margin-right: 10px">Edit</button>
		    <button class="btn btn-danger pull-right" onclick="openModalReasonChief('assy')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
    @else
    	@if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Chief'))
		    @if($audit->car_approver_id_inj == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/recorder/qa_audit/injeksi/chief/'.$id)}}" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-warning pull-right" onclick="openModalEdit('injeksi')" style="font-weight:bold;margin-right: 10px">Edit</button>
		    <button class="btn btn-danger pull-right" onclick="openModalReasonChief('injeksi')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
	@endif

    @if($type == 'assy')
	    @if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Manager'))
		    @if($audit->car_manager_id_rc == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/recorder/qa_audit/assy/manager/'.$id)}}" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-danger pull-right" onclick="openModalReason('assy')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
    @else
    	@if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Manager'))
		    @if($audit->car_manager_id_inj == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/recorder/qa_audit/injeksi/manager/'.$id)}}" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-danger pull-right" onclick="openModalReason('injeksi')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
    @endif
    <button class="btn btn-primary pull-right" style="margin-right: 10px" onclick="myFunction()">Print</button>
    <br>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
<style type="text/css">
	@media print {
	.table {-webkit-print-color-adjust: exact;}
	}

		table tr td,
		table tr th{
			font-size: 12pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}
		.centera{
			text-align: center;
			vertical-align: middle !important;
		}
		.square {
			height: 5px;
			width: 5px;
			border: 1px solid black;
			background-color: transparent;
		}
		table {
			page-break-inside: avoid;
		}
		#loading, #error { display: none; }
</style>
@endsection
@section('content')
<section class="content">
@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
@endif
@if (session('error'))
	<div class="alert alert-warning alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4> Warning!</h4>
		{{ session('error') }}
	</div>   
@endif
<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary" style="overflow-x: scroll;">
      <div class="box-body" >
      	<table class="table" style="border: 1px solid black;">
		<thead>
			<tr>
				<td rowspan="4" colspan="" class="centera" style="width: 6%">
					<!-- <img width="80px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""> -->
					<img width="200px" src="{{ asset('waves.jpg') }}" alt="">
				</td>
				<td rowspan="4" colspan="2" class="centera" style="font-size: 25px;font-weight: bold;width: 10%">CORRECTIVE ACTION REPORT {{strtoupper($type)}}</td>
				<td class="centera" width="10%" style="width: 4%">Approved By</td>
				<td class="centera" width="10%" style="width: 4%">Approved By</td>
			</tr>
			<tr>
				<td class="centera" style="color: green;font-weight: bold;">
					@if($type == 'assy')
						@if($audit->car_manager_id_rc != null)
							Approved<br>
							{{explode(' ',$audit->car_approved_at_manager_rc)[0]}}<br>{{explode(' ',$audit->car_approved_at_manager_rc)[1]}}
						@else
						<br>
						<br>
						@endif
					@else
						@if($audit->car_manager_id_inj != null)
							Approved<br>
							{{explode(' ',$audit->car_approved_at_manager_inj)[0]}}<br>{{explode(' ',$audit->car_approved_at_manager_inj)[1]}}
						@else
						<br>
						<br>
						@endif
					@endif
				</td>
				<td class="centera" style="color: green;font-weight: bold;">
					@if($type == 'assy')
						@if($audit->car_approver_id_rc != null)
							Approved<br>
							{{explode(' ',$audit->car_approved_at_rc)[0]}}<br>{{explode(' ',$audit->car_approved_at_rc)[1]}}
						@else
						<br>
						<br>
						@endif
					@else
						@if($audit->car_approver_id_inj != null)
							Approved<br>
							{{explode(' ',$audit->car_approved_at_inj)[0]}}<br>{{explode(' ',$audit->car_approved_at_inj)[1]}}
						@else
						<br>
						<br>
						@endif
					@endif
				</td>
			</tr>
			<tr>
				<td class="centera">@if($type == 'assy')
				{{$audit->car_manager_name_rc}}
				@else
				{{$audit->car_manager_name_inj}}
				@endif</td>
				<td class="centera">@if($type == 'assy')
				{{$audit->car_approver_name_rc}}
				@else
				{{$audit->car_approver_name_inj}}
				@endif</td>
			</tr>
			<tr>
				<td class="centera">Manager</td>
				<td class="centera">Chief</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="font-weight: bold;border-top: 2px solid black !important">
					Product
				</td>
				<td style="font-weight: bold;border-top: 2px solid black !important">
					Date
				</td>
				<td style="font-weight: bold;border-top: 2px solid black !important">
					Auditor
				</td>
				<td style="font-weight: bold;border-top: 2px solid black !important">
					PIC Kensa Kakunin Assy
				</td>
				<td style="font-weight: bold;border-top: 2px solid black !important">
					PIC Injeksi
				</td>
			</tr>
			<tr>
				<td>
					{{$audit->product}} - {{$audit->part_name}}
				</td>
				<td>
					{{date('d M Y',strtotime($audit->date))}}
				</td>
				<td>
					{{$audit->auditor}}
				</td>
				<td>
					{{$audit->auditee}}
				</td>
				<td>
					{{$audit->pic_injection}}
				</td>
			</tr>
			<tr>
				<td rowspan="3">
					<b>Evidence</b><br>
					<img style="width: 200px" src="data:image/png;base64,{{base64_encode(file_get_contents('http://10.109.52.4/mirai/public/data_file/recorder/qa_audit/'.$audit->image))}}" alt="">
				</td>
				<td colspan="5" style="vertical-align: middle;">
					<b>Defect</b>
					<br>
					{{$audit->defect}}
				</td>
			</tr>
			<tr>
				<td colspan="5" style="vertical-align: middle;">
					<b>Area</b>
					<br>
					{{$audit->area}}
				</td>
			</tr>
			<tr>
				<td colspan="5" style="vertical-align: middle;">
					<b>Category</b>
					<br>
					{{$audit->category}}
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">Description</b><br>
					@if($type == 'assy')
					<?php echo $audit->car_description_rc ?>
					@else
					<?php echo $audit->car_description_inj ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">A. Immediately Action</b><br>
					@if($type == 'assy')
					<?php echo $audit->car_action_now_rc ?>
					@else
					<?php echo $audit->car_action_now_inj ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">B. Possibility Cause</b><br>
					@if($type == 'assy')
					<?php echo $audit->car_cause_rc ?>
					@else
					<?php echo $audit->car_cause_inj ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">C. Corrective Action</b><br>
					@if($type == 'assy')
					<?php echo $audit->car_action_rc ?>
					@else
					<?php echo $audit->car_action_inj ?>
					@endif
				</td>
			</tr>
		</tbody>
		</table>
	</div>
  </div>

  <div class="modal fade" id="modalReason">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #ff9e9e; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						REJECT CAR BY MANAGER
					</h4>
				</div>
				<form action="{{url('reject/recorder/qa_audit/'.$id)}}" method="post">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Reason</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<textarea id="reason" name="reason">
										
									</textarea>
									<input type="hidden" name="type" id="type">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button type="submit" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalReasonChief">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #ff9e9e; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						REJECT CAR BY CHIEF
					</h4>
				</div>
				<form action="{{url('reject_chief/recorder/qa_audit/'.$id)}}" method="post">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body table-responsive">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<center><span style="font-weight: bold; font-size: 18px;">Reason</span></center>
								</div>
								<div class="col-xs-12" style="padding-top: 10px">
									<textarea id="reason_chief" name="reason_chief">
										
									</textarea>
									<input type="hidden" name="type_chief" id="type_chief">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button type="submit" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
										CONFIRM
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCouncelingInjeksi">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #fcba03;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling">TRAINING DAN KONSELING INJEKSI</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">

			            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
			            	<span style="padding: 20px;font-size: 20px">EDIT CAR</span>
			            </div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Deskripsi Injection</label><br>
									<textarea id="car_description_inj" style="width: 100%"><?php echo $audit->car_description_inj ?></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action Injection</label><br>
									<textarea id="car_action_now_inj" style="width: 100%"><?php echo $audit->car_action_now_inj ?></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause Injection</label><br>
									<textarea id="car_cause_inj" style="width: 100%"><?php echo $audit->car_cause_inj ?></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action Injection</label><br>
									<textarea id="car_action_inj" style="width: 100%"><?php echo $audit->car_action_inj ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 30px" onclick="submitCouncel('injeksi')">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalCouncelingAssy">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #03adfc;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling">TRAINING DAN KONSELING ASSY</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
			            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
			            	<span style="padding: 20px;font-size: 20px">EDIT CAR</span>
			            </div>
			            <div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Deskripsi Assy</label><br>
									<textarea id="car_description_rc" style="width: 100%"><?php echo $audit->car_description_rc ?></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action Assy</label><br>
									<textarea id="car_action_now_rc" style="width: 100%"><?php echo $audit->car_action_now_rc ?></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause Assy</label><br>
									<textarea id="car_cause_rc" style="width: 100%"><?php echo $audit->car_cause_rc ?></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action Assy</label><br>
									<textarea id="car_action_rc" style="width: 100%"><?php echo $audit->car_action_rc ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 30px" onclick="submitCouncel('assy')">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
  @endsection
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		CKEDITOR.replace('reason' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('reason_chief' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_description_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_rc' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('car_description_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_inj' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    // $("#inputaction").html(CKEDITOR.instances.inputaction.setData(''));
	});
    function myFunction() {
	  window.print();
	}

	function openModalReason(param) {
		$('#modalReason').modal('show');
		$("#type").val(param);
		$("#reason").html(CKEDITOR.instances.reason.setData(''));
	}

	function openModalReasonChief(param) {
		$('#modalReasonChief').modal('show');
		$("#type_chief").val(param);
		$("#reason_chief").html(CKEDITOR.instances.reason.setData(''));
	}

	function openModalEdit(param) {
		if (param === 'assy') {
			$('#modalCouncelingAssy').modal('show');
		}else{
			$('#modalCouncelingInjeksi').modal('show');
		}
	}

	function submitCouncel(param) {
		$('#loading').show();
		if (param === 'injeksi') {
			if (CKEDITOR.instances.car_description_inj.getData() == ""
				|| CKEDITOR.instances.car_action_now_inj.getData() == ""
				|| CKEDITOR.instances.car_cause_inj.getData() == ""
				|| CKEDITOR.instances.car_action_inj.getData() == "") {
				$('#loading').hide();
				alert('Error! Semua Harus Diisi');
				return false;
			}
			var id_audit = '{{$id}}';

			var description_inj = CKEDITOR.instances.car_description_inj.getData();
			var action_now_inj = CKEDITOR.instances.car_action_now_inj.getData();
			var cause_inj = CKEDITOR.instances.car_cause_inj.getData();
			var action_inj = CKEDITOR.instances.car_action_inj.getData();

			var data = {
				id_audit: id_audit,
				description_inj:description_inj,
				action_now_inj:action_now_inj,
				cause_inj:cause_inj,
				action_inj:action_inj,
				param:param,
			}
		}else{
			if (CKEDITOR.instances.car_description_rc.getData() == ""
				|| CKEDITOR.instances.car_action_now_rc.getData() == ""
				|| CKEDITOR.instances.car_cause_rc.getData() == ""
				|| CKEDITOR.instances.car_action_rc.getData() == "") {
				$('#loading').hide();
				alert('Error! Semua Harus Diisi');
				return false;
			}
			var id_audit = '{{$id}}';

			var description_rc = CKEDITOR.instances.car_description_rc.getData();
			var action_now_rc = CKEDITOR.instances.car_action_now_rc.getData();
			var cause_rc = CKEDITOR.instances.car_cause_rc.getData();
			var action_rc = CKEDITOR.instances.car_action_rc.getData();

			var data = {
				id_audit: id_audit,
				description_rc:description_rc,
				action_now_rc:action_now_rc,
				cause_rc:cause_rc,
				action_rc:action_rc,
				param:param,
			}
		}

		$.post('{{ url("update/recorder/counceling") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();
				location.reload();
			}else{
				$('#loading').hide();
				alert(result.message);
			}
		})
	}


	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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
