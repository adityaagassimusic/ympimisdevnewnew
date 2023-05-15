@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	#detailTable > tbody > tr:hover{
		/*cursor: pointer;*/
		background-color: #7dfa8c !important;
	}
	#resumeTable > tbody > tr > td:hover{
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	#resumeCategoryTable > tbody > tr:hover{
		cursor: pointer;
		background-color: #7dfa8c !important;
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
		padding:  2px 5px 2px 5px;
	}
	#detailTable > tbody > tr > td{
		height: 40px;
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
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalCreate();"><i class="fa fa-pencil-square-o"></i> Create New</button>
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
	{{-- 	<div class="xol-xs-12 col-md-2 col-lg-4" id="container1" style="height: 40vh;">
	</div> --}}
	<div class="xol-xs-12 col-md-4 col-lg-2">
		<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px; height: 40vh;">
			<thead style="background-color: rgba(126,86,134,.7);">
				<tr>
					<th style="text-align: left;">Status</th>
					<th style="text-align: right;">Count</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td onclick="fetchStatus('All')" style="width: 1%; font-weight: bold; font-size: 1.2vw;">All</td>
					<td onclick="fetchStatus('All')" id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"></td>
				</tr>
				
				<tr>
					<td onclick="fetchStatus('AtRisk')" style="width: 1%; background-color: orange; font-weight: bold; font-size: 1.2vw;">AtRisk</td>
					<td onclick="fetchStatus('AtRisk')" id="count_risk" style="width: 1%; text-align: right; font-weight: bold; background-color: orange; font-size: 1.2vw;"></td>
				</tr>
				<tr>
					<td onclick="fetchStatus('Expired')" style="width: 1%; background-color: rgb(255,204,255); font-weight: bold; font-size: 1.2vw;">Expired</td>
					<td onclick="fetchStatus('Expired')" id="count_expired" style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(255,204,255); font-size: 1.2vw;"></td>
				</tr>
				<tr>
					<td onclick="fetchStatus('Discontinue')" style="width: 1%; background-color: grey; font-weight: bold; color: white; font-size: 1.2vw;">Discontinue</td>
					<td onclick="fetchStatus('Discontinue')" id="count_discontinue" style="width: 1%; text-align: right; font-weight: bold; background-color: grey; color: white; font-size: 1.2vw;"></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="xol-xs-12 col-md-4 col-lg-4">
		<table id="resumeCategoryTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px; height: 40vh;">
			<thead style="background-color: rgba(126,86,134,.7);">
				<tr>
					<th style="text-align: left; width: 6%">Category</th>
					<th style="text-align: right; width: 1%">Active</th>
					<th style="text-align: right; width: 1%">At Risk</th>
					<th style="text-align: right; width: 1%">Expr.</th>
					<th style="text-align: right; width: 1%">Disc.</th>
				</tr>
			</thead>
			<tbody id="resumeCategoryTableBody">
			</tbody>
		</table>
	</div>
	<div id="container2" class="xol-xs-12 col-md-4 col-lg-6">
	</div>
	<div class="col-xs-12">
		<table id="detailTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
			<thead style="background-color: rgba(126,86,134,.7);">
				<tr>
					<th style="border:1px solid black; width: 0.1%; text-align: center;" rowspan="2">#</th>
					{{-- <th style="border:1px solid black; width: 0.1%; text-align: left;" rowspan="2">ID</th> --}}
					<th style="border:1px solid black; width: 2.5%; text-align: left;" rowspan="2">Category</th>
					<th style="border:1px solid black; width: 2%; text-align: left;" rowspan="2">Employee ID<br>Employee Name</th>
					<th style="border:1px solid black; width: 4%; text-align: left;" rowspan="2">Remark</th>
					<th style="border:1px solid black; width: 1%; text-align: left;" rowspan="2">No. License</th>
					<th style="border:1px solid black; width: 2%; text-align: left;" rowspan="2">Loc<br>Deptartment</th>
					<th style="border:1px solid black; width: 1.5%; text-align: center;" colspan="3">Valid</th>
					<th style="border:1px solid black; width: 0.5%; text-align: left;" rowspan="2">Freq.</th>
					<th style="border:1px solid black; width: 1%; text-align: center;" rowspan="2">Att.</th>
					<th style="border:1px solid black; width: 0.5%; text-align: center;" rowspan="2">Status</th>
				</tr>
				<tr>
					<th style="width: 0.5%; text-align: right; border:1px solid black;">From</th>
					<th style="width: 0.5%; text-align: right; border:1px solid black;">To</th>
					<th style="width: 0.5%; text-align: right; border:1px solid black;">Diff</th>
				</tr>
			</thead>
			<tbody id="detailTableBody">
			</tbody>
		</table>
	</div>
</div>
</section>

<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Create New License<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<select class="form-control select2" name="createCategory" id="createCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach($license_categories as $license_category)
										<option value="{{ $license_category }}">{{ $license_category }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
								<div class="col-sm-7">
									<select class="form-control select2" name="createDepartment" id="createDepartment" data-placeholder="Select Department" style="width: 100%;">
										<option></option>
										@foreach($departments as $department)
										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Location<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter Location" id="createLocation">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">User<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<select class="form-control select2" name="createUser" id="createUser" data-placeholder="Select User" style="width: 100%;">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}-{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No. License<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter License Number" id="createLicense">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Valid From<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="createValidFrom" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Valid To<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="createValidTo" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Frequency<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" name="createFrequency" id="createFrequency" data-placeholder="Select Frequency" style="width: 100%;">
										<option value=""></option>
										<option value="1 Month">1 Month</option>
										<option value="1 Year">1 Year</option>
										<option value="2 Years">2 Years</option>
										<option value="3 Years">3 Years</option>
										<option value="4 Years">4 Years</option>
										<option value="5 Years">5 Years</option>
										<option value="Perpetual">Perpetual</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Reminder<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Reminder" id="createReminder">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Day(s) Before Expired
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Enter Remark" id="createCompliance">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Note<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Enter Note" id="createRemark">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Attachment<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="createAttachment">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Secrecy<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" name="createSecrecy" id="createSecrecy" data-placeholder="Select Frequency" style="width: 100%;">
										<option value="Public">Public</option>
										<option value="Confidential">Confidential</option>
									</select>
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="createNew()">CREATE</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Edit License<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">License ID<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter Location" id="editID" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" name="editStatus" id="editStatus" data-placeholder="Select Frequency" style="width: 100%;">
										<option value="Active">Active</option>
										<option value="AtRisk">AtRisk</option>
										<option value="Expired">Expired</option>
										<option value="Discontinue">Discontinue</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
								<div class="col-sm-6">
									<select class="form-control select2" name="editCategory" id="editCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach($license_categories as $license_category)
										<option value="{{ $license_category }}">{{ $license_category }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
								<div class="col-sm-7">
									<select class="form-control select2" name="editDepartment" id="editDepartment" data-placeholder="Select Department" style="width: 100%;">
										<option></option>
										@foreach($departments as $department)
										<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Location<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter Location" id="editLocation">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">User<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<select class="form-control select2" name="editUser" id="editUser" data-placeholder="Select User" style="width: 100%;">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}-{{ $employee->name }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No. License<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter License Number" id="editLicense">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Valid From<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editValidFrom" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Valid To<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="editValidTo" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Frequency<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" name="editFrequency" id="editFrequency" data-placeholder="Select Frequency" style="width: 100%;">
										<option value=""></option>
										<option value="1 Month">1 Month</option>
										<option value="1 Year">1 Year</option>
										<option value="2 Year">2 Years</option>
										<option value="3 Year">3 Years</option>
										<option value="4 Year">4 Years</option>
										<option value="5 Year">5 Years</option>
										<option value="Perpetual">Perpetual</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Reminder<span class="text-red">*</span> :</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Reminder" id="editReminder">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Day(s) Before Expired
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Enter Remark" id="editCompliance">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Note<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Enter Note" id="editRemark">-</textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Attachment<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="editAttachment">
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Secrecy<span class="text-red"></span> :</label>
								<div class="col-sm-3">
									<select class="form-control select2" name="editSecrecy" id="editSecrecy" data-placeholder="Select Frequency" style="width: 100%;">
										<option value="Public">Public</option>
										<option value="Confidential">Confidential</option>
									</select>
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12" style="padding-bottom: 10px;">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="editLicense()">UPDATE</button>
					</div>
					<span style="font-weight: bold; font-size: 1.2vw;">Change Logs</span>
					<table id="tableLog" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 0.1%; text-align: center;">#</th>
								<th style="width: 1%; text-align: left;">Status</th>
								<th style="width: 1%; text-align: left;">Updated By</th>
								<th style="width: 1%; text-align: right;">Updated At</th>
							</tr>
						</thead>
						<tbody id="tableLogBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalAtt">
	<div class="modal-dialog modal-md" style="width: 30%;">
		<div class="modal-content">x
			<div class="modal-header">
				<h4 id="modalAttTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableAtt" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 1%; text-align: center;">#</th>
								<th style="width: 1%; text-align: right;">Valid From</th>
								<th style="width: 1%; text-align: right;">Valid To</th>
								<th style="width: 1%; text-align: left;">Attachment</th>
								<th style="width: 2%; text-align: left;">Uploaded By</th>
							</tr>
						</thead>
						<tbody id="tableAttBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalChart">
	<div class="modal-dialog modal-md" style="width: 30%;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableModal" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th style="width: 0.1%">ID</th>
								<th style="width: 1%">Category</th>
								<th style="width: 1%">Employee</th>
								<th style="width: 1%">Location</th>
								<th style="width: 1%">Valid</th>
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
<script src="{{ url("js/data.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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
		fetchTable();
		$('#createValidFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#createValidTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#editValidFrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#editValidTo').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	$(function () {
		$('#createCategory').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('#createSecrecy').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('#createDepartment').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('#createUser').select2({
			dropdownParent: $('#modalCreate')
		});
	});

	$(function () {
		$('#createFrequency').select2({
			dropdownParent: $('#modalCreate'),
			minimumResultsForSearch: -1
		});
	});

	$(function () {
		$('#editCategory').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	$(function () {
		$('#editSecrecy').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	$(function () {
		$('#editDepartment').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	$(function () {
		$('#editUser').select2({
			dropdownParent: $('#modalEdit')
		});
	});

	$(function () {
		$('#editFrequency').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
	});

	$(function () {
		$('#editStatus').select2({
			dropdownParent: $('#modalEdit'),
			minimumResultsForSearch: -1
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var licenses = [];
	var license_logs = [];
	var license_attachments = [];

	function modalEdit(license_id){

		$.each(licenses, function(key, value){
			if(value.license_id == license_id){
				$('#editID').val(value.license_id);
				$('#editStatus').val(value.status).change();
				$('#editCategory').val(value.license_category).change();
				$('#editDepartment').val(value.department).change();
				$('#editLocation').val(value.location);
				$('#editUser').val(value.employee_id+'-'+value.employee_name).change();
				$('#editLicense').val(value.license_number);
				$('#editValidFrom').val(value.valid_from);
				$('#editValidTo').val(value.valid_to);
				$('#editFrequency').val(value.frequency).change();
				$('#editReminder').val(value.reminder);
				$('#editCompliance').val(value.compliance);
				$('#editRemark').val(value.remark);
				$('#editSecrecy').val(value.secrecy).change();
			}
		});

		var tableLogBody = "";
		$('#tableLogBody').html("");

		var count_log = 0;

		$.each(license_logs, function(key, value){
			if(value.license_id == license_id){
				count_log += 1;
				tableLogBody += '<tr>';
				tableLogBody += '<td style="text-align: center;">'+count_log+'</td>';
				tableLogBody += '<td style="text-align: left; font-weight: bold;"><a href="javascript:void(0)" onclick="alertLog(\''+value.id+'\')">'+value.log+'</a></td>';
				tableLogBody += '<td style="text-align: left;">'+value.creator_name+'</td>';
				tableLogBody += '<td style="text-align: right;">'+value.created_at+'</td>';
				tableLogBody += '</tr>';
			}
		});

		$('#tableLogBody').append(tableLogBody);
		$('#modalEdit').modal('show');
	}

	function alertLog(id){
		var licenseCategory = "";
		var status = "";
		var department = "";
		var location = "";
		var user = "";
		var license = "";
		var validFrom = "";
		var validTo = "";
		var frequency = "";
		var reminder = "";
		var compliance = "";
		var remark = "";
		var secrecy = "";

		$.each(license_logs, function(key, value){
			if(value.id == id){
				licenseCategory = value.license_category;
				department = value.department;
				location = value.location;
				status = value.status;
				user = value.employee_id+'-'+value.employee_name;
				license = value.license_number;
				validFrom = value.valid_from;
				validTo = value.valid_to;
				frequency = value.frequency;
				reminder = value.reminder;
				compliance = value.compliance;
				remark = value.remark;
				secrecy = value.secrecy;
			}
		});

		alert(
			'Status: '+status+'\n'+
			'Category: '+licenseCategory+'\n'+
			'Department: '+department+'\n'+
			'Location: '+location+'\n'+
			'User: '+user+'\n'+
			'No. License: '+license+'\n'+
			'Valid From: '+validFrom+'\n'+
			'Valid To: '+validTo+'\n'+
			'Frequency: '+frequency+'\n'+
			'Reminder: '+reminder+'\n'+
			'Compliance: '+compliance+'\n'+
			'Remark: '+remark+'\n'+
			'Secrecy: '+secrecy
			);
	}

	function modalAtt(license_id, category, number){
		var tableAttBody = "";
		$('#tableAttBody').html("");
		count_att = 0;

		$('#modalAttTitle').text('Attachment Of '+category+ ' ('+number+')');
		$.each(license_attachments, function(key, value){
			if(value.license_id == license_id){
				count_att += 1;
				tableAttBody += '<tr>';
				tableAttBody += '<td style="text-align: center;">'+count_att+'</td>';
				tableAttBody += '<td style="text-align: right;">'+value.valid_from+'</td>';
				tableAttBody += '<td style="text-align: right;">'+value.valid_to+'</td>';
				tableAttBody += '<td style="text-align: left;"><a href="{{ asset('files/licenses')}}/'+value.file_name+'" target="_blank">'+value.file_name+'</a></td>';
				tableAttBody += '<td style="text-align: left;">'+value.creator_name+'</td>';
				tableAttBody += '</tr>';
			}
		});

		$('#tableAttBody').append(tableAttBody);
		$('#modalAtt').modal('show');
	}

	function fetchCategory(category){
		var cnt_license = 0;
		var detailTableBody = "";
		$('#detailTableBody').html("");	
		$('#detailTable').DataTable().clear();
		$('#detailTable').DataTable().destroy();
		$.each(licenses, function(key, value){
			if(value.license_category == category){
				cnt_license += 1;
				if(value.secrecy == 'Confidential' && value.creator_id != "{{ Auth::user()->username }}"){
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;">'+value.license_id+'<br>'+value.license_category+'</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}
				else{
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.license_id+'\')">'+value.license_id+'</a><br>'+value.license_category+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.employee_id+'<br>'+value.employee_name+'</td>';
					detailTableBody += '<td style="width: 4%;">'+value.compliance+'</td>';
					detailTableBody += '<td style="width: 1%;">'+value.license_number+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.location+'<br>'+value.department+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_from+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_to+'</td>';
					if(value.frequency == 'Perpetual'){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= 0){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= value.reminder){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
					}
					else{
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					detailTableBody += '<td style="width: 0.5%;">'+value.frequency+'</td>';

					var cnt_att = 0;
					for(var i = 0; i < license_attachments.length; i++){
						if(license_attachments[i].license_id == value.license_id){
							cnt_att += 1;
						}
					}
					detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalAtt(\''+value.license_id+'\',\''+value.license_category+'\',\''+value.license_number+'\')">'+cnt_att+' Att(s)</a></td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}		
				if(value.status == 'Active'){
					count_active += 1;
				}
				if(value.status == 'AtRisk'){
					count_risk += 1;
				}
				if(value.status == 'Expired'){
					count_expired += 1;
				}
				if(value.status == 'Discontinue'){
					count_discontinue += 1;
				}
			}
		});
$('#detailTableBody').append(detailTableBody);

$('#detailTable').DataTable({
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
}

function fetchStatus(status){
	if(status == 'All'){
		fetchTable();
	}
	else{
		var cnt_license = 0;
		var detailTableBody = "";
		$('#detailTableBody').html("");	
		$('#detailTable').DataTable().clear();
		$('#detailTable').DataTable().destroy();
		$.each(licenses, function(key, value){
			if(value.status == status){
				cnt_license += 1;
				if(value.secrecy == 'Confidential' && value.creator_id != "{{ Auth::user()->username }}"){
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;">'+value.license_id+'<br>'+value.license_category+'</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}
				else{
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.license_id+'\')">'+value.license_id+'</a><br>'+value.license_category+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.employee_id+'<br>'+value.employee_name+'</td>';
					detailTableBody += '<td style="width: 4%;">'+value.compliance+'</td>';
					detailTableBody += '<td style="width: 1%;">'+value.license_number+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.location+'<br>'+value.department+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_from+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_to+'</td>';
					if(value.frequency == 'Perpetual'){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= 0){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= value.reminder){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
					}
					else{
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					detailTableBody += '<td style="width: 0.5%;">'+value.frequency+'</td>';

					var cnt_att = 0;
					for(var i = 0; i < license_attachments.length; i++){
						if(license_attachments[i].license_id == value.license_id){
							cnt_att += 1;
						}
					}
					detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalAtt(\''+value.license_id+'\',\''+value.license_category+'\',\''+value.license_number+'\')">'+cnt_att+' Att(s)</a></td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}		
				if(value.status == 'Active'){
					count_active += 1;
				}
				if(value.status == 'AtRisk'){
					count_risk += 1;
				}
				if(value.status == 'Expired'){
					count_expired += 1;
				}
				if(value.status == 'Discontinue'){
					count_discontinue += 1;
				}
			}
		});
$('#detailTableBody').append(detailTableBody);

$('#detailTable').DataTable({
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
}

}

$.date = function(dateObject) {
	var d = new Date(dateObject);
	var day = d.getDate();
	var month = d.getMonth() + 1;
	var year = d.getFullYear();
	if (day < 10) {
		day = "0" + day;
	}
	if (month < 10) {
		month = "0" + month;
	}
	var date = year + "-" + month + "-" + day;

	return date;
};

function fetchModal(cat, name){
	var tableModalBody = "";
	$('#tableModalBody').html("");

	$.each(licenses, function(key, value){
		if(value.month_reminder == cat){
			if(value.status != 'Discontinue'){
				tableModalBody += '<tr>';
				tableModalBody += '<td>'+value.license_id+'</td>';
				tableModalBody += '<td>'+value.license_category+'</td>';
				tableModalBody += '<td>'+value.employee_id+'<br>'+value.employee_name+'</td>';
				tableModalBody += '<td>'+value.location+'<br>'+value.department+'</td>';
				tableModalBody += '<td>'+value.valid_from+' to '+value.valid_to+'</td>';
				tableModalBody += '</tr>';				
			}
		}
	});

	$('#tableModalBody').append(tableModalBody);
	$('#modalChart').modal('show');
}

function fetchTable(){
	var category = "{{ $category }}";
	var data = {
		category:category
	}
	$.get('{{ url("fetch/license") }}', data, function(result, status, xhr){
		if(result.status){
			var cnt_license = 0;
			var detailTableBody = "";
			$('#detailTableBody').html("");	
			$('#detailTable').DataTable().clear();
			$('#detailTable').DataTable().destroy();

			licenses = result.licenses;
			license_logs = result.license_logs;
			license_attachments = result.license_attachments;

			var count_active = 0;
			var count_risk = 0;
			var count_expired = 0;
			var count_discontinue = 0;

			$.each(result.licenses, function(key, value){
				cnt_license += 1;
				if(value.secrecy == 'Confidential' && value.creator_id != "{{ Auth::user()->username }}"){
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;">'+value.license_id+'<br>'+value.license_category+'</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					detailTableBody += '<td style="background-color: black; color: red; text-align: center; font-weight: bold;"><i class="glyphicon glyphicon-ban-circle"></i> Confidential<br>(By: '+value.creator_name+')</td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}
				else{
					detailTableBody += '<tr>';
					detailTableBody += '<td style="width: 0.1%; text-align: center;">'+cnt_license+'</td>';
					detailTableBody += '<td style="width: 2.5%;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.license_id+'\')">'+value.license_id+'</a><br>'+value.license_category+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.employee_id+'<br>'+value.employee_name+'</td>';
					detailTableBody += '<td style="width: 4%;">'+value.compliance+'</td>';
					detailTableBody += '<td style="width: 1%;">'+value.license_number+'</td>';
					detailTableBody += '<td style="width: 2%;">'+value.location+'<br>'+value.department+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_from+'</td>';
					detailTableBody += '<td style="width: 0.5%; text-align: right;">'+value.valid_to+'</td>';
					if(value.frequency == 'Perpetual'){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= 0){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
					}
					else if(value.diff <= value.reminder){
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
					}
					else{
						detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
					}
					detailTableBody += '<td style="width: 0.5%;">'+value.frequency+'</td>';

					var cnt_att = 0;
					for(var i = 0; i < result.license_attachments.length; i++){
						if(result.license_attachments[i].license_id == value.license_id){
							cnt_att += 1;
						}
					}
					detailTableBody += '<td style="width: 1%; font-weight: bold; text-align: center;"><a href="javascript:void(0)" onclick="modalAtt(\''+value.license_id+'\',\''+value.license_category+'\',\''+value.license_number+'\')">'+cnt_att+' Att(s)</a></td>';
					if(value.status == 'Active'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
					}
					else if(value.status == 'AtRisk'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: orange;">'+value.status+'</td>';
					}
					else if(value.status == 'Expired'){
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
					}
					else{
						detailTableBody += '<td style="width: 0.5%; text-align: center; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
					}
					detailTableBody += '</tr>';
				}		
				if(value.status == 'Active'){
					count_active += 1;
				}
				if(value.status == 'AtRisk'){
					count_risk += 1;
				}
				if(value.status == 'Expired'){
					count_expired += 1;
				}
				if(value.status == 'Discontinue'){
					count_discontinue += 1;
				}		
			});

$('#count_all').text(cnt_license);
$('#count_active').text(count_active);
$('#count_risk').text(count_risk);
$('#count_expired').text(count_expired);
$('#count_discontinue').text(count_discontinue);
$('#detailTableBody').append(detailTableBody);

var resume_reminder = {};
var series = [];

$.each(result.licenses, function(key, value){
	if(value.status != 'Discontinue' && value.month_reminder != ''){
		key = Date.parse(value.month_reminder);
		if (!resume_reminder[key]) {
			resume_reminder[key] = 0;
		}
		resume_reminder[key] += 1;
	}
});

var ordered = Object.keys(resume_reminder).sort().reduce(
	(obj, key) => { 
		obj[key] = resume_reminder[key]; 
		return obj;
	}, 
	{}
	);

var xCategories = [];
var series = [];
var series2 = [];

$.each(ordered, function(key, value){
	if(value.status != 'Discontinue'){
		xCategories.push($.date(parseFloat(key)));
		series.push(value);
		series2.push([parseFloat(key), parseFloat(value)]);						
	}
});

Highcharts.chart('container2', {
	chart: {
		backgroundColor: null,
		type: 'column',
	},
	title: {
		text: 'License Renewal Monitoring'
	},
	credits: {
		enabled: false
	},
	xAxis:{
		tickInterval: 1,
		gridLineWidth: 1,
		categories: xCategories,
		crosshair: true
	},
	yAxis: [{
		title: {
			text: ''
		}
	}],
	legend: {
		enabled: false,
		borderWidth: 1
	},
	tooltip: {
		enabled: true
	},
	plotOptions: {
		column: {
			pointPadding: 0.93,
			groupPadding: 0.93,
			borderWidth: 0.8,
			borderColor: 'black'
		},
		series: {
			dataLabels: {
				enabled: true,
				format: '{point.y}',
				style:{
					textOutline: false
				}
			},
			cursor: 'pointer',
			point: {
				events: {
					click: function () {
						fetchModal(this.category, this.series.name);
					}
				}
			}
		}	
	},
	series: [{
		name: 'Licenses',
		data: series,
		color: 'orange'
	}]
});

$('#detailTable').DataTable({
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

resume_category = {};



var array = result.licenses;
var result = [];
array.reduce(function(res, value) {
	if (!res[value.license_category]) {
		res[value.license_category] = { license_category: value.license_category, total: 0, atrisk: 0, expired: 0, discontinue: 0 };
		result.push(res[value.license_category])
	}
	if(value.status == 'Active'){
		res[value.license_category].total += 1;
	}
	if(value.status == 'AtRisk'){
		res[value.license_category].atrisk += 1;						
	}
	if(value.status == 'Expired'){
		res[value.license_category].expired += 1;						
	}
	if(value.status == 'Discontinue'){
		res[value.license_category].discontinue += 1;						
	}
	return res;
}, {});


var resumeCategoryTableBody = "";
$('#resumeCategoryTableBody').html("");

$.each(result, function(key, value){
	resumeCategoryTableBody += '<tr>';
	resumeCategoryTableBody += '<td onclick="fetchCategory(\''+value.license_category+'\')">'+value.license_category+'</td>';
	resumeCategoryTableBody += '<td onclick="fetchCategory(\''+value.license_category+'\')" style="text-align: right; font-weight: bold;">'+value.total+'</td>';
	resumeCategoryTableBody += '<td onclick="fetchCategory(\''+value.license_category+'\')" style="text-align: right; font-weight: bold;">'+value.atrisk+'</td>';
	resumeCategoryTableBody += '<td onclick="fetchCategory(\''+value.license_category+'\')" style="text-align: right; font-weight: bold;">'+value.expired+'</td>';
	resumeCategoryTableBody += '<td onclick="fetchCategory(\''+value.license_category+'\')" style="text-align: right; font-weight: bold;">'+value.discontinue+'</td>';
	resumeCategoryTableBody += '</tr>';
});

$('#resumeCategoryTableBody').append(resumeCategoryTableBody);

}
else{
	alert('Unidentified Error '+result.message);
	audio_error.play();
	return false;
}
});
}

function editLicense(){
	if(confirm("Apakah anda yakin akan menambahkan data ini?")){
		// $('#loading').show();
		var category = "{{ $category }}";
		var prefix = "{{ $prefix }}";
		var licenseId = $('#editID').val();
		var licenseCategory = $('#editCategory').val();
		var status = $('#editStatus').val();
		var department = $('#editDepartment').val();
		var location = $('#editLocation').val();
		var user = $('#editUser').val();
		var license = $('#editLicense').val();
		var validFrom = $('#editValidFrom').val();
		var validTo = $('#editValidTo').val();
		var frequency = $('#editFrequency').val();
		var reminder = $('#editReminder').val();
		var compliance = $('#editCompliance').val();
		var remark = $('#editRemark').val();
		var attachment = $('#editAttachment').val();
		var secrecy = $('#editSecrecy').val();

		if(category == '' || department == '' || validFrom == '' || validTo == '' || frequency == '' || reminder == '' || user == '' || status == ''){
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		var old_licenseCategory = "";
		var old_department = "";
		var old_location = "";
		var old_status = "";
		var old_user = "";
		var old_license = "";
		var old_validFrom = "";
		var old_validTo = "";
		var old_frequency = "";
		var old_reminder = "";
		var old_compliance = "";
		var old_remark = "";

		$.each(licenses, function(key, value){
			if(value.license_id = licenseId){
				old_licenseCategory = value.license_category;
				old_department = value.department;
				old_location = value.location;
				old_status = value.status;
				old_user = value.employee_id+'-'+value.employee_name;
				old_license = value.license_number;
				old_validFrom = value.valid_from;
				old_validTo = value.valid_to;
				old_frequency = value.frequency;
				old_reminder = value.reminder;
				old_compliance = value.compliance;
				old_remark = value.remark;
			}
		});

		if(old_licenseCategory == licenseCategory 
			&& old_department == department
			&& old_location == location
			&& old_status == status
			&& old_user == user
			&& old_license == license
			&& old_validFrom == validFrom
			&& old_validTo == validTo
			&& old_frequency == frequency
			&& old_reminder == reminder
			&& old_compliance == compliance
			&& old_remark == remark
			&& attachment == "")
		{
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!', 'Tidak ada perubahan terdeteksi.');
			return false;
		}

		var formData = new FormData();
		var attachment  = $('#editAttachment').prop('files')[0];
		var file = $('#editAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('code', 'Updated');
		formData.append('licenseId', licenseId);
		formData.append('status', status);
		formData.append('category', category);
		formData.append('licenseCategory', licenseCategory);
		formData.append('department', department);
		formData.append('location', location);
		formData.append('user', user);
		formData.append('license', license);
		formData.append('validFrom', validFrom);
		formData.append('validTo', validTo);
		formData.append('frequency', frequency);
		formData.append('reminder', reminder);
		formData.append('compliance', compliance);
		formData.append('remark', remark);
		formData.append('attachment', attachment);
		formData.append('secrecy', secrecy);
		formData.append('prefix', prefix);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/license') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#modalEdit').modal('hide');
					$('#loading').hide();
					clearAll();
					fetchTable();
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}
			}
		});
	}
	else{
		return false;
	}
}

function createNew(){
	if(confirm("Apakah anda yakin akan menambahkan data ini?")){
		$('#loading').show();
		var category = "{{ $category }}";
		var prefix = "{{ $prefix }}";
		var licenseCategory = $('#createCategory').val();
		var department = $('#createDepartment').val();
		var location = $('#createLocation').val();
		var status = 'Active';
		var user = $('#createUser').val();
		var license = $('#createLicense').val();
		var validFrom = $('#createValidFrom').val();
		var validTo = $('#createValidTo').val();
		var frequency = $('#createFrequency').val();
		var reminder = $('#createReminder').val();
		var compliance = $('#createCompliance').val();
		var remark = $('#createRemark').val();
		var attachment = $('#createAttachment').val();
		var secrecy = $('#createSecrecy').val();

		if(category == '' || department == '' || validFrom == '' || validTo == '' || frequency == '' || reminder == ''){
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
			return false;
		}

		var formData = new FormData();
		var attachment  = $('#createAttachment').prop('files')[0];
		var file = $('#createAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('code', 'Created');
		formData.append('category', category);
		formData.append('status', status);
		formData.append('licenseCategory', licenseCategory);
		formData.append('department', department);
		formData.append('location', location);
		formData.append('user', user);
		formData.append('license', license);
		formData.append('validFrom', validFrom);
		formData.append('validTo', validTo);
		formData.append('frequency', frequency);
		formData.append('reminder', reminder);
		formData.append('compliance', compliance);
		formData.append('remark', remark);
		formData.append('attachment', attachment);
		formData.append('secrecy', secrecy);
		formData.append('prefix', prefix);
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('input/license') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					$('#modalCreate').modal('hide');
					$('#loading').hide();
					clearAll();
					fetchTable();
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}
			}
		});
	}
	else{
		return false;
	}
}

function modalCreate(){
	clearAll();
	$('#modalCreate').modal('show');
}

function clearAll(){
	$('#loading').hide();
	$('#createCategory').prop('selectedIndex', 0).change();
	$('#createDepartment').prop('selectedIndex', 0).change();
	$('#createUser').prop('selectedIndex', 0).change();
	$('#createLocation').val("");
	$('#createLicense').val("");
	$('#createValidFrom').val("");
	$('#createValidTo').val("");
	$('#createFrequency').prop('selectedIndex', 0).change();
	$('#createReminder').val(30);
	$('#createCompliance').val("-");
	$('#createRemark').val("-");
	$('#createAttachment').val("");
}

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