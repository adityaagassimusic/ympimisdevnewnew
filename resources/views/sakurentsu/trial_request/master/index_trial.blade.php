@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
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

	.btn { margin: 2px; }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
		<button class="btn btn-success pull-right" onclick="newData('new')"><i class="fa fa-plus"></i> Create Trial Request</button>
	</ol>
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
		<h4><i class="icon fa fa-exclamation"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center style="position: absolute; top: 45%; left: 35%;">
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-refresh"></i> &nbsp; Please Wait ...</span>
			</center>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<center><h3 style="margin-top: 10px; margin-bottom: 0px">Sakurentsu - Trial Request <span class="text-purple">作連通 - 試作依頼</span></h3></center>
							<table class="table table-bordered" style="width: 100%" id="master">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Sakurentsu Number</th>
										<th>Title</th>
										<th>Applicant</th>
										<th style="width: 3%">Target Date</th>
										<th style="width: 3%">Upload Date</th>
										<th style="width: 1%">File</th>
										<th style="width: 1%">Status</th>
										<th style="width: 5%">Action</th>
									</tr>
								</thead>
								<tbody id="body_master"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">Trial Request List<span class="text-purple"> ??</span></span></h3>
				</div>
				<div class="box-body">
					<table id="listTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Submission Date</th>
								<th>Form Number</th>
								<th>Subject</th>
								<th>Requester</th>
								<th>Trial To</th>
								<th>Trial Date</th>
								<th>Reference No</th>
								<th>Position</th>
								<th>QC Report</th>
								<th>Report</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="listTableBody">
						</tbody>
						<tfoot>
							<tr>								
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog" style="width: 90%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center style="background-color: #00a65a;"><h3 style="font-weight: bold; padding: 20px;margin:0; color: white" id="modalNewTitle"></h3></center>
				<div class="row" style="margin-top: 10px">
					<input type="hidden" id="id_edit">
					<div class="col-md-6" style="padding-right: 0">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
							<label for="invoice_date" class="col-sm-3 control-label">Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" value="<?= date('d M Y') ?>" placeholder="Submission Date" disabled>
									<input type="hidden" class="form-control pull-right datepicker" id="submission_date" name="submission_date" value="<?= date('Y-m-d') ?>" placeholder="Submission Date">
								</div>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
							<label for="requester" class="col-sm-3 control-label">Requester<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" placeholder="Requester" value="{{$emp->employee_id}} - {{$emp->name}}" readonly="">
								<input type="hidden" class="form-control" id="requester" name="requester" placeholder="Requester" value="{{$emp->employee_id}}">
								<input type="hidden" class="form-control" id="requester_name" name="requester_name" placeholder="Requester Name" value="{{$emp->name}}">
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
							<label for="reference_no" class="col-sm-3 control-label">Reference No</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="Reference Number">
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
							<label for="apd" class="col-sm-3 control-label">Total APD/Material</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="apd" name="pad" placeholder="Total APD / Material">
							</div>
						</div>

					</div>
					<div class="col-md-6" style="padding-left: 0">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
							<label for="subject" class="col-sm-3 control-label">Subject<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="subject" name="subject" placeholder="Trial Request Subject">	
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
							<label for="department" class="col-sm-3 control-label">Department<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="department" name="department" data-placeholder='Department To' style="width: 100%" onchange="select_main_section()">
									<option value="">&nbsp;</option>
									@foreach($dept as $dp)
									<option value="{{ $dp->department }}">{{ $dp->department }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
							<label for="department" class="col-sm-3 control-label">Section<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="section" name="section" data-placeholder='Section To' style="width: 100%">
									<option value="">&nbsp;</option>
								</select>
							</div>
						</div>					

						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
							<label for="do_date" class="col-sm-3 control-label">Trial Date</label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="trial_date" name="trial_date" placeholder="Trial Date">
								</div>
							</div>
						</div>

					</div>

					<div class="col-md-12" style="padding-right:0px">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<hr style="margin-bottom: 10px; margin-top: 10px">
							<label for="trial_purpose" class="col-sm-12 control-label">Trial Purpose<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<textarea class="form-control" id="trial_purpose" name="trial_purpose" placeholder="Tujuan Dilakukan Trial"></textarea>
							</div>
						</div>
					</div>

					<div class="col-md-6" style="padding-right:0px">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
							<label for="kondisi_sebelum" class="col-sm-12 control-label">Condition Before<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<textarea class="form-control" id="kondisi_sebelum" name="kondisi_sebelum"></textarea>
							</div>
						</div>
					</div>

					<div class="col-md-6" style="padding-right:0px">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
							<label for="trial" class="col-sm-12 control-label">Trial Condition<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<textarea class="form-control" id="trial" name="trial" placeholder="Trial"></textarea>
							</div>
						</div>
					</div>

					<div class="col-md-7" style="padding-right:0px">
						<div class="col-md-10" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							
							<label for="trial_location" class="col-sm-12 control-label">Location<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="trial_location" name="trial_location" placeholder="Lokasi Trial">
							</div>
						</div>
					</div>

					<div class="col-md-7" style="padding-right:0px">
						<div class="col-md-10" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							
							<label for="att" class="col-sm-12 control-label">Lampiran</label>
							<div class="col-sm-12">
								<input type="file" name="att[]" id="att" multiple="">
								<br>
							</div>
						</div>
					</div>

					<div class="col-md-12" style="padding-right:0px">
						<div class="col-md-2" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<div class="col-sm-12">
								<button type="button" class="btn btn-md btn-success" onclick="add_mat()"><i class="fa fa-plus"></i> Add Material</button>
							</div>
						</div>
					</div>
					
					
					<div class="col-md-6" style="padding-right:0px">
						<div class="col-md-12">
							<table style="width: 100%; margin-left: 10px; margin-right: 10px">
								<thead>
									<tr>
										<td style="width: 65%"><label class="col-sm-12 control-label">Material<span class="text-red">*</span></label></td>
										<td style="width: 15%"><label class="col-sm-12 control-label">Quantity<span class="text-red">*</span></label></td>
										<td><label class="col-sm-12 control-label">Delete</label></td>
									</tr>
								</thead>
								<tbody id="body_mat">
									<tr>
										<td style="padding-right: 10px"><input type="text" class="form-control mat" placeholder="Material"></td>
										<td style="padding-left: 10px"><input type="text" class="form-control qty" placeholder="Quantity"></td>
										<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- <div class="col-md-6" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label for="material1" class="col-sm-12 control-label">Material<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="material1" name="material1" placeholder="Material"></textarea>
							</div>
						</div>

						<div class="col-md-4" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<label for="jumlah1" class="col-sm-12 control-label">Jumlah<span class="text-red">*</span></label>
							<div class="col-sm-12">
								<input type="text" class="form-control" id="jumlah1" name="jumlah1" placeholder="Contoh : 2 Pcs"></textarea>
							</div>
						</div> -->
					</div>

					

					<div class="col-md-12" style="padding-right:0px">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							
							<label for="trial_info" class="col-sm-12 control-label">Description / Specification</label>
							<div class="col-sm-12">
								<textarea class="form-control" id="trial_info" placeholder="Keterangan / Spesifikasi"></textarea>
							</div>
						</div>
					</div>

					<div class="col-md-12" style="padding-right:0px">
						<div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
							<span style="font-weight: bold; font-size: 16px;"><center style="background-color: #00a65a; line-height: 2">RECEIVER TRIAL REQUEST</center></span>
							<div class="col-sm-12">
								<br>
								<button class="btn btn-success" onclick="add_penerima()"><i class="fa fa-plus"></i>&nbsp; Add Receiver Trial</button>
								<table class="table" style="width: 80%">
									<thead>
										<tr>
											<th style="width: 40%">Department</th>
											<th style="width: 40%">Section</th>
											<th style="width: 20%">Delete</th>
										</tr>
									</thead>
									<tbody id="body_penerima">
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-md-12" style="margin-top: 5px;">
						<a class="btn btn-success pull-right" onclick="SaveTrial('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton"><i class="fa fa-check"></i> CREATE</a>
						<a class="btn btn-info pull-right" onclick="SaveTrial('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalFile">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Lampiran Trial Request</h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableFile">
						<tbody id='bodyFile'></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #00a65a">
				<center><h2 style="margin: 0px"><b>Send Result Notification</b></h2></center>
			</div>
			<div class="modal-body">
				<input type="hidden" id="email_form_id">
				<input type="hidden" id="trial_sakurentsu_number">
				<center style="font-size: 18px"><b><i class="fa fa-book"></i> <span id="trial_number_mail"></span></b></center>
				<!-- <br>
				<label>3M Requirement<span class="text-red">*</span> : </label>
				<label class="radio-inline">
					<input type="radio" name="three_m_requirement" value="Need 3M">Need 3M
				</label>
				<label class="radio-inline">
					<input type="radio" name="three_m_requirement" value="No Need 3M">No Need 3M
				</label> -->
				<br>
				<label>Select PIC to fill Trial Result : </label>&nbsp;
				<button class="btn btn-success btn-xs" id="add_pic" onclick="add_pic()"><i class="fa fa-plus"></i> Add PIC</button>
				<table class="table table-striped" id="table_pic">
					<thead>
						<tr>
							<th><center>PIC</center></th>
							<th style="width: 10%"><center>#</center></th>
						</tr>
					</thead>
					<tbody id="body_pic"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="sendMail()"><i class="fa fa-send"></i> Send</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="uploadQCMOdal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_qc">
				<div class="modal-header" style="background-color: #00a65a">
					<center><h2 style="margin: 0px"><b>Upload QC Report</b></h2></center>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<input type="hidden" id="qc_form_id" name="qc_form_id">
							<center style="font-size: 18px"><b><i class="fa fa-book"></i> <span id="trial_number_qc"></span></b></center>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-4" style="padding: 0px;" align="right">
								<span style="font-weight: bold; font-size: 16px;">QC Report File : <span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<input type="file" id="qc_report_file" name="qc_report_file" accept="application/pdf">
								<input type="hidden" name="qc_report_status" id="qc_report_status" value="OK">
							</div>
						</div>

						<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-4" style="padding: 0px;" align="right">
								<span style="font-weight: bold; font-size: 16px;">QC Report Status : <span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6" id="div_qc_report">
								<select class="form-control select3" id="qc_report_status" name="qc_report_status" data-placeholder='Select Report Status' style="width: 100%">
									<option value=""></option>
									<option value="Approval">Approval</option>
									<option value="OK">OK</option>
									<option value="Not OK">Not OK</option>
								</select>
							</div>
						</div> -->
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" type="submit"><i class="fa fa-upload"></i> Upload</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modalThreeM" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: #00a65a">
				<center><h2 style="margin: 0px"><b>Determine 3M Status</b></h2></center>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<input type="hidden" id="three_m_form_id" name="three_m_form_id">
						<input type="hidden" id="three_m_reff_num" name="three_m_reff_num">
						<center style="font-size: 18px"><b><i class="fa fa-book"></i> <span id="three_m_id"></span></b></center>
					</div>

					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" style="padding: 0px;" align="right">
							<span style="font-weight: bold; font-size: 16px;">3M Status : <span class="text-red">*</span></span>
						</div>
						<div class="col-xs-6">
							<select class="form-control select2" id="three_m_status" name="three_m_status" data-placeholder='Select 3M Status' style="width: 100%">
								<option value=""></option>
								<option value="Need 3M">Need 3M</option>
								<option value="No Need 3M">No Need 3M</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-success" onclick="determine_three_m()"><i class="fa fa-check"></i> Submit</button>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("ckeditor/ckeditor.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var file = [];

	var dept = <?php echo json_encode($dept); ?>;

	var section = <?php echo json_encode($section); ?>;

	var pic_arr = <?php echo json_encode($pic); ?>;

	var no_penerima = 1;
	
	var role = "{{ Auth::user()->role_code }}";

	jQuery(document).ready(function() {

		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		$('.select4').select2({
			allowClear: true,
			dropdownParent: $('#modalNew'),
		});

		$('.select3').select2({
			dropdownAutoWidth : true,
			allowClear: true,
			dropdownParent: $('#div_qc_report'),
		});

		fetchTable();
	});


	$('.select5').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#modalNew'),
	});


	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#modalThreeM'),
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');


	CKEDITOR.replace('kondisi_sebelum' ,{
		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'		
	});

	CKEDITOR.replace('trial' ,{
		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	});

	CKEDITOR.replace('trial_purpose' ,{
		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
		toolbar: [
		{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
		{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] },
		{ name: 'insert', items: [ 'Image' ] },
		{ name: 'tools', items: [ 'Maximize' ] }
		],
		height:100
	});

	function select_main_section() {
		if ($("#department").val() != '') {
			$("#section").empty();
			var sec = "";

			sec += "<option value=''></option>";
			$.each(section, function(key, value) {
				if (value.department == $("#department").val()) {
					sec += "<option value='"+value.section+"'>"+value.section+"</option>";
				}
			})
			$("#section").append(sec);

			// $('.select4').select2({
			// 	allowClear: true,
			// 	dropdownParent: $('#modalNew'),
			// });
		}
	}

	function fetchTable(){
		$('#loading').show();

		$.get('{{ url("fetch/trial_request") }}', function(result, status, xhr){
			if(result.status){
				// ----------- SAKURENTSU LIST
				
				$('#master').DataTable().clear();
				$('#master').DataTable().destroy();
				$("#body_master").empty();
				body = "";

				$.each(result.sk_trial, function(key, value) {
					body += "<tr>";
					body += "<td>"+value.sakurentsu_number+"</td>";
					body += "<td>"+value.title+"</td>";
					body += "<td>"+value.applicant+"</td>";
					body += "<td>"+value.target_date+"</td>";
					body += "<td>"+value.upload_date+"</td>";
					body += "<td><center>"+('<button class="btn btn-xs" onclick="getFileInfo('+key+',\''+value.sakurentsu_number+'\')"><i class="fa fa-paperclip"></i></button>' || '')+"</center></td>";
					body += "<td>"+value.status+"</td>";
					body += "<td><button class='btn btn-xs btn-success' onclick='createModal(\""+value.sakurentsu_number+"\")'><i class='fa fa-plus'></i> Issue Trial</button></button></td>";
					body += "</tr>";

					file.push({'sk_number' : value.sakurentsu_number, 'file' : value.file_translate});
					
				})
				$("#body_master").append(body);

				var table = $('#master').DataTable({
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
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				// ----------- INTERNAL TRIAL REQUEST

				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";

				$.each(result.trial, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td style="width:1%;">'+value.submission_date+'</td>';
					listTableBody += '<td style="width:1%;">'+value.form_number+'</td>';
					listTableBody += '<td style="width:3%;">'+value.subject+'</td>';
					listTableBody += '<td style="width:2%;">'+value.requester_name+'</td>';
					listTableBody += '<td style="width:5%;">'+value.department+'</td>';
					listTableBody += '<td style="width:3%;">'+value.trial_date+'</td>';
					listTableBody += '<td style="width:3%;">'+(value.sakurentsu_number || '')+'</td>';
					listTableBody += '<td style="width:3%;">'+value.status+'</td>';

					listTableBody += '<td style="width:2%; font-weight: bold; text-align: center; font-size: 20px">'+(value.qc_report_status || '')+'</td>';

					listTableBody += '<td style="width:2%;"><center>';
					// listTableBody += '<button class="btn btn-xs btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-eye"></i> Detail</button>';
					listTableBody += '  <a class="btn btn-xs btn-danger" target="_blank" href="{{ url("uploads/sakurentsu/trial_req/report") }}/Report_'+value.form_number+'.pdf"><i class="fa fa-file-pdf-o"></i> Report</a>';

					if (value.att) {
						listTableBody += '  <button class="btn btn-xs btn-danger" onclick="openModalFile(\''+value.att+'\')"><i class="fa fa-file-pdf-o"></i> Lampiran</button>';
					}

					if(value.sakurentsu_number) {

						listTableBody += '  <button class="btn btn-xs btn-danger" onclick="openModalFileSk(\''+value.form_number+'\',\''+value.file.replace(/"/g, "")+'\',\''+value.file_translate.replace(/"/g, "")+'\')"><i class="fa fa-file-pdf-o"></i> Sakurentsu File</button>';
					}

					listTableBody += '</center></td>';


					listTableBody += '<td style="width:2%;"><center>';

					if (value.count_result >= 0 && value.status == 'received') {
						if (result.dept_status == '') {
							listTableBody += '<button class="btn btn-xs btn-success" onclick="modal_mail_result(\''+value.form_number+'\',\''+value.sakurentsu_number+'\')"><i class="fa fa-envelope-o"></i> Send Result Notif</button>';
						}
					}

					if (value.status == 'received' || value.status == 'reporting') {
						listTableBody += '<button class="btn btn-xs btn-danger" onclick="modal_qc(\''+value.form_number+'\',)"><i class="fa fa-upload"></i> Upload QC Report</button>';
					}

					if (value.status == '3m_need') {
						if (result.dept_status == '') {
							listTableBody += '<button class="btn btn-xs btn-primary" onclick="modal_three_em(\''+value.form_number+'\',\''+value.sakurentsu_number+'\')"><i class="fa fa-hand-pointer-o"></i> Determine 3M Status</button>';
						}
					}

					if (value.status == '3M') {
						listTableBody += '<a class="btn btn-xs btn-success" href="{{ url("index/sakurentsu/3m_trial") }}/'+value.form_number+'"><i class="fa fa-plus"></i> issue 3M</a>';
					}

					if (value.status == 'approval' && ~role.indexOf("MIS")) {
						listTableBody += '<button class="btn btn-xs btn-primary" onclick="resend(\'resend/sakurentsu/trial_request/'+value.form_number+'\')"><i class="fa fa-envelope"></i> Resend Mail</button>';
					}

					if (value.status == 'receiving' && ~role.indexOf("MIS")) {
						listTableBody += '<button class="btn btn-xs btn-primary" onclick="resend(\'resend/sakurentsu/trial_request_receive/'+value.form_number+'\')"><i class="fa fa-envelope"></i> Resend Receive Mail</button>';

					}

					if (~role.indexOf("MIS")) {
						listTableBody += '<a class="btn btn-xs btn-danger" target="_blank" href="{{ url("report/sakurentsu/trial_request/issue/") }}/'+value.form_number+'"><i class="fa fa-refresh"></i> Generate Pdf</a>';
					}

					listTableBody += '</center></td>';

					listTableBody += '</tr>';
				});

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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
					'pageLength': 20,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
}

function newData(id){
	$('#modalNewTitle').text('Create Trial Request');
	$('#newButton').show();
	$('#updateButton').hide();
	clearNew();
	$('#modalNew').modal('show');	
}

function SaveTrial(id){
	$('#loading').show();

	if(id == 'new'){
		if($("#submission_date").val() == "" || $('#subject').val() == null || $('#department').val() == null || CKEDITOR.instances.kondisi_sebelum.getData() == ""  || CKEDITOR.instances.trial.getData() == "" || $('#requester').val() == "" || CKEDITOR.instances.trial_purpose.getData() == "" || $('#material1').val() == "" || $('#jumlah1').val() == "" || $('#trial_location').val() == ""){

			$('#loading').hide();
			openErrorGritter('Error', "Please fill field with (*) sign.");
			return false;
		}

		var mat_arr = [];
		$('.mat').each(function(index, value) {
			mat_arr.push($(this).val());
		});

		var qty_arr = [];
		$('.qty').each(function(index, value) {
			qty_arr.push($(this).val());
		});

		var dept_arr = [];
		$('.dept').each(function(index, value) {
			dept_arr.push($(this).val());
		});

		var sec_arr = [];
		$('.sec').each(function(index, value) {
			sec_arr.push($(this).val());
		});


		var formData = new FormData();
		formData.append('submission_date', $("#submission_date").val());
		formData.append('subject', $("#subject").val());
		formData.append('department', $("#department").val());
		formData.append('section', $("#section").val());
		formData.append('kondisi_sebelum', CKEDITOR.instances.kondisi_sebelum.getData());
		formData.append('requester', $("#requester").val());
		formData.append('requester_name', $("#requester_name").val());
		formData.append('trial_date', $("#trial_date").val());
		formData.append('reference_no', $("#reference_no").val());
		formData.append('apd', $("#apd").val());
		formData.append('trial', CKEDITOR.instances.trial.getData());
		formData.append('trial_purpose', CKEDITOR.instances.trial_purpose.getData());
		formData.append('trial_location', $("#trial_location").val());

		var att_count = 0;
		for (var i = 0; i < $('#att').prop('files').length; i++) {
			formData.append('att_'+i, $('#att').prop('files')[i]);
			att_count++;
		}

		formData.append('att_count', att_count);
		formData.append('trial_info', $("#trial_info").val());

		formData.append('material', mat_arr);
		formData.append('jumlah', qty_arr);

		formData.append('department_receive', dept_arr);
		formData.append('section_receive', sec_arr);

		$.ajax({
			url:"{{ url('create/trial_request') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success', data.message);
					audio_ok.play();
					$('#loading').hide();
					$('#modalNew').modal('hide');
					clearNew();
					fetchTable();
				}else{
					openErrorGritter('Error!',data.message);
					$('#loading').hide();
					audio_error.play();
				}

			}
		});
	}
	else{
		if($("#submission_date").val() == "" || $('#subject').val() == null || $('#department').val() == null || CKEDITOR.instances.kondisi_sebelum.getData() == ""  || CKEDITOR.instances.trial.getData() == "" || $('#requester').val() == "" || CKEDITOR.instances.trial_purpose.getData() == "" || $('#material1').val() == "" || $('#jumlah1').val() == "" || $('#trial_location').val() == ""){

			$('#loading').hide();
			openErrorGritter('Error', "Please fill field with (*) sign.");
			return false;
		}

		var formData = new FormData();
		formData.append('id_edit', $("#id_edit").val());
		formData.append('submission_date', $("#submission_date").val());
		formData.append('subject', $("#subject").val());
		formData.append('department', $("#department").val());
		formData.append('section', $("#section").val());
		formData.append('kondisi_sebelum', CKEDITOR.instances.kondisi_sebelum.getData());
		formData.append('requester', $("#requester").val());
		formData.append('requester_name', $("#requester_name").val());
		formData.append('trial_date', $("#trial_date").val());
		formData.append('reference_no', $("#reference_no").val());
		formData.append('trial', CKEDITOR.instances.trial.getData());
		formData.append('trial_purpose', CKEDITOR.instances.trial_purpose.getData());		
		formData.append('material', $("#material1").val());
		formData.append('jumlah', $("#jumlah1").val());
		formData.append('trial_location', $("#trial_location").val());
		formData.append('trial_info', $("#trial_info").val());

		$.ajax({
			url:"{{ url('edit/trial_request') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success', data.message);
					audio_ok.play();
					$('#loading').hide();
					$('#modalNew').modal('hide');
					clearNew();
					fetchTable();
				}else{
					openErrorGritter('Error!',data.message);
					$('#loading').hide();
					audio_error.play();
				}

			}
		});
	}
}

function clearNew(){
	$('#id_edit').val('');
	$("#subject").val('');
	$("#department").val('').trigger('change');
	$('#kondisi_sebelum').html(CKEDITOR.instances.kondisi_sebelum.setData(""));
	$('#trial_date').val('');
	$('#reference_no').val("");
	$('#trial').html(CKEDITOR.instances.trial.setData(""));
	$('#trial_purpose').html(CKEDITOR.instances.trial_purpose.setData(""));
	$('#material1').val('');
	$('#jumlah1').val("");
	$('#trial_location').val("");
	$('#trial_info').val("");

}

function getFileInfo(num, sk_num) {
	$("#sk_num").text(sk_num+" File(s)");

	$("#bodyFile").empty();

	body_file = "";
	$.each(file, function(key, value) {  
		if (sk_num == value.sk_number) {
			var obj = JSON.parse(value.file);
			var app = "";

			if (obj) {
				for (var i = 0; i < obj.length; i++) {
					body_file += "<tr>";
					body_file += "<td>";
					body_file += "<center><a href='{{ url('files/translation/') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a></center>";
					body_file += "</td>";
					body_file += "</tr>";
				}
			}
		}
	});

	$("#bodyFile").append(body_file);

	$("#modalFile").modal('show');
}

function openModalFileSk(form_number, sk_file, sk_translate_file) {
	$("#sk_num").text(form_number+" File(s)");

	$("#bodyFile").empty();

	body_file = "";

	var obj = [];
	var fil = sk_translate_file.replace('[', "");
	fil = fil.replace(']', "");

	obj = fil.split(',');
	var app = "";

	if (obj) {
		for (var i = 0; i < obj.length; i++) {
			body_file += "<tr>";
			body_file += "<td>";
			body_file += "<center><a href='{{ url('files/translation/') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a></center>";
			body_file += "</td>";
			body_file += "</tr>";
		}
	}

	$("#bodyFile").append(body_file);

	$("#modalFile").modal('show');
}

function createModal(sk_num) {
	$('#modalNewTitle').text('Create Trial Request');
	$('#newButton').show();
	$('#updateButton').hide();
	clearNew();
	$("#reference_no").val(sk_num);
	$('#modalNew').modal('show');
}

function add_mat() {
	var body = "";

	body += '<tr>';
	body += '<td style="padding-right: 10px"><input type="text" class="form-control mat" placeholder="Material"></td>';
	body += '<td style="padding-left: 10px"><input type="text" class="form-control qty" placeholder="Quantity"></td>';
	body += '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
	body += '</tr>';

	$("#body_mat").append(body);
}

function deleteMat(elem) {
	$(elem).closest('tr').remove();
}

function add_penerima() {
	var body = "";

	var option_dept = "";

	$.each(dept, function(key, value) { 
		option_dept += "<option value='"+value.department+"'>"+value.department+"</option>" ;
	})

	body += '<tr id="tr_'+no_penerima+'">';
	body += '<td style="padding-right: 10px"><select type="text" class="form-control select5 dept" data-placeholder="Pilih Departemen" onchange="select_section(this,'+no_penerima+')" id="dept_'+no_penerima+'"><option value=""></option>'+option_dept+'</select></td>';
	body += '<td style="padding-left: 10px"><select class="form-control select5 sec" id="sec_'+no_penerima+'" data-placeholder="Pilih Section"><option value=""></option></select></td>';
	body += '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
	body += '</tr>';

	$("#body_penerima").append(body);

	$('.select5').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#tr_'+no_penerima),
	});

	no_penerima++;
}

function select_section(elem, no) {
	var isi = $(elem).val();
	var option_sec = "";

	$('#sec_'+no).empty();
	$('#sec_'+no).append("<option value=''></option>");

	$.each(section, function(key, value) { 
		if (value.department == isi) {
			option_sec += "<option value='"+value.section+"'>"+value.section+"</option>" ;
		}
	});

	$('#sec_'+no).append(option_sec);
}

function modal_mail_result(form_number, sakurentsu_number) {
	$("#sendMailModal").modal('show');
	$("#trial_number_mail").text(form_number);
	$("#trial_sakurentsu_number").val(sakurentsu_number);
}

function sendMail() {
	// if (!$("input[name='three_m_requirement']:checked").val()) {
	// 	openErrorGritter('Error','Please Select 3M Requirement');
	// 	return false;
	// }

	var pic = [];

	$('.pic').each(function(key, value) {
		pic.push($(this).val());
	});


	var data = {
		form_number : $("#trial_number_mail").text(),
		sakurentsu_number : $("#trial_sakurentsu_number").val(),
		three_m_requirement : $('input[name="three_m_requirement"]:checked').val(),
		pic_arr : pic
	}

	$.post('{{ url("send/trial_request/trial_result") }}', data, function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success', 'Data Successfully sent');
			audio_ok.play();
			$("#sendMailModal").modal('hide');
		}
	})
}

function modal_qc(form_number, sakurentsu_number) {
	$("#uploadQCMOdal").modal('show');
	$("#qc_form_id").val(form_number);
	$("#trial_number_qc").text(form_number);
	$("#trial_sakurentsu_number").val(sakurentsu_number);
}

$("form#form_qc").submit(function(e) {
	$("#loading").show();

	if( document.getElementById("qc_report_file").files.length == 0 ){
		openErrorGritter('Error', 'No files selected');
		audio_error.play();
		$("#loading").hide();
		return false;
	}

	e.preventDefault();    
	var formData = new FormData(this);

	$.ajax({
		url: '{{ url("upload/trial_request/qc_report") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

			$('#uploadQCMOdal').modal('hide');

			openSuccessGritter('Success', result.message);
			audio_ok.play();

			fetchTable();
		},
		error: function(result, status, xhr){
			$("#loading").hide();

			openErrorGritter('Error!', result.message);
			audio_error.play();
		},
		cache: false,
		contentType: false,
		processData: false
	});
});

// function uploadQC() {
// 	var data = {
// 		form_number : $("#trial_number_mail").text()
// 	}

// 	$.post('{{ url("send/trial_request/trial_result") }}', data, function(result, status, xhr){
// 		if(result.status){
// 			openSuccessGritter('Success', 'Email Successfully sent');
// 			$("#sendMailModal").modal('hide');
// 		}
// 	})
// }

function add_pic() {
	var pic = '';

	pic += '<tr>';
	pic += '<td>';
	pic += '<select class="form-control select6 pic" data-placeholder="Select PIC to Fill Trial Result">';
	pic += "<option value=''></option>";

	$.each(pic_arr, function(key, value) { 
		pic += "<option value='"+value.employee_id+"/"+value.name+"'>"+value.employee_id+" - "+value.name+"</option>";
	});

	pic += '</select>';
	pic += '<td>';
	pic += '<td><center><button class="btn btn-danger" onclick="deleteMat(this)"><i class="fa fa-minus"></i></center></td>';
	pic += '</tr>';

	$("#body_pic").append(pic);

	$('.select6').select2({
		dropdownAutoWidth : true,
		allowClear: true,
		dropdownParent: $('#sendMailModal'),
	});
}

function modal_three_em(form_number, reff_num) {
	$('#three_m_form_id').val(form_number);
	$('#three_m_reff_num').val(reff_num);
	$('#three_m_id').text(form_number);
	$('#modalThreeM').modal('show');
}

function determine_three_m() {
	if(confirm('Are you sure want to submit this 3M requirement and send email notification to related department?')) {
		if ($("#three_m_status").val() == '') {
			openErrorGritter('Error', 'Please Select 3M Status');
			audio_error.play();
			return false;
		}

		$("#loading").show();
		var data = {
			form_id : $("#three_m_form_id").val(),
			reff_num : $('#three_m_reff_num').val(),
			three_m_status : $("#three_m_status").val()
		}

		$.post('{{ url("post/trial_request/three_m_status") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Data Successfully Updated');
				audio_ok.play();
				$("#modalThreeM").modal('hide');
				fetchTable();
			}
		})
	}

}

function resend(url) {
	if (confirm('Are you sure want to re send Email Approval?')) {
		$("#loading").show();
		$.post('{{ url("/") }}/'+url, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Email has been sent');
				audio_ok.play();
			} else {
				openErrorGritter('Error', result.message);
				audio_error.play();
			}
		})
	}
}

function openModalFile(file) {
	$("#modalFile").modal('show');
	$("#bodyFile").empty();

	if (file != 'null') {
		var obj = file.split(',');
		var body_file = '';

		for (var i = 0; i < obj.length; i++) {
			body_file += "<tr>";
			body_file += "<td>";
			body_file += "<center><a href='{{ url('uploads/sakurentsu/trial_req/att') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> "+obj[i]+"</a></center>";
			body_file += "</td>";
			body_file += "</tr>";
		}

		$("#bodyFile").append(body_file);
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

</script>
@endsection

