@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
table{
	border:1px solid black !important;
}
thead>tr>th{
	vertical-align: middle !important;
	text-align:center !important;
	border:1px solid black !important;
}
tbody>tr>td{
	border:1px solid black !important;
}
tfoot>tr>th{
	border:1px solid black !important;
}
.select2-container.select2-container--default.select2-container--open  {
	z-index: 5000;
}
.crop2 {
	overflow: hidden;
}
.crop2 img {
	height: 70px;
	margin: -2.7% 0 0 0 !important;
}
#tableResumeFYBody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
#tableResumeFY2Body > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
#tableResumeProjectBody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
#tableResumeDepartmentBody > tr:hover {
	cursor: pointer;
	background-color: #7dfa8c;
}
#loading { display: none; }
#alert { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
		<select class="form-control select2 pull-right" id="filterFiscalYear" data-placeholder="Select Fiscal Year" onchange="fetchResume()" style="width: 10%;">
			<option value="all">ALL</option>
			@foreach($weekly_calendars as $weekly_calendar)
			@if($weekly_calendar->fiscal_year == $now->fiscal_year)
			<option value="{{ $weekly_calendar->fiscal_year }}" selected>{{ $weekly_calendar->fiscal_year }}</option>
			@else
			<option value="{{ $weekly_calendar->fiscal_year }}">{{ $weekly_calendar->fiscal_year }}</option>
			@endif
			@endforeach
		</select>
		<label style="color: green;" for="" class="control-label pull-right">Fiscal Year :&nbsp;&nbsp;&nbsp;</label>
	</h1>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header" style="border-bottom: 1px solid black;">
					<h3 class="box-title">Resume Fiscal Year</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-7">
									<table class="table table-bordered table-responsive" width="70%" id="tableResumeFY">
										<thead>
											<tr>
												<th style="width: 16.66%;">Fiscal Year</th>
												<th style="width: 16.66%;">Month</th>
												<th style="width: 16.66%;">Count Ticket</th>
												<th style="width: 16.66%;">Total Effect/Month (USD)</th>
												<th style="width: 16.66%;">Total Spent (USD)</th>
												<th style="width: 16.66%;">Total Man Time (Hour)</th>
											</tr>
										</thead>
										<tbody id="tableResumeFYBody">
										</tbody>
										<tfoot id="tableResumeFYFoot">
										</tfoot>
									</table>
								</div>
								<div class="col-xs-5">
									<table class="table table-bordered table-responsive" width="20%" id="tableResumeFY2">
										<thead>
											<tr>
												<th style="width: 1%;">Fiscal Year</th>
												<th style="width: 1%;">Category</th>
												<th style="width: 1%;">Total Effect/Month</th>
											</tr>
										</thead>
										<tbody id="tableResumeFY2Body">
										</tbody>
										<tfoot id="tableResumeFY2Foot">
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header with-border" style="border-bottom: 1px solid black;">
					<h3 class="box-title">
						Resume Department
					</h3>
				{{-- 	<select class="form-control select2 pull-right" id="filterDepartment" data-placeholder="Select Department" style="width: 10%;">
						<option value="all">All</option>
						@foreach($departments as $department)
						<option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
						@endforeach
					</select>
					<label style="" for="" class="control-label pull-right">Select Department<span class="text-red"></span> :&nbsp;&nbsp;&nbsp;&nbsp;</label> --}}
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered table-responsive" width="100%" id="tableResumeDepartment">
								<thead>
									<tr>
										<th style="width: 3%;">Dept.</th>
										<th style="width: 1%;">Count Ticket</th>
										<th style="width: 1%;">Total Effect/Month (USD)</th>
										<th style="width: 1%;">Total Spent (USD)</th>
										<th style="width: 1%;">Total Man Time (Hour)</th>
									</tr>
								</thead>
								<tbody id="tableResumeDepartmentBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="box box-solid" style="border: 1px solid black;">
				<div class="box-header with-border" style="border-bottom: 1px solid black;">
					<h3 class="box-title">
						Resume Project
					</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered table-responsive" width="100%" id="tableResumeProject">
								<thead>
									<tr>
										<th style="width: 1%;">Ticket ID</th>
										<th style="width: 1%;">Dept.</th>
										<th style="width: 5%;">Project</th>
										<th style="width: 1%;">PIC</th>
										<th style="width: 3%;">Contributors</th>
										<th style="width: 1%;">Start</th>
										<th style="width: 1%;">Finish</th>
										<th style="width: 1%;">Progress</th>
										<th style="width: 1%;">Total Effect/Month (USD)</th>
										<th style="width: 1%;">Total Spent (USD)</th>
										<th style="width: 1%;">Total Man Time (Hour)</th>
									</tr>
								</thead>
								<tbody id="tableResumeProjectBody">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	var no = 2;
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('.select2').select2();
		$('.select2').addClass("pull-right");
		fetchResume();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function fetchResume(){
		var data = {
			fiscal_year:$('#filterFiscalYear').val()
		}
		$.get('{{ url("fetch/ticket/resume") }}', data, function(result, status, xhr){
			if(result.status){

				var resumefy_cd = {};
				var resumefy_sp = {};
				var resumefy_mt = {};
				var resumefy_ct = {};
				var resumefy_category = {};

				var resumedept_ct = {};
				var resumedept_cd = {};
				var resumedept_sp = {};
				var resumedept_mt = {};

				var resumeproject_cd = {};
				var resumeproject_sp = {};

				var key = "";

				$.each(result.costdowns, function(key, value){
					key = value.fiscal_year+'__'+value.mon;
					if (!resumefy_cd[key]) {
						resumefy_cd[key] = 0;
					}
					resumefy_cd[key] += value.amount;

					key = value.department;
					if (!resumedept_cd[key]) {
						resumedept_cd[key] = 0;
					}
					resumedept_cd[key] += value.amount;

					key = value.ticket_id;
					if (!resumeproject_cd[key]) {
						resumeproject_cd[key] = 0;
					}
					resumeproject_cd[key] += value.amount;
				});
				$.each(result.spents, function(key, value){
					key = value.fiscal_year+'__'+value.mon;
					if (!resumefy_sp[key]) {
						resumefy_sp[key] = 0;
					}
					resumefy_sp[key] += value.amount;

					key = value.department;
					if (!resumedept_sp[key]) {
						resumedept_sp[key] = 0;
					}
					resumedept_sp[key] += value.amount;

					key = value.ticket_id;
					if (!resumeproject_sp[key]) {
						resumeproject_sp[key] = 0;
					}
					resumeproject_sp[key] += value.amount;
				});
				$.each(result.man_times, function(key, value){
					key = value.fiscal_year+'__'+value.mon;
					if (!resumefy_mt[key]) {
						resumefy_mt[key] = 0;
					}
					resumefy_mt[key] += parseFloat(value.duration);

					key = value.department;
					if (!resumedept_mt[key]) {
						resumedept_mt[key] = 0;
					}
					resumedept_mt[key] += parseFloat(value.duration);
				});
				$.each(result.man_times, function(key, value){
					key = value.fiscal_year+'__'+value.mon;
					if (!resumefy_ct[key]) {
						resumefy_ct[key] = 0;
					}
					resumefy_ct[key] += 1;

					key = value.department;
					if (!resumedept_ct[key]) {
						resumedept_ct[key] = 0;
					}
					resumedept_ct[key] += 1;
				});
				$.each(result.costdowns, function(key, value){
					if(value.category !== null){
						key = value.fiscal_year+'__'+value.category;
						if (!resumefy_category[key]) {
							resumefy_category[key] = 0;
						}
						resumefy_category[key] += value.amount;
					}
				});

				$('#tableResumeFY').DataTable().clear();
				$('#tableResumeFY2').DataTable().clear();
				$('#tableResumeDepartment').DataTable().clear();
				$('#tableResumeProject').DataTable().clear();

				$('#tableResumeFY').DataTable().destroy();
				$('#tableResumeFY2').DataTable().destroy();
				$('#tableResumeDepartment').DataTable().destroy();
				$('#tableResumeProject').DataTable().destroy();

				$('#tableResumeFYBody').html("");
				$('#tableResumeFYFoot').html("");
				var tableResumeFYBody = "";
				var tableResumeFYFoot = "";
				var totalFYct = 0;
				var totalFYcd = 0;
				var totalFYsp = 0;
				var totalFYmt = 0;

				$.each(resumefy_cd, function(key, value){
					var a = key.split('__');
					tableResumeFYBody += '<tr>';
					tableResumeFYBody += '<td>'+a[0]+'</td>';
					tableResumeFYBody += '<td style="text-align: right;">'+a[1]+'</td>';
					$.each(resumefy_ct, function(key,value){
						var b = key.split('__');
						if(a[0] == b[0] && a[1] == b[1]){
							tableResumeFYBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
							totalFYct += value;
						}
					});
					tableResumeFYBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
					totalFYcd += value;
					$.each(resumefy_sp, function(key,value){
						var b = key.split('__');
						if(a[0] == b[0] && a[1] == b[1]){
							tableResumeFYBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
							totalFYsp += value;
						}
					});
					$.each(resumefy_mt, function(key,value){
						var b = key.split('__');
						if(a[0] == b[0] && a[1] == b[1]){
							tableResumeFYBody += '<td style="text-align: right;">'+value/60+'</td>';
							totalFYmt += value/60;
						}
					});
					tableResumeFYBody += '</tr>';
				});

				tableResumeFYFoot += '<tr>';
				tableResumeFYFoot += '<th style="text-align: right;" colspan="2">Subtotal: </th>';
				tableResumeFYFoot += '<th style="text-align: right;">'+totalFYct.toLocaleString()+'</th>';
				tableResumeFYFoot += '<th style="text-align: right;">'+totalFYcd.toLocaleString()+'</th>';
				tableResumeFYFoot += '<th style="text-align: right;">'+totalFYsp.toLocaleString()+'</th>';
				tableResumeFYFoot += '<th style="text-align: right;">'+totalFYmt.toLocaleString()+'</th>';
				tableResumeFYFoot += '</tr>';

				$('#tableResumeFYBody').append(tableResumeFYBody);
				$('#tableResumeFYFoot').append(tableResumeFYFoot);

				$('#tableResumeFY2Body').html("");
				var tableResumeFY2Body = "";

				$.each(resumefy_category, function(key,value){
					var a = key.split('__');
					tableResumeFY2Body += '<tr>';
					tableResumeFY2Body += '<td style="text-align: right;">'+a[0]+'</td>';
					tableResumeFY2Body += '<td style="text-align: right;">'+a[1]+'</td>';
					tableResumeFY2Body += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
					tableResumeFY2Body += '</tr>';
				});

				$('#tableResumeFY2Body').append(tableResumeFY2Body);

				$('#tableResumeDepartmentBody').html("");
				var tableResumeDepartmentBody = "";

				$.each(resumedept_cd, function(key, value){
					var a = key;
					tableResumeDepartmentBody += '<tr>';
					tableResumeDepartmentBody += '<td>'+a+'</td>';
					$.each(resumedept_ct, function(key,value){
						var b = key;
						if(a == b){
							tableResumeDepartmentBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
						}
					});
					tableResumeDepartmentBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
					$.each(resumedept_sp, function(key,value){
						var b = key;
						if(a == b){
							tableResumeDepartmentBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
						}
					});
					$.each(resumedept_mt, function(key,value){
						var b = key;
						if(a == b){
							tableResumeDepartmentBody += '<td style="text-align: right;">'+value/60+'</td>';
						}
					});
					tableResumeDepartmentBody += '</tr>';
				});

				$('#tableResumeDepartmentBody').append(tableResumeDepartmentBody);

				$('#tableResumeProjectBody').html("");
				var tableResumeProjectBody = "";

				$.each(result.man_times, function(key, value){
					var a = value.ticket_id;
					tableResumeProjectBody += '<tr>';
					tableResumeProjectBody += '<td>'+value.ticket_id+'</td>';
					tableResumeProjectBody += '<td>'+value.department_shortname+'</td>';
					tableResumeProjectBody += '<td>'+value.project_name+'</td>';
					tableResumeProjectBody += '<td>'+value.pic_shortname+'</td>';
					tableResumeProjectBody += '<td>'+value.pic+'</td>';
					tableResumeProjectBody += '<td>'+value.estimated_due_date_from+'</td>';
					tableResumeProjectBody += '<td>'+value.estimated_due_date_to+'</td>';
					tableResumeProjectBody += '<td>'+value.progress+'%</td>';
					$.each(resumeproject_cd, function(key,value){
						var b = key;
						if(a == b){
							tableResumeProjectBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
						}
					});
					$.each(resumeproject_sp, function(key,value){
						var b = key;
						if(a == b){
							tableResumeProjectBody += '<td style="text-align: right;">'+value.toLocaleString()+'</td>';
						}
					});
					tableResumeProjectBody += '<td>'+value.duration/60+'</td>';
					tableResumeProjectBody += '</tr>';
				});

				$('#tableResumeProjectBody').append(tableResumeProjectBody);

				$('#tableResumeFY').DataTable({
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
						]
					},
					'paging': false,
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

				$('#tableResumeFY2').DataTable({
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
						]
					},
					'paging': false,
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

				$('#tableResumeDepartment').DataTable({
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
						]
					},
					'paging': false,
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

				$('#tableResumeProject').DataTable({
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

			}
			else{
				alert('Attempt to retrieve data failed');
				audio_error.play();
				return false;
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