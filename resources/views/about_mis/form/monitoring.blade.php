@extends('layouts.master')
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
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" data-toggle="modal" data-target="#modalSelectForm"><i class="fa fa-edit"></i>&nbsp;&nbsp;Create Form</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div style="background-color: #605ca8; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
				<span style="font-weight: bold; font-size: 30px">IN PROGRESS</span>
			</div>
			<table id="tableProgress" class="table table-bordered table-hover table-striped">
				<thead style="">
					<tr>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Form ID</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Dept.</th>
						<th style="width: 3%; text-align: center; background-color: #605ca8; color: white;">Form</th>
						<th style="width: 1.5%; text-align: left; background-color: #605ca8; color: white;">Employee</th>
						<th style="width: 1%; text-align: center; background-color: #00a65a; color: white;">Submitted By</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>DGM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>GM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Chief MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Director</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>Security</th>
					</tr>
				</thead>
				<tbody id="tableProgressBody">
				</tbody>
			</table>
			<div style="background-color: #00a65a; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
				<span style="font-weight: bold; font-size: 30px">FINISHED</span>
			</div>
			<table id="tableFinished" class="table table-bordered table-hover table-striped">
				<thead style="">
					<tr>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Form ID</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Dept.</th>
						<th style="width: 3%; text-align: center; background-color: #605ca8; color: white;">Form</th>
						<th style="width: 1.5%; text-align: left; background-color: #605ca8; color: white;">Employee</th>
						<th style="width: 1%; text-align: center; background-color: #00a65a; color: white;">Submitted By</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>DGM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>GM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Chief MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Director</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>Security</th>
					</tr>
				</thead>
				<tbody id="tableFinishedBody">
				</tbody>
			</table>
			<div style="background-color: black; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
				<span style="font-weight: bold; font-size: 30px">REJECTED</span>
			</div>
			<table id="tableRejected" class="table table-bordered table-hover table-striped">
				<thead style="">
					<tr>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Form ID</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
						<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Dept.</th>
						<th style="width: 3%; text-align: center; background-color: #605ca8; color: white;">Form</th>
						<th style="width: 1.5%; text-align: left; background-color: #605ca8; color: white;">Employee</th>
						<th style="width: 1%; text-align: center; background-color: #00a65a; color: white;">Submitted By</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>DGM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>GM</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Chief MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Manager MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Approved By<br>Director</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>MIS</th>
						<th style="width: 1.5%; text-align: center; background-color: #00a65a; color: white;">Confirmed By<br>Security</th>
					</tr>
				</thead>
				<tbody id="tableRejectedBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalSelectForm">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body" style="min-height: 100px;">
					<div class="col-md-12">
						<table id="tableFinished" class="table table-bordered table-hover table-striped">
							<thead style="">
								<tr>
									<th style="width: 0.1%; text-align: center;">#</th>
									<th style="width: 10%;">Nama Form</th>
									<th style="width: 1%; text-align: center;">Buat Form</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$form_names = array();
								$count = 1; ?>
								@foreach($form_approvers as $form_approver)
								@if(!in_array($form_approver['form_name'], $form_names))
								@if(str_contains(Auth::user()->role_code, 'MIS'))
								<tr>
									<td style="width: 0.1%; text-align: center;">{{ $count }}</td>
									<td style="width: 10%;">{{ $form_approver['form_name'] }}</td>
									<td style="width: 1%; text-align: center;">
										<button class="btn btn-success" id="{{ $form_approver['form_name'] }}" onclick="modalForm(id)" style="width: 100%; margin-bottom: 10px; font-weight: bold; white-space: normal;">
											<i class="fa fa-pencil-square-o"></i>
										</button>
									</td>
								</tr>
								<?php array_push($form_names, $form_approver['form_name']);
								$count += 1; ?>
								@else
								@if($form_approver['pic'] != 'mis')
								<tr>
									<td style="width: 0.1%; text-align: center;">{{ $count }}</td>
									<td style="width: 10%;">{{ $form_approver['form_name'] }}</td>
									<td style="width: 1%; text-align: center;">
										<button class="btn btn-success" id="{{ $form_approver['form_name'] }}" onclick="modalForm(id)" style="width: 100%; margin-bottom: 10px; font-weight: bold; white-space: normal;">
											<i class="fa fa-pencil-square-o"></i>
										</button>
									</td>
								</tr>
								<?php array_push($form_names, $form_approver['form_name']);
								$count += 1; ?>
								@endif
								@endif
								@endif
								@endforeach
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMIS">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body" style="min-height: 100px;">
					<input type="hidden" id="confirmFormId">
					<div class="form-group">
						<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Form<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<textarea style="width: 100%; text-align: center;" class="form-control" placeholder="" type="text" id="confirmFormName" disabled></textarea>
						</div>
					</div>
					<div class="form-group">
						<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Karyawan<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="confirmEmployee" placeholder="Pilih Karyawan" disabled>
								@foreach($employee_syncs as $employee_sync)
								<option value="{{ $employee_sync->employee_id }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group">
						<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Isi Form<span class="text-red"></span> :</label>
						<div class="col-xs-12">
							<textarea class="form-control" rows="3" placeholder="" id="confirmFormDescription"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" onclick="closeModal()" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
				<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmForm('MIS Member')">KONFIRMASI</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalSecurity">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body" style="min-height: 100px;">
					<div class="col-md-12">
						<input type="hidden" id="securityFormId">
						<div class="form-group">
							<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Form<span class="text-red"></span> :</label>
							<div class="col-xs-12">
								<textarea style="width: 100%; text-align: center;" class="form-control" placeholder="" type="text" id="securityFormName" disabled></textarea>
							</div>
						</div>
						<div class="form-group">
							<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Karyawan<span class="text-red"></span> :</label>
							<div class="col-xs-12">
								<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="securityEmployee" placeholder="Pilih Karyawan" disabled>
									@foreach($employee_syncs as $employee_sync)
									<option value="{{ $employee_sync->employee_id }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Security<span class="text-red"></span> :</label>
							<div class="col-xs-12">
								<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="securitySecurity" placeholder="Pilih Security">
									<option value=""></option>
									@foreach($employee_syncs as $employee_sync)
									@if($employee_sync->group == 'Security Group')
									<option value="{{ $employee_sync->employee_id }}_{{ $employee_sync->name }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" onclick="closeModal()" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
				<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmForm('Security Member')">KONFIRMASI</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body" style="min-height: 100px; ">
					<div class="col-xs-5">
						<div class="row">
							<div class="form-group">
								<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Nama Form<span class="text-red">*</span> :</label>
								<div class="col-xs-12">
									<textarea style="" class="form-control" placeholder="" type="text" id="createFormName" disabled></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Pilih Karyawan<span class="text-red">*</span> :</label>
								<div class="col-xs-12">
									<select class="form-control select2" style="width: 100%; text-align: center;" type="text" id="createEmployee" placeholder="Pilih Karyawan" onchange="fetchApprover(value)">
										<option value=""></option>
										@foreach($employee_syncs as $employee_sync)
										<option value="{{ $employee_sync->employee_id }}_{{ $employee_sync->name }}_{{ $employee_sync->department_shortname }}_{{ $employee_sync->department }}">{{ $employee_sync->employee_id }} - {{ $employee_sync->name }} ({{ $employee_sync->department_shortname }})</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-7">
						<div class="form-group">
							<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Persetujuan Terkait<span class="text-red"></span> :</label>
							<div class="col-xs-12" id="createApprover">
								<ol id="approverList">
								</ol>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Isi Form<span class="text-red">*</span> :</label>
						<div class="col-xs-12">
							<textarea class="form-control" rows="3" placeholder="" id="createFormDescription"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger pull-left" onclick="closeModal()" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
				<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="createForm()">BUAT FORM</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var form_approvers = <?php echo json_encode($form_approvers); ?>;
	var form_descriptions = <?php echo json_encode($form_descriptions); ?>;
	var employee_syncs = <?php echo json_encode($employee_syncs); ?>;
	var approvers = <?php echo json_encode($approvers); ?>;
	var create_approver = [];
	var ticket_forms = [];
	var ticket_form_approvers = [];
	var approver_headers = ['Manager', 'Deputy General Manager', 'General Manager', 'Chief MIS', 'Manager MIS', 'Director', 'MIS', 'Security'];

	$(function () {
		$('#createEmployee').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#securitySecurity').select2({
			dropdownParent: $('#modalSecurity')
		});
	});

	function fetchTable(){
		var data = {

		}
		$.get('{{ url("fetch/mis/form") }}', data, function(result, status, xhr){
			if(result.status){
				ticket_forms = result.ticket_forms;
				ticket_form_approvers = result.ticket_form_approvers;

				$('#tableProgressBody').html("");
				$('#tableFinishedBody').html("");
				$('#tableRejectedBody').html("");
				$('#tableProgress').DataTable().clear();
				$('#tableProgress').DataTable().destroy();
				$('#tableFinished').DataTable().clear();
				$('#tableFinished').DataTable().destroy();
				$('#tableRejected').DataTable().clear();
				$('#tableRejected').DataTable().destroy();
				// $('.table').DataTable().clear();
				// $('.table').DataTable().destroy();

				for (var i = 0; i < ticket_forms.length; i++) {
					var tableBody = "";
					var report = '{{ url("create/pdf/form_mis")}}';
					tableBody += '<tr>';
					tableBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="'+report+'/'+ticket_forms[i].form_id+'" target="_blank">'+ticket_forms[i].form_id+'</a></td>';
					var color = "";
					if(ticket_forms[i].status == 'Rejected'){
						color = "color: black; background-color: rgb(255,204,255);";
					}
					if(ticket_forms[i].status == 'Requested'){
						color = "color: black; background-color: orange;";
					}
					if(ticket_forms[i].status == 'Partially Approved'){
						color = "color: black; background-color: yellow;";
					}
					if(ticket_forms[i].status == 'Fully Approved'){
						color = "color: white; background-color: #00a65a;";
					}
					if(ticket_forms[i].status == 'Finished'){
						color = "color: black; background-color: null;";
					}
					tableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; '+color+'">'+ticket_forms[i].status+'</td>';
					tableBody += '<td style="width: 0.1%; text-align: center;">'+ticket_forms[i].department_shortname+'</td>';
					tableBody += '<td style="width: 3%; text-align: center;">'+ticket_forms[i].form_name+'</td>';
					tableBody += '<td style="width: 1.5%; text-align: left;">'+ticket_forms[i].employee_id+'<br>'+ticket_forms[i].employee_name+'</td>';
					tableBody += '<td style="width: 1%; text-align: center; background-color: #00a65a; color: white;">'+ticket_forms[i].created_by_name+'<br>'+ticket_forms[i].created_at+'</td>';
					for (var j = 0; j < approver_headers.length; j++) {
						var approver = false;
						var color = "";
						for (var k = 0; k < ticket_form_approvers.length; k++) {
							var approved_at = "";
							if(ticket_form_approvers[k].approved_at){
								approved_at = ticket_form_approvers[k].approved_at;
							}
							if(ticket_form_approvers[k].status == 'Rejected'){
								color = "color: black; background-color: rgb(255,204,255);";
							}
							if(ticket_form_approvers[k].status == 'Waiting'){
								color = "color: black; background-color: null;";
							}
							if(ticket_form_approvers[k].status == 'Approved'){
								color = "color: white; background-color: #00a65a;";
							}
							if(approver_headers[j] == ticket_form_approvers[k].position && ticket_form_approvers[k].form_id == ticket_forms[i].form_id){
								if(ticket_form_approvers[k].status == 'Waiting'){
									if(ticket_form_approvers[k].position == 'MIS'){
										tableBody += '<td style="width: 1%; text-align: center; '+color+'"><a style="color: black;" href="javascript:void(0)" onclick="modalMIS(\''+ticket_forms[i].form_id+'\')"><div style="height:100%; width:100%;">'+ticket_form_approvers[k].approver_name+'<br>('+ticket_form_approvers[k].status+')<br>'+approved_at+'</div></a></td>';

									}
									else if(ticket_form_approvers[k].position == 'Security'){
										tableBody += '<td style="width: 1%; text-align: center; '+color+'"><a style="color: black;" href="javascript:void(0)" onclick="modalSecurity(\''+ticket_forms[i].form_id+'\')"><div style="height:100%; width:100%;">'+ticket_form_approvers[k].approver_name+'<br>('+ticket_form_approvers[k].status+')<br>'+approved_at+'</div></a></td>';

									}
									else{
										tableBody += '<td style="width: 1%; text-align: center; '+color+'"><a style="color: black;" href="{{ url('approval/mis/form') }}?form_id='+ticket_forms[i].form_id+'&approver_id='+ticket_form_approvers[k].approver_id+'"><div style="height:100%; width:100%;">'+ticket_form_approvers[k].approver_name+'<br>('+ticket_form_approvers[k].status+')<br>'+approved_at+'</div></a></td>';
									}
									approver = true;
									break;
								}
								else{
									tableBody += '<td style="width: 1%; text-align: center; '+color+'">'+ticket_form_approvers[k].approver_name+'<br>('+ticket_form_approvers[k].status+')<br>'+approved_at+'</td>';
									approver = true;
									break;
								}
							}
						}
						if(approver == false){
							tableBody += '<td style="width: 1%; text-align: center; color: white; background-color: black;">None</td>';
						}
					}
					tableBody += '</tr>';
					if(ticket_forms[i].status == 'Rejected'){
						$('#tableRejectedBody').append(tableBody);
					}
					else if(ticket_forms[i].status == 'Finished'){
						$('#tableFinishedBody').append(tableBody);
					}
					else{
						$('#tableProgressBody').append(tableBody);						
					}
				}

				$('.table').DataTable({
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
						{
							extend: 'copy',
							className: 'btn btn-success',
							text: '<i class="fa fa-copy"></i> Copy',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'excel',
							className: 'btn btn-info',
							text: '<i class="fa fa-file-excel-o"></i> Excel',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
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
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
				return false;
			}
		});
}

function createForm(){
	$('#loading').show();

	var form_name = $('#createFormName').val();
	var employee = $('#createEmployee').val().split('_');
	var form_description = CKEDITOR.instances.createFormDescription.getData();

	if(form_name == "" || employee.length <= 0 || form_description == "" || create_approver.length <= 0){
		$('#loading').hide();
		openErrorGritter('Error!', "Mohon periksa kelengkapan data.");
		audio_error.play();
		return false;
	}

	var formData = new FormData();
	formData.append('form_name', form_name);
	formData.append('form_description', form_description);
	formData.append('employee_id', employee[0]);
	formData.append('employee_name', employee[1]);
	formData.append('department_shortname', employee[2]);
	formData.append('department', employee[3]);

	formData.append('create_approver', JSON.stringify(create_approver));

	$.ajax({
		url:"{{ url('input/mis/form') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			if (data.status) {
				closeModal();
				$('#loading').hide();
				openSuccessGritter('Success!', data.message);
				audio_ok.play();
				fetchTable();
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

function closeModal(){
	$('#modalSelectForm').hide();
	$('#modalCreate').hide();
	$('#modalMIS').hide();
	$('#modalSecurity').hide();
	$('#modalSelectForm').modal('hide');
	$('#modalCreate').modal('hide');
	$('#modalMIS').modal('hide');
	$('#modalSecurity').modal('hide');
}

function confirmForm(approver){
	var formData = new FormData();

	if(approver == 'MIS Member'){
		var form_id = $('#confirmFormId').val();
		var form_description = CKEDITOR.instances.confirmFormDescription.getData();

		formData.append('form_id', form_id);
		formData.append('form_description', form_description);
		formData.append('approver', approver);
	}

	if(approver == 'Security Member'){
		var form_id = $('#securityFormId').val();
		var security = $('#securitySecurity').val().split('_');

		formData.append('form_id', form_id);
		formData.append('approver', approver);
		formData.append('security_id', security[0]);
		formData.append('security_name', security[1]);
	}

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
				fetchTable();
				closeModal();
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

function modalSecurity(form_id){
	for (var i = 0; i < ticket_forms.length; i++) {
		if(ticket_forms[i].form_id == form_id){
			$('#securityFormId').val(form_id);
			$('#securityFormName').val(ticket_forms[i].form_name);
			$('#securityEmployee').val(ticket_forms[i].employee_id).change();
			$('#securitySecurity').prop('selectedIndex', 0).change();
			break;
		}
	}
	$('#modalSecurity').modal('show');
}

function modalMIS(form_id){
	for (var i = 0; i < ticket_forms.length; i++) {
		if(ticket_forms[i].form_id == form_id){
			$('#confirmFormId').val(form_id);
			$('#confirmFormName').val(ticket_forms[i].form_name);
			$('#confirmEmployee').val(ticket_forms[i].employee_id).change();
			$('#confirmFormDescription').html(CKEDITOR.instances.confirmFormDescription.setData(ticket_forms[i].form_description));
			break;
		}
	}
	$('#modalMIS').modal('show');
}

function modalForm(form_name){
	$('#modalSelectForm').hide();
	$('#approverList').html("");
	$('#createFormName').val(form_name);
	$('#createEmployee').prop('selectedIndex', 0).change();
	for (var i = 0; i < form_descriptions.length; i++) {
		if(form_descriptions[i].form_name == form_name){
			$('#createFormDescription').html(CKEDITOR.instances.createFormDescription.setData(form_descriptions[i].description));
			break;
		}
	}
	$('#modalCreate').modal('show');
}

function fetchApprover(value){
	if(value != ""){
		var form_name = $('#createFormName').val();
		var employee = value.split('_');
		create_approver = [];

		$('#approverList').html("");
		var approverList = "";
		for (var i = 0; i < form_approvers.length; i++) {
			if(form_approvers[i].form_name == form_name){

				if(form_approvers[i].approver == 'MIS'){
					approverList += '<li>MIS Member - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
					create_approver.push({
						approver_id: 'MIS Member',
						approver_name: 'MIS Member',
						approver_email: 'MIS Member',
						position: form_approvers[i].approver,
						remark: form_approvers[i].remark
					});
				}
				else if(form_approvers[i].approver == 'Security'){
					approverList += '<li>Security Member - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
					create_approver.push({
						approver_id: 'Security Member',
						approver_name: 'Security Member',
						approver_email: 'Security Member',
						position: form_approvers[i].approver,
						remark: form_approvers[i].remark
					});
				}
				else{
					for (var j = 0; j < approvers.length; j++) {
						if(form_approvers[i].approver == 'Chief MIS' && approvers[j].department == 'Management Information System Department' && approvers[j].remark == 'Chief'){
							approverList += '<li>'+approvers[j].approver_name+' - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
							create_approver.push({
								approver_id: approvers[j].approver_id,
								approver_name: approvers[j].approver_name,
								approver_email: approvers[j].approver_email,
								position: form_approvers[i].approver,
								remark: form_approvers[i].remark
							});
						}
						if(form_approvers[i].approver == 'Manager MIS' && approvers[j].department == 'Management Information System Department' && approvers[j].remark == 'Manager'){
							approverList += '<li>'+approvers[j].approver_name+' - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
							create_approver.push({
								approver_id: approvers[j].approver_id,
								approver_name: approvers[j].approver_name,
								approver_email: approvers[j].approver_email,
								position: form_approvers[i].approver,
								remark: form_approvers[i].remark
							});
						}
						else if(form_approvers[i].approver == 'Director' && approvers[j].department == 'Human Resources Department' && approvers[j].remark == 'Director'){
							approverList += '<li>'+approvers[j].approver_name+' - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
							create_approver.push({
								approver_id: approvers[j].approver_id,
								approver_name: approvers[j].approver_name,
								approver_email: approvers[j].approver_email,
								position: form_approvers[i].approver,
								remark: form_approvers[i].remark
							});
						}
						else if(approvers[j].remark == form_approvers[i].approver && approvers[j].department == employee[3]){
							approverList += '<li>'+approvers[j].approver_name+' - '+form_approvers[i].remark+' ('+form_approvers[i].approver+')</li>';
							create_approver.push({
								approver_id: approvers[j].approver_id,
								approver_name: approvers[j].approver_name,
								approver_email: approvers[j].approver_email,
								position: form_approvers[i].approver,
								remark: form_approvers[i].remark
							});
						}
					}
				}
			}
		}
		$('#approverList').append(approverList);
	}
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

CKEDITOR.replace('createFormDescription' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
	height: '40vh'
});
CKEDITOR.replace('confirmFormDescription' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
	height: '40vh'
});

</script>

@endsection
