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
	<h1>
		List of {{ $page }}
	</h1>
	<ol class="breadcrumb">
		<li>
			<button class="btn btn-warning btn-md" style="color:white" onclick="openModalRemind()"><i class="fa fa-hourglass-2 "></i> Reminder CIP</button>
		</li>
		<li>
			<a href="{{ url('index/fixed_asset_cip/transfer_cip/form_user') }}" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Confirmation Asset CIP</a>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<center style='font-size: 22px'><b>List Asset</b></center>
					<table id="AssetTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Fixed Asset Number</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 5%">Register Date</th>
								<th style="width: 9%">Clasification</th>
								<th style="width: 10%">Vendor</th>
								<th style="width: 5%">Investment Number</th>
								<th style="width: 3%">Applicant</th>
								<th style="width: 5%">Section</th>
								<th style="width: 3%">Location</th>
								<th style="width: 3%">Asset Image</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
						<tbody id="AssetBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>

					<center style='font-size: 22px'><b>Confirmation CIP Asset Form List</b></center>
					<table id="ConfirmationTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Form Number</th>
								<th style="width: 10%">Fixed Asset List</th>
								<th style="width: 3%">Status</th>
								<th style="width: 3%">Applicant</th>
								<th style="width: 3%">Create at</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
						<tbody id="ConfirmationBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>

					<center style='font-size: 22px'><b>Transfered CIP Asset List</b></center>
					<table id="cipTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Fixed Asset Number</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<th style="width: 5%">Register Date</th>
								<th style="width: 9%">Clasification</th>
								<th style="width: 10%">Vendor</th>
								<th style="width: 5%">Investment Number</th>
								<th style="width: 3%">Applicant</th>
								<th style="width: 5%">Section</th>
								<th style="width: 3%">Location</th>
								<th style="width: 3%">Asset Image</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
						<tbody id="cipBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="createModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00a65a;">
					<center><h2 style="margin: 0px;"><b>Transfer Asset CIP</b></h2></center>
				</div>
				<div class="modal-body">
					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-4" align="right" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Select Asset to Transfer : </span>
						</div>
						<div class="col-xs-2">
							<button class="btn btn-sm btn-success" onclick="add_asset()"><i class="fa fa-plus"></i> Add Asset</button>
						</div>
					</div>
					<div class="col-xs-12">
						<table id="div_asset" style="width: 100%">
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success pull-left" onclick="sendMail()"><i class="fa fa-check"></i> YES</button>
					<button class="btn btn-danger"><i class="fa fa-close"></i> NO</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalRemind" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #f39c12;">
					<center><h2 style="margin: 0px;"><b>Reminder Asset CIP</b></h2></center>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="form-group">
								<label for="period" class="col-sm-1 control-label">Period</label>
								<div class="col-sm-2">
									<input type="text" class="form-control datepicker2" id="period" placeholder="Select Period" value="{{ date('Y-m') }}">
								</div>
								<div class="col-s">
									<button class="btn btn-primary" onclick="draw_data()"><i class="fa fa-search"></i> Cari</button>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<table style="width: 100%" class="table table-bordered">
								<thead>
									<tr>
										<th>Section</th>
										<th>Form Number</th>
										<th>Remind Date</th>
										<th>Qty CIP Asset</th>
										<th>Qty Reminder CIP Asset</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="body_reminder">
								</tbody>
							</table>
						</div>
					</div>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('.datepicker2').datepicker({
			format: "yyyy-mm",
			startView: "months",
			minViewMode: "months",
			autoclose: true,
			todayHighlight: true
		});

		draw_data();
	});

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#createModal')
		});
	})

	function draw_data() {
		var data = {
			category : 'CIP',
		}

		$.get('{{ url("fetch/fixed_asset/asset_list") }}', data, function(result, status, xhr) {
			$('#AssetTable').DataTable().clear();
			$('#AssetTable').DataTable().destroy();
			$("#AssetBody").empty();
			body = "";

			assets = result.assets;

			$.each(result.assets, function(index, value){
				body += "<tr>";
				body += "<td>"+value.sap_number+"</td>";
				body += "<td>"+value.fixed_asset_name+"</td>";
				body += "<td>"+value.request_date+"</td>";
				body += "<td>"+(value.classification_category || '')+"</td>";
				body += "<td>"+value.vendor+"</td>";
				body += "<td>"+(value.investment || '')+"</td>";
				body += "<td>"+value.name+"</td>";
				body += "<td>"+value.section+"</td>";
				body += "<td>"+value.location+"</td>";

				var url = "{{ url('files/fixed_asset/asset_picture') }}/"+value.picture;
				body += "<td><img src='"+url+"' style='max-width: 100px; max-height: 100px; cursor:pointer' onclick='modalImage(\""+url+"\")' Alt='Image Not Found'></td>";
				body += "<td> - </td>";

				body += "</tr>";
			});

			$('#AssetBody').append(body);

			var table = $('#AssetTable').DataTable({
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

		// ------  GET FORM CONFIRMATION ----
		var data2 = {
			period : $("#period").val()
		}

		$.get('{{ url("fetch/fixed_asset/asset_cip_list") }}', data2, function(result, status, xhr) {
			var body2 = '';
			$('#ConfirmationTable').DataTable().clear();
			$('#ConfirmationTable').DataTable().destroy();
			$("#ConfirmationBody").empty();

			$.each(result.cips, function(index, value){
				body2 += "<tr>";
				body2 += "<td>"+value.form_number+"</td>";
				body2 += "<td>"+value.asset.replace(/, /g, "<br>")+"</td>";
				body2 += "<td>"+value.status+"</td>";
				body2 += "<td>"+value.name+"</td>";
				body2 += "<td>"+value.created_at+"</td>";
				body2 += "<td>";
				if (value.fa_receive_at && ('{{ Auth::user()->username }}' == 'PI2002021' || '{{ Auth::user()->username }}' == 'PI0905001') ) {
					body2 += "<a class='btn btn-success btn-xs' href='{{ url('index/fixed_asset/transfer_cip/form/fa_control') }}/"+value.form_number+"'><i class='fa fa-random'></i> Transfer Asset</a><br>";
				} 

				body2 += "<button class='btn btn-primary btn-xs' onclick='sendMail(\""+value.form_number+"\")'><i class='fa fa-send'></i> Send Email</button>";

				// body2 += "<button class='btn btn-warning btn-xs' onclick='sendMail(\""+value.form_number+"\")'><i class='fa fa-send'></i> Disposal Asset</button>";

				// body2 += "<button class='btn btn-warning btn-xs' onclick='sendMail(\""+value.form_number+"\")'><i class='fa fa-send'></i> Special Reason Letter</button>";
				body2 += "</td>";

				body2 += "</tr>";
			});
			
			$('#ConfirmationBody').append(body2);

			var table = $('#ConfirmationTable').DataTable({
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

			var body_reminder = '';
			$("#body_reminder").empty();
			if (result.reminder_lists.length > 0) {
				$.each(result.reminder_lists, function(index, value){
					body_reminder += '<tr>';
					body_reminder += '<td>'+value.section+'</td>';
					if (value.form) {
						body_reminder += '<td>'+value.form.split(',')[0]+'</td>';
						body_reminder += '<td>'+value.remind_date.split(',')[0]+'</td>';
					} else {
						body_reminder += '<td></td>';
						body_reminder += '<td></td>';
					}
					body_reminder += '<td>'+value.tot+'</td>';
					body_reminder += '<td>'+value.remind+'</td>';

					if (value.form) {
						body_reminder += '<td><buton class="btn btn-primary" onclick="reminder(\''+value.section+'\', \''+$("#period").val()+'\')"><i class="fa fa-envelope-o"></i> Send Reminder</button></td>';
					} else {
						body_reminder += '<td></td>';
					}

					body_reminder += '</tr>';
				});

				$("#body_reminder").append(body_reminder);
			}
		})

	}

	var assets = [];

	$("form#data_edit").submit(function(e) {
		$("#loading").show();

		if(document.getElementById("asset_foto_edit").files.length == 0 ){
			openErrorGritter('Error', 'No files selected');
			$("#loading").hide();
			return false;
		}

		var invoice_name = $("#invoice_name_edit").val();
		var form_number = $("#invoice_form_number_edit").val();
		var item_name = $("#item_name_edit").val();
		var invoice_number = $("#invoice_number_edit").val();
		var clasification = $("#clasification_mid_edit").val();
		var investment_number = $("#investment_number_edit").val();
		var budget = $("#budget_edit").val();
		var vendor = $("#vendor_edit").val();
		var currency = $("#currency_edit").val();
		var amount = $("#amount_edit").val();
		var amount_usd = $("#amount_usd_edit").val();
		var location_e = $("#location_edit").val();
		var pic = $("#pic_edit").val();
		var usage_term = $('input[name="usage_term"]:checked').val();
		var usage_est = $("#usage_est_edit").val();

		if(usage_term == "not use yet"){
			if (usage_est == '') {
				openErrorGritter('Error!', 'Date Usage Estimation must be filled');
				$("#loading").hide();
				return false;
			}
		}

		e.preventDefault();    
		var formData = new FormData();

		formData.append('id', $("#id_edit").val());
		formData.append('id_asset', $("#id_asset_edit").val());
		formData.append('form_number', form_number);
		formData.append('invoice_name', invoice_name);
		formData.append('item_name', item_name);
		formData.append('invoice_number', invoice_number);
		formData.append('clasification', clasification);
		formData.append('investment_number', investment_number);
		formData.append('budget', budget);
		formData.append('vendor', vendor);
		formData.append('currency', currency);
		formData.append('amount', amount);
		formData.append('amount_usd', amount_usd);
		formData.append('location', location_e);
		formData.append('pic', pic);
		formData.append('pic_asset', $("#pic_asset_edit").val());
		formData.append('usage_term', usage_term);
		formData.append('usage_est', usage_est);

		$.ajax({
			url: '{{ url("update/fixed_asset/registration_asset_form") }}',
			type: 'POST',
			contentType: 'multipart/form-data',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$("#invoice_name_edit").val("");
				$("#item_name_edit").val("");
				$("#invoice_number_edit").val("");
				$("#clasification_edit").select2("val", "");
				$("#clasification_mid_edit").select2("val", "");
				$("#investment_number_edit").val("");
				$("#budget_edit").val("");
				$("#vendor_edit").val("");
				$("#currency_edit").select2("val", "");
				$("#amount_edit").val("");
				$("#amount_usd_edit").val("");
				$("#location_edit").empty();
				$("#location_edit").append("<option value=''></option>");
				$("#pic_edit").val("");
				$('input[name="usage_term_edit"]').prop('checked', false);
				$("#usage_est_edit").val("");


				$('#editModal').modal('hide');

				openSuccessGritter('Success', result.message);

				// location.reload();
				draw_data();
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

	function delete_asset(elem) {
		$(elem).closest('tr').remove();
	}

	function sendMail(form_number) {
		if (confirm('Are you sure want to send email this form ?')) {
			var data = {
				form_number : form_number
			}

			$.post('{{ url("post/fixed_asset/asset_cip/send_mail") }}', data, function(result, status, xhr) {
				if (result.status) {
					openSuccessGritter('Success', 'Send Mail Success');
				}
			})
		}
	}

	function add_asset() {
		var body = '';

		body += '<tr>';
		body += '<td style="width: 70%; padding: 2px"><select class="asset_list select2" data-placeholder="Select Asset" style="width:100%">';
		body += '<option value=""></option>';

		$.each(assets, function(index, value){
			body += '<option value="'+value.sap_number+'">'+value.fixed_asset_name+'('+value.sap_number+')</option>';
		});

		body += '</select></td>';
		body += '<td style="padding: 2px"><button class="btn btn-danger btn-sm" onclick="delete_asset(this)"><i class="fa fa-minus"></i></button></td>';
		body += '</tr>';

		$("#div_asset").append(body);

		$('.select2').select2({
			dropdownParent: $('#createModal')
		});
	}

	function openModalRemind() {
		$("#modalRemind").modal('show');
	}

	function reminder(section, period) {
		$("#loading").show();

		var data = {
			section : section,
			period : period,
		}

		$.post('{{ url("post/fixed_asset/asset_cip/resend_mail") }}', data, function(result, status, xhr) {
			$("#loading").hide();

			if (result.status) {
				openSuccessGritter('Success', 'Send Mail Success');
			} else {
				openErrorGritter('Error', result.message);
			}
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