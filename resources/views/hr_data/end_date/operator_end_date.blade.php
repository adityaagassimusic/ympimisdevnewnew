@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.table > tbody > tr:hover {
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
		height: 30px;
		padding:  2px 5px 2px 5px;
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
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait .. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

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
	<div class="box box">
		<div class="box-body">
			<div class="nav-tabs-custom">
				<!-- <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active" style="width: 50%"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Data Sunfish</a></li>
					<li class="vendor-tab" style="width: 50%"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Data Mirai</a></li>
				</ul> -->
				<!-- <div class="tab-content"> -->

					<!-- <div class="tab-pane active" id="tab_1"> -->
						<div class="col-xs-10" style="padding-bottom: 10px; padding-top: 7px" align="right">
							<label>Select FY</label>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px" align="center">
							<select class="form-control select2" name="select_fy" id='select_fy' style="width: 100%;" onchange="Grafik(this.value)" data-placeholder="Select FY">
								<option value=""></option>
								@foreach($fy as $fy)
								<option value="{{ $fy->fiscal_year }}">{{ $fy->fiscal_year }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-12">
							<div id="grafik-data-sunfish" style="width: 100%; height: 50vh; margin-bottom: 10px; border: 1px solid black;"></div>
						</div>
						<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 10px; padding-left: 5px">
							<span style="font-weight: bold; font-size: 1.6vw;">Detail Employee End Contract</span>
						</div>
						<div class="col-xs-4" style="padding-bottom: 10px" align="center">
							<label>Department</label>
							<select class="form-control select2" name="department_sunfish" id='department_sunfish' data-placeholder="Select Department" style="width: 100%;">
								<option value=""></option>
								@foreach($department as $dept)
								<option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px" align="center">
							<label>Month From</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="month_join_sunfish" data-placeholder="Select Month">
							</div>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px" align="center">
							<label>Month To</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="month_end_sunfish" data-placeholder="Select Month">
							</div>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
							<button class="btn btn-primary" style="width: 100%" onclick="DataList()"><i class="fa fa-search" aria-hidden="true"> Search</i></button>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
							<button class="btn btn-warning" style="width: 100%" onclick="Clear()"><i class="fa fa-eraser" aria-hidden="true"> Clear</i></button>
						</div>
						<div class="col-xs-12">
							<table id="TableListSunfish" class="table table-bordered table-striped table-hover">
								<thead style="background-color: #BDD5EA; color: black;">
									<tr>
										<th width="5%" style="text-align: center;">No</th>
										<th width="10%">Employee ID</th>
										<th width="15%">Name</th>
										<th width="10%">Department</th>
										<th width="10%">Section</th>
										<th width="10%">Group</th>
										<th width="10%">Sub Group</th>
										<th width="10%">Status</th>
										<th width="10%" style="text-align: right;">Join Date</th>
										<th width="10%" style="text-align: right;">End Date</th>
									</tr>
								</thead>
								<tbody id="bodyTableListSunfish">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					<!-- </div> -->

					<!-- <div class="tab-pane" id="tab_2">
						<div class="col-xs-12">
							<div id="grafik-data-mirai" style="width: 100% !important; height: 50vh; margin-bottom: 10px; border: 1px solid black;"></div>
						</div>
						<div class="col-xs-12" style="background-color: #78a1d0; text-align: center; margin-bottom: 10px;">
							<span style="font-weight: bold; font-size: 1.6vw;">Employee Data Mirai</span>
						</div>
						<div class="col-xs-4" style="padding-bottom: 10px" align="center">
							<label>Department</label>
							<select class="form-control select2" name="department_mirai" id='department_mirai' data-placeholder="Select Department" style="width: 100%;">
								<option value=""></option>
								@foreach($department as $dept)
								<option value="{{ $dept->department_name }}">{{ $dept->department_name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px" align="center">
							<label>Month Join Date</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="month_join_mirai" data-placeholder="Select Month">
							</div>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px" align="center">
							<label>Month End Date</label>
							<div class="input-group date">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control pull-right" id="month_end_mirai" data-placeholder="Select Month">
							</div>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
							<button class="btn btn-primary" style="width: 100%" onclick="DataMirai()">Search</button>
						</div>
						<div class="col-xs-2" style="padding-bottom: 10px; padding-top: 25px" align="center">
							<button class="btn btn-warning" style="width: 100%" onclick="Clear()">Clear</button>
						</div>
						<div class="col-xs-12">
							<table id="TableListMirai" class="table table-bordered table-striped table-hover">
								<thead style="background-color: #BDD5EA; color: black;">
									<tr>
										<th width="5%" style="text-align: center;">No</th>
										<th width="10%" style="text-align: center;">Employee ID</th>
										<th width="15%" style="text-align: center;">Name</th>
										<th width="10%" style="text-align: center;">Department</th>
										<th width="10%" style="text-align: center;">Section</th>
										<th width="10%" style="text-align: center;">Group</th>
										<th width="10%" style="text-align: center;">Sub Group</th>
										<th width="10%" style="text-align: center;">Employee Code</th>
										<th width="10%" style="text-align: center;">Join Date</th>
										<th width="10%" style="text-align: center;">Actual End Date</th>
									</tr>
								</thead>
								<tbody id="bodyTableListMirai">
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div> -->

				<!-- </div> -->
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		$('#month_join_sunfish').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#month_end_sunfish').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#month_join_mirai').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		$('#month_end_mirai').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
		Grafik();
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

	function DataList(jenis, bulan){
		$("#loading").show();

		var date = new Date(bulan);
		var nama_bulan = date.getMonth();
		switch(nama_bulan) {
		case 0: nama_bulan = "January"; break;
		case 1: nama_bulan = "February"; break;
		case 2: nama_bulan = "March"; break;
		case 3: nama_bulan = "April"; break;
		case 4: nama_bulan = "May"; break;
		case 5: nama_bulan = "June"; break;
		case 6: nama_bulan = "July"; break;
		case 7: nama_bulan = "August"; break;
		case 8: nama_bulan = "September"; break;
		case 9: nama_bulan = "October"; break;
		case 10: nama_bulan = "November"; break;
		case 11: nama_bulan = "December"; break;
		}

		// $("#judul_biru").html('Periode End '+nama_bulan);
		if (jenis == 'Data Grafik') {
			var month_end_sunfish = bulan;
		}else{
			var month_end_sunfish = $('#month_end_sunfish').val();
		}
		var department_sunfish = $('#department_sunfish').val();
		var month_join_sunfish = $('#month_join_sunfish').val();

		var kode = 'Cek Data Sunfish';

		var data = {
			department_sunfish:department_sunfish,
			month_join_sunfish:month_join_sunfish,
			month_end_sunfish:month_end_sunfish,
			kode:kode,
			jenis:jenis
		}


		$.get('{{ url("fetch/employee_end_contract") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', 'Success !');
				$('#TableListSunfish').DataTable().clear();
				$('#TableListSunfish').DataTable().destroy();
				$('#bodyTableListSunfish').html("");
				var tableData = "";
				var index = 1;
				$.each(result.data, function(key, value) {

					tableData += '<tr>';
					tableData += '<td style="text-align: center">'+ index++ +'</td>';
					tableData += '<td>'+ value.Emp_no +'</td>';
					tableData += '<td>'+ value.Full_name +'</td>';
					tableData += '<td>'+ value.Department +'</td>';
					tableData += '<td>'+ value.SECTION +'</td>';
					tableData += '<td>'+ (value.Groups||'') +'</td>';
					tableData += '<td>'+ (value.Sub_Groups||'') +'</td>';
					tableData += '<td>'+ value.employ_code +'</td>';
					tableData += '<td style="text-align: right">'+ (value.start_date||'') +'</td>';
					tableData += '<td style="text-align: right">'+ (value.employment_enddate||'') +'</td>';
					tableData += '</tr>';

				});
				$('#bodyTableListSunfish').append(tableData);

				var table = $('#TableListSunfish').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
					'buttons': {
						buttons:[{
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
						}]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'DataListing': true	,
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
			}
		});
	}

	// function DataMirai(bulan, jenis){
	// 	$("#loading").show();
	// 	if (jenis == 'Data Grafik') {
	// 		switch(bulan) {
	// 		case 'January': bulan = "01"; break;
	// 		case 'February': bulan = "02"; break;
	// 		case 'March': bulan = "03"; break;
	// 		case 'April': bulan = "04"; break;
	// 		case 'May': bulan = "05"; break;
	// 		case 'June': bulan = "06"; break;
	// 		case 'July': bulan = "07"; break;
	// 		case 'August': bulan = "08"; break;
	// 		case 'September': bulan = "09"; break;
	// 		case 'October': bulan = "10"; break;
	// 		case 'November': bulan = "11"; break;
	// 		case 'December': bulan = "12"; break;
	// 		}

	// 		var tahun = new Date().getFullYear();
	// 		var month_end_mirai = tahun+'-'+bulan;
	// 	}else{
	// 		var month_end_mirai = $('#month_end_mirai').val();
	// 	}
	// 	var department_mirai = $('#department_mirai').val();
	// 	var month_join_mirai = $('#month_join_mirai').val();
	// 	// var month_end_mirai = $('#month_end_mirai').val();
	// 	var kode = 'Cek Data Mirai';

	// 	var data = {
	// 		department_mirai:department_mirai,
	// 		month_join_mirai:month_join_mirai,
	// 		month_end_mirai:month_end_mirai,
	// 		kode:kode
	// 	}


	// 	$.get('{{ url("fetch/employee_end_contract") }}', data, function(result, status, xhr){
	// 		if(result.status){
	// 			$("#loading").hide();
	// 			openSuccessGritter('Success', 'Success !');
	// 			$('#TableListMirai').DataTable().clear();
	// 			$('#TableListMirai').DataTable().destroy();
	// 			$('#bodyTableListMirai').html("");
	// 			var tableData = "";
	// 			var index = 1;
	// 			$.each(result.data, function(key, value) {

	// 				tableData += '<tr>';
	// 				tableData += '<td style="text-align: center">'+ index++ +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.employee_id +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.name +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.department +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.section +'</td>';
	// 				tableData += '<td style="text-align: center">'+ (value.group||'-') +'</td>';
	// 				tableData += '<td style="text-align: center">'+ (value.sub_group||'-') +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.employment_status +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.hire_date +'</td>';
	// 				tableData += '<td style="text-align: center">'+ value.end_date +'</td>';
	// 				tableData += '</tr>';

	// 			});
	// 			$('#bodyTableListMirai').append(tableData);

	// 			var table = $('#TableListMirai').DataTable({
	// 				'dom': 'Bfrtip',
	// 				'responsive':true,
	// 				'lengthMenu': [
	// 					[ 10, 25, 50, -1 ],
	// 					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 					],
	// 				'buttons': {
	// 					buttons:[{
	// 						extend: 'pageLength',
	// 						className: 'btn btn-default',
	// 					}]
	// 				},
	// 				'paging': true,
	// 				'lengthChange': true,
	// 				'pageLength': 10,
	// 				'DataListing': true	,
	// 				'ordering': true,
	// 				'order': [],
	// 				'info': true,
	// 				'autoWidth': true,
	// 				"sPaginationType": "full_numbers",
	// 				"bJQueryUI": true,
	// 				"bAutoWidth": false,
	// 				"processing": true
	// 			});
	// 		}
	// 		else{
	// 			alert('Attempt to retrieve data failed');
	// 		}
	// 	});
	// }

	function Clear(){
		location.reload(true);
	}

	function Grafik(fy){
		$("#loading").show();
		var p = '';
		if (fy == 'undefined') {
			p = '';
		}else{
			p = fy;
		}
		var data = {
			fy:p
		}
		$.get('{{ url("grafik/employee_end_contract") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				// var bulan = [];
				// var jumlah = [];

				// for (var i = 0; i < result.data.length; i++) {
				// 	var date = new Date(result.data[i].bulan);
				// 	var nama_bulan = date.getMonth();
				// 	switch(nama_bulan) {
				// 	case 0: nama_bulan = "January"; break;
				// 	case 1: nama_bulan = "February"; break;
				// 	case 2: nama_bulan = "March"; break;
				// 	case 3: nama_bulan = "April"; break;
				// 	case 4: nama_bulan = "May"; break;
				// 	case 5: nama_bulan = "June"; break;
				// 	case 6: nama_bulan = "July"; break;
				// 	case 7: nama_bulan = "August"; break;
				// 	case 8: nama_bulan = "September"; break;
				// 	case 9: nama_bulan = "October"; break;
				// 	case 10: nama_bulan = "November"; break;
				// 	case 11: nama_bulan = "December"; break;
				// 	}

				// 	bulan.push(nama_bulan);
				// 	jumlah.push(parseInt(result.data[i].jumlah));
				// }


				var categori = [];
				var series = [];
				var year = result.fy;

				$.each(result.wc, function(key, value){
					var isi = 0;
					var date = new Date(value.bulan);
					var nama_bulan = date.getMonth();
					switch(nama_bulan) {
					case 0: nama_bulan = "January"; break;
					case 1: nama_bulan = "February"; break;
					case 2: nama_bulan = "March"; break;
					case 3: nama_bulan = "April"; break;
					case 4: nama_bulan = "May"; break;
					case 5: nama_bulan = "June"; break;
					case 6: nama_bulan = "July"; break;
					case 7: nama_bulan = "August"; break;
					case 8: nama_bulan = "September"; break;
					case 9: nama_bulan = "October"; break;
					case 10: nama_bulan = "November"; break;
					case 11: nama_bulan = "December"; break;
					}
					
					categori.push(nama_bulan);

					$.each(result.data, function(key2, value2){
						if (value.bulan == value2.bulan) {
							series.push({y:parseInt(value2.jumlah), key: value.bulan});
							isi = 1;
						}
					});
					if (isi == 0) {
						series.push(0);
					}
				});

				// console.log(series);

				// var bulan_mirai = [];
				// var jumlah_mirai = [];

				// for (var i = 0; i < result.data_mirai.length; i++) {
				// 	var date = new Date(result.data_mirai[i].bulan);
				// 	var nama_bulan_mirai = date.getMonth();
				// 	switch(nama_bulan_mirai) {
				// 	case 0: nama_bulan_mirai = "January"; break;
				// 	case 1: nama_bulan_mirai = "February"; break;
				// 	case 2: nama_bulan_mirai = "March"; break;
				// 	case 3: nama_bulan_mirai = "April"; break;
				// 	case 4: nama_bulan_mirai = "May"; break;
				// 	case 5: nama_bulan_mirai = "June"; break;
				// 	case 6: nama_bulan_mirai = "July"; break;
				// 	case 7: nama_bulan_mirai = "August"; break;
				// 	case 8: nama_bulan_mirai = "September"; break;
				// 	case 9: nama_bulan_mirai = "October"; break;
				// 	case 10: nama_bulan_mirai = "November"; break;
				// 	case 11: nama_bulan_mirai = "December"; break;
				// 	}

				// 	bulan_mirai.push(nama_bulan_mirai);
				// 	jumlah_mirai.push(parseInt(result.data_mirai[i].jumlah));
				// }

				Highcharts.chart('grafik-data-sunfish', {
					chart: {
						scrollablePlotArea: {
							minWidth: 700
						}
						// width:1110
					},
					title: {
						text: 'Monitoring Employee End Contract <br>'+year
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						// categories: bulan,
						categories: categori,
						crosshair: true
					},
					yAxis: [{
						title: {
							text: ''
						}
					}],
					legend: {
						borderWidth: 1
					},
					tooltip: {
						backgroundColor: '#FCFFC5',
						borderColor: 'black',
						borderRadius: 5,
						borderWidth: 1
					},
					plotOptions: {
						column: {
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121'
						},
						series: {
							dataLabels: {
								enabled: true
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										DataList('Data Grafik', this.options.key);
									}
								}
							}
						}
					},
					series: [{
						name: 'Qty Employee',
						type: 'column',
						// data: jumlah,
						data: series,
						color: '#f9a825'
					}]
				});

				// Highcharts.chart('grafik-data-mirai', {
				// 	chart: {
				// 		width:1110
				// 	},
				// 	title: {
				// 		text: 'Grafik Actual Employee End Contract <br> Tahun '+year
				// 	},
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	xAxis: {
				// 		tickInterval: 1,
				// 		gridLineWidth: 1,
				// 		categories: bulan_mirai,
				// 		crosshair: true
				// 	},
				// 	yAxis: [{
				// 		title: {
				// 			text: ''
				// 		}
				// 	}],
				// 	legend: {
				// 		borderWidth: 1
				// 	},
				// 	tooltip: {
				// 		backgroundColor: '#FCFFC5',
				// 		borderColor: 'black',
				// 		borderRadius: 5,
				// 		borderWidth: 1
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			pointPadding: 0.93,
				// 			groupPadding: 0.93,
				// 			borderWidth: 0.8,
				// 			borderColor: '#212121'
				// 		},
				// 		series: {
				// 			dataLabels: {
				// 				enabled: true
				// 			},
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						DataMirai(this.category, 'Data Grafik');
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},
				// 	series: [{
				// 		name: 'Qty',
				// 		type: 'column',
				// 		data: jumlah_mirai,
				// 		color: '#f9a825'
				// 	}]
				// });

			}else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function DetailGrafikPriority(a, b, c){
		console.log(a,b,c);
	}
</script>
@endsection