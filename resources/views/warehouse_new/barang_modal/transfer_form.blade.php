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
	h3	{
		margin-top: 10px;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		List of {{ $page }}s
	</h1>
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Request Transfer Location</a>
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
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-header" style="padding: 0px; background-color: #605ca8; color: white"><center><h3><b>Request Transfer Non Asset List</b></h3></center></div>
				<div class="box-body">
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 10%">Item Non Asset Name</th>
								<th style="width: 5%">Item Non Asset No.</th>
								<th style="width: 10%">Old PIC</th>
								<th style="width: 10%">Old Location</th>
								<th style="width: 10%">New Section</th>
								<th style="width: 10%">New PIC</th>
								<th style="width: 3%">Status Approval</th>
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


	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-header" style="padding: 0px; background-color: #605ca8; color: white"><center><h3><b>Received Transfer Non Asset</b></h3></center></div>
				<div class="box-body">
					<table id="receiveTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 10%">Item Non Asset Name</th>
								<th style="width: 5%">Item Non Asset No.</th>
								<th style="width: 10%">Old Section</th>
								<th style="width: 10%">Old Location</th>
								<th style="width: 10%">Old PIC</th>
								<th style="width: 10%">New Section</th>
								<th style="width: 3%">Status Approval</th>
								<th style="width: 5%">Action</th>
							</tr>
						</thead>
						<tbody id="receiveBody">
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Form Non Asset Location</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<form method="post" enctype="multipart/form-data" autocomplete="off" id="form_master">
							<div class="row">
								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Non Asset Name : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-8">
										<input type="hidden" id="asset_name" name="asset_name">
										<select class="form-control select2" id="asset_id" name="asset_id" data-placeholder="Select Asset" style="width: 100%" onchange="pilihAsset(this)">
											<option value=""></option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%" >
									<div class="col-xs-4" align="right" style="padding: 0px">
										<label style="font-weight: bold; font-size: 16px;">Non Asset Picture : <span class="text-red">*</span></label>
									</div>
									<div class="col-xs-6">
										<input type="file" id="asset_picture" name="asset_picture" accept="image/*"> 
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">Non Asset No : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="asset_no" name="asset_no" placeholder="fixed asset number" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">OLD Section Control : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="old_section" name="old_section" placeholder="Old Section Control" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">OLD Location : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-6">
										<input type="text" class="form-control" id="old_location" name="old_location" placeholder="Old Location" readonly>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-4" style="padding: 0px;" align="right">
										<span style="font-weight: bold; font-size: 16px;">OLD PIC : <span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" id="old_pic" name="old_pic" placeholder="Old PIC" readonly>
										<!-- <select class="form-control select2" id="old_pic" name="old_pic" data-placeholder="Select Old PIC" style="width: 100%"> -->
											<!-- </select> -->
											<!-- <input type="text" class="form-control" id="old_pic_view" name="old_pic_view" placeholder="OLD PIC"> -->
										</div>
									</div>

									<div class="col-xs-12" style="padding-bottom: 1%;">
										<div class="col-xs-4" style="padding: 0px;" align="right">
											<span style="font-weight: bold; font-size: 16px;">NEW Section Control : <span class="text-red">*</span></span>
										</div>
										<div class="col-xs-8">
											<select class="form-control select2" id="new_section" name="new_section" data-placeholder="Select NEW Section Control" style="width: 100%" onchange="select_new_pic(this)">
												<option value=""></option>
											</select>
										</div>
									</div>

									<div class="col-xs-12" style="padding-bottom: 1%;">
										<div class="col-xs-4" style="padding: 0px;" align="right">
											<span style="font-weight: bold; font-size: 16px;">NEW Location : <span class="text-red">*</span></span>
										</div>
										<div class="col-xs-6">
											<!-- <input type="text" class="form-control" id="new_location" name="new_location" placeholder="New Location"> -->
											<select class="form-control select2" id="new_location" name="new_location" data-placeholder="Select NEW Location" style="width: 100%">
												<option value=""></option>
											</select>
										</div>
									</div>

									<div class="col-xs-12" style="padding-bottom: 1%;">
										<div class="col-xs-4" style="padding: 0px;" align="right">
											<span style="font-weight: bold; font-size: 16px;">NEW PIC : <span class="text-red">*</span></span>
										</div>
										<div class="col-xs-6">
											<!-- <input type="text" class="form-control" id="new_pic" name="new_pic" placeholder="New PIC"> -->
											<select class="form-control select2" id="new_pic" name="new_pic" data-placeholder="Select New PIC" style="width: 100%">
											</select>
										</div>
									</div>

									<div class="col-xs-12" style="padding-bottom: 1%;">
										<div class="col-xs-4" style="padding: 0px;" align="right">
											<span style="font-weight: bold; font-size: 16px;">Transfer Reason : <span class="text-red">*</span></span>
										</div>
										<div class="col-xs-6">
											<textarea class="form-control" id="transfer_reason" name="transfer_reason"></textarea>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-xs-4 pull-right">
										<center><button class="btn btn-success" type="submit" id="create_btn"><i class="fa fa-check"></i> Request Transfer </button></center>
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
						<button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
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
			jQuery(document).ready(function() {
				$('body').toggleClass("sidebar-collapse");

				$('.datepicker').datepicker({
					autoclose: true,
					format: "yyyy-mm-dd",
					todayHighlight: true
				});

				$('.select2').select2({
					dropdownParent: $('#createModal'),
				});

				$('.select3').select2({
					dropdownParent: $('#editModal'),
				})

				drawData();

			});

			function drawData() {
				$.get('{{ url("fetch/fixed_asset/transfer_asset") }}', function(result, status, xhr){
					$('#masterTable').DataTable().clear();
					$('#masterTable').DataTable().destroy();
					$("#masterBody").empty();
					var body = "";
					$.each(result.datas, function(index, value){
						body += "<tr>";
						body += "<td>"+(value.form_number || '')+"</td>";
						body += "<td>"+value.create_at+"</td>";
						body += "<td>"+value.fixed_asset_name+"</td>";
						body += "<td>"+value.fixed_asset_no+"</td>";
						body += "<td>"+value.old_pic+"</td>";
						body += "<td>"+value.old_location+"</td>";
						body += "<td>"+value.new_section+"</td>";
						body += "<td>"+value.new_pic+"</td>";
						body += "<td>";
						if (value.status == 'acc_manager') {
							body += '<small class="label bg-green"><i class="fa fa-check"></i> Done</small>';
						} else {
							body += value.status;
						}

						body += "</td>";
						body += "<td>";

						if (value.status == 'created' || value.status == 'hold' || value.status == 'reject') {
							body += "<button class='btn btn-primary btn-xs' onclick='openEditModal("+value.id+","+value.fixed_asset_no+")'><i class='fa fa-pencil'></i> Edit</button>&nbsp;";
						}

						body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_transfer/Transfer_') }}"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";

						if (value.reject_status && (value.status != 'hold' && value.status != 'reject')) {
							body += "<button class='btn btn-success btn-xs' onclick='openSendMail("+value.id+",\""+value.status+"\")'><i class='fa fa-send'></i> Send Email</button>";
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

					$('#receiveTable').DataTable().clear();
					$('#receiveTable').DataTable().destroy();
					$("#receiveBody").empty();

					var body = "";

					$.each(result.data_receives, function(index, value){
						body += "<tr>";
						body += "<td>"+value.id+"</td>";
						body += "<td>"+value.create_at+"</td>";
						body += "<td>"+value.fixed_asset_name+"</td>";
						body += "<td>"+value.fixed_asset_no+"</td>";
						body += "<td>"+value.old_section+"</td>";
						body += "<td>"+value.old_location+"</td>";
						body += "<td>"+value.old_pic+"</td>";
						body += "<td>"+value.new_section+"</td>";
						body += "<td>"+value.status+"</td>";
						body += "<td>";
					// body += "<button class='btn btn-primary btn-xs'><i class='fa fa-eye'></i> Detail</button>";
					body += "<a class='btn btn-danger btn-xs' href='{{ url('files/fixed_asset/report_transfer/Transfer_') }}"+value.form_number+".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i>&nbsp; Report</a>";
					body += "</td>";
					body += "</tr>";
				})

					$("#receiveBody").append(body);

					var table2 = $('#receiveTable').DataTable({
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
			$("#old_section").val(value.section);
			$("#old_location").val(value.location);
			$("#old_pic").val(value.pic+' - '+value.name);

			}

		})
}


$("form#form_master").submit(function(e) {

	if( document.getElementById("asset_picture").files.length == 0 ){
		openErrorGritter('Error', 'Please Upload Asset Photo');
		return false;
	}

	if ($("#transfer_reason").val() == '') {
		openErrorGritter('Error', 'Transfer Reason cannot be empty');
		return false;
	}

	$("#loading").show();
	e.preventDefault();    
	var formData = new FormData(this);

	$.ajax({
		url: '{{ url("post/fixed_asset/transfer_asset") }}',
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

function openSendMail(id, approval) {
	var appr = "";

	if (approval == 'created') {
		appr = 'PIC Asset';
	} else if (approval == 'pic_old') {
		appr = 'PIC Manager';
	} else if (approval == 'manager_old') {
		appr = 'New PIC';
	} else if (approval == 'pic_new') {
		appr = 'New PIC Manager';
	} else if (approval == 'manager_new') {
		appr = 'Accounting Manager';
	}

	$("#sendMailModal").modal('show');
	$("#approval_name").text(appr);
	$("#email_form_id").val(id);
}

function sendMail() {
	data = {
		id : $("#email_form_id").val(),
		form : 'Transfer Form'
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