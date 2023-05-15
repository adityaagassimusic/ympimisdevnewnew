@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#ticketTableBody > tr:hover {
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

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
	.crop2 {
		overflow: hidden;
	}
	.crop2 img {
		height: 70px;
		margin: -20% 0 0 0 !important;
	}
	#loading { display: none; }

	.cke_dialog {
		z-index: 100000000 !important;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 12%;" onclick="modalCreate();"><i class="fa fa-pencil-square-o"></i> Buat EJOR</button>
		<a href="{{ url('index/ejor/monitoring') }}" class="btn btn-info pull-right" style="margin-left: 5px; width: 10%;"><i class="fa fa-desktop"></i> Monitoring</a>
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
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('all')">Total</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Created')">Created</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Approval')">Approval</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Waiting')">Waiting</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('InProgress')">InProgress</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Verifying')">Verifying</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('OnHold')">OnHold</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Finished')">Finished</th>
						<th style="width: 12.5%; text-align: center; font-size: 1.5vw; cursor: pointer;" onclick="fetchTicket('Rejected')">Rejected</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="count_all" style="cursor: pointer; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('all')">0</td>
						<td id="count_created" style="cursor: pointer; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Created')">0</td>
						<td id="count_approval" style="cursor: pointer; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Approval')">0</td>
						<td id="count_waiting" style="cursor: pointer; background-color: #ffeb3b; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Waiting')">0</td>
						<td id="count_inprogress" style="cursor: pointer; background-color: #71b1e5; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('InProgress')">0</td>
						<td id="count_verif" style="cursor: pointer; background-color: #71b1e5; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Verifying')">0</td>
						<td id="count_onhold" style="cursor: pointer; background-color: #f9a825; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('OnHold')">0</td>
						<td id="count_finished" style="cursor: pointer; background-color: #aee571; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Finished')">0</td>
						<td id="count_rejected" style="cursor: pointer; background-color: #e53935; text-align: center; font-size: 1.8vw; font-weight: bold;" onclick="fetchTicket('Rejected')">0</td>
					</tr>
				</tbody>
			</table>
			<table id="ticketTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">ID</th>
						<th style="width: 5%;">Title</th>
						<!-- <th style="width: 7%;">Description</th> -->
						<th style="width: 1%;">Status</th>
						<!-- <th style="width: 1%;">#</th> -->
						<th style="width: 4%;">PIC</th>
						<th style="width: 1%;">Att</th>
						<th style="width: 1%;">Due Date</th>
						<th style="width: 2%;">Created By</th>
						<th style="width: 2%;">Action</th>
					</tr>
				</thead>
				<tbody id="ticketTableBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						BUAT EJOR<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Section<span class="text-red">*</span> :</label>

								<div class="col-sm-7">
									<input type="text" class="form-control" name="createSection" id="createSection" value="{{ $employee->section }}" readonly>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Judul<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Title" id="createTitle">
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Prioritas<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="createPriority" id="createPriority" style="width: 100%" data-placeholder="Pilih Prioritas" onchange="selectPriority()">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="Urgent">Urgent</option>
									</select>
								</div>
							</div>

							<div class="form-group" style="display: none" id="div_priority">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Alasan Prioritas<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<textarea id="createPriorityReason" name="createPriorityReason" placeholder="Isikan Alasan Prioritas" class="form-control"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Tipe Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="createType" id="createType" data-placeholder="Select Type" style="width: 100%;">
										<option></option>
										@foreach($types as $type)
										<option value="{{ $type }}">{{ $type }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kategori Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="createCategory" id="createCategory" data-placeholder="Select Category" style="width: 100%;" onchange="getCategory(this)">
										<option></option>
										@foreach($categories as $category)
										<option value="{{ $category }}">{{ $category }}</option>
										@endforeach
									</select>
								</div>
							</div>

							

							<div class="form-group" id="note_lain" style="display: none">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Note Kategori Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="createCategoryLain" id="createCategoryLain" placeholder="Write Category Note">
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Alasan Pembuatan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="createReason" id="createReason" style="width: 100%" data-placeholder="Pilih Alasan">
										<option value=""></option>
										<option value="Penanganan komplain">Penanganan komplain</option>
										<option value="Temuan Patrol">Temuan Patrol</option>
										<option value="Perbaikan ketidaksesuaian">Perbaikan ketidaksesuaian</option>
										<option value="Pembuatan Drawing">Pembuatan Drawing</option>
										<option value="Kaizen">Kaizen</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Target Penyelesaian<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="createTarget" id="createTarget" placeholder="Select Target Date">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Deskripsi Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" placeholder="Enter Description" id="createDescription"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Tujuan Perbaikan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" placeholder="Enter Goal" id="createGoal"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Sekarang<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Condition" id="createBefore"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Perbaikan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Kaizen Condition" id="createAfter"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Lampiran<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="createAttachment" multiple="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="saveForm()">BUAT EJOR</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						EDIT EJOR<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Section<span class="text-red">*</span> :</label>

								<div class="col-sm-7">
									<input type="hidden" name="editFormId" id="editFormId">
									<input type="text" class="form-control" name="editSection" id="editSection" readonly>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Judul<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Title" id="editTitle">
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Prioritas<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="editPriority" id="editPriority" style="width: 100%" data-placeholder="Pilih Prioritas" onchange="selectEditPriority()">
										<option value=""></option>
										<option value="Normal">Normal</option>
										<option value="Urgent">Urgent</option>
									</select>
								</div>
							</div>

							<div class="form-group" style="display: none" id="div_edit_priority">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Alasan Prioritas<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<textarea id="editPriorityReason" name="editPriorityReason" placeholder="Isikan Alasan Prioritas" class="form-control"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Tipe Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="editType" id="editType" data-placeholder="Select Type" style="width: 100%;">
										<option></option>
										@foreach($types as $type)
										<option value="{{ $type }}">{{ $type }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kategori Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="editCategory" id="editCategory" data-placeholder="Select Category" style="width: 100%;" onchange="getCategory(this)">
										<option></option>
										@foreach($categories as $category)
										<option value="{{ $category }}">{{ $category }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group" id="note_lain" style="display: none">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Note Kategori Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" name="editCategoryLain" id="editCategoryLain" placeholder="Write Category Note">
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Alasan Pembuatan<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<select class="form-control select2" name="editReason" id="editReason" style="width: 100%" data-placeholder="Pilih Alasan">
										<option value=""></option>
										<option value="Penanganan komplain">Penanganan komplain</option>
										<option value="Temuan Patrol">Temuan Patrol</option>
										<option value="Perbaikan ketidaksesuaian">Perbaikan ketidaksesuaian</option>
										<option value="Pembuatan Drawing">Pembuatan Drawing</option>
										<option value="Kaizen">Kaizen</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Target Penyelesaian<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" name="editTarget" id="editTarget" placeholder="Select Target Date">
									</div>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Deskripsi Pekerjaan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" placeholder="Enter Description" id="editDescription"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Tujuan Perbaikan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" placeholder="Enter Goal" id="editGoal"></textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Sekarang<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Condition" id="editBefore">-</textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Perbaikan<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" placeholder="Enter Kaizen Condition" id="editAfter">-</textarea>
								</div>
							</div>

							<div class="form-group">
								<label style="padding-top: 0;" class="col-sm-3 control-label">Lampiran<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="editAttachment" multiple="">
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="editForm()">SIMPAN PERUBAHAN</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
		$('.select2').select2();
		$('#createTarget').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
		});

		$('#editTarget').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
		});

		fetchTicket();
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 37.5%; z-index: 9999; border: 2px solid grey;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn btn-default" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){
		$(this).find('.del').addClass('btn-default');
		$(this).find('.clear').addClass('btn-default');
		$(this).find('.cancel').addClass('btn-default');		
		$(this).find('.done').addClass('btn-success');
	};

	// $(function () {
	// 	$('#createCategory').select2({
	// 		dropdownParent: $('#modalCreate')			
	// 	});
	// });

	// $(function () {
	// 	$('#createPriority').select2({
	// 		dropdownParent: $('#modalCreate'),
	// 	});
	// });

	// $(function () {
	// 	$('#createDepartment').select2({
	// 		dropdownParent: $('#modalCreate')
	// 	});
	// });

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var costdowns = [];
	var costdown_count = 0;
	var role = "{{ Auth::user()->role_code }}";

	function detailProject(id){
		window.open('{{ url("index/ticket/detail") }}'+'/'+id, '_blank');
	}

	function fetchTicket(stat){
		$('#loading').show();

		var stats = 'all';
		if(stat){
			stats = stat;
		}

		var data = {
			status : stats
		}

		$.get('{{ url("fetch/ejor") }}', data, function(result, status, xhr){
			if(result.status){
				clearAll();
				$('#ticketTable').DataTable().clear();
				$('#ticketTable').DataTable().destroy();
				$('#ticketTableBody').html("");
				var ticketTable = "";
				var tickets = result.datas;
				var count_status = [];

				tickets.reduce(function (res, value) {
					if (!res[value.status]) {
						res[value.status] = {
							cnt: 0,
							status: value.status
						};
						count_status.push(res[value.status])
					}
					res[value.status].cnt += 1
					return res;
				}, {});

				var total_status = 0;
				$.each(count_status, function(key, value){
					if(value.status == 'Approval'){
						total_status += value.cnt;
						$('#count_approval').text(value.cnt);
					}
					if(value.status == 'Created'){
						total_status += value.cnt;
						$('#count_created').text(value.cnt);
					}
					if(value.status == 'Waiting'){
						total_status += value.cnt;
						$('#count_waiting').text(value.cnt);
					}
					if(value.status == 'InProgress'){
						total_status += value.cnt;
						$('#count_inprogress').text(value.cnt);
					}
					if(value.status == 'Verifying'){
						total_status += value.cnt;
						$('#count_verif').text(value.cnt);
					}
					if(value.status == 'OnHold'){
						total_status += value.cnt;
						$('#count_onhold').text(value.cnt);
					}
					if(value.status == 'Finished'){
						total_status += value.cnt;
						$('#count_finished').text(value.cnt);
					}
					if(value.status == 'Rejected'){
						total_status += value.cnt;
						$('#count_rejected').text(value.cnt);
					}
				});
				
				if(stats == 'all'){
					$('#count_all').text(total_status);					
				}

				$.each(result.datas, function(key, value){
					ticketTable += '<tr>';
					ticketTable += '<td>'+replaceNull(value.form_id)+'</td>';
					ticketTable += '<td>'+replaceNull(value.title)+'</td>';
					// ticketTable += '<td>'+replaceNull(value.description)+'</td>';
					ticketTable += '<td>'+replaceNull(value.status)+'</td>';
					// ticketTable += '<td>';
					// if(replaceNull(value.pic) != '-'){
					// 	var foto = value.pic.toUpperCase();
					// 	var avatar = 'images/avatar/'+foto+'.jpg';
					// 	var url = "{{ url('') }}"+'/'+avatar;
					// 	ticketTable += '<div class="crop2">';
					// 	ticketTable += '<img src="'+url+'">';
					// 	ticketTable += '</div>';
					// }
					// else{
					// 	ticketTable += '-';
					// }
					// ticketTable += '</td>';
					ticketTable += '<td>'+replaceNull(value.pic)+'<br>'+(value.pic_name || '')+'</td>';
					ticketTable += '<td>';
					if (value.attachment) {
						var atts = value.attachment.split(',');

						$.each(atts, function(key2, value2) {
							ticketTable += '<a class="btn btn-xs btn-primary" href="{{ url("files/ejor/att/") }}/'+value2+'" target="_blank" style="margin-bottom: 2px"><i class="fa fa-file-text"></i> '+value2+'</a><br>';

						})
					}

					ticketTable += '<a class="btn btn-xs btn-danger" href="{{ url("files/ejor/form/") }}/'+value.form_id+'.pdf" target="_blank" style="margin-bottom: 2px"><i class="fa fa-file-pdf-o"></i> Report PDF.pdf</a><br>';

					$.each(result.evidences, function(key2, value2) {
						if (value.form_id == value2.form_id) {
							var atts = value2.attachment.split(',');

							$.each(atts, function(key3, value3) {
								ticketTable += '<a class="btn btn-xs btn-warning" href="{{ url("files/ejor/evidence/") }}/'+value3+'" target="_blank" style="margin-bottom: 2px"><i class="fa fa-file-text"></i> '+value3+'</a><br>';

							})
						}
					})

					ticketTable += '</td>';
					ticketTable += '<td>'+replaceNull(value.target_date)+'</td>';
					ticketTable += '<td>'+replaceNull(value.created_by)+'<br>'+replaceNull(value.name)+'</td>';
					ticketTable += '<td>';
					// ticketTable += '<button class="btn btn-xs btn-default" onclick="detailProject(\''+value.form_id+'\')"><i class="fa fa-info"></i> Details</button><br>';

					if (~role.indexOf("MIS")) {
						ticketTable += '<a class="btn btn-xs btn-danger" href="{{ url("generate/pdf/ejor/") }}/'+value.form_id+'" target="_blank"><i class="fa fa-refresh"></i> Generate PDF</a><br>';
					}


					if (value.status == 'Created') {
						ticketTable += '<button class="btn btn-xs btn-primary" onclick="editModal(\''+value.form_id+'\')"><i class="fa fa-pencil"></i> Edit</button><br>';
					}

					if (value.status != 'Finished') {
						ticketTable += '<button class="btn btn-xs btn-success" onclick="sendEmail(\''+value.form_id+'\')"><i class="fa fa-paper-plane"></i> Send Email</button>';
					}
					ticketTable += '</td>';
					ticketTable += '</tr>';
				});

				$('#ticketTableBody').append(ticketTable);

				$('#ticketTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
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
					'ordering': true,
					'order': [[0, 'desc']],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

			}
			else{
				alert('Unidentified Error '+result.message);
				audio_error.play();
				return false;
			}
		});
}

function saveForm(){
	if(confirm("Apakah anda yakin akan mengajukan EJOR ini?")){

		var section = $('#createSection').val();
		var category = $('#createCategory').val();
		var category_lain = $('#createCategoryLain').val();
		var title = $('#createTitle').val();
		var priority = $('#createPriority').val();
		var priority_reason = $('#createPriorityReason').val();
		var type = $('#createType').val();
		var reason = $('#createReason').val();
		var target = $('#createTarget').val();		
		var description = CKEDITOR.instances.createDescription.getData();
		var goal = CKEDITOR.instances.createGoal.getData();
		var before = CKEDITOR.instances.createBefore.getData();
		var after = CKEDITOR.instances.createAfter.getData();
		var status = true;
		var message = "";
		if(category == '' || priority == '' || title == '' || description == '' || before == '' || after == '' || goal == '' || type == '' || target == '' || reason == ''){
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		if (category == 'Lain-lain' && category_lain == "") {
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		if (priority == 'Urgent' && priority_reason == "") {
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		var formData = new FormData();

		if(status == false){
			openErrorGritter('Error!', message);
			return false;
		}

		$('#loading').show();

		formData.append('section', section);
		formData.append('category', category);
		formData.append('category_note', category_lain);
		formData.append('type', type);
		formData.append('title', title);
		formData.append('target', target);
		formData.append('description', description);
		formData.append('goal', goal);
		formData.append('before', before);
		formData.append('priority', priority);
		formData.append('priority_reason', priority_reason);
		formData.append('reason', reason);

		var att_count = 0;
		for (var i = 0; i < $('#createAttachment').prop('files').length; i++) {
			formData.append('att_'+i, $('#createAttachment').prop('files')[i]);
			att_count++;
		}

		formData.append('att_count', att_count);

		$.ajax({
			url:"{{ url('input/ejor') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#loading').hide();
					clearAll();
					fetchTicket();
					openSuccessGritter('Success!',data.message);
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
				}
			}
		});
	}
	else{
		return false;
	}

}

function modalCreate(){
	clearAll();
	$('#modalCreate').modal('show');
}

function editModal(form_id) {
	$("#loading").show();

	var data = {
		form_id : form_id,
		status : 'all'
	}
	$.get('{{ url("fetch/ejor") }}', data, function(result, status, xhr){
		$("#loading").hide();

		$('#modalEdit').modal('show');
		
		$("#editFormId").val(form_id);
		$("#editSection").val(result.datas[0].section);
		$("#editTitle").val(result.datas[0].title);
		$("#editType").val(result.datas[0].job_type).trigger('change');
		$("#editCategory").val(result.datas[0].job_category).trigger('change');
		$("#editCategoryLain").val(result.datas[0].job_category_note);
		$("#editTarget").val(result.datas[0].request_date);
		$("#editPriority").val(result.datas[0].priority).trigger('change');
		$("#editPriorityReason").val(result.datas[0].priority_reason);
		$("#editReason").val(result.datas[0].reason).trigger('change');


		$("#editDescription").html(CKEDITOR.instances.editDescription.setData(result.datas[0].description));
		$("#editGoal").html(CKEDITOR.instances.editGoal.setData(result.datas[0].purpose));
		$("#editBefore").html(CKEDITOR.instances.editBefore.setData(result.datas[0].condition_before));
		$("#editAfter").html(CKEDITOR.instances.editAfter.setData(result.datas[0].condition_after));

		// $("#editDescription").val(result.datas[0].description);
		// $("#editGoal").val(result.datas[0].purpose);
		// $("#editBefore").val(result.datas[0].condition_before);
		// $("#editAfter").val(result.datas[0].condition_after);
		// $("#editAttachment").val(result.datas[0].attachment);

	})
}

function editForm(){
	if(confirm("Apakah anda yakin akan menyimpan perubahan EJOR ini?")){

		var section = $('#editSection').val();
		var form_id = $('#editFormId').val();
		var category = $('#editCategory').val();
		var category_lain = $('#editCategoryLain').val();
		var title = $('#editTitle').val();
		var type = $('#editType').val();
		var target = $('#editTarget').val();		
		var description = CKEDITOR.instances.editDescription.getData();
		var goal = CKEDITOR.instances.editGoal.getData();
		var before = CKEDITOR.instances.editBefore.getData();
		var after = CKEDITOR.instances.editAfter.getData();
		var status = true;
		var message = "";
		if(category == '' || title == '' || description == '' || before == '' || after == '' || goal == '' || type == '' || target == ''){
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		if (category == 'Lain-lain' && category_lain == "") {
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		var formData = new FormData();

		if(status == false){
			openErrorGritter('Error!', message);
			return false;
		}

		$('#loading').show();

		formData.append('form_id', form_id);
		formData.append('section', section);
		formData.append('category', category);
		formData.append('category_note', category_lain);
		formData.append('type', type);
		formData.append('title', title);
		formData.append('target', target);
		formData.append('description', description);
		formData.append('goal', goal);
		formData.append('before', before);
		formData.append('after', after);

		var att_count = 0;
		for (var i = 0; i < $('#editAttachment').prop('files').length; i++) {
			formData.append('att_'+i, $('#editAttachment').prop('files')[i]);
			att_count++;
		}

		formData.append('att_count', att_count);

		$.ajax({
			url:"{{ url('edit/ejor') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#loading').hide();
					clearAll2();
					fetchTicket();
					openSuccessGritter('Success!',data.message);
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
				}
			}
		});
	}
	else{
		return false;
	}

}

function clearAll(){
	$('#loading').hide();
	$('#modalCreate').modal('hide');

	$("#createCategory").prop('selectedIndex', 0).change();
	$("#createType").prop('selectedIndex', 0).change();
	$('#createTitle').val("");
	$('#createTarget').val("");
	$('#createDescription').val("");
	$('#createGoal').val("");
	$('#createBefore').val("");
	$('#createAfter').val("");
	$('#createAttachment').val("");
}

function clearAll2(){
	$('#loading').hide();
	$('#modalEdit').modal('hide');

	$("#editCategory").prop('selectedIndex', 0).change();
	$("#editType").prop('selectedIndex', 0).change();
	$('#editTitle').val("");
	$('#editTarget').val("");
	$('#editDescription').val("");
	$('#editGoal').val("");
	$('#editBefore').val("");
	$('#editAfter').val("");
	$('#editAttachment').val("");
}

function sendEmail(form_id) {
	if (confirm('Anda yakin akan mengirim email approval ejor ini?')) {
		var data = {
			form_id : form_id
		}
		
		$("#loading").show();

		$.post('{{ url("mail/ejor") }}', data, function(result, status, xhr){
			if (result.status) {
				openSuccessGritter('Success', 'Send Email Berhasil');
				fetchTicket();			
			} else {
				openErrorGritter('Error', result.message);
			}
		})
	}
}

function selectPriority() {
	if ($("#createPriority").val() == 'Urgent') {
		$("#div_priority").show();

		$("#createTarget").val('').trigger('change');

		$("#createTarget").removeAttr("disabled");
	} else {
		$("#div_priority").hide();

		var currentDate = new Date();
		currentDate.setDate(currentDate.getDate() + 14);

		let isoDate = currentDate.toISOString().substring(0, 10);

		$("#createTarget").val(isoDate).trigger('change');

		$("#createTarget").attr("disabled", "disabled");
	}
}

function selectEditPriority() {
	if ($("#editPriority").val() == 'Urgent') {
		$("#div_edit_priority").show();
	} else {
		$("#div_edit_priority").hide();
	}
}



function getCategory(elem) {
	if ($(elem).val() == 'Lain-lain') {
		$("#note_lain").show();
	} else {
		$("#note_lain").hide();
	}
}

function truncate(str, n){
	return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
};

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '5000'
	});
	audio_ok.play();
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '5000'
	});
	audio_error.play();
}

function replaceNull(s) {
	return s == null ? "-" : s;
}


CKEDITOR.replace('createBefore' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('createAfter' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('createDescription' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('createGoal' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('editBefore' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('editAfter' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('editDescription' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});

CKEDITOR.replace('editGoal' ,{
	filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
});




</script>

@endsection
