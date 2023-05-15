@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<link href="{{ url("bower_components/bootstrap-daterangepicker/daterangepicker.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
		font-weight: bold;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
	.blink {
		animation-duration: 1s;
		animation-name: blink;
		animation-iteration-count: infinite;
		animation-direction: alternate;
		animation-timing-function: ease-in-out;
	}
	@keyframes blink {
		50% {
			opacity: 1;
		}
		100% {
			opacity: 0;
		}
	}

	.gambar {
		width: 100%;
		background-color: none;
		border-radius: 5px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.containers {
		display: inline-block;
		position: relative;
		padding-left: 50px;
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
		margin-left: 15px;
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
	.onoffswitch2 {
		position: relative; width: 70px;
		-webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
	}

	.onoffswitch2-checkbox {
		display: none;
	}

	.onoffswitch2-label {
		display: block; overflow: hidden; cursor: pointer;
		border: 2px solid #999999; border-radius: 5px;
	}

	.onoffswitch2-inner {
		display: block; width: 200%; margin-left: -100%;
		-moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
		-o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
	}

	.onoffswitch2-inner:before, .onoffswitch2-inner:after {
		display: block; float: left; width: 50%; height: 40px; line-height: 40px;
		font-size: 12px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
		-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
	}

	.onoffswitch2-inner:before {
		content: "Order Makan Ramadhan";
		padding-right: 10px;
		font-size: 20px;
		text-align: center;
		background-color: #2196F3; color: #FFFFFF;
	}

	.onoffswitch2-inner:after {
		content: "Order Extra Food";
		background-color: #00a65a;; color: #FFFFFF;
		font-size: 20px;
		text-align: center;
	}

	.onoffswitch2-switch {
		display: block; width: 18px; margin: 0px;
		background: #FFFFFF;
		border: 2px solid #999999; border-radius: 5px;
		position: absolute; top: 0; bottom: 0; right: 55px;
		-moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
		-o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s; 
		background-image: -moz-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
		background-image: -webkit-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
		background-image: -o-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
		background-image: linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
	}

	.onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-inner {
		margin-left: 0;
	}

	.onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-switch {
		right: 0px; 
	}

	.switch {
		position: relative;
		display: inline-block;
		width: 60px;
		height: 34px;
	}

	.switch input { 
		opacity: 0;
		width: 0;
		height: 0;
	}
	
	.dataTables_info {
		color: black;
	}
	

/* Rounded sliders */
.slider.round {
	border-radius: 34px;
}

.slider.round:before {
	border-radius: 50%;
}


</style>
@stop
@section('header')

@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>
	<div class="row" style="margin-bottom: 1%;">

		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon bg-olive" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker2" id="tanggal_from" name="tanggal_from" placeholder="Pilih Tanggal" autocomplete="off" value="{{date('Y-m-d')}}">
			</div>
		</div>
		<div class="col-xs-2">
			<button id="search" onClick="fetchChart()" class="btn bg-olive">Search</button>
		</div>
		<div class="col-xs-6 pull-right" >

			<?php 
			if (isset($userss->position)) {
				if ((str_contains(strtolower($userss->position), 'operator'))) {
				} else {
					echo '<a data-toggle="modal" onclick="createModal()" class="btn btn-success btn-md pull-right" style="margin-left: 5px; "><i class="fa fa-upload"></i> Buat Pengajuan Makan</a>';
				}
			}
			?>
			@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'GA' || $role_user->role_code == 'S-GA')
			<a data-toggle="modal" onclick="modalResume()" class="btn btn-warning btn-md pull-right" style="margin-left: 5px; "><i class="fa fa-list"></i> Resume</a>
			<button class="btn btn-info pull-right" style="margin-left: 5px;" onclick="modalReports()" ><i class="fa fa-file-excel-o"></i> Report</button>
			<a class="btn btn-md pull-right" href="{{ url('index/order/overtime/attendance') }}" style="color:white;background-color: #a16eac;"><i class="fa fa-users"></i>&nbsp;&nbsp;Order Food Attendance</a>
			@endif
		</div>

	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-6">
					<center><span style="color: white; font-weight: bold; font-size: 3vw;">TOTAL MAKAN RAMADHAN</span></center>
				</div> -->
				<div class="col-xs-6">
					<center><span style="color: white; font-weight: bold; font-size: 3vw;">TOTAL MAKAN OVERTIME</span></center>
				</div>
				<div class="col-xs-6">
					<center><span style="color: white; font-weight: bold; font-size: 3vw;">TOTAL EXTRA FOOD</span></center>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-6 col-xs-12" style="padding:2px;padding-top:0px">
					<div class="col-xs-4" style="margin-left: 10px; padding: 2px; width:22%;">
					</div>
					<div class="col-xs-4" style="margin-left: 15px; padding: 2px; width:48%;">
						small box
						<div class="small-box bg-green" style="font-size: 20px;font-weight: bold;height: 153px; cursor: pointer;" onclick="ShowModalAll('Shift 1 Order Makan Ramadhan')">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>SHIFT 1</b></h3>
								<h2 style="margin: 0px;font-size: 5vw;" id='total_shift1_food'>0<sup style="font-size: 2vw"> </sup></h2>
							</div>
							<div class="icon" style="padding-top: 35px;">
								<i class="ion ion-spoon"></i>
								<i class="ion ion-fork"></i>
							</div>
						</div>
					</div>
					
				</div> -->
				<div class="col-xs-6 col-xs-12" style="padding:2px;padding-top:0px">
					<div class="col-xs-4" style="margin-left: 10px; padding: 2px; width:22%;">
					</div>
					<div class="col-xs-4" style="margin-left: 15px; padding: 2px; width:48%;">
						<!-- small box -->
						<div class="small-box bg-green" style="font-size: 20px;font-weight: bold;height: 153px; cursor: pointer;" onclick="showModalTable('Shift 1 Order Makan Overtime')">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>SHIFT 1</b></h3>
								<h2 style="margin: 0px;font-size: 5vw;" id='total_shift1_food_overtime'>0<sup style="font-size: 2vw"> </sup></h2>
							</div>
							<div class="icon" style="padding-top: 35px;">
								<i class="ion ion-spoon"></i>
								<i class="ion ion-fork"></i>
							</div>
						</div>
					</div>
					
				</div>

				<div class="col-xs-6 col-xs-12"  style="padding:2px;padding-top:0px"> 

					<div class="col-xs-6" id="shift1_btn" style="margin-left: 2px; padding: 2px;"  hidden>
						<!-- small box -->
						<div class="small-box" style="background: #ff9800; font-size: 20px;font-weight: bold;height: 153px;cursor: pointer;" onclick="showModalTable('Shift 1 Order Extra Food Overtime')">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw; color: white;"><b>SHIFT 1</b></h3>
								<h2 style="margin: 0px;font-size: 5vw; color: white;" id='total_shift1_ext_food'>0<sup style="font-size: 2vw"> </sup></h2>
							</div>
							<div class="icon" style="padding-top: 35px;">
								<i class="ion ion-coffee"></i>
							</div>
						</div>
					</div>

					<div class="col-xs-6" id="shift2_btn" style="margin-left: 2px; padding: 2px;">
						<!-- small box -->
						<div class="small-box" style="background: #ff9800; font-size: 20px;font-weight: bold;height: 153px;cursor: pointer;" onclick="showModalTable('Shift 2 Order Extra Food Overtime')">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw; color: white;"><b>SHIFT 2</b></h3>
								<h2 style="margin: 0px;font-size: 5vw; color: white;" id='total_shift2_ext_food'>0<sup style="font-size: 2vw"> </sup></h2>
							</div>
							<div class="icon" style="padding-top: 35px;">
								<i class="ion ion-coffee"></i>
							</div>
						</div>
					</div>
					<div class="col-xs-6" id="shift3_btn" style="margin-left: 2px; padding: 2px;">
						<!-- small box -->
						<div class="small-box" style="background: #ff9800; font-size: 20px;font-weight: bold;height: 153px;cursor: pointer;" onclick="showModalTable('Shift 3 Order Extra Food Overtime')">
							<div class="inner" style="padding-bottom: 0px;">
								<h3 style="margin-bottom: 0px;font-size: 2vw; color: white;"><b>SHIFT 3</b></h3>
								<h2 style="margin: 0px;font-size: 5vw; color: white;" id='total_shift3_ext_food'>0<sup style="font-size: 2vw"> </sup></h2>
							</div>
							<div class="icon" style="padding-top: 35px;">
								<i class="ion ion-coffee"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xs-12" style="padding-top: 20px;">
		<div class="row"  id="divTable">
		</div>
	</div>
</div>

<input type="hidden" id="purpose_code">

<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
	<div class="modal-dialog modal-md" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
			</div>
			<div class="modal-body">
				<div class="row" id="divTable2">
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>
	</div>
</div> 
</section>

<div class="modal modal-default fade" id="create_modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">ORDER EXTRA FOOD</h1>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<form class="form-horizontal">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<center>
									<div class="form-group" style="width:40%;">
										<input type='checkbox' name='onoffswitch2' onchange="modalChange()" class='onoffswitch2-checkbox' id='myonoffswitch' checked><label class='onoffswitch2-label' for='myonoffswitch'><span class='onoffswitch2-inner'></span></label>
									</div>
								</center>
								<div id="makan_ramadhan" hidden>
									<div class="form-group">
										<label class="col-sm-2">Diajukan Oleh<span class="text-red">*</span></label>
										<div class="col-sm-4" align="left">
											<input type="text" class="form-control" id="request_by" placeholder="Requested" required  readonly>
										</div>
									</div>


									<div class="form-group">
										<label class="col-sm-2">Shift<span class="text-red">*</span>:</label>
										<div class="col-sm-5">
											<input class="form-check-input" type="radio" name="shift" id="shift" value="Shift 1" checked>
											<label class="form-check-label" for="exampleRadios1">
												Shift 1
											</label>

										</div>
									</div>

									<div class="form-group">
										<label  class="col-sm-2">Tanggal<span class="text-red">*</span> :</label>
										<div class="col-sm-4">
											<input type="text" class="form-control datepicker" id="RequestDate" placeholder="Select Date">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2">Upload Karyawan [NIK]<span class="text-red">*</span>:</label>
										<div class="col-sm-4" align="left" id="selectEmp">
											<textarea id="upload" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
										</div>
										<div class="col-xs-3 row">
											<button class="btn btn-success" onclick="uploadForecast1('ramadhan')">
												<i class="fa fa-plus"></i> Tambahkan
											</button>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2">Total Operator<span class="text-red">*</span>:</label>
										<div class="col-sm-3" align="left">
											<input type="text" class="form-control" id="tot_operator" placeholder="Total Operator" required  readonly>
										</div>
									</div>

									<table class="table table-hover table-bordered table-striped" id="tableEmployee">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 1%; text-align: center;">ID</th>
												<th style="width: 6%; text-align: center;">Name</th>
												<th style="width: 1%; text-align: center;">Shift</th>
												<th style="width: 1%; text-align: center;">Action</th>
											</tr>
										</thead>
										<tbody id="tableEmployeeBody">
										</tbody>

									</table>

								</div>

								<div id="makan_overtime" hidden>
									<div class="form-group">
										<label class="col-sm-2">Diajukan Oleh<span class="text-red">*</span></label>
										<div class="col-sm-4" align="left">
											<input type="text" class="form-control" id="request_by_overtime" placeholder="Requested" required  readonly>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2">Shift<span class="text-red">*</span>:</label>
										<div class="col-sm-5">
											<input class="form-check-input" type="radio" name="shift_overtime" id="shift_overtime" value="Shift 2" checked>
											<label class="form-check-label" for="exampleRadios1">
												Shift 2
											</label>
											&nbsp;
										</div>
									</div>

									<div class="form-group">
										<label  class="col-sm-2">Tanggal<span class="text-red">*</span> :</label>
										<div class="col-sm-4">
											<input type="text" class="form-control datepicker" id="RequestDateOvertime" placeholder="Select Date">
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2">Upload Karyawan [NIK]<span class="text-red">*</span>:</label>
										<div class="col-sm-4" align="left" id="selectEmpOvertime">
											<textarea id="uploadOvertime" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
										</div>
										<div class="col-xs-3 row">
											<button class="btn btn-success" onclick="uploadForecast1('overtime')">
												<i class="fa fa-plus"></i> Tambahkan
											</button>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2">Total Operator<span class="text-red">*</span>:</label>
										<div class="col-sm-3" align="left">
											<input type="text" class="form-control" id="tot_operator_overtime" placeholder="Total Operator" required  readonly>
										</div>
									</div>

									<table class="table table-hover table-bordered table-striped" id="tableEmployeeOvertime">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 1%; text-align: center;">ID</th>
												<th style="width: 6%; text-align: center;">Name</th>
												<th style="width: 1%; text-align: center;">Shift</th>
												<th style="width: 1%; text-align: center;">Action</th>
											</tr>
										</thead>
										<tbody id="tableEmployeeBodyOvertime">
										</tbody>

									</table>

								</div>

								<div class="form-group" id="divCity" style="display: none">
									<label class="col-sm-2">Kota Tujuan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="add_city" placeholder="Masukkan Kota Tujuan" required value="">
									</div>
								</div>
								<div class="form-group" id="divDestination" style="display: none">
									<label class="col-sm-2">Detail Tujuan<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="add_destination" placeholder="Masukkan Detail Tujuan" required value="">
									</div>
									<div class="col-xs-3">
										<button class="btn btn-success" onclick="addDestination1()">
											<i class="fa fa-plus"></i> Tambahkan
										</button>
									</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="tableDestination" style="display: none">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 5%;">Tujuan</th>
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
					</form>
				</div>
			</div>

			<div class="modal-footer">
				<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
				<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="createRequest1()">Save</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="modalReport">
	<div class="modal-dialog modal-lg" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleReport">Order Food Report</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom" placeholder="Date From">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto"  placeholder="Date To">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-4">
							<div class="form-group pull-right">
								<button id="close" onClick="$('#modalReport').modal('hide')" class="btn btn-danger">Close</button>
								<button id="search" onClick="fetchReport()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>
					<div id="divTableReport">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- 
		<div class="modal modal-default fade" id="modalReport1">
			<div class="modal-dialog modal-lg" >
				<div class="modal-content">
					<div class="modal-header">
						<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleReport">Order Food Report</h3>
						</center>
						<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
							<div class="col-md-12 col-md-offset-3">
								<div class="col-md-3">
									<div class="form-group">
										<label>Date From</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom" placeholder="Date From">
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Date To</label>
										<div class="input-group date">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto"  placeholder="Date To">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-md-offset-3">
								<div class="col-md-4">
									<div class="form-group pull-right">
										<button id="close" onClick="$('#modalReport').modal('hide')" class="btn btn-danger">Close</button>
										<button id="search" onClick="fetchReport()" class="btn btn-primary">Search</button>
									</div>
								</div>
							</div>
							<table class="table table-hover table-bordered table-striped" id="tableReport">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">No.</th>
										<th style="width: 1%;">ID</th>
										<th style="width: 5%;">Name</th>
										<th style="width: 5%;">Section</th>
										<th style="width: 5%;">Shift</th>
										<th style="width: 5%;">Jam</th>
										<th style="width: 1%;">Makan</th>
										<th style="width: 5%;">Extra Food</th>
										<th style="width: 1%;">Status</th>
									</tr>
								</thead>
								<tbody id="bodyTableReport">

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	-->
	<div class="modal fade" id="myModalinfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Informasi Jam Pengajuan Makan</h4>
				</div>
				<div class="modal-body">
					<b>
					Pengajuan Makan Ramadhan Pukul 07:00 - 15:00</b><br>
				</div>
				<div class="modal-footer">
					<input type="hidden" style="width: 100%" class="form-control" id="category">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalResume">
		<div class="modal-dialog modal-lg" style="width: 90%;">
			<div class="modal-content">
				<div class="modal-header">
					<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: black;">Resume Report</h3>
					</center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label" style="text-align: right;">Periode<span class="text-red"> :</span></label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="resumeDate" name="resumeDate" placeholder="Select Date">
								<span class="help-block" id="checkDate2"></span>
							</div>
							<div class="col-sm-3">
								<button onclick="fetchResume()" class="btn btn-success">Search</button>
							</div>
						</div>
						<div class="col-xs-12">
							<table class="table table-hover table-bordered table-striped" id="tableResume">
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	@endsection
	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/jquery.numpad.js")}}"></script>
	<script src="{{ url("bower_components/moment/min/moment.min.js")}}"></script>
	<script src="{{ url("bower_components/bootstrap-daterangepicker/daterangepicker.js")}}"></script>
	<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
	<script src="{{ url("js/bootstrap-toggle.min.js")}}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		role_users = [];
		list_order = [];

		var employees = [];
		var employees2 = [];

		var shift = [];
		var extra_food = [];
		var countDestination = 0;
		var countDestination2 = 0;

		var countTotal = [];
		var countTotal2 = [];

		var nik = [];
		var nik2 = [];


		sta = 'Order Extra Food';

		jQuery(document).ready(function() {
			$('#tag').val('');
			$('.select2').prop('selectedIndex', 0).change();
			$('.select2').select2();
			fetchChart();
			clearAll();
			$('#modalPurpose').modal({
				backdrop: 'static',
				keyboard: false
			});

			$('#resumeDate').datepicker({
				autoclose: true,
				format: "yyyy-mm",
				startView: "months", 
				minViewMode: "months",
			});
			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,
			});

			$('.datepicker2').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,
			});

			$("#datepickerfrom").datepicker({
				todayBtn:  1,
				autoclose: true,
			}).on('changeDate', function (selected) {
				var minDate = new Date(selected.date.valueOf());
				$('#datepickerto').datepicker('setStartDate', minDate);
			});

			$("#datepickerto").datepicker()
			.on('changeDate', function (selected) {
				var maxDate = new Date(selected.date.valueOf());
				$('#datepickerfrom').datepicker('setEndDate', maxDate);
			});

			$('#toggle-two').bootstrapToggle({
				on: 'Enabled',
				off: 'Disabled'
			});




			$('.timepicker').timepicker({
				showMeridian: false,
				minuteStep: 5
			});
			$('#tanggal_hid').val('');


		});

		function modalResume(){
			$('#modalResume').modal('show');
		}

		function modalChange(){
			if($('input[id="myonoffswitch"]').is(':checked'))
			{
				$('#makan_overtime').hide();
				openErrorGritter('Error', 'Menu Order Makan Ramadhan Masih Belum Dibuka');
				$('#makan_ramadhan').hide();
				sta = 'Order Makan Ramadhan';
			}else{
				$('#makan_ramadhan').hide();
				$('#makan_overtime').hide();
				openErrorGritter('Error', 'Menu Order Extra Food Masih Belum Dibuka');
				sta = 'Order Extra Food';
			}
		}

		function fetchResume(){
			var date = $('#resumeDate').val();
			var data = {
				resume:date
			}
			$.get('{{ url("fetch/food/order_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableResume').html("");
					var tableResume = "";
					var calendars = result.calendars;

					tableResume += '<thead>';
					tableResume += '<tr>';
					tableResume += '<th style="width: 1%; font-size:0.8vw;">ID</th>';
					tableResume += '<th style="width: 5%; font-size:0.8vw;">Name</th>';

					var res = [];


					$.each(result.foods, function(key, value){
						if($.inArray(value.employee_id+'_'+value.employee_name, res) === -1){
							res.push(value.employee_id+'_'+value.employee_name);
						}
					});


					$.each(result.calendars, function(key, value){
						if(value.remark == 'H'){
							tableResume += '<th style="width: 0.1%; font-size:0.8vw; background-color: rgba(80,80,80,0.5)">'+value.header+'</th>';
						}
						else{
							tableResume += '<th style="width: 0.1%; font-size:0.8vw;">'+value.header+'</th>';						
						}
					});

					tableResume += '<th style="width: 1%; font-size:0.8vw;">Qty</th>';
					tableResume += '</tr>';
					tableResume += '</thead>';
					tableResume += '<tbody>';
					var insert = false;
					var cnt = 0;


					for (var i = 0; i < res.length; i++) {
						var str = res[i].split('_');
						tableResume += '<tr>';
						tableResume += '<td>'+str[0]+'</td>';
						tableResume += '<td>'+str[1]+'</td>';
						for (var j = 0; j < calendars.length; j++) {
							insert = false;

							$.each(result.foods, function(key, value){
								if(value.time_in == calendars[j].week_date && str[0] == value.employee_id){
									tableResume += '<td style="text-align: center; background-color: #ccff90;">'+value.qty+'</td>';
									insert = true;
									cnt += value.qty;
								}
							});

							if(insert == false){
								if(calendars[j].remark == 'H'){
									tableResume += '<td style="text-align: center; background-color: rgba(80,80,80,0.5);"></td>';
								}
								else{
									tableResume += '<td style="text-align: center; background-color: #ff6090;"></td>';								
								}
							}
						}
						tableResume += '<td style="text-align: center;">'+cnt+'</td>';
						tableResume += '</tr>';
						cnt = 0;
					}

					tableResume += '</tbody>';

					$('#tableResume').append(tableResume);
					$('#tableResume').DataTable().clear();
					$('#tableResume').DataTable().destroy();
					$('#tableResume').html('');
					$('#tableResume').append(tableResume);

					$('#tableResume').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
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
							}
							]
						},
						'paging': false,
						'lengthChange': true,
						'searching': true,
						'ordering': false,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				}
				else{
					openErrorGritter(result.message);
					audio_error.play();
					return false;				
				}
			});
		}

		function createModal() {
			var date_now = '{{date("H")}}';
			var emp_nows = '{!! auth()->user()->role_code !!}';

			if (date_now >= "01" && date_now <= "17") {
				if (emp_nows != 'Operator Outsource'||emp_nows != 'Operator' ||emp_nows != 'Operator Contract') {

					$('#create_modal').modal('show');
					$('#RequestDate').val('');
					$('#tot_operator').val('');

				}else{
					openErrorGritter('Error', 'Tidak bisa Membuat Pengajuan');
				}
			}else{
				$('#myModalinfo').modal('show');
			}
		}

		function modalReports() {
			fetchReport();
		}

		function fetchReport() {
			initiateReportTable();

			$('#loading').show();
			var bodyDetail = "";

			$('#tableReport4').DataTable().clear();
			$('#tableReport4').DataTable().destroy();
			$("#bodyTableReport4").html("");


			var datefrom = $('#datefrom').val();
			var dateto = $('#dateto').val();
			var st_makan = "";
			var st_food = "";
			var waktus = "";


			var data = {
				datefrom:datefrom,
				dateto:dateto
			}
			$.get('{{ url("report/overtime/food") }}',data, function(result, status, xhr){
				if(result.status){
					var index = 1;
					$.each(result.datas, function(key, value){

						bodyDetail += '<tr style="background-color:'+color+'">';
						bodyDetail += '<td>'+index+'</td>';
						bodyDetail += '<td>'+value.time_in+'</td>';
						bodyDetail += '<td>'+value.employee_id+'</td>';
						bodyDetail += '<td>'+value.name+'</td>';
						bodyDetail += '<td>'+(value.section || "")+'</td>';
						if (value.shiftdaily_code != null) {
							bodyDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';
						}else{

							bodyDetail += '<td>'+value.shift+'</td>';
						}
						bodyDetail += '<td>'+value.remark+'</td>';
						if (value.status != null) {
							bodyDetail += '<td>Tambahan</td>';
						}else{
							bodyDetail += '<td>-</td>';
						}

						if (value.remark == "Order Makan Overtime" || value.remark == "Order Extra Food") {
							if (value.attend_date == null) {
								
								var color = '#ffc7c7';
								bodyDetail += '<td style="background-color:orange;">Belum Hadir</td>';
								bodyDetail += '<td>-</td>';


							}else{
								
								var color = '#c7ffde';
								bodyDetail += '<td style="background-color:#32a860;">Hadir</td>';
								bodyDetail += '<td>'+value.attend_date+'</td>';


							}

						}else{
							var color = '#ffc7c7';
							
							bodyDetail += '<td>-</td>';
							bodyDetail += '<td>-</td>';
						}
						


						bodyDetail += '</tr>';
						index++;
					});
					$('#bodyTableReport4').append(bodyDetail);

					$('#tableReport4 tfoot th').each( function () {
						var title = $(this).text();
						$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
					} );
					var table = $('#tableReport4').DataTable({
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

							],
						},

						initComplete: function() {
							this.api()
							.columns([5,6,7,8])
							.every(function(dd) {
								var column = this;
								var theadname = $("#tableReport4 th").eq([dd])
								.text();
								var select = $(
									'<select><option value="" style="font-size:11px;">All</option></select>'
									)
								.appendTo($(column.footer()).empty())
								.on('change', function() {
									var val = $.fn.dataTable.util
									.escapeRegex($(this)
										.val());

									column.search(val ? '^' + val + '$' :
										'', true,
										false)
									.draw();
								});
								column
								.data()
								.unique()
								.sort()
								.each(function(d, j) {
									var vals = d;
									if ($("#tableReport4 th").eq([dd])
										.text() ==
										'Category') {
										vals = d.split(' ')[0];
								}
								select.append(
									'<option style="font-size:12px;"  value="' +
									d + '">' + vals + '</option>');
							});
							});
						},
						'paging': true,
						'lengthChange': true,
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
					$('#tableReport4').show();


					table.columns().every( function () {
						var that = this;
						$( '#search', this.footer() ).on( 'keyup change', function () {
							if ( that.search() !== this.value ) {
								that
								.search( this.value )
								.draw();
							}
						} );
					} );

					$('#tableReport4 tfoot tr').appendTo('#tableReport4 thead');
					$('#loading').hide();
				}else{
					$('#loading').hide();
					$('#modalReport').modal('hide');
					openErrorGritter('Error!',result.message);
					audio_error.play();
					return false;
				}
			});
$('#modalReport').modal('show');


}


function uploadForecast1(st) {

	if(st == 'ramadhan'){
		var upload = $('#upload').val();
		var request_date = $('#RequestDate').val();
		var shift = $('input[id="shift"]:checked').val();

		if (request_date == "") {
			audio_error.play();
			openErrorGritter('Error!','Pilih Tanggal Pengajuan');
			return false;

		}

		if (upload == "") {
			audio_error.play();
			openErrorGritter('Error!','Data Upload Karyawan Masih Kosong');
			return false;
		}

		var data = {
			upload : upload,
			request_date:request_date,
			shift:shift,
			sts: 'ramadhan'
		}

		$('#loading').show();
		$.post('{{ url("upload/overtime/eat") }}', data, function(result, status, xhr){
			if(result.status){

				var tableDestination = "";
				var kata = "";

				$('#upload').val('');
				$('#uploadModal').modal('hide');
				$('#loading').hide();

				for (var i = 0; i < result.getData.length; i++) {
					if($.inArray(result.names[i][0], nik) != -1){
						audio_error.play();
						openErrorGritter('Error!','Karyawan sudah ada di list.');
						return false;
					}

					tableDestination += "<tr id='"+countDestination+"'>";
					tableDestination += "<td class='emp'>"+result.names[i][0]+"</td>";  
					tableDestination += "<td >"+result.names[i][1]+"</td>";
					tableDestination += "<td class='shift'>"+result.names[i][2]+"</td>";
					tableDestination += "<td><a href='javascript:void(0)' onclick='remDestination(id,\""+result.names[i][0]+"\",\"ramadhan\")' id='"+countDestination+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
					tableDestination += "</tr>";

					employees.push({
						'employee' :result.names[i][0]
					});

					nik.push(result.names[i][0]);

					kata = countDestination;

					countTotal.push({
						'numbers1' :kata
					});

					countDestination += 1;
				}

				$('#tot_operator').val(countDestination);

				$('#tableEmployeeBody').append(tableDestination);


				if (result.nik_error.length > 0) {
					if (result.nik_error[0]) {}
						openErrorGritter('Error!','Error NIK:<br>'+result.nik_error.join(',')+' Tidak Terdaftar');

				}else if (result.nik_no_request.length > 0) {
					openErrorGritter('Error!','Error NIK:<br>'+result.nik_no_request.join(',')+' Request Khusus Wanita , jika Anda Non Muslim Hubungi GA');
				}

				else{
					openSuccessGritter('Success','Success Add Employee');
				}

			}else {
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});
	}else{
		var upload = $('#uploadOvertime').val();
		var request_date = $('#RequestDateOvertime').val();
		var shift = $('input[id="shift_overtime"]:checked').val();

		if (request_date == "") {
			audio_error.play();
			openErrorGritter('Error!','Pilih Tanggal Pengajuan');
			return false;

		}

		if (upload == "") {
			audio_error.play();
			openErrorGritter('Error!','Data Upload Karyawan Masih Kosong');
			return false;
		}

		var data = {
			upload : upload,
			request_date:request_date,
			shift:shift,
			sts : 'overtime'
		}

		$('#loading').show();
		$.post('{{ url("upload/overtime/eat") }}', data, function(result, status, xhr){
			if(result.status){

				var tableDestination2 = "";
				var kata = "";

				$('#upload').val('');
				$('#uploadModal').modal('hide');
				$('#loading').hide();

				for (var i = 0; i < result.getData.length; i++) {
					if($.inArray(result.names[i][0], nik2) != -1){
						audio_error.play();
						$('#uploadOvertime').val('');
						openErrorGritter('Error!','Karyawan sudah ada di list.');
						return false;
					}

					tableDestination2 += "<tr id='"+countDestination2+"'>";
					tableDestination2 += "<td class='emp_overtime'>"+result.names[i][0]+"</td>";  
					tableDestination2 += "<td >"+result.names[i][1]+"</td>";
					tableDestination2 += "<td class='shift_overtime'>"+result.names[i][2]+"</td>";

					tableDestination2 += "<td><a href='javascript:void(0)' onclick='remDestination(id,\""+result.names[i][0]+"\",\"overtime\")' id='"+countDestination2+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
					tableDestination2 += "</tr>";

					employees2.push({
						'employee' :result.names[i][0]
					});

					nik2.push(result.names[i][0]);

					kata = countDestination2;

					$('#uploadOvertime').val('');

					countTotal2.push({
						'numbers1' :kata
					});

					countDestination2 += 1;
				}

				$('#tot_operator_overtime').val(countDestination2);

				$('#tableEmployeeBodyOvertime').append(tableDestination2);


				if (result.nik_error.length > 0) {
					if (result.nik_error[0]) {}
						openErrorGritter('Error!','Error NIK:<br>'+result.nik_error.join(',')+' Tidak Terdaftar');

				}
				else{
					openSuccessGritter('Success','Success Add Employee');
				}

			}else {
				$('#loading').hide();
				openErrorGritter('Error', result.message);
			}
		});

	}
}

function remDestination(id,emps,st){

	if (st == "ramadhan") {
		nik.splice( $.inArray(emps), 1 );
		employees1 = employees.filter(emp => emp.employee.localeCompare(emps));

		var index = countTotal.findIndex(e => e.numbers1 == id);
		countTotal.splice(index, 1);

		$('#'+id).remove();
		countDestination -= 1;
		$('#tot_operator').val(countDestination);
	}else{
		employees2 = employees2.filter(emp => emp.employee.localeCompare(emps));

		var index = countTotal2.findIndex(e => e.numbers1 == id);
		countTotal2.splice(index, 1);

		$('#'+id).remove();
		countDestination2 -= 1;
		$('#tot_operator_overtime').val(countDestination2);

	}

}

function createRequest1() {

	var emps = [];
	var shifts = [];

	var date = "";

	if (sta == "Order Extra Food") {

		$('.emp_overtime').each(function(){
			emps.push($(this).html());
		});

		$('.shift_overtime').each(function(){
			shifts.push($(this).html());
		});

		date = $('#RequestDateOvertime').val();

	}else{
		sta = "Order Makan Ramadhan";
		$('.emp').each(function(){
			emps.push($(this).html());
		});

		$('.shift').each(function(){
			shifts.push($(this).html());
		});
		date = $('#RequestDate').val();

	}


	if (date == "" && emps.length == 0) {
		audio_error.play();
		openErrorGritter('Error!','Pilih Tanggal Pengajuan & Upload Data Karyawan Masih Kosong');
		return false;

	}else if (date == "") {
		audio_error.play();
		openErrorGritter('Error!','Pilih Tanggal Pengajuan');
		return false;
	}else if (emps.length == 0){
		audio_error.play();
		openErrorGritter('Error!','Upload Data Karyawan Masih Kosong');
		return false;
	}

	var data = {
		employees:emps,
		shift:shifts,
		dates:date,
		sta:sta
	}

	$.post('{{ url("create/order/puasa") }}', data, function(result, status, xhr){
		if(result.status){
			clearAll();
			$("#create_modal").modal('hide');
			$('#loading').hide();
			openSuccessGritter('Success','Sukses Membuat Pengajuan.');
			audio_ok.play();
			fetchChart();

		} else {
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!',result.message);
		}
	})
}


var detail = [];

function fetchChart(){
	var date = $('#tanggal_from').val();
	var d = "";
	if (date == null) {
		d = new Date();
	}else{
		d = new Date(date);
	}

	var locale = 'en-US';

	if (d.toLocaleString(locale, { weekday: 'long'}) == "Saturday" || d.toLocaleString(locale, { weekday: 'long'}) == "Sunday") {
		$('#shift1_btn').show();
		document.getElementById('shift1_btn').style.width = '32%';
		document.getElementById('shift2_btn').style.width = '32%';
		document.getElementById('shift3_btn').style.width = '32%';

	}else{
		$('#shift1_btn').hide();
		document.getElementById('shift2_btn').style.width = '48%';
		document.getElementById('shift3_btn').style.width = '48%';
	}

	var data = {
		date:date
	}
	$.get('{{ url("fetch/report/overtime_food") }}',data,function(result, status, xhr){
		if(result.status){

			list_order = [];
			role_users = [];

			$.each(result.datas, function(key2, value2){
				list_order.push({
					id:value2.id,
					ot_from:value2.ot_from,
					ot_to:value2.ot_to,
					date:value2.time_in,
					employee_id:value2.employee_id,
					nama:value2.name,
					section:value2.section,
					shift:value2.shift,
					attend_date:value2.attend_date,
					remark:value2.remark,
					created_by:value2.created_by,
					shiftdaily_code:value2.shiftdaily_code
				});
			});

			role_users.push(result.role_user.role_code);
			initiateTable();

		}
	});

}


function initiateTable() {
	$('#divTable').html("");
	var tableData = "";
	tableData += "<table id='tableAttendance' class='table table-bordered table-striped table-hover'>";
	tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
	tableData += '<tr>';
	tableData += '<th style="color:white;width:1%">Date</th>';
	tableData += '<th style="color:white;width:1%">Time</th>';
	tableData += '<th style="color:white;width:1%">Employee ID</th>';
	tableData += '<th style="color:white;width:4%">Name</th>';
	tableData += '	<th style="color:white;width:5%">Section</th>';
	tableData += '<th style="color:white;width:1%">Shift</th>';
	tableData += '<th style="color:white;width:2%">Remark</th>';
	tableData += '<th style="color:white;width:1%">Kehadiran</th>';
	tableData += '</tr>';
	tableData += '</thead>';
	tableData += '<tbody id="bodyTableAttendance">';
	tableData += "</tbody>";
	tableData += "<tfoot>";
	tableData += "<tr>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "</tr>";
	tableData += "</tfoot>";
	tableData += "</table>";
	$('#divTable').append(tableData);
	fetchListOrder();

}

function showModalTable(st) {
	$('#divTable2').html("");
	var tableData = "";
	tableData += "<div class='col-md-12'>";
	tableData += "<table id='tableDetail' class='table table-bordered table-striped table-hover'>";
	tableData += '<thead>';
	tableData += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
	tableData += '<th style="width:1%">Tanggal</th>';
	tableData += '<th style="width:2%">ID</th>';
	tableData += '<th style="width:4%">Nama</th>';
	tableData += '<th style="width:4%">Section</th>';
	tableData += '<th style="width:1%">Shift</th>';
	tableData += '<th style="width:1%">Action</th>';
	tableData += '</tr>';
	tableData += '</thead>';
	tableData += '<tbody id="bodyTableDetail">';
	tableData += "</tbody>";
	tableData += "<tfoot>";
	tableData += "<tr>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "</tr>";
	tableData += "</tfoot>";
	tableData += "</table>";
	tableData += "</div>";
	$('#divTable2').append(tableData);
	ShowModalAll(st);

}


function initiateReportTable() {
	$('#divTableReport').html("");
	var tableData = "";
	tableData += "<table id='tableReport4' class='table table-bordered table-striped table-hover'>";
	tableData += '<thead style="background-color: rgba(126,86,134,.7);">';
	tableData += '<tr>';
	tableData += '<th style="color:white;width:1%">No</th>';
	tableData += '<th style="color:white;width:1%">Date</th>';
	tableData += '<th style="color:white;width:1%">ID</th>';
	tableData += '<th style="color:white;width:4%">Name</th>';
	tableData += '	<th style="color:white;width:5%">Section</th>';
	tableData += '<th style="color:white;width:1%">Shift</th>';
	tableData += '<th style="color:white;width:2%">Remark</th>';
	tableData += '<th style="color:white;width:1%">Status</th>';
	tableData += '<th style="color:white;width:1%">Kehadiran</th>';
	tableData += '<th style="color:white;width:2%">Waktu</th>';
	tableData += '</tr>';
	tableData += '</thead>';
	tableData += '<tbody id="bodyTableReport4">';
	tableData += "</tbody>";
	tableData += "<tfoot>";
	tableData += "<tr>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "<th></th>";
	tableData += "</tr>";
	tableData += "</tfoot>";
	tableData += "</table>";
	$('#divTableReport').append(tableData);

}

function fetchListOrder() {

	$('#tableAttendance').DataTable().clear();
	$('#tableAttendance').DataTable().destroy();
	var tableDetail = '';
	$('#bodyTableAttendance').html('');
	var count_active1 = 0;
	var count_active2 = 0;
	var count_active3 = 0;
	var count_active1_ov = 0;
	var count_active1_ov_food = 0;



	var total_shift1_food = 0;
	var total_shift1_food_overtime = 0;
	var total_shift2_ext_food = 0;
	var total_shift3_ext_food = 0;
	var total_shift1_ext_food_ov = 0;
	var st = "";


	$.each(list_order, function(key, value){

		tableDetail += '<tr style="border: 2px solid #fff;border-bottom: 2px solid white; font-weight: bold; background-color: #ffd8b7;color: black;font-size: 20px;width: 33%;" >';
		tableDetail += '<td>'+value.date+'</td>';
		if (value.ot_from != null) {
			st = value.ot_from+'-'+value.ot_to;
		}else{
			st = "-";
		}
		tableDetail += '<td>'+st+'</td>';
		tableDetail += '<td>'+value.employee_id+'</td>';
		tableDetail += '<td>'+value.nama+'</td>';
		tableDetail += '<td>'+value.section+'</td>';
		if (value.shiftdaily_code != null) {
			tableDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';

		}else{

			tableDetail += '<td>'+value.shift+'</td>';
		}
		tableDetail += '<td>'+value.remark+'</td>';
		if (value.remark == "Order Makan Overtime" || value.remark == "Order Extra Food") {
			if (value.attend_date == null) {
				tableDetail += '<td><a class="btn btn-primary btn-xs" data-toggle="tooltip" style="width: 100px">Belum Hadir</a></td>';
			}else{
				tableDetail += '<td><a class="btn btn-success btn-xs" data-toggle="tooltip" style="width: 100px">Hadir</a></td>';
			}
		}else{
			tableDetail += '<td>-</td>';			
		}
		
		tableDetail += '</tr>';

		// if(value.remark == "Order Makan Ramadhan" && value.shift == 'Shift 1'){
		// 	count_active1 += 1;
		// }

		if(value.remark == "Order Makan Overtime" && value.shift == 'Shift 1'){
			count_active1_ov_food += 1;
		}


		if(value.remark == "Order Extra Food" && value.shift == 'Shift 2'){
			count_active2 += 1;
		}

		if(value.remark == "Order Extra Food" && value.shift == 'Shift 3'){
			count_active3 += 1;
		}
		if(value.remark == "Order Extra Food" && value.shift == 'Shift 1'){
			count_active1_ov += 1;
		}
	});

	total_shift1_food_overtime

	total_shift1_food_overtime = total_shift1_food_overtime + parseInt(count_active1_ov_food);

	$('#total_shift1_food_overtime').html(total_shift1_food_overtime+' <span style="font-size:2.4vw"></span>');


	// total_shift1_food = total_shift1_food + parseInt(count_active1);

	// $('#total_shift1_food').html(total_shift1_food+' <span style="font-size:2.4vw"></span>');

	var total_shift1_ext_food_ov = total_shift1_ext_food_ov + parseInt(count_active1_ov);
	$('#total_shift1_ext_food').html(total_shift1_ext_food_ov+' <span style="font-size:2.4vw"></span>');	


	var total_shift2_ext_food = total_shift2_ext_food + parseInt(count_active2);
	$('#total_shift2_ext_food').html(total_shift2_ext_food+' <span style="font-size:2.4vw"></span>');		

	total_shift3_ext_food = total_shift3_ext_food + parseInt(count_active3);
	$('#total_shift3_ext_food').html(total_shift3_ext_food+' <span style="font-size:2.4vw"></span>');					

	$('#bodyTableAttendance').append(tableDetail);

	$('#tableAttendance tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	} );

	var table = $('#tableAttendance').DataTable({
		"order": [],
		'dom': 'Bfrtip',
		'responsive': true,
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
				extend: 'excel',
				className: 'btn btn-info',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			},
			]
		},
		initComplete: function() {
			this.api()
			.columns([1,4,5,6])
			.every(function(dd) {
				var column = this;
				var theadname = $("#tableAttendance th").eq([dd])
				.text();
				var select = $(
					'<select><option value="" style="font-size:11px;">All</option></select>'
					)
				.appendTo($(column.footer()).empty())
				.on('change', function() {
					var val = $.fn.dataTable.util
					.escapeRegex($(this)
						.val());

					column.search(val ? '^' + val + '$' :
						'', true,
						false)
					.draw();
				});
				column
				.data()
				.unique()
				.sort()
				.each(function(d, j) {
					var vals = d;
					if ($("#tableAttendance th").eq([dd])
						.text() ==
						'Category') {
						vals = d.split(' ')[0];
				}
				select.append(
					'<option style="font-size:12px;"  value="' +
					d + '">' + vals + '</option>');
			});
			});
		},
	});

	table.columns().every( function () {
		var that = this;
		$( '#search', this.footer() ).on( 'keyup change', function () {
			if ( that.search() !== this.value ) {
				that
				.search( this.value )
				.draw();
			}
		} );
	} );

	$('#tableAttendance tfoot tr').appendTo('#tableAttendance thead');
}

function ShowModalAll(status) {	

	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#loading').show();
	$('#bodyTableDetail').html('');
	var tableDetail = '';

	var emp_now = '{!! auth()->user()->username !!}';
	var role_now = role_users[0];

	$.each(list_order, function(key, value){
		if (status == "Shift 2 Order Extra Food Overtime" && value.shift == "Shift 2" && value.remark == "Order Extra Food") {
			tableDetail += '<tr>';
			tableDetail += '<td>'+value.date+'</td>';
			tableDetail += '<td>'+value.employee_id+'</td>';
			tableDetail += '<td>'+value.nama+'</td>';
			tableDetail += '<td>'+value.section+'</td>';
			if (value.shiftdaily_code != null) {
				tableDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';

			}else{

				tableDetail += '<td>'+value.shift+'</td>';
			}
			if (value.created_by == emp_now && value.attend_date == null || role_now == 'S-MIS' && value.attend_date == null || emp_now == "PI1201002" && value.attend_date == null) {
				tableDetail += "<td><a href='javascript:void(0)' onclick='remOvertime(id)' id='"+value.id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			}else{
				tableDetail += '<td>-</td>';
			}

			tableDetail += '</tr>';

		}else if (status == "Shift 3 Order Extra Food Overtime" && value.shift == "Shift 3" && value.remark == "Order Extra Food"){
			tableDetail += '<tr>';
			tableDetail += '<td>'+value.date+'</td>';
			tableDetail += '<td>'+value.employee_id+'</td>';
			tableDetail += '<td>'+value.nama+'</td>';
			tableDetail += '<td>'+value.section+'</td>';

			if (value.shiftdaily_code != null) {
				tableDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';

			}else{

				tableDetail += '<td>'+value.shift+'</td>';
			}

			if (value.created_by == emp_now && value.attend_date == null || role_now == 'S-MIS' && value.attend_date == null || emp_now == "PI1201002" && value.attend_date == null) {
				tableDetail += "<td><a href='javascript:void(0)' onclick='remOvertime(id)' id='"+value.id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			}else{
				tableDetail += '<td>-</td>';
			}
			tableDetail += '</tr>';
		}else if (status == "Shift 1 Order Extra Food Overtime" && value.shift == "Shift 1" && value.remark == "Order Extra Food"){
			tableDetail += '<tr>';
			tableDetail += '<td>'+value.date+'</td>';
			tableDetail += '<td>'+value.employee_id+'</td>';
			tableDetail += '<td>'+value.nama+'</td>';
			tableDetail += '<td>'+value.section+'</td>';
			if (value.shiftdaily_code != null) {
				tableDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';

			}else{

				tableDetail += '<td>'+value.shift+'</td>';
			}
			if (value.created_by == emp_now && value.attend_date == null || role_now == 'S-MIS' && value.attend_date == null || emp_now == "PI1201002" && value.attend_date == null) {
				tableDetail += "<td><a href='javascript:void(0)' onclick='remOvertime(id)' id='"+value.id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			}else{
				tableDetail += '<td>-</td>';
			}
			tableDetail += '</tr>';
		}
		else if (status == "Shift 1 Order Makan Overtime" && value.shift == "Shift 1" && value.remark == "Order Makan Overtime"){
			tableDetail += '<tr>';
			tableDetail += '<td>'+value.date+'</td>';
			tableDetail += '<td>'+value.employee_id+'</td>';
			tableDetail += '<td>'+value.nama+'</td>';
			tableDetail += '<td>'+value.section+'</td>';
			if (value.shiftdaily_code != null) {
				tableDetail += '<td>'+value.shift+' | '+value.shiftdaily_code+' </td>';

			}else{

				tableDetail += '<td>'+value.shift+'</td>';
			}
			if (value.created_by == emp_now && value.attend_date == null || role_now == 'S-MIS' && value.attend_date == null || emp_now == "PI1201002" && value.attend_date == null) {
				tableDetail += "<td><a href='javascript:void(0)' onclick='remOvertime(id)' id='"+value.id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			}else{
				tableDetail += '<td>-</td>';
			}
			tableDetail += '</tr>';

		}
	});

	$('#bodyTableDetail').append(tableDetail);

	$('#tableDetail tfoot th').each( function () {
		var title = $(this).text();
		$(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
	} );


	var table = $('#tableDetail').DataTable({
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
				className: 'btn btn-success',
				text: '<i class="fa fa-file-excel-o"></i> Excel',
				exportOptions: {
					columns: ':not(.notexport)'
				}
			}

			]
		},
		initComplete: function() {
			this.api()
			.columns([3,4])
			.every(function(dd) {
				var column = this;
				var theadname = $("#tableDetail th").eq([dd])
				.text();
				var select = "";
				var select = $(
					'<select><option value="" style="font-size:11px;">All</option></select>'
					)
				.appendTo($(column.footer()).empty())
				.on('change', function() {
					var val = $.fn.dataTable.util
					.escapeRegex($(this)
						.val());

					column.search(val ? '^' + val + '$' :
						'', true,
						false)
					.draw();
				});
				column
				.data()
				.unique()
				.sort()
				.each(function(d, j) {
					var vals = d;
					if ($("#tableDetail th").eq([dd])
						.text() ==
						'Category') {
						vals = d.split(' ')[0];
				}
				select.append(
					'<option style="font-size:12px;"  value="' +
					d + '">' + vals + '</option>');
			});
			});
		},
		'paging': true,
		'lengthChange': true,
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


	table.columns().every( function () {
		var that = this;
		$( '#search', this.footer() ).on( 'keyup change', function () {
			if ( that.search() !== this.value ) {
				that
				.search( this.value )
				.draw();
			}
		} );
	} );

	$('#tableDetail tfoot tr').appendTo('#tableDetail thead');

	$('#judul_detail').html('Detail Operator '+status);
	$('#modalDetail').modal('show');
	$('#loading').hide();
}

function remOvertime(id){

	if(confirm("Are you sure you want to cancel?")){

		var data = {
			id:id
		}
		$("#loading").show();

		$.post('{{ url("delete/overtime/request") }}', data, function(result, status, xhr){
			if (result.status == true) {
				openSuccessGritter("Success","Data Berhasil Dihapus");
				$("#loading").hide();
				location.reload();
			}
			else{
				$("#loading").hide();
				openErrorGritter("Gagal","Data Gagal Dihapus");
			}
		});
	}
	else{
		openErrorGritter('Error!');
		return false;
	}
}

function clearAll() {
	$('#RequestDate').val('');
	$('#RequestDateOvertime').val('');
	$('#dateto').val('');
	$('#datefrom').val('');
	$('#resumeDate').val('');
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '3000'
	});
}
</script>
@endsection