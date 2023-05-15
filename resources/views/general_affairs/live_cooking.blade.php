@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.css")}}">
<link rel="stylesheet" href="{{ url("bower_components/fullcalendar/dist/fullcalendar.print.min.css")}}" media="print">
<style type="text/css">
	table > tr:hover {
		background-color: #7dfa8c;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>td{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		padding-left: 2px;
		padding-right: 2px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > td{
		font-size: 0.93vw;
		border:1px solid black;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
	}
	.blink_text {

		animation:1.2s blinker linear infinite;
		-webkit-animation:1.2s blinker linear infinite;
		-moz-animation:1.2s blinker linear infinite;

		color: yellow;
	}

	@-moz-keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@-webkit-keyframes blinker {
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}

	@keyframes blinker {  
		50% { opacity: 0.7; }
		100% { opacity: 1.0; }
	}
	#loading, #error { display: none; }
	.fc-event {
		font-size: 1vw;
		cursor: pointer;
	}

	.fc-event-time, .fc-event-title {
		padding: 0 1px;
		white-space: nowrap;
	}

	.fc-title {
		white-space: normal;
	}
	.fc-content {
	    cursor: pointer;
	}
	.content{
		padding-top: 0px;
		padding-left: 7px;
		padding-right: 7px;
		padding-bottom: 0px;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		
	</h1>
</section>
@endsection

@section('content')
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<!-- <div class="col-xs-12" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;padding-top: 0px">
		<span style="font-size: 20px;color: white;width: 100%;" id="periode">Live Cooking Periode {{$monthTitle}}</span>
	</div> -->
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-success">				
				<div class="box-header">
					<center>
						<span style="background-color: yellow; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Requested&nbsp;&nbsp;</span>
						<span style="background-color: #ccff90; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Confirmed&nbsp;&nbsp;</span>
						<span style="background-color: #ff6090; color: black; font-weight: bold; font-size: 1.1vw; border: 1px solid black;">&nbsp;&nbsp;Rejected&nbsp;&nbsp;</span>
					</center>
				</div>
				<div class="box-body">
					<div class="col-xs-12" style="padding-top: 0px;padding-bottom: 10px">
						<a href="{{url('home')}}" class="btn btn-danger btn-xs">
							<i class="fa fa-arrow-left"></i> Back
						</a>
						<?php if ($user == 'PI1201002' || str_contains($role, 'MIS')): ?>
							<button class="btn btn-xs btn-info pull-right" style="margin-left: 5px; width: 10%;" onclick="modalUploadMenu();"><i class="fa fa-upload"></i> Upload Menu</button>
							<a href="{{url('index/ga_control/live_cooking/confirm')}}" class="btn btn-success btn-xs pull-right" id="btn_confirm" style="margin-left: 5px; width: 10%;">
								<i class="fa fa-check"></i> Confirm (0)
							</a>
							<!-- <button class="btn btn-xs btn-warning pull-right" style="margin-left: 5px; width: 10%;" onclick="modalRandomize();"><i class="fa fa-random"></i> Randomize</button> -->
							<button class="btn btn-xs btn-danger pull-right" style="margin-left: 5px; width: 10%;" onclick="modalReport();"><i class="fa fa-file-text-o"></i> Report</button>
						<?php endif ?>
						<button class="btn btn-xs btn-warning pull-right" style="margin-left: 5px; width: 10%;" onclick="modalReportPay();"><i class="fa fa-file-text-o"></i> Resume Berbayar</button>
					</div>
					<div id="calendar"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalMenu">
	<div class="modal-dialog modal-md" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header" id="modalMenuBody">
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalUploadMenu">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Upload Menu</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="menuDate" name="menuDate" placeholder="Select Month">
						</div>
						<div class="col-sm-4" align="left">
							<a class="btn btn-info pull-right" href="{{url('download/ga_control/live_cooking')}}">Example</a>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">File Excel<span class="text-red"> :</span></label>
						<div class="col-sm-8" align="left">
							<input type="file" name="menuFile" id="menuFile">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="uploadMenu()" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalRandomize">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header" style="margin-bottom: 20px">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Random Live Cooking</h3>
				</center>
			</div>
			<div class="modal-body table-responsive no-padding" style="min-height: 90px;padding-top: 5px">
				<div class="col-xs-12">
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Periode<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="menuDateRandom" name="menuDateRandom" placeholder="Select Month">
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Date From<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control datepicker" id="dateFromRandom" name="dateFromRandom" placeholder="Select Date From">
						</div>
					</div>
					<div class="form-group row" align="right">
						<label for="" class="col-sm-4 control-label">Date To<span class="text-red"> :</span></label>
						<div class="col-sm-4">
							<input type="text" class="form-control datepicker" id="dateToRandom" name="dateToRandom" placeholder="Select Date To">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer" style="margin-top: 10px;">
				<div class="col-xs-12">
					<div class="row">
						<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Cancel</button>
						<button onclick="startRandomize()" class="btn btn-success pull-right"><i class="fa fa-random"></i> Start Randomize</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Create Your Order<br>予約を作成</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="nowDate" value="{{ date("Y-m-d") }}">
						<input type="hidden" id="lastDate" value="{{ date("Y-m-t") }}">
						<input type="hidden" id="firstDate" value="{{ date("Y-m-01") }}">
						<input type="hidden" id="total_day" value="{{ $total_day }}">
						<input type="hidden" id="count_kuota" value="{{ count($kuota) }}">
						<input type="hidden" id="month_now" value="{{ $month_now }}">
						<input type="hidden" id="role" value="{{ $role }}">
						<input type="hidden" id="user" value="{{ $user }}">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addUser" value="{{ strtoupper(Auth::user()->username) }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDate" placeholder="Select Date">
								</div>
							</div>
						</div>
					</form>
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="confirmOrder()">CONFIRM ORDER 確認</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCreateGa">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Pengajuan Live Cooking</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="nowDateGa" value="{{ date("Y-m-d") }}">
						<input type="hidden" id="lastDateGa" value="{{ date("Y-m-t") }}">
						<input type="hidden" id="firstDateGa" value="{{ date("Y-m-01") }}">
						<input type="hidden" id="total_dayGa" value="{{ $total_day }}">
						<input type="hidden" id="month_nowGa" value="{{ $month_now }}">
						<input type="hidden" id="roleGa" value="{{ $role }}">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="addUserGa" value="{{ strtoupper(Auth::user()->username) }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="addUserNameGa" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="addDateGa" placeholder="Select Date" value="{{date('Y-m-d')}}" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Kuota<span class="text-red"> :</span></label>
								<div class="col-sm-9" id="divAddKuota">
									<select class="form-control addKuota" name="addKuota" id="addKuota" data-placeholder="Select Kuota" style="width: 100%;" onchange="changeKuota(this.value)">
										<option value=""></option>
										<?php for ($i=0; $i < count($kuota); $i++) { ?>
											<option value="{{ $kuota[$i]['dept'] }}_{{ $kuota[$i]['sect'] }}_{{ $kuota[$i]['kuota'] }}">{{ $kuota[$i]['dept'] }} - {{ $kuota[$i]['sect'] }} - {{ $kuota[$i]['kuota'] }}</option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-12" style="padding-top: 10px">
									<div class="row">
										<table class="table table-hover table-bordered table-striped" id="tableOrderGa">
											<thead style="background-color: rgba(126,86,134,.7);">
												<tr>
													<th style="width: 2%;">Cat</th>
													<th style="width: 1%;">Detail Cat</th>
													<th style="width: 1%;">Kuota</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td id="dept_kuota"></td>
													<td id="sect_kuota"></td>
													<td id="nilai_kuota"></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Ordered For<span class="text-red"> :</span></label>
								<div class="col-sm-9" id="divAddEmployee">
									<select class="form-control addEmployee" name="addEmployeeGa" id="addEmployeeGa" data-placeholder="Select Employee" style="width: 100%;">
										<option value=""></option>
										
									</select>
								</div>
							</div>
							<a class="btn btn-primary pull-right" id="addCartBtnGa" onclick="addCartGa('param')">Add To <i class="fa fa-shopping-cart"></i></a>
						</div>
					</form>
					<div class="col-xs-12" style="padding-top: 10px;">
						<div class="row">
							<span style="font-weight: bold; font-size: 1.2vw;"><i class="fa fa-shopping-cart"></i> Cart List</span>
							<table class="table table-hover table-bordered table-striped" id="tableOrderGa">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">ID</th>
										<th style="width: 5%;">Name</th>
										<th style="width: 1%;">Date</th>
										<th style="width: 1%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableOrderGaBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<td>Total: </td>
										<td id="countTotalGa"></td>
										<td></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 2vw; width: 100%;" onclick="confirmOrderGa()">CONFIRM ORDER</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="modal fade" id="modalEdit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;">Edit Your Order</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<input type="hidden" id="editID" value="">
						<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
								<div class="col-sm-4">
									<input class="form-control" type="text" id="editUser" value="{{ Auth::user()->username }}" disabled>
								</div>
								<div class="col-sm-5" style="padding-left: 0px;">
									<input class="form-control" type="text" id="editUserName" value="{{ Auth::user()->name }}" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
								<div class="col-sm-5">
									<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date">
								</div>
							</div>
						</div>
					</form>
					<button class="btn btn-danger pull-left" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="deleteOrder()">DELETE <i class="fa fa-trash"></i></button>
					<button class="btn btn-warning pull-right" id="editOrderBtn" style="font-weight: bold; font-size: 1.3vw; width: 30%;" onclick="updateOrder()">EDIT <i class="fa fa-shopping-cart"></i></button>
				</div>
			</div>
		</div>
	</div>
</div> -->
<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 1000px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleDetail">Live Cooking Detail</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<input type="hidden" id="due_date">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 5%;">Name</th>
								<th style="width: 5%;">Dept</th>
								<th style="width: 5%;">Sect</th>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Menu</th>
								<?php if ($user == 'PI1201002' || str_contains($role, 'MIS')): ?>
									<th style="width: 1%;">Additional</th>
									<th style="width: 1%;">Action</th>
								<?php endif ?>
							</tr>
						</thead>
						<tbody id="bodyTableDetail">
							
						</tbody>
					</table>
					<div class="col-xs-12" id="divEdit">
						<div class="row">
							<form class="form-horizontal">
								<input type="hidden" id="editID" value="">
								<div class="col-md-10 col-md-offset-1" style="padding-bottom: 5px;">
									<div class="form-group">
										<label for="" class="col-sm-3 control-label">Employee<span class="text-red"> :</span></label>
										<div class="col-sm-9" id="divEditEmployee">
											<select class="form-control selectEditEmployee" name="editEmployee" id="editEmployee" data-placeholder="Select Employee" style="width: 100%;">
												<option></option>
												@foreach($employees as $emp)
												<option value="{{$emp->employee_id}}">{{$emp->employee_id}} - {{$emp->name}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="" class="col-sm-3 control-label">Date<span class="text-red"> :</span></label>
										<div class="col-sm-9">
											<input type="text" class="form-control datepicker" id="editDate" placeholder="Select Date">
										</div>
									</div>
								</div>
							</form>
							<div class="col-xs-4">
								<input type="hidden" name="" id="editType">
								<button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw;width: 100%" onclick="cancelEdit()"><i class="fa fa-close"></i> CANCEL</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-danger" style="font-weight: bold; font-size: 1.3vw;width: 100%" id="btn_delete" onclick="deleteOrder()"><i class="fa fa-trash"></i> DELETE</button>
							</div>
							<div class="col-xs-4">
								<button class="btn btn-warning" style="font-weight: bold; font-size: 1.3vw;width: 100%" onclick="updateOrder()"><i class="fa fa-edit"></i> EDIT</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalReport">
	<div class="modal-dialog modal-lg" style="width: 1000px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleReport">Live Cooking Report</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12">
						<div class="col-md-6">
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
						<div class="col-md-6">
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
								<th style="width: 5%;">Dept</th>
								<th style="width: 5%;">Sect</th>
								<th style="width: 5%;">Group</th>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Menu</th>
								<th style="width: 1%;">Additional</th>
								<th style="width: 1%;">Kehadiran</th>
								<th style="width: 2%;">Waktu</th>
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

<div class="modal fade" id="modalReportPay">
	<div class="modal-dialog modal-lg" style="width: 90%">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #f39c12; font-weight: bold; padding: 3px; margin-top: 0; color: white;" id="titleReport">Report Live Cooking Berbayar</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-md-12" style="padding-left: 0px;">
						<div class="col-md-4" style="padding-left: 0px;">
							<div class="form-group">
								<label>Periode</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="monthReport" name="monthReport" placeholder="Pilih Periode">
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12" style="padding-left: 0px;">
						<div class="col-md-4" style="padding-left: 0px;">
							<div class="form-group">
								<button id="close" onClick="$('#modalReportPay').modal('hide')" class="btn btn-danger">Close</button>
								<button id="search" onClick="fetchReportPay()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>

					<div id="divTablePay">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (count($admins) > 0) {
	$adminrole =$admins[0]->live_cooking_role;
} ?>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script src="{{ url("bower_components/moment/moment.js")}}"></script>
<script src="{{ url("bower_components/fullcalendar/dist/fullcalendar.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		var date = new Date();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: date
		});

		$('#datefrom').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#dateto').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
			// startDate: date
		});

		$('#menuDate').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		$('#monthReport').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});

		// $('.datepicker').datepicker({
		// 	<?php $tgl_max = date('Y-m-d') ?>
		// 	autoclose: true,
		// 	format: "yyyy-mm-dd",
		// 	todayHighlight: true,	
		// 	endDate: '<?php echo $tgl_max ?>'
		// });

		$('#menuDateRandom').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		
		$('.addEmployee').select2({
			dropdownParent:$('#divAddEmployee')
		});

		$('.selectEditEmployee').select2({
			dropdownParent:$('#modalDetail')
		});

		$('.addKuota').select2({
			dropdownParent:$('#divAddEmployee'),
			allowClear:true
		});

		$('#selectCategory').select2({
			dropdownParent:$('#divSelectCategory'),
			allowClear:true
		});
		$('#dateOk').hide();
		$('#dateError').hide();

		fetchOrderList();
	});
	var employees = [];
	var count = 0;

	function modalUploadMenu() {
		$('#modalUploadMenu').modal('show');
	}

	function modalRandomize() {
		$('#menuDateRandom').val('');
		$('#dateFromRandom').val('');
		$('#dateToRandom').val('');
		$('#modalRandomize').modal('show');
	}

	function startRandomize() {
		$('#loading').show();
		if ($('#menuDateRandom').val() == '' || $('#dateFromRandom').val() == '' || $('#dateToRandom').val() == '') {
			openErrorGritter('Error!','Isi Semua Data');
			$('#loading').hide();
		}else{
			var menuDateRandom = $('#menuDateRandom').val();
			var dateFromRandom = $('#dateFromRandom').val();
			var dateToRandom = $('#dateToRandom').val();

			if (dateFromRandom.split('-')[0]+'-'+dateFromRandom.split('-')[1] != menuDateRandom) {
				openErrorGritter('Error!','Tanggal tidak sesuai dengan periode');
				return false;
			}
			if (dateToRandom.split('-')[0]+'-'+dateToRandom.split('-')[1] != menuDateRandom) {
				openErrorGritter('Error!','Tanggal tidak sesuai dengan periode');
				return false;
			}

			var data = {
				menuDateRandom:menuDateRandom,
				dateFromRandom:dateFromRandom,
				dateToRandom:dateToRandom,
			}

			$.get('{{ url("fetch/ga_control/live_cooking_randomize") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success',result.message);
					$('#modalRandomize').modal('hide');
					fetchOrderList();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!',result.message);
					audio_error.play();
					return false;
				}
			});
		}
	}

	function changeKuota(kuota) {
		if (kuota === '') {
			$('#dept_kuota').html('');
			$('#sect_kuota').html('');
			$('#nilai_kuota').html('');
			employees = [];
			count = 0;
			$('#countTotalGa').html(0);
			$('#tableOrderGaBody').html('');
		}else{
			$('#dept_kuota').html(kuota.split('_')[0]);
			$('#sect_kuota').html(kuota.split('_')[1]);
			$('#nilai_kuota').html(kuota.split('_')[2]);

			employees = [];
			count = 0;
			$('#tableOrderGaBody').html('');
			$('#countTotalGa').html(0);
			var roles = '';
			if ('{{count($admins)}}' > 0) {
				roles = '{{$adminrole}}';
			}else{
				roles = '';
			}

			$('#loading').show();

			var data = {
				department:kuota.split('_')[0],
				section:kuota.split('_')[1],
				roles:roles,
				due_date:$('#addDateGa').val()
			}
			$.get('{{ url("fetch/ga_control/live_cooking_employees") }}',data, function(result, status, xhr){
				if(result.status){
					if (result.emp.length > 0) {
						var selectEmp = '';
						$('#addEmployeeGa').html('');

						selectEmp += '<option value=""></option>';

						for(var i = 0; i < result.emp.length;i++){
							selectEmp += '<option value="'+result.emp[i].employee_id+'_'+result.emp[i].name+'">'+result.emp[i].employee_id+' - '+result.emp[i].name+'</option>';
						}
						$('#addEmployeeGa').append(selectEmp);
						$('#loading').hide();

						if (kuota.split('_')[0] == 'Berbayar') {
							$('#nilai_kuota').html(result.sisa_kuota);
						}
					}else{
						$('#loading').hide();
					}
				}else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;
				}
			});
		}
	}

	function openModalCreate(cat, d, id,color,id_live,status_order){
		employees= [];
		count = 0;
		if(cat == 'new'){
			// if ($('#total_day').val() <= 7 && $('#month_now').val() != d.split('-')[0]+'-'+d.split('-')[1] && d > $("#nowDate").val() && id != 'Libur 休日') {
			if (id == 'Libur 休日' || id == '' || id.match(/OFF/gi)) {
				audio_error.play();
				openErrorGritter('Error!','Tidak Ada Schedule.');
			}else{
				if ('{{count($admins)}}' > 0) {
					var time = new Date();
					if (d >= $('#nowDate').val()) {
						if (d == $('#nowDate').val() && addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds()) <= '09:00:00') {
							$('#addDateGa').val(d);
							$('#addDateGa').prop('disabled', true);

							$('#addCartBtnGa').removeClass('disabled');
							$('#addCartBtnGa').removeAttr('disabled','disabled');
							$('#addEmployeeGa').val('').trigger('change');

							// $('#editOrderBtn').removeClass('disabled');
							// $('#editOrderBtn').removeAttr('disabled','disabled');
							$('#addKuota').val('').trigger('change');
							$('#tableOrderGaBody').html('');
							$('#dept_kuota').html('');
							$('#sect_kuota').html('');
							$('#nilai_kuota').html('');
							$('#modalCreateGa').modal('show');
						}else if(d > $('#nowDate').val()){
							$('#addDateGa').val(d);
							$('#addDateGa').prop('disabled', true);

							$('#addCartBtnGa').removeClass('disabled');
							$('#addCartBtnGa').removeAttr('disabled','disabled');
							$('#addEmployeeGa').val('').trigger('change');

							// $('#editOrderBtn').removeClass('disabled');
							// $('#editOrderBtn').removeAttr('disabled','disabled');
							$('#addKuota').val('').trigger('change');
							$('#tableOrderGaBody').html('');
							$('#dept_kuota').html('');
							$('#sect_kuota').html('');
							$('#nilai_kuota').html('');
							$('#modalCreateGa').modal('show');
						}
					}
				}
				else{
					// var time = new Date();
					// if (d > $('#nowDate').val()) {
					// 	$('#addDateGa').val(d);
					// 	$('#addDateGa').prop('disabled', true);

					// 	$('#addCartBtnGa').removeClass('disabled');
					// 	$('#addCartBtnGa').removeAttr('disabled','disabled');
					// 	$('#addEmployeeGa').val('').trigger('change');

					// 	// $('#editOrderBtn').removeClass('disabled');
					// 	// $('#editOrderBtn').removeAttr('disabled','disabled');
					// 	$('#addKuota').val('').trigger('change');
					// 	$('#tableOrderGaBody').html('');
					// 	$('#dept_kuota').html('');
					// 	$('#sect_kuota').html('');
					// 	$('#nilai_kuota').html('');
					// 	$('#modalCreateGa').modal('show');
					// }
				}
			}
		}
		if(cat == 'detail'){
			if (color == "rgb(126,86,134)" || color == '#ffbf36') {
				$('#divEdit').hide();
				$('#titleDetail').html('Live Cooking Detail Tanggal '+d+'<br>Menu '+id);
				fetchDetail(d);
				$('#modalDetail').modal('show');
			}else if(color == "#ccff90" || color == "yellow"){
				if (status_order != 'Rejected') {
					$('#modalDetail').modal('show');
					openModalEdit(id_live,id.split(' - ')[0],d,'editName');
					$("#btn_delete").removeAttr('disabled');
					if (d <= $('#nowDate').val()) {
						$("#btn_delete").prop('disabled',true);
					}
				}
				// if (d > $('#nowDate').val()) {
				// 	if (status_order != 'Rejected') {
				// 		$('#modalDetail').modal('show');
				// 		openModalEdit(id_live,id.split(' - ')[0],d,'editName');
				// 	}
				// }
			}
		}
	}

	function addCartGa(){
		var now_date = $('#nowDateGa').val();
		var date = $('#addDateGa').val();

		var str = $('#addEmployeeGa').val();

		if(date == "" || str == "" || str == null){
			audio_error.play();
			openErrorGritter('Error!','Pilih Nama Karyawan');
			return false;
		}

		if ($('#nilai_kuota').html() == 0) {
			$('#addEmployeeGa').val('').trigger('change');
			audio_error.play();
			openErrorGritter('Error!','Kuota Sudah Penuh.');
			return false;
		}

		if($.inArray(employee_id+'_'+date, employees) != -1){
			$('#addEmployeeGa').val('').trigger('change');
			audio_error.play();
			openErrorGritter('Error!','Karyawan sudah ada di list.');
			return false;
		}

		var employee_id = str.split("_")[0];
		var employee_name = str.split("_")[1];

		var tableOrder = "";

		tableOrder += "<tr id='"+employee_id+'_'+date+"'>";
		tableOrder += "<td>"+employee_id+"</td>";
		tableOrder += "<td>"+employee_name+"</td>";
		tableOrder += "<td>"+date+"</td>";
		tableOrder += "<td><a href='javascript:void(0)' onclick='remOrder(id)' id='"+employee_id+'_'+date+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
		tableOrder += "</tr>";

		employees.push(employee_id+'_'+date);
		count += 1;

		$('#countTotalGa').text(count);
		$('#tableOrderGaBody').append(tableOrder);
		$('#addEmployeeGa').val('').trigger('change');
		$('#nilai_kuota').html(parseInt($('#nilai_kuota').text())-1);
	}

	function remOrder(id){
		employees.splice( $.inArray(id), 1 );
		count -= 1;
		$('#countTotalGa').text(count);
		$('#nilai_kuota').html(parseInt($('#nilai_kuota').text())+1);
		$('#'+id).remove();	
	}

	function fetchDetail(d) {
		$('#loading').show();
		$('#due_date').val(d);
		$("#bodyTableDetail").html("");
		var bodyDetail = "";

		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();

		var data = {
			due_date:d
		}
		$.get('{{ url("detail/ga_control/live_cooking") }}',data, function(result, status, xhr){
			if(result.status){
				$.each(result.datas, function(key, value){
					bodyDetail += '<tr>';
					bodyDetail += '<td>'+value.employee_id+'</td>';
					bodyDetail += '<td>'+value.name+'</td>';
					bodyDetail += '<td>'+(value.department_shortname || "")+'</td>';
					bodyDetail += '<td>'+(value.section || "")+'</td>';
					bodyDetail += '<td>'+value.due_date+'</td>';
					bodyDetail += '<td>'+value.menu_name+'</td>';
					var type = 'editForm';
					if ($('#user').val() == 'PI1201002' || $('#role').val().match(/MIS/gi)) {
						bodyDetail += '<td>'+(value.additional || "")+'</td>';
						if (value.due_date >= value.date_now) {
							bodyDetail += '<td><button class="btn btn-warning" onclick="openModalEdit(\''+value.id_live+'\',\''+value.employee_id+'\',\''+value.due_date+'\',\''+type+'\')"><i class="fa fa-edit"></i> Edit</button></td>';
						}else{
							bodyDetail += '<td></td>';
						}
					}
					bodyDetail += '</tr>';
				});
				$('#bodyTableDetail').append(bodyDetail);
				$('#tableDetail').DataTable({
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
						},
						]
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
				$('#tableDetail').show();
				$('#loading').hide();
				$('#divEdit').hide();
			}else{
				$('#loading').hide();
				$('#modalDetail').modal('hide');
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function confirmOrder(){
		var order_by = $('#addUser').val();
		var date = $('#addDate').val();

		if(confirm("Apakah Anda yakin dengan order berikut?")){
			$('#loading').show();
			var data = {
				order_by:order_by,
				date:date
			}

			$.post('{{ url("input/ga_control/live_cooking_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter('Success',result.message);
					$('#modalCreate').modal('hide');
					$('#addDate').val("");
					fetchOrderList();
					$('#loading').hide();
					location.reload();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function confirmOrderGa(){
		var order_by = $('#addUserGa').val();
		var order_list = employees;

		if(order_list.length <= 0){
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!','Masukkan Nama Karyawan');
			return false;
		}

		if(confirm("Apakah Anda yakin?")){
			$('#loading').show();
			var data = {
				due_date:$('#addDateGa').val(),
				order_by:order_by,
				department:$("#dept_kuota").text(),
				section:$("#sect_kuota").text(),
				order_list:order_list,
			}

			$.post('{{ url("input/ga_control/live_cooking_order") }}', data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter(result.message);
					$('#modalCreateGa').modal('hide');
					$('#addDateGa').val("");
					$("#addEmployeeGa").prop('selectedIndex', 0).change();
					count = 0;
					$('#countTotalGa').text(count);
					$('#tableOrderGaBody').html("");
					employees = [];
					fetchOrderList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter(result.message);
					return false;
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function uploadMenu(){
		$('#loading').show();
		if($('#menuDate').val() == ""){
			openErrorGritter('Error!', 'Please input period');
			audio_error.play();
			$('#loading').hide();
			return false;	
		}

		var formData = new FormData();
		var newAttachment  = $('#menuFile').prop('files')[0];
		var file = $('#menuFile').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('menuDate', $("#menuDate").val());

		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('upload/ga_control/live_cooking_menu') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					audio_ok.play();
					$('#menuDate').val("");
					$('#menuFile').val("");
					$('#modalUploadMenu').modal('hide');
					$('#loading').hide();
					fetchOrderList();
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
	}

	function fetchOrderList(){
		$('#loading').show();
		$.get('{{ url("fetch/ga_control/live_cooking_order_list") }}', function(result, status, xhr){
			if(result.status){
				var quota = [];
				var ordered = [];
				var cat = [];
				var percentage = [];

				$.each(result.quota, function(key, value){
					quota.push(value.serving_quota);
					ordered.push(value.serving_ordered);
					cat.push(value.due_date);
					percentage.push((value.serving_ordered/value.serving_quota)*100);
				});

				var cal = {};
				var cals = [];
				var bg = "";
				var tx = "";

				// console.log(result.menus);

				$.each(result.menus, function(key,value){
					if(value.remark == 'H'){
						cal = {
							title: 'Libur 休日',
							start: Date.parse(value.week_date),
							allDay: true,
							backgroundColor: '#ff1744',
							textColor: 'white',
							borderColor: 'black',
						}
						cals.push(cal);	
					}
					else{
						if (value.menu_name != null) {
							if (value.serving_ordered == value.serving_quota) {
								cal = {
									title: value.menu_name+" ("+value.serving_ordered+"/"+value.serving_quota+")",
									id_live: "",
									status_order:"",
									start: Date.parse(value.week_date),
									allDay: true,
									backgroundColor:  '#427bff',
									textColor: 'white',
									borderColor: 'black'
								}
								cals.push(cal);
							}else{
								cal = {
									title: value.menu_name+" ("+value.serving_ordered+"/"+value.serving_quota+")",
									id_live: "",
									status_order:"",
									start: Date.parse(value.week_date),
									allDay: true,
									backgroundColor:  'rgb(126,86,134)',
									textColor: 'white',
									borderColor: 'black'
								}
								cals.push(cal);
							}

							// if (value.serving_ordered_pay == 20) {
							// 	cal = {
							// 		title: "Order Live Cooking Berbayar ("+value.serving_ordered_pay+"/20)",
							// 		id_live: "",
							// 		status_order:"",
							// 		start: Date.parse(value.week_date),
							// 		allDay: true,
							// 		backgroundColor:  '#427bff',
							// 		textColor: 'white',
							// 		borderColor: 'black'
							// 	}
							// 	cals.push(cal);
							// }else{
							// 	cal = {
							// 		title: "Order Live Cooking Berbayar ("+value.serving_ordered_pay+"/20)",
							// 		id_live: "",
							// 		status_order:"",
							// 		start: Date.parse(value.week_date),
							// 		allDay: true,
							// 		backgroundColor:  '#ffbf36',
							// 		textColor: 'black',
							// 		borderColor: 'black'
							// 	}
							// 	cals.push(cal);
							// }
						}
					}
				});

				$.each(result.resumes, function(key, value){
					if(value.status == 'Confirmed'){
						bg = '#ccff90';
						tx = 'black';
					}
					else if(value.status == 'Rejected'){
						bg = '#ff6090';
						tx = 'black';
					}
					else if(value.status == 'Requested'){
						bg = 'yellow';
						tx = 'black';
					}
					cal = {
						title: value.order_for+' - '+value.name_for,
						id_live: value.id_live,
						status_order: value.status,
						start: Date.parse(value.due_date),
						allDay: true,
						backgroundColor: bg,
						textColor: tx,
						borderColor: 'black'
					}
					cals.push(cal);
				});

				$("#btn_confirm").html('<i class="fa fa-check"></i> Confirm ('+result.resume_confirmation[0].qty+')');

				$(function () {			
					$('#calendar').fullCalendar({
						contentHeight: 600,
						header    : {
							left  : 'prev,next today',
							center: 'title',
							right : 'month,agendaWeek,agendaDay',
						},
						buttonText: {
							today: 'today',
							month: 'month',
							week : 'week',
							day  : 'day'
						},
						eventOrder: 'color,start',
						dayClick: function(date, allDay, jsEvent, view) { 
							var d = addZero(formatDate(date));
							var event_id = "";
							var event_color = "";
							var id_live = "";
							$('#calendar').fullCalendar('clientEvents', function(event) {
				                if (d == addZero(formatDate(event.start))) {
				                	event_id = event.title;
				                	event_color = event.backgroundColor;
				                }
				            });
							openModalCreate('new', d, event_id,event_color,"","");
						},
						eventClick: function(info) {
							openModalCreate('detail', formatDate(info.start), info.title,info.backgroundColor,info.id_live,info.status_order);
						},
						events    : cals,
						editable  : false
					})
					$('#calendar').fullCalendar( 'removeEvents' );
					$('#calendar').fullCalendar( 'addEventSource', cals); 

					var currColor = '#3c8dbc'
					var colorChooser = $('#color-chooser-btn')
					$('#color-chooser > li > a').click(function (e) {
						e.preventDefault()
						currColor = $(this).css('color')
						$('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
					})
					$('#add-new-event').click(function (e) {
						e.preventDefault()
						var val = $('#new-event').val()
						if (val.length == 0) {
							return
						}

						var event = $('<div />')
						event.css({
							'background-color': currColor,
							'border-color'    : currColor,
							'color'           : '#fff'
						}).addClass('external-event')
						event.html(val)
						$('#external-events').prepend(event)

						init_events(event)

						$('#new-event').val('')
					})
				});
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function formatDate(date) {
		var d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;

		return [year, month, day].join('-');
	}

	function openModalEdit(id,employee_id,due_date,type) {
		$('#editEmployee').val(employee_id).trigger('change.select2');
		$('#editDate').val(due_date).datepicker("setDate", new Date(due_date) );
		$('#editID').val(id);
		$('#divEdit').show();
		$('#tableDetail').hide();
		$('#tableDetail').DataTable().clear();
		$('#tableDetail').DataTable().destroy();
		$('#editType').val(type);
	}

	function updateOrder() {
		$('#loading').show();
		var editID = $('#editID').val();
		var editEmployee = $('#editEmployee').val();
		var editDate = $('#editDate').val();

		var data = {
			id:editID,
			status:'edit',
			order_by:editEmployee,
			order_for:editEmployee,
			due_date:editDate
		}
		$.post('{{ url("edit/ga_control/live_cooking_order") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				openSuccessGritter('Success',result.message);
				$('#modalCreate').modal('hide');
				$('#editID').val("");
				$('#editEmployee').val("").trigger('change.select2');
				$('#editDate').val("");
				$('#modalEdit').modal('hide');
				cancelEdit();
				fetchOrderList();
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;				
			}
		});
	}

	function deleteOrder(){
		var id = $('#editID').val();
		var data = {
			id:id,
			status:'delete',
		}
		if(confirm("Apakah Anda yakin akan menghapus data?")){
			$('#loading').show();
			$.post('{{ url("edit/ga_control/live_cooking_order") }}',data, function(result, status, xhr){
				if(result.status){
					audio_ok.play();
					openSuccessGritter('Success',result.message);
					$('#modalEdit').modal('hide');
					$('#editID').val("");
					$('#editDate').val("");
					$('#editEmployee').val("").trigger('change.select2');
					cancelEdit();
					fetchOrderList();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!',result.message);
					return false;				
				}
			});
		}
		else{
			$('#loading').hide();
			return false;
		}
	}

	function cancelEdit() {
		if ($("#editType").val() == 'editForm') {
			$('#editEmployee').val("").trigger('change.select2');
			$('#editDate').val("");
			$('#editID').val("");
			$('#divEdit').hide();
			$('#tableDetail').show();
			fetchDetail($('#due_date').val());
		}else{
			$('#modalDetail').modal('hide');
		}
	}

	$(function () {
		
	});

	$(function () {
		
	});

	$(function () {
		
	});

	function modalReport() {
		fetchReport();
		$('#modalReport').modal('show');
	}

	function modalReportPay() {
		$("#monthReport").val('');
		$('#modalReportPay').modal('show');
	}

	function fetchReport() {
		$('#loading').show();
		$("#bodyTableReport").html("");
		var bodyDetail = "";

		$('#tableReport').DataTable().clear();
		$('#tableReport').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto
		}
		$.get('{{ url("report/ga_control/live_cooking") }}',data, function(result, status, xhr){
			if(result.status){
				var index = 1;
				$.each(result.datas, function(key, value){
					if (value.attend_date == null) {
						var color = '#ffc7c7';
						var hadir = 'Tidak Hadir';
					}else{
						var color = '#c7ffde';
						var hadir = 'Hadir';
					}
					bodyDetail += '<tr style="background-color:'+color+'">';
					bodyDetail += '<td>'+index+'</td>';
					bodyDetail += '<td>'+value.employee_id+'</td>';
					bodyDetail += '<td>'+value.name+'</td>';
					bodyDetail += '<td>'+(value.department || "")+'</td>';
					bodyDetail += '<td>'+(value.section || "")+'</td>';
					bodyDetail += '<td>'+(value.group || "")+'</td>';
					bodyDetail += '<td>'+value.due_date+'</td>';
					bodyDetail += '<td>'+value.menu_name+'</td>';
					bodyDetail += '<td>'+(value.additional || "")+'</td>'
					bodyDetail += '<td>'+hadir+'</td>';
					bodyDetail += '<td>'+(value.attend_date || "")+'</td>';
					bodyDetail += '</tr>';
					index++;
				});
				$('#bodyTableReport').append(bodyDetail);
				$('#tableReport').DataTable({
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
						},
						]
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
				$('#tableReport').show();
				$('#loading').hide();
			}else{
				$('#loading').hide();
				$('#modalReport').modal('hide');
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
	}

	var calendars = null;

	function initiateTable() {
		$('#tableReportPay').DataTable().clear();
		$('#tableReportPay').DataTable().destroy();
		$('#divTablePay').html("");
		var tableData = "";
		tableData += '<table class="table table-hover table-bordered table-striped" id="tableReportPay">';
		tableData += '<thead style="" id="headTableReportPay">';
		tableData += '<tr>';
		tableData += '<th style="border-bottom:2px solid black;font-size:0.8vw;width: 1%;">No.</th>';
		tableData += '<th style="border-bottom:2px solid black;font-size:0.8vw;width: 1%;">ID</th>';
		tableData += '<th style="border-bottom:2px solid black;font-size:0.8vw;width: 10%;">Name</th>';
		for(var i = 0; i < calendars.length;i++){
			if (calendars[i].remark == 'H') {
				tableData += '<th style="width: 0.1%; border-bottom:2px solid black;font-size:0.8vw; background-color: rgba(80,80,80,0.5)">'+calendars[i].dates+'</th>';
			}else{
				tableData += '<th style="width: 0.1%;border-bottom:2px solid black;font-size:0.8vw;">'+calendars[i].dates+'</th>';
			}
		}
		tableData += '<th style="border-bottom:2px solid black;font-size:0.8vw;width: 1%;">Qty</th>';
		tableData += '<th style="border-bottom:2px solid black;font-size:0.8vw;width: 1%;">Amt</th>';
		tableData += '</tr>';
		tableData += '</thead>';
		tableData += '<tbody id="bodyTableReportPay">';
		tableData += '</tbody>';
		tableData += '</table>';
		$('#divTablePay').append(tableData);
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

	function fetchReportPay() {
		$('#loading').show();

		if ($('#monthReport').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Pilih Bulan');
			audio_error.play();
			return false;
		}

		$('#divTablePay').html('');

		var data = {
			month:$('#monthReport').val(),
		}
		$.get('{{ url("report/ga_control/live_cooking_pay") }}',data, function(result, status, xhr){
			if(result.status){
				
				calendars = result.calendar;
				initiateTable();

				$("#bodyTableReportPay").html("");
				var bodyDetail = "";

				var emp = [];
				for(var i = 0; i < result.report.length;i++){
					emp.push(result.report[i].order_by);
				}

				var emp_unik = emp.filter(onlyUnique);

				var index = 1;
				for(var i = 0; i < emp_unik.length;i++){
					bodyDetail += '<tr>';
					bodyDetail += '<td style="text-align:center;">'+index+'</td>';
					bodyDetail += '<td style="text-align:left;padding-left:2px;">'+emp_unik[i]+'</td>';
					var names = '';
					for(var j = 0; j < result.emp.length;j++){
						if (result.emp[j].employee_id == emp_unik[i]) {
							names = result.emp[j].name;
						}
					}
					bodyDetail += '<td style="text-align:left;padding-left:2px;">'+names+'</td>';
					var qty_all = 0;
					for(var k = 0; k < result.calendar.length;k++){
						var qty = 0;
						for(var u = 0; u < result.report.length;u++){
							if (result.report[u].order_by == emp_unik[i] && result.report[u].due_date == result.calendar[k].week_date) {
								qty++;
								qty_all++;
							}
						}
						if (result.calendar[k].remark == 'H') {
							bodyDetail += '<td style="text-align:center;background-color:rgba(80,80,80,0.5)"></td>';
						}else{
							if (qty == 0) {
								bodyDetail += '<td style="text-align:center;background-color:#ff6090"></td>';
							}else{
								bodyDetail += '<td style="text-align:center;background-color:#ccff90">'+qty+'</td>';
							}
						}
					}
					bodyDetail += '<td style="text-align:center;">'+qty_all+'</td>';
					bodyDetail += '<td style="text-align:center;">'+(parseInt(qty_all)*15000)+'</td>';
					bodyDetail += '</tr>';
					index++;
				}

				$('#bodyTableReportPay').append(bodyDetail);

				$('#tableReportPay').DataTable({
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

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		});
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