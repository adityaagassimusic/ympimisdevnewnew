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
			<a href="{{ url('files/fixed_asset/manual_book/Manual Book Fixed Asset - Registrasi  Asset.pdf') }}" class="btn btn-warning btn-xs"><i class="fa fa-question"></i>Manual Book - Asset Registration</a>
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
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-body">
					<table id="requestAssetTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Form Number</th>
								<th>Investment Number</th>
								<th>Att</th>
								<th>Investment Applicant</th>
								<th>Create Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="requestAssetBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<div class="box no-border">
				<div class="box-header"></div>
				<div class="box-body">
					<table id="registrationTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%">Request Date</th>
								<th style="width: 5%">Form Number</th>
								<th style="width: 10%">Fixed Asset Name</th>
								<!-- <th style="width: 10%">Clasification</th> -->
								<th style="width: 3%">Invoice Number</th>
								<th style="width: 10%">Vendor</th>
								<th style="width: 5%">Investment Number</th>
								<!-- <th style="width: 9%">Currency</th> -->
								<!-- <th style="width: 5%">Original Amount</th> -->
								<!-- <th style="width: 5%">Amount In USD</th> -->
								<!-- <th style="width: 3%">Budget Number</th> -->
								<th style="width: 3%">Applicant</th>
								<th style="width: 3%">Invoice File</th>
								<th style="width: 3%">Jurnal Entry</th>
								<th style="width: 3%">Status</th>
								<th style="width: 3%">Report</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
						<tbody id="RegistrationBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Registration Fixed Asset Form</h1>
					</div>
					<form id="data" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<br>
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Registration Form Number</span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="invoice_form_number" id="invoice_form_number" style="width: 100%; font-size: 15px;" readonly>
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
								<input type="text" class="form-control" name="item_name" id="item_name" rows='1' placeholder="Fixed Asset Name" style="width: 100%; font-size: 15px;" required maxlength="50">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Invoice Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="invoice_number" id="invoice_number" placeholder="Invoice Number" style=" font-size: 15px;" required>
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
								<select class="select2" id="clasification_mid" name="clasification_mid" style="width: 100%" data-placeholder="Middle Clasification (lifetime)">
									<option></option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Investment Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="investment_number" id="investment_number" placeholder="Invoice Number" style=" font-size: 15px;" required>
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
							<div class="col-xs-4">
								<input class="form-control" type="text" name="vendor" id="vendor" placeholder="Vendor" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Currency:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="select2" id="currency" name="currency" data-placeholder="Select Currency" style="width: 100%" required>
									<option></option>
									<option value="IDR">IDR</option>
									<option value="USD">USD</option>
									<option value="JPY">JPY</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Original Amount:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="amount" id="amount" placeholder="amount" style=" font-size: 15px;" required>
							</div>
						</div>

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
								<input class="form-control" type="text" name="pic" id="pic" placeholder="PIC Name" style=" font-size: 15px;" required readonly>

							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Location:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								{{-- <input class="form-control" type="text" name="location" id="location" placeholder="location" style=" font-size: 15px;" required> --}}
								<select class="form-control select2" id="location" name="location" data-placeholder="Select Location" required style="width: 100%">
									<option value=""></option>
								</select>
								
								<input type="hidden" name="pic_asset" id="pic_asset" readonly>
							</div>
						</div>

						

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Usage Term:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-5">
								<div class="form-group">
									<div class="radio">
										<label>
											<input type="radio" name="usage_term" id="not_use" value="not use yet" required>
											Not Use Yet ( if yes fill usage estimation)
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="usage_term" id="soon" value="soon">
											Soon
										</label>
									</div>
								</div>
								<div class="form-group" id="usage_tab" style="display: none">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control datepicker" type="text" name="usage_est" id="usage_est" placeholder="Select Date Usage Estimation" style="font-size: 15px;">
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Photo:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="file" name="asset_foto" id="asset_foto"  accept="image/*">
							</div>
						</div>


						<div class="col-xs-12" style="padding-right: 12%;">
							<button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i> Submit</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="CheckModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Check Registration Fixed Asset</h1>
					</div>
					<form id="update" method="post" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<br>
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Name on Invoice<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="id_update">
								<input type="hidden" id="id_asset">
								<input type="text" class="form-control" name="invoice_name_update" id="invoice_name_update" rows='1' placeholder="Name on Invoice" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Fixed Asset Name:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="item_name_update" id="item_name_update" rows='1' placeholder="Fixed Asset Name" style="width: 100%; font-size: 15px;" required maxlength="50">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Invoice Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="invoice_number_update" id="invoice_number_update" placeholder="Invoice Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Clasification:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-3">
								<input type="hidden" id="clasification_name_update">
								<select class="select3" id="clasification_update" name="clasification_update" style="width: 100%" data-placeholder="Large Clasification">
									<option></option>
								</select>
							</div>
							<div class="col-xs-6 col-xs-offset-3">
								<select class="select3" id="clasification_mid_update" name="clasification_mid_update" style="width: 100%" data-placeholder="Middle Clasification (lifetime)">
									<option></option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Investment Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="investment_number_update" id="investment_number_update" placeholder="Invoice Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Budget Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="budget_update" id="budget_update" placeholder="Budget Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Vendor:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="vendor_update" id="vendor_update" placeholder="Vendor" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Currency:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="select3" id="currency_update" name="currency_update" data-placeholder="Select Currency" style="width: 100%" required>
									<option></option>
									<option value="IDR">IDR</option>
									<option value="USD">USD</option>
									<option value="JPY">JPY</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Original Amount:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="amount_update" id="amount_update" placeholder="amount" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Amount in USD:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="exchange_update" value="0">
								<input class="form-control" type="text" name="amount_usd_update" id="amount_usd_update" placeholder="amount in USD" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">PIC:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="pic_update" id="pic_update" placeholder="PIC Name" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Location:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" id="location_update" name="location_update" data-placeholder="Select Location" required style="width: 100%">
									<option value=""></option>
								</select>
								<input type="hidden" name="pic_asset_update" id="pic_asset_update" readonly>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Usage Term:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-5">
								<div class="form-group">
									<div class="radio">
										<label>
											<input type="radio" name="usage_term_update" id="not_use_update" value="not use yet">
											Not Use Yet ( if yes fill usage estimation)
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="usage_term_update" id="soon_update" value="soon">
											Soon
										</label>
									</div>
								</div>
								<div class="form-group" id="usage_tab_update" style="display: none">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control datepicker" type="text" name="usage_est_update" id="usage_est_update" placeholder="Select Date Usage Estimation" style="font-size: 15px;">
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Photo:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="file" name="asset_foto_update" id="asset_foto_update"  accept="image/*">
								<a href="#" id="image_asset_update" target="_blank"><i class="fa fa-file-image-o"></i> Asset Image</a>
							</div>
						</div>

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
									<input class="form-control" type="text" id="usefulllife" style=" font-size: 15px;">
								</div>
							</div>

							<div class="col-xs-12" style="padding-bottom: 1%;">
								<div class="col-xs-3" align="right" style="padding: 0px;">
									<span style="font-weight: bold; font-size: 16px;">SAP File:<span class="text-red">*</span></span>
								</div>
								<div class="col-xs-8">
									<input type="file" id="sap_file" >
								</div>
							</div>

						</div>

						<div class="col-xs-12" style="padding-right: 12%;">
							<button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i> Save & Send Approval</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>

	<!--  ------------------------------  EDIT MODAL  ----------------------------- -->

	<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Registration Fixed Asset Form</h1>
					</div>
					<form id="data_edit" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%; margin-top: 5px;">
							<br>
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Registration Form Number</span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="id_edit">
								<input type="hidden" id="id_asset_edit">
								<input type="text" class="form-control" name="invoice_form_number_edit" id="invoice_form_number_edit" style="width: 100%; font-size: 15px;" readonly>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<br>
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Name on Invoice<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="invoice_name_edit" id="invoice_name_edit" rows='1' placeholder="Name on Invoice" style="width: 100%; font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Fixed Asset Name:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="text" class="form-control" name="item_name_edit" id="item_name_edit" rows='1' placeholder="Fixed Asset Name" style="width: 100%; font-size: 15px;" required maxlength="50">
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Invoice Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="invoice_number_edit" id="invoice_number_edit" placeholder="Invoice Number" style=" font-size: 15px;" required>
							</div>
						</div>
						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Clasification:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-3">
								<input type="hidden" id="clasification_name_edit">
								<select class="select4" id="clasification_edit" name="clasification_edit" style="width: 100%" data-placeholder="Large Clasification">
									<option></option>
								</select>
							</div>
							<div class="col-xs-6 col-xs-offset-3">
								<select class="select4" id="clasification_mid_edit" name="clasification_mid_edit" style="width: 100%" data-placeholder="Middle Clasification (lifetime)">
									<option></option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Investment Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="investment_number_edit" id="investment_number_edit" placeholder="Investment Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Budget Number:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="budget_edit" id="budget_edit" placeholder="Budget Number" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Vendor:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<input class="form-control" type="text" name="vendor_edit" id="vendor_edit" placeholder="Vendor" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Currency:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="select4" id="currency_edit" name="currency_edit" data-placeholder="Select Currency" style="width: 100%" required>
									<option></option>
									<option value="IDR">IDR</option>
									<option value="USD">USD</option>
									<option value="JPY">JPY</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Original Amount:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="amount_edit" id="amount_edit" placeholder="amount" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Amount in USD:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input type="hidden" id="exchange_edit" value="0">
								<input class="form-control" type="text" name="amount_usd_edit" id="amount_usd_edit" placeholder="amount in USD" style=" font-size: 15px;" required>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">PIC:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-8">
								<input class="form-control" type="text" name="pic_edit" id="pic_edit" placeholder="PIC Name" style=" font-size: 15px;" required readonly>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Location:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select4" id="location_edit" name="location_edit" data-placeholder="Select Location" required style="width: 100%">
									<option value=""></option>
								</select>
								<input type="hidden" name="pic_asset_edit" id="pic_asset_edit" readonly>
							</div>
						</div>



						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Usage Term:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-5">
								<div class="form-group">
									<div class="radio">
										<label>
											<input type="radio" name="usage_term_edit" id="not_use_edit" value="not use yet" required>
											Not Use Yet ( if yes fill usage estimation)
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="usage_term_edit" id="soon_edit" value="soon">
											Soon
										</label>
									</div>
								</div>
								<div class="form-group" id="usage_tab_edit" style="display: none">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input class="form-control datepicker" type="text" name="usage_est_edit" id="usage_est_edit" placeholder="Select Date Usage Estimation" style="font-size: 15px;">
									</div>
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Asset Photo:</span>
							</div>
							<div class="col-xs-8">
								<input type="file" name="asset_foto_edit" id="asset_foto_edit">
								<a href="#" id="image_asset" target="_blank"><i class="fa fa-file-image-o"></i> Asset Image</a>
							</div>
						</div>


						<div class="col-xs-12" style="padding-right: 12%;">
							<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-check"></i> Save</button>
						</div>
					</form>
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

	var clasic = [];
	var exchange_rate = <?php echo json_encode($exchange_rate); ?>;
	var pics = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		console.log(exchange_rate);

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

		$("#clasification_update").empty();
		cat = "<option></option>";
		$.each(xCategories, function(index, value){
			cat += "<option value='"+value+"'>"+value+"</option>";
		})
		$("#clasification_update").append(cat);

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		draw_data();
	});

	$(function () {
		$('.select2').select2({
			dropdownParent: $('#createModal')
		});
	})

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#CheckModal')
		});
	})

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#editModal')
		});
	})

	$('input:radio[name="usage_term"]').change(
		function(){
			if ($(this).is(':checked') && $(this).val() == 'not use yet') {
				$("#usage_tab").show();
			} else {
				$("#usage_tab").hide();
			}
		});

	$('input:radio[name="usage_term_edit"]').change(
		function(){
			if ($(this).is(':checked') && $(this).val() == 'not use yet') {
				$("#usage_tab_edit").show();
			} else {
				$("#usage_tab_edit").hide();
			}
		});

	$('input:radio[name="usage_term_update"]').change(
		function(){
			if ($(this).is(':checked') && $(this).val() == 'not use yet') {
				$("#usage_tab_update").show();
			} else {
				$("#usage_tab_update").hide();
			}
		});

	function filterByProperty(array, prop, value){
		var filtered = [];
		for(var i = 0; i < array.length; i++){

			var obj = array[i];

			for(var key in obj){
				if(typeof(obj[key] == "object")){
					var item = obj[key];
					if(item[prop] == value){
						filtered.push(item);
					}
				}
			}

		}    

		return filtered;

	}


	$('#clasification').on('change', function() {
		var val = this.value;
		$("#clasification_mid").empty();
		cat = "<option></option>";

		filteredArray = clasic.filter(function(item)
		{
			return item.category.indexOf(this.value) > -1;
		});
		// console.log(clasic);

		$.each(clasic, function(index, value){
			if (value.category == val) {
				cat += "<option value='"+value.category_code+"'>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			}
		})
		// console.log(filteredArray);
		$("#clasification_mid").append(cat);
	});

	$('#clasification_edit').on('change', function() {
		$("#clasification_mid_edit").empty();
		
		cat = "<option></option>";

		filteredArray = clasic.filter(function(item)
		{
			return item.category.indexOf(this.value) > -1;
		});

		$.each(clasic, function(index, value){
			if ($("#clasification_name_edit").val() == value.clasification_name) {
				cat += "<option value='"+value.category_code+"' selected>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			} else {
				cat += "<option value='"+value.category_code+"'>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			}

		})
		$("#clasification_mid_edit").append(cat);
	});

	$('#clasification_update').on('change', function() {
		$("#clasification_mid_update").empty();
		
		cat = "<option></option>";

		filteredArray = clasic.filter(function(item)
		{
			return item.category.indexOf(this.value) > -1;
		});

		$.each(clasic, function(index, value){
			if ($("#clasification_name_update").val() == value.clasification_name) {
				cat += "<option value='"+value.category_code+"' selected>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			} else {
				cat += "<option value='"+value.category_code+"'>"+value.category_code+". "+value.clasification_name+" ( "+value.life_time+" years )"+"</option>";
			}

		})
		$("#clasification_mid_update").append(cat);
	});

	$('#currency').on('change', function() {
		var val = this.value;
		$.each(exchange_rate, function(index, value){
			if (val == value.currency) {
				$("#exchange").val(value.rate);
			}
		})
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


	$('#currency_update').on('change', function() {
		var val = this.value;
		$.each(exchange_rate, function(index, value){
			if (val == value.currency) {
				$("#exchange_update").val(value.rate);
			}
		})
	});

	$('#amount_update').keyup(function() {
		var ex = parseInt($("#exchange_update").val());
		if (this.value != '') {
			var amount = parseFloat(this.value);
		} else {
			var amount = 0;
		}

		usd = (amount / ex).toFixed(2);

		$("#amount_usd_update").val(usd);
	});

	$('#currency_edit').on('change', function() {
		var val = this.value;
		$.each(exchange_rate, function(index, value){
			if (val == value.currency) {
				$("#exchange_edit").val(value.rate);
			}
		})
	});

	$('#amount_edit').keyup(function() {
		var ex = parseInt($("#exchange_edit").val());
		if (this.value != '') {
			var amount = parseFloat(this.value);
		} else {
			var amount = 0;
		}

		usd = (amount / ex).toFixed(2);

		$("#amount_usd_edit").val(usd);
	});

	function draw_data() {
		$.get('{{ url("fetch/fixed_asset/registration_asset_form") }}', function(result, status, xhr) {
			$('#registrationTable').DataTable().clear();
			$('#registrationTable').DataTable().destroy();
			$("#RegistrationBody").empty();
			body = "";

			$.each(result.assets, function(index, value){
				body += "<tr>";
				body += "<td>"+(value.request_date || '')+"</td>";
				body += "<td>"+value.form_number+"</td>";
				body += "<td>"+value.asset_name+"</td>";
				body += "<td>"+value.invoice_number+"</td>";
				body += "<td>"+value.vendor+"</td>";
				body += "<td>"+value.investment_number+"</td>";
				body += "<td>"+value.pic+"</td>";
				body += "<td>";

				if (value.invoice_file) {
					var att = value.invoice_file.split(',');

					$.each(att, function(index2, value2){
						body += "<a href='{{ url('files/fixed_asset') }}/"+value2+"' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-file-pdf-o'></i> File Invoice "+(index2+1)+"</a>";
					})
				}

				body += "</td>";
				body += "<td><a href='{{ url('files/fixed_asset/sap_file') }}/"+value.sap_file+"' target='_blank' class='btn btn-danger btn-xs'><i class='fa fa-file-pdf-o'></i> File Jurnal</a></td>";
				body += "<td>"+value.status+"</td>";
				body += "<td><a href='{{ url('files/fixed_asset/report_registration') }}/Reg_"+value.form_number+".pdf' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-file-pdf-o'></i> Asset Report</a></td>";
				body += "<td>";
				// if (value.status == 'created') {
					if ('{{Auth::user()->username}}' == 'PI0905001' && value.status == 'created') {
						body += "<button class='btn btn-warning btn-xs' onclick='openCheckModal("+value.id+")'><i class='fa fa-eye'></i> check</button><br>";
					} else if('{{Auth::user()->username}}' == 'PI2002021') {
						body += "<button class='btn btn-warning btn-xs' onclick='openCheckModal("+value.id+")'><i class='fa fa-eye'></i> check</button><br>";
						body += "<button class='btn btn-primary btn-xs' onclick='openDetail("+value.id+")'><i class='fa fa-pencil'></i> Edit</button><br>";
						body += "<button class='btn btn-success btn-xs' onclick='openSendMail("+value.id+",\""+value.status+"\")'><i class='fa fa-send'></i> Send Email</button>";

					} else {
						body += "<button class='btn btn-primary btn-xs' onclick='openDetail("+value.id+")'><i class='fa fa-pencil'></i> Edit</button><br>";
						if (value.reject_status && (value.status != 'hold' || value.status != 'reject' || value.status != 'fa_receive')) {
							body += "<button class='btn btn-success btn-xs' onclick='openSendMail("+value.id+",\""+value.status+"\")'><i class='fa fa-send'></i> Send Email</button>";
						}
					}
					body += "</td>";
				// }
				body += "</tr>";
			});
			$('#RegistrationBody').append(body);

			var table = $('#registrationTable').DataTable({
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


			$('#requestAssetTable').DataTable().clear();
			$('#requestAssetTable').DataTable().destroy();
			$("#requestAssetBody").empty();
			body_req = "";

			$.each(result.asset_requests, function(index, value){
				body_req += "<tr>";
				body_req += "<td>"+value.form_id+"</td>";
				body_req += "<td>"+value.investment_number+"</td>";


				var att = value.atts.split(',');
				body_req += "<td>";

				$.each(att, function(index2, value2){
					body_req += "<a href='{{ url('files/fixed_asset/') }}/"+value2+"' target='_blank'>"+value2+"</a><br>";

				})

				body_req += "</td>";

				body_req += "<td>"+value.name+"</td>";
				body_req += "<td>"+value.create_at+"</td>";
				body_req += "<td><button class='btn btn-success btn-xs' onclick='creatNew(\""+value.form_id+"\")'><i class='fa fa-plus'></i> Create Registration Form</button></td>";
				body_req += "</tr>";
			});

			$("#requestAssetBody").append(body_req);

			var table = $('#requestAssetTable').DataTable({
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

$("form#data").submit(function(e) {
	$("#loading").show();


	if( document.getElementById("asset_foto").files.length == 0 ){
		openErrorGritter('Error', 'No files selected');
		$("#loading").hide();
		return false;
	}

	var invoice_name = $("#invoice_name").val();
	var form_number = $("#invoice_form_number").val();
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
	formData.append('location', location);
	formData.append('pic', pic);
	formData.append('pic_asset', $("#pic_asset").val());
	formData.append('usage_term', usage_term);
	formData.append('usage_est', usage_est);
	formData.append('asset_foto', $('#asset_foto').prop('files')[0]);


	$.ajax({
		url: '{{ url("send/fixed_asset/registration_asset_form") }}',
		type: 'POST',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

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
			$("#location").empty();
			$("#location").append("<option value=''></option>");
			$("#pic").val("");
			$('input[name="usage_term"]').prop('checked', false);
			$("#usage_est").val("");


			$('#createModal').modal('hide');

			openSuccessGritter('Success', result.message);

				// location.reload(true);
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

$("form#data_edit").submit(function(e) {
	$("#loading").show();

	// if(document.getElementById("asset_foto_edit").files.length == 0 ){
	// 	openErrorGritter('Error', 'No files selected');
	// 	$("#loading").hide();
	// 	return false;
	// }

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
	formData.append('asset_id', $("#id_asset_edit").val());
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
	formData.append('asset_foto', $('#asset_foto_edit').prop('files')[0]);

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

function openCheckModal(id) {
	$("#CheckModal").modal('show');

	var data = {
		id : id
	}

	$.get('{{ url("fetch/fixed_asset/registration_asset_form/by") }}', data, function(result, status, xhr) {
		$("#id_update").val(id);
		$("#id_asset").val(result.asset.form_number);
		$("#invoice_name_update").val(result.asset.invoice_name);
		$("#item_name_update").val(result.asset.asset_name);
		$("#invoice_number_update").val(result.asset.invoice_number);
		$("#clasification_name_update").val(result.asset.clasification_name);
		$("#clasification_update").val(result.asset.category).trigger('change');
		// $("#clasification_mid_update").val(result.asset.clasification_name).trigger('change');
		$("#investment_number_update").val(result.asset.investment_number);
		$("#budget_update").val(result.asset.budget_number);
		$("#vendor_update").val(result.asset.vendor);
		$('#currency_update').val(result.asset.currency).trigger('change');
		$("#amount_update").val(result.asset.amount);
		$("#amount_usd_update").val(result.asset.amount_usd);
		$("#pic_update").val(result.asset.pic);

		$("input[name=usage_term_update][value='" + result.asset.usage_term + "']").prop('checked', true);

		if (result.asset.usage_term != 'soon') {
			$("#usage_tab_update").show();
		}

		$("#usage_est_update").val(result.asset.usage_estimation);

		$("#location_update").empty();
		loc = '<option value=""></option>';
		pics = result.location;

		$.each(result.location, function(index, value){
			var lok = value.location.split(",");

			$.each(lok, function(index2, value2){
				if (value2 == result.asset.location) {
					loc += '<option value="'+value2+'" selected>'+value2+'</option>';
				} else {
					loc += '<option value="'+value2+'">'+value2+'</option>';
				}
			})
		})

		$("#location_update").append(loc);

		$("#image_asset_update").attr('href', "{{ url('files/fixed_asset/registration') }}/"+result.asset.asset_picture);
		
		// $("#usage_update").val(result.asset.usage_term);
		// $("#usage2_update").val(result.asset.usage_estimation);
		$("#usefulllife").val(result.asset.life_time+" years");
	})
}


$("form#update").submit(function(e) {
	$("#loading").show();

	e.preventDefault();    
	var formData = new FormData();

	formData.append('id', $("#id_update").val());
	formData.append('asset_id', $("#id_asset").val());
	formData.append('invoice_name', $("#invoice_name_update").val());
	formData.append('item_name', $("#item_name_update").val());
	formData.append('invoice_number', $("#invoice_number_update").val());
	formData.append('investment_number', $("#investment_number_update").val());
	formData.append('budget', $("#budget_update").val());
	formData.append('vendor', $("#vendor_update").val());
	formData.append('currency', $("#currency_update").val());
	formData.append('amount', $("#amount_update").val());
	formData.append('amount_usd', $("#amount_usd_update").val());
	formData.append('pic', $("#pic_update").val());
	formData.append('pic_asset', $("#pic_asset_update").val());
	formData.append('location', $("#location_update").val());
	formData.append('category_code', $("#category_code").val());
	formData.append('category', $("#category").val());
	formData.append('sap_id', $("#sap").val());
	formData.append('reg_date', $("#reg_date").val());
	formData.append('depreciation', $("#depreciation").val());
	formData.append('sap_file', $('#sap_file').prop('files')[0]);

	var clasification = $("#clasification_mid_update").val();
	var usage_term = $('input[name="usage_term_update"]:checked').val();
	var usage_est = $("#usage_est_update").val();

	if(usage_term == "not use yet"){
		if (usage_est == '') {
			openErrorGritter('Error!', 'Date Usage Estimation must be filled');
			$("#loading").hide();
			return false;
		}
	}

	formData.append('clasification', clasification);
	formData.append('usage_term', usage_term);
	formData.append('usage_est', usage_est);
	formData.append('asset_foto', $('#asset_foto_update').prop('files')[0]);

	$.ajax({
		url: '{{ url("update/fixed_asset/registration_asset_form") }}',
		type: 'POST',
		contentType: 'multipart/form-data',
		data: formData,
		success: function (result, status, xhr) {
			$("#loading").hide();

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


			$('#CheckModal').modal('hide');

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

function creatNew(form_id) {
	var data = {
		form_id : form_id
	}

	$("#invoice_form_number").val(form_id);

	$.get('{{ url("fetch/fixed_asset/registration_asset_form") }}', data, function(result, status, xhr) {
		$("#createModal").modal('show');
		$("#investment_number").val(result.asset_requests[0].investment_number);
		$("#budget").val(result.asset_requests[0].budget_no);
		$("#vendor").val(result.asset_requests[0].supplier_name);
		$("#currency").val(result.asset_requests[0].currency).trigger('change');

		if (result.asset_requests[0].section == 'Secretary Admin Section ') {
			section = 'GA Control Section';
		} else {
			section = result.asset_requests[0].section;
		}
		$("#pic").val(section);

		$("#location").empty();
		loc = '<option value=""></option>';

		pics = result.location;

		$.each(result.location, function(index, value){
			var lok = value.location.split(",");
			$.each(lok, function(index2, value2){
				loc += '<option value="'+value2+'">'+value2+'</option>';
			})
		})

		$("#location").append(loc);

		$("#amount").val(result.asset_requests[0].total_ori);
		$("#amount_usd").val(result.asset_requests[0].total);
	})
}

function openDetail(id) {
	var data = {
		id : id
	}

	$.get('{{ url("fetch/fixed_asset/registration_asset_form/by") }}', data, function(result, status, xhr) {
		$("#editModal").modal('show');
		$("#invoice_form_number_edit").val(result.asset.form_number);

		$("#id_edit").val(id);
		$("#id_asset_edit").val(result.asset.asset_id);
		$("#invoice_name_edit").val(result.asset.invoice_name);
		$("#item_name_edit").val(result.asset.asset_name);
		$("#invoice_number_edit").val(result.asset.invoice_number);
		$("#clasification_name_edit").val(result.asset.clasification_name);
		$("#clasification_edit").val(result.asset.category).trigger('change');
		$("#investment_number_edit").val(result.asset.investment_number);
		$("#budget_edit").val(result.asset.budget_number);
		$("#vendor_edit").val(result.asset.vendor);
		$('#currency_edit').val(result.asset.currency).trigger('change');
		$("#amount_edit").val(result.asset.amount);
		$("#amount_usd_edit").val(result.asset.amount_usd);
		$("#pic_edit").val(result.asset.pic);
		$("#image_asset").attr('href', "{{ url('files/fixed_asset/registration') }}/"+result.asset.asset_picture);
		// $("#location_edit").val(result.asset.location);

		$("#location_edit").empty();
		loc = '<option value=""></option>';
		pics = result.location;

		$.each(result.location, function(index, value){
			var lok = value.location.split(",");

			$.each(lok, function(index2, value2){
				if (value2 == result.asset.location) {
					loc += '<option value="'+value2+'" selected>'+value2+'</option>';
				} else {
					loc += '<option value="'+value2+'">'+value2+'</option>';
				}
			})
		})

		$("#location_edit").append(loc);

		$("input[name=usage_term_edit][value='" + result.asset.usage_term + "']").prop('checked', true);

		if (result.asset.usage_term != 'soon') {
			$("#usage_tab_edit").show();
		}

		$("#usage_est_edit").val(result.asset.usage_estimation);
	})
}

function openSendMail(id, approval) {
	var appr = "";

	if (approval == 'created') {
		appr = 'Fixed Asset Control';
	} else if (approval == 'filled') {
		appr = 'PIC Manager';
	} else if (approval == 'approved_manager') {
		appr = 'Accounting Manager';
	}

	$("#sendMailModal").modal('show');
	$("#approval_name").text(appr);
	$("#email_form_id").val(id);
}

function sendMail() {
	data = {
		id : $("#email_form_id").val(),
		form : 'Registration Form'
	}

	$.post('{{ url("send/mail/fixed_asset") }}', data, function(result, status, xhr) {
		openSuccessGritter('Success', 'Send Email Successfully');
	})
}


$('#location').on('change', function() {
	var val = this.value;

	$.each(pics, function(index, value){
		if (value.location.indexOf(val) >= 0) {
			$("#pic_asset").val(value.employee_id);
		}
	})
});

$('#location_update').on('change', function() {
	var val = this.value;

	$.each(pics, function(index, value){
		if (value.location.indexOf(val) >= 0) {
			$("#pic_asset_update").val(value.employee_id);
		}
	})
});

$('#location_edit').on('change', function() {
	var val = this.value;

	$.each(pics, function(index, value){
		if (value.location.indexOf(val) >= 0) {
			$("#pic_asset_edit").val(value.employee_id);
		}
	})
});

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