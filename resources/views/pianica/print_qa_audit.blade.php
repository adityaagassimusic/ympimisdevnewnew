@extends('layouts.master')
@section('header')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    <!-- <small>it all starts here</small> --> 
    @if($type == 'initial')
	    @if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Chief'))
		    @if($audit_all[0]->car_approver_id == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/pn/qa_audit/initial/chief/'.$id)}}" onclick="$('#loading').show()" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-warning pull-right" onclick="openModalEdit('initial')" style="font-weight:bold;margin-right: 10px">Edit</button>
		    <button class="btn btn-danger pull-right" onclick="openModalReasonChief('initial')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
    @else
    	@if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Chief'))
		    @if($audit_all[0]->car_approver_id == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/pn/qa_audit/final/chief/'.$id)}}" onclick="$('#loading').show()" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-warning pull-right" onclick="openModalEdit('final')" style="font-weight:bold;margin-right: 10px">Edit</button>
		    <button class="btn btn-danger pull-right" onclick="openModalReasonChief('final')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
	@endif

    @if($type == 'initial')
	    @if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Manager'))
		    @if($audit_all[0]->car_manager_id == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/pn/qa_audit/initial/manager/'.$id)}}" onclick="$('#loading').show()" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-danger pull-right" onclick="openModalReason('initial')" style="font-weight:bold;margin-right: 10px">Reject</button>
	    	@endif
    	@endif
    @else
    	@if(str_contains($emp->role_code,'MIS') || str_contains($emp->position,'Manager'))
		    @if($audit_all[0]->car_manager_id == null)
		    <a class="btn btn-success pull-right" href="{{url('approve/pn/qa_audit/final/manager/'.$id)}}" onclick="$('#loading').show()" style="font-weight:bold;margin-right: 10px">Approve</a>
		    <button class="btn btn-danger pull-right" onclick="openModalReason('final')" style="font-weight:bold;margin-right: 10px">Reject</button>
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
				<td rowspan="4" colspan="2" class="centera" style="font-size: 25px;font-weight: bold;width: 10%">CORRECTIVE ACTION REPORT PIANICA {{strtoupper($type)}}</td>
				<td class="centera" width="10%" style="width: 4%">Approved By</td>
				<td class="centera" width="10%" style="width: 4%">Approved By</td>
			</tr>
			<tr>
				<td class="centera" style="color: green;font-weight: bold;">
					@if($type == 'assy')
						@if($audit_all[0]->car_manager_id != null)
							Approved<br>
							{{explode(' ',$audit_all[0]->car_approved_at_manager)[0]}}<br>{{explode(' ',$audit_all[0]->car_approved_at_manager)[1]}}
						@else
						<br>
						<br>
						@endif
					@else
						@if($audit_all[0]->car_manager_id != null)
							Approved<br>
							{{explode(' ',$audit_all[0]->car_approved_at_manager)[0]}}<br>{{explode(' ',$audit_all[0]->car_approved_at_manager)[1]}}
						@else
						<br>
						<br>
						@endif
					@endif
				</td>
				<td class="centera" style="color: green;font-weight: bold;">
					@if($type == 'assy')
						@if($audit_all[0]->car_approver_id != null)
							Approved<br>
							{{explode(' ',$audit_all[0]->car_approved_at)[0]}}<br>{{explode(' ',$audit_all[0]->car_approved_at)[1]}}
						@else
						<br>
						<br>
						@endif
					@else
						@if($audit_all[0]->car_approver_id != null)
							Approved<br>
							{{explode(' ',$audit_all[0]->car_approved_at)[0]}}<br>{{explode(' ',$audit_all[0]->car_approved_at)[1]}}
						@else
						<br>
						<br>
						@endif
					@endif
				</td>
			</tr>
			<tr>
				<td class="centera">@if($type == 'assy')
				{{$audit_all[0]->car_manager_name}}
				@else
				{{$audit_all[0]->car_manager_name}}
				@endif</td>
				<td class="centera">@if($type == 'assy')
				{{$audit_all[0]->car_approver_name}}
				@else
				{{$audit_all[0]->car_approver_name}}
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
				<td colspan="2" style="font-weight: bold;border-top: 2px solid black !important">
					PIC
				</td>
			</tr>
			<tr>
				<td>
					{{$audit_all[0]->product}}
				</td>
				<td>
					{{date('d M Y',strtotime($audit_all[0]->date))}}
				</td>
				<td>
					{{$audit_all[0]->auditor_id}} - {{$audit_all[0]->auditor_name}}
				</td>
				<td colspan="2">
					@foreach($audit_all as $audit)
					<b>{{$audit->check_type}}</b><br>
					{{$audit->employee_id}} - {{$audit->employee_name}}
					<br>
					@endforeach
				</td>
			</tr>
			<tr>
				<td rowspan="3">
					<b>Evidence</b><br>
					<img style="width: 200px" src="data:image/png;base64,{{base64_encode(file_get_contents('http://10.109.52.4/mirai/public/data_file/pianica/qa_audit/'.$audit_all[0]->image))}}" alt="">
				</td>
				<td colspan="5" style="vertical-align: middle;">
					<b>Defect</b>
					<br>
					{{$audit_all[0]->defect}}
				</td>
			</tr>
			<tr>
				<td colspan="5" style="vertical-align: middle;">
					<b>Area</b>
					<br>
					{{$audit_all[0]->area}}
				</td>
			</tr>
			<tr>
				<td colspan="5" style="vertical-align: middle;">
					<b>Category</b>
					<br>
					{{$audit_all[0]->category}}
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">Description</b><br>
					@if($type == 'assy')
					<?php echo $audit_all[0]->car_description ?>
					@else
					<?php echo $audit_all[0]->car_description ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">A. Immediately Action</b><br>
					@if($type == 'assy')
					<?php echo $audit_all[0]->car_action_now ?>
					@else
					<?php echo $audit_all[0]->car_action_now ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">B. Possibility Cause</b><br>
					@if($type == 'assy')
					<?php echo $audit_all[0]->car_cause ?>
					@else
					<?php echo $audit_all[0]->car_cause ?>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<b style="font-size: 20px">C. Corrective Action</b><br>
					@if($type == 'assy')
					<?php echo $audit_all[0]->car_action ?>
					@else
					<?php echo $audit_all[0]->car_action ?>
					@endif
				</td>
			</tr>
		</tbody>
		</table>
	</div>
  </div>

  <div class="modal fade" id="modalCounceling">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #fcba03;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling">TRAINING DAN KONSELING</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">

			            <div class="col-xs-12" style="text-align: center;background-color: green;color: white;margin-top: 10px;font-weight: bold;">
			            	<span style="padding: 20px;font-size: 20px">EDIT CAR</span>
			            </div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Deskripsi</label><br>
									<textarea id="car_description" style="width: 100%"><?php echo $audit_all[0]->car_description ?></textarea>
								</div>
								<div class="form-group">
									<label>Immediately Action</label><br>
									<textarea id="car_action_now" style="width: 100%"><?php echo $audit_all[0]->car_action_now ?></textarea>
								</div>
								<div class="form-group">
									<label>Possibility Cause</label><br>
									<textarea id="car_cause" style="width: 100%"><?php echo $audit_all[0]->car_cause ?></textarea>
								</div>
								<div class="form-group">
									<label>Corrective Action</label><br>
									<textarea id="car_action" style="width: 100%"><?php echo $audit_all[0]->car_action ?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 30px" onclick="submitCouncel()">Submit</button>
					</div>
				</div>
			</div>
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
				<form action="{{url('reject/pn/qa_audit/'.$id)}}" method="post">
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
									<button type="submit" onclick="$('#loading').show();" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
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
				<form action="{{url('reject_chief/pn/qa_audit/'.$id)}}" method="post">
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
									<button type="submit" onclick="$('#loading').show();" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
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
	    CKEDITOR.replace('car_description' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action_now' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_cause' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('car_action' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	 //    CKEDITOR.replace('car_description' ,{
  //     		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	 //    });
	 //    CKEDITOR.replace('car_action_now' ,{
  //     		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	 //    });
	 //    CKEDITOR.replace('car_cause' ,{
  //     		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	 //    });
	 //    CKEDITOR.replace('car_action' ,{
  //     		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	 //    });

	    // $("#inputaction").html(CKEDITOR.instances.inputaction.setData(''));
	});
    function myFunction() {
	  window.print();
	}

	function openModalEdit(param) {
		$('#modalCounceling').modal('show');
	}

	function openModalReason(param) {
		$('#modalReason').modal('show');
		$('#type').val(param);
		$("#reason").html(CKEDITOR.instances.reason.setData(''));
	}

	function openModalReasonChief(param) {
		$('#modalReasonChief').modal('show');
		$('#type_chief').val(param);
		$("#reason_chief").html(CKEDITOR.instances.reason.setData(''));
	}

	function submitCouncel() {
		$('#loading').show();
		if (CKEDITOR.instances.car_description.getData() == ""
			|| CKEDITOR.instances.car_action_now.getData() == ""
			|| CKEDITOR.instances.car_cause.getData() == ""
			|| CKEDITOR.instances.car_action.getData() == "") {
			$('#loading').hide();
			alert('Error! Semua Harus Diisi');
			return false;
		}
		var id_audit = '{{$id}}';

		var description = CKEDITOR.instances.car_description.getData();
		var action_now = CKEDITOR.instances.car_action_now.getData();
		var cause = CKEDITOR.instances.car_cause.getData();
		var action = CKEDITOR.instances.car_action.getData();

		var data = {
			id_audit: id_audit,
			description:description,
			action_now:action_now,
			cause:cause,
			action:action,
			param:'{{$type}}'
		}

		$.post('{{ url("update/pn/counceling") }}', data, function(result, status, xhr) {
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
