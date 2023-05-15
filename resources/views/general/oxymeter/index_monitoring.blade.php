@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
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
		font-size: 18px;
		padding-top: 1px;
		padding-bottom: 1px;
		border:1px solid black;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		font-size: 16px;
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		background-color: #8CD790;
		color: #000;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 16px;
		border:1px solid black;
		background-color: #ffffc2;
	}

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff0033;
			color: white;
		}
		50%, 100% {
			background-color: #ffccff;
		}
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	div#tableDetail_info.dataTables_info,
	div#tableDetail_filter.dataTables_filter label,
	div#tableDetail_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetail_info.dataTables_info,
	#tableDetail_info.dataTables_length {
		color: black;
	}

	div#tableDetailCheck_info.dataTables_info,
	div#tableDetailCheck_filter.dataTables_filter label,
	div#tableDetailCheck_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailCheck_info.dataTables_info,
	#tableDetailCheck_info.dataTables_length {
		color: black;
	}

	#tableTotalOfc tr td {
		cursor: pointer;
	}

	#tableTotalPrd tr td {
		cursor: pointer;
	}

	.alert {
		-webkit-animation: fade 1s infinite;  /* Safari 4+ */
		-moz-animation: fade 1s infinite;  /* Fx 5+ */
		-o-animation: fade 1s infinite;  /* Opera 12+ */
		animation: fade 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes fade {
		0%, 49% {
			background-color: #8cd790;
		}
		50%, 100% {
			background-color: #ed5c64;
		}
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px; padding-left: 0px;">
			<div class="row">
				<form method="GET" action="{{ action('TemperatureController@indexBodyTempMonitoring') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date" onchange="draw_data()">
						</div>
					</div>
					<div style="margin: 0px;padding-top: 0px;padding-right: 1vw	;font-size: 25px;color: white; font-weight: bold">
						<center>OXYGEN AND HEART RATE MONITORING on <span id='tgl'></span></center></div>
					</form>
				</div>
			</div>
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-5">
						<span style="color: white; font-size: 1.5vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Office</span>
						<table class="table table-bordered" id="tableTotalOfc" style="margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Shift Schedule</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Checked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Unchecked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Total</th>
								</tr>
							</thead>
							<tbody id="tableTotalBodyOfc">
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Shift 1</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_ofc_1"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_ofc_1"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_ofc_1"></td>
								</tr>
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Shift 2</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_ofc_2"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_ofc_2"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_ofc_2"></td>
								</tr>
							</tbody>
						</table>

						<span style="color: white; font-size: 1.5vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Production</span>
						<table class="table table-bordered" id="tableTotalPrd" style="margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Shift Schedule</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Checked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Unchecked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Total</th>
								</tr>
							</thead>
							<tbody id="tableTotalBodyPrd">
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Shift 1</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_prd_1"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_prd_1"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_prd_1"></td>
								</tr>
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Shift 2</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_prd_2"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_prd_2"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_prd_2"></td>
								</tr>
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Shift 3</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_prd_3"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_prd_3"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_prd_3"></td>
								</tr>
								<tr>
									<td style="font-size: 1vw; font-weight: bold;color: black;" id="">OTHERS (OS,DRV,SEC,CTN)</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check_os"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck_os"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person_os"></td>
								</tr>
							</tbody>
						</table>
						<span style="color: white; font-size: 1.5vw; font-weight: bold;"><i class="fa fa-caret-right"></i> TOTAL</span>
						<table class="table table-bordered" id="tableAll" style="margin-bottom: 5px;">
							<thead style="color: white">
								<tr>
									<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">#</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Checked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Unchecked</th>
									<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Total</th>
								</tr>			
							</thead>
							<tbody id="bodyAll">
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Total</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_check"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_uncheck"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="total_person"></td>
								</tr>
								<tr>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="">Percentage</td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="prc_check"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="prc_uncheck"></td>
									<td style="font-size: 1.2vw; font-weight: bold;color: black;" id="prc_person"></td>
								</tr>
							</tbody>
						</table>
						<span style="color: white; font-size: 1.5vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Oxygen Rate < 95</span>
						<table class="table table-bordered" id="tableAbnormal" style="margin-bottom: 5px;">
							<thead style="color: white">
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 3%;">ID</th>
									<th style="width: 9%;">Name</th>
									<th style="width: 3%;">Dept</th>
									<th style="width: 3%;">Shift</th>
									<th style="width: 2%;">Oxy</th>
									<th style="width: 2%;">Status</th>
								</tr>			
							</thead>
							<tbody id="bodyAbnormal">
							</tbody>
						</table>

						<span style="color: white; font-size: 1.5vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Heart Rate < 90 and Heart Rate > 120</span>
						<table class="table table-bordered" id="tableAbnormalPulse" style="margin-bottom: 5px;">
							<thead style="color: white">
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 3%;">ID</th>
									<th style="width: 9%;">Name</th>
									<th style="width: 3%;">Dept</th>
									<th style="width: 3%;">Shift</th>
									<th style="width: 2%;">Heart</th>
									<th style="width: 2%;">Status</th>
								</tr>			
							</thead>
							<tbody id="AbnormalPulse">
							</tbody>
						</table>
					</div>
					<div class="col-xs-7">
						<div id="chart" style="width: 100%;height: 300px"></div>
						<div id="chart2" style="width: 100%;height: 300px"></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitle"></h4>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<center>
							<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
						</center>
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="width: 1%;">#</th>
									<th style="width: 3%;">ID</th>
									<th style="width: 9%;">Name</th>
									<th style="width: 9%;">Dept</th>
									<th style="width: 9%;">Sect</th>
									<th style="width: 9%;">Group</th>
									<th style="width: 9%;">Point</th>
									<th style="width: 3%;">Time</th>
									<th style="width: 2%;">Oxy</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetailCheck">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitleCheck"></h4>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<table class="table table-hover table-bordered table-striped" id="tableDetailCheck">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr style="color: white">
									<th style="color:white;width: 1%; font-size: 1.2vw;">#</th>
									<th style="color:white;width: 5%; font-size: 1.2vw; text-align: center;">ID</th>
									<th style="color:white;width: 30%; font-size: 1.2vw; text-align: center;">Name</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Dept</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Sect</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Group</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Shift</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Oxygen Rate</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Hearth Rate</th>
									<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Check Time</th>
								</tr>
							</thead>
							<tbody id="tableDetailCheckBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection
	@section('scripts')
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
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

		jQuery(document).ready(function() {
			$('.datepicker').datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				todayHighlight: true,	
				endDate: new Date()
			});

			$('.select2').select2();

			draw_data();
			setInterval(draw_data, 300000);
		});

		function draw_data() {
			var data = {
				dt : $("#tanggal_from").val()
			}

			$.get('{{ url("fetch/general/oxymeter/data") }}', data, function(result, status, xhr){
				var xCategories = [];
				var xCategories2 = [];
				var xSeries = [];
				var xSeriesPulse = [];
				var xColor = [];

				$("#tgl").text(result.date);

			// total_check_ofc_1
			// total_uncheck_ofc_1
			// total_person_ofc_1

			for (var i = 71; i <= 120; i++) {
				xCategories2.push(i);

				if (i >= 80 && i <= 100) {
					xCategories.push(i);
					var stat = 0;

					$.each(result.oxy_datas, function(index, value){
						if (parseInt(value.remark) == i) {
							stat = 1;
							xSeries.push(value.qty);
						}
					})

					if (stat == 0) {
						xSeries.push(0);
					}
				}

				

				// ---------  Pulse ----------

				var stat2 = 0;
				$.each(result.pulse_datas, function(index, value){
					if (parseInt(value.remark2) == i) {
						stat2 = 1;
						xSeriesPulse.push(value.qty);
					}
				})

				if (stat2 == 0) {
					xSeriesPulse.push(0);
				}
			}

			// --------------   CHECKED TABLE   -------------------

			$("#total_check_ofc_1").empty();
			$("#total_uncheck_ofc_1").empty();
			$("#total_person_ofc_1").empty();

			$("#total_check_ofc_2").empty();
			$("#total_uncheck_ofc_2").empty();
			$("#total_person_ofc_2").empty();

			$("#total_check_prd_1").empty();
			$("#total_uncheck_prd_1").empty();
			$("#total_person_prd_1").empty();

			$("#total_check_prd_2").empty();
			$("#total_uncheck_prd_2").empty();
			$("#total_person_prd_2").empty();

			$("#total_check_prd_3").empty();
			$("#total_uncheck_prd_3").empty();
			$("#total_person_prd_3").empty();

			$("#total_check_os").empty();
			$("#total_uncheck_os").empty();
			$("#total_person_os").empty();

			$("#total_check").empty();
			$("#total_uncheck").empty();
			$("#total_person").empty();

			$("#prc_check").empty();
			$("#prc_uncheck").empty();
			$("#prc_person").empty();

			var ofc_cek_1 = 0;
			var ofc_uncek_1 = 0;
			var ofc_total_1 = 0;

			var ofc_cek_2 = 0;
			var ofc_uncek_2 = 0;
			var ofc_total_2 = 0;

			var prd_cek_1 = 0;
			var prd_uncek_1 = 0;
			var prd_total_1 = 0;

			var prd_cek_2 = 0;
			var prd_uncek_2 = 0;
			var prd_total_2 = 0;

			var prd_cek_3 = 0;
			var prd_uncek_3 = 0;
			var prd_total_3 = 0;

			var os_cek = 0;
			var os_uncek = 0;
			var os_total = 0;

			var below_rate = [];
			var below_rate_pulse = [];


			var detail_check_ofc_1 = [];
			var detail_uncheck_ofc_1 = [];
			var detail_total_ofc_1 = [];

			var detail_check_ofc_2 = [];
			var detail_uncheck_ofc_2 = [];
			var detail_total_ofc_2 = [];

			var detail_check_prd_1 = [];
			var detail_uncheck_prd_1 = [];
			var detail_total_prd_1 = [];

			var detail_check_prd_2 = [];
			var detail_uncheck_prd_2 = [];
			var detail_total_prd_2 = [];

			var detail_check_prd_3 = [];
			var detail_uncheck_prd_3 = [];
			var detail_total_prd_3 = [];

			var detail_check_os = [];
			var detail_uncheck_os = [];
			var detail_total_os = [];


			$.each(result.shift, function(index, value){
				if (value.remark == 'OFC' || value.remark == 'Jps') {
					if (value.remark == 'OFC' || value.remark == 'Jps') {
						if (value.shiftdaily_code.match(/Shift_1/gi)) {
							ofc_total_1++;
							if (value.oxy != null) {
								ofc_cek_1++;
								detail_check_ofc_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy, 'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								ofc_uncek_1++;
								detail_uncheck_ofc_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}

							detail_total_ofc_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						} else if (value.shiftdaily_code.match(/Shift_2/gi)) {
							ofc_total_2++;
							if (value.oxy != null) {
								ofc_cek_2++;
								detail_check_ofc_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								ofc_uncek_2++;
								detail_uncheck_ofc_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}

							detail_total_ofc_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						}
					}
				} else {
					if (value.section == 'GA Control Section' || value.section == 'Industrial Relation Section') {
						os_total++;
						if (value.oxy != null) {
							os_cek++;
							detail_check_os.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
						} else {
							os_uncek++;
							detail_uncheck_os.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
						}
						detail_total_os.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
					} else {
						if (value.shiftdaily_code.match(/Shift_1/gi)) {
							prd_total_1++;
							if (value.oxy != null) {
								prd_cek_1++;
								detail_check_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								prd_uncek_1++;
								detail_uncheck_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}
							detail_total_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						} else if (value.shiftdaily_code.match(/Shift_2/gi)) {
							prd_total_2++;
							if (value.oxy != null) {
								prd_cek_2++;
								detail_check_prd_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								prd_uncek_2++;
								detail_uncheck_prd_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}
							detail_total_prd_2.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						}  else if (value.shiftdaily_code.match(/Shift_3/gi)) {
							prd_total_3++;
							if (value.oxy != null) {
								prd_cek_3++;
								detail_check_prd_3.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								prd_uncek_3++;
								detail_uncheck_prd_3.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}
							detail_total_prd_3.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						} else {
							prd_total_1++;
							if (value.oxy != null) {
								prd_cek_1++;
								detail_check_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							} else {
								prd_uncek_1++;
								detail_uncheck_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.group});
							}
							detail_total_prd_1.push({'employee_id': value.employee_id,'name':value.name, 'dept': value.department_shortname, 'shift': value.shiftdaily_code, 'oxy':value.oxy,  'pulse':value.pulse, 'check_time':value.check_time, 'section':value.section, 'group':value.groups});
						}
					}
				}


				if (parseInt(value.oxy) < 95) {
					below_rate.push({'emp_id': value.employee_id, 'name': value.name, 'shift': value.shiftdaily_code, 'oxy': value.oxy, 'dept': value.department_shortname, 'status': value.status});
				}

				if (parseInt(value.pulse) > 120 ) {
					below_rate_pulse.push({'emp_id': value.employee_id, 'name': value.name, 'shift': value.shiftdaily_code, 'pulse': value.pulse, 'dept': value.department_shortname, 'status': value.status});
				}

				
			})


$("#total_check_ofc_1").text(ofc_cek_1);
$("#total_uncheck_ofc_1").text(ofc_uncek_1);
$("#total_person_ofc_1").text(ofc_total_1);

$("#total_check_ofc_2").text(ofc_cek_2);
$("#total_uncheck_ofc_2").text(ofc_uncek_2);
$("#total_person_ofc_2").text(ofc_total_2);

$("#total_check_prd_1").text(prd_cek_1);
$("#total_uncheck_prd_1").text(prd_uncek_1);
$("#total_person_prd_1").text(prd_total_1);

$("#total_check_prd_2").text(prd_cek_2);
$("#total_uncheck_prd_2").text(prd_uncek_2);
$("#total_person_prd_2").text(prd_total_2);

$("#total_check_prd_3").text(prd_cek_3);
$("#total_uncheck_prd_3").text(prd_uncek_3);
$("#total_person_prd_3").text(prd_total_3);

$("#total_check_os").text(os_cek);
$("#total_uncheck_os").text(os_uncek);
$("#total_person_os").text(os_total);

var total_all = ofc_total_1+ofc_total_2+prd_total_1+prd_total_2+prd_total_3+os_total;

$("#total_check").text((ofc_cek_1+ofc_cek_2+prd_cek_1+prd_cek_2+prd_cek_3+os_cek));
$("#total_uncheck").text((ofc_uncek_1+ofc_uncek_2+prd_uncek_1+prd_uncek_2+prd_uncek_3+os_uncek));
$("#total_person").text((ofc_total_1+ofc_total_2+prd_total_1+prd_total_2+prd_total_3+os_total));

$("#prc_check").text(((ofc_cek_1+ofc_cek_2+prd_cek_1+prd_cek_2+prd_cek_3+os_cek) / total_all * 100).toFixed(1)+" %");
$("#prc_uncheck").text(((ofc_uncek_1+ofc_uncek_2+prd_uncek_1+prd_uncek_2+prd_uncek_3+os_uncek) / total_all * 100).toFixed(1)+" %");
$("#prc_person").text(((ofc_total_1+ofc_total_2+prd_total_1+prd_total_2+prd_total_3+os_total) / total_all * 100).toFixed(1)+" %");

			// ------------------   ADD CLICK LISTENER  ---------------------

			var elem_total_check_ofc_1 = document.getElementById('total_check_ofc_1');

			elem_total_check_ofc_1.addEventListener('click', function(){
				checkDetails(detail_check_ofc_1);
			});

			var elem_total_uncheck_ofc_1 = document.getElementById('total_uncheck_ofc_1');

			elem_total_uncheck_ofc_1.addEventListener('click', function(){
				checkDetails(detail_uncheck_ofc_1);
			});

			var elem_total_person_ofc_1 = document.getElementById('total_person_ofc_1');

			elem_total_person_ofc_1.addEventListener('click', function(){
				checkDetails(detail_total_ofc_1);
			});

			var elem_total_check_ofc_2 = document.getElementById('total_check_ofc_2');

			elem_total_check_ofc_2.addEventListener('click', function(){
				checkDetails(detail_check_ofc_2);
			});

			var elem_total_uncheck_ofc_2 = document.getElementById('total_uncheck_ofc_2');

			elem_total_uncheck_ofc_2.addEventListener('click', function(){
				checkDetails(detail_uncheck_ofc_2);
			});

			var elem_total_person_ofc_2 = document.getElementById('total_person_ofc_2');

			elem_total_person_ofc_2.addEventListener('click', function(){
				checkDetails(detail_total_ofc_2);
			});

			
			// -----------------------------------------------


			var elem_total_check_prd_1 = document.getElementById('total_check_prd_1');

			elem_total_check_prd_1.addEventListener('click', function(){
				checkDetails(detail_check_prd_1);
			});

			var elem_total_uncheck_prd_1 = document.getElementById('total_uncheck_prd_1');

			elem_total_uncheck_prd_1.addEventListener('click', function(){
				checkDetails(detail_uncheck_prd_1);
			});

			var elem_total_person_prd_1 = document.getElementById('total_person_prd_1');

			elem_total_person_prd_1.addEventListener('click', function(){
				checkDetails(detail_total_prd_1);
			});

			var elem_total_check_prd_2 = document.getElementById('total_check_prd_2');

			elem_total_check_prd_2.addEventListener('click', function(){
				checkDetails(detail_check_prd_2);
			});

			var elem_total_uncheck_prd_2 = document.getElementById('total_uncheck_prd_2');

			elem_total_uncheck_prd_2.addEventListener('click', function(){
				checkDetails(detail_uncheck_prd_2);
			});

			var elem_total_person_prd_2 = document.getElementById('total_person_prd_2');

			elem_total_person_prd_2.addEventListener('click', function(){
				checkDetails(detail_total_prd_2);
			});

			var elem_total_check_prd_3 = document.getElementById('total_check_prd_3');

			elem_total_check_prd_3.addEventListener('click', function(){
				checkDetails(detail_check_prd_3);
			});

			var elem_total_uncheck_prd_3 = document.getElementById('total_uncheck_prd_3');

			elem_total_uncheck_prd_3.addEventListener('click', function(){
				checkDetails(detail_uncheck_prd_3);
			});

			var elem_total_person_prd_3 = document.getElementById('total_person_prd_3');

			elem_total_person_prd_3.addEventListener('click', function(){
				checkDetails(detail_total_prd_3);
			});



			// ------------------  TABLE BELOW RATE ------------------------
			$("#bodyAbnormal").empty();
			var body = "";

			$.each(below_rate, function(index, value){
				body += "<tr>";
				body += "<td class='alert'>"+(index + 1)+"</td>";
				body += "<td class='alert'>"+value.emp_id+"</td>";
				body += "<td class='alert'>"+value.name+"</td>";
				body += "<td class='alert'>"+value.dept+"</td>";
				body += "<td class='alert'>"+value.shift+"</td>";
				body += "<td class='alert'>"+value.oxy+"</td>";
				
				var clinic = "";
				$.each(result.clinic, function(index2, value2){
					if (value2.employee_id == value.emp_id) {
						clinic = "clinic";
					}
				})

				if (clinic == "") {
					body += "<td class='alert'>"+(value.status || '')+"</td>";
				} else {
					body += "<td class='alert'>"+clinic+"</td>";
				}
				body += "</tr>";
			})

			$("#bodyAbnormal").append(body);

			// ----------------- TABLE BELOW RATE PULSE ------------------

			$("#AbnormalPulse").empty();
			var body = "";

			below_rate_pulse.sort(function(a, b) {
				return parseInt(a.pulse) - parseInt(b.pulse);
			});

			console.log(below_rate_pulse);

			var num = 1;
			for (var i = below_rate_pulse.length - 1; i >= 0; i--) {
				body += "<tr>";
				body += "<td>"+num+"</td>";
				body += "<td>"+below_rate_pulse[i].emp_id+"</td>";
				body += "<td>"+below_rate_pulse[i].name+"</td>";
				body += "<td>"+below_rate_pulse[i].dept+"</td>";
				body += "<td>"+below_rate_pulse[i].shift+"</td>";
				body += "<td>"+below_rate_pulse[i].pulse+"</td>";
				body += "<td>"+below_rate_pulse[i].status+"</td>";
				body += "</tr>";

				num++;
			}

			$("#AbnormalPulse").append(body);


			// ------------------   GRAFIK   ----------------------------
			Highcharts.chart('chart', {
				chart: {
					type: 'column',
					animation: false
				},
				title: {
					text: 'OXYGEN RATE',
					style: {
						fontSize: '20px',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: xCategories,
					// gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					label: {
						style: {
							// fontSize: '20px',
							fontWeight: 'bold'
						},
						step: 1
					},
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Count Person(s)'
					},
					// tickInterval: 1,
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{series.name} <b>{point.key}</b></span><table>',
					pointFormat: '<tr><td style="padding:0"><b>{point.y} Person</b></td></tr>',
					footerFormat: '</table>',
					useHTML: true
				},
				plotOptions: {
					column: {
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}, 
				},

				legend: {
					enabled: false
				},
				
				credits: {
					enabled: false
				},
				series: [{
					name: 'Oxygen',
					data: xSeries
				}]
			});


			Highcharts.chart('chart2', {
				chart: {
					type: 'column',
					animation: false
				},
				title: {
					text: 'HEART RATE',
					style: {
						fontSize: '20px',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: xCategories2,
					// gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					label: {
						style: {
							// fontSize: '20px',
							fontWeight: 'bold'
						},
						step: 1
					},
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Count Person(s)'
					},
					// tickInterval: 1,
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{series.name} <b>{point.key}</b></span><table>',
					pointFormat: '<tr><td style="padding:0"><b>{point.y} Person</b></td></tr>',
					footerFormat: '</table>',
					useHTML: true
				},
				plotOptions: {
					column: {
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}, 
				},

				legend: {
					enabled: false
				},
				
				credits: {
					enabled: false
				},
				series: [{
					name: 'Pulse',
					data: xSeriesPulse,
					color: '#bd67cf'
				}]
			});
		})
}

function checkDetails(param) {
	$('#modalDetailCheck').modal('show');
	$('#modalDetailTitleCheck').html("");

	$('#tableDetailCheckBody').html('');

	$('#tableDetailCheck').DataTable().clear();
	$('#tableDetailCheck').DataTable().destroy();

	var index = 1;
	var resultData = "";
	var total = 0;

	$.each(param, function(key, value) {
		resultData += '<tr>';
		resultData += '<td>'+ index +'</td>';
		resultData += '<td>'+ value.employee_id +'</td>';
		resultData += '<td>'+ value.name +'</td>';
		resultData += '<td>'+ value.dept +'</td>';
		resultData += '<td>'+ (value.section || '') +'</td>';
		resultData += '<td>'+ (value.group || '') +'</td>';
		resultData += '<td>'+ value.shift +'</td>';
		resultData += '<td>'+ (value.oxy || '') +'</td>';
		resultData += '<td>'+ (value.pulse || '') +'</td>';
		resultData += '<td>'+ (value.check_time || '' ) +'</td>';
		resultData += '</tr>';
		index += 1;
	});
	$('#tableDetailCheckBody').append(resultData);
	$('#modalDetailTitleCheck').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees</span></center>");

	var table = $('#tableDetailCheck').DataTable({
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
}
	// STYLE CHART
	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#aaeeee', '#bd67cf', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}
</script>
@endsection
