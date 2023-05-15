@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
	#tableResume > tbody > tr > td:hover{
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	#tableResume > tbody > tr > td{
		font-weight: bold;
	}
	#tableDetail > tbody > tr:hover{
		/*cursor: pointer;*/
		background-color: #7dfa8c !important;
	}
	tbody>tr>td{
		padding: 10px 5px 10px 5px;
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
		border:1px solid black;
		vertical-align: middle;
		height: 40px;
		padding:  2px 5px 2px 5px;
	}
	.control-label {
		padding-top: 0 !important;
	}
	#tableAgreement > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		height: 30px;
		padding:  2px 5px 2px 5px;
	}
	#tableAgreement > thead > tr > th{
		height: 30px;
		border:1px solid black;
		vertical-align: middle;
		padding:  2px 5px 2px 5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}
	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalCreate()"><i class="fa fa-pencil-square-o"></i> Create New</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="xol-xs-12 col-md-2 col-lg-3" id="container1" style="height: 40vh;">
		</div>
		<div class="xol-xs-12 col-md-4 col-lg-3" id="container2" style="height: 40vh;">
		</div>
		<div class="xol-xs-12 col-md-3 col-lg-3" style="height: 40vh;">
			<table id="tableAgreement" class="table table-striped table-hover" style="height: 38vh;">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 1%; text-align: center;" colspan="2">AGREEMENT</th>
						<th style="width: 1%; text-align: center;" rowspan="2">COUNT</th>
						<th style="width: 1%; text-align: center;" rowspan="2">TOTAL</th>
					</tr>
					<tr>
						<th style="width: 1%; text-align: center;">OLD</th>
						<th style="width: 1%; text-align: center;">NEW</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">NO</td>
						<td style="padding: 0 0 0 0; font-weight: bold; color: red;">NO</td>
						<td id="no_no" style="padding: 0 0 0 0; font-weight: bold; color: red;"></td>
						<td id="no_no_ok_no" style="padding: 0 0 0 0; font-weight: bold; color: red; font-size: 30px;" rowspan="2"></td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">OK</td>
						<td style="padding: 0 0 0 0; font-weight: bold; color: red;">NO</td>
						<td id="ok_no" style="padding: 0 0 0 0; font-weight: bold; color: red;"></td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">NO</td>
						<td style="padding: 0 0 0 0; font-weight: bold; color: green;">OK</td>
						<td id="no_ok" style="padding: 0 0 0 0; font-weight: bold; color: green;"></td>
						<td id="no_ok_ok_ok" style="padding: 0 0 0 0; font-weight: bold; color: green; font-size: 30px;" rowspan="2"></td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">OK</td>
						<td style="padding: 0 0 0 0; font-weight: bold; color: green;">OK</td>
						<td id="ok_ok" style="padding: 0 0 0 0; font-weight: bold; color: green;"></td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">NO</td>
						<td style="padding: 0 0 0 0; font-weight: bold;">END</td>
						<td id="no_end" style="padding: 0 0 0 0; font-weight: bold;"></td>
						<td id="no_end_ok_end" style="padding: 0 0 0 0; font-weight: bold;" rowspan="2">No Need MTA</td>
					</tr>
					<tr>
						<td style="padding: 0 0 0 0; font-weight: bold;">OK</td>
						<td style="padding: 0 0 0 0; font-weight: bold;">END</td>
						<td id="ok_end" style="padding: 0 0 0 0; font-weight: bold;"></td>
					</tr>
				</tbody>
			</table>			
		</div>
		<div class="xol-xs-12 col-md-3 col-lg-3" id="container3" style="height: 40vh;">
		</div>
		<div class="xol-xs-12 col-md-12 col-lg-12">
			<table id="tableResume" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 1.5%;" rowspan="2">Group</th>
						<th style="width: 1%; text-align: center;" colspan="2">Location</th>
						<th style="width: 0.1%; text-align: center;" rowspan="2">Total</th>
						<th style="width: 1%; text-align: center;" colspan="2">Old Version</th>
						<th style="width: 1%; text-align: center;" rowspan="2">Signed<br>or Closed</th>
						<th style="width: 1%; text-align: center;" rowspan="2">Open</th>
						<th style="width: 1%; text-align: center;" rowspan="2">MTA Progress %</th>
						<th style="width: 1%; text-align: center;" colspan="2">MTA File</th>
						<th style="width: 1%; text-align: center;" colspan="6">Sent Progress</th>
					</tr>
					<tr>
						<th style="width: 0.1%; text-align: center;">Domestic</th>
						<th style="width: 0.1%; text-align: center;">Overseas</th>
						<th style="width: 0.1%; text-align: center;">None</th>
						<th style="width: 0.1%; text-align: center;">Exist</th>
						{{-- <th style="width: 1%; text-align: center;">① No Draft</th> --}}
						<th style="width: 1%; text-align: center;">Not Sent</th>
						<th style="width: 1%; text-align: center;">Sent</th>
						<th style="width: 1%; text-align: center;">Refused</th>
						<th style="width: 1%; text-align: center;">Under Check</th>
						<th style="width: 1%; text-align: center;">MTA Change Request</th>
						<th style="width: 1%; text-align: center;">Agree to Sign</th>
						<th style="width: 1%; text-align: center;">Supplier Approved</th>
						<th style="width: 1%; text-align: center;">YCJ Approval</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Direct & Subcon</td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_domestic"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_overseas"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_total"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; color: red;" id="count_direct_none"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; color: green;" id="count_direct_exist"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(204,255,255);" id="count_direct_closed"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_direct_open"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_percentage"></td>
						{{-- <td style="text-align: center;" id="count_direct_no_draft"></td> --}}
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_direct_draft"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_direct_draft_sent"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_refused"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_under_check"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_change_request"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_agree_to_sign"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_supplier_approved"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_direct_ycj_approved"></td>
					</tr>
					<tr>
						<td>Indirect</td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_domestic"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_overseas"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_total"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; color: red;" id="count_indirect_none"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; color: green;" id="count_indirect_exist"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(204,255,255);" id="count_indirect_closed"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_indirect_open"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_percentage"></td>
						{{-- <td style="text-align: center;" id="count_indirect_no_draft"></td> --}}
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_indirect_draft"></td>
						<td onclick="fetchDetail(id)" style="text-align: center; background-color: RGB(255,204,255);" id="count_indirect_draft_sent"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_refused"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_under_check"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_change_request"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_agree_to_sign"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_supplier_approved"></td>
						<td onclick="fetchDetail(id)" style="text-align: center;" id="count_indirect_ycj_approved"></td>
					</tr>
				</tbody>
				<tfoot style="background-color: RGB(252, 248, 227);">
					<tr>
						<th>All</th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_domestic"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_overseas"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_total"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_none"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_exist"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_closed"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_open"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_percentage"></th>
						{{-- <th style="text-align: center;" id="total_no_draft"></th> --}}
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_draft"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_draft_sent"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_refused"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_under_check"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_change_request"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_agree_to_sign"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_supplier_approved"></th>
						<th onclick="fetchDetail(id)" style="text-align: center;" id="total_ycj_approved"></th>
					</tr>					
				</tfoot>
			</table>
			<table id="tableDetail" class="table table-bordered table-striped table-hover">
				<thead style="background-color: #605ca8; color: white;">
					<tr>
						<th style="width: 0.1%; text-align: center;" rowspan="2">#</th>
						<th style="width: 3%;" rowspan="2">Name</th>
						<th style="width: 3%;" rowspan="2">Name SAP</th>
						<th style="width: 0.1%;" rowspan="2">Curr</th>
						<th style="width: 0.1%; text-align: center;" rowspan="2">Vendor</th>
						<th style="width: 0.1%;" rowspan="2">Location</th>
						<th style="width: 0.5%; text-align: right;" rowspan="2">Annual<br>Purchase (USD)</th>
						{{-- <th style="width: 0.1%;" rowspan="2">Category</th> --}}
						<th style="width: 0.1%;" rowspan="2">Status</th>
						<th style="width: 1%;" rowspan="2">Group</th>
						<th style="width: 1%;" rowspan="2">PIC</th>
						<th style="width: 0.1%; text-align: right;" rowspan="2">Old Ver.</th>
						<th style=" text-align: center;" colspan="5">Approval Progress</th>
						<th style="width: 1%; text-align: right;" rowspan="2">Progress</th>
						<th style="width: 0.1%;" rowspan="2">Status</th>
						<th style="width: 0.1%; text-align: center;" rowspan="2"><i class="glyphicon glyphicon-paperclip"></i></th>
					</tr>
					<tr>
						<th style="width: 0.1%; text-align: right;">Sent ①</th>
						<th style="width: 0.1%; text-align: right;">Vendor ②</th>
						<th style="width: 0.1%; text-align: right;">YCJ Pre ③</th>
						<th style="width: 0.1%; text-align: right;">YCJ ④</th>
						<th style="width: 0.1%; text-align: right;">YMPI ⑤</th>
					</tr>
				</thead>
				<tbody id="tableDetailBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalChart">
	<div class="modal-dialog modal-md" style="width: 30%;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableModal" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 1%">#</th>
								<th style="width: 1%">Code</th>
								<th style="width: 4%">Vendor</th>
								<th style="width: 1%">Sent At</th>
								<th style="width: 2%">Day(s) Since Sent</th>
							</tr>
						</thead>
						<tbody id="tableModalBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUpdateProgress" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" style="width: 25%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #3c8dbc; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Update Progress<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<input type="hidden" id="addVendorCode">
							<input type="hidden" id="addCategory">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Date<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDueFrom" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Evidence<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="file" id="addAttachment">
								</div>
							</div>							
						</div>
					</form>
					<div class="col-sm-12" style="margin-bottom: 10px;">
						<button class="btn btn-primary pull-right" onclick="updateProgress()">Update</button>
						<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" style="width: 35%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Create New Agreement Record<br>
					</h3>
				</center>
				<form class="form-horizontal">
					<div class="col-md-12">
						<div class="form-group">
							<label for="createVendorCode" class="col-sm-3 control-label">Vendor Code<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="text" class="form-control pull-right" id="createVendorCode" name="createVendorCode" placeholder="Vendor Code">
							</div>
						</div>
						<div class="form-group">
							<label for="createVendorName" class="col-sm-3 control-label">Vendor Name<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="createVendorName" name="createVendorName" placeholder="Vendor Name">
							</div>
						</div>
						{{-- <div class="form-group">
							<label for="createEmail" class="col-sm-3 control-label">Vendor Email<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="createEmail" name="createEmail" placeholder="Vendor Email">
							</div>
						</div> --}}
						<div class="form-group">
							<label for="createLocation" class="col-sm-3 control-label">City/Country<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="text" class="form-control pull-right" id="createLocation" name="createLocation" placeholder="City/Country">
							</div>
						</div>
						<div class="form-group">
							<label for="createLocationGroup" class="col-sm-3 control-label">Location<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<select class="form-control select2" id="createLocationGroup" name="createLocationGroup" data-placeholder='Location' style="width: 100%">
									<option value=""></option>
									<option value="Domestic">Domestic</option>
									<option value="Overseas">Overseas</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createAnnual" class="col-sm-3 control-label">Annual Purch.<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<div class="input-group">
									<input type="text" value="0" class="numpad form-control" placeholder="Annual" id="createAnnual">
									<div class="input-group-addon" style="">
										USD
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="createCategory" class="col-sm-3 control-label">Category<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<select class="form-control select2" id="createCategory" name="createCategory" data-placeholder='Category' style="width: 100%">
									<option value=""></option>
									@foreach($categories as $category)
									<option value="{{ $category }}">{{ $category }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createStatusVendor" class="col-sm-3 control-label">Status<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="createStatusVendor" name="createStatusVendor" data-placeholder='Location' style="width: 100%">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createGroup" class="col-sm-3 control-label">Group<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<select class="form-control select2" id="createGroup" name="createGroup" data-placeholder='Group' style="width: 100%">
									<option value=""></option>
									<option value="G08">G08</option>
									<option value="G15">G15</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createGroupName" class="col-sm-3 control-label">Group Name<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<select class="form-control select2" id="createGroupName" name="createGroupName" data-placeholder='Group Name' style="width: 100%">
									<option value=""></option>
									<option value="Direct Material">Direct Material</option>
									<option value="Indirect Material">Indirect Material</option>
									<option value="Subcont.">Subcont.</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createPic" class="col-sm-3 control-label">PIC<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select2" id="createPic" name="createPic" data-placeholder='Select PIC' style="width: 100%">
									<option value=""></option>
									@foreach($employees as $employee)
									<option value="{{ $employee->employee_id }}_{{ $employee->name }}">{{ $employee->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createCurrency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="createCurrency" name="createCurrency" data-placeholder='Currency' style="width: 100%">
									<option value=""></option>
									<option value="EUR">EUR</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
									<option value="JPY/USD">JPY/USD</option>
									<option value="USD">USD</option>
									<option value="USD/IDR">USD/IDR</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="createPaymentTerm" class="col-sm-3 control-label">Payment Term<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select2" id="createPaymentTerm" name="createPaymentTerm" data-placeholder='Payment Term' style="width: 100%">
									<option value=""></option>
									<option value="30 Days After B/L Date">30 Days After B/L Date</option>
									<option value="60 Days After B/L Date">60 Days After B/L Date</option>
									<option value="120 Days After B/L Date">120 Days After B/L Date</option>
									<option value="30 Days After I/V Date">30 Days After I/V Date</option>
									<option value="30 Days After Received">30 Days After Received</option>
									<option value="56 Days After Rec.Date">56 Days After Rec.Date</option>
									<option value="60 Days From Delivery Date">60 Days From Delivery Date</option>
									<option value="8 Weeks After Receiving Date">8 Weeks After Receiving Date</option>
									<option value="End Of 3th Month After Rec. Date">End Of 3th Month After Rec. Date</option>
									<option value="End Of Next Month After Rec. Date">End Of Next Month After Rec. Date</option>
									<option value="One Month After Rec. Date">One Month After Rec. Date</option>
									<option value="T/T 100% In Advance">T/T 100% In Advance</option>
									<option value="T/T At The End Of Next Month After I/V Date">T/T At The End Of Next Month After I/V Date</option>
									<option value="T/T Remittance Within 8 Weeks After Arrival">T/T Remittance Within 8 Weeks After Arrival</option>
									<option value="T/T Bank Remittance">T/T Bank Remittance</option>
								</select>
							</div>
						</div>
						{{-- <div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Sent At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="createSentAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="createSAEv">
							</div>
						</div> --}}
						<div class="form-group">
							<label for="createRemark" class="col-sm-3 control-label">Note<span class="text-red"></span></label>
							<div class="col-sm-9">
								<textarea class="form-control" rows="2" placeholder="Enter Note" id="createRemark"></textarea>
							</div>
						</div>
					</div>
				</form>
				<div class="col-sm-12" style="margin-bottom: 10px;">
					<button class="btn btn-primary pull-right" onclick="createAgreement()">Create</button>
					<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" style="width: 35%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Edit/Update Agreement Record<br>
					</h3>
				</center>
				<form class="form-horizontal">
					<div class="col-md-12">
						<div class="form-group">
							<label for="editVendorCode" class="col-sm-3 control-label">Vendor Code<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="text" class="form-control pull-right" id="editVendorCode" name="editVendorCode" placeholder="Vendor Code">
							</div>
						</div>
						<div class="form-group">
							<label for="editVendorName" class="col-sm-3 control-label">Vendor Name<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="editVendorName" name="editVendorName" placeholder="Vendor Name">
							</div>
						</div>
						{{-- <div class="form-group">
							<label for="editEmail" class="col-sm-3 control-label">Vendor Email<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="editEmail" name="editEmail" placeholder="Vendor Email">
							</div>
						</div> --}}
						<div class="form-group">
							<label for="editLocation" class="col-sm-3 control-label">City/Country<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="text" class="form-control pull-right" id="editLocation" name="editLocation" placeholder="City/Country">
							</div>
						</div>
						<div class="form-group">
							<label for="editLocationGroup" class="col-sm-3 control-label">Location<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<select class="form-control select2" id="editLocationGroup" name="editLocationGroup" data-placeholder='Location' style="width: 100%">
									<option value=""></option>
									<option value="Domestic">Domestic</option>
									<option value="Overseas">Overseas</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editAnnual" class="col-sm-3 control-label">Annual Purch.<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<div class="input-group">
									<input type="text" value="0" class="numpad form-control" placeholder="Annual" id="editAnnual">
									<div class="input-group-addon" style="">
										USD
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="editCategory" class="col-sm-3 control-label">Category<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<select class="form-control select2" id="editCategory" name="editCategory" data-placeholder='Category' style="width: 100%">
									<option value=""></option>
									@foreach($categories as $category)
									<option value="{{ $category }}">{{ $category }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editStatusVendor" class="col-sm-3 control-label">Status<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="editStatusVendor" name="editStatusVendor" data-placeholder='Location' style="width: 100%">
									<option value="Active">Active</option>
									<option value="Inactive">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editGroup" class="col-sm-3 control-label">Group<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<select class="form-control select2" id="editGroup" name="editGroup" data-placeholder='Group' style="width: 100%">
									<option value=""></option>
									<option value="G08">G08</option>
									<option value="G15">G15</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editGroupName" class="col-sm-3 control-label">Group Name<span class="text-red">*</span></label>
							<div class="col-sm-6">
								<select class="form-control select2" id="editGroupName" name="editGroupName" data-placeholder='Group Name' style="width: 100%">
									<option value=""></option>
									<option value="Direct Material">Direct Material</option>
									<option value="Indirect Material">Indirect Material</option>
									<option value="Subcont.">Subcont.</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editPic" class="col-sm-3 control-label">PIC<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select2" id="editPic" name="editPic" data-placeholder='Select PIC' style="width: 100%">
									<option value=""></option>
									@foreach($employees as $employee)
									<option value="{{ $employee->employee_id }}_{{ $employee->name }}">{{ $employee->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editCurrency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="editCurrency" name="editCurrency" data-placeholder='Currency' style="width: 100%">
									<option value=""></option>
									<option value="EUR">EUR</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
									<option value="JPY/USD">JPY/USD</option>
									<option value="USD">USD</option>
									<option value="USD/IDR">USD/IDR</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editPaymentTerm" class="col-sm-3 control-label">Payment Term<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select2" id="editPaymentTerm" name="editPaymentTerm" data-placeholder='Payment Term' style="width: 100%">
									<option value=""></option>
									<option value="30 Days After B/L Date">30 Days After B/L Date</option>
									<option value="60 Days After B/L Date">60 Days After B/L Date</option>
									<option value="120 Days After B/L Date">120 Days After B/L Date</option>
									<option value="30 Days After I/V Date">30 Days After I/V Date</option>
									<option value="30 Days After Received">30 Days After Received</option>
									<option value="56 Days After Rec.Date">56 Days After Rec.Date</option>
									<option value="60 Days From Delivery Date">60 Days From Delivery Date</option>
									<option value="8 Weeks After Receiving Date">8 Weeks After Receiving Date</option>
									<option value="End Of 3th Month After Rec. Date">End Of 3th Month After Rec. Date</option>
									<option value="End Of Next Month After Rec. Date">End Of Next Month After Rec. Date</option>
									<option value="One Month After Rec. Date">One Month After Rec. Date</option>
									<option value="T/T 100% In Advance">T/T 100% In Advance</option>
									<option value="T/T At The End Of Next Month After I/V Date">T/T At The End Of Next Month After I/V Date</option>
									<option value="T/T Remittance Within 8 Weeks After Arrival">T/T Remittance Within 8 Weeks After Arrival</option>
									<option value="T/T Bank Remittance">T/T Bank Remittance</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editOldVersion" class="col-sm-3 control-label">Old Version<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="editOldVersion" name="editOldVersion" data-placeholder='Old Version' style="width: 100%">
									<option value=""></option>
									<option value="0">None</option>
									<option value="1">Exist</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Sent At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="editSentAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="editSAEv">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Sign By Vendor At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="editSignAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="editSEv">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Pre Approval YCJ At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="editPreYcjAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="editPYEv">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Approved By YCJ At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="editAppYcjAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="editAYEv">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Approved By Director YMPI At<span class="text-red">*</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control datepicker" id="editAppDirAt" placeholder="   Select Date">
							</div>
							<div class="col-sm-5">
								<input type="file" id="editADEv">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">MTA English<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="file" id="editAEn">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">MTA Bahasa<span class="text-red">*</span></label>
							<div class="col-sm-5">
								<input type="file" id="editAId">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">MTA Amandment English<span class="text-red"></span></label>
							<div class="col-sm-5">
								<input type="file" id="editAmEn">
							</div>
						</div>
						<div class="form-group">
							<label style="padding-top: 0;" for="" class="col-sm-3 control-label">MTA Amandment Bahasa<span class="text-red"></span></label>
							<div class="col-sm-5">
								<input type="file" id="editAmId">
							</div>
						</div>
						<div class="form-group">
							<label for="editProgress" class="col-sm-3 control-label">Progress<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="editProgress" name="editProgress" data-placeholder='Progress' style="width: 100%">
									<option value=""></option>
									<option value="MTA Change Request">MTA Change Request</option>
									<option value="Refused">Refused</option>
									<option value="Signed">Signed</option>
									<option value="Signed by Supplier">Signed by Supplier</option>
									<option value="Under Check">Under Check</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editStatus" class="col-sm-3 control-label">Status<span class="text-red">*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" id="editStatus" name="editStatus" data-placeholder='Progress' style="width: 100%">
									<option value="Open">Open</option>
									<option value="Closed">Closed</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="editRemark" class="col-sm-3 control-label">Note<span class="text-red"></span></label>
							<div class="col-sm-9">
								<textarea class="form-control" rows="2" placeholder="Enter Note" id="editRemark"></textarea>
							</div>
						</div>
					</div>
				</form>
				<div class="col-sm-12" style="margin-bottom: 10px;">
					<button class="btn btn-primary pull-right" onclick="editAgreement()">Save</button>
					<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('#addDueFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#createSentAt').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		fetchMonitoring("");
	});

	$(function () {
		$('#createLocationGroup').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createStatusVendor').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createCategory').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#createPic').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#createGroup').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#createGroupName').select2({
			dropdownParent: $('#modalCreate')
		});
		$('#createCurrency').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
		$('#createPaymentTerm').select2({
			dropdownParent: $('#modalCreate')
		});

		$('#editLocationGroup').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editStatusVendor').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editCategory').select2({
			dropdownParent: $('#modalEdit')
		});
		$('#editPic').select2({
			dropdownParent: $('#modalEdit')
		});
		$('#editGroup').select2({
			dropdownParent: $('#modalEdit')
		});
		$('#editGroupName').select2({
			dropdownParent: $('#modalEdit')
		});
		$('#editCurrency').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
		$('#editPaymentTerm').select2({
			dropdownParent: $('#modalEdit')
		});
		$('#editOldVersion').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var trade_agreements = "";

	function modalChartFile(category){
		$('#tableModalBody').html("");
		var tableModalBody = "";
		var cnt = 0;
		var vendor = [];

		str = category.replace(/\s/g, '');

		cat = str.split("&");

		var cat_old = cat[0];
		var cat_new = cat[1];

		$.each(trade_agreements, function(key, value){			
			if(cat_new == 'END'){
				if(value.old_version == 1 && cat_old == 'OK' && value.status_vendor == 'Inactive'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
				if(value.old_version == 0 && cat_old == 'NO' && value.status_vendor == 'Inactive'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
			}
			else{
				if(cat_old == 'NO' && value.old_version == 0 && cat_new == 'OK' && value.status == 'Closed' && value.status_vendor == 'Active'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
				if(cat_old == 'OK' && value.old_version == 1 && cat_new == 'OK' && value.status == 'Closed' && value.status_vendor == 'Active'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
				if(cat_old == 'NO' && value.old_version == 0 && cat_new == 'NO' && value.status == 'Open' && value.status_vendor == 'Active'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
				if(cat_old == 'OK' && value.old_version == 1 && cat_new == 'NO' && value.status == 'Open' && value.status_vendor == 'Active'){
					cnt += 1;
					tableModalBody += '<tr>';
					tableModalBody += '<td>'+cnt+'</td>';
					tableModalBody += '<td>'+value.vendor_code+'</td>';
					tableModalBody += '<td>'+value.vendor_name+'</td>';
					tableModalBody += '<td>'+value.sent_at+'</td>';
					tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
					tableModalBody += '</tr>';
				}
			}
		});
		$('#tableModalBody').append(tableModalBody);

		$('#modalChart').modal('show');
	}

	function modalChartProgress(progress){
		$('#tableModalBody').html("");
		var tableModalBody = "";
		var cnt = 0;
		var vendor = [];

		$.each(trade_agreements, function(key, value){
			if(value.progress == progress && value.status == 'Open'){

				if(jQuery.inArray(value.vendor_name, vendor) !== -1){

				}
				else{
					cnt += 1;
					vendor.push(value.vendor_name);
				}
				tableModalBody += '<tr>';
				tableModalBody += '<td>'+cnt+'</td>';
				tableModalBody += '<td>'+value.vendor_code+'</td>';
				tableModalBody += '<td>'+value.vendor_name+'</td>';
				tableModalBody += '<td>'+value.sent_at+'</td>';
				tableModalBody += '<td style="text-align: right;">'+value.count_sent_at+' Day(s)</td>';
				tableModalBody += '</tr>';
			}
		});
		$('#tableModalBody').append(tableModalBody);

		$('#modalChart').modal('show');
	}

	function modalUpdateProgress(vendor_code, category){
		$('#addVendorCode').val(vendor_code);
		$('#addCategory').val(category);
		$('#addDueFrom').val("");
		$('#addAttachment').val("");
		$('#modalUpdateProgress').modal('show');
	}

	function modalCreate(){
		$('#createVendorCode').val("");
		$('#createVendorName').val("");
		// $('#createEmail').val("");
		$('#createLocation').val("");
		$('#createLocationGroup').prop('selectedIndex', 0).change();
		$('#createAnnual').val("");
		$('#createCategory').prop('selectedIndex', 0).change();
		$('#createStatusVendor').prop('selectedIndex', 0).change();
		$('#createGroup').prop('selectedIndex', 0).change();
		$('#createGroupName').prop('selectedIndex', 0).change();
		$('#createPic').prop('selectedIndex', 0).change();
		$('#createCurrency').prop('selectedIndex', 0).change();
		$('#createPaymentTerm').prop('selectedIndex', 0).change();
		$('#createRemark').val("");

		$('#modalCreate').modal('show');
	}

	function modalEdit(id){

		$.each(trade_agreements, function(key, value){
			if(value.vendor_code == id){
				$('#editVendorCode').val(value.vendor_code);
				$('#editVendorName').val(value.vendor_name);
				// $('#editEmail').val(value.email);
				$('#editLocation').val(value.location);
				$('#editAnnual').val(value.annual_purchase);

				$('#editLocationGroup').val(value.location_group).change();
				$('#editCategory').val(value.category).change();
				$('#editStatusVendor').val(value.status_vendor).change();
				$('#editGroup').val(value.pgr).change();
				$('#editGroupName').val(value.pgr_name).change();
				$('#editPic').val(value.pic_id+'_'+value.pic_name).change();
				$('#editCurrency').val(value.currency).change();
				$('#editPaymentTerm').val(value.payment_term).change();
				$('#editOldVersion').val(value.old_version).change();

				$('#editSentAt').val(value.sent_at);
				$('#editSignAt').val(value.sign_at);
				$('#editPreYcjAt').val(value.pre_ycj_at);
				$('#editAppYcjAt').val(value.app_ycj_at);
				$('#editAppDirAt').val(value.app_dir_at);

				if(value.pgr_name == 'Indirect Material'){
					$('#editPreYcjAt').prop('disabled', true);
					$('#editAppYcjAt').prop('disabled', true);
					$('#editPYEv').prop('disabled', true);
					$('#editAYEv').prop('disabled', true);
				}


				$('#editRemark').val(value.remark);

				$('#editProgress').val(value.progress).change();
				$('#editStatus').val(value.status).change();
				$('#editAmendment').val(value.vendor_code);
				$('#editVersion').val(value.vendor_code);
				$('#editAttId').val(value.vendor_code);
				$('#editAttEn').val(value.vendor_code);
			}
		});
		$('#modalEdit').modal('show');
	}

	function createAgreement(){
		// alert('Under Maintenance!!\nWe are sorry this function is still under repair by Developer Team.\nMIS-Team');
		// return false;

		var vendor_code = $('#createVendorCode').val();
		var vendor_name = $('#createVendorName').val();
		// var email = $('#createEmail').val();
		var location = $('#createLocation').val();
		var location_group = $('#createLocationGroup').val();
		var annual_purchase = $('#createAnnual').val();
		var category = $('#createCategory').val();
		var status_vendor = $('#createStatusVendor').val();
		var pgr = $('#createGroup').val();
		var pgr_name = $('#createGroupName').val();
		var pic = $('#createPic').val();
		var currency = $('#createCurrency').val();
		var payment_term = $('#createPaymentTerm').val();
		// var sent_at = $('#createSentAt').val();
		var remark = $('#createRemark').val();

		if(vendor_code == "" || vendor_name == "" || location == "" || location_group == "" || annual_purchase == "" || category == "" || status_vendor == "" || pgr == "" || pgr_name == "" || pic == "" || currency == "" || payment_term == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'All required field must be filled.');
			audio_error.play();
		}

		var formData = new FormData();

		formData.append('vendor_code', vendor_code);
		formData.append('vendor_name', vendor_name);
		// formData.append('email', email);
		formData.append('location', location);
		formData.append('location_group', location_group);
		formData.append('annual_purchase', annual_purchase);
		formData.append('category', category);
		formData.append('status_vendor', status_vendor);
		formData.append('pgr', pgr);
		formData.append('pgr_name', pgr_name);
		formData.append('pic', pic);
		formData.append('currency', currency);
		formData.append('payment_term', payment_term);
		// formData.append('sent_at', sent_at);
		formData.append('remark', remark);

		if(confirm("Are you sure want to submit this vendor to MTA Monitoring?")){
			$.ajax({
				url:"{{ url('create/trade_agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						fetchMonitoring();
						$('#loading').hide();
						$('#modalCreate').modal('hide');
						openSuccessGritter('Success!', data.message);
						audio_ok.play();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!', data.message);
						audio_error.play();
					}

				}
			});
		}
		else{
			return false;
		}
	}

	function editAgreement(){
		// alert('Under Maintenance!!\nWe are sorry this function is still under repair by Developer Team.\nMIS-Team');
		// return false;

		var vendor_code = $('#editVendorCode').val();
		var vendor_name = $('#editVendorName').val();
		// var email = $('#editEmail').val();
		var location = $('#editLocation').val();
		var location_group = $('#editLocationGroup').val();
		var annual_purchase = $('#editAnnual').val();
		var category = $('#editCategory').val();
		var status_vendor = $('#editStatusVendor').val();
		var pgr = $('#editGroup').val();
		var pgr_name = $('#editGroupName').val();
		var pic = $('#editPic').val();
		var currency = $('#editCurrency').val();
		var payment_term = $('#editPaymentTerm').val();
		var old_version = $('#editOldVersion').val();

		var sent_at = $('#editSentAt').val();
		var sign_at = $('#editSignAt').val();
		var pre_ycj_at = $('#editPreYcjAt').val();
		var app_ycj_at = $('#editAppYcjAt').val();
		var app_dir_at = $('#editAppDirAt').val();

		var sent_at_ev = $('#editSAEv').prop('files')[0];
		var sign_at_ev = $('#editSEv').prop('files')[0];
		var pre_ycj_at_ev = $('#editPYEv').prop('files')[0];
		var app_ycj_at_ev = $('#editAYEv').prop('files')[0];
		var app_dir_at_ev = $('#editADEv').prop('files')[0];

		var file_sent_at_ev = $('#editSAEv').val().replace(/C:\\fakepath\\/i, '').split(".");
		var file_sign_at_ev = $('#editSEv').val().replace(/C:\\fakepath\\/i, '').split(".");
		var file_pre_ycj_at_ev = $('#editPYEv').val().replace(/C:\\fakepath\\/i, '').split(".");
		var file_app_ycj_at_ev = $('#editAYEv').val().replace(/C:\\fakepath\\/i, '').split(".");
		var file_app_dir_at_ev = $('#editADEv').val().replace(/C:\\fakepath\\/i, '').split(".");

		var progress = $('#editProgress').val();
		var status = $('#editStatus').val();

		var att_id = $('#editAId').prop('files')[0];
		var att_en = $('#editAEn').prop('files')[0];
		var amendment_id = $('#editAmId').prop('files')[0];
		var amendment_en = $('#editAmEn').prop('files')[0];

		var att_id_ev = $('#editAId').val().replace(/C:\\fakepath\\/i, '').split(".");
		var att_en_ev = $('#editAEn').val().replace(/C:\\fakepath\\/i, '').split(".");
		var amendment_id_ev = $('#editAmId').val().replace(/C:\\fakepath\\/i, '').split(".");
		var amendment_en_ev = $('#editAmEn').val().replace(/C:\\fakepath\\/i, '').split(".");
		

		var remark = $('#editRemark').val();

		var formData = new FormData();

		formData.append('vendor_code', vendor_code);
		formData.append('vendor_name', vendor_name);
		// formData.append('email', email);
		formData.append('location', location);
		formData.append('location_group', location_group);
		formData.append('annual_purchase', annual_purchase);
		formData.append('category', category);
		formData.append('status_vendor', status_vendor);
		formData.append('pgr', pgr);
		formData.append('pgr_name', pgr_name);
		formData.append('pic', pic);
		formData.append('currency', currency);
		formData.append('payment_term', payment_term);
		formData.append('old_version', old_version);

		formData.append('sent_at', sent_at);
		formData.append('sign_at', sign_at);
		formData.append('pre_ycj_at', pre_ycj_at);
		formData.append('app_ycj_at', app_ycj_at);
		formData.append('app_dir_at', app_dir_at);

		formData.append('sent_at_ev', sent_at_ev);
		formData.append('sign_at_ev', sign_at_ev);
		formData.append('pre_ycj_at_ev', pre_ycj_at_ev);
		formData.append('app_ycj_at_ev', app_ycj_at_ev);
		formData.append('app_dir_at_ev', app_dir_at_ev);

		formData.append('sent_at_ext', file_sent_at_ev[1]);
		formData.append('sent_at_fn', file_sent_at_ev[0]);
		formData.append('sign_at_ext', file_sign_at_ev[1]);
		formData.append('sign_at_fn', file_sign_at_ev[0]);
		formData.append('pre_ycj_at_ext', file_pre_ycj_at_ev[1]);
		formData.append('pre_ycj_at_fn', file_pre_ycj_at_ev[0]);
		formData.append('app_ycj_at_ext', file_app_ycj_at_ev[1]);
		formData.append('app_ycj_at_fn', file_app_ycj_at_ev[0]);
		formData.append('app_dir_at_ext', file_app_dir_at_ev[1]);
		formData.append('app_dir_at_fn', file_app_dir_at_ev[0]);

		formData.append('att_id', att_id);
		formData.append('att_en', att_en);
		formData.append('amendment_id', amendment_id);
		formData.append('amendment_en', amendment_en);

		formData.append('att_id_ext', att_id_ev[1]);
		formData.append('att_id_fn', att_id_ev[0]);
		formData.append('att_en_ext', att_en_ev[1]);
		formData.append('att_en_fn', att_en_ev[0]);
		formData.append('amendment_id_ext', amendment_id_ev[1]);
		formData.append('amendment_id_fn', amendment_id_ev[0]);
		formData.append('amendment_en_ext', amendment_en_ev[1]);
		formData.append('amendment_en_fn', amendment_en_ev[0]);

		formData.append('progress', progress);
		formData.append('status', status);
		formData.append('remark', remark);

		if(confirm("Are you sure want to update this supplier data?")){
			$.ajax({
				url:"{{ url('edit/trade_agreement') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						fetchMonitoring();
						$('#loading').hide();
						$('#modalEdit').modal('hide');
						openSuccessGritter('Success!', data.message);
						audio_ok.play();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!', data.message);
						audio_error.play();
					}

				}
			});
		}
		else{
			return false;			
		}
	}

	function updateProgress(){
		if(confirm("Apakah anda yakin akan mengajukan tiket ini?")){

			var vendor_code = $('#addVendorCode').val();
			var category = $('#addCategory').val();
			var due = $('#addDueFrom').val();
			var evidence  = $('#addAttachment').prop('files')[0];
			var file = $('#addAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			var formData = new FormData();

			if(vendor_code == '' || category == '' || due == '' || evidence == '' ){
				openErrorGritter('Error!', 'Please fill all required field.');
				return false;
				$('#loading').hide();
				audio_error.play();
			}

			formData.append('vendor_code', vendor_code);
			formData.append('category', category);
			formData.append('due', due);
			formData.append('evidence', evidence);
			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('update/trade_agreement/progress') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						fetchMonitoring();
						$('#loading').hide();
						$('#modalUpdateProgress').modal('hide');
						openSuccessGritter('Success!', data.message);
						audio_ok.play();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!', data.message);
						audio_error.play();
					}

				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function fetchDetail(filter){
		console.log(filter);
	}

	function fetchMonitoring(){
		var data = {

		}
		$.get('{{ url("fetch/trade_agreement") }}', data, function(result, status, xhr){
			if(result.status){

				var tableDetailBody = "";
				$('#tableDetailBody').html("");
				trade_agreements = result.trade_agreements;
				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				var no_no = 0;
				var no_no_ok_no = 0;
				var ok_no = 0;
				var no_ok = 0;
				var no_ok_ok_ok = 0;
				var ok_ok = 0;
				var no_end = 0;
				var no_end_ok_end = 0;
				var ok_end = 0;

				var count_indirect_domestic = 0;
				var count_indirect_overseas = 0;
				var count_indirect_total = 0;
				var count_indirect_none = 0;
				var count_indirect_exist = 0;
				var count_indirect_no_draft = 0;
				var count_indirect_draft = 0;
				var count_indirect_draft_sent = 0;
				var count_indirect_refused = 0;
				var count_indirect_under_check = 0;
				var count_indirect_change_request = 0;
				var count_indirect_agree_to_sign = 0;
				var count_indirect_supplier_approved = 0;
				var count_indirect_ycj_approved = 0;
				var count_indirect_closed = 0;
				var count_indirect_open = 0;
				var count_indirect_percentage = 0;

				var count_direct_domestic = 0;
				var count_direct_overseas = 0;
				var count_direct_total = 0;
				var count_direct_none = 0;
				var count_direct_exist = 0;
				var count_direct_no_draft = 0;
				var count_direct_draft = 0;
				var count_direct_draft_sent = 0;
				var count_direct_refused = 0;
				var count_direct_under_check = 0;
				var count_direct_change_request = 0;
				var count_direct_agree_to_sign = 0;
				var count_direct_supplier_approved = 0;
				var count_direct_ycj_approved = 0;
				var count_direct_closed = 0;
				var count_direct_open = 0;
				var count_direct_percentage = 0;

				var cnt = 0;
				var vendor = [];

				$.each(result.trade_agreements, function(key, value){
					if(jQuery.inArray(value.vendor_name, vendor) !== -1){
						
					}
					else{
						cnt += 1;
						if(value.status_vendor == 'Inactive'){
							if(value.old_version == 0){
								no_end += 1;
								no_end_ok_end += 1;
							}
							if(value.old_version == 1){
								ok_end += 1;
								no_end_ok_end += 1;
							}
						}

						if(value.status_vendor == 'Active'){
							if(value.old_version == 0 && value.status == 'Open'){
								no_no += 1;
								no_no_ok_no += 1;
							}
							if(value.old_version == 1 && value.status == 'Open'){
								ok_no += 1;
								no_no_ok_no += 1;
							}
							if(value.old_version == 0 && value.status == 'Closed'){
								no_ok += 1;
								no_ok_ok_ok += 1;
							}
							if(value.old_version == 1 && value.status == 'Closed'){
								ok_ok += 1;
								no_ok_ok_ok += 1;
							}
						}

						if(value.pgr_name == 'Indirect Material'){
							count_indirect_total += 1;
						}
						if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
							count_direct_total += 1;
						}

						if(value.location_group == 'Domestic'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_domestic += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_domestic += 1;
							}
						}

						if(value.location_group == 'Overseas'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_overseas += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_overseas += 1;
							}
						}

						if(value.old_version == 1){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_exist += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_exist += 1;
							}
						}

						if(value.old_version == 0){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_none += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_none += 1;
							}
						}


						if(value.status == 'Open'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_open += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_open += 1;
							}
						}

						if(value.status == 'Closed'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_closed += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_closed += 1;
							}
						}

						if(value.progress == 'Refused'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_refused += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_refused += 1;
							}
						}

						if(value.progress == 'Under Check'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_under_check += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_under_check += 1;
							}
						}

						if(value.progress == 'MTA Change Request'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_change_request += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_change_request += 1;
							}
						}

						if(value.progress == 'Agree to Sign'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_agree_to_sign += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_agree_to_sign += 1;
							}
						}

						if(value.progress == 'Signed by Supplier'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_supplier_approved += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_supplier_approved += 1;
							}
						}

						if(value.progress == 'YCJ Approval'){
							if(value.pgr_name == 'Indirect Material'){
								count_indirect_ycj_approved += 1;
							}
							if(value.pgr_name == 'Direct Material' || value.pgr_name == 'Subcont.'){
								count_direct_ycj_approved += 1;
							}
						}
						vendor.push(value.vendor_name);
					}
					var annual = parseFloat(value.annual_purchase.toFixed());
					tableDetailBody += "<tr>";
					tableDetailBody += "<td style='text-align: center; width: 0.1%;'>"+cnt+"</td>";
					tableDetailBody += '<td style="width: 3%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.vendor_code+'\')">'+value.vendor_name+'</a></td>';
					tableDetailBody += '<td style="width: 3%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.vendor_code+'\')">'+value.vendor_name_sap+'</a></td>';
					tableDetailBody += '<td style="width: 0.1%;">'+value.currency+'</td>';
					tableDetailBody += '<td style="text-align: center; width: 0.1%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.vendor_code+'\')">'+value.vendor_code+'</a></td>';
					tableDetailBody += "<td style='width: 0.1%;'>"+value.location+"</td>";
					tableDetailBody += "<td style='text-align: right; width: 0.5%;'>"+annual.toLocaleString()+"</td>";
					// tableDetailBody += "<td style='width: 0.1%;'>"+value.category+"</td>";
					if(value.status_vendor == "Inactive"){
						tableDetailBody += "<td style='width: 0.1%; background-color: #555555; font-weight: bold; color: white;'>"+value.status_vendor+"</td>";
					}
					else{
						tableDetailBody += "<td style='width: 0.1%;'>"+value.status_vendor+"</td>";
					}
					tableDetailBody += "<td style='width: 0.8%;'>"+value.pgr_name+"</td>";
					tableDetailBody += "<td style='width: 1%;'>"+value.pic_id+"<br>"+value.pic_name+"</td>";
					if(value.old_version == 0){
						tableDetailBody += "<td style='width: 0.1%; font-weight: bold; color: red;'>None</td>";
					}
					else{
						tableDetailBody += "<td style='width: 0.1%; font-weight: bold; color: green;'>Exist</td>";	
					}
					if(value.pic_id == "" && value.status_vendor == 'Active'){
						tableDetailBody += "<td style='text-align: center; width: 0.1%;'></td>";
						tableDetailBody += "<td style='text-align: center; width: 0.1%;'></td>";
						tableDetailBody += "<td style='text-align: center; width: 0.1%;'></td>";
						tableDetailBody += "<td style='text-align: center; width: 0.1%;'></td>";
						tableDetailBody += "<td style='text-align: center; width: 0.1%;'></td>";
					}
					else{
						if(value.sent_at == ""){
							tableDetailBody += "<td style='text-align: center; width: 0.1%;'>";
							tableDetailBody += '<button onclick="modalUpdateProgress(\''+value.vendor_code+'\',\'sent_at\')" type="button" class="btn btn-primary btn-sm">Update</button>';
							tableDetailBody += "</td>";
						}
						else{
							if(value.sent_at_ev != ""){
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>";
								tableDetailBody += "<a href='{{ asset('trade_agreements/sent_at')}}/"+value.sent_at_ev+"' target='_blank'>"+value.sent_at+"</a>";
								tableDetailBody += "</td>";
							}
							else{
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>"+value.sent_at+"</td>";
							}
						}
						if(value.sign_at == ""){
							tableDetailBody += "<td style='text-align: center; width: 0.1%;'>";
							tableDetailBody += '<button onclick="modalUpdateProgress(\''+value.vendor_code+'\',\'sign_at\')" type="button" class="btn btn-primary btn-sm">Update</button>';
							tableDetailBody += "</td>";
						}
						else{
							if(value.sign_at_ev != ""){
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>";
								tableDetailBody += "<a href='{{ asset('trade_agreements/sign_at')}}/"+value.sign_at_ev+"' target='_blank'>"+value.sign_at+"</a>";
								tableDetailBody += "</td>";
							}
							else{
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>"+value.sign_at+"</td>";
							}
						}
						if(value.pre_ycj_at == ""){
							tableDetailBody += "<td style='text-align: center; width: 0.1%;'>";
							tableDetailBody += '<button onclick="modalUpdateProgress(\''+value.vendor_code+'\',\'pre_ycj_at\')" type="button" class="btn btn-primary btn-sm">Update</button>';
							tableDetailBody += "</td>";
						}
						else{
							if(value.pre_ycj_at_ev != ""){
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>";
								tableDetailBody += "<a href='{{ asset('trade_agreements/pre_ycj_at')}}/"+value.pre_ycj_at_ev+"' target='_blank'>"+value.pre_ycj_at+"</a>";
								tableDetailBody += "</td>";
							}
							else{
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>"+value.pre_ycj_at+"</td>";
							}
						}
						if(value.app_ycj_at == ""){
							tableDetailBody += "<td style='text-align: center; width: 0.1%;'>";
							tableDetailBody += '<button onclick="modalUpdateProgress(\''+value.vendor_code+'\',\'app_ycj_at\')" type="button" class="btn btn-primary btn-sm">Update</button>';
							tableDetailBody += "</td>";
						}
						else{
							if(value.app_ycj_at_ev != ""){
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>";
								tableDetailBody += "<a href='{{ asset('trade_agreements/app_ycj_at')}}/"+value.app_ycj_at_ev+"' target='_blank'>"+value.app_ycj_at+"</a>";
								tableDetailBody += "</td>";
							}
							else{
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>"+value.app_ycj_at+"</td>";
							}
						}
						if(value.app_dir_at == ""){
							tableDetailBody += "<td style='text-align: center; width: 0.1%;'>";
							tableDetailBody += '<button onclick="modalUpdateProgress(\''+value.vendor_code+'\',\'app_dir_at\')" type="button" class="btn btn-primary btn-sm">Update</button>';
							tableDetailBody += "</td>";
						}
						else{
							if(value.app_dir_at_ev != ""){
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>";
								tableDetailBody += "<a href='{{ asset('trade_agreements/app_dir_at')}}/"+value.app_dir_at_ev+"' target='_blank'>"+value.app_dir_at+"</a>";
								tableDetailBody += "</td>";
							}
							else{
								tableDetailBody += "<td style='text-align: right; width: 0.1%;'>"+value.app_dir_at+"</td>";
							}
						}
					}
					
					tableDetailBody += "<td style='width: 1%;'>"+value.progress+"</td>";
					if(value.status == 'Open'){
						tableDetailBody += "<td style='background-color: RGB(255,204,255); width: 0.1%;'>"+value.status+"</td>";
					}
					else{
						tableDetailBody += "<td style='background-color: RGB(204,255,255); width: 0.1%;'>"+value.status+"</td>";
					}
					tableDetailBody += "<td style='width: 0.1%;'>";
					if(value.att_en != ""){
						tableDetailBody += "<a href='{{ asset('trade_agreements')}}/"+value.att_en+"' target='_blank'>"+value.att_en+"</a>";
					}
					if(value.att_id != ""){
						tableDetailBody += "<br>";
						tableDetailBody += "<a href='{{ asset('trade_agreements')}}/"+value.att_id+"' target='_blank'>"+value.att_id+"</a>";						
					}
					if(value.amendment_en != ""){
						tableDetailBody += "<br>";
						tableDetailBody += "<a href='{{ asset('trade_agreements')}}/"+value.amendment_en+"' target='_blank'>"+value.amendment_en+"</a>";						
					}
					if(value.amendment_id != ""){
						tableDetailBody += "<br>";
						tableDetailBody += "<a href='{{ asset('trade_agreements')}}/"+value.amendment_id+"' target='_blank'>"+value.amendment_id+"</a>";						
					}
					tableDetailBody += "</td>";
					tableDetailBody += "</tr>";
				});

$('#no_no').text(no_no);
$('#no_no_ok_no').text(no_no_ok_no);
$('#ok_no').text(ok_no);
$('#no_ok').text(no_ok);
$('#no_ok_ok_ok').text(no_ok_ok_ok);
$('#ok_ok').text(ok_ok);
$('#no_end').text(no_end);
$('#no_end_ok_end').html('<span style="font-size: 30px;">'+no_end_ok_end+'</span><br>(No Need MTA)');
$('#ok_end').text(ok_end);

$('#count_indirect_domestic').text(count_indirect_domestic);
$('#count_indirect_overseas').text(count_indirect_overseas);
$('#count_indirect_total').text(count_indirect_total);
$('#count_indirect_none').text(count_indirect_none);
$('#count_indirect_exist').text(count_indirect_exist);
var indirect_draft = count_indirect_open-count_indirect_refused-count_indirect_under_check-count_indirect_change_request-count_indirect_agree_to_sign-count_indirect_supplier_approved-count_indirect_ycj_approved;
var indirect_draft_sent = count_indirect_refused+count_indirect_under_check+count_indirect_change_request+count_indirect_agree_to_sign+count_indirect_supplier_approved+count_indirect_ycj_approved;
				// $('#count_indirect_no_draft').text(indirect_draft);
				$('#count_indirect_draft').text(indirect_draft);
				$('#count_indirect_draft_sent').text(indirect_draft_sent);
				$('#count_indirect_refused').text(count_indirect_refused);
				$('#count_indirect_under_check').text(count_indirect_under_check);
				$('#count_indirect_change_request').text(count_indirect_change_request);
				$('#count_indirect_agree_to_sign').text(count_indirect_agree_to_sign);
				$('#count_indirect_supplier_approved').text(count_indirect_supplier_approved);
				$('#count_indirect_ycj_approved').text(count_indirect_ycj_approved);
				$('#count_indirect_closed').text(count_indirect_closed);
				$('#count_indirect_open').text(count_indirect_open);
				var indirect_percentage = (count_indirect_closed/(count_indirect_closed+count_indirect_open)*100).toFixed(2);
				$('#count_indirect_percentage').text(indirect_percentage+'%');


				$('#count_direct_domestic').text(count_direct_domestic);
				$('#count_direct_overseas').text(count_direct_overseas);
				$('#count_direct_total').text(count_direct_total);
				$('#count_direct_none').text(count_direct_none);
				$('#count_direct_exist').text(count_direct_exist);
				var direct_draft = count_direct_open-count_direct_refused-count_direct_under_check-count_direct_change_request-count_direct_agree_to_sign-count_direct_supplier_approved-count_direct_ycj_approved;
				var direct_draft_sent = count_direct_refused+count_direct_under_check+count_direct_change_request+count_direct_agree_to_sign+count_direct_supplier_approved+count_direct_ycj_approved;
				// $('#count_direct_no_draft').text(direct_draft);
				$('#count_direct_draft').text(direct_draft);
				$('#count_direct_draft_sent').text(direct_draft_sent);
				$('#count_direct_refused').text(count_direct_refused);
				$('#count_direct_under_check').text(count_direct_under_check);
				$('#count_direct_change_request').text(count_direct_change_request);
				$('#count_direct_agree_to_sign').text(count_direct_agree_to_sign);
				$('#count_direct_supplier_approved').text(count_direct_supplier_approved);
				$('#count_direct_ycj_approved').text(count_direct_ycj_approved);
				$('#count_direct_closed').text(count_direct_closed);
				$('#count_direct_open').text(count_direct_open);
				var direct_percentage = (count_direct_closed/(count_direct_closed+count_direct_open)*100).toFixed(2);
				$('#count_direct_percentage').text(direct_percentage+'%');


				$('#total_domestic').text(count_indirect_domestic+count_direct_domestic);
				$('#total_overseas').text(count_indirect_overseas+count_direct_overseas);
				$('#total_total').text(count_indirect_total+count_direct_total);
				$('#total_none').text(count_indirect_none+count_direct_none);
				$('#total_exist').text(count_indirect_exist+count_direct_exist);
				$('#total_no_draft').text(count_indirect_no_draft+count_direct_no_draft);
				$('#total_draft').text(indirect_draft+direct_draft);
				$('#total_draft_sent').text(count_indirect_draft_sent+count_direct_draft_sent);
				$('#total_refused').text(count_indirect_refused+count_direct_refused);
				$('#total_under_check').text(count_indirect_under_check+count_direct_under_check);
				$('#total_change_request').text(count_indirect_change_request+count_direct_change_request);
				$('#total_agree_to_sign').text(count_indirect_agree_to_sign+count_direct_agree_to_sign);
				$('#total_supplier_approved').text(count_indirect_supplier_approved+count_direct_supplier_approved);
				$('#total_ycj_approved').text(count_indirect_ycj_approved+count_direct_ycj_approved);
				$('#total_closed').text(count_indirect_closed+count_direct_closed);
				$('#total_open').text(count_indirect_open+count_direct_open);
				var total_percentage = ((count_indirect_closed+count_direct_closed)/((count_indirect_closed+count_direct_closed)+(count_indirect_open+count_direct_open))*100).toFixed(2);
				$('#total_percentage').text(total_percentage+'%');

				$('#tableDetailBody').append(tableDetailBody);

				Highcharts.chart('container1', {
					chart: {
						backgroundColor: null,
						type: 'pie',
						options3d: {
							enabled: true,
							alpha: 45,
							beta: 0
						},
					},
					title: {
						text: ''
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					legend: {
						enabled: false,
						symbolRadius: 1,
						borderWidth: 1
					},
					credits:{
						enabled:false
					},
					tooltip: {
						pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							edgeWidth: 1,
							edgeColor: 'rgb(126,86,134)',
							depth: 35,
							dataLabels: {
								// distance: -50,
								enabled: true,
								format: '<b>{point.name}<br>{point.y} item(s)</b><br>{point.percentage:.1f} %',
								style:{
									fontSize:'0.8vw',
									textOutline:0
								},
								color:'black',
								connectorWidth: '3px'
							},
							showInLegend: true,
						}
					},
					series: [{
						type: 'pie',
						data: [{
							name: 'Closed',
							y: count_indirect_closed+count_direct_closed,
							color: '#90ee7e'
						}, {
							name: 'Open',
							y: count_indirect_open+count_direct_open,
							color: '#d32f2f'
						}]
					}]
				});

				Highcharts.chart('container2', {
					chart: {
						type:'bar',
						backgroundColor: null,
						options3d: {
							enabled: true,
							alpha: 15,
							beta: 15,
							depth: 50,
							viewDistance: 25
						}
					},
					title: {
						text: 'Open MTA Progress'
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						gridLineColor: 'Black',
						categories: ['Not Sent','Refused','Under Check','MTA Change Request','Agree to Sign','Signed by Supplier','YCJ Approval'],
						crosshair: true,
						labels:{
							style:{
								color: 'black'									
							}
						}
					},
					yAxis: [{
						title: {
							text: ''
						},
						gridLineColor: 'Black'
					}],
					legend: {
						enabled: false,
						borderWidth: 1
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0;text-shadow: -1px 0 #909090, 0 1px #909090, 1px 0 #909090, 0 -1px #909090;font-size: 16px;font-weight:bold;">{series.name}: </td>' +
						'<td style="padding:0;font-size:16px;"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							edgeColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										modalChartProgress(this.category);
									}
								}
							}
						},
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								style:{
									color: 'black',
									fontSize: '20px'
								}
							}
						}
					},
					series: [{
						name: 'Count MTA',
						type: 'column',
						stack: 'Stock',
						data: [indirect_draft+direct_draft,count_indirect_refused+count_direct_refused,count_indirect_under_check+count_direct_under_check,count_indirect_change_request+count_direct_change_request,count_indirect_agree_to_sign+count_direct_agree_to_sign,count_indirect_supplier_approved+count_direct_supplier_approved,count_indirect_ycj_approved+count_direct_ycj_approved],
						color: '#d32f2f'
					}]
				});

				Highcharts.chart('container3', {
					colors: ['#d32f2f', '#d32f2f', '#90ee7e', '#90ee7e', 'Black', 'Black'],
					chart: {
						type:'bar',
						backgroundColor: null,
						options3d: {
							enabled: true,
							alpha: 15,
							beta: 15,
							depth: 50,
							viewDistance: 25
						}
					},
					title: {
						text: 'File MTA Status'
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						gridLineColor: 'Black',
						categories: ['NO & NO','OK & NO','NO & OK','OK & OK','NO & END','OK & END'],
						crosshair: true,
						labels:{
							style:{
								color: 'black'									
							}
						}
					},
					yAxis: [{
						title: {
							text: ''
						},
						gridLineColor: 'Black'
					}],
					legend: {
						symbolPadding: 0,
						symbolWidth: 0,
						symbolHeight: 0,
						squareSymbol: false,
						layout: 'horizontal',
						align: 'left',
						verticalAlign: 'top',
						x: -10,
						y: 15,
						floating: true,
						borderWidth: 0,
						backgroundColor: null,
						itemStyle: {
							color: 'black'
						}
					},
					tooltip: {
						enabled: false,
					},
					plotOptions: {
						column: {
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							edgeColor: '#212121',
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										modalChartFile(this.category);
									}
								}
							}
						},
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								style:{
									color: 'black',
									fontSize: '20px'
								}
							}
						}
					},
					series: [{
						name: 'OLD&nbsp;&nbsp;&nbsp;NEW',
						type: 'column',
						stack: 'Stock',
						colorByPoint: true,
						data: [no_no, ok_no, no_ok, ok_ok, no_end, ok_end]
					}]
				});

				$('#tableDetail').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 25, 50, -1 ],
					[ '25 rows', '50 rows', 'Show all' ]
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
					'searching': true,
					'ordering': false,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
				// MergeGridCells();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed.');
			}
		});
}

// function MergeGridCells() {
// 	var dimension_cells = new Array();
// 	var dimension_col = null;
// 	var columnCount = $("#tableDetail tr:first th").length;
// 	var columnCount = 3;
// 	for (dimension_col = 0; dimension_col < columnCount; dimension_col++) {
//         // first_instance holds the first instance of identical td
//         var first_instance = null;
//         var rowspan = 1;
//         // iterate through rows
//         $("#tableDetail").find('tr').each(function () {

//             // find the td of the correct column (determined by the dimension_col set above)
//             var dimension_td = $(this).find('td:nth-child(' + dimension_col + ')');

//             if (first_instance == null) {
//                 // must be the first row
//                 first_instance = dimension_td;
//             } else if (dimension_td.text() == first_instance.text()) {
//                 // the current td is identical to the previous
//                 // remove the current td
//                 dimension_td.remove();
//                 ++rowspan;
//                 // increment the rowspan attribute of the first instance
//                 first_instance.attr('rowspan', rowspan);
//             } else {
//                 // this cell is different from the last
//                 first_instance = dimension_td;
//                 rowspan = 1;
//             }
//         });
//     }
// }

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '5000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '5000'
	});
}
</script>

@endsection