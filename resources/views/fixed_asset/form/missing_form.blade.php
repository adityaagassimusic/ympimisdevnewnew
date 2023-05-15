@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.datepicker {
		padding: 6px 12px 6px 12px;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of {{$page}}
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Missing Asset.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Missing Report</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Report Missing</a>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 10%">Section Control</th>
								<th style="width: 10%">Registration Date</th>
								<th style="width: 10%">Status</th>
								<th style="width: 5%">Report</th>
								<th style="width: 5%">Action</th>
							</tr>
						</thead>
						<tbody id="masterBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	
	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Missing Asset Report</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Request Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-4">
										<input type="text" class="form-control" id="request_date" name="request_date" placeholder="Section Control" value="{{ date('Y-m-d') }}" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="hidden" id="asset_name" name="asset_name">
										<select class="form-control select2" id="asset_id" name="asset_id" data-placeholder="Select Asset" style="width: 100%" onchange="pilihAsset(this)">
											<option value=""></option>
											@foreach($asset_list as $al)
											<option value="{{ $al->sap_number }}">{{ $al->sap_number }} - {{ $al->fixed_asset_name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_cls" name="asset_cls" placeholder="fixed asset Clasification" readonly>
										<input type="hidden" id="asset_pic" name="asset_pic">
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="section" name="section" placeholder="Section Control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="file" id="asset_picture" name="asset_picture" accept="image/*">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason" name="disposal_reason" placeholder="Disposal Reason"></textarea>
									</div>
								</div>

								<div class="col-xs-12">
									<br>
									<center style="background-color: #00a65a; font-size: 16px; font-weight: bold"><label>- - - - - - - - - - - - &nbsp;  IMPROVEMENT STATEMENT &nbsp; - - - - - - - - - - - -</label></center>
									<br>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Missing Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="missing_reason" name="missing_reason" placeholder="Missing Reason"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Improvement Plan : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="improvement_plan" name="improvement_plan" placeholder="Improvement Plan"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-right: 12%;">
									<br>
									<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;&nbsp;&nbsp;&nbsp;Note :&nbsp;&nbsp;&nbsp;</span><br>
									<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;- Tanda bintang (*) wajib diisi&nbsp;&nbsp;</span><br><br>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Submit </button></center>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalFill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #f39c12;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Missing Asset Report</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master_fill">
							<div class="row">

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Form Number : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="form_number_fill" name="form_number_fill" readonly>
									</div>
								</div>
								
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Request Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="request_date_fill" name="request_date_fill" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="asset_name_fill" name="asset_name_fill" readonly>
										<input type="hidden" id="id_fill" name="id_fill">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : </label>
									</div>
									<div class="col-xs-6">
										<a href="#" id="asset_picture_fill"><i class="fa fa-file-image-o"></i> Asset Picture</a>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no_fill" name="asset_no_fill" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_cls_fill" name="asset_cls_fill" placeholder="fixed asset Clasification" readonly>
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="section_fill" name="section_fill" placeholder="Section Control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_fill" name="disposal_reason_fill" readonly></textarea>
									</div>
								</div>

								<div class="col-xs-12">
									<br>
									<center style="background-color: #00a65a; font-size: 16px; font-weight: bold"><label>- - - - - - - - - - - - &nbsp;  IMPROVEMENT STATEMENT &nbsp; - - - - - - - - - - - -</label></center>
									<br>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Missing Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="missing_reason_fill" name="missing_reason_fill" placeholder="Missing Reason" readonly></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Improvement Plan : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="improvement_plan_fill" name="improvement_plan_fill" readonly></textarea>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sx-12" style="padding-bottom: 1%">
									<center style='background-color: #f39c12; font-size: 16px; font-weight: bold;'><label>- - - - - - - - - - - - &nbsp;  FA SECTION &nbsp; - - - - - - - - - - - -</label></center>
									<br>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Acquisition Cost : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="acq_fill" name="acq_fill" placeholder="Acquisition Cost">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Acquisition Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control datepicker" id="acq_date_fill" name="acq_date_fill" placeholder="Acquisition Date">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Net Book Value : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="net_fill" name="net_fill" placeholder="Net Book Value">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-warning" type="submit" id="update_btn"><i class="fa fa-check"></i> Update Report </button></center>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Form Missing Asset</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master_edit">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Form Number : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="form_number_edit" name="form_number_edit" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="asset_name_edit" name="asset_name_edit" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="file" id="asset_picture_edit" name="asset_picture_edit">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no_edit" name="asset_no_edit" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset Clasification : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_cls_edit" name="asset_cls_edit" placeholder="fixed asset Clasification" readonly>
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="section_edit" name="section_edit" placeholder="Section Control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="disposal_reason_edit" name="disposal_reason_edit" ></textarea>
									</div>
								</div>

								<div class="col-xs-12">
									<br>
									<center style="background-color: #3c8dbc; font-size: 16px; font-weight: bold"><label>- - - - - - - - - - - - &nbsp;  IMPROVEMENT STATEMENT &nbsp; - - - - - - - - - - - -</label></center>
									<br>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Missing Reason : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="missing_reason_edit" name="missing_reason_edit" placeholder="Missing Reason"></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Improvement Plan : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<textarea class="form-control" id="improvement_plan_edit" name="improvement_plan_edit"></textarea>
									</div>
								</div>

							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-primary" type="submit" id="edit_btn"><i class="fa fa-check"></i> Save </button></center>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00a65a">
					<center><h2 style="margin: 0px"><b>Send Mail</b></h2></center>
				</div>
				<div class="modal-body">
					<input type="hidden" id="email_form_id">
					<center style="font-size: 18px">Are you sure want to Send Mail to "<b><span id="approval_name"></span></b>" Again?</center>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success pull-left" onclick="sendMail()"><i class="fa fa-check"></i> YES</button>
					<button class="btn btn-danger" data-dismiss='modal'><i class="fa fa-close"></i> NO</button>
				</div>
			</div>
		</div>
	</div>

</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var no = 0;
	var investment_list = [];
	var pic_list = [];
	var asset_list = <?php echo json_encode($asset_list); ?>;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.select2').select2({
			dropdownParent: $('#createModal'),
		})

		$('.select3').select2({
			dropdownParent: $('#modalFill'),
		})

		$('.select4').select2({
			dropdownParent: $('#modalEdit'),
		})

		drawData();

	});

	function drawData() {
		$.get('{{ url("fetch/fixed_asset/missing") }}', function(result, status, xhr){
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			$("#masterBody").empty();
			var body = "";

			$.each(result.missing_datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.created_at+"</td>";
				body += "<td>"+value.fixed_asset_id+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.section_control+"</td>";
				body += "<td>"+(value.acquisition_date || '')+"</td>";
				body += "<td>"+value.status+"</td>";
				body += "<td>";
				body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_missing') }}/Missing_"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";
				body += "</td>";
				body += "<td>";
				if (value.status == 'pic' && value.last_status == 'pic' && ("{{ Auth::user()->role_code }}" == 'MIS' || "{{ Auth::user()->username }}" == 'PI0905001')) {
					body += "<button class='btn btn-warning btn-xs' onclick='openFillModal(\""+value.form_number+"\")'><i class='fa fa-pencil'></i> Fill</button>&nbsp;";
				}
				
				if ("{{ Auth::user()->role_code }}" != 'ACC-SPL') {
					body += "<button class='btn btn-primary btn-xs' onclick='openEditModal(\""+value.form_number+"\")'><i class='fa fa-pencil'></i> Edit</button>&nbsp;";
				}

				if (value.status != 'hold' && value.status != 'reject' && value.status != 'presdir') {
					if ("{{ Auth::user()->role_code }}" != 'ACC-SPL') {
						body += "<button class='btn btn-success btn-xs' onclick='openSendMail(\""+value.form_number+"\",\""+value.status+"\")'><i class='fa fa-send'></i> Send Mail</button>&nbsp;";
					}
				}
				body += "</td>";
				body += "</tr>";
			})

			$("#masterBody").append(body);

			var table = $('#masterTable').DataTable({
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
				"processing": true,
			});

		})
	}

	function pilihAsset(elem) {
		var id_asset = $(elem).val();

		$.each(asset_list, function(index, value){
			if (value.sap_number == id_asset) {
				$("#asset_no").val(id_asset);
				$("#asset_name").val(value.fixed_asset_name);
				$("#asset_cls").val(value.classification_category);
				$("#section").val(value.section);
				$("#asset_pic").val(value.pic);
			}

		})

		// $.get('{{ url("fetch/fixed_asset/transfer_asset/byId") }}', data, function(result, status, xhr){
		// 	$("#asset_no").val(result.data.sap_id);
		// 	$("#asset_name").val(result.data.asset_name);

		// 	$("#section").val(result.data.pic);
		// 	$("#asset_cls").val(result.data.category);
		// 	$("#asset_pic").val(result.data.created_by);
		// })
	}

	$("form#form_master").submit(function(e) {
		if( document.getElementById("asset_picture").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Asset Image');
			return false;
		}

		if($("#disposal_reason").val() == "" || $("#missing_reason").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason');
			return false;
		}

		if($("#improvement_plan").val() == ""){
			openErrorGritter('Error!', 'Please Add Improvement Plan');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/fixed_asset/missing") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				drawData();
			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function openFillModal(id) {
		$("#modalFill").modal('show');

		var data = {
			id : id
		}

		$.get('{{ url("fetch/fixed_asset/missing/byId") }}', data, function(result, status, xhr){
			$("#id_fill").val(result.missing.id);
			$("#form_number_fill").val(result.missing.form_number);
			$("#asset_name_fill").val(result.missing.fixed_asset_name);
			$("#request_date_fill").val(result.missing.request_date);
			$("#asset_picture_fill").attr('href', "{{ url('files/fixed_asset/missing_picture') }}/"+result.missing.new_picture);
			$("#asset_no_fill").val(result.missing.fixed_asset_id);
			$("#asset_cls_fill").val(result.missing.clasification);
			$("#section_fill").val(result.missing.section_control);
			$("#disposal_reason_fill").val(result.missing.reason);
			$("#missing_reason_fill").val(result.missing.missing_reason);
			$("#improvement_plan_fill").val(result.missing.improvement_plan);
		})
	}

	function openEditModal(id) {
		$("#modalEdit").modal('show');

		var data = {
			id : id
		}

		$.get('{{ url("fetch/fixed_asset/missing/byId") }}', data, function(result, status, xhr){
			$("#form_number_edit").val(result.missing.form_number);
			$("#asset_name_edit").val(result.missing.fixed_asset_name);
			$("#asset_id_edit").val(result.missing.fixed_asset_id);
			$("#asset_no_edit").val(result.missing.fixed_asset_id);
			$("#asset_cls_edit").val(result.missing.clasification);
			$("#section_edit").val(result.missing.section_control);
			$("#disposal_reason_edit").val(result.missing.reason);

			$("#missing_reason_edit").val(result.missing.missing_reason);
			$("#improvement_plan_edit").val(result.missing.improvement_plan);


			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true
			});
		})
	}

	$("form#form_master_fill").submit(function(e) {
		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("fill/fixed_asset/missing") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#modalFill').modal('hide');

				openSuccessGritter('Success', result.message);

				drawData();
			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	$("form#form_master_edit").submit(function(e) {
		if( document.getElementById("asset_picture_edit").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture');
			return false;
		}

		if($("#disposal_reason_edit").val() == "" || $("#missing_reason_edit").val() == ""){
			openErrorGritter('Error!', 'Please Add Reason');
			return false;
		}

		if($("#improvement_plan_edit").val() == ""){
			openErrorGritter('Error!', 'Please Add Improvement Plan');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/fixed_asset/missing") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#modalEdit').modal('hide');

				openSuccessGritter('Success', result.message);

				drawData();
			},
			error: function(result, status, xhr){
				$("#loading").hide();

				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function openSendMail(form_number, approval) {
		var appr = "";

		if (approval == 'created') {
			appr = 'PIC Asset';
		} else if (approval == 'pic') {
			appr = 'Fixed Asset Control';
		} else if (approval == 'fa_control') {
			appr = 'PIC Manager';
		} else if (approval == 'manager') {
			appr = 'General Manager';
		} else if (approval == 'gm') {
			appr = 'Accounting Manager';
		} else if (approval == 'acc_manager') {
			appr = 'Finance Director';
		} else if (approval == 'director_fin') {
			appr = 'President Director';
		}


		$("#sendMailModal").modal('show');
		$("#approval_name").text(appr);
		$("#email_form_id").val(form_number);
	}

	function sendMail() {
		data = {
			id : $("#email_form_id").val(),
			form : 'Missing Form'
		}

		$.post('{{ url("send/mail/fixed_asset") }}', data, function(result, status, xhr) {
			openSuccessGritter('Success', 'Send Email Successfully');
			$("#sendMailModal").modal("hide");
		})
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

</script>
@endsection