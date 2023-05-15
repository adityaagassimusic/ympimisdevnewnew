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
		List of Request Label Asset
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Label Request.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Label Request</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Request Label</a>
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
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-header"></div>
				<div class="box-body">					
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%">Id.</th>
								<th style="width: 2%">Form Number</th>
								<th style="width: 5%">Created Date</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 5%">Fixed Asset No.</th>
								<th style="width: 10%">PIC</th>
								<th style="width: 10%">Location</th>
								<th style="width: 10%">Reason</th>
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
	
	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" style="width: 95%">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Form Label Request</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<button class="btn btn-success" type="button" onclick="add_asset()"><i class="fa fa-plus"></i> Add</button>
						<table style="width: 100%" class="table">
							<thead>
								<tr>
									<th style="width: 25%">Asset Name<span class="text-red">*</span></th>
									<th>Asset No<span class="text-red">*</span></th>
									<th>Section Control<span class="text-red">*</span></th>
									<th>Location<span class="text-red">*</span></th>
									<th>PIC<span class="text-red">*</span></th>
								</tr>
							</thead>
							<tbody id="body_asset">
							</tbody>
						</table>
						<div class="form-group">
							<label for="reason">Reason<span class="text-red">*</span></label>
							<textarea class="form-control" id="reason" placeholder="Enter Reason Request"></textarea>
						</div>

						<div class="form-group">
							<center><button class="btn btn-success" id="create_btn" onclick="save_form()"><i class="fa fa-check"></i> Request Label </button></center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Form Label Request</h1>
					</div>
					<div class="col-xs-12" style="padding-top: 10px" >
						<button class="btn btn-success" type="button" onclick="add_asset_edit()"><i class="fa fa-plus"></i> Add</button>
						<input type="hidden" name="form_number_edit" id="form_number_edit">
						<table style="width: 100%" class="table">
							<thead>
								<tr>
									<th>Asset Name<span class="text-red">*</span></th>
									<th>Asset No<span class="text-red">*</span></th>
									<th>Section Control<span class="text-red">*</span></th>
									<th>Location<span class="text-red">*</span></th>
									<th>PIC<span class="text-red">*</span></th>
								</tr>
							</thead>
							<tbody id="body_asset_edit">
							</tbody>
						</table>
						<div class="form-group">
							<label for="reason">Reason<span class="text-red">*</span></label>
							<textarea class="form-control" id="reason_edit" placeholder="Enter Reason Request"></textarea>
						</div>

						<div class="form-group">
							<center><button class="btn btn-success" id="create_btn_edit" onclick="save_edit_form()"><i class="fa fa-check"></i> Save </button></center>
						</div>
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
	var fa_list = <?php echo json_encode($asset_list); ?>;

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

		drawData();

	});

	function drawData() {
		$.get('{{ url("fetch/fixed_asset/label_asset") }}', function(result, status, xhr){
			$('#masterTable').DataTable().clear();
			$('#masterTable').DataTable().destroy();
			$("#masterBody").empty();
			var body = "";
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.id+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.create_at+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.fixed_asset_no+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.location+"</td>";
				body += "<td>"+value.reason+"</td>";
				body += "<td>"+value.status+"</td>";
				body += "<td>";

				if (value.reject_status && (value.status == 'hold' || value.status == 'reject')) {
					body += "<button class='btn btn-primary btn-xs' onclick='openEditModal(\""+value.form_number+"\")'><i class='fa fa-pencil'></i> Edit</button>&nbsp;";
				}

				if (value.reject_status && (value.status != 'hold' || value.status != 'reject')) {
					body += "<button class='btn btn-success btn-xs' onclick='openSendMail(\""+value.form_number+"\",\""+value.status+"\")'><i class='fa fa-send'></i> Send Mail</button>&nbsp;";
				}
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

		$.each(fa_list, function(index, value){
			if (value.sap_number == id_asset) {
				$(elem).parent().parent().find('.asset_no').val(value.sap_number);				
				$(elem).parent().parent().find('.asset_name').val(value.fixed_asset_name);
				$(elem).parent().parent().find('.section').val(value.section);
				$(elem).parent().parent().find('.location').val(value.location);
				$(elem).parent().parent().find('.pic').val(value.pic);
				$(elem).parent().parent().find('.pic_view').val(value.name);
			}
		})
	}

	function pilihAssetEdit(elem) {
		var id_asset = $(elem).val();

		$.each(fa_list, function(index, value){
			if (value.sap_number == id_asset) {
				$(elem).parent().parent().find('.asset_no_edit').val(value.sap_number);				
				$(elem).parent().parent().find('.asset_name_edit').val(value.fixed_asset_name);
				$(elem).parent().parent().find('.section_edit').val(value.section);
				$(elem).parent().parent().find('.location_edit').val(value.location);
				$(elem).parent().parent().find('.pic_edit').val(value.pic);
				$(elem).parent().parent().find('.pic_view_edit').val(value.name);
			}
		})
	}

	$("form#form_master").submit(function(e) {
		if ($("#request_reason").val() == "") {
			openErrorGritter('Failed','Please Fill Reason Field');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("post/fixed_asset/label_asset") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);

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

	function save_form() {
		var arr_asset_id = [];
		var arr_asset_name = [];
		var arr_asset_section = [];
		var arr_asset_location = [];
		var arr_asset_pic = [];


		$('.asset_no').each(function(index, value) {
			arr_asset_id.push($(this).val());
		});

		$('.asset_name').each(function(index, value) {
			arr_asset_name.push($(this).val());
		});

		$('.section').each(function(index, value) {
			arr_asset_section.push($(this).val());
		});

		$('.location').each(function(index, value) {
			arr_asset_location.push($(this).val());
		});

		$('.pic').each(function(index, value) {
			arr_asset_pic.push($(this).val());
		});

		if(jQuery.inArray("", arr_asset_id) !== -1) {
			openErrorGritter('Error', 'there is empty field');
			return false;
		}

		if ($("#reason").val() == "") {
			openErrorGritter('Error', 'Reason field is empty');
			return false;
		}

		var data = {
			asset_id : arr_asset_id,
			asset_name : arr_asset_name,
			asset_section : arr_asset_section,
			asset_location : arr_asset_location,
			asset_pic : arr_asset_pic,
			reason : $("#reason").val()
		}

		$.post('{{ url("post/fixed_asset/label_asset") }}', data, function(result, status, xhr){
			openSuccessGritter('Success', 'Success Request Label');
			$("#createModal").modal('hide');
			$("#body_asset").empty();
			$("#reason").val("");

			drawData();
		})
	}

	function add_asset() {
		var body = '';
		body += '<tr style="margin-bottom: 3px">';

		body += '<td>';
		body += '<input type="hidden" name="asset_name" class="asset_name">';
		body += '<select class="form-control select2 asset_id" name="asset_id" data-placeholder="Select Asset" style="width: 100%" onchange="pilihAsset(this)">';
		body += '<option value=""></option>';
		var asset_list = <?php echo json_encode($asset_list); ?>;

		$.each(asset_list, function(index, value){
			body += '<option value="'+value.sap_number+'">'+value.sap_number+' - '+value.fixed_asset_name+'</option>';
		})
		body += '</td>';

		body += '<td><input type="text" class="form-control asset_no" placeholder="Fixed Asset Number" readonly></td>';
		body += '<td><input type="text" class="form-control section" placeholder="Section Control" readonly></td>';
		body += '<td><input type="text" class="form-control location" placeholder="Location" readonly></td>';
		body += '<td><input type="hidden" class="form-control pic" placeholder="PIC">';
		body += '<input type="text" class="form-control pic_view" placeholder="PIC" readonly>';
		body += '</td>';
		body += '<td><button type="button" class="btn btn-danger" onclick="del(this)"><i class="fa fa-minus"></i></td>';
		body += '</tr>';

		$("#body_asset").append(body);

		$(".select2").select2({
			dropdownParent: $('#createModal'),
		});
	}
	
	function add_asset_edit() {
		var body = '';
		body += '<tr style="margin-bottom: 3px">';

		body += '<td>';
		body += '<input type="hidden" name="asset_name_edit" class="asset_name_edit">';
		body += '<select class="form-control select2 asset_id_edit" name="asset_id_edit" data-placeholder="Select Asset" style="width: 100%" onchange="pilihAssetEdit(this)">';
		body += '<option value=""></option>';
		var asset_list = <?php echo json_encode($asset_list); ?>;

		$.each(asset_list, function(index, value){
			body += '<option value="'+value.sap_number+'">'+value.sap_number+' - '+value.fixed_asset_name+'</option>';
		})
		body += '</td>';

		body += '<td><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" readonly></td>';
		body += '<td><input type="text" class="form-control section_edit" placeholder="Section Control" readonly></td>';
		body += '<td><input type="text" class="form-control location_edit" placeholder="Location" readonly></td>';
		body += '<td><input type="hidden" class="form-control pic_edit" placeholder="PIC">';
		body += '<input type="text" class="form-control pic_view_edit" placeholder="PIC" readonly>';
		body += '</td>';
		body += '<td><button type="button" class="btn btn-danger" onclick="del(this)"><i class="fa fa-minus"></i></td>';
		body += '</tr>';

		$("#body_asset_edit").append(body);

		$(".select2").select2();
	}

	function del(elem) {
		$(elem).closest('tr').remove();
	}

	function openEditModal(form_id) {
		$("#editModal").modal('show');
		$("#body_asset_edit").empty();
		$("#reason_edit").val("");
		$("#form_number_edit").val("");

		var data = {
			form_id : form_id
		}
		$.get('{{ url("fetch/fixed_asset/label_asset") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#form_number_edit").val(form_id);
				var body = '';
				$.each(result.datas, function(index2, value2){
					body += '<tr style="margin-bottom: 3px">';

					body += '<td>';
					body += '<input type="hidden" name="asset_name_edit" class="asset_name_edit" value="'+value2.fixed_asset_name+'">';
					body += '<select class="form-control select2 asset_id_edit" name="asset_id_edit" data-placeholder="Select Asset" style="width: 100%" onchange="pilihAssetEdit(this)">';
					body += '<option value=""></option>';
					var asset_list = <?php echo json_encode($asset_list); ?>;

					$.each(asset_list, function(index, value){
						if (value.sap_number == value2.fixed_asset_id) {
							body += '<option value="'+value.sap_number+'" selected>'+value.sap_number+' - '+value.fixed_asset_name+'</option>';
						} else {
							body += '<option value="'+value.sap_number+'">'+value.sap_number+' - '+value.fixed_asset_name+'</option>';
						}
					})
					body += '</td>';

					body += '<td><input type="text" class="form-control asset_no_edit" placeholder="Fixed Asset Number" value="'+value2.fixed_asset_id+'" readonly></td>';
					body += '<td><input type="text" class="form-control section_edit" placeholder="Section Control" value="'+value2.section+'" readonly></td>';
					body += '<td><input type="text" class="form-control location_edit" placeholder="Location" value="'+value2.location+'" readonly></td>';
					body += '<td><input type="hidden" class="form-control pic_edit" placeholder="PIC" value="'+value2.pic+'">';
					body += '<input type="text" class="form-control pic_view_edit" placeholder="PIC" value="'+value2.name+'" readonly>';
					body += '</td>';
					body += '<td><button type="button" class="btn btn-danger" onclick="del(this)"><i class="fa fa-minus"></i></td>';
					body += '</tr>';
				});

				$("#body_asset_edit").append(body);

				$(".select2").select2();

				$("#reason_edit").val(result.datas[0].reason);
			}
		})
	}

	function save_edit_form() {
		var arr_asset_id = [];
		var arr_asset_name = [];
		var arr_asset_section = [];
		var arr_asset_location = [];
		var arr_asset_pic = [];


		$('.asset_no_edit').each(function(index, value) {
			arr_asset_id.push($(this).val());
		});

		$('.asset_name_edit').each(function(index, value) {
			arr_asset_name.push($(this).val());
		});

		$('.section_edit').each(function(index, value) {
			arr_asset_section.push($(this).val());
		});

		$('.location_edit').each(function(index, value) {
			arr_asset_location.push($(this).val());
		});

		$('.pic_edit').each(function(index, value) {
			arr_asset_pic.push($(this).val());
		});

		if(jQuery.inArray("", arr_asset_id) !== -1) {
			openErrorGritter('Error', 'there is empty field');
			return false;
		}

		if ($("#reason_edit").val() == "") {
			openErrorGritter('Error', 'Reason field is empty');
			return false;
		}

		$("#loading").show();

		var data = {
			form_number : $("#form_number_edit").val(),
			asset_id : arr_asset_id,
			asset_name : arr_asset_name,
			asset_section : arr_asset_section,
			asset_location : arr_asset_location,
			asset_pic : arr_asset_pic,
			reason : $("#reason_edit").val()
		}

		$.post('{{ url("edit/fixed_asset/label_asset") }}', data, function(result, status, xhr){
			if (result.status) {
				$("#loading").hide();
				openSuccessGritter('Success', 'Success Request Label');
				$("#editModal").modal('hide');
				$("#body_asset_edit").empty();
				$("#reason_edit").val("");

				drawData();
			} else {
				openErrorGritter('Error', result.message);
				$("#loading").hide();
			}
		})
	}

	function openSendMail(form_id, approval) {
		var appr = "";

		if (approval == 'created') {
			appr = 'PIC Asset';
		} else if (approval == 'pic') {
			appr = 'PIC Asset';
		} else if (approval == 'acc_control') {
			appr = 'Fixed Asset Control';
		}

		$("#sendMailModal").modal('show');
		$("#approval_name").text(appr);
		$("#email_form_id").val(form_id);
	}

	function sendMail() {
		data = {
			id : $("#email_form_id").val(),
			form : 'Label Form'
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