@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	#efficiencyTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		padding: 2px 5px 2px 5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
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
	.crop2 {
		overflow: hidden;
	}
	.crop2 img {
		height: 70px;
		margin: -20% 0 0 0 !important;
	}
	#gritter-notice-wrapper{
		z-index: 9999;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>

		<div class="input-group date pull-right" style="width: 10%; margin-left: 5px;">
			<div class="input-group-addon" style="border-color:black; color:white; background-color: rgba(126,86,134,.7)">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control datepicker" id="due_date" placeholder="Select Date" style="border-color: black" onchange="fetchEfficiencies()">
		</div>
		<button class="btn btn-danger pull-right" style="margin-left: 5px; width: 10%;" onclick="modalTarget();"><i class="fa fa-crosshairs"></i> Target</button>
		<button class="btn btn-primary pull-right" style="margin-left: 5px; width: 10%;" onclick="modalManpower();"><i class="fa fa-pencil-square-o"></i> Manpower</button>
		<button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="modalNonProduction();"><i class="fa fa-pencil-square-o"></i> Non Production</button>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); opacity: 0.8; display: none; z-index: 5000;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-7">
			<div id="container" style="height: 60vh; border:1px solid rgb(150,150,150);"></div>
		</div>
		<div class="col-xs-5">
			<div class="row">
				<div class="col-xs-5" style="padding-left: 0px;">
					<table class="table table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="3" style="text-align: center; font-size: 1.1vw;">PRODUCTION</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1vw; text-align: left;">Qty</td>
								<td style="font-weight: bold; width: 2%; font-size: 1vw; text-align: right;" id="total_quantity"></td>
								<td style="font-weight: bold; width: 0.1%; font-size: 1vw; text-align: left;">Pcs</td>
							</tr>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1vw; text-align: left;">In</td>
								<td style="font-weight: bold; width: 2%; font-size: 1vw; text-align: right;" id="total_input"></td>
								<td style="font-weight: bold; width: 0.1%; font-size: 1vw; text-align: left;">Min</td>
							</tr>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1vw; text-align: left;">Out</td>
								<td style="font-weight: bold; width: 2%; font-size: 1vw; text-align: right;" id="total_output"></td>
								<td style="font-weight: bold; width: 0.1%; font-size: 1vw; text-align: left;">Min</td>
							</tr>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1vw; text-align: left;">Eff</td>
								<td style="font-weight: bold; width: 2%; font-size: 1vw; text-align: right;" id="total_efficiency"></td>
								<td style="font-weight: bold; width: 0.1%; font-size: 1vw; text-align: left;">%</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-xs-7" style="padding-left: 0px;">
					<table class="table table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="3" style="text-align: center; font-size: 1.1vw;">NON PRODUCTION</th>
							</tr>
						</thead>
						<tbody id="nonProductionTable">
						</tbody>
					</table>
				</div>
				<div class="col-xs-12" style="padding-left: 0px;">
					<table class="table table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th colspan="3" style="text-align: center; font-size: 1.3vw;">RESUME</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;">Input</td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;" id="final_input"></td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: left;">Menit</td>
							</tr>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;">Output</td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;" id="final_output"></td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: left;">Menit</td>
							</tr>
							<tr>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;">Efisiensi Hari Ini</td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: right;" id="final_efficiency"></td>
								<td style="font-weight: bold; width: 1%; font-size: 1.1vw; text-align: left;">%</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-xs-12" style="padding-top: 10px;">
					<div class="row">
						<div class="col-xs-4" style="border: 1px solid black; background-color: #dd4b39; height: 20vh;">
							<center>
								<span style="font-weight: bold; color: white; font-size: 2vw;">Eff. Target</span><br>
								<span style="font-size: 4vw; font-weight: bold; color: white;" id="eff_target">100%</span>
							</center>
						</div>
						<div class="col-xs-4" style="border: 1px solid black; background-color: #00a65a; height: 20vh;">
							<center>
								<span style="font-weight: bold; color: white; font-size: 2vw;">Eff. Bulan Ini</span><br>
								<span style="font-size: 4vw; font-weight: bold; color: white;" id="eff_month">100%</span>
							</center>
						</div>
						<div class="col-xs-4" style="border: 1px solid black; height: 20vh;" id="eff_remark">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<table class="table table-bordered table-striped" id="efficiencyTable">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 0.5%; text-align: left;">Shift</th>
						<th style="width: 0.5%; text-align: center;">Jam</th>
						<th style="width: 1%; text-align: right;">Manpower*Jam Kerja<br>(Orang)*(Menit)</th>
						<th style="width: 1%; text-align: right;">Input Time<br>(Menit)</th>
						<th style="width: 1%; text-align: right;">Over Time<br>(Menit)</th>
						<th style="width: 1%; text-align: right;">Quantity<br>(Pc)</th>
						<th style="width: 1%; text-align: right;">Output Time<br>(Menit)</th>
						<th style="width: 1%; text-align: right;">Acc. Input Time<br>(Menit)</th>
						<th style="width: 1%; text-align: right;">Acc. Output Time<br>(Menit)</th>
						<th style="width: 1%; text-align: right;">Efisiensi</th>
						<th style="width: 1%; text-align: right;">Acc. Efisiensi</th>
					</tr>
				</thead>
				<tbody id="efficiencyTableBody">
				</tbody>
			</table>
		</div>
	</div>
</section>


<div class="modal fade" id="modalNonProduction" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Add/Remove Non Productions<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Aktifitas<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<select class="form-control select2" name="addActivity" id="addActivity" data-placeholder="Pilih Aktifitas" style="width: 100%;">
										<option value=""></option>
										<option value="Training Non Process">Training Non Process</option>
										<option value="KYT">KYT</option>
										<option value="Listrik Padam">Listrik Padam</option>
										<option value="Acara Perusahaan (Celebration/Ultah Yamaha)">Acara Perusahaan (Celebration/Ulang Tahun Yamaha)</option>
										<option value="Interview">Interview</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Catatan<span class="text-red"></span> :</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" placeholder="Catatan" id="addNote"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah Orang<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Jumlah Orang" id="addOrang">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Orang
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Durasi<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Waktu Time" id="addDurasi">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Menit
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
					<div class="col-sm-11" style="margin-bottom: 10px;">
						<button class="btn btn-primary pull-right" onclick="confirmAdd()">Tambah</button>
						<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Keluar</button>
					</div>
					<table class="table table-bordered table-striped" id="addNonProductionTable" style="margin-top: 10px;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 5%; text-align: left;">Aktifitas</th>
								<th style="width: 5%; text-align: left;">Catatan</th>
								<th style="width: 2%; text-align: right;">Input</th>
								<th style="width: 1%; text-align: center;">Delete</th>
							</tr>
						</thead>
						<tbody id="addNonProductionTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalManpower" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Add/Remove Manpower<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Manpower<span class="text-red">*</span> :</label>
								<div class="col-sm-7">
									<select class="form-control select2" name="addManpower" id="addManpower" data-placeholder="Pilih Manpower" style="width: 100%;">
										<option value=""></option>
										@foreach($employees as $employee)
										<option value="{{$employee->employee_id}}-{{$employee->name}}">{{$employee->employee_id}} {{$employee->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</form>
					<div class="col-sm-10" style="margin-bottom: 10px;">
						<button class="btn btn-primary pull-right" onclick="confirmAddManpower()">Tambah</button>
						<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Keluar</button>
					</div>
					<table class="table table-bordered table-striped" id="manpowerTable" style="margin-top: 10px;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 0.1%; text-align: right;">#</th>
								<th style="width: 2%; text-align: left;">NIK</th>
								<th style="width: 5%; text-align: left;">Nama</th>
								<th style="width: 0.1%; text-align: center;">Delete</th>
							</tr>
						</thead>
						<tbody id="manpowerTableBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modalTarget" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" style="width: 20%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Perbaharui Target<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Target<span class="text-red">*</span> :</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Target" id="addTarget">
										<div class="input-group-addon" style="">
											%
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
					<div class="col-sm-10" style="margin-bottom: 10px;">
						<button class="btn btn-primary pull-right" onclick="confirmAddTarget()">Perbaharui</button>
						<button class="btn btn-danger pull-right" data-dismiss="modal" style="margin-right: 10px;">Keluar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
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
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('.select2').select2();
		fetchEfficiencies();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		endDate: '<?php echo $tgl_max ?>'
	});

	function modalNonProduction(){
		$("#addActivity").prop('selectedIndex', 0).change();
		$("#addInput").val(0);
		$('#modalNonProduction').modal('show');
	}

	function modalManpower(){
		$("#addManpower").prop('selectedIndex', 0).change();
		$('#modalManpower').modal('show');
	}

	function modalTarget(){
		$("#addTarget").val(0);
		$('#modalTarget').modal('show');
	}

	function remManpower(id){
		if(confirm("Apakah anda yakin akan menghapus aktifitas ini?")){
			$('#loading').show();
			var date = $('#due_date').val();
			var data = {
				date:date,
				id:id
			}

			$.post('{{ url("delete/efficiency/manpower") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fetchEfficiencies();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function remActivity(id){
		if(confirm("Apakah anda yakin akan menghapus aktifitas ini?")){
			$('#loading').show();
			var date = $('#due_date').val();
			var data = {
				date:date,
				id:id
			}

			$.post('{{ url("delete/efficiency/non_production") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fetchEfficiencies();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function confirmAddTarget(){
		if(confirm("Apakah anda yakin akan memperbaharui persentase target?")){
			$('#loading').show();
			var date = $('#due_date').val();
			var target = $('#addTarget').val();
			var loc = '{{$location}}'.split('-');

			if(target == 0 || target == ""){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Target harus lebih dari 0.');
				return false;
			}

			var data = {
				date:date,
				target:target,
				location:loc[0],
				category:loc[1],
				hpl:loc[2],
				sloc:loc[3]
			}

			$.post('{{ url("input/efficiency/target") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fetchEfficiencies();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function confirmAddManpower(){
		if(confirm("Apakah anda yakin akan menambahkan manpower ini?")){
			$('#loading').show();
			var date = $('#due_date').val();
			var manpower = $('#addManpower').val();
			var loc = '{{$location}}'.split('-');

			if(manpower == ""){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Silahkan pilih manpower.');
				return false;
			}

			var data = {
				date:date,
				manpower:manpower,
				location:loc[0],
				category:loc[1],
				hpl:loc[2],
				sloc:loc[3]
			}

			$.post('{{ url("input/efficiency/manpower") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fetchEfficiencies();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function confirmAdd(){
		if(confirm("Apakah anda yakin akan menambahkan aktifitas ini?")){
			$('#loading').show();
			var date = $('#due_date').val();
			var activity = $('#addActivity').val();
			var note = $('#addNote').val();
			var duration = $('#addDurasi').val()*$('#addOrang').val();
			var loc = '{{$location}}'.split('-');

			if(activity == ""){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Silahkan pilih jenis aktifitas.');
				return false;
			}
			if(duration <= 0){
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Input harus lebih dari 0.');
				return false;
			}
			var data = {
				date:date,
				activity:activity,
				note:note,
				duration:duration,
				location:loc[0],
				category:loc[1],
				hpl:loc[2],
				sloc:loc[3]
			}

			$.post('{{ url("input/efficiency/non_production") }}', data, function(result, status, xhr){
				if(result.status){
					$('#loading').hide();
					fetchEfficiencies();
					audio_ok.play();
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
					return false;
				}
			});
		}
		else{
			return false;
		}
	}

	function fetchEfficiencies(){
		$('#loading').show();
		var loc = '{{$location}}'.split('-');
		var date = $('#due_date').val();

		var data = {
			date:date,
			location:loc[0],
			category:loc[1],
			hpl:loc[2],
			sloc:loc[3]
		}

		$.get('{{ url("fetch/efficiency/report_efficiency_hourly") }}', data, function(result, status, xhr){
			if(result.status){
				$('#efficiencyTableBody').html("");
				var efficiencyTableBody = "";
				var total_work_hour = 0;
				var total_quantity = 0;
				var total_output = 0;
				var total_input = 0;

				var xCategories = [];
				var effSeries = [];
				var accSeries = [];
				var tarSeries = [];
				var monSeries = [];

				$('#eff_target').text(result.efficiencies[0].target.toFixed(1)+'%');
				$('#eff_month').text(result.efficiencies[0].monthly.toFixed(1)+'%');

				var effRemark = "";
				$('#eff_remark').html("");

				if(result.efficiencies[0].monthly >= result.efficiencies[0].target){
					effRemark += '<center>';
					effRemark += '<span style="font-weight: bold; color: black; font-size: 1.5vw;">TERCAPAI</span>';
					effRemark += '<img style="height: 15vh;" src="{{ asset("images/smiley/smiley_thumb.gif") }}">';
					effRemark += '</center>';
				}
				else{
					effRemark += '<center>';
					effRemark += '<span style="font-weight: bold; color: black; font-size: 1.5vw;">TIDAK TERCAPAI</span>';
					effRemark += '<img style="height: 15vh;" src="{{ asset("images/smiley/smiley_tear.gif") }}">';
					effRemark += '</center>';
				}

				$('#eff_remark').append(effRemark);


				$.each(result.efficiencies, function(key, value){
					xCategories.push('('+value.jam+'-'+(parseInt(value.jam)+1)+')');
					effSeries.push(parseFloat((value.eff_per_hour*100).toFixed(2)));
					accSeries.push(parseFloat((value.acc_eff_per_hour*100).toFixed(2)));
					tarSeries.push(parseFloat((value.target).toFixed(2)));
					monSeries.push(parseFloat((value.monthly).toFixed(2)));

					efficiencyTableBody += '<tr>';
					efficiencyTableBody += '<td style="text-align: left;">'+value.shift+'</td>';
					efficiencyTableBody += '<td style="text-align: center;">'+value.jam+' - '+(parseInt(value.jam)+1)+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.manpower+'*'+value.work_hour+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.work_per_hour+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.ot+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.quantity+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.output+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.acc_work_per_hour+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+value.acc_output+'</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+(value.eff_per_hour*100).toFixed(2)+'%</td>';
					efficiencyTableBody += '<td style="text-align: right;">'+(value.acc_eff_per_hour*100).toFixed(2)+'%</td>';
					efficiencyTableBody += '</tr>';
					total_quantity += parseFloat(value.quantity);
					total_output += parseFloat(value.output);
					total_input += parseFloat(value.work_per_hour);
				});
				$('#efficiencyTableBody').append(efficiencyTableBody);

				$('#total_quantity').text(total_quantity);
				$('#total_output').text(total_output.toFixed(2));
				$('#total_input').text(total_input.toFixed(2));
				$('#total_efficiency').text(((total_output/total_input)*100).toFixed(2));

				$('#nonProductionTable').html("");
				var nonProductionTable = "";
				$('#addNonProductionTableBody').html("");
				var addNonProductionTableBody = "";
				var total_non_production = 0;

				$.each(result.clinics, function(key, value){
					nonProductionTable += '<tr>';
					nonProductionTable += '<td style="width: 5%;">Kunjungan Klinik</td>';
					nonProductionTable += '<td style="width: 1%; text-align: right;">'+parseFloat(value.duration).toFixed(2)+'</td>';
					nonProductionTable += '<td style="width: 0.1%;">Min</td>';
					nonProductionTable += '</tr>';
					total_non_production += parseFloat(value.duration);
				});

				$.each(result.leaves, function(key, value){
					nonProductionTable += '<tr>';
					nonProductionTable += '<td style="width: 5%;">Meninggalkan Pabrik</td>';
					nonProductionTable += '<td style="width: 1%; text-align: right;">'+parseFloat(value.duration).toFixed(2)+'</td>';
					nonProductionTable += '<td style="width: 0.1%;">Min</td>';
					nonProductionTable += '</tr>';
					total_non_production += parseFloat(value.duration);

				});

				$.each(result.non_productions, function(key, value){
					nonProductionTable += '<tr>';
					nonProductionTable += '<td style="width: 5%;">'+value.activity+'</td>';
					nonProductionTable += '<td style="width: 1%; text-align: right;">'+parseFloat(value.duration).toFixed(2)+'</td>';
					nonProductionTable += '<td style="width: 0.1%;">Min</td>';
					nonProductionTable += '</tr>';
					total_non_production += parseFloat(value.duration);

					addNonProductionTableBody += '<tr>';
					addNonProductionTableBody += '<td style="text-align: left;">'+value.activity+'</td>';
					addNonProductionTableBody += '<td style="text-align: left;">'+value.note+'</td>';
					addNonProductionTableBody += '<td style="text-align: right;">'+parseFloat(value.duration).toFixed(2)+'</td>';
					addNonProductionTableBody += '<td style="text-align: center;"><button class="btn btn-danger btn-sm" onclick="remActivity(id)" id="'+value.id+'"><i class="fa fa-trash"></i></button></td>';
					addNonProductionTableBody += '</tr>';
				});
				$('#addNonProductionTableBody').append(addNonProductionTableBody);


				$('#manpowerTable').DataTable().clear();
				$('#manpowerTable').DataTable().destroy();
				$('#manpowerTableBody').html("");
				var manpowerTableBody = "";
				var cnt = 0;

				$.each(result.manpowers, function(key, value){
					cnt += 1;
					manpowerTableBody += '<tr>';
					manpowerTableBody += '<td style="width: 0.1%;">'+cnt+'</td>';
					manpowerTableBody += '<td style="width: 2%;">'+value.employee_id+'</td>';
					manpowerTableBody += '<td style="width: 5%;">'+value.employee_name+'</td>';
					manpowerTableBody += '<td style="text-align: center; width: 0.1%;"><button class="btn btn-danger btn-sm" onclick="remManpower(id)" id="'+value.id+'"><i class="fa fa-trash"></i></button></td>';
					manpowerTableBody += '</tr>';
				});
				$('#manpowerTableBody').append(manpowerTableBody);

				nonProductionTable += '<tr>';
				nonProductionTable += '<td style="width: 5%; font-weight: bold; font-size: 1.1vw;">Total Non Production</td>';
				nonProductionTable += '<td style="width: 1%; font-weight: bold; font-size: 1.1vw; text-align: right;">'+total_non_production.toFixed(2)+'</td>';
				nonProductionTable += '<td style="width: 0.1%; font-weight: bold; font-size: 1.1vw;">Min</td>';
				nonProductionTable += '</tr>';

				$('#nonProductionTable').append(nonProductionTable);

				final_input = (total_input+total_non_production).toFixed(2);

				$('#final_input').text(final_input);
				// $('#ot_input').text(parseFloat(result.overtime_input).toFixed(2));
				$('#final_output').text(total_output.toFixed(2));
				// $('#ot_output').text(parseFloat(result.overtime_output).toFixed(2));
				$('#final_efficiency').text((((total_output)/(final_input))*100).toFixed(2));

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						backgroundColor: 'transparent'
					},
					title: {
						text: '<b>Efficiency Monitoring</b> ('+result.date+')'
					},
					xAxis: {
						categories: xCategories,
						gridLineWidth: 1,
						gridLineColor: 'RGB(200,200,200)'
					},
					legend: {
						layout: 'horizontal',
						align: 'center',
						verticalAlign: 'top',
						x: 25,
						y: 30,
						floating: true,
						borderWidth: 1,
						backgroundColor: null
					},
					credits: {
						enabled: false
					},
					yAxis: {
						tickInterval: 10,
						title: {
							text: 'Efficiency (%)'
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}%</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.8,
							borderColor: '#212121'
						},
						series: {
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								format: '{point.y:.1f}%',
								style:{
									color: 'rgba(126,86,134)',
									textOutline: 1
								}
							}
						}
					},
					series: [{
						name: 'Efficiency / Hour',
						data: effSeries,
						color: 'rgba(126,86,134,.7)'
					},{
						type: 'line',
						data: accSeries,
						name: "Acc. Efficiency / Hour",
						colorByPoint: false,
						color: "#0066b1",
						animation: false,
						dashStyle:'shortdash',
						lineWidth: 2,
						dataLabels:{
							enabled: true,
							color: "#0066b1"
						},
						marker: {
							radius: 0,
							lineColor: '#fff',
							lineWidth: 1
						},
					},{
						type: 'line',
						data: tarSeries,
						name: "Target Efficiency",
						colorByPoint: false,
						color: "red",
						animation: false,
						// dashStyle:'shortdash',
						lineWidth: 2,
						dataLabels:{
							enabled: false,
							color: "red"
						},
						marker: {
							radius: 0,
							lineColor: '#fff',
							lineWidth: 1
						},
					},{
						type: 'line',
						data: monSeries,
						name: "Acc. Efficiency This Month",
						colorByPoint: false,
						color: "green",
						animation: false,
						dashStyle:'longdash',
						lineWidth: 2,
						dataLabels:{
							enabled: false,
							color: "red"
						},
						marker: {
							radius: 0,
							lineColor: '#fff',
							lineWidth: 1
						},
					}]
				});

				$('#manpowerTable').DataTable({
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
				alert('Attempt to retrieve data failed. '+result.message);
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

function replaceNull(s) {
	return s == null ? "-" : s;
}
</script>

@endsection