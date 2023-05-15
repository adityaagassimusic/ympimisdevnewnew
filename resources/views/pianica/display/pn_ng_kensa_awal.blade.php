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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	.gambar {
		width: 100%;
		background-color: none;
		border-radius: 5px;
		margin-left: 0px;
		margin-top: 10px;
		display: inline-block;
		border: 2px solid white;
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
			background: #ff8080;
		}
		50%, 100% {
			background-color: #ffe8e8;
		}
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ action('Pianica@indexDisplayKensaAwal') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<select class="form-control select2" id="line" name="line" style="width: 100%" data-placeholder="Pilih Line">
							<option value=""></option>
							<option value="Line 1">Line 1</option>
							<option value="Line 2">Line 2</option>
							<option value="Line 3">Line 3</option>
							<option value="Line 4">Line 4</option>
							<option value="Line 5">Line 5</option>
						</select>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>

					<div class="col-xs-1 pull-right">
						<button class="btn btn-primary btn-xs pull-right" type="button" onclick="openModal()"><i class="fa fa-pencil"></i> Cek In</button>
					</div>
				</form>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-9">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0px">
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr>
									<td id="lowest_avatar_daily_tuning" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name_daily_tuning" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng_daily_tuning" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr>
									<td id="lowest_avatar_tuning" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name_tuning" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng_tuning" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" id="highest_title_daily">BAD QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr id="not_counceled">
									<td id="not_counceled_td_daily_tuning" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_avatar_daily_tuning" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name_daily_tuning" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_ng_daily_tuning" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" onclick="councelingModalTuning()" class="sedang" id="highest_title_tuning">BAD QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr id="not_counceled_tuning">
									<td onclick="councelingModalTuning()" id="not_counceled_td_tuning" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang">BELUM TRAINING & KONSELING</td>
								</tr>
								<tr>
									<td id="highest_avatar_tuning" onclick="councelingModalTuning()" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name_tuning" onclick="councelingModalTuning()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
								<tr>
									<td id="highest_ng_tuning" onclick="councelingModalTuning()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-9">
					<div id="container2" class="container2" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0px">
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr>
									<td id="lowest_avatar_daily_bensuki" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name_daily_bensuki" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng_daily_bensuki" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr>
									<td id="lowest_avatar_bensuki" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_name_bensuki" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
								<tr>
									<td id="lowest_ng_bensuki" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 12px;font-weight: bold;"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" id="highest_title_daily_bensuki">BAD QUALITY EMPLOYEE<br>OF THE DAY</td>
								</tr>
								<tr id="not_counceled">
									<td id="not_counceled_td_daily_bensuki" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_avatar_daily_bensuki" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name_daily_bensuki" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
								<tr>
									<td id="highest_ng_daily_bensuki" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" ></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
						<div class="gambar">
							<table style="text-align:center;width:100%">
								<tr>
									<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" onclick="councelingModalBensuki()" class="sedang" id="highest_title_bensuki">BAD QUALITY EMPLOYEE<br>OF THE WEEK</td>
								</tr>
								<tr id="not_counceled_bensuki">
									<td onclick="councelingModalBensuki()" id="not_counceled_td_bensuki" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang">BELUM TRAINING & KONSELING</td>
								</tr>
								<tr>
									<td id="highest_avatar_bensuki" onclick="councelingModalBensuki()" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 12px;font-weight: bold;cursor: pointer;"></td>
								</tr>
								<tr>
									<td id="highest_name_bensuki" onclick="councelingModalBensuki()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
								<tr>
									<td id="highest_ng_bensuki" onclick="councelingModalBensuki()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 12px;font-weight: bold;cursor: pointer;" class="sedang"></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px;margin-top: 10px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Check Date</th>
								<th style="width: 3%;">Model</th>
								<th style="width: 4%;">Nomor Reed</th>
								<th style="width: 2%;">NG</th>
								<th style="width: 4%;">Quantity</th>
								<th style="width: 2%;">Qty</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot>
							<tr style="background-color:rgba(126,86,134,.7);font-size:15px;font-weight:bold">
								<th colspan="6" style="border-top:1px solid black;border-bottom:1px solid black">TOTAL</th>
								<th style="border-top:1px solid black;border-bottom:1px solid black" id="total_ng"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCounceling">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #03adfc;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
					<table class="table table-hover table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 2%;">Name</th>
								<th style="width: 2%;">NG Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="employee_id"></td>
								<td id="name"></td>
								<td id="ng_qty"></td>
							</tr>
						</tbody>
					</table>

					<div class="form-group">
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
							<label for="">Trainee Employee</label>
						</div>
						<div class="col-xs-10" style="padding-left: 0px">
							<input type="text" name="tag_employee" id="tag_employee" class="form-control" placeholder="Scan ID Card Employee">
						</div>
						<div class="col-xs-2" style="padding-right: 0px">
							<button class="btn btn-danger" onclick="cancelScan('tag_employee')"><i class="fa fa-close"></i> Cancel</button>
						</div>
						<input type="hidden" name="firstDate" id="firstDate" class="form-control" placeholder="">
						<input type="hidden" name="lastDate" id="lastDate" class="form-control" placeholder="">
						<input type="hidden" name="line" id="line" class="form-control" placeholder="">
						<input type="hidden" name="category" id="category" class="form-control" placeholder="">
					</div>

					<div class="form-group">
						<div class="col-xs-12" style="padding-left: 0px">
							<label for="">Trained By</label>
						</div>
						<div class="col-xs-10" style="padding-left: 0px">
							<input type="text" name="tag_leader" id="tag_leader" class="form-control" placeholder="Scan ID Card Sub Leader / Leader">
						</div>
						<div class="col-xs-2" style="padding-right: 0px">
							<button class="btn btn-danger" onclick="cancelScan('tag_leader')"><i class="fa fa-close"></i> Cancel</button>
						</div>
					</div>

					<div class="form-group">
						<div class="col-xs-12" style="padding-left: 0px;">
							<label for="">Document Training</label>
						</div>
						<div class="col-xs-10" style="padding-left: 0px;">
							<!-- <input type="file" name="counceled_image" id="counceled_image" class="form-control" placeholder="Scan ID Card Sub Leader / Leader"> -->
							<a href="{{url('input/pianica/kensa_awal/training_document')}}" target="_blank" class="btn btn-primary"><i class="fa fa-pencil"></i>&nbsp; Input Document Training</a>
						</div>
					</div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel()"><i class="fa fa-check"></i> Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="tuningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<center><h4><b>Cek In Operator {{ $_GET['line'] }}</b></h4></center>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-qrcode"></i></span>
					<input type="text" class="form-control" placeholder="TAP DISINI" id="op_tuning_input" style="text-align: center">
				</div>
				<br>
				<table class="table table-bordered" id="table_tuning">
					<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th>Employee Id</th>
							<th>Name</th>
							<th>Process</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody id="body_tuning"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<!-- <script src="{{ url("js/highcharts-3d.js")}}"></script> -->
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#tanggal').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2({
			allowClear:true
		});
		fetchChart();
		$('#modalDetail').on('hidden.bs.modal', function () {
			$('#tableDetail').DataTable().clear();
		});
		setInterval(fetchChart, 300000);
	});

	var detail_all_injeksi = [];
	var detail_all_assy = [];

	function fetchChart(){
		// $("#loading").show();
		var tanggal = "{{$_GET['tanggal']}}";
		var line = "{{$_GET['line']}}";

		$("#tanggal").val(tanggal);
		$("#line").val(line).trigger('change');

		var data = {
			tanggal:tanggal,
			line:line,
		}

		var data_ng = [
		{kode : '101', name : 'Biri'},
		{kode : '102', name : 'Oktaf'},
		{kode : '104', name : 'T. Rendah'},
		{kode : '103', name : 'T. Tinggi'}
		];

		$.get('{{ url("fetch/pn/display/kensa_awal") }}', data, function(result, status, xhr) {
			$("#loading").hide();
			if(result.status){
				var series = [];
				var series_ng = [];
				var operator = [];

				// var op_low_tuning, op_low_ben;
				// var op_high_tuning, op_high_ben;

				// var op_low_week_tuning, op_low_week_bensuki;
				// var op_high_week;

				var datas = [];
				var data_op_tuning = [];
				var data_op_bensuki = [];

				var tuning_biri = [];
				var tuning_oktaf = [];
				var tuning_rendah = [];
				var tuning_tinggi = [];

				var bensuki_biri = [];
				var bensuki_oktaf = [];
				var bensuki_rendah = [];
				var bensuki_tinggi = [];

				// $.each(result.data_ng, function(index, value){
				// 	var ng = '';


				// 	$.each(data_ng, function(index3, value3){
				// 		if (value.ng == value3.kode) {
				// 			ng = value3.name;
				// 		}
				// 	})

				// 	datas.push({op_tuning : value.nama_tuning, op_bensuki : value.nama_bensuki, ng : ng, qty : value.qty});

				// 	if(data_op_tuning.indexOf(value.nama_tuning) === -1){
				// 		data_op_tuning[data_op_tuning.length] = value.nama_tuning;
				// 	}

				// 	if (value.nama_bensuki) {
				// 		if(data_op_bensuki.indexOf(value.nama_bensuki) === -1){
				// 			data_op_bensuki[data_op_bensuki.length] = value.nama_bensuki;
				// 		}
				// 	}

				// });

				data_ng_tuning2 = result.data_ng_tuning.reduce(function (r, o) {
					(r[o.name_tuning])? r[o.name_tuning] += parseInt(o.jml) : r[o.name_tuning] = parseInt(o.jml);
					return r;
				}, {});

				data_ng_bensuki2 = result.data_ng_bensuki.reduce(function (r, o) {
					(r[o.name_ben])? r[o.name_ben] += parseInt(o.jml) : r[o.name_ben] = parseInt(o.jml);
					return r;
				}, {});

				$.each(result.op_login, function(index, value){
					if (value.process_name == 'Tuning') {
						data_op_tuning.push(value.operator_name);
					}

					if (value.process_name == 'Bensuki' || value.process_name == 'Benage') {

						data_op_bensuki.push(value.operator_name);
					}
					// if(data_op_tuning.indexOf(value.name_tuning) === -1){
					// }
				});

				// $.each(result.data_ng_bensuki, function(index, value){
				// 	if(data_op_bensuki.indexOf(value.name_ben) === -1){
				// 	}
				// });

				var op_tuning = [];
				var op_bensuki = [];

				$.each(data_op_tuning, function(index, value){
					op_tuning.push(value);
					tuning_biri.push(0);
					tuning_oktaf.push(0);
					tuning_rendah.push(0);
					tuning_tinggi.push(0);
				})

				$.each(data_op_bensuki, function(index, value){
					op_bensuki.push(value);
					bensuki_biri.push(0);
					// bensuki_oktaf.push(0);
					bensuki_rendah.push(0);
					bensuki_tinggi.push(0);
				})

				$.each(result.data_ng_tuning, function(index, value){					
					$.each(data_op_tuning, function(index2, value2){
						if (value2 == value.name_tuning) {
							if (value.ng == '101') {
								tuning_biri[index2] = tuning_biri[index2] += parseInt(value.jml);
							} else if (value.ng == '102') {
								tuning_oktaf[index2] = tuning_oktaf[index2] += parseInt(value.jml);
							} else if (value.ng == '103') {
								tuning_rendah[index2] = tuning_rendah[index2] += parseInt(value.jml);
							} else if (value.ng == '104') {
								tuning_tinggi[index2] = tuning_tinggi[index2] += parseInt(value.jml);
							} 
						}
					})

				});

				$.each(result.data_ng_bensuki, function(index, value){
					$.each(data_op_bensuki, function(index2, value2){
						if (value2 == value.name_ben) {
							if (value.ng == '101') {
								bensuki_biri[index2] = bensuki_biri[index2] += parseInt(value.jml);
							// } else if (value.ng == '102') {
							// 	bensuki_oktaf[index2] = bensuki_oktaf[index2] += parseInt(value.jml);
						} else if (value.ng == '103') {
							bensuki_rendah[index2] = bensuki_rendah[index2] += parseInt(value.jml);
						} else if (value.ng == '104') {
							bensuki_tinggi[index2] = bensuki_tinggi[index2] += parseInt(value.jml);
						} 
					}
				})
				})				

				// ------ NG ------------
				$.each(data_ng, function(index, value){
					var stat2 = 0;
					$.each(result.datas_ng, function(index2, value2){
						if (value == value2.ng) {
							stat2 = 1;
							series_ng.push(parseInt(value2.jml_ng));
						}
					})

					if (stat2 == 0) {
						series_ng.push(0);
					}
				})

				//HIGHEST LOWEST TODAY

				var low_tuning = '';
				var high_tuning = '';

				var low_bensuki = '';
				var high_bensuki = '';

				if (result.yesterday_tuning.length > 0 && result.yesterday_bensuki.length > 0) {
					$.each(result.data_op, function(index, value){
						if (value.nik == result.yesterday_tuning[result.yesterday_tuning.length-1].op_tuning) {
							high_tuning = {'nik' : result.yesterday_tuning[result.yesterday_tuning.length-1].op_tuning, 'nama' : result.yesterday_tuning[result.yesterday_tuning.length-1].op_tuning+" - "+value.nama, 'ng' : parseInt(result.yesterday_tuning[result.yesterday_tuning.length-1].jml)};
						}

						if (value.nik == result.yesterday_tuning[0].op_tuning) {
							low_tuning = {'nik' : result.yesterday_tuning[0].op_tuning, 'nama' : result.yesterday_tuning[0].op_tuning+" - "+value.nama, 'ng' : parseInt(result.yesterday_tuning[0].jml)};
						}

						if (value.nik == result.yesterday_bensuki[result.yesterday_bensuki.length-1].op_ben) {
							high_bensuki = {'nik' : result.yesterday_bensuki[result.yesterday_bensuki.length-1].op_ben, 'nama' : result.yesterday_bensuki[result.yesterday_bensuki.length-1].op_ben+" - "+value.nama, 'ng' : parseInt(result.yesterday_bensuki[result.yesterday_bensuki.length-1].jml)};
						}

						if (value.nik == result.yesterday_bensuki[0].op_ben) {
							low_bensuki = {'nik' : result.yesterday_bensuki[0].op_ben, 'nama' : result.yesterday_bensuki[0].op_ben+" - "+value.nama, 'ng' : parseInt(result.yesterday_bensuki[0].jml)};

						}
					});
				}




				// $.each(result.operator, function(index, value){
				// 	var stat = 0;

				// 	$.each(result.harian, function(index2, value2){
				// 		if (value2.nik_op_plate == value.nik) {
				// 			stat = 1;
				// 		}
				// 	})

				// 	if (stat == 0) {
				// 		op_low = {'nik' : value.nik, 'nama' : value.nik+" - "+value.nama, 'ng' : 0};
				// 	}
				// })

				// if (op_low.length == 0) {
				// 	op_low = {'nik' : result.harian[0].nik_op_plate, 'nama' : result.harian[0].nik_op_plate+" - "+result.harian[0].name, 'ng' : parseInt(result.harian[0].jml_ng)};
				// }

				// op_high = {'nik' : result.yesterday_tuning[result.yesterday_tuning.length-1].op_tuning, 'nama' : result.harian[result.harian.length-1].nik_op_plate+" - "+result.harian[result.harian.length-1].name, 'ng' : parseInt(result.harian[result.harian.length-1].jml_ng)};
				
				var thumbs_up = '{{ url("data_file/injection/ok.png") }}';
				var thumbs_down = '{{ url("data_file/injection/not_ok.png") }}';

				//  --  TUNING --

				var url_lowest = '{{ url("images/avatar/") }}/'+low_tuning.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+high_tuning.nik+'.jpg';

				// op_bad1 = ['','','PI9901005','PI9807004','PI9806003'];
				// op_good1 = ['','','PI9904015','PI9809004', 'PI9812005'];
				// op_bad2 = ['','','PI1308006','PI9901006','PI2201058'];
				// op_good2 = ['','','PI9902011', 'PI9902001', 'PI9811004'];

				// var url_lowest = '{{ url("images/avatar/") }}/'+low_tuning.nik+'.jpg';
				// var url_highest = '{{ url("images/avatar/") }}/'+high_tuning.nik+'.jpg';


				$("#lowest_avatar_daily_tuning").html("<img src='"+url_lowest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_up+"' >");
				$("#lowest_name_daily_tuning").html(low_tuning.nama);
				$("#lowest_ng_daily_tuning").html("Jumlah NG = "+ low_tuning.ng);

				$("#highest_avatar_daily_tuning").html("<img src='"+url_highest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_down+"' >");
				
				$("#highest_name_daily_tuning").html(high_tuning.nama);
				$("#highest_ng_daily_tuning").html("Jumlah NG = "+ high_tuning.ng);


				//  --  BENSUKI --

				var url_lowest = '{{ url("images/avatar/") }}/'+low_bensuki.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+high_bensuki.nik+'.jpg';

				$("#lowest_avatar_daily_bensuki").html("<img src='"+url_lowest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_up+"' >");
				$("#lowest_name_daily_bensuki").html(low_bensuki.nama);
				$("#lowest_ng_daily_bensuki").html("Jumlah NG = "+ low_bensuki.ng);

				$("#highest_avatar_daily_bensuki").html("<img src='"+url_highest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_down+"' >");
				
				$("#highest_name_daily_bensuki").html(high_bensuki.nama);
				$("#highest_ng_daily_bensuki").html("Jumlah NG = "+ high_bensuki.ng);

				// //HIGHEST LOWEST WEEKLY

				var counceling = "";

				// $.each(result.operator, function(index, value){
				// 	var stat = 0;

				// 	$.each(result.mingguan, function(index2, value2){
				// 		if (value2.nik_op_plate == value.nik) {
				// 			stat = 1;
				// 		}
				// 	})

				// 	if (stat == 0) {
				// 		op_low_week = {'nik' : value.nik, 'nama' : value.nik+" - "+value.nama, 'ng' : 0};
				// 	}
				// })

				var low_week_tuning = '';
				var high_week_tuning = '';

				var low_week_bensuki = '';
				var high_week_bensuki = '';

				if (result.week_tuning.length > 0 && result.week_bensuki.length > 0) {
					$.each(result.data_op, function(index, value){
						if (value.nik == result.week_tuning[result.week_tuning.length-1].op_tuning) {
							high_week_tuning = {'nik' : result.week_tuning[result.week_tuning.length-1].op_tuning, 'nama' : result.week_tuning[result.week_tuning.length-1].op_tuning+" - "+value.nama, 'ng' : parseInt(result.week_tuning[result.week_tuning.length-1].jml)};
						}

						if (value.nik == result.week_tuning[0].op_tuning) {
							low_week_tuning = {'nik' : result.week_tuning[0].op_tuning, 'nama' : result.week_tuning[0].op_tuning+" - "+value.nama, 'ng' : parseInt(result.week_tuning[0].jml)};
						}

						if (value.nik == result.week_bensuki[result.week_bensuki.length-1].op_ben) {
							high_week_bensuki = {'nik' : result.week_bensuki[result.week_bensuki.length-1].op_ben, 'nama' : result.week_bensuki[result.week_bensuki.length-1].op_ben+" - "+value.nama, 'ng' : parseInt(result.week_bensuki[result.week_bensuki.length-1].jml)};
						}

						if (value.nik == result.week_bensuki[0].op_ben) {
							low_week_bensuki = {'nik' : result.week_bensuki[0].op_ben, 'nama' : result.week_bensuki[0].op_ben+" - "+value.nama, 'ng' : parseInt(result.week_bensuki[0].jml)};

						}
					})
				}

				// op_high_week = {'nik' : result.mingguan[result.mingguan.length-1].nik_op_plate, 'nama' : result.mingguan[result.mingguan.length-1].nik_op_plate+" - "+result.mingguan[result.mingguan.length-1].name, 'ng' : parseInt(result.mingguan[result.mingguan.length-1].jml_ng), 'from' : result.mingguan[result.mingguan.length-1].from, 'to' : result.mingguan[result.mingguan.length-1].to};

				var url_lowest = '{{ url("images/avatar/") }}/'+low_week_tuning.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+high_week_tuning.nik+'.jpg';

				$("#lowest_avatar_tuning").html("<img src='"+url_lowest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_up+"' >");
				$("#lowest_name_tuning").html(low_week_tuning.nama);
				$("#lowest_ng_tuning").html("Jumlah NG = "+ low_week_tuning.ng);

				$("#highest_avatar_tuning").html("<img src='"+url_highest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_down+"' >");
				$("#highest_name_tuning").html(high_week_tuning.nama);
				$("#highest_ng_tuning").html("Jumlah NG = "+ high_week_tuning.ng);
				$("#firstDate").val(result.firstdayweek);
				$("#lastDate").val(result.lastdayweek);

				var url_lowest = '{{ url("images/avatar/") }}/'+low_week_bensuki.nik+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+high_week_bensuki.nik+'.jpg';

				$("#lowest_avatar_bensuki").html("<img src='"+url_lowest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_up+"' >");
				$("#lowest_name_bensuki").html(low_week_bensuki.nama);
				$("#lowest_ng_bensuki").html("Jumlah NG = "+ low_week_bensuki.ng);

				$("#highest_avatar_bensuki").html("<img src='"+url_highest+"' style='width: 60px' alt='user image'> <img style='width:60px' src='"+thumbs_down+"' >");
				$("#highest_name_bensuki").html(high_week_bensuki.nama);
				$("#highest_ng_bensuki").html("Jumlah NG = "+ high_week_bensuki.ng);

				// training
				var tuning_train_stat = false;
				var bensuki_train_stat = false;

				$.each(result.training_week, function(index4, value4){
					if (value4.employee_id == high_week_tuning.nik) {
						tuning_train_stat = true;
					}

					if (value4.employee_id == high_week_bensuki.nik) {
						bensuki_train_stat = true;
					}
				})

				if (tuning_train_stat) {
					$("#highest_title_tuning").removeClass("sedang");
					$("#not_counceled_td_tuning").removeClass("sedang");
					$("#not_counceled_td_tuning").html("SUDAH TRAINING & KONSELING");
					$("#not_counceled_td_tuning").css('background-color', '#82ff80');
					$("#highest_name_tuning").removeClass("sedang");
					$("#highest_ng_tuning").removeClass("sedang");
				} else {
					$('#highest_title_tuning').prop('class','sedang');
					$('#not_counceled_td_tuning').prop('class','sedang');
					$('#not_counceled_td_tuning').html('BELUM TRAINING & KONSELING');
					$("#not_counceled_td_tuning").css('background-color', '#ff8080');
					$('#highest_name_tuning').prop('class','sedang');
					$('#highest_ng_tuning').prop('class','sedang');
				}


				if (bensuki_train_stat) {
					$("#highest_title_bensuki").removeClass("sedang");
					$("#not_counceled_td_bensuki").removeClass("sedang");
					$("#not_counceled_td_bensuki").html("SUDAH TRAINING & KONSELING");
					$("#not_counceled_td_bensuki").css('background-color', '#82ff80');
					$("#highest_name_bensuki").removeClass("sedang");
					$("#highest_ng_bensuki").removeClass("sedang");
				} else {
					$('#highest_title_bensuki').prop('class','sedang');
					$('#not_counceled_td_bensuki').prop('class','sedang');
					$('#not_counceled_td_bensuki').html('BELUM TRAINING & KONSELING');
					$("#not_counceled_td_bensuki").css('background-color', '#ff8080');
					$('#highest_name_bensuki').prop('class','sedang');
					$('#highest_ng_bensuki').prop('class','sedang');
				}

				
				// $.each(result.training, function(index3, value3){
				// 	if (value3.employee_id == op_high_week.nik && value3.period_from == op_high_week.from && value3.period_to == op_high_week.to) {
				// 		$("#highest_title").removeClass("sedang");
				// 		$("#not_counceled_td").removeClass("sedang");
				// 		$("#not_counceled_td").html("SUDAH DILAKUKAN <br> TRAINING & KONSELING");
				// 		$("#not_counceled_td").css('background-color', '#82ff80');
				// 		$("#highest_name").removeClass("sedang");
				// 		$("#highest_ng").removeClass("sedang");
				// 	}
				// })

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '400',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG TUNING "+line,
						style: {
							fontSize: '17px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: op_tuning,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						// max:150,
						stackLabels: {
							enabled: true,
							align: 'center',
							style: {
								fontSize: '17px'
							}
						}
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						column: {
							stacking: 'normal',
						},

						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category, 'Operator');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [{
						name: 'Biri',
						data: tuning_biri,
						color: "#2b908f"
					}, {
						name: 'Oktaf',
						data: tuning_oktaf,
						color: "#90ee7e"
					}, {
						name: 'T. Rendah',
						data: tuning_rendah,
						color: "#7798bf"
					}, {
						name: 'T. Tinggi',
						data: tuning_tinggi,
						color: "#f45b5b"
					}]
				});


				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						height: '400',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG BENSUKI "+line,
						style: {
							fontSize: '17px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: op_bensuki,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"20px"
							}
						},
						type: 'linear',
						// max:150,
						stackLabels: {
							enabled: true,
							align: 'center',
							style: {
								fontSize: '17px'
							}
						}
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{this.category} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category, 'NG');
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
						column : {
							stacking: 'normal'
						}
					},
					series: [{
						name: 'Biri',
						data: bensuki_biri,
						color: '#2b908f'
					// }, {
					// 	name: 'Oktaf',
					// 	data: bensuki_oktaf
				}, {
					name: 'T. Rendah',
					data: bensuki_rendah,
					color: "#7798bf"
				}, {
					name: 'T. Tinggi',
					data: bensuki_tinggi,
					color: "#f45b5b"
				}]

			});

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(category, stat) {
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');

	var bodyDetail = '';
	var total_ng = 0;

	$('#modalDetailTitle').html('Detail NG From '+category);
	if (stat === 'injeksi') {
		var index = 1;
		for (var i = 0; i < detail_all_injeksi.length; i++) {
			if (detail_all_injeksi[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].material_number+'<br>'+detail_all_injeksi[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].operator_injection+'<br>'+detail_all_injeksi[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_injeksi[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}
	if (stat === 'assy') {
		var index = 1;
		for (var i = 0; i < detail_all_assy.length; i++) {
			if (detail_all_assy[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].material_number+'<br>'+detail_all_assy[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].operator_injection+'<br>'+detail_all_assy[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_assy[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}

	$('#tableDetailBody').append(bodyDetail);

	$('#total_ng').html(total_ng);

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
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true,
		// "footerCallback": function ( row, data, start, end, display ) {
  //           var api = this.api(), data;

  //           var intVal = function ( i ) {
  //               return typeof i === 'string' ?
  //                   i.replace(/[\$,]/g, '')*1 :
  //                   typeof i === 'number' ?
  //                       i : 0;
  //           };

  //           pageTotal = api
  //               .column( 7, { page: 'current'} )
  //               .data()
  //               .reduce( function (a, b) {
  //                   return intVal(a) + intVal(b);
  //               }, 0 );

  //           $( api.column( 7 ).footer() ).html(
  //               pageTotal
  //           );
  //       }
});

	$('#modalDetail').modal('show');
}

function councelingModalBensuki() {
	if ($('#not_counceled_td_bensuki').text() == 'BELUM TRAINING & KONSELING') {
		$('#modalCounceling').modal('show');
		$('#category').val('NG Tinggi Bensuki');
		$('#employee_id').html($('#highest_name_bensuki').text().split(' - ')[0]);
		$('#name').html($('#highest_name_bensuki').text().split(' - ')[1]);
		$('#ng_qty').html($('#highest_ng_bensuki').text().split(' = ')[1]);

		$('#modalDetailTitleCounceling').html('TRAINING DAN KONSELING');

		$('#tag_employee').val('');
		$('#tag_leader').val('');
		// document.getElementById("counceled_image").value = "";
		$('#tag_employee').removeAttr('disabled');
		$('#tag_leader').removeAttr('disabled');
		$('#tag_employee').focus();
	}
}

function councelingModalTuning() {
	if ($('#not_counceled_td_tuning').text() == 'BELUM TRAINING & KONSELING') {
		$('#modalCounceling').modal('show');
		$('#category').val('NG Tinggi Tuning');
		$('#employee_id').html($('#highest_name_tuning').text().split(' - ')[0]);
		$('#name').html($('#highest_name_tuning').text().split(' - ')[1]);
		$('#ng_qty').html($('#highest_ng_tuning').text().split(' = ')[1]);

		$('#modalDetailTitleCounceling').html('TRAINING DAN KONSELING');

		$('#tag_employee').val('');
		$('#tag_leader').val('');
		// document.getElementById("counceled_image").value = "";
		$('#tag_employee').removeAttr('disabled');
		$('#tag_leader').removeAttr('disabled');
		$('#tag_employee').focus();
	}
}

function submitCouncel() {
	$('#loading').show();
	if ($('#tag_employee').val() == "" || $('#tag_leader').val() == "") {
		$('#loading').hide();
		openErrorGritter('Error!','Semua Data Harus Diisi');
		return false;
	}
	var counceled_employee = $("#tag_employee").val();
	var counceled_by = $("#tag_leader").val();
	var first_date = $("#firstDate").val();
	var last_date = $("#lastDate").val();

	var formData = new FormData();
	formData.append('counceled_employee', counceled_employee);
	formData.append('counceled_by', counceled_by);
	formData.append('first_date', first_date);
	formData.append('last_date', last_date);
	formData.append('total_ng', $("#ng_qty").text());
	formData.append('line', $("#line").text());
	formData.append('category', $("#category").val());

	$.ajax({		
		url:"{{ url('input/pianica/counceling/kensa_awal') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			$('#loading').hide();
			fetchChart();
			$('#modalCounceling').modal('hide');
			openSuccessGritter('Success','Input Konseling Berhasil');
		},
		error: function (err) {
			openErrorGritter('Error!',err);
		}
	})
}

$('#tag_employee').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_employee').val().length > 9 ){
			var data = {
				employee_id : $("#tag_employee").val()
			}

			$.get('{{ url("scan/injection/counceled_employee") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.employee.employee_id != $('#employee_id').text()) {
						audio_error.play();
						openErrorGritter('Error!', 'Operator Tidak Sama');
						$('#tag_employee').val('');
					}else{
						$('#tag_employee').val(result.employee.employee_id+'-'+result.employee.name);
						$('#tag_employee').prop('disabled',true);
						openSuccessGritter('Success!', result.message);
					}
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#tag_employee').val('');
				}
			});
		}else{
			openErrorGritter('Error!', 'Tag Tidak Ditemukan');
			$('#tag_employee').val('');
		}
	}
});

$('#tag_leader').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_leader').val().length > 9 ){
			var data = {
				employee_id : $("#tag_leader").val()
			}

			$.get('{{ url("scan/injection/counceled_by") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tag_leader').val(result.employee.employee_id+'-'+result.employee.name);
					$('#tag_leader').prop('disabled',true);
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_leader').val('');
				}
			});
		}else{
			openErrorGritter('Error', 'Tag Tidak Ditemukan');
			$('#tag_leader').val('');
		}
	}
});

function cancelScan(btn) {
	$('#'+btn).val('');
	$('#'+btn).removeAttr('disabled');
	$('#'+btn).focus();
}

function openModal() {
	$("#tuningModal").modal('show');

	var data = {
		line : getUrlParameter('line')
	}

	$("#body_tuning").empty();
	$.get('{{ url("fetch/pianica/sign_in/tuning") }}', data, function(result, status, xhr){
		var body = '';

		$.each(result.data_log, function(key, value) {
			body += '<tr>';
			body += '<td>'+value.operator_id+'</td>';
			body += '<td>'+value.operator_name+'</td>';
			body += '<td>'+value.process_name+'</td>';
			body += '<td><button class="btn btn-xs btn-danger" onclick="deleteOp(\''+value.operator_id+'\', this)"><i class="fa fa-minus"></i></button></td>';
			body += '</tr>';

		});
		$("#body_tuning").append(body);

		$("#op_tuning_input").focus();

	})
}

$('#op_tuning_input').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		var data = {
			id : this.value,
			line : getUrlParameter('line')
		}
		$.post('{{ url("post/pianica/sign_in/tuning") }}', data, function(result, status, xhr){
			if (result.status) {
				var body = '';
				body += '<tr>';
				body += '<td>'+result.datas.operator_id+'</td>';
				body += '<td>'+result.datas.operator_name+'</td>';
				body += '<td>'+result.datas.process_name+'</td>';
				body += '<td><button class="btn btn-xs btn-danger" onclick="deleteOp(\''+result.datas.operator_id+'\', this)"><i class="fa fa-minus"></i></button></td>';
				body += '</tr>';

				$("#body_tuning").append(body);
				openSuccessGritter('Success', 'Cek In Sukses');
				$("#op_tuning_input").val('');
			} else {
				openErrorGritter('Error', result.message);
			}
		})
	}
})

function deleteOp(op_id, elem) {
	var data = {
		operator_id : op_id
	}

	$.get('{{ url("delete/pianica/sign_in/tuning") }}', data, function(result, status, xhr){
		$(elem).closest('tr').remove();
	})
}

function dynamicSort(property) {
	var sortOrder = 1;
	if(property[0] === "-") {
		sortOrder = -1;
		property = property.substr(1);
	}
	return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
         var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
         return result * sortOrder;
     }
 }

 function perbandingan(a,b){
 	return a-b;
 }

 function onlyUnique(value, index, self) {
 	return self.indexOf(value) === index;
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

 var getUrlParameter = function getUrlParameter(sParam) {
 	var sPageURL = window.location.search.substring(1),
 	sURLVariables = sPageURL.split('&'),
 	sParameterName,
 	i;

 	for (i = 0; i < sURLVariables.length; i++) {
 		sParameterName = sURLVariables[i].split('=');

 		if (sParameterName[0] === sParam) {
 			return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
 		}
 	}
 	return false;
 };

 var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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


 Highcharts.createElement('link', {
 	href: '{{ url("fonts/UnicaOne.css")}}',
 	rel: 'stylesheet',
 	type: 'text/css'
 }, null, document.getElementsByTagName('head')[0]);

 Highcharts.theme = {
 	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
 	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
 	chart: {
 		backgroundColor: null,
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

</script>
@endsection