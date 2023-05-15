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
		List of {{ $page }}s
	</h1>
	<ol class="breadcrumb">
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Upload Invoice</a>
		</li>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
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
					<table id="SpecialLetterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Form Number</th>
								<th style="width: 5%">Fixed Asset List</th>
									<th style="width: 10%">Acquisition Date</th>
									<th style="width: 9%">Plan Use</th>
									<th style="width: 3%">PIC</th>
									<th style="width: 3%">Status</th>
									<th style="width: 8%">Action</th>
								</tr>
							</thead>
							<tbody id="SpecialLetterBody">
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
							<h1 style="text-align: center; margin:5px; font-weight: bold;">Upload Invoice</h1>
						</div>
						<div class="col-xs-12" style="padding-top: 10px">
							<div class="row">

								<label>Investment Number : </label>
								<select name="investment_number" class="form-control select2" id="investment_number" data-placeholder="Select Investment Number">
								</select>
								<button class="btn btn-primary pull-right" onclick="add_invoice_new()"><i class="fa fa-plus"></i>&nbsp; Add File</button>
								<br>
								<table class="table table-bordered table-striped" id="table_inv">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th>Invoice File</th>
										</tr>
									</thead>
									<tbody id="body_inv">
									</tbody>
								</table>
								<br>
								<button class="btn btn-success btn-lg pull-right" onclick="upload_invoice()"><i class="fa fa-upload"></i>&nbsp;Upload and Send</button>
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

			var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
			var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

			var no = 0;
			var investment_list = [];

			jQuery(document).ready(function() {
				$('body').toggleClass("sidebar-collapse");

				$('.datepicker').datepicker({
					autoclose: true,
					format: "yyyy-mm-dd",
					todayHighlight: true
				});

				draw_data();
			});

			$(function () {
				$('.select2').select2({
					dropdownParent: $('#createModal'),
				})
			})

			function add_invoice() {
				body_inv = "";
				body_inv += "<tr style='background-color:#dc7afa' class='"+no+"'>";
				body_inv += "<td><input type='text' class='form-control inv_num' id='invoice_"+no+"' placeholder='invoice number'></td>";
				body_inv += "<td><input type='text' class='form-control inv_name' placeholder='invoice name'></td>";
				body_inv += "<td><input type='text' class='form-control fix_name' placeholder='fixed asset name'></td>";
			// body_inv += "<td><input type='text' class='form-control inv_item' placeholder='invoice item'></td>";
			body_inv += "<td><input type='file' class='inv_file'></td>";
			body_inv += "<td><button class='btn btn-success btn-sm' onclick='add_item_inv(this,"+no+")'><i class='fa fa-plus'></i>&nbsp; add</button></td>";
			body_inv += "<td><button class='btn btn-danger btn-sm' onclick='delete_invoice("+no+")'><i class='fa fa-trash'></i>&nbsp; delete</button></td>";
			body_inv += "</tr>";

			body_inv += "<tr>";
			body_inv += "<td colspan='6'>";
			body_inv += "<table width='100%'>";
			body_inv += "<thead><tr>";
			body_inv += "<th>Vendor</th>";
			body_inv += "<th>CUR</th>";
			body_inv += "<th>Amount</th>";
			body_inv += "<th>USD Amount</th>";
			body_inv += "</tr></thead>";
			body_inv += "<tbody id='"+no+"'></tbody>";
			body_inv += "</table>";
			body_inv += "</td>";
			body_inv += "</tr>";

			$("#body_inv").append(body_inv);
			no++;
		}

		function add_invoice_new() {
			body_inv = "";
			body_inv += "<tr class='"+no+"'>";
			body_inv += "<td><input type='file' class='form-control inv_file' id='file_"+no+"'></td>";
			body_inv += "</tr>";

			$("#body_inv").append(body_inv);
			no++;
		}

		function add_item_inv(elem, number) {
			body_inv_child = "";
			body_inv_child += "<tr class='child_"+number+"'>";
			body_inv_child += "<td><input type='text' class='form-control vendor' placeholder='vendor'></td>";
			body_inv_child += "<td><input type='text' class='form-control currency' placeholder='currency'></td>";
			body_inv_child += "<td><input type='text' class='form-control amount' placeholder='amount'></td>";
			body_inv_child += "<td><input type='text' class='form-control amount_usd' placeholder='amount usd'></td>";
			body_inv_child += "<td><button class='btn btn-danger btn-sm' onclick='remove_item_inv(this)'><i class='fa fa-minus'></i></button></td><td></td>";
			body_inv_child += "</tr>";

			$("#"+number).append(body_inv_child);
		}

		function remove_item_inv(elem) {
			$(elem).closest("tr").remove();
		}

		function delete_invoice(number) {
			$("."+number).each(function() {
				$(this).closest("tr").remove();
			});
		}

		function draw_data() {
			$.get('{{ url("fetch/fixed_asset/invoice_form") }}', function(result, status, xhr) {
				$("#RegistrationBody").empty();
				$('#registrationTable').DataTable().clear();
				$('#registrationTable').DataTable().destroy();
				body = "";

				$.each(result.invoices, function(index, value){
					body += "<tr>";
					body += "<td>"+value.form_id+"</td>";
					body += "<td>"+value.investment_number+"</td>";
					// body += "<td>"+value.invoice_number+"</td>";
					// body += "<td>"+value.invoice_name+"</td>";
					// body += "<td>"+value.fixed_asset_name+"</td>";
					body += "<td>"+value.type+"</td>";
					body += "<td>"+value.applicant_name+"</td>";
					body += "<td>"+value.created_at.split(" ")[0]+"</td>";
					body += "<td>"+value.status+"</td>";
					body += "<td>";

					var atts = value.att.split(',');

					$.each(atts, function(index2, value2){
						body += "<a href='{{ url('files/fixed_asset/') }}/"+value2+"' target='_blank' class='btn btn-xs btn-danger'><i class='fa fa-file-pdf-o'></i> Dokumen "+(index2+1)+"</a><br>";

					})
					body += "</td>";
					body += "</tr>";
				});
				$('#RegistrationBody').append(body);

				$('#registrationTable').DataTable({
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
					'pageLength': 10,
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
			})
		}


		$("form#data").submit(function(e) {
			$("#loading").show();

			var invoice_name = $("#invoice_name").val();
			var item_name = $("#item_name").val();
			var invoice_number = $("#invoice_number").val();
			var clasification = $("#clasification_mid").val();
			var investment_number = $("#investment_number").val();
			var budget = $("#budget").val();
			var vendor = $("#vendor").val();
			var currency = $("#currency").val();
			var amount = $("#amount").val();
			var amount_usd = $("#amount_usd").val();
			var location = $("#location").val();
			var pic = $("#pic").val();
			var usage_term = $('input[name="usage_term"]:checked').val();
			var usage_est = $("#usage_est").val();

			if(usage_term == "not use yet"){
				if (usage_est == '') {
					openErrorGritter('Error!', 'Date Usage Estimation must be filled');
					$("#loading").hide();
					return false;
				}
			}

			e.preventDefault();    
			var formData = new FormData();
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
			formData.append('location', location);
			formData.append('pic', pic);
			formData.append('usage_term', usage_term);
			formData.append('usage_est', usage_est);

			$.ajax({
				url: '{{ url("send/fixed_asset/registration_asset_form") }}',
				type: 'POST',
				data: formData,
				success: function (result, status, xhr) {
					$("#loading").hide();

					return false;

					$("#invoice_name").val("");
					$("#item_name").val("");
					$("#invoice_number").val("");
					$("#clasification").select2("val", "");
					$("#clasification_mid").select2("val", "");
					$("#investment_number").val("");
					$("#budget").val("");
					$("#vendor").val("");
					$("#currency").select2("val", "");
					$("#amount").val("");
					$("#amount_usd").val("");
					$("#location").val("");
					$("#pic").val("");
					$('input[name="usage_term"]').prop('checked', false);
					$("#usage_est").val("");


					$('#createModal').modal('hide');

					openSuccessGritter('Success', result.message);

				// location.reload(true);

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

		function upload_invoice() {
			$("#loading").show();
			$("#investment_number").val();

			var inv_num = [];
			var inv_name = [];
			var inv_item = [];
			var fix_name = [];
			var inv_file = [];

			var vendor = [];
			var currency = [];
			var amount = [];
			var amount_usd = [];

			$('.inv_num').each(function(){
				inv_num.push($(this).val());
			});

			$('.inv_name').each(function(){
				inv_name.push($(this).val());
			});

			$('.fix_name').each(function(){
				fix_name.push($(this).val());
			});

			// $('.inv_item').each(function(){
			// 	var arr_tmp = [];
			// 	inv_item.push({'class' : $(this).closest("tr").attr('class'), 'item_name' : $(this).val()});
			// });

			// var inv_item_new = [];
			// var tmp = [];
			// $.each(inv_item, function(index, value){
			// 	tmp.push(value.item_name);
			// 	if (typeof inv_item[index+1] !== 'undefined') {
			// 		if (inv_item[index+1].class != value.class) {
			// 			inv_item_new.push(tmp);
			// 			tmp = [];
			// 		}
			// 	} else {
			// 		inv_item_new.push(tmp);
			// 		tmp = [];
			// 	}
			// })

			$('.vendor').each(function(){
				var cls = $(this).closest("tr").attr('class');
				cls = cls.split("_")[1];
				var invoice =  $("#invoice_"+cls).val();
				vendor.push({'invoice' : invoice, 'vendor' : $(this).val()});
			});

			$('.currency').each(function(){
				var cls = $(this).closest("tr").attr('class');
				cls = cls.split("_")[1];
				var invoice =  $("#invoice_"+cls).val();
				currency.push({'invoice' : invoice, 'currency' : $(this).val()});
			});

			$('.amount').each(function(){
				var cls = $(this).closest("tr").attr('class');
				cls = cls.split("_")[1];
				var invoice =  $("#invoice_"+cls).val();
				amount.push({'invoice' : invoice, 'amount' : $(this).val()});
			});

			$('.amount_usd').each(function(){
				var cls = $(this).closest("tr").attr('class');
				cls = cls.split("_")[1];
				var invoice =  $("#invoice_"+cls).val();
				amount_usd.push({'invoice' : invoice, 'amount_usd' : $(this).val()});
			});

			var myFormData = new FormData();
			myFormData.append('investment_number', $("#investment_number").val());
			myFormData.append('invoice_num', JSON.stringify(inv_num));
			myFormData.append('invoice_name', JSON.stringify(inv_name));

			var len = 0;
			$('.inv_file').each(function(index){
				myFormData.append('invoice_file_'+index, $(this).prop('files')[0]);
				len++;
			});

			// myFormData.append('invoice_item', inv_item_new);

			myFormData.append('len', len);
			// myFormData.append('currency', JSON.stringify(currency));
			// myFormData.append('amount', JSON.stringify(amount));
			// myFormData.append('amount_usd', JSON.stringify(amount_usd));
			// myFormData.append('amount_usd', JSON.stringify(amount_usd));

			$.ajax({
				url: '{{ url("send/fixed_asset/invoice_asset_form") }}',
				type: 'POST',
				processData: false, 
				contentType: false,
				dataType : 'JSON',
				data: myFormData,
				success: function (data) { 
					$("#loading").hide();
					openSuccessGritter('Success', 'Upload Successfully');
					// setTimeout(location.reload.bind(location), 2000);

					setTimeout(function(){window.location.reload()},2000);
				}
			});
		}

		function filter(phaseName) {
			return clasic.filter(item => {
				return item.category === phaseName;
			});
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
			audio_ok.play();
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
			audio_error.play();
		}

	</script>
	@endsection