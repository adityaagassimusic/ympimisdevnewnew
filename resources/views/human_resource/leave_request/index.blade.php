@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">


<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
		padding-left: 5px !important;
	}
	tfoot>tr>th{
		/*text-align:center;*/
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.containers {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 15px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	  padding-top: 6px;
	}

	/* Hide the browser's default checkbox */
	.containers input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  margin-top: 4px;
	}

	/* On mouse-over, add a grey background color */
	.containers:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.containers input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.containers input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.containers .checkmark:after {
	  left: 9px;
	  top: 5px;
	  width: 5px;
	  height: 10px;
	  border: solid white;
	  border-width: 0 3px 3px 0;
	  -webkit-transform: rotate(45deg);
	  -ms-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px" onclick="clearAll()">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Buat Pengajuan
		</button>
		<?php if (isset($user)): ?>
			<?php if ($user->department == 'Human Resources Department' || $user->department == 'General Affairs Department' || $user->department == 'Management Information System Department' || $user->department == null): ?>
				<a class="btn btn-primary btn-sm pull-right" href="{{url('index/human_resource/leave_request_report')}}" style="margin-right: 5px">
					<i class="fa fa-book"></i>&nbsp;&nbsp; Report Surat Izin Keluar
				</a>
			<?php endif ?>
		<?php endif ?>
		<button class="btn btn-info btn-sm pull-right" style="margin-right: 5px" onclick="fillList()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>			
	<div class="row">
		<div class="col-xs-12 pull-left">
			<div style="background-color: #3f51b5;color: white;padding: 5px;text-align: center;margin-bottom: 8px">
				<span style="font-weight: bold;font-size: 30px">IN PROGRESS</span>
			</div>
			<!-- <h2 style="margin-top: 0px;">Master Operator Welding</h2> -->
			<table id="tableLeave" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">Req</th>
						<th width="1%">Dept</th>
						<th width="1%">Cat</th>
						<th width="2%">Purpose</th>
						<th width="1%">From</th>
						<th width="1%">To</th>
						<th width="1%">Return</th>
						<th width="1%">Emp</th>
						<th width="2%" style="background-color: #3064db">Applicant</th>
						<th width="2%" style="background-color: #3064db">Approver</th>
						<th width="2%" style="background-color: #3064db">HR</th>
						<th width="2%" style="background-color: #3064db">GA</th>
						<th width="2%" style="background-color: #3064db">Security</th>
						<th width="2%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableLeave">
				</tbody>
				<!-- <tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot> -->
			</table>
		</div>
		<div class="col-xs-12 pull-left">
			<div style="background-color: #32a852;color: white;padding: 5px;text-align: center;margin-bottom: 8px;margin-top: 10px">
				<span style="font-weight: bold;font-size: 30px">COMPLETED</span>
			</div>
			<!-- <h2 style="margin-top: 0px;">Master Operator Welding</h2> -->
			<table id="tableLeaveComplete" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #fff;">
					<tr>
						<th width="1%">Req</th>
						<th width="1%">Dept</th>
						<th width="1%">Cat</th>
						<th width="2%">Purpose</th>
						<th width="1%">From</th>
						<th width="1%">To</th>
						<th width="1%">Return</th>
						<th width="1%">Emp</th>
						<th width="2%" style="background-color: #3064db">Applicant</th>
						<th width="2%" style="background-color: #3064db">Approver</th>
						<th width="2%" style="background-color: #3064db">HR</th>
						<th width="2%" style="background-color: #3064db">GA</th>
						<th width="2%" style="background-color: #3064db">Security</th>
						<th width="2%">Action</th>
					</tr>
				</thead>
				<tbody id="bodyTableLeaveComplete">
				</tbody>
				<!-- <tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot> -->
			</table>
		</div>
	</div>

	<div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Are you sure delete?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Pengajuan Surat Izin Keluar</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<?php if (date('Y-m-d H:i:s') >= date('Y-m-d 17:00:00') && date('Y-m-d H:i:s') <= date('Y-m-d', strtotime("+1 day")).' 06:00:00'): ?>
							<center>
								<span style="font-weight: bold;color: red">Shift 2 / Shift 3</span><br>
								<span style="color: red">Approval Manager dan HR / GA akan dilanjutkan otomatis oleh sistem.<br>Silahkan cek Email konfirmasi <b>Request ID (Contoh : SIK....)</b> untuk ditunjukkan ke Security sebelum keluar perusahaan.</span>
							</center>
						<?php endif ?>
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-2">Yang Mengajukan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="request_by" placeholder="Requested" required value="<?php if(ISSET($user->employee_id)){
											echo $user->employee_id;
										} ?> - <?php if(ISSET($user->name)){
											echo $user->name;
										} ?>" readonly>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Tanggal Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control datepicker" id="date" placeholder="Req Date" required value="{{date('Y-m-d')}}">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Keperluan<span class="text-red">*</span></label>
									<div class="col-sm-3" align="left" style="padding-right: 2px" id="selectPur">
										<select class="form-control selectPur" data-placeholder="Pilih Kategori Keperluan" name="add_purpose_category" id="add_purpose_category" style="width: 100%" onchange="changePurpose(this.value)">
											<option value=""></option>
											<option value="DINAS">DINAS</option>
											<option value="PRIBADI">PRIBADI</option>
										</select>
									</div>
									<div class="col-sm-3" align="left" style="padding-left: 2px;padding-right: 2px" id="selectDet">
										<select class="form-control selectDet" data-placeholder="Pilih Detail Kategori Keperluan" name="add_purpose_detail" id="add_purpose_detail" style="width: 100%" onchange="changePurposeDetail(this.value)">
										</select>
									</div>
									<div class="col-sm-3" align="left" style="padding-left: 2px" id="divDetail">
										<input type="text" class="form-control" id="add_detail" placeholder="Masukkan Detail Keperluan" required value="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Detail Kota<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectCitys">
										<select class="form-control selectCity" data-placeholder="Pilih Detail Kota" name="add_detail_city" id="add_detail_city" style="width: 100%">
											<option value=""></option>
											<option value="Dalam Kota">Dalam Kota</option>
											<option value="Luar Kota">Luar Kota</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Waktu<span class="text-red">*</span></label>
									<div class="col-sm-2" align="left" style="padding-right: 2px">
										<input type="text" id="add_time_departure" name="add_time_departure" class="form-control timepicker" value="0:00">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 2px;padding-right: 10px">
										<input type="text" id="add_time_arrived" name="add_time_arrived" class="form-control timepicker" value="0:00">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 8px;padding-right: 2px;">
										<label class="containers" onclick="changeReturn()">Tidak Kembali
										  <input type="checkbox" id="add_return_or_not" name="add_return_or_not">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Karyawan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="selectEmp">
										<select class="form-control selectEmp" data-placeholder="Pilih Karyawan" name="add_employees" id="add_employees" style="width: 100%">
											<option value=""></option>
											@foreach($employees as $emp)
											<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-xs-3 row">
										<button class="btn btn-success" onclick="addEmployee()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableEmployee">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 5%;">Name</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableEmployeeBody">
									</tbody>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th>Total: </th>
											<th id="countTotal"></th>
											<th></th>
										</tr>
									</tfoot>
								</table>
								<div class="form-group row" style="border-top:2px solid red;margin-top: 20px">
									<label class="col-sm-2">Driver<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<label class="containers" onclick="changeDriver()">Butuh Driver
										  <input type="checkbox" id="add_driver" name="add_driver">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row" id="divCity" style="display: none">
									<label class="col-sm-2">Kota Destinasi<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="add_city" placeholder="Masukkan Kota Destinasi" required value="">
									</div>
								</div>
								<div class="form-group row" id="divDestination" style="display: none">
									<label class="col-sm-2">Detail Destinasi<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="add_destination" placeholder="Masukkan Detail Destinasi" required value="">
									</div>
									<div class="col-xs-3 row">
										<button class="btn btn-success" onclick="addDestination()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableDestination" style="display: none">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 5%;">Destinasi</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableDestinationBody">
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
					<button class="btn btn-success" onclick="createRequest()"><i class="fa fa-plus"></i> Buat Pengajuan</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="edit-modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Surat Izin Keluar</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row">
									<label class="col-sm-2">Yang Mengajukan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="request_by" placeholder="Requested" required value="<?php if(ISSET($user->employee_id)){
											echo $user->employee_id;
										} ?> - <?php if(ISSET($user->name)){
											echo $user->name;
										} ?>" readonly>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Tanggal Pengajuan<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="hidden" name="edit_request_id" id="edit_request_id">
										<input type="text" class="form-control datepicker" id="edit_date" placeholder="Req Date" required readonly="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Keperluan<span class="text-red">*</span></label>
									<div class="col-sm-3" align="left" style="padding-right: 2px" id="selectPurEdit">
										<select class="form-control selectPurEdit" data-placeholder="Pilih Keperluan" name="edit_purpose_category" id="edit_purpose_category" style="width: 100%" onchange="changePurposeEdit(this.value)">
											<option value=""></option>
											<option value="DINAS">DINAS</option>
											<option value="PRIBADI">PRIBADI</option>
										</select>
									</div>
									<div class="col-sm-3" align="left" style="padding-left: 2px;padding-right: 2px" id="selectDetEdit">
										<select class="form-control selectDetEdit" data-placeholder="Pilih Detail Keperluan" name="edit_purpose_detail" id="edit_purpose_detail" style="width: 100%" onchange="changePurposeDetailEdit(this.value)">
										</select>
									</div>
									<div class="col-sm-3" align="left" style="padding-left: 2px" id="divDetail">
										<input type="text" class="form-control" id="edit_detail" placeholder="Masukkan Detail" required value="">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Detail Kota<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" style="padding-right: 2px" id="selectCityEdits">
										<select class="form-control selectCityEdit" data-placeholder="Pilih Detail Kota" name="edit_detail_city" id="edit_detail_city" style="width: 100%">
											<option value=""></option>
											<option value="Dalam Kota">Dalam Kota</option>
											<option value="Luar Kota">Luar Kota</option>
										</select>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Waktu<span class="text-red">*</span></label>
									<div class="col-sm-2" align="left" style="padding-right: 2px">
										<input type="text" id="edit_time_departure" name="edit_time_departure" class="form-control timepicker" value="0:00">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 2px;padding-right: 10px">
										<input type="text" id="edit_time_arrived" name="edit_time_arrived" class="form-control timepicker" value="0:00">
									</div>
									<div class="col-sm-2" align="left" style="padding-left: 8px;padding-right: 2px;">
										<label class="containers" onclick="changeReturnEdit()">Tidak Kembali
										  <input type="checkbox" id="edit_return_or_not" name="edit_return_or_not">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2">Karyawan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left" id="selectEmpEdit">
										<select class="form-control selectEmpEdit" data-placeholder="Pilih Karyawan" name="edit_employees" id="edit_employees" style="width: 100%">
											<option value=""></option>
											@foreach($employees as $emp)
											<option value="{{$emp->employee_id}}_{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="col-xs-3 row">
										<button class="btn btn-success" onclick="addEmployeeEdit()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableEmployeeEdit">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 1%;">ID</th>
											<th style="width: 5%;">Name</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableEmployeeBodyEdit">
									</tbody>
									<tfoot style="background-color: RGB(252, 248, 227);">
										<tr>
											<th>Total: </th>
											<th id="countTotalEdit"></th>
											<th></th>
										</tr>
									</tfoot>
								</table>
								<div class="form-group row" style="border-top:2px solid red;margin-top: 20px">
									<label class="col-sm-2">Driver<span class="text-red">*</span></label>
									<div class="col-sm-2" align="left">
										<label class="containers" onclick="changeDriverEdit()">Butuh Driver
										  <input type="checkbox" id="edit_driver" name="edit_driver">
										  <span class="checkmark"></span>
										</label>
									</div>
								</div>
								<div class="form-group row" id="divCityEdit" style="display: none">
									<label class="col-sm-2">Kota Destinasi<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="edit_city" placeholder="Masukkan Kota Destinasi" required value="">
									</div>
								</div>
								<div class="form-group row" id="divDestinationEdit" style="display: none">
									<label class="col-sm-2">Detail Destinasi<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="edit_destination" placeholder="Masukkan Detail Destinasi" required value="">
									</div>
									<div class="col-xs-3 row">
										<button class="btn btn-success" onclick="addDestinationEdit()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableDestinationEdit" style="display: none">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 5%;">Destinasi</th>
											<th style="width: 1%;">Action</th>
										</tr>
									</thead>
									<tbody id="tableDestinationBodyEdit">
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
					<button class="btn btn-success" onclick="updateRequest()"><i class="fa fa-pencil"></i> Update</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="detailModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">
								&times;
							</span>
						</button>
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail Pengajuan</h1>
					</div>
				</div>
				<div class="modal-body" style="padding-top: 0px">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body" style="padding-top: 0px;margin-top: 0px">
								<center style="padding-top: 0px">
									<div style="width: 60%" style="padding-top: 0px">
										<span id="reason" style="font-weight: bold;font-size: 18px;color: red">
											aa
										</span>
										<br>
										<table style="border:1px solid black; border-collapse: collapse;">
											<tbody align="center">
												<tr>
													<td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Informasi Pengajuan</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Request ID
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="request_id">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Kategori Keperluan
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="purpose_category">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Detail Keperluan
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="purpose_detail">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Detail Kota
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="city_detail">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Waktu Keluar
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="time_departure">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Waktu Kembali
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="time_arrived">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Kembali / Tidak
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="return_or_not">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Butuh Driver
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="add_driver_or_not">
														
													</td>
												</tr>
											</tbody>
										</table>

										<table style="border:1px solid black; border-collapse: collapse;margin-top:20px;width: 100%">
											<thead align="center">
												<tr>
													<td colspan="3" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Karyawan</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15; font-weight: bold;background-color: #d4e157;">
														ID
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 3%; height: 15;font-weight: bold;background-color: #d4e157;">
														Name
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;">
														Dept
													</td>
												</tr>
											</thead>
											<tbody align="center" id="bodyEmp">
												
											</tbody>
										</table>

										<table style="border:1px solid black; border-collapse: collapse;margin-top:20px;width: 100%" id="tableClinicDetail">
											<thead align="center">
												<tr>
													<td colspan="2" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Hasil Klinik</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;background-color: #d4e157;">
														Diagnose
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;" id="diagnose">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;background-color: #d4e157;">
														Action
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;" id="action">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;background-color: #d4e157;">
														Suggestion
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 3%; height: 15;" id="suggestion">
													</td>
												</tr>
											</thead>
											<!-- <tbody align="center" id="bodyDestinationDetail">
											</tbody> -->
										</table>

										<table style="border:1px solid black; border-collapse: collapse;margin-top:20px;width: 100%" id="tableDestinationDetail">
											<thead align="center">
												<tr>
													<td colspan="2" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Destinasi</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 15px; width: 11%; height: 15; font-weight: bold;background-color: #d4e157;">
														#
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 3%; height: 15; font-weight: bold;background-color: #d4e157;">
														Tujuan
													</td>
												</tr>
											</thead>
											<tbody align="center" id="bodyDestinationDetail">
											</tbody>
										</table>
										
									</div>
									<div style="width: 80%">
										<table style="border:1px solid black; border-collapse: collapse;margin-top:20px">
											<thead align="center" id="headApproval">
												
											</thead>
											<tbody align="center" id="bodyApproval">
												<tr>
												
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;">
													</td>
												</tr>
												
											</tbody>
										</table>
									</div>
								</center>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>




</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	var employees = [];
	var count = 0;
	var destinations = [];
	var countDestination = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		clearAll();

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: '<?php echo $tgl_max ?>'
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	});


	$(function () {
		$('.selectPur').select2({
			dropdownParent: $('#selectPur'),
			allowClear:true
		});
		$('.selectCity').select2({
			dropdownParent: $('#selectCitys'),
			allowClear:true
		});
		$('.selectDet').select2({
			dropdownParent: $('#selectDet'),
			allowClear:true
		});
		$('.selectEmp').select2({
			dropdownParent: $('#selectEmp'),
			allowClear:true
		});

		$('.selectPurEdit').select2({
			dropdownParent: $('#selectPurEdit'),
			allowClear:true
		});
		$('.selectCityEdit').select2({
			dropdownParent: $('#selectCityEdits'),
			allowClear:true
		});
		$('.selectDetEdit').select2({
			dropdownParent: $('#selectDetEdit'),
			allowClear:true
		});
		$('.selectEmpEdit').select2({
			dropdownParent: $('#selectEmpEdit'),
			allowClear:true
		});
	});

	function clearAll() {
		$('#divDestination').hide();
    	$('#divCity').hide();
    	$('#tableDestination').hide();
		$('#add_purpose_category').val('').trigger('change');
		$('#add_purpose_detail').val('').trigger('change');
		$('#add_detail').val('');
		$('#add_employees').val('').trigger('change');
		$('#add_destination').val('');
		$('#add_detail_city').val('').trigger('change');
		$('#tableEmployeeBody').html('');

		$('#countTotal').html('0');
		employees = [];
		count = 0;
		destinations = [];
		countDestination = 0;

		$("input[name='add_return_or_not']").each(function (i) {
            $('#add_return_or_not')[i].checked = false;
        });

        $("input[name='add_driver']").each(function (i) {
            $('#add_driver')[i].checked = false;
        });
        $('#add_time_departure').val('0:00');
        $('#add_time_arrived').val('0:00');
        $('#add_time_arrived').show();
        $('#date').val(getActualFullDate());
	}

	function changePurpose(param) {
		$('#add_purpose_detail').html('');
		var purpose_detail = '';
		purpose_detail += '<option value=""></option>';
		if (param === 'DINAS') {
			purpose_detail += '<option value="SUPPLIER">SUPPLIER</option>';
			purpose_detail += '<option value="KANTOR PEMERINTAHAN">KANTOR PEMERINTAHAN</option>';
			purpose_detail += '<option value="LAIN-LAIN">LAIN-LAIN</option>';
		}else if(param === 'PRIBADI'){
			purpose_detail += '<option value="SAKIT">SAKIT</option>';
			purpose_detail += '<option value="MUSIBAH KELUARGA">MUSIBAH KELUARGA</option>';
			purpose_detail += '<option value="LAIN-LAIN">LAIN-LAIN</option>';
		}
		$('#add_purpose_detail').append(purpose_detail);
	}

	function changePurposeDetail(param) {
		var data = {
			purpose_detail:param
		}
		$.get('{{ url("fetch/human_resource/leave_request/employees") }}', data, function(result, status, xhr){
			if(result.status){
				var emps = '';
				$('#add_employees').html('');
				emps += '<option value=""></option>';
				for(var i = 0; i<result.emp.length;i++){
					emps += '<option value="'+result.emp[i].employee_id+'_'+result.emp[i].name+'">'+result.emp[i].employee_id+' - '+result.emp[i].name+'</option>';
				}
				$('#add_employees').append(emps);
				$('#add_employees').val('').trigger('change');
			}else{
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function changePurposeDetailEdit(param) {
		var data = {
			purpose_detail:param
		}
		$.get('{{ url("fetch/human_resource/leave_request/employees") }}', data, function(result, status, xhr){
			if(result.status){
				var emps = '';
				$('#edit_employees').html('');
				emps += '<option value=""></option>';
				for(var i = 0; i<result.emp.length;i++){
					emps += '<option value="'+result.emp[i].employee_id+'_'+result.emp[i].name+'">'+result.emp[i].employee_id+' - '+result.emp[i].name+'</option>';
				}
				$('#edit_employees').append(emps);
				$('#edit_employees').val('').trigger('change');
			}else{
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function changePurposeEdit(param) {
		$('#edit_purpose_detail').html('');
		var purpose_detail = '';
		purpose_detail += '<option value=""></option>';
		if (param === 'DINAS') {
			purpose_detail += '<option value="SUPPLIER">SUPPLIER</option>';
			purpose_detail += '<option value="KANTOR PEMERINTAHAN">KANTOR PEMERINTAHAN</option>';
			purpose_detail += '<option value="LAIN-LAIN">LAIN-LAIN</option>';
		}else if(param === 'PRIBADI'){
			purpose_detail += '<option value="SAKIT">SAKIT</option>';
			purpose_detail += '<option value="MUSIBAH KELUARGA">MUSIBAH KELUARGA</option>';
			purpose_detail += '<option value="LAIN-LAIN">LAIN-LAIN</option>';
		}
		$('#edit_purpose_detail').append(purpose_detail);
	}


	function changeReturn() {
		var returns = '';
		$("input[name='add_return_or_not']:checked").each(function (i) {
            returns = $(this).val();
        });
        if (returns == 'on') {
        	$('#add_time_arrived').hide();
        	$('#add_time_arrived').val('0:00');
        }else{
        	$('#add_time_arrived').show();
        	$('#add_time_arrived').val('0:00');
        }
	}

	function changeReturnEdit() {
		var returns = '';
		$("input[name='edit_return_or_not']:checked").each(function (i) {
            returns = $(this).val();
        });
        if (returns == 'on') {
        	$('#edit_time_arrived').hide();
        	$('#edit_time_arrived').val('0:00');
        }else{
        	$('#edit_time_arrived').show();
        	$('#edit_time_arrived').val('0:00');
        }
	}

	function addEmployee() {
		var str = $('#add_employees').val();
		var employee_id = str.split('_')[0];
		var name = str.split('_')[1];

		if (str == "") {
			audio_error.play();
			openErrorGritter('Error!','Pilih Karyawan');
			return false;
		}

		if($.inArray(employee_id, employees) != -1){
			audio_error.play();
			openErrorGritter('Error!','Karyawan sudah ada di list.');
			return false;
		}

		var tableEmployee = "";

		tableEmployee += "<tr id='"+employee_id+"'>";
		tableEmployee += "<td>"+employee_id+"</td>";
		tableEmployee += "<td>"+name+"</td>";
		tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployee(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableEmployee += "</tr>";

		employees.push(employee_id);
		count += 1;

		$('#countTotal').text(count);
		$('#tableEmployeeBody').append(tableEmployee);
		$('#add_employees').val('').trigger('change');
	}

	function addEmployeeEdit() {
		var str = $('#edit_employees').val();
		var employee_id = str.split('_')[0];
		var name = str.split('_')[1];

		if (str == "") {
			audio_error.play();
			openErrorGritter('Error!','Pilih Karyawan');
			return false;
		}

		if($.inArray(employee_id, employees) != -1){
			audio_error.play();
			openErrorGritter('Error!','Karyawan sudah ada di list.');
			return false;
		}

		var tableEmployee = "";

		tableEmployee += "<tr id='"+employee_id+"'>";
		tableEmployee += "<td>"+employee_id+"</td>";
		tableEmployee += "<td>"+name+"</td>";
		tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployeeEdit(id)' id='"+employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableEmployee += "</tr>";

		employees.push(employee_id);
		count += 1;

		$('#countTotalEdit').text(count);
		$('#tableEmployeeBodyEdit').append(tableEmployee);
		$('#edit_employees').val('').trigger('change');
	}

	function addDestination() {
		var str = $('#add_destination').val();

		if (str == "") {
			audio_error.play();
			openErrorGritter('Error!','Masukkan detail Destinasi.');
			return false;
		}

		if($.inArray(str, destinations) != -1){
			audio_error.play();
			openErrorGritter('Error!','Detail Destinasi sudah ada di list.');
			return false;
		}

		var tableDestination = "";

		tableDestination += "<tr id='destination_"+countDestination+"'>";
		tableDestination += "<td>"+str+"</td>";
		tableDestination += "<td><a href='javascript:void(0)' onclick='remDestination(id)' id='destination_"+countDestination+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableDestination += "</tr>";

		destinations.push(str);
		countDestination += 1;

		$('#tableDestinationBody').append(tableDestination);
		$('#add_destination').val('');
		$('#add_destination').focus();
	}

	function addDestinationEdit() {
		var str = $('#edit_destination').val();

		if (str == "") {
			audio_error.play();
			openErrorGritter('Error!','Masukkan detail Destinasi.');
			return false;
		}

		if($.inArray(str, destinations) != -1){
			audio_error.play();
			openErrorGritter('Error!','Tujuan sudah ada di list.');
			return false;
		}

		var tableDestination = "";

		tableDestination += "<tr id='destination_"+countDestination+"'>";
		tableDestination += "<td>"+str+"</td>";
		tableDestination += "<td><a href='javascript:void(0)' onclick='remDestination(id)' id='destination_"+countDestination+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableDestination += "</tr>";

		destinations.push(str);
		countDestination += 1;

		$('#tableDestinationBodyEdit').append(tableDestination);
		$('#edit_destination').val('');
		$('#edit_destination').focus();
	}

	function remEmployee(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotal').text(count);
		$('#'+id).remove();	
	}

	function remEmployeeEdit(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotalEdit').text(count);
		$('#'+id).remove();	
	}

	function remDestination(id){
		destinations.splice( $.inArray(id), 1 );
		count -= 1;
		$('#'+id).remove();	
	}

	function changeDriver() {
		var returns = '';
		$("input[name='add_driver']:checked").each(function (i) {
            returns = $(this).val();
        });
        if (returns == 'on') {
        	$('#divDestination').show();
        	$('#divCity').show();
        	$('#tableDestination').show();
        }else{
        	$('#divDestination').hide();
        	$('#divCity').hide();
        	$('#tableDestination').hide();
        }
	}

	function changeDriverEdit() {
		var returns = '';
		$("input[name='edit_driver']:checked").each(function (i) {
            returns = $(this).val();
        });
        if (returns == 'on') {
        	$('#divDestinationEdit').show();
        	$('#divCityEdit').show();
        	$('#tableDestinationEdit').show();
        }else{
        	$('#divDestinationEdit').hide();
        	$('#divCityEdit').hide();
        	$('#tableDestinationEdit').hide();
        }
	}

	function createRequest() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var date = $('#date').val();
			var purpose_category = $('#add_purpose_category').val();
			var purpose = $('#add_purpose_detail').val();
			var purpose_detail = $('#add_detail').val();
			var time_departure = $('#add_time_departure').val();
			var time_arrived = $('#add_time_arrived').val();
			var detail_city = $('#add_detail_city').val();
			var city = $('#add_city').val();
			var returns = '';
			$("input[name='add_return_or_not']:checked").each(function (i) {
	            returns = $(this).val();
	        });
	        if (returns == 'on') {
	        	var return_or_not = 'NO';
	        }else{
	        	var return_or_not = 'YES';
	        }

	        var drivers = '';
			$("input[name='add_driver']:checked").each(function (i) {
	            drivers = $(this).val();
	        });
	        if (drivers == 'on') {
	        	if (destinations.length == 0 || $("#add_city").val() == '') {
					audio_error.play();
					$('#loading').hide();
					openErrorGritter('Error!','Masukkan Kota dan Tujuan.');
					return false;
	        	}
	        	var add_driver = 'YES';
	        }else{
	        	var add_driver = 'NO';
	        }

			if (purpose_category == '' || purpose == '' || purpose_detail == '' || detail_city == '' || employees.length == 0) {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Masukkan Semua Data.');
				return false;
			}

			var data = {
				date:date,
				purpose_category:purpose_category,
				purpose:purpose,
				purpose_detail:purpose_detail,
				time_departure:time_departure,
				time_arrived:time_arrived,
				detail_city:detail_city,
				return_or_not:return_or_not,
				add_driver:add_driver,
				destinations:destinations,
				employees:employees,
				city:city,
			}
			
			$.post('{{ url("input/human_resource/leave_request") }}', data, function(result, status, xhr){
				if(result.status){
					clearAll();

					$("#create_modal").modal('hide');
					fillList();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Sukses Membuat Pengajuan.');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
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

	var kata_confirm = 'Apakah Anda Yakin?';

	function fillList(){
		$("#loading").show();
		$.get('{{ url("fetch/human_resource/leave_request") }}', function(result, status, xhr){
			if(result.status){
				$('#tableLeave').DataTable().clear();
				$('#tableLeave').DataTable().destroy();
				$('#bodyTableLeave').html("");
				var tableData = "";
				$('#tableLeaveComplete').DataTable().clear();
				$('#tableLeaveComplete').DataTable().destroy();
				$('#bodyTableLeaveComplete').html("");
				var tableDataComplete = "";
				$.each(result.leave_request, function(key, value) {
					tableData += '<tr>';
					if (value.remark == 'Requested') {
						var remarkss = '<span class="label label-primary" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Partially Approved') {
						var remarkss = '<span class="label label-warning" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Fully Approved') {
						var remarkss = '<span class="label label-success" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Rejected') {
						var remarkss = '<span class="label label-danger" style="margin-right:4px;">'+value.remark+'</span>';
					}
					tableData += '<td><span style="font-weight:bold;color:red;">'+ value.request_id +'</span><br>'+remarkss+'<br>'+value.date+'</td>';
					// tableData += '<td>'+ value.date +'</td>';
					// tableData += '<td>';
					// if (value.remark == 'Requested') {
					// 	tableData += '<span class="label label-primary">'+value.remark+'</span>';
					// }else if (value.remark == 'Partially Approved') {
					// 	tableData += '<span class="label label-warning">'+value.remark+'</span>';
					// }else if (value.remark == 'Fully Approved') {
					// 	tableData += '<span class="label label-success">'+value.remark+'</span>';
					// }else if (value.remark == 'Rejected') {
					// 	tableData += '<span class="label label-danger">'+value.remark+'</span>';
					// }
					// tableData += '</td>';
					tableData += '<td>'+ (value.department_shortname || '') +'</td>';
					tableData += '<td>'+ value.purpose_category +'<br>'+value.purpose+'</td>';
					tableData += '<td>'+ value.purpose_detail +'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.time_departure +'</td>';
					tableData += '<td style="text-align:right;padding-right:4px;">'+ value.time_arrived +'</td>';
					if (value.return_or_not == 'YES') {
						tableData += '<td>KEMBALI</td>';
					}else{
						tableData += '<td>TIDAK KEMBALI</td>';
					}
					var empss = '';
					for(var i = 0; i < result.leave_details[key].length;i++){
						empss += result.leave_details[key][i].name+'<br>';
					}
					tableData += '<td>'+empss+'</td>';
					// tableData += '<td>';
					var last_approval = '';
					var approval_remark = [];
					var approval_remarks = [];
					for(var i = 0; i < result.leave_approvals.length;i++){
						if (result.leave_approvals[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals[i].length;j++){
								approval_remark.push(result.leave_approvals[i][j].remark);
								approval_remarks.push({remark:result.leave_approvals[i][j].remark,keutamaan:result.leave_approvals[i][j].keutamaan});
							}
						}
					}
					var clinic = '';

					for(var i = 0; i < result.leave_approvals.length;i++){
						if (result.leave_approvals[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals[i].length;j++){
								if (result.leave_approvals[i][j].status == 'Approved') {
									if (result.leave_approvals[i][j].remark == 'Clinic') {
										clinic = '<br>Clinic : '+result.leave_approvals[i][j].approver_name+'<br>';
									}
								}
							}
						}
					}
					if (approval_remark.indexOf("Applicant") != -1) {
						for(var i = 0; i < result.leave_approvals.length;i++){
							if (result.leave_approvals[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals[i].length;j++){
									if (result.leave_approvals[i][j].status == 'Approved') {
										if (result.leave_approvals[i][j].remark == 'Applicant') {
											tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+''+clinic+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == 'Rejected'){
										if (result.leave_approvals[i][j].remark == 'Applicant') {
											tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+''+clinic+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == null){
										if (result.leave_approvals[i][j].remark == 'Applicant') {
											if (result.leave_approvals[i][j].keutamaan == 'belum') {
												tableData += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting'+clinic+'</td>';
											}
										}
									}
								}
							}
						}
					}else{
						tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					// if (approval_remark.indexOf("Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals.length;i++){
					// 		if (result.leave_approvals[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals[i].length;j++){
					// 				if (result.leave_approvals[i][j].status == 'Approved') {
					// 					if (result.leave_approvals[i][j].remark == 'Manager') {
					// 						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals[i][j].remark == 'Manager') {
					// 						tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == null){
					// 					if (result.leave_approvals[i][j].remark == 'Manager') {
					// 						if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/Manager">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("Deputy General Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals.length;i++){
					// 		if (result.leave_approvals[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals[i].length;j++){
					// 				if (result.leave_approvals[i][j].status == 'Approved') {
					// 					if (result.leave_approvals[i][j].remark == 'Deputy General Manager') {
					// 						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals[i][j].remark == 'Deputy General Manager') {
					// 						tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == null){
					// 					if (result.leave_approvals[i][j].remark == 'Deputy General Manager') {
					// 						if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" onclick="return confirm(\''+kata_confirm+'\')" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/Deputy General Manager">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("General Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals.length;i++){
					// 		if (result.leave_approvals[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals[i].length;j++){
					// 				if (result.leave_approvals[i][j].status == 'Approved') {
					// 					if (result.leave_approvals[i][j].remark == 'General Manager') {
					// 						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals[i][j].remark == 'General Manager') {
					// 						tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == null){
					// 					if (result.leave_approvals[i][j].remark == 'General Manager') {
					// 						if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/General Manager">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("Director") != -1) {
					// 	for(var i = 0; i < result.leave_approvals.length;i++){
					// 		if (result.leave_approvals[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals[i].length;j++){
					// 				if (result.leave_approvals[i][j].status == 'Approved') {
					// 					if (result.leave_approvals[i][j].remark == 'Director') {
					// 						tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals[i][j].remark == 'Director') {
					// 						tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals[i][j].status == null){
					// 					if (result.leave_approvals[i][j].remark == 'Director') {
					// 						if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/Director">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals[i][j].keutamaan == 'belum') {
					// 								tableData += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					for(var i = 0; i < result.leave_approvals.length;i++){
						if (result.leave_approvals[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals[i].length;j++){
								if (result.leave_approvals[i][j].status == 'Approved') {
									if (result.leave_approvals[i][j].remark != 'HR' && result.leave_approvals[i][j].remark != 'GA' && result.leave_approvals[i][j].remark != 'Applicant' && result.leave_approvals[i][j].remark != 'Security' && result.leave_approvals[i][j].remark != 'Clinic') {
										tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].remark+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
									}
								}else if(result.leave_approvals[i][j].status == 'Rejected'){
									if (result.leave_approvals[i][j].remark != 'HR' && result.leave_approvals[i][j].remark != 'GA' && result.leave_approvals[i][j].remark != 'Applicant' && result.leave_approvals[i][j].remark != 'Security') {
										tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].remark+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
									}
								}else if(result.leave_approvals[i][j].status == null){
									if (result.leave_approvals[i][j].remark != 'HR' && result.leave_approvals[i][j].remark != 'GA' && result.leave_approvals[i][j].remark != 'Applicant' && result.leave_approvals[i][j].remark != 'Security') {
										if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
											if (result.leave_approvals[i][j].keutamaan == 'belum') {
												tableData += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												var url = "{{ url('approval/human_resource/leave_request/') }}";
												tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/'+result.leave_approvals[i][j].remark+'">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].remark+'<br>Waiting</a></td>';
											}
										}else{
											if (result.leave_approvals[i][j].keutamaan == 'belum') {
												tableData += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].remark+'<br>Waiting</td>';
											}
										}
									}
								}
							}
						}
					}

					if (approval_remark.indexOf("HR") != -1) {
						for(var i = 0; i < result.leave_approvals.length;i++){
							if (result.leave_approvals[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals[i].length;j++){
									if (result.leave_approvals[i][j].status == 'Approved') {
										if (result.leave_approvals[i][j].remark == 'HR') {
											tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == 'Rejected'){
										if (result.leave_approvals[i][j].remark == 'HR') {
											tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == null){
										if (result.leave_approvals[i][j].remark == 'HR') {
											if (('{{isset($user->department)}}' && '{{$user->department}}'== 'Human Resources Department') || '{{$role_code}}'.match(/MIS/gi)) {
												if (result.leave_approvals[i][j].keutamaan == 'belum') {
													tableData += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													var url = "{{ url('approval/human_resource/leave_request/') }}";
													tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/HR">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
												}
											}else{
												if (result.leave_approvals[i][j].keutamaan == 'belum') {
													tableData += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
						}
					}else{
						tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					if (approval_remark.indexOf("GA") != -1) {
						for(var i = 0; i < result.leave_approvals.length;i++){
							if (result.leave_approvals[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals[i].length;j++){
									if (result.leave_approvals[i][j].status == 'Approved') {
										if (result.leave_approvals[i][j].remark == 'GA') {
											tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == 'Rejected'){
										if (result.leave_approvals[i][j].remark == 'GA') {
											tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == null){
										if (result.leave_approvals[i][j].remark == 'GA') {
											if (('{{ISSET($user->department)}}' && '{{$user->department}}' == 'General Affairs Department')  || '{{$role_code}}'.match(/MIS/gi)) {
												if (result.leave_approvals[i][j].keutamaan == 'belum') {
													tableData += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													var url = "{{ url('approval/human_resource/leave_request/') }}";
													tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals[i][j].request_id+'/GA">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
												}
											}else{
												if (result.leave_approvals[i][j].keutamaan == 'belum') {
													tableData += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
						}
					}else{
						tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					if (approval_remark.indexOf("Security") != -1) {
						for(var i = 0; i < result.leave_approvals.length;i++){
							if (result.leave_approvals[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals[i].length;j++){
									if (result.leave_approvals[i][j].status == 'Approved') {
										if (result.leave_approvals[i][j].remark == 'Security') {
											tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == 'Rejected'){
										if (result.leave_approvals[i][j].remark == 'Security') {
											tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals[i][j].status == null){
										if (result.leave_approvals[i][j].remark == 'Security') {
											if (result.leave_approvals[i][j].keutamaan == 'belum') {
												tableData += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">Waiting</td>';
											}
										}
									}
								}
							}
						}
					}else{
						tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}
					for(var i = 0; i < result.leave_approvals.length;i++){
						if (result.leave_approvals[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals[i].length;j++){
								// if (result.leave_approvals[i][j].status != null) {
								// 	last_approval = result.leave_approvals[i][j].remark;
								// }
								if (result.leave_approvals[i][j].keutamaan == 'utama') {
									last_approval = result.leave_approvals[i][j].remark;
								}
							}
						}
					}
					// tableData += '</td>';

					tableData += '<td>';
					tableData += '<button style="margin-right:2px" class="btn btn-xs btn-info" onclick="detailInformation(\''+value.request_id+'\')">Detail</button>';
					if (value.position == 'applicant') {
						tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="editRequest(\''+value.request_id+'\');">Edit</a>';
						// tableData += '<a style="" href="" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.request_id+'\',\''+value.operator_id+'\')">Delete</a>';
					}
					if (value.position != 'closed') {
						tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-danger"  onclick="cancelRequest(\''+value.request_id+'\');">Cancel</a>';
					}
					if (value.position == 'security' || value.position == 'closed') {
						
					}else{
						if (last_approval != '') {
							tableData += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-primary"  onclick="resendEmail(\''+value.request_id+'\',\''+last_approval+'\');">Resend Email</a>';
						}
					}
					tableData += '</td>';
					tableData += '</tr>';
				});
				$('#bodyTableLeave').append(tableData);

				var table = $('#tableLeave').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$.each(result.leave_request_complete, function(key, value) {
					tableDataComplete += '<tr>';
					if (value.remark == 'Requested') {
						var remarkss = '<span class="label label-primary" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Partially Approved') {
						var remarkss = '<span class="label label-warning" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Fully Approved') {
						var remarkss = '<span class="label label-success" style="margin-right:4px;">'+value.remark+'</span>';
					}else if (value.remark == 'Rejected') {
						var remarkss = '<span class="label label-danger" style="margin-right:4px;">'+value.remark+'</span>';
					}
					tableDataComplete += '<td><span style="font-weight:bold;color:red;">'+ value.request_id +'</span><br>'+remarkss+'<br>'+value.date+'</td>';
					// tableDataComplete += '<td>'+ value.date +'</td>';
					// tableDataComplete += '<td>';
					// if (value.remark == 'Requested') {
					// 	tableDataComplete += '<span class="label label-primary">'+value.remark+'</span>';
					// }else if (value.remark == 'Partially Approved') {
					// 	tableDataComplete += '<span class="label label-warning">'+value.remark+'</span>';
					// }else if (value.remark == 'Fully Approved') {
					// 	tableDataComplete += '<span class="label label-success">'+value.remark+'</span>';
					// }else if (value.remark == 'Rejected') {
					// 	tableDataComplete += '<span class="label label-danger">'+value.remark+'</span>';
					// }
					// tableDataComplete += '</td>';
					tableDataComplete += '<td>'+ (value.department_shortname || '') +'</td>';
					tableDataComplete += '<td>'+ value.purpose_category +'<br>'+value.purpose+'</td>';
					tableDataComplete += '<td>'+ value.purpose_detail +'</td>';
					tableDataComplete += '<td style="text-align:right;padding-right:4px;">'+ value.time_departure +'</td>';
					tableDataComplete += '<td style="text-align:right;padding-right:4px;">'+ value.time_arrived +'</td>';
					if (value.return_or_not == 'YES') {
						tableDataComplete += '<td>KEMBALI</td>';
					}else{
						tableDataComplete += '<td>TIDAK KEMBALI</td>';
					}
					var empss = '';
					for(var i = 0; i < result.leave_details_complete[key].length;i++){
						empss += result.leave_details_complete[key][i].name+'<br>';
					}
					tableDataComplete += '<td>'+empss+'</td>';
					// tableDataComplete += '<td>';
					var last_approval = '';
					var approval_remark = [];
					for(var i = 0; i < result.leave_approvals_complete.length;i++){
						if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
								approval_remark.push(result.leave_approvals_complete[i][j].remark);
							}
						}
					}

					var clinic = '';

					for(var i = 0; i < result.leave_approvals_complete.length;i++){
						if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
								if (result.leave_approvals_complete[i][j].status == 'Approved') {
									if (result.leave_approvals_complete[i][j].remark == 'Clinic') {
										clinic = '<br>Clinic : '+result.leave_approvals_complete[i][j].approver_name+'<br>';
									}
								}
							}
						}
					}

					if (approval_remark.indexOf("Applicant") != -1) {
						for(var i = 0; i < result.leave_approvals_complete.length;i++){
							if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
									if (result.leave_approvals_complete[i][j].status == 'Approved') {
										if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
											tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+''+clinic+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
										if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
											tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+''+clinic+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == null){
										if (result.leave_approvals_complete[i][j].remark == 'Applicant') {
											if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
												tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting'+clinic+'</td>';
											}
										}
									}
								}
							}
						}
					}else{
						tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					// if (approval_remark.indexOf("Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals_complete.length;i++){
					// 		if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
					// 				if (result.leave_approvals_complete[i][j].status == 'Approved') {
					// 					if (result.leave_approvals_complete[i][j].remark == 'Manager') {
					// 						tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Manager') {
					// 						tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == null){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Manager') {
					// 						if ('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id) {
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/Manager">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("Deputy General Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals_complete.length;i++){
					// 		if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
					// 				if (result.leave_approvals_complete[i][j].status == 'Approved') {
					// 					if (result.leave_approvals_complete[i][j].remark == 'Deputy General Manager') {
					// 						tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Deputy General Manager') {
					// 						tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == null){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Deputy General Manager') {
					// 						if ('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id) {
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/Manager">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("General Manager") != -1) {
					// 	for(var i = 0; i < result.leave_approvals_complete.length;i++){
					// 		if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
					// 				if (result.leave_approvals_complete[i][j].status == 'Approved') {
					// 					if (result.leave_approvals_complete[i][j].remark == 'General Manager') {
					// 						tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals_complete[i][j].remark == 'General Manager') {
					// 						tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == null){
					// 					if (result.leave_approvals_complete[i][j].remark == 'General Manager') {
					// 						if ('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id) {
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" onclick="return confirm(\''+kata_confirm+'\')" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/Manager">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					// if (approval_remark.indexOf("Director") != -1) {
					// 	for(var i = 0; i < result.leave_approvals_complete.length;i++){
					// 		if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
					// 			for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
					// 				if (result.leave_approvals_complete[i][j].status == 'Approved') {
					// 					if (result.leave_approvals_complete[i][j].remark == 'Director') {
					// 						tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Director') {
					// 						tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
					// 					}
					// 				}else if(result.leave_approvals_complete[i][j].status == null){
					// 					if (result.leave_approvals_complete[i][j].remark == 'Director') {
					// 						if ('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id) {
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								var url = "{{ url('approval/human_resource/leave_request/') }}";
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" onclick="return confirm(\''+kata_confirm+'\')" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/Director">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
					// 							}
					// 						}else{
					// 							if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
					// 								tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
					// 							}else{
					// 								tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
					// 							}
					// 						}
					// 					}
					// 				}
					// 			}
					// 		}
					// 	}
					// }else{
					// 	tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					// }

					for(var i = 0; i < result.leave_approvals_complete.length;i++){
						if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
								if (result.leave_approvals_complete[i][j].status == 'Approved') {
									if (result.leave_approvals_complete[i][j].remark != 'HR' && result.leave_approvals_complete[i][j].remark != 'GA' && result.leave_approvals_complete[i][j].remark != 'Applicant' && result.leave_approvals_complete[i][j].remark != 'Security' && result.leave_approvals_complete[i][j].remark != 'Clinic') {
										tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].remark+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
									}
								}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
									if (result.leave_approvals_complete[i][j].remark != 'HR' && result.leave_approvals_complete[i][j].remark != 'GA' && result.leave_approvals_complete[i][j].remark != 'Applicant' && result.leave_approvals_complete[i][j].remark != 'Security') {
										tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].remark+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
									}
								}else if(result.leave_approvals_complete[i][j].status == null){
									if (result.leave_approvals_complete[i][j].remark != 'HR' && result.leave_approvals_complete[i][j].remark != 'GA' && result.leave_approvals_complete[i][j].remark != 'Applicant' && result.leave_approvals_complete[i][j].remark != 'Security') {
										if (('{{isset($user->employee_id)}}' && '{{$user->employee_id}}' == result.leave_approvals_complete[i][j].approver_id) || '{{$role_code}}'.match(/MIS/gi)) {
											if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
												tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												var url = "{{ url('approval/human_resource/leave_request/') }}";
												tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" onclick="return confirm(\''+kata_confirm+'\')" target="_blank" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/'+result.leave_approvals_complete[i][j].remark+'">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].remark+'<br>Waiting</a></td>';
											}
										}else{
											if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
												tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].remark+'<br>Waiting</td>';
											}
										}
									}
								}
							}
						}
					}

					if (approval_remark.indexOf("HR") != -1) {
						for(var i = 0; i < result.leave_approvals_complete.length;i++){
							if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
									if (result.leave_approvals_complete[i][j].status == 'Approved') {
										if (result.leave_approvals_complete[i][j].remark == 'HR') {
											tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
										if (result.leave_approvals_complete[i][j].remark == 'HR') {
											tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == null){
										if (result.leave_approvals_complete[i][j].remark == 'HR') {
											if ('{{isset($user->department)}}' && '{{$user->department}}' == 'Human Resources Department') {
												if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
													tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													var url = "{{ url('approval/human_resource/leave_request/') }}";
													tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" onclick="return confirm(\''+kata_confirm+'\')" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/HR">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
												}
											}else{
												if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
													tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
						}
					}else{
						tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					if (approval_remark.indexOf("GA") != -1) {
						for(var i = 0; i < result.leave_approvals_complete.length;i++){
							if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
									if (result.leave_approvals_complete[i][j].status == 'Approved') {
										if (result.leave_approvals_complete[i][j].remark == 'GA') {
											tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
										if (result.leave_approvals_complete[i][j].remark == 'GA') {
											tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == null){
										if (result.leave_approvals_complete[i][j].remark == 'GA') {
											if ('{{isset($user->department)}}' && '{{isset($user->department)}}' == 'General Affairs Department') {
												if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
													tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													var url = "{{ url('approval/human_resource/leave_request/') }}";
													tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;" target="_blank" onclick="return confirm(\''+kata_confirm+'\')" href="'+url+'/'+result.leave_approvals_complete[i][j].request_id+'/GA">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
												}
											}else{
												if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
													tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
												}else{
													tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
												}
											}
										}
									}
								}
							}
						}
					}else{
						tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}

					if (approval_remark.indexOf("Security") != -1) {
						for(var i = 0; i < result.leave_approvals_complete.length;i++){
							if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
								for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
									if (result.leave_approvals_complete[i][j].status == 'Approved') {
										if (result.leave_approvals_complete[i][j].remark == 'Security') {
											tableDataComplete += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == 'Rejected'){
										if (result.leave_approvals_complete[i][j].remark == 'Security') {
											tableDataComplete += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;cursor:pointer;">'+result.leave_approvals_complete[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals_complete[i][j].approved_ats+'</td>';
										}
									}else if(result.leave_approvals_complete[i][j].status == null){
										if (result.leave_approvals_complete[i][j].remark == 'Security') {
											if (result.leave_approvals_complete[i][j].keutamaan == 'belum') {
												tableDataComplete += '<td style="font-weight:bold;font-size:11px"></td>';
											}else{
												tableDataComplete += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;cursor:pointer;">Waiting</td>';
											}
										}
									}
								}
							}
						}
					}else{
						tableDataComplete += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
					}
					for(var i = 0; i < result.leave_approvals_complete.length;i++){
						if (result.leave_approvals_complete[i][0].request_id == value.request_id) {
							for(var j = 0; j < result.leave_approvals_complete[i].length;j++){
								if (result.leave_approvals_complete[i][j].status != null) {
									last_approval = result.leave_approvals_complete[i][j].remark;
								}
							}
						}
					}
					// tableDataComplete += '</td>';

					tableDataComplete += '<td>';
					tableDataComplete += '<button style="margin-right:2px" class="btn btn-xs btn-info" onclick="detailInformation(\''+value.request_id+'\')">Detail</button>';
					// if (value.position == 'applicant') {
					// 	tableDataComplete += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#edit-modal" onclick="editRequest(\''+value.request_id+'\');">Edit</a>';
					// 	// tableDataComplete += '<a style="" href="" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#myModal" onclick="deleteConfirmation(\''+value.request_id+'\',\''+value.operator_id+'\')">Delete</a>';
					// }
					// if (value.position != 'closed') {
					// 	tableDataComplete += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-danger"  onclick="cancelRequest(\''+value.request_id+'\');">Cancel</a>';
					// }
					// if (value.position == 'security' || value.position == 'closed') {
						
					// }else{
					// 	if (last_approval != '') {
					// 		tableDataComplete += '<a style="margin-right:2px" type="button" class="btn btn-xs btn-primary"  onclick="resendEmail(\''+value.request_id+'\',\''+last_approval+'\');">Resend Email</a>';
					// 	}
					// }
					tableDataComplete += '</td>';
					tableDataComplete += '</tr>';
				});
				$('#bodyTableLeaveComplete').append(tableDataComplete);

				var table = $('#tableLeaveComplete').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$("#loading").hide();
			}
			else{
				$("#loading").hide();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	

	function deleteConfirmation(name,id) {
		var url	= '{{ url("index/welding/destroy_operator") }}';
		// jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
		jQuery('#modalDeleteButton').attr("href", url+'/'+id+'/'+name);
	}

	function detailInformation(request_id) {
		$('#loading').show();
		var data = {
			request_id:request_id
		}
		$.get('{{ url("fetch/human_resource/leave_request/detail") }}', data,function(result, status, xhr){
			if(result.status){
				$('#request_id').html(result.leave_request.request_id);
				$('#purpose_category').html(result.leave_request.purpose_category+' - '+result.leave_request.purpose);
				$('#purpose_detail').html(result.leave_request.purpose_detail);
				$('#time_departure').html(result.leave_request.time_departure);
				$('#time_arrived').html(result.leave_request.time_arrived);
				$('#city_detail').html(result.leave_request.detail_city);
				$('#return_or_not').html(result.leave_request.return_or_not);
				$('#add_driver_or_not').html(result.leave_request.add_driver);

				$('#reason').html('');

				if (result.leave_request.remark == 'Rejected') {
					$('#reason').html('Permintaan Anda ditolak oleh Manager dengan alasan:<br>'+result.leave_request.reason);
				}

				$('#bodyEmp').html('');
				var bodyEmp = '';

				for(var i = 0; i < result.detail_emp.length;i++){
					bodyEmp += '<tr>';
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.detail_emp[i].employee_id+'</td>';
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.detail_emp[i].name+'</td>';														
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+(result.detail_emp[i].department_shortname || '')+'</td>';
					bodyEmp += '</tr>';
				}

				$('#bodyEmp').append(bodyEmp);

				if (result.leave_request.add_driver == 'YES') {
					$('#bodyDestinationDetail').html('');
					var bodyDestination = '';

					var index = 1;

					for(var i = 0; i < result.destinations.length;i++){
						bodyDestination += '<tr>';
						bodyDestination += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;">'+index+'</td>';
						bodyDestination += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;">'+result.destinations[i].remark+'</td>';	
						bodyDestination += '</tr>';
						index++;
					}

					$('#bodyDestinationDetail').append(bodyDestination);
					$('#tableDestinationDetail').show();
				}else{
					$('#tableDestinationDetail').hide();
				}

				if (result.leave_request.purpose == 'SAKIT') {
					$('#tableClinicDetail').show();
					$('#diagnose').html(result.leave_request.diagnose);
					$('#action').html(result.leave_request.action);
					$('#suggestion').html(result.leave_request.suggestion);
				}else{
					$('#tableClinicDetail').hide();
				}

				$('#headApproval').html('');
				var headApproval = '';

				headApproval += '<tr>';
				headApproval += '<td colspan="'+result.approval_progress.length+'" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Proses Persetujuan</td>';
				headApproval += '</tr>';

				$('#headApproval').append(headApproval);

				$('#bodyApproval').html('');
				var bodyApproval = '';

				bodyApproval += '<tr>';
				for(var i = 0; i < result.approval_progress.length;i++){
					if (result.approval_progress[i].status == 'Approved') {
						var statuses = 'Approved<br>'+result.approval_progress[i].approved_date;
						var color = "color:#1b9427";
					}else if (result.approval_progress[i].status == 'Rejected') {
						var statuses = 'Rejected<br>'+result.approval_progress[i].approved_date;
						var color = "color:#fa3939";
					}else{
						var statuses = '';
					}
					bodyApproval += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;padding-top:35px;padding-bottom:35px;'+color+'">'+statuses+'</td>';
				}
				bodyApproval += '</tr>';

				bodyApproval += '<tr>';
				for(var i = 0; i < result.approval_progress.length;i++){
					if (result.approval_progress[i].approver_name != null) {
						bodyApproval += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;">'+result.approval_progress[i].approver_name.split(' ').slice(0,2).join(' ')+'</td>';
					}else{
						bodyApproval += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;"></td>';
					}
				}
				bodyApproval += '</tr>';

				bodyApproval += '<tr>';				
				for(var i = 0; i < result.approval_progress.length;i++){
					bodyApproval += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;">'+result.approval_progress[i].remark+'</td>';
				}
				bodyApproval += '</tr>';

				$('#bodyApproval').append(bodyApproval);

				$('#loading').hide();
				$('#detailModal').modal('show');
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error',result.message);
			}
		});
	}

	function editRequest(request_id) {
		$('#loading').show();
		var data = {
			request_id:request_id
		}
		$.get('{{ url("fetch/human_resource/leave_request/detail") }}', data,function(result, status, xhr){
			if(result.status){
				$('#edit_request_id').val(result.leave_request.request_id);
				$('#edit_purpose_category').val(result.leave_request.purpose_category).trigger('change');
				$('#edit_purpose_detail').val(result.leave_request.purpose).trigger('change');
				$('#edit_detail').val(result.leave_request.purpose_detail);
				$('#edit_detail_city').val(result.leave_request.detail_city).trigger('change');

				var time_departure = new Date(result.leave_request.time_departure);
				var date = time_departure.toLocaleDateString();

				var hour = addZero(time_departure.getHours());
				var minute = addZero(time_departure.getMinutes());
				
				$('#edit_time_departure').val(hour+':'+minute);

				var time_arrived = new Date(result.leave_request.time_arrived);
				var date = time_arrived.toLocaleDateString();

				var hour = addZero(time_arrived.getHours());
				var minute = addZero(time_arrived.getMinutes());

				$('#edit_time_arrived').val(hour+':'+minute);

				$('#edit_date').val(result.leave_request.date);

				if (result.leave_request.return_or_not == 'NO') {
					$('#edit_return_or_not')[0].checked = true;
				}else{
					$('#edit_return_or_not')[0].checked = false;
				}

				var tableEmployee = "";
				$('#tableEmployeeBody').html('');
				$('#tableEmployeeBodyEdit').html('');

				employees = [];
				count = 0;

				for(var i = 0; i < result.detail_emp.length;i++){
					tableEmployee += "<tr id='"+result.detail_emp[i].employee_id+"'>";
					tableEmployee += "<td>"+result.detail_emp[i].employee_id+"</td>";
					tableEmployee += "<td>"+result.detail_emp[i].name+"</td>";
					tableEmployee += "<td><a href='javascript:void(0)' onclick='remEmployeeEdit(id)' id='"+result.detail_emp[i].employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
					tableEmployee += "</tr>";

					employees.push(result.detail_emp[i].employee_id);
					count += 1;
				}

				$('#countTotalEdit').text(count);
				$('#tableEmployeeBodyEdit').append(tableEmployee);
				var tableDestination = "";
				$('#tableDestinationBodyEdit').html('');
				destinations = [];
				countDestination = 0;
				$('#edit_city').val('');

				if (result.leave_request.add_driver == 'YES') {
					$('#edit_driver')[0].checked = true;
					$('#divCityEdit').show();
					$('#divDestinationEdit').show();
					$('#tableDestinationEdit').show();

					tableDestination = "";
					$('#tableDestinationBodyEdit').html('');
					destinations = [];
					countDestination = 0;

					for(var i = 0;i < result.destinations.length; i++){

						tableDestination += "<tr id='destination_"+countDestination+"'>";
						tableDestination += "<td>"+result.destinations[i].remark+"</td>";
						tableDestination += "<td><a href='javascript:void(0)' onclick='remDestination(id)' id='destination_"+countDestination+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
						tableDestination += "</tr>";

						destinations.push(result.destinations[i].remark);
						countDestination += 1;
					}

					$('#tableDestinationBodyEdit').append(tableDestination);

					$('#edit_city').val(result.driver.destination_city);

					$('#edit_employees').val('').trigger('change');
					$('#edit_destination').val('');


				}else{
					destinations = [];
					countDestination = 0;
					$('#edit_driver')[0].checked = false;
					$('#divCityEdit').hide();
					$('#divDestinationEdit').hide();
					$('#tableDestinationEdit').hide();
				}

				$('#loading').hide();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error',result.message);
			}
		});
	}

	function resendEmail(request_id,remark) {
		if (confirm('Apakah Anda yakin akan mengirim ulang Email?')) {
			$('#loading').show();
			$.get('{{ url("resend/human_resource/leave_request/") }}/'+request_id+'/'+remark,  function(result, status, xhr){
				if(result.status){
					clearAll();
					fillList();
					$('#loading').hide();
					audio_ok.play();
					openSuccessGritter('Success','Success Resend Email');
				} else {
					fillList();
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function cancelRequest(request_id) {
		if (confirm('Apakah Anda yakin akan membatalkan pengajuan?')) {
			$('#loading').show();
			var data = {
				request_id:request_id
			}
			$.get('{{ url("delete/human_resource/leave_request/") }}', data, function(result, status, xhr){
				if(result.status){
					clearAll();
					fillList();
					audio_ok.play();
					$('#loading').hide();
					openSuccessGritter('Success','Success Cancel Request');
				} else {
					fillList();
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function updateRequest() {
		if (confirm('Apakah Anda yakin akan mengubah data?')) {
			$('#loading').show();
			var request_id = $('#edit_request_id').val();
			var date = $('#edit_date').val();
			var purpose_category = $('#edit_purpose_category').val();
			var purpose = $('#edit_purpose_detail').val();
			var purpose_detail = $('#edit_detail').val();
			var time_departure = $('#edit_time_departure').val();
			var time_arrived = $('#edit_time_arrived').val();
			var detail_city = $('#edit_detail_city').val();
			var city = $('#edit_city').val();
			var returns = '';
			$("input[name='edit_return_or_not']:checked").each(function (i) {
	            returns = $(this).val();
	        });
	        if (returns == 'on') {
	        	var return_or_not = 'NO';
	        }else{
	        	var return_or_not = 'YES';
	        }

	        var drivers = '';
			$("input[name='edit_driver']:checked").each(function (i) {
	            drivers = $(this).val();
	        });
	        if (drivers == 'on') {
	        	var add_driver = 'YES';
	        	if (destinations.length == 0 || $('#edit_city').val() == '') {
	        		$('#loading').hide();
	        		openErrorGritter('Error!','Fill City and Destination.');
	        		audio_error.play();
	        		return false;
	        	}
	        }else{
	        	var add_driver = 'NO';
	        }

			if (purpose_category == '' || purpose == '' || detail_city == '' || purpose_detail == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				return false;
			}

			var data = {
				request_id:request_id,
				date:date,
				purpose_category:purpose_category,
				purpose:purpose,
				purpose_detail:purpose_detail,
				time_departure:time_departure,
				time_arrived:time_arrived,
				return_or_not:return_or_not,
				add_driver:add_driver,
				detail_city:detail_city,
				destinations:destinations,
				employees:employees,
				city:city,
			}
			
			$.post('{{ url("update/human_resource/leave_request") }}', data, function(result, status, xhr){
				if(result.status){
					clearAll();

					$("#edit-modal").modal('hide');
					fillList();
					audio_ok.play();
					$('#loading').hide();
					openSuccessGritter('Success','Success Update Request');
				} else {
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
				}
			})
		}
	}

	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection