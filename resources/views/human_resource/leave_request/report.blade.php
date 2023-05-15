@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:2px;
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
		<a class="btn btn-success btn-sm pull-right" href="{{url('index/human_resource/leave_request')}}">
			<i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Buat Pengajuan
		</a>
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
		<div class="col-xs-12" style="padding-right: 5px;">
			<div class="box box-solid">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12" style="margin-bottom: 10px">
							<center style="background-color: rgb(126,86,134); color: #fff;">
								<span style="font-size: 17px;font-weight: bold;padding: 5px">Filter</span>
							</center>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Date From</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Date To</span>
							<div class="form-group">
								<div class="input-group date">
									<div class="input-group-addon bg-white">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_to" placeholder="Select Date To" autocomplete="off">
								</div>
							</div>
						</div>
						<div class="col-md-4 col-md-offset-2">
							<span style="font-weight: bold;">Status</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Status" id="leave_status">
									<option value=""></option>
									@foreach($leave_status as $leave_status)
									<option value="{{$leave_status}}">{{$leave_status}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<span style="font-weight: bold;">Department</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Department" id="department">
									<option value=""></option>
									@foreach($department as $department)
									<option value="{{$department->department_name}}">{{$department->department_shortname}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-8 col-md-offset-2">
							<span style="font-weight: bold;">Category</span>
							<div class="form-group">
								<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Category" id="category">
									<option value=""></option>
									<option value="Pulang Cepat">Pulang Cepat</option>
									<option value="Dinas Makan Siang">Dinas Makan Siang</option>
								</select>
							</div>
						</div>
						<div class="col-md-5 col-md-offset-2">
							<div class="col-md-12">
								<div class="form-group pull-right">
									<a href="{{ url('index/injeksi') }}" class="btn btn-warning">Back</a>
									<a href="{{url('index/human_resource/leave_request')}}" class="btn btn-danger">Clear</a>
									<button class="btn btn-primary col-sm-14" onclick="fillList()">Search</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-right: 5px">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12 pull-left" style="overflow-x: scroll;">
						<div class="row">
							<table id="tableLeave" class="table table-bordered table-striped table-hover table-responsive" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #fff;">
									<tr>
										<th width="1%">ID</th>
										<th width="1%">Req Date</th>
										<th width="1%">Status</th>
										<th width="1%">Cat</th>
										<th width="2%">Purpose</th>
										<th width="2%">Detail</th>
										<th width="2%">City</th>
										<th width="2%">Emp ID</th>
										<th width="2%">Name</th>
										<th width="2%">Dept</th>
										<th width="2%">From</th>
										<th width="2%">To</th>
										<th width="2%">Act From</th>
										<th width="2%">Act To</th>
										<th width="2%">Act Diff</th>
										<th width="2%">City</th>
										<th width="2%">Return</th>
										<th width="2%">Potong Gaji</th>
										<th width="2%">Makan Siang</th>
										<!-- <th width="2%">Approval</th> -->
										<th width="2%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableLeave">
								</tbody>
							</table>
						</div>
					</div>
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
						<h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail Request</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<center>
									<div style="width: 60%">
										<span id="reason" style="font-weight: bold;font-size: 18px;color: red">
											aa
										</span>
										<br>
										<table style="border:1px solid black; border-collapse: collapse;">
											<tbody align="center">
												<tr>
													<td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">PT. Yamaha Musical Products Indonesia</td>
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
														Purpose Category
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="purpose_category">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Purpose Detail
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="purpose_detail">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Departure
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="time_departure">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Arrived
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="time_arrived">
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Return Or Not
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="return_or_not">
														
													</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
														Need Driver
													</td>
													<td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="add_driver_or_not">
														
													</td>
												</tr>
											</tbody>
										</table>

										<table style="border:1px solid black; border-collapse: collapse;margin-top:20px;width: 100%">
											<thead align="center">
												<tr>
													<td colspan="3" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Employees</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 15px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;">
														ID
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 20%; height: 15;font-weight: bold;background-color: #d4e157;">
														Name
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 20%; height: 15;font-weight: bold;background-color: #d4e157;">
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
													<td colspan="2" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Clinic Details</td>
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
													<td colspan="2" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Destination</td>
												</tr>
												<tr>
													<td style="border:1px solid black; font-size: 15px; width: 11%; height: 15; font-weight: bold;background-color: #d4e157;">
														#
													</td>
													<td style="border:1px solid black; font-size: 15px; width: 3%; height: 15; font-weight: bold;background-color: #d4e157;">
														Destination
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
	var employees = [];
	var count = 0;
	var destinations = [];
	var countDestination = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		fillList();

		clearAll();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,
		});

		$('.timepicker').timepicker({
			showInputs: false,
			showMeridian: false,
			defaultTime: '0:00',
		});
	});


	$(function () {
		$('.select2').select2({
			allowClear:true
		});
	});

	function clearAll() {
		$('#add_purpose_category').val('').trigger('change');
		$('#add_purpose_detail').val('').trigger('change');
		$('#add_detail').val('');
		$('#add_employees').val('').trigger('change');
		$('#add_destination').val('');
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

	function fillList(){
		$('#loading').show();
		var data = {
			date_from:$('#tanggal_from').val(),
			date_to:$('#tanggal_to').val(),
			leave_status:$('#leave_status').val(),
			department:$('#department').val(),
			category:$('#category').val(),
		}
		$.get('{{ url("fetch/human_resource/leave_request_report") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableLeave').DataTable().clear();
				$('#tableLeave').DataTable().destroy();
				$('#bodyTableLeave').html("");
				var tableData = "";
				$.each(result.leave_request, function(key, value) {
					tableData += '<tr style="padding:2px">';
					tableData += '<td>'+ value.request_id +'</td>';
					tableData += '<td>'+ value.date +'</td>';
					tableData += '<td>';
					if (value.remark == 'Requested') {
						tableData += '<span class="label label-primary">'+value.remark+'</span>';
					}else if (value.remark == 'Partially Approved') {
						tableData += '<span class="label label-warning">'+value.remark+'</span>';
					}else if (value.remark == 'Fully Approved') {
						tableData += '<span class="label label-success">'+value.remark+'</span>';
					}else if (value.remark == 'Rejected') {
						tableData += '<span class="label label-danger">'+value.remark+'</span>';
					}
					tableData += '</td>';
					tableData += '<td>'+ value.purpose_category +'</td>';
					tableData += '<td>'+ value.purpose+'</td>';
					tableData += '<td>'+ value.purpose_detail +'</td>';
					tableData += '<td>'+ (value.detail_city || '') +'</td>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.department_shortname +'</td>';
					tableData += '<td>'+ value.time_departure +'</td>';
					tableData += '<td>'+ value.time_arrived +'</td>';
					tableData += '<td>'+ (value.confirmed_at || "") +'</td>';
					tableData += '<td>'+ (value.returned_at || "") +'</td>';
					if (value.confirmed_at != null && value.returned_at != null) {
						var diff = moment.utc(moment(value.returned_at,"YYYY-MM-DD HH:mm:ss").diff(moment(value.confirmed_at,"YYYY-MM-DD HH:mm:ss"))).format("HH:mm:ss")
					}else{
						var diff = '';
					}
					tableData += '<td><span class="label label-success">'+ diff +'</span></td>';
					tableData += '<td>'+ (value.destination_city || '') +'</td>';
					if (value.return_or_not == 'YES') {
						tableData += '<td>KEMBALI</td>';
					}else{
						tableData += '<td>TIDAK KEMBALI</td>';
					}
					if (value.return_or_not == 'NO') {
						if (value.purpose_category == 'DINAS') {
							var salary = '<span class="label label-success">NO</span>';
						}else{
							var salary = '<span class="label label-danger">YES</span>';
						}
					}else if(value.return_or_not == 'YES'){
						if (value.purpose_category == 'DINAS') {
							var salary = '<span class="label label-success">NO</span>';
						}else{
							if(parseInt(diff.split(':')[0]) > 2){
								var salary = '<span class="label label-danger">YES</span>';
							}else{
								var salary = '<span class="label label-success">NO</span>';
							}
						}
					}else{
						var salary = '<span class="label label-success">NO</span>';
					}
					tableData += '<td>'+salary+'</td>';

					var departure = new Date(value.time_departure);
					var time_depart_new = addZero(departure.getHours())+':'+addZero(departure.getMinutes())+':'+addZero(departure.getSeconds());

					var arrived = new Date(value.time_arrived);
					var time_arrived_new = addZero(arrived.getHours())+':'+addZero(arrived.getMinutes())+':'+addZero(arrived.getSeconds());
					if (value.purpose_category == 'DINAS') {
						if (time_depart_new <= '11:45:00' && time_arrived_new >= '12:55:00') {
							var maksi = '<span class="label label-success">YES</span>';
						}else{
							var maksi = '<span class="label label-danger">NO</span>';
						}
					}else{
						var maksi = '<span class="label label-danger">NO</span>';
					}
					tableData += '<td>'+maksi+'</td>';
					// tableData += '<td>';
					// for(var i = 0; i < result.leave_approvals.length;i++){
					// 	if (result.leave_approvals[i][0].request_id == value.request_id) {
					// 		var index = result.leave_approvals[i].length;
					// 		for(var j = 0; j < result.leave_approvals[i].length;j++){
					// 			var k = j+1;
					// 			if (result.leave_approvals[i][j].status == 'Approved') {
					// 				if (index == k) {
					// 					tableData += '<span class="label label-success">'+result.leave_approvals[i][j].remark+'</span>';
					// 				}else{
					// 					tableData += '<span class="label label-success">'+result.leave_approvals[i][j].remark+'</span>&#8658;';
					// 				}
					// 			}else{
					// 				if (index == k) {
					// 					tableData += '<span class="label label-danger">'+result.leave_approvals[i][j].remark+'</span>';
					// 				}else{
					// 					tableData += '<span class="label label-danger">'+result.leave_approvals[i][j].remark+'</span>&#8658;';
					// 				}
					// 			}
					// 		}
					// 	}
					// }
					// tableData += '</td>';

					tableData += '<td>';
					tableData += '<button style="margin-right:2px" class="btn btn-sm btn-info" onclick="detailInformation(\''+value.request_id+'\')">Detail</button>';
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
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');
			}
		});
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
				$('#return_or_not').html(result.leave_request.return_or_not);
				$('#add_driver_or_not').html(result.leave_request.add_driver);

				$('#reason').html('');

				if (result.leave_request.remark == 'Rejected') {
					$('#reason').html('Permintaan Anda ditolak oleh Manager dengan reason<br>'+result.leave_request.reason);
				}

				$('#bodyEmp').html('');
				var bodyEmp = '';

				for(var i = 0; i < result.detail_emp.length;i++){
					bodyEmp += '<tr>';
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.detail_emp[i].employee_id+'</td>';
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.detail_emp[i].name+'</td>';														
					bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.detail_emp[i].department_shortname+'</td>';
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
				headApproval += '<td colspan="'+result.approval_progress.length+'" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Approval Progress</td>';
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
					bodyApproval += '<td style="border:1px solid black; font-size: 13px; width: 20%; height: 15; font-weight: bold;background-color: #d4e157;">'+result.approval_progress[i].approver_name.split(' ').slice(0,2).join(' ')+'</td>';
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