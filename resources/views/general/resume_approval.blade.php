@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		background-color: #f9f9f9;
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
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
		padding-top: 2px;
		padding-bottom: 2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		vertical-align: middle;
	}
	img {max-width:100%}
	#loading, #error { display: none; }
</style>
@stop
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body" style="border: 1px solid grey;">
					<!-- <form class="form-horizontal"> -->
						<div class="col-xs-12">

							<div class="col-md-2 col-md-offset-4">
								<div class="form-group">
									<label >Application Date</label>
									<input type="text" class="form-control datepicker" id="filterApplicationDate"
									name="filterApplicationDate" placeholder="Select Date">
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Approval</label>
									<select class="form-control select2" multiple="multiple" id="filterApproval" data-placeholder="Select Approval" style="width: 100%;">
										<option></option>
										@foreach ($approvals as $approval)
										<option value="{{ $approval['approval'] }}">({{ $approval['department'] }}) {{ $approval['approval'] }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="col-md-2 col-md-offset-3">
								<div class="form-group">
									<label>Applicant</label>
									<select class="form-control select2" multiple="multiple" id="filterApplicant" data-placeholder="Select Applicant" style="width: 100%;">
										<option></option>
										@foreach ($employees as $employee)
										<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Category</label>
									<select class="form-control select3" id="filterCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										<option value="Adagio">With Category</option>
										<option value="None">No Category</option>
									</select>
								</div>
							</div>

							<div class="col-md-2 ">
								<div class="form-group">
									<label>Status</label>
									<select class="form-control select2" id="filterStatus" data-placeholder="Select Status" style="width: 100%;" multiple="multiple">
										<option></option>
										<option value="Fully Approved">Fully Approved</option>
										<option value="Partially Approved">Partially Approved</option>
										<option value="Rejected">Rejected</option>
									</select>
								</div>
							</div>

							<div class="col-md-4 col-md-offset-4" style="margin-top:10px">
								<div class="form-group">
									<div class="col-md-12">
										<button class="btn btn-danger" onclick="clearAll()" style="width: 30%;margin-left:60px">Clear</button>
										<button class="btn btn-primary" onclick="fetchData()" style="width: 30%;margin-right: 10px;">Search</button>
									</div>
								</div>
							</div>
						</div>

						<!-- 
							<div class="form-group">
								<label for="exampleInputEmail1" class="col-sm-2 control-label">Applicant</label>
								<div class="col-xs-3">
									<select class="form-control select2" multiple="multiple" id="filterApplicant" data-placeholder="Select Applicant" style="width: 100%;">
										<option></option>
										@foreach ($employees as $employee)
										<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div> -->
							{{-- <div class="form-group">
								<label for="exampleInputEmail1" class="col-sm-3 control-label">Category</label>
								<div class="col-xs-9">
									<select class="form-control select2" multiple="multiple" id="filterCategory" data-placeholder="Select Category" style="width: 100%;">
										<option></option>
										@foreach ($categories as $category)
										<option value="{{ $category['code'] }} : {{ $category['name'] }}">{{ $category['code'] }} : {{ $category['name'] }}</option>
										@endforeach
									</select>
								</div>
							</div> --}}
							<!-- </div> -->
							<!-- </form> -->
					<!-- <div class="col-xs-6 col-md-offset-3">
						<button class="btn btn-primary pull-right" onclick="fetchData()">Search</button>
						<button class="btn btn-danger pull-right" onclick="clearAll()" style="margin-right: 10px;">Clear</button>
					</div> -->
					<div class="col-xs-12" style="margin-top:20px;padding: 0;">
						<table id="tableData" class="table table-bordered table-hover" style="margin-top: 10px;">
							<thead style="">
								<tr>
									<th style="width: 0.2%; text-align: center; background-color: #605ca8; color: white;">Final<br>Approval Date</th>
									<th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Final<br>Approver</th>
									<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Application No</th>
									<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
									<th style="width: 2%; text-align: left; background-color: #605ca8; color: white;">Subject</th>
									<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">HR<br>Matter</th>
									<th style="width: 2%; text-align: center; background-color: #605ca8; color: white;">Category</th>
									<th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Applicant</th>
									<th style="width: 0.2%; text-align: center; background-color: #605ca8; color: white;">Application<br>Date</th>
									<th style="width: 0.5%; text-align: right; background-color: #605ca8; color: white;">Amount</th>
									<th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Unit</th>
								</tr>
							</thead>
							<tbody id="tableDataBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2({
			dropdownAutoWidth : true
			// allowClear: true
		});
		$('.select3').select2({
			dropdownAutoWidth : true,
			allowClear: true
		});
		$('#filterApplicationDate').daterangepicker({
			locale: {
				format: 'YYYY-MM-DD'
			}
		});
		$('#filterApplicationDate').val("");
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearAll(){
		location.reload(true);		
	}

	function fetchData(){
		$('#loading').show();
		var filterApplicationDate = $('#filterApplicationDate').val();
		var filterApproval = $('#filterApproval').val();
		var filterApplicant = $('#filterApplicant').val();
		var filterCategory = $('#filterCategory').val();
		var filterStatus = $('#filterStatus').val();

		if (filterApplicationDate == "") {
			$('#loading').hide();
			alert('Please select application date.');
			return false;
		}

		var data = {
			filterApplicationDate:filterApplicationDate,
			filterApproval:filterApproval,
			filterApplicant:filterApplicant,
			// filterCategory:filterCategory,
			filterStatus:filterStatus,
		}

		$.get('{{ url("fetch/general/resume_approval") }}', data, function(result, status, xhr){
			if(result.status){
				var tableDataBody = "";
				$('#tableDataBody').html("");
				$('#tableData').DataTable().clear();
				$('#tableData').DataTable().destroy();

				if (filterCategory == 'Adagio') {
					$.each(result.resumes, function(key, value){
						if (value.category != '-') {
							tableDataBody += '<tr>';
							if (value.status == "Fully Approved") {
								tableDataBody += '<td style="width: 0.2%; text-align: center;">'+ (value.final_date || '')+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 0.2%; text-align: center;"></td>';
							}
							if(value.approver_name){
								tableDataBody += '<td style="width: 1%; text-align: left;"><b>'+value.approver_position+'</b><br>'+value.approver_name+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 1%; text-align: left;"></td>';						
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.id+'</td>';
							var color = "";
							if(value.status == 'Fully Approved'){
								color = "color: #009551;";
							}
							else if(value.status == 'Partially Approved'){
								color = "color: #f39c12;";
							}
							else{
								color = "color: #dd4b39;";
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center; '+color+'">'+value.status+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: left;">'+value.subject+'</td>';
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.hr_matter+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: center;">'+value.category+'</td>';
							tableDataBody += '<td style="width: 1%; text-align: left;">'+value.applicant_id+'<br>'+value.applicant_name+'</td>';
							tableDataBody += '<td style="width: 0.2%; text-align: center;">'+value.application_date+'</td>';
							if (value.amount != "-") {
								tableDataBody += '<td style="width: 0.5%; text-align: right;">'+parseInt(value.amount).toLocaleString('de-DE')+'</td>';
							}else{
								tableDataBody += '<td style="width: 0.5%; text-align: right;">-</td>';
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.unit+'</td>';
							tableDataBody += '</tr>';
						}
					});
				}else{
					$.each(result.resumes, function(key, value){
						if (value.category == '-') {
							tableDataBody += '<tr>';
							if (value.status == "Fully Approved") {
								tableDataBody += '<td style="width: 0.2%; text-align: center;">'+ (value.final_date || '')+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 0.2%; text-align: center;"></td>';
							}
							if(value.approver_name){
								tableDataBody += '<td style="width: 1%; text-align: left;"><b>'+value.approver_position+'</b><br>'+value.approver_name+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 1%; text-align: left;"></td>';						
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.id+'</td>';
							var color = "";
							if(value.status == 'Fully Approved'){
								color = "color: #009551;";
							}
							else if(value.status == 'Partially Approved'){
								color = "color: #f39c12;";
							}
							else{
								color = "color: #dd4b39;";
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center; '+color+'">'+value.status+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: left;">'+value.subject+'</td>';
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.hr_matter+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: center;">'+value.category+'</td>';
							tableDataBody += '<td style="width: 1%; text-align: left;">'+value.applicant_id+'<br>'+value.applicant_name+'</td>';
							tableDataBody += '<td style="width: 0.2%; text-align: center;">'+value.application_date+'</td>';
							if (value.amount != "-") {
								tableDataBody += '<td style="width: 0.5%; text-align: right;">'+parseInt(value.amount).toLocaleString('de-DE')+'</td>';
							}else{
								tableDataBody += '<td style="width: 0.5%; text-align: right;">-</td>';
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.unit+'</td>';
							tableDataBody += '</tr>';
						}else{
							tableDataBody += '<tr>';
							if (value.status == "Fully Approved") {
								tableDataBody += '<td style="width: 0.2%; text-align: center;">'+ (value.final_date || '')+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 0.2%; text-align: center;"></td>';
							}
							if(value.approver_name){
								tableDataBody += '<td style="width: 1%; text-align: left;"><b>'+value.approver_position+'</b><br>'+value.approver_name+'</td>';
							}
							else{
								tableDataBody += '<td style="width: 1%; text-align: left;"></td>';						
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.id+'</td>';
							var color = "";
							if(value.status == 'Fully Approved'){
								color = "color: #009551;";
							}
							else if(value.status == 'Partially Approved'){
								color = "color: #f39c12;";
							}
							else{
								color = "color: #dd4b39;";
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center; '+color+'">'+value.status+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: left;">'+value.subject+'</td>';
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.hr_matter+'</td>';
							tableDataBody += '<td style="width: 2%; text-align: center;">'+value.category+'</td>';
							tableDataBody += '<td style="width: 1%; text-align: left;">'+value.applicant_id+'<br>'+value.applicant_name+'</td>';
							tableDataBody += '<td style="width: 0.2%; text-align: center;">'+value.application_date+'</td>';
							if (value.amount != "-") {
								tableDataBody += '<td style="width: 0.5%; text-align: right;">'+parseInt(value.amount).toLocaleString('de-DE')+'</td>';
							}else{
								tableDataBody += '<td style="width: 0.5%; text-align: right;">-</td>';
							}
							tableDataBody += '<td style="width: 0.1%; text-align: center;">'+value.unit+'</td>';
							tableDataBody += '</tr>';
							
						}

					});
				}
				$('#tableDataBody').append(tableDataBody);

				$('#tableData').DataTable({
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
				$('#loading').hide();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
}

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

