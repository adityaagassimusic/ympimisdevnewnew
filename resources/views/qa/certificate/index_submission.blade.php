@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
		overflow:hidden;
		margin-left: 2px;
	}
	tbody>tr>td{
		/*text-align:center;*/
		margin-left: 2px;
	}
	tfoot>tr>th{
		/*text-align:center;*/
		margin-left: 2px;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.containers {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 15px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	  padding-top: 6px;
	}

	/* Hide the browser's default checkbox */
	.containers input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  margin-top: 4px;
	}

	/* On mouse-over, add a grey background color */
	.containers:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.containers input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.containers input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.containers .checkmark:after {
	  left: 9px;
	  top: 5px;
	  width: 5px;
	  height: 10px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.notification .badge {
	  position: absolute;
	  top: -10px;
	  right: -10px;
	  padding: 5px 7px;
	  border-radius: 50%;
	  background-color: red;
	  color: white;
	}
	.notification {
	  position: relative;
	  display: inline-block;
	  border-radius: 2px;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal_new" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Buat Pengajuan Baru
		</button>
		<button class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#create_modal_deactivate" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Buat Pengajuan Non-Aktif
		</button>
		<button class="btn btn-info btn-sm pull-right" style="margin-right: 5px" onclick="fillList()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
		</button>
		<button class="btn btn-primary btn-sm pull-right notification" style="margin-right: 10px;border-radius: 25px" data-toggle="modal" data-target="#modal_notif_new">
			<i class="fa fa-bell"></i>
			<span class="badge" id="notif_qty">0</span>
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-12 pull-left">
			<div style="background-color: #3f51b5;color: white;padding: 5px;text-align: center;margin-bottom: 8px">
				<span style="font-weight: bold;font-size: 25px">PENGAJUAN BARU</span>
			</div>
			<!-- <h2 style="margin-top: 0px;">Master Operator Welding</h2> -->
			<table id="tableNew" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;font-size: 0.9vw;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">ID</th>
						<th width="1%">Req Date</th>
						<th width="1%">Status</th>
						<th width="3%">Emp</th>
						<th width="1%">Certificate Name</th>
						<th width="1%">Dept</th>
						<th width="1%">Sect</th>
						<th width="1%">Group</th>
						<th width="1%">Sub Group</th>
						<!-- <th width="2%" style="background-color: #3064db">Applicant</th>
						<th width="2%" style="background-color: #3064db">Manager</th>
						<th width="2%" style="background-color: #3064db">DGM</th>
						<th width="2%" style="background-color: #3064db">GM</th>
						<th width="2%" style="background-color: #3064db">Director</th>
						<th width="2%" style="background-color: #3064db">HR</th>
						<th width="2%" style="background-color: #3064db">GA</th>
						<th width="2%" style="background-color: #3064db">Security</th> -->
						<th width="2%" style="background-color: #3064db">Applicant</th>
						<th width="2%" style="background-color: #3064db">Foreman / Chief</th>
						<th width="2%" style="background-color: #3064db">Staff QA</th>
						<th width="2%" style="background-color: #3064db">Foreman QA</th>
						<th width="2%" style="background-color: #3064db">Leader QA</th>
						<th width="1%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableNew">
				</tbody>
				<!-- <tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot> -->
			</table>
		</div>
		<div class="col-xs-12 pull-left">
			<div style="background-color: #32a852;color: white;padding: 5px;text-align: center;margin-bottom: 8px;margin-top: 10px">
				<span style="font-weight: bold;font-size: 25px">PENGAJUAN NONAKTIF</span>
			</div>
			<!-- <h2 style="margin-top: 0px;">Master Operator Welding</h2> -->
			<table id="tableDeactivate" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">ID</th>
						<th width="1%">Req Date</th>
						<th width="1%">Status</th>
						<th width="2%">Cert. ID</th>
						<th width="2%">Cert. Code</th>
						<th width="3%">Emp</th>
						<th width="1%">Dept</th>
						<th width="1%">Sect</th>
						<th width="1%">Group</th>
						<th width="1%">Sub Group</th>
						<th width="2%" style="background-color: #3064db">Applicant</th>
						<th width="2%" style="background-color: #3064db">Staff QA</th>
						<th width="1%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableDeactivate">
				</tbody>
				<!-- <tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot> -->
			</table>
		</div>
	</div>

	<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Are you sure delete?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="create_modal_new">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Pengajuan Sertifikat Kensa Baru</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-2">Tgl Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5" style="padding-right: 2px">
										<input type="text" class="form-control datepicker" id="add_date" placeholder="Req Date" required value="{{date('Y-m-d')}}">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Tujuan Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectReason">
										<select class="form-control selectReason" data-placeholder="Pilih Tujuan Pengajuan" name="add_reason" id="add_reason" style="width: 100%">
											<option value=""></option>
											<option value="Karyawan Baru">Karyawan Baru</option>
											<option value="Karyawan Mutasi">Karyawan Mutasi</option>
											<option value="Multi Sklil">Multi Sklil</option>
											<option value="Kensa Pengganti / Ririfu">Kensa Pengganti / Ririfu</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Area<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectArea">
										<select class="form-control selectArea" data-placeholder="Pilih Area" name="add_certificate_name" id="add_certificate_name" style="width: 100%">
											<option value=""></option>
											@foreach($certificate_name as $certificate_name)
											<option value="{{$certificate_name}}">{{$certificate_name}}</option>
											@endforeach()
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Karyawan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectEmp">
										<select class="form-control selectEmp" data-placeholder="Pilih Karyawan" name="add_employees" id="add_employees" style="width: 100%">
											<option value=""></option>
											@foreach($employees as $emp)
											<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-xs-3 row" style="margin-left: 2px">
										<button class="btn btn-success" onclick="addEmployee()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableEmployee">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 5%;">Name</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableEmployeeBody">
									</tbody>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th style='padding:5px'>Total: </th>
											<th style='padding:5px;text-align: center;' id="countTotal"></th>
											<th style='padding:5px'></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
					<button class="btn btn-success" onclick="createRequest()"><i class="fa fa-plus"></i> Buat Pengajuan</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="modal_notif_new">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">New Certificate Notification</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<table class="table table-hover table-bordered table-striped" id="tableNotif">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">Request ID</th>
											<th style="width: 1%;">Request Date</th>
											<th style="width: 4%;">Emp</th>
											<th style="width: 1%;">Reason</th>
											<th style="width: 2%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableNotifBody">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Close</button>
					<!-- <button class="btn btn-success" onclick="createRequest()"><i class="fa fa-plus"></i> Buat Pengajuan</button> -->
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="create_modal_deactivate">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Pengajuan Sertifikat Nonaktif</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-2">Tgl Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5" style="padding-right: 2px">
										<input type="text" class="form-control datepicker" id="add_date_non" placeholder="Req Date" required value="{{date('Y-m-d')}}">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Tujuan Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectReasonNon">
										<select class="form-control selectReasonNon" data-placeholder="Pilih Tujuan Pengajuan" name="add_reason_non" id="add_reason_non" style="width: 100%">
											<option value=""></option>
											<option value="Resign / Habis Kontrak">Resign / Habis Kontrak</option>
											<option value="Karyawan Mutasi">Karyawan Mutasi</option>
											<option value="Dalam Pengawasan Atasan">Dalam Pengawasan Atasan</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Area<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectAreaNon">
										<select class="form-control selectAreaNon" data-placeholder="Pilih Area" name="add_certificate_name_non" id="add_certificate_name_non" style="width: 100%" onchange="changeCertificateNameNon(this.value)">
											<option value=""></option>
											@foreach($certificate_name2 as $certificate_name)
											<option value="{{$certificate_name}}">{{$certificate_name}}</option>
											@endforeach()
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Sertifikat<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectCode">
										<select class="form-control selectCode" data-placeholder="Pilih Sertifikat" name="add_certificate_code_non" id="add_certificate_code_non" style="width: 100%">
											<option value=""></option>
											
										</select>
									</div>
									<div class="col-xs-3 row" style="margin-left: 2px">
										<button class="btn btn-success" onclick="addCode()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableCode">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 5%;">Name</th>
											<th style="width: 5%;">Code</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableCodeBody">
									</tbody>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th style='padding:5px'>Total: </th>
											<th colspan="3" style='padding:5px;text-align: center;' id="countTotalNon"></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
					<button class="btn btn-success" onclick="createRequestNon()"><i class="fa fa-plus"></i> Buat Pengajuan</button>
				</div>
			</div>
		</div>
	</div>



</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
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

	var certificate_code_all = null;

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = [];
	var code = [];
	var count = 0;
	var count_non = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		clearAll();

		setInterval(fillList,600000);

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: '<?php echo $tgl_max ?>'
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	});

	function changeCertificateNameNon(value) {
		if (certificate_code_all != null) {
			$('#add_certificate_code_non').html('');
			var cercode = '';
			for(var i = 0; i < certificate_code_all.length;i++){
				if (certificate_code_all[i].certificate_name == value) {
					cercode += '<option value="'+certificate_code_all[i].certificate_id+'_'+certificate_code_all[i].certificate_code+'_'+certificate_code_all[i].employee_id+'_'+certificate_code_all[i].name+'">'+certificate_code_all[i].certificate_code+' - '+certificate_code_all[i].employee_id+' - '+certificate_code_all[i].name+'</option>';
				}			
			}

			$('#add_certificate_code_non').append(cercode);
		}
	}


	$(function () {
		$('.selectReason').select2({
			dropdownParent: $('#selectReason'),
			allowClear:true
		});
		$('.selectReasonNon').select2({
			dropdownParent: $('#selectReasonNon'),
			allowClear:true
		});
		$('.selectArea').select2({
			dropdownParent: $('#selectArea'),
			allowClear:true
		});
		$('.selectAreaNon').select2({
			dropdownParent: $('#selectAreaNon'),
			allowClear:true
		});
		$('.selectCode').select2({
			dropdownParent: $('#selectCode'),
			allowClear:true
		});
		$('.selectEmp').select2({
			dropdownParent: $('#selectEmp'),
			allowClear:true
		});
	});

	function clearAll() {
		$('#add_employees').val('').trigger('change');
		$('#add_reason').val('').trigger('change');
		$("#add_certificate_name").val('').trigger('change');
		$('#tableEmployeeBody').html('');

		$('#add_reason_non').val('').trigger('change');
		$("#add_certificate_name_non").val('').trigger('change');
		$("#add_certificate_code_non").val('').trigger('change');
		$('#tableCodeBody').html('');

		$('#countTotal').html('0');
		$('#countTotalNon').html('0');
		employees = [];
		count = 0;

		code = [];
		count_non = 0;
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	var kata_confirm = 'Apakah Anda Yakin?';

	function fillList(){
		$.get('{{ url("fetch/submission/qa/certificate") }}', function(result, status, xhr){
			if(result.status){
				$('#tableNew').DataTable().clear();
				$('#tableNew').DataTable().destroy();
				$('#bodyTableNew').html("");
				var tableData = "";

				$.each(result.new_submission, function(key, value) {
					tableData += '<tr>';
					tableData += '<td style="font-weight:bold;color:red;padding:5px;">'+value.request_id+'</td>';
					tableData += '<td style="text-align:right;padding:5px;">'+value.request_date+'</td>';
					if (value.request_status == 'Requested') {
						tableData += '<td style="background-color:#3f51b5 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Partially Approved' || value.request_status == 'Receive Leader QA') {
						tableData += '<td style="background-color:#f39c12 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Fully Approved') {
						tableData += '<td style="background-color:#00a65a !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Rejected') {
						tableData += '<td style="background-color:#dd4b39 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}
					tableData += '<td style="padding:5px;">'+value.employee_id+'<br>'+value.name+'</td>';
					tableData += '<td style="padding:5px;">'+value.certificate_name+'</td>';

					var dept = '';
					var sect = '';
					var group = '';
					var sub_group = '';

					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.employee_id) {
							for(var j = 0; j < result.department.length;j++){
								if (result.department[j].department_name == result.emp[i].department) {
									dept = result.department[j].department_shortname;
								}
							}
							sect = result.emp[i].section;
							group = result.emp[i].group;
							sub_group = result.emp[i].sub_group;
						}
					}

					tableData += '<td style="padding:5px;">'+dept+'</td>';
					tableData += '<td style="padding:5px;">'+sect+'</td>';
					tableData += '<td style="padding:5px;">'+group+'</td>';
					tableData += '<td style="padding:5px;">'+sub_group+'</td>';

					if (value.applicant.split('_')[2] == '') {
						tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.applicant.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					}else{
						tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.applicant.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.applicant.split('_')[2]))+'</td>';
					}

					if (value.foreman_prod.split('_')[2] == '') {
						if (value.foreman_prod.split('_')[2] == '') {
							var url_foreman = '{{url("approval/submission/qa/certificate/new/staff_qa/")}}'+'/'+value.request_id;
							if (result.role.match(/MIS/gi)) {
								tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_foreman+'">'+value.foreman_prod.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
							}else{
								if (value.foreman_prod.split('_')[0] == result.employee_id.toUpperCase()) {
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_foreman+'">'+value.foreman_prod.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
								}else{
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_prod.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
								}
							}
						}else{
							tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_prod.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.foreman_prod.split('_')[2]))+'</td>';
						}
					}else{
						tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_prod.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.foreman_prod.split('_')[2]))+'</td>';
					}

					if (value.staff_qa.split('_')[2] == '') {
						if (value.staff_qa.split('_')[2] == '') {
							var url_staff_qa = '{{url("approval/submission/qa/certificate/new/foreman_qa/")}}'+'/'+value.request_id;
							if (result.role.match(/MIS/gi)) {
								tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_staff_qa+'">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
							}else{
								if (value.staff_qa.split('_')[0] == result.employee_id.toUpperCase()) {
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_staff_qa+'">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
								}else{
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
								}
							}
						}else{
							tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.staff_qa.split('_')[2]))+'</td>';
						}
					}else{
						tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.staff_qa.split('_')[2]))+'</td>';
					}

					if (value.foreman_qa.split('_')[2] == '') {
						if (value.foreman_qa.split('_')[2] == '') {
							var url_foreman_qa = '{{url("approval/submission/qa/certificate/new/leader_qa/")}}'+'/'+value.request_id;
							if (result.role.match(/MIS/gi)) {
								tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_foreman_qa+'">'+value.foreman_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
							}else{
								if (value.foreman_qa.split('_')[0] == result.employee_id.toUpperCase()) {
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_foreman_qa+'">'+value.foreman_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
								}else{
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
								}
							}
						}else{
							tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.foreman_qa.split('_')[2]))+'</td>';
						}
					}else{
						tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.foreman_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.foreman_qa.split('_')[2]))+'</td>';
					}
					if (value.leader_qa != null) {
						if (value.leader_qa.split('_')[2] == '') {
							var url_leader = '{{url("approval/submission/qa/certificate/new/full/")}}'+'/'+value.request_id;
							if (result.role.match(/MIS/gi)) {
								tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_leader+'">'+value.leader_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
							}else{
								if (value.leader_qa.split('_')[0] == result.employee_id.toUpperCase()) {
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" target="_blank" href="'+url_leader+'">'+value.leader_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
								}else{
									tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.leader_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
								}
							}
						}else{
							tableData += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.leader_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>'+getFormattedDateTime(new Date(value.leader_qa.split('_')[2]))+'</td>';
						}
					}else{
						tableData += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">Waiting</td>';
					}
					tableData += '<td style="padding:5px;text-align:center">';
					if (value.request_status == 'Receive Leader QA') {
						if (result.employee_id.toUpperCase() == value.leader_qa.split('_')[0]) {
							tableData += "<a class='btn btn-success btn-xs' target='_blank' href='{{url('new/qa/certificate')}}'>Buat Sertifikat</a>";
						}
						var url_delete = "{{url('new/qa/certificate/delete/')}}"+'/'+value.request_id;
						// tableData += '<button class="btn btn-warning btn-xs" data-toggle="modal" onclick="editNew(\''+value.request_id+'\')" data-target="#edit_modal_new">Edit</button>';
						tableData += '<a class="btn btn-danger btn-xs" onclick="return confirm(\''+kata_confirm+'\')" style="margin-left:2px" href="'+url_delete+'">Delete / Cancel</a>';
					}else if (value.request_status == 'Fully Approved') {
						var url_delete = "{{url('delete/submission/qa/certificate/new/')}}"+'/'+value.request_id;
						tableData += '<a class="btn btn-danger btn-xs" onclick="return confirm(\''+kata_confirm+'\')" style="margin-left:2px" href="'+url_delete+'">Delete / Cancel</a>';
					}else{
						var url_delete = "{{url('delete/submission/qa/certificate/new/')}}"+'/'+value.request_id;
						// tableData += '<button class="btn btn-warning btn-xs" data-toggle="modal" onclick="editNew(\''+value.request_id+'\')" data-target="#edit_modal_new">Edit</button>';
						tableData += '<a class="btn btn-danger btn-xs" onclick="return confirm(\''+kata_confirm+'\')" style="margin-left:2px" href="'+url_delete+'">Delete / Cancel</a>';
					}
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#bodyTableNew').append(tableData);

				var table = $('#tableNew').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#tableDeactivate').DataTable().clear();
				$('#tableDeactivate').DataTable().destroy();
				$('#bodyTableDeactivate').html("");
				var tableDataDeactivate = "";

				$.each(result.deactivation, function(key, value) {
					tableDataDeactivate += '<tr>';
					tableDataDeactivate += '<td style="font-weight:bold;color:red;padding:5px;">'+value.request_id+'</td>';
					tableDataDeactivate += '<td style="text-align:right;padding:5px;">'+value.request_date+'</td>';
					if (value.request_status == 'Requested') {
						tableDataDeactivate += '<td style="background-color:#3f51b5 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Partially Approved') {
						tableDataDeactivate += '<td style="background-color:#f39c12 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Fully Approved') {
						tableDataDeactivate += '<td style="background-color:#00a65a !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}else if (value.request_status == 'Rejected') {
						tableDataDeactivate += '<td style="background-color:#dd4b39 !important;color:white;font-size:12px;font-weight:bold;padding:5px;">'+value.request_status+'</td>';
					}
					tableDataDeactivate += '<td style="padding:5px;">'+value.certificate_id+'</td>';
					tableDataDeactivate += '<td style="padding:5px;">'+value.certificate_code+'</td>';
					tableDataDeactivate += '<td style="padding:5px;">'+value.employee_id+'<br>'+value.name+'</td>';

					var dept = '';
					var sect = '';
					var group = '';
					var sub_group = '';

					for(var i = 0; i < result.emp.length;i++){
						if (result.emp[i].employee_id == value.employee_id) {
							for(var j = 0; j < result.department.length;j++){
								if (result.department[j].department_name == result.emp[i].department) {
									dept = result.department[j].department_shortname;
								}
							}
							sect = result.emp[i].section;
							group = result.emp[i].group;
							sub_group = result.emp[i].sub_group;
						}
					}

					tableDataDeactivate += '<td style="padding:5px;">'+dept+'</td>';
					tableDataDeactivate += '<td style="padding:5px;">'+sect+'</td>';
					tableDataDeactivate += '<td style="padding:5px;">'+group+'</td>';
					tableDataDeactivate += '<td style="padding:5px;">'+sub_group+'</td>';

					if (value.applicant.split('_')[2] == '') {
						tableDataDeactivate += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.applicant.split('_')[1]+'<br>Waiting</td>';
					}else{
						tableDataDeactivate += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.applicant.split('_')[1]+'<br>'+getFormattedDateTime(new Date(value.applicant.split('_')[2]))+'</td>';
					}

					if (value.staff_qa.split('_')[2] == '') {
						var url_staff_qa = '{{url("approval/submission/qa/certificate/non/staff_qa/")}}'+'/'+value.request_id;
						if (result.role.match(/MIS/gi)) {
							tableDataDeactivate += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" href="'+url_staff_qa+'">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
						}else if(value.staff_qa.split('_')[0] == result.employee_id.toUpperCase()){
							tableDataDeactivate += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;"><a style="text-decoration: none;color:white;" href="'+url_staff_qa+'">'+value.staff_qa.split('_')[1].split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
						}else{
							tableDataDeactivate += '<td style="background-color:#dd4b39;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.staff_qa.split('_')[1]+'<br>Waiting</td>';
						}
					}else{
						tableDataDeactivate += '<td style="background-color:#00a65a;color:white;padding:5px;font-size:11px;font-weight:bold;">'+value.staff_qa.split('_')[1]+'<br>'+getFormattedDateTime(new Date(value.staff_qa.split('_')[2]))+'</td>';
					}
					tableDataDeactivate += '<td style="text-align:center">';
					if (value.request_status == 'Fully Approved') {
						var url_delete = "{{url('delete/submission/qa/certificate/non/')}}"+'/'+value.request_id;
						tableDataDeactivate += '<a class="btn btn-danger btn-xs" onclick="return confirm(\''+kata_confirm+'\')" style="margin-left:2px" href="'+url_delete+'">Delete / Cancel</a>';
					}else{
						var url_edit = "{{url('edit/submission/qa/certificate/non/')}}"+'/'+value.request_id;
						var url_delete = "{{url('delete/submission/qa/certificate/non/')}}"+'/'+value.request_id;
						// tableDataDeactivate += "<a class='btn btn-warning btn-xs' href='"+url_edit+"'>Edit</a>";
						tableDataDeactivate += '<a class="btn btn-danger btn-xs" onclick="return confirm(\''+kata_confirm+'\')" style="margin-left:2px" href="'+url_delete+'">Delete / Cancel</a>';
					}
					tableDataDeactivate += '</td>';
					tableDataDeactivate += '</tr>';
				});
				$('#bodyTableDeactivate').append(tableDataDeactivate);

				var table = $('#tableDeactivate').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#notif_qty').html(result.notif.length);

				$('#tableNotifBody').html('');
				var notif = '';

				for(var i = 0; i < result.notif.length; i++){
					notif += "<tr>";
					notif += "<td style='padding:5px'>"+result.notif[i].request_id+"</td>";
					notif += "<td style='padding:5px;text-align:right'>"+result.notif[i].request_date+"</td>";
					notif += "<td style='padding:5px'>"+result.notif[i].employee_id+" - "+result.notif[i].name+"</td>";
					notif += "<td style='padding:5px'>"+result.notif[i].reason+"</td>";
					if (result.notif[i].request_type == 'new') {
						notif += "<td style='padding:5px;text-align:center'><a class='btn btn-success btn-sm' href='{{url('new/qa/certificate')}}'>Buat Sertifikat</a></td>";
					}else{
						notif += "<td style='padding:5px;text-align:center'><a class='btn btn-danger btn-sm' href='{{url('index/qa/certificate/code')}}'>Deactivate</a></td>";
					}
					notif += "</tr>";
				}

				$("#tableNotifBody").append(notif);

				certificate_code_all = result.certificate_code;
				// changeCertificateNameNon('');

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function addEmployee() {

		if ($('#add_employees').val() == "" || $('#add_employees').val() == null) {
			audio_error.play();
			openErrorGritter('Error!','Pilih Karyawan');
			return false;
		}

		var str = $('#add_employees').val();

		var employee_id = str.split('_')[0];
		var name = str.split('_')[1];

		if($.inArray(employee_id, employees) != -1){
			audio_error.play();
			openErrorGritter('Error!','Karyawan sudah ada di list.');
			return false;
		}

		var tableEmployee = "";

		tableEmployee += "<tr id='"+employee_id+"'>";
		tableEmployee += "<td style='padding:5px'>"+employee_id+"</td>";
		tableEmployee += "<td style='padding:5px'>"+name+"</td>";
		tableEmployee += "<td style='padding:5px;text-align:center'><a href='javascript:void(0)' onclick='remEmployee(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableEmployee += "</tr>";

		employees.push(employee_id);
		count += 1;

		$('#countTotal').text(count);
		$('#tableEmployeeBody').append(tableEmployee);
		$('#add_employees').val('').trigger('change');
	}

	function remEmployee(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotal').text(count);
		$('#'+id).remove();	
	}

	function addCode() {
		if ($('#add_certificate_code_non').val() == "" || $('#add_certificate_code_non').val() == null) {
			audio_error.play();
			openErrorGritter('Error!','Pilih Sertifikat');
			return false;
		}

		var str = $('#add_certificate_code_non').val();
		var employee_id = str.split('_')[2];
		var name = str.split('_')[3];
		var certificate_code = str.split('_')[1];
		var certificate_id = str.split('_')[0];

		if(code.includes(certificate_id)){
			audio_error.play();
			openErrorGritter('Error!','Sertifikat sudah ada di list.');
			return false;
		}

		var tableCode = "";

		tableCode += "<tr id='"+certificate_id+"'>";
		tableCode += "<td style='padding:5px'>"+employee_id+"</td>";
		tableCode += "<td style='padding:5px'>"+name+"</td>";
		tableCode += "<td style='padding:5px' id='code_"+certificate_id+"'>"+certificate_code+"</td>";
		tableCode += "<td style='padding:5px;text-align:center'><a href='javascript:void(0)' onclick='remCode(id)' id='"+certificate_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableCode += "</tr>";

		code.push(certificate_id);
		count_non += 1;

		$('#countTotalNon').text(count_non);
		$('#tableCodeBody').append(tableCode);
		$('#add_certificate_code_non').val('').trigger('change');
	}

	function remCode(id){
		code.splice( $.inArray(id), 1 );
		count_non -= 1;
		$('#countTotalNon').text(count_non);
		$('#'+id).remove();	
	}

	function createRequest() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var reason = $('#add_reason').val();
			var request_date = $('#add_date').val();
			var certificate_name = $('#add_certificate_name').val();

			if (reason == '' || request_date == '' || certificate_name == '' || employees.length == 0) {
				$('#loading').hide();
				openErrorGritter('Error!','Masukkan Semua Data');
				audio_error.play();
				return false;
			}

			var data = {
				reason:reason,
				request_date:request_date,
				certificate_name:certificate_name,
				employees:employees,
				request_type:'new'
			}

			$.post('{{ url("input/submission/qa/certificate") }}', data, function(result, status, xhr){
				if(result.status){
					clearAll();
					$("#create_modal_new").modal('hide');
					fillList();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Sukses Membuat Pengajuan Sertifikat Baru.');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function createRequestNon() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var reason = $('#add_reason_non').val();
			var request_date = $('#add_date_non').val();
			var certificate_name = $('#add_certificate_name_non').val();

			if (reason == '' || request_date == '' || certificate_name == '' || code.length == 0) {
				$('#loading').hide();
				openErrorGritter('Error!','Masukkan Semua Data');
				audio_error.play();
				return false;
			}

			var certificate_code = [];

			for(var i = 0; i < code.length; i++){
				certificate_code.push($('#code_'+code[i]).text());
			}

			var data = {
				reason:reason,
				request_date:request_date,
				certificate_name:certificate_name,
				certificate_id:code,
				certificate_code:certificate_code,
				request_type:'deactivate',
			}

			$.post('{{ url("input/submission/qa/certificate") }}', data, function(result, status, xhr){
				if(result.status){
					clearAll();
					$("#create_modal_deactivate").modal('hide');
					fillList();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Sukses Membuat Pengajuan Sertifikat Nonaktif.');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return day + '-' + monthNames[month] + '-' + year +'<br>'+ hour +':'+ minute +':'+ second;
    }

	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection