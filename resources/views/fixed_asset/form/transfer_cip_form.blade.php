@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:left;
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
	hr {
		border-top-color: #ddd;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">	
	<h1>
		List of {{ $page }}
	</h1>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<center style='font-size: 22px'><b>Transfer Asset CIP</b></center>
					<div class="col-xs-12" style="padding-bottom: 1%;">
						<div class="col-xs-2" style="padding: 0px;">
							<span style="font-weight: bold; font-size: 16px;">Select Asset to Transfer : </span>
						</div>
						<div class="col-xs-5">
							<select class="select2" data-placeholder="Select Asset" style="width:100%" id="asset_list">';
							</select>
						</div>
						<div class="col-xs-2">
							<button class="btn btn-sm btn-success" onclick="add_asset()"><i class="fa fa-plus"></i> Add Asset</button>
						</div>
					</div>
					<div class="col-xs-6" id="div_asset" style="border: 1px solid black; padding: 2px 5px 2px 5px">
					</div>

					<div class="col-xs-6" style="border: 1px solid black; padding: 2px 5px 2px 5px">
						<div class="col-xs-12" style="padding-bottom: 1%; margin-top: 5px;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Invoice Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="invoice_number" id="invoice_number" placeholder="Invoice Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<br>
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Name on Invoice<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="invoice_name" id="invoice_name" rows='1' placeholder="Name on Invoice" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Fixed Asset Name:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="item_name" id="item_name" rows='1' placeholder="Fixed Asset Name" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Clasification:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-3">
								<select class="select2" id="clasification" name="clasification" style="width: 100%" data-placeholder="Large Clasification">
									<option></option>
								</select>
							</div>
							<div class="col-xs-6 col-xs-offset-3">
								<select class="select2" id="clasification_mid" name="clasification_mid" style="width: 100%" data-placeholder="Middle Clasification (lifetime)" onchange="clasification_change(this)">
									<option></option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Investment Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="investment_number" id="investment_number" placeholder="Investment Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Budget Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="budget" id="budget" placeholder="Budget Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Vendor:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="vendor" id="vendor" placeholder="Vendor" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Currency:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input type="text" class="form-control" name="currency" id="currency" value="USD" readonly>
									<!-- <select class="select2" id="currency" name="currency" data-placeholder="Select Currency" style="width: 100%" required>
										<option></option>
										<option value="IDR">IDR</option>
										<option value="USD">USD</option>
										<option value="JPY">JPY</option>
									</select> -->
								</div>
							</div>

							<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Original Amount:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-8">
									<input class="form-control" type="text" name="amount" id="amount" placeholder="amount" style=" font-size: 15px;" required>
								</div>
							</div> -->

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Amount in USD:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-8">
									<input type="hidden" id="exchange" value="0">
									<input class="form-control" type="text" name="amount_usd" id="amount_usd" placeholder="amount in USD" style=" font-size: 15px;" required>
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">PIC:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-8">
									<input class="form-control" type="text" name="pic" id="pic" placeholder="PIC Name" style=" font-size: 15px;" value="{{ $assets[0]->name }}" required readonly>

								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Asset Location:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-4">
									<select class="form-control select2" id="location" name="location" data-placeholder="Select Location" required style="width: 100%">
										<option value=""></option>
									</select>

									<input type="hidden" name="pic_asset" id="pic_asset" value="{{ $assets[0]->pic }}" readonly>
								</div>
							</div>



							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Usage Term:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-5">
									<input type="text" class="form-control" name="usage_term" value="soon" readonly>
								</div>
							</div>

							<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">Asset Photo:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-8">
									<input type="file" name="asset_foto" id="asset_foto" required>
								</div>
							</div> -->

							<!-- FA SECTION -->
							<div class="col-xs-12">
								<center><label>- - - - - - - - - - - - &nbsp;  FA SECTION &nbsp; - - - - - - - - - - - -</label></center>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Category Code:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input class="form-control" type="text" name="category_code" id="category_code" placeholder="Category Code" style=" font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Category:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input class="form-control" type="text" name="category" id="category" placeholder="Category" style=" font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">SAP ID:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input class="form-control" type="text" name="sap" id="sap" placeholder="SAP ID" style=" font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Registration Date:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input class="form-control datepicker" type="text" name="reg_date" id="reg_date" placeholder="Select Date" style=" font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Depreciation Key:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input class="form-control" type="text" name="depreciation" id="depreciation" placeholder="Depreciation Key" style=" font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Usefull Life:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-3">
										<input class="form-control" type="text" id="usefulllife" style=" font-size: 15px;" readonly>
									</div>
								</div>

								<!-- <div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">SAP File:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="file" id="sap_file" required>
									</div>
								</div> -->

							</div>


							<div class="col-xs-12" style="padding-right: 12%; padding-left: 12%; margin-top: 10px">
								<button type="submit" class="btn btn-primary" style="width: 100%" onclick="save_transfer()"><b><i class="fa fa-angle-double-down"></i> Save <i class="fa fa-angle-double-down"></i></b></button>
							</div>
						</div>

						<div class="col-xs-12" style="padding: 2px 5px 2px 5px; border: 1px solid black">
							<table class="table table-bordered" style="width: 100%; margin-top: 10px" id="transfer_table">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>No</th>
										<th>CIP Number</th>
										<th>CIP Name</th>
										<th>FA Number</th>
										<th>FA Name</th>
										<th>CIP Amount</th>
										<th>Amount Detail<span class="text-red">*</span></th>
										<th>FA Amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="transfer_body">
								</tbody>
							</table>
							<div class="form-group">
								<label class="col-sm-2 control-label">SAP File<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="file" name="sap_file" id="sap_file" accept="application/pdf">
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<br>
									<button class="btn btn-success" onclick="save_cip()" style="width: 100%; font-size: 20px; font-weight: bold"><i class="fa fa-check"></i> SUBMIT</button>
								</div>
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

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
		var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

		var selected = [];
		var clasic = [];
		var exchange_rate = <?php echo json_encode($exchange_rate); ?>;
		var assets = <?php echo json_encode($assets); ?>;

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			clasic = <?php echo json_encode($clasification); ?>;
			var	xCategories = [];
			$.each(clasic, function(index, value){
				if(xCategories.indexOf(value.category) === -1){
					xCategories[xCategories.length] = value.category;
				}

			})

			$("#clasification").empty();
			cat = "<option></option>";
			$.each(xCategories, function(index, value){
				cat += "<option value='"+value+"'>"+value+"</option>";
			})
			$("#clasification").append(cat);

			$("#clasification_edit").empty();
			cat = "<option></option>";
			$.each(xCategories, function(index, value){
				cat += "<option value='"+value+"'>"+value+"</option>";
			})
			$("#clasification_edit").append(cat);

			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true
			});

			draw_data();
			fetchTransfer();
		});

		$(function () {
			$('.select2').select2({
				allowClear : true
			});
		})

		function draw_data() {
			$("#asset_list").empty();
			var body = '';

			$("#location").empty();
			var locs = [];
			loc = '<option value=""></option>';

			body += '<option value=""></option>';
			$.each(assets, function(key, value) {
				body += '<option value="'+value.sap_number+'">'+value.fixed_asset_name+' ( '+value.sap_number+' ) </option>';


				if(locs.indexOf(value.location) === -1){
					locs[locs.length] = value.location;
					loc += '<option value="'+value.location+'">'+value.location+'</option>';
				}

			});

			$("#location").append(loc);
			$("#asset_list").append(body);
		}

		function add_asset() {
			var param = $("#asset_list").val();
			var body = '';
			var stat = true;

			$.each(assets, function(key, value) {
				if (value.sap_number == param) {
					if(jQuery.inArray(value.sap_number, selected) === -1) {
						if (selected.length == 0) {
							$("#invoice_number").val(value.invoice_number);
							$("#invoice_name").val(value.invoice_name);
							$("#item_name").val(value.fixed_asset_name);
							$("#clasification").val(value.clasification_category).trigger('change');
							$("#clasification_mid").val(value.clasification).trigger('change');
							$("#investment_number").val(value.investment);
							$("#budget").val(value.budget_number);
							$("#vendor").val(value.vendor);
							$("#amount_usd").val(value.amount_usd);
							$("#category_code").val(value.category_code);
							$("#category").val(value.category);
							$("#depreciation").val(value.depreciation_key);
							$("#usefulllife").val(value.usefull_life);
						}

						body += '<table style="width : 100%">';
						body += '<tr>';
						body += '<td style="width: 30%">Asset Number</td>';
						body += '<td>'+value.sap_number+'</td>';
						body += '<td style="width: 1%"><button class="btn btn-danger btn-xs" title="Hapus" onclick="delete_asset(this,\''+value.sap_number+'\')"><i class="fa fa-trash"></i></button></td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Asset Name</td>';
						body += '<td colspan="2">'+value.fixed_asset_name+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Clasification Category</td>';
						body += '<td colspan="2">'+value.clasification_category+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Amount (USD)</td>';
						body += '<td colspan="2">'+value.amount_usd+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Vendor</td>';
						body += '<td colspan="2">'+value.vendor+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Acquisition Date</td>';
						body += '<td colspan="2">'+value.acquisition_date+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Section</td>';
						body += '<td colspan="2">'+value.section+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>PIC</td>';
						body += '<td colspan="2">'+value.name+'</td>';
						body += '</tr>';
						body += '<tr>';
						body += '<td>Location</td>';
						body += '<td colspan="2">'+value.location+'</td>';
						body += '</tr>';
						body += '</tr><td colspan=3><br><hr></td></tr>';
						body += '</table>';
						selected.push(value.sap_number);
					} else {
						stat = false;
					}
				} 
			});
			$("#div_asset").append(body);

			if (!stat) {
				openErrorGritter('Error', 'Asset Sudah Dipilih');
			}
		}

		function delete_asset(elem, fa_num) {
			$(elem).closest('table').remove();

			selected = jQuery.grep(selected, function(value) {
				return value != fa_num;
			});
		}

		function save_cip() {
			if (confirm('Are you sure want to Transfer this CIP to Normal Asset ?')) {
				var assets = [];
				$("#loading").show();

				if ($('#sap_file').prop('files').length < 1) {
					openErrorGritter('Error', 'Please Select SAP File');
					return false;
				}

				var stat = true;
				$('.amount_detail').each(function(i, obj) {
					if ($(obj).val() == '') {
						stat = false;
					}

					asset = $(obj).attr('id').split('_');

					assets.push({'form_number' : asset[2], 'cip_number' : asset[1], 'amount_detail' : $(obj).val()});
				});

				if (!stat) {
					openErrorGritter('Error', 'Please Complete All "Amount Use" Field');
					return false;
				}

				var formData = new FormData();
				formData.append('assets', JSON.stringify(assets));
				formData.append('cip_number', '{{  Request::segment(6) }}');
				formData.append('sap_file', $('#sap_file').prop('files')[0]);

				$.ajax({
					url: '{{ url("post/fixed_asset/cip/true_transfer") }}',
					type: 'POST',
					data: formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (result, status, xhr) {
						$("#loading").hide();
						window.setTimeout(function(){ window.location.href = "{{ url('index/fixed_asset/transfer_cip') }}";}, 2000);
					}
				})

			}
		}


		function save_transfer() {
		// e.preventDefault();    
		$("#loading").show();
		var formData = new FormData();
		formData.append('assets', selected);
		formData.append('form_number', '{{  Request::segment(6) }}');
		formData.append('invoice_number', $("#invoice_number").val());
		formData.append('invoice_name', $("#invoice_name").val());
		formData.append('item_name', $("#item_name").val());
		formData.append('clasification_code', $("#clasification_mid").val());
		formData.append('clasification_name', $("#clasification_mid option:selected").text());
		formData.append('investment_number', $("#investment_number").val());
		formData.append('budget', $("#budget").val());
		formData.append('vendor', $("#vendor").val());
		formData.append('currency', $("#currency").val());
		formData.append('amount', $("#amount").val());
		formData.append('amount_usd', $("#amount_usd").val());
		formData.append('location', $("#location").val());
		formData.append('pic', $("#pic").val());
		formData.append('pic_asset', $("#pic_asset").val());
		// formData.append('asset_foto', $('#asset_foto').prop('files')[0]);

		formData.append('category_code', $("#category_code").val());
		formData.append('category', $("#category").val());
		formData.append('sap_number', $("#sap").val());
		formData.append('register_date', $("#reg_date").val());
		formData.append('depreciation', $("#depreciation").val());
		formData.append('life', $("#usefulllife").val());
		// formData.append('sap_file', $("#sap_file").prop('files')[0]);

		$.ajax({
			url: '{{ url("post/fixed_asset/cip/transfer") }}',
			type: 'POST',
			data: formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: function (result, status, xhr) {
				$("#loading").hide();
				fetchTransfer();
			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function fetchTransfer() {
		var data = {
			form_number : '{{  Request::segment(6) }}'
		}

		$.get('{{ url("fetch/fixed_asset/transfer_cip") }}', data, function(result, status, xhr){
			if(result.status){
				$("#transfer_body").empty();
				body = "";

				$.each(result.transfers, function(key, value) {
					body += "<tr>";
					body += "<td>"+(key+1)+"</td>";
					body += "<td>"+value.cip_sap_number+"</td>";
					body += "<td>"+value.cip_asset_name+"</td>";
					body += "<td>"+value.new_sap_number+"</td>";
					body += "<td>"+value.new_asset_name+"</td>";
					body += "<td>$ "+value.cip_amount_usd+"</td>";

					body += "<td><div class='input-group'><span class='input-group-addon'><i class='fa fa-usd'></i></span><input type='text' class='form-control amount_detail' placeholder='Amount Use' id='ad_"+value.cip_sap_number+"_"+value.cip_form_number+"'></div></td>";

					var con = 1;

					$.each(result.count_cip, function(key2, value2) {
						if (value2.new_sap_number == value.new_sap_number) {
							con = parseInt(value2.con);
						}
					})

					if ( typeof result.transfers[key-1] !== 'undefined' ) {
						if (value.new_sap_number != result.transfers[key-1].new_sap_number) {
							body += "<td rowspan='"+con+"'>$ "+value.amount_usd+"</td>";
							
						}
					} else {
						body += "<td rowspan='"+con+"'>$ "+value.amount_usd+"</td>";
					}

					if ( typeof result.transfers[key-1] !== 'undefined' ) {
						if (value.new_sap_number != result.transfers[key-1].new_sap_number) {
							body += "<td rowspan='"+con+"'><center><button class='btn btn-danger'><i class='fa fa-trash'></i> Delete</button></center></td>";
						}
					} else {
						body += "<td rowspan='"+con+"'><center><button class='btn btn-danger'><i class='fa fa-trash'></i> Delete</button></center></td>";
					}
				})

				$("#transfer_body").append(body);
			}
		})
	}


	$('#clasification').on('change', function() {
		var val = this.value;
		$("#clasification_mid").empty();
		cat = "<option></option>";

		filteredArray = clasic.filter(function(item)
		{
			return item.category.indexOf(this.value) > -1;
		});

		$.each(clasic, function(index, value){
			if (value.category == val) {
				cat += "<option value='"+value.category_code+"'>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			}
		})
		$("#clasification_mid").append(cat);
	});

	$('#amount').keyup(function() {
		var ex = parseInt($("#exchange").val());
		if (this.value != '') {
			var amount = parseFloat(this.value);
		} else {
			var amount = 0;
		}

		usd = (amount / ex).toFixed(2);

		$("#amount_usd").val(usd);
	});

	function clasification_change(elem) {
		var vals = $("#clasification_mid option:selected").text();

		lifetime = vals.split('( ');
		lifetime = lifetime[1].split(' years')[0];

		$("#usefulllife").val(lifetime);
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