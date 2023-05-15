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
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Disposal Scrap Report.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Disposal Scrap Report</a>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<label>Outstanding Disposal Scrap List</label>
					<table id="outstandingTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 10%">Section Control</th>
								<th style="width: 10%">Request Disposal Date</th>
								<th style="width: 10%">Status</th>
								<th style="width: 5%">Report</th>
								<th style="width: 5%">Action</th>
							</tr>
						</thead>
						<tbody id="outstandingBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<label>Disposal Scrap List</label>
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 10%">Officer Department</th>
								<th style="width: 10%">Officer</th>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Report Disposal Scrap</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Disposal Form Number : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<select class="form-control select2" id="form_number" name="form_number" data-placeholder="Select Disposal" style="width: 100%" onchange="pilihAsset(this)">
											<option value=""></option>
											@foreach($dispo_list as $dl)
											<option value="{{ $dl->form_number }}">{{ $dl->form_number }} - {{ $dl->fixed_asset_name }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="Fixed Asset Number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="asset_name" name="asset_name" placeholder="Fixed Asset Name" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control datepicker" id="disposal_date" name="disposal_date" placeholder="Select Disposal Date">
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Officer Department : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="officer_dept" name="officer_dept" placeholder="Office Department" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Officer Name : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="hidden" name="officer_id" id="officer_id">
										<input type="text" class="form-control" id="officer" name="officer" id="officer" placeholder="Officer" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture Before : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_before" id="picture_before">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture After : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_after" id="picture_after">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture Process : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_process" id="picture_process">
									</div>
								</div>
								<div class="col-xs-12" style="padding-bottom: 1%">
									<div class="col-xs-12" style="padding: 0px">
										<span id="error-message" class="validation-error-label"></span>
									</div>
								</div>

							</div>

							<div class="row">
								<div class="col-xs-4 pull-right">
									<center><button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Create Report </button></center>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Report Disposal Asset</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master_edit">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Disposal Form Number : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="hidden" class="form-control" name="id_edit" id="id_edit">
										<input type="text" class="form-control" name="form_number_edit" id="form_number_edit" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no_edit" name="asset_no_edit" placeholder="Fixed Asset Number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_name_edit" name="asset_name_edit" placeholder="Fixed Asset Name" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Disposal Date : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control datepicker" id="disposal_date_edit" name="disposal_date_edit" placeholder="Select Disposal Date">
									</div>
								</div>


								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Officer Department : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="officer_dept_edit" name="officer_dept_edit" placeholder="Office Department" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Officer Name : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="hidden" name="officer_id_edit" id="officer_id_edit">
										<input type="text" class="form-control" id="officer_edit" name="officer_edit" placeholder="Officer" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture Before : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_before_edit" id="picture_before_edit">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture After : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_after_edit" id="picture_after_edit">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Picture Process : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="file" name="picture_process_edit" id="picture_process_edit">
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
					<button class="btn btn-danger"><i class="fa fa-close"></i> NO</button>
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

		$('.select4').select2({
			dropdownParent: $('#modalEdit'),
		})

		drawData();

	});

	function drawData() {

		$.get('{{ url("fetch/fixed_asset/disposal/scrap") }}', function(result, status, xhr){
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			$("#masterBody").empty();
			var body = "";
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.request_date+"</td>";
				body += "<td>"+value.fixed_asset_id+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.officer_department+"</td>";
				body += "<td>"+value.officer+"</td>";
				body += "<td>"+value.status+"</td>";
				body += "<td>";
				body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_disposal_scrap/DisposalScrap_') }}"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";
				body += "</td>";
				body += "<td>";
				if (value.status == 'pic' && value.last_status == 'pic') {
					body += "<button class='btn btn-warning btn-xs' onclick='openFillModal("+value.id+")'><i class='fa fa-pencil'></i> Fill</button>&nbsp;";
				}
				
				body += "<button class='btn btn-primary btn-xs' onclick='openEditModal("+value.id+")'><i class='fa fa-pencil'></i> Edit</button>&nbsp;";

				if (value.status != 'hold' && value.status != 'reject' && value.status != 'created') {
					body += "<button class='btn btn-success btn-xs' onclick='openSendMail("+value.id+",\""+value.status+"\")'><i class='fa fa-send'></i> Send Mail</button>&nbsp;";
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


			$('#outstandingTable').DataTable().clear();
			$('#outstandingTable').DataTable().destroy();
			$("#outstandingBody").empty();
			var body = "";
			$.each(result.outstanding, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.create_at+"</td>";
				body += "<td>"+value.fixed_asset_id+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.section_control+"</td>";
				body += "<td>"+(value.disposal_request_date || '')+"</td>";
				body += "<td>"+value.status+"</td>";
				body += "<td>";
				body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_disposal_scrap/DisposalScrap_') }}"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";
				body += "</td>";
				body += "<td>";
				if (value.status == 'new_pic') {
					body += "<button class='btn btn-success btn-xs' onclick='createModal(\""+value.form_number+"\")'><i class='fa fa-plus'></i> Create Report </button>&nbsp;";
				}
				body += "</td>";
				body += "</tr>";
			})

			$("#outstandingBody").append(body);

			var table = $('#outstandingTable').DataTable({
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
		var form_id = $(elem).val();

		var data = {
			form_id : form_id
		}

		$.get('{{ url("fetch/fixed_asset/disposal") }}', data, function(result, status, xhr){
			$("#asset_no").val(result.datas[0].fixed_asset_id);
			$("#asset_name").val(result.datas[0].fixed_asset_name);
			// $("#disposal_date").val(result.datas[0].create_at);
			$("#officer_dept").val(result.datas[0].pic_incharge);
			$("#officer").val(result.datas[0].new_pic_app.split("/")[1]);
			$("#officer_id").val(result.datas[0].new_pic_app.split("/")[0]);
		})
	}

	$("form#form_master").submit(function(e) {
		if( document.getElementById("picture_before").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture Before');
			return false;
		}

		if(document.getElementById("picture_after").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture After');
			return false;
		}


		if(document.getElementById("picture_process").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture In Process');
			return false;
		}

		for(var i=0; i< $("#picture_before").get(0).files.length; ++i){
			var file1 = $("#picture_before").get(0).files[i].size;
			if(file1){
				var file_size = $("#picture_before").get(0).files[i].size;
				if(file_size > 2000000){
					$('#error-message').html("Picture Before size is larger than 2MB");
					$('#error-message').css("display","block");
					$('#error-message').css("color","red");
				}else{
					$('#error-message').css("display","none");
				}
			}
		}

		for(var i=0; i< $("#picture_process").get(0).files.length; ++i){
			var file1 = $("#picture_process").get(0).files[i].size;
			if(file1){
				var file_size = $("#picture_process").get(0).files[i].size;
				if(file_size > 2000000){
					$('#error-message').html("Picture Before size is larger than 2MB");
					$('#error-message').css("display","block");
					$('#error-message').css("color","red");
				}else{
					$('#error-message').css("display","none");
				}
			}
		}

		for(var i=0; i< $("#picture_after").get(0).files.length; ++i){
			var file1 = $("#picture_after").get(0).files[i].size;
			if(file1){
				var file_size = $("#picture_after").get(0).files[i].size;
				if(file_size > 2000000){
					$('#error-message').html("Picture Before size is larger than 2MB");
					$('#error-message').css("display","block");
					$('#error-message').css("color","red");
				}else{
					$('#error-message').css("display","none");
				}
			}
		}

		if( $("#disposal_date").val() == ""){
			openErrorGritter('Error!', 'Please Add Disposal Date');
			return false;
		}

		$("#loading").show();

		e.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/fixed_asset/disposal/scrap") }}',
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

	function openEditModal(id) {
		$("#modalEdit").modal('show');

		var data = {
			id : id
		}

		$.get('{{ url("fetch/fixed_asset/disposal/scrap/byId") }}', data, function(result, status, xhr){
			$("#id_edit").val(result.datas.id);
			$("#form_number_edit").val(result.datas.form_number_disposal);
			$("#asset_no_edit").val(result.datas.fixed_asset_id);
			$("#asset_name_edit").val(result.datas.fixed_asset_name);
			$("#disposal_date_edit").val(result.datas.disposal_date);
			$("#officer_dept_edit").val(result.datas.officer_department);
			$("#officer_id_edit").val(result.datas.officer.split("/")[0]);
			$("#officer_edit").val(result.datas.officer.split("/")[1]);

			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true
			});
		})
	}

	$("form#form_master_edit").submit(function(e) {
		if( $("#disposal_date_edit").val() == ""){
			openErrorGritter('Error!', 'Please Select Disposal Date');
			return false;
		}

		if( document.getElementById("picture_before_edit").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture Before');
			return false;
		}

		if(document.getElementById("picture_after_edit").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture After');
			return false;
		}


		if(document.getElementById("picture_process_edit").files.length == 0 ){
			openErrorGritter('Error!', 'Please Add Picture In Process');
			return false;
		}
		

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/fixed_asset/disposal/scrap") }}',
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

	function openSendMail(id, approval) {
		var appr = "";

		if (approval == 'created') {
			appr = 'PIC Asset';
		} else if (approval == 'pic') {
			appr = 'PIC Manager';
		} else if (approval == 'manager') {
			appr = 'PIC General Manager';
		} else if (approval == 'gm') {
			appr = 'Accounting Manager';
		} else if (approval == 'acc_manager') {
			appr = 'Finance Director';
		} else if (approval == 'director') {
			appr = 'PIC Accounting';
		} 


		$("#sendMailModal").modal('show');
		$("#approval_name").text(appr);
		$("#email_form_id").val(id);
	}

	function sendMail() {
		data = {
			id : $("#email_form_id").val(),
			form : 'Disposal Scrap Form'
		}

		$.post('{{ url("send/mail/fixed_asset") }}', data, function(result, status, xhr) {
			openSuccessGritter('Success', 'Send Email Successfully');
			$("#sendMailModal").modal("hide");
		})
	}
	
	function createModal(form_number) {
		$("#createModal").modal('show');

		$("#form_number").val(form_number).trigger('change');
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